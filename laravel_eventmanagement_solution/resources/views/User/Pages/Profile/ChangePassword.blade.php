@extends('User.Layouts.Layout.HomeLayout')
@section('UserContent')

<section>
    <br><br><br><br><br>

    <div class="container mt-10">
        <div class=" page-container" id="page-content">
            <div class="row   justify-content-center">
                <div class="col-xl-12 col-md-12">
                    <div class="card user-card-full">
                        <div class="row m-l-0 m-r-0">
                            <div class="col-sm-4 user-profile">
                                <div class="card-block text-center text-white">
                                    <div class="m-b-25">
                                        <img src="{{ asset('Assets/images/' . auth()->user()->image) }}" class="img-radius" alt="User-Profile-Image" height="100px" width="100px">
                                    </div>
                                    <i class=" mdi mdi-square-edit-outline feather icon-edit m-t-10 f-16"></i>

                                </div>

                                <div class="col-md-12">
                                    <div class="nav nav-pills flex-column flex-md-row mb-3">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="{{ route('EditUserProfile', Auth::user()->id )}}"><i class="bx bx-user me-1"></i> Edit Profile</a>
                                        </li>
                                    </div>

                                </div>
                                <div class="col-md-12">
                                    <ul class="nav nav-pills flex-column flex-md-row mb-3">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="{{ route('ChangePassword') }}"><i class="bx bx-user me-1"></i> Change Password</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-12">
                                        <ul class="nav nav-pills flex-column flex-md-row mb-3">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="{{ route('TicketListing') }}"><i class="bx bx-user me-1"></i> Bookings</a>
                                            </li>
                                        </ul>
                                    </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="card-block">
                                    <div class="row">
                                        <span><a href="{{ route('UserProfile', Auth::user()->id )}}" class="btn btn-success btn-lg float-right">Back</a>
                                            <h5 class="m-b-20 p-b-5 b-b-default ">Edit Your Profile</h5>
                                        </span>
                                    </div>
                                    <form method="POST" id="formAccountSettings" action="{{route('PasswordChange', Auth::user()->id)}}" enctype="multipart/form-data">
                                        @csrf
                                      
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label for="current_password" class="form-label">Current Password</label>
                                                <input class="form-control" type="password" id="current_password" name="current_password" placeholder="Current Password"  autofocus />
                                            </div>
                                            
                                            <div class="mb-3 col-md-6">
                                            <label class="form-label" for="country">New Password</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" minlength="8" />

                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="country">Confirm Password</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" minlength="8" />
                                        </div>
                          
                                            
                                            <div class="mt-2">
                                                <input type="submit" class="btn btn-primary " value="Save Changes">
                                            </div>
                                            </div>
                                    </form>
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    body {
        background-color: black;

    }

    .col-sm-4.user-profile {
        background: radial-gradient(black, transparent);
    }

    .row.m-l-0.m-r-0 {
        height: 600px;
    }

    .padding {
        padding: 3rem !important
    }

    .user-card-full {
        overflow: hidden;
    }

    .card {
        border-radius: 5px;
        -webkit-box-shadow: 0 1px 20px 0 rgba(69, 90, 100, 0.08);
        box-shadow: 0 1px 20px 0 rgba(69, 90, 100, 0.08);
        border: none;
        margin-bottom: 30px;
    }

    .m-r-0 {
        margin-right: 0px;
    }

    .m-l-0 {
        margin-left: 0px;
    }

    .user-card-full .user-profile {
        border-radius: 5px 0 0 5px;
    }

    .bg-c-lite-green {
        background: -webkit-gradient(linear, left top, right top, from(#f29263), to(#ee5a6f));
        background: linear-gradient(to right, #ee5a6f, #f29263);
    }

    .user-profile {
        padding: 20px 0;
    }

    .card-block {
        padding: 1.25rem;
    }

    .m-b-25 {
        margin-bottom: 25px;
    }

    .img-radius {
        border-radius: 5px;
    }



    h6 {
        font-size: 14px;
    }

    .card .card-block p {
        line-height: 25px;
    }

    @media only screen and (min-width: 1400px) {
        p {
            font-size: 14px;
        }
    }

    .card-block {
        padding: 1.25rem;
    }

    .b-b-default {
        border-bottom: 1px solid #e0e0e0;
    }

    .m-b-20 {
        margin-bottom: 20px;
    }

    .p-b-5 {
        padding-bottom: 5px !important;
    }

    .card .card-block p {
        line-height: 25px;
    }

    .m-b-10 {
        margin-bottom: 10px;
    }

    .text-muted {
        color: #919aa3 !important;
    }

    .b-b-default {
        border-bottom: 1px solid #e0e0e0;
    }

    .f-w-600 {
        font-weight: 600;
    }

    .m-b-20 {
        margin-bottom: 20px;
    }

    .m-t-40 {
        margin-top: 20px;
    }

    .p-b-5 {
        padding-bottom: 5px !important;
    }

    .m-b-10 {
        margin-bottom: 10px;
    }

    .m-t-40 {
        margin-top: 20px;
    }

    .user-card-full .social-link li {
        display: inline-block;
    }

    .user-card-full .social-link li a {
        font-size: 20px;
        margin: 0 10px 0 0;
        -webkit-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
    }
</style>
@endsection('UserContent')