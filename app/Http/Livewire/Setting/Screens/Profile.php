<?php

namespace App\Http\Livewire\Setting\Screens;

use Livewire\Component;
use Svg\Tag\Rect;
use WithDebounce;
use Illuminate\Http\Request;
use App\Models\EmailVerifications;
use App\Models\PhoneVerifications;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\V1\Profile\ProfileController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class Profile extends Component
{
    public $errorMessage, $validationErrorsJson, $statusCode, $updatedProfileData, $error;
    public $activeTab = 'panel-1';
    public $name, $email, $special_id, $company_name, $address, $pincode, $phone, $gst_number, $pancard, $state, $city, $bank_name, $branch_name, $bank_account_no, $ifsc_code, $profileData, $data, $message, $successMessage;
    public $otpSent = false;
    public $emailOTPSent = false;
    public $dataUpdate = false;
    protected $listeners = ['cityAndStateByPincode'];
    public $updateProfileData = array(
        'name' => '',
        'email' => '',
        'company_name' => '',
        'address' => '',
        'phone' => '',
        'pincode' => '',
        'gst_number' => '',
        'pancard' => '',
        'state' => '',
        'city' => '',
        'bank_name' => '',
        'branch_name' => '',
        'bank_account_number' => '',
        'ifsc_code' => '',
    );
    public $challanDesignData = array(
        [
            'name' => '',
        'email' => '',
        'company_name' => '',
        'address' => '',
        'phone' => '',
        'pincode' => '',
        'gst_number' => '',
        'pancard' => '',
        'state' => '',
        'city' => '',
        'bank_name' => '',
        'branch_name' => '',
        'bank_account_number' => '',
        'ifsc_code' => '',
        ]

    );
    
    public function mount()
    { 
        $request = new request();
        $challanController = new UserAuthController();
        $tableTdData = $challanController->index($request);
        $this->tableTdData = $tableTdData->getData()->data;

        $userProfile = new ProfileController;
        $data = $userProfile->index($request);
        // $this->challanDesignData = $data->getData() ;

        $this->profileVerificationData = json_encode($data->getData());
        $updateProfileData = json_decode($data->getContent(), true);
        $this->updateProfileData = $updateProfileData['profile'];

        if (isset($this->updateProfileData['city']) && isset($this->updateProfileData['state']) && isset($this->updateProfileData['pincode'])) {
        $this->city = $this->updateProfileData['city'];
        $this->state = $this->updateProfileData['state'];
        $this->pincode = $this->updateProfileData['pincode'];
        $this->address = $this->updateProfileData['address'];
        }
        // Email verification
        $this->emailVerification = EmailVerifications::where('user_id', Auth::user()->id)->pluck('verified_at')->first();
        // dd($emailVerification);
        // Phone verification
        $this->phoneVerification = PhoneVerifications::where('user_id', Auth::user()->id)->pluck('verified_at')->first();
        

    }
  
    public function cityAndStateByPincode(): void
    {
        $pincode = $this->updateProfileData['pincode'];
        // dd($this->updateProfileData['pincode']);
        // dd($this->updateProfileData['address']);
        $receiverController = new ReceiversController();
        $response = $receiverController->fetchCityAndStateByPincode($pincode);
        $result = $response->getData();
        // dd($result);
        if (isset($result->city) && isset($result->state)) {
            // Update the city and state fields
            $this->city = $result->city;
            // dd($this->city);
            $this->state = $result->state;
            $this->updateProfileData['city'] = $result->city;
            $this->updateProfileData['state'] = $result->state;
            $this->updateProfileData['pincode'] = $this->updateProfileData['pincode'];
            $this->updateProfileData['address'] = $this-> address;
        }
      
    }
    public function updateUserProfile()
    {

        $request =  request();
        $request->replace([]);

        // dd($this->updateProfileData);
        // dd($this->profileData['profile']['name']);
        $this->updateProfileData = [
            'name' => $this->profileData['profile']['name'],
            'email' => $this->profileData['profile']['email'],
            'company_name' => $this->profileData['profile']['company_name'],
            'address' => $this->profileData['profile']['address'],
            'phone' => $this->profileData['profile']['phone'],
            'pincode' => $this->profileData['profile']['pincode'],
            'gst_number' => $this->profileData['profile']['gst_number'],
            'pancard' => $this->profileData['profile']['pancard'],
            'state' => $this->profileData['profile']['state'],
            'city' => $this->profileData['profile']['city'],
            'bank_name' => $this->profileData['profile']['bank_name'],
            'branch_name' => $this->profileData['profile']['branch_name'],
            'bank_account_number' => $this->profileData['profile']['branch_name'],
            'ifsc_code' => $this->profileData['profile']['ifsc_code'],
            'special_id' => $this->profileData['profile']['special_id'],
        ];
         

    }

    public function updateData(){
        $this->cityAndStateByPincode(); // Add this line immediately after initializing $this->updateProfileData

        $request = request()->merge($this->updateProfileData);
        // $request =  request();
        $request->merge($this->updateProfileData);
        // dd($request );
        $id = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $updateProfile = new ProfileController;
        $response =  $updateProfile->update($request, $id);
        $result = $response->getData();
        $this->statusCode = $result->status_code;
        // dd($result);
        if ($result->status_code === 200) {
            $this->successMessage = $result->message;

        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        $this->mount();
    }

    

    public function sendOTP($type){
        $request =  request();
        if($type === 'phone'){
            $this->otpSent = true;
        }
        else{
            $this->emailOTPSent = true;
        }
        $updateProfile = new UserAuthController;
        $response =  $updateProfile->sendOtpForVerification($request, $type);
        // dd($response);
        $result = $response->getData();
        // dd($result);
        $this->statusCode = $result->status_code;
        $this->resendDisabled = true;
        $this->emit('otpSent');
        // Initialize resend timer to 120 seconds
        $this->resendTimer = 120;
        // Enable "Resend OTP" button after 2 minutes
        $this->emit('enableResend');
        if ($result->status_code === 200) {
            $this->successMessage = $result->message;

        } else {
            $this->errorMessage = json_encode($result->errors);
        }

    }

    public function enableResend()
    {
        $this->resendDisabled = false;
    }

    public $phoneVerification = null;
    public $emailVerification = null;
    public $otp;
    public $resendDisabled = true;
    public $resendTimer = 0;

    public function verifyOTP()
    { 
        // $request = request();
        $otp = $this->otp;
        // dd($otpValue);
        $updateProfile = new UserAuthController;
        $response = $updateProfile->verifyOTP($otp, 'email');
        $result = $response->getData();
        // dd($result->status_code);
        // $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->emailOTPSent = false;
            $this->resendDisabled = true;
            $this->successMessage = $result->message;
            return redirect()->route('profile');
        $this->emailOTPSent  = false;
        } else {
            $this->error = $result->message;
        }
        
    }
    public function verifyPhoneOTP()
    { 
        $otp = $this->otp;
        $this->reset('error', 'successMessage');
        $updateProfile = new UserAuthController;
        $response = $updateProfile->verifyOTP($otp, 'phone');
        $result = $response->getData();

        if ($result->status_code === 200) {
            $this->otpSent = false;
            $this->resendDisabled = true;
            $this->successMessage = $result->message;
            $this->otpSent = false;
            return redirect()->route('profile');
        } else {
            $this->error = $result->message; // Set the error message here
        }
    }
    


    public function render()
    {
        
        $request = new request();
 

        
        // dd($this->updateProfileData['city']);
        

        // $newChallanDesign = new PanelColumnsController;
        // $response = $newChallanDesign->index($request);
        // $this->challanDesignData = $response->getData()->data;

        // $this->updateUserProfile();
        return view('livewire.setting.screens.profile');
    }
}
