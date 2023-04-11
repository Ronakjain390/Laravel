@extends('User/Layouts/Layout/HomeLayout')
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
                                        <img src="{{ asset('Assets/images/' . auth()->user()->image) }}" class="img-radius" alt="User-Profile-Image" height="100px" width="100px" >
                                    </div>
                                    <h6 class="f-w-600">Hello {{ Auth::user()->name }}</h6>
                                    <i class=" mdi mdi-square-edit-outline feather icon-edit m-t-10 f-16"></i>

                                </div>

                                <div class="col-md-12">
                                    <div class="nav nav-pills flex-column flex-md-row mb-3">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="{{ route('EditUserProfile', Auth::user()->id )}} "><i class="bx bx-user me-1"></i> Edit Profile</a>
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
                                    <form method="POST" id="formAccountSettings" action="{{ route('UpdateUserProfile', Auth::user()->id) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label for="firstName" class="form-label">Name</label>
                                                <input class="form-control" type="text" id="name" name="name" value="{{ Auth::user()->name }}" autofocus />
                                            </div>
                                            <!-- <div class="mb-3 col-md-6">
                                                <label for="lastName" class="form-label">Last Name</label>
                                                <input class="form-control" type="text" name="lastName" id="lastName" value="Doe" />
                                            </div> -->
                                            <div class="mb-3 col-md-6">
                                                <label for="email" class="form-label">E-mail</label>
                                                <input class="form-control" type="text" id="email" name="email" value="{{ Auth::user()->email }}" placeholder="john.doe@example.com" />
                                            </div>
                                            <!-- <div class="mb-3 col-md-6">
                                                <label for="organization" class="form-label">Organization</label>
                                                <input type="text" class="form-control" id="organization" name="organization" value="{{ Auth::user()->name }}" />
                                            </div> -->
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="phoneNumber">Phone Number</label>
                                                <div class="input-group input-group-merge">
                                                    <span class="input-group-text">(+91)</span>
                                                    <input type="text" id="phoneNumber" name="contact" value="{{ Auth::user()->contact }}" class="form-control" placeholder="202 555 0111" />
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="address" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="address" name="address" placeholder="Address" />
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="state" class="form-label">State</label>
                                                <input class="form-control" type="text" id="state" name="state" value="{{ Auth::user()->state }}" placeholder="State" />
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="state" class="form-label">City</label>
                                                <input class="form-control" type="text" id="city" name="city" value="{{ Auth::user()->city }}" placeholder="City" />
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="zipCode" class="form-label">Zip Code</label>
                                                <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ Auth::user()->postal_code }}" placeholder="231465" maxlength="6" />
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="country">Country</label>
                                                <input type="text" class="form-control" id="country" name="country" value="{{ Auth::user()->country }}" placeholder="231465" maxlength="6" />

                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="country">Image</label>
                                                <input type="file" class="form-control" id="image" name="image" value="{{ Auth::user()->image }}" placeholder="231465" maxlength="6" />

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
<script>
    $(document).ready(function() {
    $("#lat_area").addClass("d-none");
    $("#long_area").addClass("d-none");
});

google.maps.event.addDomListener(window, 'load', function() {
    var places = new google.maps.places.Autocomplete(document.getElementById('address'));
    google.maps.event.addListener(places, 'place_changed', function() {
        var place = places.getPlace();
        var address = place.formatted_address;
        var latitude = place.geometry.location.lat();
        var longitude = place.geometry.location.lng();
        var latlng = new google.maps.LatLng(latitude, longitude);
        var geocoder = geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            'latLng': latlng
        }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                //console.log(results[0]);
                if (results[0]) {
                    var address = results[0].formatted_address;
                    var pin = results[0].address_components[results[0].address_components.length - 1].long_name;
                    var country = results[0].address_components[results[0].address_components.length - 2].long_name;
                    var state = results[0].address_components[results[0].address_components.length - 3].long_name;
                    var city = results[0].address_components[results[0].address_components.length - 4].long_name;
                    var countrycode = results[0].address_components[results[0].address_components.length - 2].long_name;
                    var statecode = results[0].address_components[results[0].address_components.length - 3].long_name;
                    var latitude = place.geometry.location.lat();
                    var longitude = place.geometry.location.lng();
                    //var name= name1.substr(0, name1.indexOf(','));

                    document.getElementById('country').value = country;
                    document.getElementById('state').value = state;
                    document.getElementById('city').value = city;
                    document.getElementById('postal_code').value = pin;
                    document.getElementById('state').value = statecode;
                    document.getElementById('country').value = countrycode;
                    document.getElementById('latitude').value = latitude;
                    document.getElementById('longitude').value = longitude;
                    //document.getElementById('hotel_name').value = name;

                }
            }
        });
    });
});

</script>
@endsection('UserContent')