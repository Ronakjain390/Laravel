<?php

namespace App\Http\Controllers\V1\Seller;

use App\Models\User;
use App\Models\Seller;
use Illuminate\Support\Str;
use App\Models\SellerDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SellerController extends Controller
{
    public function addSeller(Request $request)
    {
        // dd($request);
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'seller_special_id' => 'required|string|max:255|exists:users,special_id',
            // 'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $sellerSpecialId = $request->input('seller_special_id');
        $userId = $request->input('user_id');

        // Fetch the user details based on receiver special ID and user ID
        $user = User::where('special_id', $sellerSpecialId)
            // ->where('id', $userId)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => 'User not found',
                'errors' => ['special_id'=>'User not found'],
            ], 400);
        }

        // Create the seller
        $seller = Seller::create([
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'seller_user_id' => $user->id,
            'seller_name' => $user->name,
            'status' => 'active', // Default status for a new seller
            'seller_special_id' => $sellerSpecialId,
        ]);

        // Create the seller detail using the user's details
        $sellerDetail = SellerDetails::create([
            'seller_id' => $seller->id,

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
            'status' => 'active', // Default status for a new seller detail
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Seller added successfully',
            'seller' => $seller,
            'seller_detail' => $sellerDetail,
        ], 200);
    }

    public function addManualSeller(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:users,email',
            'seller_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'pincode' => 'nullable|integer',
            'phone' => 'nullable|string|max:191|unique:users,phone',
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
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'special_id' => $specialId,
            'phone' => $request->input('phone'),
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

        // Create the seller
        $seller = Seller::create([
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'seller_user_id' => $user->id,
            'seller_name' => $request->input('seller_name'),
            'status' => 'active',
            'seller_special_id' => $specialId,
        ]);

        // Create the seller detail
        $sellerDetail = SellerDetails::create([
            'seller_id' => $seller->id,

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

        return response()->json([
            'status' => 200,
            'message' => 'Seller added successfully',
            'seller' => $seller,
            'seller_detail' => $sellerDetail,
        ], 200);
    }

    public function storeSellerDetail(Request $request)
    {
        // Validate the incoming request data for storing the seller detail
        $validator = Validator::make($request->all(), [
            'seller_id' => 'required|exists:sellers,id',
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

        // Create the seller detail
        $sellerDetail = SellerDetails::create([
            'seller_id' => $request->input('seller_id'),
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
            'message' => 'Seller Detail added successfully',
            'seller_detail' => $sellerDetail,
        ], 200);
    }
    public function updateSeller(Request $request, $sellerId)
    {
        // Validate the incoming request data for updating the seller
        $validator = Validator::make($request->all(), [
            'seller_name' => 'required|string|max:255',
            // 'status' => 'nullable|in:active,inactive,terminated',
            // 'seller_special_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Get the seller by ID
        $seller = Seller::find($sellerId);
        if (!$seller) {
            return response()->json([
                'status' => 400,
                'message' => 'Seller not found',
            ], 400);
        }

        // Update the seller
        $seller->update([
            'seller_name' => $request->input('seller_name'),
            'status' => $request->input('status'),
            // 'seller_special_id' => $request->input('seller_special_id'),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Seller updated successfully',
            'seller' => $seller,
        ], 200);
    }
    public function updateSellerDetail(Request $request, $sellerDetailId)
    {
        // Validate the incoming request data for updating the seller detail
        $validator = Validator::make($request->all(), [
            'address' => 'nullable|string|max:255',
            'pincode' => 'nullable|integer',
            'phone' => 'nullable|string|max:191',
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

        // Get the seller detail by ID
        $sellerDetail = SellerDetails::find($sellerDetailId);
        if (!$sellerDetail) {
            return response()->json([
                'status' => 400,
                'message' => 'Seller Detail not found',
            ], 400);
        }

        // Update the seller detail
        $sellerDetail->update([
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
            'message' => 'Seller Detail updated successfully',
            'seller_detail' => $sellerDetail,
        ], 200);
    }

    public function index(Request $request)
    {
        $query = Seller::query()->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);

        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
            $query->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
        }elseif ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by seller_user_id
        if ($request->input('seller_user_id')) {
            $query->where('seller_user_id', $request->input('seller_user_id'));
        }

        // Filter by seller_name
        if ($request->input('seller_name')) {
            $query->where('seller_name', $request->input('seller_name'));
        }

        // Filter by seller_special_id
        if ($request->input('seller_special_id')) {
            $query->where('seller_special_id', $request->input('seller_special_id'));
        }

        // Filter by status
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $Sellers = $query->with([
            'user' => function ($query) {
                $query->select([
                    'id', 'special_id', 'name', 'email',
                    'address', 'pincode', 'company_name', 'phone','gst_number',
                    'pancard', 'state', 'city','tan', 'remember_token', 'status', 'sender', 'receiver', 'seller', 'buyer','added_by','bank_name','branch_name','bank_account_no','ifsc_code'

                ]);
            },
            'user.details', 'details', 'invoiceNumber'
        ])->get();

        // Get filtered Sellers
        // $Sellers = $query->with('user', 'details', 'invoiceNumber')->get();
        return response()->json([
            'data' => $Sellers,
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
        $Seller = Seller::where('id', $id)->with('user', 'details')->first();

        if (!$Seller) {
            return response()->json([
                'data' => null,
                'message' => 'Seller not found',
                'status_code' => 400,
            ], 400);
        }

        return response()->json([
            'data' => $Seller,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    // public function destroy($id)
    // {
    //     // Get the panel series number with the given ID.
    //     $Seller = Seller::find($id);

    //     // Return a 404 response if the panel series number doesn't exist
    //     if (!$Seller) {
    //         return response()->json([
    //             'data' => null,
    //             'message' => 'Seller not found.',
    //             'status_code' => 400,
    //         ], 400);
    //     }

    //     // // Check if the panel series number status is "terminated"
    //     // if ($Seller->status !== 'terminated') {
    //     //     return response()->json([
    //     //         'data' => null,
    //     //         'message' => 'Please terminate this panel series number first.',
    //     //         'status_code' => 400,
    //     //     ], 400);
    //     // }

    //     // Delete the panel series number.
    //     $Seller->delete();

    //     // Return a success message.
    //     return response()->json([
    //         'data' => null,
    //         'message' => 'Seller destroyed.',
    //         'status_code' => 200
    //     ]);
    // }
    public function delete($id)
    {
        // Get the panel series number with the given ID.
        $Seller = Seller::find($id);

        // Return a 404 response if the panel series number doesn't exist
        if (!$Seller) {
            return response()->json([
                'data' => null,
                'message' => 'Seller not found.',
                'status_code' => 400,
            ], 400);
        }

        // // Check if the panel series number status is "terminated"
        // if ($Seller->status !== 'terminated') {
        //     return response()->json([
        //         'data' => null,
        //         'message' => 'Please terminate this panel series number first.',
        //         'status_code' => 400,
        //     ], 400);
        // }

        // Delete the panel series number.
        $Seller->details()->delete();
        $Seller->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Seller destroyed.',
            'status_code' => 200
        ]);
    }
}
