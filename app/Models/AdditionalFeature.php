<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalFeature extends Model
{
    use HasFactory;
    protected $fillable = [
        'additional_feature_name',
        'section_id',
        'panel_id',
        'feature_id',
        'status',
        // Add other fields as needed
    ];

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_additional_features', 'additional_feature_id', 'plan_id')
            ->withPivot('additional_feature_usage_limit')
            ->withTimestamps();
    }

    public function topups()
    {
        return $this->hasMany(AdditionalFeatureTopup::class);
    }

    public function usageRecords()
    {
        return $this->hasMany(PlanAdditionalFeatureUsageRecord::class);
    }
}
