<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanelDesign extends Model
{
    use HasFactory;
    protected $table = 'panel_design';

    protected $fillable = ['feature_name', 'feature_id', 'template_name', 'template_id', 'status'];

    protected $dates = ['created_at', 'updated_at'];

    public function feature()
    {
        return $this->belongsTo('App\Feature');
    }

    public function template()
    {
        return $this->belongsTo('App\Template');
    }
}
