<?php

namespace App\Http\Controllers\V1\Orders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Plans;
use App\Models\Feature;
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
use App\Services\PDFServices\PDFEmailService;
use App\Models\PlanAdditionalFeatureUsageRecord;
use App\Services\PDFServices\PDFWhatsAppService;
use App\Models\AdditionalFeatureTopupUsageRecord;
use App\Services\PDFServices\PDFGeneratorService;

class OrdersController extends Controller
{
    public function importStore(Request $request)
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
        // if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
        //     if ($request->user_id !=  Auth::guard(Auth::getDefaultDriver())->user()->id) {
        //         return response()->json([
        //             'errors' => 'Incorrect User Id',
        //             'status_code' => 422,
        //         ], 422);
        //     }
        // }

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
                    // if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
                        $order->added_by = 'user';
                    // } elseif (Auth::guard(Auth::getDefaultDriver())->check()) {
                    //     $order->added_by = 'admin';
                    // } else {
                    //     $order->added_by = '';
                    // }
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

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'plan_ids' => 'required|array',
            'plan_ids.*' => 'integer|exists:plans,id',
            'amount' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
            if ($request->user_id !=  Auth::guard(Auth::getDefaultDriver())->user()->id) {
                return response()->json([
                    'errors' => 'Incorrect User Id',
                    'status_code' => 422,
                ], 422);
            }
        }

        try {
            foreach ($request->plan_ids as $plan_id) {

                $plan = Plans::where('id', $plan_id)->with('features', 'additionalFeatures')->first();
                if ($plan) {
                    $order = new Order();
                    $order->user_id = $request->user_id;
                    $order->plan_id = $plan_id;
                    $order->section_id = $plan->section_id;
                    $order->panel_id = $plan->panel_id;
                    $order->purchase_date = today()->format('Y-m-d H:i:s');
                    $order->expiry_date = today()->addDays($plan->validity_days);
                    $order->amount = $request->amount;
                    $order->status = 'active';
                    if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
                        $order->added_by = 'user';
                    } elseif (Auth::guard(Auth::getDefaultDriver())->check()) {
                        $order->added_by = 'admin';
                    } else {
                        $order->added_by = '';
                    }
                    $order->save();
                   // Check if plan_id is not in [3, 11, 10, 19]
                    if (!in_array($plan_id, [3, 11, 10, 19])) {
                        // Update the sender, receiver, seller, and buyer fields in the users table based on panel_id
                        switch ($plan->panel_id) {
                            case 1:
                                User::where('id', $request->user_id)->update(['sender' => 1]);
                                break;
                            case 2:
                                User::where('id', $request->user_id)->update(['receiver' => 1]);
                                break;
                            case 3:
                                User::where('id', $request->user_id)->update(['seller' => 1]);
                                break;
                            case 4:
                                User::where('id', $request->user_id)->update(['buyer' => 1]);
                                break;
                            case 5:
                                User::where('id', $request->user_id)->update(['receipt_note' => 1]);
                                break;
                        }
                    }
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
                    $order = Order::where('user_id',  Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->with('featureUsageRecords', 'additionalFeatureUsageRecords', 'featureTopupUsageRecords', 'additionalFeatureTopupUsageRecords', 'plan', 'user')->latest()->first();
                    // dd($order->amount);
                    if($order->amount != 0){


                    $pdfGenerator = new PDFGeneratorService();
                    $response = $pdfGenerator->planInvoicePDF($order);
                    // dd($response);

                    $response = (array) $response->getData();
                    $order->pdf_url = $response['pdf_url'];
                    $order->save();

                    // dd($order);
                }

                } else {

                    return response()->json([
                        'data' => $request->plan_id,
                        'message' => 'Invalid Plan Id.',
                        'status_code' => 409
                    ], 409);
                }
            }
        //     dd($order);
        //     // $order = Order::query();

        //     $orders = Order::where('user_id',  Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->with('featureUsageRecords', 'additionalFeatureUsageRecords', 'featureTopupUsageRecords', 'additionalFeatureTopupUsageRecords', 'plan', 'user')->latest()->first();
        //     // dd($orders);
        //     $pdfGenerator = new PDFGeneratorService();
        //     $response = $pdfGenerator->planInvoicePDF($orders);
        //     // dd($response);

        //     $response = (array) $response->getData();
        //     if ($response['status_code'] === 200) {
        //         // dd($response['pdf_url']);
        // }
            return response()->json([
                'data' => $order,
                'message' => 'Order Placed sucessfully',
                'status_code' => 200
            ]);

            // Generate the PDF for the Invoice using PDFGenerator class

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

    public function storeAdmin(Request $request)
    {
        // dd($request->all());
            foreach ($request->plan_ids as $plan_id) {

                $plan = Plans::where('id', $plan_id)->with('features', 'additionalFeatures')->first();
                // dd($plan, $request->user_id);
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
                    } elseif (Auth::guard(Auth::getDefaultDriver())->check()) {
                        $order->added_by = 'admin';
                        $order->is_paid = $request->plan_type === 'paid' ? 1 : 0;
                    } else {
                        $order->added_by = '';
                    }
                    // dd($order);
                    if($order->is_paid == 'paid'){
                        $pdfGenerator = new PDFGeneratorService();
                        $response = $pdfGenerator->planInvoicePDF($order);
                        // dd($response);

                        $response = (array) $response->getData();
                        $order->pdf_url = $response['pdf_url'];
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


            return response()->json([
                'message' => 'An error occurred while processing the order.',
                'error' => $e->getMessage(),
                'status_code' => 201
            ], 201);
        // }
    }

    public function topupOrderStore(Request $request)
    {
        // dd($request);
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'feature_topup_ids' => 'required|array',
            'feature_topup_ids.*' => 'integer|exists:feature_topups,id',
            // 'additional_feature_topup_ids' => 'required|array',
            // 'additional_feature_topup_ids.*' => 'integer|exists:additional_feature_topups,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
            if ($request->user_id !=  Auth::guard(Auth::getDefaultDriver())->user()->id) {
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
                // dd($existingPlanOrder, $planFeatureUsageRecord);

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
                    } elseif (Auth::guard(Auth::getDefaultDriver())->check()) {
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

            // foreach ($request->additional_feature_topup_ids as $key => $additional_feature_topup_id) {
            //     $existingAdditionalFeatureTopupUsageRecord = AdditionalFeatureTopupUsageRecord::where('additional_feature_topup_id', $additional_feature_topup_id)
            //         ->where('order_id', $existingPlanOrder->id)
            //         ->where('created_at', today())
            //         ->first();

            //     if ($existingAdditionalFeatureTopupUsageRecord) {
            //         // Plan with the same name already exists


            //         return response()->json([
            //             'data' => $existingAdditionalFeatureTopupUsageRecord,
            //             'message' => 'TopUp for same additional feature already done today.',
            //             'status_code' => 409
            //         ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
            //     }

            //     $additionalFeatureTopup = AdditionalFeatureTopup::where('id', $additional_feature_topup_id)->first();

            //     $planFeatureUsageRecord = PlanFeatureUsageRecord::where('order_id', $existingPlanOrder->id)
            //         ->where('feature_id', $FeatureTopup->feature_id)->where('status', 'active')->latest()->first();

            //     if ($planFeatureUsageRecord) {
            //         $additionalFeatureTopupUsageRecord = new AdditionalFeatureTopupUsageRecord();
            //         $additionalFeatureTopupUsageRecord->order_id = $planFeatureUsageRecord->order_id;
            //         $additionalFeatureTopupUsageRecord->additional_feature_topup_id = $additionalFeatureTopup->id;
            //         $additionalFeatureTopupUsageRecord->additional_feature_id = $additionalFeatureTopup->additional_feature_id;
            //         $additionalFeatureTopupUsageRecord->usage_count = 0;
            //         $additionalFeatureTopupUsageRecord->usage_limit = $additionalFeatureTopup->usage_limit;
            //         $additionalFeatureTopupUsageRecord->amount = $additionalFeatureTopup->price;
            //         // $additionalFeatureTopupUsageRecord->status = 'active';
            //         if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
            //             $additionalFeatureTopupUsageRecord->added_by = 'user';
            //         } elseif (Auth::guard(Auth::getDefaultDriver())->check()) {
            //             $additionalFeatureTopupUsageRecord->added_by = 'admin';
            //         } else {
            //             $additionalFeatureTopupUsageRecord->added_by = '';
            //         }
            //         $additionalFeatureTopupUsageRecord->save();
            //     } else {

            //         return response()->json([
            //             'data' => $request->plan_id,
            //             'message' => 'The Feature for this TopUp is not available in your active plan.',
            //             'status_code' => 409
            //         ], 409);
            //     }
            // }

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

        $order = $order->with('plan.panel','featureUsageRecords.feature','additionalFeatureUsageRecords', 'featureTopupUsageRecords.feature', 'additionalFeatureTopupUsageRecords')->orderByDesc('id')->latest()->paginate(50);
        return response()->json([
            'data' => $order,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function userActivePlanIndex(Request $request)
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
            $order->where('status', 'active');
        }

        $order = $order->with('plan.panel','featureUsageRecords.feature','additionalFeatureUsageRecords', 'featureTopupUsageRecords.feature', 'additionalFeatureTopupUsageRecords')->orderByDesc('id')->latest()->paginate(50);
        // dd($order);
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
    public function user_details(Request $request)
    {
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
            $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
            // Retrieve the active plan with panels and features
            $userWithActivePlan = User::where('id', $user->id)->with('plansActive.panel')->first();

            $userId = $userWithActivePlan->id;
            foreach ($userWithActivePlan->plansActive as $key => $plan) {
                // dd($userWithActivePlan,$plan);
                $plan->panel->feature = Feature::select('features.id', 'features.feature_type_id', 'features.panel_id', 'features.feature_name', 'features.template_id', 'features.status')
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

                    ->with('plansActive')
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

    // public function user_details(Request $request)
    // {
    //     if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
    //         $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
    //         // Retrieve the active plan with panels and features
    //         $userWithActivePlan = User::where('id', $user->id)->with('plans.panel')->first();

    //         $userId = $userWithActivePlan->id;
    //         foreach ($userWithActivePlan->plans as $key => $plan) {
    //             // dd($userWithActivePlan,$plan);
    //             // $plan->panel->feature = Feature::select('features.id', 'features.feature_type_id', 'features.panel_id', 'features.feature_name', 'features.template_id', 'features.status')
    //             //     ->where('features.panel_id', $plan->panel->id)
    //             $plan->panel->feature = Feature::select('features.id', 'features.feature_type_id', 'features.panel_id', 'features.feature_name', 'features.template_id', 'features.status')
    //             ->where('features.panel_id', $plan->panel->id)

    //                 ->leftJoin('plan_feature_usage_records', function ($join) use ($userId) {
    //                     $join->on('plan_feature_usage_records.feature_id', '=', 'features.id')
    //                         ->join('orders as plan_orders', 'plan_feature_usage_records.order_id', '=', 'plan_orders.id')
    //                         ->where('plan_orders.user_id', $userId)
    //                         ->where('plan_orders.id', $userId)
    //                         ->where('plan_feature_usage_records.usage_count', '!=', null)
    //                         ->where('plan_feature_usage_records.usage_limit', '!=', null)
    //                         ->distinct('plan_feature_usage_records.feature_id')
    //                         ->where('plan_orders.status', 'active')->limit(1);
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
    //                 ->with('template', 'sentChallans')
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


}
