<?php

namespace App\Exports\Buyer;

use App\Models\PurchaseOrder;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PurchaseOrderExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function map($purchaseOrder): array
    {
        $status = null;
        if($purchaseOrder && $purchaseOrder->statuses && $purchaseOrder->statuses->first()){
            $status = $purchaseOrder->statuses->first()->status;
        }
        // Concatenate series_num with purchase_order_series using a hyphen
        $purchaseOrderSeries = $purchaseOrder->purchase_order_series . '-' . $purchaseOrder->series_num;


        return [
            $purchaseOrder->id,
            $purchaseOrderSeries,
            $purchaseOrder->created_at->format('d-m-Y'),
            $purchaseOrder->created_at->format('H:i:s'),
            $purchaseOrder->sender,
            $purchaseOrder->receiver,
            $purchaseOrder->total_qty,
            $purchaseOrder->total,
            $status,
            $purchaseOrder->comment,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'PurchaseOrder Series',
            'Date',
            'Time',
            'Sender',
            'Receiver',
            'Total Qty',
            'Total',
            'Status',
            'Comment',
        ];
    }


    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        $request = $this->request;
        // dd($request);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = PurchaseOrder::query()->where('seller_id', $userId)->orderByDesc('id');

        $isFilterApplied = false;

        // Filter by specific buyer_id if provided in the request
        if ($request->has('buyer_id') && $request->buyer_id !== null) {
            $query->where('buyer_id', $request->buyer_id);
            $isFilterApplied = true;
        }

        // Filter by status
        if ($request->has('status') && $request->status !== null) {
            $query->whereHas('statuses', function ($q) use ($request) {
                $q->latest()->where('status', $request->status);
            });
            $isFilterApplied = true;
        }

        // Filter by date
        if ($request->has('from_date') && $request->from_date !== null && $request->has('to_date') && $request->to_date !== null) {
            $query->whereBetween('order_date', [$request->from_date, $request->to_date]);
            $isFilterApplied = true;
        }

        // Filter by challan series
        if ($request->has('purchase_order_series') && $request->purchase_order_series !== null) {
            // Existing logic to filter by challan series
            $isFilterApplied = true;
        }


        if ($request->all_data == 'all_data') {
            $purchaseOrders = $query->with(['statuses', 'orderDetails', 'sfp', 'buyerDetails'])->select('purchase_orders.*')->get();

        } elseif ($request->filtered_data == 'filtered_data') {
             $purchaseOrders = $query->with(['statuses', 'orderDetails', 'sfp', 'buyerDetails'])->select('purchase_orders.*')->get();
                // dd($purchaseOrders);
        } elseif ($request->current_page == 'current_page') {
            $purchaseOrders = $query->with(['statuses', 'orderDetails', 'sfp', 'buyerDetails'])->select('purchase_orders.*')->forPage($this->page, 50)->get();
            // $purchaseOrders = $query->with(['receiverUser', 'statuses', 'buyerDetails', 'orderDetails', 'orderDetails.columns', 'sfp'])
            //     ->select('purchase_orders.*')->forPage($this->page, 50)->get();
                // dd($purchaseOrders);
        } else {
            $purchaseOrders = $query->with(['statuses', 'orderDetails', 'sfp', 'buyerDetails'])->select('purchase_orders.*')->paginate(50);

        }
        // $purchaseOrders = $query->with(['statuses', 'orderDetails', 'sfp', 'buyerDetails'])->select('purchase_orders.*')->get();
        return $purchaseOrders;
    }
}
