@extends('auth/UserAuth/Layouts/Layout/Base')
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
                                                <img src="{{asset('Assets/DefaultImage/logoDefault.jpg' )}}" alt="" width="250px">
                                                @else
                                                <img src="{{asset('Assets/images/' .$generalSetting->logo )}}" alt="" width="250px">
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="input-box d-flex flex-row align-items-center mb-4">
                        <i class="fas fa-user fa-lg me-3 fa-fw" aria-hidden="true"></i>
                        <div class="form-outline flex-fill mb-0">
                            <x-text-input id="name" placeholder="Name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />                        
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="input-box d-flex flex-row align-items-center mb-4">
                        <i class="fas fa-envelope fa-lg me-3 fa-fw" aria-hidden="true"></i>
                        <div class="form-outline flex-fill mb-0">
                            <x-text-input id="email" class="block mt-1 w-full" placeholder="Enter Email Address" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="input-box d-flex flex-row align-items-center mb-4">
                        <i class="fas fa-lock fa-lg me-3 fa-fw" aria-hidden="true"></i>
                        <div class="form-outline flex-fill mb-0">
                            <x-text-input id="password" class="block mt-1 w-full" placeholder="Enter Password" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="input-box d-flex flex-row align-items-center mb-4">
                        <i class="fas fa-lock fa-lg me-3 fa-fw" aria-hidden="true"></i>
                        <div class="form-outline flex-fill mb-0">
                            <x-text-input id="password_confirmation" placeholder="Confirm Password" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            
                        </div>
                    </div>
                    <br><br>
                    <!-- <input type="text" name="type" value="1" hidden > -->

                    <div class="flex items-center justify-end ">

                        <x-primary-button class="ml-4">
                            {{ __('Register') }}
                        </x-primary-button>
                        
                    </div>
                    <div class="flex items-center justify-end mt-4 signup">Already registered?  
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                            {{ __('Login') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
    .logo {
        padding: 20px;
    }
</style>
@endsection('UserAuthContent')