<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuery extends Model
{
    protected $table = 'user_queries';

    protected $fillable = [
        'phone',
        'email',
        'comment',
        'status',
    ];
    use HasFactory;
}
