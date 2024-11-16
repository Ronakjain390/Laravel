<?php

namespace App\Models;

use App\Models\User;
use App\Models\TagsTable;
use App\Models\InvoiceSfp;
use App\Models\InvoiceStatus;
use App\Models\ReceiverDetails;
use App\Models\InvoiceOrderColumn;
use App\Models\InvoiceOrderDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'invoice_series', 'series_num', 'seller_id', 'seller', 'buyer_id', 'buyer','buyer_detail_id','pdf_url', 'status_comment','team_id','deleted_at', 'comment','calculate_tax', 'total', 'round_off', 'total_qty','purchase_order_series', 'invoice_date','eway_bill_no', 'estimate_series'
    ];

    public function sellerUser()
    {
        return $this->belongsTo(User::class, 'seller_id','id');
    }

    public function buyerUser()
    {
        return $this->belongsTo(User::class, 'buyer_id','id');
    }

    public function buyerDetails()
    {
        return $this->belongsTo(BuyerDetails::class, 'buyer_detail_id','id');
    }

    public function orderDetails()
    {
        return $this->hasMany(InvoiceOrderDetail::class,'invoice_id','id')->orderByDesc('id');
    }
    public function statuses()
    {
        return $this->hasMany(InvoiceStatus::class,'invoice_id','id')->orderByDesc('id');
    }

    public function sfp()
    {
        return $this->hasMany(InvoiceSfp::class)->orderByDesc('id');
    }
    public function sfpBy()
    {
        return $this->hasMany(InvoiceSfp::class, 'invoice_id', 'id')->orderByDesc('id');
    }

    public function tableTags()
    {
        return $this->belongsToMany(TagsTable::class, 'invoice_tags')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}
