<?php

namespace App\Http\Livewire\Grn\Screen;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\ReceiverGoodsReceipt\ReceiverGoodsReceiptsController;

class AddGoodsReceiver extends Component
{
    public $manuallyAdded, $success, $message, $errorMessage, $statusCode ,$session;
    public $showInputBoxes = true;
    public $addReceiverData = array(
        'receiver_name' => '',
        'company_name' => '',
        'email' => '',
        'address' => '',
        'pincode' => '',
        'state' => '',
        'city' => '',
        'phone' => '',
        'organisation_type' => '',
        'receiver_special_id' => '',
        'gst_number'=> '',
    );
    public $activeTab = 'receiver-manually';

    public function callAddReceiverManually(Request $request)
    {
       // Assuming $data contains your 'addReceiverData' array
    // $data = $request->all(); // Or however you're obtaining the data

    // // Create a validator instance
    // $validator = Validator::make($data, [
    //     'receiver_name' => 'required|string|max:255',
    //     'email' => 'nullable|email',
    //     'address' => 'nullable|string|max:255',
    //     'pincode' => 'nullable|integer',
    //     'phone' => 'nullable|string|unique:users,phone',
    //     'gst_number' => 'nullable|string|max:191',
    //     'state' => 'nullable|string|max:75',
    //     'city' => 'nullable|string|max:75',
    //     'organisation_type' => 'nullable|string',
    //     'company_name' => 'nullable|string',
    // ]);

    // // Check if validation fails
    // if ($validator->fails()) {
    //     return response()->json([
    //         'status' => 400,
    //         'message' => 'Validation Error',
    //         'errors' => $validator->errors(),
    //     ], 400);
    // }

        
        
        $request->merge($this->addReceiverData); 
       
        $ReceiversController = new ReceiverGoodsReceiptsController;

        $response = $ReceiversController->addManualReceiver($request);

        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status;
     
        if ($this->statusCode === 200) {
            // $this->successMessage = $result->message;
           session()->flash('message', $result->message);
            $this->addReceiverData = []; 
            $this->reset(['message']);
            // $this->reset(['createChallanRequest',  'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
            $this->reset([ 'message']);
        }
    }

    public function checkPincodeLength($pincode)
    {
        if (strlen($pincode) === 6) {
            $this->cityAndStateByPincode($pincode);
        }
    }
        public function cityAndStateByPincode()
        {
            $pincode = $this->addReceiverData['pincode'];
            // dd($pincode);
    
            $receiverController = new ReceiverGoodsReceiptsController();
            $response = $receiverController->fetchCityAndStateByPincode($pincode);
            $result = $response->getData();
            // dd($result);
            if (isset($result->city) && isset($result->state)) {
                // Update the city and state fields
                $this->addReceiverData['city'] = $result->city;
                $this->addReceiverData['state'] = $result->state;
            }
        }
    public function render()
    {
        return view('livewire.grn.screen.add-goods-receiver');
    }
}
