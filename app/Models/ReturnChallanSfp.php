<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnChallanSfp extends Model
{
    protected $fillable = ['challan_id', 'sfp_by_id', 'sfp_by_name', 'sfp_to_id', 'sfp_to_name', 'comment', 'status','type'];

    public function challan()
    {
        return $this->belongsTo(ReturnChallan::class);
    }

    public function sfpByUser()
    {
        return $this->belongsTo(User::class, 'sfp_by_id');
    }

    public function sfpToUser()
    {
        return $this->belongsTo(User::class, 'sfp_to_id');
    }
}
