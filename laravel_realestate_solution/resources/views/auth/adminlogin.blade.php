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
                    <div class="mx-auto">
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
                        <div class="login-container auto-form-wrapper">
                            <div class="card">
                                @if($logo)
                                <a href="{{url('/')}}">
                                    <img src="{{ asset('assets/images/generalsetting/'.$logo) }}" alt="" width="150" height="40" class="logocuston mx-auto"></a>
                                @else
                                <a href="{{url('/')}}">
                                    <img src="{{ asset('assets/images/logo.png') }}" alt="" width="150" height="40" class="logocuston mx-auto"></a>
                                @endif
                                <div class="card-header logocuston mx-0"><p class="h2">Admin Login</p></div><br>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('adminLoggedin') }}">
                                        @csrf
                                        <div class="form-group">
                                            <!-- <label class="label">Username<span class="required">*</span></label> -->
                                            
                                            <div class="input-group input-box d-flex flex-row align-items-center mb-4">
                                                <i class="mdi mdi-email me-3" aria-hidden="true"></i>
                                                <div class="form-outline flex-fill mb-0">
                                                    <input type="text" name="email" class="form-control" placeholder="Username">
                                                </div>
                                                
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="mdi mdi-check-circle-outline"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group input-box d-flex flex-row align-items-center mb-4">
                                                <i class="mdi mdi-lock me-3" aria-hidden="true"></i>
                                                <div class="form-outline flex-fill mb-0">
                                                    <input type="password" name="password" class="form-control" placeholder="Password">
                                                </div>
                                                
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="mdi mdi-check-circle-outline"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <!-- <label class="label">Password<span class="required">*</span></label> -->
                                        </div>
                                        <div class="form-group d-flex justify-content-between">
                                            <div class="form-check form-check-flat mt-0">
                                                <input type="checkbox" class="form-check-input" name="remember" checked> <label class="form-check-label p-0">
                                                    Keep me signed in </label>
                                            </div>
                                            <a href="{{ route('admin_forgot_screen') }}" class=" text-small forgot-password">
                                                Forgot Password
                                            </a>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-login">Login</button>
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
</style>