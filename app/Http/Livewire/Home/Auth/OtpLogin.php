<?php

namespace App\Http\Livewire\Home\Auth;

use App\Models\User;
use App\Models\Feature;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class OtpLogin extends Component
{
    public $emailOrPhone = '';
    public $password;
    public $user;
    public $isAuthenticated;
    public $persistedTemplate;
    public $successMessage;
    public $errorMessage;
    public $otp = '';
    protected $response = [];

    public function sendOtp()
    {
        // Validate the input data
        $request = request();
        // $validation = $this->validate();

        $request->merge([
            'email_or_phone'  => $this->emailOrPhone,
        ]);
        // dd($request->all());
        // Inject the UserAuthController class as a dependency
        $userAuthController = new UserAuthController;

        // Login the user and get the response
        $response = $userAuthController->sendOTPForLogin($request);
        $response = $response->getData();
        // dd($response);

        if ($response->success == "true") {
            session()->flash('otpSent', 'OTP has been sent successfully on Email.');
            $this->reset(['errorMessage']);
            return redirect()->route('otpconfirmation',['phone'=>$response->phone]);
        } else {
            // dd($response->errors ?? [[$response->message]]);
            $this->errorMessage = json_encode($response->errors ?? [$response->message]);
        }
        // Return success response with user details and token
    }

    public function userRegister(Request $request)
    {
        // Validate the input data
        $this->validate();
        $request->merge([
            'otp' => $this->otp,
        ]);

        // Register the user and get the response
        $userAuthController = app(UserAuthController::class);
        $response = $userAuthController->register($request);

        // Return success response with user details and token
        $this->response = $response->getData();
        session()->flash('success', 'You have been successfully registered!');
        return redirect()->route('dashboard');
    }
    public function render()
    {
        return view('livewire.home.auth.otpLogin')
        ->extends('layouts.home.app')->section('body');
    }
}
