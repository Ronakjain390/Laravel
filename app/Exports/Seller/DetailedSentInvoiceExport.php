<?php

namespace App\Exports\Seller;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Models\InvoiceStatus;
use App\Models\ReceiverDetails;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class DetailedSentInvoiceExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $request;
    protected $index = 1;
    protected $lastSeriesNum = null;


    public function __construct(Request $request)
    {
        $this->request = $request;
        // dd($this->request);
    }

    // public function map($invoice): array
    // {
    //     $status = null;
    //     if($invoice && $invoice->statuses && $invoice->statuses->first()){
    //         $status = $invoice->statuses->first()->status;
    //     }
    //     $columns = [];
    //     if($invoice->orderDetails && $invoice->orderDetails->first() && $invoice->orderDetails->first()->columns){
    //         foreach($invoice->orderDetails->first()->columns as $index => $column){
    //             if($index < 3){
    //                 $columns[] = $column['column_value'];
    //             }
    //         }
    //     }

    //     // Fill the remaining columns if they are less than 3
    //     while(count($columns) < 3){
    //         $columns[] = null;
    //     }

    //     return array_merge([
    //     $invoice->id,
    //     $invoice->invoice_series,
    //     $invoice->created_at->format('d-m-Y'),
    //     $invoice->created_at->format('H:i:s'),
    //     $invoice->seller,
    //     $invoice->buyer,
    //     // $invoice->total_qty,
    //     $invoice->total,
    //     $status,
    //     $invoice->comment,
    // ], $columns);
    // }

    public function map($invoice): array
    {
        $status = null;
        if($invoice && $invoice->statuses && $invoice->statuses->first()){
            $status = $invoice->statuses->first()->status;
        }

        // Concatenate series_num with purchase_order_series using a hyphen
        $challanOrderSeries = $invoice->invoice_series . '-' . $invoice->series_num;

        $rows = [];
        $isFirstRow = true;

        foreach ($invoice->orderDetails as $orderDetail) {
            // Check if this is a new series_num
            if ($this->lastSeriesNum !== $invoice->series_num) {
                $indexToShow = $this->index++;
                $this->lastSeriesNum = $invoice->series_num;
            } else {
                $indexToShow = '';
            }

            // Get the first three columns
            $columns = $orderDetail->columns->take(3)->pluck('column_value')->toArray();

            // Ensure we have exactly 3 columns, even if there are less than 3
            while (count($columns) < 3) {
                $columns[] = '';
            }

            $rows[] = [
                $isFirstRow ? $indexToShow : '',  // Show index only for the first row of each invoice
                $challanOrderSeries,
                $invoice->created_at->format('d-m-Y'),
                $invoice->created_at->format('H:i:s'),
                $invoice->seller,
                $invoice->buyer,
                $columns[0],  // First column
                $columns[1],  // Second column
                $columns[2],  // Third column
                $orderDetail->unit ?? '',
                $orderDetail->qty ?? '',
                $orderDetail->rate ?? '',
                $orderDetail->total_amount ?? '',
                $status,
                $invoice->comment,
            ];

            $isFirstRow = false;  // Set to false after the first row
        }

        return $rows;
    }


    public function headings(): array
    {
        return [
            'ID',
            'Invoice Series',
            'Date',
            'Time',
            'Seller',
            'Buyer',
            'Article',
            'Hsn',
            'Detail',
            'Unit',
            'Qty',
            'Price',
            'Tax',
            'Total Amount',
            'Comment',
        ];
    }

    public function collection()
    {
        $request = $this->request;
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = Invoice::query()->where('seller_id', $userId)->orderByDesc('id');

        $isFilterApplied = false;

        // Filter by specific buyer_id if provided in the request
        if ($request->filled('buyer_id')) {
            $query->where('buyer_id', $request->buyer_id);
            $isFilterApplied = true;
        }

        // Filter by date
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = $request->from_date;
            $to = $request->to_date;
            $query->whereBetween('invoice_date', [$from, $to]);
            $isFilterApplied = true;
        }

        // Filter by invoice series
        if ($request->filled('invoice_series')) {
            $searchTerm = $request->invoice_series;
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                $query->where('invoice_series', $series)
                    ->where('series_num', $num);
            }
            $isFilterApplied = true;
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->whereHas('statuses', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
            $isFilterApplied = true;
        }

        // Filter by state
        if ($request->filled('state')) {
            $query->whereHas('buyerDetails', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
            $isFilterApplied = true;
        }

        // If no filters are applied, fetch all invoices
        if (!$isFilterApplied) {
            // No additional where clauses needed, it will fetch all invoices for the seller
        }

        $invoices = $query->with(['orderDetails', 'orderDetails.columns', 'sfp', 'statuses', 'buyerDetails'])->get();
        // dd($invoices);
        return $invoices;
    }

}
