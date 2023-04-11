@extends('Auth/UserAuth/Layouts/Layout/Base')
@section('UserAuthContent')
<?php
$generalSetting = generalSetting();
$logo = '';
if ($generalSetting) {
    $logo = $generalSetting->logo;
}
?>
<main>
    <div class="row">
        <div class="colm-logo">
            <lottie-player src="https://assets8.lottiefiles.com/packages/lf20_mdyrjbcc.json" background="transparent" speed="1" style="width: 400px; height: 600px;" loop autoplay></lottie-player>
        </div>
        <div class="colm-form">
            <div class="form-container">
                <header>
                    <div class="header-area ">
                        <div class="container">
                            <div class="header_bottom_border">
                                <div class="align-items-center">

                                    <div class="col-xl-3 col-lg-2">
                                        <div class="logo">
                                            <a href="{{ url('/') }}">
                                                @if($logo=='')
                                                <img src="{{asset('Assets/DefaultImage/logoDefault.jpg' )}}" alt="" width="200px">
                                                @else
                                                <img src="{{asset('Assets/images/' .$generalSetting->logo )}}" alt="" width="200px">
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header><br>
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('Forgot your password? No problem.') }}
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">

                    @csrf

                    <!-- Email Address -->
                    <div class="input-box d-flex flex-row align-items-center mb-4">
                        <i class="fas fa-envelope fa-lg me-3 fa-fw" aria-hidden="true"></i>
                        <div class="form-outline flex-fill mb-0">
                            <x-text-input id="email" class="block mt-1 w-full" placeholder="Enter Your Email" type="email" name="email" :value="old('email')" required autofocus />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />                        
                        </div>
                    </div>
                    <br>
                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button>
                            {{ __('Email Password Reset Link') }}
                        </x-primary-button>
                    </div>
                </form>
                @endsection('UserAuthContent')