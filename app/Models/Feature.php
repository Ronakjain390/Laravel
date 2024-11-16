<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Plans;
use App\Models\Challan;
use App\Models\Template;
use App\Models\FeatureType;
use App\Models\FeatureTopup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PlanFeatureUsageRecord;
use App\Models\FeatureTopupUsageRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feature extends Model
{
    use HasFactory;
    protected $table = 'features';

    protected $fillable = ['feature_type_id', 'panel_id', 'section_id', 'feature_name', 'template_id', 'status'];

    protected $dates = ['created_at', 'updated_at'];

    public function featureType()
    {
        return $this->belongsTo(FeatureType::class,'feature_type_id','id');
    }

    public function additionalFeature()
    {
        return $this->belongsTo('App\AdditionalFeature');
    }

    public function plans()
    {
        return $this->belongsToMany(Plans::class, 'plan_features', 'feature_id', 'plan_id')
            ->withPivot('feature_usage_limit')
            ->withTimestamps();
    }
    public function plansActive()
    {
        return $this->belongsToMany(Plans::class, 'plan_features', 'feature_id', 'plan_id')
            ->withPivot('feature_usage_limit')
            ->withTimestamps();
    }
    public function topups()
    {
        return $this->hasMany(FeatureTopup::class);
    }

    public function usageRecords()
    {
        $userId = auth()->guard(Auth::getDefaultDriver())->user()->id;

        $usageRecords = $this->hasMany(PlanFeatureUsageRecord::class, 'feature_id', 'id')
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 'active')->select('usage_limit');
            });

        return $usageRecords;
    }

    public function topupUsageRecords()
    {
        $userId = auth()->guard(Auth::getDefaultDriver())->user()->id;

        $topupUsageRecords = $this->hasMany(FeatureTopupUsageRecord::class)
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 'active')->select('usage_limit');
            });

        return $topupUsageRecords;
    }
    public function template()
    {
        return $this->hasOne(Template::class, 'id', 'template_id')->select("id", "template_name", "template_page_name", "status");
    }

    public function sentChallans()
    {
        $userId = auth()->guard(Auth::getDefaultDriver())->user()->id;
        return $this->hasMany(Challan::class, 'sender_id', $userId);
    }

}
