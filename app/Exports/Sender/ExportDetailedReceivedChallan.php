<?php

namespace App\Exports\Sender;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReturnChallan;
use App\Models\ChallanStatus;
use App\Models\ReceiverDetails;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportDetailedReceivedChallan implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $request;
    protected $index = 1;
    protected $lastSeriesNum = null;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function map($challan): array
    {
        $status = null;
        if($challan && $challan->statuses && $challan->statuses->first()){
            $status = $challan->statuses->first()->status;
        }

        // Concatenate series_num with purchase_order_series using a hyphen
        $challanOrderSeries = $challan->challan_series . '-' . $challan->series_num;

        $rows = [];
        $isFirstRow = true;

        foreach ($challan->orderDetails as $orderDetail) {
            // Check if this is a new series_num
            if ($this->lastSeriesNum !== $challan->series_num) {
                $indexToShow = $this->index++;
                $this->lastSeriesNum = $challan->series_num;
            } else {
                $indexToShow = '';
            }

            $rows[] = [
                $isFirstRow ? $indexToShow : '',  // Show index only for the first row of each challan
                $challanOrderSeries,
                $challan->created_at->format('d-m-Y'),
                $challan->created_at->format('H:i:s'),
                $challan->sender,
                $challan->receiver,
                $orderDetail->unit ?? '',
                $orderDetail->qty ?? '',
                $orderDetail->rate ?? '',
                $orderDetail->total_amount ?? '',
                $status,
                $challan->comment,
            ];

            $isFirstRow = false;  // Set to false after the first row
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'S.No.',
            'Challan Series',
            'Date',
            'Time',
            'Sender',
            'Receiver',
            'Unit',
            'Qty',
            'Unit Price',
            'Total Amount',
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
        $query = ReturnChallan::query()->where('receiver_id', $userId)->orderByDesc('id');
        // dd($query);
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
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('challan_date', [$request->from_date, $request->to_date]);
            $isFilterApplied = true;
        }

        // Filter by challan series
        if ($request->challan_series !== null) {
            // Existing logic to filter by challan series
            $searchTerm = $request->challan_series;
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                $query->where('challan_series', $series)
                    ->where('series_num', $num);
            }
            $isFilterApplied = true;
        }

        // Apply default filter if no other filters are applied
        if (!$isFilterApplied) {
            $fromDate = now()->subMonth()->startOfDay(); // 1 month before, at 00:00 hours
            $toDate = now()->endOfDay(); // Today, at 23:59 hours
            $query->whereBetween('challan_date', [$fromDate, $toDate]);
        }

        $challans = $query->with(['statuses', 'orderDetails', 'sfp', 'receiverDetails'])->get();
        // dd($challans);
        return $challans;
    }
}