@extends('user/layouts/profileLayout/baseProfile')
@section('baseprofile')
<h1 class="heading-title">Profile</h1>
<div class='card'>
    <div class='card-header'>
        <span class="pull-right bread"><a href="/">Home /</a> Profile</span>
        <br>
        <h2>Welcome, {{ Auth::user()->name }}</h2>
    </div>
    <div class='card-body'>
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
        <form class="inputUserna" method="POST" action="{{ route('updateProfile',$profile->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="row gx-3 mb-3">
                <div class="col-sm-4 com-md-4">
                    <div class="profileImage">
                        @if($profile->image && File::exists(public_path('assets/images/users/'.$profile->image)))
                        <img src="{{ asset('assets/images/users/' .$profile->image ) }}" width="200" class="img-thumbnail" />
                        @else
                        <img src="{{ asset('assets/images/users/default_user.png') }}" class="img-thumbnail" alt="img" />
                        @endif
                    </div>
                    <input class="img-input form-control" type="file" name="image" id="image">
                    <input class="img-input form-control" type="hidden" name="hidden_Image" id="image" value="{{$profile->image}}">
                </div>
                <div class="col-sm-4 com-md-4">
                    <div class="form-group">
                        <label class="medium mb-1" for="name">Name</label>
                        <input class="form-control" id="name" type="text" name="name" placeholder="Enter your Name" value="{{ $profile->name }}" />
                    </div>
                    <div class="form-group">
                        <label class="medium mb-1" for="address">Address</label>
                        <input class="form-control" id="address" type="text" name="address" value="{{ $profile->address }}" />
                    </div>
                    <div class="form-group">
                        <label class="medium mb-1" for="country">Country</label>
                        <input class="form-control" id="country" type="text" name="country" value="{{ $profile->country }}" />
                    </div>
                    <div class="form-group">
                        <label class="medium mb-1" for="city">City</label>
                        <input class="form-control" id="city" type="text" name="city" value="{{ $profile->city }}" />
                    </div>
                </div>
                <div class="col-sm-4 com-md-4">
                    <div class="form-group">
                        <label class="medium mb-1" for="state">State</label>
                        <input class="form-control" id="state" type="text" name="state" value="{{ $profile->state }}" />
                    </div>
                    <div class="form-group">
                        <label class="medium mb-1" for="postal_code">Postal code</label>
                        <input class="form-control" id="postal_code" type="text" name="postal_code" value="{{ $profile->postal_code }}" />
                    </div>
                    <div class="form-group">
                        <label class="medium mb-1" for="email">Email address</label>
                        <input class="form-control" id="email" type="email" name="email" placeholder="Enter your email address" value="{{ $profile->email }}" />
                    </div>
                    <div class="form-group">
                        <label class="medium mb-1" for="contact">Phone number</label>
                        <input class="form-control" id="contact" type="tel" name="contact" placeholder="Enter your phone number" value="{{ $profile->contact }}" />
                    </div>
                </div>
            </div>
            <button class="btn btn-primary read-more-btn" type="submit">Save changes</button>
        </form>
    </div>
</div>
<style>
    .profileImage {
        height: 170px;
        margin-bottom: 10px;
    }

    .profileImage img {
        height: 100%;
        object-fit: cover;
    }

    .img-input {
        padding: 5px 10px;
        margin-bottom: 10px;
    }
</style>
@endsection('baseprofile')