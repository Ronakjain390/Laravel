<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'section_id',
        'panel_id',
        'is_paid',
        'purchase_date',
        'expiry_date',
        'amount',
        'added_by',
        'pdf_url',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plans::class,'plan_id','id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function panel()
    {
        return $this->belongsTo(Panel::class)->select('id','panel_name', 'section_id', 'status')->with('features');
    }

    public function featureUsageRecords()
    {
        // dd('order');
        return $this->hasMany(PlanFeatureUsageRecord::class, 'order_id', 'id');
    }

    public function additionalFeatureUsageRecords()
    {
        return $this->hasMany(PlanAdditionalFeatureUsageRecord::class, 'order_id', 'id');
    }

    public function featureTopupUsageRecords()
    {
        return $this->hasMany(FeatureTopupUsageRecord::class, 'order_id', 'id');
    }

    public function additionalFeatureTopupUsageRecords()
    {
        return $this->hasMany(AdditionalFeatureTopupUsageRecord::class, 'order_id', 'id');
    }
}
