<?php

namespace App\Http\Controllers\V1\Challan;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Units;
use App\Models\Challan;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Models\Notification;
use App\Models\Receiver;
use App\Models\Product;
use App\Models\ProductLog;
use App\Models\UserDetails;
use App\Models\ChallanSfp;
use App\Models\CompanyLogo;
use App\Models\ReturnChallan;
use App\Models\BulkImportLog;
use App\Models\TransactionLog;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Str;
use App\Jobs\CreateBulkChallanJob;
use Illuminate\Http\Request;
use App\Models\ChallanStatus;
use Spatie\Async\Pool;
use Illuminate\Support\Collection;
use App\Models\ReceiverDetails;
use Illuminate\Http\Response;
use App\Models\ChallanOrderColumn;
use App\Models\ChallanOrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Models\ReturnChallanStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PlanFeatureUsageRecord;
use App\Models\FeatureTopupUsageRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\ReturnChallanOrderColumn;
use App\Models\ReturnChallanOrderDetail;
use App\Services\PDFServices\PDFEmailService;
use App\Models\PlanAdditionalFeatureUsageRecord;
use App\Services\PDFServices\PDFWhatsAppService;
use App\Models\AdditionalFeatureTopupUsageRecord;
use App\Services\PDFServices\PDFGeneratorService;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;

class ChallanController extends Controller
{

    public function store(Request $request)
    {
        // dd($request->all() , $request->series_num);
        // Check if challanId is present in the request
        $challanId = $request->challanId;

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'challan_series' => 'required|string',
            'series_num' => 'nullable',
            'challan_date' => 'required',
            'receiver_id' => 'nullable|exists:users,id',
            'receiver' => 'nullable|string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            'additional_phone_number' => 'nullable|string',
            'order_details.*.unit' => 'nullable|string',
            'order_details.*.rate' => 'nullable|numeric|min:0',
            'order_details.*.qty' => 'numeric|min:0',
            'order_details.*.details' => 'nullable|string',
            'order_details.*.tax' => 'nullable|numeric|min:0',
            'order_details.*.item_code' => 'nullable',
            'order_details.*.columns.*.column_name' => 'nullable|string',
            'order_details.*.columns.*.column_value' => 'nullable|string',
            'statuses.*.comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        if (!$request->has('challanId') || $request->challanId === null) {
            // Validate usage limit for PlanFeatureUsageRecord
            $featureId = 1; // Replace with YOUR_FEATURE_ID
            $PlanFeatureUsageRecord = new PlanFeatureUsageRecord();
            $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->validateUsageLimit($featureId);

            if ($PlanFeatureUsageRecordResponse === 'not_found') {
                return response()->json([
                    'message' => 'Feature usage limit is over or expired.',
                    'challan_id' => null,
                    'order_detail_ids' => null,
                    'order_column_ids' => null,
                    'status_ids' => null,
                    'status_code' => 422
                ], 422);
            }

            // If PlanFeatureUsageRecord is not 'active', check FeatureTopupUsageRecord
            if ($PlanFeatureUsageRecordResponse !== 'active') {
                $FeatureTopupUsageRecord = new FeatureTopupUsageRecord();
                $FeatureTopupUsageRecordResponse = $FeatureTopupUsageRecord->validateUsageLimit($featureId);

                if ($FeatureTopupUsageRecordResponse !== 'active') {
                    return response()->json([
                        'message' => 'Feature usage limit is over or expired.',
                        'challan_id' => null,
                        'order_detail_ids' => null,
                        'order_column_ids' => null,
                        'status_ids' => null,
                        'status_code' => 422
                    ], 422);
                }
            }
        }



        // Create or update a Challan
        $challanData = [
            'challan_series' => $request->challan_series,
            'challan_date' => $request->challan_date . ' ' . now()->format('H:i:s'),
            'series_num' => $request->series_num,
            'sender_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'sender' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
            'receiver_id' => $request->receiver_id ?? null,
            'receiver_detail_id' => $request->receiver_detail_id ?? null,
            'user_detail_id' => $request->user_detail_id ?? null,
            'receiver' => !empty($request->receiver) ? $request->receiver : 'Default',
            'comment' => $request->comment,
            'total' => isset($request->total) ? (float) $request->total : 0.00,
            'round_off' => $request->round_off ?? null,
            'total_qty' => $request->total_qty ?? 0.00,
            'additional_phone_number' => $request->additional_phone_number ?? null,
            'team_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_id : null,
        ];

        if ($challanId) {
            // Update existing Challan
            $challan = Challan::find($challanId);
            if (!$challan) {
                return response()->json([
                    'message' => 'Challan not found.',
                    'status_code' => 404
                ], 404);
            }
            $challan->update($challanData);
        } else {
            // Create new Challan
            $challan = Challan::create($challanData);
        }

