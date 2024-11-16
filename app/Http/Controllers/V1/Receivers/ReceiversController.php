<?php

namespace App\Http\Controllers\V1\Receivers;

use App\Models\User;
use Aws\S3\S3Client;
use App\Models\Challan;
use App\Models\Receiver;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ReceiverDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Env;
use Illuminate\Support\Facades\Validator;

class ReceiversController extends Controller
{
    public function addReceiver(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'receiver_special_id' => 'required|string|max:255|exists:users,special_id',
            // 'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $receiverSpecialId = $request->input('receiver_special_id');
        // $userId = $request->input('user_id');

        // Fetch the user details based on receiver special ID and user ID
        $user = User::where('special_id', $receiverSpecialId)
            // ->where('id', $userId)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => 'User not found',
                'errors' => ['special_id' => 'User not found'],

            ], 400);
        }

        // Check if receiver already exists
        $existingReceiver = Receiver::where('receiver_user_id', $user->id)
            ->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->first();

            if ($existingReceiver) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Receiver already exists',
                    'errors' => ['Receiver already exists for this user'],
                ], 400);
            }

        // Create the receiver
        $receiver = Receiver::create([
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'receiver_user_id' => $user->id,
            'receiver_name' => $user->name,
            'status' => 'active', // Default status for a new receiver
            'receiver_special_id' => $receiverSpecialId,
        ]);

        // Create the receiver detail using the user's details
        $receiverDetail = ReceiverDetails::create([
            'receiver_id' => $receiver->id,

            'address' => $user->address,
            'pincode' => $user->pincode,
            'phone' => $user->phone,
            'gst_number' => $user->gst_number,
            'state' => $user->state,
            'city' => $user->city,
            'bank_name' => $user->bank_name,
            'branch_name' => $user->branch_name,
            'bank_account_no' => $user->bank_account_no,
            'ifsc_code' => $user->ifsc_code,
            'tan' => $user->tan,
            'status' => 'active', // Default status for a new receiver detail
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Receiver added successfully',
            'receiver' => $receiver,
            'receiver_detail' => $receiverDetail,
        ], 200);
    }

    public function importManualReceiver(Request $request)
    {

        $receiver = Receiver::create([
            'user_id' => $request->input('user_id'),
            'receiver_user_id' => $request->input('receiver_user_id'),
            'receiver_name' => $request->input('receiver_name'),
            'status' => 'active',
            'receiver_special_id' => $request->input('receiver_special_id'),
        ]);

        // Create the receiver detail
        $receiverDetail = ReceiverDetails::create([
            'receiver_id' => $receiver->id,
            'address' => $request->input('address'),
            'pincode' => $request->input('pincode'),
            'phone' => $request->input('phone'),
            'gst_number' => $request->input('gst_number'),
            'state' => $request->input('state'),
            'city' => $request->input('city'),
            'bank_name' => $request->input('bank_name'),
            'branch_name' => $request->input('branch_name'),
            'bank_account_no' => $request->input('bank_account_no'),
            'ifsc_code' => $request->input('ifsc_code'),
            'tan' => $request->input('tan'),
            'status' => 'active', // Default value: active
        ]);
        return true;

    }

    public function addManualReceiver(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:users,email',
            'receiver_name' => 'string|max:255',
            'address' => 'nullable|string|max:255',
            'pincode' => 'nullable|integer',
            'phone' => 'nullable|string|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'gst_number' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:75',
            'city' => 'nullable|string|max:75',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'bank_account_no' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'tan' => 'nullable|string|max:15',
            'organisation_type' => 'nullable|string',
            'location_name' => 'nullable|string',
            'company_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }
        // Generate a 10-digit alphanumeric special ID
        $specialId = Str::random(10);

        // Set pincode to null if it's not provided
        $pincode = $request->input('pincode');
        if($pincode === '' || $pincode === null){
            $pincode = null;
        }

        // Create a new user
        $user = User::create([
            'name' => $request->input('receiver_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'special_id' => $specialId,
            'company_name' => $request->input('company_name'),
            'added_by' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'address' => $request->input('address'),
            'pincode' => $pincode,
            'phone' => $request->input('phone'),
            'gst_number' => $request->input('gst_number'),
            'state' => $request->input('state'),
            'city' => $request->input('city'),
            'bank_name' => $request->input('bank_name'),
            'branch_name' => $request->input('branch_name'),
            'bank_account_no' => $request->input('bank_account_no'),
            'ifsc_code' => $request->input('ifsc_code'),
            'tan' => $request->input('tan'),
        ]);

        // Create the receiver
        $receiver = Receiver::create([
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'receiver_user_id' => $user->id,
            'receiver_name' => $request->input('receiver_name'),
            'status' => 'active',
            'receiver_special_id' => $specialId,
        ]);


        // Create the receiver detail
        $receiverDetail = ReceiverDetails::create([
            'receiver_id' => $receiver->id,
            'address' => $request->input('address'),
            'pincode' => $pincode,
            'phone' => $request->input('phone'),
            'gst_number' => $request->input('gst_number'),
            'state' => $request->input('state'),
            'city' => $request->input('city'),
            'bank_name' => $request->input('bank_name'),
            'branch_name' => $request->input('branch_name'),
            'bank_account_no' => $request->input('bank_account_no'),
            'ifsc_code' => $request->input('ifsc_code'),
            'organisation_type' => $request->input('organisation_type'),
            'location_name' => $request->input('location_name'),
            'tan' => $request->input('tan'),
            'status' => 'active', // Default value: active
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Receiver added successfully',
            'receiver' => $receiver,
            'receiver_detail' => $receiverDetail,
        ], 200);
    }

    public function storeReceiverDetail(Request $request)
    {
        // Validate the incoming request data for storing the receiver detail
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:receivers,id',
            'address' => 'nullable|string|max:255',
            'pincode' => 'nullable|integer',
            'phone' => 'nullable|string',
            'gst_number' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:75',
            'city' => 'nullable|string|max:75',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'bank_account_no' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'tan' => 'nullable|string|max:15',
            'organisation_type' => 'nullable|string',
            'location_name' => 'nullable|string',
            'status' => 'nullable|in:active,pause,terminate',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Create the receiver detail
        $receiverDetail = ReceiverDetails::create([
            'receiver_id' => $request->input('receiver_id'),
            'user_id' => auth()->user()->id,
            'address' => $request->input('address'),
            'pincode' => $request->input('pincode'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'), // Added 'email' field to the receiver details table
            'gst_number' => $request->input('gst_number'),
            'state' => $request->input('state'),
            'city' => $request->input('city'),
            'bank_name' => $request->input('bank_name'),
            'branch_name' => $request->input('branch_name'),
            'bank_account_no' => $request->input('bank_account_no'),
            'ifsc_code' => $request->input('ifsc_code'),
            'tan' => $request->input('tan'),
            'organisation_type' => $request->input('organisation_type'),
            'location_name' => $request->input('location_name'),
            'status' => $request->input('status', 'active'), // Default value: active
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Receiver Detail added successfully',
            'receiver_detail' => $receiverDetail,
        ], 200);
    }
    public function updateReceiver(Request $request, $receiverId)
    {
        // Validate the incoming request data for updating the receiver
        $validator = Validator::make($request->all(), [
            'receiver_name' => 'required|string|max:255',
            'status' => 'in:active,inactive,terminated',
            // 'receiver_special_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // $this->receiverId = $receiverId;
        // Get the receiver by ID
        $receiver = Receiver::find($receiverId);

        if (!$receiver) {
            return response()->json([
                'status' => 400,
                'message' => 'Receiver not found',
            ], 400);
        }

        // Update the receiver
        $receiver->update([
            'receiver_name' => $request->input('receiver_name'),
            'status' => $request->input('status', 'active'),
            // 'receiver_special_id' => $request->input('receiver_special_id'),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Receiver updated successfully',
            'receiver' => $receiver,
        ], 200);
    }
    public function updateReceiverDetail(Request $request, $receiverDetailId)
    {
        // dd($request->all(), $receiverDetailId);
        // Validate the incoming request data for updating the receiver detail
        $validator = Validator::make($request->all(), [
            'address' => 'nullable|string|max:255',
            'pincode' => 'nullable|integer',
            'phone' => 'nullable|string',
            'gst_number' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:75',
            'city' => 'nullable|string|max:75',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'bank_account_no' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'tan' => 'nullable|string|max:15',
            'location_name' => 'nullable|string',
            'organisation_type' => 'nullable|string',
            'status' => 'nullable|in:active,pause,terminate',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Get the receiver detail by ID
        $receiverDetail = ReceiverDetails::find($receiverDetailId);

        if (!$receiverDetail) {
            return response()->json([
                'status' => 400,
                'message' => 'Receiver Detail not found',
            ], 400);
        }

        // Update the receiver detail
        $receiverDetail->update([
            'address' => $request->input('address'),
            'pincode' => $request->input('pincode'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'gst_number' => $request->input('gst_number'),
            'state' => $request->input('state'),
            'city' => $request->input('city'),
            'bank_name' => $request->input('bank_name'),
            'branch_name' => $request->input('branch_name'),
            'bank_account_no' => $request->input('bank_account_no'),
            'ifsc_code' => $request->input('ifsc_code'),
            'tan' => $request->input('tan'),
            'location_name' => $request->input('location_name'),
            // 'organisation_type' => $request->input('organisation_type'),
            'status' => $request->input('status', 'active'), // Default value: active
        ]);

        // Fetch User details
        $userId = Receiver::where('id', $request->id)->pluck('receiver_user_id');
        // dd($userId);
        // Update user Details also

        return response()->json([
            'status' => 200,
            'message' => 'Receiver Detail updated successfully',
            'receiver_detail' => $receiverDetail,
        ], 200);
    }

    // Export Receivers to CSV
    public function exportColumns()
    {
        $request = request();
        $this->columnDisplayNames = ['Name', 'Email', 'Phone', 'Address', 'Pincode', 'State', 'City', 'GST','Company Name', 'Pancard', 'Ornanization Type'];


        $request = request();


        $filename = 'add_receiver.csv';

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

    public function bulkUpload(Request $request)
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
        if (!$file->isValid()) {
            return response()->json([
                'errors' => 'File upload failed.',
                'status_code' => 400,
            ], 400);
        }

        $handle = fopen($file->getRealPath(), "r");
        if (!$handle) {
            return response()->json([
                'errors' => 'Unable to open the CSV file',
                'status_code' => 400,
            ], 400);
        }

        $header = fgetcsv($handle, 1000, ",");
        if (!$header) {
            return response()->json([
                'errors' => 'Unable to read the CSV file',
                'status_code' => 400,
            ], 400);
        }

        $expectedHeaders = ['Name', 'Email', 'Phone', 'Address', 'Pincode', 'State', 'City', 'GST', 'Company Name', 'Pancard', 'Ornanization Type'];
        if ($header !== $expectedHeaders) {
            return response()->json([
                'errors' => 'The CSV file is missing the required headers or the headers are incorrect.',
                'status_code' => 400,
            ], 400);
        }

        $errors = [];
        $successCount = 0;
        $rowNumber = 1; // Start counting rows from 1 (assuming first row is header)

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rowNumber++;
                $rowData = array_combine($header, $row);
                $rowErrors = $this->validateRow($rowData, $rowNumber);

                if (!empty($rowErrors)) {
                    $errors = array_merge($errors, $rowErrors);
                    continue;
                }

                $this->createReceiverFromRow($rowData);
                $successCount++;
            }

            if (!empty($errors)) {
                DB::rollBack();
                return response()->json([
                    'errors' => $errors,
                    'status_code' => 422,
                ], 422);
            }

            DB::commit();

            return response()->json([
                'message' => "$successCount receivers created successfully",
                'status_code' => 200,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => 'An error occurred while processing the file: ' . $e->getMessage(),
                'status_code' => 500,
            ], 500);
        } finally {
            fclose($handle);
        }
    }

    private function validateRow($row, $rowNumber)
    {
        $errors = [];

        $mandatoryFields = ['Name', 'Email', 'Phone', 'Address', 'Pincode', 'State', 'City'];
        foreach ($mandatoryFields as $field) {
            if (empty($row[$field])) {
                $errors[] = "Row $rowNumber: The $field field is required.";
            }
        }

        if (!filter_var($row['Email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Row $rowNumber: The Email field must be a valid email address.";
        }

        if (!preg_match('/^\d{6}$/', $row['Pincode'])) {
            $errors[] = "Row $rowNumber: The Pincode field must be a valid 6-digit number.";
        }

        if (!preg_match('/^\d{10}$/', $row['Phone'])) {
            $errors[] = "Row $rowNumber: The Phone field must be a valid 10-digit number.";
        }

        $user = DB::table('users')
            ->where('email', $row['Email'])
            ->orWhere('phone', $row['Phone'])
            ->first();

        if ($user) {
            $errors[] = "Row $rowNumber: The Email or Phone already exists. You can add it as the receiver with the special id.";
        }

        return $errors;
    }

    private function createReceiverFromRow($row)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $specialId = Str::random(10);
        $pincode = $row['Pincode'] === '' || $row['Pincode'] === null ? null : $row['Pincode'];

        $user = User::create([
            'name' => $row['Name'],
            'email' => $row['Email'],
            'phone' => $row['Phone'],
            'special_id' => $specialId,
            'company_name' => $row['Company Name'],
            'added_by' => $userId,
            'address' => $row['Address'],
            'pincode' => $pincode,
            'gst_number' => $row['GST'],
            'state' => $row['State'],
            'city' => $row['City'],
        ]);

        $receiver = Receiver::create([
            'user_id' => $userId,
            'receiver_user_id' => $user->id,
            'receiver_name' => $row['Name'],
            'status' => 'active',
            'receiver_special_id' => $specialId,
        ]);

        ReceiverDetails::create([
            'receiver_id' => $receiver->id,
            'address' => $row['Address'],
            'pincode' => $pincode,
            'phone' => $row['Phone'],
            'gst_number' => $row['GST'],
            'state' => $row['State'],
            'city' => $row['City'],
            'organisation_type' => $row['Ornanization Type'],
            'status' => 'active',
        ]);
    }




    public function index(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $receiverUserId = $request->input('receiver_user_id');
            $receiverName = $request->input('receiver_name');
            $receiverSpecialId = $request->input('receiver_special_id');
            $status = $request->input('status');

            $query = Receiver::query();

            if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
                $query->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
            } elseif ($userId) {
                $query->where('user_id', $userId);
            }

            $query->when($receiverUserId, function ($query, $receiverUserId) {
                return $query->where('receiver_user_id', $receiverUserId);
            });

            $query->when($receiverName, function ($query, $receiverName) {
                return $query->where('receiver_name', $receiverName);
            });

            $query->when($receiverSpecialId, function ($query, $receiverSpecialId) {
                return $query->where('receiver_special_id', $receiverSpecialId);
            });

            $query->when($status, function ($query, $status) {
                return $query->where('status', $status);
            });

            $receivers = $query->with([
                'user' => function ($query) {
                    $query->select([
                        'id', 'special_id', 'name', 'email',
                        'address', 'pincode', 'company_name', 'phone','gst_number',
                        'pancard', 'state', 'city','tan','sender', 'receiver', 'seller', 'buyer','added_by'

                    ]);
                },
                'user.details', 'details', 'seriesNumber'
            ])->get();

            $responseData = [
                'data' => $receivers,
                'message' => 'Success',
                'status_code' => 200
            ];

            /// Compress the response data
            $compressedData = gzencode(json_encode($responseData), 9);

            return response($compressedData, 200, [
                'Content-Encoding' => 'gzip',
                'Content-Length' => strlen($compressedData),
                'Content-Type' => 'application/json',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function indexSender(Request $request)
    {
        // dd($request->all());
        $query = Receiver::query();

        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
            $query->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
        } elseif ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by receiver_user_id
        if ($request->input('receiver_user_id')) {
            $query->where('receiver_user_id', $request->input('receiver_user_id'));
        }

        // Filter by receiver_name
        if ($request->input('receiver_name')) {
            $query->where('receiver_name', $request->input('receiver_name'));
        }

        // Filter by receiver_special_id
        if ($request->input('receiver_special_id')) {
            $query->where('receiver_special_id', $request->input('receiver_special_id'));
        }

        // Filter by status
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        // Get filtered Receivers
        $Receivers = $query->with('user', 'details', 'seriesNumber')->get();
        return response()->json([
            'data' => $Receivers,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }
    public function showUser($special_id)
    {
        $User = User::where('special_id', $special_id)->first();

        if (!$User) {
            return response()->json([
                'data' => null,
                'message' => 'User not found',
                'status_code' => 400,
            ], 400);
        }

        return response()->json([
            'data' => $User,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function show($id)
    {
        $Receiver = Receiver::where('id', $id)->with('user', 'details', 'seriesNumber')->first();

        // if ($Receiver->series_number != null) {
        // Get the latest series_num for the given challan_series and user_id
        $Receiver->serial_number = Challan::where('challan_series', $Receiver->series_number)
            ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->max('series_num');

        // Increment the latestSeriesNum for the new challan

        $Receiver->serial_number = $Receiver->serial_number ? $Receiver->serial_number + 1 : 1;
        // }

        if (!$Receiver) {
            return response()->json([
                'data' => null,
                'message' => 'Receiver not found',
                'status_code' => 400,
            ], 400);
        }

        return response()->json([
            'data' => $Receiver,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function delete($id)
    {
        // Get the panel series number with the given ID.
        $Receiver = Receiver::find($id);

        // Return a 404 response if the panel series number doesn't exist
        if (!$Receiver) {
            return response()->json([
                'data' => null,
                'message' => 'Receiver not found.',
                'status_code' => 400,
            ], 400);
        }

        // // Check if the panel series number status is "terminated"
        // if ($Receiver->status !== 'terminated') {
        //     return response()->json([
        //         'data' => null,
        //         'message' => 'Please terminate this panel series number first.',
        //         'status_code' => 400,
        //     ], 400);
        // }

        // Delete the panel series number.
        $Receiver->details()->delete();
        $Receiver->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Receiver destroyed.',
            'status_code' => 200
        ]);
    }

    public function fetchCityAndStateByPincode($pincode)
    {
        try {
            $apiUrl = env('POSTAL_PINCODE_API_URL') . '/' . $pincode;
            $response = Http::get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data) && is_array($data)) {
                    $city = $data[0]['PostOffice'][0]['District'];
                    $state = $data[0]['PostOffice'][0]['State'];

                    return response()->json([
                        'city' => $city,
                        'state' => $state,
                        'message' => 'State and City fetch Successfully',
                        'status_code' => 200
                    ]);
                }
            }
            return response()->json(['error' => 'Failed to fetch data from the API'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching data from the API'], 500);
        }
    }
}
