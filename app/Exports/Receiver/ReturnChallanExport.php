<?php

namespace App\Exports\Receiver;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use App\Models\ReturnChallan;
use App\Models\ReturnChallanStatus;
use App\Models\ReceiverDetails;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


use Maatwebsite\Excel\Concerns\FromCollection;

class ReturnChallanExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        // dd($this->request);
    }
  
    public function map($challan): array
    {
        $status = null;
        if($challan && $challan->statuses && $challan->statuses->first()){
            $status = $challan->statuses->first()->status; 
        }

        return [
            $challan->id,
            $challan->challan_series,
            $challan->created_at->format('d-m-Y'),
            $challan->created_at->format('H:i:s'),
            $challan->sender,
            $challan->receiver,
            $challan->total_qty,
            $challan->total,
            $status,
            $challan->comment,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'ReturnChallan Series',
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
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = ReturnChallan::query()->where('sender_id', $userId)->orderByDesc('id');
    
        $isFilterApplied = false;
    
        // Filter by specific receiver_id if provided in the request
        if ($request->has('receiver_id') && $request->receiver_id !== null) {
            $query->where('receiver_id', $request->receiver_id);
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
            $query->whereBetween('challan_date', [$request->from_date, $request->to_date]);
            $isFilterApplied = true;
        }
    
        // Filter by challan series
        if ($request->has('challan_series') && $request->challan_series !== null) {
            // Existing logic to filter by challan series
            $isFilterApplied = true;
        }
    
        // Apply default filter if no other filters are applied
        // if (!$isFilterApplied) {
        //     $fromDate = now()->subMonth()->startOfDay(); // 1 month before, at 00:00 hours
        //     $toDate = now()->endOfDay(); // Today, at 23:59 hours
        //     $query->whereBetween('challan_date', [$fromDate, $toDate]);
        // }
    
        
        if ($request->all_data == 'all_data') {
            $challans = $query->with(['statuses', 'orderDetails', 'sfp', 'receiverDetails'])->get(); 

        } elseif ($request->filtered_data == 'filtered_data') {
             $challans = $query->with(['statuses', 'orderDetails', 'sfp', 'receiverDetails'])->get(); 
                // dd($challans);
        } elseif ($request->current_page == 'current_page') {
            $challans = $query->with(['statuses', 'orderDetails', 'sfp', 'receiverDetails'])->forPage($this->page, 50)->get();
            // $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'orderDetails', 'orderDetails.columns', 'sfp'])
            //     ->forPage($this->page, 50)->get();
                // dd($challans);
        } else {
            $challans = $query->with(['statuses', 'orderDetails', 'sfp', 'receiverDetails'])->paginate(50);
            // $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'orderDetails', 'orderDetails.columns', 'sfp'])
            //     ->select('challans.*')->paginate(25);
                // dd($challans);
        }

        // $challans = $query->with(['statuses', 'orderDetails', 'sfp', 'receiverDetails'])->get(); 
        return $challans;
    }
}
