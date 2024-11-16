<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends Model
{
    use HasFactory;
    protected $table = 'pdf_template';

    protected $fillable = ['pdf_name', 'pdf_template_name', 'status'];

    protected $dates = ['created_at', 'updated_at'];
}
