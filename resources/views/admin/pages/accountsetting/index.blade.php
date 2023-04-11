@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col col-md-6"><b>Account Setting</b></div>
                        <div class="col col-md-6">
                            <a href="{{ route('accountsetting.edit', $accountsetting->id) }}" class="btn btn-primary rounded-1 btn-sm float-end">Edit</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="row mb-3">
                                <label class="col-label-form"><b>Name</b></label>
                                <div>
                                    {{ $accountsetting->name }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-label-form"><b>Email</b></label>
                                <div>
                                    {{ $accountsetting->email }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class=" col-label-form"><b>Contact Number</b></label>
                                <div>
                                    {{ $accountsetting->contact }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class=" col-label-form"><b>Address</b></label>
                                <div>
                                    {{ $accountsetting->address }}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="row mb-3">
                                <label class=" col-label-form"><b>City</b></label>
                                <div>
                                    {{ $accountsetting->city }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class=" col-label-form"><b>State</b></label>
                                <div>
                                    {{ $accountsetting->state }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class=" col-label-form"><b>Country</b></label>
                                <div>
                                    {{ $accountsetting->country }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class=" col-label-form"><b>Postal Code</b></label>
                                <div>
                                    {{ $accountsetting->postal_code }}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="row mb-3">
                            <label class=" col-label-form"><b>Profile Picture</b></label>
                                <div>
                                    @if(Auth::user()->image)
                                    <img src="{{ asset('assets/images/admin/'.Auth::user()->image) }}" class="img-circle elevation-2" alt="User Image" height="100px"> <span class="availability-status online"></span>
                                    @else
                                    <img src="{{ asset('images/default_admin.png') }}" class="img-circle elevation-2" alt="User Image" height="100px"> <span class="availability-status online"></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('main_content')