<?php
$generalSetting = generalSetting();
$logo = '';
if ($generalSetting) {
    $logo = $generalSetting->logo;
}
?>
<header class="header">
    <div class="header-area ">
        <div id="sticky-header" class="main-header-area">
            <div class="container">
                <div class="header_bottom_border">
                    <div class="d-flex align-items-center header-container">
                        <div class="logo">
                            <a href="{{ url('/') }}">
                                @if($logo=='')
                                <img src="{{asset('Assets/DefaultImage/logoDefault.jpg' )}}" alt="" width="200px">
                                @else
                                <img src="{{asset('Assets/images/' .$generalSetting->logo )}}" alt="" width="200px">
                                @endif
                            </a>
                        </div>
                        
                        <div class="main-menu  d-none d-lg-block">
                            <nav class="">
                                <ul id="navigation">
                                    <li class="{{ request()->is('*/*') ? 'active' : '' }}"><a href="{{ route('home') }}">home</a></li>
                                    <li class="{{ request()->is('*view-event*') ? 'active' : '' }}"><a href="{{ route('EventShow')}}">Events</a></li>
                                    <li class="{{ request()->is('*view-artist*') ? 'active' : '' }}"><a href="{{ route('ArtistShow')}}">Artist</a></li>
                                    <!-- <li><a href="#">pages <i class="ti-angle-down"></i></a>
                                            <ul class="submenu">
                                                <li><a href="about.html">about</a></li>
                                                <li><a href="Program.html">Program</a></li>
                                                <li><a href="#">Venue</a></li>
                                                <li><a href="elements.html">elements</a></li>
                                            </ul>
                                        </li> -->
                                    <!-- <li><a href="#">blog <i class="ti-angle-down"></i></a>
                                                <ul class="submenu">
                                                    <li><a href="blog.html">blog</a></li>
                                                    <li><a href="single-blog.html">single-blog</a></li>
                                                </ul>
                                            </li> -->

                                            @if(!Auth::user())
                                        <li class="{{ request()->is('*event-orgnizer/login*') ? 'active' : '' }}"><a href="{{ route('EventOrg') }}">Create Event</a></li>
@endif
                                        <!-- <li><a href="contact.html">Contact</a></li> -->
                                        @if(Auth::user())
                                        <li><a class="account" style="height: 50px;">
                                                @if(Auth::user()->image)
                                                <img src="{{ asset('Assets/images/'.Auth::user()->image) }}" alt class="rounded-circle" height="40px" width="40px" />
                                                @else
                                                <img src="{{ asset('Assets/DefaultImage/defaultProfile.png') }}" class="img-thumbnail" alt="No Image" height="50px" width="40px" />
                                                @endif
                                                My Account</a></a>
                                            <ul class="submenu" style="font-size: 16px;">
                                                <li><a href="{{ route('UserProfile') }}">Profile</a></li>
                                                <li><a href="{{ route('ViewFavouriteArtist') }}">Favourite Artist</a></li>
                                                <li><a href="{{ route('TicketListing') }}">Your Orders</a></li>
                                                <li><a href="{{ route('ChangePassword') }}">Change Password</a></li>
                                                <li><a href="{{ route('logout') }}">Log out</a></li>
                                            </ul>
                                        </li>
                                        @else
                                        <li class="{{ request()->is('*login*') ? 'active' : '' }}"><a href="{{ route('login') }}">Login</a></li>
                                        @endif
                                    </ul>


                            </nav>
                        </div>
                        <div class="search-bar">
                            <?php $search = "search";  ?>
                            <aside class="single_sidebar_widget search_widget">
                                <form action="{{ route('Search') }}">
                                    <div class="form-group mb-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search" placeholder='Search Keyword' onfocus="this.placeholder = ''" onblur="this.placeholder = 'Search Keyword'">

                                            <div class="input-group-append mx-0">
                                                <button class="btn btn-outline-dark px-3" name="" type="submit"><i class="ti-search"></i></button>
                                            </div>

                                        </div>
                                    </div>

                                </form>
                            </aside>
                        </div>
                        <!-- <div class="col-xl-3 col-lg-3 d-none d-lg-block">
                                <div class="buy_tkt">
                                    <div class="book_btn d-none d-lg-block">
                                        <a href="#">Buy Tickets</a>
                                    </div>
                                </div>
                            </div> -->
                        <div class="toggler d-block d-lg-none">
                            <div class="mobile_menu"></div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</header>
@push('js')

<script>
    $(document).ready(function() {
        $("#regForm").validate({
            rules: {
                search: "required",
            }
        });
    });
</script>
@if(Session::has('success'))
<script>
    toastr.success("{{ Session::get('success') }}")
</script>
@elseif(Session::has('error'))
<script>
    toastr.error("{{ Session::get('error') }}")
</script>
@endif

<style>
    .main-menu li.active a {
        color: #037EE6 !important;
        /* text-decoration: underline; */
    }
</style>
@endpush