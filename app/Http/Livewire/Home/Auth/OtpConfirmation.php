<?php

namespace App\Http\Livewire\Home\Auth;

use App\Models\User;
use App\Models\Feature;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class OtpConfirmation extends Component
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

    public function mount(Request $request)
    {
        $this->emailOrPhone = $request->phone;
        if (session()->has('otpSent')) {
            $this->successMessage = session('otpSent');
        }
    }

    public function validateOTPForLogin()
    {
        // Validate the input data
        $this->validate([
            'otp' => 'required|string|min:4|max:4', // Assuming OTP is 6 digits
        ]);

        $request = request();
        $request->merge([
            'phone_number' => $this->emailOrPhone,
            'otp' => $this->otp,
        ]);

        // Inject the UserAuthController class as a dependency
        $userAuthController = new UserAuthController;

        // Login the user and get the response
        $response = $userAuthController->validateOTPForLogin($request);
        $response = $response->getData();
        // dd($response);
        if ($response->success == "true") {
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);
            return redirect()->route('dashboard');
        } else {
            $this->errorMessage = $response->message; // Store the error message directly
        }
    }

    public function userRegister(Request $request)
    {
        $this->validate();
        $request->merge([
            'otp' => $this->otp,
        ]);

        // Register the user and get the response
        $userAuthController = app(UserAuthController::class);
        $response = $userAuthController->register($request);

        $this->response = $response->getData();
        session()->flash('success', 'You have been successfully registered!');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.home.auth.otpConfirmation')
            ->extends('layouts.home.app')->section('body');
    }
}
