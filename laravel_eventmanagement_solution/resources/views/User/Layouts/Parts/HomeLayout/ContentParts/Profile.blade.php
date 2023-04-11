<?php
$generalSetting = generalSetting();
?>
<header>
    <div class="header-area ">
        <div id="sticky-header" class="main-header-area">
            <div class="container">
                <div class="header_bottom_border">
                    <div class="row align-items-center">
                        <div class="col-xl-3 col-lg-3">
                            <div class="logo">
                                <a href="index.html">
                                    <img src="{{asset('Assets/SuperAdmin/img/GeneralSetting/' .$generalSetting->logo )}}" alt="" width="200px">
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6">
                            <div class="main-menu  d-none d-lg-block">
                                <nav>
                                    <ul id="navigation">
                                        <li><a href="{{ route('home') }}">home</a></li>
                                        <li><a href="{{ route('EventShow')}}">Events</a></li>
                                        <li><a href="{{ route('ArtistShow')}}">Artist</a></li>
                                        <li><a href="#">pages <i class="ti-angle-down"></i></a>
                                            <ul class="submenu">
                                                <li><a href="about.html">about</a></li>
                                                <li><a href="Program.html">Program</a></li>
                                                <li><a href="#">Venue</a></li>
                                                <li><a href="elements.html">elements</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#">blog <i class="ti-angle-down"></i></a>
                                            <ul class="submenu">
                                                <li><a href="blog.html">blog</a></li>
                                                <li><a href="single-blog.html">single-blog</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="contact.html">Contact</a></li>
                                        
                                        @if(Auth::user())
                                        <li><a href="#"></a> <i class="fa fa-user" aria-hidden="true"></i></a>
                                        
                                            <ul class="submenu">
                                                <li><a href="{{ route('UserProfile') }}">Profile</a></li>
                                                <li><a href="{{ route('ChangePassword') }}">Change Password</a></li>
                                                <li><a href="{{ route('logout') }}">Log out</a></li>
                                            </ul>
                                        </li>
                                        @else
                                        <li><a href="{{ route('login') }}">Login</a></li>
                                        @endif

                                    </ul>
                                </nav>
                            </div>
                            </li>
                        </div>
                    </div>
                    <!-- <div class="col-xl-3 col-lg-3 d-none d-lg-block">
                                <div class="buy_tkt">
                                    <div class="book_btn d-none d-lg-block">
                                        <a href="#">Buy Tickets</a>
                                    </div>
                                </div>
                            </div> -->
                    <div class="col-12">
                        <div class="mobile_menu d-block d-lg-none"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
</header> 