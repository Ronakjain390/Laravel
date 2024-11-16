<?php

namespace App\Http\Livewire\Home\Auth;

use App\Models\UserQuery;
use App\Models\Feature;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class Landing extends Component
{
    public $emailOrPhone = '';
    public $userQueryData = [
        'phone' => '',
        'email'  => '',
        'comment' => '',
    ];
    
    public $password;
    public $isModalOpen = false;

    public $isAuthenticated;
    public $persistedTemplate;
    public $successMessage;
    public $errorMessage;
    public $otp = '';
    public $status;
    protected $response = [];

    public function userQuery(Request $request){
        $request->merge($this->userQueryData);
        // dd($request);
        $userAuthController = new UserAuthController;

        // Login the user and get the response
        $response = $userAuthController->userQuery($request);
        $response = $response->getData();
        // dd($response);
        if ($response->status == "true") {
            
            $this->reset(['errorMessage']);
            $this->successMessage = $response->message;
            return redirect()->route('home')->with('success', 'Thank You!! TheParchi team shall connect with you shortly');
            // $this->isModalOpen = false; // Close the modal
        // dd($response);
        } else {
            // dd($response);
            $this->errorMessage = json_encode($response->error ?? [$response->message]);
        }
    }
 
    public function render()
    {
        return view('livewire.home.auth.landingPage')
        ->extends('layouts.home.app')->section('body');
    }
}
