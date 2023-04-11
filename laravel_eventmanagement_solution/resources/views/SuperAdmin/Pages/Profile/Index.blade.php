@extends('SuperAdmin/Layouts/Layout/HomeLayout')
@section('SuperAdminContent')
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->


        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->

                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Profile Settings /</span> Account</h4>

                    <div class="row">
                        <div class="col-md-12">
                            <!-- <ul class="nav nav-pills flex-column flex-md-row mb-3">
                                <li class="nav-item">
                                    <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Account</a>
                                </li>
                            </ul> -->
                            <div class="card mb-4">
                                <h5 class="card-header">Profile Details</h5>
                                @if (Session::has('success'))
                                <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" id="message" role="alert">
                                    {{ Session::get('success') }}
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                @elseif(Session::has('error'))
                                <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" id="message" role="alert">
                                    {{ Session::get('error') }}
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                @endif
                                <!-- Account -->
                                <div class="card-body">
                                    <form id="formAccountSettings" action="{{ route('UpdateAdminProfile', Auth::user()->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                                        @csrf
                                        @method('PUT')
                                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                                            @if(auth()->user()->image == '')
                                            <img src="{{ asset('Assets/DefaultImage/defaultProfile.png') }}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                                            @else
                                            <img src="{{ asset('Assets/images/' . auth()->user()->image) }}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                                            @endif
                                            <div class="button-wrapper">
                                                <label for="logo">Profile Image</label>
                                                <input type="file" class="form-control" id="image" name="image" />
                                                <input type="hidden" name="hidden_Image" value="{{ auth()->user()->image }}" />
                                                <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                                            </div>
                                        </div>
                                </div>
                                <hr class="my-0" />
                                <div class="card-body">

                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label for="firstName" class="form-label">Name</label>
                                            <input class="form-control" type="text" id="firstName" name="name" value="{{ Auth::user()->name }}" autofocus />
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label for="email" class="form-label">E-mail</label>
                                            <input class="form-control" type="text" id="email" name="email" value="{{ Auth::user()->email }}" placeholder="john.doe@example.com" />
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="phoneNumber">Phone Number</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text">(+91)</span>
                                                <input type="text" id="phoneNumber" name="contact_number" value="{{ Auth::user()->contact_number }}" class="form-control" placeholder="202 555 0111" />
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
                                        <!-- <div class="mb-3 col-md-6">
                                                <label for="language" class="form-label">Language</label>
                                                <select id="language" class="select2 form-select">
                                                    <option value="">Select Language</option>
                                                    <option value="en">English</option>
                                                    <option value="fr">French</option>
                                                    <option value="de">German</option>
                                                    <option value="pt">Portuguese</option>
                                                </select>
                                            </div> -->
                                        <!-- <div class="mb-3 col-md-6">
                                                <label for="timeZones" class="form-label">Timezone</label>
                                                <select id="timeZones" class="select2 form-select">
                                                    <option value="">Select Timezone</option>
                                                    <option value="-12">(GMT-12:00) International Date Line West</option>
                                                    <option value="-11">(GMT-11:00) Midway Island, Samoa</option>
                                                    <option value="-10">(GMT-10:00) Hawaii</option>
                                                    <option value="-9">(GMT-09:00) Alaska</option>
                                                    <option value="-8">(GMT-08:00) Pacific Time (US & Canada)</option>
                                                    <option value="-8">(GMT-08:00) Tijuana, Baja California</option>
                                                    <option value="-7">(GMT-07:00) Arizona</option>
                                                    <option value="-7">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                                                    <option value="-7">(GMT-07:00) Mountain Time (US & Canada)</option>
                                                    <option value="-6">(GMT-06:00) Central America</option>
                                                    <option value="-6">(GMT-06:00) Central Time (US & Canada)</option>
                                                    <option value="-6">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                                                    <option value="-6">(GMT-06:00) Saskatchewan</option>
                                                    <option value="-5">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
                                                    <option value="-5">(GMT-05:00) Eastern Time (US & Canada)</option>
                                                    <option value="-5">(GMT-05:00) Indiana (East)</option>
                                                    <option value="-4">(GMT-04:00) Atlantic Time (Canada)</option>
                                                    <option value="-4">(GMT-04:00) Caracas, La Paz</option>
                                                </select>
                                            </div> -->
                                        <!-- <div class="mb-3 col-md-6">
                                                <label for="currency" class="form-label">Currency</label>
                                                <select id="currency" class="select2 form-select">
                                                    <option value="">Select Currency</option>
                                                    <option value="usd">USD</option>
                                                    <option value="euro">Euro</option>
                                                    <option value="pound">Pound</option>
                                                    <option value="bitcoin">Bitcoin</option>
                                                </select>
                                            </div> -->

                                        <hr class="my-0" />
                                        <h5 class="card-header">Change Password</h5>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="country">Current Password</label>
                                            <input type="password" class="form-control" autocomplete="new-password" id="old_password" name="old_password" placeholder="Current Password" maxlength="12" />

                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="country">New Password</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" maxlength="12" />

                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="country">Confirm Password</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" maxlength="12" />
                                        </div>

                                    </div>
                                    <div class="mt-2">
                                        <input type="submit" class="btn btn-primary " value="Save Changes">
                                    </div>
                                    </form>
                                </div>
                                <!-- /Account -->
                            </div>
                            <!-- <div class="card">
                  <h5 class="card-header">Delete Account</h5>
                  <div class="card-body">
                    <div class="mb-3 col-12 mb-0">
                      <div class="alert alert-warning">
                        <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your account?</h6>
                        <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
                      </div>
                    </div>
                    <form id="formAccountDeactivation" onsubmit="return false">
                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="accountActivation"
                          id="accountActivation" />
                        <label class="form-check-label" for="accountActivation">I confirm my account
                          deactivation</label>
                      </div>
                      <button type="submit" class="btn btn-danger deactivate-account">Deactivate Account</button>
                    </form>
                  </div>
                </div> -->
                        </div>
                    </div>
                </div>
                <!-- / Content -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->
@endsection('SuperAdminContent')