        // Check if the challan involves a product from the stock
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                if (isset($orderDetailData['item_code'])) {
                    // Create a transaction log for each item code
                    TransactionLog::create([
                        'challan_id' => $challan->id,
                        'action' => 'challan_created',
                        'details' => 'New challan created with ID: ' . $challan->id . ' for item code: ' . $orderDetailData['item_code'],
                    ]);
                }
            }
        }

        // Handle order details and product logs
        if ($request->has('order_details')) {
            // Delete existing order details if updating
            if ($challanId) {
                $challan->orderDetails()->delete();
            }

            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new ChallanOrderDetail([
                    'challan_id' => $challan->id,
                    'unit' => $orderDetailData['unit'] ?? null,
                    'rate' => $orderDetailData['rate'] ?? null,
                    'qty' => $orderDetailData['qty'] ?? 0.00,
                    'remaining_qty' => $orderDetailData['qty'] ?? 0.00,
                    'tax' => $orderDetailData['tax'] ?? 0.00,
                    'details' => $orderDetailData['details'] ?? '',
                    'discount' => $orderDetailData['discount'] ?? null,
                    'item_code' => $orderDetailData['item_code'] ?? null,
                    'total_amount' => isset($orderDetailData['rate'], $orderDetailData['qty'])
                        ? (floatval($orderDetailData['rate']) * floatval($orderDetailData['qty'])) * (1 + floatval($orderDetailData['tax'] ?? 0.00) / 100)
                        : null,
                ]);
                $orderDetail->save();

                if (!$challanId && isset($orderDetailData['item_code'])) {
                    // Only update product quantity for new challans
                    $product = Product::where('item_code', $orderDetailData['item_code'])
                        ->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                        ->first();

                    if ($product) {
                        $newQty = max(0, $product->qty - $orderDetailData['qty']);
                        $product->update(['qty' => $newQty]);

                        ProductLog::create([
                            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                            'product_id' => $product->id,
                            'qty_out' => $orderDetailData['qty'],
                            'out_method' => 'challan',
                            'out_at' => now(),
                            'challan_id' => $challan->id,
                        ]);
                    }

                    TransactionLog::create([
                        'challan_id' => $challan->id,
                        'action' => 'challan_created',
                        'details' => 'New challan created with ID: ' . $challan->id . ' for item code: ' . $orderDetailData['item_code'],
                    ]);
                }

                // Create columns
                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        ChallanOrderColumn::create([
                            'challan_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ?? ''
                        ]);
                    }
                }
            }
        }

        // Create or update Challan Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                ChallanStatus::updateOrCreate(
                    ['challan_id' => $challan->id, 'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id],
                    [
                        'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                        'team_user_name' => Auth::user()->team_user_name ?? null,
                        'status' => $request->series_num ? 'created' : 'draft',
                        'comment' => 'Challan saved successfully',
                    ]
                );
            }
        }


        // Get the IDs of the created or updated records
        $challanId = $challan->id;
        $orderDetailIds = $challan->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $challan->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $challan->statuses->pluck('id')->toArray();

        if (!$request->has('challanId') || $request->challanId === null) {
            $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount($featureId, 1);
            // dd($PlanFeatureUsageRecordResponse);
            if (!$PlanFeatureUsageRecordResponse) {
                // Update usage count for FeatureTopupUsageRecord
                $FeatureTopupUsageRecorddResponse = $FeatureTopupUsageRecord->updateUsageCount($featureId, 1);

                if (!$FeatureTopupUsageRecorddResponse) {
                    return response()->json([
                        'message' => 'Something Went Wrong.',
                        'challan_id' => null,
                        'order_detail_ids' => null,
                        'order_column_ids' => null,
                        'status_ids' => null,
                        'status_code' => 400
                    ], 400);
                }
            }
        }

        // Generate the PDF for the Challan using PDFGenerator class
        $challan = Challan::where('id', $challan->id)->with('receiverUser', 'receiverDetails', 'userDetails', 'senderUser', 'orderDetails', 'orderDetails.columns', 'statuses')->first();
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateChallanPDF($challan);

        $response = (array) $response->getData();
        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $challan->pdf_url = $response['pdf_url'];
            $challan->save();
        }

        return response()->json([
            'message' => 'Challan Created successfully.',
            'challan_id' => $challanId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200
        ], 200);
    }

    public function importStore(Request $request)
    {
        $featureId = $request->feature_id; // Replace with YOUR_FEATURE_ID

        // Create a new Challan
        $challan = new Challan([
            'challan_series' => $request->challan_series,
            'challan_date' => $request->challan_date . ' ' . now()->format('H:i:s'),
            'series_num' => $request->series_num,
            'sender_id' => $request->sender_id,
            'sender' => $request->sender,
            'receiver_id' => $request->receiver_id,
            'receiver_detail_id' => $request->receiver_detail_id ?? null,
            'receiver' => $request->receiver,
            'comment' => $request->comment,
             'total' => isset($request->total) ? (float) $request->total : 0.00,
            'total_qty' => $request->total_qty ?? 0.00,
            'created_at' => $request->created_at,
            'updated_at' => $request->updated_at,
        ]);
        $challan->save();

        // Create Challan Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new ChallanOrderDetail([
                    'challan_id' => $challan->id,
                    'unit' => $orderDetailData['unit'],
                    'rate' => $orderDetailData['rate'] ?? 0.00,
                    'qty' => $orderDetailData['qty'] ?? 0.00,
                    'total_amount' => $orderDetailData['total_amount'] ?? 0.00,
                ]);
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new ChallanOrderColumn([
                            'challan_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ?? '',
                        ]);
                        $orderColumn->save();
                    }
                }
            }
        }

        // Create Challan Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new ChallanStatus([
                    'challan_id' => $challan->id,
                    'user_id' => $request->sender_id,
                    'user_name' => $request->sender,
                    'status' => $statusData['status'],
                    'comment' => $statusData['comment'],
                    'created_at' => $request->created_at,
                    'updated_at' => $request->updated_at,
                ]);
                $status->save();
            }
        }
        // $challan = Challan::where('id', $challan->id)->with('receiverUser', 'receiverDetails', 'senderUser', 'orderDetails', 'orderDetails.columns',  'statuses')->first();
        $challan = Challan::where('id', $challan->id)->with('receiverUser', 'receiverDetails', 'senderUser', 'orderDetails', 'orderDetails.columns',  'statuses')->first();
        // Generate the PDF for the Challan using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();


        $response = $pdfGenerator->generateChallanPDF($challan);


        $response = (array) $response->getData();
        // Handle the response from PDFGenerator

        // PDF generated successfully
        $challan->pdf_url = $response['pdf_url'];
        $challan->save();

        // Get the IDs of the created records
        $challanId = $challan->id;
        $orderDetailIds = $challan->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $challan->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $challan->statuses->pluck('id')->toArray();



        // $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount($featureId, 1);

        // if (!$PlanFeatureUsageRecordResponse) {
        //     // Update usage count for FeatureTopupUsageRecord
        //     $FeatureTopupUsageRecorddResponse = $FeatureTopupUsageRecord->updateUsageCount($featureId, 1);

        //     if (!$FeatureTopupUsageRecorddResponse) {
        //         return response()->json([
        //             'message' => 'Something Went Wrong.',
        //             'challan_id' => null,
        //             'order_detail_ids' => null,
        //             'order_column_ids' => null,
        //             'status_ids' => null,
        //             'status_code' => 400
        //         ], 400);
        //         // Handle the case when both usage counts could not be updated successfully
        //         // Add appropriate error handling or log the issue for further investigation.
        //     }
        // }
        return true;
        // return response()->json([
        //     'message' => 'Challan created successfully.',
        //     'challan_id' => $challanId,
        //     'order_detail_ids' => $orderDetailIds,
        //     'order_column_ids' => $orderColumnIds,
        //     'status_ids' => $statusIds,
        //     'status_code' => 200
        // ], 200);
    }
    /*  */
    public function exportColumns(Request $request, $option)
    {
        $filename = 'bulk_challan.csv';
        if($option == 1){

        // Get the column headers from the panelColumnDisplayNames array
        $columnHeaders = [
            'different challans',
            'receiver_special_id',
            'challan_date',
            'address',
            'unit',
            'rate',
            'qty',
        ];
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




        // Merge custom column names with the default column headers
        $columnHeaders = array_merge($columnHeaders, $columnDisplayNames);
        $data = [
            ['1','3eYuwIZcI1', '16-09-2023', 'Address',  'Unit 1', '10.25', '5', '0', 'Value 1'],
        ];
        }
            else {
            $columnHeaders = [
                'different challans',
                'receiver_special_id',
                'address',
                'item_code',
                'qty',
                'challan_date',
            ];
             // Sample data (you can replace this with your actual data)
            $data = [
                ['1','3eYuwIZcI1',  'Address', 'ABC123', '20', '16-09-2023'],
            ];

        }

        // Create the CSV content as a string
        $csvContent = implode(',', $columnHeaders) . PHP_EOL; // Add a newline after headers


        // Add data rows to the CSV content
        foreach ($data as $row) {
            $csvContent .= implode(',', $row) . PHP_EOL; // Add a newline after each row
        }

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

    private function parseCustomDate($dateString) {
        $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return Carbon::instance($date);
            }
        }
        throw new \Exception("Invalid date format: {$dateString}");
    }

    public function bulkChallanImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $file = $request->file;
        $fileName = $file->getClientOriginalName();
        $fileType = $file->getClientOriginalExtension();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Store the file in S3
        $fileUrl = Storage::disk('s3')->putFileAs('bulk_imports', $file, $fileName);

        // Create a new BulkImportLog entry
        $uploadLog = BulkImportLog::create([
            'user_id' => $userId,
            'file_name' => $fileName,
            'type' => 'challan',
            'file_type' => $fileType,
            'file_path' => $fileUrl,
            'status' => 'processing',
        ]);

        $handle = fopen($file->getRealPath(), "r");

        if (!$handle) {
            return response()->json([
                'error' => 'Unable to open the CSV file.',
                'status_code' => 500,
            ], 500);
        }

        DB::beginTransaction();

        try {
            $header = fgetcsv($handle, 1000, ",");
            if (!$header) {
                throw new \Exception("The CSV file is empty or invalid.");
            }

            $requiredColumns = ['different challans', 'receiver_special_id'];
            $missingColumns = array_diff($requiredColumns, $header);

            if (!empty($missingColumns)) {
                throw new \Exception("Missing required columns: " . implode(', ', $missingColumns));
            }

            $dataGroupedByChallan = [];
            $challanIds = [];
            $challanReceiverMap = [];
            $errors = [];

            $hasItemCode = in_array('item_code', $header);
            $rowNumber = 1;

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $rowNumber++;
                $data = array_pad($data, count($header), null);

                if (empty(trim($data[0]))) {
                    $errors[] = "Row {$rowNumber}: The 'different challan' field cannot be empty.";
                    continue;
                }

                if (count($header) !== count($data)) {
                    $errors[] = "Row {$rowNumber}: The number of header columns and data columns do not match.";
                    continue;
                }

                $rowData = array_combine($header, $data);
                $differentChallan = $rowData['different challans'];
                $receiverSpecialId = $rowData['receiver_special_id'];

                if (empty($receiverSpecialId)) {
                    $errors[] = "Row {$rowNumber}: The 'receiver_special_id' field cannot be empty.";
                    continue;
                }

                if (isset($challanReceiverMap[$differentChallan]) && $challanReceiverMap[$differentChallan] !== $receiverSpecialId) {
                    $errors[] = "Row {$rowNumber}: Challan number '{$differentChallan}' cannot be used for different receiver_special_id '{$receiverSpecialId}'.";
                    continue;
                }

                $challanReceiverMap[$differentChallan] = $receiverSpecialId;

                if (!isset($dataGroupedByChallan[$differentChallan])) {
                    $dataGroupedByChallan[$differentChallan] = [];
                }

                $dataGroupedByChallan[$differentChallan][] = $rowData;

                if ($hasItemCode && !empty($rowData['item_code'])) {
                    $product = Product::where('item_code', $rowData['item_code'])->first();
                    if (!$product) {
                        $errors[] = "Row {$rowNumber}: Product with item_code '{$rowData['item_code']}' not found.";
                        continue;
                    }
                }
            }

            if (!empty($errors)) {
                return response()->json([
                    'message' => 'Errors found in the CSV file.',
                    'errors' => $errors,
                    'status_code' => 422,
                ], 422);
            }

            foreach ($dataGroupedByChallan as $differentChallan => $rows) {
                $firstRow = $rows[0];

                $receiver = User::where('special_id', $firstRow['receiver_special_id'])
                    ->join('receivers', 'receivers.receiver_user_id', 'users.id')
                    ->leftJoin('panel_series_numbers', function ($join) {
                        $join->on('panel_series_numbers.assigned_to_id', '=', 'receivers.id')
                            ->orWhere(function ($query) {
                                $query->on('panel_series_numbers.user_id', '=', 'receivers.id')
                                    ->where('panel_series_numbers.section_id', '=', '1');
                            });
                    })
                    ->join('receiver_detail', 'receiver_detail.receiver_id', '=', 'receivers.id')
                    ->where('receivers.user_id', $userId)
                    ->select('users.*', 'receivers.*', 'panel_series_numbers.*', 'receiver_detail.id as receiver_detail_id')
                    ->first();

                if ($receiver === null) {
                    $errors[] = "Receiver with special_id '{$firstRow['receiver_special_id']}' not found or not assigned.";
                    continue;
                }

                    if ($receiver->assigned_to_id == null) {
                        $series_number = PanelSeriesNumber::where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                            ->where('default', "1")
                            ->where('panel_id', '1')
                            ->first();
                        $series_number_value = $series_number ? $series_number->series_number : null;
                    }

                    $latestSeriesNum = Challan::where('challan_series', $series_number->series_number)
                        ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                        ->max(DB::raw('CAST(series_num AS UNSIGNED)'));
                    $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

                    $userDetail = UserDetails::where('user_id', $receiver->receiver_user_id)
                        ->where('location_name', $firstRow['address'])
                        ->first();
                        // $PlanFeatureUsageRecord = new PlanFeatureUsageRecord();
                        // $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount(1, 1);

                        // Parse the date string using the correct format
                        // $challanDate = \DateTime::createFromFormat('d/m/Y', $firstRow['challan_date']);
                        // if (!$challanDate) {
                        //     throw new \Exception("Invalid date format for challan_date: {$firstRow['challan_date']}");
                        // }

                    // $challanDate = Carbon::parse($firstRow['challan_date']);

                    try {
                        $challanDate = $this->parseCustomDate($firstRow['challan_date']);
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => $e->getMessage(),
                            'status_code' => 400,
                        ], 400);
                    }


                    $challanData = [
                        'challan_series' => $receiver->assigned_to_id ? $receiver->series_number : $series_number_value,
                        'challan_date' => $challanDate->format('Y-m-d') . ' ' . now()->format('H:i:s'),
                        'receiver_id' => $receiver->receiver_user_id,
                        'receiver_detail_id' => $receiver->receiver_detail_id,
                        'user_detail_id' => $userDetail->id ?? null,
                        'receiver_special_id' => $firstRow['receiver_special_id'],
                        'receiver' => $receiver->receiver_name,
                        'comment' => $firstRow['comment'] ?? null,
                        'series_num' => $seriesNum,
                        'sender_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                        'sender' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                        'total' => 0,
                        'total_qty' => 0,
                    ];

                    $challan = new Challan($challanData);
                    $challan->save();
                    $challanIds[] = $challan->id;

                    // $PlanFeatureUsageRecord = new PlanFeatureUsageRecord();
                    // $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount(1, 1);


                    $status = new ChallanStatus([
                        'challan_id' => $challan->id,
                        'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                        'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                        'status' => 'draft',
                        'comment' => 'Challan created successfully',
                    ]);
                    $status->save();

                    $importTotalQty = 0;
                    $importTotalAmount = 0;

                    $importTotalQty = 0;
                    $importTotalAmount = 0;
                    $errors = [];

                    foreach ($rows as $index => $rowData) {
                        $rowNumber = $index + 2; // Adding 2 because index starts at 0 and we want to skip the header row

                        if (array_key_exists('item_code', array_flip($header))) {
                            // Validate item_code and qty
                            if (!isset($rowData['item_code']) || empty($rowData['item_code'])) {
                                $errors[] = "Row {$rowNumber}: 'item_code' is required.";
                                continue;
                            }

                            if (!isset($rowData['qty']) || !is_numeric($rowData['qty']) || floatval($rowData['qty']) <= 0) {
                                $errors[] = "Row {$rowNumber}: 'qty' is required and must be a positive number.";
                                continue;
                            }

                            $product = Product::where('item_code', $rowData['item_code'])->with('details')->first();

                            if (!$product) {
                                $errors[] = "Row {$rowNumber}: Product with item_code '{$rowData['item_code']}' not found.";
                                continue;
                            }

                            $qty = floatval($rowData['qty']);
                            if ($qty > $product->qty) {
                                $errors[] = "Row {$rowNumber}: Requested quantity ({$qty}) exceeds available quantity ({$product->qty}) for item '{$rowData['item_code']}'.";
                                continue;
                            }

                            $orderDetail = new ChallanOrderDetail([
                                'challan_id' => $challan->id,
                                'unit' => $product->unit,
                                'rate' => $product->rate,
                                'qty' => $qty,
                                'remaining_qty' => $qty,
                                'total_amount' => $product->rate * $qty,
                            ]);
                            $orderDetail->save();

                            // Store additional product details
                            foreach ($product->details as $detail) {
                                $orderColumn = new ChallanOrderColumn([
                                    'challan_order_detail_id' => $orderDetail->id,
                                    'column_name' => $detail->column_name,
                                    'column_value' => $detail->column_value,
                                ]);
                                $orderColumn->save();
                            }

                            // Store any additional custom columns from $rowData
                            foreach ($rowData as $columnName => $columnValue) {
                                if (!in_array($columnName, ['challan_series', 'challan_date', 'receiver_special_id', 'comment', 'different challans', 'unit', 'rate', 'qty', 'total_amount', 'address', 'item_code'])) {
                                    $orderColumn = new ChallanOrderColumn([
                                        'challan_order_detail_id' => $orderDetail->id,
                                        'column_name' => $columnName,
                                        'column_value' => $columnValue,
                                    ]);
                                    $orderColumn->save();
                                }
                            }
                            $importTotalQty += $qty;
                            $importTotalAmount += $orderDetail->total_amount;

                            $challan->total_qty += $qty;
                            $challan->total += $orderDetail->total_amount;

                            $challan->save();

                            $productUpdate = Product::where('item_code', $rowData['item_code'])->first();
                            if ($productUpdate) {
                                $previousQuantity = $productUpdate->qty;
                                $newQty = max(0, $previousQuantity - $qty);
                                $productUpdate->update(['qty' => $newQty]);

                                // Create a log entry in the product_logs table
                                ProductLog::create([
                                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                                    'product_id' => $productUpdate->id,
                                    'qty_out' => $qty,
                                    'out_method' => 'challan',
                                    'out_at' => now(),
                                    'challan_id' => $challan->id,
                                ]);
                            }
                        } else {

                            // Validate rate and Qty of each row
                            if (!isset($rowData['rate']) || !is_numeric($rowData['rate']) || floatval($rowData['rate']) <= 0) {
                                $errors[] = "Row {$rowNumber}: 'rate' is required and must be a positive number.";
                                continue;
                            }

                            if (!isset($rowData['qty']) || !is_numeric($rowData['qty']) || floatval($rowData['qty']) <= 0) {
                                $errors[] = "Row {$rowNumber}: 'qty' is required and must be a positive number.";
                                continue;
                            }



                            $orderDetail = new ChallanOrderDetail([
                                'challan_id' => $challan->id,
                                'unit' => $rowData['unit'],
                                'rate' => $rowData['rate'],
                                'qty' => $rowData['qty'],
                                'remaining_qty' => $rowData['qty'],
                                'total_amount' => floatval($rowData['rate']) * floatval($rowData['qty']),
                            ]);
                            $orderDetail->save();
                            // dd($orderDetail)
                            $importTotalQty += floatval($rowData['qty']);
                            $importTotalAmount += floatval($orderDetail->total_amount);

                            $challan->total_qty += floatval($orderDetail->qty);
                            $challan->total += floatval($orderDetail->total_amount);
                            $challan->save();
                            foreach ($rowData as $columnName => $columnValue) {
                                if (
                                    $columnName !== 'challan_series' && $columnName !== 'challan_date'
                                    && $columnName !== 'receiver_special_id' && $columnName !== 'comment'
                                    && $columnName !== 'different challans' && $columnName !== 'different challans'
                                    && $columnName !== 'unit' && $columnName !== 'rate'
                                    && $columnName !== 'qty' && $columnName !== 'total_amount'
                                    && $columnName !== 'remaining_qty'
                                    && $columnName !== 'address'
                                ) {
                                    // Create a new InvoiceOrderColumn record for each custom column
                                    $orderColumn = new ChallanOrderColumn([
                                        'challan_order_detail_id' => $orderDetail->id,
                                        'column_name' => $columnName,
                                        'column_value' => $columnValue,
                                    ]);
                                    $orderColumn->save();
                                }
                            }
                        }
                    }

                    // After the foreach loop, check if there were any errors
                    if (!empty($errors)) {
                        DB::rollback();
                        return response()->json([
                            'errors' => $errors,
                            'status_code' => 422,
                        ], 422);
                    }

                    $PlanFeatureUsageRecord = new PlanFeatureUsageRecord();
                    $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount(1, 1);

                    if (!$PlanFeatureUsageRecordResponse) {
                        $errors[] = "Usage for creating Challans is over, please recharge.";
                    }

                    // After the foreach loop, check if there were any errors
                    if (!empty($errors)) {
                        DB::rollback();
                        return response()->json([
                            'errors' => $errors,
                            'status_code' => 400,
                        ], 400);
                    }


                    $pdfGenerator = new PDFGeneratorService();
                    $response = $pdfGenerator->generateChallanPDF($challan);

                    $responseArray = $response->original;

                    if (is_array($responseArray) && isset($responseArray['status_code']) && $responseArray['status_code'] === 200) {
                        $challan->pdf_url = $responseArray['pdf_url'];
                        $challan->save();
                    } else {
                        throw new \Exception('Error generating PDF: ' . json_encode($responseArray));
                    }
                }

                DB::commit();
                fclose($handle);

                // Update the FileUploadLog status to 'completed'
                $uploadLog->update(['status' => 'completed']);

                return response()->json([
                    'data' => [
                        'challan_ids' => $challanIds,
                    ],
                    'message' => 'Bulk challan created successfully.',
                    'status_code' => 200,
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                fclose($handle);

                // Update the FileUploadLog status to 'failed' with error message
                $uploadLog->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                return response()->json([
                    'error' => 'Error occurred while creating challans: ' . $e->getMessage(),
                    'status_code' => 500,
                ], 500);
            }
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'challan_series' => 'string',
            'challan_date' => 'required',
            'receiver_id' => 'nullable|exists:users,id',
            'receiver' => 'nullable|string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            'order_details.*.unit' => 'nullable|string',
            'order_details.*.rate' => 'nullable|numeric|min:0',
            'order_details.*.qty' => 'numeric|min:0',
            'order_details.*.details' => 'nullable|string',
            'order_details.*.columns.*.column_name' => 'nullable|string',
            'order_details.*.columns.*.column_value' => 'nullable|string',
            'statuses.*.comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        // Find the Challan by ID
        $challan = Challan::find($id);

        if (!$challan) {
            return response()->json([
                'message' => 'Challan not found.',
                'status_code' => 400,
            ], 400);
        }

       // Retrieve the previous quantity before updating the details
        $previousQuantities = [];
        foreach ($challan->orderDetails as $orderDetail) {
            $previousQuantities[$orderDetail->id] = $orderDetail->qty;
        }

        // Update Challan data
        $challan->comment = $request->input('comment', $challan->comment);
        $challan->total = $request->input('total', $challan->total);
        $challan->total_qty = $request->input('total_qty', $challan->total_qty);
        $challan->challan_date = $request->input('challan_date', $challan->challan_date);
        $challan->save();

        // Update Challan Order Details and their Columns
        if ($request->has('order_details')) {
            ChallanOrderDetail::where('challan_id', $id)->delete();

            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new ChallanOrderDetail([
                    'challan_id' => $challan->id,
                    'unit' => $orderDetailData['unit'] ?? null,
                    'rate' => $orderDetailData['rate'] ?? null,
                    'qty' => $orderDetailData['qty'] ?? 0.00,
                    'tax' => $orderDetailData['tax'] ?? null,
                    'remaining_qty' => $orderDetailData['qty'] ?? 0.00,
                    'discount' => $orderDetailData['discount'] ?? null,
                    'details' => $orderDetailData['details'] ?? '',
                    'total_amount' => isset($orderDetailData['rate'], $orderDetailData['qty']) ? floatval($orderDetailData['rate']) * floatval($orderDetailData['qty']) : null,
                ]);

                $orderDetail->save();

                // Adjust stock quantity based on previous and updated quantity
                $previousQuantity = $previousQuantities[$orderDetail->id] ?? 0;
                $newQuantity = $orderDetail->qty;
                $difference = $newQuantity - $previousQuantity;

                if ($difference != 0 && isset($orderDetailData['item_code'])) {
                    $product = Product::where('item_code', $orderDetailData['item_code'])->first();
                    if ($product) {
                        $newQty = max(0, $product->qty - $difference);
                        $product->update(['qty' => $newQty]);

                        ProductLog::create([
                            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                            'product_id' => $product->id,
                            'qty_out' => $difference,
                            'out_method' => 'challan',
                            'out_at' => now(),
                            'challan_id' => $challan->id,
                        ]);
                    }
                }

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new ChallanOrderColumn([
                            'challan_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ?? '',
                        ]);
                        $orderColumn->save();
                    }
                }
            }
        }

            $status = new ChallanStatus([
                'challan_id' => $challan->id,
                'user_id' => Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                'status' => 'modified',
                'comment' => 'Challan updated successfully',
            ]);
            $status->save();


        // Get the IDs of the created records
        $challanId = $challan->id;
        $orderDetailIds = $challan->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $challan->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $challan->statuses->pluck('id')->toArray();

        $challan = Challan::where('id', $challanId)->with('receiverUser', 'senderUser', 'orderDetails', 'orderDetails.columns',  'statuses')->first();

        // // Generate the PDF for the Challan using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateChallanPDF($challan);

        $response = (array) $response->getData();

        // Handle the response from PDFGenerator
        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $challan->pdf_url = $response['pdf_url'];
            $challan->save();
        }

        return response()->json([
            'message' => 'Challan updated successfully.',
            'challan_id' => $challanId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200,
        ], 200);
    }


    public function modify(Request $request, $id)
    {
        // dd($id);
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'challan_series' => 'string',
            'challan_date' => 'required',
            'receiver_id' => 'exists:users,id',
            'receiver' => 'string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            // 'order_details.*.id' => 'required|exists:challan_order_details,id',
            'order_details.*.unit' => 'required|string',
            'order_details.*.rate' => 'numeric|min:0',
            'order_details.*.qty' => 'numeric|min:0',
            'order_details.*.details' => 'nullable|string',
            'order_details.*.total_amount' => 'numeric|min:0',
            // 'order_details.*.columns.*.id' => 'required|exists:challan_order_columns,id',
            'order_details.*.columns.*.column_name' => 'string',
            'order_details.*.columns.*.column_value' => 'string',
            'statuses.*.comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        // Find the Challan by ID
        $challan = Challan::find($id);

        if (!$challan) {
            return response()->json([
                'message' => 'Challan not found.',
                'status_code' => 400,
            ], 400);
        }

        // Update Challan data
        $challan->comment = $request->input('comment', $challan->comment);
        $challan->total = $request->input('total', $challan->total);
        $challan->total_qty = $request->input('total_qty', $challan->total_qty);

        $challan->challan_date = $request->input('challan_date', $challan->challan_date);
        // Update other fields as needed
        $challan->save();

        // Update Challan Order Details and their Columns
        if ($request->has('order_details')) {
            $ChallanOrderDetail = ChallanOrderDetail::where('challan_id', $id)->with('columns')->get();
            if ($ChallanOrderDetail) {
                foreach ($ChallanOrderDetail as $key => $value) {
                    // Delete the associated comments first
                    $ChallanOrderDetail[$key]->columns()->delete();
                    $ChallanOrderDetail[$key]->delete();
                }
                // Then, delete the ChallanOrderDetail itself
            }
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new ChallanOrderDetail([
                    'challan_id' => $challan->id,
                    'unit' => $orderDetailData['unit'],
                    'rate' => $orderDetailData['rate'] ?? 0.00,
                    'qty' => $orderDetailData['qty'] ?? 0,
                    'details' => $orderDetailData['details'] ?? 0,
                    'total_amount' => $orderDetailData['total_amount'] ?? 0.00,
                ]);
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new ChallanOrderColumn([
                            'challan_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ?? '',
                        ]);
                        $orderColumn->save();
                    }
                }
            }
        }

        // Create Challan Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new ChallanStatus([
                    'challan_id' => $challan->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    'status' => 'modified',
                    'comment' => 'Challan modified successfully',
                ]);
                $status->save();
            }
        }

        // Get the IDs of the created records
        $challanId = $challan->id;
        $orderDetailIds = $challan->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $challan->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $challan->statuses->pluck('id')->toArray();

        // Find the Challan by ID
        $challan = Challan::findOrFail($challanId)->with('receiverUser', 'senderUser', 'orderDetails', 'orderDetails.columns', 'statuses')->first();
        // $challan = Challan::findOrFail($challanId)->with('receiverUser', 'senderUser', 'orderDetails',  'statuses', 'sfp');

        // Generate the PDF for the Challan using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateChallanPDF($challan);

        if ($response['status_code'] === 200) {
            $challan->pdf_url = $response['pdf_url'];
            $challan->save();
            return response()->json([
                'message' => 'Challan modified successfully.',
                'challan_id' => $challanId,
                'order_detail_ids' => $orderDetailIds,
                'order_column_ids' => $orderColumnIds,
                'status_ids' => $statusIds,
                'status_code' => 200,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Challan PDF updation fail.',
                'challan_id' => $challanId,
                'order_detail_ids' => $orderDetailIds,
                'order_column_ids' => $orderColumnIds,
                'status_ids' => $statusIds,
                'status_code' => 400,
            ], 400);
        }
    }

    public function send(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            $challan = Challan::where('id', $challanId)->with('receiverUser', 'senderUser', 'orderDetails', 'orderDetails.columns', 'statuses')->first();

            $permissionsSender = $challan->senderUser->permissions ? json_decode($challan->senderUser->permissions, true) : null;
            $permissionsReceiver = $challan->receiverUser->permissions ? json_decode($challan->receiverUser->permissions, true) : null;

            // Generate the PDF for the Challan using PDFGenerator class
            $pdfGenerator = new PDFGeneratorService();
            $response = $pdfGenerator->generateChallanPDF($challan);

            $response = (array) $response->getData();

            // Handle the response from PDFGenerator
            if ($response['status_code'] === 200) {
                // PDF generated successfully
                $challan->pdf_url = $response['pdf_url'];
                $challan->created_at = Carbon::now();
                $challan->save();

                // Send the PDF via email
                if ($challan->receiverUser->email != null) {
                    $pdfEmailService = new PDFEmailService();
                    $recipientEmail = $challan->receiverUser->email;
                    $pdfEmailService->sendChallanByEmail($challan, $response['pdf_url'], $recipientEmail);
                }

                // Check permissions and send WhatsApp notifications if needed
                $sendWhatsApp = false;
                $phoneNumbers = [$challan->receiverUser->phone];
                if (!empty($challan->additional_phone_number)) {
                    $phoneNumbers[] = $challan->additional_phone_number;
                }
                $receiverUserEmail = $challan->senderUser ? $challan->senderUser->email : null;
                $receiverUser = $challan->receiverUser->name;
                $senderUser = $challan->senderUser->name;
                $challanNo = $challan->challan_series . '-' . $challan->series_num;
                $challanId = $challan->id;
                $heading = 'Challan';

                // Check sender permissions
                if (isset($permissionsSender['sender']['whatsapp']['sent_challan']) && $permissionsSender['sender']['whatsapp']['sent_challan']) {
                    $wallet = Wallet::where('user_id', $challan->senderUser->id)->first();
                    $deduction = 0.90 + (0.90 * 0.18);
                    if ($wallet !== null && $wallet->balance >= $deduction) {
                        $wallet->balance -= $deduction;
                        $wallet->save();
                        // Log the deduction
                        WalletLog::create([
                            'user_id' => $wallet->user_id,
                            'amount_deducted' => $deduction,
                            'remaining_balance' => $wallet->balance,
                            'challan_id' => $challanId,
                            'action' => 'challan_sent',
                            'recipient' => $challan->receiverUser->name,

                        ]);
                        $sendWhatsApp = true;
                    }
                }

                // Check receiver permissions
                if (isset($permissionsReceiver['receiver']['whatsapp']['received_challan']) && $permissionsReceiver['receiver']['whatsapp']['received_challan']) {
                    $wallet = Wallet::where('user_id', $challan->receiverUser->id)->first();
                    $deduction = 0.90 + (0.90 * 0.18);
                    if ($wallet !== null && $wallet->balance >= $deduction) {
                        $wallet->balance -= $deduction;
                        $wallet->save();

                        // Log the deduction
                        WalletLog::create([
                            'user_id' => $wallet->user_id,
                            'amount_deducted' => $deduction,
                            'remaining_balance' => $wallet->balance,
                            'challan_id' => $challanId,
                            'action' => 'challan_received',
                            'recipient' => $challan->senderUser->name,

                        ]);

                        $sendWhatsApp = true;
                    }
                }

                if ($sendWhatsApp) {
                    $pdfWhatsAppService = new PDFWhatsAppService();
                    $pdfWhatsAppService->sendChallanOnWhatsApp($phoneNumbers, $response['pdf_url'], $challanNo, $challanId, $receiverUser, $senderUser, $heading);
                }

                // Add a new "sent" status to the Challan
                $status = new ChallanStatus([
                    'challan_id' => $challanId,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    'team_user_name' => Auth::guard(Auth::getDefaultDriver())->user()->team_user_name ?? '',
                    'status' => 'sent',
                    'comment' => 'Challan sent for acceptance',
                ]);
                $status->save();

                if ($request->status_comment && trim($request->status_comment) != '') {
                    // Get the existing status_comment data
                    $statusComment = json_decode($challan->status_comment, true);

                    // Add the new comment to the status_comment data
                    $statusComment[] = [
                        'comment' => $request->status_comment,
                        'date' => date('Y-m-d'),
                        'time' => date('H:i:s'),
                        'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    ];

                    // Update the status_comment field with the combined data
                    $challan->update(['status_comment' => json_encode($statusComment)]);
                }

                $sfpExists = ChallanSfp::where('challan_id', $challanId)->exists();

                if ($sfpExists) {
                    $challanSfp = new ChallanSfp([
                        'challan_id' => $challanId,
                        'sfp_by_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                        'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
                        'sfp_to_id' => null,
                        'sfp_to_name' => $challan->receiverUser->company_name ?? $challan->receiverUser->name,
                        'status' => 'sent',
                        'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
                    ]);
                    $challanSfp->save();
                }

                // Show Notifications in Status
                $notification = new Notification([
                    'user_id' => $challan->receiverUser->id,
                    'message' => 'New Challan Received by ' . $challan->senderUser->name,
                    'added_id' => $challanId,
                    'type' => 'challan',
                    'panel' => 'receiver',
                    'template_name' => 'received_return_challan',
                ]);
                $notification->save();

                // Return a response with the token and other relevant information
                return response()->json([
                    'message' => 'Challan sent successfully.',
                    'challan_id' => $challanId,
                    'pdf_url' => $response['pdf_url'],
                    'status_code' => 200
                ], 200);
            } else {
                // Error occurred during PDF generation and storage
                // Return an error response
                return response()->json([
                    'message' => 'Error generating and storing Challan PDF.',
                    'challan_id' => $challanId,
                    'pdf_url' => null,
                    'status_code' => $response['status_code']
                ], $response['status_code']);
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('Error in send function: ' . $e->getMessage());

            // Return a response with the error message
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    public function sendBulk(Request $request, array $challanIds)
    {

        $responses = [];
        foreach ($challanIds as $challanId) {
            try {
            // Find the Challan by ID
            $challan = Challan::where('id', $challanId)->with('receiverUser', 'senderUser', 'orderDetails', 'orderDetails.columns', 'statuses')->first();
            // $challan = Challan::where('id', $challanId)->with('receiverUser', 'senderUser', 'orderDetails', 'orderDetails.columns',  'statuses')->first();

            // dd($challan->senderUser->permissions);
            $permissions = $challan->senderUser->permissions ? json_decode($challan->senderUser->permissions, true) : null;

            // Generate the PDF for the Challan using PDFGenerator class
            $pdfGenerator = new PDFGeneratorService();
            $response = $pdfGenerator->generateChallanPDF($challan);
            // dd($response);

            $response = (array) $response->getData();
            // $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            // $companyLogoData = CompanyLogo::where('user_id', $userId)->first();
            // dd($response);
            // Handle the response from PDFGenerator

            if ($response['status_code'] === 200) {
                // PDF generated successfully
                $challan->pdf_url = $response['pdf_url'];
                $challan->save();

                // Send the PDF via email
                if ($challan->receiverUser->email != null) {
                    // dd('dnj');
                    $pdfEmailService = new PDFEmailService();
                    $recipientEmail = $challan->receiverUser->email; // Replace with the actual recipient email address
                    // dd($recipientEmail);
                    $pdfEmailService->sendChallanByEmail($challan, $response['pdf_url'], $recipientEmail);
                    // dd($pdfEmailService->sendChallanByEmail($challan, $response['pdf_url'], $recipientEmail));
                }

                // Assuming that PlanAdditionalFeatureUsageRecord and AdditionalFeatureTopupUsageRecord models have been imported.

                // dd($challan);

                if ($permissions
                    && is_array($permissions['sender'])
                    && is_array($permissions['sender']['whatsapp'])
                    && $permissions['sender']['whatsapp']['sent_challan']) {

                    if ($challan->receiverUser->phone != null) {
                        $featureId = $request->feature_id; // Replace with YOUR_FEATURE_ID

                        // Validate usage limit for PlanAdditionalFeatureUsageRecord
                        $PlanAdditionalFeatureUsageRecord = new PlanAdditionalFeatureUsageRecord();

                        // Validate usage limit for AdditionalFeatureTopupUsageRecord
                        $AdditionalFeatureTopupUsageRecord = new AdditionalFeatureTopupUsageRecord();

                        $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, 1);
                        // dd($PlanAdditionalFeatureUsageRecordResponse);

                        $sendWhatsApp = false;
                $phoneNumbers = [$challan->receiverUser->phone];
                if (!empty($challan->additional_phone_number)) {
                    $phoneNumbers[] = $challan->additional_phone_number;
                }
                $receiverUserEmail = $challan->senderUser ? $challan->senderUser->email : null;
                $receiverUser = $challan->receiverUser->name;
                $senderUser = $challan->senderUser->name;
                $challanNo = $challan->challan_series . '-' . $challan->series_num;
                $challanId = $challan->id;
                $heading = 'Challan';

                // Check sender permissions
                if (isset($permissionsSender['sender']['whatsapp']['sent_challan']) && $permissionsSender['sender']['whatsapp']['sent_challan']) {
                    $wallet = Wallet::where('user_id', $challan->senderUser->id)->first();
                    $deduction = 0.90 + (0.90 * 0.18);
                    if ($wallet !== null && $wallet->balance >= $deduction) {
                        $wallet->balance -= $deduction;
                        $wallet->save();
                        // Log the deduction
                        WalletLog::create([
                            'user_id' => $wallet->user_id,
                            'amount_deducted' => $deduction,
                            'remaining_balance' => $wallet->balance,
                            'challan_id' => $challanId,
                            'action' => 'challan_sent',
                            'recipient' => $challan->receiverUser->name,

                        ]);
                        $sendWhatsApp = true;
                    }
                }

                // Check receiver permissions
                if (isset($permissionsReceiver['receiver']['whatsapp']['received_challan']) && $permissionsReceiver['receiver']['whatsapp']['received_challan']) {
                    $wallet = Wallet::where('user_id', $challan->receiverUser->id)->first();
                    $deduction = 0.90 + (0.90 * 0.18);
                    if ($wallet !== null && $wallet->balance >= $deduction) {
                        $wallet->balance -= $deduction;
                        $wallet->save();

                        // Log the deduction
                        WalletLog::create([
                            'user_id' => $wallet->user_id,
                            'amount_deducted' => $deduction,
                            'remaining_balance' => $wallet->balance,
                            'challan_id' => $challanId,
                            'action' => 'challan_received',
                            'recipient' => $challan->senderUser->name,

                        ]);

                        $sendWhatsApp = true;
                    }
                }

                if ($sendWhatsApp) {
                    $pdfWhatsAppService = new PDFWhatsAppService();
                    $pdfWhatsAppService->sendChallanOnWhatsApp($phoneNumbers, $response['pdf_url'], $challanNo, $challanId, $receiverUser, $senderUser, $heading);
                }


                    }
                }

                // dd($challan->sender);
                // Add a new "sent" status to the Challan
                $status = new ChallanStatus([
                    'challan_id' => $challanId,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    'team_user_name' => Auth::guard(Auth::getDefaultDriver())->user()->team_user_name ?? '',
                    'status' => 'sent',
                    'comment' => 'Challan sent for acceptance',
                ]);
                $status->save();

                if($request->status_comment && trim($request->status_comment) != ''){
                // Get the existing status_comment data
                $statusComment = json_decode($challan->status_comment, true);

                // Add the new comment to the status_comment data
                $statusComment[] = [
                    'comment' => $request->status_comment,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                ];

                // Update the status_comment field with the combined data
                $challan->update(['status_comment' => json_encode($statusComment)]);
            }

                // Send a notification
                // $this->sendNotification($challan->receiverUser->id);


                $sfpExists = ChallanSfp::where('challan_id', $challanId)->exists();

                if ($sfpExists) {
                    $challanSfp = new ChallanSfp(
                        [
                            'challan_id' => $challanId,
                            'sfp_by_id' =>  Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                            'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
                            'sfp_to_id' => null,
                            'sfp_to_name' => $challan->receiverUser->company_name ?? $challan->receiverUser->name,
                            'status' => 'sent',
                            'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
                        ]
                    );
                    $challanSfp->save();
                }


               // Return a response with the token and other relevant information
            $responses[] = [
                'message' => 'Challan sent successfully.',
                // 'challan_id' => $challanId,
                'pdf_url' => $response['pdf_url'],
                'status_code' => 200
            ];
            } else {
                // Error occurred during PDF generation and storage
                // Return an error response
                return response()->json([
                    'message' => 'Error generating and storing Challan PDF.',
                    // 'challan_id' => $challanId,
                    // 'token' => $token,
                    // 'token_expiry' => $status->token_expiry,
                    'pdf_url' => null,
                    'status_code' => $response['status_code']
                ], $response['status_code']);
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('Error in send function: ' . $e->getMessage());

            // Return a response with the error message
            $responses[] = [
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    return response()->json([
        'message' => 'Challan sent successfully.',
        // 'challan_id' => $challanId,
        // 'token' => $token,
        // 'token_expiry' => $status->token_expiry,
        // 'pdf_url' => $response['pdf_url'],
        'status_code' => 200
    ], 200);
}

    private function sendNotification($id)
    {
        // $firebaseCredsFile = env('FIREBASE_CREDS_FILE');

        $firebaseCredsFile = 'C:\xampp\htdocs\TheParchi-3.0-dev\firebase_credentials.json';

        $factory = (new \Kreait\Firebase\Factory())->withServiceAccount($firebaseCredsFile);
        // Get the FCM token for the user with the given ID
        // Replace this with your actual code to get the FCM token
        // Create a messaging instance
        $messaging = $factory->createMessaging();
        $fcmToken = User::find($id)->device_token;

        // Create a message
        $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $fcmToken)
            ->withNotification(\Kreait\Firebase\Messaging\Notification::fromArray([
                'title' => 'Challan Received',
                'body' => 'A challan Has been Received. Please check your challan list.',
            ])); // Customize this as needed

        // Send the message
        // dd($message);
        $messaging->send($message);
    }

    public function challanSfpCreate(Request $request)
    {
        // dd($request->all());
        $teamUsers = DB::table('team_users')->whereIn('id', $request->team_user_ids)->get();

        // Fetch admins
        $admins = DB::table('users')->whereIn('id', $request->admin_ids)->get();

        // Combine team users and admins into one collection
        $receivers = $teamUsers->concat($admins);

        // dd($receivers);
        if ($receivers->isEmpty()) {
            return response()->json([
                'errors' => 'User not found.',
                'status_code' => 500
            ], 500);
        }
        // Fetch Challan by ID
        $challan = Challan::findOrFail($request->challan_id);
        $challan->load('statuses', 'sfp');

        $subuser = $challan->statuses[0]->team_user_name;


        foreach ($receivers as $receiver) {
            // dd($receiver->email);
            $challanSfp = new ChallanSfp(
                [
                    'challan_id' => $request->challan_id,
                    'sfp_by_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
                    'sfp_to_id' => $receiver->id,
                    'sfp_to_name' => $receiver->team_user_name ?? $receiver->name,
                    'comment' => $request->comment,
                    'status' => 'sent',
                    'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
                ]
            );
            $challanSfp->save();
            $userName = $challanSfp->sfp_to_name;

            $pdfUrl = $challan->pdf_url;
            $senderUser = $challanSfp->sfp_by_name;
            $receiverUser =  $challanSfp->sfp_to_name;
            $phone = $receiver->phone;
            $challanNo = $challan->challan_series . '-' . $challan->series_num;
            // Replace these lines:
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            // dd($user->permissions);

            // With these lines:
            if (Auth::getDefaultDriver() == 'team-user') {
                $teamUser = Auth::guard(Auth::getDefaultDriver())->user();
                $permissions = $teamUser->user->permissions;
            } else {
                $user = Auth::guard(Auth::getDefaultDriver())->user();
                $permissions = $user->permissions;
            }
            // dd($permissions);
            $permissions = json_decode($permissions, true);
            // dd($permissions['sender']['email']['sfp']) ;
            $deduction = 0.90 + (0.90 * 0.18);
                    // Get the user's wallet
                    $wallet = Wallet::where('user_id', $userId)->first();
                    // dd($wallet->balance);
                    // Check if the wallet balance is greater than the deduction
                    if ($wallet !== null && $wallet->balance >= $deduction) {
                    if (isset($permissions['sender']['whatsapp']['sfp']) && $permissions['sender']['whatsapp']['sfp'] == true) {

                    $pdfWhatsAppService = new PDFWhatsAppService();
                    $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendSFPOnWhatsApp($phone, $senderUser, $pdfUrl, $receiverUser, $challanNo);
                        if($pdfWhatsAppServiceResponse == true){
                            // Deduct the cost from the wallet
                            $wallet->balance -= $deduction;
                            $wallet->save();
                        }
                    }
                }


            $pdfUrl = $challan->pdf_url;
            if (isset($permissions['sender']['email']['sfp']) && $permissions['sender']['email']['sfp'] == true) {

            // Send the PDF via email for SFP Challan Alert
            if ($receiver->email != null) {
                $pdfEmailService = new PDFEmailService();
                $recipientEmail = $receiver->email; // Replace with the actual recipient email address
                $pdfEmailService->sendChallanSfpByEmail($challan, $recipientEmail, $userName);

            }
        }
        }

        return response()->json([
            'message' => 'Challan SFP successfully.',
            'status_code' => 200
        ], 200);
    }

    public function resend(Request $request, $challanId)
    {
        // Find the Challan by ID
        $challan = Challan::findOrFail($challanId)->with('receiverUser', 'senderUser', 'orderDetails', 'orderDetails.columns', 'statuses')->first();
        // $challan = Challan::findOrFail($challanId)->with('receiverUser', 'senderUser', 'orderDetails',  'statuses', 'sfp');

        // PDF generated successfully

        // Send the PDF via email
        if ($challan->receiverUser->email != null) {
            $pdfEmailService = new PDFEmailService();
            $recipientEmail = $challan->receiverUser->email; // Replace with the actual recipient email address
            $pdfEmailService->sendChallanByEmail($challan, $challan->pdf_url, $recipientEmail);
        }

        // Assuming that PlanAdditionalFeatureUsageRecord and AdditionalFeatureTopupUsageRecord models have been imported.

        if ($challan->receiverUser->phone != null) {
            $featureId = $request->feature_id; // Replace with YOUR_FEATURE_ID

            // Validate usage limit for PlanAdditionalFeatureUsageRecord
            $PlanAdditionalFeatureUsageRecord = new PlanAdditionalFeatureUsageRecord();

            // Validate usage limit for AdditionalFeatureTopupUsageRecord
            $AdditionalFeatureTopupUsageRecord = new AdditionalFeatureTopupUsageRecord();

            $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, 1);

            if ($PlanAdditionalFeatureUsageRecordResponse) {
                $pdfWhatsAppService = new PDFWhatsAppService();
                $recipientPhoneNumber = $challan->receiverUser->phone; // Replace with the actual recipient phone number
                $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendChallanOnWhatsApp($challan, $challan->pdf_url, $recipientPhoneNumber);

                if (!$pdfWhatsAppServiceResponse) {
                    Log::error('Error sending Challan PDF Whatsapp for Challan Id: ' . $challan->id);
                    $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, -1);
                }
            } else {
                // Update usage count for AdditionalFeatureTopupUsageRecord
                $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, 1);

                if ($AdditionalFeatureTopupUsageRecordResponse) {
                    $pdfWhatsAppService = new PDFWhatsAppService();
                    $recipientPhoneNumber = $challan->receiverUser->phone; // Replace with the actual recipient phone number
                    $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendChallanOnWhatsApp($challan, $challan->pdf_url, $recipientPhoneNumber);

                    if (!$pdfWhatsAppServiceResponse) {
                        Log::error('Error sending Challan PDF Whatsapp for Challan Id: ' . $challan->id);
                        $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, -1);
                    }
                }
            }
        }


        // Add a new "sent" status to the Challan
        $status = new ChallanStatus([
            'challan_id' => $challan->id,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
            'status' => 'resent',
            'comment' => 'Challan resent for acceptance',
        ]);
        $status->save();

        // Return a response with the token and other relevant information
        return response()->json([
            'message' => 'Challan resent successfully.',
            'challan_id' => $challan->id,
            // 'token' => $token,
            // 'token_expiry' => $status->token_expiry,
            'pdf_url' => $challan->pdf_url,
            'status_code' => 200
        ], 200);
    }
    public function index(Request $request)
    {
        try {
        // dd($request->all());
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = Challan::query()->orderByDesc('id');
        $combinedValues = [];
        if (!$request->has('sender_id') && !$request->has('receiver_id')) {
            // Assuming you have a logged-in user, you can get the user ID like this:
            $query->where('sender_id', $userId);
            // Fetch the distinct filter values for Challan table (for this user)
            $distinctChallanSeries = Challan::where('sender_id', $userId)->distinct()->pluck('challan_series');
            $distinctChallanSeriesNum = Challan::where('sender_id', $userId)->distinct()->pluck('series_num');
           // Initialize an empty array to store the combined values


            // Loop through each element of $distinctChallanSeries
            foreach ($distinctChallanSeries as $series) {
                // Loop through each element of $distinctChallanSeriesNum
                foreach ($distinctChallanSeriesNum as $num) {
                    // Combine the series and number and push it into the combinedValues array
                    $combinedValues[] = $series . '-' . $num;
                }
            }
            // dd($combinedValues);

            // $distinctSenderIds = Challan::where('sender_id', $userId)->distinct()->get();
            $distinctSenderIds = Challan::where('sender_id', $userId)->distinct()->pluck('sender', 'sender_id');
            // dd($distinctSenderIds );
            $distinctReceiverIds = Challan::where('sender_id', $userId)->distinct()->pluck('receiver', 'receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');
            $distinctStatuses = ChallanStatus::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('city');
        }

        if ($request->has('challan_series')) {
            $searchTerm = $request->challan_series;

            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                // Split the string into series and number
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                // Perform the search
                $query->where('challan_series', $series)
                      ->where('series_num', $num);
            } else {
                // Invalid search term format, handle accordingly
                // For example, you could return an error message or ignore the filter
            }
        }


        // Filter by sender_id
        if ($request->has('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

        // Filter by date
        if ($request->has('from') && $request->has('to')) {
            $from = $request->from;
            $to = $request->to;

            $query->whereBetween('challan_date', [$from, $to]);
        }

        // Filter by receiver_id
        if ($request->has('receiver_id')) {
            $query->where('receiver_id', $request->receiver_id);
            // Fetch the distinct filter values for Challan table (for this user)
            $distinctChallanSeries = Challan::where('receiver_id', $userId)->distinct()->pluck('challan_series');
            $distinctChallanSeriesNum = Challan::where('receiver_id', $userId)->distinct()->pluck('series_num');
            // $distinctSenderIds = Challan::where('receiver_id', $userId)->distinct()->get();
            $distinctSenderIds = Challan::where('receiver_id', $userId)->distinct()->pluck('sender', 'receiver_id');
            // dd($distinctSenderIds );
            $distinctReceiverIds = Challan::where('receiver_id', $userId)->distinct()->pluck('receiver', 'receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            $distinctStatuses = ChallanStatus::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('city');
        }
        // Fetch the distinct filter values for status

            // Filter by status
            if ($request->has('status')) {
                $query->whereHas('statuses', function ($q) use ($request) {
                    $q->latest()->where('status', $request->status);
                });
            }
            // Filter by status
            // if ($request->has('status')) {
            //     $query->whereHas('statuses', function ($q) use ($request) {
            //         $q->where('status', $request->status);
            //     });
            // }
                // if ($request->has('status')) {
                //     $query->whereHas('statuses', function ($q) use ($request) {
                //         $q->latest()->where('status', $request->status); // Consider only the latest status
                //     });
                // }

            if ($request->has('status')) {
                // Subquery that gets the maximum created_at for each challan_id
                $subquery = ChallanStatus::select('challan_id', DB::raw('MAX(created_at) as max_created_at'))
                    ->groupBy('challan_id');

                // Main query that joins the subquery with the challan_statuses table and filters by status
                $query->joinSub($subquery, 'latest_statuses', function ($join) {
                    $join->on('challans.id', '=', 'latest_statuses.challan_id');
                })
                ->join('challan_statuses', function ($join) use ($request) {
                    $join->on('challans.id', '=', 'challan_statuses.challan_id')
                        ->on('latest_statuses.max_created_at', '=', 'challan_statuses.created_at')
                        ->where('challan_statuses.status', '=', $request->status);
                });
            }
        // Filter by deleted
        if ($request->has('deleted')) {
            $query->where('deleted', $request->deleted);
        }

        // Filter by state in ReceiverDetails
        if ($request->has('state')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        // Filter by city in ReceiverDetails
        if ($request->has('city')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        $perPage = $request->perPage ?? 100;
            $page = $request->page ?? 1;

            $challans = $query
                ->with([ 'statuses', 'orderDetails',  'sfp'])
                ->select('challans.*')
                ->paginate(50);

            // Calculate the starting item number for the current page
            $startItemNumber = ($page - 1) * $perPage + 1;

            // Add a custom attribute to each item in the collection with the calculated item number
            $challans->each(function ($item) use (&$startItemNumber) {
                $item->setAttribute('custom_item_number', $startItemNumber++);
            });
            // dd($challans);

                // dd($distinctReceiverIds);
        // return response()->json($challans, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $challans,
            'status_code' => 200,
            'pagination' => [
                'current_page' => $challans->currentPage(),
                'per_page' => $challans->perPage(),
                'total' => $challans->total(),
                'last_page' => $challans->lastPage(),
            ],
            'filters' => [
                'challan_series' =>  $distinctChallanSeries,
                // 'challan_series' =>  $combinedValues,
                'merged_challan_series' => $combinedValues,
                'sender_id' => $distinctSenderIds,
                'receiver_id' => $distinctReceiverIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                'status' => $distinctStatuses,
                'series_num' => $distinctChallanSeriesNum,
                // Add any other filter values here if needed
            ]
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error',
            'errors' => $e->getMessage(),
            'status_code' => 500
        ], 500);
    }
    }
    public function indexDetailed(Request $request)
    {
        try {
        // dd($request->page);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = Challan::query()->orderByDesc('id');
        $combinedValues = [];
        if (!$request->has('sender_id') && !$request->has('receiver_id')) {
            // Assuming you have a logged-in user, you can get the user ID like this:
            $query->where('sender_id', $userId);
            // Fetch the distinct filter values for Challan table (for this user)
            $distinctChallanSeries = Challan::where('sender_id', $userId)->distinct()->pluck('challan_series');
            $distinctChallanSeriesNum = Challan::where('sender_id', $userId)->distinct()->pluck('series_num');
           // Initialize an empty array to store the combined values


            // Loop through each element of $distinctChallanSeries
            foreach ($distinctChallanSeries as $series) {
                // Loop through each element of $distinctChallanSeriesNum
                foreach ($distinctChallanSeriesNum as $num) {
                    // Combine the series and number and push it into the combinedValues array
                    $combinedValues[] = $series . '-' . $num;
                }
            }
            // dd($combinedValues);

            // $distinctSenderIds = Challan::where('sender_id', $userId)->distinct()->get();
            $distinctSenderIds = Challan::where('sender_id', $userId)->distinct()->pluck('sender', 'sender_id');
            // dd($distinctSenderIds );
            $distinctReceiverIds = Challan::where('sender_id', $userId)->distinct()->pluck('receiver', 'receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');
            $distinctStatuses = ChallanStatus::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('city');
        }

        if ($request->has('challan_series')) {
            $searchTerm = $request->challan_series;

            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                // Split the string into series and number
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                // Perform the search
                $query->where('challan_series', $series)
                      ->where('series_num', $num);
            } else {
                // Invalid search term format, handle accordingly
                // For example, you could return an error message or ignore the filter
            }
        }


        // Filter by sender_id
        if ($request->has('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

        // Filter by date
        if ($request->has('from') && $request->has('to')) {
            $from = $request->from;
            $to = $request->to;

            $query->whereBetween('challan_date', [$from, $to]);
        }

        // Filter by receiver_id
        if ($request->has('receiver_id')) {
            $query->where('receiver_id', $request->receiver_id);
            // Fetch the distinct filter values for Challan table (for this user)
            $distinctChallanSeries = Challan::where('receiver_id', $userId)->distinct()->pluck('challan_series');
            $distinctChallanSeriesNum = Challan::where('receiver_id', $userId)->distinct()->pluck('series_num');
            // $distinctSenderIds = Challan::where('receiver_id', $userId)->distinct()->get();
            $distinctSenderIds = Challan::where('receiver_id', $userId)->distinct()->pluck('sender', 'receiver_id');
            // dd($distinctSenderIds );
            $distinctReceiverIds = Challan::where('receiver_id', $userId)->distinct()->pluck('receiver', 'receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            $distinctStatuses = ChallanStatus::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('city');
        }
        // Fetch the distinct filter values for status

            // Filter by status
            if ($request->has('status')) {
                $query->whereHas('statuses', function ($q) use ($request) {
                    $q->latest()->where('status', $request->status);
                });
            }
            // Filter by status
        // if ($request->has('status')) {
        //     $query->whereHas('statuses', function ($q) use ($request) {
        //         $q->where('status', $request->status);
        //     });
        // }
            // if ($request->has('status')) {
            //     $query->whereHas('statuses', function ($q) use ($request) {
            //         $q->latest()->where('status', $request->status); // Consider only the latest status
            //     });
            // }

            if ($request->has('status')) {
                // Subquery that gets the maximum created_at for each challan_id
                $subquery = ChallanStatus::select('challan_id', DB::raw('MAX(created_at) as max_created_at'))
                    ->groupBy('challan_id');

                // Main query that joins the subquery with the challan_statuses table and filters by status
                $query->joinSub($subquery, 'latest_statuses', function ($join) {
                    $join->on('challans.id', '=', 'latest_statuses.challan_id');
                })
                ->join('challan_statuses', function ($join) use ($request) {
                    $join->on('challans.id', '=', 'challan_statuses.challan_id')
                        ->on('latest_statuses.max_created_at', '=', 'challan_statuses.created_at')
                        ->where('challan_statuses.status', '=', $request->status);
                });
            }
        // Filter by deleted
        if ($request->has('deleted')) {
            $query->where('deleted', $request->deleted);
        }


        // Filter by state in ReceiverDetails
        if ($request->has('state')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        // Filter by city in ReceiverDetails
        if ($request->has('city')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        // Add any other desired filters

        // $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'sfp'])->paginate(200);
        // $challans = $query
        // ->with(['receiverUser', 'statuses', 'receiverDetails', 'sfp'])
        // ->paginate(200);
        // $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails','orderDetails', 'sfp'])->select('challans.*')->paginate(100,null,null,$request->page??1);
        // dd($request->perPage);
        $perPage = $request->perPage ?? 100;
            $page = $request->page ?? 1;

            $challans = $query
                ->with([ 'orderDetails', 'orderDetails.columns' ])
                ->select('challans.*')
                ->paginate(50);

            // Calculate the starting item number for the current page
            $startItemNumber = ($page - 1) * $perPage + 1;

            // Add a custom attribute to each item in the collection with the calculated item number
            $challans->each(function ($item) use (&$startItemNumber) {
                $item->setAttribute('custom_item_number', $startItemNumber++);
            });
            // dd($challans);

                // dd($distinctReceiverIds);
        // return response()->json($challans, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $challans,
            'status_code' => 200,
            'pagination' => [
                'current_page' => $challans->currentPage(),
                'per_page' => $challans->perPage(),
                'total' => $challans->total(),
                'last_page' => $challans->lastPage(),
            ],
            'filters' => [
                'challan_series' =>  $distinctChallanSeries,
                // 'challan_series' =>  $combinedValues,
                'merged_challan_series' => $combinedValues,
                'sender_id' => $distinctSenderIds,
                'receiver_id' => $distinctReceiverIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                'status' => $distinctStatuses,
                'series_num' => $distinctChallanSeriesNum,
                // Add any other filter values here if needed
            ]
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred: ' . $e->getMessage(),
            'status_code' => 500
        ], 500);
    }
    }

    public function indexData(Request $request)
    {
        $query = Challan::query()->orderByDesc('id');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        if (!$request->has('sender_id') && !$request->has('receiver_id')) {
            // Assuming you have a logged-in user, you can get the user ID like this:
            $query->where('sender_id', $userId);
            // Fetch the distinct filter values for Challan table (for this user)
            $distinctChallanSeries = Challan::where('sender_id', $userId)->distinct()->pluck('challan_series');
            // $distinctSenderIds = Challan::where('sender_id', $userId)->distinct()->get();
            $distinctSenderIds = Challan::where('sender_id', $userId)->distinct()->pluck('sender', 'sender_id');
            // dd($distinctSenderIds );
            $distinctReceiverIds = Challan::where('sender_id', $userId)->distinct()->pluck('receiver', 'receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('city');
        }
        // Filter by challan_series
        if ($request->has('challan_series')) {
            $query->where('challan_series', $request->challan_series);
        }

        // Filter by sender_id
        if ($request->has('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

        // Filter by receiver_id
        if ($request->has('receiver_id')) {
            $query->where('receiver_id', $request->receiver_id);
            // Fetch the distinct filter values for Challan table (for this user)
            $distinctChallanSeries = Challan::where('receiver_id', $userId)->distinct()->pluck('challan_series');
            // $distinctSenderIds = Challan::where('receiver_id', $userId)->distinct()->get();
            $distinctSenderIds = Challan::where('receiver_id', $userId)->distinct()->pluck('sender', 'receiver_id');
            // dd($distinctSenderIds );
            $distinctReceiverIds = Challan::where('receiver_id', $userId)->distinct()->pluck('receiver', 'receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('city');
        }

        // Filter by deleted
        if ($request->has('deleted')) {
            $query->where('deleted', $request->deleted);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->whereHas('statuses', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Filter by state in ReceiverDetails
        if ($request->has('state')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        // Filter by city in ReceiverDetails
        if ($request->has('city')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        // Add any other desired filters

        // $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'sfp'])->paginate(200);
        // $challans = $query
        // ->with(['receiverUser', 'statuses', 'receiverDetails', 'sfp'])
        // ->paginate(200);

        $challans = $query->with(['statuses',  'sfp', 'orderDetails', 'orderDetails.columns'])->select('challans.*')->first();

        //         dd($challans);
        // return response()->json($challans, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $challans,
            'status_code' => 200,

        ], 200);
    }

    public function indexCounts(Request $request)
    {
        // dd($request->page);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = Challan::query()->where('receiver_id', $userId)->orderByDesc('id');


        $challans = $query->with(['statuses'])->get();
        return response()->json([
            'message' => 'Success',
            'data' => $challans,
            'status_code' => 200,
        ], 200);
    }

    public function sidebarCounts(Request $request)
    {
        $query = Challan::query();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        if ($request->has('receiver_id')) {
            $count = $query->where('receiver_id', $userId)
                        //    ->where('receiver_id', $request->receiver_id)
                           ->count();
        } else {
            $count = $query->where('sender_id', $userId)->count();
        }

        return response()->json([
            'message' => 'Success',
            'count' => $count,
            'status_code' => 200
        ], 200);
    }
    public function indexSfp(Request $request)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $teamUserId = Auth::guard(Auth::getDefaultDriver())->user()->id;

        $query = Challan::join('challan_sfps', 'challans.id', '=', 'challan_sfps.challan_id')->where('challan_sfps.sfp_to_id', $teamUserId);

        if (!$request->has('sender_id') && !$request->has('receiver_id')) {
            // Assuming you have a logged-in user, you can get the user ID like this:
            $query->where('sender_id', $userId);
            // Fetch the distinct filter values for Challan table (for this user)
            $distinctChallanSeries = Challan::where('sender_id', $userId)->distinct()->pluck('challan_series');
            // $distinctSenderIds = Challan::where('sender_id', $userId)->distinct()->get();
            $distinctSenderIds = Challan::where('sender_id', $userId)->distinct()->pluck('sender', 'sender_id');
            // dd($distinctSenderIds );
            $distinctReceiverIds = Challan::where('sender_id', $userId)->distinct()->pluck('receiver', 'receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('city');
        }
        // Filter by challan_series
        if ($request->has('challan_series')) {
            $query->where('challan_series', $request->challan_series);
        }

        // Filter by sender_id
        if ($request->has('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

        // Filter by receiver_id
        if ($request->has('receiver_id')) {
            $query->where('receiver_id', $request->receiver_id);
            // Fetch the distinct filter values for Challan table (for this user)
            $distinctChallanSeries = Challan::where('receiver_id', $userId)->distinct()->pluck('challan_series');
            // $distinctSenderIds = Challan::where('receiver_id', $userId)->distinct()->get();
            $distinctSenderIds = Challan::where('receiver_id', $userId)->distinct()->pluck('sender', 'receiver_id');
            // dd($distinctSenderIds );
            $distinctReceiverIds = Challan::where('receiver_id', $userId)->distinct()->pluck('receiver', 'receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('city');
        }

        // Filter by deleted
        if ($request->has('deleted')) {
            $query->where('deleted', $request->deleted);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->whereHas('statuses', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Filter by state in ReceiverDetails
        if ($request->has('state')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        // Filter by city in ReceiverDetails
        if ($request->has('city')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }
        // Add any other desired filters

        $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'sfp'])->paginate(50);

                // dd($challans);
        // return response()->json($challans, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $challans,
            'status_code' => 200,
            'filters' => [
                'challan_series' => $distinctChallanSeries,
                'sender_id' => $distinctSenderIds,
                'receiver_id' => $distinctReceiverIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
            ]
        ], 200);
    }

    public function indexDetail(Request $request)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $sentChallanDetails = Challan::rightJoin('challan_order_details', 'challans.id', '=', 'challan_order_details.challan_id')
            ->rightJoin('challan_order_columns', 'challan_order_details.id', '=', 'challan_order_columns.challan_order_detail_id')
            ->where('challans.sender_id', $userId) // Add this line to filter by sender_id
            ->latest('challans.created_at')
            ->get();

        return response()->json([
            'message' => 'Success',
            'data' => $sentChallanDetails,
            'status_code' => 200,
        ], 200);
    }


    public function indexCheckBalance(Request $request)
    {
        // dd($request->all());

        $query = Challan::query()->orderByDesc('id');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        if ($request->has('challan_series')) {
            $searchTerm = $request->challan_series;

            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                // Split the string into series and number
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                // Perform the search
                $query->where('challan_series', $series)
                      ->where('series_num', $num);
            }
        }
        // dd($request->has('from'));
         // Filter by date
        if ($request->has('from') && $request->has('to')) {
            $from = $request->from;
            $to = $request->to;

            $query->whereBetween('challan_date', [$from, $to]);
        }

        if ($request->has('recvdfrom') && $request->has('recvdto')) {
            $recvdfrom = $request->recvdfrom;
            $recvdto = $request->recvdto;
            $query->whereBetween('challan_date', [$recvdfrom, $recvdto]);
         }

         if ($request->has('status')) {
            // Subquery that gets the maximum created_at for each challan_id
            $subquery = ChallanStatus::select('challan_id', DB::raw('MAX(created_at) as max_created_at'))
                ->groupBy('challan_id');

            // Main query that joins the subquery with the challan_statuses table and filters by status
            $query->joinSub($subquery, 'latest_statuses', function ($join) {
                $join->on('challans.id', '=', 'latest_statuses.challan_id');
            })
            ->join('challan_statuses', function ($join) use ($request) {
                $join->on('challans.id', '=', 'challan_statuses.challan_id')
                    ->on('latest_statuses.max_created_at', '=', 'challan_statuses.created_at')
                    ->where('challan_statuses.status', '=', $request->status);
            });
            }
        // Filter by challan_series, sender_id, receiver_id, deleted, status, state, city, article_sent
        $filters = ['challan_series', 'sender_id', 'receiver_id', 'deleted', 'status', 'state', 'city', 'article_sent'];
        foreach ($filters as $filter) {
            if ($request->has($filter)) {
                if ($filter === 'article_sent') {
                    $query->whereHas('orderDetails.columns', function ($query) use ($request) {
                        $query->where('column_value', $request->input('article_sent'))
                            ->where('column_name', 'Article');
                    });

                }   else {
                    $query->where($filter, $request->input($filter));
                }
            }
        }



            $perPage = $request->perPage ?? 50;
            $page = $request->page ?? 1;

            $challans = $query
            ->where('sender_id', $userId)
            ->with([
                'receiverUser',
                'statuses' => function ($query) {
                    $query->whereIn('status', ['draft', 'reject']);
                },
                'receiverDetails',
                'orderDetails.columns',
                'sfp',
                'returnChallan.statuses',
                'returnChallan.orderDetails.columns'
            ])
            ->select('challans.*')
            ->paginate(50);

            // Calculate the starting item number for the current page
            $startItemNumber = ($page - 1) * $perPage + 1;

            // Add a custom attribute to each item in the collection with the calculated item number
            $challans->each(function ($item) use (&$startItemNumber) {
                $item->setAttribute('custom_item_number', $startItemNumber++);
            });


        // Fetch the distinct filter values for Challan table (for this user)
        $distinctChallanSeries = Challan::where('sender_id', $userId)->distinct('challan_series')->pluck('challan_series');
        $distinctChallanSeriesNum = Challan::where('sender_id', $userId)->distinct()->pluck('series_num');

        // Loop through each element of $distinctChallanSeries
        foreach ($distinctChallanSeries as $series) {
            // Loop through each element of $distinctChallanSeriesNum
            foreach ($distinctChallanSeriesNum as $num) {
                // Combine the series and number and push it into the combinedValues array
                $combinedValues[] = $series . '-' . $num;
            }
        }

        $distinctSenderIds = Challan::where('sender_id', $userId)->distinct('sender_id')->pluck('sender', 'sender_id');
        $distinctReceiverIds = Challan::where('sender_id', $userId)->distinct('receiver_id')->pluck('receiver', 'receiver_id');

        // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
        $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            $query->select('id')->from('receivers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            $query->select('id')->from('receivers')->where('user_id', $userId);
        })->distinct()->pluck('city');


        $distinctSentArticles = Challan::with('orderDetails.columns')
        ->where('sender_id', $userId)
        ->whereHas('orderDetails', function ($query) {
            $query->whereHas('columns', function ($query) {
                $query->where('column_name', 'Article');
            });
        })
        ->get()
        ->flatMap(function ($challan) {
            return $challan->orderDetails->flatMap(function ($orderDetail) {
                return $orderDetail->columns->where('column_name', 'Article')->pluck('column_value');
            });
        })
        ->unique()
        ->values();

        // Fetch distinct received articles
        $distinctReceivedArticles = Challan::with('returnChallan.orderDetails.columns')
        ->where('sender_id', $userId)
        ->whereHas('returnChallan.orderDetails', function ($query) {
            $query->whereHas('columns', function ($query) {
                $query->where('column_name', 'Article');
            });
        })
        ->get()
        ->pluck('returnChallan.*.orderDetails.*.columns.*.column_value')
        ->flatten()
        ->unique()
        ->values();

        // Merge all the arrays into a single array
        $organizedData = collect(); // Using Laravel collection for easier merging
        $totalBalance = 0;

        $chunks = $challans->chunk(10); // Split into chunks for parallel processing

        $pool = Pool::create();

        foreach ($chunks as $chunk) {
            $pool->add(function () use ($chunk, &$totalBalance) {
                $chunkData = [];
                foreach ($chunk as $challan) {
                    $challanStatus = optional($challan->statuses->first())->status;
                    if (in_array($challanStatus, ['draft', 'reject'])) {
                        continue;
                    }

                    $receiver = $challan->receiver;
                    $sentDate = $challan->created_at->format('Y-m-d');
                    $challanNo = $challan->challan_series . '-' . $challan->series_num;

                    foreach ($challan->orderDetails as $orderDetail) {
                        $articleSent = optional($orderDetail->columns->first())->column_value;
                        $qtySent = $orderDetail->qty;
                        $balance = $orderDetail->remaining_qty;
                        $totalBalance += $balance;

                        $organizedDataItem = [
                            'Challan Id' => $orderDetail->id,
                            'Receiver' => $receiver,
                            'Sent Date' => $sentDate,
                            'Challan No.' => $challanNo,
                            'Article' => $articleSent,
                            'QTY Sent' => $qtySent,
                            'Challan Status' => $challanStatus,
                            'Balance' => $balance,
                        ];

                        $hasReturnChallan = false;

                        foreach ($challan->returnChallan as $returnChallan) {
                            $recvdChallanNo = $returnChallan->challan_series . '-' . $returnChallan->series_num;
                            $returnChallanStatus = optional($returnChallan->statuses->first())->status;

                            foreach ($returnChallan->orderDetails as $returnOrderDetail) {
                                $returnArticle = optional($returnOrderDetail->columns->first())->column_value;
                                if ($articleSent !== $returnArticle) {
                                    continue;
                                }

                                $recvdDate = $returnOrderDetail->created_at->format('Y-m-d');
                                if (request()->has('recvdfrom') && request()->has('recvdto')) {
                                    if ($recvdDate < request()->recvdfrom || $recvdDate > request()->recvdto) {
                                        continue;
                                    }
                                }

                                $recvdQty = $returnOrderDetail->qty;

                                $returnDataItem = $organizedDataItem; // Copy the original data
                                $returnDataItem['Recvd Challan No.'] = $recvdChallanNo;
                                $returnDataItem['RecvArticle'] = $returnArticle;
                                $returnDataItem['Recvd Date'] = $recvdDate;
                                $returnDataItem['Recvd QTY'] = $recvdQty;
                                $returnDataItem['Return Challan Status'] = $returnChallanStatus;
                                $returnDataItem['Margin QTY'] = $orderDetail->margin;
                                $returnDataItem['Balance'] = $balance;
                                $returnDataItem['Action'] = '';

                                $chunkData[] = $returnDataItem;
                                $hasReturnChallan = true;
                            }
                        }

                        if (!$hasReturnChallan) {
                            $chunkData[] = $organizedDataItem;
                        }
                    }
                }
                return $chunkData;
            })->then(function ($chunkData) use (&$organizedData) {
                $organizedData = $organizedData->merge($chunkData);
            });
        }

        $pool->wait(); // Wait for all tasks to finish

        // Convert organized data back to array if necessary
        $organizedDataArray = $organizedData->all();

        // dd($organizedDataArray);
        return response()->json([
            'message' => 'Success',
            'data' => $organizedDataArray,
            'challans' => $challans,
            'totalBalance' => $totalBalance,
            'status_code' => 200,
            'filters' => [
                'challan_series' => $distinctChallanSeries,
                'received_article' => $distinctReceivedArticles,
                'article_sent' => $distinctSentArticles,
                'data' => $organizedData,
                'merged_challan_series' => $combinedValues,
                'series_num' => $distinctChallanSeriesNum,
                'sender_id' => $distinctSenderIds,
                'receiver_id' => $distinctReceiverIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
            ]
        ], 200);
    }

    public function acceptMargin(Request $request, $id)
    {
        $columnName = ChallanOrderDetail::find($id);
        if (!$columnName) {
            return response()->json(['success' => false, 'message' => 'Column not found'], 404);
        }
        // Store the last remaining_qty value in the balance field
        $columnName->margin = $columnName->remaining_qty;
        // Set remaining_qty to 0
        $columnName->remaining_qty = 0;
        $columnName->save();
        return response()->json([
            'success' => true,
            'status_code' => 200,
        ]);
    }


    public function show(Request $request, $id)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Fetch the challan by ID (for this user)
        $challan = Challan::where('sender_id', $userId)->find($id);
        // Load related data
        $challan->load(['orderDetails.columns', 'statuses', 'receiverUser', 'returnChallan.orderDetails', 'senderUser']);
        // dd($challan);
        if (!$challan) {
            return response()->json([
                'data' => null,
                'message' => 'Challan not found',
                'status_code' => 200,
            ], 200);
        }

        // Return the response
        return response()->json([
            'message' => 'Success',
            'data' => $challan,
            'status_code' => 200,
        ], 200);
    }

    public function sfpAccept(Request $request, $sfpId)
    {
        try {

            $sfp = ChallanSfp::where('id', $sfpId)->update(['status' => 'accept']);
            if ($sfp) {
                return response()->json([
                    'message' => 'SFP Accepted successfully.',
                    'status_code' => 200
                ], 200);
            } else {
                return response()->json([
                    'errors' => 'SFP Not accepted.',
                    'status_code' => 500
                ], 500);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'SFP Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function sfpReject(Request $request, $sfpId)
    {
        try {

            $sfp = ChallanSfp::where('id', $sfpId)->update(['status' => 'reject']);
            if ($sfp) {
                return response()->json([
                    'message' => 'SFP Rejected successfully.',
                    'status_code' => 200
                ], 200);
            } else {
                return response()->json([
                    'errors' => 'SFP Not rejected.',
                    'status_code' => 500
                ], 500);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'SFP Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function accept(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            $challan = Challan::where('id', $challanId)
                ->with('receiverUser', 'receiverDetails', 'userDetails', 'senderUser', 'orderDetails', 'orderDetails.columns', 'statuses')
                ->first();

            // Check if the Challan exists
            if (!$challan) {
                return response()->json([
                    'message' => 'Challan Not Found.',
                    'status_code' => 400
                ], 400);
            }

            if ($request->status_comment && trim($request->status_comment) != '') {
                // Get the existing status_comment data
                $statusComment = json_decode($challan->status_comment, true);

                // Add the new comment to the status_comment data
                $statusComment[] = [
                    'comment' => $request->status_comment,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                ];

                // Update the status_comment field with the combined data
                $challan->update(['status_comment' => json_encode($statusComment)]);
            }

            // Check if senderUser and receiverUser exist
            if ($challan->senderUser && $challan->receiverUser) {
                // Show Notifications in Status
                $notification = new Notification([
                    'user_id' => $challan->senderUser->id,
                    'message' => 'Challan is Accepted by ' . $challan->receiverUser->name,
                    'type' => 'challan',
                    'added_id' => $challan->id,
                    'panel' => 'sender',
                    'template_name' => 'sent_challan',
                ]);
                $notification->save();
            }

            // Update the status of the Challan to "accepted"
            $challan->statuses()->create([
                'challan_id' => $challan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                'status' => 'accept',
                'comment' => 'Challan accepted',
            ]);

            if ($challan->senderUser && $challan->senderUser->email != null) {
                $pdfEmailService = new PDFEmailService();
                $recipientEmail = $challan->senderUser->email; // Replace with the actual recipient email address
                $pdfEmailService->acceptChallanByEmail($challan, $challan->pdf_url, $recipientEmail);
            }

            $sfpExists = ChallanSfp::where('challan_id', $challanId)->exists();

            if ($sfpExists) {
                $challanSfp = new ChallanSfp([
                    'challan_id' => $challanId,
                    'sfp_by_id' => Auth::user()->id,
                    'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
                    'sfp_to_id' => null,
                    'sfp_to_name' => null,
                    'status' => 'accept',
                    'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
                ]);

                $challanSfp->save();
            }
            // Return a response indicating success
            return response()->json([
                'data' => $challan->statuses,
                'message' => 'Challan accepted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Challan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function selfAccept(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            $challan = Challan::findOrFail($challanId);

            // Update the status of the Challan to "self-accepted"
            $challan->statuses()->create([
                'challan_id' => $challan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                'status' => 'self_accept',
                'comment' => 'Challan self accepted',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $challan->statuses,
                'message' => 'Challan self delivered.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Challan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function selfReturn(Request $request, $challanId)
    {
        $validator = Validator::make($request->all(), [
            'challan_series' => 'required|string',
            'challan_date' => 'required',
            'receiver_id' => 'required|exists:users,id',
            'receiver' => 'required|string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            'order_details.*.unit' => 'nullable|string',
            'order_details.*.rate' => 'nullable|numeric|min:0',
            'order_details.*.qty' => 'nullable|integer|min:0',
            'order_details.*.details' => 'nullable|string',
            'order_details.*.total_amount' => 'nullable|numeric|min:0',
            'order_details.*.columns.*.column_name' => 'nullable|string',
            'order_details.*.columns.*.column_value' => 'nullable|string',
            'statuses.*.comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $currentFinancialYearStart = now()->startOfYear()->month(4)->day(1);
        $nextFinancialYearEnd = now()->startOfYear()->addYear(1)->month(3)->day(31);

        if (now() < $currentFinancialYearStart) {
            $currentFinancialYearStart = $currentFinancialYearStart->subYear();
            $nextFinancialYearEnd = $nextFinancialYearEnd->subYear();
        }

        $financialYear = ($currentFinancialYearStart->year % 100) . '-' . ($nextFinancialYearEnd->year % 100);
        $companyName = Auth::user()->company_name ?? Auth::user()->name;
        $name = strtoupper(str_replace(' ', '', substr($companyName, 0, 4)));
        $series = 'RT-' . $name . '-' . $financialYear;

        $user = Auth::getDefaultDriver() == 'team-user'
            ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
            : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $latestSeriesNum = ReturnChallan::where('challan_series', $series)
            ->where('receiver_id', $user)
            ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

        $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

        $returnChallan = new ReturnChallan([
            'challan_id' => $challanId,
            'challan_series' => $series,
            'challan_date' => now()->format('Y-m-d H:i:s'),
            'series_num' => $seriesNum,
            'sender_id' => $request->receiver_id,
            'sender' => Auth::guard('web')->check()
                ? Auth::guard('web')->user()->name
                : (Auth::guard('user')->check()
                    ? Auth::guard('user')->user()->name
                    : Auth::guard('team-user')->user()->user->name),
            'receiver_id' => $user,
            'receiver' => Auth::guard('web')->check()
                ? Auth::guard('web')->user()->name
                : (Auth::guard('user')->check()
                    ? Auth::guard('user')->user()->name
                    : Auth::guard('team-user')->user()->user->name),
            'comment' => $request->comment,
            'total' => isset($request->total) ? (float) $request->total : 0.00,
            'total_qty' => $request->total_qty ?? 0,
        ]);
        $returnChallan->save();

        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $challanOrderDetail = ChallanOrderDetail::find($orderDetailData['id']);
                if ($challanOrderDetail) {
                    $orderDetail = new ReturnChallanOrderDetail([
                        'challan_id' => $returnChallan->id,
                        'sender_challan_id' => $orderDetailData['challan_id'],
                        'unit' => $orderDetailData['unit'] ?? null,
                        'rate' => $orderDetailData['rate'] ?? null,
                        'qty' => $orderDetailData['qty'] ?? null,
                        'details' => $orderDetailData['details'],
                        'challan_order_detail_id' => $challanOrderDetail->id,
                        'total_amount' => $orderDetailData['total_amount'] ?? null,
                    ]);
                    $orderDetail->save();

                    if (isset($orderDetailData['columns'])) {
                        foreach ($orderDetailData['columns'] as $columnData) {
                            $orderColumn = new ReturnChallanOrderColumn([
                                'challan_order_detail_id' => $orderDetail->id,
                                'column_name' => $columnData['column_name'] ?? '',
                                'column_value' => $columnData['column_value'] ?? '',
                            ]);
                            $orderColumn->save();
                        }
                    }

                    // Reduce the `remaining_qty`
                    $challanOrderDetail->remaining_qty -= $orderDetailData['qty'];
                    $challanOrderDetail->save();
                }
            }
        }

        $challan = Challan::findOrFail($challanId);
        $challan->load('orderDetails');

        $partiallySelfReturn = false;
        foreach ($challan->orderDetails as $challanOrderDetail) {
            if ($challanOrderDetail->remaining_qty > 0) {
                $partiallySelfReturn = true;
                break;
            }
        }

        $statusValue = $partiallySelfReturn ? 'partially_self_return' : 'self_return';
        $comment = $partiallySelfReturn ? 'Partially self Return' : 'Self Return';

        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new ReturnChallanStatus([
                    'challan_id' => $returnChallan->id,
                    'user_id' => $user,
                    'user_name' => Auth::guard('web')->check()
                        ? Auth::guard('web')->user()->name
                        : (Auth::guard('user')->check()
                            ? Auth::guard('user')->user()->name
                            : Auth::guard('team-user')->user()->user->name),
                    'status' => trim($statusValue),
                    'comment' => 'Return Challan Successfully',
                ]);
                $status->save();
            }
        }

        $challan->statuses()->create([
            'challan_id' => $challanId,
            'user_id' => $user,
            'user_name' => Auth::guard('web')->check()
                ? Auth::guard('web')->user()->name
                : (Auth::guard('user')->check()
                    ? Auth::guard('user')->user()->name
                    : Auth::guard('team-user')->user()->user->name),
            'status' => trim($statusValue),
            'comment' => 'Return Challan Successfully',
        ]);

        $returnChallan = ReturnChallan::where('id', $returnChallan->id)
            ->with('receiverUser', 'receiverDetails', 'senderUser', 'orderDetails', 'orderDetails.columns', 'statuses')
            ->first();

        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateSelfReturnChallanPDF($returnChallan);

        $response = (array) $response->getData();
        if ($response['status_code'] === 200) {
            $returnChallan->pdf_url = $response['pdf_url'];
            $returnChallan->save();
        }

        return response()->json([
            'message' => 'Return Challan created successfully.',
            'challan_id' => $returnChallan->id,
            'status_code' => 200,
        ], 200);
    }


    public function reject(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            // $challan = Challan::findOrFail($challanId);
            $challan = Challan::where('id', $challanId)->with('receiverUser', 'receiverDetails', 'userDetails', 'senderUser', 'orderDetails', 'orderDetails.columns',  'statuses')->first();

            // dd($challan);
            // Update the status of the Challan to "rejected"
            $challan->statuses()->create([
                'challan_id' => $challan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                'status' => 'reject',
                'comment' => 'Challan rejected',
            ]);

            // If the challan has order details and item_code is present
            if ($challan->orderDetails) {
                foreach ($challan->orderDetails as $orderDetail) {
                    if (isset($orderDetail->item_code)) {
                        // Find the product with the item_code
                        $product = Product::where('item_code', $orderDetail->item_code)->first();

                        // If the product exists, add back the quantity
                        if ($product) {
                            $product->qty += $orderDetail->qty;
                            $product->save();
                        }
                    }
                }
            }
             // Show Notifications in Status
             $notification = new Notification([
                'user_id' => $challan->senderUser->id,
                'message' => 'Challan is Rejected by ' . $challan->receiverUser->name,
                'type' => 'challan',
                'added_id' => $challan->id,
                'panel' => 'sender',
                'template_name' => 'sent_challan',
            ]);
            $notification->save();
            if ($challan->senderUser->email != null) {
                $pdfEmailService = new PDFEmailService();
                $recipientEmail = $challan->senderUser->email; // Replace with the actual recipient email address
                // dd($recipientEmail);
                $pdfEmailService->rejectChallanByEmail($challan, $challan->pdf_url, $recipientEmail);
                // dd($pdfEmailService->sendChallanByEmail($challan, $response['pdf_url'], $recipientEmail));
            }


            if($request->status_comment && trim($request->status_comment) != ''){
                // Get the existing status_comment data
                $statusComment = json_decode($challan->status_comment, true);

                // Add the new comment to the status_comment data
                $statusComment[] = [
                    'comment' => $request->status_comment,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                ];

                // Update the status_comment field with the combined data
                $challan->update(['status_comment' => json_encode($statusComment)]);
            }

            $sfpExists = ChallanSfp::where('challan_id', $challanId)->exists();

            if ($sfpExists) {
            $challanSfp = new ChallanSfp(
                [
                    'challan_id' => $challanId,
                    'sfp_by_id' => Auth::user()->id,
                    'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
                    'sfp_to_id' => null,
                    'sfp_to_name' => null,
                    'status' => 'reject',
                    'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
                ]
            );
            $challanSfp->save();
            }


            // Return a response indicating success
            return response()->json([
                'data' => $challan->statuses,
                'message' => 'Challan rejected successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Challan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }
    public function selfReject(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            $challan = Challan::findOrFail($challanId);

            // Update the status of the Challan to "self_reject"
            $challan->statuses()->create([
                'challan_id' => $challan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                'status' => 'reject',
                'comment' => 'Challan self rejected',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $challan->statuses,
                'message' => 'Challan self rejected successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Challan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function delete(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            $challan = Challan::findOrFail($challanId);

            // Update the status of the Challan to "deleted"
            $challan->statuses()->create([
                'challan_id' => $challan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                'team_user_name' => Auth::guard(Auth::getDefaultDriver())->user()->team_user_name ?? '',
                'status' => 'deleted',
                'comment' => 'Challan deleted',
            ]);

            $challan->delete();

            // Return a response indicating success
            return response()->json([
                'data' => $challan->statuses,
                'message' => 'Challan deleted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Challan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function forceDelete(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            $challan = Challan::findOrFail($challanId);

            $challan->forceDelete();

            // Return a response indicating success
            return response()->json([
                'message' => 'Challan permanently deleted.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Challan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }
    public function deletedChallan(Request $request)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $sentChallanDetails = Challan::rightJoin('challan_order_details', 'challans.id', '=', 'challan_order_details.challan_id')
            ->rightJoin('challan_order_columns', 'challan_order_details.id', '=', 'challan_order_columns.challan_order_detail_id')
            ->where('challans.sender_id', $userId)
            ->onlyTrashed()
            ->select('*')
            ->get();
        return response()->json([
            'message' => 'Success',
            'data' => $sentChallanDetails,
            'status_code' => 200,
        ], 200);;
    }

    public function senderDetails($id)
    {
        $query = User::query();
        $query->where('id', $id);

        // Get filtered Sender
        $Sender = $query->with('seriesNumber')->first();
        // dd($Sender);
        return response()->json([
            'data' => $Sender,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function getSender(Request $request)
    {
        $user = Auth::guard(Auth::getDefaultDriver())->user();

        $senderList = Challan::where('receiver_id', $user->id)
        ->with('orderDetails', 'statuses', 'orderDetails.columns')
        ->distinct()
            ->get();
            // dd($senderList);
        $responseData = [
            'message' => 'Sender Details.',
            'sender_list' => $senderList,
            'status_code' => 200,
        ];

        return response()->json($responseData, 200);
    }

    public function getSenderDataForSeries(Request $request)
    {
       $user = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // dd($user);
       $senderList = Receiver::join('challans', 'challans.receiver_id', '=', 'receivers.receiver_user_id')
       ->join('users', 'challans.sender_id', '=', 'users.id')
       ->where('receivers.receiver_user_id', '=', $user)
       ->select('users.name as sender', 'challans.sender_id', 'receivers.receiver_user_id', 'users.company_name')
       ->distinct()
       ->groupBy('challans.sender_id', 'sender', 'receivers.id')
       ->with('seriesNumber') // Group by sender_id, sender, and receiver id
       ->get();

       $responseData = [
           'message' => 'Sender Details.',
           'sender_list' => $senderList,
           'status_code' => 200,
       ];


       return response()->json($responseData, 200);
    }
    // Export Sent Challan

    public function exportChallan(Request $request)
    {

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = Challan::query()->orderByDesc('id');
        $combinedValues = [];
        if (!$request->has('sender_id') && !$request->has('receiver_id')) {
            // Assuming you have a logged-in user, you can get the user ID like this:
            $query->where('sender_id', $userId);
            // Fetch the distinct filter values for Challan table (for this user)
            $distinctChallanSeries = Challan::where('sender_id', $userId)->distinct()->pluck('challan_series');
            $distinctChallanSeriesNum = Challan::where('sender_id', $userId)->distinct()->pluck('series_num');
           // Initialize an empty array to store the combined values


            // Loop through each element of $distinctChallanSeries
            foreach ($distinctChallanSeries as $series) {
                // Loop through each element of $distinctChallanSeriesNum
                foreach ($distinctChallanSeriesNum as $num) {
                    // Combine the series and number and push it into the combinedValues array
                    $combinedValues[] = $series . '-' . $num;
                }
            }
            // dd($combinedValues);

            // $distinctSenderIds = Challan::where('sender_id', $userId)->distinct()->get();
            $distinctSenderIds = Challan::where('sender_id', $userId)->distinct()->pluck('sender', 'sender_id');
            // dd($distinctSenderIds );
            $distinctReceiverIds = Challan::where('sender_id', $userId)->distinct()->pluck('receiver', 'receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');
            $distinctStatuses = ChallanStatus::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('city');
        }

        if ($request->challan_series != null) {

            $searchTerm = $request->challan_series;

            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                // Split the string into series and number
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                // Perform the search
                $query->where('challan_series', $series)
                      ->where('series_num', $num);
            } else {
                // Invalid search term format, handle accordingly
                // For example, you could return an error message or ignore the filter
            }
        }


        // Filter by sender_id
        if ($request->has('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

         // Filter by date
         if ($request->from_date && $request->to_date) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();

            $query->whereBetween('challan_date', [$from, $to]);
        }

        // Filter by receiver_id
        // dd($request);
        if ($request->receiver_id != null) {
            // dd('adf');
            $query->where('receiver_id', $request->receiver_id);

            // Fetch the distinct filter values for Challan table (for this user)
            $distinctChallanSeries = Challan::where('receiver_id', $userId)->distinct()->pluck('challan_series');
            $distinctChallanSeriesNum = Challan::where('receiver_id', $userId)->distinct()->pluck('series_num');
            // $distinctSenderIds = Challan::where('receiver_id', $userId)->distinct()->get();
            $distinctSenderIds = Challan::where('receiver_id', $userId)->distinct()->pluck('sender', 'receiver_id');
            // dd($distinctSenderIds );
            $distinctReceiverIds = Challan::where('receiver_id', $userId)->distinct()->pluck('receiver', 'receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            $distinctStatuses = ChallanStatus::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('city');
        }
        // Fetch the distinct filter values for status

            // Filter by status
            if ($request->has('status')) {
                $query->whereHas('statuses', function ($q) use ($request) {
                    $q->latest()->where('status', $request->status);
                });
            }
            // Filter by status


            if ($request->has('status')) {
                // Subquery that gets the maximum created_at for each challan_id
                $subquery = ChallanStatus::select('challan_id', DB::raw('MAX(created_at) as max_created_at'))
                    ->groupBy('challan_id');

                // Main query that joins the subquery with the challan_statuses table and filters by status
                $query->joinSub($subquery, 'latest_statuses', function ($join) {
                    $join->on('challans.id', '=', 'latest_statuses.challan_id');
                })
                ->join('challan_statuses', function ($join) use ($request) {
                    $join->on('challans.id', '=', 'challan_statuses.challan_id')
                        ->on('latest_statuses.max_created_at', '=', 'challan_statuses.created_at')
                        ->where('challan_statuses.status', '=', $request->status);
                });
            }
        // Filter by deleted
        if ($request->has('deleted')) {
            $query->where('deleted', $request->deleted);
        }


        // Filter by state in ReceiverDetails
        if ($request->has('state')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        // Filter by city in ReceiverDetails
        if ($request->has('city')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

            $challans = $query
                ->with([ 'statuses', 'orderDetails',  'sfp', 'statuses', 'receiverDetails'])
                ->select('challans.*')
                ->get();
        // Create an array to store the exported data
        $exportedData = [];

        // Iterate through the products and their related product details
        foreach ($challans as $key => $challan) {
            $rowData['id'] =  ++$key;

            // foreach ($challan as $productDetail) {
            //     $rowData[$productDetail->column_name] = $productDetail->column_value;
            // }
            // $rowData['Time'] = $challan->(date('h:i A', strtotime($challan->created_at)));


            $rowData['challan_series'] = $challan->challan_series;
            $rowData['Time'] = Carbon::parse($challan->created_at)->format('h:i A');
            $rowData['Date'] = Carbon::parse($challan->created_at)->format('j F Y');
            // $rowData['Date'] = $challan->(date('j F Y', strtotime($challan->created_at)));
            $rowData['sender'] = $challan->sender;
            $rowData['receiver'] = $challan->receiver;
            // $rowData['item_code'] = $challan->item_code;
            // $rowData['unit'] = $challan->unit;
            // $rowData['rate'] = $challan->rate;
            $rowData['qty'] = $challan->total_qty;
            $rowData['total_amount'] = $challan->total;
                // dd($challan->receiverDetails);

            // $rowData['status'] = $challan->statuses[0]->status;
            $rowData['status'] = '';

        if ($challan->statuses->isNotEmpty()) {
            $status = $challan->statuses[0]->status;
            $user_name = $challan->statuses[0]->user_name;

            if ($status == 'draft') {
                $rowData['status'] = 'Not Sent';
            } elseif ($status == 'sent') {
                $rowData['status'] = 'Sent';
            } elseif ($status == 'self_accept') {
                $rowData['status'] = 'Self Delivered';
            } elseif ($status == 'accept') {
                $rowData['status'] = 'Accepted By ' . $user_name;
            } elseif ($status == 'reject') {
                $rowData['status'] = 'Rejected By ' . $user_name;
            } elseif ($status == 'return') {
                $rowData['status'] = 'Self Returned';
            }
        }

            $rowData['comment'] = $challan->comment;


            $exportedData[] = $rowData;
        }

        // Create a temporary file path for the CSV
        $filePath = 'temp/' . uniqid() . '.csv';

        // Store the CSV file using Laravel Storage
        Storage::disk('local')->put($filePath, $this->generateCsvFile($exportedData));

        // Define the file name and content type
        $fileName = 'exported_challans.csv';
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
    // Export DEtailed Sent Challan
    public function exportDetailedChallan(Request $request)
    {
        // Fetch the products and their related product details
        // $products = Challan::with('details')->get();
        $query = Challan::query()->orderByDesc('id');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails','orderDetails', 'sfp'])->select('challans.*')->paginate(100,null,null,$request->page??1);
        $challans = $query->where('sender_id', $userId)->with('receiverUser', 'statuses', 'receiverDetails','orderDetails','orderDetails.columns')->select('challans.*')->paginate(100,null,null,$request->page??1);

        // dd($challans->orderDetails);

        // Create an array to store the exported data
        $exportedData = [];

        // Iterate through the products and their related product details
        foreach ($challans as $key => $challan) {
            $rowData['id'] =  ++$key;

            $rowData['challan_series'] = $challan->challan_series;
            $rowData['Time'] = Carbon::parse($challan->created_at)->format('h:i A');
            $rowData['Date'] = Carbon::parse($challan->created_at)->format('j F Y');
            // $rowData['Date'] = $challan->(date('j F Y', strtotime($challan->created_at)));
            $rowData['sender'] = $challan->sender;
            $rowData['receiver'] = $challan->receiver;


            if ($challan->column !== null && is_iterable($challan->column)) {
                foreach ($challan->column as $index => $col) {
                    if ($index == 0) {
                        // Use the value for the first occurrence of column index 0
                        $rowData['article'] = $col->column_value;
                    } elseif ($index == 1) {
                        // Use the value for the first occurrence of column index 1
                        $rowData['hsn'] = $col->column_value;
                        break; // Stop the loop after the first occurrence of column index 1
                    }
                }
            } else {
                // Handle the case where $challan->column is null or not iterable
                // This could be setting default values, logging an error message, throwing an exception, etc.
            }



           // Initialize arrays for order details
           $rowData['unit'] = [];
           $rowData['qty'] = [];
           $rowData['unit_price'] = [];
           $rowData['total_amount'] = [];

           foreach ($challan->orderDetails as $orderDetail) {
               // Create a new row for each order detail
               $detailRow = $rowData;

               // Add details to the row
               $detailRow['unit'] = $orderDetail->unit ?? '';
               $detailRow['qty'] = $orderDetail->qty ?? '';
               $detailRow['unit_price'] = $orderDetail->rate ?? '';
               $detailRow['total_amount'] = $orderDetail->total_amount ?? '';

               // Add the row to the exported data
               $exportedData[] = $detailRow;
           }
           // Convert arrays to strings
           $rowData['unit'] = implode(',', $rowData['unit']);
           $rowData['qty'] = implode(',', $rowData['qty']);
           $rowData['unit_price'] = implode(',', $rowData['unit_price']);
           $rowData['total_amount'] = implode(',', $rowData['total_amount']);
       }

        // Create a temporary file path for the CSV
        $filePath = 'temp/' . uniqid() . '.csv';

        // Store the CSV file using Laravel Storage
        Storage::disk('local')->put($filePath, $this->generateCsvFile($exportedData));

        // Define the file name and content type
        $fileName = 'exported_challans.csv';
        $contentType = 'text/csv';

        // Create a CSV response
        $response = new Response();
        $response->header('Content-Type', $contentType);
        $response->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->setCharset('UTF-8');

        // Get the CSV content from the stored file and add it to the response
        $response->setContent(Storage::disk('local')->get($filePath));
    //    dd($response);
        // Delete the temporary CSV file after downloading
        Storage::disk('local')->delete($filePath);

        return $response;
    }


    private function generateCsvFile($data)
    {
        $handle = fopen('php://temp', 'w+');

        // Check if $data is not empty
        if (!empty($data)) {
            fputcsv($handle, array_keys($data[0])); // Write the header row
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
        }

        rewind($handle);

        return stream_get_contents($handle);
    }


    public function exportCheckBalanceChallan(Request $request)
    {
        // dd('exportCheckBalanceChallan');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $query = Challan::query()->orderByDesc('id')->where('sender_id', $userId);

        // Filter by challan_series, sender_id, receiver_id, deleted, status, state, city
        $filters = ['challan_series', 'sender_id', 'receiver_id', 'deleted', 'status', 'state', 'city'];
        foreach ($filters as $filter) {
            if ($request->has($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        // Eager load the related data
        $challans = $query->with('receiverUser', 'statuses', 'receiverDetails','orderDetails','orderDetails.columns')->select('challans.*')->paginate(100,null,null,$request->page??1);

        // Create an array to store the exported data
        $exportedData = [];
        // dd($challans);
        // Iterate through the products and their related product details
        $organizedData = [];
        $recvdChallanNo = '';
        $articleRecvd = '';
        $recvdDate = '';
        $recvdQty = '';
        // $marginQty = '';

        $returnChallanStatus = '';
        // Iterate through the data and organize it into rows
        foreach ($challans as $challan) {
            $challanStatus = $challan->statuses->first() ? $challan->statuses->first()->status : '';
            // dd($challanStatus);
            $receiver = $challan->receiver;
            $sentDate = date('Y-m-d', strtotime($challan->created_at));
            $challanNo = $challan->challan_series . '-' . $challan->series_num;

            $orderDetailsCount = count($challan->orderDetails);
            $currentOrderDetailIndex = 0;

            foreach ($challans as $challan) {
                $challanStatus = $challan->statuses->first() ? $challan->statuses->first()->status : '';
                // dd($challanStatus);
                $receiver = $challan->receiver;
                $sentDate = date('Y-m-d', strtotime($challan->created_at));
                $challanNo = $challan->challan_series . '-' . $challan->series_num;

                $orderDetailsCount = count($challan->orderDetails);
                $currentOrderDetailIndex = 0;
                $totalBalance = 0; // Initialize the total balance
                foreach ($challan->orderDetails as $orderDetail) {
                    $currentOrderDetailIndex++;
                    $articleSent = $orderDetail->columns->first() ? $orderDetail->columns->first()->column_value : '';
                    $qtySent = $orderDetail->qty;
                    $balance = $orderDetail->remaining_qty;
                    $totalBalance += $balance; // Add the balance to the total

                    $returnChallanCount = count($challan->returnChallan);
                    $currentReturnChallanIndex = 0;

                    $organizedDataItem = [
                        'Challan Id' => $orderDetail->id,
                        'Receiver' => $receiver,
                        'Sent Date' => $sentDate,
                        'Challan No.' => $challanNo,
                        'Article' => $articleSent,
                        'QTY Sent' => $qtySent,
                        'Challan Status' => $challanStatus,
                        'Recvd Challan No.' => null,
                        'RecvArticle' => null,
                        'Recvd Date' => null,
                        'Recvd QTY' => null,
                        'Return Challan Status' => null,
                        'Margin QTY' => null,
                        'Balance' => null,

                    ];

                    $organizedData[] = $organizedDataItem;

                    foreach ($challan->returnChallan as $returnChallan) {
                        $currentReturnChallanIndex++;
                        $recvdChallanNo = $returnChallan->challan_series . '-' . $returnChallan->series_num;
                        $returnChallanStatus = $returnChallan->statuses->first() ? $returnChallan->statuses->first()->status : '';

                        foreach($returnChallan->orderDetails as $returnOrderDetail){
                            if ($returnOrderDetail && $returnOrderDetail->columns->first() && $articleSent == $returnOrderDetail->columns->first()->column_value) {
                                $articleRecvd = $returnOrderDetail->columns->first()->column_value;
                                $recvdDate = date('Y-m-d', strtotime($returnOrderDetail->created_at));
                                $recvdQty = $returnOrderDetail->qty;

                                $returnDataItem = $organizedDataItem; // Copy the original data
                                $returnDataItem['Recvd Challan No.'] = $recvdChallanNo;
                                $returnDataItem['RecvArticle'] = $articleRecvd;
                                $returnDataItem['Recvd Date'] = $recvdDate;
                                $returnDataItem['Recvd QTY'] = $recvdQty;
                                $returnDataItem['Return Challan Status'] = $returnChallanStatus;
                                $returnDataItem['Margin QTY'] = $currentReturnChallanIndex == $returnChallanCount ? $orderDetail->margin : null;
                                $returnDataItem['Balance'] = $currentReturnChallanIndex == $returnChallanCount ? $balance : null;
                                $returnDataItem['Action'] = '';


                                $organizedData[] = $returnDataItem; // Add the return data to the array
                            }
                        }
                    }
                }
            }

        // Create a temporary file path for the CSV
        $filePath = 'temp/' . uniqid() . '.csv';

        // Store the CSV file using Laravel Storage
        Storage::disk('local')->put($filePath, $this->generateCsvFile($organizedData));

        // Define the file name and content type
        $fileName = 'check_balance.csv';
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
}
        public function addComment(Request $request, $challanIds)
    {
        try {
            $permissions = json_decode(Auth::user()->permissions, true);
            $successCount = 0;
            $totalCount = 0;

            // Convert single ID to array if necessary
            $challanIds = is_array($challanIds) ? $challanIds : [$challanIds];

            foreach ($challanIds as $challanId) {
                $totalCount++;

                // Find the Challan by ID
                $challan = Challan::findOrFail($challanId);
                $challan = $challan->load('receiverUser', 'senderUser');

                if ($request->has('receiver')) {
                    $receiverUserEmail = $challan->senderUser ? $challan->senderUser->email : null;
                    $phone = $challan->senderUser->phone;
                    $senderUser = $challan->receiverUser->name;
                    $challanNo = $challan->challan_series . '-' . $challan->series_num;
                    $pdfUrl = $challan->pdf_url;
                    $heading = 'Challan';

                    // Show Notifications in Status
                    $notification = new Notification([
                        'user_id' => $challan->senderUser->id,
                        'message' => 'New Comment added by ' . $challan->receiverUser->name,
                        'type' => 'challan',
                        'added_id' => $challan->id,
                        'panel' => 'sender',
                        'template_name' => 'sent_challan',
                    ]);
                    $notification->save();

                } elseif ($request->has('sender')) {
                    $receiverUserEmail = $challan->receiverUser ? $challan->receiverUser->email : null;
                    $phone = $challan->receiverUser->phone;
                    $senderUser = $challan->senderUser->name;
                    $challanNo = $challan->challan_series . '-' . $challan->series_num;
                    $pdfUrl = $challan->pdf_url;
                    $heading = 'Challan';

                    // Show Notifications in Status
                    $notification = new Notification([
                        'user_id' => $challan->receiverUser->id,
                        'message' => 'New Comment added by ' . $challan->senderUser->name,
                        'type' => 'challan',
                        'added_id' => $challan->id,
                        'panel' => 'receiver',
                        'template_name' => 'received_return_challan',
                    ]);
                    $notification->save();
                }

                if ($request->has('status_comment')) {
                    // Get the existing status_comment data
                    $statusComment = json_decode($challan->status_comment, true) ?? [];

                    // Add the new comment to the status_comment data
                    $statusComment[] = [
                        'comment' => $request->status_comment,
                        'date' => date('Y-m-d'),
                        'time' => date('H:i:s'),
                        'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    ];

                    // Update the status_comment field with the combined data
                    $challan->update(['status_comment' => json_encode($statusComment)]);

                    if (isset($permissions['sender']['email']['Add Comment']) && $permissions['sender']['email']['Add Comment'] == true) {
                        // Send the PDF via email for SFP Challan Alert
                        if ($receiverUserEmail != null) {
                            $pdfEmailService = new PDFEmailService();
                            $pdfEmailService->addCommentSentChallanMail($challan, $receiverUserEmail, $request->status_comment);
                        }
                    }

                    // Calculate the amount to deduct (90 paisa + 18% GST)
                    $deduction = 0.90 + (0.90 * 0.18);
                    // Get the user's wallet
                    $wallet = Wallet::where('user_id', Auth::id())->first();

                    // Check if the wallet balance is greater than the deduction
                    if ($wallet !== null && $wallet->balance >= $deduction) {
                        if (isset($permissions['sender']['whatsapp']['Add Comment']) && $permissions['sender']['whatsapp']['Add Comment'] == true) {
                            $pdfWhatsAppService = new PDFWhatsAppService();
                            $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendCommentOnWhatsApp($phone, $senderUser, $challanNo, $request->status_comment, $pdfUrl, $heading);

                            if($pdfWhatsAppServiceResponse == true){
                                // Deduct the cost from the wallet
                                $wallet->balance -= $deduction;
                                $wallet->save();
                            }
                        }
                    }

                    $successCount++;
                }
            }

            // Prepare the response message
            $message = $totalCount === 1
                ? "Comment added successfully."
                : "Comments added successfully.";

            // Return a response indicating success
            return response()->json([
                'message' => $message,
                'status_code' => 200,
                'success' => true
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'One or more Challans Not Found.',
                'status_code' => 400,
                'success' => false
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while adding comments: ' . $e->getMessage(),
                'status_code' => 500,
                'success' => false
            ], 500);
        }
    }


    public function addBulkComment(Request $request, array $challanIds)
    {
        $responses = [];

        foreach ($challanIds as $challanId) {
            try {
                // Find the Challan by ID
                $challan = Challan::findOrFail($challanId);
                $challan = $challan->load('receiverUser', 'senderUser');
                if($request->has('receiver')){
                    $receiverUserEmail = $challan->senderUser ? $challan->senderUser->email : null;
                }
                elseif($request->has('sender')){
                    $receiverUserEmail = $challan->receiverUser ? $challan->receiverUser->email : null;
                }

                if($request->has('status_comment')){
                    // Get the existing status_comment data
                    $statusComment = json_decode($challan->status_comment, true);

                    // Add the new comment to the status_comment data
                    $statusComment[] = [
                        'comment' => $request->status_comment,
                        'date' => date('Y-m-d'),
                        'time' => date('H:i:s'),
                        'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    ];

                    // Update the status_comment field with the combined data
                    $challan->update(['status_comment' => json_encode($statusComment)]);
                    // Send the PDF via email for SFP Challan Alert
                    if ($receiverUserEmail != null) {
                        $pdfEmailService = new PDFEmailService();
                        $pdfEmailService->addCommentSentChallanMail($challan, $receiverUserEmail, $request->status_comment);
                    }
                }

                // Add a response indicating success for this ID
                $responses[] = [
                    'data' => $challan->statuses,
                    'message' => 'Comment added successfully for challan ID: ' . $challanId,
                    'status_code' => 200
                ];
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                // Add a response indicating failure for this ID
                $responses[] = [
                    'message' => 'Challan Not Found for ID: ' . $challanId,
                    'status_code' => 400
                ];
            }
        }

        return response()->json([
            'data' => $challan->statuses,
            'message' => 'Comment added successfully.',
            'status_code' => 200
        ], 200);
    }


    public function uploadSignature(Request $request)
    {
        // dd($request->all());
        $image_data = $request->input('signed');

        // Remove the "data:image/png;base64," part from the beginning of the string
        $image_data = preg_replace('/^data:image\/\w+;base64,/', '', $image_data);

        // Decode the base64 data
        $image_data = base64_decode($image_data);

        // Generate a unique filename
        $filename = uniqid('signature_') . '.png';

        // Specify the path where you want to store the uploaded signature
        $path = public_path('image/') . $filename;

        // dd( $path);
        // Save the signature to the specified path
        $data =file_put_contents($path, $image_data);

        $signature = Challan::find($request->column_id);
        // dd($signature);
        $signature->signature = $filename; // Assuming your public folder is accessible via web
        $signature->save();

        $challan = Challan::where('id', $request->column_id)->with('receiverUser', 'senderUser', 'orderDetails', 'orderDetails.columns', 'statuses')->first();

        // Generate the PDF for the Challan using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateChallanPDF($challan);

        $response = (array) $response->getData();
        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $challan->pdf_url = $response['pdf_url'];
            $challan->save();
        }

        return response()->json([
            'message' => 'Successfully uploaded signature.',
            'status_code' => 200,
        ], 200);
    }

}
