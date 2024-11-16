<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureTopup extends Model
{
    protected $fillable = [
        'feature_id',
        'price',
        'usage_limit',
        'comment',
    ];

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function usageRecords()
    {
        return $this->hasMany(FeatureTopupUsageRecord::class);
    }
}
