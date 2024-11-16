<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id', 'panel_id'];

    public function goodsReceipts()
    {
        return $this->belongsToMany(GoodsReceipt::class, 'goods_receipt_payment_status')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
