@extends('agent/layouts/master')

@section('content')
<div class="card m-4">
    @if(Session::has('success'))
    <div class="alert alert-success">
        <p>{{ Session::get('success') }}</p>
    </div>
    @endif
    <div class="card-header">
        <div class="row">
            <div class="col col-md-6 d-flex align-items-center"><b>Account Setting</b></div>
            <div class="col col-md-6">
                <a href="{{ route('agentEdit', $profile->id) }}" class="btn btn-outline-primary btn-sm float-end">Edit</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-4">
                <div class="row mb-3">
                    <label class="col-label-form"><b>Name</b></label>
                    <div>
                        {{ $profile->name }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-label-form"><b>Email</b></label>
                    <div>
                        {{ $profile->email }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Contact Number</b></label>
                    <div>
                        {{ $profile->contact }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Address</b></label>
                    <div>
                        {{ $profile->address }}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row mb-3">
                    <label class=" col-label-form"><b>City</b></label>
                    <div>
                        {{ $profile->city }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>State</b></label>
                    <div>
                        {{ $profile->state }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Country</b></label>
                    <div>
                        {{ $profile->country }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Postal Code</b></label>
                    <div>
                        {{ $profile->postal_code }}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row mb-3">
                    <label class=" col-label-form"><b>Profile Picture</b></label>
                    <div>
                        @if(Auth::user()->image)
                        <img src="{{ asset('assets/images/agent/'.Auth::user()->image) }}" height="180px" width="180px" class="img-circle elevation-2 profile-img" alt="User Image"> <span class="availability-status online"></span>
                        @else
                        <img src="{{ asset('assets/images/default_user.jpg') }}" height="180px" width="180px" class="img-circle elevation-2 profile-img" alt="User Image"> <span class="availability-status online"></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('content')