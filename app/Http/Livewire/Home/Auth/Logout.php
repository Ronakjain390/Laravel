<?php

namespace App\Http\Livewire\Home\Auth;

use App\Models\User;
use App\Models\Feature;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class Logout extends Component
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

    public function Logout()
    {
        // Validate the input data
        $request = request();
        // $validation = $this->validate();

        // Inject the UserAuthController class as a dependency
        $userAuthController = new UserAuthController;

        // Login the user and get the response
        $response = $userAuthController->logout($request);
        $response = $response->getData();
        if ($response->success == "true") {
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);
        // dd($response);
            return redirect()->route('login');
        } else {
            // dd($response);
            $this->errorMessage = json_encode($response->error ?? [[$response->message]]);
        }
        // Return success response with user details and token
    }

    public function render()
    {
        return view('livewire.home.auth.logout');
    }
}
