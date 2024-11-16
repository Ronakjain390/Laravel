<?php

namespace App\Http\Controllers\V1\PurchaseOrder;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Buyer;
use App\Models\PurchaseOrder;
use App\Models\Invoice;
use App\Models\Wallet;
use App\Models\WalletLog;
use Illuminate\Support\Str;
use App\Models\BuyerDetails;
use Illuminate\Http\Request;
use App\Models\PurchaseOrderSfp;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\PurchaseOrderColumn;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseOrderStatus;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PlanFeatureUsageRecord;
use App\Models\FeatureTopupUsageRecord;
use Illuminate\Support\Facades\Validator;
use App\Services\PDFServices\PDFEmailService;
use App\Models\PlanAdditionalFeatureUsageRecord;
use App\Services\PDFServices\PDFWhatsAppService;
use App\Models\AdditionalFeatureTopupUsageRecord;
use App\Services\PDFServices\PDFGeneratorService;

class PurchaseOrderController extends Controller
{

    public function store(Request $request)
    {
        // dd($request);
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'purchase_order_series' => 'nullable|string',
            'order_date' => 'nullable|date',
            'buyer_id' => 'nullable|exists:users,id',
            'buyer_name' => 'nullable|string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            'order_details.*.unit' => 'nullable|string',
            'order_details.*.rate' => 'nullable|numeric|min:0',
            'order_details.*.qty' => 'nullable|numeric|min:0',
            'order_details.*.tax' => 'nullable|numeric|min:0',
            'order_details.*.details' => 'nullable|string',
            'order_details.*.total_amount' => 'numeric|min:0',
            'order_details.*.columns.*.column_name' => 'nullable|string',
            'order_details.*.columns.*.column_value' => 'nullable|string',
            'statuses.*.comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }


        // dd($request->invoice_series);
        $featureId = 22; // Replace with YOUR_FEATURE_ID
        // dd($featureId);
        // Validate usage limit for PlanFeatureUsageRecord
        $PlanFeatureUsageRecord = new PlanFeatureUsageRecord();
        $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->validateUsageLimit($featureId);
        // dd($PlanFeatureUsageRecordResponse);
        // Validate usage limit for FeatureTopupUsageRecord
        $FeatureTopupUsageRecord = new FeatureTopupUsageRecord();

        if ($PlanFeatureUsageRecordResponse != 'active') {
            // Update usage count for FeatureTopupUsageRecord
            $FeatureTopupUsageRecorddResponse = $FeatureTopupUsageRecord->validateUsageLimit($featureId);
            if ($FeatureTopupUsageRecorddResponse != 'active') {
                return response()->json([
                    'message' => 'Your Feature usage limit is over or expired.',
                    'invoice_id' => null,
                    'order_detail_ids' => null,
                    'order_column_ids' => null,
                    'status_ids' => null,
                    'status_code' => 200
                ], 200);
                // Handle the case when both usage counts could not be updated successfully
                // Add appropriate error handling or log the issue for further investigation.
            }
        }


        // Get the authenticated user
        $user = Auth::guard(Auth::getDefaultDriver())->user();

         // Get the latest purchase_order_series for the given purchase_order_series and user_id
        $latestSeriesNum = PurchaseOrder::where('purchase_order_series', $request->purchase_order_series)
            ->where('seller_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->max(DB::raw('CAST(series_num AS UNSIGNED)'));
        // Increment the latestSeriesNum for the new invoice
        $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

        // dd($seriesNum,  $request->seller_name, 'Buyer',$request->buyer_name , $request);

        // Create a new PurchaseOrder
        $purchaseOrder = new PurchaseOrder([
            'purchase_order_series' => $request->purchase_order_series,
            'series_num' => $request->series_num,
            'seller_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'seller_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
            'buyer_id' => $request->buyer_id,
            'buyer_name' => $request->buyer_name,
            'comment' => $request->comment,
            'total' => $request->total ?? 0.00,
            'order_date' => $request->order_date,
            'total' => isset($request->total) && $request->total != 0 ? (float) $request->total : null,
            'total_qty' => isset($request->total_qty) && $request->total_qty != 0 ? (float) $request->total_qty : null,
            'round_off' => $request->round_off ?? null,
            'team_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_id : null,
        ]);

        $purchaseOrder->save();
        // dd($purchaseOrder);
        // Create PurchaseOrder Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new PurchaseOrderDetail([
                    'purchase_order_id' => $purchaseOrder->id,
                    'unit' => $orderDetailData['unit'] ?? null,
                    'rate' => $orderDetailData['rate'] ?? null,
                    'qty' => $orderDetailData['qty'] ?? null,
                    'tax' => $orderDetailData['tax'] ?? 0,
                    'total_amount' => $orderDetailData['total_amount'] ?? 0.00,
                ]);
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new PurchaseOrderColumn([
                            'purchase_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ?? '',
                        ]);
                        $orderColumn->save();
                    }
                }
            }
        }

        // Create PurchaseOrder Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new PurchaseOrderStatus([
                    'purchase_order_id' => $purchaseOrder->id,
                    'user_id' => $user->id,
                    'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    'team_user_name' => Auth::user()->team_user_name ?? null,
                    'status' => 'created',
                    'comment' => 'Purchase Order created successfully',
                ]);
                $status->save();
            }
        }

        // Get the IDs of the created records
        $purchaseOrderId = $purchaseOrder->id;
        $orderDetailIds = $purchaseOrder->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $purchaseOrder->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $purchaseOrder->statuses->pluck('id')->toArray();

        $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount($featureId, 1);

        if (!$PlanFeatureUsageRecordResponse) {
            // Update usage count for FeatureTopupUsageRecord
            $FeatureTopupUsageRecorddResponse = $FeatureTopupUsageRecord->updateUsageCount($featureId, 1);

            if (!$FeatureTopupUsageRecorddResponse) {
                return response()->json([
                    'message' => 'Something Went Wrong.',
                    'purchase_order_id' => null,
                    'order_detail_ids' => null,
                    'order_column_ids' => null,
                    'status_ids' => null,
                    'status_code' => 400
                ], 400);
                // Handle the case when both usage counts could not be updated successfully
                // Add appropriate error handling or log the issue for further investigation.
            }
        }

        $purchaseOrder = PurchaseOrder::where('id', $purchaseOrderId)->with('sellerUser', 'buyerUser', 'orderDetails', 'orderDetails.columns',  'statuses')->first();
        // Generate the PDF for the purchaseOrder using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generatePurchaseOrder($purchaseOrder);

        $response = (array) $response->getData();

        // Handle the response from PDFGenerator

        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $purchaseOrder->pdf_url = $response['pdf_url'];
            $purchaseOrder->save();
        }

        return response()->json([
            'message' => 'Purchase Order created successfully.',
            'purchase_order_id' => $purchaseOrderId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200,
        ], 200);
    }

    public function poSfpCreate(Request $request)
    {
        $teamUsers = DB::table('team_users')->whereIn('id', $request->team_user_ids)->get();
        $admins = DB::table('users')->whereIn('id', $request->admin_ids)->get();

        $sellers = $teamUsers->concat($admins);

        if ($sellers->isNotEmpty()) {
            foreach ($sellers as $seller) {
                $PoSfp = new PurchaseOrderSfp([
                    'purchase_order_id' => $request->purchase_order_id,
                    'sfp_by_id' => Auth::user()->id,
                    'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
                    'sfp_to_id' => $seller->id,
                    'sfp_to_name' => $seller->team_user_name ?? $seller->name,
                    'comment' => $request->comment,
                    'status' => 'sent',
                    'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
                ]);
                $PoSfp->save();
            }

            return response()->json([
                'message' => 'PO SFP created successfully.',
                'status_code' => 200
            ], 200);
        } else {
            return response()->json([
                'errors' => 'No users found.',
                'status_code' => 500
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // dd($request, $id);
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'purchase_order_series' => 'string',
            'purchase_order_id' => 'nullable|date',
            'buyer_id' => 'nullable|exists:users,id',
            'buyer' => 'nullable|string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            // 'order_details.*.id' => 'nullable|exists:invoice_order_details,id',
            'order_details.*.unit' => 'nullable|string',
            'order_details.*.rate' => 'nullable|numeric|min:0',
            'order_details.*.qty' => 'nullable|numeric|min:0',
            'order_details.*.tax' => 'nullable|numeric|min:0',
            'order_details.*.details' => 'nullable|string',
            'order_details.*.total_amount' => 'nullable|numeric|min:0',
            // 'order_details.*.columns.*.id' => 'nullable|exists:invoice_order_columns,id',
            'order_details.*.columns.*.column_name' => 'nullable|string',
            'order_details.*.columns.*.column_value' => 'nullable|string',
            'statuses.*.comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }
        // dd($request->all());
        // Find the PurchaseOrder by ID
        $purchaseOrder = PurchaseOrder::find($id);

        if (!$purchaseOrder) {
            return response()->json([
                'message' => 'Purchase Order not found.',
                'status_code' => 400,
            ], 400);
        }

        // Update PurchaseOrder data
        $purchaseOrder->comment = $request->input('comment', $purchaseOrder->comment);
        $purchaseOrder->total = $request->input('total', $purchaseOrder->total);
        // $purchaseOrder->invoice_date = $request->input('invoice_date', $purchaseOrder->invoice_date);
        // Update other fields as needed
        $purchaseOrder->save();

        // Update PurchaseOrder Order Details and their Columns
        if ($request->has('order_details')) {
            $PurchaseOrderDetail = PurchaseOrderDetail::where('purchase_order_id', $id)->with('columns')->get();
            if ($PurchaseOrderDetail) {
                foreach ($PurchaseOrderDetail as $key => $value) {
                    // Delete the associated comments first
                    $PurchaseOrderDetail[$key]->columns()->delete();
                    $PurchaseOrderDetail[$key]->delete();
                }
                // Then, delete the purchase$purchaseOrderOrderDetail itself
            }
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new PurchaseOrderDetail([
                    'purchase_order_id' => $purchaseOrder->id,
                    'unit' => $orderDetailData['unit'],
                    'rate' => $orderDetailData['rate'] ?? null,
                    'qty' => $orderDetailData['qty'] ?? 0.00,
                    'tax' => $orderDetailData['tax'] ?? null,
                    'details' => $orderDetailData['details'] ?? null,
                    // 'total_qty' => $orderDetailData['total_qty'],
                    'total_amount' => $orderDetailData['total_amount'] ?? 0.00,
                ]);
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new PurchaseOrderColumn([
                            'purchase_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ?? '',
                        ]);
                        $orderColumn->save();
                    }
                }
            }

            // foreach ($request->order_details as $orderDetailData) {
            //     $orderDetail = PurchaseOrderDetail::find($orderDetailData['id']);

            //     if (!$orderDetail) {
            //         return response()->json([
            //             'message' => 'Purchase Order Order Detail not found.',
            //             'status_code' => 400,
            //         ], 400);
            //     }

            //     $orderDetail->unit = $orderDetailData['unit'];
            //     $orderDetail->rate = $orderDetailData['rate'] ?? 0.00;
            //     $orderDetail->qty = $orderDetailData['qty'] ?? 0;
            //     $orderDetail->total_amount = $orderDetailData['total_amount'] ?? 0.00;
            //     // Update other fields as needed
            //     $orderDetail->save();

            //     if (isset($orderDetailData['columns'])) {
            //         foreach ($orderDetailData['columns'] as $columnData) {
            //             $orderColumn = PurchaseOrderColumn::find($columnData['id']);

            //             if (!$orderColumn) {
            //                 return response()->json([
            //                     'message' => 'Purchase Order Order Column not found.',
            //                     'status_code' => 400,
            //                 ], 400);
            //             }

            //             $orderColumn->column_name = $columnData['column_name'];
            //             $orderColumn->column_value = $columnData['column_value'];
            //             // Update other fields as needed
            //             $orderColumn->save();
            //         }
            //     }
            // }
        }

        // Create PurchaseOrder Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new PurchaseOrderStatus([
                    'purchase_order_id' => $purchaseOrder->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    'status' => 'draft',
                    'comment' => 'Purchase Order updated successfully',
                ]);
                $status->save();
            }
        }

        // Get the IDs of the updated records
        $purchaseOrderId = $purchaseOrder->id;
        $orderDetailIds = $purchaseOrder->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $purchaseOrder->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $purchaseOrder->statuses->pluck('id')->toArray();
        // dd($orderDetail);
        $purchaseOrder = PurchaseOrder::where('id', $purchaseOrderId)->with('sellerUser', 'buyerUser', 'orderDetails', 'orderDetails.columns',  'statuses')->first();
        // Generate the PDF for the purchaseOrder using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generatePurchaseOrder($purchaseOrder);

        $response = (array) $response->getData();

        // Handle the response from PDFGenerator

        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $purchaseOrder->pdf_url = $response['pdf_url'];
            $purchaseOrder->save();
        }

        return response()->json([
            'message' => 'Purchase Order updated successfully.',
            'purchase_order_id' => $purchaseOrderId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200,
        ], 200);
    }

    // Get Seller Details
    // public function getSeller(Request $request)
    // {
    //     // dd($request);
    //     $user = auth()->user();
    //     $sellerList = Buyer::join('invoices', 'invoices.buyer_id', '=', 'buyers.buyer_user_id')
    //     ->join('users', 'invoices.seller_id', '=', 'users.id')
    //         ->where('buyers.buyer_user_id', '=', $user->id)
    //         ->select('*')
    //         ->distinct()
    //         ->get();
    //         // dd($sellerList);
    //     $responseData = [
    //         'message' => 'Seller Details.',
    //         'seller_list' => $sellerList,
    //         'status_code' => 200,
    //     ];
    //     return response()->json($responseData, 200);
    // }
     // Get Seller Details
     public function getSeller(Request $request)
     {
         $user = Auth::guard(Auth::getDefaultDriver())->user();
         $sellerList = Buyer::join('purchase_orders', 'purchase_orders.buyer_id', '=', 'buyers.buyer_user_id')
             ->join('users', 'purchase_orders.seller_id', '=', 'users.id')
             ->where('buyers.buyer_user_id', '=', $user->id)
             ->select('users.name as seller', 'users.email', 'users.address', 'users.phone', 'users.gst_number', 'purchase_orders.seller_id', 'buyers.id')

             ->distinct()
             ->get();

         $responseData = [
             'message' => 'Seller Details.',
             'seller_list' => $sellerList,
             'status_code' => 200,
         ];
         return response()->json($responseData, 200);
     }

    // public function getSellerData(Request $request)
    // {
    //     $user = auth()->user();
    //     $sellerDataList = Invoice::where('buyer_id', $user->id)
    //         ->join('invoice_order_details', 'invoice_order_details.purchase_order_id', '=', 'invoices.id')
    //         ->join('invoice_order_columns', 'invoice_order_details.id', '=', 'invoice_order_columns.invoice_order_detail_id')
    //         ->join('buyers', 'invoices.buyer_id', '=', 'buyers.buyer_user_id')
    //         ->where('buyers.buyer_user_id', '=', $user->id)
    //         ->select('rate', 'unit', 'qty', 'total_amount', 'comment', 'column_value', 'column_name', 'invoices.seller_id', 'invoices.seller', 'buyers.id', 'purchase_order_id', 'purchase_order_series', 'invoice_date', 'buyers.status')
    //         ->distinct()
    //         ->get();
    //     $responseData = [
    //         'message' => 'Seller Details.',
    //         'seller_list' => $sellerDataList,
    //         'status_code' => 200,
    //     ];
    //     return response()->json($responseData, 200);
    // }
    public function getSellerData(Request $request)
    {
        $user = auth()->user();
        $sellerDataList = Invoice::where('buyer_id', $user->id)
            ->join('invoice_order_details', 'invoice_order_details.invoice_id', '=', 'invoices.id')
            ->join('invoice_order_columns', 'invoice_order_details.id', '=', 'invoice_order_columns.invoice_order_detail_id')
            ->join('buyers', 'invoices.buyer_id', '=', 'buyers.buyer_user_id')
            ->where('buyers.buyer_user_id', '=', $user->id)
            ->select('rate', 'unit', 'qty', 'total_amount', 'comment', 'column_value', 'column_name', 'invoices.seller_id', 'invoices.seller', 'buyers.id', 'invoice_id', 'invoice_series', 'invoice_date', 'buyers.status')
            ->distinct()
            ->get();
        $responseData = [
            'message' => 'Seller Details.',
            'seller_list' => $sellerDataList,
            'status_code' => 200,
        ];
        return response()->json($responseData, 200);
    }

    public function getallInvoiceData(Request $request)
    {
        $user = auth()->user();
        $getallSellerData = Invoice::join('buyers', 'buyers.buyer_user_id', '=', 'invoices.buyer_id')
            ->join('users', 'invoices.seller_id', '=', 'users.id')
            ->where('buyers.buyer_user_id', '=', $user->id)
            ->select('*', 'invoices.id')
            ->with(['statuses'])
            ->distinct()
            ->get();

        $responseData = [
            'message' => 'Seller Details.',
            'seller_list' => $getallSellerData,
            'status_code' => 200,
        ];
        // dd($responseData);
        return response()->json($responseData, 200);
    }


    public function modify(Request $request, $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'purchase_order_series' => 'string',
            'invoice_date' => 'required|date',
            'buyer_id' => 'exists:users,id',
            'buyer' => 'string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            // 'order_details.*.id' => 'required|exists:invoice_order_details,id',
            'order_details.*.unit' => 'required|string',
            'order_details.*.rate' => 'numeric|min:0',
            'order_details.*.qty' => 'integer|min:0',
            'order_details.*.details' => 'string|nullable',
            'order_details.*.total_amount' => 'numeric|min:0',
            // 'order_details.*.columns.*.id' => 'required|exists:invoice_order_columns,id',
            'order_details.*.columns.*.column_name' => 'required|string',
            'order_details.*.columns.*.column_value' => 'required|string',
            'statuses.*.comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        // Find the PurchaseOrder by ID
        $purchaseOrder = PurchaseOrder::find($id);

        if (!$purchaseOrder) {
            return response()->json([
                'message' => 'Purchase Order not found.',
                'status_code' => 400,
            ], 400);
        }

        // Update PurchaseOrder data
        $purchaseOrder->comment = $request->input('comment', $purchaseOrder->comment);
        $purchaseOrder->total = $request->input('total', $purchaseOrder->total);
        $purchaseOrder->invoice_date = $request->input('invoice_date', $purchaseOrder->invoice_date);
        // Update other fields as needed
        $purchaseOrder->save();

        // Update PurchaseOrder Order Details and their Columns
        if ($request->has('order_details')) {
            $PurchaseOrderDetail = PurchaseOrderDetail::where('purchase_order_id', $id)->with('columns')->get();
            if ($PurchaseOrderDetail) {
                foreach ($PurchaseOrderDetail as $key => $value) {
                    // Delete the associated comments first
                    $PurchaseOrderDetail[$key]->columns()->delete();
                    $PurchaseOrderDetail[$key]->delete();
                }
                // Then, delete the purchase$purchaseOrderOrderDetail itself
            }
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new PurchaseOrderDetail([
                    'purchase_order_id' => $purchaseOrder->id,
                    'unit' => $orderDetailData['unit'],
                    'rate' => $orderDetailData['rate'] ?? 0.00,
                    'qty' => $orderDetailData['qty'] ?? 0,
                    'details' => $orderDetailData['details'],
                    'total_amount' => $orderDetailData['total_amount'] ?? 0.00,
                ]);
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new PurchaseOrderColumn([
                            'invoice_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'],
                            'column_value' => $columnData['column_value'],
                        ]);
                        $orderColumn->save();
                    }
                }
            }

            // foreach ($request->order_details as $orderDetailData) {
            //     $orderDetail = PurchaseOrderDetail::find($orderDetailData['id']);

            //     if (!$orderDetail) {
            //         return response()->json([
            //             'message' => 'Purchase Order Order Detail not found.',
            //             'status_code' => 400,
            //         ], 400);
            //     }

            //     $orderDetail->unit = $orderDetailData['unit'];
            //     $orderDetail->rate = $orderDetailData['rate'] ?? 0.00;
            //     $orderDetail->qty = $orderDetailData['qty'] ?? 0;
            //     $orderDetail->total_amount = $orderDetailData['total_amount'] ?? 0.00;
            //     // Update other fields as needed
            //     $orderDetail->save();

            //     if (isset($orderDetailData['columns'])) {
            //         foreach ($orderDetailData['columns'] as $columnData) {
            //             $orderColumn = PurchaseOrderColumn::find($columnData['id']);

            //             if (!$orderColumn) {
            //                 return response()->json([
            //                     'message' => 'Purchase Order Order Column not found.',
            //                     'status_code' => 400,
            //                 ], 400);
            //             }

            //             $orderColumn->column_name = $columnData['column_name'];
            //             $orderColumn->column_value = $columnData['column_value'];
            //             // Update other fields as needed
            //             $orderColumn->save();
            //         }
            //     }
            // }
        }

        // Create PurchaseOrder Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new PurchaseOrderStatus([
                    'purchase_order_id' => $purchaseOrder->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                    'status' => 'modified',
                    'comment' => 'Purchase Order modified successfully',
                ]);
                $status->save();
            }
        }

        // Get the IDs of the modified records
        $purchaseOrderId = $purchaseOrder->id;
        $orderDetailIds = $purchaseOrder->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $purchaseOrder->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $purchaseOrder->statuses->pluck('id')->toArray();

        return response()->json([
            'message' => 'Purchase Order modified successfully.',
            'purchase_order_id' => $purchaseOrderId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200,
        ], 200);
    }


    public function send(Request $request, $purchaseOrderId)
    {
        // dd($request, $purchaseOrderId);
        // Find the PurchaseOrder by ID
        // $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);
        $purchaseOrder = PurchaseOrder::where('id', $purchaseOrderId)->with('buyerUser', 'sellerUser', 'orderDetails', 'orderDetails.columns', 'statuses')->first();
        // dd($purchaseOrder);

        // Generate the PDF for the PurchaseOrder using PDFGenerator class
        // $pdfGenerator = new PDFGeneratorService();
        // $response = $pdfGenerator->generatePurchaseOrderPDF($purchaseOrder);


        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generatePurchaseOrder($purchaseOrder);
        // dd($response);

        $response = (array) $response->getData();
        // Handle the response from PDFGenerator

        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $purchaseOrder->pdf_url = $response['pdf_url'];
            $purchaseOrder->save();

            // Send the PDF via email
            if ($purchaseOrder->buyerUser->email != null) {
                $pdfEmailService = new PDFEmailService();
                $recipientEmail = $purchaseOrder->buyerUser->email; // Replace with the actual recipient email address
                $pdfEmailService->sendPurchaseOrderByEmail($purchaseOrder, $response['pdf_url'], $recipientEmail);
            }
            // Check permissions and send WhatsApp notifications if needed
            $sendWhatsApp = false;
            $phoneNumbers = [$purchaseOrder->buyerUser->phone];
            if (!empty($purchaseOrder->additional_phone_number)) {
                $phoneNumbers[] = $purchaseOrder->additional_phone_number;
            }
            $buyerUserEmail = $purchaseOrder->sellerUser ? $purchaseOrder->sellerUser->email : null;
            $buyerUser = $purchaseOrder->buyerUser->name;
            $sellerUser = $purchaseOrder->sellerUser->name;
            $purchaseOrderNo = $purchaseOrder->purchase_order_series . '-' . $purchaseOrder->series_num;
            $purchaseOrderId = $purchaseOrder->id;
            $heading = 'Purchase Order';

            // Check sender permissions
            if (isset($permissionsSender['buyer']['whatsapp']['sent_challan']) && $permissionsSender['buyer']['whatsapp']['sent_challan']) {
                $wallet = Wallet::where('user_id', $purchaseOrder->sellerUser->id)->first();
                $deduction = 0.90 + (0.90 * 0.18);
                if ($wallet !== null && $wallet->balance >= $deduction) {
                    $wallet->balance -= $deduction;
                    $wallet->save();
                    // Log the deduction
                    WalletLog::create([
                        'user_id' => $wallet->user_id,
                        'amount_deducted' => $deduction,
                        'remaining_balance' => $wallet->balance,
                        'purchase_order_id' => $purchaseOrderId,
                        'action' => 'po_sent',
                        'recipient' => $purchaseOrder->buyerUser->name,

                    ]);
                    $sendWhatsApp = true;
                }
            }

            if ($sendWhatsApp) {
                $pdfWhatsAppService = new PDFWhatsAppService();
                $pdfWhatsAppService->sendChallanOnWhatsApp($phoneNumbers, $response['pdf_url'], $purchaseOrderNo, $purchaseOrderId, $buyerUser, $sellerUser, $heading);
            }

            // Add a new "sent" status to the PurchaseOrder
            $status = new PurchaseOrderStatus([
                'purchase_order_id' => $purchaseOrder->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                'status' => 'sent',
                'comment' => 'PurchaseOrder sent for acceptance',
            ]);
            $status->save();

            if ($request->status_comment && trim($request->status_comment) != '') {
                // Get the existing status_comment data
                $statusComment = json_decode($purchaseOrder->status_comment, true);

                // Add the new comment to the status_comment data
                $statusComment[] = [
                    'comment' => $request->status_comment,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                ];

                // Update the status_comment field with the combined data
                $purchaseOrder->update(['status_comment' => json_encode($statusComment)]);
            }

            // $PoSfp = new PurchaseOrderSfp(
            //     [
            //         'purchase_order_id' => $purchaseOrderId,
            //         'sfp_by_id' => Auth::user()->id,
            //         'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
            //         'sfp_to_id' => null,
            //         'sfp_to_name' =>$purchaseOrder->buyerUser->company_name ?? $purchaseOrder->buyerUser->name,
            //         'status' => 'sent',
            //         'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
            //     ]
            // );
            // $PoSfp = $PoSfp->save();
             // Show Notifications in Status
            $notification = new Notification([
                'user_id' => $purchaseOrder->buyerUser->id,
                'message' => 'New PO Received by ' . $purchaseOrder->sellerUser->name,
                'added_id' => $purchaseOrderId,
                'type' => 'po',
                'panel' => 'receiver',
                'template_name' => 'received_return_challan',
            ]);
            $notification->save();

            // Return a response with the token and other relevant information
            return response()->json([
                'message' => 'PurchaseOrder sent successfully.',
                'purchase_order_id' => $purchaseOrder->id,
                // 'token' => $token,
                // 'token_expiry' => $status->token_expiry,
                'pdf_url' => $response['pdf_url'],
                'status_code' => 200
            ], 200);
        } else {
            // Error occurred during PDF generation and storage
            // Return an error response
            return response()->json([
                'message' => 'Error generating and storing PurchaseOrder PDF.',
                'purchase_order_id' => $purchaseOrder->id,
                // 'token' => $token,
                // 'token_expiry' => $status->token_expiry,
                'pdf_url' => null,
                'status_code' => $response['status_code']
            ], $response['status_code']);
        }
    }

    public function resend(Request $request, $purchaseOrderId)
    {
        // Find the PurchaseOrder by ID
        $purchaseOrder = PurchaseOrder::where('id', $purchaseOrderId)->with('buyerUser', 'sellerUser', 'orderDetails', 'orderDetails.column', 'statuses')->first();


        // PDF generated successfully

        // Send the PDF via email
        if ($purchaseOrder->buyerUser->email != null) {
            $pdfEmailService = new PDFEmailService();
            $recipientEmail = $purchaseOrder->buyerUser->email; // Replace with the actual recipient email address
            $pdfEmailService->sendPurchaseOrderByEmail($purchaseOrder, $purchaseOrder->pdf_url, $recipientEmail);
        }

        // Assuming that PlanAdditionalFeatureUsageRecord and AdditionalFeatureTopupUsageRecord models have been imported.

        if ($purchaseOrder->buyerUser->phone != null) {
            $featureId = $request->feature_id; // Replace with YOUR_FEATURE_ID

            // Validate usage limit for PlanAdditionalFeatureUsageRecord
            $PlanAdditionalFeatureUsageRecord = new PlanAdditionalFeatureUsageRecord();

            // Validate usage limit for AdditionalFeatureTopupUsageRecord
            $AdditionalFeatureTopupUsageRecord = new AdditionalFeatureTopupUsageRecord();

            $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, 1);

            if ($PlanAdditionalFeatureUsageRecordResponse) {
                $pdfWhatsAppService = new PDFWhatsAppService();
                $recipientPhoneNumber = $purchaseOrder->buyerUser->phone; // Replace with the actual recipient phone number
                $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendPurchaseOrderOnWhatsApp($purchaseOrder, $purchaseOrder->pdf_url, $recipientPhoneNumber);

                if (!$pdfWhatsAppServiceResponse) {
                    Log::error('Error sending PurchaseOrder PDF Whatsapp for PurchaseOrder Id: ' . $purchaseOrder->id);
                    $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, -1);
                }
            } else {
                // Update usage count for AdditionalFeatureTopupUsageRecord
                $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, 1);

                if ($AdditionalFeatureTopupUsageRecordResponse) {
                    $pdfWhatsAppService = new PDFWhatsAppService();
                    $recipientPhoneNumber = $purchaseOrder->buyerUser->phone; // Replace with the actual recipient phone number
                    $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendPurchaseOrderOnWhatsApp($purchaseOrder, $purchaseOrder->pdf_url, $recipientPhoneNumber);

                    if (!$pdfWhatsAppServiceResponse) {
                        Log::error('Error sending PurchaseOrder PDF Whatsapp for PurchaseOrder Id: ' . $purchaseOrder->id);
                        $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, -1);
                    }
                }
            }
        }

        // Add a new "resent" status to the PurchaseOrder
        $status = new PurchaseOrderStatus([
            'purchase_order_id' => $purchaseOrder->id,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
            'status' => 'resent',
            'comment' => 'PurchaseOrder resent for acceptance',
        ]);
        $status->save();

        // Return a response with the token and other relevant information
        return response()->json([
            'message' => 'PurchaseOrder resent successfully.',
            'purchase_order_id' => $purchaseOrder->id,
            // 'token' => $token,
            // 'token_expiry' => $status->token_expiry,
            'pdf_url' => $purchaseOrder->pdf_url,
            'status_code' => 200
        ], 200);
    }
    public function index(Request $request)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;


        $query = PurchaseOrder::query()->orderByDesc('id')->where('seller_id', $userId);
        $combinedValues = [];
        // dd($request->purchase_order_series);
        // Filter by purchase_order_series
        if ($request->has('purchase_order_series')) {
            $searchTerm = $request->purchase_order_series;

            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                // Split the string into series and number
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                // Perform the search
                $query->where('purchase_order_series', $series)
                      ->where('series_num', $num);
            } else {
                // Invalid search term format, handle accordingly
                // For example, you could return an error message or ignore the filter
            }
        }
        // Filter by seller_id
        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        // Filter by buyer_id
        if ($request->has('buyer_id')) {
            $query->where('buyer_id', $request->buyer_id);
        }

        // Filter by deleted
        if ($request->has('deleted')) {
            $query->where('deleted', $request->deleted);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->whereHas('statuses', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }
        // Filter by date
        if ($request->has('from') && $request->has('to')) {
          $from = $request->from;
          $to = $request->to;
          $query->whereBetween('created_at', [$from, $to]);
        }

        // Filter by state in BuyerDetails
        if ($request->has('state')) {
            $query->whereHas('buyerDetails', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        // Filter by city in BuyerDetails
        if ($request->has('city')) {
            $query->whereHas('buyerDetails', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        // Fetch the distinct filter values for PurchaseOrder table (for this user)
        $distinctPurchaseOrderSeries = PurchaseOrder::where('seller_id', $userId)->distinct()->pluck('purchase_order_series');
        $distinctPurchaseOrderSeriesNum = PurchaseOrder::where('seller_id', $userId)->distinct()->pluck('series_num');
        $distinctSellerIds = PurchaseOrder::where('seller_id', $userId)->distinct()->pluck('seller_id');
        $distinctBuyerIds = PurchaseOrder::where('seller_id', $userId)->distinct()->pluck('buyer_id');

        // Loop through each element of $distinctPurchaseOrderSeries
        foreach ($distinctPurchaseOrderSeries as $series) {
           // Loop through each element of $distinctPurchaseOrderSeriesNum
           foreach ($distinctPurchaseOrderSeriesNum as $num) {
               // Combine the series and number and push it into the combinedValues array
               $combinedValues[] = $series . '-' . $num;
           }
        }
        // $distinctStatuses = Status::distinct()->pluck('status');

        // Fetch the distinct "state" and "city" values from BuyerDetail table for buyers of this user
        $distinctStates = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('city');

        // Add any other desired filters
        $perPage = $request->perPage ?? 100;
        $page = $request->page ?? 1;

        $purchaseOrders = $query->with(['buyerUser', 'statuses', 'buyerDetails','orderDetails', 'orderDetails.columns', 'sfp'])
            ->paginate(50);

        $startItemNumber = ($page - 1) * $perPage + 1;

        $purchaseOrders->each(function ($item) use (&$startItemNumber) {
            $item->setAttribute('custom_item_number', $startItemNumber++);
        });

        // dd($purchaseOrders);
        // return response()->json($purchaseOrders, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $purchaseOrders,
            'status_code' => 200,
            'filters' => [
                'purchase_order_series' => $distinctPurchaseOrderSeries,
                'purchase_order_series_num' => $distinctPurchaseOrderSeriesNum,
                'merged_purchase_order_series' => $combinedValues,
                'seller_id' => $distinctSellerIds,
                'buyer_id' => $distinctBuyerIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                // Add any other filter values here if needed
            ]
        ], 200);
    }
    public function indexDetail(Request $request)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $purchaseOrderDetails = PurchaseOrder::leftJoin('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
        ->leftJoin('purchase_order_columns', 'purchase_order_details.id', '=', 'purchase_order_columns.purchase_order_detail_id')
        ->select('purchase_orders.*', 'purchase_order_details.*', 'purchase_order_columns.*')
        ->orderByDesc('purchase_orders.id')
        ->where('purchase_orders.seller_id', $userId)
        ->get();

        return response()->json([
            'message' => 'Success',
            'data' => $purchaseOrderDetails,
            'status_code' => 200,
        ], 200);;
    }

    public function getIndexDetailData(Request $request)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $purchaseOrderDetails = PurchaseOrder::leftJoin('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
        ->leftJoin('purchase_order_columns', 'purchase_order_details.id', '=', 'purchase_order_columns.purchase_order_detail_id')
        ->select('purchase_orders.*', 'purchase_order_details.*', 'purchase_order_columns.*')
        ->orderByDesc('purchase_orders.id')
        ->where('purchase_orders.buyer_id', $userId)
        ->get();
        // dd($purchaseOrderDetails);

        return response()->json([
            'message' => 'Success',
            'data' => $purchaseOrderDetails,
            'status_code' => 200,
        ], 200);;
    }

    public function getPurchaseOrders(Request $request)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;


        $query = PurchaseOrder::query()->orderByDesc('id')->where('buyer_id', $userId);
        $combinedValues = [];
        // Filter by purchase_order_series
        if ($request->has('purchase_order_series')) {
            $searchTerm = $request->purchase_order_series;

            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                // Split the string into series and number
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                // Perform the search
                $query->where('purchase_order_series', $series)
                      ->where('series_num', $num);
            } else {
                // Invalid search term format, handle accordingly
                // For example, you could return an error message or ignore the filter
            }
        }

        // Filter by seller_id
        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        // Filter by buyer_id
        if ($request->has('buyer_id')) {
            $query->where('buyer_id', $request->buyer_id);
        }

        // Filter by deleted
        if ($request->has('deleted')) {
            $query->where('deleted', $request->deleted);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->whereHas('statuses', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Filter by state in BuyerDetails
        if ($request->has('state')) {
            $query->whereHas('buyerDetails', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        // Filter by city in BuyerDetails
        if ($request->has('city')) {
            $query->whereHas('buyerDetails', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        // Fetch the distinct filter values for PurchaseOrder table (for this user)
        $distinctPurchaseOrderSeries = PurchaseOrder::where('seller_id', $userId)->distinct()->pluck('purchase_order_series');
        $distinctPurchaseOrderSeriesNum = PurchaseOrder::where('seller_id', $userId)->distinct()->pluck('series_num');

         // Loop through each element of $distinctPurchaseOrderSeries
        foreach ($distinctPurchaseOrderSeries as $series) {
           // Loop through each element of $distinctPurchaseOrderSeriesNum
           foreach ($distinctPurchaseOrderSeriesNum as $num) {
               // Combine the series and number and push it into the combinedValues array
               $combinedValues[] = $series . '-' . $num;
           }
        }

        $distinctSellerIds = PurchaseOrder::where('seller_id', $userId)->distinct()->pluck('seller_id');
        $distinctBuyerIds = PurchaseOrder::where('seller_id', $userId)->distinct()->pluck('buyer_id');
        // $distinctStatuses = Status::distinct()->pluck('status');

        // Fetch the distinct "state" and "city" values from BuyerDetail table for buyers of this user
        $distinctStates = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('city');

        // Add any other desired filters
        $perPage = $request->perPage ?? 100;
        $page = $request->page ?? 1;

        $purchaseOrders = $query->with(['buyerUser', 'statuses', 'buyerDetails'])
            ->paginate($perPage, ['*'], 'page', $page);

        $startItemNumber = ($page - 1) * $perPage + 1;
        // Add a custom attribute to each item in the collection with the calculated item number
        $purchaseOrders->each(function ($item) use (&$startItemNumber) {
            $item->setAttribute('custom_item_number', $startItemNumber++);
        });

        // return response()->json($purchaseOrders, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $purchaseOrders,
            'status_code' => 200,
            'filters' => [
                'purchase_order_series' => $distinctPurchaseOrderSeries,
                'purchase_order_series_num' => $distinctPurchaseOrderSeriesNum,
                'merged_purchase_order_series' => $combinedValues,
                'seller_id' => $distinctSellerIds,
                'buyer_id' => $distinctBuyerIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                // Add any other filter values here if needed
            ]
        ], 200);
    }

    // public function indexDetail(Request $request)
    // {
    //     $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

    //     $query = PurchaseOrder::leftJoin('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
    //         ->leftJoin('purchase_order_columns', 'purchase_order_details.id', '=', 'purchase_order_columns.purchase_order_detail_id')
    //         ->select('purchase_orders.*', 'purchase_order_details.*', 'purchase_order_columns.*')
    //         ->orderByDesc('purchase_orders.id')
    //         ->where('purchase_orders.seller_id', $userId)
    //         ->get();

    //     // Group the data by purchase_order_series and series_num
    //     // dd($query);
    //     $groupedData = $query->groupBy(['purchase_order_series', 'series_num']);
    // // dd($groupedData);

    //     // Merge the entries with the same purchase_order_series and series_num
    //     $groupedData->transform(function ($entries) {
    //         $mergedEntry = $entries->reduce(function ($result, $entry) {
    //             $result['column_values'][$entry->column_name] = $entry->column_value;
    //             return $result;
    //         }, []);

    //         // Take the first entry as the base
    //         $baseEntry = $entries->first();
    //         dd($baseEntry);
    //         unset($baseEntry->column_name, $baseEntry->column_value);

    //         // Merge the base entry with the merged column values
    //         return array_merge($baseEntry->toArray(), $mergedEntry);
    //     });
    //     return response()->json([
    //         'message' => 'Success',
    //         'data' => $groupedData,
    //         'status_code' => 200,
    //     ], 200);
    // }


    public function show(Request $request, $id)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Fetch the return invoice by ID (for this user)
        $purchaseOrder = PurchaseOrder::where('seller_id', $userId)->find($id);

        // Load related data
        $purchaseOrder->load(['orderDetails.columns', 'statuses', 'buyerDetails']);

        if (!$purchaseOrder) {
            return response()->json([
                'data' => null,
                'message' => 'PurchaseOrder not found',
                'status_code' => 200,
            ], 200);
        }

        // Return the response
        return response()->json([
            'message' => 'Success',
            'data' => $purchaseOrder,
            'status_code' => 200,
        ], 200);
    }


    public function accept(Request $request, $purchaseOrderId)
    {
        // dd($request, $purchaseOrderId);
        try {
            // Find the PurchaseOrder by ID
            $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);
            // dd($purchaseOrder);
            if($request->status_comment && trim($request->status_comment) != ''){
                // Get the existing status_comment data
                $statusComment = json_decode($purchaseOrder->status_comment, true);

                // Add the new comment to the status_comment data
                $statusComment[] = [
                    'comment' => $request->status_comment,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                ];

                // Update the status_comment field with the combined data
                $purchaseOrder->update(['status_comment' => json_encode($statusComment)]);
            }

            // Update the status of the PurchaseOrder to "accepted"
            $purchaseOrder->statuses()->create([
                'purchase_order_id' => $purchaseOrder->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'accept',
                'comment' => 'PurchaseOrder accepted',
            ]);

            // $PoSfp = new PurchaseOrderSfp(
            //     [
            //         'purchase_order_id' => $purchaseOrderId,
            //         'sfp_by_id' => Auth::user()->id,
            //         'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
            //         'sfp_to_id' => null,
            //         'sfp_to_name' => null,
            //         'status' => 'accept',
            //         'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
            //     ]
            // );
            // $PoSfp = $PoSfp->save();

            // Return a response indicating success
            return response()->json([
                'data' => $purchaseOrder->statuses,
                'message' => 'PurchaseOrder accepted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'PurchaseOrder Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function selfAccept(Request $request, $purchaseOrderId)
    {
        try {
            // Find the PurchaseOrder by ID
            $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);

            // Update the status of the PurchaseOrder to "self-accepted"
            $purchaseOrder->statuses()->create([
                'purchase_order_id' => $purchaseOrder->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                'status' => 'self_accept',
                'comment' => 'PurchaseOrder self accepted',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $purchaseOrder->statuses,
                'message' => 'PurchaseOrder self accepted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'PurchaseOrder Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function reject(Request $request, $purchaseOrderId)
    {
        try {
            // Find the PurchaseOrder by ID
            $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);

            if($request->status_comment && trim($request->status_comment) != ''){
                // Get the existing status_comment data
                $statusComment = json_decode($purchaseOrder->status_comment, true);

                // Add the new comment to the status_comment data
                $statusComment[] = [
                    'comment' => $request->status_comment,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                ];

                // Update the status_comment field with the combined data
                $purchaseOrder->update(['status_comment' => json_encode($statusComment)]);
            }

            // Update the status of the PurchaseOrder to "rejected"
            $purchaseOrder->statuses()->create([
                'purchase_order_id' => $purchaseOrderId,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'reject',
                'comment' => 'PurchaseOrder rejected',
            ]);

            // $PoSfp = new PurchaseOrderSfp(
            //     [
            //         'purchase_order_id' => $purchaseOrderId,
            //         'sfp_by_id' => Auth::user()->id,
            //         'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
            //         'sfp_to_id' => null,
            //         'sfp_to_name' => null,
            //         'status' => 'reject',
            //         'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
            //     ]
            // );
            // $PoSfp = $PoSfp->save();

            // Return a response indicating success
            return response()->json([
                'data' => $purchaseOrder->statuses,
                'message' => 'PurchaseOrder rejected successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'PurchaseOrder Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function selfReject(Request $request, $purchaseOrderId)
    {
        try {
            // Find the PurchaseOrder by ID
            $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);

            // Update the status of the PurchaseOrder to "self_reject"
            $purchaseOrder->statuses()->create([
                'purchase_order_id' => $purchaseOrder->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'self_reject',
                'comment' => 'PurchaseOrder self rejected',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $purchaseOrder->statuses,
                'message' => 'PurchaseOrder self rejected successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'PurchaseOrder Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function delete(Request $request, $purchaseOrderId)
    {
        try {
            // Find the PurchaseOrder by ID
            $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);

            // Update the status of the PurchaseOrder to "deleted"
            $purchaseOrder->statuses()->create([
                'purchase_order_id' => $purchaseOrder->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'deleted',
                'comment' => 'PurchaseOrder deleted',
            ]);

            $purchaseOrder->delete();

            // Return a response indicating success
            return response()->json([
                'data' => $purchaseOrder->statuses,
                'message' => 'PurchaseOrder deleted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'PurchaseOrder Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function forceDelete(Request $request, $purchaseOrderId)
    {
        try {
            // Find the PurchaseOrder by ID
            $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);

            $purchaseOrder->forceDelete();

            // Return a response indicating success
            return response()->json([
                'message' => 'PurchaseOrder permanently deleted.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'PurchaseOrder Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    // Export PurchaseOrder to CSV
    public function exportPurchaseOrder(Request $request)
    {
        // Fetch the products and their related product details
        // $products = purchase$purchaseOrder::with('details')->get();
        $query = PurchaseOrder::query()->orderByDesc('id');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // $purchaseOrders = $query->with(['sellerUser', 'statuses', 'buyerDetails','orderDetails', 'sfp'])->select('purchase$purchaseOrders.*')->paginate(100,null,null,$request->page??1);
        $purchaseOrders = $query->where('seller_id', $userId)->with('statuses', 'buyerDetails','orderDetails','orderDetails.columns')->select('purchase_orders.*')->paginate(100,null,null,$request->page??1);
        // dd($purchaseOrders->statuses);

        // Create an array to store the exported data
        $exportedData = [];

        // Iterate through the products and their related product details
        foreach ($purchaseOrders as $key => $purchaseOrder) {
            $rowData['id'] =  ++$key;

            // foreach ($purchaseOrder as $productDetail) {
            //     $rowData[$productDetail->column_name] = $productDetail->column_value;
            // }
            // $rowData['Time'] = $purchaseOrder->(date('h:i A', strtotime($purchaseOrder->created_at)));


            $rowData['purchase_order_series'] = $purchaseOrder->purchase_order_series;
            $rowData['Time'] = Carbon::parse($purchaseOrder->created_at)->format('h:i A');
            $rowData['Date'] = Carbon::parse($purchaseOrder->created_at)->format('j F Y');
            // $rowData['Date'] = $purchaseOrder->(date('j F Y', strtotime($purchaseOrder->created_at)));
            $rowData['buyer'] = $purchaseOrder->buyer_name;
            $rowData['seller'] = $purchaseOrder->seller_name;
            // $rowData['item_code'] = $purchaseOrder->item_code;
            // $rowData['unit'] = $purchaseOrder->unit;
            // $rowData['rate'] = $purchaseOrder->rate;
            // $rowData['qty'] = $purchaseOrder->total_qty;
            $rowData['total_amount'] = $purchaseOrder->total;
                // dd($purchaseOrder->buyerDetails);

            // $rowData['status'] = $purchaseOrder->statuses[0]->status;
            $rowData['status'] = '';

        if ($purchaseOrder->statuses->isNotEmpty()) {
            $status = $purchaseOrder->statuses[0]->status;
            $user_name = $purchaseOrder->statuses[0]->user_name;

            if ($status == 'draft') {
                $rowData['status'] = 'Not Sent';
            } elseif ($status == 'sent') {
                $rowData['status'] = 'Sent';
            } elseif ($status == 'self_accept') {
                $rowData['status'] = 'Self Delivered';
            } elseif ($status == 'accept') {
                $rowData['status'] = 'Accepted By ' . $user_name;
            } elseif ($status == 'reject') {
                $rowData['status'] = 'Rejected By ' . $user_name;
            } elseif ($status == 'return') {
                $rowData['status'] = 'Self Returned';
            }
        }

            $rowData['comment'] = $purchaseOrder->comment;


            $exportedData[] = $rowData;
        }

        // Create a temporary file path for the CSV
        $filePath = 'temp/' . uniqid() . '.csv';

        // Store the CSV file using Laravel Storage
        Storage::disk('local')->put($filePath, $this->generateCsvFile($exportedData));

        // Define the file name and content type
        $fileName = 'exported_purchaseOrders.csv';
        $contentType = 'text/csv';

        // Create a CSV response
        $response = new Response();
        $response->header('Content-Type', $contentType);
        $response->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->setCharset('UTF-8');

        // Get the CSV content from the stored file and add it to the response
        $response->setContent(Storage::disk('local')->get($filePath));

        // Delete the temporary CSV file after downloading
        Storage::disk('local')->delete($filePath);

        return $response;
    }
    // Export DEtailed Sent purchase$purchaseOrder
    public function exportDetailedPurchaseOrder(Request $request)
    {
        // Fetch the products and their related product details
        // $products = purchase$purchaseOrder::with('details')->get();
        $query = PurchaseOrder::query()->orderByDesc('id');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // $purchaseOrders = $query->with(['sellerUser', 'statuses', 'buyerDetails','orderDetails', 'sfp'])->select('purchase$purchaseOrders.*')->paginate(100,null,null,$request->page??1);
        $purchaseOrders = $query->where('seller_id', $userId)->with('sellerUser', 'statuses', 'buyerDetails','orderDetails','orderDetails.columns')->select('purchase_orders.*')->paginate(100,null,null,$request->page??1);

        // dd($purchaseOrders->orderDetails);

        // Create an array to store the exported data
        $exportedData = [];

        // Iterate through the products and their related product details
        foreach ($purchaseOrders as $key => $challan) {
            $rowData['id'] =  ++$key;
            // dd($challan);
            // foreach ($challan->orderDetails as $productDetail) {
            //     // $rowData[$productDetail->column_name] = $productDetail->column_value;
            //     dd($productDetail->unit);
            // }
            // $rowData['Time'] = $challan->(date('h:i A', strtotime($challan->created_at)));


            $rowData['purchase_order_series'] = $challan->purchase_order_series;
            $rowData['Time'] = Carbon::parse($challan->created_at)->format('h:i A');
            $rowData['Date'] = Carbon::parse($challan->created_at)->format('j F Y');
            // $rowData['Date'] = $challan->(date('j F Y', strtotime($challan->created_at)));
            $rowData['seller'] = $challan->seller_name;
            $rowData['buyer'] = $challan->buyer_name;


            if ($challan->column !== null && is_iterable($challan->column)) {
                foreach ($challan->column as $index => $col) {
                    if ($index == 0) {
                        // Use the value for the first occurrence of column index 0
                        $rowData['article'] = $col->column_value;
                    } elseif ($index == 1) {
                        // Use the value for the first occurrence of column index 1
                        $rowData['hsn'] = $col->column_value;
                        break; // Stop the loop after the first occurrence of column index 1
                    }
                }
            } else {
                // Handle the case where $challan->column is null or not iterable
                // This could be setting default values, logging an error message, throwing an exception, etc.
            }



            // Initialize arrays for order details
            $rowData['unit'] = [];
            $rowData['qty'] = [];
            $rowData['unit_price'] = [];
            $rowData['tax'] = [];
            $rowData['total_amount'] = [];

            foreach ($challan->orderDetails as $orderDetail) {
                // Create a new row for each order detail
                $detailRow = $rowData;

                // Add details to the row
                $detailRow['unit'] = $orderDetail->unit ?? '';
                $detailRow['qty'] = $orderDetail->qty ?? '';
                $detailRow['unit_price'] = $orderDetail->rate ?? '';
                $detailRow['tax'] = $orderDetail->tax ?? '';
                $detailRow['total_amount'] = $orderDetail->total_amount ?? '';

                // Add the row to the exported data
                $exportedData[] = $detailRow;
            }
            // Convert arrays to strings
            $rowData['unit'] = implode(',', $rowData['unit']);
            $rowData['qty'] = implode(',', $rowData['qty']);
            $rowData['unit_price'] = implode(',', $rowData['unit_price']);
            $rowData['tax'] = implode(',', $rowData['tax']);
            $rowData['total_amount'] = implode(',', $rowData['total_amount']);


        }
        // Create a temporary file path for the CSV
        $filePath = 'temp/' . uniqid() . '.csv';

        // Store the CSV file using Laravel Storage
        Storage::disk('local')->put($filePath, $this->generateCsvFile($exportedData));

        // Define the file name and content type
        $fileName = 'exported_detailed_purchase.csv';
        $contentType = 'text/csv';

        // Create a CSV response
        $response = new Response();
        $response->header('Content-Type', $contentType);
        $response->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->setCharset('UTF-8');

        // Get the CSV content from the stored file and add it to the response
        $response->setContent(Storage::disk('local')->get($filePath));

        // Delete the temporary CSV file after downloading
        Storage::disk('local')->delete($filePath);

        return $response;
    }

    private function generateCsvFile($data)
    {
        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, array_keys($data[0])); // Write the header row
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);

        return stream_get_contents($handle);
    }

    // public function addComment(Request $request, $poId)
    // {
    //     try {
    //         // Find the Challan by ID
    //         $returnChallan = ReturnPurchaseOrder::findOrFail($returnChallanId);

    //         if($request->has('status_comment')){
    //             // Get the existing status_comment data
    //             $statusComment = json_decode($returnChallan->status_comment, true);

    //             // Add the new comment to the status_comment data
    //             $statusComment[] = [
    //                 'comment' => $request->status_comment,
    //                 'date' => date('Y-m-d'),
    //                 'time' => date('H:i:s'),
    //                 'name' => Auth::user()->name ?? Auth::user()->team_user_name,
    //             ];

    //             // Update the status_comment field with the combined data
    //             $returnChallan->update(['status_comment' => json_encode($statusComment)]);
    //         }


    //         // Return a response indicating success
    //         return response()->json([
    //             'data' => $returnChallan->statuses,
    //             'message' => 'Comment added successfully.',
    //             'status_code' => 200
    //         ], 200);
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return response()->json([
    //             'message' => 'Challan Not Found.',
    //             'status_code' => 400
    //         ], 400);
    //     }
    // }

    public function addComment(Request $request, $purchaseOrderIds)
    {
        try {
            $permissions = json_decode(Auth::user()->permissions, true);
            $successCount = 0;
            $totalCount = 0;

            // Convert single ID to array if necessary
            $purchaseOrderIds = is_array($purchaseOrderIds) ? $purchaseOrderIds : [$purchaseOrderIds];

            foreach ($purchaseOrderIds as $purchaseOrderId) {
                $totalCount++;

                // Find the PurchaseOrder by ID
                $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);
                $purchaseOrder->load('buyerUser', 'sellerUser');

                if ($request->has('buyer')) {
                    $receiverUserEmail = $purchaseOrder->sellerUser ? $purchaseOrder->sellerUser->email : null;
                    $phone = $purchaseOrder->sellerUser->phone;
                    $senderUser = $purchaseOrder->buyerUser->name;
                    $purchaseOrderNo = $purchaseOrder->purchase_order_series . '-' . $purchaseOrder->series_num;
                    $pdfUrl = $purchaseOrder->pdf_url;
                    $heading = 'PurchaseOrder';

                    // Show Notifications in Status
                    $notification = new Notification([
                        'user_id' => $purchaseOrder->sellerUser->id,
                        'message' => 'New Comment added by ' . $purchaseOrder->buyerUser->name,
                        'type' => 'PO',
                        'added_id' => $purchaseOrder->id,
                        'panel' => 'seller',
                        'template_name' => 'sent_invoice',
                    ]);
                    $notification->save();

                } elseif ($request->has('seller')) {
                    $receiverUserEmail = $purchaseOrder->buyerUser ? $purchaseOrder->buyerUser->email : null;
                    $phone = $purchaseOrder->buyerUser->phone;
                    $senderUser = $purchaseOrder->sellerUser->name;
                    $purchaseOrderNo = $purchaseOrder->purchase_order_series . '-' . $purchaseOrder->series_num;
                    $pdfUrl = $purchaseOrder->pdf_url;
                    $heading = 'Purchase order';

                    // Show Notifications in Status
                    $notification = new Notification([
                        'user_id' => $purchaseOrder->buyerUser->id,
                        'message' => 'New Comment added by ' . $purchaseOrder->sellerUser->name,
                        'type' => 'PO',
                        'added_id' => $purchaseOrder->id,
                        'panel' => 'buyer',
                        'template_name' => 'purchase_order_seller',
                    ]);
                    $notification->save();
                }

                if ($request->has('status_comment')) {
                    // Get the existing status_comment data
                    $statusComment = json_decode($purchaseOrder->status_comment, true) ?? [];

                    // Add the new comment to the status_comment data
                    $statusComment[] = [
                        'comment' => $request->status_comment,
                        'date' => date('Y-m-d'),
                        'time' => date('H:i:s'),
                        'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    ];

                    // Update the status_comment field with the combined data
                    $purchaseOrder->update(['status_comment' => json_encode($statusComment)]);

                    // Define the cost for sending a comment via WhatsApp
                    if (isset($permissions['buyer']['email']['Add Comment']) && $permissions['buyer']['email']['Add Comment'] == true) {
                        // Send the PDF via email for SFP Challan Alert
                        if ($receiverUserEmail != null) {
                            $pdfEmailService = new PDFEmailService();
                            $pdfEmailService->addCommentSentChallanMail($purchaseOrder, $receiverUserEmail, $request->status_comment);
                        }
                    }
                    // Calculate the amount to deduct (90 paisa + 18% GST)
                    $deduction = 0.90 + (0.90 * 0.18);
                    // Get the user's wallet
                    $wallet = Wallet::where('user_id', Auth::id())->first();
                    // Check if the wallet balance is greater than the deduction
                    if ($wallet !== null && $wallet->balance >= $deduction) {
                        if (isset($permissions['buyer']['whatsapp']['Add Comment']) && $permissions['buyer']['whatsapp']['Add Comment'] == true) {
                            $pdfWhatsAppService = new PDFWhatsAppService();
                            $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendCommentOnWhatsApp($phone, $senderUser, $purchaseOrderNo, $request->status_comment, $pdfUrl, $heading);
                            if($pdfWhatsAppServiceResponse == true){
                                // Deduct the cost from the wallet
                                $wallet->balance -= $deduction;
                                $wallet->save();
                                WalletLog::create([
                                    'user_id' => $wallet->user_id,
                                    'amount_deducted' => $deduction,
                                    'remaining_balance' => $wallet->balance,
                                    'challan_id' => $purchaseOrderId,
                                    'action' => 'add_comment',
                                    'recipient' => $purchaseOrder->buyer_name
                                ]);
                            }
                        }
                    }

                    $successCount++;
                }
            }

            // Prepare the response message
            // $message = $totalCount === 1
            //     ? "Comment added successfully."
            //     : "$successCount out of $totalCount comments added successfully.";

                $message = $totalCount === 1
                ? "Comment added successfully."
                : "Comments added successfully.";

            // Return a response indicating success
            return response()->json([
                'message' => $message,
                'status_code' => 200,
                'success' => true
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'One or more PurchaseOrders Not Found.',
                'status_code' => 400,
                'success' => false
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while adding comments: ' . $e->getMessage(),
                'status_code' => 500,
                'success' => false
            ], 500);
        }
    }

}
