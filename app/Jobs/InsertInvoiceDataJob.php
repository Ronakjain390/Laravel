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
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class InsertInvoiceDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;
    public function __construct($users)
    {
        $this->users = $users;
        foreach($this->users as $user){
            // dd($user);

            foreach ($user->invoices as $invoice) {
                // dd($invoice);

                if (isset($invoice->buyer)) {
                    $request = request();
                    $request->replace([]);
                    $chDetails = [];
                    $total_qty = 0;
                    $status = 'draft';
                    $comment = 'Invoice created successfully';
                    foreach ($invoice->invoice_details as $detail) {
                        $chColumns = [];

                        array_push($chColumns, [
                            'column_name' => 'Article',
                            'column_value' => $detail->article_name,
                        ]);
                        array_push($chColumns, [
                            'column_name' => 'Hsn',
                            'column_value' => $detail->hsn,
                        ]);

                        if ($detail->column1 != null) {
                            array_push($chColumns, [
                                'column_name' => $invoice->invoice_column->column1??'Column 1',
                                'column_value' => $detail->column1,
                            ]);
                        }
                        if ($detail->column2 != null) {
                            array_push($chColumns, [
                                'column_name' => $invoice->invoice_column->column2??'Column 2',
                                'column_value' => $detail->column2,
                            ]);
                        }
                        if ($detail->column3 != null) {
                            array_push($chColumns, [
                                'column_name' => $invoice->invoice_column->column3??'Column 3',
                                'column_value' => $detail->column3,
                            ]);
                        }
                        if ($detail->column4 != null) {
                            array_push($chColumns, [
                                'column_name' => $invoice->invoice_column->column14??'Column 4',
                                'column_value' => $detail->column4,
                            ]);
                        }
                        $total_qty = $total_qty + $detail->qty;
                        array_push($chDetails, [
                            'unit' => $detail->unit,
                            'rate' => $detail->price ?? 0.00,
                            'tax' => $detail->tax ?? 0, 
                            'qty' => $detail->qty,
                            'total_amount' => $detail->total_amount ?? 0.00,
                            'columns' => $chColumns
                        ]);
                    }
                    // $buyUser = DB::table('users')->where('special_id', $buyer->special_id)->first();
                    if ($invoice->status == 0 && $invoice->inner_status == 0) {
                        $status = 'draft';
                        $comment = 'Invoice created successfully';
                    } elseif ($invoice->status == 0 && $invoice->inner_status == 1) {
                        $status = 'sent';
                        $comment = 'Invoice sent successfully';
                    } elseif ($invoice->status == 0 && $invoice->inner_status == 2) {
                        $status = 'reject';
                        $comment = 'Invoice rejected successfully';
                    } elseif ($invoice->status == 1) {
                        if ($invoice->action_by == $invoice->invoice_by) {
                            $status = 'accept';
                            $comment = 'Invoice self accepted successfully';
                        } else {
                            $status = 'accept';
                            $comment = 'Invoice accepted successfully';
                        }
                    } elseif ($invoice->status == 2) {
                        $status = 'reject';
                        $comment = 'Invoice rejected successfully';
                    }

                    // dd($invoice,$invoice->buyer,$invoice->buyer->id);
                    $request->merge([
                        'invoice_series' => $invoice->invoice_prefix,
                        'invoice_date' => $invoice->created_at,
                        'series_num' => $invoice->invoice_num,
                        'seller_id' => $invoice->seller->id,
                        'seller' => $invoice->seller->name,
                        'buyer_id' => $invoice->buyer->id,
                        'buyer_detail_id' => $invoice->buyer->details[0]->id,
                        'buyer' => $invoice->buyer->buyer_name,
                        'comment' => $invoice->comment_on_behalf,
                        'total' => $invoice->max_total_amount != "NaN" ? $invoice->max_total_amount ?? 0.00 : 0.00,
                        'total_qty' => $total_qty ?? 0,
                        'created_at' => $invoice->created_at,
                        'updated_at' => $invoice->updated_at,
                        'order_details' => $chDetails,
                        'statuses' => [
                            [
                                'status' => trim($status),
                                'comment' => $comment,
                            ]
                        ]
                    ]);
                    // dump("chaallan");
                    // print_r(json_encode($request->all()));
                    $InvoiceController = new InvoiceController();
                    $InvoiceController->importStore($request);
                }
            }
        }
        return true;
        // Constructor logic
    }

    public function handle()
    {
        // foreach ($this->users as $user) {
        //     DB::transaction(function () use ($user) {
        //         try {
        //             $UserAuthController = new UserAuthController();
        //             $UserAuthController->importUser($user);

        //             foreach ($user->buyers as $buyer) {
        //                 $request = request();
        //                 $request->replace([]);
        //                 $recvUser = DB::table('users')->where('special_id', $buyer->special_id)->first();
        //                 $request->merge([
        //                     'user_id' => $user->id,
        //                     'buyer_user_id' => $recvUser->id,
        //                     'buyer_name' => $buyer->name,
        //                     'buyer_special_id' => $buyer->special_id,

        //                     'address' => $buyer->address,
        //                     'pincode' => $buyer->pincode,
        //                     'phone' => $buyer->phone,
        //                     'gst_number' => $buyer->gst_number,
        //                     'state' => $buyer->state,
        //                     'city' => $buyer->city,
        //                     'bank_name' => $buyer->bank_name,
        //                     'branch_name' => $buyer->branch_name,
        //                     'bank_account_no' => $buyer->bank_account_no,
        //                     'ifsc_code' => $buyer->ifsc_code,
        //                     'tan' => $buyer->tan
        //                 ]);

        //                 $BuyersController = new BuyersController();
        //                 $BuyersController->importManualBuyer($request);
        //             }

        //             // foreach($user->seller_prefix as $prefix) {
        //             //     $prefix->seller = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$prefix->seller_id)->pluck('special_id')->first())->first();
        //             //     $prefix->buyer = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$prefix->buyer_id)->pluck('special_id')->first())->first();
        //             // }
        //             // $user->buyer_prefix = DB::connection('mysql_second')->table('buyer_prefix_numbers')->where('buyer_id',$user->id)->get();

        //             // foreach($user->buyer_prefix as $prefix) {
        //             //     $prefix->seller = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$user->id)->pluck('special_id')->first())->first();
        //             //     $prefix->buyer = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$prefix->buyer_id)->pluck('special_id')->first())->first();
        //             // }

        //             // $user->seller_prefix = DB::connection('mysql_second')->table('sellers_invoice_prefix_numbers')->where('seller_id',$user->id)->get();
        //             // foreach($user->seller_prefix as $prefix) {
        //             //     $prefix->seller = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$user->seller_id)->pluck('special_id')->first())->first();
        //             //     $prefix->buyer = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$prefix->buyer_id)->pluck('special_id')->first())->first();
        //             // }
        //             foreach ($user->buyers as $buyer) {
        //                 $request = request();
        //                 $request->replace([]);
        //                 $buyUser = DB::table('users')->where('special_id', $buyer->special_id)->first();
        //                 $request->merge([
        //                     'user_id' => $user->id,
        //                     'buyer_user_id' => $buyUser->id,
        //                     'buyer_name' => $buyer->name,
        //                     'buyer_special_id' => $buyer->special_id,

        //                     'address' => $buyer->address,
        //                     'pincode' => $buyer->pincode,
        //                     'phone' => $buyer->phone,
        //                     'gst_number' => $buyer->gst_number,
        //                     'state' => $buyer->state,
        //                     'city' => $buyer->city,
        //                     'bank_name' => $buyer->bank_name,
        //                     'branch_name' => $buyer->branch_name,
        //                     'bank_account_no' => $buyer->bank_account_no,
        //                     'ifsc_code' => $buyer->ifsc_code,
        //                     'tan' => $buyer->tan
        //                 ]);

        //                 $BuyersController = new BuyersController();
        //                 $BuyersController->importManualBuyer($request);
        //             }


        //             foreach ($user->seller_prefix as $prefix) {
        //                 $request = request();
        //                 $request->replace([]);
        //                 $request->merge([
        //                     'series_number' => $prefix->prefix,
        //                     'user_id' => isset($prefix->seller) ? DB::table('users')->where('special_id', $prefix->seller->special_id)->pluck('id')->first() : null,
        //                     'panel_id' => 1,
        //                     'section_id' => 1,
        //                     'assigned_to_id' => isset($prefix->buyer) ? DB::table('buyers')->where([['special_id', $prefix->buyer->special_id], ['user_id', $prefix->seller->special_id]])->pluck('id')->first() : null,
        //                     'assigned_to_name' => isset($prefix->buyer) ? DB::table('buyers')->where([['special_id', $prefix->buyer->special_id], ['user_id', $prefix->seller->special_id]])->pluck('name')->first() : null,
        //                     'status' => 'active',
        //                     'valid_from' => $prefix->valid_from,
        //                     'valid_till' => $prefix->valid_till,
        //                     'default' => $prefix->buyer_id == 0 ? "1" : "0",
        //                 ]);

        //                 $BuyersController = new PanelSeriesNumberController();
        //                 $BuyersController->importStore($request);
        //             }

        //             foreach ($user->buyer_prefix as $prefix) {
        //                 $request = request();
        //                 $request->replace([]);
        //                 $request->merge([
        //                     'series_number' => $prefix->prefix,
        //                     'user_id' => isset($prefix->seller) ? DB::table('users')->where('special_id', $prefix->seller->special_id)->pluck('id')->first() : null,
        //                     'panel_id' => 2,
        //                     'section_id' => 1,
        //                     'assigned_to_id' => isset($prefix->buyer) ? DB::table('buyers')->where([['special_id', $prefix->buyer->special_id], ['user_id', $prefix->seller->special_id]])->pluck('id')->first() : null,
        //                     'assigned_to_name' => isset($prefix->buyer) ? DB::table('buyers')->where([['special_id', $prefix->buyer->special_id], ['user_id', $prefix->seller->special_id]])->pluck('name')->first() : null,
        //                     'status' => 'active',
        //                     'valid_from' => $prefix->valid_from,
        //                     'valid_till' => $prefix->valid_till,
        //                     'default' => $prefix->buyer_id == 0 ? "1" : "0",
        //                 ]);

        //                 $BuyersController = new PanelSeriesNumberController();
        //                 $BuyersController->importStore($request);
        //             }

        //             foreach ($user->seller_prefix as $prefix) {
        //                 $request = request();
        //                 $request->replace([]);
        //                 $request->merge([
        //                     'series_number' => $prefix->prefix,
        //                     'user_id' => isset($prefix->seller) ? DB::table('users')->where('special_id', $prefix->seller->special_id)->pluck('id')->first() : null,
        //                     'panel_id' => 1,
        //                     'section_id' => 2,
        //                     'assigned_to_id' => isset($prefix->buyer) ? DB::table('buyers')->where([['special_id', $prefix->buyer->special_id], ['user_id', $prefix->seller->special_id]])->pluck('id')->first() : null,
        //                     'assigned_to_name' => isset($prefix->buyer) ? DB::table('buyers')->where([['special_id', $prefix->buyer->special_id], ['user_id', $prefix->seller->special_id]])->pluck('name')->first() : null,
        //                     'status' => 'active',
        //                     'valid_from' => $prefix->valid_from,
        //                     'valid_till' => $prefix->valid_till,
        //                     'default' => $prefix->buyer_id == "Default" ? "1" : "0",
        //                 ]);

        //                 $BuyersController = new PanelSeriesNumberController();
        //                 $BuyersController->importStore($request);
        //             }


        //             foreach ($user->invoices as $invoice) {
        //                 $request = request();
        //                 $request->replace([]);
        //                 $buyUser = DB::table('users')->where('special_id', $buyer->special_id)->first();
        //                 $request->merge([
        //                     'invoice_series' => '',
        //                     'invoice_date' => '',
        //                     'series_num' => '',
        //                     'seller_id' => '',
        //                     'seller' => '',
        //                     'buyer_id' => '',
        //                     'buyer_detail_id' => '',
        //                     'buyer' => '',
        //                     'comment' => '',
        //                     'total' => '',
        //                     'total_qty' => '',
        //                     'order_details' => [
        //                         'invoice_id' => '',
        //                         'unit' => '',
        //                         'rate' => '',
        //                         'qty' => '',
        //                         'total_amount' => '',
        //                         'columns' => [
        //                             'invoice_order_detail_id' => '',
        //                             'column_name' => '',
        //                             'column_value' => '',
        //                         ]
        //                     ]
        //                 ]);

        //                 $InvoiceController = new InvoiceController();
        //                 $InvoiceController->importManualBuyer($request);
        //             }
        //         } catch (\Throwable $exception) {
        //             // Log the exception
        //             Log::channel('error')->error($exception->getMessage());
        //             throw new \Exception('Failed to send import users');
        //         }
        //     });
        // }
    }
}
