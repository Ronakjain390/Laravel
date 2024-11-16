<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerDetails extends Model
{
    use HasFactory;
    protected $table = 'buyer_detail';

    protected $fillable = [
        'buyer_id',
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

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
