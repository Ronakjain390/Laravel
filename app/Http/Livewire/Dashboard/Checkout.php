<?php

namespace App\Http\Livewire\Dashboard;
use App\Http\Livewire\RazorpayCheckout;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\Plans\PlansController;
use App\Http\Controllers\V1\Profile\ProfileController;
use App\Http\Controllers\V1\FeatureTopup\FeatureTopupController;
use App\Http\Controllers\V1\Coupons\CouponController;
use App\Http\Controllers\V1\Orders\OrdersController;
use Livewire\Component;
use Illuminate\Http\Request;
use App\Http\Controllers\V1\Payment\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Svg\Tag\Rect;
use Carbon\Carbon;
use App\Models\Coupons;


class Checkout extends Component
{
    public $errorMessage, $validationErrorsJson, $statusCode, $updatedProfileData, $message, $result;
    public $plans, $totalAmountWithoutGst, $totalAmountWithGst, $igstAmount, $cgstAmount, $errorData, $sgstAmount, $gst_number;



    public $price;
    public $validityDate, $formattedValidityDate;
    public $totalAmount;
    public $planPrices = [];
    public $planValidityDays = 30;
    public $planTotalAmounts = [];
    public $plansData = []; // An array to store plan data for each plan
    public $validityDays = [];
    public $showInput = false; // A property to control the visibility of the input field
    public $gstNumber = '';
    public $userData;
    public $code;
    protected $razorpayCheckout;
    public $razorpayComponent;
    public $couponCode;
    public $couponDataset = [
        'code' => '',
    ];
    // public $listeners = [
    //     'cartUpdated' => 'render',
    // ];
    public $amountWithGst;
    public $discountDisabled = false;
    protected $listeners = ['initiateRazorpayPayment' => 'showRazorpayPayment', 'cartUpdated' => 'handleCartUpdated'];


