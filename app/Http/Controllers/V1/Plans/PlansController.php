<?php

namespace App\Http\Controllers\V1\Plans;

use App\Models\Plan;
use App\Models\Plans;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
use App\Models\Feature;
use App\Models\TeamUser;

class PlansController extends Controller
{

    public function index(Request $request)
    { 
        $plans = Plans::query();

        // Filter by plan name
        if ($request->has('plan_name')) {
            $plans->where('plan_name', 'like', '%' . $request->input('plan_name') . '%');
        }

        // Filter by section ID
        if ($request->has('section_id')) {
            $plans->where('section_id', $request->input('section_id'));
        }

        // Filter by panel ID
        if ($request->has('panel_id')) {
            $plans->where('panel_id', $request->input('panel_id'));
        }

        // Filter by enterprise plan status
        if ($request->has('is_enterprise_plan')) {
            $plans->where('is_enterprise_plan', $request->input('is_enterprise_plan'));
        }

        // Filter by enterprise user ID
        if ($request->has('enterprise_user_id')) {
            $plans->where('enterprise_user_id', $request->input('enterprise_user_id'));
        }

        // Filter by validity days
        if ($request->has('validity_days')) {
            $plans->where('validity_days', $request->input('validity_days'));
        }

        // Filter by status
        if ($request->has('status')) {
            $plans->where('status', $request->input('status'));
        }

        // Get the associated features and additional features
        $plans->with('features', 'additionalFeatures', 'additionalFeatureTopups');

        $plans = $plans->get();
        return response()->json([
            'data' => $plans,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }


    public function show($id)
    {
        $Plan = Plans::where('id', $id)->with('features', 'additionalFeatures')->first();

        if (!$Plan) {
            return response()->json([
                'data' => null,
                'message' => 'Plan not found',
                'status_code' => 400,
            ], 400);
        }

        return response()->json([
            'data' => $Plan,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }
 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_name' => 'required|max:255',
            'price' => 'required|integer',
            'discounted_price' => 'nullable|integer',
            'section_id' => 'required|integer|exists:section,id',
            'panel_id' => 'required|integer|exists:panel,id',
            'is_enterprise_plan' => 'integer|nullable',
            'enterprise_user_id' => 'integer|nullable',
            'validity_days' => 'integer|nullable',
            'status' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $data = $request->all();

        // Convert empty strings to null
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }

        $Plan = Plans::create($data);

        return response()->json([
            'data' => $Plan,
            'message' => 'Plan Created',
            'status_code' => 201
        ], 201);
    }
    

    public function update(Request $request, $id)
    {
        // dd($request);
        $validator = Validator::make($request->all(), [
            'plan_name' => 'required|max:255',
            'price' => 'required',
            'section_id' => 'required|integer|exists:section,id',
            'panel_id' => 'required|integer|exists:panel,id',
            'is_enterprise_plan' => 'integer|nullable',
            'enterprise_user_id' => 'integer|nullable',
            'validity_days' => 'integer',
            'status' => 'string',


        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $Plan = Plans::where('id', $id);

        if (!$Plan) {
            return response()->json([
                'data' => null,
                'message' => 'Plan not found',
                'status_code' => 400,
            ], 400);
        }

        $Plan->update($request->all());

        return response()->json([
            'data' => $Plan,
            'message' => 'Plan Updated',
            'status_code' => 200
        ]);
    }

    public function delete($id)
    {
        // Get the Plan with the given ID.
        // $Plan = Plans::where('id', $id)->get();
        $Plan = Plans::find($id);
// dd($Plan->status);
        // Delete the Plan.
        // $Plan->delete();

        // Return a 404 response if the Plan doesn't exist
        if ($Plan->status == 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Plan Already deleted.',
                'status_code' => 400,
            ], 400);
        }

        // Fill in the Plan's data.
        $Plan->status = 'terminated';

        // Save the Plan.
        $Plan->save();

        // Update the updated_at timestamp
        $Plan->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Plan Deleted',
            'status_code' => 200
        ]);
    }


