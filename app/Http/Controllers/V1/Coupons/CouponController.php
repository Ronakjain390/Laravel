<?php

namespace App\Http\Controllers\V1\Coupons;

use App\Models\Coupons;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        // dd($request);

        $coupons = Coupons::query();

        // Get the associated features and additional features
        $coupons->where('status', 'active')->get();

        $coupons = $coupons->get();
        return response()->json([
            'data' => $coupons,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function create(Request $request)
    {
        // dd($request->all());
        // Validate coupon creation request
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:coupons',
            'discount_amount' => 'required|numeric|min:0',
            'discount_basis' => 'required|in:percentage,direct',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,inactive',
            'applicable_on' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        // Create coupon
        $coupon = Coupons::create([
            'code' => $request->code,
            'discount_amount' => $request->discount_amount,
            'discount_basis' => $request->discount_basis,
            'valid_from' => $request->valid_from,
            'valid_to' => $request->valid_to,
            'usage_limit' => $request->usage_limit,
            'applicable_on' => $request->applicable_on, 
            'status' => $request->status ?? 'active', // Set default status if not provided
        ]);

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Coupon created successfully',
            'coupon' => $coupon,
        ]);

    }

    public function applyCoupon(Request $request)
    {
        // dd($request);
        $validator = Validator::make($request->all(), [
            'code' => 'required|exists:coupons,code',
            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $coupon = Coupons::where('code', $request->code)
                        ->where('status', 'active')
                        ->where('valid_from', '<=', now())
                        ->where('valid_to', '>=', now())
                        ->where('applicable_on', 'payment')
                        ->first();
        // dd($coupon);
        if (!$coupon) {
            return response()->json([
                'message' => 'Expired coupon',
                'status_code' => 400,
            ], 400);
        }

        // Apply the coupon to the order
        // This will depend on your order processing logic

        return response()->json([
            'data' => $coupon,
            'message' => 'Coupon applied successfully',
            'status_code' => 200,
        ]);
    }
}