    public function applyCoupon(Request $request)
    {
        // Apply the coupon
        $request->merge($this->couponDataset);
        $PanelColumnsController = new CouponController;
        $columnsResponse = $PanelColumnsController->applyCoupon($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        // dd($columnsData);
        switch ($columnsData['status_code']) {
            case 200:
                if ($columnsData['data']['status'] == 'active') {
                    // Set discount information
                    $this->discountApplied = true;
                    $this->discountAmount = $columnsData['data']['discount_amount'];
                    $this->discountBasis = $columnsData['data']['discount_basis']; // Set the discount basis

                    // Apply the discount
                    $this->applyDiscount($this->discountBasis, $this->discountAmount);
                    session()->flash('message', 'Coupon applied');
                }
                break;
            case 400:
                $this->errorMessage = 'Coupon Expired';
                break;
            case 422:
                $this->errorMessage = $columnsData['errors']['code'][0];
                break;

            default:
                $this->errorMessage = 'An unknown error occurred';
                break;
        }
    }

    public function removeCoupon()
    {
        // dd('remove');
        // Remove the coupon
        $this->discountApplied = false;
        $this->discountAmount = 0;
        $this->discountedAmount = 0;
        $this->totalAmountWithGst = $this->totalAmountWithoutGst;
        $this->discountDisabled = false;
    }


    public $company_name, $address, $pincode, $state, $city;

    public function updateProfile()
    {
        $this->validate( [
            'company_name' => 'required',
            'gst_number' => 'required',
        ]);

        // Assuming that you have authenticated user
        $user = auth()->user();

        $user->update([
            'company_name' => $this->company_name,
            'gst_number' => $this->gst_number,
        ]);
        $this->successMessage = 'Profile updated successfully';

        $this->showInput = false;
        return redirect()->back()->with('successMessage', 'Profile updated successfully');
    }



    // Method to increase plan duration by 1 month
    public function increasePlanDurationByMonth()
    {
        $this->planValidityDays += 30; // Add 30 days (1 month)
        $this->calculateAmount();
    }

    // Method to increase plan duration by 1 year
    public function increasePlanDurationByYear()
    {
        $this->planValidityDays += 365; // Add 365 days (1 year)
        $this->calculateAmount();
    }

    // Method to recalculate amount based on plan validity days
    public function calculateAmount()
    {
        // Your logic to calculate the amount based on plan duration
        $this->amount = ($this->planValidityDays / 30) * 100; // Assuming $100 per month
    }
//     public function showRazorpayPayment($amountWithGst)
//     {
//         $this->amountWithGst = $amountWithGst;
//     }

//    // RazorpayCheckout.php
//    public function initiatePayment($amountWithGst)
//    {
//     //    dd($amountWithGst); // Debugging statement to check if the method is called
//        // Your existing code to initiate payment
//        $this->emit('paymentInitiated', $amountWithGst);
//    }
    // Change date format
    public function formatDate($date)
    {
        return Carbon::parse($date)->format('d M Y');
    }

    public function updatedPrice()
    {
        $this->calculateTotalAmount();
    }

    public function updatedValidityDays()
    {
        $this->calculateTotalAmount();
    }

    private function calculateTotalAmount()
    {
        $this->totalAmount = $this->price * $this->validityDays;
    }

//     public function totalAmountWithoutGst()
// {
//     $this->totalAmountWithoutGst = array_sum($this->planTotalAmounts);
// }

public function removeFromSession($index)
{
    if (session()->has('cart.items')) {
        session()->forget('cart.items.'.$index);
        session()->put('cart.items', array_values(session('cart.items')));
    }

    if (session()->has('cart.topups')) {
        session()->forget('cart.topups.'.$index);
        session()->put('cart.topups', array_values(session('cart.topups')));
    }

    $this->emit('cartUpdated', 'reloadPage');
}




public function handleCartUpdated($action)
{
    if ($action === 'reloadPage') {
        $this->reloadPage();
    }
}

public function reloadPage()
{
    // This will emit a Livewire event to trigger a page reload
    $this->emit('reloadPage');
}

    // public function removeFromSession($index)
    // {
    //     // dd($index);
    //     session()->forget('cart.items.'.$index);

    //     // Reindex the session array to remove the gap
    //     session()->put('cart.items', array_values(session('cart.items')));

    //     // Emit an event to notify other components and the Livewire component
    //     // $this->emit('cartUpdated');
    //     $request = request();
    //     $this->mount($request);

    // }
    // public function cartData(Request $request)
    // {
    //     $cartItems = session()->get('cart.items');

    //     if ($cartItems) {
    //         // Extract plan IDs from the cart items
    //         $planIds = array_column($cartItems, 'plan_id');

    //         $request->replace([]);

    //         $planData = new PlansController;
    //         $request->merge(['plan_ids' => $planIds]);
    //         $data = $planData->planCheckout($request);
    //         $this->plans = $data->getData()->data;
    //         // dd($data);

    //     } else {
    //         return redirect()->route('pricing');
    //     }
    // }
    public function toggleInput()
    {
        $this->showInput = true;
    }

    public function saveGstNumber()
    {
        $request = request();
        $request->merge($this->userData);

        // Create an array with user data
        $this->userData = [
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'company_name' => Auth::user()->company_name,
            'address' => Auth::user()->address,
            'phone' => Auth::user()->phone,
            'pincode' => Auth::user()->pincode,
            'gst_number' => $request->gst_number,
            'pancard' => Auth::user()->pancard,
            'state' => Auth::user()->state,
            'city' => Auth::user()->city,
            'bank_name' => Auth::user()->bank_name,
            'branch_name' => Auth::user()->branch_name,
            'bank_account_number' => Auth::user()->bank_account_number,
            'ifsc_code' => Auth::user()->ifsc_code,
            'special_id' => Auth::user()->special_id,
        ];

        // Create a request instance and merge the user data
        $request->merge($this->userData);
        // dd($request);

        $userId = Auth::getDefaultDriver() == 'team-user'
            ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
            : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $updateProfile = new ProfileController;
        $response = $updateProfile->update($request, $userId);

        $result = $response->getData();

        // dd($result);
        $this->statusCode = $result->status_code;
        if ($result->status_code === 200 || $result->status_code === 201) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage' , 'result']);

        } else {
            $this->errorMessage = json_encode($result->errors);
        }

        // $this->showInput = false;
        $this->mount();
    }

