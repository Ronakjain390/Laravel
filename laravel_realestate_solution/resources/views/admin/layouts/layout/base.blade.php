<?php

use App\Models\SocialLink;

$data = masterData();

$logo = '';
$favicon = '';
$title = '';
$copy = '';
$logo_mini = '';

if (isset($data[0]->logo)) {
    $logo = $data[0]->logo;
}
if (isset($data[0]->favicon)) {
    $favicon = $data[0]->favicon;
}
if (isset($data[0]->title)) {
    $title = $data[0]->title;
}
if (isset($data[0]->logo_mini)) {
    $logo_mini = $data[0]->logo_mini;
}
if (isset($data[0]->copy)) {
    $copy = $data[0]->copy;
}

?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $title }}</title>
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <link rel="shortcut icon" href="{{asset('assets/images/generalsetting/'.$favicon)}}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css')  }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css')  }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css')  }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css')  }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/fontawesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" />
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>

<body>
    <div class="container-scroller">
        <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                @if($logo)
                <a class="navbar-brand brand-logo" href="{{ url('/admin/index') }}"><img src="{{asset('assets/images/generalsetting/'.$logo)}}" alt="logo" /></a>
                @else
                <a class="navbar-brand brand-logo" href="{{ url('/admin/index') }}"><img src="{{asset('assets/images/logo.png')}}" alt="logo" /></a>
                @endif
                <a class="navbar-brand brand-logo-mini" href="{{ url('/admin/index') }}"><img src="{{asset('assets/images/generalsetting/'.$logo_mini)}}" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-stretch">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="mdi mdi-menu"></span>
                </button>
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="nav-profile-img">
                                @if(Auth::user()->image)
                                <img src="{{ asset('assets/images/admin/'.Auth::user()->image) }}" class="img-circle elevation-2" alt="User Image"> <span class="availability-status online"></span>
                                @else
                                <img src="{{ asset('assets/images/admin/default_admin.png') }}" class="img-circle elevation-2" alt="User Image"> <span class="availability-status online"></span>
                                @endif
                            </div>
                            <div class="nav-profile-text">
                                <p class="mb-1 text-black">{{ Auth::user()->name }}</p>
                            </div>
                        </a>
                        <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                            <a class="dropdown-item" href="{{ url('/admin/accountsetting') }}">
                                <i class="mdi mdi-cached me-2 text-success"></i> Edit profile </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('chnagepassword') }}">
                                <i class="mdi mdi-account me-2 text-primary"></i> Change Password </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('customLogout') }}">
                                <i class="mdi mdi-logout me-2 text-primary"></i> Signout </a>

                        </div>
                    </li>
                    <li class="nav-item d-none d-lg-block full-screen-link">
                        <a class="nav-link">
                            <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
                        </a>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                    <span class="mdi mdi-menu"></span>
                </button>
            </div>
        </nav>
        <div class="container-fluid page-body-wrapper">
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item nav-profile">
                        <a href="{{ url('/admin/index')}}" class="nav-link">
                            <div class="nav-profile-image">
                                @if(Auth::user()->image)
                                <img src="{{ asset('assets/images/admin/'.Auth::user()->image) }}" class="img-circle elevation-2" alt="User Image"> <span class="availability-status online"></span>
                                @else
                                <img src="{{ asset('assets/images/admin/default_admin.png') }}" class="img-circle elevation-2" alt="User Image"> <span class="availability-status online"></span>
                                @endif
                                <span class="login-status online"></span>
                            </div>
                            <div class="nav-profile-text d-flex flex-column">
                                <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
                                <span class="text-secondary text-small">Admin</span>
                            </div>
                            <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
                        </a>
                    </li>

                    <div id="list_sidebar">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/index')}}">
                                <span class="menu-title">Dashboard</span>
                                <i class="mdi mdi-home menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/users') }}">
                                <span class="menu-title">User Manager</span>
                                <i class="mdi mdi-account menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/agents')}}">
                                <span class="menu-title">Agent Manager</span>
                                <i class="mdi mdi-account-box-multiple menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/propertymanager') }}">
                                <span class="menu-title">Property Manager</span>
                                <i class="mdi mdi-domain menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/amenities')}}">
                                <span class="menu-title">All Amenities</span>
                                <i class="mdi mdi-pool menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/propertyenquiries')}}">
                                <span class="menu-title">Property Enquiries</span>
                                <i class="mdi mdi-account-question menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/portals')}}">

                                <span class="menu-title">CMS Manager</span>

                                <i class="mdi mdi-account-card-details menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/destinations')}}">

                                <span class="menu-title">Popular Destinations</span>

                                <i class="mdi mdi-account-card-details menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/newsletters')}}">
                                <span class="menu-title">Newsletter Templates</span>
                                <i class="mdi mdi-information menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/subscribed-users')}}">
                                <span class="menu-title">Subscribed User</span>
                                <i class="mdi mdi-account-star menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/sent-to-users')}}">
                                <span class="menu-title">Mail Sent Users </span>
                                <i class="mdi mdi-email menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/faqs')}}">
                                <span class="menu-title">Faq's</span>
                                <i class="mdi mdi-comment-question-outline menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/contacts')}}">
                                <span class="menu-title">Contacts Enquiries</span>
                                <i class="mdi mdi-contacts menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/banners') }}">
                                <span class="menu-title">Banners</span>
                                <i class="mdi mdi-flag-plus menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/news') }}">
                                <span class="menu-title">News</span>
                                <i class="mdi mdi-newspaper menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/reviewsratings') }}">
                                <span class="menu-title">Reviews and Ratings</span>
                                <i class="mdi mdi-more menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/templates') }}">
                                <span class="menu-title">Templates</span>
                                <i class="mdi mdi-checkbox-multiple-blank menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/generalsetting') }}">
                                <span class="menu-title">General Settings</span>
                                <i class="mdi mdi-cogs menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/sociallink') }}">
                                <span class="menu-title">Social Links</span>
                                <i class="mdi mdi-feather menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/smtpmail') }}">
                                <span class="menu-title">SMTP Mail</span>
                                <i class="mdi mdi-email menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/currencies') }}">
                                <span class="menu-title">Currency</span>
                                <i class="mdi mdi-currency-usd menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/propertytypes') }}">
                                <span class="menu-title">Property Type</span>
                                <i class="mdi mdi-home-circle menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('PropertyCategory') }}">
                                <span class="menu-title">Property category</span>
                                <i class="mdi mdi-home-group menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('Flags') }}">
                                <span class="menu-title">Flags</span>
                                <i class="mdi mdi-flag-minus menu-icon"></i>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/countries') }}">
                                <span class="menu-title">Countries</span>
                                <i class="mdi mdi-tie menu-icon"></i>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/states') }}">
                                <span class="menu-title">States</span>
                                <i class="mdi mdi-map-marker-distance menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/cities') }}">
                                <span class="menu-title">Cities</span>
                                <i class="mdi mdi-map-marker-radius menu-icon"></i>
                            </a>
                        </li>
                    </div>

                </ul>
            </nav>
            <div class="main-panel">
                @yield('main_content')
                <footer class="footer">
                    <div class="container-fluid d-flex justify-content-between">
                        <span class="text-muted d-block text-center text-sm-start d-sm-inline-block">Copyright © AddWebSolution@2022</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js')  }}"></script>
    <script src="{{ asset('assets/vendors/chart.js/Chart.min.js')  }}"></script>
    <script src="{{ asset('assets/js/jquery.cookie.js')  }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/off-canvas.js')  }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js')  }}"></script>
    <script src="{{ asset('assets/js/misc.js')  }}"></script>
    <script src="{{ asset('assets/js/dashboard.js')  }}"></script>
    <script src="{{ asset('assets/js/todolist.js')  }}"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
    <script src="{{ asset('js/toogle.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false&key={{ env('GOOGLE_ADDRESS_KEY') }}&libraries=places&callback=initAutocomplete"></script>
    <script src="{{ asset('js/googleAddress.js')  }}"></script>

    <script src="https://unpkg.com/bootstrap@3.3.2/dist/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/bootstrap-multiselect@0.9.13/dist/js/bootstrap-multiselect.js"></script>
    <script src="{{ asset('js/checkbox.js') }}"></script>

    <script src="{{ asset('js/ckeeditor.js') }}"></script>

    <script>
        let list_sidebar = document.getElementById("list_sidebar")
        let path = window.location.href

        let all_li = list_sidebar.getElementsByTagName("li")

        for (let i of all_li) {
            let a = i.getElementsByTagName('a')[0]
            if (a.href === path) {
                i.classList.add("active")
            }
        }
    </script>
</body>

</html>