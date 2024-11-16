<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
    protected $table = 'templates';

    protected $fillable = ['template_name', 'template_page_name','template_image','status'];

    protected $dates = ['created_at', 'updated_at'];
}
