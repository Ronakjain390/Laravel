<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'units';
    protected $fillable = [

        'unit',
        'short_name',
        'user_id',
        'status',
        'panel_type',
        'is_default',
    ];
    /**
     * Get the user that owns the unit.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
