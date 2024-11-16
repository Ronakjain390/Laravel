<?php

namespace App\Http\Livewire\Home\Auth;

use App\Models\User;
use App\Models\Feature;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class ForgotPasswordOtp extends Component
{
    public $emailOrPhone = '';
    public $password;
    public $user;
    public $isAuthenticated;
    public $persistedTemplate;
    public $successMessage;
    public $errorMessage;
    public $isProcessing = false;
    public $otp = '';
    public $phone;
    public $token;
    public $errorDetails = [];

    protected $response = [];

    public function mount(Request $request)
    {
        // dd($request);
        $this->emailOrPhone = $request->phone;
    }
    public function validateOTPForLogin()
    {
        // Validate the input data
        $request = request();

        $request->merge([
            'phone_number'  => $this->emailOrPhone,
            'otp'  => $this->otp,
            // 'token' => $this->token,

        ]);

        // Inject the UserAuthController class as a dependency
        $userAuthController = new UserAuthController;

        // Login the user and get the response
        $response = $userAuthController->validateOTPForResetPassword($request);
        $response = $response->getData();
        // dd($response);
        // return redirect()->route('changepassword');

        if ($response->success) {
            // Set success message
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);
            session()->flush();
        
            // Store tokenForReset and userPhone in the session
            session(['tokenForReset' => $response->tokenForReset, 'userPhone' => $response->user->phone]);
        
            // Redirect to the changepassword route
            return redirect()->route('changepassword');
            
        } else {
            // dd($response);
        $this->errorMessage = $response->message;
        $this->errorDetails = $response->errors ?? [];
        }
        // Return success response with user details and token
    }



    
    public function render()
    {
        return view('livewire.home.auth.forgotPasswordOtp')
            ->extends('layouts.home.app')->section('body');
    }
}
