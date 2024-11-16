<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    use HasFactory;
    protected $table = 'transaction_logs';
    protected $fillable = [

        'challan_id',
        'invoice_id',
        'user_id',
        'action',
        'details',
    ];
}
