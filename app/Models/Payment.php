<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 
        'amount',
        'payment_method',
        'payment_id',
        'razorpay_payment_id',
        'payment_done',
        'order_id',
    ];

}
