<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    use HasFactory;
    protected $table = 'panel';

    protected $fillable = ['panel_name', 'section_id', 'status'];

    protected $dates = ['created_at', 'updated_at'];

    public function section()
    {
        return $this->belongsTo('App\Section');
    }

    public function features()
    {
        return $this->hasMany(Feature::class, 'panel_id', 'id')->distinct('feature_name');
    }

}
