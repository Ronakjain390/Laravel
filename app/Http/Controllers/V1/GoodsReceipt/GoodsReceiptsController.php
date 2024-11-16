<?php

namespace App\Http\Controllers\V1\GoodsReceipt;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Models\GoodsReceipt;
use App\Models\Notification;
use App\Models\GoodsReceiptSfp;
use App\Models\GoodsReceiptOrderDetail;
use App\Models\GoodsReceiptOrderColumn;
use App\Models\GoodsReceiptStatus;
use App\Models\PlanFeatureUsageRecord;
use App\Models\FeatureTopupUsageRecord;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\PDFServices\PDFEmailService;
use App\Models\PlanAdditionalFeatureUsageRecord;
use App\Services\PDFServices\PDFWhatsAppService;
use App\Models\AdditionalFeatureTopupUsageRecord;
use App\Services\PDFServices\PDFGeneratorService;


use Illuminate\Http\Request;

class GoodsReceiptsController extends Controller
{
    public function store(Request $request)
    {
        // dd($request->all());
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'goods_series' => 'required|string',
            'series_num' => 'required',
            // 'goods_receipts_date' => 'required|date',
            // 'feature_id' => 'required|exists:features,id',
            'receiver_goods_receipts_id' => 'nullable',
            'receiver_goods_receipts' => 'nullable|string',
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

        $featureId = 122; // Replace with YOUR_FEATURE_ID
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
                    'goods_receipt_id' => null,
                    'order_detail_ids' => null,
                    'order_column_ids' => null,
                    'status_ids' => null,
                    'status_code' => 200
                ], 200);
                // Handle the case when both usage counts could not be updated successfully
                // Add appropriate error handling or log the issue for further investigation.
            }
        }

        // Get the latest series_num for the given goods_series and user_id
        $latestSeriesNum = GoodsReceipt::where('goods_series', $request->goods_series)
            ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->max('series_num');
        // Increment the latestSeriesNum for the new invoice
        $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

        // Compute receiver_goods_receipts value before creating the GoodsReceipt
        $receiverGoodsReceipts = empty($request['receiver_name'])
            ? (!empty($request['phone']) ? $request['phone'] : 'Others')
            : $request['receiver_name'];
        // Create a new GoodsReceipt
        $goodsReceipt = new GoodsReceipt([
            'goods_series' => $request->goods_series,
            'goods_receipts_date' => $request->goods_receipts_date,
            'series_num' => $request->series_num,
            'sender_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'sender' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
            'receiver_goods_receipts_id' => $request->receiver_goods_receipts_id ?? null,
            'receiver_goods_receipts_detail_id' => $request->receiver_goods_receipts_detail_id ?? null,
            'receiver_goods_receipts' => $receiverGoodsReceipts,
            'comment' => $request->comment,
            'calculate_tax' => $request->calculate_tax ?? null,
            'total' => isset($request->total) && $request->total != 0 ? (float) $request->total : null,
            'total_qty' => isset($request->total_qty) && $request->total_qty != 0 ? (float) $request->total_qty : null,
            'round_off' => $request->round_off ?? null,
            'team_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_id : null,
        ]);
            // dd($goodsReceipt);
        $goodsReceipt->save();

        // Create GoodsReceipt Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new GoodsReceiptOrderDetail([
                    'goods_receipt_id' => $goodsReceipt->id,
                    'item_code' => $orderDetailData['item_code']?? null,
                    'unit' => $orderDetailData['unit'],
                    'rate' => $orderDetailData['rate'] ?? null,
                    'qty' => $orderDetailData['qty'] ?? null,
                    'details' => $orderDetailData['details'] ?? '',
                    'tax' => $orderDetailData['tax'] ?? null,
                    'discount' => $orderDetailData['discount'] ?? 0.00,
                    'total_amount' => isset($orderDetailData['total_amount']) && $orderDetailData['total_amount'] != 0 ? $orderDetailData['total_amount'] : null,
                ]);
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new GoodsReceiptOrderColumn([
                            'goods_receipt_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ?? '',
                        ]);
                        $orderColumn->save();
                    }
                }
            }
        }

        // Create GoodsReceipt Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new GoodsReceiptStatus([
                    'goods_receipt_id' => $goodsReceipt->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    'team_user_name' => Auth::user()->team_user_name ?? null,
                    'status' => 'created',
                    'comment' => 'GoodsReceipt created successfully',
                ]);
                $status->save();
            }
        }
        // Get the IDs of the created records
        $goodsReceiptId = $goodsReceipt->id;
        $orderDetailIds = $goodsReceipt->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $goodsReceipt->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $goodsReceipt->statuses->pluck('id')->toArray();

        // $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount($featureId, 1);

        // if (!$PlanFeatureUsageRecordResponse) {
        //     // Update usage count for FeatureTopupUsageRecord
        //     $FeatureTopupUsageRecorddResponse = $FeatureTopupUsageRecord->updateUsageCount($featureId, 1);

        //     if (!$FeatureTopupUsageRecorddResponse) {
        //         return response()->json([
        //             'message' => 'Something Went Wrong.',
        //             'goods_receipt_id' => null,
        //             'order_detail_ids' => null,
        //             'order_column_ids' => null,
        //             'status_ids' => null,
        //             'status_code' => 400
        //         ], 400);
        //         // Handle the case when both usage counts could not be updated successfully
        //         // Add appropriate error handling or log the issue for further investigation.
        //     }
        // }
        $goodsReceipt = GoodsReceipt::where('id', $goodsReceiptId)->with('buyerUser', 'SenderUser', 'orderDetails', 'orderDetails.columns', 'orderDetails.columns', 'statuses')->first();

        // Generate the PDF for the GoodsReceipt using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateGoodsReceiptPDF($goodsReceipt);
        // dd($response);

        $response = (array) $response->getData();
        // Handle the response from PDFGenerator

        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $goodsReceipt->pdf_url = $response['pdf_url'];
            $goodsReceipt->save();
        }

        return response()->json([
            'message' => 'GoodsReceipt created successfully.',
            'goods_receipt_id' => $goodsReceiptId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200
        ], 200);
    }
    public function showPdf(Request $request)
    {
        $pdfUrl = $request->query('pdfUrl');
        // Decode the PDF URL if necessary and proceed with your logic
        $decodedUrl = urldecode($pdfUrl);
        return redirect($decodedUrl);
    }
    public function index(Request $request)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // dd($request->has('sender_id'), $request->has('receiver_goods_receipts_id'));
        // $query = GoodsReceipt::query()->orderByDesc('id')->where('sender_id', $userId);
        $query = GoodsReceipt::query()->orderByDesc('id');
        $combinedValues = [];
        if (!$request->has('sender_id') && !$request->has('receiver_goods_receipts_id')) {
            // Assuming you have a logged-in user, you can get the user ID like this:
            $query->where('sender_id', $userId);
        // Fetch the distinct filter values for Receipt Note table (for this user)
        $distinctInvoiceSeries = GoodsReceipt::where('sender_id', $userId)->distinct()->pluck('goods_series');
        $distinctInvoiceSeriesNum = GoodsReceipt::where('sender_id', $userId)->distinct()->pluck('series_num');
        // $distinctSellerIds = GoodsReceipt::where('sender_id', $userId)->distinct()->get();
            // dd($distinctInvoiceSeriesNum);
        //    // Loop through each element of $distinctChallanSeries
        foreach ($distinctInvoiceSeries as $series) {
            // Loop through each element of $distinctChallanSeriesNum
            foreach ($distinctInvoiceSeriesNum as $num) {
                // Combine the series and number and push it into the combinedValues array
                $combinedValues[] = $series . '-' . $num;
            }
        }



        $distinctSellerIds = GoodsReceipt::where('sender_id', $userId)->distinct()->pluck('seller', 'sender_id');
        // dd($distinctSellerIds );
        $distinctBuyerIds = GoodsReceipt::where('sender_id', $userId)->distinct()->pluck('buyer', 'receiver_goods_receipts_id');
        // $distinctStatuses = Status::distinct()->pluck('status');

        // Fetch the distinct "state" and "city" values from BuyerDetail table for buyers of this user
        $distinctStates = BuyerDetails::whereIn('receiver_goods_receipts_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = BuyerDetails::whereIn('receiver_goods_receipts_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('city');
        }
        // Filter by goods_series
        // if ($request->has('goods_series')) {
        //     $query->where('goods_series', $request->goods_series);
        // }

        if ($request->has('goods_series')) {
            $searchTerm = $request->goods_series;

            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                // Split the string into series and number
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                // Perform the search
                $query->where('goods_series', $series)
                      ->where('series_num', $num);
            } else {
                // Invalid search term format, handle accordingly
                // For example, you could return an error message or ignore the filter
            }
        }


        // Filter by sender_id
        if ($request->has('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

         // Filter by date
         if ($request->has('from') && $request->has('to')) {
            $from = $request->from;
            $to = $request->to;

            $query->whereBetween('goods_receipts_date', [$from, $to]);
        }

        // Filter by receiver_goods_receipts_id
        if ($request->has('receiver_goods_receipts_id')) {
        // dd($request->receiver_goods_receipts_id);
            $query->where('receiver_goods_receipts_id', $request->receiver_goods_receipts_id);
            // dd($query);
            // Fetch the distinct filter values for Receipt Note table (for this user)
            $distinctInvoiceSeries = GoodsReceipt::where('receiver_goods_receipts_id', $userId)->distinct()->pluck('goods_series');

            $distinctInvoiceSeriesNum = GoodsReceipt::where('receiver_goods_receipts_id', $userId)->distinct()->pluck('series_num');

            foreach ($distinctInvoiceSeries as $series) {
                // Loop through each element of $distinctChallanSeriesNum
                foreach ($distinctInvoiceSeriesNum as $num) {
                    // Combine the series and number and push it into the combinedValues array
                    $combinedValues[] = $series . '-' . $num;
                }
            }

            // $distinctSellerIds = GoodsReceipt::where('receiver_goods_receipts_id', $userId)->distinct()->get();
            $distinctSellerIds = GoodsReceipt::where('receiver_goods_receipts_id', $userId)->distinct()->pluck('seller', 'receiver_goods_receipts_id');
            // dd($distinctSellerIds );
            $distinctBuyerIds = GoodsReceipt::where('receiver_goods_receipts_id', $userId)->distinct()->pluck('buyer', 'receiver_goods_receipts_id');
            // $distinctStatuses = Status::distinct()->pluck('status');
            // dd($distinctBuyerIds );

            // Fetch the distinct "state" and "city" values from BuyerDetail table for buyers of this user
            $distinctStates = BuyerDetails::whereIn('receiver_goods_receipts_id', function ($query) use ($userId) {
                $query->select('id')->from('buyers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = BuyerDetails::whereIn('receiver_goods_receipts_id', function ($query) use ($userId) {
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
                'goods_series' => $distinctInvoiceSeries,
                'series_num' => $distinctInvoiceSeriesNum,
                'merged_invoice_series' => $combinedValues,
                'sender_id' => $distinctSellerIds,
                'receiver_goods_receipts_id' => $distinctBuyerIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                // Add any other filter values here if needed
            ]
        ], 200);
    }

    public function send(Request $request, $invoiceId)
    {
        // Find the Receipt Note by ID
        $invoice = GoodsReceipt::where('id', $invoiceId)->with('buyerUser', 'SenderUser', 'orderDetails',   'orderDetails.columns', 'statuses','buyerUser.details' )->first();
        // dd($invoice);
        $permissionsSender = $invoice->senderUser->permissions ? json_decode($invoice->senderUser->permissions, true) : null;

        // dd($permissionsSender['receipt_note']['whatsapp']);

        $status = new GoodsReceiptStatus([
            'goods_receipt_id' => $invoiceId,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
            'status' => 'sent',
            'comment' => 'Receipt Note sent for acceptance',
        ]);
        $status->save();

         // Generate the PDF for the Challan using PDFGenerator class
         $pdfGenerator = new PDFGeneratorService();
         $response = $pdfGenerator->generateGoodsReceiptPDF($invoice);
         // dd($response);

         $response = (array) $response->getData();
         // $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
         // $companyLogoData = CompanyLogo::where('user_id', $userId)->first();
         // dd($response);
         // Handle the response from PDFGenerator

         if ($response['status_code'] === 200) {
             // PDF generated successfully
             $invoice->pdf_url = $response['pdf_url'];
             $invoice->save();


             if (isset($invoice->buyerUser) && !empty($invoice->buyerUser->email)) {
                $pdfEmailService = new PDFEmailService();
                $recipientEmail = $invoice->buyerUser->email; // Replace with the actual recipient email address

                $pdfEmailService->sendReceiptNoteByEmail($goodsReceipt, $response['pdf_url'], $recipientEmail);
            }
         }

         // Check sender permissions
         if (isset($permissionsSender['receipt_note']['whatsapp']['sent_receipt_note']) && $permissionsSender['receipt_note']['whatsapp']['sent_receipt_note']) {
            $wallet = Wallet::where('user_id', $invoice->senderUser->id)->first();

         $deduction = 0.90 + (0.90 * 0.18);

         // Get the user's wallet
         $wallet = Wallet::where('user_id', Auth::id())->first();
         if ($wallet !== null && $wallet->balance >= $deduction) {
            // dd($wallet);
            $deduction = 0.90 + (0.90 * 0.18);
                    if ($wallet !== null && $wallet->balance >= $deduction) {
                        $wallet->balance -= $deduction;
                        $wallet->save();
                        // Log the deduction
                        WalletLog::create([
                            'user_id' => $wallet->user_id,
                            'amount_deducted' => $deduction,
                            'remaining_balance' => $wallet->balance,
                            'challan_id' => $invoiceId,
                            'action' => 'receipt_note_sent',
                            'recipient' => $invoice->buyerUser->receiver_name,

                        ]);
                        $sendWhatsApp = true;
                    }
             $pdfWhatsAppService = new PDFWhatsAppService;
             $phoneNumbers = [$invoice->buyerUser->details[0]->phone]; // Replace with the actual recipient phone number

             if (!empty($invoice->additional_phone_number)) {
                 $phoneNumbers[] = $invoice->additional_phone_number;
             }
             $receiverUserEmail = $invoice->senderUser ? $invoice->senderUser->email : null;
             $receiverUser = $invoice->receiver_goods_receipts;
             $senderUser = $invoice->sender;
             $invoiceNo = $invoice->goods_series . '-' . $invoice->series_num;
             $invoiceId = $invoice->id;
             $heading = 'Goods Receipt';
             $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendGrnOnWhatsApp($phoneNumbers, $response['pdf_url'], $invoiceNo, $invoiceId, $receiverUser, $senderUser, $heading);
             if($pdfWhatsAppServiceResponse == true){
                 // Deduct the cost from the wallet
                 $wallet->balance -= $deduction;
                 $wallet->save();
             }
         }
        }

        if($request->status_comment && trim($request->status_comment) != ''){
            // Get the existing status_comment data
            $statusComment = json_decode($invoice->status_comment, true);

            // Add the new comment to the status_comment data
            $statusComment[] = [
                'comment' => $request->status_comment,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
                'name' => Auth::user()->name ?? Auth::user()->team_user_name,
            ];

            // Update the status_comment field with the combined data
            $invoice->update(['status_comment' => json_encode($statusComment)]);
        }

        return response()->json([
            'message' => 'Receipt Note sent successfully.',
            'status_code' => 200
        ], 200);
    }

    public function addComment(Request $request, $receiptNoteId){
        $challan = GoodsReceipt::findOrFail($receiptNoteId);
        $challan->load('buyerUser', 'SenderUser');

        if ($request->has('status_comment')) {
            // Get the existing status_comment data
            $statusComment = json_decode($challan->status_comment, true) ?? [];

            // Add the new comment to the status_comment data
            $statusComment[] = [
                'comment' => $request->status_comment,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
                'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
            ];

            // Update the status_comment field with the combined data
            $challan->update(['status_comment' => json_encode($statusComment)]);
        }

        $receiverUserEmail = $challan->senderUser ? $challan->senderUser->email : null;
        $phone = null;
        if ($challan->buyerUser && !empty($challan->buyerUser->details)) {
            $phone = $challan->buyerUser->details[0]->phone ?? null;
        }

        $senderUser = $challan->sender;
        $challanNo = $challan->goods_series . '-' . $challan->series_num;
        $pdfUrl = $challan->pdf_url;
        $heading = 'Receipt Note';

        if ($phone) {
            $pdfWhatsAppService = new PDFWhatsAppService();
            $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendGrnCommentOnWhatsApp($phone, $senderUser, $challanNo, $request->status_comment, $pdfUrl, $heading);
        }

        // Return a response indicating success
        return response()->json([
            'data' => $challan->statuses,
            'message' => 'Comment added successfully.',
            'status_code' => 200
        ], 200);
    }

    public function addBulkComment(Request $request, array $receiptNoteIds)
    {
        // dd($request->all(), $receiptNoteIds);
        $responses = [];

        foreach ($receiptNoteIds as $receiptNoteId) {
            try {
                // Find the Challan by ID
                $challan = GoodsReceipt::findOrFail($receiptNoteId);
                $challan = $challan->load('buyerUser', 'senderUser');
                // dd($challan);
                // if($request->has('receiver')){
                //     $receiverUserEmail = $challan->senderUser ? $challan->senderUser->email : null;
                // }
                // elseif($request->has('sender')){
                //     $receiverUserEmail = $challan->receiverUser ? $challan->receiverUser->email : null;
                // }

                if($request->has('status_comment')){
                    // Get the existing status_comment data
                    $statusComment = json_decode($challan->status_comment, true);

                    // Add the new comment to the status_comment data
                    $statusComment[] = [
                        'comment' => $request->status_comment,
                        'date' => date('Y-m-d'),
                        'time' => date('H:i:s'),
                        'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    ];

                    // Update the status_comment field with the combined data
                    $challan->update(['status_comment' => json_encode($statusComment)]);
                    // Send the PDF via email for SFP Challan Alert
                    // if ($receiverUserEmail != null) {
                    //     $pdfEmailService = new PDFEmailService();
                    //     $pdfEmailService->addCommentSentChallanMail($challan, $receiverUserEmail, $request->status_comment);
                    // }
                }

                // Add a response indicating success for this ID
                $responses[] = [
                    'data' => $challan->statuses,
                    'message' => 'Comment added successfully for challan ID: ' . $receiptNoteId,
                    'status_code' => 200
                ];
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                // Add a response indicating failure for this ID
                $responses[] = [
                    'message' => 'Challan Not Found for ID: ' . $receiptNoteId,
                    'status_code' => 400
                ];
            }
        }

        return response()->json([
            'data' => $challan->statuses,
            'message' => 'Comment added successfully.',
            'status_code' => 200
        ], 200);
    }

    public function receiptNoteSfpCreate(Request $request)
    {
        // dd($request->all());
        $teamUsers = DB::table('team_users')->whereIn('id', $request->team_user_ids)->get();

        // Fetch admins
        $admins = DB::table('users')->whereIn('id', $request->admin_ids)->get();

        // Combine team users and admins into one collection
        $buyers = $teamUsers->concat($admins);

            if($buyers->isEmpty()){
                return response()->json([
                    'errors' => 'Team User not found.',
                    'status_code' => 400
                ], 400);
            }
            // Fetch Challan by ID
            $invoice = GoodsReceipt::findOrFail($request->receipt_note_id);
            $invoice->load('statuses', 'sfp');
            // dd($invoice);

            $subuser = $invoice->statuses[0]->team_user_name;
            foreach ($buyers as $buyer) {
            $invoiceSfp = new GoodsReceiptSfp(
                [
                    'goods_receipts_id' => $request->receipt_note_id,
                    'sfp_by_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
                    'sfp_to_id' => $buyer->id,
                    'sfp_to_name' => $buyer->team_user_name ?? $buyer->name,
                    'comment' => $request->comment,
                    'status' => 'sent',
                    'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
                ]
            );
            $invoiceSfp->save();
        }

        return response()->json([
            'message' => 'Receipt Note SFP successfully.',
            'status_code' => 200
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'goods_series' => 'string',
            'goods_receipts_date' => 'required',
            'receiver_goods_receipts_id' => 'nullable|exists:users,id',
            'receiver_goods_receipts' => 'nullable|string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            'order_details.*.unit' => 'nullable|string',
            'order_details.*.rate' => 'nullable|numeric|min:0',
            'order_details.*.qty' => 'numeric|min:0',
            'order_details.*.details' => 'nullable|string',
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

        // Find the GoodsReceipt by ID
        $goodsReceipt = GoodsReceipt::find($id);

        if (!$goodsReceipt) {
            return response()->json([
                'message' => 'GoodsReceipt not found.',
                'status_code' => 400,
            ], 400);
        }

       // Retrieve the previous quantity before updating the details
        $previousQuantities = [];
        foreach ($goodsReceipt->orderDetails as $orderDetail) {
            $previousQuantities[$orderDetail->id] = $orderDetail->qty;
        }

        // Update GoodsReceipt data
        $goodsReceipt->comment = $request->input('comment', $goodsReceipt->comment);
        $goodsReceipt->total = $request->input('total', $goodsReceipt->total);
        $goodsReceipt->total_qty = $request->input('total_qty', $goodsReceipt->total_qty);
        $goodsReceipt->goods_receipts_date = $request->input('goods_receipts_date', $goodsReceipt->goods_receipts_date);
        $goodsReceipt->save();

        // Update GoodsReceipt Order Details and their Columns
        if ($request->has('order_details')) {
            GoodsReceiptOrderDetail::where('goods_receipt_id', $id)->delete();

            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new GoodsReceiptOrderDetail([
                    'goods_receipt_id' => $goodsReceipt->id,
                    'unit' => $orderDetailData['unit'] ?? null,
                    'rate' => $orderDetailData['rate'] ?? null,
                    'qty' => $orderDetailData['qty'] ?? 0.00,
                    'tax' => $orderDetailData['tax'] ?? null,
                    'discount' => $orderDetailData['discount'] ?? null,
                    'details' => $orderDetailData['details'] ?? '',
                    'total_amount' => isset($orderDetailData['rate'], $orderDetailData['qty']) ? floatval($orderDetailData['rate']) * floatval($orderDetailData['qty']) : null,
                ]);

                $orderDetail->save();

                // Adjust stock quantity based on previous and updated quantity
                $previousQuantity = $previousQuantities[$orderDetail->id] ?? 0;
                $newQuantity = $orderDetail->qty;
                $difference = $newQuantity - $previousQuantity;

                if ($difference != 0 && isset($orderDetailData['item_code'])) {
                    $product = Product::where('item_code', $orderDetailData['item_code'])->first();
                    if ($product) {
                        $newQty = max(0, $product->qty - $difference);
                        $product->update(['qty' => $newQty]);

                        ProductLog::create([
                            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                            'product_id' => $product->id,
                            'qty_out' => $difference,
                            'out_method' => 'receipt note',
                            'out_at' => now(),
                            'goods_receipt_id' => $goodsReceipt->id,
                        ]);
                    }
                }

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new GoodsReceiptOrderColumn([
                            'goods_receipt_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ?? '',
                        ]);
                        $orderColumn->save();
                    }
                }
            }
        }

            $status = new GoodsReceiptStatus([
                'goods_receipt_id' => $goodsReceipt->id,
                'user_id' => Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                'status' => 'modified',
                'comment' => 'GoodsReceipt updated successfully',
            ]);
            $status->save();


        // Get the IDs of the created records
        $challanId = $goodsReceipt->id;
        $orderDetailIds = $goodsReceipt->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $goodsReceipt->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $goodsReceipt->statuses->pluck('id')->toArray();

        $goodsReceipt = GoodsReceipt::where('id', $challanId)->with('buyerUser', 'SenderUser', 'orderDetails', 'orderDetails.columns', 'orderDetails.columns', 'statuses')->first();


        // // Generate the PDF for the GoodsReceipt using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateGoodsReceiptPDF($goodsReceipt);

        $response = (array) $response->getData();

        // Handle the response from PDFGenerator
        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $goodsReceipt->pdf_url = $response['pdf_url'];
            $goodsReceipt->save();
        }

        return response()->json([
            'message' => 'GoodsReceipt updated successfully.',
            'goods_receipt_id' => $challanId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200,
        ], 200);
    }


}
