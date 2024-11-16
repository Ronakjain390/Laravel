<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Models\User;
use App\Models\Buyer;
use Illuminate\Support\Str;
use App\Models\BuyerDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    public function addManualBuyer(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:users,email',
            'buyer_name' => 'required|string|max:255',
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
        // Create a new user
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'special_id' => $specialId,
            // 'password' => bcrypt($request->input('password')),
            // Include other user details as needed
        ]);

        // Create the buyer
        $buyer = Buyer::create([
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'buyer_user_id' => $user->id,
            'buyer_name' => $request->input('buyer_name'),
            'status' => 'active',
            'buyer_special_id' => $specialId,
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
            'status' => 'required|in:active,inactive,terminated',
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

    public function index(Request $request)
    {
        $query = Buyer::query();

        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
            $query->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
        }elseif ($request->has('user_id')) {
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

        // Get filtered Buyers
        $Buyers = $query->with('user', 'details')->get();

        return response()->json([
            'data' => $Buyers,
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

    public function destroy($id)
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
        $Buyer->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Buyer destroyed.',
            'status_code' => 200
        ]);
    }
}
