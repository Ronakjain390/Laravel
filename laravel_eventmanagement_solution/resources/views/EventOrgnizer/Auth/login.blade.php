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
                                <a href="" class="logo d-flex align-items-center w-auto">
                                    <img src="assets/img/logo.png" alt="">
                                    <span class="d-none d-lg-block">NiceAdmin</span>
                                </a>
                            </div> -->

                            <div class="card mb-3">

                                <div class="card-body">

                                    <div class="pt-4 pb-2">

                                        <h5 class="card-title text-center pb-0 fs-4">
                                            <span class="app-brand-logo demo">
                                                <img src="{{ asset('Assets/DefaultImage/Default_logo.png')}}" alt="img" height="50" width="200">
                                            </span>
                                        </h5>
                                        <p class="card-title text-center pb-0 fs-4">Login to Your Account</p>
                                    </div>

                                    <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('eventOrgnizerLogin') }}">
                                        @csrf
                                        <div class="col-12">
                                            <label for="yourUsername" class="form-label">Email</label>
                                            <div class="input-group has-validation">
                                                <input type="email" name="email" class="form-control" id="Your Email" required>
                                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                <div class="invalid-feedback">Please enter your Email.</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Password</label>
                                            <div class="input-group has-validation">

                                                <input type="password" name="password" class="form-control" id="password" required>
                                                <span class="input-group-text" id="inputGroupPrepend"> <i class="bx bx-show-alt me-1" id="togglePassword"></i></span>

                                            </div>

                                            <div class="invalid-feedback">Please enter your password!</div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                                                <label class="form-check-label" for="rememberMe">Remember me</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Login</button>
                                        </div>
                                    </form>
                                    <div class="col-12 mt-4">
                                        <a class="small mb-0" href="{{ route('forgotpassword') }}">Forgot Password?</a>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <p class="small mb-0">Don't have account? <a href="{{ route('orgnizerregister') }}">Sign Up</a></p>
                                    </div>
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

    <script>
        function showPassword() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
    
        togglePassword.addEventListener("click", function() {
    
            // toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            // toggle the eye icon
            this.classList.toggle('bx bx-show-alt me-1');
            this.classList.toggle('bx bx-edit-alt me-1');
        });
    </script>
</body>

</html>