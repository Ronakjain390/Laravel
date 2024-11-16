<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanelSeriesNumber extends Model
{
    use HasFactory;
    protected $table = 'panel_series_numbers';

    protected $fillable = [
        'series_number',
        'user_id',
        'panel_id',
        'section_id',
        'assigned_to_id',
        'assigned_to_name',
        'status',
        'valid_from',
        'valid_till',
        'default',
    ];

    protected $casts = [
        'default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function panel()
    {
        return $this->belongsTo(Panel::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

}
