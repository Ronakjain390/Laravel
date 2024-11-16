<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptSfp extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_receipts_id',
        'sfp_by_id',
        'sfp_by_name',
        'sfp_to_id',
        'sfp_to_name',
        'comment',
        'status',
    ];

    /**
     * Get the goods receipt that owns the SFP.
     */
    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class, 'goods_receipts_id');
    }

    /**
     * Get the user who sent the SFP.
     */
    public function sfpBy()
    {
        return $this->belongsTo(User::class, 'sfp_by_id');
    }

    /**
     * Get the user who received the SFP.
     */
    public function sfpTo()
    {
        return $this->belongsTo(User::class, 'sfp_to_id');
    }
}
