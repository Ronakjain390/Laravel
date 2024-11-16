<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = ['user_id', 'message', 'read_at', 'type', 'added_id', 'panel', 'template_name'];
}
