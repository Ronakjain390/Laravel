<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta property="og:site_name" content="theparchi.com">
    <meta property="og:title" content="TheParchi" />
    <meta property="og:description"
        content="Paperless proof of delivery of your goods!! No more paper challan and signed receiving required for sending or receiving goods. Instant confirmation with digital records accessible online all the time." />
    <meta property="og:image" itemprop="image" content="https://theparchi.com/Vector.png">
    <meta property="og:type" content="website" />
    <meta property="og:updated_time" content="1440432930" />
    <link rel="icon" src="{{asset('image/Vector.png')}}" type="image/gif" sizes="16x16">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style type="text/css">
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            margin: 0;
        }
    </style>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TheParchi</title>

    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '957334241530125');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=957334241530125&ev=PageView&noscript=1" /></noscript>


    <link href="{{asset('css/bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('css/home.css')}}" rel="stylesheet">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body class="login" style="background-color: #070505;">

    <div class="container-fluid overflow-hidden">
        <div class="row ro1">
            <div class="coll co1">
                <ul class="navbar-nav uli">
                    <li class="nav-item uli-1" style="cursor: pointer;"><a class="nav-link isDown" data-modal-target="default-modal" data-modal-toggle="default-modal">Connect
                            us!!</a></li>
                </ul>
            </div>
            
            <div class="coll co2 ">
                <li class="mt-3 nav-item uli-5 ml-5" style="
                    list-style: none;
                "><a
                        class="isDown ml-5 nav-link text-center confirmation-text">
                        <p class="mr-2" style="text-align: center;text-align-last: left;float: right;">Instant
                            confirmation on<br />receipt from Buyer</p>
                    </a></li>
            </div>
            <div class="coll co3">
                <ul class="navbar-nav uli">
                    <li class="nav-item uli-2" style="cursor: pointer;"><a class="nav-link isDown what-is-tp">What is
                            TheParchi??</a></li>
                </ul>
            </div>
        </div>
        <div class="row ro2 d-flex justify-content-center " style="align-items: center;">
            <div class="coll co4">
                {{-- <ul class="navbar-nav uli">
                    <li class="nav-item uli-3" onclick="location.href='/paperless-proof'"><a
                            class="nav-link isDown easy-invoicing">Easy Invoicing!!</a></li>
                </ul> --}}
                <ul class="navbar-nav uli">
                    <li class="nav-item uli-3" ><a
                            class="nav-link isDown easy-invoicing">Easy Invoicing!!</a></li>
                </ul>
            </div>
            <div class="coll co5"></div>
            <div class="coll co6">
            </div>
            <div class=" mt-0 mb-auto col-11 d-flex align-items-center  coll4-1 px-3 py-3 px-md-5 py-md-5 " style>
                
                <div class="login_wrapper w-100">
                    
                    <div class="animate form login_form" style="height: 100%">
                        <section class="login_content">
                            @if ((session('success')))
                                <div id="alert-border-3" class="flex items-center p-2 mb-4 text-green-800 border-t-4 border-green-300 bg-[#d4edda] dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
                                    <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                      <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                    </svg>
                                    <div class="ms-3 text-sm font-medium">
                                        Thank You!! TheParchi team shall connect with you shortly 
                                    </div>
                                    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700"  data-dismiss-target="#alert-border-3" aria-label="Close">
                                      <span class="sr-only">Dismiss</span>
                                      <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                      </svg>
                                    </button>
                                </div>
                                @endif
                            <div class="row">
                                <div class="col-12">
                                    <p class="fa-3x"><span style="color:#8159A9;">Move Your Business</span><br><small
                                            class="small">From paper to</small>
                                        <br> TheParchi
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-0 small">TheParchi provides paperless, contactless and digital
                                        mechanism
                                        for proof and record keeping of goods delivered,received,
                                        sold and bought</p>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <a href="https://theparchi.com"><img src="{{asset('image/Vector.png')}}"
                                        class="splash-logo" /></a>
                            </div>
                            {{-- <div class="row mb-1">
                                <div class="col-12 col-md-9 mx-auto custom-floating">
                                    <a href="/login" class="btn btn-block"
                                        style="background-color: black; color:#E5F811;font-size: 14px;" value="seller"
                                        name="submit"><i class="fa fa-user" aria-hidden="true" style></i>
                                        Login</a>
                                </div>
                            </div> --}}
                            <div class="row mb-1">
                                
                                <div class="col-12 col-md-9 mx-auto custom-floating inline-flex ">
                                    <a  href="/login"
                                    class="inline-flex w-full rounded-xl justify-center items-center px-4 py-2 text-sm font-medium text-[#E5F811] bg-black border border-gray-200 rounded-s-lg hover:no-underline hover:text-[#E5F811] dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600  ">
                                    <svg className="text-gray-800 dark:text-white" class="h-3 mr-3"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        viewBox="0 0 14 18">
                                        <path
                                            d="M7 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Zm2 1H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z" />
                                    </svg>
                                    Login
                                </a>
                                       
                                </div>
                            </div>
                            <div class="row mb-1">
                                
                                <small class="col-12 col-md-9 mx-auto custom-floating inline-flex">Still exploring.....</small>
                                <div class="col-12 col-md-9 mx-auto custom-floating inline-flex ">
                                    <a  href="/register"
                                    class="inline-flex w-full rounded-xl justify-center items-center px-4 py-2 text-sm font-medium  bg-white text-black border border-dark hover:text-black hover:no-underline">
                                    {{-- <svg className="text-gray-800 dark:text-white" class="h-3 mr-3"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        viewBox="0 0 14 18">
                                        <path
                                            d="M7 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Zm2 1H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z" />
                                    </svg> --}}
                                    Start free trial
                                </a>
                                       
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="col-12 col-md-9 mx-auto custom-floating">
                                    <small>Still exploring.....</small>
                                    <a href="/register" class="btn btn-block "
                                        style="background-color: rgb(255, 255, 255); color:#000000;font-size: 14px; border: 1px solid black; text-align: center;"
                                        value="buyer" name="submit">Start free trial</a>
                                </div>
                                
                            </div> --}}
                            <div class="clearfix"></div>
                            <div class="separator" style="display: none;">
                                </p>
                                <div class="clearfix"></div>
                                <br />
                                <div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <div class="row ro3">
            <div class="coll co7">
                <ul class="navbar-nav uli">
                    <li class="nav-item uli-6" style="cursor: pointer;"><a class="nav-link isDown free-trial">Free
                            trial</a></li>
                </ul>
            </div>
            <div class="coll co8">
                {{-- <ul class="navbar-nav uli">
                    <li class="nav-item uli-4" onclick="location.href='/paperless-proof'" style="cursor: pointer; min-width:250px;">
                        <a class="nav-link isDown">Paperless proof of delivery of your goods!!</a>
                    </li>
                </ul> --}}
                <ul class="navbar-nav uli">
                    <li class="nav-item uli-4" style="cursor: pointer; min-width:250px;">
                        <a class="nav-link isDown">Paperless proof of delivery of your goods!!</a>
                    </li>
                </ul>
                
                {{-- <ul class="navbar-nav uli">
                    <li class="nav-item uli-7" style="cursor: pointer;"
                        onclick="location.href='/contactless'"><a
                            class="nav-link isDown">#Contactless</a>
                    </li>
                </ul> --}}
                <ul class="navbar-nav uli">
                    <li class="nav-item uli-7" style="cursor: pointer;"
                        ><a
                            class="nav-link isDown">#Contactless</a>
                    </li>
                </ul>
            </div>
            <div class="coll co9">
                <ul class="navbar-nav uli">
                    <li class="nav-item uli-2" style="cursor: pointer;"><a class="nav-link isDown what-is-tp">What
                            is<br>
                            TheParchi??</a></li>
                </ul>
            </div>
        </div>
        <div class="row ro5">
            <nav>
                {{-- <div class="navicon isDown">
                    <div class="icon"><i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i></div>
                </div> --}}
                <div class="navicon1 isDown">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a  class="nav-link isDown connect-us2">Connect us!!</a></li>
                        {{-- <li class="nav-item" onclick="location.href='/paperless-proof'"><a
                                class="nav-link isDown"
                                onclick="location.href='/paperless-proof'">Easy
                                Invoicing!!</a></li> --}}
                        <li class="nav-item free-trial"><a class="nav-link isDown " onclick="freetrial()">Free
                                trial</a>
                        </li>
                        <li class="nav-item" onclick="location.href='/contactless'">
                            <a
                                class="nav-link isDown">#Contactless</a></li>
                        <li class="nav-item"><a class="nav-link isDown">Easy Tracking</a></li>
                        <li class="nav-item"><a class="nav-link isDown what-is-tp what-is-tp-mobile"
                                onclick="howTheParchiWorksModal()">What is TP</a></li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="row">
        </div>
    </div>


    <script>
        $(".icon i").click(function() {
            event.preventDefault();
            if ($(".navicon").hasClass("isDown")) {
                $(".ro5").stop().css({
                    'z-index': '1000',
                });
                $(".navicon").stop().css({
                    'height': '600px',
                    'width': '400px',
                    'border-radius': '0%',
                    'transition-duration': '0.6s'
                }, 200);
                $(".navicon").removeClass("isDown");
                $(".icon i").stop().css({
                    'transform': 'rotate(-180deg)',
                    'transition-duration': '0.3s',
                    'transition-delay': '0.5s'
                });
                $(".navicon1 ul li").stop().css({
                    'margin-left': '5px',
                    'transition-duration': '0.1s',
                    'transition-delay': '0.5s'
                });
            } else {
                $(".navicon1 ul li").stop().css({
                    'margin-left': '-200px',
                    'transition-duration': '0.1s'
                });
                $(".icon i").stop().css({
                    'transform': 'rotate(0deg)',
                    'transition-duration': '0.3s',
                });
                $(".navicon").stop().css({
                    'height': '50px',
                    'width': '50px',
                    'border-radius': '100%',
                    'transition-delay': '0.5s',
                    'transition-duration': '0.6s'
                }, 200);
                $(".navicon").addClass("isDown");
                $(".ro5").stop().css({
                    'z-index': '1',
                });

            }
        });
    </script>
    <script>
        $(".uli li").mouseover(function() {
            $(this).parents(".coll").css({
                "border": "7px solid rgba(0,0,0,.075)"
            });
        });
        $(".uli li").mouseout(function() {
            $(this).parents(".coll").css({
                "border": "0px solid white"
            });
        });
    </script>

    <script type="text/javascript">
        document.querySelector('.free-trial').addEventListener('click', () => {
            $('#exampleModalfreetrial').modal('show');
        });
        document.querySelector('.what-is-tp').addEventListener('click', () => {
            $('#exampleModalwhatistp').modal('show');
        });

        function senderAndReceiverModal() {
            $('#exampleModalwhatistp').modal('hide');
            $('#exampleModalsenderandreceiver').modal('show');
        }

        function howTheParchiWorksModal() {
            $('#exampleModalsenderandreceiver').modal('hide');
            $('#exampleModalwhatistp').modal('show');
        }

        function freetrial() {
            $('#exampleModalfreetrial').modal('show');
        }
        document.querySelector('.connect-us').addEventListener('click', () => {
            $('#exampleModalConnectUs').modal('show');
        });
        document.querySelector('.connect-us2').addEventListener('click', () => {
            $('#exampleModalConnectUs').modal('show');
        });
    </script>
    <style type="text/css">
        .heading {

            font-style: normal;
            font-weight: normal;
            font-size: 32px;
            line-height: 42px;
            /*margin-top: 50px;*/
            color: black;
        }

        .text-body {
            background: #E7E7E7;
            box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
            height: auto;
        }

        #page-content-wrapper {
            background: #F2F3F4 !important;
        }

        .paragraph-text {

            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 28px;
            letter-spacing: 0em;
            text-align: left;
            color: black;


        }

        @media only screen and (max-width: 600px) {
            .text-body {
                /*height: auto;*/
            }

            .heading {
                font-size: 24px;
            }
        }
    </style>

    <div class="modal pr-0 pl-0  fade" id="exampleModalfreetrial" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content mr-2 mr-md-0" style="background: transparent;border: none">
                <div class="modal-body" style>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 text-body py-4 px-4">
                                <h2 class="heading text-center text-justify">FREE TRIAL</h2>
                                <hr class="w-75" style="border: 2px solid #000000;transform: rotate(-0.11deg);">
                                <span class="text-justify paragraph-text">Signup to enjoy free package of TheParchi and
                                    upgrade to enjoy more benefits. Free Package can be availed for any one of the
                                    services out of all. Have any query connect us to start the service.<br>
                                    Check PRICING section to know more about plans and features.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal pr-0 pl-0  fade" id="exampleModalwhatistp" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true" style>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content mr-2 mr-md-0" style="background-color: transparent;">
                <div class="modal-body p-0 p-md-0 " style>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 text-body p-3  p-md-5 ">
                                <span class=" mt-n2 d-block d-md-none float-right text-white font-weight-bold"
                                    data-dismiss="modal">X</span>
                                <h2 class="heading text-center text-justify">HOW THEPARCHI WORKS </h2>
                                <hr class="w-75" style="border: 2px solid #000000;transform: rotate(-0.11deg);">
                                <span style="width:100%;">
                                    <iframe width="560" height="315"
                                        src="https://www.youtube.com/embed/NnGdUCsj4Kk" title="YouTube video player"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                                </span>
                                <p class="text-justify paragraph-text  ">TheParchi provides paperless, contactless and
                                    digital mechanism for proof and record keeping of goods delivered, received, sold
                                    and bought. This is a very user-friendly application which completely removes all
                                    the physical paper work of challan and invoices exchanged between two business
                                    entities or individuals. The features integrated in Theparchi helps small to large
                                    business to manage and operate all their processes of goods delivery & receipt to
                                    sale & purchase. It reduces the involvement of manpower as well as human error in
                                    calculation involved in record keeping. Multi-user functionality for single
                                    organisation gives complete control to business owners to keep check and track of
                                    all the different departments under same organisation.</p>
                                <p class="paragraph-text ">TheParchi is divided into two segments:<br>&#xb7; <span
                                        style="cursor: pointer;" class="paragraph-text"
                                        onclick="senderAndReceiverModal()">Sender & Receiver </span> <span
                                        onclick="senderAndReceiverModal()" style="float: right;cursor: pointer;"> <svg
                                            width="15" height="28" viewBox="0 0 26 35" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M24.9041 17.2229L0.590217 34.1578L0.199746 0.862811L24.9041 17.2229Z"
                                                fill="#F0AC49" />
                                        </svg> </span><br>&#xb7; <span class="paragraph-text" onclick>Seller &
                                        Buyer</span> </p>
                                <div class="float-right" style="cursor: pointer;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal pr-0 pl-0 fade" id="exampleModalsenderandreceiver" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true" style>
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" style=" ">
            <div class="modal-content mr-0 mr-md-0" style="background-color: transparent;">
                <div class="modal-body p-0 p-md-0 " style="overflow-x: hidden;">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 text-body p-4  p-md-5 ">
                                <span class="mt-n3 d-block d-md-none float-right text-white font-weight-bold"
                                    data-dismiss="modal">X</span>
                                <h2 class="heading text-center text-justify">SENDER & RECEIVER</h2>
                                <hr class="w-75" style="border: 2px solid #000000;transform: rotate(-0.11deg);">
                                <p class="sub-headings font-weight-bold">Sender: <br> <span
                                        class="text-justify paragraph-text ">Sender is the one who send certain goods
                                        to other business entity on returnable basis.That means goods sent by sender to
                                        receiver will be returned back to sender after certain time interval.</span>
                                </p>
                                <p class="sub-headings font-weight-bold">Receiver: <br> <span
                                        class="text-justify paragraph-text ">Receiver is the one who takes goods from
                                        other business entity and return them back after certain time interval.</span>
                                </p>
                                <p class="sub-headings font-weight-bold">An example: <br> <span
                                        class="text-justify paragraph-text ">A Hotel sends their linens for
                                        dry-cleaning to some other laundry unit. In this case Hotel is the “Sender” and
                                        Laundry Unit is the “Receiver”.</span> </p>
                                <p class="sub-headings font-weight-bold">How TheParchi is useful here for Hotel &
                                    Laundry Unit: <br> <span class="text-justify paragraph-text ">Sender “Hotel” sends
                                        say 100 towels for dry cleaning on paper challan as proof of
                                        delivery to Laundry Unit, after two days receiver “Laundry” units returns 80
                                        towels and 3rd day returns remaining 20 towels again through returned paper
                                        challan created by receiver “Laundry”. TheParchi will help both sender &
                                        receiver to convert paper challan transaction between them to digital mode and
                                        give them option of calculating total sent and received quantity with single
                                        click. No more paper work, hassle of keeping record books, lengthy calculations
                                        and chances of manual calculation error. Once challan through TheParchi is sent
                                        by Sender they will get instant notification via Mail, SMS
                                        and in application after acceptance by receiver.
                                    </span> </p>
                                <div class="float-left" style="cursor: pointer;" onclick="howTheParchiWorksModal()">
                                    <svg width="15" height="28" viewBox="0 0 25 35" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0.225733 17.2229L24.5397 34.1578L24.9301 0.862811L0.225733 17.2229Z"
                                            fill="#F0AC49" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="default-modal" tabindex="-1" aria-hidden="true" class="{{ $isModalOpen ? '' : 'hidden' }} overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content mr-2 mr-md-0" style="background-color: transparent;">
                <div class="modal-body p-0 p-md-0 " style>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 text-body  px-3 py-4  p-md-5 ">
                                <span class=" mt-n2 d-block d-md-none float-right text-white font-weight-bold"
                                    data-dismiss="modal">X</span>
                                <h2 class="heading text-center text-justify">CONNECT US </h2>
                                <hr  style="border: 2px solid #000000;transform: rotate(-0.11deg);">
                                <div class="row">
                                    <div class="col-8 offset-2">
                                        
                                            <input type="hidden" name="_token"
                                               >
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Phone Number</label>
                                                <input type="number" min="0" autocomplete="nope" wire:model.defer="userQueryData.phone"
                                                    class="form-control" 
                                                    aria-describedby="emailHelp" onkeyup="phoneValidation(this)"
                                                    name="phone">
                                                <small class="phoneError" style="display: none;"></small>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Email address</label>
                                                <input type="email" autocomplete="nope" class="form-control" wire:model.defer="userQueryData.email"
                                                     aria-describedby="emailHelp"
                                                      name="email">
                                                <small style="display: none;" class="emailError"></small>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleFormControlTextarea1">Query</label>
                                                <textarea style="resize: none;" wire:model.defer="userQueryData.comment" class="form-control" id="exampleFormControlTextarea1" rows="3"
                                                    name="message"></textarea>
                                            </div>
                                            <button disabled type="submit"    wire:click.prevent='userQuery'  class="btn submit-btn"
                                                style="background: black;color: white;">Submit</button>
                                               
                                        
                                    </div>
                                    <div class="col-2 d-flex justify-content-center align-items-center">
                                        <ul class="list-unstyled" style="line-height: 2.5;">
                                            <li><a class="text-dark" target="_blank"
                                                    href="https://twitter.com/theparchi_?s=11"> <i
                                                        class="fa fa-twitter fa-lg" aria-hidden="true"></i> </a></li>
                                            <li><a class="text-dark" target="_blank"
                                                    href="https://instagram.com/the.parchi?igshid=1nbwopc5lqu8r"> <i
                                                        class="fa fa-instagram fa-lg" aria-hidden="true"></i> </a>
                                            </li>
                                            <li><a class="text-dark" target="_blank"
                                                    href="https://www.facebook.com/TheParchiIndia/"> <i
                                                        class="fa fa-facebook fa-lg" aria-hidden="true"></i> </a></li>
                                            <li><a class="text-dark" target="_blank" href="tel:+919873232926"> <i
                                                        class="fa fa-phone fa-lg" aria-hidden="true"></i> </a></li>
                                            <li><a class="text-dark" target="_blank"
                                                    href="https://wa.me/message/6A2OOL4CK53KH1"> <i
                                                        class="fa fa-whatsapp fa-lg" aria-hidden="true"></i> </a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-center mt-4">
                                        <a href="{{ route('page', ['slug' => 'privacy-policy']) }}" class="small sender-and-receiver-text">Privacy Policy |</a>
                                        <a href="{{ route('page', ['slug' => 'terms-and-conditions']) }}"
                                            class="small sender-and-receiver-text a-active">Terms & Conditions |</a>
                                        <a href="{{ route('page', ['slug' => 'cancellation-policy']) }}"
                                            class="small sender-and-receiver-text">Cancellation Policy</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-center mt-4">
                                        <p class="small">Company Details: Eightleaf designs Pvt Ltd.
                                            A-194, ground floor, sector-83, Ph-2 Noida-201301, UP</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <button type="button" id="getme" style="display: none;" class="btn btn-primary" data-toggle="modal"
        data-target="#exampleModalCenter">
        Launch demo modal
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>

        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style>
                    <div class="modal-body text-center" style="background-color: #7449f0;border: 2px solid white;">
                        <h2 class="text-white font-weight-bold mt-4">SUBSCRIBE</h2>
                        <style type="text/css">
                            .form-control::-webkit-input-placeholder {
                                font-weight: unset;
                            }

                            .mememe::-webkit-input-placeholder {
                                /* Chrome/Opera/Safari */
                                font-weight: 800 !important;
                                color: #BCB4B4 !important;
                                font-size: 16px;

                            }
                        </style>
                        <form class="font-weight-bold" action="https://theparchi.com/subscriber" method="post">
                            <input type="hidden" name="_token" value="vHzzsXTUkWCjhQzytHEp8nMq5JosBSwqbIRrqsXb">
                            <div class="input-group mb-3 font-weight-bold">
                                <input type="text" name="email" class="form-control mt-3 mememe"
                                    placeholder="ENTER YOUR EMAIL ID" aria-label="Recipient's username"
                                    aria-describedby="basic-addon2"
                                    style="padding: 1.375rem .75rem!important;border-radius: 0px!important;">
                                <div class="input-group-append">
                                    <button class="btn mt-3 text-white font-weight-bold" type="submit"
                                        style="border-radius:0px!important;border-color: black;background-color: black;color: blanchedalmond!important;">SUBMIT</button>
                                </div>
                            </div>
                        </form>
                        <h2 class="text-white font-weight-bold mt-3" style="font-size: 16px;letter-spacing: 1.5px;">
                            Stay connected for
                            latest Update & exciting offers!!</p>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            function phoneValidation(e) {
                let phoneno = /^\d{10}$/;
                if (!e.value.match(phoneno)) {
                    document.querySelector('.phoneError').style.display = 'block';
                    document.querySelector('.phoneError').innerHTML = `Phone number must be of 10 digits only!`;
                    document.querySelector('.submit-btn').setAttribute('disabled', true);
                    return false;
                } else {
                    document.querySelector('.phoneError').style.display = 'none';
                    document.querySelector('.submit-btn').removeAttribute('disabled');
                }
            }

            function emailValidation(e) {
                let regex_email = '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}';
                if (!e.value.match(regex_email)) {
                    document.querySelector('.emailError').style.display = 'block';
                    document.querySelector('.emailError').innerHTML = `Invalid email address!`;
                    return false;
                } else {
                    document.querySelector('.emailError').style.display = 'none';
                    document.querySelector('.submit-btn').removeAttribute('disabled');
                }
            }
        </script>
</body>

</html>
