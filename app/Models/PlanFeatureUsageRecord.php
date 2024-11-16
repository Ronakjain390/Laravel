<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Feature;
use App\Models\PlanFeature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class PlanFeatureUsageRecord extends Model
{
    protected $fillable = [
        'order_id',
        'plan_feature_id',
        'feature_id',
        'usage_count',
        'usage_limit',
        'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function planFeature()
    {
        return $this->belongsTo(PlanFeature::class);
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function updateUsageCount($featureId, $usageCount)
    {
        // Get the authenticated user's ID
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // dd($userId);
     
        $activeOrders = User::where('id', $userId)
            // ->where('users.status', 'active')
            ->orderBy('created_at', 'asc')
            ->with('plans.featureUsageRecords')->first();
         

        // Loop through active orders and find the first one with available usage limit
        // if (isset($activeOrders) && !is_null($activeOrders->plans)){
        foreach ($activeOrders->plans as $order) {
            // $plan = $order->plan;

            // // Check if the plan has the specified feature
             
            $usageRecord = $order->featureUsageRecords->where('feature_id', $featureId)->where('status', 'active')->first();
            // dd($usageRecord);
            if ($usageRecord) {
                $newUsageCount = $usageRecord->usage_count + $usageCount;

                // Check if the usage limit is not exceeded.
                $usageLimit = $usageRecord->usage_limit; // Use usage_limit directly from the featureUsageRecord
                if ($usageLimit >= 0 && $newUsageCount > $usageLimit) {
                    // Usage limit exceeded, mark the featureUsageRecord as expired and proceed to the next active order.
                    $usageRecord->status = 'expired';
                    $usageRecord->save();
                    continue;
                }

                // Make sure the usage count doesn't go below 0.
                $usageRecord->usage_count = max(0, $newUsageCount);

                // Check if the usage count has reached its limit.
                if ($usageLimit >= 0 && $newUsageCount >= $usageLimit) {
                    $usageRecord->status = 'expired';
                }

                $usageRecord->save();

                return true; // Usage count updated successfully for the feature in the current order.
            }
            // }
        }
    // }

        return false; // No active order found with the specified feature or usage limit exceeded in all active orders.
    }

    public function validateUsageLimit($featureId)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $activeOrders = Order::where('user_id', $userId)
            ->where('status', 'active')
            ->get();
            // dd($activeOrders);
        foreach ($activeOrders as $order) {
            // dd($order);
            $usageRecord = $order->featureUsageRecords
                ->where('feature_id', $featureId)
                ->where('status', 'active')
                ->where('usage_count', '<', 'usage_limit')
                ->first();
                // dd($usageRecord);
            if ($usageRecord) {
                $newUsageCount = $usageRecord->usage_count; // No increment here
                $usageLimit = $usageRecord->usage_limit;
                // Check if the usage count has reached its limit.
                if ($usageLimit >= 0 && $newUsageCount >= $usageLimit) {
                    return 'expired'; // Usage limit reached, return 'expired'.
                }

                // If the usage count is below the limit and the feature record is active, return 'active'.
                return 'active';
            }
        }

        return 'not_found'; // Feature not found in any active order, return 'not_found'.
    }

    // ===========================usage demo
    // ===========================usage demo
    // $planFeatureUsageRecord = new PlanFeatureUsageRecord();
    // $orderId = 123; // Replace with the actual order ID.
    // $usageCount = 1; // Replace with the actual usage count.

    // if ($planFeatureUsageRecord->updateUsageCount($orderId, $usageCount)) {
    //     // Usage count updated successfully, allow the user to use the feature.
    //     // Your feature implementation code here.
    // } else {
    //     // Usage limit exceeded, do not allow the user to use the feature.
    //     // Show an error message or take appropriate action.
    // }
    // ===========================usage demo
    // ===========================usage demo



    // ============================ Implement these later
    // 1. make order expire itself after there expiry date is reached
    // ============================ Implement these later

}
