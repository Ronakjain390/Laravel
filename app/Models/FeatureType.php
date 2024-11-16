<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureType extends Model
{
    use HasFactory;
    protected $table = 'feature_type';

    protected $fillable = ['feature_type_name', 'status'];

    protected $dates = ['created_at', 'updated_at'];
}
