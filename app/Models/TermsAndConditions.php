<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsAndConditions extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'content', 'panel_id', 'section_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