    // public function render()
    // {
    //    $this->razorpayCheckout = app(RazorpayCheckout::class);

    //     $cartItems = session()->get('cart.items');
    //     $cartTopup = session()->get('cart.topups');
    //     // dd($cartTopup);
    //     if($cartTopup){


    //     $topupId = array_column($cartTopup, 'topup_id');
    //         $request = request();
    //         $request->replace([]);

    //         $planData = new FeatureTopupController;
    //         $request->merge(['topup_ids' => $topupId]);
    //         $data = $planData->topupCheckout($request);
    //         $this->plans = $data->getData()->data;
    //     }
    //     if ($cartItems) {
    //         // Extract plan IDs from the cart items
    //         $planIds = array_column($cartItems, 'plan_id');
    //         $request = request();
    //         $request->replace([]);

    //         $planData = new PlansController;
    //         $request->merge(['plan_ids' => $planIds]);
    //         $data = $planData->planCheckout($request);
    //         $this->plans = $data->getData()->data;

    //         // dd($this->plans);
    //         // Iterate through each plan to set the price and validityDays
    //         foreach ($this->plans as $plan) {
    //             // Store the data in the respective arrays
    //             if($plan->price == 30){
    //             $this->planValidityDays[] = 1;
    //             }else{
    //                 $this->planValidityDays[] = 1;
    //             }
    //             $this->planPrices[] = $plan->price;
    //             $this->planTotalAmounts[] = $plan->price * $this->planValidityDays[count($this->planValidityDays) - 1];

    //                             $today = \Carbon\Carbon::now();
    //                             $this->validityDate = $today->addDays($plan->validity_days);

    //                             $this->formattedValidityDate = $this->formatDate($this->validityDate);
    //             // $this->planPrices[] = $plan->price;
    //             // // $this->planValidityDays[] = $plan->validity_days;
    //             // $this->planTotalAmounts[] = $plan->price * $plan->validity_days;
    //         }
    //         $this->totalAmountWithoutGst = array_sum($this->planTotalAmounts);


    //         // Determine the tax rates based on the user's state
    //         $userState = Auth::user()->state;

    //         if ($userState === 'UTTAR PRADESH') {
    //             // Calculate CGST and SGST at 9% each
    //             $cgstRate = 0.09;
    //             $sgstRate = 0.09;
    //             $this->cgstAmount = $this->totalAmountWithoutGst * $cgstRate;
    //             $this->sgstAmount = $this->totalAmountWithoutGst * $sgstRate;
    //             $this->totalAmountWithGst = $this->cgstAmount + $this->sgstAmount + $this->totalAmountWithoutGst;

    //         } else {
    //             // Calculate IGST at 18%
    //             $igstRate = 0.18;
    //             $this->igstAmount = $this->totalAmountWithoutGst * $igstRate;
    //             $this->totalAmountWithGst = $this->igstAmount + $this->totalAmountWithoutGst;
    //         }
    //         // dd( $this->totalAmountWithoutGst);
    //     } else {
    //         return redirect()->route('pricing');
    //     }


    //     return view('livewire.dashboard.pricing.checkout')
    //     ->extends('layouts.app')
    //     ->section('content');
    // }

    public function mount()
    {
        $this->company_name = Auth::user()->company_name;
        $this->address = Auth::user()->address;
        $this->pincode = Auth::user()->pincode;
        $this->state = Auth::user()->state;
        $this->city = Auth::user()->city;
    }

    public function cityAndStateByPincode(): void
    {
        // dd($this->pincode);
        $pincode = $this->pincode;
        // dd($this->address
        $receiverController = new ReceiversController();
        $response = $receiverController->fetchCityAndStateByPincode($pincode);
        $result = $response->getData();
        // dd($result);
        if (isset($result->city) && isset($result->state)) {
            // Update the city and state fields
            $this->city = $result->city;
            // dd($this->city);
            $this->state = $result->state;
            $this->city = $result->city;
        }

    }

