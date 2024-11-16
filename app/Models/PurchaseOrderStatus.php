<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderStatus extends Model
{
    protected $fillable = ['purchase_order_id', 'user_id', 'user_name' ,'team_user_name' , 'status', 'comment'];

    public function invoice()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
