<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title'];


    public function category()
    {
        return $this->belongsTo(EventCategory::class,'category_id');
    }

    public function ticketManages()
    {
        return $this->hasMany(EventManage::class,'event_id');
    }


}
