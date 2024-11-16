<?php

namespace App\Http\Livewire\Home\Auth;

use App\Models\User;
use App\Models\Feature;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Admin\Auth\AdminAuthController;

class AdminLogin extends Component
{
    public $emailOrPhone = '';
    public $loginCred = [
        'email_or_phone'  => '',
        'password' => ''
    ];
    public $password;

    public $user;
    public $isAuthenticated;
    public $persistedTemplate;
    public $successMessage;
    public $errorMessage;
    public $otp = '';
    protected $response = [];

    public function adminLogin()
    {
        // Validate the input data
        $request = request();
        // $validation = $this->validate();

        // 'email'  => 'test@gmail.com',
        // 'password' => 'test@2k23'
        $request->merge([
        'email'  => $this->emailOrPhone,
        'password' => $this->password
        ]);

        // dd($request);
        // Inject the AdminAuthController class as a dependency
        $AdminAuthController = new AdminAuthController;

        // Login the user and get the response
        $response = $AdminAuthController->login($request);
        $response = $response->getData();
        // dd($response);
        // if ($response->success == "true") {
        //     $this->successMessage = $response->message;
        //     $this->reset(['errorMessage']);
        // // dd($response);
        //     return redirect()->route('admin-dashboard');
        // } else {
        //     // dd($response);
        //     $this->errorMessage = json_encode($response->error ?? [[$response->message]]);
        // }
        return redirect()->route('admin/dashboard');
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
        $AdminAuthController = app(AdminAuthController::class);
        $response = $AdminAuthController->register($request);

        // Return success response with user details and token
        $this->response = $response->getData();
        session()->flash('success', 'You have been successfully registered!');
        return redirect()->route('admin-dashboard');
    }
    public function render()
    {
        return view('livewire.home.auth.adminLogin')
        ->extends('layouts.home.app')->section('body');
    }
}
