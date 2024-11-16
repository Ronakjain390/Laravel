<?php

namespace App\Http\Livewire\Admin\Dashboard;

use stdClass;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\Plans\PlansController;
use App\Http\Controllers\V1\Panel\PanelController;
use App\Http\Controllers\V1\FeatureTopup\FeatureTopupController;


class Topup extends Component
{
    public $allTopupData, $allPanelData, $selectedPanel, $selectedFeatures, $package_name, $heading1value, $heading2value, $seriesNumberFeatureValue, $usersvalue, $price, $validity_days,$selectedFeatureId, $errors, $statusCode, $message;
    public $selectedPanel1 = null;
    public $selectedPanel2 = null;
    public $heading1Feature;
    public $heading2Feature;
    public $seriesNumberFeature;

    public $topupData = array(
        'plan_name' => '',
        'price' => '',
        'feature_id' => null,
        'usage_limit' => '',
        'status' => 'active',
        'comment' => '',
        
        
    );


   
    
    public function mount()
    {
        // dd('ash');
        $request = request();
        // dd($request);
        $allTopups = new FeatureTopupController;
        $topupData = $allTopups->index($request);
        $columnsData = json_encode($topupData->getData()->data);
        // dd($columnsData);
        $this->allTopupData = $columnsData;

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
        // $this->selectedFeatureIds = '';
        $this->selectedFeatures = $decodedPanel->features;
        switch ($decodedPanel->panel_name) {
            case 'Sender':
                $this->heading1Feature = $decodedPanel->features[0]->feature_name;
                $this->selected1FeatureIds = $decodedPanel->features[0]->id;
                $this->heading2Feature = $decodedPanel->features[2]->feature_name;
                $this->selected2FeatureIds = $decodedPanel->features[2]->id;
                $this->seriesNumberFeature = $decodedPanel->features[4]->feature_name;
                $this->selected3FeatureIds = $decodedPanel->features[4]->id;
                break;

            case 'Receiver':
                $this->heading1Feature = $decodedPanel->features[0]->feature_name;
                $this->selected1FeatureIds = $decodedPanel->features[0]->id;
                $this->heading2Feature = $decodedPanel->features[2]->feature_name;
                $this->selected2FeatureIds = $decodedPanel->features[2]->id;
                $this->seriesNumberFeature = $decodedPanel->features[3]->feature_name;
                $this->selected3FeatureIds = $decodedPanel->features[3]->id;
                break;
            case 'Seller':
                $this->heading1Feature = $decodedPanel->features[0]->feature_name;
                $this->selected1FeatureIds = $decodedPanel->features[0]->id;
                $this->heading2Feature = $decodedPanel->features[2]->feature_name;
                $this->selected2FeatureIds = $decodedPanel->features[2]->id;
                $this->seriesNumberFeature = $decodedPanel->features[5]->feature_name;
                $this->selected3FeatureIds = $decodedPanel->features[5]->id;
                break;
            case 'Buyer':
                $this->heading1Feature = $decodedPanel->features[1]->feature_name;
                $this->selected1FeatureIds = $decodedPanel->features[1]->id;
                $this->heading2Feature = $decodedPanel->features[0]->feature_name;
                $this->selected2FeatureIds = $decodedPanel->features[0]->id;
                // $this->seriesNumberFeature = $decodedPanel->features[3]->feature_name;
                break;

            default:
                // Handle default case if necessary
                break;
        }
    }


    public function createTopup(Request $request){
        $request->merge($this->topupData);
        // dd($request);
        // dd($this->selectedFeatures);
        // if (isset($this->selectedFeatures) && count($this->selectedFeatures) > 0) {
        //     $featureIds = $this->selectedFeatureIds;
        //      // Access the feature_id
    
        //     $request->merge([
        //         'feature_id' => $featureIds, 
        //     ]);
        // }
        
        // dd($request);
        $PlansController = new FeatureTopupController;

        $response = $PlansController->store($request);
        $result = $response->getData();
        // dd($result);
        $this->message = $result->message;
        
    }

    public function render()
    {
        // dd('jsdn');
        return view('livewire.admin.dashboard.topups.topups', [
            'heading1Feature' => $this->heading1Feature,
            'heading2Feature' => $this->heading2Feature,
            'seriesNumberFeature' => $this->seriesNumberFeature,
            // 'validity_days' => $this->validity_days,
        ]);
    }
}