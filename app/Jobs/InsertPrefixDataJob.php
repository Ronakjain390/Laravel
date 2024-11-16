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

class InsertPrefixDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;
    public function __construct($users)
    {
        $this->users = $users;
        foreach ($this->users as $user) {
            
            foreach ($user->sender_prefix as $prefix) {
                // dd($prefix->sender_id);
                // dd(date('Y-m-d', strtotime("0000-00-00")),
                // date('Y-m-d', strtotime($prefix->valid_till)),$prefix->created_at);
                // DB::table('receivers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->id]])->first()
                // if(isset($prefix->receiver)){
                //     dd(isset($prefix->receiver),$prefix,$prefix->receiver,DB::table('receivers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->first());
                // }
                $request = request();
                $request->replace([]);
                $request->merge([
                    'series_number' => $prefix->prefix,
                    'user_id' => isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null,
                    'panel_id' => 1,
                    'section_id' => 1,
                    'assigned_to_id' => isset($prefix->receiver) ? DB::table('receivers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->pluck('id')->first() : null,
                    'assigned_to_name' => isset($prefix->receiver) ? DB::table('receivers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->pluck('receiver_name')->first() : null,
                    'status' => 'active',
                    'valid_from' => date('Y-m-d', strtotime($prefix->valid_from == '0000-00-00' ? "2021-01-01" : $prefix->valid_from)),
                    'valid_till' => date('Y-m-d', strtotime($prefix->valid_till == '0000-00-00' ? "2021-01-01" : $prefix->valid_till)),
                    'default' => $prefix->receiver_id == 0 ? "1" : "0",
                ]);
                $BuyersController = new PanelSeriesNumberController();
                $BuyersController->importStore($request);
            }

            foreach ($user->receiver_prefix as $prefix) {
                // dd($prefix);
                $request = request();
                $request->replace([]);
                $request->merge([
                    'series_number' => $prefix->prefix,
                    'user_id' => isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null,
                    'panel_id' => 2,
                    'section_id' => 1,
                    'assigned_to_id' => isset($prefix->receiver) ? DB::table('receivers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->pluck('id')->first() : null,
                    'assigned_to_name' => isset($prefix->receiver) ? DB::table('receivers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->pluck('receiver_name')->first() : null,
                    'status' => 'active',
                    'valid_from' => date('Y-m-d', strtotime($prefix->valid_from == '0000-00-00' ? "2021-01-01" : $prefix->valid_from)),
                    'valid_till' => date('Y-m-d', strtotime($prefix->valid_till == '0000-00-00' ? "2021-01-01" : $prefix->valid_till)),
                    'default' => $prefix->receiver_id == 0 ? "1" : "0",
                ]);

                $BuyersController = new PanelSeriesNumberController();
                $BuyersController->importStore($request);
            }

            foreach ($user->seller_prefix as $prefix) {
                $request = request();
                $request->replace([]);
                if ($prefix->sender) {
                    if (DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first()) {
                        $request->merge([
                            'series_number' => $prefix->prefix,
                            'user_id' => isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null,
                            'panel_id' => 1,
                            'section_id' => 2,
                            'assigned_to_id' => isset($prefix->receiver) ? DB::table('buyers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->pluck('id')->first() : null,
                            'assigned_to_name' => isset($prefix->receiver) ? DB::table('buyers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->pluck('receiver_name')->first() : null,
                            'status' => 'active',
                            'valid_from' => date('Y-m-d', strtotime($prefix->valid_from == '0000-00-00' ? "2021-01-01" : $prefix->valid_from)),
                            'valid_till' => date('Y-m-d', strtotime($prefix->valid_till == '0000-00-00' ? "2021-01-01" : $prefix->valid_till)),
                            'default' => $prefix->buyer_id == "Default" ? "1" : "0",
                        ]);

                        $BuyersController = new PanelSeriesNumberController();
                        $BuyersController->importStore($request);
                    }
                }
            }


        }
        return true;
        // Constructor logic
    }

    public function handle()
    {
        foreach ($this->users as $user) {
            // dd($user);
            foreach ($user->sender_prefix as $prefix) {
                // dd(date('Y-m-d', strtotime("0000-00-00")),
                // date('Y-m-d', strtotime($prefix->valid_till)),$prefix->created_at);
                // DB::table('receivers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', $prefix->sender->id]])->first()
                // if(isset($prefix->receiver)){
                //     dd(isset($prefix->receiver),$prefix,$prefix->receiver,DB::table('receivers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->first());
                // }
                $request = request();
                $request->replace([]);
                $request->merge([
                    'series_number' => $prefix->prefix,
                    'user_id' => isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null,
                    'panel_id' => 1,
                    'section_id' => 1,
                    'assigned_to_id' => isset($prefix->receiver) ? DB::table('receivers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->pluck('id')->first() : null,
                    'assigned_to_name' => isset($prefix->receiver) ? DB::table('receivers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->pluck('receiver_name')->first() : null,
                    'status' => 'active',
                    'valid_from' => date('Y-m-d', strtotime($prefix->valid_from == '0000-00-00' ? "2021-01-01" : $prefix->valid_from)),
                    'valid_till' => date('Y-m-d', strtotime($prefix->valid_till == '0000-00-00' ? "2021-01-01" : $prefix->valid_till)),
                    'default' => $prefix->receiver_id == 0 ? "1" : "0",
                ]);
                $BuyersController = new PanelSeriesNumberController();
                $BuyersController->importStore($request);
            }

            foreach ($user->receiver_prefix as $prefix) {
                $request = request();
                $request->replace([]);
                $request->merge([
                    'series_number' => $prefix->prefix,
                    'user_id' => isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null,
                    'panel_id' => 2,
                    'section_id' => 1,
                    'assigned_to_id' => isset($prefix->receiver) ? DB::table('receivers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->pluck('id')->first() : null,
                    'assigned_to_name' => isset($prefix->receiver) ? DB::table('receivers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->pluck('receiver_name')->first() : null,
                    'status' => 'active',
                    'valid_from' => date('Y-m-d', strtotime($prefix->valid_from == '0000-00-00' ? "2021-01-01" : $prefix->valid_from)),
                    'valid_till' => date('Y-m-d', strtotime($prefix->valid_till == '0000-00-00' ? "2021-01-01" : $prefix->valid_till)),
                    'default' => $prefix->receiver_id == 0 ? "1" : "0",
                ]);

                $BuyersController = new PanelSeriesNumberController();
                $BuyersController->importStore($request);
            }

            foreach ($user->seller_prefix as $prefix) {
                $request = request();
                $request->replace([]);
                if ($prefix->sender) {
                    if (DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first()) {
                        $request->merge([
                            'series_number' => $prefix->prefix,
                            'user_id' => isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null,
                            'panel_id' => 1,
                            'section_id' => 2,
                            'assigned_to_id' => isset($prefix->receiver) ? DB::table('buyers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->pluck('id')->first() : null,
                            'assigned_to_name' => isset($prefix->receiver) ? DB::table('buyers')->where([['receiver_special_id', $prefix->receiver->special_id], ['user_id', DB::table('users')->where('special_id',$prefix->sender->special_id)->pluck('id')->first()]])->pluck('receiver_name')->first() : null,
                            'status' => 'active',
                            'valid_from' => date('Y-m-d', strtotime($prefix->valid_from == '0000-00-00' ? "2021-01-01" : $prefix->valid_from)),
                            'valid_till' => date('Y-m-d', strtotime($prefix->valid_till == '0000-00-00' ? "2021-01-01" : $prefix->valid_till)),
                            'default' => $prefix->buyer_id == "Default" ? "1" : "0",
                        ]);

                        $BuyersController = new PanelSeriesNumberController();
                        $BuyersController->importStore($request);
                    }
                }
            }


        }
        return true;

    }
}
