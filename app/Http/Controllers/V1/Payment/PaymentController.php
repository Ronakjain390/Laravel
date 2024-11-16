<?php

namespace App\Http\Controllers\V1\Payment;

use App\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function store(Request $request)
    {   
         try {
            // Validate the request data
            $validatedData = $request->validate([
                'user_id' => 'required|integer',
                'amount' => 'required|numeric',
                'razorpay_payment_id' => '',
                'order_id' => '',
                // 'payment_method' => 'required|string',
                // 'payment_id' => 'required|string',
                // Add validation rules for other fields
            ]);
            // dd($validatedData);
            // Create a new payment record
            $payment = Payment::create($validatedData);
            
            // Return a JSON response for success
            return response()->json([
                'data' => $payment,
                'message' => 'Payment stored successfully',
                'status_code' => 200,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'An error occurred during payment processing.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ], 500);
        } 

        
    }
    public function handleRazorpayResponse(Request $request)
    {
        try {
            // Extract the payment details from the request
            $paymentId = $request->input('razorpay_payment_id');
            $orderId = $request->input('razorpay_order_id');
            $signature = $request->input('razorpay_signature');

            // Verify the signature (you should have this logic in place to ensure the response is valid)

            // If the signature is valid, mark the payment as successful
            // Process the payment and any other necessary logic

            // Here, create an array with the data you want to return
            $responseData = [
                'data' => $payment,  // This is where you can place any payment-related data
                'message' => 'Payment stored successfully',
                'status_code' => 200,
            ];

            // Return a JSON response with the data
            return response()->json($responseData, 200);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return response()->json([
                    'message' => 'An error occurred during payment processing.',
                        'error' => $e->getMessage(),
                        'status_code' => 500,
                ], 500);
            }   
    }

    public function initiatePayment(Request $request)
    {
        // dd($request);
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'user_id' => 'required|integer',
                'amount' => 'required|numeric',
                'razorpay_payment_id' => '',
                'order_id' => '',
                // 'payment_method' => 'required|string',
                // 'payment_id' => 'required|string',
                // Add validation rules for other fields
            ]);
            // Create a new payment record
            dd($validatedData);
            $payment = Payment::create($validatedData);
            // Check if the payment was created successfully
            if ($payment) {
                if (isset($request->plan_ids) && !empty($request->plan_ids)) {
                    // Process plan_ids
                    foreach ($request->plan_ids as $planId) {
                        $order = new OrdersController;
                        $data = $order->store($request);
                        $result = $data->getData();
                        dd($result);
                    }
                }
                if (isset($request->feature_topup_ids) && !empty($request->feature_topup_ids)) {
                    // Process feature_topup_ids
                    foreach ($request->feature_topup_ids as $topupId) {
                        $order = new OrdersController;
                        $data = $order->topupOrderStore($request);
                        $result = $data->getData();
                    }
                }
                if (isset($result) && $result->status_code === 200) {
                    // Assuming the 'cart.items' array contains the data you want to remove
                    // session()->forget('cart.items');
                    return redirect()->route('active-plans')->with('success', 'Payment Successful');
                } else {
                    return redirect()->back()->with('error', 'Order Failed');
                }
            }

            // Return a JSON response for success
            return response()->json([
                'data' => $payment,
                'message' => 'Payment stored successfully',
                'status_code' => 200,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'An error occurred during payment processing.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ], 500);
        }
    }
}
