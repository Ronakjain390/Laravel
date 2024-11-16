<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'product';
    protected $fillable = [
        'code', 'unit', 'rate','with_tax', 'tax', 'qty', 'total_amount','user_id', 'item_code','category','warehouse', 'location', 'qty_out', 'out_method', 'out_at'
    ];

    public function details()
    {
        return $this->hasMany(ProductDetail::class);
    }
    public function logs()
    {
        return $this->hasMany(ProductLog::class);
    }

    // public function productUploadLogs()
    // {
    //     return $this->hasMany(ProductUploadLog::class);
    // }
}
