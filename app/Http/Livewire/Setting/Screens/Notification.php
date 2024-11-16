<?php

namespace App\Http\Livewire\Setting\Screens;

use App\Models\Buyer;
use App\Models\Team;
use Livewire\Component;
use App\Models\WalletLog;
use App\Models\Coupons;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\V1\Plans\PlansController;
use App\Http\Controllers\V1\Orders\OrdersController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class Notification extends Component
{
    public $activePlan,$activeTab, $companyLogoData, $errorMessage,$successMessage, $index;
    public $permissions;
    public $deductionAmount;
    protected $queryString = ['activeTab'];
    protected $listeners = [
        'deleteSelected' => 'deleteSelected',
        'postUpdated' => '$refresh',
        'closeModal' => 'closeModal',
    ];


    // public function forgetSuccessSession()
    // {
    //     session()->forget('success');
    // }

    public $accordionState = [
        'sender' => ['whatsapp' => false, 'email' => false],
        'receiver' => ['whatsapp' => false, 'email' => false],
        'seller' => ['whatsapp' => false, 'email' => false],
        'buyer' => ['whatsapp' => false, 'email' => false],
        'receipt_note' => ['whatsapp' => false, 'email' => false],
    ];
    public function updatedActiveTab($value)
    {

    }
    public function toggleAccordion($role, $channel)
    {
        // Check the current state of the clicked accordion
        $isCurrentlyOpen = $this->accordionState[$role][$channel];

        // Reset all accordions to false
        foreach ($this->accordionState as $r => $channels) {
            foreach ($channels as $ch => $state) {
                $this->accordionState[$r][$ch] = false;
            }
        }

        // Set the state of the clicked accordion to the opposite of its current state
        $this->accordionState[$role][$channel] = !$isCurrentlyOpen;
    }

    public function mount(Request $request)
{
    if (!request()->query('activeTab')) {
        $this->activeTab = 'tab1';
    }

    $notification = new UserAuthController();
    $notification = $notification->index($request);
    $notification = $notification->getData();
    $permissions = json_decode($notification->data[0]->permissions, true);

    // Get the user's wallet
    $user = auth()->user();
    $wallet = Wallet::where('user_id', $user->id)->first();

    // Check if the wallet has a balance less than 1 and update permissions
    if ($wallet === null || $wallet->balance <= 1) {
        foreach ($permissions as $role => &$channels) {
            foreach ($channels as $channel => &$actions) {
                foreach ($actions as $action => &$value) {
                    $value = false;
                }
            }
        }
        $user->permissions = json_encode($permissions);
        $user->save();
    }

    $this->permissions = $permissions;
    // dd($permissions);
}



    public function updatedPermissions($value, $keys)
    {
        $user = auth()->user();
        $keys = explode('.', $keys);
        $role = $keys[0];
        $channel = $keys[1];
        $action = $keys[2];

        // Fetch the user's wallet
        $wallet = Wallet::where('user_id', $user->id)->first();

        // Check if the wallet has a balance less than 1
        if ($wallet === null || $wallet->balance <= 1) {

            $this->dispatchBrowserEvent('show-error-message', ['Insufficient balance to use WhatsApp']);
            // session()->flash('error', ' Insufficient balance to use WhatsApp.');

            // Update permissions to false in Livewire component state
            $this->permissions[$role][$channel][$action] = false;

            // Update permissions to false in the user model
            $userPermissions = json_decode($user->permissions, true);
            $userPermissions[$role][$channel][$action] = false;

            $user->permissions = json_encode($userPermissions);
            $user->save();
            // $this->emit('postUpdated');

            return;
        }

        // Update the permission based on the user's input
        $this->permissions[$role][$channel][$action] = $value;

        $userPermissions = json_decode($user->permissions, true);
        $userPermissions[$role][$channel][$action] = $value;

        $user->permissions = json_encode($userPermissions);
        $user->save();

        // session()->flash('success', 'Permissions updated successfully.');
        $this->dispatchBrowserEvent('show-success-message', ['Permissions updated successfully']);

        // $this->emit('postUpdated');
        // $this->dispatchBrowserEvent('flash-success', ['message' => 'Permissions updated successfully.']);
    }



public $error;
public $amount;
public $couponCode;
public $discountedAmount;

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'couponCode' => 'nullable|string',
    ];

    public function applyCoupon()
    {
        $this->validate();

        // Current date for validity check
        $currentDate = now()->toDateString();

        $coupon = Coupons::where('code', $this->couponCode)
                        ->where('status', 'active')
                        ->where('applicable_on', 'whatsapp')
                        ->first();

        if ($coupon) {
            // Check if the discount is a percentage
            if ($coupon->discount_basis === 'percentage') {
                $this->discountedAmount = $this->amount - ($this->amount * $coupon->discount_amount / 100);
                $this->deductionAmount = $this->amount * $coupon->discount_amount / 100;
                $this->error = null;
            } else {
                // Check if the discount is direct and the amount is less than or equal to the discount amount
                if ($this->amount <= $coupon->discount_amount) {
                    // If the amount is less than or equal to the discount, do not apply the discount
                    $this->discountedAmount = null;
                    $this->deductionAmount = null;
                    $this->error = 'Invalid coupon code.';
                } else {
                    // Apply the discount if the amount is greater than the discount amount
                    $this->discountedAmount = $this->amount - $coupon->discount_amount;
                    $this->deductionAmount = $coupon->discount_amount;
                    $this->error = null;
                }
            }
        } else {
            $this->error = 'Invalid coupon code.';
            $this->discountedAmount = null;
            $this->deductionAmount = null;
        }
    }

    public function removeCoupon()
    {
        $this->reset(['couponCode', 'discountedAmount', 'deductionAmount', 'error']);
    }



    // public function forgetErrorSession()
    // {
    //     session()->forget('error');
    // }


    public function initiatePayment(Request $request)
    {
        // dd($this->deduc)
        $paymentId = $request->input('razorpay_payment_id');
        $orderId = $request->input('razorpay_order_id');
        $signature = $request->input('razorpay_signature');

        $user = auth()->user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

        // Calculate the total amount to add to the wallet
        $deductionAmount = $request->deductionAmount; // Get deduction_amount from request, default to 0 if not set
        $totalAmount = $request->amount + $deductionAmount; // Add deductionAmount to the request amount

        // Update the wallet balance
        $wallet->balance += $totalAmount;
        $wallet->save();

        $this->isOpen = false;
        $this->emit('closeModal');
        // Redirect or return response as needed
        session()->flash('success', 'Payment successful.');
        return response()->json(['redirect_url' => route('notification')]);
    }


    public $isOpen = false;
    public function handleAction()
    {
        $this->isOpen = true;

    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['amount', 'couponCode', 'discountedAmount', 'deductionAmount', 'error']);
    }


    public function render()
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $whatsappBalance = Wallet::where('user_id', $userId)->pluck('balance')->first();
        $whatsappLogs = WalletLog::where('user_id', $userId)->get();
        // dd($teams);
        return view('livewire.setting.screens.notification',
        [
            'whatsappBalance' => $whatsappBalance,
            'whatsappLogs' => $whatsappLogs,
        ]);
    }
}