    // Destroy a Plan
    public function destroy($id)
    {
        // Get the Plan with the given ID.
        // $Plan = Plans::where('id', $id);
        $Plan = Plans::find($id);
        // Return a 404 response if the Plan doesn't exist
        if (!$Plan) {
            return response()->json([
                'data' => null,
                'message' => 'Plan Already Destroyed.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the Plan status is "terminated"
        if ($Plan->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this Plan first.',
                'status_code' => 400,
            ], 400);
        }

        // Delete the Plan.
        $Plan->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Plan Destroyed',
            'status_code' => 200
        ]);
    }

        public function planCheckout(Request $request)
    {
        // dd($request->input('plan_ids'));

        $planIds = $request->input('plan_ids'); // Assuming you pass an array of plan IDs
        $plans = Plans::query();

        // Filter by plan IDs
        if ($planIds) {
            $plans->whereIn('id', $planIds);
        }

        // Get the associated features and additional features
        $plans->with('features', 'additionalFeatures', 'additionalFeatureTopups');

        $plans = $plans->get();

        return response()->json([
            'data' => $plans,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    // public function userDetails(Request $request)
    // {
    //     if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
    //         $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
    //         // Retrieve the active plan with panels and features
    //         $userWithActivePlan = User::where('id', $user->id)->with('plansActive.panel')->first();

    //         $userId = $userWithActivePlan->id;
    //         foreach ($userWithActivePlan->plans as $key => $plan) {
    //             // dd($userWithActivePlan,$plan);
    //             $plan->panel->feature = Feature::select('features.id', 'features.feature_type_id', 'features.panel_id', 'features.feature_name', 'features.template_id', 'features.status')
    //                 ->where('features.panel_id', $plan->panel->id)
    //                 ->leftJoin('plan_feature_usage_records', function ($join) use ($userId) {
    //                     $join->on('plan_feature_usage_records.feature_id', '=', 'features.id')
    //                         ->join('orders as plan_orders', 'plan_feature_usage_records.order_id', '=', 'plan_orders.id')
    //                         ->where('plan_orders.user_id', $userId)
    //                         ->where('plan_feature_usage_records.usage_count', '!=', null)
    //                         ->where('plan_feature_usage_records.usage_limit', '!=', null)
    //                         ->where('plan_orders.status', 'active');
    //                 })
    //                 ->leftJoin('feature_topup_usage_records', function ($join) use ($userId) {
    //                     $join->on('feature_topup_usage_records.feature_id', '=', 'features.id')
    //                         ->join('orders as topup_orders', 'feature_topup_usage_records.order_id', '=', 'topup_orders.id')
    //                         ->where('topup_orders.user_id', $userId)
    //                         ->where('topup_orders.status', 'active');
    //                 })
    //                 ->groupBy('features.id', 'features.feature_type_id', 'features.feature_name', 'features.template_id', 'features.status', 'features.panel_id', 'plan_feature_usage_records.usage_count', 'plan_feature_usage_records.usage_limit')
    //                 // ->select('plan_feature_usage_records.usage_count','plan_feature_usage_records.usage_limit')

    //                 ->selectRaw('SUM(plan_feature_usage_records.usage_count) AS total_usage_count')
    //                 ->selectRaw('SUM(plan_feature_usage_records.usage_limit) AS total_usage_limit')
    //                 ->selectRaw('SUM(plan_feature_usage_records.usage_limit - plan_feature_usage_records.usage_count) AS total_available_usage')
    //                 ->selectRaw('SUM(feature_topup_usage_records.usage_count) AS total_usage_count_topup')
    //                 ->selectRaw('SUM(feature_topup_usage_records.usage_limit) AS total_usage_limit_topup')
    //                 ->selectRaw('SUM(feature_topup_usage_records.usage_limit - feature_topup_usage_records.usage_count) AS total_available_usage_topup')

    //                 ->with('plans')
    //                 ->get();
    //         }
    //         $userWithActivePlan->team_user = null;
    //         // dd($userWithActivePlan);
    //         if (Auth::getDefaultDriver() == "team-user") {
    //             $teamUser = TeamUser::find(Auth::guard(Auth::getDefaultDriver())->user()->id)->with('permissions')->first();
    //             $teamUser->permissions->permission = json_decode($teamUser->permissions->permission);
    //             $userWithActivePlan->team_user = $teamUser;
    //         }

    //         // Return the user data
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'User data retrieved successfully',
    //             'user' => $userWithActivePlan,
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'status' => 401,
    //         'message' => 'Unauthorized',
    //     ], 401);
    // }
    public function user_details(Request $request)
    {
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
            $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
            // Retrieve the active plan with panels and features
            $userWithActivePlan = User::where('id', $user->id)->with('plans.panel')->first();

            $userId = $userWithActivePlan->id;
            foreach ($userWithActivePlan->plans as $key => $plan) {
                // dd($userWithActivePlan,$plan);
                // $plan->panel->feature = Feature::select('features.id', 'features.feature_type_id', 'features.panel_id', 'features.feature_name', 'features.template_id', 'features.status')
                //     ->where('features.panel_id', $plan->panel->id)
                $plan->panel->feature = Feature::select('features.id', 'features.feature_type_id', 'features.panel_id', 'features.feature_name', 'features.template_id', 'features.status')
                ->where('features.panel_id', $plan->panel->id)

                    ->leftJoin('plan_feature_usage_records', function ($join) use ($userId) {
                        $join->on('plan_feature_usage_records.feature_id', '=', 'features.id')
                            ->join('orders as plan_orders', 'plan_feature_usage_records.order_id', '=', 'plan_orders.id')
                            ->where('plan_orders.user_id', $userId)
                            ->where('plan_orders.id', $userId)
                            ->where('plan_feature_usage_records.usage_count', '!=', null)
                            ->where('plan_feature_usage_records.usage_limit', '!=', null)
                            ->distinct('plan_feature_usage_records.feature_id')
                            ->where('plan_orders.status', 'active')->limit(1);
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
                    ->with('template', 'sentChallans')
                    ->get();
            }
            $userWithActivePlan->team_user = null;
            // dd($userWithActivePlan);
            if (Auth::getDefaultDriver() == "team-user") {
                $teamUser = TeamUser::find(Auth::guard(Auth::getDefaultDriver())->user()->id)->with('permissions')->first();
                $teamUser->permissions->permission = json_decode($teamUser->permissions->permission);
                $userWithActivePlan->team_user = $teamUser;
            }

            // Return the user data
            return response()->json([
                'success' => true,
                'message' => 'User data retrieved successfully',
                'user' => $userWithActivePlan,
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => 401,
            'message' => 'Unauthorized',
        ], 401);
    }
}
