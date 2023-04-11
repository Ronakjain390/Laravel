@extends('user/layouts/parts/master')
@section('content')
<div class="login-form">
    <div class="login-container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <p class="h2">Agent {{ __('Register') }}</p>
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
                        <form method="POST" action="{{ route('customRegister') }}">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input-box d-flex flex-row align-items-center mb-4">
                                        <i class="fa fa-user me-3" aria-hidden="true"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <input id="name" type="text" placeholder="{{ __('Name') }}" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                        </div>
                                    </div>
                                    <!-- <label for="name" class=" col-form-label text-md-end">{{ __('Name') }}<span class="required">*</span></label> -->



                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input-box d-flex flex-row align-items-center mb-4">
                                        <i class="fa fa-envelope me-3" aria-hidden="true"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <input id="email" type="email" placeholder="{{ __('Email Address') }}" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                        </div>
                                    </div>
                                    <!-- <label for="email" class="col-form-label text-md-end">{{ __('Email Address') }}<span class="required">*</span></label> -->



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
                                        <i class="fa fa-phone me-3" aria-hidden="true"></i>
                                        <div class="form-outline flex-fill mb-0">
                                        <input id="contact" type="text" placeholder="{{ __('Contact') }}" class="form-control @error('email') is-invalid @enderror" name="contact_number" required autocomplete="email">
                                        </div>
                                    </div>
                                    <!-- <label for="contact" class="col-form-label text-md-end">{{ __('Contact') }}<span class="required">*</span></label> -->


                                    @error('contact')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="address" class="col-form-label text-md-end">{{ __('Image') }}<span class="required">*</span></label>


                                    <input id="image" type="file" class="form-control @error('email') is-invalid @enderror" name="image" value="{{ old('image') }}" required autocomplete="email">

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div> -->


                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input-box d-flex flex-row align-items-center mb-4">
                                        <i class="fa fa-lock me-3" aria-hidden="true"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <input id="password" type="password" placeholder="{{ __('Password') }}" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                        </div>
                                    </div>
                                    <!-- <label for="password" class=" col-form-label text-md-end">{{ __('Password') }}<span class="required">*</span></label> -->



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
                                    <!-- <label for="password-confirm" class="col-form-label text-md-end">{{ __('Confirm Password') }}<span class="required">*</span></label> -->


                                </div>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="hidden" value="2" id="flexCheckChecked" name="type">
                            </div>
                            <div class="form-group col-md-3" hidden>
                                <label for="status">Status</label><br />
                                <input class="content" type="checkbox" id="status" name="status" class="toggle-class" checked>
                            </div>
                            <br>
                            <div class="row mb-0">
                                <div class="col-md-12 offset-md-4">
                                    <button type="submit" class="btn btn-login">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-12 offset-md-4">
                                    <p class="register">Already registered? 
                                        <a class="bold" href="{{ route('agentLogin') }}">
                                        Login
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <br>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('content')