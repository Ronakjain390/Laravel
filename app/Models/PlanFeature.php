<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanFeature extends Model
{
    protected $fillable = [
        'plan_id',
        'feature_id',
        'feature_usage_limit'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function usageRecords()
    {
        return $this->hasMany(PlanFeatureUsageRecord::class);
    }
}

