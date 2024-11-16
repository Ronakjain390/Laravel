<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiverGoodsReceiptsDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'receiver_id',
        'email',
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

    // Define the inverse relationship with ReceiverGoodsReceipt
    public function receiver()
    {
        return $this->belongsTo(ReceiverGoodsReceipt::class, 'receiver_id');
    }
}
