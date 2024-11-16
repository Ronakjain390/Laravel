<?php

namespace App\Http\Livewire\Admin\Dashboard;

use stdClass;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\Plans\PlansController;
use App\Http\Controllers\V1\Panel\PanelController;
use App\Http\Controllers\V1\Feature\FeatureController;
use App\Http\Controllers\V1\PlanFeature\PlanFeatureController;


class Packages extends Component
{
    public $allPlansData, $allPanelData, $selectedPanel, $selectedFeatures, $package_name, $heading1value, $heading2value, $seriesNumberFeatureValue, $usersvalue, $price, $validity_days,$selectedFeatureId, $errors, $statusCode, $message, $successMessage, $errorMessage;
    public $selectedPanel1 = null;
    public $selectedPanel2 = null;
    public $heading1Feature;
    public $heading2Feature;
    public $seriesNumberFeature;

    public $planData = array(
        'plan_name' => '',
        'price' => '',
        'discounted_price' => '',
        'section_id' => '',
        'panel_id' => '',
        'validity_days' => '',
        'status' => 'active',
        'comment' => '',
        'user' => '',
        'topup' => '',
        'feature_usage_limit' => ([
        ]), 
    );


   
    
    public function mount()
    {
        // dd('ash');
        $request = request();

        // Merge the "status" key with the value "active" into the request data
        $request->merge(['status' => 'active']);
        
        $allPlans = new PlansController;
        $plansData = $allPlans->index($request);
        $columnsData = json_encode($plansData->getData()->data);
        
        // dd($columnsData);
        $this->allPlansData = $columnsData;

        $panel = new  PanelController;
        $allPanel = $panel->index($request);
        $allPanel = json_encode($allPanel->getData()->data);
        $this->allPanelData = $allPanel;
        // dd($allPanel);
    }

    public function selectPanel($panel)
    {
        $decodedPanel = json_decode($panel);
        // dd($decodedPanel);
        $this->selectedFeatureIds = [];
        $this->selectedFeatures = $decodedPanel->features;
        // dd($this->selectedFeatures);
        switch ($decodedPanel->panel_name) {
            case 'Sender':
                $this->heading1Feature = $decodedPanel->features[0]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[0]->id;
                $this->heading2Feature = $decodedPanel->features[2]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[2]->id;
                $this->seriesNumberFeature = $decodedPanel->features[4]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[4]->id;
                break;

            case 'Receiver':
                $this->heading1Feature = $decodedPanel->features[0]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[0]->id;
                $this->heading2Feature = $decodedPanel->features[2]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[2]->id;
                $this->seriesNumberFeature = $decodedPanel->features[3]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[3]->id;
                break;
            case 'Seller':
                $this->heading1Feature = $decodedPanel->features[0]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[0]->id;
                $this->heading2Feature = $decodedPanel->features[2]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[2]->id;
                $this->seriesNumberFeature = $decodedPanel->features[5]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[5]->id;
                break;
            case 'Buyer':
                $this->heading1Feature = $decodedPanel->features[1]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[1]->id;
                $this->heading2Feature = $decodedPanel->features[0]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[0]->id;
                // $this->seriesNumberFeature = $decodedPanel->features[3]->feature_name;
                break;
            case 'Goods Receipt Note':
                $this->heading1Feature = $decodedPanel->features[0]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[0]->id;
                $this->heading2Feature = $decodedPanel->features[4]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[4]->id;
                $this->seriesNumberFeature = $decodedPanel->features[3]->feature_name;
                $this->selectedFeatureIds[] = $decodedPanel->features[3]->id;
                break;

            default:
                // Handle default case if necessary
                break;
        }
    }
    

    public $additionalFeatures = [];

    public function addFeature()
    {
        $this->additionalFeatures[] = ['name' => '', 'usage_limit' => ''];
    }

    public function removeFeature($index)
    {
        unset($this->additionalFeatures[$index]);
        $this->additionalFeatures = array_values($this->additionalFeatures); // Reindex the array
    }

