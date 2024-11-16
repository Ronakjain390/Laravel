<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamUserPermission extends Model
{
    use HasFactory;
    protected $table = 'team_user_permission';
    protected $primaryKey = 'id';
    public $timestamps = false; // Since you have 'created_at' and 'updated_at' timestamps in the migration

    protected $fillable = [
        'team_user_id',
        'team_id',
        'team_owner_user_id',
        'permission',
        'status',
    ];

    // Define the relationships
    public function teamUser()
    {
        return $this->belongsTo(TeamUser::class, 'team_user_id', 'id');
    }

    public function teamOwner()
    {
        return $this->belongsTo(User::class, 'team_owner_user_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}
