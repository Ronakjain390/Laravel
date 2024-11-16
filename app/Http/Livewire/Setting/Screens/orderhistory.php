<?php

namespace App\Http\Livewire\Setting\Screens;

use App\Models\Buyer;
use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\V1\Orders\OrdersController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class orderhistory extends Component
{
    public $panelName;
    public $companyLogoData, $planData, $featureData, $showModal = false, $orderData, $orderDetail,  $errorMessage, $index, $companyLogo, $user, $successMessage;
    public $UserDetails;
    // public function mount(Request $request)
    // {

    //     $companyLogo = new OrdersController;
    //     $response = $companyLogo->userIndex($request);
    //     $response = $response->getData();
    //     // dd($response);

    //     if ($response->message == "Success") {
    //         $this->UserDetails = $response->data->data;
    //         // $this->user = json_encode($response->user);
    //         $this->successMessage = $response->message;
    //         $this->reset(['errorMessage']);
    //     } else {
    //         $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
    //     }
    // }
    // public function updated(){
    //     $this->UserDetails;
    // }

    public function openPlanModal($order_detail)
    {
        $this->orderDetail = $order_detail;
        $this->showModal = true;

    }
    public function openModal()
    {
        $this->showModal = true;
    }

    public function findOrder($order_id, $plan_id)
    {
        // Initialize variables to store the values
        $totalUsageCount = null;
        $panelName = null;
        $planName = null;
        $totalUsageCountCreateChallan = null;
        $totalUsageCountViewReceiver = null;
        $totalUsageCountChallanSeries = null;

        // Search for the order in your data based on order_id and plan_id
        foreach ($this->companyLogoData as $order) {
            if ($order['panel']['id'] == $order_id) {
                foreach ($order['panel']['feature'] as $feature) {
                    if ($feature['feature_name'] === "Create Challan") {
                        $totalUsageCountCreateChallan = $feature['total_usage_count'];
                    } elseif ($feature['feature_name'] === "View Receiver") {
                        $totalUsageCountViewReceiver = $feature['total_usage_count'];
                    } elseif ($feature['feature_name'] === "Challan Series Number") {
                        $totalUsageCountChallanSeries = $feature['total_usage_count'];
                    }
                }
                foreach ($order['panel']['feature'][0]['plans'] as $plan) {
                    if ($plan['id'] == $plan_id) {
                        $totalUsageCount = $order['panel']['feature'][0]['total_usage_count'];
                        $panelName = $order['panel']['panel_name'];
                        $planName = $plan['plan_name'];
                        break; // Exit the loop once you find the order
                    }
                }
            }
        }

        // Create an array to hold the values
        $orderData = [
            'total_usage_count' => $totalUsageCount,
            'panel_name' => $panelName,
            'plan_name' => $planName,
            'total_usage_count_create_challan' => $totalUsageCountCreateChallan,
            'total_usage_count_view_receiver' => $totalUsageCountViewReceiver,
            'total_usage_count_challan_series' => $totalUsageCountChallanSeries,
        ];
        // dd($orderData);
        return $this->orderData;
    }

    // public function findOrder($order_id, $plan_id)
    // {
    //     // Initialize variables to store the values
    //     $totalUsageCount = null;
    //     $panelName = null;
    //     $planName = null;
    //     $totalUsageCountCreateChallan = null;
    //     $totalUsageCountViewReceiver = null;
    //     $totalUsageCountChallanSeries = null;

    //     // Search for the order in your data based on order_id and plan_id
    //     foreach ($this->companyLogoData as $order) {
    //         if ($order['panel']['id'] == $order_id) {
    //             foreach ($order['panel']['feature'] as $feature) {
    //                 if ($feature['feature_name'] === "Create Challan") {
    //                     $totalUsageCountCreateChallan = $feature['total_usage_count'];
    //                 } elseif ($feature['feature_name'] === "View Receiver") {
    //                     $totalUsageCountViewReceiver = $feature['total_usage_count'];
    //                 } elseif ($feature['feature_name'] === "Challan Series Number") {
    //                     $totalUsageCountChallanSeries = $feature['total_usage_count'];
    //                 }
    //             }
    //             foreach ($order['panel']['feature'][0]['plans'] as $plan) {
    //                 if ($plan['id'] == $plan_id) {
    //                     $totalUsageCount = $order['panel']['feature'][0]['total_usage_count'];
    //                     $panelName = $order['panel']['panel_name'];
    //                     $planName = $plan['plan_name'];
    //                     break; // Exit the loop once you find the order
    //                 }
    //             }
    //         }
    //     }

    //     // Assign the values to the orderData property
    //     $this->orderData = [
    //         'total_usage_count' => $totalUsageCount,
    //         'panel_name' => $panelName,
    //         'plan_name' => $planName,
    //         'total_usage_count_create_challan' => $totalUsageCountCreateChallan,
    //         'total_usage_count_view_receiver' => $totalUsageCountViewReceiver,
    //         'total_usage_count_challan_series' => $totalUsageCountChallanSeries,
    //     ];
    //     // dd($this->orderData );
    // }

    public function closeModal()
    {
        $this->showModal = false;
    }




    // public function openModal($planData)
    // {
    //     $this->planData = json_decode($planData);
    //     $this->emit('openModal'); // Emit an event to trigger modal opening.
    // }



    // public function openPlanModal($featureData)
    // {
    //     // dd($featureData);
    //     $this->featureData = $featureData;
    //     $this->showModal = true;
    // }

    public function closePlanModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        // dd($this->UserDetails);
        $request = request();
        $companyLogo = new OrdersController;
        $response = $companyLogo->userIndex($request);
        $response = $response->getData();
        // dd($response);

        if ($response->message == "Success") {
            $this->UserDetails = $response->data->data;
            // $this->user = json_encode($response->user);
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
        return view('livewire.setting.screens.orderHistory');
    }
}
