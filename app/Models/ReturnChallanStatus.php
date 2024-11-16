<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnChallanStatus extends Model
{
    protected $fillable = ['challan_id', 'user_id', 'user_name','team_user_name', 'status', 'comment'];

    public function challan()
    {
        return $this->belongsTo(ReturnChallan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
