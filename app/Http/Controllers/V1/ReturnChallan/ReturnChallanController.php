<?php

namespace App\Http\Controllers\V1\ReturnChallan;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Challan;
use App\Models\Product;
use App\Models\Receiver;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ReturnChallan;
use App\Models\ReceiverDetails;
use App\Models\ReturnChallanSfp;
use App\Models\ChallanOrderColumn;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\ChallanOrderDetail;
use Illuminate\Support\Facades\DB;
use App\Models\ReturnChallanStatus;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PlanFeatureUsageRecord;
use App\Models\FeatureTopupUsageRecord;
use App\Models\ReturnChallanOrderColumn;
use App\Models\ReturnChallanOrderDetail;
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
        // dd($request->all());

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            // 'challan_id' => 'required|exists:challans,id',
            'challan_series' => 'required|string',
            'challan_date' => 'required|date',
            'receiver_id' => 'required|exists:users,id',
            'receiver' => 'required|string',
            'comment' => 'nullable|string',
            'total' => 'numeric|min:0',
            // 'order_details.*.unit' => 'nullable|string',
            // 'order_details.*.sender_challan_id' => '',
            'order_details.*.rate' => 'nullable|numeric|min:0',
            'order_details.*.qty' => 'numeric|min:0',
            'order_details.*.item_code' => 'nullable|string',
            // 'order_details.*.total_amount' => 'numeric|min:0',
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

            // Validate usage limit for PlanFeatureUsageRecord
            $featureId = 7; // Replace with YOUR_FEATURE_ID
            $PlanFeatureUsageRecord = new PlanFeatureUsageRecord();
            $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->validateUsageLimit($featureId);

            if ($PlanFeatureUsageRecordResponse === 'not_found') {
                return response()->json([
                    'message' => 'Feature usage limit is over or expired.',
                    'challan_id' => null,
                    'order_detail_ids' => null,
                    'order_column_ids' => null,
                    'status_ids' => null,
                    'status_code' => 422
                ], 422);
            }

            // If PlanFeatureUsageRecord is not 'active', check FeatureTopupUsageRecord
            if ($PlanFeatureUsageRecordResponse !== 'active') {
                $FeatureTopupUsageRecord = new FeatureTopupUsageRecord();
                $FeatureTopupUsageRecordResponse = $FeatureTopupUsageRecord->validateUsageLimit($featureId);

                if ($FeatureTopupUsageRecordResponse !== 'active') {
                    return response()->json([
                        'message' => 'Feature usage limit is over or expired.',
                        'challan_id' => null,
                        'order_detail_ids' => null,
                        'order_column_ids' => null,
                        'status_ids' => null,
                        'status_code' => 422
                    ], 422);
                }
            }

        // Get the authenticated user
        $user = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Get the latest series_num for the given challan_series and user_id
        $latestSeriesNum = ReturnChallan::where('challan_series', $request->challan_series)
            ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

        // Increment the latestSeriesNum for the new challan
        $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

        $returnChallanDetail = (object) $request->order_details;


        foreach ($returnChallanDetail as $returnChallan) {
            // Access 'id' directly from the array
            $challanId = $returnChallan['id'];


            // Retrieve the record based on the 'id'
            $challanOrderDetail = ChallanOrderDetail::find($challanId);
            // dd($challanOrderDetail);
        }
        // Create a new ReturnChallan
        $returnChallan = new ReturnChallan([
            'challan_id' => $challanOrderDetail->challan_id,
            'challan_series' => $request->challan_series,
            'challan_date' => $request->challan_date,
            'series_num' => $request->series_num,
            'sender_id' => $user,
            'sender' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
            'receiver_id' => $request->receiver_id,
            'receiver' => $request->receiver,
            'comment' => $request->comment,
            'total' => $request->total ?? 0.00,
            'total_qty' => $request->total_qty ?? 0,
            'roundoff' => $request->round_off ?? null,
            'team_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_id : null,
        ]);
        $returnChallan->save();

        // Create ReturnChallan Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $challan_order_detail_id = null;
                if (isset($orderDetailData['columns'][0]['challan_order_detail_id'])) {
                    $challan_order_detail_id = $orderDetailData['columns'][0]['challan_order_detail_id'];
                }
                $orderDetail = new ReturnChallanOrderDetail([
                    'challan_id' => $returnChallan->id,
                    'sender_challan_id' => $orderDetailData['challan_id'],
                    'unit' => $orderDetailData['unit'] ?? null,
                    'rate' => $orderDetailData['rate'] ?? null,
                    'qty' => $orderDetailData['remaining_qty'] ?? 0,
                    'details' => $orderDetailData['details'] ?? null,
                    'item_code' => $orderDetailData['item_code'] ?? null,
                    'challan_order_detail_id' => $challan_order_detail_id,
                    'total_amount' => isset($orderDetailData['rate'], $orderDetailData['qty']) ? floatval($orderDetailData['rate']) * floatval($orderDetailData['qty']) : null,
                    'tax' => $orderDetailData['tax'] ?? null,
                    'discount' => $orderDetailData['discount'] ?? null,
                ]);

                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new ReturnChallanOrderColumn([
                            'challan_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ??'',
                        ]);
                        $orderColumn->save();
                    }
                }

                // Update the 'remaining_qty' in ChallanOrderDetail
                $challanOrderDetail = ChallanOrderDetail::find($orderDetailData['id']);
                // dd($challanOrderDetail->remaining_qty, $orderDetail->qty);
                if ($challanOrderDetail) {
                    $challanOrderDetail->update([
                        'remaining_qty' => $challanOrderDetail->remaining_qty - $orderDetail->qty,
                    ]);
                }
            }
        }


        // Create ReturnChallan Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new ReturnChallanStatus([
                    'challan_id' => $returnChallan->id,
                    'user_id' => $user,
                    'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    'team_user_name' => Auth::user()->name ?? Auth::user()->team_user_name ?? null,
                    'status' => 'created',
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

        $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount($featureId, 1);

        if (!$PlanFeatureUsageRecordResponse) {
            // Update usage count for FeatureTopupUsageRecord
            $FeatureTopupUsageRecorddResponse = $FeatureTopupUsageRecord->updateUsageCount($featureId, 1);

            if (!$FeatureTopupUsageRecorddResponse) {
                return response()->json([
                    'message' => 'Something Went Wrong.',
                    'return_challan_id' => null,
                    'order_detail_ids' => null,
                    'order_column_ids' => null,
                    'status_ids' => null,
                    'status_code' => 400
                ], 400);
                // Handle the case when both usage counts could not be updated successfully
                // Add appropriate error handling or log the issue for further investigation.
            }
        }

        $returnChallanDetail = (object) $request->order_details;


        // foreach ($returnChallanDetail as $returnChallan) {
        //     // Access 'id' directly from the array
        //     $challanId = $returnChallan['id'];


        //     // Retrieve the record based on the 'id'
        //     $challanOrderDetail = ChallanOrderDetail::find($challanId);
        //     // dd($orderDetail->qty);

        //     // $challanOrderDetail->remaining_qty - $orderDetail->qty;
        //     // dd($orderDetail->qty);

        //     // Check if the record exists
        //     if ($challanOrderDetail) {
        //         // Update the 'return_qty' column
        //         $challanOrderDetail->update([
        //             'remaining_qty' => $challanOrderDetail->remaining_qty - $orderDetail->qty
        //         ]);


        //         // Optionally, you can save the updated record
        //         // $challanOrderDetail->save();
        //     }


        //     // Rest of your code...
        //     // dd($challanOrderDetail);
        // }
        $returnChallan = ReturnChallan::where('id', $returnChallanId)->with('receiverUser', 'receiverDetails', 'senderUser', 'orderDetails', 'orderDetails.columns',   'statuses')->first();
        // dd($returnChallan);

        // Generate the PDF for the ReturnChallan using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateReturnChallanPDF($returnChallan);


        // $response = ['status_code' => 200, 'pdf_url' => ""];
        $response = (array) $response->getData();
        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $returnChallan->pdf_url = $response['pdf_url'];
            $returnChallan->save();
        }
        return response()->json([
            'message' => 'Return Challan created successfully.',
            'challan_id' => $returnChallanId,
            'order_detail_ids' => $orderDetailIds,
            'order_column_ids' => $orderColumnIds,
            'status_ids' => $statusIds,
            'status_code' => 200,
        ], 200);
    }

    public function importStore(Request $request)
    {
        // Validate the incoming request data
        // $validator = Validator::make($request->all(), [
        //     // 'challan_id' => 'required|exists:challans,id',
        //     'challan_series' => 'required|string',
        //     'challan_date' => 'required|date',
        //     'receiver_id' => 'required|exists:users,id',
        //     'receiver' => 'required|string',
        //     'comment' => 'nullable|string',
        //     'total' => 'numeric|min:0',
        //     'order_details.*.unit' => 'required|string',
        //     // 'order_details.*.sender_challan_id' => '',
        //     'order_details.*.rate' => 'numeric|min:0',
        //     'order_details.*.qty' => 'numeric|numeric:0',
        //     'order_details.*.total_amount' => 'numeric|min:0',
        //     'order_details.*.columns.*.column_name' => 'required|string',
        //     'order_details.*.columns.*.column_value' => 'required|string',
        //     'statuses.*.comment' => 'nullable|string',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'errors' => $validator->errors(),
        //         'status_code' => 422,
        //     ], 422);
        // }

        // Get the authenticated user
        // $user = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // // Get the latest series_num for the given challan_series and user_id
        // $latestSeriesNum = ReturnChallan::where('challan_series', $request->challan_series)
        //     ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
        //     ->max('series_num');
        // // Increment the latestSeriesNum for the new challan
        // $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

            // dd($request->challan_id);
        // Create a new ReturnChallan
        $returnChallan = new ReturnChallan([
            'challan_id' => $request->challan_id,
            'challan_series' => $request->challan_series,
            'challan_date' => $request->challan_date,
            'series_num' => $request->series_num,
            'sender_id' => $request->sender_id,
            'sender' => $request->sender,
            'receiver_id' => $request->receiver_id,
            // 'receiver_detail_id' => $request->receiver_detail_id,
            'receiver' => $request->receiver,
            'comment' => $request->comment,
            'total' => $request->total ?? 0.00,
            'total_qty' => $request->total_qty ?? 0,
            'created_at' => $request->created_at,
            'updated_at' => $request->updated_at,
        ]);
        $returnChallan->save();
        // dd($returnChallan);
        // Create ReturnChallan Order Details and their Columns
        if ($request->has('order_details')) {
            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new ReturnChallanOrderDetail([
                    'challan_id' => $returnChallan->id,
                    'sender_challan_id' => $orderDetailData['challan_id'],
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
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ?? '',
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
                    'user_id' => $request->sender_id,
                    'user_name' => $request->sender,
                    'status' => $statusData['status'],
                    'comment' => $statusData['comment'],
                    'created_at' => $request->created_at,
                    'updated_at' => $request->updated_at,
                ]);
                $status->save();
            }
        }
        // dd($returnChallan->id);
        $returnChallan = ReturnChallan::where('id', $returnChallan->id)->with('receiverUser', 'receiverDetails', 'senderUser', 'orderDetails', 'orderDetails.columns',  'statuses')->first();


        // Generate the PDF for the ReturnChallan using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateReturnChallanPDF($returnChallan);


        // $response = ['status_code' => 200, 'pdf_url' => ""];
        $response = (array) $response->getData();

        // Handle the response from PDFGenerator
        // dd($response['pdf_url']);
        // Handle the response from PDFGenerator
        // PDF generated successfully
        $returnChallan->pdf_url = $response['pdf_url'];
        // dd($returnChallan);
        $returnChallan->save();

        // Get the IDs of the created records
        $returnChallanId = $returnChallan->id;
        $orderDetailIds = $returnChallan->orderDetails->pluck('id')->toArray();
        $orderColumnIds = $returnChallan->orderDetails->flatMap->columns->pluck('id')->toArray();
        $statusIds = $returnChallan->statuses->pluck('id')->toArray();

        return true;
        // return response()->json([
        //     'message' => 'Return Challan created successfully.',
        //     'challan_id' => $returnChallanId,
        //     'order_detail_ids' => $orderDetailIds,
        //     'order_column_ids' => $orderColumnIds,
        //     'status_ids' => $statusIds,
        //     'status_code' => 200,
        // ], 200);
    }

    public function returnChallanSfpCreate(Request $request)
    {
        $teamUsers = DB::table('team_users')->whereIn('id', $request->team_user_ids)->get();

        // Fetch admins
        $admins = DB::table('users')->whereIn('id', $request->admin_ids)->get();

        // Combine team users and admins into one collection
        $receivers = $teamUsers->concat($admins);
        if ($receivers->isEmpty()) {
            return response()->json([
                'errors' => 'User not found.',
                'status_code' => 500
            ], 500);
        }

        // Fetch Challan by ID
        $challan = ReturnChallan::findOrFail($request->challan_id);
        $challan->load('statuses', 'sfp');
        $subuser = $challan->statuses[0]->team_user_name;

        foreach ($receivers as $receiver) {
            $challanSfp = new ReturnChallanSfp(
                [
                    'challan_id' => $request->challan_id,
                    'sfp_by_id' => Auth::user()->id,
                    'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
                    'sfp_to_id' => $receiver->id,
                    'sfp_to_name' => $receiver->team_user_name ?? $receiver->name,
                    'comment' => $request->comment,
                    'status' => 'sent',
                    'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
                ]
            );

            $challanSfp->save();
            $userName = $challanSfp->sfp_to_name;
            // dd($userName);
              // Send the PDF via email for SFP Challan Alert
              if ($receiver->email != null) {
                $pdfEmailService = new PDFEmailService();
                $recipientEmail = $receiver->email; // Replace with the actual recipient email address
                $pdfEmailService->returnChallanSfpByEmail($challan, $recipientEmail, $userName);

            }
        }
        return response()->json([
            'message' => 'Challan SFP successfully.',
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
            // 'order_details.*.id' => 'required|exists:challan_order_details,id',
            'order_details.*.unit' => 'nullable|string',
            'order_details.*.rate' => 'numeric|min:0',
            'order_details.*.qty' => 'numeric|numeric:0',
            // 'order_details.*.total_amount' => 'numeric|min:0',
            // 'order_details.*.columns.*.id' => 'required|exists:challan_order_columns,id',
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
        $returnChallan->total_qty = $request->input('total_qty', $returnChallan->total_qty);

        $returnChallan->challan_date = $request->input('challan_date', $returnChallan->challan_date);
        // Update other fields as needed
        $returnChallan->save();

        // Update ReturnChallan Order Details and their Columns
        if ($request->has('order_details')) {
            $ReturnChallanOrderDetail = ReturnChallanOrderDetail::where('challan_id', $id)->with('columns')->get();
            if ($ReturnChallanOrderDetail) {
                foreach ($ReturnChallanOrderDetail as $key => $value) {
                    // Delete the associated comments first
                    $ReturnChallanOrderDetail[$key]->columns()->delete();
                    $ReturnChallanOrderDetail[$key]->delete();
                }
                // Then, delete the ChallanOrderDetail itself
            }

            foreach ($request->order_details as $orderDetailData) {
                $orderDetail = new ReturnChallanOrderDetail([
                    'challan_id' => $returnChallan->id,
                    'sender_challan_id' => $orderDetailData['challan_id'],
                    'unit' => $orderDetailData['unit'],
                    'rate' => $orderDetailData['rate'] ?? 0.00,
                    'qty' => $orderDetailData['qty'] ?? 0,
                    'total_amount' => isset($orderDetailData['rate'], $orderDetailData['qty']) ? floatval($orderDetailData['rate']) * floatval($orderDetailData['qty']) : null,
                    'challan_order_detail_id' => $orderDetailData['challan_order_detail_id'],
                ]);
                $orderDetail->save();

                if (isset($orderDetailData['columns'])) {
                    foreach ($orderDetailData['columns'] as $columnData) {
                        $orderColumn = new ReturnChallanOrderColumn([
                            'challan_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ?? '',
                        ]);
                        $orderColumn->save();
                    }
                }
            }

            // foreach ($request->order_details as $orderDetailData) {
            //     $orderDetail = ReturnChallanOrderDetail::find($orderDetailData['id']);

            //     if (!$orderDetail) {
            //         return response()->json([
            //             'message' => 'Return Challan Order Detail not found.',
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
            //             $orderColumn = ReturnChallanOrderColumn::find($columnData['id']);

            //             if (!$orderColumn) {
            //                 return response()->json([
            //                     'message' => 'Return Challan Order Column not found.',
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

        // Create ReturnChallan Statuses
        if ($request->has('statuses')) {
            foreach ($request->statuses as $statusData) {
                $status = new ReturnChallanStatus([
                    'challan_id' => $returnChallan->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                   'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
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

    // Get Sender Details
    // public function getSender(Request $request)
    // {
    //     $user = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    //     $senderList = Receiver::join('challans', 'challans.receiver_id', '=', 'receivers.receiver_user_id')
    //         ->join('users', 'challans.sender_id', '=', 'users.id')
    //         ->where('receivers.receiver_user_id', '=', $user->id)
    //         ->select('users.name as sender', 'users.email', 'users.address', 'users.phone', 'users.gst_number', 'challans.sender_id', 'receivers.id')
    //         ->distinct()
    //         ->get();

    //     $responseData = [
    //         'message' => 'Sender Details.',
    //         'sender_list' => $senderList,
    //         'status_code' => 200,
    //     ];
    //     return response()->json($responseData, 200);
    // }

    public function getSender(Request $request)
    {
       $user = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // dd($user);
       $senderList = Receiver::join('challans', 'challans.receiver_id', '=', 'receivers.receiver_user_id')
       ->join('users', 'challans.sender_id', '=', 'users.id')
       ->where('receivers.receiver_user_id', '=', $user)
       ->select('users.name as sender', 'challans.sender_id', 'receivers.receiver_user_id')
       ->distinct()
       ->groupBy('challans.sender_id', 'sender', 'receivers.id')
       ->with('seriesNumber') // Group by sender_id, sender, and receiver id
       ->get();


    // dd($senderList);
       $responseData = [
           'message' => 'Sender Details.',
           'sender_list' => $senderList,
           'status_code' => 200,
       ];


       return response()->json($responseData, 200);
    }
    //     public function getSender(Request $request)
    // {
    //     $user = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;


    //     $senderList = Challan::join('receivers', function ($join) use ($user) {
    //         $join->on('challans.receiver_id', '=', 'receivers.receiver_user_id')
    //              ->where('receivers.receiver_user_id', '=', $user->id);
    //     })
    //     ->select(
    //         'challans.sender as sender',
    //         'receivers.*'
    //     )
    //     ->distinct()
    //     ->get();


    //     // dd($senderList);


    //     $responseData = [
    //         'message' => 'Sender Details.',
    //         'sender_list' => $senderList,
    //         'status_code' => 200,
    //     ];


    //     return response()->json($responseData, 200);
    // }
    public function getSenderData(Request $request, $id)
    {
        // dd($id);
        $user = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $articles = Challan::where('sender_id', $id)
            // ->leftJoin('receivers', 'challans.receiver_id', '=', 'receivers.receiver_user_id')
            ->where('receiver_id', $user)
            ->with('orderDetails', 'orderDetails.columns', 'statuses')
            ->get();
        $responseData = [
            'message' => 'Sender Article Details.',
            'article' => $articles,
            'status_code' => 200,
        ];
        return response()->json($responseData, 200);
    }

    public function getallSenderData(Request $request)
    {
        $user = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $getallSenderData = Challan::join('receivers', 'receivers.id', '=', 'challans.receiver_id')
            ->join('users', 'challans.sender_id', '=', 'users.id')
            ->where('receivers.receiver_user_id', '=', $user->id)
            ->select('*', 'challans.id')
            ->with(['statuses'])
            ->distinct()
            ->get();
        // dd($getallSenderData, $id);


        $responseData = [
            'message' => 'Sender Details.',
            'sender_list' => $getallSenderData,
            'status_code' => 200,
        ];
        // dd($responseData);
        return response()->json($responseData, 200);
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
            // 'order_details.*.id' => 'required|exists:challan_order_details,id',
            'order_details.*.unit' => 'required|string',
            'order_details.*.rate' => 'numeric|min:0',
            'order_details.*.qty' => 'numeric|numeric:0',
            'order_details.*.total_amount' => 'numeric|min:0',
            // 'order_details.*.columns.*.id' => 'required|exists:challan_order_columns,id',
            'order_details.*.columns.*.column_name' => 'string',
            'order_details.*.columns.*.column_value' => 'string',
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
            $ReturnChallanOrderDetail = ReturnChallanOrderDetail::where('challan_id', $id)->with('columns')->get();
            if ($ReturnChallanOrderDetail) {
                foreach ($ReturnChallanOrderDetail as $key => $value) {
                    // Delete the associated comments first
                    $ReturnChallanOrderDetail[$key]->columns()->delete();
                    $ReturnChallanOrderDetail[$key]->delete();
                }
                // Then, delete the ChallanOrderDetail itself
            }
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
                            'column_name' => $columnData['column_name'] ?? '',
                            'column_value' => $columnData['column_value'] ?? '',
                        ]);
                        $orderColumn->save();
                    }
                }
            }

            // foreach ($request->order_details as $orderDetailData) {
            //     $orderDetail = ReturnChallanOrderDetail::find($orderDetailData['id']);

            //     if (!$orderDetail) {
            //         return response()->json([
            //             'message' => 'Return Challan Order Detail not found.',
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
            //             $orderColumn = ReturnChallanOrderColumn::find($columnData['id']);

            //             if (!$orderColumn) {
            //                 return response()->json([
            //                     'message' => 'Return Challan Order Column not found.',
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


    // public function send(Request $request, $returnChallanId)
    // {
    //     // Find the ReturnChallan by ID
    //     // $returnChallan = ReturnChallan::findOrFail($returnChallanId);
    //     $returnChallan = ReturnChallan::where('id', $returnChallanId)->with('receiverUser', 'senderUser', 'orderDetails', 'orderDetails.columns', 'statuses')->first();
    //     $permissions = $returnChallan->senderUser->permissions ? json_decode($returnChallan->senderUser->permissions, true) : null;

    //     if (isset($permissions['receiver']['whatsapp']['sent_return_challan'])) {
    //         $sentReturnChallan = $permissions['receiver']['whatsapp']['sent_return_challan'];

    //             if ($sentReturnChallan) {
    //             // Calculate the amount to deduct (90 paisa + 18% GST)
    //             $deduction = 0.90 + (0.90 * 0.18);
    //             // Get the user's wallet
    //             $wallet = Wallet::where('user_id', Auth::id())->first();

    //             if ($wallet !== null && $wallet->balance >= $deduction) {
    //                 $pdfWhatsAppService = new PDFWhatsAppService;
    //                 $phoneNumbers = [$returnChallan->receiverUser->phone]; // Replace with the actual recipient phone number

    //                 if (!empty($returnChallan->additional_phone_number)) {
    //                     $phoneNumbers[] = $returnChallan->additional_phone_number;
    //                 }
    //                 $receiverUserEmail = $returnChallan->senderUser ? $returnChallan->senderUser->email : null;
    //                 $receiverUser = $returnChallan->receiverUser->name;
    //                 $senderUser = $returnChallan->senderUser->name;
    //                 $returnChallanNo = $returnChallan->challan_series . '-' . $returnChallan->series_num;
    //                 $returnChallanId = $returnChallan->id;
    //                 $heading = 'Return Challan';
    //                 $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendChallanOnWhatsApp($phoneNumbers, $response['pdf_url'], $challanNo, $challanId, $receiverUser, $senderUser, $heading);
    //                 if($pdfWhatsAppServiceResponse == true){
    //                     // Deduct the cost from the wallet
    //                     $wallet->balance -= $deduction;
    //                     $wallet->save();
    //                 }
    //             }
    //         }
    //     }

    //     $permissionsReceiver = $returnChallan->receiverUser->permissions ? json_decode($returnChallan->receiverUser->permissions, true) : null;
    //     // dd($permissionsReceiver);
    //     if (isset($permissionsReceiver['sender']['whatsapp']['received_return_challan'])) {
    //         $sentReturnChallan = $permissionsReceiver['sender']['whatsapp']['received_return_challan'];

    //             if ($sentReturnChallan) {
    //             // Calculate the amount to deduct (90 paisa + 18% GST)
    //             $deduction = 0.90 + (0.90 * 0.18);
    //             // Get the user's wallet
    //             $wallet = Wallet::where('user_id', $returnChallan->receiverUser->id)->first();

    //             if ($wallet !== null && $wallet->balance >= $deduction) {
    //                 $pdfWhatsAppService = new PDFWhatsAppService;
    //                 $phoneNumbers = [$returnChallan->receiverUser->phone]; // Replace with the actual recipient phone number

    //                 if (!empty($returnChallan->additional_phone_number)) {
    //                     $phoneNumbers[] = $returnChallan->additional_phone_number;
    //                 }
    //                 $receiverUserEmail = $returnChallan->senderUser ? $returnChallan->senderUser->email : null;
    //                 $receiverUser = $returnChallan->receiverUser->name;
    //                 $senderUser = $returnChallan->senderUser->name;
    //                 $returnChallanNo = $returnChallan->challan_series . '-' . $returnChallan->series_num;
    //                 $returnChallanId = $returnChallan->id;
    //                 $heading = 'Return Challan';
    //                 $pdfWhatsAppServiceResponse = $pdfWhatsAppService->sendChallanOnWhatsApp($phoneNumbers, $response['pdf_url'], $challanNo, $challanId, $receiverUser, $senderUser, $heading);
    //                 if($pdfWhatsAppServiceResponse == true){
    //                     // Deduct the cost from the wallet
    //                     $wallet->balance -= $deduction;
    //                     $wallet->save();
    //                 }
    //             }
    //         }
    //     }


    //     // Generate the PDF for the ReturnChallan using PDFGenerator class
    //     $pdfGenerator = new PDFGeneratorService();
    //     $response = $pdfGenerator->generateReturnChallanPDF($returnChallan);


    //     // $response = ['status_code' => 200, 'pdf_url' => ""];
    //     $response = (array) $response->getData();


    //     // Handle the response from PDFGenerator
    //     if ($response['status_code'] === 200) {
    //         // PDF generated successfully
    //         $returnChallan->pdf_url = $response['pdf_url'];
    //         $returnChallan->save();

    //         // Send the PDF via email
    //         if ($returnChallan->receiverUser->email != null) {
    //             $pdfEmailService = new PDFEmailService();
    //             $recipientEmail = $returnChallan->receiverUser->email; // Replace with the actual recipient email address
    //              $pdfEmailService->sendReturnChallanByEmail($returnChallan, $response['pdf_url'], $recipientEmail);
    //             // $pdfEmailService->sendReturnChallanByEmail($returnChallan, $response['pdf_url'], $recipientEmail);
    //         }

    //         // Add a new "sent" status to the ReturnChallan
    //         $status = new ReturnChallanStatus([
    //             'challan_id' => $returnChallan->id,
    //             'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
    //             'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name?? Auth::user()->team_user_name,
    //             'status' => 'sent',
    //             'comment' => 'ReturnChallan sent for acceptance',
    //         ]);
    //         $status->save();

    //         if($request->status_comment && trim($request->status_comment) != ''){
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

    //         $sfpExists = ReturnChallanSfp::where('challan_id', $returnChallanId)->exists();
    //         if ($sfpExists) {
    //             $challanSfp = new ReturnChallanSfp(
    //             [
    //                 'challan_id' => $returnChallanId,
    //                 'sfp_by_id' => Auth::user()->id,
    //                 'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
    //                 'sfp_to_id' => null,
    //                 'sfp_to_name' => $returnChallan->receiverUser->company_name ?? $returnChallan->receiverUser->name,
    //                 'status' => 'sent',
    //                 'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
    //             ]
    //         );
    //         $challanSfp->save();
    //     }

    //         // Return a response with the token and other relevant information
    //         return response()->json([
    //             'message' => 'ReturnChallan sent successfully.',
    //             'challan_id' => $returnChallan->id,
    //             // 'token' => $token,
    //             // 'token_expiry' => $status->token_expiry,
    //             'pdf_url' => $response['pdf_url'],
    //             'status_code' => 200
    //         ], 200);
    //     } else {
    //         // Error occurred during PDF generation and storage
    //         // Return an error response
    //         return response()->json([
    //             'message' => 'Error generating and storing ReturnChallan PDF.',
    //             'challan_id' => $returnChallan->id,
    //             // 'token' => $token,
    //             // 'token_expiry' => $status->token_expiry,
    //             'pdf_url' => null,
    //             'status_code' => $response['status_code']
    //         ], $response['status_code']);
    //     }
    // }
    public function send(Request $request, $returnChallanId)
    {
        // Find the ReturnChallan by ID
        $returnChallan = ReturnChallan::where('id', $returnChallanId)
            ->with('receiverUser', 'senderUser', 'orderDetails', 'orderDetails.columns', 'statuses')
            ->first();
        // dd($returnChallan);
        $permissionsSender = $returnChallan->senderUser->permissions ? json_decode($returnChallan->senderUser->permissions, true) : null;
        $permissionsReceiver = $returnChallan->receiverUser->permissions ? json_decode($returnChallan->receiverUser->permissions, true) : null;

        $deduction = 0.90 + (0.90 * 0.18); // Calculate the amount to deduct (90 paisa + 18% GST)

        $senderWallet = Wallet::where('user_id', $returnChallan->senderUser->id)->first();
        $receiverWallet = Wallet::where('user_id', $returnChallan->receiverUser->id)->first();
        // dd($senderWallet, $receiverWallet);
        $shouldSendNotification = false;

        // Check if sender has opted for the notification and has enough balance
        if (isset($permissionsSender['receiver']['whatsapp']['sent_return_challan']) && $permissionsSender['receiver']['whatsapp']['sent_return_challan']) {
            if ($senderWallet !== null && $senderWallet->balance >= $deduction) {
                $senderWallet->balance -= $deduction;
                $senderWallet->save();
                $shouldSendNotification = true;
            }
        }

        // Check if receiver has opted for the notification and has enough balance
        if (isset($permissionsReceiver['sender']['whatsapp']['received_return_challan']) && $permissionsReceiver['sender']['whatsapp']['received_return_challan']) {
            if ($receiverWallet !== null && $receiverWallet->balance >= $deduction) {
                $receiverWallet->balance -= $deduction;
                $receiverWallet->save();
                $shouldSendNotification = true;
            }
        }

        // Generate the PDF for the ReturnChallan using PDFGenerator class
        $pdfGenerator = new PDFGeneratorService();
        $response = $pdfGenerator->generateReturnChallanPDF($returnChallan);
        $response = (array) $response->getData();

        if ($response['status_code'] === 200) {
            // PDF generated successfully
            $returnChallan->pdf_url = $response['pdf_url'];
            $returnChallan->created_at = Carbon::now();
            $returnChallan->save();

            if ($shouldSendNotification) {
                $pdfWhatsAppService = new PDFWhatsAppService;
                $phoneNumbers = [$returnChallan->receiverUser->phone];

                if (!empty($returnChallan->additional_phone_number)) {
                    $phoneNumbers[] = $returnChallan->additional_phone_number;
                }

                $receiverUserEmail = $returnChallan->senderUser ? $returnChallan->senderUser->email : null;
                $receiverUser = $returnChallan->receiverUser->name;
                $senderUser = $returnChallan->senderUser->name;
                $returnChallanNo = $returnChallan->challan_series . '-' . $returnChallan->series_num;
                $returnChallanId = $returnChallan->id;
                $heading = 'Return Challan';

                $pdfWhatsAppService->sendChallanOnWhatsApp($phoneNumbers, $response['pdf_url'], $returnChallanNo, $returnChallanId, $receiverUser, $senderUser, $heading);
            }

            // Send the PDF via email
            if ($returnChallan->receiverUser->email != null) {
                $pdfEmailService = new PDFEmailService();
                $recipientEmail = $returnChallan->receiverUser->email; // Replace with the actual recipient email address
                $pdfEmailService->sendReturnChallanByEmail($returnChallan, $response['pdf_url'], $recipientEmail);
            }

            // Add a new "sent" status to the ReturnChallan
            $status = new ReturnChallanStatus([
                'challan_id' => $returnChallan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::guard(Auth::getDefaultDriver())->user()->name ?? Auth::user()->team_user_name,
                'status' => 'sent',
                'comment' => 'ReturnChallan sent for acceptance',
            ]);
            $status->save();

            if ($request->status_comment && trim($request->status_comment) != '') {
                // Get the existing status_comment data
                $statusComment = json_decode($returnChallan->status_comment, true);

                // Add the new comment to the status_comment data
                $statusComment[] = [
                    'comment' => $request->status_comment,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'name' => Auth::user()->name ?? Auth::user()->team_user_name,
                ];

                // Update the status_comment field with the combined data
                $returnChallan->update(['status_comment' => json_encode($statusComment)]);
            }

            $sfpExists = ReturnChallanSfp::where('challan_id', $returnChallanId)->exists();
            if ($sfpExists) {
                $challanSfp = new ReturnChallanSfp([
                    'challan_id' => $returnChallanId,
                    'sfp_by_id' => Auth::user()->id,
                    'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
                    'sfp_to_id' => null,
                    'sfp_to_name' => $returnChallan->receiverUser->company_name ?? $returnChallan->receiverUser->name,
                    'status' => 'sent',
                    'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
                ]);
                $challanSfp->save();
            }

            // Show Notifications in Status
            $notification = new Notification([
                'user_id' => $returnChallan->senderUser->id,
                'message' => 'New Return Challan Received by ' . $returnChallan->receiverUser->name,
                'added_id' => $returnChallan->id,
                'type' => 'return_challan',
                'panel' => 'sender',
                'template_name' => 'received_challan',
            ]);
            $notification->save();

            // Return a response with the token and other relevant information
            return response()->json([
                'message' => 'ReturnChallan sent successfully.',
                'challan_id' => $returnChallan->id,
                'pdf_url' => $response['pdf_url'],
                'status_code' => 200
            ], 200);
        } else {
            // Error occurred during PDF generation and storage
            return response()->json([
                'message' => 'Error generating and storing ReturnChallan PDF.',
                'challan_id' => $returnChallan->id,
                'pdf_url' => null,
                'status_code' => $response['status_code']
            ], $response['status_code']);
        }
    }

    public function resend(Request $request, $returnChallanId)
    {
        // Find the ReturnChallan by ID
        $returnChallan = ReturnChallan::where('id', $returnChallanId)->with('receiverUser', 'senderUser', 'orderDetails', 'orderDetails.columns', 'statuses')->first();


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


        $query = ReturnChallan::query()->orderByDesc('id');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;


        $combinedValues = [];
        if (!$request->has('sender_id') && !$request->has('receiver_id')) {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $query->where('sender_id', $userId);

            // Fetch the distinct filter values for ReturnChallan table (for this user)
            $distinctReturnChallanSeries = ReturnChallan::where('sender_id', $userId)->distinct()->pluck('challan_series');
            $distinctReturnChallanSeriesNum = ReturnChallan::where('sender_id', $userId)->distinct()->pluck('series_num');

               // Loop through each element of $distinctChallanSeries
               foreach ($distinctReturnChallanSeries as $series) {
                // Loop through each element of $distinctChallanSeriesNum
                foreach ($distinctReturnChallanSeriesNum as $num) {
                    // Combine the series and number and push it into the combinedValues array
                    $combinedValues[] = $series . '-' . $num;
                }
            }

            // dd($distinctReturnChallanSeries);
            $distinctSenderIds = ReturnChallan::where('sender_id', $userId)->distinct()->pluck('sender_id');
            $distinctReceiverIds = ReturnChallan::where('sender_id', $userId)->distinct()->pluck('receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('city');
        }

        if ($request->has('challan_series')) {
            $searchTerm = $request->challan_series;

            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                // Split the string into series and number
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                // Perform the search
                $query->where('challan_series', $series)
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

            $query->whereBetween('challan_date', [$from, $to]);
        }

        // Filter by receiver_id
        if ($request->has('receiver_id')) {
            $query->where('receiver_id', $request->receiver_id);
            // dd($query);
             // Fetch the distinct filter values for ReturnChallan table (for this user)
             $distinctReturnChallanSeries = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('challan_series');
             $distinctReturnChallanSeriesNum = ReturnChallan::where('receiver_id', $userId)->distinct()->pluck('series_num');

             $distinctSenderIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('sender_id');
             $distinctReceiverIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('receiver_id');
             // $distinctStatuses = Status::distinct()->pluck('status');

             // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
             $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                 $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
             })->distinct()->pluck('state');

             $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                 $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
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

        $perPage = $request->perPage ?? 100;
        $page = $request->page ?? 1;

        // $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails','receiverDetails.details','sfp'])->paginate(200);
        $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'receiverDetails.details', 'sfp', 'orderDetails', 'orderDetails.columns',])->select('return_challans.*')  ->paginate($perPage, ['*'], 'page', $page);

        // Calculate the starting item number for the current page
        $startItemNumber = ($page - 1) * $perPage + 1;

        // Add a custom attribute to each item in the collection with the calculated item number
        $returnChallans->each(function ($item) use (&$startItemNumber) {
         $item->setAttribute('custom_item_number', $startItemNumber++);
        });
        // dd($returnChallans );
        // return response()->json($returnChallans, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $returnChallans,
            'status_code' => 200,
            'filters' => [
                'challan_series' => $distinctReturnChallanSeries,
                'series_num' => $distinctReturnChallanSeriesNum,
                'merged_challan_series' => $combinedValues,
                'sender_id' => $distinctSenderIds,
                'receiver_id' => $distinctReceiverIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                // Add any other filter values here if needed
            ]
        ], 200);
    }
    public function indexReceivedReturnChallan(Request $request)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:


        $query = ReturnChallan::query()->orderByDesc('id');
        $combinedValues = [];
        // dd($request->has('sender_id'), $request->has('receiver_id'));
        if (!$request->has('sender_id') && !$request->has('receiver_id')) {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $query->where('receiver_id', $userId);

            // Fetch the distinct filter values for ReturnChallan table (for this user)
            $distinctReturnChallanSeries = ReturnChallan::where('receiver_id', $userId)->distinct()->pluck('challan_series');
            $distinctReturnChallanSeriesNum = ReturnChallan::where('receiver_id', $userId)->distinct()->pluck('series_num');
            // dd($distinctReturnChallanSeriesNum);
            $distinctSenderIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('sender_id');
            $distinctReceiverIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('city');

            // Loop through each element of $distinctChallanSeries
            foreach ($distinctReturnChallanSeries as $series) {
                // Loop through each element of $distinctChallanSeriesNum
                foreach ($distinctReturnChallanSeriesNum as $num) {
                    // Combine the series and number and push it into the combinedValues array
                    $combinedValues[] = $series . '-' . $num;
                }
            }

        }
        // Loop through each element of $distinctChallanSeries
        foreach ($distinctReturnChallanSeries as $series) {
            // Loop through each element of $distinctChallanSeriesNum
            foreach ($distinctReturnChallanSeriesNum as $num) {
                // Combine the series and number and push it into the combinedValues array
                $combinedValues[] = $series . '-' . $num;
            }
        }
        if ($request->has('challan_series')) {
            $searchTerm = $request->challan_series;

            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                // Split the string into series and number
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                // Perform the search
                $query->where('challan_series', $series)
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

        // Filter by receiver_id
        if ($request->has('receiver_id')) {
            $query->where('receiver_id', $request->receiver_id);
             // Fetch the distinct filter values for ReturnChallan table (for this user)
             $distinctReturnChallanSeries = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('challan_series');
             $distinctSenderIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('sender_id');
             $distinctReceiverIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('receiver_id');
             // $distinctStatuses = Status::distinct()->pluck('status');

             // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
             $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                 $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
             })->distinct()->pluck('state');

             $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                 $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
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


        // Add any other desired filters

        // $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails','receiverDetails.details','sfp'])->paginate(200);
        $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'orderDetails', 'orderDetails.columns', 'sfp'])->select('return_challans.*')->paginate(100,null,null,$request->page??1);
        // dd($returnChallans );
        // return response()->json($returnChallans, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $returnChallans,
            'status_code' => 200,
            'filters' => [
                'challan_series' => $distinctReturnChallanSeries,
                'series_num' => $distinctReturnChallanSeriesNum,
                'merged_challan_series' => $combinedValues,
                'sender_id' => $distinctSenderIds,
                'receiver_id' => $distinctReceiverIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                // 'series_num' => $distinctChallanSeriesNum,
                // Add any other filter values here if needed
            ]
        ], 200);
    }

    public function indexCounts(Request $request)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:


        $query = ReturnChallan::query()->orderByDesc('id');

        if (!$request->has('sender_id') && !$request->has('receiver_id')) {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $query->where('receiver_id', $userId);

            // Fetch the distinct filter values for ReturnChallan table (for this user)
            $distinctReturnChallanSeries = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('challan_series');
            $distinctSenderIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('sender_id');
            $distinctReceiverIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('city');
        }
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
             // Fetch the distinct filter values for ReturnChallan table (for this user)
             $distinctReturnChallanSeries = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('challan_series');
             $distinctSenderIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('sender_id');
             $distinctReceiverIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('receiver_id');
             // $distinctStatuses = Status::distinct()->pluck('status');

             // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
             $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                 $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
             })->distinct()->pluck('state');

             $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                 $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
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


        // Add any other desired filters

        // $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails','receiverDetails.details','sfp'])->paginate(200);
        $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'receiverDetails.details', 'sfp'])->select('return_challans.*')->get();

        // return response()->json($returnChallans, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $returnChallans,
            'status_code' => 200,
            'filters' => [
                'challan_series' => $distinctReturnChallanSeries,
                'sender_id' => $distinctSenderIds,
                'receiver_id' => $distinctReceiverIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                // Add any other filter values here if needed
            ]
        ], 200);
    }
    public function sidebarCounts(Request $request)
    {
        $query = ReturnChallan::query();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $count = $query->where('receiver_id', $userId)->count();

        return response()->json([
            'message' => 'Success',
            'count' => $count,
            'status_code' => 200
        ], 200);
    }
    public function returnChallanCounts(Request $request)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:


        $query = ReturnChallan::query()->orderByDesc('id');

        if (!$request->has('sender_id') && !$request->has('receiver_id')) {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $query->where('sender_id', $userId);

            // Fetch the distinct filter values for ReturnChallan table (for this user)
            $distinctReturnChallanSeries = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('challan_series');
            $distinctSenderIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('sender_id');
            $distinctReceiverIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('city');
        }
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
             // Fetch the distinct filter values for ReturnChallan table (for this user)
             $distinctReturnChallanSeries = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('challan_series');
             $distinctSenderIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('sender_id');
             $distinctReceiverIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('receiver_id');
             // $distinctStatuses = Status::distinct()->pluck('status');

             // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
             $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                 $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
             })->distinct()->pluck('state');

             $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                 $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
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


        // Add any other desired filters

        // $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails','receiverDetails.details','sfp'])->paginate(200);
        $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'receiverDetails.details', 'sfp'])->select('return_challans.*')->get();

        // return response()->json($returnChallans, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $returnChallans,
            'status_code' => 200,
            'filters' => [
                'challan_series' => $distinctReturnChallanSeries,
                'sender_id' => $distinctSenderIds,
                'receiver_id' => $distinctReceiverIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                // Add any other filter values here if needed
            ]
        ], 200);
    }


    public function sfpIndex(Request $request)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:



        $query = ReturnChallan::query()->orderByDesc('id');

        if (!$request->has('sender_id') && !$request->has('receiver_id')) {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $query->where('sender_id', $userId);

            // // Fetch the distinct filter values for ReturnChallan table (for this user)
            // $distinctReturnChallanSeries = ReturnChallan::where('sender_id', $userId)->distinct()->pluck('challan_series');
            // $distinctSenderIds = ReturnChallan::where('sender_id', $userId)->distinct()->pluck('sender_id');
            // $distinctReceiverIds = ReturnChallan::where('sender_id', $userId)->distinct()->pluck('receiver_id');
            // // $distinctStatuses = Status::distinct()->pluck('status');

            // // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            // $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            //     $query->select('id')->from('receivers')->where('user_id', $userId);
            // })->distinct()->pluck('state');

            // $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            //     $query->select('id')->from('receivers')->where('user_id', $userId);
            // })->distinct()->pluck('city');

            // Fetch the distinct filter values for ReturnChallan table (for this user)
            $distinctReturnChallanSeries = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('challan_series');
            $distinctSenderIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('sender_id');
            $distinctReceiverIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('city');
        }
        // Filter by challan_series
        if ($request->has('challan_series')) {
            $query->where('challan_series', $request->challan_series);
        }

        if ($request->has('sfp_to_id')) {
            $query->where('sfp_to_id', $request->sfp_to_id);
        }

        // Filter by sender_id
        if ($request->has('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

        // Filter by receiver_id
        if ($request->has('receiver_id')) {
            $query->where('receiver_id', $request->receiver_id);
             // Fetch the distinct filter values for ReturnChallan table (for this user)
             $distinctReturnChallanSeries = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('challan_series');
             $distinctSenderIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('sender_id');
             $distinctReceiverIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('receiver_id');
             // $distinctStatuses = Status::distinct()->pluck('status');

             // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
             $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                 $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
             })->distinct()->pluck('state');

             $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                 $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
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


        // Add any other desired filters

        // $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails','receiverDetails.details','sfp'])->paginate(20);
        $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'receiverDetails.details', 'sfp'])->select('return_challans.*')->paginate(100,null,null,$request->page??1);

        // return response()->json($returnChallans, 200);
        return response()->json([
            'message' => 'Success',
            'data' => $returnChallans,
            'status_code' => 200,
            'filters' => [
                'challan_series' => $distinctReturnChallanSeries,
                'sender_id' => $distinctSenderIds,
                'receiver_id' => $distinctReceiverIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                // Add any other filter values here if needed
            ]
        ], 200);
    }

    public function sfpAccept(Request $request, $sfpId)
    {
        try {

            $sfp = ReturnChallanSfp::where('id', $sfpId)->update(['status' => 'accept']);
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

            $sfp = ReturnChallanSfp::where('id', $sfpId)->update(['status' => 'reject']);
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

    public function show(Request $request, $id)
    {
        // Assuming you have a logged-in user, you can get the user ID like this:
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Fetch the return challan by ID (for this user) and load related data
        $returnChallan = ReturnChallan::with(['orderDetails.columns', 'statuses', 'receiverDetails', 'senderUser'])
            ->where('sender_id', $userId)
            ->find($id);

        if (!$returnChallan) {
            return response()->json([
                'data' => null,
                'message' => 'ReturnChallan not found',
                'status_code' => 200,
            ], 200);
        }

        foreach ($returnChallan->orderDetails as $returnOrderDetail) {
            $challanOrderDetail = ChallanOrderDetail::find($returnOrderDetail->challan_order_detail_id);
            $remainingQty = $challanOrderDetail->remaining_qty;
            // Use the $remainingQty as needed
            // dd($remainingQty);
        }

        // Return the response
        return response()->json([
            'message' => 'Success',
            'data' => $returnChallan,
            'status_code' => 200,
        ], 200);
    }


    // For Self Return
    public function showReturnChallan(Request $request, $receiverId)
    {
        // dd($receiverId);
        $user = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $articles = Challan::where('sender_id', $user)
            // ->leftJoin('receivers', 'challans.receiver_id', '=', 'receivers.receiver_user_id')
            ->where('receiver_id', $receiverId)
            ->where('id', $request->id)
            ->with('orderDetails', 'orderDetails.columns')
            ->get();
        $responseData = [
            'message' => 'Sender Article Details.',
            'article' => $articles,
            'status_code' => 200,
        ];
        return response()->json($responseData, 200);
    }

    public function accept(Request $request, $returnChallanId)
    {
        try {
            // Find the ReturnChallan by ID
            $returnChallan = ReturnChallan::findOrFail($returnChallanId);
            $returnChallan = $returnChallan->load('receiverUser', 'senderUser', 'orderDetails');

            if ($request->status_comment && trim($request->status_comment) != '') {
                // Get the existing status_comment data
                $statusComment = json_decode($returnChallan->status_comment, true);

                // Add the new comment to the status_comment data
                $statusComment[] = [
                    'comment' => $request->status_comment,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'name' => Auth::user()->name ?? Auth::user()->team_user_name,
                ];

                // Update the status_comment field with the combined data
                $returnChallan->update(['status_comment' => json_encode($statusComment)]);
            }

            // Check if add_stock_back is true
            if ($request->add_stock_back) {
                foreach ($returnChallan->orderDetails as $orderDetail) {
                    // Find the product by item_code
                    $product = Product::where('item_code', $orderDetail->item_code)->first();

                    if ($product) {
                        // Add the qty back to the product
                        $product->qty += $orderDetail->qty;
                        $product->save();
                    }
                }
            }

            // Update the status of the ReturnChallan to "accepted"
            $returnChallan->statuses()->create([
                'challan_id' => $returnChallan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_user_name : Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'accept',
                'comment' => 'ReturnChallan accepted',
            ]);

            // Show Notifications in Status
            $notification = new Notification([
                'user_id' => $returnChallan->senderUser->id,
                'message' => 'Challan Accepted by ' . $returnChallan->receiverUser->name,
                'type' => 'return_challan',
                'added_id' => $returnChallan->id,
                'panel' => 'receiver',
                'template_name' => 'sent_return_challan',
            ]);
            $notification->save();

            if ($returnChallan->senderUser->email != null) {
                $pdfEmailService = new PDFEmailService();
                $recipientEmail = $returnChallan->senderUser->email; // Replace with the actual recipient email address
                $pdfEmailService->acceptReturnChallanByEmail($returnChallan, $returnChallan->pdf_url, $recipientEmail);
            }

            $challanSfp = ReturnChallanSfp::firstOrCreate(
                ['challan_id' => $returnChallanId],
                [
                    'sfp_by_id' => Auth::user()->id,
                    'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
                    'sfp_to_id' => null,
                    'sfp_to_name' => null,
                    'status' => 'accept',
                    'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
                ]
            );

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
                'status' => 'accept',
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


            $returnChallan = ReturnChallan::with('orderDetails')->findOrFail($returnChallanId);
            $returnChallan = $returnChallan->load('receiverUser', 'senderUser');
            // dd($returnChallan);
            foreach ($returnChallan->orderDetails as $orderDetail) {
                // Initialize the query
                $query = ChallanOrderDetail::where('challan_id', $returnChallan->challan_id);

                // Add the challan_order_detail_id to the query if it exists
                if ($orderDetail->challan_order_detail_id) {
                    $query = $query->where('id', $orderDetail->challan_order_detail_id);
                } else {
                    // If challan_order_detail_id does not exist, use item_code or id to find the corresponding ChallanOrderDetail
                    if ($orderDetail->item_code) {
                        $query = $query->where('item_code', $orderDetail->item_code);
                    } else {
                        $query = $query->where('id', $orderDetail->id);
                    }
                }

                // Execute the query
                $challanOrderDetail = $query->first();

                if ($challanOrderDetail) {
                    // Update the 'remaining_qty' in ChallanOrderDetail
                    $challanOrderDetail->update([
                        'remaining_qty' => ($challanOrderDetail->remaining_qty ?? 0) + $orderDetail->qty,
                    ]);
                }
            }




            if($request->status_comment && trim($request->status_comment) != ''){
                // Get the existing status_comment data
                $statusComment = json_decode($returnChallan->status_comment, true);

                // Add the new comment to the status_comment data
                $statusComment[] = [
                    'comment' => $request->status_comment,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'name' => Auth::user()->name ?? Auth::user()->team_user_name,
                ];

                // Update the status_comment field with the combined data
                $returnChallan->update(['status_comment' => json_encode($statusComment)]);
            }
            // Update the status of the ReturnChallan to "rejected"
            $returnChallan->statuses()->create([
                'challan_id' => $returnChallan->id,
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'user_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_user_name : Auth::guard(Auth::getDefaultDriver())->user()->name,
                'status' => 'reject',
                'comment' => 'ReturnChallan rejected',
            ]);
             // Show Notifications in Status
             $notification = new Notification([
                'user_id' => $returnChallan->senderUser->id,
                'message' => 'Challan Accepted by ' . $returnChallan->receiverUser->name,
                'type' => 'return_challan',
                'added_id' => $returnChallan->id,
                'panel' => 'receiver',
                'template_name' => 'sent_return_challan',
            ]);
            $notification->save();

            if ($returnChallan->senderUser->email != null) {
                $pdfEmailService = new PDFEmailService();
                $recipientEmail = $returnChallan->senderUser->email; // Replace with the actual recipient email address
                 $pdfEmailService->rejectReturnChallanByEmail($returnChallan, $returnChallan->pdf_url, $recipientEmail);
                // $pdfEmailService->sendReturnChallanByEmail($returnChallan, $response['pdf_url'], $recipientEmail);
            }

            // $challanSfp = new ReturnChallanSfp(
            //     [
            //         'challan_id' => $returnChallanId,
            //         'sfp_by_id' => Auth::user()->id,
            //         'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
            //         'sfp_to_id' => null,
            //         'sfp_to_name' => null,
            //         'status' => 'reject',
            //         'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
            //     ]
            // );
            // $challanSfp->save();

            $challanSfp = ReturnChallanSfp::firstOrCreate(
                ['challan_id' => $returnChallanId],
                [
                    'sfp_by_id' => Auth::user()->id,
                    'sfp_by_name' => Auth::getDefaultDriver() == 'team-user' ? Auth::user()->team_user_name : Auth::user()->name,
                    'sfp_to_id' => null,
                    'sfp_to_name' => null,
                    'status' => 'reject',
                    'type' => Auth::getDefaultDriver() == 'team-user' ? 'team-user' : 'user',
                ]
            );
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

    // public function reject(Request $request, $returnChallanId)
    // {
    //     try {
    //         // $query = ReturnChallan::query()->orderByDesc('id');
    //         $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    //         // $returnChallans = $query->where('id', $returnChallanId)->with(['receiverUser', 'statuses', 'receiverDetails', 'receiverDetails.details', 'sfp', 'orderDetails', 'orderDetails.columns',])->get();

    //         $returnChallan = ReturnChallan::with('orderDetails', 'statuses',)->find($returnChallanId);

    //         // $status = new ReturnChallanStatus([
    //         //     'challan_id' => $returnChallan->id,
    //         //     'user_id' => $user,
    //         //     'user_name' => Auth::user()->name ?? Auth::user()->team_user_name,
    //         //     'status' => 'reject',
    //         //     'comment' => 'ReturnChallan rejected successfully',
    //         // ]);
    //         // $status->save();


    //             // Loop over the orderDetails of each ReturnChallan
    //             foreach ($returnChallan->orderDetails as $orderDetail) {
    //                 $challanOrderDetails = ChallanOrderDetail::where('challan_id', $returnChallan->challan_id)->get();
    //                 foreach ($challanOrderDetails as $challanOrderDetail) {
    //                     if ($challanOrderDetail) {
    //                         $challanOrderDetail->update([
    //                             'remaining_qty' => $challanOrderDetail->remaining_qty + $orderDetail->qty,
    //                         ]);
    //                     }
    //                 }
    //             }

    //         if ($returnChallan->senderUser->email != null) {
    //             $pdfEmailService = new PDFEmailService();
    //             $recipientEmail = $returnChallan->senderUser->email; // Replace with the actual recipient email address
    //             $pdfEmailService->rejectReturnChallanByEmail($returnChallan, $returnChallan->pdf_url, $recipientEmail);
    //         }

    //         // Return a response indicating success
    //         return response()->json([
    //             'data' => $returnChallan->statuses,
    //             'message' => 'ReturnChallan rejected successfully.',
    //             'status_code' => 200
    //         ], 200);
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return response()->json([
    //             'message' => 'ReturnChallan Not Found.',
    //             'status_code' => 400
    //         ], 400);
    //     }
    // }

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
                'status' => 'reject',
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
    public function exportReturnChallan(Request $request)
    {
        // Fetch the products and their related product details
        // $products = Challan::with('details')->get();
        $query = ReturnChallan::query()->orderByDesc('id');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails','orderDetails', 'sfp'])->select('challans.*')->paginate(100,null,null,$request->page??1);
        // $challans = $query->where('sender_id', $userId)->with('receiverUser', 'statuses', 'receiverDetails','orderDetails','orderDetails.columns')->select('challans.*')->paginate(100,null,null,$request->page??1);
        $returnChallans = $query->where('sender_id', $userId)->with(['receiverUser', 'statuses', 'receiverDetails', 'receiverDetails.details', 'sfp', 'orderDetails', 'orderDetails.columns',])->select('return_challans.*')->paginate(100,null,null,$request->page??1);
        // dd($returnChallans);


        // Create an array to store the exported data
        $exportedData = [];

        // Iterate through the products and their related product details
        foreach ($returnChallans as $key => $challan) {
            $rowData['id'] =  ++$key;

            // foreach ($challan as $productDetail) {
            //     $rowData[$productDetail->column_name] = $productDetail->column_value;
            // }
            // $rowData['Time'] = $challan->(date('h:i A', strtotime($challan->created_at)));


            $rowData['challan_series'] = $challan->challan_series;
            $rowData['Time'] = Carbon::parse($challan->created_at)->format('h:i A');
            $rowData['Date'] = Carbon::parse($challan->created_at)->format('j F Y');
            // $rowData['Date'] = $challan->(date('j F Y', strtotime($challan->created_at)));
            $rowData['sender'] = $challan->sender;
            $rowData['receiver'] = $challan->receiver;
            // $rowData['item_code'] = $challan->item_code;
            // $rowData['unit'] = $challan->unit;
            // $rowData['rate'] = $challan->rate;
            $rowData['qty'] = $challan->total_qty;
            $rowData['total_amount'] = $challan->total;
                // dd($challan->receiverDetails);

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
    // Export DEtailed Sent Challan
    public function exportDetailedReturnChallan(Request $request)
    {
        // Fetch the products and their related product details
        // $products = Challan::with('details')->get();
        $query = ReturnChallan::query()->orderByDesc('id');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails','orderDetails', 'sfp'])->select('challans.*')->paginate(100,null,null,$request->page??1);
        // $challans = $query->where('sender_id', $userId)->with('receiverUser', 'statuses', 'receiverDetails','orderDetails','orderDetails.columns')->select('challans.*')->paginate(100,null,null,$request->page??1);
        $returnChallans = $query->where('sender_id', $userId)->with(['receiverUser', 'statuses', 'receiverDetails', 'receiverDetails.details', 'sfp', 'orderDetails', 'orderDetails.columns',])->select('return_challans.*')->paginate(100,null,null,$request->page??1);
        // dd($returnChallans);

        // Create an array to store the exported data
        $exportedData = [];

        // Iterate through the products and their related product details
        foreach ($returnChallans as $key => $challan) {
            $rowData['id'] =  ++$key;
            // dd($challan->orderDetails);
            // foreach ($challan->orderDetails as $productDetail) {
            //     // $rowData[$productDetail->column_name] = $productDetail->column_value;
            //     dd($productDetail->unit);
            // }
            // $rowData['Time'] = $challan->(date('h:i A', strtotime($challan->created_at)));


            $rowData['challan_series'] = $challan->challan_series;
            $rowData['Time'] = Carbon::parse($challan->created_at)->format('h:i A');
            $rowData['Date'] = Carbon::parse($challan->created_at)->format('j F Y');
            // $rowData['Date'] = $challan->(date('j F Y', strtotime($challan->created_at)));
            $rowData['sender'] = $challan->sender;
            $rowData['receiver'] = $challan->receiver;


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
            $rowData['total_amount'] = [];

            foreach ($challan->orderDetails as $orderDetail) {
                // Create a new row for each order detail
                $detailRow = $rowData;

                // Add details to the row
                $detailRow['unit'] = $orderDetail->unit ?? '';
                $detailRow['qty'] = $orderDetail->qty ?? '';
                $detailRow['unit_price'] = $orderDetail->rate ?? '';
                $detailRow['total_amount'] = $orderDetail->total_amount ?? '';

                // Add the row to the exported data
                $exportedData[] = $detailRow;
            }
            // Convert arrays to strings
            $rowData['unit'] = implode(',', $rowData['unit']);
            $rowData['qty'] = implode(',', $rowData['qty']);
            $rowData['unit_price'] = implode(',', $rowData['unit_price']);
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

    public function addComment(Request $request, $returnChallanIds)
    {
        // dd($returnChallanIds);
        try {
            $permissions = json_decode(Auth::user()->permissions, true);
            $successCount = 0;
            $totalCount = 0;

            // Convert single ID to array if necessary
            $returnChallanIds = is_array($returnChallanIds) ? $returnChallanIds : [$returnChallanIds];

            foreach ($returnChallanIds as $returnChallanId) {
                $totalCount++;

                // Find the ReturnChallan by ID
                $returnChallan = ReturnChallan::findOrFail($returnChallanId);
                $returnChallan = $returnChallan->load('receiverUser', 'senderUser');

                // if($request->has('receiver')){
                //     $receiverUserEmail = $returnChallan->senderUser ? $returnChallan->senderUser->email : null;
                //     // dd($receiverUserEmail, 'receiver');
                //     // Show Notifications in Status
                //     $notification = new Notification([
                //         'user_id' => $returnChallan->senderUser->id,
                //         'message' => 'New Comment added by ' . $returnChallan->senderUser->name,
                //         'type' => 'return_challan',
                //         'added_id' => $returnChallan->id,
                //         'panel' => 'receiver',
                //         'template_name' => 'sent_return_challan',
                //     ]);
                //     $notification->save();
                // }
                // elseif($request->has('sender')){
                //     $receiverUserEmail = $returnChallan->receiverUser ? $returnChallan->receiverUser->email : null;
                //     // dd($receiverUserEmail, 'send');
                //     // Show Notifications in Status
                //     $notification = new Notification([
                //         'user_id' => $returnChallan->receiverUser->id,
                //         'message' => 'New Comment added by ' . $returnChallan->senderUser->name,
                //         'type' => 'challan',
                //         'added_id' => $returnChallan->id,
                //         'panel' => 'sender',
                //         'template_name' => 'received_challan',
                //     ]);
                //     $notification->save();
                // }

                if($request->has('status_comment')){
                    // Get the existing status_comment data
                    $statusComment = json_decode($returnChallan->status_comment, true) ?? [];

                    // Add the new comment to the status_comment data
                    $statusComment[] = [
                        'comment' => $request->status_comment,
                        'date' => date('Y-m-d'),
                        'time' => date('H:i:s'),
                        'name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    ];

                    // Update the status_comment field with the combined data
                    $returnChallan->update(['status_comment' => json_encode($statusComment)]);

                    // Send the PDF via email for SFP Challan Alert
                    // if ($receiverUserEmail != null) {
                    //     $pdfEmailService = new PDFEmailService();
                    //     $pdfEmailService->addCommentReturnChallanMail($returnChallan, $receiverUserEmail, $request->status_comment);
                    // }

                    $successCount++;
                }
            }

            // Prepare the response message
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
                'message' => 'One or more Return Challans Not Found.',
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
