<?php

namespace App\Exports\Exports;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Challan;
use App\Models\ChallanStatus;
use App\Models\ReceiverDetails;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


use Maatwebsite\Excel\Concerns\FromCollection;

class ChallanExport implements FromCollection, WithHeadings, WithMapping
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

    public function map($challan): array
    {
        $status = null;
        if($challan && $challan->statuses && $challan->statuses->first()){
            $status = $challan->statuses->first()->status;
        }

        // Concatenate series_num with purchase_order_series using a hyphen
        $challanOrderSeries = $challan->challan_series . '-' . $challan->series_num;

        return [
            $challan->id,
            $challanOrderSeries,
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
            'Challan Series',
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
        $query = Challan::query()->where('sender_id', $userId)->orderByDesc('id');

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


        if ($request->all_data == 'all_data') {
            $challans = $query->with(['statuses', 'orderDetails', 'sfp', 'receiverDetails'])->select('challans.*')->get();

        } elseif ($request->filtered_data == 'filtered_data') {
             $challans = $query->with(['statuses', 'orderDetails', 'sfp', 'receiverDetails'])->select('challans.*')->get();
                // dd($challans);
        } elseif ($request->current_page == 'current_page') {
            $challans = $query->with(['statuses', 'orderDetails', 'sfp', 'receiverDetails'])->select('challans.*')->forPage($this->page, 50)->get();
            // $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'orderDetails', 'orderDetails.columns', 'sfp'])
            //     ->select('challans.*')->forPage($this->page, 50)->get();
                // dd($challans);
        } else {
            $challans = $query->with(['statuses', 'orderDetails', 'sfp', 'receiverDetails'])->select('challans.*')->paginate(50);

        }
        // $challans = $query->with(['statuses', 'orderDetails', 'sfp', 'receiverDetails'])->select('challans.*')->get();
        return $challans;
    }
}
