<?php

namespace App\Http\Controllers\V1\Estimate;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Estimates;
use App\Models\Product;
use App\Models\ProductLog;
use App\Models\EstimateSfps;
use Illuminate\Support\Str;
use App\Models\Buyer;
use App\Models\Notification;
use App\Models\UserDetails;
use App\Models\BuyerDetails;
use App\Models\BulkImportLog;
use Illuminate\Http\Request;
use App\Models\PanelSeriesNumber;
use App\Models\EstimateStatus;
use App\Models\EstimateOrderColumns;
use App\Models\EstimateOrderDetail;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;


class EstimateController extends Controller
{
    public function store(Request $request)
    {
        // dd($request->all());
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'estimate_series' => 'required|string',
            'series_num' => 'required',
            'estimate_date' => 'required',
            // 'feature_id' => 'required|exists:features,id',
            'buyer_id' => 'nullable|exists:users,id',
            'buyer' => 'nullable|string',
            'comment' => 'nullable|string',
            'calculate_tax' => 'nullable|boolean',
            'total' => 'numeric|min:0',
            'order_details.*.unit' => 'nullable|string',
            'order_details.*.rate' => 'nullable|numeric|min:0',
            'order_details.*.qty' => 'nullable|numeric|min:0',
            // 'order_details.*.details' => 'nullable|string',
            'order_details.*.tax' => 'nullable|numeric|min:0',
            'order_details.*.discount' => 'nullable|numeric|min:0',
            // 'order_details.*.total_amount' => 'numeric|min:0',
            'order_details.*.item_code' => 'nullable',
            'order_details.*.columns.*.column_name' => 'nullable|string',
            'order_details.*.columns.*.column_value' => 'nullable|string',
            'statuses.*.comment' => 'nullable|string',
        ]);
        // dd($validator);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        // dd($request->estimate_series);
        $featureId = 12; // Replace with YOUR_FEATURE_ID
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
                    'estimate_id' => null,
                    'order_detail_ids' => null,
                    'order_column_ids' => null,
                    'status_ids' => null,
                    'status_code' => 200
                ], 200);
                // Handle the case when both usage counts could not be updated successfully
                // Add appropriate error handling or log the issue for further investigation.
            }
        }

        // Get the latest series_num for the given estimate_series and user_id
        // $latestSeriesNum = Estimates::where('estimate_series', $request->estimate_series)
        //     ->where('seller_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
        //     ->max('series_num');
        // // Increment the latestSeriesNum for the new estimate
        // $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

        // Create a new Estimates
        $estimate = new Estimates([
            'estimate_series' => $request->estimate_series,
            'estimate_date' => $request->estimate_date . ' ' . now()->format('H:i:s'),
            'series_num' => $request->series_num,
            'seller_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'seller' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
            'buyer_id' => $request->buyer_id?? null,
            'buyer_detail_id' => $request->buyer_detail_id?? null,
            'buyer' => !empty($request->buyer) ? $request->buyer : 'Cash',
            'comment' => $request->comment,
            'calculate_tax' => $request->calculate_tax ?? null,
            'total' => isset($request->total) ? (float) $request->total : 0.00,
            'total_qty' => $request->total_qty ?? 0.00,
            'round_off' => $request->round_off ?? null,
            'purchase_order_series' => $request->purchase_order_series ?? null,
            'team_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_id : null,
        ]);
        $estimate->save();
        // dd($estimate);

        // Fetch buyer's state from the database
        $buyer = User::find($request->buyer_id);
        $buyerState = $buyer ? $buyer->state : null;

        // Determine if CGST/SGST or IGST should be calculated
        $sellerState = Auth::guard(Auth::getDefaultDriver())->user()->state;
        // Create Estimates Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $tax = $orderDetailData['tax'] ?? 0.00;

                if ($sellerState === $buyerState) {
                    // Same state: calculate CGST and SGST
                    $cgst = round($tax * 0.50, 2); // Example: 50% of the tax
                    $sgst = round($tax * 0.50, 2); // Example: 50% of the tax
                    $igst = null;
                } else {
                    // Different states: calculate IGST
                    $igst = round($tax * 1.00, 2); // Example: 100% of the tax
                    $cgst = null;
                    $sgst = null;
                }


                $orderDetail = new EstimateOrderDetail([
                    'estimate_id' => $estimate->id,
                    'item_code' => $orderDetailData['item_code'] ?? null,
                    'unit' => $orderDetailData['unit'],
                    'rate' => $orderDetailData['rate'] ?? 0.00,
                    'qty' => $orderDetailData['qty'] ?? 0,
                    'details' => $orderDetailData['details'] ?? '',
                    'tax' => $tax,
                    'igst' => $igst,
                    'cgst' => $cgst,
                    'sgst' => $sgst,
                    'discount' => $orderDetailData['discount'] ?? 0.00,
                    'total_amount' => $orderDetailData['total_amount'] ?? 0.00,
                ]);
                $orderDetail->save();

                // Update Product
                if (isset($orderDetailData['item_code'])) {
                    // Find the product based on item_code
                    $product = Product::where('item_code', $orderDetailData['item_code'])->first();
                    // dd($after);
                    if ($product) {
                        // Update the quantity
                        $newQty = max(0, $product->qty - $orderDetailData['qty']);

                        // Save the updated quantity back to the database
                        $product->update(['qty' => $newQty]);
                        ProductLog::create([
                            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                            'product_id' => $product->id,
                            'qty_out' => $orderDetailData['qty'],
                            'out_method' => 'estimate',
                            'out_at' => now()
                        ]);
                    }
                }

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new EstimateOrderColumns([
                            'estimate_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ?? '',
                        ]);
                        $orderColumn->save();
                    }
                }
            }
        }
        // dd($request->all());
        // Create Estimates Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new EstimateStatus([
                    'estimate_id' => $estimate->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    'team_user_name' => Auth::user()->team_user_name ?? null,
                    'status' => 'draft',
                    'comment' => 'Estimates created successfully',
                ]);
                $status->save();
            }
        }
        // Get the IDs of the created records
        $invoiceId = $estimate->id;
        $orderDetailIds = $estimate->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $estimate->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $estimate->statuses->pluck('id')->toArray();

        // $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount($featureId, 1);

        // if (!$PlanFeatureUsageRecordResponse) {
        //     // Update usage count for FeatureTopupUsageRecord
        //     $FeatureTopupUsageRecorddResponse = $FeatureTopupUsageRecord->updateUsageCount($featureId, 1);

        //     if (!$FeatureTopupUsageRecorddResponse) {
        //         return response()->json([
        //             'message' => 'Something Went Wrong.',
        //             'estimate_id' => null,
        //             'order_detail_ids' => null,
        //             'order_column_ids' => null,
        //             'status_ids' => null,
        //             'status_code' => 400
        //         ], 400);
        //         // Handle the case when both usage counts could not be updated successfully
        //         // Add appropriate error handling or log the issue for further investigation.
        //     }
        // }

        $estimate = Estimates::where('id', $invoiceId)->with('buyerUser', 'sellerUser', 'orderDetails', 'orderDetails.columns', 'orderDetails.columns', 'statuses')->first();

        // // Generate the PDF for the Estimates using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateEstimatePDF($estimate);
        // dd($response);

        $response = (array) $response->getData();
        // Handle the response from PDFGenerator

        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $estimate->pdf_url = $response['pdf_url'];
            $estimate->save();
        }

        return response()->json([
            'message' => 'Estimates created successfully.',
            'estimate_id' => $invoiceId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200
        ], 200);
    }

    // public function invoiceSfpCreate(Request $request)
    // {
    //     $buyers = DB::table('team_users')->where('id', $request->id)->get();

    //     if($buyers->isEmpty()){
    //         return response()->json([
    //             'errors' => 'Buyer not found.',
    //             'status_code' => 400
    //         ], 400);
    //     }
    //     // dd($request);
    //     // $buyer = Auth::getDefaultDriver() == 'team-user' ?  DB::table('team_users')->where('id', $request->id)->first() :  DB::table('users')->where('id', $request->id)->first();
    //    foreach($buyers as $buyer){

    //         $invoiceSfp = new EstimateSfps(
    //             [
    //                 'estimate_id' => $request->estimate_id,
    //                 'sfp_by_id' => Auth::user()->id,
    //                 'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
    //                 'sfp_to_id' => $request->id,
    //                 'sfp_to_name' => $buyer->team_user_name ?? $buyer->name,
    //                 'comment' => $request->comment,
    //                 'status' => 'sent',
    //                 'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
    //             ]
    //         );
    //          $invoiceSfp->save();
    //     }
    //         return response()->json([
    //             'message' => 'Inovice SFP successfully.',
    //             'status_code' => 200
    //         ], 200);

    // }

    public function estimateSfpCreate(Request $request)
    {
        $teamUsers = DB::table('team_users')->whereIn('id', $request->team_user_ids)->get();
        // dd($teamUsers, $request->team_user_ids);
        // Fetch admins
        $admins = DB::table('users')->whereIn('id', $request->admin_ids)->get();

        // Combine team users and admins into one collection
        $buyers = $teamUsers->concat($admins);

        // $buyers = DB::table('team_users')->where('id', $request->id)->get();
        // dd($buyers, $request->id);
            if($buyers->isEmpty()){
                return response()->json([
                    'errors' => 'Buyer not found.',
                    'status_code' => 400
                ], 400);
            }
            // dd($buyers, $request);
            // Fetch Challan by ID
            $estimate = Estimates::findOrFail($request->estimate_id);
            $estimate->load('statuses', 'sfp');

            $subuser = $estimate->statuses[0]->team_user_name;
            foreach ($buyers as $buyer) {
            $invoiceSfp = new EstimateSfps(
                [
                    'estimate_id' => $request->estimate_id,
                    'sfp_by_id' => Auth::user()->id,
                    'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
                    'sfp_to_id' => $buyer->id,
                    'sfp_to_name' => $buyer->team_user_name ?? $buyer->name,
                    'comment' => $request->comment,
                    'status' => 'sent',
                    'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
                ]
            );
            $invoiceSfp->save();
            $userName = $invoiceSfp->sfp_to_name;

            $pdfUrl = $estimate->pdf_url;
            $senderUser = $invoiceSfp->sfp_to_name;
            $receiverUser = $invoiceSfp->sfp_by_name;
            $phone = $buyer->phone;
            $invoiceNo = $estimate->estimate_series . '-' . $estimate->series_num;

            $permissions = json_decode(Auth::user()->permissions, true);
            $deduction = 0.90 + (0.90 * 0.18);
                    // Get the user's wallet
                    $wallet = Wallet::where('user_id', Auth::id())->first();
                    // dd($wallet->balance);
                    // Check if the wallet balance is greater than the deduction
                    if ($wallet !== null && $wallet->balance >= $deduction) {
                    if (isset($permissions['sender']['whatsapp']['Sfp']) && $permissions['sender']['whatsapp']['Sfp'] == true) {

                    $pdfWhatsAppService = new PDFWhatsAppService();
                    $pdfWhatsAppServiceResponse = $pdfWhatsAppService->purchase_order_series($phone, $senderUser, $pdfUrl, $receiverUser, $challanNo);
                        if($pdfWhatsAppServiceResponse == true){
                            // Deduct the cost from the wallet
                            $wallet->balance -= $deduction;
                            $wallet->save();
                        }
                    }
                }


            // Send the PDF via email for SFP Challan Alert
            // if ($buyer->email != null) {
            //     $pdfEmailService = new PDFEmailService();
            //     $recipientEmail = $buyer->email; // Replace with the actual recipient email address
            //     $pdfEmailService->invoiceSfpByEmail($estimate, $recipientEmail, $userName);

            // }
        }
        return response()->json([
            'message' => 'Estimates SFP successfully.',
            'status_code' => 200
        ], 200);
    }

    public function sfpAccept(Request $request, $sfpId)
    {
        try {

            $sfp = EstimateSfps::where('id', $sfpId)->update(['status' => 'accept']);
            if ($sfp) {
                return response()->json([
                    'message' => 'SFP Accepted successfully.',
                    'status_code' => 200
                ], 200);
            } else {
                return response()->json([
                    'errors' => 'SFP Not accepted.',
                    'status_code' => 500
                ], 500);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'SFP Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function sfpReject(Request $request, $sfpId)
    {
        try {

            $sfp = EstimateSfps::where('id', $sfpId)->update(['status' => 'reject']);
            if ($sfp) {
                return response()->json([
                    'message' => 'SFP Rejected successfully.',
                    'status_code' => 200
                ], 200);
            } else {
                return response()->json([
                    'errors' => 'SFP Not rejected.',
                    'status_code' => 500
                ], 500);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'SFP Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'estimate_series' => 'string',
            'estimate_date' => 'required',
            'buyer_id' => 'nullable|exists:users,id',
            'buyer' => 'nullable|string',
            'comment' => 'nullable|string',
            'calculate_tax' => 'nullable|boolean',
            'total' => 'numeric|min:0',
            // 'order_details.*.id' => 'required|exists:invoice_order_details,id',
            'order_details.*.unit' => ' nullable|string',
            'order_details.*.rate' => 'numeric|min:0',
            'order_details.*.qty' => 'numeric|min:0',
            'order_details.*.details' => 'nullable|string',
            'order_details.*.total_amount' => 'numeric|min:0',
            'order_details.*.discount' => 'nullable|numeric|min:0',
            // 'order_details.*.columns.*.id' => 'required|exists:invoice_order_columns,id',
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

        // Find the Estimates by ID
        $estimate = Estimates::find($id);

        if (!$estimate) {
            return response()->json([
                'message' => 'Estimates not found.',
                'status_code' => 400,
            ], 400);
        }

        // Update Estimates data
        $estimate->comment = $request->input('comment', $estimate->comment);
        $estimate->total = $request->input('total', $estimate->total);
        $estimate->estimate_date = $request->input('estimate_date', $estimate->estimate_date);
        $estimate->round_off = $request->input('round_off', $estimate->round_off);
        // Update other fields as needed
        $estimate->save();

        // Update Estimates Order Details and their Columns
        if ($request->has('order_details')) {
            $EstimateOrderDetail = EstimateOrderDetail::where('estimate_id', $id)->with('columns')->get();
            if ($EstimateOrderDetail) {
                foreach ($EstimateOrderDetail as $key => $value) {
                    // Delete the associated comments first
                    $EstimateOrderDetail[$key]->columns()->delete();
                    $EstimateOrderDetail[$key]->delete();
                }
                // Then, delete the EstimateOrderDetail itself
            }
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new EstimateOrderDetail([
                    'estimate_id' => $estimate->id,
                    'unit' => $orderDetailData['unit'] ?? null,
                    'rate' => $orderDetailData['rate'] ?? 0.00,
                    'qty' => $orderDetailData['qty'] ?? 0,
                    'details' => $orderDetailData['details'] ?? '',
                    'tax' => $orderDetailData['tax'] ?? null,
                    'discount' => $orderDetailData['discount'] ?? 0.00,
                    'total_amount' => $orderDetailData['total_amount'] ?? 0.00,


                ]);
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        if (isset($columnData['column_name']) && isset($columnData['column_value'])) {
                            $orderColumn = new EstimateOrderColumns([
                                'estimate_order_detail_id' => $orderDetail->id,
                                'column_name' => $columnData['column_name'],
                                'column_value' => $columnData['column_value'],
                            ]);
                            $orderColumn->save();
                        }
                    }
                }
            }



        }

        // Create Estimates Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new EstimateStatus([
                    'estimate_id' => $estimate->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'team_user_name' => Auth::user()->name ?? Auth::user()->team_user_name ?? null,
                    'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                    'status' => 'draft',
                    'comment' => 'Estimates updated successfully',
                ]);
                $status->save();
            }
        }

        // Get the IDs of the created records
        $invoiceId = $estimate->id;
        $orderDetailIds = $estimate->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $estimate->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $estimate->statuses->pluck('id')->toArray();

        $estimate = Estimates::where('id', $invoiceId)->with('buyerUser', 'sellerUser', 'orderDetails', 'orderDetails.columns', 'orderDetails.columns', 'statuses')->first();
        // dd($estimate);
        // Generate the PDF for the Estimates using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateEstimatePDF($estimate);
        // dd($response);

        $response = (array) $response->getData();
        // Handle the response from PDFGenerator

        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $estimate->pdf_url = $response['pdf_url'];
            $estimate->save();
        }


        return response()->json([
            'message' => 'Estimates updated successfully.',
            'estimate_id' => $invoiceId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200,
        ], 200);
    }
    public function modify(Request $request, $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'estimate_series' => 'string',
            'estimate_date' => 'required',
            'buyer_id' => 'exists:users,id',
            'buyer' => 'string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            // 'order_details.*.id' => 'required|exists:invoice_order_details,id',
            'order_details.*.unit' => 'required|string',
            'order_details.*.rate' => 'numeric|min:0',
            'order_details.*.qty' => 'integer|min:0',
            'order_details.*.details' => 'nullable|string',
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

        // Find the Estimates by ID
        $estimate = Estimates::find($id);

        if (!$estimate) {
            return response()->json([
                'message' => 'Estimates not found.',
                'status_code' => 400,
            ], 400);
        }

        // Update Estimates data
        $estimate->comment = $request->input('comment', $estimate->comment);
        $estimate->total = $request->input('total', $estimate->total);
        $estimate->estimate_date = $request->input('estimate_date', $estimate->estimate_date);
        // Update other fields as needed
        $estimate->save();

        // Update Estimates Order Details and their Columns
        if ($request->has('order_details')) {
            $EstimateOrderDetail = EstimateOrderDetail::where('estimate_id', $id)->with('columns')->get();
            if ($EstimateOrderDetail) {
                foreach ($EstimateOrderDetail as $key => $value) {
                    // Delete the associated comments first
                    $EstimateOrderDetail[$key]->columns()->delete();
                    $EstimateOrderDetail[$key]->delete();
                }
                // Then, delete the EstimateOrderDetail itself
            }
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new EstimateOrderDetail([
                    'estimate_id' => $estimate->id,
                    'unit' => $orderDetailData['unit'],
                    'rate' => $orderDetailData['rate'] ?? 0.00,
                    'qty' => $orderDetailData['qty'] ?? 0,
                    'details' => $orderDetailData['details'],
                    'total_amount' => $orderDetailData['total_amount'] ?? 0.00,
                ]);
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new EstimateOrderColumns([
                            'estimate_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'],
                            'column_value' => $columnData['column_value'],
                        ]);
                        $orderColumn->save();
                    }
                }
            }
            //     foreach ($request->order_details as $orderDetailData) {
            //         $orderDetail = EstimateOrderDetail::find($orderDetailData['id']);

            //         if (!$orderDetail) {
            //             return response()->json([
            //                 'message' => 'Estimates Order Detail not found.',
            //                 'status_code' => 400,
            //             ], 400);
            //         }

            //         $orderDetail->unit = $orderDetailData['unit'];
            //         $orderDetail->rate = $orderDetailData['rate'] ?? 0.00;
            //         $orderDetail->qty = $orderDetailData['qty'] ?? 0;
            //         $orderDetail->total_amount = $orderDetailData['total_amount'] ?? 0.00;
            //         // Update other fields as needed
            //         $orderDetail->save();

            //         if (isset($orderDetailData['columns'])) {
            //             foreach ($orderDetailData['columns'] as $columnData) {
            //                 $orderColumn = EstimateOrderColumns::find($columnData['id']);

            //                 if (!$orderColumn) {
            //                     return response()->json([
            //                         'message' => 'Estimates Order Column not found.',
            //                         'status_code' => 400,
            //                     ], 400);
            //                 }

            //                 $orderColumn->column_name = $columnData['column_name'];
            //                 $orderColumn->column_value = $columnData['column_value'];
            //                 // Update other fields as needed
            //                 $orderColumn->save();
            //             }
            //         }
            //     }
        }

        // Create Estimates Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new EstimateStatus([
                    'estimate_id' => $estimate->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                    'status' => 'modified',
                    'comment' => 'Estimates modified successfully',
                ]);
                $status->save();
            }
        }

        // Get the IDs of the created records
        $invoiceId = $estimate->id;
        $orderDetailIds = $estimate->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $estimate->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $estimate->statuses->pluck('id')->toArray();

        // Find the Estimates by ID
        $estimate = Estimates::findOrFail($invoiceId)->with('buyerUser', 'sellerUser', 'orderDetails', 'orderDetails.columns', 'statuses')->first();
        // $estimate = Estimates::findOrFail($invoiceId)->with('buyerUser', 'sellerUser', 'orderDetails', 'orderDetails.columns', 'statuses', 'sfp');

        // Generate the PDF for the Estimates using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateEstimatePDF($estimate);

        if ($response['status_code'] === 200) {
            $estimate->pdf_url = $response['pdf_url'];
            $estimate->save();
            return response()->json([
                'message' => 'Estimates modified successfully.',
                'estimate_id' => $invoiceId,
                'order_detail_ids' => $orderDetailIds,
                'order_column_ids' => $orderColumnIds,
                'status_ids' => $statusIds,
                'status_code' => 200,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Estimates PDF updation fail.',
                'estimate_id' => $invoiceId,
                'order_detail_ids' => $orderDetailIds,
                'order_column_ids' => $orderColumnIds,
                'status_ids' => $statusIds,
                'status_code' => 400,
            ], 400);
        }
    }

    public function importStore(Request $request)
    {
    //    dd($request);
        $featureId = $request->feature_id; // Replace with YOUR_FEATURE_ID
        $invoiceDate = Carbon::parse($request->estimate_date)->format('Y-m-d H:i:s');
        if (!$invoiceDate) {
            $invoiceDate = now()->format('Y-m-d H:i:s');
        }

        // Create a new Estimates
        $estimate = new Estimates([
            'estimate_series' => $request->estimate_series,
            'estimate_date' => $invoiceDate,
            'series_num' => $request->series_num,
            'seller_id' => $request->seller_id,
            'seller' => $request->seller,
            'buyer_id' => $request->buyer_id,
            'buyer_detail_id' => $request->buyer_detail_id ?? null,
            'buyer' => $request->buyer,
            'comment' => $request->comment,
            'total' => isset($request->total) ? (float) $request->total : 0.00,
            'total_qty' => $request->total_qty ?? 0.00,
            'created_at' => $request->created_at,
            'updated_at' => $request->updated_at,
        ]);
        $estimate->save();

        // Create Estimates Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new EstimateOrderDetail([
                    'estimate_id' => $estimate->id,
                    // 'item_code' => $orderDetailData['item_code'],
                    'unit' => $orderDetailData['unit'],
                    'rate' => $orderDetailData['rate'] ?? 0.00,
                    'qty' => $orderDetailData['qty'] ?? 0,
                    'details' => $orderDetailData['details'] ?? '',
                    'tax' => $orderDetailData['tax'] ?? 0.00,
                    // 'discount' => $orderDetailData['discount'] ?? 0.00,
                    'total_amount' => $orderDetailData['total_amount'] ?? 0.00,
                ]);
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new EstimateOrderColumns([
                            'estimate_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'],
                            'column_value' => $columnData['column_value'],
                        ]);
                        $orderColumn->save();
                    }
                }
            }
        }

        // Create Estimates Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new EstimateStatus([
                    'estimate_id' => $estimate->id,
                    'user_id' => $request->seller_id,
                    'user_name' => $request->seller,
                    'status' => $statusData['status'],
                    'comment' => $statusData['comment'],
                    'created_at' => $request->created_at,
                    'updated_at' => $request->updated_at,
                ]);
                $status->save();
            }
        }
        // $estimate = Estimates::where('id', $estimate->id)->with('buyerUser', 'buyerDetails', 'sellerUser', 'orderDetails', 'orderDetails.columns', 'orderDetails.columns', 'statuses')->first();
        // $estimate = Estimates::where('id', $estimate->id)->with('buyerUser', 'buyerDetails', 'sellerUser', 'orderDetails', 'orderDetails.columns', 'orderDetails.columns', 'statuses')->first();
        $estimate = Estimates::where('id', $estimate->id)->with('buyerUser', 'sellerUser', 'orderDetails', 'orderDetails.columns', 'orderDetails.columns', 'statuses')->first();
        // Generate the PDF for the Estimates using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();


        $response = $pdfGenerator->generateEstimatePDF($estimate);


        $response = (array) $response->getData();
        // Handle the response from PDFGenerator

        // PDF generated successfully
        $estimate->pdf_url = $response['pdf_url'];
        $estimate->save();

        // Get the IDs of the created records
        $invoiceId = $estimate->id;
        $orderDetailIds = $estimate->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $estimate->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $estimate->statuses->pluck('id')->toArray();



        // $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount($featureId, 1);

        // if (!$PlanFeatureUsageRecordResponse) {
        //     // Update usage count for FeatureTopupUsageRecord
        //     $FeatureTopupUsageRecorddResponse = $FeatureTopupUsageRecord->updateUsageCount($featureId, 1);

        //     if (!$FeatureTopupUsageRecorddResponse) {
        //         return response()->json([
        //             'message' => 'Something Went Wrong.',
        //             'estimate_id' => null,
        //             'order_detail_ids' => null,
        //             'order_column_ids' => null,
        //             'status_ids' => null,
        //             'status_code' => 400
        //         ], 400);
        //         // Handle the case when both usage counts could not be updated successfully
        //         // Add appropriate error handling or log the issue for further investigation.
        //     }
        // }
        return true;
        // return response()->json([
        //     'message' => 'Estimates created successfully.',
        //     'estimate_id' => $invoiceId,
        //     'order_detail_ids' => $orderDetailIds,
        //     'order_column_ids' => $orderColumnIds,
        //     'status_ids' => $statusIds,
        //     'status_code' => 200
        // ], 200);
    }
    public function send(Request $request, $invoiceId)
    {
        // Find the Estimates by ID
        $estimate = Estimates::where('id', $invoiceId)->with('buyerUser', 'sellerUser', 'orderDetails', 'orderDetails.columns', 'orderDetails.columns', 'statuses')->first();
        // dd($estimate);
        $permissions = $estimate->sellerUser->permissions ? json_decode($estimate->sellerUser->permissions, true) : null;
        // dd($permissions);
        // Generate the PDF for the Estimates using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateEstimatePDF($estimate);
        // dd($response);

        $response = (array) $response->getData();
        // Handle the response from PDFGenerator

        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $estimate->pdf_url = $response['pdf_url'];
            $estimate->save();
            if ($permissions
            && is_array($permissions['seller'])
            && is_array($permissions['seller']['whatsapp'])
            && $permissions['seller']['whatsapp']['sent_invoice']) {
                // Send the PDF via email
                if ($estimate->buyerUser->email != null) {
                    $pdfEmailService = new PDFEmailService();
                    $recipientEmail = $estimate->buyerUser->email; // Replace with the actual recipient email address

                    $pdfEmailService->sendEstimateByEmail($estimate, $response['pdf_url'], $recipientEmail);
                    // dd($pdfEmailService->sendInvoiceByEmail($estimate, $response['pdf_url'], $recipientEmail));
                }

                // Assuming that PlanAdditionalFeatureUsageRecord and AdditionalFeatureTopupUsageRecord models have been imported.


                if ($estimate->buyerUser->phone != null) {
                    $featureId = $request->feature_id; // Replace with YOUR_FEATURE_ID

                    // Validate usage limit for PlanAdditionalFeatureUsageRecord
                    $PlanAdditionalFeatureUsageRecord = new PlanAdditionalFeatureUsageRecord();

                    // Validate usage limit for AdditionalFeatureTopupUsageRecord
                    $AdditionalFeatureTopupUsageRecord = new AdditionalFeatureTopupUsageRecord();

                    $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, 1);

                    // Calculate the amount to deduct (90 paisa + 18% GST)
                    $deduction = 0.90 + (0.90 * 0.18);
                    // Get the user's wallet
                    $wallet = Wallet::where('user_id', Auth::id())->first();
                    if ($wallet !== null && $wallet->balance >= $deduction) {
                        $pdfWhatsAppService = new PDFWhatsAppService;
                        $phoneNumbers = [$estimate->buyerUser->phone]; // Replace with the actual recipient phone number

                        if (!empty($estimate->additional_phone_number)) {
                            $phoneNumbers[] = $estimate->additional_phone_number;
                        }
                        $receiverUserEmail = $estimate->senderUser ? $estimate->senderUser->email : null;
                        $receiverUser = $estimate->buyerUser->name;
                        $senderUser = $estimate->sellerUser->name;
                        $invoiceNo = $estimate->estimate_series . '-' . $estimate->series_num;
                        $invoiceId = $estimate->id;
                        $heading = 'Estimate';
                        $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendChallanOnWhatsApp($phoneNumbers, $response['pdf_url'], $invoiceNo, $invoiceId, $receiverUser, $senderUser, $heading);
                        if($pdfWhatsAppServiceResponse == true){
                            // Deduct the cost from the wallet
                            $wallet->balance -= $deduction;
                            $wallet->save();
                        }
                    }
                    if ($PlanAdditionalFeatureUsageRecordResponse) {
                        $pdfWhatsAppService = new PDFWhatsAppService();
                        $recipientPhoneNumber = $estimate->buyerUser->phone; // Replace with the actual recipient phone number
                        $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendInvoiceOnWhatsApp($estimate, $response['pdf_url'], $recipientPhoneNumber);

                        if (!$pdfWhatsAppServiceResponse) {
                            Log::error('Error sending Estimates PDF Whatsapp for Estimates Id: ' . $estimate->id);
                            $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, -1);
                        }
                    } else {
                        // Update usage count for AdditionalFeatureTopupUsageRecord
                        $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, 1);

                        if ($AdditionalFeatureTopupUsageRecordResponse) {
                            $pdfWhatsAppService = new PDFWhatsAppService();
                            $recipientPhoneNumber = $estimate->buyerUser->phone; // Replace with the actual recipient phone number
                            $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendInvoiceOnWhatsApp($estimate, $response['pdf_url'], $recipientPhoneNumber);

                            if (!$pdfWhatsAppServiceResponse) {
                                Log::error('Error sending Estimates PDF Whatsapp for Estimates Id: ' . $estimate->id);
                                $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, -1);
                            }
                        }
                    }
                }
            }


            // Add a new "sent" status to the Estimates
            $status = new EstimateStatus([
                'estimate_id' => $invoiceId,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                'team_user_name' => Auth::user()->name ?? Auth::user()->team_user_name ?? null,
                'status' => 'sent',
                'comment' => 'Estimates sent for acceptance',
            ]);
            $status->save();

            if($request->status_comment && trim($request->status_comment) != ''){
                // Get the existing status_comment data
                $statusComment = json_decode($estimate->status_comment, true);

                // Add the new comment to the status_comment data
                $statusComment[] = [
                    'comment' => $request->status_comment,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'name' => Auth::user()->name ?? Auth::user()->team_user_name,
                ];

                // Update the status_comment field with the combined data
                $estimate->update(['status_comment' => json_encode($statusComment)]);
            }

            // $sfpExists = EstimateSfps::where('estimate_id', $invoiceId)->exists();

            // if ($sfpExists) {
            //     $invoiceSfp = new EstimateSfps(
            //         [
            //             'estimate_id' => $invoiceId,
            //             'sfp_by_id' => Auth::user()->id,
            //             'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
            //             'sfp_to_id' => null,
            //             'sfp_to_name' =>  $estimate->buyerUser->company_name ?? $estimate->buyerUser->name,
            //             'status' => 'sent',
            //             'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
            //         ]
            //     );
            //     $invoiceSfp = $invoiceSfp->save();
            // }

            // // Show Notifications in Status
            $notification = new Notification([
                'user_id' => $estimate->sellerUser->id,
                'message' => 'New Estimates Received by ' . $estimate->buyerUser->name,
                'added_id' => $estimate->id,
                'type' => 'estimate',
                'panel' => 'buyer',
                'template_name' => 'all_invoice',
            ]);
            $notification->save();

            // Add a new "sent" status to the Estimates
            $status = new EstimateStatus([
                'estimate_id' => $invoiceId,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'team_user_name' => Auth::user()->name ?? Auth::user()->team_user_name ?? null,
                'status' => 'sent',
                'comment' => 'Estimates sent for acceptance',
            ]);
            $status->save();
            // Return a response with the token and other relevant information
            return response()->json([
                'message' => 'Estimates sent successfully.',
                'estimate_id' => $invoiceId,
                // 'token' => $token,
                // 'token_expiry' => $status->token_expiry,
                'pdf_url' => $response['pdf_url'],
                'status_code' => 200
            ], 200);
        } else {
            // Error occurred during PDF generation and storage
            // Return an error response
            return response()->json([
                'message' => 'Error generating and storing Estimates PDF.',
                'estimate_id' => $invoiceId,
                // 'token' => $token,
                // 'token_expiry' => $status->token_expiry,
                'pdf_url' => null,
                'status_code' => $response['status_code']
            ], $response['status_code']);
        }
    }
    public function resend(Request $request, $invoiceId)
    {
        // Find the Estimates by ID
        $estimate = Estimates::findOrFail($invoiceId)->with('buyerUser', 'sellerUser', 'orderDetails', 'orderDetails.columns', 'statuses')->first();
        // $estimate = Estimates::findOrFail($invoiceId)->with('buyerUser', 'sellerUser', 'orderDetails', 'orderDetails.columns', 'statuses', 'sfp');

        // PDF generated successfully

        // Send the PDF via email
        if ($estimate->buyerUser->email != null) {
            $pdfEmailService = new PDFEmailService();
            $recipientEmail = $estimate->buyerUser->email; // Replace with the actual recipient email address
            $pdfEmailService->sendInvoiceByEmail($estimate, $estimate->pdf_url, $recipientEmail);
        }

        // Assuming that PlanAdditionalFeatureUsageRecord and AdditionalFeatureTopupUsageRecord models have been imported.

        if ($estimate->buyerUser->phone != null) {
            $featureId = $request->feature_id; // Replace with YOUR_FEATURE_ID

            // Validate usage limit for PlanAdditionalFeatureUsageRecord
            $PlanAdditionalFeatureUsageRecord = new PlanAdditionalFeatureUsageRecord();

            // Validate usage limit for AdditionalFeatureTopupUsageRecord
            $AdditionalFeatureTopupUsageRecord = new AdditionalFeatureTopupUsageRecord();

            $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, 1);

            if ($PlanAdditionalFeatureUsageRecordResponse) {
                $pdfWhatsAppService = new PDFWhatsAppService();
                $recipientPhoneNumber = $estimate->buyerUser->phone; // Replace with the actual recipient phone number
                $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendInvoiceOnWhatsApp($estimate, $estimate->pdf_url, $recipientPhoneNumber);

                if (!$pdfWhatsAppServiceResponse) {
                    Log::error('Error sending Estimates PDF Whatsapp for Estimates Id: ' . $estimate->id);
                    $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, -1);
                }
            } else {
                // Update usage count for AdditionalFeatureTopupUsageRecord
                $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, 1);

                if ($AdditionalFeatureTopupUsageRecordResponse) {
                    $pdfWhatsAppService = new PDFWhatsAppService();
                    $recipientPhoneNumber = $estimate->buyerUser->phone; // Replace with the actual recipient phone number
                    $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendInvoiceOnWhatsApp($estimate, $estimate->pdf_url, $recipientPhoneNumber);

                    if (!$pdfWhatsAppServiceResponse) {
                        Log::error('Error sending Estimates PDF Whatsapp for Estimates Id: ' . $estimate->id);
                        $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, -1);
                    }
                }
            }
        }


        // Add a new "sent" status to the Estimates
        $status = new EstimateStatus([
            'estimate_id' => $estimate->id,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
            'team_user_name' => Auth::user()->name ?? Auth::user()->team_user_name ?? null,
            'status' => 'resent',
            'comment' => 'Estimates resent for acceptance',
        ]);
        $status->save();

        // Return a response with the token and other relevant information
        return response()->json([
            'message' => 'Estimates resent successfully.',
            'estimate_id' => $estimate->id,
            // 'token' => $token,
            // 'token_expiry' => $status->token_expiry,
            'pdf_url' => $estimate->pdf_url,
            'status_code' => 200
        ], 200);
    }
    public function index(Request $request)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // dd($request->has('seller_id'), $request->has('buyer_id'));
        // $query = Estimates::query()->orderByDesc('id')->where('seller_id', $userId);
        $query = Estimates::query()->orderByDesc('id');
        $combinedValues = [];
        if (!$request->has('seller_id') && !$request->has('buyer_id')) {
            // Assuming you have a logged-in user, you can get the user ID like this:
            $query->where('seller_id', $userId);
        // Fetch the distinct filter values for Estimates table (for this user)
        $distinctInvoiceSeries = Estimates::where('seller_id', $userId)->distinct()->pluck('estimate_series');
        $distinctInvoiceSeriesNum = Estimates::where('seller_id', $userId)->distinct()->pluck('series_num');
        // $distinctSellerIds = Estimates::where('seller_id', $userId)->distinct()->get();
            // dd($distinctInvoiceSeriesNum);
        //    // Loop through each element of $distinctChallanSeries
        foreach ($distinctInvoiceSeries as $series) {
            // Loop through each element of $distinctChallanSeriesNum
            foreach ($distinctInvoiceSeriesNum as $num) {
                // Combine the series and number and push it into the combinedValues array
                $combinedValues[] = $series . '-' . $num;
            }
        }



        $distinctSellerIds = Estimates::where('seller_id', $userId)->distinct()->pluck('seller', 'seller_id');
        // dd($distinctSellerIds );
        $distinctBuyerIds = Estimates::where('seller_id', $userId)->distinct()->pluck('buyer', 'buyer_id');
        // $distinctStatuses = Status::distinct()->pluck('status');

        // Fetch the distinct "state" and "city" values from BuyerDetail table for buyers of this user
        $distinctStates = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('city');
        }
        // Filter by estimate_series
        // if ($request->has('estimate_series')) {
        //     $query->where('estimate_series', $request->estimate_series);
        // }

        if ($request->has('estimate_series')) {
            $searchTerm = $request->estimate_series;

            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                // Split the string into series and number
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                // Perform the search
                $query->where('estimate_series', $series)
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

         // Filter by date
         if ($request->has('from') && $request->has('to')) {
            $from = $request->from;
            $to = $request->to;

            $query->whereBetween('estimate_date', [$from, $to]);
        }

        // Filter by buyer_id
        if ($request->has('buyer_id')) {
        // dd($request->buyer_id);
            $query->where('buyer_id', $request->buyer_id);
            // dd($query);
            // Fetch the distinct filter values for Estimates table (for this user)
            $distinctInvoiceSeries = Estimates::where('buyer_id', $userId)->distinct()->pluck('estimate_series');

            $distinctInvoiceSeriesNum = Estimates::where('buyer_id', $userId)->distinct()->pluck('series_num');

            foreach ($distinctInvoiceSeries as $series) {
                // Loop through each element of $distinctChallanSeriesNum
                foreach ($distinctInvoiceSeriesNum as $num) {
                    // Combine the series and number and push it into the combinedValues array
                    $combinedValues[] = $series . '-' . $num;
                }
            }

            // $distinctSellerIds = Estimates::where('buyer_id', $userId)->distinct()->get();
            $distinctSellerIds = Estimates::where('buyer_id', $userId)->distinct()->pluck('seller', 'buyer_id');
            // dd($distinctSellerIds );
            $distinctBuyerIds = Estimates::where('buyer_id', $userId)->distinct()->pluck('buyer', 'buyer_id');
            // $distinctStatuses = Status::distinct()->pluck('status');
            // dd($distinctBuyerIds );

            // Fetch the distinct "state" and "city" values from BuyerDetail table for buyers of this user
            $distinctStates = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
                $query->select('id')->from('buyers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
                $query->select('id')->from('buyers')->where('user_id', $userId);
            })->distinct()->pluck('city');
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


        // Add any other desired filters

        // $invoices = $query->with(['buyerUser', 'statuses', 'buyerDetails','sfp'])->paginate(200);
        // $invoices = $query->with(['buyerUser', 'statuses', 'buyerDetails', 'orderDetails','orderDetails.columns', 'sfp'])->select('invoices.*')->paginate(100,null,null,$request->page??1);

        $perPage = $request->perPage ?? 100;
        $page = $request->page ?? 1;

        $invoices = $query
            ->with(['buyerUser', 'statuses', 'buyerDetails', 'orderDetails','orderDetails.columns', 'sfp'])
            ->select('invoices.*')
            ->paginate($perPage, ['*'], 'page', $page);

        // Calculate the starting item number for the current page
        $startItemNumber = ($page - 1) * $perPage + 1;

        // Add a custom attribute to each item in the collection with the calculated item number
        $invoices->each(function ($item) use (&$startItemNumber) {
            $item->setAttribute('custom_item_number', $startItemNumber++);
        });
        // dd($invoices);
        return response()->json([
            'message' => 'Success',
            'data' => $invoices,
            'status_code' => 200,
            'filters' => [
                'estimate_series' => $distinctInvoiceSeries,
                'series_num' => $distinctInvoiceSeriesNum,
                'merged_invoice_series' => $combinedValues,
                'seller_id' => $distinctSellerIds,
                'buyer_id' => $distinctBuyerIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                // Add any other filter values here if needed
            ]
        ], 200);
    }
    public function indexCounts(Request $request)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // $query = Estimates::query()->orderByDesc('id')->where('seller_id', $userId);
        $query = Estimates::query()->orderByDesc('id');
        if (!$request->has('seller_id') && !$request->has('buyer_id')) {
            // Assuming you have a logged-in user, you can get the user ID like this:
            $query->where('seller_id', $userId);
        // Fetch the distinct filter values for Estimates table (for this user)
        $distinctInvoiceSeries = Estimates::where('seller_id', $userId)->distinct()->pluck('estimate_series');
        // $distinctSellerIds = Estimates::where('seller_id', $userId)->distinct()->get();
        $distinctSellerIds = Estimates::where('seller_id', $userId)->distinct()->pluck('seller', 'seller_id');
        // dd($distinctSellerIds );
        $distinctBuyerIds = Estimates::where('seller_id', $userId)->distinct()->pluck('buyer', 'buyer_id');
        // $distinctStatuses = Status::distinct()->pluck('status');

        // Fetch the distinct "state" and "city" values from BuyerDetail table for buyers of this user
        $distinctStates = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('city');
        }
        // Filter by estimate_series
        if ($request->has('estimate_series')) {
            $query->where('estimate_series', $request->estimate_series);
        }

        // Filter by seller_id
        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }
        // Filter by buyer_id
        if ($request->has('buyer_id')) {
        // dd($request->buyer_id);
            $query->where('buyer_id', $request->buyer_id);
            // dd($query);
            // Fetch the distinct filter values for Estimates table (for this user)
            $distinctInvoiceSeries = Estimates::where('buyer_id', $userId)->distinct()->pluck('estimate_series');
            // $distinctSellerIds = Estimates::where('buyer_id', $userId)->distinct()->get();
            $distinctSellerIds = Estimates::where('buyer_id', $userId)->distinct()->pluck('seller', 'buyer_id');
            // dd($distinctSellerIds );
            $distinctBuyerIds = Estimates::where('buyer_id', $userId)->distinct()->pluck('buyer', 'buyer_id');
            // $distinctStatuses = Status::distinct()->pluck('status');
            // dd($distinctBuyerIds );

            // Fetch the distinct "state" and "city" values from BuyerDetail table for buyers of this user
            $distinctStates = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
                $query->select('id')->from('buyers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
                $query->select('id')->from('buyers')->where('user_id', $userId);
            })->distinct()->pluck('city');
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


        // Add any other desired filters

        // $invoices = $query->with(['buyerUser', 'statuses', 'buyerDetails','sfp'])->paginate(200);
        $invoices = $query->with(['buyerUser', 'statuses', 'buyerDetails','sfp'])->select('invoices.*')->get();
        // dd($invoices);
        // return response()->json($invoices, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $invoices,
            'status_code' => 200,
            'filters' => [
                'estimate_series' => $distinctInvoiceSeries,
                'seller_id' => $distinctSellerIds,
                'buyer_id' => $distinctBuyerIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                // Add any other filter values here if needed
            ]
        ], 200);
    }

    public function sidebarCounts(Request $request)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $query = Estimates::query();

        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        } else {
            $query->where('seller_id', $userId);
        }

        if ($request->has('buyer_id')) {
            $query->where('buyer_id', $request->buyer_id);
        }

        $count = $query->count();

        return response()->json([
            'message' => 'Success',
            'count' => $count,
            'status_code' => 200
        ], 200);
    }
    public function indexDetail(Request $request)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $query = Estimates::query()->orderByDesc('id')->where('seller_id', $userId);
        $sentInvoiceDetails = Estimates::rightJoin('invoice_order_details', 'invoices.id', '=', 'invoice_order_details.estimate_id')
            ->rightJoin('invoice_order_columns', 'invoice_order_details.id', '=', 'invoice_order_columns.estimate_order_detail_id')
            ->select('*')
            ->get();
        // dd($sentInvoiceDetails);
        return response()->json([
            'message' => 'Success',
            'data' => $sentInvoiceDetails,
            'status_code' => 200,
        ], 200);;
    }

    public function deletedInvoice(Request $request)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $query = Estimates::query()->orderByDesc('id')->where('seller_id', $userId);
        $sentInvoiceDetails = Estimates::rightJoin('invoice_order_details', 'invoices.id', '=', 'invoice_order_details.estimate_id')
            ->rightJoin('invoice_order_columns', 'invoice_order_details.id', '=', 'invoice_order_columns.estimate_order_detail_id')
            ->onlyTrashed()
            ->select('*')
            ->get();
        return response()->json([
            'message' => 'Success',
            'data' => $sentInvoiceDetails,
            'status_code' => 200,
        ], 200);;
    }

    public function show(Request $request, $id)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Fetch the estimate by ID (for this user)
        $estimate = Estimates::where('seller_id', $userId)->find($id);

        // Load related data
        $estimate->load(['orderDetails.columns', 'statuses', 'buyerDetails', 'buyerUser', 'sellerUser']);

        if (!$estimate) {
            return response()->json([
                'data' => null,
                'message' => 'Estimates not found',
                'status_code' => 200,
            ], 200);
        }

        // Return the response
        return response()->json([
            'message' => 'Success',
            'data' => $estimate,
            'status_code' => 200,
        ], 200);
    }
    public function accept(Request $request, $invoiceId)
    {
        try {
            // Find the Estimates by ID
            $estimate = Estimates::findOrFail($invoiceId);
            // Update the status of the Estimates to "accepted"
            $estimate->statuses()->create([
                'estimate_id' => $estimate->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'accept',
                'comment' => 'Estimates accepted',
            ]);

            if($request->status_comment && trim($request->status_comment) != ''){
                // Get the existing status_comment data
                $statusComment = json_decode($estimate->status_comment, true);

                // Add the new comment to the status_comment data
                $statusComment[] = [
                    'comment' => $request->status_comment,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                ];

                // Update the status_comment field with the combined data
                $estimate->update(['status_comment' => json_encode($statusComment)]);
            }
            // $invoiceSfp = new EstimateSfps(
            //     [
            //         'estimate_id' => $invoiceId,
            //         'sfp_by_id' => Auth::user()->id,
            //         'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
            //         'sfp_to_id' => null,
            //         'sfp_to_name' => null,
            //         'status' => 'accept',
            //         'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
            //     ]
            // );
            // $invoiceSfp = $invoiceSfp->save();
            // Return a response indicating success
            return response()->json([
                'data' => $estimate->statuses,
                'message' => 'Estimates accepted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Estimates Not Found.',
                'status_code' => 400
            ], 400);
        }
    }
    public function selfAccept(Request $request, $invoiceId)
    {
        try {
            // Find the Estimates by ID
            $estimate = Estimates::findOrFail($invoiceId);

            // Update the status of the Estimates to "self-accepted"
            $estimate->statuses()->create([
                'estimate_id' => $estimate->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                'status' => 'accepted',
                'comment' => 'Estimates self accepted',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $estimate->statuses,
                'message' => 'Estimates self accepted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Estimates Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function reject(Request $request, $invoiceId)
    {
        try {
            // Find the Estimates by ID
            $estimate = Estimates::findOrFail($invoiceId);
            // Update the status of the Estimates to "rejected"
            $estimate->statuses()->create([
                'estimate_id' => $estimate->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'reject',
                'comment' => 'Estimates rejected',
            ]);
            // If the estimate has order details and item_code is present

            if ($estimate->orderDetails) {
                foreach ($estimate->orderDetails as $orderDetail) {
                    if (isset($orderDetail->item_code)) {
                        // Find the product with the item_code
                        $product = Product::where('item_code', $orderDetail->item_code)->first();

                        // If the product exists, add back the quantity
                        if ($product) {
                            $product->qty += $orderDetail->qty;
                            $product->save();
                        }
                    }
                }
            }
            if($request->status_comment && trim($request->status_comment) != ''){
                // Get the existing status_comment data
                $statusComment = json_decode($estimate->status_comment, true);

                // Add the new comment to the status_comment data
                $statusComment[] = [
                    'comment' => $request->status_comment,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                ];

                // Update the status_comment field with the combined data
                $estimate->update(['status_comment' => json_encode($statusComment)]);
            }

            // $invoiceSfp = new EstimateSfps(
            //     [
            //         'estimate_id' => $invoiceId,
            //         'sfp_by_id' => Auth::user()->id,
            //         'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
            //         'sfp_to_id' => null,
            //         'sfp_to_name' => null,
            //         'status' => 'reject',
            //         'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
            //     ]
            // );

            // Return a response indicating success
            return response()->json([
                'data' => $estimate->statuses,
                'message' => 'Estimates rejected successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Estimates Not Found.',
                'status_code' => 400
            ], 400);
        }
    }
    public function selfReject(Request $request, $invoiceId)
    {
        try {
            // Find the Estimates by ID
            $estimate = Estimates::findOrFail($invoiceId);

            // Update the status of the Estimates to "self_reject"
            $estimate->statuses()->create([
                'estimate_id' => $estimate->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'reject',
                'comment' => 'Estimates self rejected',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $estimate->statuses,
                'message' => 'Estimates self rejected successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Estimates Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function delete(Request $request, $invoiceId)
    {
        try {
            // Find the Estimates by ID
            $estimate = Estimates::findOrFail($invoiceId);

            // Update the status of the Estimates to "deleted"
            $estimate->statuses()->create([
                'estimate_id' => $estimate->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'deleted',
                'comment' => 'Estimates deleted',
            ]);

            $estimate->delete();

            // Return a response indicating success
            return response()->json([
                'data' => $estimate->statuses,
                'message' => 'Estimates deleted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Estimates Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function forceDelete(Request $request, $invoiceId)
    {
        try {
            // Find the Estimates by ID
            $estimate = Estimates::findOrFail($invoiceId);

            $estimate->forceDelete();

            // Return a response indicating success
            return response()->json([
                'message' => 'Estimates permanently deleted.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Estimates Not Found.',
                'status_code' => 400
            ], 400);
        }
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
    public function exportInvoice(Request $request)
    {
        // Fetch the products and their related product details
        // $products = Challan::with('details')->get();
        $query = Estimates::query()->orderByDesc('id');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // $challans = $query->with(['buyerUser', 'statuses', 'buyerDetails','orderDetails', 'sfp'])->select('challans.*')->paginate(100,null,null,$request->page??1);
        // $challans = $query->where('seller_id', $userId)->with('buyerUser', 'statuses', 'buyerDetails','orderDetails','orderDetails.columns')->select('challans.*')->paginate(100,null,null,$request->page??1);
        // $invoices = $query->where('seller_id', $userId)->with(['buyerUser', 'statuses', 'buyerDetails', 'buyerDetails.details', 'sfp', 'orderDetails', 'orderDetails.columns',])->select('return_challans.*')->paginate(100,null,null,$request->page??1);
        $invoices = $query->where('seller_id', $userId)->with(['buyerUser', 'statuses', 'buyerDetails', 'orderDetails','orderDetails.columns', 'sfp'])->select('invoices.*')->paginate(50,null,null,$request->page??1);
        // dd($invoices);


        // Create an array to store the exported data
        $exportedData = [];

        // Iterate through the products and their related product details
        foreach ($invoices as $key => $challan) {
            $rowData['id'] =  ++$key;
            // dd($challan);
            // foreach ($challan as $productDetail) {
            //     $rowData[$productDetail->column_name] = $productDetail->column_value;
            // }
            // $rowData['Time'] = $challan->(date('h:i A', strtotime($challan->created_at)));


            $rowData['estimate_series'] = $challan->estimate_series;
            $rowData['Time'] = Carbon::parse($challan->created_at)->format('h:i A');
            $rowData['Date'] = Carbon::parse($challan->created_at)->format('j F Y');
            // $rowData['Date'] = $challan->(date('j F Y', strtotime($challan->created_at)));
            $rowData['seller'] = $challan->seller;
            $rowData['buyer'] = $challan->buyer;
            // $rowData['item_code'] = $challan->item_code;
            // $rowData['unit'] = $challan->unit;
            // $rowData['rate'] = $challan->rate;
            // $rowData['qty'] = $challan->total_qty;
            $rowData['total_amount'] = $challan->total;
                // dd($challan->buyerDetails);

            // $rowData['status'] = $challan->statuses[0]->status;
            $rowData['status'] = '';

        if ($challan->statuses->isNotEmpty()) {
            $status = $challan->statuses[0]->status;
            $user_name = $challan->statuses[0]->user_name;

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

            $rowData['comment'] = $challan->comment;


            $exportedData[] = $rowData;
        }

        // Create a temporary file path for the CSV
        $filePath = 'temp/' . uniqid() . '.csv';

        // Store the CSV file using Laravel Storage
        Storage::disk('local')->put($filePath, $this->generateCsvFile($exportedData));

        // Define the file name and content type
        $fileName = 'exported_challans.csv';
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

    public function exportDetailedInvoice(Request $request)
    {
        // Fetch the products and their related product details
        // $products = Challan::with('details')->get();
        $query = Estimates::query()->orderByDesc('id');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // $challans = $query->with(['buyerUser', 'statuses', 'buyerDetails','orderDetails', 'sfp'])->select('challans.*')->paginate(100,null,null,$request->page??1);
        // $challans = $query->where('seller_id', $userId)->with('buyerUser', 'statuses', 'buyerDetails','orderDetails','orderDetails.columns')->select('challans.*')->paginate(100,null,null,$request->page??1);
        // $invoices = $query->where('seller_id', $userId)->with(['buyerUser', 'statuses', 'buyerDetails', 'buyerDetails.details', 'sfp', 'orderDetails', 'orderDetails.columns',])->select('return_challans.*')->paginate(100,null,null,$request->page??1);
        $invoices = $query->where('seller_id', $userId)->with(['buyerUser', 'statuses', 'buyerDetails', 'orderDetails','orderDetails.columns', 'sfp'])->select('invoices.*')->paginate(50,null,null,$request->page??1);
        // dd($invoices);

        // Create an array to store the exported data
        $exportedData = [];

        // Iterate through the products and their related product details
        foreach ($invoices as $key => $challan) {
            $rowData['id'] =  ++$key;
            // dd($challan);
            // foreach ($challan->orderDetails as $productDetail) {
            //     // $rowData[$productDetail->column_name] = $productDetail->column_value;
            //     dd($productDetail->unit);
            // }
            // $rowData['Time'] = $challan->(date('h:i A', strtotime($challan->created_at)));


            $rowData['estimate_series'] = $challan->estimate_series;
            $rowData['Time'] = Carbon::parse($challan->created_at)->format('h:i A');
            $rowData['Date'] = Carbon::parse($challan->created_at)->format('j F Y');
            // $rowData['Date'] = $challan->(date('j F Y', strtotime($challan->created_at)));
            $rowData['seller'] = $challan->seller;
            $rowData['buyer'] = $challan->buyer;


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
        $fileName = 'exported_challans.csv';
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

    public function exportColumns(Request $request, $option)
    {
        $filename = 'bulk_invoice.csv';
        if($option == 1){

        // Get the column headers from the panelColumnDisplayNames array
        $columnHeaders = [
            'different invoices',
            'receiver_special_id',
            'estimate_date',
            'address',
            'unit',
            'rate',
            'qty',
        ];

        $columnFilterDataset = [
            'feature_id' => 1,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ];

        $request->merge($columnFilterDataset);
        $panelColumnsController = new PanelColumnsController;
        $columnsResponse = $panelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $columnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);




        // Merge custom column names with the default column headers
        $columnHeaders = array_merge($columnHeaders, $columnDisplayNames);
        }
            else {
            $columnHeaders = [
                'different invoices',
                'receiver_special_id',
                'address',
                'item_code',
                'estimate_date',
            ];
        }

        // Create the CSV content as a string
        $csvContent = implode(',', $columnHeaders) . PHP_EOL; // Add a newline after headers

        // Sample data (you can replace this with your actual data)
        $data = [
            ['1', '3eYuwIZcI1', '16-09-2023', 'Address', 'Unit 1', '10.25', '5', '0', 'Value 1'],
        ];
        // Add data rows to the CSV content
        foreach ($data as $row) {
            $csvContent .= implode(',', $row) . PHP_EOL; // Add a newline after each row
        }

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
    // public function bulkInvoiceImport(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'file' => 'required|file|mimes:csv,txt',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'errors' => $validator->errors(),
    //             'status_code' => 422,
    //         ], 422);
    //     }

    //     $file = $request->file; // Get the file from the request
    //     // dd($file);
    //     // Read the CSV file and process its contents
    //     $handle = fopen($file->getRealPath(), "r");

    //     if ($handle) {
    //         DB::beginTransaction();

    //         try {

    //             // Read the header row to get the column names
    //             $header = fgetcsv($handle, 1000, ",");
    //             $dataGroupedByInvoice = [];
    //             $invoiceIds = [];
    //             $challanReceiverMap = [];

    //             while (($data = fgetcsv($handle, 1000, ",")) !== false) {
    //                 // Create an associative array using the header row as keys
    //                 $rowData = array_combine($header, $data);
    //                 $differentChallan = $rowData['different challans'];
    //                 $receiverSpecialId = $rowData['receiver_special_id'];

    //                 // Check if estimate_series has changed
    //                 if ($rowData['buyer_special_id'] !== $invoiceData['buyer_special_id']) {
    //                     $importTotalQty = 0; // Initialize the total quantity for the entire import
    //                     $importTotalAmount = 0; // Initialize the total amount for the entire import

    //                     $buyer = User::where('special_id', $rowData['buyer_special_id'])
    //                     ->join('buyers', 'buyers.buyer_user_id', 'users.id')
    //                     ->leftJoin('panel_series_numbers', function ($join) {
    //                         $join->on('panel_series_numbers.assigned_to_id', '=', 'buyers.id')
    //                             ->orWhere(function ($query) {
    //                                 $query->on('panel_series_numbers.user_id', '=', 'buyers.id')
    //                                     ->where('panel_series_numbers.section_id', '=', '2');
    //                             });
    //                     })
    //                     ->join('buyer_detail', 'buyer_detail.buyer_id', '=', 'buyers.id')
    //                     ->where('buyers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //                     ->select('users.*', 'buyers.*', 'panel_series_numbers.*', 'buyer_detail.id as buyer_detail_id')
    //                     ->first();



    //                     // dd($buyer);
    //                     if($buyer->assigned_to_id == null)
    //                     {
    //                     $series_number = PanelSeriesNumber::where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //                     ->where('default', "1")
    //                     ->where('panel_id', '3')
    //                     ->first();
    //                     $series_number_value = $series_number ? $series_number->series_number : null;

    //                     }
    //                     // dd($series_number);
    //                     // Create a new challan when buyer_special_id changes
    //                     $latestSeriesNum = Estimates::where('estimate_series', $series_number->series_number)
    //                     ->where('seller_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //                     ->max(DB::raw('CAST(series_num AS UNSIGNED)'));
    //                     // Increment the latestSeriesNum for the new estimate
    //                     // dd($latestSeriesNum);
    //                     $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

    //                     $userDetail = UserDetails::where('user_id', $buyer->buyer_user_id)
    //                     ->where('location_name', $rowData['address'])
    //                     ->first();
    //                     // dd($userDetail->id);

    //                     $invoiceData = [
    //                         'estimate_series' => $buyer->assigned_to_id ? $buyer->series_number : $series_number_value,
    //                         'estimate_date' => (new \DateTime($rowData['estimate_date']))->format('Ymd'),
    //                         'buyer_id' => $buyer->buyer_user_id,
    //                         'buyer_detail_id' => $buyer->buyer_detail_id,
    //                         'user_detail_id' => $userDetail->id ?? null,
    //                         'buyer_special_id' => $rowData['buyer_special_id'],
    //                         'buyer' => $buyer->buyer_name,
    //                         'comment' => $rowData['comment'] ?? null,
    //                         'series_num' => $seriesNum,
    //                         'seller_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
    //                         'seller' => Auth::user()->name,
    //                         'total' => 0, // Initialize total to 0
    //                         'total_qty' => 0, // Initialize total quantity to 0
    //                     ];
    //                     // dd($invoiceData);
    //                     $estimate = new Estimates($invoiceData);
    //                     $estimate->save();
    //                     $invoiceIds[] = $estimate->id;
    //                     $status = new EstimateStatus([
    //                         'estimate_id' => $estimate->id,
    //                         'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
    //                         'user_name' => Auth::user()->name ?? Auth::user()->team_user_name,
    //                         'status' => 'draft',
    //                         'comment' => 'Challan created successfully',
    //                     ]);
    //                     $status->save();
    //                 }

    //                 if (array_key_exists('item_code', array_flip($header))) {
    //                     // dd('inside header', $rowData);


    //                     $user_id =  Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    //                     $product = Product::where('user_id', $user_id)->where('item_code', $rowData['item_code'])->with('details')->get();


    //                     $totalAmount =  floatval($product[0]->rate) * floatval($rowData['order_qty']);
    //                     $taxAmount = 0;

    //                     if (!empty($rowData['order_tax'])) {

    //                     }
    //                     $taxAmount = $totalAmount * (floatval($rowData['order_tax']) / 100);




    //                     $orderDetail = new EstimateOrderDetail([
    //                         'estimate_id' => $estimate->id,
    //                         'unit' => $product[0]->unit,
    //                         'rate' => $product[0]->rate,
    //                         'qty' => $rowData['order_qty'],
    //                         'tax' => $rowData['order_tax'] ?? null,
    //                         'total_amount' => $totalAmount + $taxAmount,
    //                     ]);
    //                     // dd($orderDetail);
    //                     $orderDetail->save();
    //                     // Update the total_qty and total_amount for the entire import
    //                     $importTotalQty += $rowData['order_qty'];
    //                     $importTotalAmount += $orderDetail->total_amount;


    //                     // Update the total_qty and total for the challan
    //                     $estimate->total_qty = $orderDetail->total_qty + $importTotalQty;
    //                     $estimate->total = $importTotalAmount;
    //                     $estimate->save();

    //                     if (isset($rowData['item_code'])) {
    //                         // Find the product based on item_code
    //                         // Product::where('item_code', $rowData['item_code'])->get();
    //                         $productUpdate = Product::where('item_code', $rowData['item_code'])->first();

    //                         if ($productUpdate) {
    //                             // Update the quantity
    //                             $newQty = max(0, $productUpdate->qty - $rowData['order_qty']);

    //                             // Save the updated quantity back to the database
    //                             $productUpdate->update(['qty' => $newQty]);
    //                             ProductLog::create([
    //                                 'product_id' => $productUpdate->id,
    //                                 'qty_out' => $rowData['order_qty'],
    //                                 'out_method' => 'estimate',
    //                                 'out_at' => now()
    //                             ]);

    //                         }

    //                     }
    //                      // Handle custom columns dynamically
    //                      foreach ($product as $item) {
    //                         // dd($item);
    //                         $details = $item->details;
    //                     foreach ($details as $columnValue) {

    //                     $orderColumn = new EstimateOrderColumns([
    //                         'estimate_order_detail_id' => $orderDetail->id,
    //                         'column_name' => $columnValue->column_name ?? '',
    //                         'column_value' => $columnValue->column_value ?? '',
    //                     ]);
    //                     $orderColumn->save();
    //                        }

    //                 }

    //                 } else{


    //                 $totalAmount = floatval($rowData['order_rate']) * floatval($rowData['order_qty']);
    //                 $taxAmount = 0;

    //                 if (!empty($rowData['order_tax'])) {
    //                 }
    //                 $taxAmount = $totalAmount * (floatval($rowData['order_tax']) / 100);

    //                 // Process the custom columns dynamically
    //                 $orderDetail = new EstimateOrderDetail([
    //                     'estimate_id' => $estimate->id,
    //                     'unit' => $rowData['order_unit'],
    //                     'rate' => $rowData['order_rate'],
    //                     'qty' => $rowData['order_qty'],
    //                     'tax' => $rowData['order_tax']?? null,
    //                     'total_amount' => $totalAmount + $taxAmount,
    //                 ]);
    //                 $orderDetail->save();

    //                 // Update the total_qty and total_amount for the entire import
    //                 $importTotalQty += floatval($rowData['order_qty']);
    //                 $importTotalAmount += floatval($orderDetail->total_amount);


    //                 // Update the total_qty and total for the challan
    //                 $estimate->total_qty = floatval($estimate->total_qty) + floatval($importTotalQty);
    //                 $estimate->total = floatval($estimate->total) + floatval($importTotalAmount);
    //                 $estimate->save();
    //                 // dd($rowData);
    //                 // Handle custom columns dynamically
    //                 foreach ($rowData as $columnName => $columnValue) {
    //                     if (
    //                         $columnName !== 'estimate_series' && $columnName !== 'estimate_date'
    //                         && $columnName !== 'buyer_special_id' && $columnName !== 'comment'
    //                         && $columnName !== 'order_unit' && $columnName !== 'order_rate'
    //                         && $columnName !== 'order_qty' && $columnName !== 'order_total_amount'
    //                         && $columnName !== 'order_tax'  // Exclude 'tax' from being stored as a custom column
    //                         && $columnName !== 'address'
    //                     ) {
    //                         // Create a new EstimateOrderColumns record for each custom column
    //                         $orderColumn = new EstimateOrderColumns([
    //                             'estimate_order_detail_id' => $orderDetail->id,
    //                             'column_name' => $columnName,
    //                             'column_value' => $columnValue,
    //                         ]);
    //                         $orderColumn->save();
    //                     }
    //                 }
    //             }
    //             }

    //             // Update the total_qty and total for the challan
    //             // $estimate->total_qty = $importTotalQty;
    //             // $estimate->total = $importTotalAmount;
    //             // $estimate->save();
    //             // Create Challan Statuses
    //             DB::commit();
    //             fclose($handle);

    //             return response()->json([
    //                 'data' => [
    //                     'invoice_ids' => $invoiceIds,
    //                     // Other data you may want to include
    //                 ],
    //                 'message' => 'Bulk Estimates created successful.',
    //                 'status_code' => 200,
    //             ], 200);
    //         } catch (\Exception $e) {
    //             DB::rollback();
    //             fclose($handle);
    //             return response()->json([
    //                 'error' => 'Error occurred while creating invoices: ' . $e->getMessage(),
    //                 'status_code' => 500,
    //             ], 500);
    //         }
    //     } else {
    //         return response()->json([
    //             'error' => 'Unable to open the CSV file.',
    //             'status_code' => 500,
    //         ], 500);
    //     }
    // }
    public function bulkInvoiceImport(Request $request)
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
        $fileName = $file->getClientOriginalName();
        $fileType = $file->getClientOriginalExtension();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Store the file in the s3
        $fileUrl = Storage::disk('s3')->putFileAs('bulk_imports', $file, $fileName);


        // Create a new BulkImportLog entry
        $bulkImportLog = new BulkImportLog([
            'user_id' => $userId,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'type' => 'estimate',
            'file_path' => $fileUrl, // Store the S3 file URL
            'status' => 'processing',
        ]);

        $handle = fopen($file->getRealPath(), "r");

        if (!$handle) {
            return response()->json([
                'error' => 'Unable to open the CSV file.',
                'status_code' => 500,
            ], 500);
        }

        DB::beginTransaction();

        try {
            $header = fgetcsv($handle, 1000, ",");
            $dataGroupedByInvoice = [];
            $invoiceIds = [];
            $challanReceiverMap = [];

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $data = array_pad($data, count($header), null);

                if (empty(trim($data[0]))) {
                    return response()->json([
                        'message' => "The 'different invoices' field cannot be empty.",
                        'status_code' => 422,
                    ], 422);
                }

                if (!is_numeric($data[6]) || !is_numeric($data[7])) {
                    return response()->json([
                        'message' => "The 'rate' and 'qty' fields must be numeric.",
                        'status_code' => 422,
                    ], 422);
                }

                if (empty(array_filter($data))) {
                    continue;
                }

                if (count($header) !== count($data)) {
                    return response()->json([
                        'message' => "The number of header columns and data columns do not match.",
                        'status_code' => 422,
                    ], 422);
                }

                $rowData = array_combine($header, $data);
                $differentChallan = $rowData['different invoices'];
                $receiverSpecialId = $rowData['buyer_special_id'];

                if (isset($challanReceiverMap[$differentChallan]) && $challanReceiverMap[$differentChallan] !== $receiverSpecialId) {
                    return response()->json([
                        'message' => "Estimates number '{$differentChallan}' cannot be used for different Buyer '{$receiverSpecialId}'.",
                        'status_code' => 400,
                    ], 400);
                }

                $challanReceiverMap[$differentChallan] = $receiverSpecialId;

                if (!isset($dataGroupedByInvoice[$differentChallan])) {
                    $dataGroupedByInvoice[$differentChallan] = [];
                }

                $dataGroupedByInvoice[$differentChallan][] = $rowData;
            }

            foreach ($dataGroupedByInvoice as $differentChallan => $rows) {
                $firstRow = $rows[0];

                $buyer = User::where('special_id', $rowData['buyer_special_id'])
                        ->join('buyers', 'buyers.buyer_user_id', 'users.id')
                        ->leftJoin('panel_series_numbers', function ($join) {
                            $join->on('panel_series_numbers.assigned_to_id', '=', 'buyers.id')
                                ->orWhere(function ($query) {
                                    $query->on('panel_series_numbers.user_id', '=', 'buyers.id')
                                        ->where('panel_series_numbers.section_id', '=', '2');
                                });
                        })
                        ->join('buyer_detail', 'buyer_detail.buyer_id', '=', 'buyers.id')
                        ->where('buyers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                        ->select('users.*', 'buyers.*', 'panel_series_numbers.*', 'buyer_detail.id as buyer_detail_id')
                        ->first();

                if ($buyer === null) {
                    return response()->json([
                        'message' => 'Please assign the buyer first.',
                        'status_code' => 400,
                    ], 400);
                }

                if ($buyer->assigned_to_id == null) {
                    $series_number = PanelSeriesNumber::where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                        ->where('default', "1")
                        ->where('panel_id', '3')
                        ->first();
                    $series_number_value = $series_number ? $series_number->series_number : null;
                }

                $latestSeriesNum = Estimates::where('estimate_series', $series_number->series_number)
                    ->where('seller_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));
                $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

                $userDetail = UserDetails::where('user_id', $buyer->buyer_user_id)
                    ->where('location_name', $firstRow['address'])
                    ->first();

                try {
                    $invoiceDate = Carbon::parse($firstRow['estimate_date']);

                    $invoiceData = [
                        'estimate_series' => $buyer->assigned_to_id ? $buyer->series_number : $series_number_value,
                        'estimate_date' => $invoiceDate->format('Ymd'),
                        'buyer_id' => $buyer->buyer_user_id,
                        'buyer_detail_id' => $buyer->buyer_detail_id,
                        'user_detail_id' => $userDetail->id ?? null,
                        'buyer_special_id' => $rowData['buyer_special_id'],
                        'buyer' => $buyer->buyer_name,
                        'comment' => $rowData['comment'] ?? null,
                        'series_num' => $seriesNum,
                        'seller_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                        'seller' => Auth::user()->name,
                        'total' => 0,
                        'total_qty' => 0,
                    ];

                    $estimate = new Estimates($invoiceData);
                    $estimate->save();
                    $invoiceIds[] = $estimate->id;

                    $status = new EstimateStatus([
                        'estimate_id' => $estimate->id,
                        'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                        'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                        'team_user_name' => Auth::user()->name ?? Auth::user()->team_user_name ?? null,
                        'status' => 'draft',
                        'comment' => 'Estimates created successfully',
                    ]);
                    $status->save();

                    $importTotalQty = 0;
                    $importTotalAmount = 0;

                    foreach ($rows as $rowData) {
                        if (array_key_exists('item_code', array_flip($header))) {
                            $product = Product::where('item_code', $rowData['item_code'])->with('details')->first();

                            $totalAmount =  floatval($product[0]->rate) * floatval($rowData['order_qty']);
                            $taxAmount = 0;

                            if (!empty($rowData['order_tax'])) {
                                $taxAmount = $totalAmount * (floatval($rowData['order_tax']) / 100);
                            }
                            $orderDetail = new EstimateOrderDetail([
                                'estimate_id' => $estimate->id,
                                'unit' => $product[0]->unit,
                                'rate' => $product[0]->rate,
                                'qty' => $rowData['order_qty'],
                                'tax' => $rowData['order_tax'] ?? null,
                                'total_amount' => $totalAmount + $taxAmount,
                            ]);
                            $orderDetail->save();

                            $importTotalQty += $rowData['order'];
                            $importTotalAmount += $orderDetail->total_amount;

                            $estimate->total_qty += $orderDetail->qty;
                            $estimate->total += $orderDetail->total_amount;
                            $estimate->save();

                            if (isset($rowData['item_code'])) {
                                $productUpdate = Product::where('item_code', $rowData['item_code'])->first();
                                if ($productUpdate) {
                                    $newQty = max(0, $productUpdate->qty - $rowData['order']);
                                    $productUpdate->update(['qty' => $newQty]);
                                }
                            }
                        } else {
                            $totalAmount =  floatval($rowData['rate']) * floatval($rowData['qty']);
                            $taxAmount = 0;

                            if (!empty($rowData['tax'])) {
                                $taxAmount = $totalAmount * (floatval($rowData['tax']) / 100);
                            }
                            $orderDetail = new EstimateOrderDetail([
                                'estimate_id' => $estimate->id,
                                'unit' => $rowData['unit'],
                                'rate' => $rowData['rate'],
                                'qty' => $rowData['qty'],
                                'tax' => $rowData['tax']?? null,
                                'total_amount' => $totalAmount + $taxAmount,
                            ]);
                            $orderDetail->save();

                            $importTotalQty += floatval($rowData['qty']);
                            $importTotalAmount += floatval($orderDetail->total_amount);

                            $estimate->total_qty += floatval($orderDetail->qty);
                            $estimate->total += floatval($orderDetail->total_amount);
                            $estimate->save();

                            foreach ($rowData as $columnName => $columnValue) {
                                if (
                                    $columnName !== 'estimate_series' && $columnName !== 'estimate_date'
                                    && $columnName !== 'buyer_special_id' && $columnName !== 'comment'
                                    && $columnName !== 'different invoices' && $columnName !== 'different invoices'
                                    && $columnName !== 'unit' && $columnName !== 'rate'
                                    && $columnName !== 'qty' && $columnName !== 'total_amount'
                                    && $columnName !== 'tax'
                                    && $columnName !== 'address'
                                ) {
                                    $orderColumn = new EstimateOrderColumns([
                                        'estimate_order_detail_id' => $orderDetail->id,
                                        'column_name' => $columnName,
                                        'column_value' => $columnValue,
                                    ]);
                                    $orderColumn->save();
                                }
                            }
                        }
                    }

                    try {
                        $pdfGenerator = new PDFGeneratorService();
                        $response = $pdfGenerator->generateEstimatePDF($estimate);

                        $responseArray = $response->original;

                        if (is_array($responseArray) && isset($responseArray['status_code']) && $responseArray['status_code'] === 200) {
                            $estimate->pdf_url = $responseArray['pdf_url'];
                            $estimate->save();
                        } else {
                            \Log::error('Error generating PDF for estimate ' . $estimate->id . ': ' . json_encode($responseArray));
                        }
                    } catch (\Exception $pdfError) {
                        \Log::error('Exception while generating PDF for estimate ' . $estimate->id . ': ' . $pdfError->getMessage());
                    }

                } catch (InvalidFormatException $e) {
                    throw new \Exception("Invalid date format for estimate_date: {$firstRow['estimate_date']}");
                }
            }

            DB::commit();
            fclose($handle);

             // Update the FileUploadLog status to 'completed'
            $bulkImportLog->update(['status' => 'completed']);

            return response()->json([
                'data' => [
                    'invoice_ids' => $invoiceIds,
                ],
                'message' => 'Bulk estimate created successfully.',
                'status_code' => 200,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            fclose($handle);
            // Update the FileUploadLog status to 'failed' with error message
            $uploadLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Error occurred while creating invoices: ' . $e->getMessage(),
                'status_code' => 500,
            ], 500);
        }
    }

    public function addComment(Request $request, $invoiceIds)
    {
        try {
            $invoiceIds = is_array($invoiceIds) ? $invoiceIds : [$invoiceIds];
            $successCount = 0;
            $failedCount = 0;

            foreach ($invoiceIds as $invoiceId) {
                try {
                    $estimate = Estimates::with('buyerUser', 'sellerUser')->findOrFail($invoiceId);
                    // dd($estimate, $request);
                    if ($request->has('status_comment')) {
                        $statusComment = json_decode($estimate->status_comment, true) ?: [];
                        $statusComment[] = [
                            'comment' => $request->status_comment,
                            'date' => now()->format('Y-m-d'),
                            'time' => now()->format('H:i:s'),
                            'name' => Auth::user()->name ?? Auth::user()->team_user_name,
                        ];
                        $estimate->update(['status_comment' => json_encode($statusComment)]);
                    }

                    // if ($request->has('seller') || $request->has('buyer')) {
                    //     $notification = new Notification([
                    //         'user_id' => $request->has('seller') ? $estimate->sellerUser->id : $estimate->buyerUser->id,
                    //         'message' => 'New Comment added by ' . ($request->has('seller') ? $estimate->buyerUser->name : $estimate->sellerUser->name),
                    //         'type' => 'challan',
                    //         'added_id' => $estimate->id,
                    //         'panel' => $request->has('seller') ? 'buyer' : 'seller',
                    //         'template_name' => $request->has('seller') ? 'all_invoice' : 'sent_invoice',
                    //     ]);
                    //     $notification->save();
                    // }

                    $successCount++;
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    $failedCount++;
                    \Log::error("Estimates not found: {$invoiceId}");
                }
            }

            $message = $successCount > 0 ? "Comment added successfully." : "";
            $message .= $failedCount > 0 ? " Failed to add comment." : "";
            // $message = $successCount > 0 ? "Comment added successfully to {$successCount} estimate(s)." : "";
            // $message .= $failedCount > 0 ? " Failed to add comment to {$failedCount} estimate(s)." : "";

            return response()->json([
                'message' => trim($message),
                'status_code' => $successCount > 0 ? 200 : 400
            ], $successCount > 0 ? 200 : 400);

        } catch (\Exception $e) {
            \Log::error('Error in addComment: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while processing the request.',
                'status_code' => 500
            ], 500);
        }
    }
}
