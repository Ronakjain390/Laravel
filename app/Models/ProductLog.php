<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLog extends Model
{
    use HasFactory;
    protected $table = 'product_logs';
    protected $fillable = ['product_id', 'qty_out', 'out_method', 'out_at', 'challan_id', 'user_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challan()
    {
        return $this->belongsTo(Challan::class);
    }
    
}


 