<?php

namespace App\Http\Controllers\Web\ReturnChallan;

use Carbon\Carbon;
use App\Models\ReturnChallan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ReturnChallanStatus;
use App\Models\ReceiverDetails;
use App\Models\ReturnChallanOrderColumn;
use App\Models\ReturnChallanOrderDetail;
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

class ReturnChallanController extends Controller
{

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'challan_id' => 'required|exists:challans,id',
            'challan_series' => 'required|string',
            'challan_date' => 'required|date',
            'receiver_id' => 'required|exists:users,id',
            'receiver' => 'required|string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            'order_details.*.unit' => 'required|string',
            'order_details.*.rate' => 'numeric|min:0',
            'order_details.*.qty' => 'integer|min:0',
            'order_details.*.total_amount' => 'numeric|min:0',
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

        // Get the authenticated user
        $user = Auth::guard(Auth::getDefaultDriver())->user();

        // Create a new ReturnChallan
        $returnChallan = new ReturnChallan([
            'challan_id' => $request->challan_id,
            'challan_series' => $request->challan_series,
            'challan_date' => $request->challan_date,
            'sender_id' => $user->id,
            'sender' => $user->name,
            'receiver_id' => $request->receiver_id,
            'receiver' => $request->receiver,
            'comment' => $request->comment,
            'total' => $request->total ?? 0.00,
        ]);
        $returnChallan->save();

