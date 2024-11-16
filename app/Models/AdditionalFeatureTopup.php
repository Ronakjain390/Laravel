<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalFeatureTopup extends Model
{
    protected $fillable = [
        'additional_feature_id',
        'price',
        'usage_limit'
    ];

    public function additionalFeature()
    {
        return $this->belongsTo(AdditionalFeature::class);
    }

    public function usageRecords()
    {
        return $this->hasMany(AdditionalFeatureTopupUsageRecord::class);
    }
}