    public function render()
{
    $this->razorpayCheckout = app(RazorpayCheckout::class);

    $cartItems = session()->get('cart.items');
    $cartTopup = session()->get('cart.topups');
    // dd($cartTopup);
    // Initialize arrays to store plan data
    $this->plans = [];
    $this->planValidityDays = [];
    $this->planPrices = [];
    $this->planTotalAmounts = [];

    if ($cartTopup) {
        $topupIds = array_column($cartTopup, 'topup_id');
        $this->fetchPlanData($topupIds, 'topup');
    }

    if ($cartItems) {
        $planIds = array_column($cartItems, 'plan_id');
        $this->fetchPlanData($planIds, 'plan');
    }
    // $userState = Auth::user()->state;
    // if (strtoupper($userState) === 'UTTAR PRADESH') {
    //     // Calculate CGST and SGST at 9% each
    //     $cgstRate = 0.09;
    //     $sgstRate = 0.09;
    //     $this->cgstAmount = $this->totalAmountWithoutGst * $cgstRate;
    //     $this->sgstAmount = $this->totalAmountWithoutGst * $sgstRate;
    //     $this->totalAmountWithGst = $this->cgstAmount + $this->sgstAmount + $this->totalAmountWithoutGst;
    // } else {
    //     // Calculate IGST at 18%
    //     $igstRate = 0.18;
    //     $this->igstAmount = $this->totalAmountWithoutGst * $igstRate;
    //     $this->totalAmountWithGst = $this->igstAmount + $this->totalAmountWithoutGst;
    // }



    return view('livewire.dashboard.pricing.checkout', [
        'totalAmountWithGst' => $this->totalAmountWithGst,
    ])
        ->extends('layouts.app')
        ->section('content');
}

    public $topupIds, $planIds, $discountApplied, $discountedAmount;

    public function applyDiscount($discountBasis, $discountAmount)
    {
        $originalAmount = $this->totalAmountWithoutGst;

        if ($discountBasis == 'direct') {
            $this->totalAmountWithoutGst -= $discountAmount;
            // dd($this->totalAmountWithoutGst);
        } elseif ($discountBasis == 'percentage') {
            $discountAmount = $originalAmount * ($discountAmount / 100);
            $this->totalAmountWithoutGst -= $discountAmount;
        }

        $this->discountedAmount = $originalAmount - $this->totalAmountWithoutGst;
        // dd($this->discountedAmount);
        // Determine the GST rate based on the user's state
        if (strtoupper(Auth::user()->state) == 'UTTAR PRADESH') {
            $gstRate = 0.09 + 0.09; // CGST + SGST
        } else {
            $gstRate = 0.18; // IGST
        }
        $userState = Auth::user()->state;
        if (strtoupper($userState) === 'UTTAR PRADESH') {
            // Calculate CGST and SGST at 9% each
            $cgstRate = 0.09;
            $sgstRate = 0.09;
            $this->cgstAmount = $this->totalAmountWithoutGst * $cgstRate;
            $this->sgstAmount = $this->totalAmountWithoutGst * $sgstRate;
            $this->totalAmountWithGst = $this->cgstAmount + $this->sgstAmount + $this->totalAmountWithoutGst;
        } else {
            // Calculate IGST at 18%
            $igstRate = 0.18;
            $this->igstAmount = $this->totalAmountWithoutGst * $igstRate;
            $this->totalAmountWithGst = $this->igstAmount + $this->totalAmountWithoutGst;
        }

            // Add the GST to the discounted amount
            $this->totalAmountWithGst = $this->totalAmountWithoutGst * (1 + $gstRate);

            $this->discountDisabled = true;
            $this->emit('discountApplied', $this->totalAmountWithGst);
    }

