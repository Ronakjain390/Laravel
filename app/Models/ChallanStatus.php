<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallanStatus extends Model
{
    protected $fillable = ['challan_id', 'user_id', 'user_name', 'status', 'comment', 'team_user_name', 'created_at', 'updated_at'];

    public function challan()
    {
        return $this->belongsTo(Challan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
