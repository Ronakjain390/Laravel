<?php

namespace App\Http\Livewire\Home\Auth;

use App\Models\User;
use App\Models\Feature;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class ChangePassword extends Component
{
    
    public $emailOrPhone = '';
    public $password;
    public $confirmpassword;
    public $user;
    public $isAuthenticated;
    public $persistedTemplate;
    public $successMessage;
    public $errorMessage;
    public $otp = '';
    public $token;
    protected $response = [];
    

    public function mount(Request $request)
    {
        // dd($request);
        $this->emailOrPhone = $request->phone;
    }
    // public function changePassword()
    // {
    //     $request = request();
    //     $request->merge([
    //         'phone_number'  => $this->emailOrPhone,
    //         'otp'  => $this->otp,
    //     ]);
        

    // }
    protected $rules = [
        'password' => 'required|min:8',
        'confirmpassword' => 'required|same:password',
    ];
    
    protected $messages = [
        'password.required' => 'Please enter the password',
        'password.min' => 'The password must be at least :min characters',
        'confirmpassword.required' => 'The Confirm password field is required.',
        'confirmpassword.same' => 'The Confirm password must match the password.',
    ];
    
    public function resetPassword()
    {
        $this->validate();
    
        $request = request();
        $tokenForReset = session('tokenForReset');
        $userPhone = session('userPhone');
    
        $request->merge([
            'token' => $tokenForReset,
            'phone' => $userPhone,
            'password' => $this->password,
            'confirmpassword' => $this->confirmpassword,
        ]);
    
        // Inject the UserAuthController class as a dependency
        $userAuthController = new UserAuthController;
    
        // Login the user and get the response
        $response = $userAuthController->validateOtpAndResetPassword($request);
        $response = $response->getData();
    
        if ($response->success == "true") {
            $this->successMessage = $response->message;
            return redirect()->route('login')->with('success', 'Password Reset Successfully');
        } else {
            $this->errorMessage = json_encode($response->error ?? [[$response->message]]);
            return redirect()->back()->with('error', 'Failed to reset password');
        }
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
        return view('livewire.home.auth.changePassword')
            ->extends('layouts.home.app')->section('body');
    }
}
