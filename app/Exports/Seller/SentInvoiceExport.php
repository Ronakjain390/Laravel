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

class SentInvoiceExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $request;
    
  
    
    public function __construct(Request $request)
    {
        $this->request = $request;
        // dd($this->request);
    }

    public function map($invoice): array
    {
        $status = null;
        if($invoice && $invoice->statuses && $invoice->statuses->first()){
            $status = $invoice->statuses->first()->status; 
        }
        return [
            $invoice->id,
            $invoice->invoice_series,
            $invoice->created_at->format('d-m-Y'),
            $invoice->created_at->format('H:i:s'),
            $invoice->seller,
            $invoice->buyer,
            // $invoice->total_qty,
            $invoice->total,
            $status,
            $invoice->comment,
        ];
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
            'Total',
            'Status',
            'Comment',
        ];
    }

    public function collection()
    {
        // dd($this->request);
        $request = $this->request;
        // dd($request->buyer_id);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = Invoice::query()->where('seller_id', $userId)->orderByDesc('id');
        $isFilterApplied = false;
         // Filter by specific receiver_id if provided in the request
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
            $query->whereBetween('invoice_date', [$request->from_date, $request->to_date]);
            $isFilterApplied = true;
        }
        
        // Filter by challan series
        if ($request->has('invoice_series') && $request->invoice_series !== null) {
            $searchTerm = $request->invoice_series;
        
            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');
            // Split the string into series and number
            $series = substr($searchTerm, 0, $lastDashPos);
            $num = substr($searchTerm, $lastDashPos + 1);
    
            // Perform the search
            $query->where('invoice_series', $series)
                  ->where('series_num', $num);
            $isFilterApplied = true;
        }

            // $invoice = $query
            //     ->with([ 'statuses', 'orderDetails',  'sfp', 'statuses', 'buyerDetails'])
            //     ->select('invoices.*')
            //     ->get(); 
                
                if ($request->all_data == 'all_data') {
                    $invoice = $query->with([ 'statuses', 'orderDetails',  'sfp', 'statuses', 'buyerDetails'])->select('invoices.*')->get(); 
        
                } elseif ($request->filtered_data == 'filtered_data') {
                     $invoices = $query->with([ 'statuses', 'orderDetails',  'sfp', 'statuses', 'buyerDetails'])->select('invoices.*')->get(); 
                        // dd($invoice);
                } elseif ($request->current_page == 'current_page') {
                    $invoice = $query->with([ 'statuses', 'orderDetails',  'sfp', 'statuses', 'buyerDetails'])->select('invoices.*')->forPage($this->page, 50)->get();
                    // $invoice = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'orderDetails', 'orderDetails.columns', 'sfp'])
                    //     ->select('invoice.*')->forPage($this->page, 50)->get();
                        // dd($invoice);
                } else {
                    $invoice = $query->with([ 'statuses', 'orderDetails',  'sfp', 'statuses', 'buyerDetails'])->select('invoices.*')->paginate(50);
                   
                }
        return $invoice;
    }

}
