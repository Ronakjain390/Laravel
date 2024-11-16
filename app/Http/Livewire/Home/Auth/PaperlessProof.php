<?php

namespace App\Http\Livewire\Home\Auth;

use App\Models\User;
use App\Models\Feature;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class PaperlessProof extends Component
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

   
    public function render()
    {
        return view('livewire.home.auth.paperlessProof')
        ->extends('layouts.home.app')->section('body');
    }
}
