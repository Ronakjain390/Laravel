<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiverDetails extends Model
{
    use HasFactory;
    protected $table = 'receiver_detail';

    protected $fillable = [
        'receiver_id',
        'address',
        'pincode',
        'phone',
        'email',
        'gst_number',
        'state',
        'city',
        'bank_name',
        'branch_name',
        'bank_account_no',
        'ifsc_code',
        'tan',
        'organisation_type',
        'location_name',
        'status',
    ];

    public function receiver()
    {
        return $this->belongsTo(Receiver::class, 'receiver_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
