<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerDetails extends Model
{
    use HasFactory;
    protected $table = 'seller_detail';

    protected $fillable = [
        'seller_id',
        'address',
        'pincode',
        'phone',
        'gst_number',
        'state',
        'city',
        'bank_name',
        'branch_name',
        'bank_account_no',
        'ifsc_code',
        'tan',
        'status',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
