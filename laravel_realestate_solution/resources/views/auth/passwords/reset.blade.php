@extends('user/layouts/parts/master')
@section('content')
<div class="login-form">
    <div class="login-container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <p class="h2">{{ __('Reset Password') }}</p>
                    </div><br>

                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input-box d-flex flex-row align-items-center mb-4">
                                        <i class="fa fa-envelope me-3" aria-hidden="true"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <input id="email" type="email" placeholder="{{ __('Email Address') }}" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                                        </div>
                                    </div>
                                    <!-- <label for="email" class="col-form-label text-md-end">{{ __('Email Address') }}</label> -->



                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input-box d-flex flex-row align-items-center mb-4">
                                        <i class="fa fa-lock me-3" aria-hidden="true"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <input id="password" type="password" placeholder="{{ __('Password') }}" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                        </div>
                                    </div>
                                    <!-- <label for="password" class="col-form-label text-md-end">{{ __('Password') }}</label> -->



                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input-box d-flex flex-row align-items-center mb-4">
                                        <i class="fa fa-lock me-3" aria-hidden="true"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <input id="password-confirm" type="password" placeholder="{{ __('Confirm Password') }}" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                        </div>
                                    </div>
                                    <!-- <label for="password-confirm" class="col-form-label text-md-end">{{ __('Confirm Password') }}</label> -->


                                </div>
                            </div>
                            <br>

                            <div class="row mb-0">
                                <div class="col-md-12 offset-md-4">
                                    <button  type="submit" class="btn btn-login">
                                        {{ __('Reset Password') }}
                                    </button>
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