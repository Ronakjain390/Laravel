<?php

namespace App\Http\Livewire;

use Razorpay\Api\Api;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Payment\PaymentController;
use App\Http\Controllers\V1\Orders\OrdersController;

class RazorpayCheckout extends Component
{
    public $amountWithGst, $totalAmountWithGst, $discount, $userData, $amount, $payment_id, $prefill_name, $prefill_email, $image, $result, $panel_id, $plan_id, $section_id, $order, $response, $topupIds, $planIds;
    public $totalAmount;
    protected $listeners = ['paymentInitiated' => 'finalPayment', 'event-name' => 'handleEvent'];

    public function mount($amountWithGst, $discount, $plans, $planIds, $topupIds)
    {
        // dd($amountWithGst);
        $this->amountWithGst = $amountWithGst;
        // $this->panel_id = $panel_id;
        $this->plans = $plans;
        $this->planIds = $planIds;
        $this->topupIds = $topupIds;
        // dd($plans);
    }
    // protected $listeners = ['event-name' => 'handleEvent'];

    public function handleEvent($data)
    {
        $this->amountWithGst = $data['amountWithGst'];
        // dd($this->amountWithGst);
        $this->plans = $data['plans'];
        $this->planIds = $data['planIds'];
        $this->topupIds = $data['topupIds'];
    }

    // public function finalPayment()
    // {
    //     $request = request();
    //     $userData = [
    //         'user_id' => Auth::user()->id,
    //         'amount' => $this->amountWithGst,
    //     ];
    //     $paymentData = new PaymentController;
    //     $request->merge($userData);
        

    //     // dd($request);
    //     $response = $paymentData->store($request);
    //     $result = $response->getData();
    //     dd($result);
    // }
    public function initiatePayment(Request $request)
    {
        // dd($request);
        $orderData = [
            'order_id' => 'order_' . uniqid(),
        ];

        $request->merge($orderData);
        dd($request);
        $response = new PaymentController;
        $data = $response->store($request);
        // dd($data);
        $result = $data->getData();
        if ($result->status_code === 200) {
            if (isset($request->plan_ids) && !empty($request->plan_ids)) {
                // Process plan_ids
                foreach ($request->plan_ids as $planId) {
                    $order = new OrdersController;
                    $data = $order->store($request);
                    $result = $data->getData();
                }
            }
            if (isset($request->feature_topup_ids) && !empty($request->feature_topup_ids)) {
                // Process feature_topup_ids
                foreach ($request->feature_topup_ids as $topupId) {
                    $order = new OrdersController;
                    $data = $order->topupOrderStore($request);
                    $result = $data->getData();
                }
                // dd($result);
            }
            if ($result->status_code === 200) {
                // Assuming the 'cart.items' array contains the data you want to remove
                // session()->forget('cart.items');
                return redirect()->route('active-plans')->with('success', 'Payment Successful');
            } else {
                return redirect()->back()->with('error', 'Order Failed');
            }
        } else {
            // Handle the case when the payment status is not 200 (e.g., payment failed)
            return redirect()->route('pricing')->with('error', 'Payment Failed');
        }

    }

    public function executeRazorpayCheckoutScript()
    {
        // Execute the Razorpay checkout script here
        // This ensures that the script is executed after the component is rendered with the new value
        // You can use JavaScript to execute the script
        $script = "
            // Your Razorpay checkout script goes here
            // This script should use the updated value of \$amountWithGst
        ";
        
        // Echo the script to be executed within the Livewire component's view
        echo '<script>' . $script . '</script>';
    }
    
    public function render()
    {
        // dd('jdsba');
        return view('livewire.razorpay-checkout');
    }
}
