<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_receipt_id',
        'unit',
        'rate',
        'qty',
        'details',
        'tax',
        'discount',
        'total_amount',
    ];

    /**
     * Get the goods receipt that owns the order detail.
     */
    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    /**
     * Get the columns associated with the order detail.
     */
    public function columns()
    {
        return $this->hasMany(GoodsReceiptOrderColumn::class, 'goods_receipt_order_detail_id');
    }
}
