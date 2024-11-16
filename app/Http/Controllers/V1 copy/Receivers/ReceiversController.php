<?php

namespace App\Http\Controllers\Web\Receivers;

use App\Models\User;
use App\Models\Receiver;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ReceiverDetails;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReceiversController extends Controller
{
    public function addReceiver(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'receiver_special_id' => 'required|string|max:255',
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

    public function addManualReceiver(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:users,email',
            'receiver_name' => 'required|string|max:255',
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
            'message' => 'Receiver Detail added successfully',
            'receiver_detail' => $receiverDetail,
        ], 200);
    }
    public function updateReceiver(Request $request, $receiverId)
    {
        // Validate the incoming request data for updating the receiver
        $validator = Validator::make($request->all(), [
            'receiver_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,terminated',
            // 'receiver_special_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

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
            'status' => $request->input('status'),
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
            'message' => 'Receiver Detail updated successfully',
            'receiver_detail' => $receiverDetail,
        ], 200);
    }

    public function index(Request $request)
    {
        $query = Receiver::query();

        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
            $query->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
        }elseif ($request->has('user_id')) {
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
        $Receivers = $query->with('user', 'details','seriesNumber')->get();

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
        $Receiver = Receiver::where('id', $id)->with('user', 'details','seriesNumber')->first();

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

    public function destroy($id)
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
        $Receiver->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Receiver destroyed.',
            'status_code' => 200
        ]);
    }
}
