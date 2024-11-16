<?php

namespace App\Models;

use App\Models\User;
use App\Models\Invoice;
use App\Models\TagsTable;
use App\Models\PurchaseOrderSfp;
use App\Models\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Model;
use App\Models\PurchaseOrderOrderDetail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'purchase_order_series', 'series_num', 'seller_id', 'seller_name', 'buyer_id', 'buyer_name','pdf_url', 'team_id', 'comment', 'status_comment', 'total','tax', 'total_qty', 'round_off', 'order_date'
    ];

    public function sellerUser()
    {
        return $this->belongsTo(User::class,  'seller_id','id');
    }

    public function buyerUser()
    {
        return $this->belongsTo(User::class, 'buyer_id','id');
    }



    public function orderDetails()
    {
        return $this->hasMany(PurchaseOrderDetail::class,'purchase_order_id','id')->orderByDesc('id');
    }



    public function statuses()
    {
        return $this->hasMany(PurchaseOrderStatus::class,'purchase_order_id','id')->orderByDesc('id');
    }

    public function sfp()
    {
        return $this->hasMany(PurchaseOrderSfp::class)->orderByDesc('id');
    }

    public function sfpBy()
    {
        return $this->hasMany(PurchaseOrderSfp::class, 'purchase_order_id', 'id')->orderByDesc('id');
    }

    public function buyerDetails()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id', 'id');
    }

    public function tableTags()
    {
        return $this->belongsToMany(TagsTable::class, 'purchase_order_tags')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }


}
