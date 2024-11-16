<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderColumn extends Model
{
    protected $fillable = ['purchase_order_detail_id', 'column_name', 'column_value'];

    public function orderDetail()
    {
        return $this->belongsTo(PurchaseOrderDetail::class, 'purchase_order_detail_id');
    }
}
