<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagsTable extends Model
{
    use HasFactory;
    protected $table = 'tags_table';
    protected $fillable = ['name', 'user_id', 'panel_id', 'table_id'];
    public function goodsReceipts()
    {
        return $this->belongsToMany(GoodsReceipt::class, 'goods_receipt_tags')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }

    public function challans()
    {
        return $this->belongsToMany(Challan::class, 'challan_tags')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }

    public function returnChallans()
    {
        return $this->belongsToMany(ReturnChallan::class, 'return_challan_tags')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }

    public function invoice()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_tags')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }

    public function purchaseOrder()
    {
        return $this->belongsToMany(PurchaseOrder::class, 'purchase_order_tags')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }

    public function estimate()
    {
        return $this->belongsToMany(Estimates::class, 'estimate_tags')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
