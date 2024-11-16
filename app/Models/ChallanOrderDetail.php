<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallanOrderDetail extends Model
{
    protected $fillable = ['challan_id','item_code', 'unit', 'rate', 'qty' ,'tax', 'remaining_qty' ,'margin', 'details', 'total_amount', 'discount'];

    public function challan()
    {
        return $this->belongsTo(Challan::class);
    }

    public function columns()
    {
        return $this->hasMany(ChallanOrderColumn::class, 'challan_order_detail_id','id');
    }
}
