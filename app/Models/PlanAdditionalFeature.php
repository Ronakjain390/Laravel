<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanAdditionalFeature extends Model
{
    protected $fillable = [
        'plan_id',
        'additional_feature_id',
        'additional_feature_usage_limit'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function additionalFeature()
    {
        return $this->belongsTo(AdditionalFeature::class);
    }

    public function usageRecords()
    {
        return $this->hasMany(PlanAdditionalFeatureUsageRecord::class);
    }
}
