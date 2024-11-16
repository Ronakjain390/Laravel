<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureTopupUsageRecord extends Model
{
    protected $fillable = [
        'order_id',
        'feature_topup_id',
        'feature_id',
        'usage_count',
        'usage_limit',
        'added_by',
        'amount'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function featureTopup()
    {
        return $this->belongsTo(FeatureTopup::class);
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    // Function to update the usage count for FeatureTopupUsageRecord
    public function updateUsageCount($featureId, $usageCount)
    {
        // Get the authenticated user's ID
        $userId = auth()->user()->id;

        // Get all active orders for the user
        // $activeOrders = Order::where('user_id', $userId)
        //     ->where('status', 'active')
        //     ->orderBy('created_at', 'asc')
        //     ->get();
        $activeOrders = User::where('id', $userId)
            // ->where('status', 'active')
            ->orderBy('created_at', 'asc')
            ->with('plans.featureTopupUsageRecords')->first();

        // Loop through active orders and find the first one with available usage limit
        // if (isset($activeOrders) && !is_null($activeOrders->plans)){
        foreach ($activeOrders->plans as $order) {
            // $featureTopups = $order->featureTopups;

            // // Check if the order has the specified feature topup
            // $featureTopup = $featureTopups->where('id', $featureId)->first();
            // if ($featureTopup) {
            $usageRecord = $order->featureTopupUsageRecords
                ->where('feature_id', $featureId)

                ->first();

            if ($usageRecord) {
                $newUsageCount = $usageRecord->usage_count + $usageCount;

                // Check if the usage limit is not exceeded.
                $usageLimit = $usageRecord->usage_limit; // Use usage_limit directly from the featureTopupUsageRecord
                if ($usageLimit >= 0 && $newUsageCount > $usageLimit) {
                    // Usage limit exceeded, mark the featureTopupUsageRecord as expired and proceed to the next active order.
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

                return true; // Usage count updated successfully for the feature topup in the current order.
            }
            // }
        }
    // }

        return false; // No active order found with the specified feature topup or usage limit exceeded in all active orders.
    }

    // Function to validate the usage limit for FeatureTopupUsageRecord
    public function validateUsageLimit($featureId)
    {
        $userId = auth()->user()->id;
        $activeOrders = Order::where('user_id', $userId)
            ->where('status', 'active')
            ->get();

        foreach ($activeOrders as $order) {
            $usageRecord = $order->featureTopupUsageRecords
                ->where('feature_id', $featureId)

                ->where('usage_count', '<', 'usage_limit')
                ->first();

            if ($usageRecord) {
                $newUsageCount = $usageRecord->usage_count; // No increment here
                $usageLimit = $usageRecord->usage_limit;

                // Check if the usage count has reached its limit.
                if ($usageLimit >= 0 && $newUsageCount >= $usageLimit) {
                    return 'expired'; // Usage limit reached, return 'expired'.
                }

                // If the usage count is below the limit and the feature topup record is active, return 'active'.
                return 'active';
            }
        }

        return 'not_found'; // Feature topup not found in any active order, return 'not_found'.
    }
}
