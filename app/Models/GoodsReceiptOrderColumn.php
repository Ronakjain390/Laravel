<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptOrderColumn extends Model
{
    use HasFactory;
    protected $fillable = ['goods_receipt_order_detail_id', 'column_name', 'column_value'];

    public function orderDetail()
    {
        return $this->belongsTo(GoodsReceiptOrderDetail::class, 'goods_receipt_order_detail_id','id');
    }
}
