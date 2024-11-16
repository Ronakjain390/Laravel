<?php

namespace App\Exports\ReceiptNote;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GoodsReceipt;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class DetailedReceiptNote implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $request;
    protected $index = 1;
    protected $lastSeriesNum = null;


    public function __construct(Request $request)
    {
        // dd($request);
        $this->request = $request;
    }

    public function map($challan): array
    {
        $status = null;
        if($challan && $challan->statuses && $challan->statuses->first()){
            $status = $challan->statuses->first()->status;
        }

        // Concatenate series_num with purchase_order_series using a hyphen
        $challanOrderSeries = $challan->goods_series . '-' . $challan->series_num;

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

            // Get the first three columns
            $columns = $orderDetail->columns->take(3)->pluck('column_value')->toArray();

            // Ensure we have exactly 3 columns, even if there are less than 3
            while (count($columns) < 3) {
                $columns[] = '';
            }

            $rows[] = [
                $isFirstRow ? $indexToShow : '',  // Show index only for the first row of each challan
                $challanOrderSeries,
                $challan->created_at->format('d-m-Y'),
                $challan->created_at->format('H:i:s'),
                $challan->sender,
                $challan->receiver,
                $columns[0],  // First column
                $columns[1],  // Second column
                $columns[2],  // Third column
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
            'Article',
            'Hsn',
            'Detail',
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
        $query = GoodsReceipt::query()->where('sender_id', $userId)->orderByDesc('id');

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
            $query->whereBetween('goods_receipts_date', [$request->from_date, $request->to_date]);
            $isFilterApplied = true;
        }

        // Filter by challan series
        if ($request->goods_series !== null) {
            // Existing logic to filter by challan series
            $searchTerm = $request->goods_series;
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                $query->where('goods_series', $series)
                    ->where('series_num', $num);
            }
            $isFilterApplied = true;
        }

        // Apply default filter if no other filters are applied
        if (!$isFilterApplied) {
            $fromDate = now()->subMonth()->startOfDay(); // 1 month before, at 00:00 hours
            $toDate = now()->endOfDay(); // Today, at 23:59 hours
            $query->whereBetween('goods_receipts_date', [$fromDate, $toDate]);
        }

        $challans = $query->with(['statuses', 'orderDetails', 'sfp', 'buyerDetails'])->get();
        // dd($challans);
        return $challans;
    }
}