        // Create ReturnChallan Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new ReturnChallanOrderDetail([
                    'challan_id' => $returnChallan->id,
                    'unit' => $orderDetailData['unit'],
                    'rate' => $orderDetailData['rate'] ?? 0.00,
                    'qty' => $orderDetailData['qty'] ?? 0,
                    'total_amount' => $orderDetailData['total_amount'] ?? 0.00,
                ]);
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new ReturnChallanOrderColumn([
                            'challan_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'],
                            'column_value' => $columnData['column_value'],
                        ]);
                        $orderColumn->save();
                    }
                }
            }
        }

        // Create ReturnChallan Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new ReturnChallanStatus([
                    'challan_id' => $returnChallan->id,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'status' => 'draft',
                    'comment' => 'Return Challan created successfully',
                ]);
                $status->save();
            }
        }

        // Get the IDs of the created records
        $returnChallanId = $returnChallan->id;
        $orderDetailIds = $returnChallan->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $returnChallan->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $returnChallan->statuses->pluck('id')->toArray();

        return response()->json([
            'message' => 'Return Challan created successfully.',
            'challan_id' => $returnChallanId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'challan_series' => 'string',
            'challan_date' => 'required|date',
            'receiver_id' => 'exists:users,id',
            'receiver' => 'string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            'order_details.*.id' => 'required|exists:challan_order_details,id',
            'order_details.*.unit' => 'required|string',
            'order_details.*.rate' => 'numeric|min:0',
            'order_details.*.qty' => 'integer|min:0',
            'order_details.*.total_amount' => 'numeric|min:0',
            'order_details.*.columns.*.id' => 'required|exists:challan_order_columns,id',
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

        // Find the ReturnChallan by ID
        $returnChallan = ReturnChallan::find($id);

        if (!$returnChallan) {
            return response()->json([
                'message' => 'Return Challan not found.',
                'status_code' => 400,
            ], 400);
        }

        // Update ReturnChallan data
        $returnChallan->comment = $request->input('comment', $returnChallan->comment);
        $returnChallan->total = $request->input('total', $returnChallan->total);
        $returnChallan->challan_date = $request->input('challan_date', $returnChallan->challan_date);
        // Update other fields as needed
        $returnChallan->save();

        // Update ReturnChallan Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = ReturnChallanOrderDetail::find($orderDetailData['id']);

                if (!$orderDetail) {
                    return response()->json([
                        'message' => 'Return Challan Order Detail not found.',
                        'status_code' => 400,
                    ], 400);
                }

                $orderDetail->unit = $orderDetailData['unit'];
                $orderDetail->rate = $orderDetailData['rate'] ?? 0.00;
                $orderDetail->qty = $orderDetailData['qty'] ?? 0;
                $orderDetail->total_amount = $orderDetailData['total_amount'] ?? 0.00;
                // Update other fields as needed
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = ReturnChallanOrderColumn::find($columnData['id']);

                        if (!$orderColumn) {
                            return response()->json([
                                'message' => 'Return Challan Order Column not found.',
                                'status_code' => 400,
                            ], 400);
                        }

                        $orderColumn->column_name = $columnData['column_name'];
                        $orderColumn->column_value = $columnData['column_value'];
                        // Update other fields as needed
                        $orderColumn->save();
                    }
                }
            }
        }

        // Create ReturnChallan Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new ReturnChallanStatus([
                    'challan_id' => $returnChallan->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                    'status' => 'draft',
                    'comment' => 'Return Challan updated successfully',
                ]);
                $status->save();
            }
        }

        // Get the IDs of the updated records
        $returnChallanId = $returnChallan->id;
        $orderDetailIds = $returnChallan->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $returnChallan->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $returnChallan->statuses->pluck('id')->toArray();

        return response()->json([
            'message' => 'Return Challan updated successfully.',
            'challan_id' => $returnChallanId,
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
            'challan_series' => 'string',
            'challan_date' => 'required|date',
            'receiver_id' => 'exists:users,id',
            'receiver' => 'string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            'order_details.*.id' => 'required|exists:challan_order_details,id',
            'order_details.*.unit' => 'required|string',
            'order_details.*.rate' => 'numeric|min:0',
            'order_details.*.qty' => 'integer|min:0',
            'order_details.*.total_amount' => 'numeric|min:0',
            'order_details.*.columns.*.id' => 'required|exists:challan_order_columns,id',
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

        // Find the ReturnChallan by ID
        $returnChallan = ReturnChallan::find($id);

        if (!$returnChallan) {
            return response()->json([
                'message' => 'Return Challan not found.',
                'status_code' => 400,
            ], 400);
        }

        // Update ReturnChallan data
        $returnChallan->comment = $request->input('comment', $returnChallan->comment);
        $returnChallan->total = $request->input('total', $returnChallan->total);
        $returnChallan->challan_date = $request->input('challan_date', $returnChallan->challan_date);
        // Update other fields as needed
        $returnChallan->save();

        // Update ReturnChallan Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = ReturnChallanOrderDetail::find($orderDetailData['id']);

                if (!$orderDetail) {
                    return response()->json([
                        'message' => 'Return Challan Order Detail not found.',
                        'status_code' => 400,
                    ], 400);
                }

                $orderDetail->unit = $orderDetailData['unit'];
                $orderDetail->rate = $orderDetailData['rate'] ?? 0.00;
                $orderDetail->qty = $orderDetailData['qty'] ?? 0;
                $orderDetail->total_amount = $orderDetailData['total_amount'] ?? 0.00;
                // Update other fields as needed
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = ReturnChallanOrderColumn::find($columnData['id']);

                        if (!$orderColumn) {
                            return response()->json([
                                'message' => 'Return Challan Order Column not found.',
                                'status_code' => 400,
                            ], 400);
                        }

                        $orderColumn->column_name = $columnData['column_name'];
                        $orderColumn->column_value = $columnData['column_value'];
                        // Update other fields as needed
                        $orderColumn->save();
                    }
                }
            }
        }

        // Create ReturnChallan Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new ReturnChallanStatus([
                    'challan_id' => $returnChallan->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                    'status' => 'modified',
                    'comment' => 'Return Challan modified successfully',
                ]);
                $status->save();
            }
        }

        // Get the IDs of the modified records
        $returnChallanId = $returnChallan->id;
        $orderDetailIds = $returnChallan->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $returnChallan->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $returnChallan->statuses->pluck('id')->toArray();

        return response()->json([
            'message' => 'Return Challan modified successfully.',
            'challan_id' => $returnChallanId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200,
        ], 200);
    }


    public function send(Request $request, $returnChallanId)
    {
        // Find the ReturnChallan by ID
        $returnChallan = ReturnChallan::findOrFail($returnChallanId);

        // Generate a secure token for accepting/rejecting the ReturnChallan (using Laravel's Str::random() for simplicity)
        // $token = Str::random(64);

        // Save the token and its expiry date to the ReturnChallanStatus record
        // $status->token = $token;
        // $status->token_expiry = Carbon::now()->addDay(); // Token is valid for 1 day
        // $status->save();

        // Generate the PDF for the ReturnChallan using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateReturnChallanPDF($returnChallan);

        // Handle the response from PDFGenerator
        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $returnChallan->pdf_url = $response['pdf_url'];
            $returnChallan->save();

            // Send the PDF via email
            if ($returnChallan->receiverUser->email != null) {
                $pdfEmailService = new PDFEmailService();
                $recipientEmail = $returnChallan->receiverUser->email; // Replace with the actual recipient email address
                $pdfEmailService->sendReturnChallanByEmail($returnChallan, $response['pdf_url'], $recipientEmail);
            }

            // Assuming that PlanAdditionalFeatureUsageRecord and AdditionalFeatureTopupUsageRecord models have been imported.

            if ($returnChallan->receiverUser->phone != null) {
                $featureId = $request->feature_id; // Replace with YOUR_FEATURE_ID

                // Validate usage limit for PlanAdditionalFeatureUsageRecord
                $PlanAdditionalFeatureUsageRecord = new PlanAdditionalFeatureUsageRecord();

                // Validate usage limit for AdditionalFeatureTopupUsageRecord
                $AdditionalFeatureTopupUsageRecord = new AdditionalFeatureTopupUsageRecord();

                $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, 1);

                if ($PlanAdditionalFeatureUsageRecordResponse) {
                    $pdfWhatsAppService = new PDFWhatsAppService();
                    $recipientPhoneNumber = $returnChallan->receiverUser->phone; // Replace with the actual recipient phone number
                    $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendReturnChallanOnWhatsApp($returnChallan, $response['pdf_url'], $recipientPhoneNumber);

                    if (!$pdfWhatsAppServiceResponse) {
                        Log::error('Error sending ReturnChallan PDF Whatsapp for ReturnChallan Id: ' . $returnChallan->id);
                        $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, -1);
                    }
                } else {
                    // Update usage count for AdditionalFeatureTopupUsageRecord
                    $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, 1);

                    if ($AdditionalFeatureTopupUsageRecordResponse) {
                        $pdfWhatsAppService = new PDFWhatsAppService();
                        $recipientPhoneNumber = $returnChallan->receiverUser->phone; // Replace with the actual recipient phone number
                        $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendReturnChallanOnWhatsApp($returnChallan, $response['pdf_url'], $recipientPhoneNumber);

                        if (!$pdfWhatsAppServiceResponse) {
                            Log::error('Error sending ReturnChallan PDF Whatsapp for ReturnChallan Id: ' . $returnChallan->id);
                            $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, -1);
                        }
                    }
                }
            }

            // Add a new "sent" status to the ReturnChallan
            $status = new ReturnChallanStatus([
                'challan_id' => $returnChallan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'sent',
                'comment' => 'ReturnChallan sent for acceptance',
            ]);
            $status->save();

            // Return a response with the token and other relevant information
            return response()->json([
                'message' => 'ReturnChallan sent successfully.',
                'challan_id' => $returnChallan->id,
                // 'token' => $token,
                // 'token_expiry' => $status->token_expiry,
                'pdf_url' => $response['pdf_url'],
                'status_code' => 200
            ], 200);
        } else {
            // Error occurred during PDF generation and storage
            // Return an error response
            return response()->json([
                'message' => 'Error generating and storing ReturnChallan PDF.',
                'challan_id' => $returnChallan->id,
                // 'token' => $token,
                // 'token_expiry' => $status->token_expiry,
                'pdf_url' => null,
                'status_code' => $response['status_code']
            ], $response['status_code']);
        }
    }

    public function resend(Request $request, $returnChallanId)
    {
        // Find the ReturnChallan by ID
        $returnChallan = ReturnChallan::findOrFail($returnChallanId);

        // PDF generated successfully

        // Send the PDF via email
        if ($returnChallan->receiverUser->email != null) {
            $pdfEmailService = new PDFEmailService();
            $recipientEmail = $returnChallan->receiverUser->email; // Replace with the actual recipient email address
            $pdfEmailService->sendReturnChallanByEmail($returnChallan, $returnChallan->pdf_url, $recipientEmail);
        }

        // Assuming that PlanAdditionalFeatureUsageRecord and AdditionalFeatureTopupUsageRecord models have been imported.

        if ($returnChallan->receiverUser->phone != null) {
            $featureId = $request->feature_id; // Replace with YOUR_FEATURE_ID

            // Validate usage limit for PlanAdditionalFeatureUsageRecord
            $PlanAdditionalFeatureUsageRecord = new PlanAdditionalFeatureUsageRecord();

            // Validate usage limit for AdditionalFeatureTopupUsageRecord
            $AdditionalFeatureTopupUsageRecord = new AdditionalFeatureTopupUsageRecord();

            $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, 1);

            if ($PlanAdditionalFeatureUsageRecordResponse) {
                $pdfWhatsAppService = new PDFWhatsAppService();
                $recipientPhoneNumber = $returnChallan->receiverUser->phone; // Replace with the actual recipient phone number
                $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendReturnChallanOnWhatsApp($returnChallan, $returnChallan->pdf_url, $recipientPhoneNumber);

                if (!$pdfWhatsAppServiceResponse) {
                    Log::error('Error sending ReturnChallan PDF Whatsapp for ReturnChallan Id: ' . $returnChallan->id);
                    $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, -1);
                }
            } else {
                // Update usage count for AdditionalFeatureTopupUsageRecord
                $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, 1);

                if ($AdditionalFeatureTopupUsageRecordResponse) {
                    $pdfWhatsAppService = new PDFWhatsAppService();
                    $recipientPhoneNumber = $returnChallan->receiverUser->phone; // Replace with the actual recipient phone number
                    $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendReturnChallanOnWhatsApp($returnChallan, $returnChallan->pdf_url, $recipientPhoneNumber);

                    if (!$pdfWhatsAppServiceResponse) {
                        Log::error('Error sending ReturnChallan PDF Whatsapp for ReturnChallan Id: ' . $returnChallan->id);
                        $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, -1);
                    }
                }
            }
        }

        // Add a new "resent" status to the ReturnChallan
        $status = new ReturnChallanStatus([
            'challan_id' => $returnChallan->id,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
            'status' => 'resent',
            'comment' => 'ReturnChallan resent for acceptance',
        ]);
        $status->save();

        // Return a response with the token and other relevant information
        return response()->json([
            'message' => 'ReturnChallan resent successfully.',
            'challan_id' => $returnChallan->id,
            // 'token' => $token,
            // 'token_expiry' => $status->token_expiry,
            'pdf_url' => $returnChallan->pdf_url,
            'status_code' => 200
        ], 200);
    }
    public function index(Request $request)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:
        $userId = auth()->user()->id;

        $query = ReturnChallan::query()->where('sender_id', $userId);

        // Filter by challan_series
        if ($request->has('challan_series')) {
            $query->where('challan_series', $request->challan_series);
        }

        // Filter by sender_id
        if ($request->has('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

        // Filter by receiver_id
        if ($request->has('receiver_id')) {
            $query->where('receiver_id', $request->receiver_id);
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

        // Filter by state in ReceiverDetails
        if ($request->has('state')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        // Filter by city in ReceiverDetails
        if ($request->has('city')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        // Fetch the distinct filter values for ReturnChallan table (for this user)
        $distinctReturnChallanSeries = ReturnChallan::where('sender_id', $userId)->distinct()->pluck('challan_series');
        $distinctSenderIds = ReturnChallan::where('sender_id', $userId)->distinct()->pluck('sender_id');
        $distinctReceiverIds = ReturnChallan::where('sender_id', $userId)->distinct()->pluck('receiver_id');
        // $distinctStatuses = Status::distinct()->pluck('status');

        // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
        $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            $query->select('id')->from('receivers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            $query->select('id')->from('receivers')->where('user_id', $userId);
        })->distinct()->pluck('city');

        // Add any other desired filters

        $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'orderDetails', 'orderDetails.columns', 'sfp'])->paginate(20);
        // dd($returnChallans);
        // return response()->json($returnChallans, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $returnChallans,
            'status_code' => 200,
            'filters' => [
                'return_challan_series' => $distinctReturnChallanSeries,
                'sender_id' => $distinctSenderIds,
                'receiver_id' => $distinctReceiverIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                // Add any other filter values here if needed
            ]
        ], 200);
    }

    public function show(Request $request, $id)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:
        $userId = auth()->user()->id;

        // Fetch the return challan by ID (for this user)
        $returnChallan = ReturnChallan::where('sender_id', $userId)->find($id);

        // Load related data
        $returnChallan->load(['returnChallanOrderDetails', 'returnChallanOrderColumns', 'returnChallanStatuses', 'receiverDetails']);

        if (!$returnChallan) {
            return response()->json([
                'data' => null,
                'message' => 'ReturnChallan not found',
                'status_code' => 200,
            ], 200);
        }

        // Return the response
        return response()->json([
            'message' => 'Success',
            'data' => $returnChallan,
            'status_code' => 200,
        ], 200);
    }


    public function accept(Request $request, $returnChallanId)
    {
        try {
            // Find the ReturnChallan by ID
            $returnChallan = ReturnChallan::findOrFail($returnChallanId);

            // Update the status of the ReturnChallan to "accepted"
            $returnChallan->statuses()->create([
                'challan_id' => $returnChallan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'accepted',
                'comment' => 'ReturnChallan accepted',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $returnChallan->statuses,
                'message' => 'ReturnChallan accepted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'ReturnChallan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function selfAccept(Request $request, $returnChallanId)
    {
        try {
            // Find the ReturnChallan by ID
            $returnChallan = ReturnChallan::findOrFail($returnChallanId);

            // Update the status of the ReturnChallan to "self-accepted"
            $returnChallan->statuses()->create([
                'challan_id' => $returnChallan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'self-accepted',
                'comment' => 'ReturnChallan self accepted',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $returnChallan->statuses,
                'message' => 'ReturnChallan self accepted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'ReturnChallan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function reject(Request $request, $returnChallanId)
    {
        try {
            // Find the ReturnChallan by ID
            $returnChallan = ReturnChallan::findOrFail($returnChallanId);

            // Update the status of the ReturnChallan to "rejected"
            $returnChallan->statuses()->create([
                'challan_id' => $returnChallan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'rejected',
                'comment' => 'ReturnChallan rejected',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $returnChallan->statuses,
                'message' => 'ReturnChallan rejected successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'ReturnChallan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function selfReject(Request $request, $returnChallanId)
    {
        try {
            // Find the ReturnChallan by ID
            $returnChallan = ReturnChallan::findOrFail($returnChallanId);

            // Update the status of the ReturnChallan to "self_reject"
            $returnChallan->statuses()->create([
                'challan_id' => $returnChallan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'self_reject',
                'comment' => 'ReturnChallan self rejected',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $returnChallan->statuses,
                'message' => 'ReturnChallan self rejected successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'ReturnChallan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function delete(Request $request, $returnChallanId)
    {
        try {
            // Find the ReturnChallan by ID
            $returnChallan = ReturnChallan::findOrFail($returnChallanId);

            // Update the status of the ReturnChallan to "deleted"
            $returnChallan->statuses()->create([
                'challan_id' => $returnChallan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'deleted',
                'comment' => 'ReturnChallan deleted',
            ]);

            $returnChallan->delete();

            // Return a response indicating success
            return response()->json([
                'data' => $returnChallan->statuses,
                'message' => 'ReturnChallan deleted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'ReturnChallan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function forceDelete(Request $request, $returnChallanId)
    {
        try {
            // Find the ReturnChallan by ID
            $returnChallan = ReturnChallan::findOrFail($returnChallanId);

            $returnChallan->forceDelete();

            // Return a response indicating success
            return response()->json([
                'message' => 'ReturnChallan permanently deleted.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'ReturnChallan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }
}
