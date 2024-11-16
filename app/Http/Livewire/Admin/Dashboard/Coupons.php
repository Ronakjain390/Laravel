<?php

namespace App\Http\Livewire\Admin\Dashboard;

use stdClass;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\Coupons\CouponController;
 


class Coupons extends Component
{
    public $usage_limit, $statusCode, $successMessage, $errorMessage, $message, $success, $error;
    public $couponDataset = array(
        'usage_limit' => '',
        'code' => '',
        'valid_from' => '',
        'valid_to' => '',
        'discount_amount' => '',
        'status' => '',
        'applicable_on' => '',
        'valid_to' => '',
        'usage_count' => '',
        'discount_basis' => '',
    );
    public function createCoupons(Request $request)
    
    {
        $request->merge($this->couponDataset);
        // dd($request);
        $planData = new CouponController;
        $response = $planData->create($request);
        $result = $response->getData();
        // Check if the operation was successful and flash the message to the session
        if ($result->success) {
            session()->flash('message', $result->message);
            reset($this->couponDataset);
        }else{
            session()->flash('error', $result->message);
        }
        
    }
   
 

    public function render()
    { 
        $request = request();
        $planData = new CouponController;
        $response = $planData->index($request);
        // $result = $response->getData();
        $this->columnsData = json_encode($response->getData()->data);
        // dd($columnsData);
        return view('livewire.admin.dashboard.coupons.coupons');
    }
}



