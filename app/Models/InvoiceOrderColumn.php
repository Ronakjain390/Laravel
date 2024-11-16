<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceOrderColumn extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_order_detail_id', 'column_name', 'column_value'];

    public function orderDetail()
    {
        return $this->belongsTo(InvoiceOrderDetail::class, 'invoice_order_detail_id','id');
    }
}
