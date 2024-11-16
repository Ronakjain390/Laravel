<?php

namespace App\Http\Controllers\V1\Products;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ProductDetail;
use App\Models\ProductUploadLog;
use App\Models\ProductLog;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\StockUpdateNotification;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;

class ProductController extends Controller
{
    //
    public $columnDisplayNames;
    public $customColumns;
   
    

      // DELETE for Single Product
      public function destroy($id)
      {
          $product = Product::find($id);

          if (!$product) {
              return response()->json([
                  'message' => 'Product not found.',
                  'status_code' => 404,
              ], 404);
          }

          $product->delete();

          $user = Auth::guard(Auth::getDefaultDriver())->user();
          $userName = $user->name;
          $teamMemberName = null;
          if (Auth::getDefaultDriver() == 'team-user') {
              $teamMemberName = $user->name;
              $userName = Auth::guard(Auth::getDefaultDriver())->user()->name;
          }

          $this->sendStockUpdateEmail('deleted', 1, $userName, $teamMemberName);

          return response()->json([
              'message' => 'Product deleted successfully.',
              'status_code' => 200,
          ], 200);
      }

      public function bulkDestroy(Request $request, $ids)
      {
          $count = Product::destroy($ids);

          $user = Auth::guard(Auth::getDefaultDriver())->user();
          $userName = $user->name;
          $teamMemberName = null;
          if (Auth::getDefaultDriver() == 'team-user') {
              $teamMemberName = $user->name;
              $userName = Auth::guard(Auth::getDefaultDriver())->user()->name;
          }

          $this->sendStockUpdateEmail('deleted', $count, $userName, $teamMemberName);

          return response()->json([
              'status_code' => 200,
              'message' => 'Products deleted successfully.'
          ], 200);
      }

