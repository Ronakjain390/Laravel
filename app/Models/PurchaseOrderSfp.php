<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderSfp extends Model
{
    protected $fillable = ['purchase_order_id', 'sfp_by_id', 'sfp_by_name', 'sfp_to_id', 'sfp_to_name', 'comment', 'status'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function sfpByUser()
    {
        return $this->belongsTo(User::class, 'sfp_by_id');
    }

    public function sfpToUser()
    {
        return $this->belongsTo(User::class, 'sfp_to_id');
    }
}
