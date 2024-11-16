<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneVerifications extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'otp',
        'expires_at',
        'verified_at',
    ];

    protected $dates = ['expires_at', 'verified_at'];
}
