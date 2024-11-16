<?php

namespace App\Http\Controllers\V1\ReceiverGoodsReceipt;
use App\Models\ReceiverGoodsReceipt;
use App\Models\ReceiverGoodsReceiptsDetails;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Env;
use Illuminate\Support\Facades\Validator;
class ReceiverGoodsReceiptsController extends Controller
{
    public function addManualReceiver(Request $request)
    {
        // dd($request);
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'receiver_name' => 'string|max:255',
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
        // $specialId = Str::random(10);
        // // Create a new user
        // $user = User::create([
        //     'name' => $request->input('receiver_name'),
        //     'email' => $request->input('email'),
        //     'phone' => $request->input('phone'),
        //     'special_id' => $specialId,
        //     'company_name' => $request->input('company_name'),
        //     'added_by' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        //     // 'password' => bcrypt($request->input('password')),
        //     // Include other user details as needed
        // ]);

        // Create the receiver
        $receiver = ReceiverGoodsReceipt::create([
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            // 'receiver_user_id' => $user->id,
            'receiver_name' => $request->input('receiver_name'),
            'status' => 'active',
            // 'receiver_special_id' => $specialId,
        ]);

        $pincode = $request->input('pincode');
        if($pincode === '' || $pincode === null){
            $pincode = null;
        }
        // Create the receiver detail
        $receiverDetail = ReceiverGoodsReceiptsDetails::create([
            'receiver_id' => $receiver->id,
            'address' => $request->input('address'),
            'pincode' => $pincode,
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
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

    public function index(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $receiverUserId = $request->input('receiver_user_id');
            $receiverName = $request->input('receiver_name');
            $receiverSpecialId = $request->input('receiver_special_id');
            $status = $request->input('status');

            $query = ReceiverGoodsReceipt::query();

            if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
                $query->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
            } elseif ($userId) {
                $query->where('user_id', $userId);
            }

            // $query->when($receiverUserId, function ($query, $receiverUserId) {
            //     return $query->where('receiver_user_id', $receiverUserId);
            // });

            $query->when($receiverName, function ($query, $receiverName) {
                return $query->where('receiver_name', $receiverName);
            });

            // $query->when($receiverSpecialId, function ($query, $receiverSpecialId) {
            //     return $query->where('receiver_special_id', $receiverSpecialId);
            // });

            $query->when($status, function ($query, $status) {
                return $query->where('status', $status);
            });


            $receivers = $query->with([
                'user' => function ($query) {
                    $query->select([
                        'id',  'name', 'email',
                        'address', 'pincode', 'company_name', 'phone','gst_number',
                        'pancard', 'state', 'city','tan', 'remember_token', 'status', 'sender', 'receiver', 'seller', 'buyer',
                    ]);
                },
                'user.details', 'details', 'seriesNumber'
            ])->get();

            $responseData = [
                'data' => $receivers,
                'message' => 'Success',
                'status_code' => 200
            ];

            return response()->json($responseData, 200);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ], 500);
            }
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

    public function delete($id)
    {
        // Get the panel series number with the given ID.
        $Receiver = ReceiverGoodsReceipt::find($id);

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

}
