<?php

namespace App\Http\Livewire\Grn\Sidebar;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\FeatureTopupUsageRecord;
use App\Models\PlanFeatureUsageRecord;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use Illuminate\Support\Facades\DB; 
use App\Http\Livewire\Grn\Screen\CreateGoodsReceipt;

class Sidebar extends Component
{
    public $panelId;
    public $sidenav;
    public $user, $UserDetails, $successMessage, $errorMessage;
    public $template;
    public $activeFeature;
    public $userData;
    public $sentChallan;
    private $challanController;
    private $returnChallanController;
    private $receiversController;
    private $panelSeriesNumberController;

    public function mount(Request $request)
    {
        $UserResource = new UserAuthController;
        $response = $UserResource->user_details($request);
        $response = $response->getData();
    
        if ($response->success == "true") {
            $this->UserDetails = $response->user->plans;
            $this->user = json_encode($response->user);
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
    
        $query = new UserAuthController;
        $response = $query->userActivePlan($request);
        $response = $response->getData();
    
        // Only keep the "Sender" data
        $response->user = (object) ['Sender' => $response->user->Sender];
        
        if ($response->success == "true") {
            $this->activePlan = json_encode($response->user);
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
        


        return $this->UserDetails;
    }
    
    public function featureRedirect($template, $activeFeature)
    {
        // Check if the template matches 'create_goods_receipt'
        if ($template === 'create_goods_receipt') {
            // Instantiate the CreateGoodsReceipt component
            $createGoodsReceipt = new CreateGoodsReceipt();
            // Optionally, you can pass the $activeFeature to the component if needed
            $createGoodsReceipt->activeFeature = $activeFeature;
            // Call the render method to return the view
            return $createGoodsReceipt->render();
        }

        // Default handling for other templates
        // dd($template, $activeFeature);
        // $this->emit('featureRoute', $template, $activeFeature); 
        $this->template = '';
        $this->activeFeature = '';
        // $this->handleFeatureRoute($template, $activeFeature);
    }
    public function render()
    {
        $featureId = 122; // Replace with YOUR_FEATURE_ID
        // dd($featureId);
        // Validate usage limit for PlanFeatureUsageRecord
        $PlanFeatureUsageRecord = new PlanFeatureUsageRecord();
        $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->validateUsageLimit($featureId);
        // dd($PlanFeatureUsageRecordResponse);
       

        $request = request();
        $UserResource = new UserAuthController;
        $response = $UserResource->features($request, '5');
        $response = $response->getData();
        $templates = $response->features;

        return view('livewire.grn.sidebar.sidebar', [
            'templates' => $templates, 
            'PlanFeatureUsageRecordResponse' => $PlanFeatureUsageRecordResponse
        ]);
    }
}
