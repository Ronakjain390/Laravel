<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanelFeature extends Model
{
    use HasFactory;
    protected $table = 'panel_features';

    protected $fillable = ['panel_feature_name', 'panel_id', 'section_id', 'status'];

    protected $dates = ['created_at', 'updated_at'];

    public function panel()
    {
        return $this->belongsTo('App\Panel');
    }

    public function section()
    {
        return $this->belongsTo('App\Section');
    }
}
