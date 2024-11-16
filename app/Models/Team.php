<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    protected $table = 'teams';

    protected $fillable = ['team_name', 'team_owner_user_id', 'team_owner_user', 'team_name_slug', 'status','view_preference'];

    protected $dates = ['created_at', 'updated_at'];

    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
