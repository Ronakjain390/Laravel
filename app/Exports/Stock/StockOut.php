<?php

namespace App\Exports\Stock;
use App\Models\Products;
use App\Models\ProductLog;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;


use Maatwebsite\Excel\Concerns\FromCollection;

class StockOut implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        // dd($this->request);
    }

    public function map($products): array
    {
        $status = null;
        if($products && $products->statuses && $products->statuses->first()){
            $status = $products->statuses->first()->status; 
        }

        return [
            $products->id,
            $products->products_series,
            $products->created_at->format('d-m-Y'),
            $products->created_at->format('H:i:s'),
            $products->sender,
            $products->receiver,
            $products->total_qty,
            $products->total,
            $status,
            $products->comment,
        ];
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
}
