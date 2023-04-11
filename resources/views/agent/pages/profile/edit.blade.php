@extends('agent/layouts/master')

@section('content')
<div class="content-wrapper">
    <div class="container">
        <div class="row">
            <div class="page-header">
                <h3 class="page-title text-center"><br>
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-account"></i>
                    </span> Update Account Setting
                </h3><br>
                <nav class="px-3" aria-label="breadcrumb">
                    <ul class="breadcrumb justify-content-end">
                        <li class="breadcrumb-item active" aria-current="page">
                            <span></span> <a href="{{ route('agentProfile') }}" class="btn btn-outline-success btn-lg float-end">Back</a>
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

        <div class="container">
            <form class="form bg-white p-4 rounded shadow" method="POST" action="{{ route('agentUpdate',$profile->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $profile->name }}" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" name="email" value="{{ $profile->email }}" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="contact">Contact Number</label>
                        <input type="text" class="form-control" name="contact" value="{{ $profile->contact }}" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" name="address" id="address" value="{{ $profile->address }}" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="city">City</label>
                        <input type="text" class="form-control" name="city" id="city" value="{{ $profile->city }}" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="state">State</label>
                        <input type="text" class="form-control" name="state" id="state" value="{{ $profile->state }}" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="country">Country</label>
                        <input type="text" class="form-control" name="country" id="country" value="{{ $profile->country }}" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" class="form-control" name="postal_code" id="postal_code" value="{{ $profile->postal_code }}" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="agent_profile_image">Image</label>
                        <input type="file" class="form-control" name="image" />
                        <input type="hidden" name="hidden_Image" value="{{ $profile->image }}" />
                    </div><br><br>
                    <input type="hidden" name="type" value="1" />
                    <input type="text" class="form-control" name="username" id="username" value="{{ $profile->username }}" hidden />
                    <input type="text" class="form-control" name="longitude" id="longitude" value="{{ $profile->longitude }}" hidden />
                    <input type="text" class="form-control" name="latitude" id="latitude" value="{{ $profile->latitude }}" hidden /><br><br>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-lg float-right text-white px-5">Submit</button>
                    </div>

                </div>
        </div>
        </form>
    </div>
    @endsection('content')