    public function createPackage(Request $request)
    {
        // Merge the planData into the request
        $request->merge($this->planData);
       
        // If selected features are set, add panel_id, section_id, and feature_id to the request
        if (isset($this->selectedFeatures) && count($this->selectedFeatures) > 0) {
            $panelId = $this->selectedFeatures[0]['panel_id'];
            $sectionId = $this->selectedFeatures[0]['section_id'];
            $featureIds = $this->selectedFeatureIds;
    
            $request->merge([
                'panel_id' => $panelId,
                'section_id' => $sectionId,
                'feature_id' => $featureIds, // Add feature_id to the request
            ]);
        }
       
        // Include additional features in the request
        $additionalFeatures = $this->additionalFeatures;
        $additionalFeatureData = [];
        foreach ($additionalFeatures as $feature) {
            $additionalFeatureData[] = [
                'feature_name' => $feature['name'],
                'feature_usage_limit' => $feature['usage_limit'],
            ];
        }
        $request->merge(['additional_features' => $additionalFeatureData]);
         
       
        if($request->additional_features){
            foreach($request->additional_features as $feature){
                // Check if feature_name is set and not empty
                if (!isset($feature['feature_name']) || empty($feature['feature_name'])) {
                    return response()->json(['error' => 'The feature name field is required.'], 422);
                }

                // If additional feature is added, then call the FeaturesController to store the feature
                $FeaturesController = new FeatureController;
                $featureRequest = new Request($feature);
                $response = $FeaturesController->store($featureRequest);
                $result = $response->getData();
                // dd($result);
                // Check if there was an error in the response
                if (isset($result->errors)) {
                    // Flash the error message to the session
                    session()->flash('error', $result->errors);
                    return back();
                }
                

                // Check if the feature already exists
                if ($result->status_code == 409) {
                    // Flash the error message to the session
                    session()->flash('error', ['Feature already exists.']);
                    return back();
                }

                // Append the feature id and feature usage limit to the arrays in the request
                $featureIds = $request->input('feature_id', []);
                $featureIds[] = $result->data->id;
                $request->request->set('feature_id', $featureIds);

                $featureUsageLimits = $request->input('feature_usage_limit', []);
                $featureUsageLimits[] = $feature['feature_usage_limit'];
                $request->request->set('feature_usage_limit', $featureUsageLimits);
            }
        }
      
        
        // // For debugging purposes
        // dd($request->all());
    
        // Create the package using the PlansController
        $PlansController = new PlansController;
        $response = $PlansController->store($request);
        $result = $response->getData();
        // dd($result);
        $planId = $result->data->id;
    
        // $planFeatureController = new PlanFeatureController;
        $planFeatureController = new PlanFeatureController;
        foreach ($featureIds as $key => $feature) {
            $request->merge([
                'plan_id' => $planId,
                'feature_id' => $feature,
                'feature_usage_limit' => isset($featureUsageLimits[$key]) ? $featureUsageLimits[$key] : null, 
            ]);

            $response = $planFeatureController->store($request);
        }
    
        // dd($result);
        
        $result = $response->getData();
        if (isset($result->status_code) && $result->status_code === 201) {
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = isset($result->errors) ? json_encode($result->errors) : 'An error occurred.';
        }
    
        $this->reset(['planData', 'additionalFeatures']);
    }
    

    public function editPackage($data)
    {
        // Set the initial values for the modal input fields
        $this->planData = [
            'id' => $data['id'],
            'plan_name' => $data['plan_name'],
            'price' => $data['price'],
            'section_id' => $data['section_id'],
            'panel_id' => $data['panel_id'],
            'validity_days' => $data['validity_days'],
            'status' => 'active',
            'comment' => $data['comment'],
            'user' => $data['user'],
            'topup' => $data['topup'],
            'features' => $data['features'],
        ]; 
        // dd($this->planData);
        // Open the modal
        $this->dispatchBrowserEvent('openEditModal');
    }
    public function updatePackage(Request $request){
        // $request = new request();
        $request->replace([]);
        $request->merge($this->planData);
          $newUserController = new PlansController;
  
          $response = $newUserController->update($request, $request->id);
          $result = $response->getData();
        //   dd($result);
        if (isset($result->status_code) && $result->status_code === 200) {
            $this->successMessage = $result->message;
            // dd($this->successMessage);
            // Additional logic if needed for a successful response
        } else {
            $this->errorMessage = isset($result->errors) ? json_encode($result->errors) : 'An error occurred.';
            // Additional logic if needed for an unsuccessful response
        }
    }

    public function deletePackage($id)
    {
        $controller = new PlansController;
        $controller->delete($id);
        // $this->emit('triggerDelete', $id);
        // $this->mount();
        // dd('delete');
        // $this->reset(['addAddress']);
    }
 
    public function render()
    {
        // dd('jsdn');
        return view('livewire.admin.dashboard.packages.packages', [
            'heading1Feature' => $this->heading1Feature,
            'heading2Feature' => $this->heading2Feature,
            'seriesNumberFeature' => $this->seriesNumberFeature,
            'validity_days' => $this->validity_days,
        ]);
    }
}