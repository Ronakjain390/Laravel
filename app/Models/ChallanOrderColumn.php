<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallanOrderColumn extends Model
{
    protected $fillable = ['challan_order_detail_id', 'column_name', 'column_value'];

    public function orderDetail()
    {
        return $this->belongsTo(ChallanOrderDetail::class, 'challan_order_detail_id','id');
    }
}
