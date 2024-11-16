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

class InsertReceiverDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;
    public function __construct($users)
    {
        $this->users = $users;
        // Constructor logic
    }

    public function handle()
    {
        foreach ($this->users as $user) {
// dd($user);
            // DB::transaction(function () use ($user) {
            //     try {
                    // $UserAuthController = new UserAuthController();
                    // $UserAuthController->importUser($user);
                    foreach ($user->receivers as $receiver) {
                        $request = request();
                        $request->replace([]);
                        $mainUser = DB::table('users')->where('special_id', $user->special_id)->first();
                        $recvUser = DB::table('users')->where('special_id', $receiver->special_id)->first();
                        $request->merge([
                            'user_id' => $mainUser->id,
                            'receiver_user_id' => $recvUser->id,
                            'receiver_name' => $receiver->name??$recvUser->name,
                            'receiver_special_id' => $receiver->special_id,

                            'address' => $receiver->address,
                            'pincode' => $receiver->pincode,
                            'phone' => $receiver->phone,
                            'gst_number' => $receiver->gst_number,
                            'state' => $receiver->state,
                            'city' => $receiver->city,
                            'bank_name' => $receiver->bank_name,
                            'branch_name' => $receiver->branch_name,
                            'bank_account_no' => $receiver->bank_account_no,
                            'ifsc_code' => $receiver->ifsc_code,
                            'tan' => $receiver->tan
                        ]);
                        // print_r($request);
                        $ReceiversController = new ReceiversController();
                        $ReceiversController->importManualReceiver($request);
                    }

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
