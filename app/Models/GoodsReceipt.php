<?php

namespace App\Models;
use App\Models\GoodsReceiptSfp;
use App\Models\GoodsReceiptStatus;
use App\Models\PaymentStatuses;
use App\Models\TagsTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Models\GoodsReceiptOrderDetail;
use App\Models\ReceiverGoodsReceiptsDetails;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoodsReceipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'goods_series',
        'series_num',
        'sender_id',
        'sender',
        'receiver_goods_receipts_id',
        'receiver_goods_receipts',
        'pdf_url',
        'comment',
        'status_comment',
        'calculate_tax',
        'total',
        'round_off',
        'total_qty',
        'receiver_goods_receipts_detail_id',
        'team_id',
        'additional_phone_number',
        'signature',
        'goods_receipts_date',
    ];

    /**
     * Get the sender user associated with the goods receipt.
     */
    public function SenderUser()
    {
        return $this->belongsTo(User::class, 'sender_id','id');
    }

    public function buyerUser()
    {
        return $this->belongsTo(ReceiverGoodsReceipt::class, 'receiver_goods_receipts_id','id');
    }

    public function buyerDetails()
    {
        return $this->belongsTo(ReceiverGoodsReceiptsDetails::class, 'receiver_goods_receipts_detail_id','id');
    }

    public function orderDetails()
    {
        return $this->hasMany(GoodsReceiptOrderDetail::class,'goods_receipt_id','id')->orderByDesc('id');
    }
    public function statuses()
    {
        return $this->hasMany(GoodsReceiptStatus::class,'goods_receipt_id','id')->orderByDesc('id');
    }

    /**
     * Get the SFPs associated with the goods receipt.
     */
    // public function sfp()
    // {
    //     // dd(Auth::user()->id);
    //     return $this->hasMany(GoodsReceiptSfp::class)->where('sfp_by_id', Auth::user()->id)->orWhere('sfp_to_id', Auth::user()->id)->orderByDesc('id');
    // }

    public function sfp()
    {
        // Replace 'custom_foreign_key' with the actual column name in your 'goods_receipt_sfps' table
        return $this->hasMany(GoodsReceiptSfp::class, 'goods_receipts_id')->orderByDesc('id');
    }
    public function sfpBy()
    {
        return $this->hasMany(GoodsReceiptSfp::class, 'goods_receipts_id', 'id')->orderByDesc('id');
    }

    public function paymentStatuses()
    {
        return $this->belongsToMany(PaymentStatus::class, 'goods_receipt_payment_status')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }

    public function tableTags()
    {
        return $this->belongsToMany(TagsTable::class, 'goods_receipt_tags')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }

}
