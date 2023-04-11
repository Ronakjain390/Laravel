@extends('User/Layouts/Layout/HomeLayout')
@section('UserContent')
<style>
    /* Customize the side card */
    .side-menu {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 250px;
        background-color: #f5f5f5;
        padding: 20px;
        overflow-y: auto;
    }

    .side-menu a {
        display: block;
        padding: 10px;
        color: #333;
        text-decoration: none;
        border-left: 3px solid transparent;
    }

    .side-menu a.active {
        border-left: 3px solid #007bff;
        background-color: #007bff;
        color: #fff;
    }

    /* Customize the content */
    .content {
        margin-left: 250px;
        padding: 20px;
    }

    /* Hide all pages except the active page */
    .page {
        display: none;
    }

    .page.active {
        display: block;
    }
</style>
<section>
    <br><br><br><br><br>

    <div class="container mt-10">
        <div class=" page-container" id="page-content">
            <div class="row   justify-content-center">

                <div class="col-xl-12 col-md-12">
                    <div class="card user-card-full">
                        <div class="row m-l-0 m-r-0">
                            <div class="col-md-3">
                                <div class="side-menu">
                                    <h4>Menu</h4>
                                    <a href="#" class="active" data-page="home">Home</a>
                                    <a href="#" data-page="about">Edit Profile</a>
                                    <!-- <a href="#" data-page="contact">Contact</a>
                                    <a href="{{ route('TicketListing') }}" >Bookings</a> -->
                                    <a href="{{ route('ChangePassword') }}" data-page="password">Change Password</a>
                                </div>
                            </div>
                            <div class="col-md-9">

                                <div id="home" class="page active"> <br>
                                    <h2>Profile Image</h2>
                                    <div class="user-avatar">
                                        @if(Auth::user()->image)
                                        <img src="{{ asset('Assets/images/'.Auth::user()->image) }}" alt class="rounded-circle" height="120px" width="120px" />
                                        @else
                                        <img src="{{ asset('Assets/DefaultImage/defaultProfile.png') }}" class="img-thumbnail" alt="No Image" height="100px" width="100px" />
                                        @endif
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="card-block">
                                            <h5 class="m-b-20 p-b-5 b-b-default ">Account Details</h5>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <h5>Email</h5>
                                                    <h6 class="text-muted f-w-400">{{ Auth::user()->email }}</h6>
                                                </div>

                                                <div class="col-sm-6">
                                                    <h5>Phone Number</h5>
                                                    <h6 class="text-muted f-w-400">{{ Auth::user()->contact }}</h6>
                                                </div>

                                                <div class="col-sm-6">
                                                    <h5>Address</h5>
                                                    <h6 class="text-muted f-w-400">{{ Auth::user()->address }}</h6>
                                                </div>

                                                <div class="col-sm-6">
                                                    <h5>Country</h5>
                                                    <h6 class="text-muted f-w-400">{{ Auth::user()->country }}</h6>
                                                </div>

                                                <div class="col-sm-6">
                                                    <h5>Postal Code</h5>
                                                    <h6 class="text-muted f-w-400">{{ Auth::user()->postal_code }}</h6>
                                                </div>

                                            </div>
                                            <!-- <h6 class="m-b-20 m-t-40 p-b-5 b-b-default f-w-600">Projects</h6>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <p class="m-b-10 f-w-600">Recent</p>
                                            <h6 class="text-muted f-w-400">Sam Disuja</h6>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="m-b-10 f-w-600">Most Viewed</p>
                                            <h6 class="text-muted f-w-400">Dinoter husainm</h6>
                                        </div>
                                    </div>
                                    <ul class="social-link list-unstyled m-t-40 m-b-10">
                                        <li><a href="#!" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="facebook" data-abc="true"><i class="mdi mdi-facebook feather icon-facebook facebook" aria-hidden="true"></i></a></li>
                                        <li><a href="#!" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="twitter" data-abc="true"><i class="mdi mdi-twitter feather icon-twitter twitter" aria-hidden="true"></i></a></li>
                                        <li><a href="#!" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="instagram" data-abc="true"><i class="mdi mdi-instagram feather icon-instagram instagram" aria-hidden="true"></i></a></li>
                                    </ul> -->
                                        </div>
                                    </div>

                                </div>
                                <div id="about" class="page">
                                    <h2>Edit Profile</h2>
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
                                                <input type="text" class="form-control" id="address" name="address" value="{{ Auth::user()->address }}" placeholder="Address" />
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
                                <div id="contact" class="page">
                                    <h2>Contact Page</h2>
                                    <p>Contact us with any questions or concerns.</p>
                                </div>

                                <!-- Change Password -->

                                <div id="password" class="page">
                                    <h2>Change Password</h2>
                                    <form method="POST" id="formAccountSettings" action="{{route('PasswordChange', Auth::user()->id)}}" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label for="current_password" class="form-label">Current Password</label>
                                                <input class="form-control" type="password" id="current_password" name="current_password" placeholder="Current Password" autofocus />
                                                <span><i class="fa fa-eye" id="current_password" onclick="myFunction(this.id)"></i></span>

                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="country">New Password</label>
                                                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" maxlength="6" />
                                                <span><i class="fa fa-eye" id="new_password" onclick="myFunction(this.id)"></i></span>

                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="country">Confirm Password</label>
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" maxlength="6" />
                                                <span><i class="fa fa-eye" id="confirm_password" onclick="myFunction(this.id)"></i></span>

                                            </div>


                                            <div class="mt-2">
                                                <input type="submit" class="btn btn-primary " value="Save Changes">
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Bookings -->

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
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
    // Get all the page links
    const pageLinks = document.querySelectorAll('.side-menu a');

    // Add a click event listener to each link
    pageLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();

            // Get the page to show
            const page = link.getAttribute('data-page');

            // Remove the active class from all links
            pageLinks.forEach(link => {
                link.classList.remove('active');
            });

            // Add the active class to the clicked link
            link.classList.add('active');

            // Hide all pages
            const pages = document.querySelectorAll('.page');
            pages.forEach(page => {
                page.classList.remove('active');
            });

            // Show the selected page
            document.getElementById(page).classList.add('active');
        });
    });

    function myFunction(id) {
        var x = document.getElementById(id);
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>
@endsection('UserContent')