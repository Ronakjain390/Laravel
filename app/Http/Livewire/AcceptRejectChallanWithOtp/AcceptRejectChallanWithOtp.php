<?php

namespace App\Http\Livewire\AcceptRejectChallanWithOtp;
use App\Models\Challan;
use Livewire\Component;
use App\Models\CompanyLogo;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Challan\ChallanController;

class AcceptRejectChallanWithOtp extends Component
{
    public $challanId;
    public $action;
    public $emailOrPhone = '';
    public $otp = '';
    public $email;
    public $phone;
    public $successMessage;
    public $errorMessage;
    public $status;
    public $challanHeading;
    public $showOtpModal = false;

    // Livewire will automatically bind the parameter from the URL
    public function mount($challanId)
    {
        $challanId = str_replace('{{1}}', '', $challanId);
        $this->challanId = $challanId;
        // $this->action = request('action', 'accept');
        $receiver = Challan::where('id', $challanId)->with('receiverUser', 'statuses')->first();
        $this->sender = $receiver->sender;
        $challanHeading = CompanyLogo::where('user_id', $receiver->receiver_id)->pluck('challan_heading')->first();
        $this->challanHeading = $challanHeading;
        if($receiver->statuses[0]->status === 'draft' || $receiver->statuses[0]->status === 'sent'){
            // dd('draft');
            $this->email  = $receiver->receiverUser->email;
            $this->phone = $receiver->receiverUser->phone;

        }
        elseif($receiver->statuses[0]->status === 'accept' || $receiver->statuses[0]->status === 'reject'){
            // dd('accept');
            $this->status = $receiver->statuses[0]->status;
            $this->challanHeading = $challanHeading;
             session()->flash('Action Performed', 'Action Performed');
        }
    }


    public function acceptReject($action){
        $this->action = $action;
        $this->showOtpModal = true;
        $request = request();
            $request->merge([
                'email_or_phone'  => $this->phone,
            ]);
            // dd($action);
        $userAuthController = new UserAuthController;

            // Login the user and get the response
            $response = $userAuthController->sendOTPForLogin($request);
            $response = $response->getData();
            if ($response->success == "true") {
                $this->successMessage = $response->message;
                $this->reset(['errorMessage']);


            } else {
                $this->errorMessage = json_encode($response->error ?? [[$response->message]]);
            }
            // Emit event to open the modal
            $this->emit('openOtpModal');
    }

    public function validateOTPForLogin()
    {
        $request = request();
        $request->merge([
            'phone_number' => $this->phone,
            'otp' => $this->otp,
        ]);

        $userAuthController = new UserAuthController;
        $response = $userAuthController->validateOTPForLogin($request);
        $response = $response->getData();
        if ($response->success == "true") {
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);

            // Check the action and perform the corresponding logic
            if ($this->action == 'accept') {
                $this->processAcceptAction($request);
            } elseif ($this->action == 'reject') {
                $this->processRejectAction($request);
            }

        } else {
            $this->errorMessage = json_encode($response->error ?? [[$response->message]]);
        }
    }

    protected function processAcceptAction($request)
    {
        $challanController = new ChallanController;
        $response = $challanController->accept($request, $this->challanId);
        $this->processChallanControllerResponse($response, 'Challan accepted successfully.');
    }

    protected function processRejectAction($request)
    {
        $challanController = new ChallanController;
        $response = $challanController->reject($request, $this->challanId);
        $this->processChallanControllerResponse($response, 'Challan rejected successfully.');
    }

    protected function processChallanControllerResponse($response, $successMessage)
    {
        $result = $response->getData();

        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            session()->flash('success', $successMessage);
            // return redirect()->back()->with('Challan Accepted');
            session()->flush();
            // $this->reset(['errorMessage']);
            return redirect()->route('login')->with(['Challan' => $this->action]);
        } else {
            $this->errorMessage = json_encode($result->errors);
            session()->flush();
        }
    }



    public function render()
    {
        // dd($challanId);
        return view('livewire.accept-reject-challan-with-otp.accept-reject-challan-with-otp')
        ->extends('layouts.home.app')->section('body');
    }
}
