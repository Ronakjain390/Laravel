<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateOrderDetail extends Model
{
    use HasFactory;
    protected $fillable = ['estimate_id', 'unit', 'rate', 'qty', 'details', 'tax',  'discount', 'total_amount', 'cgst', 'sgst','igst'];

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function columns()
    {
        return $this->hasMany(EstimateOrderColumns::class, 'estimate_order_detail_id', 'id');
    }

}

