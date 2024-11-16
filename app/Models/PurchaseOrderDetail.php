<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    protected $fillable = ['purchase_order_id', 'unit', 'rate','tax', 'qty','details', 'total_amount'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class,'purchase_order_id','id');
    }

    public function columns()
    {
        return $this->hasMany(PurchaseOrderColumn::class, 'purchase_order_detail_id', 'id');
    }
}
