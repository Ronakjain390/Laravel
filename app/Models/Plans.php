<?php

namespace App\Models;

use App\Models\User;
use App\Models\Order;
use App\Models\Panel;
use App\Models\Feature;
use App\Models\Section;
use App\Models\AdditionalFeature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plans extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_name',
        'discounted_price',
        'price',
        'section_id',
        'panel_id',
        'is_enterprise_plan',
        'enterprise_user_id',
        'validity_days',
        'comment',
        'topup',
        'pdf_url',
        'user',
        'status'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function panel()
    {
        return $this->belongsTo(Panel::class);
    }

    public function enterpriseUser()
    {
        return $this->belongsTo(User::class, 'enterprise_user_id');
    }

    // public function features()
    // {
    //     return $this->belongsToMany(Feature::class, 'plan_features', 'plan_id', 'id')
    //         ->select('features.feature_type_id', 'features.feature_name', 'features.status', 'plan_features.plan_id', 'plan_features.feature_id', 'plan_features.id', 'plan_features.feature_usage_limit')
    //         ->withTimestamps();
    // }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'plan_features', 'plan_id', 'feature_id')
            ->withPivot('id', 'feature_usage_limit')
            ->select('features.feature_type_id', 'features.feature_name', 'features.status', 'plan_features.plan_id', 'plan_features.feature_id', 'plan_features.id', 'plan_features.feature_usage_limit')
            ->withTimestamps();
    }


    public function additionalFeatures()
    {
        return $this->belongsToMany(AdditionalFeature::class, 'plan_additional_features', 'plan_id', 'id')
            ->select('additional_features.additional_feature_name', 'additional_features.status', 'plan_additional_features.plan_id', 'plan_additional_features.additional_feature_id', 'plan_additional_features.id', 'plan_additional_features.additional_feature_usage_limit')
            ->withTimestamps();
    }

    public function additionalFeatureTopups()
    {
        return $this->belongsToMany(AdditionalFeatureTopup::class, 'plan_additional_features', 'plan_id', 'additional_feature_id')
            ->withPivot('id', 'additional_feature_usage_limit')
            ->select('additional_feature_topups.*');
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
