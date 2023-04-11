@extends('auth/UserAuth/Layouts/Layout/Base')
@section('UserAuthContent')
<div class="row">
    <div class="colm-logo">
        <lottie-player src="https://assets8.lottiefiles.com/packages/lf20_mdyrjbcc.json" background="transparent" speed="1" style="width: 400px; height: 600px;" loop autoplay></lottie-player>
    </div>
    <div class="colm-form">
        <div class="form-container">
            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="input-box d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-envelope fa-lg me-3 fa-fw" aria-hidden="true"></i>
                    <div class="form-outline flex-fill mb-0"> 
                        <x-text-input id="email" placeholder="Email Address" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />                 
                    </div>
                </div>

                <!-- Password -->
                <div class="input-box d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-lock fa-lg me-3 fa-fw" aria-hidden="true"></i>
                    <div class="form-outline flex-fill mb-0"> 
                        <x-text-input id="password" placeholder="Enter Password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
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
                <br>
                <div class="flex items-center justify-end mt-4">
                    <x-primary-button>
                        {{ __('Reset Password') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection('UserAuthContent')