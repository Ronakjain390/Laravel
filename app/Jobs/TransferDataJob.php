<?php

namespace App\Jobs;

use App\Models\Buyer;
use App\Models\Receiver;
use Illuminate\Bus\Queueable;
use App\Jobs\InsertUserDataJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class TransferDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $timeout = 7200000;
    public function __construct()
    {
        // Constructor logic
    }

    public function handle()
    {
        $users = DB::connection('mysql_second')->table('customusers')->get();
        // ->where('phone',7042935808)
        // $users = $users->chunk(1);
        // dd($users);
        foreach ($users as $user) {
            // $user->receivers = DB::connection('mysql_second')->table('receivers')->where('receivers.sender_id', $user->id)
            //     ->join('customusers', 'customusers.id', 'receivers.receiver_id')
            //     ->select('customusers.*', 'receivers.id', 'receivers.sender_id', 'receivers.receiver_id')
            //     ->get();

            // $user->buyers = DB::connection('mysql_second')->table('buyers')->where('buyers.seller_id', $user->id)
            //     ->join('customusers', 'customusers.id', 'buyers.buyer_id')
            //     ->select('customusers.*', 'buyers.id', 'buyers.seller_id', 'buyers.buyer_id')
            //     ->get();

            // $user->sender_prefix = DB::connection('mysql_second')->table('senders_prefix_numbers')->where('sender_id',$user->id)->get();

            // foreach($user->sender_prefix as $prefix) {
            //     $prefix->sender = DB::connection('mysql_second')->table('customusers')->where('id',$prefix->sender_id)->first();
            //     $prefix->receiver = DB::connection('mysql_second')->table('customusers')->where('id',$prefix->receiver_id)->first();
            // }
            // $user->receiver_prefix = DB::connection('mysql_second')->table('receiver_prefix_numbers')->where('receiver_id',$user->id)->get();

            // foreach($user->receiver_prefix as $prefix) {
            //     $prefix->sender = DB::connection('mysql_second')->table('customusers')->where('id',$user->id)->first();
            //     $prefix->receiver = DB::connection('mysql_second')->table('customusers')->where('id',$prefix->receiver_id)->first();
            // }

            // $user->seller_prefix = DB::connection('mysql_second')->table('sellers_invoice_prefix_numbers')->where('seller_id',$user->id)->get();
            // foreach($user->seller_prefix as $prefix) {
            //     $prefix->sender = DB::connection('mysql_second')->table('customusers')->where('id',$user->seller_id)->first();
            //     $prefix->receiver = DB::connection('mysql_second')->table('customusers')->where('id',$prefix->buyer_id)->first();
            // }
            // $user->buyer_prefix = DB::connection('mysql_second')->table('sellers_invoice_prefix_numbers')->where('seller_id',$user->id)->get();

            // $user->sub_sellers = DB::connection('mysql_second')->table('sub_sellers')->where('seller_id', $user->id)->get();
// dd($user);
            // $user->challans = DB::connection('mysql_second')->table('transceiver_challans')->where('sender_id', $user->id)->get();
            // $user->challans = DB::connection('mysql_second')
            // ->table('transceiver_challans')
            // ->where('sender_id', $user->id)
            // // ->whereDate('created_at', '2024-01-14')
            // ->whereBetween('created_at', ['2024-01-15', now()])
            // ->get();
            // foreach ($user->challans as $ch) {
            //     $ch->challan_details = DB::connection('mysql_second')->table('transceiver_challan_orders')->where('trans_challan_id', $ch->trans_challan_id)->get();
            //     $ch->challan_column = DB::connection('mysql_second')->table('sender_challan_column')->where('user_id', $ch->trans_challan_id)->first();
            //     $ch->sender = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$ch->sender_id)->pluck('special_id')->first())->first();
            //     $ch->receiver = Receiver::where([['receiver_special_id',DB::connection('mysql_second')->table('customusers')->where('id',$ch->receiver_id)->pluck('special_id')->first()],['user_id',$ch->sender->id]])->with('details')->first();
            //         // dd($ch);

            // }
            // $user->return_challans = DB::connection('mysql_second')->table('transceiver_returned_challans')
            // ->where('receiver_id', $user->id)
            // // ->whereBetween('created_at', ['2024-01-14', now()])
            // ->get();
            //     // dd($user->return_challans);
            // foreach ($user->return_challans as $chr) {
            //     // dd($chr);
            //     $chr->challan_details = DB::connection('mysql_second')->table('transceiver_returned_challan_orders')->where('trans_returned_challan_id', $chr->trans_returned_challan_id)->get();
            //     $chr->sender = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$chr->sender_id)->pluck('special_id')->first())->first();
               
            //     $chr->receiver = Receiver::where([['receiver_special_id',DB::connection('mysql_second')->table('customusers')->where('id',$chr->receiver_id)->pluck('special_id')->first()]])->with('details')->first();

            //     $chr->challan_column = DB::connection('mysql_second')->table('sender_challan_column')->where('user_id', $chr->trans_returned_challan_id)->first();

            //     // dd($chr);

            // }
            // 2023-11-09
            // dd($user->return_challans);
            $user->invoices = DB::connection('mysql_second')->table('invoices')->where('seller_id', $user->id)->whereBetween('created_at', ['2023-11-09', now()])->get();

            foreach ($user->invoices as $in) {
                $in->invoice_details = DB::connection('mysql_second')->table('invoices_orders')->where('invoice_id', $in->invoice_id)->get();
                $in->seller = DB::table('users')->where('special_id',DB::connection('mysql_second')->table('customusers')->where('id',$in->seller_id)->pluck('special_id')->first())->first();
                $in->buyer = Buyer::where([['buyer_special_id',DB::connection('mysql_second')->table('customusers')->where('id',$in->buyer_id)->pluck('special_id')->first()],['user_id',$in->seller->id]])->with('details')->first();
                $in->invoice_column = DB::connection('mysql_second')->table('seller_invoice_column')->where('user_id', $in->invoice_id)->first();

            }

            $user->purchase_os = DB::connection('mysql_second')->table('p_orders')->where('buyer_id', $user->id)->get();

            foreach ($user->purchase_os as $por) {
                $por->purchase_o_details = DB::connection('mysql_second')->table('p_order_orders')->where('invoice_ord_id', $por->invoice_id)->get();
            }
        }

        $usersCollection = $users->chunk(50);
        // dd($users);

        foreach ($usersCollection as $collection) {

            // dd($collection);
            // foreach ($collection as $col) {
            //     dd($col);
            // }

            // dispatch(new InsertUserDataJob($collection));
            // $InsertUserDataJob = new InsertUserDataJob($collection);
            // $InsertUserDataJob->dispatchSync();

            // dispatch_sync(new InsertReceiverDataJob($collection));
            // dispatch_sync(new InsertBuyerDataJob($collection));

            // $InsertReceiverDataJob = new InsertReceiverDataJob($collection);
            // $InsertReceiverDataJob->dispatch();

            // $InsertBuyerDataJob = new InsertBuyerDataJob($collection);
            // $InsertBuyerDataJob->dispatch();

            // dispatch_sync(new InsertPrefixDataJob($collection));

            // $InsertPrefixDataJob = new InsertPrefixDataJob($collection);
            // $InsertPrefixDataJob->dispatch();

            // dispatch_sync(new InsertReturnChallanDataJob($users));
            // dispatch(new InsertReturnChallanDataJob($collection));

            // $InsertSubUserJob = new InsertSubUserJob($collection);

            // $InsertChallanDataJob = new InsertChallanDataJob($collection);
            // $InsertChallanDataJob->dispatch();

            // $InsertReturnChallanDataJob = new InsertReturnChallanDataJob($collection);
            // $InsertReturnChallanDataJob->dispatch();

            $InsertInvoiceDataJob = new InsertInvoiceDataJob($collection);
            // $InsertInvoiceDataJob->dispatch();

            // $InsertPoDataJob = new InsertPoDataJob($collection);
            // $InsertPoDataJob->dispatch();
        }
        // dump(count( $users));
        return true;

        // You can also add error handling and logging here
    }
}