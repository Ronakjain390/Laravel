<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetail extends Model
{

    use HasFactory;
    use SoftDeletes;
    protected $table = 'product_detail';

    protected $fillable = [
        'product_id', 'column_name', 'column_value',
    ];

    public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}
}
