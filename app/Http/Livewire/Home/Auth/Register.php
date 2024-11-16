<?php

namespace App\Http\Livewire\Home\Auth;

use App\Models\User;
use App\Models\Feature;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class Register extends Component
{
    public $emailOrPhone = '';
    public $loginCred = [
        'email_or_phone'  => '',
        'password' => ''
    ];
    public $name, $email, $phone, $password_confirmation, $password;

    public $user;
    public $isAuthenticated;
    public $persistedTemplate;
    public $successMessage;
    public $errorMessage;
    public $otp = '';
    protected $response = [];

    // public function userLogin()
    // {
    //     // Validate the input data
    //     $request = request();
    //     // $validation = $this->validate();

    //     $request->merge([
    //         'email_or_phone'  => $this->emailOrPhone,
    //         'password' => $this->password
    //     ]);

    //     // Inject the UserAuthController class as a dependency
    //     $userAuthController = new UserAuthController;

    //     // Login the user and get the response
    //     $response = $userAuthController->login($request);
    //     $response = $response->getData();
    //     if ($response->success == "true") {
    //         $this->successMessage = $response->message;
    //         $this->reset(['errorMessage']);
    //         // dd($response);
    //         return redirect()->route('dashboard');
    //     } else {
    //         // dd($response);
    //         $this->errorMessage = json_encode($response->error ?? [$response->message]);
    //     }
    //     // Return success response with user details and token
    // }

    public $registerUser = array(
        'email' => '',
        'name' => '',
        'password' => '',
        'password_confirmation' => '',
        'phone' => '',
    );
    
    protected $rules = [
        'name' => 'required|regex:/^[a-zA-Z0-9 ]+$/',
        'email' => [
            'required',
            'email',
            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',
            'unique:users'
        ],
        'phone' => 'required|numeric|digits:10|unique:users',
        'password' => 'required|min:6',
        'password_confirmation' => 'required',
    ];
    

    public function updated($propertyName)
    {
        // dd($propertyName);
        $this->validateOnly($propertyName, $this->rules);
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already exists.',
            'phone.unique' => 'This phone number is already exists.',
            'phone.required' => 'The phone number field is required.',
            'phone.numeric' => 'The phone number should be numeric.',
            'phone.digits' => 'The phone number should be 10 digits.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least :min characters.',
            'password_confirmation.confirmed' => 'The password confirmation does not match.',
        ];
    }

    public function userRegister(Request $request)
    {
        // Validate the input data
        $this->validate();
        
        $request->merge([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        // Register the user and get the response
        $userAuthController = new UserAuthController;
        $response = $userAuthController->register($request);

        // Return success response with user details and token
        $response = $response->getData();
        if ($response->success == "true") {
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);
            // dd($response);
            return redirect()->route('all-feature');
        } else {
            // dd($response);
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
        // Return success response with user details and token
    }
    public function render()
    {
        return view('livewire.home.auth.register')
            ->extends('layouts.home.app')->section('body');
    }
}
