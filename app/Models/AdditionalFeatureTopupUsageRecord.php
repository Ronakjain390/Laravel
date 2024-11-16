<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalFeatureTopupUsageRecord extends Model
{
    protected $fillable = [
        'order_id',
        'additional_feature_topup_id',
        'additional_feature_id',
        'usage_count',
        'usage_limit',
        'added_by',
        'amount'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function additionalFeatureTopup()
    {
        return $this->belongsTo(AdditionalFeatureTopup::class);
    }

    public function additionalFeature()
    {
        return $this->belongsTo(AdditionalFeature::class);
    }

    // Function to update the usage count for AdditionalFeatureTopupUsageRecord
    public function updateUsageCount($additionalFeatureId, $usageCount)
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
            ->with('plans.additionalFeatureTopupUsageRecords')->first();

        // Loop through active orders and find the first one with available usage limit
        foreach ($activeOrders->plans as $order) {
            // $additionalFeatureTopups = $order->additionalFeatureTopups;

            // // Check if the order has the specified additional feature topup
            // $additionalFeatureTopup = $additionalFeatureTopups->where('id', $additionalFeatureId)->first();
            // if ($additionalFeatureTopup) {
            $usageRecord = $order->additionalFeatureTopupUsageRecords
                ->where('additional_feature_id', $additionalFeatureId)

                ->first();

            if ($usageRecord) {
                $newUsageCount = $usageRecord->usage_count + $usageCount;

                // Check if the usage limit is not exceeded.
                $usageLimit = $usageRecord->usage_limit; // Use usage_limit directly from the additionalFeatureTopupUsageRecord
                if ($usageLimit >= 0 && $newUsageCount > $usageLimit) {
                    // Usage limit exceeded, mark the additionalFeatureTopupUsageRecord as expired and proceed to the next active order.
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

                return true; // Usage count updated successfully for the additional feature topup in the current order.
            }
            // }
        }

        return false; // No active order found with the specified additional feature topup or usage limit exceeded in all active orders.
    }

    // Function to validate the usage limit for AdditionalFeatureTopupUsageRecord
    public function validateUsageLimit($additionalFeatureId)
    {
        $userId = auth()->user()->id;
        $activeOrders = Order::where('user_id', $userId)
            ->where('status', 'active')
            ->get();

        foreach ($activeOrders as $order) {
            $usageRecord = $order->additionalFeatureTopupUsageRecords
                ->where('additional_feature_id', $additionalFeatureId)

                ->where('usage_count', '<', 'usage_limit')
                ->first();

            if ($usageRecord) {
                $newUsageCount = $usageRecord->usage_count; // No increment here
                $usageLimit = $usageRecord->usage_limit;

                // Check if the usage count has reached its limit.
                if ($usageLimit >= 0 && $newUsageCount >= $usageLimit) {
                    return 'expired'; // Usage limit reached, return 'expired'.
                }

                // If the usage count is below the limit and the additional feature record is active, return 'active'.
                return 'active';
            }
        }

        return 'not_found'; // Additional feature not found in any active order, return 'not_found'.
    }
}
