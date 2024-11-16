<?php

namespace App\Exports;

use App\Models\StockOutExport;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductLogsExport implements FromCollection
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    
}
