@extends('user/layouts/parts/master')
@section('content')
<div class="container login-container">
    @if ($message = Session::get('success'))
    <div class="alert alert-danger">
        <p>{{ $message }}</p>
    </div>
    @endif
    <h2 class="text-center">Change Password</h2>
    <br>
    <form method="POST" action="{{ route('passwordChange') }}">
        @csrf
        <div class="row mb-3">
            <div class="col-sm-12 ">
                <label for="password">Old Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="old_password" required autocomplete="off" autofocus>
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <br>
        <div class="row mb-3">
            <div class="col-sm-12">
                <label for="password">New Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="new_password" required autocomplete="off" autofocus>
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <br>
        <div class="row mb-3">
            <div class="col-sm-12">
                <label for="password">Confirm New Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="confirm_password" required autocomplete="off" autofocus>
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection('content')