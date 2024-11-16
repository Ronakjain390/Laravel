<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupons extends Model
{
    use HasFactory;

    protected $fillable= [
        'code',
        'discount_amount',
        'discount_basis',
        'valid_from',
        'valid_to',
        'usage_limit',
        'usage_count',
        'status',
        'applicable_on'
    ];
}
