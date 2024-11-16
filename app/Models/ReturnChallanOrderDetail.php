<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnChallanOrderDetail extends Model
{
    protected $fillable = ['challan_id','challan_order_detail_id', 'item_code', 'unit', 'rate', 'qty', 'remaining_qty', 'details', 'total_amount', 'tax', 'discount'];

    public function challan()
    {
        return $this->belongsTo(ReturnChallan::class,'challan_id','id');
    }

    public function columns()
    {
        return $this->hasMany(ReturnChallanOrderColumn::class, 'challan_order_detail_id');
    }
 
    public function challanOrderDetail()
    {
        return $this->belongsTo(ChallanOrderDetail::class, 'challan_order_detail_id');
    }
 
}
