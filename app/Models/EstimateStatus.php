<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateStatus extends Model
{
    use HasFactory;
    protected $fillable = ['estimate_id', 'user_id', 'user_name','team_user_name', 'status', 'comment'];

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
