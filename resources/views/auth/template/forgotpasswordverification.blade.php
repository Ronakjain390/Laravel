<?php

$data = masterData();

// echo $data;
// die;

$logo = '';
if (isset($data[0]->logo) == 1) {
    $logo = $data[0]->logo;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Real Estate</title>
    <link rel="stylesheet" href="{{asset('adminauth/assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{asset('adminauth/assets/vendors/iconfonts/ionicons/dist/css/ionicons.css') }}">
    <link rel="stylesheet" href="{{asset('adminauth/assets/vendors/iconfonts/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{asset('adminauth/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{asset('adminauth/assets/vendors/css/vendor.bundle.addons.css') }}">
    <link rel="stylesheet" href="{{asset('adminauth/assets/css/shared/style.css') }}">
    <link rel="shortcut icon" href="{{asset('adminauth/assets/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
                <div class="row w-100 m-0">
                    <div class="col-lg-4 col-md-6 col-sm-8 login-container  mx-auto">
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
                        <div class="auto-form-wrapper">
                            @if($logo)
                            <a href="{{url('/')}}">
                                <img src="{{ asset('assets/images/generalsetting/'.$logo) }}" alt="" width="150" height="40" class="logocuston mx-0"></a>
                            @else
                            <a href="{{url('/')}}">
                                <img src="{{ asset('assets/images/logo.png') }}" alt="" width="150" height="40" class="logocuston mx-0"></a>
                            @endif

                            <h5 mx-0 class="logocuston">Reset Password</h5>
                            <form method="POST" action="{{ route('resetpassword', $token) }}">
                                @csrf
                                <div class="form-group">
                                    <label class="label">Email<span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="email" class="form-control" placeholder="Username">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="label">New Password<span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="password" class="form-control" placeholder="password">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="label">Confirm Password<span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="confirmpassword" class="form-control" placeholder="confirm password">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary submit-btn btn-block">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('adminauth/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{asset('adminauth/assets/vendors/js/vendor.bundle.addons.js') }}"></script>
    <script src="{{asset('adminauth/assets/js/shared/off-canvas.js') }}"></script>
    <!-- <script src="{{asset('adminauth/assets/js/shared/misc.js') }}"></script> -->
    <script src="{{asset('adminauth/assets/js/shared/jquery.cookie.js') }}" type="text/javascript"></script>
</body>

</html>

<style>
    .logocuston {
        margin-left: 135px;
        margin-bottom: 20px;
    }

    .color_blue {
        color: blue;
    }
</style>