    public function bulkUpload(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'file' => 'required|file', // Skipping MIME validation
        // ]);

        if (!$request->has('file')) {
            return response()->json([
                'errors' => 'No file was uploaded.',
                'status_code' => 400,
            ], 400);
        }

        $file = $request->file;

        // Check if the file is a CSV
        $mimeType = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();

        if (!in_array($mimeType, ['text/csv', 'application/csv', 'text/plain']) && $extension !== 'csv') {
            return response()->json([
                'errors' => 'The uploaded file is not a CSV. Please upload a valid CSV file.',
                'status_code' => 400,
            ], 400);
        }

        // if ($validator->fails()) {
        //     return response()->json([
        //         'errors' => $validator->errors(),
        //         'status_code' => 422,
        //     ], 422);
        // }

        $file = $request->file;

        $handle = fopen($file->getRealPath(), "r");

        if (!$handle) {
            return response()->json([
                'errors' => 'Unable to open the CSV file',
                'status_code' => 400,
            ], 400);
        }

        DB::beginTransaction();

        $errors = []; // Array to collect all errors
        $rowNumber = 1; // Start counting rows from 1 (assuming first row is header)
        $addedCount = 0;
        $updatedCount = 0;

        try {
            $header = fgetcsv($handle, 1000, ",");

            // Check if the header is empty or invalid
            if (empty($header) || count(array_filter($header)) === 0) {
                fclose($handle); // Close the file handle
                DB::rollBack(); // Rollback the transaction
                return response()->json([
                    'errors' => 'The file is empty or does not contain valid headers.',
                    'status_code' => 400,
                ], 400);
            }

            $header = array_map(function($item) {
                switch ($item) {
                    case 'item+AF8-code':
                    case 'item_code(Unique)':
                        return 'item_code';
                    case 'rate(with tax)':
                        return 'rate';
                    default:
                        return strtolower($item);
                }
            }, $header);

            $dataArray = [];
            $rowCount = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $rowNumber++; // Increment row number for each data row
                if ($rowNumber > 405) {
                    fclose($handle); // Close the file handle
                    DB::rollBack(); // Rollback the transaction
                    return response()->json([
                        'errors' => 'The file contains more than 400 rows. Please upload a file with only 400 rows.',
                        'status_code' => 400,
                    ], 400);
                }
                if (count($header) == count($data)) {
                    $rowData = array_combine($header, $data);
                    $dataArray[] = $rowData;
                    $rowCount++;
                } else {
                    $errors[] = "Row $rowNumber: Column count mismatch";
                    continue;
                }
            }

            // Check if there are no data rows
            if ($rowCount === 0) {
                fclose($handle); // Close the file handle
                DB::rollBack(); // Rollback the transaction
                return response()->json([
                    'errors' => 'The file contains only headers and no data rows.',
                    'status_code' => 400,
                ], 400);
            }

            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            // dd($dataArray);
            foreach ($dataArray as $index => $row) {
                $currentRowNumber = $index + 2; // +2 because we start at 1 and already incremented for header

                array_walk_recursive($row, function (&$item, $key) {
                    if (is_string($item)) {
                        $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                    }
                });

                $validationRules = [
                    'item_code' => [
                        'required',
                        Rule::unique('product')->where(function ($query) use ($userId) {
                            return $query->where('user_id', $userId);
                        }),
                    ],
                    'location' => 'nullable',
                    'category' => 'nullable',
                    'warehouse' => 'nullable',
                    'unit' => 'required',
                    'rate' => 'nullable|numeric',
                    'qty' => 'required|numeric',
                ];

                $rowValidator = Validator::make($row, $validationRules);

                if ($rowValidator->fails()) {
                    foreach ($rowValidator->errors()->toArray() as $field => $fieldErrors) {
                        foreach ($fieldErrors as $error) {
                            $errors[] = "Row $currentRowNumber, $field: $error";
                        }
                    }
                    continue; // Skip to next row if there are validation errors
                }

                if (Auth::getDefaultDriver() == 'user') {
                    $userId = Auth::id();
                } elseif (Auth::getDefaultDriver() == 'team-user') {
                    $userId = Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id;
                }

                $product = Product::updateOrCreate(
                    ['item_code' => $row['item_code'], 'user_id' => $userId],
                    [
                        'user_id' => $userId,
                        'item_code' => $row['item_code'] ?? null,
                        'location' => strtolower($row['location'] ?? ''),
                        'category' => $row['category'] ?? null,
                        'warehouse' => $row['warehouse'] ?? null,
                        'unit' => strtolower($row['unit'] ?? ''),
                        'rate' => is_numeric($row['rate']) ? (float)$row['rate'] : null,
                        'qty' => is_numeric($row['qty']) ? (float)$row['qty'] : null,
                        'total_amount' => isset($row['rate'], $row['qty']) ? (float)$row['rate'] * (float)$row['qty'] : null,
                    ]
                );


                if ($product->wasRecentlyCreated) {
                    $addedCount++;
                } else {
                    $updatedCount++;
                }

                $columnFilterDataset = [
                    'feature_id' => 1,
                    'user_id' => $userId,
                ];

                $request->merge($columnFilterDataset);
                $panelColumnsController = new PanelColumnsController;
                $columnsResponse = $panelColumnsController->index($request);
                $columnsData = json_decode($columnsResponse->content(), true);
                $columnDisplayNames = array_map(function ($column) {
                    return $column['panel_column_display_name'];
                }, $columnsData['data']);

                for ($index = 3; $index <= 6; $index++) {
                    if ($columnDisplayNames[$index] === null || $columnDisplayNames[$index] === "") {
                        $columnDisplayNames[$index] = "ch_" . ($index - 2);
                    }
                }

                $columnFilterDataset = [
                    'feature_id' => 12,
                    'user_id' => $userId,
                ];

                $request->merge($columnFilterDataset);
                $panelColumnsController = new PanelColumnsController;
                $columnsResponse = $panelColumnsController->index($request);
                $columnsData = json_decode($columnsResponse->content(), true);
                $invoiceColumnDisplayNames = array_map(function ($column) {
                    return $column['panel_column_display_name'];
                }, $columnsData['data']);

                for ($index = 3; $index <= 6; $index++) {
                    if ($invoiceColumnDisplayNames[$index] === null || $invoiceColumnDisplayNames[$index] === "") {
                        $invoiceColumnDisplayNames[$index] = "in_" . ($index - 2);
                    }
                }
                $mergedArray = array_merge($columnDisplayNames, array_slice($invoiceColumnDisplayNames, 3));

                $this->customColumns = $mergedArray;
                // dd($this->customColumns);

                foreach ($this->customColumns as $columnName) {
                    // Normalize the column name by trimming spaces and converting to lowercase
                    $normalizedColumnName = ucfirst(trim($columnName));

                    // Find the corresponding value in the row, if it exists
                    $columnValue = null;
                    foreach ($row as $key => $value) {
                        if (ucfirst(trim($key)) === $normalizedColumnName) {
                            $columnValue = $value;
                            break;
                        }
                    }

                    // Update or create the product detail with the normalized column name and value
                    ProductDetail::updateOrCreate(
                        ['product_id' => $product->id, 'column_name' => $normalizedColumnName],
                        [
                            'product_id' => $product->id,
                            'column_name' => $normalizedColumnName,
                            'column_value' => $columnValue,
                        ]
                    );
                }
            }

            if (!empty($errors)) {
                DB::rollback();
                return response()->json([
                    'errors' => $errors,
                    'status_code' => 422,
                ], 422);
            }

            // Save the file to S3
            $filePath = 'products/' . uniqid() . '.csv';

            // Upload the file content to S3
            Storage::disk('s3')->put($filePath, file_get_contents($file->getRealPath()));

            // Ensure the file is correctly uploaded by checking its size
            if (!Storage::disk('s3')->exists($filePath) || Storage::disk('s3')->size($filePath) == 0) {
                throw new \Exception('File upload to S3 failed or file is empty.');
            }

            // Log successful upload
            ProductUploadLog::create([
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'status' => 'success',
                'type' => 'new',
                'uploaded_at' => now(),
            ]);

            $user = Auth::guard(Auth::getDefaultDriver())->user();
            $userName = $user->name;
            $teamMemberName = null;
            if (Auth::getDefaultDriver() == 'team-user') {
                $teamMemberName = $user->name;
                $userName = Auth::guard(Auth::getDefaultDriver())->user()->name;
            }

            if ($addedCount > 0) {
                $this->sendStockUpdateEmail('added', $addedCount, $userName, $teamMemberName);
            }
            if ($updatedCount > 0) {
                $this->sendStockUpdateEmail('updated', $updatedCount, $userName, $teamMemberName);
            }

            DB::commit();
            fclose($handle);

            return response()->json([
                'message' => 'Products and details uploaded successfully.',
                'status_code' => 200,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            fclose($handle);
            return response()->json([
                'error' => 'Error occurred while uploading products and details: ' . $e->getMessage(),
                'status_code' => 500,
            ], 500);
        }
    }



    private function downloadValidationErrorsCSV($csvContent)
    {
        $fileName = 'validation_errors.csv';

        Storage::disk('local')->put($fileName, $csvContent);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        // Send the response with the file and set the proper status code
        $response = response()->stream(function () use ($fileName) {
            $file = Storage::disk('local')->path($fileName);
            readfile($file);
        }, 500, $headers); // Use 200 status code for success

        // Delete the file after sending the response
        Storage::disk('local')->delete($fileName);

        return $response;
    }

    private function sendStockUpdateEmail($action, $count, $userName, $teamMemberName = null)
    {
        $timestamp = now();

        $data = [
            'action' => $action,
            'count' => $count,
            'userName' => Auth::guard('team-user')->check()
                ? Auth::guard('team-user')->user()->team_user_name
                : (Auth::guard('web')->check()
                    ? Auth::guard('web')->user()->name
                    : (Auth::guard('user')->check()
                        ? Auth::guard('user')->user()->name
                        : 'Unknown User')),
            'teamMemberName' => Auth::user()->team_user_name ?? null,
            'timestamp' => $timestamp,
        ];

        if (Auth::getDefaultDriver() == 'team-user') {
            // Team member is logged in
            $teamUser = Auth::guard('team-user')->user();
            $adminUser = User::find($teamUser->team_owner_user_id);

            // Send email to admin (team owner)
            if ($adminUser && $adminUser->email) {
                Mail::to($adminUser->email)->send(new StockUpdateNotification($data));
            }

            // Send email to team member
            if ($teamUser->email) {
                $teamMemberData = $data;
                $teamMemberData['userName'] = $teamUser->name;
                $teamMemberData['teamMemberName'] = null; // No need to show team member name in their own email
                Mail::to($teamUser->email)->send(new StockUpdateNotification($teamMemberData));
            }
        } else {
            // Regular user is logged in
            $user = Auth::user();
            if ($user && $user->email) {
                Mail::to($user->email)->send(new StockUpdateNotification($data));
            }
        }
    }


    public function bulkUpdate(Request $request)
    {
        // if (!$request->has('file') || !$request->file instanceof \Livewire\TemporaryUploadedFile) {
        //     return response()->json([
        //         'errors' => 'No file was uploaded or invalid file type.',
        //         'status_code' => 400,
        //     ], 400);
        // }

        // $file = $request->file;

        if (!$request->has('file')) {
            return response()->json([
                'errors' => 'No file was uploaded.',
                'status_code' => 400,
            ], 400);
        }

        $file = $request->file;

        // Check if the file is a CSV
        $mimeType = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();

        if (!in_array($mimeType, ['text/csv', 'application/csv', 'text/plain']) && $extension !== 'csv') {
            return response()->json([
                'errors' => 'The uploaded file is not a CSV. Please upload a valid CSV file.',
                'status_code' => 400,
            ], 400);
        }


        // Check if the file is empty
        if ($file->getSize() == 0) {
            return response()->json([
                'errors' => 'The uploaded file is empty.',
                'status_code' => 400,
            ], 400);
        }

        // Read the first line of the file to check for headers
        $handle = fopen($file->getRealPath(), "r");
        $headers = fgetcsv($handle);
        fclose($handle);

        if (empty($headers) || count(array_filter($headers)) === 0) {
            return response()->json([
                'errors' => 'The file does not contain valid headers.',
                'status_code' => 400,
            ], 400);
        }

        // dd('done');

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $file = $request->file;

        // Read the CSV file and process its contents
        $handle = fopen($file->getRealPath(), "r");

        $standardAttributes = [
            'item_code', 'category', 'warehouse', 'location', 'unit', 'rate', 'qty'
        ];

        if ($handle) {
            DB::beginTransaction();

            try {
                // Read the header row to get the column names
                $header = fgetcsv($handle, 1000, ",");

                // Check if the header is empty or invalid
                if (empty($header) || count(array_filter($header)) === 0) {
                    fclose($handle);
                    DB::rollBack();
                    return response()->json([
                        'errors' => 'The file is empty or does not contain valid headers.',
                        'status_code' => 400,
                    ], 400);
                }

                $header = array_map(function($item) {
                    return $item === 'item+AF8-code' ? 'item_code' : $item;
                }, $header);

                $currentRowNumber = 1;
                $changesMade = false;
                $allErrors = [];
                $updatedProductCount = 0;

                // First pass: Validate all rows
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    $currentRowNumber++;
                    $rowData = array_combine($header, $data);

                    $validationRules = [
                        'item_code' => 'required',
                        'location' => 'nullable',
                        'category' => 'nullable',
                        'rate' => 'nullable|numeric',
                        'tax' => 'nullable|numeric',
                        'qty' => 'required|numeric',
                    ];

                    $rowValidator = Validator::make($rowData, $validationRules);

                    if ($rowValidator->fails()) {
                        $allErrors["Row {$currentRowNumber}"] = $rowValidator->errors()->toArray();
                    }

                    // Check if the item exists for the current user
                    if (!empty($rowData['item_code'])) {
                        $productExists = Product::where([
                            ['item_code', $rowData['item_code']],
                            ['user_id', $userId],
                        ])->exists();

                        if (!$productExists) {
                            $allErrors["Row {$currentRowNumber}"]["item_code"] = ["The item does not exist for the current user."];
                        }
                    }
                }

                // If there are validation errors, return them all at once
                if (!empty($allErrors)) {
                    fclose($handle);
                    DB::rollBack();
                    return response()->json([
                        'errors' => $allErrors,
                        'status_code' => 422,
                    ], 422);
                }

                // Reset file pointer to start of data (after header)
                fseek($handle, 0);
                fgetcsv($handle, 1000, ","); // Skip header row

                // Second pass: Process and update data
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    $rowData = array_combine($header, $data);

                    $product = Product::where([
                        ['item_code', $rowData['item_code']],
                        ['user_id', $userId],
                    ])->first();

                    if ($product) {
                        // Update standard attributes
                        $updateData = array_intersect_key($rowData, array_flip($standardAttributes));
                        $updateData['total_amount'] = floatval($updateData['rate']) * floatval($updateData['qty']);
                        $product->update($updateData);

                        // Handle custom invoice details
                        foreach ($rowData as $key => $value) {
                            if (!in_array($key, $standardAttributes)) {
                                ProductDetail::updateOrCreate(
                                    [
                                        'product_id' => $product->id,
                                        'column_name' => $key,
                                    ],
                                    [
                                        'column_value' => $value,
                                    ]
                                );
                            }
                        }

                        $changesMade = true;
                        $updatedProductCount++;
                    }
                }

                if ($changesMade) {
                    DB::commit();
                    fclose($handle);

                    // Send stock update email
                    $userName = Auth::getDefaultDriver() == 'team-user'
                    ? Auth::guard('team-user')->user()->team_user_name
                    : Auth::user()->name;
                    $teamMemberName = Auth::getDefaultDriver() == 'team-user' ? $userName : null;
                    $this->sendStockUpdateEmail('bulk update', $updatedProductCount, $userName, $teamMemberName);

                    return response()->json([
                        'message' => 'Products updated successfully.',
                        'status_code' => 200,
                    ], 200);
                } else {
                    DB::rollBack();
                    fclose($handle);
                    return response()->json([
                        'message' => 'No products were updated.',
                        'status_code' => 200,
                    ], 200);
                }

            } catch (\Exception $e) {
                DB::rollBack();
                fclose($handle);
                return response()->json([
                    'error' => $e->getMessage(),
                    'status_code' => 500,
                ], 500);
            }
        } else {
            return response()->json([
                'error' => 'Unable to open file.',
                'status_code' => 500,
            ], 500);
        }
    }

    public function exportColumns()
    {
        $request = request();

        $columnFilterDataset = [
            'feature_id' => 1,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ];

        $request->merge($columnFilterDataset);
        $panelColumnsController = new PanelColumnsController;
        $columnsResponse = $panelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $columnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);

        // Remove empty values from the array
        $columnDisplayNames = array_filter($columnDisplayNames, function ($value) {
            return !is_null($value) && $value !== '';
        });

        // Re-index the array
        $columnDisplayNames = array_values($columnDisplayNames);

        // Fill in custom names for null values in the range of indices 3 to 6
        for ($index = 3; $index <= 7; $index++) {
            if (isset($columnDisplayNames[$index - 1]) && ($columnDisplayNames[$index - 1] === null || $columnDisplayNames[$index - 1] === "")) {
                $columnDisplayNames[$index - 1] = "col_" . ($index - 3);
            }
        }

        $columnFilterDataset = [
            'feature_id' => 12,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ];

        $request->merge($columnFilterDataset);
        $panelColumnsController = new PanelColumnsController;
        $columnsResponse = $panelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $invoiceColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);

        // Remove empty values from the array
        $invoiceColumnDisplayNames = array_filter($invoiceColumnDisplayNames, function ($value) {
            return !is_null($value) && $value !== '';
        });

        // Re-index the array
        $invoiceColumnDisplayNames = array_values($invoiceColumnDisplayNames);

        // Fill in custom names for null values in the range of indices 3 to 6
        for ($index = 3; $index <= 7; $index++) {
            if (isset($invoiceColumnDisplayNames[$index - 1]) && ($invoiceColumnDisplayNames[$index - 1] === null || $invoiceColumnDisplayNames[$index - 1] === "")) {
                $invoiceColumnDisplayNames[$index - 1] = "col_" . ($index - 3);
            }
        }
        // dd($invoiceColumnDisplayNames);
        $mergedArray = array_merge($columnDisplayNames, array_slice($invoiceColumnDisplayNames, 3));

        // Add custom columns
        array_push($mergedArray, 'item_code(Unique)', 'category', 'warehouse', 'location', 'unit', 'qty',  'rate(with tax)');
        $this->customColumns = $mergedArray;
        $this->columnDisplayNames = $mergedArray;

        $request = request();


        $filename = 'new_product_upload.csv';

        // Create the CSV content as a string
        $csvContent = implode(',', $this->columnDisplayNames);
        // dd($csvContent);

        // Store the CSV content in the storage disk
        Storage::put('public/' . $filename, $csvContent);

        // Get the file path
        $filePath = storage_path('app/public/' . $filename);

        // Create a response for the download
        $response = new Response(file_get_contents($filePath));
        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // Delete the file
        Storage::delete('public/' . $filename);

        return $response;
    }

    private function getChallanColumn($controller, $request, $userId)
    {
        $request->merge([
            'feature_id' => 1,
            'user_id' => $userId,
        ]);

        $response = $controller->index($request);
        $data = json_decode($response->content(), true);

        return collect($data['data'])->where('feature_id', 1)->pluck('panel_column_display_name')->all();
    }
    private function getInvoiceColumn($controller, $request, $userId)
    {
        $request->merge([
            'feature_id' => 12,
            'user_id' => $userId,
        ]);

        $response = $controller->index($request);
        $data = json_decode($response->content(), true);

        return collect($data['data'])->where('feature_id', 12)->pluck('panel_column_display_name')->all();
    }

    public function exportProducts(Request $request)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $products = Product::where('user_id', $userId)
            ->with('details')
            ->orderBy('created_at', 'desc')
            ->get();
        // dd($products);
        $PanelColumnsController = new PanelColumnsController;
        $challanDisplayNames = $this->getChallanColumn($PanelColumnsController, $request, $userId);
        $invoiceDisplayNames = $this->getInvoiceColumn($PanelColumnsController, $request, $userId);

        $exportedData = [];
        $headers = [
            'item_code', 'category', 'warehouse', 'location', 'unit', 'rate', 'qty'
        ];

        // Combine challan and invoice display names, filtering out empty values
        $dynamicHeaders = array_filter(array_merge($challanDisplayNames, $invoiceDisplayNames), function($value) {
            return !empty($value);
        });

        // Combine all headers
        $headers = array_merge($headers, $dynamicHeaders);

        foreach ($products as $product) {
            $rowData = [
                'item_code' => $product->item_code,
                'category' => $product->category,
                'warehouse' => $product->warehouse,
                'location' => $product->location,
                'unit' => $product->unit,
                'rate' => $product->rate,
                'qty' => $product->qty,
            ];

            foreach ($dynamicHeaders as $header) {
                $rowData[$header] = '';
            }

            foreach ($product->details as $productDetail) {
                if (!empty($productDetail->column_value) && in_array($productDetail->column_name, $headers)) {
                    $rowData[$productDetail->column_name] = $productDetail->column_value;
                }
            }

            $exportedData[] = $rowData;
        }

        // Create a temporary file path for the CSV
        $filePath = 'temp/' . uniqid() . '.csv';

        // Store the CSV file using Laravel Storage
        Storage::disk('local')->put($filePath, $this->generateCsvFile($exportedData, $headers));

        // Define the file name and content type
        $fileName = 'exported_products.csv';
        $contentType = 'text/csv';

        // Create a CSV response
        $response = new Response();
        $response->header('Content-Type', $contentType);
        $response->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->setCharset('UTF-8');

        // Get the CSV content from the stored file and add it to the response
        $response->setContent(Storage::disk('local')->get($filePath));

        // Delete the temporary CSV file after downloading
        Storage::disk('local')->delete($filePath);

        return $response;
    }

    // Update the generateCsvFile function to accept headers
    // private function generateCsvFile($data, $headers)
    // {
    //     $handle = fopen('php://temp', 'w+');
    //     fputcsv($handle, $headers); // Write the header row
    //     foreach ($data as $row) {
    //         fputcsv($handle, $row);
    //     }
    //     rewind($handle);
    //     $csv = stream_get_contents($handle);
    //     fclose($handle);
    //     return $csv;
    // }

    // Helper function to generate CSV content
    private function generateCsvFile($data)
    {
        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, array_keys($data[0])); // Write the header row
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);

        return stream_get_contents($handle);
    }


    public function index(Request $request)
    {
        $article = $request->input('article');
        $category = $request->input('category');
        $warehouse = $request->input('warehouse');
        $location = $request->input('location');
        $itemCode = $request->input('item_code');
        $userId = $request->input('user_id'); // Get user_id from the request

        $query = Product::query()->with('details')->whereHas('details', function ($query) use ($article) {
            $query->when($article, function ($query, $article) {
                return $query->where('column_value', $article);
            });
        });

        // Add a where clause to the query to filter products by user_id
        $query->where('user_id', $userId);

        $query->when($category, function ($query, $category) {
            return $query->where('category', $category);
        });

        $query->when($warehouse, function ($query, $warehouse) {
            return $query->where('warehouse', $warehouse);
        });

        $query->when($location, function ($query, $location) {
            return $query->where('location', $location);
        });

        $query->when($itemCode, function ($query, $itemCode) {
            return $query->where('item_code', $itemCode);
        });

        $products = $query->get();

        return response()->json([
            'data' => $products,
            'Content-Encoding' => 'gzip',
            'message' => 'Success',
            'status_code' => 200
        ]);
    }


    public function indexOut(Request $request) {
        // dd($request->input('item_code'));
        $article = $request->input('article');
        $category = $request->input('category');
        $warehouse = $request->input('warehouse');
        $location = $request->input('location');
        $itemCode = $request->input('item_code');
        $sentTo = $request->input('sent_to');
        $orderNo = $request->input('order_no');
        $outMethod = $request->input('out_method');


        $query = ProductLog::query()->with('product', 'product.details',  'challan');

        $query->whereHas('product.details', function ($query) use ($request) {
            if ($request->input('article')) {
                $query->where('column_value', $request->input('article'));
            }
        });


        $query->whereHas('product', function ($query) use ($request) {
            if ($request->input('location')) {
                $query->where('location', $request->input('location'));
            }
            if ($request->input('item_code')) {
                $query->where('item_code', $request->input('item_code'));
            }

            if ($request->input('category')) {
                $query->where('category', $request->input('category'));
            }

            if ($request->input('warehouse')) {
                $query->where('warehouse', $request->input('warehouse'));
            }
        });

        $query->whereHas('challan', function ($query) use ($request) {
            if ($request->input('challan_series')) {
                $query->where('challan_series', $request->input('challan_series'));
            }
            if ($request->input('series_num')) {
                $query->where('series_num', $request->input('series_num'));
            }
        });

        $query->whereHas('product.details', function ($query) use ($request) {
            if ($request->input('article')) {
                $query->where('column_value', $request->input('article'));
            }
        });

        if ($request->input('out_method')) {
            $query->where('out_method', $request->input('out_method'));
        }

        if ($request->input('from')) {
            $query->where('out_at', '>=', $request->input('from'));
        }

        if ($request->input('to')) {
            $query->where('out_at', '<=', $request->input('to'));
        }

        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
            // $query->with(['usageRecords','topupUsageRecords']);
            $query->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
        }

        $productsLog = $query->get();

        return response()->json([
            'data' => $productsLog,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function searchStock(Request $request)
    {
        try {
            $article = $request->input('article');
            $category = $request->input('category');
            $warehouse = $request->input('warehouse');
            $location = $request->input('location');
            $itemCode = $request->input('item_code');
            $userId = $request->input('user_id'); // Get user_id from the request

            $query = Product::query()->with('details')->whereHas('details', function ($query) use ($article) {
                $query->when($article, function ($query, $article) {
                    return $query->where('column_value', $article);
                });
            });

            // Add a where clause to the query to filter products by user_id
            $query->where('user_id', $userId);

            // Add a where clause to the query to filter out products where qty is not equal to 0
            $query->where('qty', '!=', 0);

            $query->when($category, function ($query, $category) {
                return $query->where('category', $category);
            });

            $query->when($warehouse, function ($query, $warehouse) {
                return $query->where('warehouse', $warehouse);
            });

            $query->when($location, function ($query, $location) {
                return $query->where('location', $location);
            });

            $query->when($itemCode, function ($query, $itemCode) {
                return $query->where('item_code', $itemCode);
            });

            $products = $query->get();

            return response()->json([
                'data' => $products,
                'Content-Encoding' => 'gzip',
                'message' => 'Success',
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function fetchProductByBarcode(Request $request){
        // $request = request();
        // dd($request->input('barcode'));
        $product = Product::where('item_code', $request->input('barcode'))
        ->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
        ->where('qty', '>', 0)
        ->with('details')->first();
        if($product){
            return response()->json([
                'data' => $product,
                'message' => 'Success',
                'status_code' => 200
            ]);
        }else{
            return response()->json([
                'message' => 'No product found',
                'status_code' => 404
            ]);
        }
    }

    public function fetchStockOutProduct()
    {
        $request = request();
        $request->merge([
            'article' => $this->Article ?? null,
            'location' => $this->location ?? null,
            'item_code' => $this->item_code ?? null,
            'category' => $this->category ?? null,
            'warehouse' => $this->warehouse ?? null,
        ]);

        $products = new ProductController;
        $response = $products->indexOut($request);
        $result = $response->getData();
        $this->products = $result->data;
        $this->articles = [];
        foreach ($this->products as $product) {
            // dd($product);
            array_push($this->articles, $product->product->details[0]->column_value);
        }
        $this->item_codes = array_unique(array_column($this->products, 'item_code'));
        $this->locations = array_unique(array_column($this->products, 'location'));
        $this->categories = array_unique(array_column($this->products, 'category'));
        $this->warehouses = array_unique(array_column($this->products, 'warehouse'));

        $this->statusCode = $result->status_code;
        if ($result->status_code !== 200) {
            $this->errorMessage = json_encode((array) $result->errors);
        }
        $this->emit('$refresh');
    }

    // Store for Single Product
    public function store(Request $request)
    {
        // dd($request->all());
        // Validate request data
        $validator = Validator::make($request->all(), [
            'item_code' => 'required',
            'category' => 'nullable',
            'warehouse' => 'nullable',
            'location' => 'nullable',
            'unit' => 'nullable',
            'rate' => 'nullable',
            'qty' => 'required',
            'tax' => 'nullable',
            'with_tax' => 'nullable',
            'columns' => 'required|array',
            'columns.*.column_name' => 'required',
            'columns.*.column_value' => 'required_if:columns.*.column_name,Article',
        ], [
            'columns.*.column_value.required_if' => 'The Article field is required.'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }


        $product = Product::updateOrCreate(
            ['item_code' => $request->input('item_code')],
            [
                'user_id' =>Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'item_code' => $request->input('item_code'),
                'category' => $request->input('category'),
                'warehouse' => strtolower($request->input('warehouse')),
                'location' => strtolower($request->input('location')),
                'unit' => strtolower($request->input('unit')),
                'rate' => $request->input('rate') ?? null,
                'qty' => $request->input('qty') ?? null,
                'tax' => $request->input('tax') ?? null,
                'with_tax' => filter_var($request->input('with_tax'), FILTER_VALIDATE_BOOLEAN),
                // 'total_amount' => $request->has('rate') && $request->has('qty') ? (float)$request->input('rate') * (float)$request->input('qty') : null,
            ]
        );

        $columnFilterDataset = [
            'feature_id' => 1,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,

        ];

        $request->merge($columnFilterDataset);
        $panelColumnsController = new PanelColumnsController;
        $columnsResponse = $panelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $columnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);

            // Fill in custom names for null values in the range of indices 3 to 6
        // Fill in custom names for null values in the range of indices 3 to 6
            // for ($index = 3; $index <= 6; $index++) {
            //     if ($columnDisplayNames[$index - 1] === null || $columnDisplayNames[$index - 1] === "") {
            //         $columnDisplayNames[$index - 1] = "col_" . ($index - 2);
            //     }
            // }
            for ($index = 3; $index <= 6; $index++) {
                if ($columnDisplayNames[$index] === null || $columnDisplayNames[$index] === "") {
                    $columnDisplayNames[$index] = "ch_" . ($index - 2);
                }
            }

            $columnFilterDataset = [
                'feature_id' => 12,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            ];

            $request->merge($columnFilterDataset);
            $panelColumnsController = new PanelColumnsController;
            $columnsResponse = $panelColumnsController->index($request);
            $columnsData = json_decode($columnsResponse->content(), true);
            $invoiceColumnDisplayNames = array_map(function ($column) {
                return $column['panel_column_display_name'];
            }, $columnsData['data']);


            // Fill in custom names for null values in the range of indices 3 to 6
            // for ($index = 3; $index <= 6; $index++) {
            //     if ($invoiceColumnDisplayNames[$index - 1] === null || $invoiceColumnDisplayNames[$index - 1] === "") {
            //         $invoiceColumnDisplayNames[$index - 1] = "col_" . ($index - 2);
            //     }
            // }
            for ($index = 3; $index <= 6; $index++) {
                if ($invoiceColumnDisplayNames[$index] === null || $invoiceColumnDisplayNames[$index] === "") {
                    $invoiceColumnDisplayNames[$index] = "in_" . ($index - 2);
                }
            }
            $mergedArray = array_merge($columnDisplayNames, array_slice($invoiceColumnDisplayNames, 3));

        $this->customColumns = $mergedArray;


        foreach ($this->customColumns as $columnName) {
            // Check if the column is present in the request columns array
            $column = collect($request->input('columns'))->firstWhere('column_name', $columnName);

            // Set the column value based on whether it exists in the request or not
            $columnValue = $column && array_key_exists('column_value', $column) ? $column['column_value'] : null;
            // dd($columnValue, $columnName);
            ProductDetail::updateOrCreate(
                ['product_id' => $product->id, 'column_name' => $columnName],
                [
                    'product_id' => $product->id,
                    'column_name' => $columnName,
                    'column_value' => $columnValue,
                ]
            );
        }


        // Return success response
        return response()->json([
            'message' => 'Product added successfully.',
            'status_code' => 200,
            'product' => $product, // Optionally, you can include the saved product in the response
        ], 200);
    }

    // Update for Single Product
    public function update(Request $request, $id)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'item_code' => 'required',
            'location' => 'nullable',
            'category' => 'nullable',
            'warehouse' => 'nullable',
            'unit' => 'nullable',
            'tax' => 'nullable',
            'rate' => 'nullable',
            'qty' => 'nullable',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found.',
                'status_code' => 404,
            ], 404);
        }

        $product->update([
            'item_code' => $request->input('item_code'),
            'location' => strtolower($request->input('location')),
            'category' => $request->input('category'),
            'warehouse' => $request->input('warehouse'),
            'unit' => strtolower($request->input('unit')),
            'rate' => $request->input('rate'),
            'qty' => $request->input('qty'),
            'tax' => $request->input('tax'),
            'total_amount' => $request->input('rate') * $request->input('qty'),
        ]);
        // dd($request->input('details'));
        foreach($request->input('details') as $detail){
            if (is_array($detail) && isset($detail['column_name'])) {
                $productDetail = ProductDetail::updateOrCreate(
                    ['product_id' => $product->id, 'column_name' => $detail['column_name']],
                    [
                        'product_id' => $product->id,
                        'column_name' => $detail['column_name'],
                        'column_value' => $detail['column_value'],
                    ]
                );
            }
        }
        return response()->json([
            'message' => 'Product updated successfully.',
            'status_code' => 200,
        ], 200);
    }



    // Move Article to another Caretory and Warehouse to their own

    public function moveCategoryAndWarehouse(Request $request)
    {
         // Retrieve data from the request
         $selectedIds = $request->input('selectedIds', []);
         $moveCategories = $request->input('moveCategories');
         $moveWarehouses = $request->input('moveWarehouses');
         $moveLocations = $request->input('moveLocations');

         // Loop through each ID and update the category and warehouse
         foreach ($selectedIds as $id) {
             $product = Product::find($id);
             if ($product) {
                 $product->category = strtolower($moveCategories);
                 $product->warehouse = strtolower($moveWarehouses);
                 $product->location = strtolower($moveLocations);
                 $product->save();
             }
         }

         return response()->json([
            'message' => 'Products moved successfully',
            'status_code' => 200,
        ]);

    }

}
