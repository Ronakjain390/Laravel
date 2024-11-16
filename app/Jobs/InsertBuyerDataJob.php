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
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Receivers\ReceiversController;

class InsertBuyerDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;
    public function __construct($users)
    {
        $this->users = $users;
        foreach ($this->users as $user) {
            // dd($users);
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
                    foreach ($user->buyers as $buyer) {
                        $request = request();
                        $request->replace([]);
                        $mainUser = DB::table('users')->where('special_id', $user->special_id)->first();
                        $buyUser = DB::table('users')->where('special_id', $buyer->special_id)->first();
                        $request->merge([
                            'user_id' => $mainUser->id,
                            'buyer_user_id' => $buyUser->id,
                            'buyer_name' => $buyer->name??$buyUser->name,
                            'buyer_special_id' => $buyer->special_id,

                            'address' => $buyer->address,
                            'pincode' => $buyer->pincode,
                            'phone' => $buyer->phone,
                            'gst_number' => $buyer->gst_number,
                            'state' => $buyer->state,
                            'city' => $buyer->city,
                            'bank_name' => $buyer->bank_name,
                            'branch_name' => $buyer->branch_name,
                            'bank_account_no' => $buyer->bank_account_no,
                            'ifsc_code' => $buyer->ifsc_code,
                            'tan' => $buyer->tan
                        ]);

                        $BuyersController = new BuyersController();
                        $BuyersController->importManualBuyer($request);
                    }


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


                    // foreach ($user->challans as $challan) {
                    //     $request = request();
                    //     $request->replace([]);
                    //     $buyUser = DB::table('users')->where('special_id', $buyer->special_id)->first();
                    //     $request->merge([
                    //         'challan_series' => '',
                    //         'challan_date' => '',
                    //         'series_num' => '',
                    //         'sender_id' => '',
                    //         'sender' => '',
                    //         'receiver_id' => '',
                    //         'receiver_detail_id' => '',
                    //         'receiver' => '',
                    //         'comment' => '',
                    //         'total' => '',
                    //         'total_qty' => '',
                    //         'order_details' => [
                    //             'challan_id' => '',
                    //             'unit' => '',
                    //             'rate' => '',
                    //             'qty' => '',
                    //             'total_amount' => '',
                    //             'columns' => [
                    //                 'challan_order_detail_id' => '',
                    //                 'column_name' => '',
                    //                 'column_value' => '',
                    //             ]
                    //         ]
                    //     ]);

                    //     $ChallanController = new ChallanController();
                    //     $ChallanController->importManualBuyer($request);
                    // }
            //     } catch (\Throwable $exception) {
            //         // Log the exception
            //         Log::channel('error')->error($exception->getMessage());
            //         throw new \Exception('Failed to send import users');
            //     }
            // });
        }
        return true;

        // Constructor logic
    }

    public function handle()
    {
        foreach ($this->users as $user) {
            dd($users);
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
                    foreach ($user->buyers as $buyer) {
                        $request = request();
                        $request->replace([]);
                        $mainUser = DB::table('users')->where('special_id', $user->special_id)->first();
                        $buyUser = DB::table('users')->where('special_id', $buyer->special_id)->first();
                        $request->merge([
                            'user_id' => $mainUser->id,
                            'buyer_user_id' => $buyUser->id,
                            'buyer_name' => $buyer->name??$buyUser->name,
                            'buyer_special_id' => $buyer->special_id,

                            'address' => $buyer->address,
                            'pincode' => $buyer->pincode,
                            'phone' => $buyer->phone,
                            'gst_number' => $buyer->gst_number,
                            'state' => $buyer->state,
                            'city' => $buyer->city,
                            'bank_name' => $buyer->bank_name,
                            'branch_name' => $buyer->branch_name,
                            'bank_account_no' => $buyer->bank_account_no,
                            'ifsc_code' => $buyer->ifsc_code,
                            'tan' => $buyer->tan
                        ]);

                        $BuyersController = new BuyersController();
                        $BuyersController->importManualBuyer($request);
                    }


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


                    // foreach ($user->challans as $challan) {
                    //     $request = request();
                    //     $request->replace([]);
                    //     $buyUser = DB::table('users')->where('special_id', $buyer->special_id)->first();
                    //     $request->merge([
                    //         'challan_series' => '',
                    //         'challan_date' => '',
                    //         'series_num' => '',
                    //         'sender_id' => '',
                    //         'sender' => '',
                    //         'receiver_id' => '',
                    //         'receiver_detail_id' => '',
                    //         'receiver' => '',
                    //         'comment' => '',
                    //         'total' => '',
                    //         'total_qty' => '',
                    //         'order_details' => [
                    //             'challan_id' => '',
                    //             'unit' => '',
                    //             'rate' => '',
                    //             'qty' => '',
                    //             'total_amount' => '',
                    //             'columns' => [
                    //                 'challan_order_detail_id' => '',
                    //                 'column_name' => '',
                    //                 'column_value' => '',
                    //             ]
                    //         ]
                    //     ]);

                    //     $ChallanController = new ChallanController();
                    //     $ChallanController->importManualBuyer($request);
                    // }
            //     } catch (\Throwable $exception) {
            //         // Log the exception
            //         Log::channel('error')->error($exception->getMessage());
            //         throw new \Exception('Failed to send import users');
            //     }
            // });
        }
        return true;

    }
}
