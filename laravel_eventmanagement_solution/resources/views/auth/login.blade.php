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
                                                <img src="{{asset('Assets/DefaultImage/logoDefault.jpg' )}}" alt="" width="230px">
                                                @else
                                                <img src="{{asset('Assets/images/' .$generalSetting->logo )}}" alt="" width="230px">
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                @if (Session::has('success'))
                <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" id="message" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @elseif(Session::has('error'))
                <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" id="message" role="alert">
                    {{ Session::get('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="input-box d-flex flex-row align-items-center mb-4">
                        <i class="fas fa-envelope fa-lg me-3 fa-fw" aria-hidden="true"></i>
                        <div class="form-outline flex-fill mb-0">
                            <x-text-input id="email" class="block mt-1 w-full" placeholder="Enter your Email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        </div>
                    </div>
                    <div class="input-box d-flex flex-row align-items-center mb-4">
                        <i class="fas fa-lock fa-lg me-3 fa-fw" aria-hidden="true"></i>
                        <div class="form-outline flex-fill mb-0">
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                            <span><i class="fa fa-eye" onclick="showPassword()"></i></span>                        
                        </div>
                    </div>
                    <input type="text" value="1" name="type" hidden>
                    <div class="text form-outline flex-fill mb-0">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                        @endif
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        

                        <x-primary-button class="ml-3">
                            {{ __('Log in') }}
                        </x-primary-button>
                    </div>
                    <div class="flex items-center justify-end mt-4 signup">Don't have an account? 
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                            {{ __('Sign Up') }}
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
<script>
    function showPassword() {
      var x = document.getElementById("password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    }
  </script>
@endsection('UserAuthContent')