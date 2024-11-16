<?php

namespace App\Http\Livewire\Dashboard;

use App\Http\Controllers\V1\Plans\PlansController;
use App\Http\Controllers\V1\Pricing\PricingController;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Svg\Tag\Rect;

class Pricing extends Component
{
    public $message, $validationErrorsJson, $statusCode, $updatedProfileData;
    public $plans;
    public $showAnnualPlans = false;
    public $monthlyValidity = true;

    // protected $listeners = ['cartUpdated' => 'render'];
    public $activeTab = 'panel-1'; // Default active tab



    public function changeTab($tab)
    {
        // dd($tab);
        $this->activeTab = $tab;
    }


    // public function mount(){
    //     $request = request();
    //     $planData = new PlansController;
    //     $data = $planData->index($request);
    //     $response = json_decode($data->getContent(), true);
    //     $this->plans = $response['data'];
    // }

    public function mount()
    {
        $this->activeTab = Session::get('activeTab', 'sender'); // Default to 'sender' if not set
        $this->activeTab = 'panel-' . $this->getPanelId($this->activeTab);
        Session::forget('activeTab'); // Clear the session after use
    }

    private function getPanelId($role)
    {
        $roles = ['sender' => 1, 'receiver' => 2, 'seller' => 3, 'buyer' => 4, 'receipt note' => 5];
        return $roles[strtolower($role)] ?? 1;
    }

    public function addToCart($planId)
    {
        // dd($planId, $userId = Auth::id());

        // Data stored in session of the selected plan:
        session()->push('cart.items', [
            'plan_id' => $planId,
            'user_id' => $userId = Auth::id(),
            // other data
        ]);
        // $this->render();
        // session()->flash('success', 'Return Challan accepted successfully.');
        $this->message = 'Package Added To Cart Successfully';
        // $this->dispatchBrowserEvent('reload-page');
        // return redirect()->route('checkout');
        $this->emit('cartUpdated');
    }

    public function buyNow($planId)
    {
        // Data stored in session of the selected plan:
        session()->push('cart.items', [
            'plan_id' => $planId,
            'user_id' => $userId = Auth::id(),
            // other data
        ]);
        return redirect()->route('checkout');
        // $this->emit('cartUpdated');
    }

    public function tryNow($id)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with('error', 'User not authenticated.');
        }

        switch ($id) {
            case '1':
                $user->update(['sender' => 1]);
                return redirect()->route('sender');
            case '2':
                $user->update(['receiver' => 1]);
                return redirect()->route('receiver');
            case '3':
                $user->update(['seller' => 1]);
                return redirect()->route('seller');
            case '4':
                $user->update(['buyer' => 1]);
                return redirect()->route('buyer');
            case '5':
                $user->update(['grn' => 1]);
                return redirect()->route('grn');
            default:
                return redirect()->back()->with('error', 'Invalid option.');
        }
    }

    public function togglePlanType($annual)
    {
        $this->showAnnualPlans = $annual;
    }



    public function render()
    {
        $request = request();
        $request->merge(['status' => 'active']);
        $planData = new PlansController;
        $data = $planData->index($request);
        $response = json_decode($data->getContent(), true);
        // dd($response);
        $this->plans = $response['data'];

        $filtered = collect($this->plans)->filter(function ($plan) {
            // Always include the "Free" plan
            if ($plan['plan_name'] === 'Free') {
                return true;
            }
            // Filter based on validity_days
            return $this->showAnnualPlans ? $plan['validity_days'] == 365 : $plan['validity_days'] == 30;
        })->all();
        // dd($filtered);

        // Custom sorting function
        usort($filtered, function ($a, $b) {
            // Define the order of plan names
            $order = ['Free', 'Basic', 'Silver'];

            // Get the index of each plan name in the order array
            $indexA = array_search($a['plan_name'], $order);
            $indexB = array_search($b['plan_name'], $order);

            // Compare the indexes to determine the sorting order
            return $indexA - $indexB;
        });
        // dd($filtered);

        return view('livewire.dashboard.pricing.pricing', ['filtered' => $filtered]);
    }

    // private function filterMonthlyPlans()
    // {
    //     return collect($this->plans)->filter(function ($plan) {
    //         return $plan['validity_days'] == 30;
    //     })->all();
    // }

    // private function filterAnnualPlans()
    // {
    //     // dd('jzz');
    //     return collect($this->plans)->filter(function ($plan) {
    //         return $plan['validity_days'] == 365;
    //     })->all();
    // }

    // public function toggleValidity()
    // {
    //     $this->showAnnualPlans = !$this->showAnnualPlans;
    //     // dd($this->showAnnualPlans);
    // }


    // public function render()
    // {
    //     $request = request();
    //     $planData = new PlansController;
    //     $data = $planData->index($request);
    //     $response = json_decode($data->getContent(), true);
    //     $this->plans = $response['data'];

    //     $filtered = $this->showAnnualPlans
    //         ? $this->filterAnnualPlans()
    //         : $this->filterMonthlyPlans();
    //         // dd($filteredPlans);

    //     return view('livewire.dashboard.pricing.pricing', ['filtered' => $filtered]);
    // }
}
