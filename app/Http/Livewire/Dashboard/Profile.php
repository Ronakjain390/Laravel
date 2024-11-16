<?php

namespace App\Http\Livewire\Dashboard;

use App\Http\Controllers\V1\Profile\ProfileController;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Svg\Tag\Rect;

class Profile extends Component
{
    public $errorMessage, $validationErrorsJson, $statusCode, $updatedProfileData;

    public $name, $email, $special_id, $company_name, $address, $pincode, $phone, $gst_number, $pancard, $state, $city, $bank_name, $branch_name, $bank_account_no, $ifsc_code, $profileData, $data, $message, $successMessage;

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

    public function mount()
    {
        $id = '';
        $request = request();
        $userProfile = new ProfileController;
        $data = $userProfile->index($request, $id);
        $this->profileData = json_decode($data->getContent(), true);
        $this->updateUserProfile();
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
        // array_push($this->updateProfileData, [
        //     'name' => $this->profileData['profile']['name'],
        //     'email' => $this->profileData['profile']['email'],
        //     'company_name' => $this->profileData['profile']['company_name'],
        //     'address' => $this->profileData['profile']['address'],
        //     'pincode' => $this->profileData['profile']['pincode'],
        //     'gst_number' => $this->profileData['profile']['gst_number'],
        //     'pancard' => $this->profileData['profile']['pancard'],
        //     'state' => $this->profileData['profile']['state'],
        //     'city' => $this->profileData['profile']['city'],
        //     'bank_name' => $this->profileData['profile']['bank_name'],
        //     'branch_name' => $this->profileData['profile']['branch_name'],
        //     'bank_account_number' => $this->profileData['profile']['branch_name'],
        //     'ifsc_code' => $this->profileData['profile']['ifsc_code'],
        // ]);

        // dd($this->updateProfileData);

    }

    public function updateData(){
        $request =  request();
        $request->merge($this->updateProfileData);
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

    public function render()
    {


        return view('livewire.dashboard.profile.profile');
    }
}
