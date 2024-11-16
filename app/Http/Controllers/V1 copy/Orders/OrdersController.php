<?php

namespace App\Http\Controllers\Web\Orders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Plans;
use App\Models\TeamUser;
use App\Models\FeatureTopup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AdditionalFeatureTopup;
use App\Models\PlanFeatureUsageRecord;
use App\Models\FeatureTopupUsageRecord;
use Illuminate\Support\Facades\Validator;
use App\Models\PlanAdditionalFeatureUsageRecord;
use App\Models\AdditionalFeatureTopupUsageRecord;

class OrdersController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'plan_ids' => 'required|array',
            'plan_ids.*' => 'integer|exists:plans,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
            if ($request->user_id != Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id) {
                return response()->json([
                    'errors' => 'Incorrect User Id',
                    'status_code' => 422,
                ], 422);
            }
        }

        try {
            foreach ($request->plan_ids as $plan_id) {
                $existingPlanOrder = Order::where('plan_id', $plan_id)
                    ->where('user_id', $request->user_id)
                    ->where('purchase_date', today()->format('Y-m-d H:i:s'))
                    ->latest()->exists();
                if ($existingPlanOrder) {

                    // Plan with the same name already exists
                    return response()->json([
                        'data' => false,
                        'message' => 'Order for same plan already purchased today.',
                        'status_code' => 409
                    ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
                }

                $plan = Plans::where('id', $plan_id)->with('features', 'additionalFeatures')->first();
                if ($plan) {
                    $order = new Order();
                    $order->user_id = $request->user_id;
                    $order->plan_id = $plan_id;
                    $order->section_id = $plan->section_id;
                    $order->panel_id = $plan->panel_id;
                    $order->purchase_date = today()->format('Y-m-d H:i:s');
                    $order->expiry_date = today()->addDays($plan->validity_days);
                    $order->amount = $plan->price;
                    $order->status = 'active';
                    if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
                        $order->added_by = 'user';
                    } elseif (Auth::guard('admin-api')->check()) {
                        $order->added_by = 'admin';
                    } else {
                        $order->added_by = '';
                    }
                    $order->save();
                    foreach ($plan->features as $key => $feature) {
                        $planFeatureUsageRecord = new PlanFeatureUsageRecord();
                        $planFeatureUsageRecord->order_id = $order->id;
                        $planFeatureUsageRecord->plan_feature_id = $feature->id;
                        $planFeatureUsageRecord->feature_id = $feature->feature_id;
                        $planFeatureUsageRecord->usage_count = 0;
                        $planFeatureUsageRecord->usage_limit = $feature->feature_usage_limit;
                        $planFeatureUsageRecord->status = 'active';
                        $planFeatureUsageRecord->save();
                    }

                    // dd(empty($plan->additionalFeatures),$plan->additionalFeatures);
                    if (!empty($plan->additionalFeatures)) {
                        foreach ($plan->additionalFeatures as $key => $add_feature) {
                            $planAdditionalFeatureUsageRecord = new PlanAdditionalFeatureUsageRecord();
                            $planAdditionalFeatureUsageRecord->order_id = $order->id;
                            $planAdditionalFeatureUsageRecord->plan_additional_feature_id = $add_feature->id;
                            $planAdditionalFeatureUsageRecord->additional_feature_id = $add_feature->additional_feature_id;
                            $planAdditionalFeatureUsageRecord->usage_count = 0;
                            $planAdditionalFeatureUsageRecord->usage_limit = $add_feature->additional_feature_usage_limit;
                            $planAdditionalFeatureUsageRecord->status = 'active';
                            $planAdditionalFeatureUsageRecord->save();
                        }
                    }
                } else {

                    return response()->json([
                        'data' => $request->plan_id,
                        'message' => 'Invalid Plan Id.',
                        'status_code' => 409
                    ], 409);
                }
            }
            return response()->json([
                'data' => $order,
                'message' => 'Order Placed sucessfully',
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            // Handle the exception or re-throw it
            // throw $e;

            return response()->json([
                'message' => 'An error occurred while processing the order.',
                'error' => $e->getMessage(),
                'status_code' => 201
            ], 201);
        }
    }

    public function topupOrderStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'feature_topup_ids' => 'required|array',
            'feature_topup_ids.*' => 'integer|exists:feature_topups,id',
            'additional_feature_topup_ids' => 'required|array',
            'additional_feature_topup_ids.*' => 'integer|exists:additional_feature_topups,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
            if ($request->user_id != Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id) {
                return response()->json([
                    'errors' => 'Incorrect User Id',
                    'status_code' => 422,
                ], 422);
            }
        }

        $existingPlanOrder = Order::where('user_id', $request->user_id)
            ->where('status', 'active')
            ->latest()->first();

        try {
            foreach ($request->feature_topup_ids as $key => $feature_topup_id) {

                $existingFeatureTopupOrder = FeatureTopupUsageRecord::where('feature_topup_id', $feature_topup_id)
                    ->where('order_id', $existingPlanOrder->id)
                    ->where('created_at', today())
                    ->first();

                if ($existingFeatureTopupOrder) {
                    // Plan with the same name already exists


                    return response()->json([
                        'data' => $existingFeatureTopupOrder,
                        'message' => 'TopUp for same feature already done today.',
                        'status_code' => 409
                    ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
                }

                $FeatureTopup = FeatureTopup::where('id', $feature_topup_id)->first();

                $planFeatureUsageRecord = PlanFeatureUsageRecord::where('order_id', $existingPlanOrder->id)
                    ->where('feature_id', $FeatureTopup->feature_id)->where('status', 'active')->latest()->first();

                if ($planFeatureUsageRecord) {
                    $FeatureTopupUsageRecord = new FeatureTopupUsageRecord();
                    $FeatureTopupUsageRecord->order_id = $planFeatureUsageRecord->order_id;
                    $FeatureTopupUsageRecord->feature_topup_id = $FeatureTopup->id;
                    $FeatureTopupUsageRecord->feature_id = $FeatureTopup->feature_id;
                    $FeatureTopupUsageRecord->usage_count = 0;
                    $FeatureTopupUsageRecord->usage_limit = $FeatureTopup->usage_limit;
                    $FeatureTopupUsageRecord->amount = $FeatureTopup->price;
                    // $FeatureTopupUsageRecord->status = 'active';
                    if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
                        $FeatureTopupUsageRecord->added_by = 'user';
                    } elseif (Auth::guard('admin-api')->check()) {
                        $FeatureTopupUsageRecord->added_by = 'admin';
                    } else {
                        $FeatureTopupUsageRecord->added_by = '';
                    }
                    $FeatureTopupUsageRecord->save();
                } else {

                    return response()->json([
                        'data' => $request->plan_id,
                        'message' => 'The Feature for this TopUp is not available in your active plan.',
                        'status_code' => 409
                    ], 409);
                }
            }

            foreach ($request->additional_feature_topup_ids as $key => $additional_feature_topup_id) {
                $existingAdditionalFeatureTopupUsageRecord = AdditionalFeatureTopupUsageRecord::where('additional_feature_topup_id', $additional_feature_topup_id)
                    ->where('order_id', $existingPlanOrder->id)
                    ->where('created_at', today())
                    ->first();

                if ($existingAdditionalFeatureTopupUsageRecord) {
                    // Plan with the same name already exists


                    return response()->json([
                        'data' => $existingAdditionalFeatureTopupUsageRecord,
                        'message' => 'TopUp for same additional feature already done today.',
                        'status_code' => 409
                    ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
                }

                $additionalFeatureTopup = AdditionalFeatureTopup::where('id', $additional_feature_topup_id)->first();

                $planFeatureUsageRecord = PlanFeatureUsageRecord::where('order_id', $existingPlanOrder->id)
                    ->where('feature_id', $FeatureTopup->feature_id)->where('status', 'active')->latest()->first();

                if ($planFeatureUsageRecord) {
                    $additionalFeatureTopupUsageRecord = new AdditionalFeatureTopupUsageRecord();
                    $additionalFeatureTopupUsageRecord->order_id = $planFeatureUsageRecord->order_id;
                    $additionalFeatureTopupUsageRecord->additional_feature_topup_id = $additionalFeatureTopup->id;
                    $additionalFeatureTopupUsageRecord->additional_feature_id = $additionalFeatureTopup->additional_feature_id;
                    $additionalFeatureTopupUsageRecord->usage_count = 0;
                    $additionalFeatureTopupUsageRecord->usage_limit = $additionalFeatureTopup->usage_limit;
                    $additionalFeatureTopupUsageRecord->amount = $additionalFeatureTopup->price;
                    // $additionalFeatureTopupUsageRecord->status = 'active';
                    if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
                        $additionalFeatureTopupUsageRecord->added_by = 'user';
                    } elseif (Auth::guard('admin-api')->check()) {
                        $additionalFeatureTopupUsageRecord->added_by = 'admin';
                    } else {
                        $additionalFeatureTopupUsageRecord->added_by = '';
                    }
                    $additionalFeatureTopupUsageRecord->save();
                } else {

                    return response()->json([
                        'data' => $request->plan_id,
                        'message' => 'The Feature for this TopUp is not available in your active plan.',
                        'status_code' => 409
                    ], 409);
                }
            }

            return response()->json([
                // 'data' => ,
                'message' => 'TopUp done sucessfully',
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            // Handle the exception or re-throw it
            // throw $e;

            return response()->json([
                'message' => 'An error occurred while processing the order.',
                'error' => $e->getMessage(),
                'status_code' => 200
            ], 200);
        }
    }

    public function index(Request $request)
    {
        $order = Order::query();

        if ($request->has('user_id')) {
            $order->where('user_id', $request->input('user_id'));
        }

        if ($request->has('plan_id')) {
            $order->where('plan_id', $request->input('plan_id'));
        }

        if ($request->has('section_id')) {
            $order->where('section_id', $request->input('section_id'));
        }

        if ($request->has('panel_id')) {
            $order->where('panel_id', $request->input('panel_id'));
        }

        if ($request->has('purchase_date')) {
            $order->whereDate('purchase_date', $request->input('purchase_date'));
        }

        if ($request->has('expiry_date')) {
            $order->whereDate('expiry_date', $request->input('expiry_date'));
        }

        if ($request->has('amount')) {
            $order->where('amount', $request->input('amount'));
        }

        if ($request->has('status')) {
            $order->where('status', $request->input('status'));
        }

        $order = $order->with('featureUsageRecords', 'additionalFeatureUsageRecords', 'featureTopupUsageRecords', 'additionalFeatureTopupUsageRecords')->latest()->paginate(50);
        return response()->json([
            'data' => $order,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function userIndex(Request $request)
    {
        $order = Order::query();

        $order->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);

        if ($request->has('plan_id')) {
            $order->where('plan_id', $request->input('plan_id'));
        }

        if ($request->has('section_id')) {
            $order->where('section_id', $request->input('section_id'));
        }

        if ($request->has('panel_id')) {
            $order->where('panel_id', $request->input('panel_id'));
        }

        if ($request->has('purchase_date')) {
            $order->whereDate('purchase_date', $request->input('purchase_date'));
        }

        if ($request->has('expiry_date')) {
            $order->whereDate('expiry_date', $request->input('expiry_date'));
        }

        if ($request->has('amount')) {
            $order->where('amount', $request->input('amount'));
        }

        if ($request->has('status')) {
            $order->where('status', $request->input('status'));
        }

        $order = $order->with('featureUsageRecords', 'additionalFeatureUsageRecords', 'featureTopupUsageRecords', 'additionalFeatureTopupUsageRecords')->latest()->paginate(50);
        return response()->json([
            'data' => $order,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function show($request, $id)
    {
        $order = Order::where('id', $id);
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
            $order->user_id = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        }
        $order->with('featureUsageRecords', 'additionalFeatureUsageRecords', 'featureTopupUsageRecords', 'additionalFeatureTopupUsageRecords')->first();

        if (!$order) {
            return response()->json([
                'data' => null,
                'message' => 'Order not found',
                'status_code' => 200,
            ], 200);
        }

        return response()->json([
            'data' => $order,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

}
