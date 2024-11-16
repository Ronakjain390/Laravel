<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceOrderDetail extends Model
{
    protected $fillable = ['invoice_id', 'unit', 'rate', 'qty', 'details', 'tax',  'discount', 'total_amount', 'cgst', 'sgst','igst'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function columns()
    {
        return $this->hasMany(InvoiceOrderColumn::class, 'invoice_order_detail_id', 'id');
    }
}
