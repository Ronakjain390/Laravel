@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account"></i>
                </span> Update Account Setting
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('accountsetting.index') }}" class="btn btn-success btn-lg float-right">Back</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    @if(session('status'))
    <div class="alert alert-success mb-1 mt-1">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('accountsetting.update',$accountsetting->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="col-md-3 form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" value="{{ $accountsetting->name }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" name="email" value="{{ $accountsetting->email }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="contact">Contact Number</label>
                <input type="text" class="form-control" name="contact" value="{{ $accountsetting->contact }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" name="address" id="address" value="{{ $accountsetting->address }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="city">City</label>
                <input type="text" class="form-control" name="city" id="city" value="{{ $accountsetting->city }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="state">State</label>
                <input type="text" class="form-control" name="state" id="state" value="{{ $accountsetting->state }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="country">Country</label>
                <input type="text" class="form-control" name="country" id="country" value="{{ $accountsetting->country }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" class="form-control" name="postal_code" id="postal_code" value="{{ $accountsetting->postal_code }}" />
            </div>
            <div class="col-md-6 form-group">
                <label for="agent_profile_image">Image</label>
                <input type="file" class="form-control" name="image" />
                <input type="hidden" name="hidden_Image" value="{{ $accountsetting->image }}" />
            </div>
            <input type="hidden" name="type" value="1" />
            <input type="text" class="form-control" name="username" id="username" value="{{ $accountsetting->username }}" hidden />
            <input type="text" class="form-control" name="longitude" id="longitude" value="{{ $accountsetting->longitude }}" hidden />
            <input type="text" class="form-control" name="latitude" id="latitude" value="{{ $accountsetting->latitude }}" hidden />
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection('main_content')