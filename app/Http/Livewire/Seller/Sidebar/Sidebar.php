<?php

namespace App\Http\Livewire\Seller\Sidebar;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\PlanFeatureUsageRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use Illuminate\Support\Facades\DB;

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
    public $featureUsageLimitExceeded = false;

    public function __construct()
    {
        $this->challanController = new ChallanController();
        $this->returnChallanController = new ReturnChallanController();
        $this->receiversController = new ReceiversController();
        $this->panelSeriesNumberController = new PanelSeriesNumberController();
    }

    public function featureRedirect($template, $activeFeature)
    {
        // $panel_id = 1;
        // $filteredItems = array_filter($this->UserDetails, function ($item) use ($panel_id) {
        //     $item = (object) $item;
        //     return $item->panel_id == $panel_id;
        // });
        // // dd($filteredItems);
        // if (!empty($filteredItems)) {
        //     $item = (object) reset($filteredItems); // Get the first item
        //     $this->panel = $item->panel;
        //     // dd($this->panel);
        //     // Store $this->panel in session data
        //     Session::put('panel', $this->panel);

        // }
        $this->emit('featureRoute', $template, $activeFeature);
        $this->template = '';
        $this->activeFeature = '';
        // $this->handleFeatureRoute($template, $activeFeature);
    }

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

    }

    public function checkFeatureUsageLimit()
    {
        // dd('sdx');
        $featureId = 12; // Replace with YOUR_FEATURE_ID for 'Create Challan'
        $PlanFeatureUsageRecord = new PlanFeatureUsageRecord();
        $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->validateUsageLimit($featureId);

        if ($PlanFeatureUsageRecordResponse === 'not_found' || $PlanFeatureUsageRecordResponse === 'expired') {
            $this->featureUsageLimitExceeded = true;
        } else {
            $this->featureUsageLimitExceeded = false;
            // Redirect to the Create Challan page
            // $user = Auth::user();
            // if (is_null($user->address) || is_null($user->pincode) || is_null($user->state) || is_null($user->city)) {
            //     return redirect()->route('profile');
            // } else {
                return redirect()->route('seller', ['template' => 'create_invoice']);
            // }
        }
    }

    public function render()
    {
        $request = request();

        // $this->sentChallan = Cache::rememberForever('sentChallan',  function () use ($request) {
        //     $response = $this->challanController->sidebarCounts($request);
        //     $responseData = json_decode($response->getContent());
        //     return $responseData->count;  // Access the count directly
        // });

        // $this->receivedChallan = Cache::rememberForever('receivedChallan', function () use ($request) {
        //     $tableTdData = $this->returnChallanController->sidebarCounts($request);
        //     $tableTdData = json_decode($tableTdData->getContent());
        //     return $tableTdData->count;  // Access the count directly
        // });

        // $responseContent = $this->receiversController->index($request)->content();
        // $decompressedContent = gzdecode($responseContent);
        // $decodedResponse = json_decode($decompressedContent);

        // if ($decodedResponse === null) {
        //     $this->receiverDatas = [];
        // } else {
        //     $this->receiverDatas = collect($decodedResponse->data)->sortBy(function ($item) {
        //         return strtolower($item->receiver_name);
        //     })->values()->all();
        // }
        // $filterDataset = [
        //     'panel_id' => 1,
        //     'section_id' => 1,
        // ];

        // $request->merge($filterDataset);
        // $request->merge(['panel_id' => '1']);

        // $this->seriesNoData = Cache::rememberForever('seriesNoData', function () use ($request) {
        //     $data = $this->panelSeriesNumberController->index($request);
        //     return $data->getData()->data;
        // });


        $UserResource = new UserAuthController;
        $response3 = $UserResource->features($request, '3');
        $response6 = $UserResource->features($request, '6');
        $response3Data = $response3->getData();
        $response6Data = $response6->getData();
        $templates = array_merge($response3Data->features, $response6Data->features);
        // dd($templates);
        return view('livewire.seller.sidebar.sidebar', [
            'templates' => $templates,
        ]);
    }
}
