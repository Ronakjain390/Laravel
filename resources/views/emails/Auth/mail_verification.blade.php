@component('mail::message')
# OTP for Email Verification

Your OTP for Email Verification is: {{ $otp }}

This OTP will expire within 2 minutes.

Thanks,
{{ config('app.name') }}
@endcomponent
