@extends('Auth/EventManagerAuth/Layouts/Layout/Base')
@section('EventManagerAuthContent')
<main>
    <div class="row">
        <div class="colm-logo">
            <lottie-player src="https://assets8.lottiefiles.com/packages/lf20_mdyrjbcc.json" background="transparent" speed="1" style="width: 400px; height: 600px;" loop autoplay></lottie-player>
        </div>
        <div class="colm-form">
            <div class="form-container">
                <input type="text" placeholder="Email address or phone number">
                <input type="password" placeholder="Password">
                <button class="btn-login">Login</button>
                <a href="#">Forgotten password?</a>
            </div>
        </div>
    </div>
</main>
@endsection('EventManagerAuthContent')