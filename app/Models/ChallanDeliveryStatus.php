<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallanDeliveryStatus extends Model
{
    // use HasFactory;
    protected $fillable = ['challan_id','challan_deliveries_id'];

    // public function challans()
    // {
    //     return $this->belongsToMany(Challan::class, 'challan_delivery_statuses'); 
    // }
}
