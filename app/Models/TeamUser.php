<?php

namespace App\Models;

use App\Models\Team;
use App\Models\User;
use App\Models\TeamUserPermission;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class TeamUser extends Model implements Authenticatable
{
    use AuthenticatableTrait, HasApiTokens, HasFactory, Notifiable;

    protected $table = 'team_users';

    protected $fillable = [
        'team_user_name',
        'team_name',
        'email',
        'password',
        'team_user_address',
        'team_user_pincode',
        'phone',
        'team_user_state',
        'team_user_city',
        'team_id',
        'team_owner_user_id',
        'unique_login_id',
        'status',
    ];

    // Define the relationships with other models, if applicable
    // For example, if you have relationships with 'users' and 'teams' tables:

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'team_user_id');
    // }
    public function user()
    {
        return $this->belongsTo(User::class, 'team_owner_user_id');
    }
    public function permissions()
    {
        return $this->hasOne(TeamUserPermission::class, 'team_user_id','id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'team_owner_user_id');
    }
}
