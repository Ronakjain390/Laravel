<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Http\Controllers\V1\Buyers\BuyersController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class InsertChallanDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;
    // public $tries = 3;
    public function __construct($users)
    {
        // dd($users);
        $this->users = $users;
        // Constructor logic
        foreach ($this->users as $user) {
            // DB::transaction(function () use ($user) {
            //     try {
            // $UserAuthController = new UserAuthController();
            // $UserAuthController->importUser($user);

            // foreach ($user->receivers as $receiver) {
            //     $request = request();
            //     $request->replace([]);
            //     $recvUser = DB::table('users')->where('special_id', $receiver->special_id)->first();
            //     $request->merge([
            //         'user_id' => $user->id,
            //         'receiver_user_id' => $recvUser->id,
            //         'receiver_name' => $receiver->name,
            //         'receiver_special_id' => $receiver->special_id,

            //         'address' => $receiver->address,
            //         'pincode' => $receiver->pincode,
            //         'phone' => $receiver->phone,
            //         'gst_number' => $receiver->gst_number,
            //         'state' => $receiver->state,
            //         'city' => $receiver->city,
            //         'bank_name' => $receiver->bank_name,
            //         'branch_name' => $receiver->branch_name,
            //         'bank_account_no' => $receiver->bank_account_no,
            //         'ifsc_code' => $receiver->ifsc_code,
            //         'tan' => $receiver->tan
            //     ]);

            //     $ReceiversController = new ReceiversController();
            //     $ReceiversController->importManualReceiver($request);
            // }

            // foreach($user->sender_prefix as $prefix) {
            //     $prefix->sender = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$prefix->sender_id)->pluck('special_id')->first())->first();
            //     $prefix->receiver = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$prefix->receiver_id)->pluck('special_id')->first())->first();
            // }
            // $user->receiver_prefix = DB::connection('mysql_second')->table('receiver_prefix_numbers')->where('receiver_id',$user->id)->get();

            // foreach($user->receiver_prefix as $prefix) {
            //     $prefix->sender = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$user->id)->pluck('special_id')->first())->first();
            //     $prefix->receiver = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$prefix->receiver_id)->pluck('special_id')->first())->first();
            // }

            // $user->seller_prefix = DB::connection('mysql_second')->table('sellers_invoice_prefix_numbers')->where('seller_id',$user->id)->get();
            // foreach($user->seller_prefix as $prefix) {
            //     $prefix->seller = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$user->seller_id)->pluck('special_id')->first())->first();
            //     $prefix->buyer = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$prefix->buyer_id)->pluck('special_id')->first())->first();
            // }
            // foreach ($user->buyers as $buyer) {
            //     $request = request();
            //     $request->replace([]);
            //     $buyUser = DB::table('users')->where('special_id', $buyer->special_id)->first();
            //     $request->merge([
            //         'user_id' => $user->id,
            //         'buyer_user_id' => $buyUser->id,
            //         'buyer_name' => $buyer->name,
            //         'buyer_special_id' => $buyer->special_id,

            //         'address' => $buyer->address,
            //         'pincode' => $buyer->pincode,
            //         'phone' => $buyer->phone,
            //         'gst_number' => $buyer->gst_number,
            //         'state' => $buyer->state,
            //         'city' => $buyer->city,
            //         'bank_name' => $buyer->bank_name,
            //         'branch_name' => $buyer->branch_name,
            //         'bank_account_no' => $buyer->bank_account_no,
            //         'ifsc_code' => $buyer->ifsc_code,
            //         'tan' => $buyer->tan
            //     ]);

            //     $BuyersController = new BuyersController();
            //     $BuyersController->importManualBuyer($request);
            // }


            // foreach ($user->sender_prefix as $prefix) {
            //     $request = request();
            //     $request->replace([]);
            //     $request->merge([
            //         'series_number' => $prefix->prefix,
            //         'user_id' => isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null,
            //         'panel_id' => 1,
            //         'section_id' => 1,
            //         'assigned_to_id' => isset($prefix->receiver) ? DB::table('receivers')->where([['special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->special_id]])->pluck('id')->first() : null,
            //         'assigned_to_name' => isset($prefix->receiver) ? DB::table('receivers')->where([['special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->special_id]])->pluck('name')->first() : null,
            //         'status' => 'active',
            //         'valid_from' => $prefix->valid_from,
            //         'valid_till' => $prefix->valid_till,
            //         'default' => $prefix->receiver_id == 0 ? "1" : "0",
            //     ]);

            //     $BuyersController = new PanelSeriesNumberController();
            //     $BuyersController->importStore($request);
            // }

            // foreach ($user->receiver_prefix as $prefix) {
            //     $request = request();
            //     $request->replace([]);
            //     $request->merge([
            //         'series_number' => $prefix->prefix,
            //         'user_id' => isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null,
            //         'panel_id' => 2,
            //         'section_id' => 1,
            //         'assigned_to_id' => isset($prefix->receiver) ? DB::table('receivers')->where([['special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->special_id]])->pluck('id')->first() : null,
            //         'assigned_to_name' => isset($prefix->receiver) ? DB::table('receivers')->where([['special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->special_id]])->pluck('name')->first() : null,
            //         'status' => 'active',
            //         'valid_from' => $prefix->valid_from,
            //         'valid_till' => $prefix->valid_till,
            //         'default' => $prefix->receiver_id == 0 ? "1" : "0",
            //     ]);

            //     $BuyersController = new PanelSeriesNumberController();
            //     $BuyersController->importStore($request);
            // }

            // foreach ($user->seller_prefix as $prefix) {
            //     $request = request();
            //     $request->replace([]);
            //     $request->merge([
            //         'series_number' => $prefix->prefix,
            //         'user_id' => isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null,
            //         'panel_id' => 1,
            //         'section_id' => 2,
            //         'assigned_to_id' => isset($prefix->receiver) ? DB::table('buyers')->where([['special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->special_id]])->pluck('id')->first() : null,
            //         'assigned_to_name' => isset($prefix->receiver) ? DB::table('buyers')->where([['special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->special_id]])->pluck('name')->first() : null,
            //         'status' => 'active',
            //         'valid_from' => $prefix->valid_from,
            //         'valid_till' => $prefix->valid_till,
            //         'default' => $prefix->receiver_id == "Default" ? "1" : "0",
            //     ]);

            //     $BuyersController = new PanelSeriesNumberController();
            //     $BuyersController->importStore($request);
            // }


            foreach ($user->challans as $challan) {
                // dd($challan);

                if (isset($challan->receiver)) {
                    $request = request();
                    $request->replace([]);
                    $chDetails = [];
                    $total_qty = 0;
                    $status = 'draft';
                    $comment = 'Challan created successfully';
                    foreach ($challan->challan_details as $detail) {
                        $chColumns = [];

                        array_push($chColumns, [
                            'column_name' => 'Article',
                            'column_value' => $detail->article_name,
                        ]);
                        array_push($chColumns, [
                            'column_name' => 'Hsn',
                            'column_value' => $detail->hsn,
                        ]);
                        array_push($chColumns, [
                            'column_name' => 'Details',
                            'column_value' => $detail->details,
                        ]);

                        if ($detail->column1 != null) {
                            array_push($chColumns, [
                                'column_name' => $challan->challan_column->column1??'Column 1',
                                'column_value' => $detail->column1,
                            ]);
                        }
                        if ($detail->column2 != null) {
                            array_push($chColumns, [
                                'column_name' => $challan->challan_column->column2??'Column 2',
                                'column_value' => $detail->column2,
                            ]);
                        }
                        if ($detail->column3 != null) {
                            array_push($chColumns, [
                                'column_name' => $challan->challan_column->column3??'Column 3',
                                'column_value' => $detail->column3,
                            ]);
                        }
                        if ($detail->column4 != null) {
                            array_push($chColumns, [
                                'column_name' => $challan->challan_column->column14??'Column 4',
                                'column_value' => $detail->column4,
                            ]);
                        }
                        $total_qty = $total_qty + $detail->qty;
                        array_push($chDetails, [
                            'unit' => $detail->unit,
                            'rate' => $detail->price ?? 0.00,
                            'qty' => $detail->qty,
                            'total_amount' => $detail->total_amount ?? 0.00,
                            'columns' => $chColumns
                        ]);
                    }
                    // $buyUser = DB::table('users')->where('special_id', $buyer->special_id)->first();
                    if ($challan->status == 0 && $challan->inner_status == 0) {
                        $status = 'draft';
                        $comment = 'Challan created successfully';
                    } elseif ($challan->status == 0 && $challan->inner_status == 1) {
                        $status = 'sent';
                        $comment = 'Challan sent successfully';
                    } elseif ($challan->status == 0 && $challan->inner_status == 2) {
                        $status = 'reject';
                        $comment = 'Challan rejected successfully';
                    } elseif ($challan->status == 1) {
                        if ($challan->action_by == $challan->challan_by) {
                            $status = 'accept';
                            $comment = 'Challan self accepted successfully';
                        } else {
                            $status = 'accept';
                            $comment = 'Challan accepted successfully';
                        }
                    } elseif ($challan->status == 2) {
                        $status = 'reject';
                        $comment = 'Challan rejected successfully';
                    }

                    // dd($challan,$challan->receiver,$challan->receiver->id);
                    $request->merge([
                        'challan_series' => $challan->challan_prefix,
                        'challan_date' => $challan->created_at,
                        'series_num' => $challan->challan_num,
                        'sender_id' => $challan->sender->id,
                        'sender' => $challan->sender->name,
                        'receiver_id' => $challan->receiver->id,
                        'receiver_detail_id' => $challan->receiver->details[0]->id,
                        'receiver' => $challan->receiver->receiver_name,
                        'comment' => $challan->comment_on_behalf,
                        'total' => $challan->max_total_amount != "NaN" ? $challan->max_total_amount ?? 0.00 : 0.00,
                        'total_qty' => $total_qty ?? 0,
                        'created_at' => $challan->created_at,
                        'updated_at' => $challan->updated_at,
                        'order_details' => $chDetails,
                        'statuses' => [
                            [
                                'status' => $status,
                                'comment' => $comment,
                            ]
                        ]
                    ]);
                    // dump("chaallan");
                    // print_r(json_encode($request->all()));
                    $ChallanController = new ChallanController();
                    $ChallanController->importStore($request);
                }
            }
            // } catch (\Throwable $exception) {
            //     // Log the exception
            //     Log::channel('error')->error($exception->getMessage());
            //     throw new \Exception('Failed to send import users');
            // }
            // });
        }
        return true;
    }

    public function handle()
    {
        // foreach ($this->users as $user) {
        //     // DB::transaction(function () use ($user) {
        //     //     try {
        //     // $UserAuthController = new UserAuthController();
        //     // $UserAuthController->importUser($user);

        //     // foreach ($user->receivers as $receiver) {
        //     //     $request = request();
        //     //     $request->replace([]);
        //     //     $recvUser = DB::table('users')->where('special_id', $receiver->special_id)->first();
        //     //     $request->merge([
        //     //         'user_id' => $user->id,
        //     //         'receiver_user_id' => $recvUser->id,
        //     //         'receiver_name' => $receiver->name,
        //     //         'receiver_special_id' => $receiver->special_id,

        //     //         'address' => $receiver->address,
        //     //         'pincode' => $receiver->pincode,
        //     //         'phone' => $receiver->phone,
        //     //         'gst_number' => $receiver->gst_number,
        //     //         'state' => $receiver->state,
        //     //         'city' => $receiver->city,
        //     //         'bank_name' => $receiver->bank_name,
        //     //         'branch_name' => $receiver->branch_name,
        //     //         'bank_account_no' => $receiver->bank_account_no,
        //     //         'ifsc_code' => $receiver->ifsc_code,
        //     //         'tan' => $receiver->tan
        //     //     ]);

        //     //     $ReceiversController = new ReceiversController();
        //     //     $ReceiversController->importManualReceiver($request);
        //     // }

        //     // foreach($user->sender_prefix as $prefix) {
        //     //     $prefix->sender = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$prefix->sender_id)->pluck('special_id')->first())->first();
        //     //     $prefix->receiver = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$prefix->receiver_id)->pluck('special_id')->first())->first();
        //     // }
        //     // $user->receiver_prefix = DB::connection('mysql_second')->table('receiver_prefix_numbers')->where('receiver_id',$user->id)->get();

        //     // foreach($user->receiver_prefix as $prefix) {
        //     //     $prefix->sender = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$user->id)->pluck('special_id')->first())->first();
        //     //     $prefix->receiver = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$prefix->receiver_id)->pluck('special_id')->first())->first();
        //     // }

        //     // $user->seller_prefix = DB::connection('mysql_second')->table('sellers_invoice_prefix_numbers')->where('seller_id',$user->id)->get();
        //     // foreach($user->seller_prefix as $prefix) {
        //     //     $prefix->seller = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$user->seller_id)->pluck('special_id')->first())->first();
        //     //     $prefix->buyer = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$prefix->buyer_id)->pluck('special_id')->first())->first();
        //     // }
        //     // foreach ($user->buyers as $buyer) {
        //     //     $request = request();
        //     //     $request->replace([]);
        //     //     $buyUser = DB::table('users')->where('special_id', $buyer->special_id)->first();
        //     //     $request->merge([
        //     //         'user_id' => $user->id,
        //     //         'buyer_user_id' => $buyUser->id,
        //     //         'buyer_name' => $buyer->name,
        //     //         'buyer_special_id' => $buyer->special_id,

        //     //         'address' => $buyer->address,
        //     //         'pincode' => $buyer->pincode,
        //     //         'phone' => $buyer->phone,
        //     //         'gst_number' => $buyer->gst_number,
        //     //         'state' => $buyer->state,
        //     //         'city' => $buyer->city,
        //     //         'bank_name' => $buyer->bank_name,
        //     //         'branch_name' => $buyer->branch_name,
        //     //         'bank_account_no' => $buyer->bank_account_no,
        //     //         'ifsc_code' => $buyer->ifsc_code,
        //     //         'tan' => $buyer->tan
        //     //     ]);

        //     //     $BuyersController = new BuyersController();
        //     //     $BuyersController->importManualBuyer($request);
        //     // }


        //     // foreach ($user->sender_prefix as $prefix) {
        //     //     $request = request();
        //     //     $request->replace([]);
        //     //     $request->merge([
        //     //         'series_number' => $prefix->prefix,
        //     //         'user_id' => isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null,
        //     //         'panel_id' => 1,
        //     //         'section_id' => 1,
        //     //         'assigned_to_id' => isset($prefix->receiver) ? DB::table('receivers')->where([['special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->special_id]])->pluck('id')->first() : null,
        //     //         'assigned_to_name' => isset($prefix->receiver) ? DB::table('receivers')->where([['special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->special_id]])->pluck('name')->first() : null,
        //     //         'status' => 'active',
        //     //         'valid_from' => $prefix->valid_from,
        //     //         'valid_till' => $prefix->valid_till,
        //     //         'default' => $prefix->receiver_id == 0 ? "1" : "0",
        //     //     ]);

        //     //     $BuyersController = new PanelSeriesNumberController();
        //     //     $BuyersController->importStore($request);
        //     // }

        //     // foreach ($user->receiver_prefix as $prefix) {
        //     //     $request = request();
        //     //     $request->replace([]);
        //     //     $request->merge([
        //     //         'series_number' => $prefix->prefix,
        //     //         'user_id' => isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null,
        //     //         'panel_id' => 2,
        //     //         'section_id' => 1,
        //     //         'assigned_to_id' => isset($prefix->receiver) ? DB::table('receivers')->where([['special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->special_id]])->pluck('id')->first() : null,
        //     //         'assigned_to_name' => isset($prefix->receiver) ? DB::table('receivers')->where([['special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->special_id]])->pluck('name')->first() : null,
        //     //         'status' => 'active',
        //     //         'valid_from' => $prefix->valid_from,
        //     //         'valid_till' => $prefix->valid_till,
        //     //         'default' => $prefix->receiver_id == 0 ? "1" : "0",
        //     //     ]);

        //     //     $BuyersController = new PanelSeriesNumberController();
        //     //     $BuyersController->importStore($request);
        //     // }

        //     // foreach ($user->seller_prefix as $prefix) {
        //     //     $request = request();
        //     //     $request->replace([]);
        //     //     $request->merge([
        //     //         'series_number' => $prefix->prefix,
        //     //         'user_id' => isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null,
        //     //         'panel_id' => 1,
        //     //         'section_id' => 2,
        //     //         'assigned_to_id' => isset($prefix->receiver) ? DB::table('buyers')->where([['special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->special_id]])->pluck('id')->first() : null,
        //     //         'assigned_to_name' => isset($prefix->receiver) ? DB::table('buyers')->where([['special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->special_id]])->pluck('name')->first() : null,
        //     //         'status' => 'active',
        //     //         'valid_from' => $prefix->valid_from,
        //     //         'valid_till' => $prefix->valid_till,
        //     //         'default' => $prefix->receiver_id == "Default" ? "1" : "0",
        //     //     ]);

        //     //     $BuyersController = new PanelSeriesNumberController();
        //     //     $BuyersController->importStore($request);
        //     // }


        //     foreach ($user->challans as $challan) {
        //         dd($challan);

        //         if (isset($challan->receiver)) {
        //             $request = request();
        //             $request->replace([]);
        //             $chDetails = [];
        //             $total_qty = 0;
        //             $status = 'draft';
        //             $comment = 'Challan created successfully';
        //             foreach ($challan->challan_details as $detail) {
        //                 $chColumns = [];

        //                 array_push($chColumns, [
        //                     'column_name' => 'Article',
        //                     'column_value' => $detail->article_name,
        //                 ]);
        //                 array_push($chColumns, [
        //                     'column_name' => 'Hsn',
        //                     'column_value' => $detail->hsn,
        //                 ]);

        //                 if ($detail->column1 != null) {
        //                     array_push($chColumns, [
        //                         'column_name' => $challan->challan_column->column1??'Column 1',
        //                         'column_value' => $detail->column1,
        //                     ]);
        //                 }
        //                 if ($detail->column2 != null) {
        //                     array_push($chColumns, [
        //                         'column_name' => $challan->challan_column->column2??'Column 2',
        //                         'column_value' => $detail->column2,
        //                     ]);
        //                 }
        //                 if ($detail->column3 != null) {
        //                     array_push($chColumns, [
        //                         'column_name' => $challan->challan_column->column3??'Column 3',
        //                         'column_value' => $detail->column3,
        //                     ]);
        //                 }
        //                 if ($detail->column4 != null) {
        //                     array_push($chColumns, [
        //                         'column_name' => $challan->challan_column->column14??'Column 4',
        //                         'column_value' => $detail->column4,
        //                     ]);
        //                 }
        //                 $total_qty = $total_qty + $detail->qty;
        //                 array_push($chDetails, [
        //                     'unit' => $detail->unit,
        //                     'rate' => $detail->price ?? 0.00,
        //                     'qty' => $detail->qty,
        //                     'total_amount' => $detail->total_amount ?? 0.00,
        //                     'columns' => $chColumns
        //                 ]);
        //             }
        //             // $buyUser = DB::table('users')->where('special_id', $buyer->special_id)->first();
        //             if ($challan->status == 0 && $challan->inner_status == 0) {
        //                 $status = 'draft';
        //                 $comment = 'Challan created successfully';
        //             } elseif ($challan->status == 0 && $challan->inner_status == 1) {
        //                 $status = 'sent';
        //                 $comment = 'Challan sent successfully';
        //             } elseif ($challan->status == 0 && $challan->inner_status == 2) {
        //                 $status = 'reject';
        //                 $comment = 'Challan rejected successfully';
        //             } elseif ($challan->status == 1) {
        //                 if ($challan->action_by == $challan->challan_by) {
        //                     $status = 'accept';
        //                     $comment = 'Challan self accepted successfully';
        //                 } else {
        //                     $status = 'accept';
        //                     $comment = 'Challan accepted successfully';
        //                 }
        //             } elseif ($challan->status == 2) {
        //                 $status = 'reject';
        //                 $comment = 'Challan rejected successfully';
        //             }

        //             // dd($challan,$challan->receiver,$challan->receiver->id);
        //             $request->merge([
        //                 'challan_series' => $challan->challan_prefix,
        //                 'challan_date' => $challan->created_at,
        //                 'series_num' => $challan->challan_num,
        //                 'sender_id' => $challan->sender->id,
        //                 'sender' => $challan->sender->name,
        //                 'receiver_id' => $challan->receiver->id,
        //                 'receiver_detail_id' => $challan->receiver->details[0]->id,
        //                 'receiver' => $challan->receiver->receiver_name,
        //                 'comment' => $challan->comment_on_behalf,
        //                 'total' => $challan->max_total_amount != "NaN" ? $challan->max_total_amount ?? 0.00 : 0.00,
        //                 'total_qty' => $total_qty ?? 0,
        //                 'created_at' => $challan->created_at,
        //                 'updated_at' => $challan->updated_at,
        //                 'order_details' => $chDetails,
        //                 'statuses' => [
        //                     [
        //                         'status' => $status,
        //                         'comment' => $comment,
        //                     ]
        //                 ]
        //             ]);
        //             // dump("chaallan");
        //             // print_r(json_encode($request->all()));
        //             $ChallanController = new ChallanController();
        //             $ChallanController->importStore($request);
        //         }
        //     }
        //     // } catch (\Throwable $exception) {
        //     //     // Log the exception
        //     //     Log::channel('error')->error($exception->getMessage());
        //     //     throw new \Exception('Failed to send import users');
        //     // }
        //     // });
        // }
        // return true;
    }
}
