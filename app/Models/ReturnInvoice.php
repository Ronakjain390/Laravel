<?php

namespace App\Models;

use App\Models\User;
use App\Models\Invoice;
use App\Models\ReturnInvoiceSfp;
use App\Models\ReturnInvoiceStatus;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReturnInvoiceOrderDetail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnInvoice extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'invoice_id', 'invoice_series', 'series_num', 'seller_id', 'seller', 'buyer_id', 'buyer','pdf_url', 'comment', 'total','invoice_date'
    ];

    public function sellerUser()
    {
        return $this->belongsTo(User::class,  'seller_id','id');
    }

    public function buyerUser()
    {
        return $this->belongsTo(User::class, 'buyer_id','id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function orderDetails()
    {
        return $this->hasMany(ReturnInvoiceOrderDetail::class,'return_invoice_id','id')->orderByDesc('id');
    }

       public function column()
    {
        return $this->hasMany(ReturnInvoiceOrderColumn::class,'return_invoice_order_detail_id','id')->orderByDesc('id');
    }

    public function statuses()
    {
        return $this->hasMany(ReturnInvoiceStatus::class,'return_invoice_id','id')->orderByDesc('id');
    }

    public function sfp()
    {
        return $this->hasOne(ReturnInvoiceSfp::class)->orderByDesc('id');
    }
}
