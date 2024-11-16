<?php

namespace App\Http\Controllers\Web\Challan;

use Carbon\Carbon;
use App\Models\Challan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ChallanStatus;
use App\Models\ReceiverDetails;
use App\Models\ChallanOrderColumn;
use App\Models\ChallanOrderDetail;
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

class ChallanController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'challan_series' => 'required|string',
            'challan_date' => 'required|date',
            'feature_id' => 'required|exists:features,id',
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

        $featureId = $request->feature_id; // Replace with YOUR_FEATURE_ID
        // Validate usage limit for PlanFeatureUsageRecord
        $PlanFeatureUsageRecord = new PlanFeatureUsageRecord();
        $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->validateUsageLimit($featureId);

        // Validate usage limit for FeatureTopupUsageRecord
        $FeatureTopupUsageRecord = new FeatureTopupUsageRecord();

        if ($PlanFeatureUsageRecordResponse != 'active') {
            // Update usage count for FeatureTopupUsageRecord
            $FeatureTopupUsageRecorddResponse = $FeatureTopupUsageRecord->validateUsageLimit($featureId);
            if ($FeatureTopupUsageRecorddResponse != 'active') {
                return response()->json([
                    'message' => 'Your Feature usage limit is over or expired.',
                    'challan_id' => null,
                    'order_detail_ids' => null,
                    'order_column_ids' => null,
                    'status_ids' => null,
                    'status_code' => 200
                ], 200);
                // Handle the case when both usage counts could not be updated successfully
                // Add appropriate error handling or log the issue for further investigation.
            }
        }

        // Get the latest series_num for the given challan_series and user_id
        $latestSeriesNum = Challan::where('challan_series', $request->challan_series)
            ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->max('series_num');
        // Increment the latestSeriesNum for the new challan
        $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

        // Create a new Challan
        $challan = new Challan([
            'challan_series' => $request->challan_series,
            'challan_date' => $request->challan_date,
            'series_num' => $seriesNum,
            'sender_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'sender' => Auth::guard(Auth::getDefaultDriver())->user()->name,
            'receiver_id' => $request->receiver_id,
            'receiver' => $request->receiver,
            'comment' => $request->comment,
            'total' => $request->total ?? 0.00,
        ]);
        $challan->save();

        // Create Challan Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new ChallanOrderDetail([
                    'challan_id' => $challan->id,
                    'unit' => $orderDetailData['unit'],
                    'rate' => $orderDetailData['rate'] ?? 0.00,
                    'qty' => $orderDetailData['qty'] ?? 0,
                    'total_amount' => $orderDetailData['total_amount'] ?? 0.00,
                ]);
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new ChallanOrderColumn([
                            'challan_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'],
                            'column_value' => $columnData['column_value'],
                        ]);
                        $orderColumn->save();
                    }
                }
            }
        }

        // Create Challan Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new ChallanStatus([
                    'challan_id' => $challan->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                    'status' => 'draft',
                    'comment' => 'Challan created successfully',
                ]);
                $status->save();
            }
        }
        // Get the IDs of the created records
        $challanId = $challan->id;
        $orderDetailIds = $challan->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $challan->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $challan->statuses->pluck('id')->toArray();

        $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount($featureId, 1);

        if (!$PlanFeatureUsageRecordResponse) {
            // Update usage count for FeatureTopupUsageRecord
            $FeatureTopupUsageRecorddResponse = $FeatureTopupUsageRecord->updateUsageCount($featureId, 1);

            if (!$FeatureTopupUsageRecorddResponse) {
                return response()->json([
                    'message' => 'Something Went Wrong.',
                    'challan_id' => null,
                    'order_detail_ids' => null,
                    'order_column_ids' => null,
                    'status_ids' => null,
                    'status_code' => 400
                ], 400);
                // Handle the case when both usage counts could not be updated successfully
                // Add appropriate error handling or log the issue for further investigation.
            }
        }

        return response()->json([
            'message' => 'Challan created successfully.',
            'challan_id' => $challanId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200
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

        // Find the Challan by ID
        $challan = Challan::find($id);

        if (!$challan) {
            return response()->json([
                'message' => 'Challan not found.',
                'status_code' => 400,
            ], 400);
        }

        // Update Challan data
        $challan->comment = $request->input('comment', $challan->comment);
        $challan->total = $request->input('total', $challan->total);
        $challan->challan_date = $request->input('challan_date', $challan->challan_date);
        // Update other fields as needed
        $challan->save();

        // Update Challan Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = ChallanOrderDetail::find($orderDetailData['id']);

                if (!$orderDetail) {
                    return response()->json([
                        'message' => 'Challan Order Detail not found.',
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
                        $orderColumn = ChallanOrderColumn::find($columnData['id']);

                        if (!$orderColumn) {
                            return response()->json([
                                'message' => 'Challan Order Column not found.',
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

        // Create Challan Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new ChallanStatus([
                    'challan_id' => $challan->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                    'status' => 'draft',
                    'comment' => 'Challan updated successfully',
                ]);
                $status->save();
            }
        }

        // Get the IDs of the created records
        $challanId = $challan->id;
        $orderDetailIds = $challan->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $challan->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $challan->statuses->pluck('id')->toArray();

        return response()->json([
            'message' => 'Challan updated successfully.',
            'challan_id' => $challanId,
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

        // Find the Challan by ID
        $challan = Challan::find($id);

        if (!$challan) {
            return response()->json([
                'message' => 'Challan not found.',
                'status_code' => 400,
            ], 400);
        }

        // Update Challan data
        $challan->comment = $request->input('comment', $challan->comment);
        $challan->total = $request->input('total', $challan->total);
        $challan->challan_date = $request->input('challan_date', $challan->challan_date);
        // Update other fields as needed
        $challan->save();

        // Update Challan Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = ChallanOrderDetail::find($orderDetailData['id']);

                if (!$orderDetail) {
                    return response()->json([
                        'message' => 'Challan Order Detail not found.',
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
                        $orderColumn = ChallanOrderColumn::find($columnData['id']);

                        if (!$orderColumn) {
                            return response()->json([
                                'message' => 'Challan Order Column not found.',
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

        // Create Challan Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new ChallanStatus([
                    'challan_id' => $challan->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                    'status' => 'modified',
                    'comment' => 'Challan modified successfully',
                ]);
                $status->save();
            }
        }

        // Get the IDs of the created records
        $challanId = $challan->id;
        $orderDetailIds = $challan->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $challan->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $challan->statuses->pluck('id')->toArray();

        // Find the Challan by ID
        $challan = Challan::findOrFail($challanId)->with('receiverUser', 'senderUser', 'orderDetails', 'column', 'statuses', 'sfp');

        // Generate the PDF for the Challan using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateChallanPDF($challan);

        if ($response['status_code'] === 200) {
            $challan->pdf_url = $response['pdf_url'];
            $challan->save();
            return response()->json([
                'message' => 'Challan modified successfully.',
                'challan_id' => $challanId,
                'order_detail_ids' => $orderDetailIds,
                'order_column_ids' => $orderColumnIds,
                'status_ids' => $statusIds,
                'status_code' => 200,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Challan PDF updation fail.',
                'challan_id' => $challanId,
                'order_detail_ids' => $orderDetailIds,
                'order_column_ids' => $orderColumnIds,
                'status_ids' => $statusIds,
                'status_code' => 400,
            ], 400);
        }
    }
    public function send(Request $request, $challanId)
    {
        // Find the Challan by ID
        $challan = Challan::findOrFail($challanId)->with('receiverUser', 'senderUser', 'orderDetails', 'column', 'statuses', 'sfp');

        // Generate a secure token for accepting/rejecting the Challan (using Laravel's Str::random() for simplicity)
        // $token = Str::random(64);

        // Save the token and its expiry date to the ChallanStatus record
        // $status->token = $token;
        // $status->token_expiry = Carbon::now()->addDay(); // Token is valid for 1 day
        // $status->save();

        // Generate the PDF for the Challan using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateChallanPDF($challan);

        // Handle the response from PDFGenerator
        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $challan->pdf_url = $response['pdf_url'];
            $challan->save();

            // Send the PDF via email
            if ($challan->receiverUser->email != null) {
                $pdfEmailService = new PDFEmailService();
                $recipientEmail = $challan->receiverUser->email; // Replace with the actual recipient email address
                $pdfEmailService->sendChallanByEmail($challan, $response['pdf_url'], $recipientEmail);
            }

            // Assuming that PlanAdditionalFeatureUsageRecord and AdditionalFeatureTopupUsageRecord models have been imported.


            if ($challan->receiverUser->phone != null) {
                $featureId = $request->feature_id; // Replace with YOUR_FEATURE_ID

                // Validate usage limit for PlanAdditionalFeatureUsageRecord
                $PlanAdditionalFeatureUsageRecord = new PlanAdditionalFeatureUsageRecord();

                // Validate usage limit for AdditionalFeatureTopupUsageRecord
                $AdditionalFeatureTopupUsageRecord = new AdditionalFeatureTopupUsageRecord();

                $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, 1);

                if ($PlanAdditionalFeatureUsageRecordResponse) {
                    $pdfWhatsAppService = new PDFWhatsAppService();
                    $recipientPhoneNumber = $challan->receiverUser->phone; // Replace with the actual recipient phone number
                    $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendChallanOnWhatsApp($challan, $response['pdf_url'], $recipientPhoneNumber);

                    if (!$pdfWhatsAppServiceResponse) {
                        Log::error('Error sending Challan PDF Whatsapp for Challan Id: ' . $challan->id);
                        $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, -1);
                    }
                } else {
                    // Update usage count for AdditionalFeatureTopupUsageRecord
                    $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, 1);

                    if ($AdditionalFeatureTopupUsageRecordResponse) {
                        $pdfWhatsAppService = new PDFWhatsAppService();
                        $recipientPhoneNumber = $challan->receiverUser->phone; // Replace with the actual recipient phone number
                        $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendChallanOnWhatsApp($challan, $response['pdf_url'], $recipientPhoneNumber);

                        if (!$pdfWhatsAppServiceResponse) {
                            Log::error('Error sending Challan PDF Whatsapp for Challan Id: ' . $challan->id);
                            $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, -1);
                        }
                    }
                }
            }


            // Add a new "sent" status to the Challan
            $status = new ChallanStatus([
                'challan_id' => $challan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'sent',
                'comment' => 'Challan sent for acceptance',
            ]);
            $status->save();

            // Return a response with the token and other relevant information
            return response()->json([
                'message' => 'Challan sent successfully.',
                'challan_id' => $challan->id,
                // 'token' => $token,
                // 'token_expiry' => $status->token_expiry,
                'pdf_url' => $response['pdf_url'],
                'status_code' => 200
            ], 200);
        } else {
            // Error occurred during PDF generation and storage
            // Return an error response
            return response()->json([
                'message' => 'Error generating and storing Challan PDF.',
                'challan_id' => $challan->id,
                // 'token' => $token,
                // 'token_expiry' => $status->token_expiry,
                'pdf_url' => null,
                'status_code' => $response['status_code']
            ], $response['status_code']);
        }
    }
    public function resend(Request $request, $challanId)
    {
        // Find the Challan by ID
        $challan = Challan::findOrFail($challanId)->with('receiverUser', 'senderUser', 'orderDetails', 'column', 'statuses', 'sfp');

        // Generate a secure token for accepting/rejecting the Challan (using Laravel's Str::random() for simplicity)
        // $token = Str::random(64);

        // Save the token and its expiry date to the ChallanStatus record
        // $status->token = $token;
        // $status->token_expiry = Carbon::now()->addDay(); // Token is valid for 1 day
        // $status->save();


        // PDF generated successfully

        // Send the PDF via email
        if ($challan->receiverUser->email != null) {
            $pdfEmailService = new PDFEmailService();
            $recipientEmail = $challan->receiverUser->email; // Replace with the actual recipient email address
            $pdfEmailService->sendChallanByEmail($challan, $challan->pdf_url, $recipientEmail);
        }

        // Assuming that PlanAdditionalFeatureUsageRecord and AdditionalFeatureTopupUsageRecord models have been imported.

        if ($challan->receiverUser->phone != null) {
            $featureId = $request->feature_id; // Replace with YOUR_FEATURE_ID

            // Validate usage limit for PlanAdditionalFeatureUsageRecord
            $PlanAdditionalFeatureUsageRecord = new PlanAdditionalFeatureUsageRecord();

            // Validate usage limit for AdditionalFeatureTopupUsageRecord
            $AdditionalFeatureTopupUsageRecord = new AdditionalFeatureTopupUsageRecord();

            $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, 1);

            if ($PlanAdditionalFeatureUsageRecordResponse) {
                $pdfWhatsAppService = new PDFWhatsAppService();
                $recipientPhoneNumber = $challan->receiverUser->phone; // Replace with the actual recipient phone number
                $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendChallanOnWhatsApp($challan, $challan->pdf_url, $recipientPhoneNumber);

                if (!$pdfWhatsAppServiceResponse) {
                    Log::error('Error sending Challan PDF Whatsapp for Challan Id: ' . $challan->id);
                    $PlanAdditionalFeatureUsageRecordResponse = $PlanAdditionalFeatureUsageRecord->updateUsageCount($featureId, -1);
                }
            } else {
                // Update usage count for AdditionalFeatureTopupUsageRecord
                $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, 1);

                if ($AdditionalFeatureTopupUsageRecordResponse) {
                    $pdfWhatsAppService = new PDFWhatsAppService();
                    $recipientPhoneNumber = $challan->receiverUser->phone; // Replace with the actual recipient phone number
                    $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendChallanOnWhatsApp($challan, $challan->pdf_url, $recipientPhoneNumber);

                    if (!$pdfWhatsAppServiceResponse) {
                        Log::error('Error sending Challan PDF Whatsapp for Challan Id: ' . $challan->id);
                        $AdditionalFeatureTopupUsageRecordResponse = $AdditionalFeatureTopupUsageRecord->updateUsageCount($featureId, -1);
                    }
                }
            }
        }


        // Add a new "sent" status to the Challan
        $status = new ChallanStatus([
            'challan_id' => $challan->id,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
            'status' => 'resent',
            'comment' => 'Challan resent for acceptance',
        ]);
        $status->save();

        // Return a response with the token and other relevant information
        return response()->json([
            'message' => 'Challan resent successfully.',
            'challan_id' => $challan->id,
            // 'token' => $token,
            // 'token_expiry' => $status->token_expiry,
            'pdf_url' => $challan->pdf_url,
            'status_code' => 200
        ], 200);
    }
    public function index(Request $request)
    {

        // Assuming you have a logged-in user, you can get the user ID like this:
        $userId = auth()->user()->id;

        $query = Challan::query()->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);

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



        // Fetch the distinct filter values for Challan table (for this user)
        $distinctChallanSeries = Challan::where('sender_id', $userId)->distinct()->pluck('challan_series');
        $distinctSenderIds = Challan::where('sender_id', $userId)->distinct()->pluck('sender_id');
        $distinctReceiverIds = Challan::where('sender_id', $userId)->distinct()->pluck('receiver_id');
        // $distinctStatuses = Status::distinct()->pluck('status');

        // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
        $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            $query->select('id')->from('receivers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            $query->select('id')->from('receivers')->where('user_id', $userId);
        })->distinct()->pluck('city');

        // Add any other desired filters

        $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails'])->paginate(20);

        // return response()->json($challans, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $challans,
            'status_code' => 200,
            'filters' => [
                'challan_series' => $distinctChallanSeries,
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

        // Fetch the challan by ID (for this user)
        $challan = Challan::where('sender_id', $userId)->find($id);

        // Load related data
        $challan->load(['challanOrderDetails', 'challanOrderColumns', 'challanStatuses', 'receiverDetails']);

        if (!$challan) {
            return response()->json([
                'data' => null,
                'message' => 'Challan not found',
                'status_code' => 200,
            ], 200);
        }

        // Return the response
        return response()->json([
            'message' => 'Success',
            'data' => $challan,
            'status_code' => 200,
        ], 200);
    }
    public function accept(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            $challan = Challan::findOrFail($challanId);

            // Update the status of the Challan to "accepted"
            $challan->statuses()->create([
                'challan_id' => $challan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'accepted',
                'comment' => 'Challan accepted',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $challan->statuses,
                'message' => 'Challan accepted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Challan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }
    public function selfAccept(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            $challan = Challan::findOrFail($challanId);

            // Update the status of the Challan to "self-accepted"
            $challan->statuses()->create([
                'challan_id' => $challan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'self-accepted',
                'comment' => 'Challan self accepted',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $challan->statuses,
                'message' => 'Challan self accepted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Challan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }
    public function reject(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            $challan = Challan::findOrFail($challanId);

            // Update the status of the Challan to "rejected"
            $challan->statuses()->create([
                'challan_id' => $challan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'rejected',
                'comment' => 'Challan rejected',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $challan->statuses,
                'message' => 'Challan rejected successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Challan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }
    public function selfReject(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            $challan = Challan::findOrFail($challanId);

            // Update the status of the Challan to "self_reject"
            $challan->statuses()->create([
                'challan_id' => $challan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'self_reject',
                'comment' => 'Challan self rejected',
            ]);

            // Return a response indicating success
            return response()->json([
                'data' => $challan->statuses,
                'message' => 'Challan self rejected successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Challan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function delete(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            $challan = Challan::findOrFail($challanId);

            // Update the status of the Challan to "deleted"
            $challan->statuses()->create([
                'challan_id' => $challan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'deleted',
                'comment' => 'Challan deleted',
            ]);

            $challan->delete();

            // Return a response indicating success
            return response()->json([
                'data' => $challan->statuses,
                'message' => 'Challan deleted successfully.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Challan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }

    public function forceDelete(Request $request, $challanId)
    {
        try {
            // Find the Challan by ID
            $challan = Challan::findOrFail($challanId);

            $challan->forceDelete();

            // Return a response indicating success
            return response()->json([
                'message' => 'Challan permanently deleted.',
                'status_code' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Challan Not Found.',
                'status_code' => 400
            ], 400);
        }
    }
}
