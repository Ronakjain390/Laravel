<?php

namespace App\Http\Livewire\Grn\Screen;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use App\Models\FeatureTopupUsageRecord;
use App\Models\PlanFeatureUsageRecord;
use Illuminate\Support\Facades\Session; 
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class Screen extends Component
{
    public $errorMessage, $successMessage, $persistedTemplate;
    public function render()
    {
        Session::put('panel', 'Receipt_Note');
        $featureId = 122;
        // Validate usage limit for PlanFeatureUsageRecord
        $PlanFeatureUsageRecord = new PlanFeatureUsageRecord();
        $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->validateUsageLimit($featureId);
        // dd($PlanFeatureUsageRecordResponse);
       
        $request = request();
    $UserResource = new UserAuthController;
    $userId = $request->user()->id; // assuming the user is authenticated and has an id

    // Try to get the user details from the cache
    $response = Cache::get('user_details_' . $userId);
        // dd($response);
    // If the user details are not in the cache, get them from the UserAuthController and store them in the cache
    if ($response === null) {
        $response = $UserResource->user_details($request);
        $response = $response->getData();
        Cache::put('user_details_' . $userId, $response, 60); // Cache the user details for 60 minutes
    }

    if ($response->success == "true") {
        $this->UserDetails = $response->user->plans;
        $this->user = json_encode($response->user);
        // dd($this->user);
        $this->successMessage = $response->message;
        $this->reset(['errorMessage', 'successMessage']);
    } else {
        $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
    }
        return view('livewire.grn.screen.screen', [
            'PlanFeatureUsageRecordResponse' => $PlanFeatureUsageRecordResponse,
        ]);
    }
}
