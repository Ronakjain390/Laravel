<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_receipt_id',
        'user_id',
        'user_name',
        'team_user_name',
        'status',
        'comment',
    ];

    /**
     * Get the goods receipt that owns the status.
     */
    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    /**
     * Get the user who updated the status.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
