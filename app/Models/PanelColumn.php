<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanelColumn extends Model
{
    use HasFactory;
    protected $table = 'panel_columns';

    protected $fillable = ['panel_column_default_name', 'panel_column_display_name', 'default', 'user_id', 'panel_id', 'section_id','feature_id', 'status'];

    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function panel()
    {
        return $this->belongsTo('App\Panel');
    }

    public function section()
    {
        return $this->belongsTo('App\Section');
    }
}
