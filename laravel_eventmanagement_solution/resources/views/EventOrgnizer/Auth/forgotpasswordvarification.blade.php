<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- <title>Pages / Login - NiceAdmin Bootstrap Template</title> -->
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('Assets/EventOrgnizer/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('Assets/EventOrgnizer/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('Assets/EventOrgnizer/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('Assets/EventOrgnizer/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('Assets/EventOrgnizer/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('Assets/EventOrgnizer/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('Assets/EventOrgnizer/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('Assets/EventOrgnizer/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('Assets/EventOrgnizer/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('Assets/EventOrgnizer/css/style.css') }}" rel="stylesheet">
</head>

<body>

    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            @if (Session::has('success'))
                            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                                {{ Session::get('success') }}
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @elseif(Session::has('error'))
                            <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" role="alert">
                                {{ Session::get('error') }}
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif
                            <!-- <div class="d-flex justify-content-center py-4">
                                <a href="#" class="logo d-flex align-items-center w-auto">
                                    <img src="assets/img/logo.png" alt="">
                                    <span class="d-none d-lg-block">NiceAdmin</span>
                                </a>
                            </div> -->

                            <div class="card mb-3">

                                <div class="card-body">
                                <h5 class="card-title text-center pb-0 fs-4">
                                        <span class="app-brand-logo demo">
                                            <img src="{{ asset('Assets/DefaultImage/Default_logo.png')}}" alt="img" height="50" width="200">
                                        </span></h5>
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Reset Password</h5>
                                        <p class="text-center small">Enter email & password to reset password</p>
                                    </div>
                                    <form class="row g-3 needs-validation" novalidate action="{{ route('resetpassword', $token) }}" method="POST">
                                        @csrf
                                        <div class="col-12">
                                            <label for="yourEmail" class="form-label">Your Email</label>
                                            <input type="email" name="email" class="form-control" id="yourEmail" required>
                                            <div class="invalid-feedback">Please enter a valid Email adddress!</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Password</label>
                                            <input type="password" name="password" class="form-control" id="yourPassword" required>
                                            <div class="invalid-feedback">Please enter your password!</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Confirm Password</label>
                                            <input type="password" name="confirmpassword" class="form-control" id="yourPassword" required>
                                            <div class="invalid-feedback">Please enter your confirm password!</div>
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Reset</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

        </div>
    </main><!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('Assets/EventOrgnizer/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('Assets/EventOrgnizer/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('Assets/EventOrgnizer/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('Assets/EventOrgnizer/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('Assets/EventOrgnizer/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('Assets/EventOrgnizer/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('Assets/EventOrgnizer/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('Assets/EventOrgnizer/vendor/php-email-form/validate.js') }}"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('Assets/EventOrgnizer/js/main.js') }}"></script>

</body>

</html>