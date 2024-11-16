<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUploadLog extends Model
{
    use HasFactory;
    protected $table = 'product_upload_logs';

    protected $fillable = [
        'file_name',
        'file_path',
        'user_id',
        'status',
        'type',
        'uploaded_at',
    ];
}
