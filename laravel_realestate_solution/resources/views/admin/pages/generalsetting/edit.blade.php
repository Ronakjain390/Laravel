@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-cogs"></i>
                </span> Edit
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('generalsetting.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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

    <form method="POST" action="{{ route('generalsetting.update',$generalsetting->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <div class="row mt-2">
                <div class="col-md-4 form-group">
                    <label for="copyrightText">Copyright Text</label>
                    <input type="text" id="copyrightText" name="copyrightText" class="form-control" value="{{ $generalsetting->copyrightText }}">
                </div>
                <div class="col-md-4 form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ $generalsetting->title }}">
                </div>
                <div class="col-md-4 form-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" class="form-control" value="{{ $generalsetting->email }}">
                </div>
                <div class="col-md-4 form-group">
                    <label for="contactNumber">Contact Number</label>
                    <input type="text" id="contactNumber" name="contactNumber" class="form-control" value="{{ $generalsetting->contactNumber }}">
                </div>
                <div class="col-md-3 form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control" value="{{ $generalsetting->address }}">
                </div>
                <div class="col-md-3 form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" class="form-control" value="{{ $generalsetting->city }}">
                </div>
                <div class="col-md-3 form-group">
                    <label for="state">State</label>
                    <input type="text" id="state" name="state" class="form-control" value="{{ $generalsetting->state }}">
                </div>
                <div class="col-md-3 form-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" class="form-control" value="{{ $generalsetting->country }}">
                </div>
                <div class="col-md-3 form-group">
                    <label for="zipCode">Zip Code</label>
                    <input type="text" id="zipCode" name="zipCode" class="form-control" value="{{ $generalsetting->zipCode }}">
                </div>
                <input type="text" class="form-control" name="longitude" id="longitude" hidden />
                <input type="text" class="form-control" name="latitude" id="latitude" hidden />
                <div class="col-md-3 form-group">
                    <label for="dateFormat">Date Format</label>
                    <input type="text" id="dateFormat" name="dateFormat" class="form-control" value="{{ $generalsetting->dateFormat }}">
                </div>
                <div class="form-group col-md-4">
                    <label for="logo">Logo</label>
                    <input type="file" class="form-control" id="logo" name="logo" />
                    <input type="hidden" name="hidden_logo" value="{{ $generalsetting->logo }}" />
                    <img src="{{ asset('assets/images/generalsetting/' . $generalsetting->logo) }}" width="200" class="mt-1 img-thumbnail" />
                </div>
                <div class="form-group col-md-4">
                    <label for="logo_mini">Mini Logo</label>
                    <input type="file" class="form-control" id="logo_mini" name="logo_mini" />
                    <input type="hidden" name="hidden_logo_mini" value="{{ $generalsetting->logo_mini }}" />
                    <img src="{{ asset('assets/images/generalsetting/' . $generalsetting->logo_mini) }}" width="200" class=" mt-1 img-thumbnail" />
                </div>
                <div class="form-group col-md-4">
                    <label for="favicon">Favicon</label>
                    <input type="file" class="form-control" id="favicon" name="favicon" />
                    <input type="hidden" name="hidden_favicon" value="{{ $generalsetting->favicon }}" />
                    <img src="{{ asset('assets/images/generalsetting/' . $generalsetting->favicon) }}" width="200" class="mt-1 img-thumbnail" />
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection('main_content')