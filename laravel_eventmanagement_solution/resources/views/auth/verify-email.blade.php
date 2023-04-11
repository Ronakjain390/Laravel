@extends('auth/UserAuth/Layouts/Layout/Base')
@section('UserAuthContent')
<div class="row">
    <div class="colm-logo">
        <lottie-player src="https://assets8.lottiefiles.com/packages/lf20_mdyrjbcc.json" background="transparent" speed="1" style="width: 400px; height: 600px;" loop autoplay></lottie-player>
    </div>
    <div class="colm-form">
        <div class="form-container" style="width: 400px;">
            <div class="mb-4 text-sm text-gray-600">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
            @endif

            <div class="mt-4 flex items-center justify-between">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <div>
                        <x-primary-button>
                            {{ __('Resend Verification Email') }}
                        </x-primary-button>
                    </div>
                </form>
                <br>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>

        </div>
    </div>
    @endsection('UserAuthContent')