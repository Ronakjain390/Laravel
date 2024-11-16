<?php

namespace App\Http\Controllers\V1\Admin\Auth;

use App\Models\Admin;
use App\Models\Feature;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthController extends Controller
{

    //

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Admin registered successfully', 'admin' => $admin]);
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::guard('admin')->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $admin = Auth::guard('admin')->user();

        // dd( Auth::guard('admin'));
        // dd($admin,$admin->createToken('admin')->accessToken);
        $token = $admin->createToken('admin', ['admin'])->accessToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }


    public function admin_details(Request $request)
    {
        if (Auth::guard(Auth::getDefaultDriver())->check()) {
            $user = Auth::guard(Auth::getDefaultDriver())->user();
            return response()->json(['data' => $user, 'data-type' => 'admin'], 200);
        }
        // return Response(['data' => 'Unauthorized'], 401);
        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized',
        ], 401);
    }

    public function admin_logout(): Response
    {
        if (Auth::guard(Auth::getDefaultDriver())->check()) {
            $accessToken = Auth::guard(Auth::getDefaultDriver())->user()->token();
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->update(['revoked' => true]);
            $accessToken->revoke();
            Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','admin')->delete();


            return Response(['data' => 'Unauthorized', 'message' => 'User logout successfully.'], 200);
        }
        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized',
        ], 401);
    }


    public function index(Request $request)
    {
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name',Auth::getDefaultDriver())->exists()) {
            $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);

            // Retrieve the active plan with panels and features
            $userWithActivePlan = User::with('plans.panel')->get();
            // $userWithActivePlan = User::get();
            
            // dd($userWithActivePlan);
            // $userId = $userWithActivePlan->id;
            // dd($userId);
            foreach ($userWithActivePlan as $key => $plan) {
                $plan->panel->features = Feature::select('features.id', 'features.feature_type_id', 'features.panel_id', 'features.feature_name', 'features.template_id', 'features.status')
                    ->where('features.panel_id', $plan->panel->id)
                    ->leftJoin('plan_feature_usage_records', function ($join) use ($userId) {
                        $join->on('plan_feature_usage_records.feature_id', '=', 'features.id')
                            ->join('orders as plan_orders', 'plan_feature_usage_records.order_id', '=', 'plan_orders.id')
                            ->where('plan_orders.user_id', $userId)
                            ->where('plan_feature_usage_records.usage_count', '!=', null)
                            ->where('plan_feature_usage_records.usage_limit', '!=', null)
                            ->where('plan_orders.status', 'active');
                    })
                    ->leftJoin('feature_topup_usage_records', function ($join) use ($userId) {
                        $join->on('feature_topup_usage_records.feature_id', '=', 'features.id')
                            ->join('orders as topup_orders', 'feature_topup_usage_records.order_id', '=', 'topup_orders.id')
                            ->where('topup_orders.user_id', $userId)
                            ->where('topup_orders.status', 'active');
                    })
                    ->groupBy('features.id', 'features.feature_type_id', 'features.feature_name', 'features.template_id', 'features.status', 'features.panel_id', 'plan_feature_usage_records.usage_count', 'plan_feature_usage_records.usage_limit')
                    // ->select('plan_feature_usage_records.usage_count','plan_feature_usage_records.usage_limit')

                    ->selectRaw('SUM(plan_feature_usage_records.usage_count) AS total_usage_count')
                    ->selectRaw('SUM(plan_feature_usage_records.usage_limit) AS total_usage_limit')
                    ->selectRaw('SUM(plan_feature_usage_records.usage_limit - plan_feature_usage_records.usage_count) AS total_available_usage')
                    ->selectRaw('SUM(feature_topup_usage_records.usage_count) AS total_usage_count_topup')
                    ->selectRaw('SUM(feature_topup_usage_records.usage_limit) AS total_usage_limit_topup')
                    ->selectRaw('SUM(feature_topup_usage_records.usage_limit - feature_topup_usage_records.usage_count) AS total_available_usage_topup')
                    ->with('template')
                    ->get();
            }
            // dd($userWithActivePlan);
            // Return the user data
            return response()->json([
                'success' => true,
                'message' => 'User data retrieved successfully',
                'user' => $userWithActivePlan,
            ]);
        }

        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized',
        ], 401);
    }

    // public function allUsers(Request $request){
    //     if (Auth::guard(Auth::getDefaultDriver())->check()) {
    //         $allUsers = User::with('plans.panel')->latest()->paginate(50);
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'User data retrieved successfully',
    //             'data' => $allUsers,
    //         ]);
    //     }
    //     // return Response(['data' => 'Unauthorized'], 401);
    //     return response()->json([
    //         'status' => 401,
    //         'message' => 'Unauthorized',
    //     ], 401);
    // }

    // public function allUsers(Request $request){
    //     $perPage = 100;
    //         $page = $request->page ?? 1;
    //     if (Auth::guard(Auth::getDefaultDriver())->check()) {
    //         $allUsers = User::select('users.*')
    //         ->selectRaw('(SELECT COUNT(*) FROM challans WHERE challans.sender_id = users.id) as challan_count')
    //         ->selectRaw('(SELECT COUNT(*) FROM return_challans WHERE return_challans.sender_id = users.id) as returnchallans_count')
    //         ->selectRaw('(SELECT COUNT(*) FROM invoices WHERE invoices.seller_id = users.id) as invoice_count')
    //         ->selectRaw('(SELECT COUNT(*) FROM purchase_orders WHERE purchase_orders.seller_id = users.id) as purchaseorders_count')
    //         ->orderBy('id', 'desc')
    //         ->paginate($perPage, null, null, $page);
    //         // Calculate the starting item number for the current page
    //         $startItemNumber = ($page - 1) * $perPage + 1;

    //         // Add a custom attribute to each item in the collection with the calculated item number
    //         $allUsers->each(function ($item) use (&$startItemNumber) {
    //             $item->setAttribute('custom_item_number', $startItemNumber++);
    //         });
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'User data retrieved successfully',
    //             'data' => $allUsers,
    //         ]);
    //     }
    //     // return Response(['data' => 'Unauthorized'], 401);
    //     return response()->json([
    //         'status' => 401,
    //         'message' => 'Unauthorized',
    //     ], 401);
    // }
    
    // public function allUsers(Request $request,  $searchTerm = null){
    //     $perPage = 100;
    //     $page = $request->page ?? 1;
    
    //     // Fetch users based on search term if provided
    //     $query = User::select('users.*')
    //         ->selectRaw('(SELECT COUNT(*) FROM challans WHERE challans.sender_id = users.id) as challan_count')
    //         ->selectRaw('(SELECT COUNT(*) FROM return_challans WHERE return_challans.sender_id = users.id) as returnchallans_count')
    //         ->selectRaw('(SELECT COUNT(*) FROM invoices WHERE invoices.seller_id = users.id) as invoice_count')
    //         ->selectRaw('(SELECT COUNT(*) FROM purchase_orders WHERE purchase_orders.seller_id = users.id) as purchaseorders_count');
    
    
    //     $allUsers = $query->orderBy('id', 'desc')->paginate($perPage, null, null, $page);
    
    //     // Calculate the starting item number for the current page
    //     $startItemNumber = ($page - 1) * $perPage + 1;
    
    //     // Add a custom attribute to each item in the collection with the calculated item number
    //     $allUsers->each(function ($item) use (&$startItemNumber) {
    //         $item->setAttribute('custom_item_number', $startItemNumber++);
    //     });
    
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'User data retrieved successfully',
    //         'data' => $allUsers,
    //     ]);
    // }
    
        public function allUsers(Request $request)
    {
        $perPage = 100;
        $page = $request->page ?? 1;
        $query = User::select('users.*') // Select all columns from users table
        ->selectRaw('(SELECT COUNT(*) FROM challans WHERE challans.sender_id = users.id) as challan_count')
        ->selectRaw('(SELECT COUNT(*) FROM return_challans WHERE return_challans.sender_id = users.id) as returnchallans_count')
        ->selectRaw('(SELECT COUNT(*) FROM invoices WHERE invoices.seller_id = users.id) as invoice_count')
        ->selectRaw('(SELECT COUNT(*) FROM purchase_orders WHERE purchase_orders.seller_id = users.id) as purchaseorders_count')
        ->selectRaw('users.added_by')
        ->leftJoin('users as added_users', 'users.added_by', '=', 'added_users.id')
        ->addSelect('added_users.name as added_by_name');
    
    $searchTerm = $request->search;
    if ($searchTerm) {
        $query->where(function ($query) use ($searchTerm) {
            $query->where('users.name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('users.email', 'LIKE', "%{$searchTerm}%")
                ->orWhere('users.phone', 'LIKE', "%{$searchTerm}%")
                ->orWhere('users.company_name', 'LIKE', "%{$searchTerm}%");
        });
    }
    
    // Check if the request has the 'test_users' parameter
    if ($request->has('test_users')) {
        $query->where('users.test_users', true);
    } else {
        $query->where('users.test_users', false);
    }

        $allUsers = $query->orderBy('id', 'desc')->paginate(100, ['*'], 'page', $page); 
        // dd($allUsers);
        // Calculate the starting item number for the current page
        $startItemNumber = ($page - 1) * $perPage + 1;

        // Add a custom attribute to each item in the collection with the calculated item number
        $allUsers->each(function ($item) use (&$startItemNumber) {
            $item->setAttribute('custom_item_number', $startItemNumber++);
        });
        // dd($allUsers);
        return response()->json([
            'success' => true,
            'message' => 'User data retrieved successfully',
            'data' => $allUsers,
        ]);
    }


    public function update(Request $request,$userId)
    {
        $user = User::find($userId);
    // dd($user);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'status_code' => 404,
            ]);
        }
        $validator = Validator::make($request->all(), [
            'sender' => 'nullable|in:0,1',
        ]);
        // dd($validator);
        // Add these debug statements
        // dd([
        //     'UserId' => $userId,
        //     $request->receiver, $request->seller, 

        //     $request->sender
        //     // 'SenderValue' => $senderValue,
        //     // 'IsSenderValueBoolean' => is_bool($senderValue),
        // ]);
            if($request->sender){

           
        $user->update([
            'sender' => $request->sender,
            // 'receiver' => $request->receiver,
            // 'seller' => $request->seller,
            // 'buyer' => $request->buyer,

        ]);
    }elseif($request->receiver){
        $user->update([
            // 'sender' => $request->sender,
            'receiver' => $request->receiver,
            // 'seller' => $request->seller,
            // 'buyer' => $request->buyer,

        ]);
    }elseif($request->seller){
        $user->update([
            // 'sender' => $request->sender,
            // 'receiver' => $request->receiver,
            'seller' => $request->seller,
            // 'buyer' => $request->buyer,

        ]);
    }elseif($request->buyer){
        $user->update([
            // 'sender' => $request->sender,
            // 'receiver' => $request->receiver,
            // 'seller' => $request->seller,
            'buyer' => $request->buyer,

        ]);
    }
        // dd($user->update);
        return response()->json(['success' => true, 'message' => 'Toggle value updated successfully']);
    }
    
    public function removeUser()
    {
        // update test_users to true
        $request = request();
        $request->merge(['test_users' => true]);
        $userId = $request->id;
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'status_code' => 404,
            ]);
        }
        $user->update([
            'test_users' => $request->test_users,
        ]);
        return response()->json(['success' => true, 'message' => 'User removed successfully']);
    }
}


