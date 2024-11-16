<?php

namespace App\Http\Controllers\V1\Buyers;

use App\Models\User;
use App\Models\Buyer;
use Illuminate\Support\Str;
use App\Models\BuyerDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Env;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;


class BuyersController extends Controller
{
    public function addBuyer(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'buyer_special_id' => 'required|string|max:255',
            // 'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $buyerSpecialId = $request->input('buyer_special_id');
        // $userId = $request->input('user_id');

        // Fetch the user details based on buyer special ID and user ID
        $user = User::where('special_id', $buyerSpecialId)
            // ->where('id', $userId)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => 'User not found',
            ], 400);
        }

        // Create the buyer
        $buyer = Buyer::create([
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'buyer_user_id' => $user->id,
            'buyer_name' => $user->name,
            'status' => 'active', // Default status for a new buyer
            'buyer_special_id' => $buyerSpecialId,
        ]);

        // Create the buyer detail using the user's details
        $buyerDetail = BuyerDetails::create([
            'buyer_id' => $buyer->id,

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
            'status' => 'active', // Default status for a new buyer detail
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Buyer added successfully',
            'buyer' => $buyer,
            'buyer_detail' => $buyerDetail,
        ], 200);
    }

    public function importManualBuyer(Request $request)
    {
        // dd($request);
        // Validate the incoming request data
        // $validator = Validator::make($request->all(), [
        //     'email' => 'nullable|email|unique:users,email',
        //     'buyer_name' => 'required|string|max:255',
        //     'address' => 'nullable|string|max:255',
        //     'pincode' => 'nullable|integer',
        //     'phone' => 'nullable|string|unique:users,phone',
        //     'gst_number' => 'nullable|string|max:191',
        //     'state' => 'nullable|string|max:75',
        //     'city' => 'nullable|string|max:75',
        //     'bank_name' => 'nullable|string|max:255',
        //     'branch_name' => 'nullable|string|max:255',
        //     'bank_account_no' => 'nullable|string|max:255',
        //     'ifsc_code' => 'nullable|string|max:255',
        //     'tan' => 'nullable|string|max:15',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 400,
        //         'message' => 'Validation Error',
        //         'errors' => $validator->errors(),
        //     ], 400);
        // }
        // Generate a 10-digit alphanumeric special ID
        // $specialId = Str::random(10);
        // // Create a new user
        // $user = User::create([
        //     'name' => $request->input('name'),
        //     'email' => $request->input('email'),
        //     'phone' => $request->input('phone'),
        //     'special_id' => $specialId,
        //     // 'password' => bcrypt($request->input('password')),
        //     // Include other user details as needed
        // ]);

        // Create the buyer
        $buyer = Buyer::create([
            'user_id' => $request->input('user_id'),
            'buyer_user_id' => $request->input('buyer_user_id'),
            'buyer_name' => $request->input('buyer_name'),
            'status' => 'active',
            'buyer_special_id' => $request->input('buyer_special_id'),
        ]);

        // Create the buyer detail
        $buyerDetail = BuyerDetails::create([
            'buyer_id' => $buyer->id,

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
        // return response()->json([
        //     'status' => 200,
        //     'message' => 'Buyer added successfully',
        //     'buyer' => $buyer,
        //     'buyer_detail' => $buyerDetail,
        // ], 200);
    }

    public function addManualBuyer(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:users,email',
            'buyer_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'pincode' => 'nullable|integer',
            'phone' => 'nullable|string|unique:users,phone',
            'gst_number' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:75',
            'city' => 'nullable|string|max:75',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'bank_account_no' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'tan' => 'nullable|string|max:15',
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
            'name' => $request->input('buyer_name'),
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

        // Create the buyer
        $buyer = Buyer::create([
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'buyer_user_id' => $user->id,
            'buyer_name' => $request->input('buyer_name'),
            'status' => 'active',
            'buyer_special_id' => $specialId,
        ]);

        $pincode = $request->input('pincode');
        if($pincode === '' || $pincode === null){
            $pincode = null;
        }
        // Create the buyer detail
        $buyerDetail = BuyerDetails::create([
            'buyer_id' => $buyer->id,

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
            'status' => 'active', // Default value: active
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Buyer added successfully',
            'buyer' => $buyer,
            'buyer_detail' => $buyerDetail,
        ], 200);
    }

    public function storeBuyerDetail(Request $request)
    {
        // Validate the incoming request data for storing the buyer detail
        $validator = Validator::make($request->all(), [
            'buyer_id' => 'required|exists:buyers,id',
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
            'status' => 'nullable|in:active,pause,terminate',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Create the buyer detail
        $buyerDetail = BuyerDetails::create([
            'buyer_id' => $request->input('buyer_id'),
            'user_id' => auth()->user()->id,
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
            'status' => $request->input('status', 'active'), // Default value: active
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Buyer Detail added successfully',
            'buyer_detail' => $buyerDetail,
        ], 200);
    }
    public function updateBuyer(Request $request, $buyerId)
    {
        // Validate the incoming request data for updating the buyer
        $validator = Validator::make($request->all(), [
            'buyer_name' => 'required|string|max:255',
            // 'status' => 'required|in:active,inactive,terminated',
            // 'buyer_special_id' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Get the buyer by ID
        $buyer = Buyer::find($buyerId);
        if (!$buyer) {
            return response()->json([
                'status' => 400,
                'message' => 'Buyer not found',
            ], 400);
        }

        // Update the buyer
        $buyer->update([
            'buyer_name' => $request->input('buyer_name'),
            'status' => $request->input('status'),
            // 'buyer_special_id' => $request->input('buyer_special_id'),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Buyer updated successfully',
            'buyer' => $buyer,
        ], 200);
    }
    public function updateBuyerDetail(Request $request, $buyerDetailId)
    {
        // Validate the incoming request data for updating the buyer detail
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
            'status' => 'nullable|in:active,pause,terminate',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Get the buyer detail by ID
        $buyerDetail = BuyerDetails::find($buyerDetailId);
        if (!$buyerDetail) {
            return response()->json([
                'status' => 400,
                'message' => 'Buyer Detail not found',
            ], 400);
        }

        // Update the buyer detail
        $buyerDetail->update([
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
            'status' => $request->input('status', 'active'), // Default value: active
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Buyer Detail updated successfully',
            'buyer_detail' => $buyerDetail,
        ], 200);
    }

    public function importManualReceiver(Request $request)
    {

        $buyer = Buyer::create([
            'user_id' => $request->input('user_id'),
            'buyer_user_id' => $request->input('buyer_user_id'),
            'buyer_name' => $request->input('buyer_name'),
            'status' => 'active',
            'buyer_special_id' => $request->input('buyer_special_id'),
        ]);

        // Create the buyer detail
        $buyerDetail = BuyerDetails::create([
            'buyer_id' => $buyer->id,
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
    // public function getMultipleAddress()
    // {

    // }
    public function index(Request $request)
    {
        try {
            $query = Buyer::query();

            if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name',Auth::getDefaultDriver())->exists()) {
                $query->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
            } elseif ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by buyer_user_id
            if ($request->input('buyer_user_id')) {
                $query->where('buyer_user_id', $request->input('buyer_user_id'));
            }

            // Filter by buyer_name
            if ($request->input('buyer_name')) {
                $query->where('buyer_name', $request->input('buyer_name'));
            }

            // Filter by buyer_special_id
            if ($request->input('buyer_special_id')) {
                $query->where('buyer_special_id', $request->input('buyer_special_id'));
            }

            // Filter by status
            if ($request->input('status')) {
                $query->where('status', $request->input('status'));
            }

            $Buyers = $query->with([
                'user' => function ($query) {
                    $query->select([
                        'id', 'special_id', 'name', 'email',
                        'address', 'pincode', 'company_name', 'phone','gst_number',
                        'pancard', 'state', 'city','tan', 'remember_token', 'status', 'sender', 'receiver', 'seller', 'buyer','added_by',
                    ]);
                },
                'user.details', 'details', 'invoiceNumber'
            ])->get();

            return response()->json([
                'data' => $Buyers,
                'message' => 'Success',
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error fetching buyers: ' . $e->getMessage());

            // Return a generic error response
            return response()->json([
                'message' => 'Failed to fetch buyers',
                'status_code' => 500
            ], 500);
        }
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
        $Buyer = Buyer::where('id', $id)->with('user', 'details')->first();

        if (!$Buyer) {
            return response()->json([
                'data' => null,
                'message' => 'Buyer not found',
                'status_code' => 400,
            ], 400);
        }

        return response()->json([
            'data' => $Buyer,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function delete($id)
    {
        // Get the panel series number with the given ID.
        $Buyer = Buyer::find($id);

        // Return a 404 response if the panel series number doesn't exist
        if (!$Buyer) {
            return response()->json([
                'data' => null,
                'message' => 'Buyer not found.',
                'status_code' => 400,
            ], 400);
        }

        // // Check if the panel series number status is "terminated"
        // if ($Buyer->status !== 'terminated') {
        //     return response()->json([
        //         'data' => null,
        //         'message' => 'Please terminate this panel series number first.',
        //         'status_code' => 400,
        //     ], 400);
        // }

        // Delete the panel series number.
        $Buyer->details()->delete();
        $Buyer->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Buyer destroyed.',
            'status_code' => 200
        ]);
    }

     // Export Receivers to CSV
     public function exportColumns()
     {
         $request = request();
         $this->columnDisplayNames = ['Name', 'Email', 'Phone', 'Address', 'Pincode', 'State', 'City', 'GST','Company Name', 'Pancard', 'Ornanization Type'];


         $request = request();


         $filename = 'add_buyer.csv';

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



    // public function bulkUpload(Request $request)
    // {
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'errors' => $validator->errors(),
    //             'status_code' => 422,
    //         ], 422);
    //     }

    //     $file = $request->file;
    //     if (!$file->isValid()) {
    //         return response()->json([
    //             'errors' => 'File upload failed.',
    //             'status_code' => 400,
    //         ], 400);
    //     }

    //     $handle = fopen($file->getRealPath(), "r");
    //     if (!$handle) {
    //         return response()->json([
    //             'errors' => 'Unable to open the CSV file',
    //             'status_code' => 400,
    //         ], 400);
    //     }

    //     $header = fgetcsv($handle, 1000, ",");
    //     if (!$header) {
    //         return response()->json([
    //             'errors' => 'Unable to read the CSV file',
    //             'status_code' => 400,
    //         ], 400);
    //     }

    //     $expectedHeaders = ['Name', 'Email', 'Phone', 'Address', 'Pincode', 'State', 'City', 'GST', 'Company Name', 'Pancard', 'Ornanization Type'];
    //     if ($header !== $expectedHeaders) {
    //         return response()->json([
    //             'errors' => 'The CSV file is missing the required headers or the headers are incorrect.',
    //             'status_code' => 400,
    //         ], 400);
    //     }

    //     $errors = [];
    //     $successCount = 0;
    //     $rowNumber = 1; // Start counting rows from 1 (assuming first row is header)

    //     DB::beginTransaction();

    //         while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
    //             $rowNumber++;
    //             $dataArr[] = array_combine($header, $row);
    //         }

    //         $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    //         try {

    //             foreach ($dataArr as $index => $row) {
    //                 // Validate mandatory fields
    //                 $mandatoryFields = ['Name', 'Email', 'Phone', 'Address', 'Pincode', 'State', 'City'];
    //                 foreach ($mandatoryFields as $field) {
    //                     if (empty($row[$field])) {
    //                         $errors[] = "Row $rowNumber: The $field field is required.";
    //                     }
    //                 }

    //                 // Validate email format
    //                 if (!filter_var($row['Email'], FILTER_VALIDATE_EMAIL)) {
    //                     $errors[] = "Row $rowNumber: The Email field must be a valid email address.";
    //                 }

    //                 // Validate phone number format (assuming 10 digits)
    //                 if (!preg_match('/^\d{10}$/', $row['Phone'])) {
    //                     $errors[] = "Row $rowNumber: The Phone field must be a valid 10-digit number.";
    //                 }

    //                 // Check if email or phone already exists in the users table
    //                 $user = DB::table('users')
    //                     ->where('email', $row['Email'])
    //                     ->orWhere('phone', $row['Phone'])
    //                     ->first();

    //                     if ($user) {
    //                         $existingFields = [];
    //                         if ($user->email === $row['email']) {
    //                             $existingFields[] = 'Email';
    //                         }
    //                         if ($user->phone === $row['phone']) {
    //                             $existingFields[] = 'Phone';
    //                         }
    //                         $existingFieldsString = implode(' and ', $existingFields);
    //                         $errors[] = "Row $rowNumber: The $existingFieldsString already exists. You can add it as the receiver with the special id.";
    //                     }

    //                 // If there are errors, skip this row
    //                 if (!empty($errors)) {
    //                     continue;
    //                 }

    //                 // Generate a 10-digit alphanumeric special ID
    //                 $specialId = Str::random(10);

    //                 // Set pincode to null if it's not provided
    //                 $pincode = $row['Pincode'];
    //                 $pincode = $row['Pincode'] === '' || $row['Pincode'] === null ? null : $row['Pincode'];

    //                 // Create a new user
    //                 $user = User::create([
    //                     'name' => $row['Name'],
    //                     'email' => $row['Email'],
    //                     'phone' => $row['Phone'],
    //                     'special_id' => $specialId,
    //                     'company_name' => $row['Company Name'],
    //                     'added_by' => $userId,
    //                     'address' => $row['Address'],
    //                     'pincode' => $pincode,
    //                     'gst_number' => $row['GST'],
    //                     'state' => $row['State'],
    //                     'city' => $row['City'],
    //                 ]);

    //                 // Create the receiver
    //                 $receiver = Buyer::create([
    //                     'user_id' => $userId,
    //                     'buyer_user_id' => $user->id,
    //                     'buyer_name' => $row['Name'],
    //                     'status' => 'active',
    //                     'buyer_special_id' => $specialId,
    //                 ]);

    //                 // Create the receiver detail
    //                 $receiverDetail = BuyerDetails::create([
    //                     'buyer_id' => $receiver->id,
    //                     'address' => $row['Address'],
    //                     'pincode' => $pincode,
    //                     'phone' => $row['Phone'],
    //                     'gst_number' => $row['GST'],
    //                     'state' => $row['State'],
    //                     'city' => $row['City'],
    //                     'organisation_type' => $row['Ornanization Type'],
    //                     'status' => 'active', // Default value: active
    //                 ]);
    //             }

    //             if (!empty($errors)) {
    //                 DB::rollBack();
    //                 return response()->json([
    //                     'errors' => $errors,
    //                     'status_code' => 422,
    //                 ], 422);
    //             }

    //             DB::commit();

    //             return response()->json([
    //                 'message' => "$successCount buyers created successfully",
    //                 'status_code' => 200,
    //             ], 200);
    //         } catch (\Exception $e) {
    //             DB::rollBack();
    //             return response()->json([
    //                 'errors' => 'An error occurred while processing the file: ' . $e->getMessage(),
    //                 'status_code' => 500,
    //             ], 500);
    //         } finally {
    //             fclose($handle);
    //         }
    // }

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

        $receiver = Buyer::create([
            'user_id' => $userId,
            'buyer_user_id' => $user->id,
            'buyer_name' => $row['Name'],
            'status' => 'active',
            'buyer_special_id' => $specialId,
        ]);

        BuyerDetails::create([
            'buyer_id' => $receiver->id,
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
