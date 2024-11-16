<?php

namespace App\Http\Livewire\Dashboard;

use App\Http\Controllers\V1\Plans\PlansController;
use App\Http\Controllers\V1\FeatureTopup\FeatureTopupController;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Svg\Tag\Rect;

class Topup extends Component
{
    public $errorMessage, $validationErrorsJson, $statusCode, $updatedProfileData;

    public $name, $email, $special_id, $company_name, $address, $pincode, $phone, $gst_number, $pancard, $state, $city, $bank_name, $branch_name, $bank_account_no, $ifsc_code, $profileData, $data, $message, $successMessage, $tabContent;

    public $activeTab = 'panel-1'; // Default active tab

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }
    public function mount(Request $request)
    {
        $companyLogo = new FeatureTopupController;
        $data = $companyLogo->index($request);
        $response = json_decode($data->getContent(), true);
        $this->plans = $response['data'];
    // dd($this->plans);
    }

    public function addToCart($topupId)
    {
        // dd($topupId, $userId = Auth::id());

        // Data stored in session of the selected plan:
        session()->push('cart.topups', [
            'topup_id' => $topupId,
            'user_id' => $userId = Auth::id(),
            // other data
        ]);
        // dd('stored');
        $this->message = 'Topup Added To Cart Successfully';
        // $this->emit('cartUpdated');
    }

    public function render()
    {

        return view('livewire.dashboard.pricing.topups');
    }
}
