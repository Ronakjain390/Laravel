@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-cogs"></i>
                </span> General Setting's
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('generalsetting.edit', $generalsetting[0]->id) }}" class="btn btn-success btn-lg float-right">Edit</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Logo</b></label>
                        <div class="col-sm-10">
                            <img src="{{ asset('assets/images/generalsetting/' . $generalsetting[0]->logo) }}" width="200" class="img-thumbnail" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Favicon</b></label>
                        <div class="col-sm-10">
                            <img src="{{ asset('assets/images/generalsetting/' . $generalsetting[0]->favicon) }}" width="200" class="img-thumbnail" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Logo mini</b></label>
                        <div class="col-sm-10">
                            <img src="{{ asset('assets/images/generalsetting/' . $generalsetting[0]->logo_mini) }}" width="200" class="img-thumbnail" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Title</b></label>
                        <div class="col-sm-10">
                            {{ $generalsetting[0]->title }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Copyright Text</b></label>
                        <div class="col-sm-10">
                            {{ $generalsetting[0]->copyrightText }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Email</b></label>
                        <div class="col-sm-10">
                            {{ $generalsetting[0]->email }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Contact Number</b></label>
                        <div class="col-sm-10">
                            {{ $generalsetting[0]->contactNumber }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Address</b></label>
                        <div class="col-sm-10">
                            {{ $generalsetting[0]->address }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>City</b></label>
                        <div class="col-sm-10">
                            {{ $generalsetting[0]->city }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>State</b></label>
                        <div class="col-sm-10">
                            {{ $generalsetting[0]->state }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Country</b></label>
                        <div class="col-sm-10">
                            {{ $generalsetting[0]->country }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Postal Code</b></label>
                        <div class="col-sm-10">
                            {{ $generalsetting[0]->zipCode }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-label-form"><b>Date</b></label>
                        <div class="col-sm-10">
                            {{ $generalsetting[0]->dateFormat }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('main_content')