@extends('user/layouts/parts/master')
@section('content')
<div class="login-form">
    <div class="login-container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <p class="h2">User {{ __('Login') }}</p>
                    </div><br>
                    <div class="card-body">
                        @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                        @endif
                        @if ($message = Session::get('error'))
                        <div class="alert alert-danger">
                            <p>{{ $message }}</p>
                        </div>
                        @endif
                        <form method="POST" action="{{ route('userLoggedin') }}">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <!-- <label for="email" class="col-form-label text-md-end"><span class="required">*</span></label> -->
                                    <div class="input-box d-flex flex-row align-items-center mb-4">
                                        <i class="fa fa-envelope me-3" aria-hidden="true"></i>
                                        <div class="form-outline flex-fill mb-0">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="{{ __('Email Address') }}" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                        </div>
                                    </div>
                                    
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <!-- <label for="password" class="col-form-label text-md-end"><span class="required">*</span></label> -->
                                    <div class="input-box d-flex flex-row align-items-center mb-4">
                                        <i class="fa fa-lock me-3" aria-hidden="true"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('Password') }}" name="password" required autocomplete="current-password">
                                        </div>
                                    </div>

                                    
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12 offset-md-4">
                                    <div class="form-checkbox">
                                        <a class="bold" href="{{ route('user_forgot_screen') }}">
                                            Forgot Password
                                        </a>
                                        <div class="form-check ">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div><br>

                            <div class="row mb-0">
                                <div class="col-md-12 offset-md-4">
                                    <button type="submit" class="btn btn-login">
                                        {{ __('Login') }}
                                    </button>

                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-12 offset-md-4">
                                    <p class="register">Don't have an account? 
                                        <a class="bold" href="{{ route('userRegister') }}">
                                        Register
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('content')