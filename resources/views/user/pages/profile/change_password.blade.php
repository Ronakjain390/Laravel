@extends('user/layouts/profileLayout/baseProfile')
@section('baseprofile')
<h1 class="heading-title">Change Password</h1>
<div class="card" style="width: 400px; padding: 0px 20px 10px; max-width: 100%;">
    
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
        <form method="POST" action="{{route('user_paas_change')}}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-12 margin_class">
                    <label for="" class="form-label">Old Password</label>
                    <input type="text" name="oldpassword" class="form-control">
                </div>
            </div>
            <div class="row margin_class">
                <div class="col-md-12">
                    <label for="" class="form-label">New Password</label>
                    <input type="text" name="newpassword" class="form-control">
                </div>
            </div>
            <div class="row margin_class">
                <div class="col-md-12">
                    <label for="" class="form-label">Confirm Password</label>
                    <input type="text" name="confirmpasswrod" class="form-control">
                </div>
            </div>
            <div class="row margin_class">
                <div class="col-md-12">
                    <button class="btn btn-primary  w-100">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    .margin_class {
        margin-top: 10px;
    }
</style>
@endsection('baseprofile')