    public function initiatePayment(Request $request)
    {
        $orderData = [
            'order_id' => 'order_' . uniqid(),
        ];
        // dd($request->plan_ids);
        $request->merge($orderData);
        $response = new PaymentController;
        $data = $response->store($request);
        // dd($data);
        $result = $data->getData();
        $amount = $result->data->amount;
        if ($result->status_code === 200) {
            if (isset($request->plan_ids) && !empty($request->plan_ids)) {
                // Process plan_ids
                foreach ($request->plan_ids as $planId) {
                    $order = new OrdersController;
                    $request->merge(['amount' => $amount]);
                    $data = $order->store($request);
                    $result = $data->getData();
                    // dd($result, '1');
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

                // Assuming the 'cart.items' array contains the data you want to remove
                session()->forget('cart.items');
                return redirect()->route('active-plans')->with('success', 'Payment Successful');

        } else {
            // Handle the case when the payment status is not 200 (e.g., payment failed)
            return redirect()->route('pricing')->with('error', 'Payment Failed');
        }

    }


public $withoutGst;
public function discountApplied($updatedAmount)
{
    // Update the total amount with the updated value
    $this->totalAmountWithGst = $updatedAmount;
}
private function fetchPlanData($ids, $type)
{
    $request = request();
    $request->replace([]);

    $controller = ($type === 'topup') ? FeatureTopupController::class : PlansController::class;
    $data = (new $controller)->{$type.'Checkout'}($request->merge([$type.'_ids' => $ids]));
    $plans = $data->getData()->data;

    // Separate plan IDs and topup IDs
    $planIds = [];
    $topupIds = [];
    foreach ($plans as $plan) {
        if ($type === 'topup') {
            $this->topupIds[] = $plan->id;
        } else {
            $this->planIds[] = $plan->id;
        }

        // Store other plan data
        $this->plans[] = $plan;
        $this->planValidityDays[] = ($plan->price == 30) ? 1 : $plan->validity_days;
        $this->planPrices[] = $plan->price;
        $this->planTotalAmounts[] = $plan->price * 1;

        // Calculate validity date
        $today = \Carbon\Carbon::now();
        $validityDate = $today->addDays($plan->validity_days ?? null);
        $this->formattedValidityDate[] = $this->formatDate($validityDate);
    }

    // Calculate total amount without GST
    $this->totalAmountWithoutGst = array_sum($this->planTotalAmounts);

    $this->withoutGst = array_sum($this->planTotalAmounts);

     // Apply discount amount if available
     $originalAmount = $this->totalAmountWithoutGst;
     if ($this->discountApplied) {
         if ($this->discountBasis == 'direct') {
             $this->totalAmountWithoutGst -= $this->discountAmount;
         } elseif ($this->discountBasis == 'percentage') {
             $discountAmount = $originalAmount * ($this->discountAmount / 100);
             $this->totalAmountWithoutGst -= $discountAmount;
         }
     }

     // Calculate GST
     $userState = Auth::user()->state;
     if (strtoupper($userState) === 'UTTAR PRADESH') {
         // Calculate CGST and SGST at 9% each
         $cgstRate = 0.09;
         $sgstRate = 0.09;
         $this->cgstAmount = $this->totalAmountWithoutGst * $cgstRate;
         $this->sgstAmount = $this->totalAmountWithoutGst * $sgstRate;
         $this->totalAmountWithGst = $this->cgstAmount + $this->sgstAmount + $this->totalAmountWithoutGst;
     } else {
         // Calculate IGST at 18%
         $igstRate = 0.18;
         $this->igstAmount = $this->totalAmountWithoutGst * $igstRate;
         $this->totalAmountWithGst = $this->igstAmount + $this->totalAmountWithoutGst;
     }

    $this->emit('event-name', ['amountWithGst' => $this->totalAmountWithGst, 'plans' => $plans, 'planIds' => $planIds, 'topupIds' => $topupIds]);

    // Pass plan IDs and topup IDs to the view
    return [
        'planIds' => $planIds,
        'topupIds' => $topupIds,
        $totalAmountWithGst= $this->totalAmountWithGst,
        // Other data if needed
    ];
}

}
