<?php

namespace App\Exports\ReceiptNote;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Http\Request;
use App\Models\GoodsReceipt;
use Illuminate\Support\Facades\Auth;

class ReceiptNoteExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $request;


    public function __construct(Request $request)
    {
        $this->request = $request;
        // dd($this->request);
    }


    public function map($receipt_note): array
    {
        $status = null;
        if($receipt_note && $receipt_note->statuses && $receipt_note->statuses->first()){
            $status = $receipt_note->statuses->first()->status;
        }

        return [
            $receipt_note->id,
            $receipt_note->goods_series . '-' . $receipt_note->series_num,
            $receipt_note->created_at->format('d-m-Y'),
            $receipt_note->created_at->format('H:i:s'),
            $receipt_note->sender,
            $receipt_note->receiver_goods_receipts,
            $receipt_note->total_qty,
            $receipt_note->total,
            $status,
            $receipt_note->comment,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Goods Receipt Series',
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
        $query = GoodsReceipt::query()->where('sender_id', $userId)->orderByDesc('id');

        // Apply filters
        if ($request->has('receiver_id') && $request->receiver_id !== null) {
            $query->where('receiver_id', $request->receiver_id);
        }

        if ($request->has('status') && $request->status !== null) {
            $query->whereHas('statuses', function ($q) use ($request) {
                $q->latest()->where('status', $request->status);
            });
        }

        if ($request->has('from_date') && $request->from_date !== null && $request->has('to_date') && $request->to_date !== null) {
            $query->whereBetween('goods_receipts_date', [$request->from_date, $request->to_date]);
        }

        if ($request->has('goods_series') && $request->goods_series !== null) {
            $query->where('goods_series', $request->goods_series);
        }

        // Apply export option
        if ($request->all_data == 'all_data') {
            $goodsReceipt = $query->with(['orderDetails:id,details', 'statuses', 'sfpBy', 'tableTags'])->get();
        } elseif ($request->filtered_data == 'filtered_data') {
            $goodsReceipt = $query->with(['statuses', 'orderDetails', 'sfp', 'buyerDetails'])->get();
        } elseif ($request->current_page == 'current_page') {
            $page = $request->input('page', 1);
            $goodsReceipt = $query->with(['statuses', 'orderDetails', 'sfp', 'buyerDetails'])
                                  ->forPage($page, 50)
                                  ->get();
        } else {
            $goodsReceipt = $query->with(['statuses', 'orderDetails', 'sfp', 'buyerDetails'])->paginate(50);
        }

        return $goodsReceipt;
    }
}
