@extends('admin/layouts/layout/base')

@section('main_content')
<div class="content-wrapper">
    <div class="row">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-box-multiple"></i>
                </span> Edit Agent
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> <a href="{{ route('agents.index') }}" class="btn btn-success btn-lg float-right">Back</a>
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

    <form method="POST" enctype="multipart/form-data" action="{{ route('agents.update', $agent->id) }} ">
        @csrf
        @method('PUT')
        <div class="row mt-2">
            <input type="hidden" class="form-control" name="id" value="{{ $agent->id }}" />

            <div class="col-md-3 form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" value="{{ $agent->name }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" name="email" value="{{ $agent->email }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" value="{{ $agent->password }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="contact">Contact Number</label>
                <input type="text" class="form-control" name="contact" value="{{ $agent->contact }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" name="address" id="address" value="{{ $agent->address }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="city">City</label>
                <input type="text" class="form-control" name="city" id="city" value="{{ $agent->city }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="state">State</label>
                <input type="text" class="form-control" name="state" id="state" value="{{ $agent->state }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="country">Country</label>
                <input type="text" class="form-control" name="country" id="country" value="{{ $agent->country }}" />
            </div>
            <div class="col-md-3 form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" class="form-control" name="postal_code" id="postal_code" value="{{ $agent->postal_code }}" />
            </div>
            <div class="col-md-6 form-group">
                <label for="agent_profile_image">Image</label>
                <input type="file" class="form-control" name="image" />
                <input type="hidden" name="hidden_Image" value="{{ $agent->image }}" />
            </div>
            <div class="form-group col-md-3">
                <label for="status">Status</label><br />
                <input type="checkbox" id="status" name="status" class="toggle-class" {{ $agent->status=='1' ? 'checked':'' }}>
            </div>
            <input type="hidden" name="type" value="2" />
            <input type="text" class="form-control" name="username" id="username" value="{{ $agent->username }}" hidden />
            <input type="text" class="form-control" name="longitude" id="longitude" value="{{ $agent->longitude }}" hidden />
            <input type="text" class="form-control" name="latitude" id="latitude" value="{{ $agent->latitude }}" hidden />
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg float-right px-5">Submit</button>
            </div>
        </div>
    </form>
</div>

@endsection('main_content')