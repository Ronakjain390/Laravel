@extends('User/Layouts/Layout/HomeLayout')
@section('UserContent')

<!-- slider_area_start -->
<div class="container-fluid bg-light ">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 94px">
    </div>
</div>
<div class="swiper mySwiper">
    <div class="swiper-wrapper">
        @foreach($banner as $banners)
        <div class="swiper-slide">
            <img class="d-block " src="{{ asset('Assets/images/' . $banners->image ) }}" alt="First slide" />
        </div>
        @endforeach
    </div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</div>



<!-- slider_area_end -->

<!-- performar_area_start  -->
<div class="performar_area ">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section_title">
                    <h2 class="mb-2 category-heading">Artists</h2>
                </div>
            </div>
        </div>
        <div class="container artist-container px-0 text-center my-3">
            <div class="row mx-auto my-auto">
                <div id="recipeCarousel" class="carousel slide w-100" data-ride="carousel">
                    <div class="carousel-inner w-100" role="listbox">
                        <?php $i = 0; ?>
                        @foreach($artist as $artist1)
                        <div class="carousel-item {{ $i == 0 ? 'active' : '' }} ">
                            <div class="col-lg-4 col-sm-6">
                                <div class="card">
                                    <h5 class="card-title"></h5>
                                    <a class="card-img-top" href="{{route('ArtistShowDetail', ['id' => $artist1->id ])}}"> <img class="img-fluid" src="{{asset('Assets/images/' .$artist1->image)}}"></a>
                                    <div class="card-body">
                                        <a href="{{route('ArtistShowDetail', ['id' => $artist1->id ])}}">
                                            <h4 class="card-title">{{ucfirst($artist1->name)}}</h4>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $i = $i + 1; ?>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev w-auto" href="#recipeCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark border border-dark rounded-circle" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next w-auto" href="#recipeCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon bg-dark border border-dark rounded-circle" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- performar_area_end  -->

<!-- Slider -->

<div class="performar_area ">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section_title">
                    <h2 class="mb-2 category-heading">Events Category</h2>
                </div>
            </div>
        </div>
        <div class="container artist-container event-container px-0 text-center my-3">
            <div class="row mx-auto my-auto">

                <div id="recipeCarousel1" class="carousel slide w-100" data-ride="carousel">
                    <div class="carousel-inner w-100" role="listbox">

                        <?php $i = 0; ?>

                        @foreach($eventCategoryData as $eventCategoryData1)

                        <div class="carousel-item {{ $i == 0 ? 'active' : '' }} ">
                            <div class="col-lg-4 col-sm-6">
                                <div class="card">
                                    @if(!is_null($eventCategoryData1->image))
                                    <a class="card-img-top" href="{{ route('EventShow', ['event' => $eventCategoryData1->id]) }}"><img class="img-fluid" src="{{asset('Assets/images/' .$eventCategoryData1->image)}}"><br></a>
                                    @else
                                    <a class="card-img-top" href="{{ route('EventShow', ['event' => $eventCategoryData1->id]) }}"><img class="img-fluid" src="{{asset('Assets/DefaultImage/bannerDefault.jpg')}}"><br></a>
                                    @endif
                                    <div class="card-body">
                                        <a href="{{ route('EventShow', ['event' => $eventCategoryData1->id]) }}">
                                            <h4 class="card-title">{{ ucfirst($eventCategoryData1->category_name) }}</h4>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $i = $i + 1; ?>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev w-auto" href="#recipeCarousel1" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark border border-dark rounded-circle" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next w-auto" href="#recipeCarousel1" role="button" data-slide="next">
                        <span class="carousel-control-next-icon bg-dark border border-dark rounded-circle" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>

                </div>
            </div>
        </div>

    </div>
</div>

<div class="performar_area ">
    <div class="container">
        <div class="properties-listing spacer"> <a href="{{ route('EventShow') }}" class="pull-right viewall ">View Upcoming Events</a>
            <h2 class="category-heading d-inline-block">Upcoming Events</h2>
            <div class="row my-3">
                @foreach($upcomingEvents as $upcomingEvents1)
                <div class="col-lg-4 col-sm-6 mb-4">
                    <div class="card event">
                        <div class="card-img-top single-defination">
                            @if(!is_null($upcomingEvents1->image))
                            <a href="{{ route('EventShowDetail',$upcomingEvents1->id ) }}"> <img class="img-fluid" src="{{asset('Assets/images/' .$upcomingEvents1->image)}}" height="250px"><br></a>
                            @else
                            <a href="{{ route('EventShowDetail',$upcomingEvents1->id ) }}"> <img class="img-fluid" src="{{asset('Assets/DefaultImage/bannerDefault.jpg')}}" height="250px"><br></a>
                            @endif
                            <!-- <span class="favrcon">
                                <button>
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            </span> -->
                        </div>
                        <div class="card-body">
                            <a href="{{ route('EventShowDetail',$upcomingEvents1->id ) }}">
                                <h4 class="mb-15">{{ ucfirst($upcomingEvents1->title) }}</h4>
                            </a>
                            <div class="d-flex justify-content-between time-date">
                                <span>
                                    <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                    {{ date('d M Y', strtotime($upcomingEvents1->start_date)); }}
                                </span>
                                <span>
                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                    {{ date('h:i A', strtotime($upcomingEvents1->start_time)); }}
                                </span>
                            </div>
                            <div class="address">
                                <span>
                                    <i class="fa fa-map-marker mt-2" aria-hidden="true"></i>
                                    {{$upcomingEvents1->address}}
                                </span>
                            </div>
                            <div class="type">
                                <span>{{$upcomingEvents1->type}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="program_details_area detials_bg_1">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section_title text-center mb-80" data-wow-duration="1s" data-wow-delay=".3s">
                        <h1>Recent Events</h1>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="program_detail_wrap">
                        @foreach($eventDetail as $event0)
                        <div class="single_propram">
                            <div class="inner_wrap">
                                <div class="circle_img"></div>
                                <div class="porgram_top">
                                    <h4 class=" wow fadeInUp title" data-wow-duration="1s" data-wow-delay=".3s"> <a href=""></a>{{$event0->title}}</h4>
                                    <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">{{ date('d M Y', strtotime($event0->start_date)); }}</h4>
                                </div>
                                <div class="thumb wow fadeInUp" data-wow-duration="1s" data-wow-delay=".5s">
                                    <img src="{{asset('Assets/images/' .$event0->image)}}" alt="">
                                </div>
                                <span class="wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".6s"> <a href=""></a> {{$event0->venue}}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- brand_area_start  -->
<!-- <div class="brand_area ">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section_title text-center mb-80">
                    <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s">Sponsor Logos</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="brand_wrap">
                    <div class="brand_active owl-carousel">
                        <div class="single_brand text-center">
                            <img src="img/brand/1.png" alt="">
                        </div>
                        <div class="single_brand text-center">
                            <img src="img/brand/2.png" alt="">
                        </div>
                        <div class="single_brand text-center">
                            <img src="img/brand/3.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row align-items-center justify-content-center">
        <div class="col-xl-12">
            <div class="slider_text text-center">
                <div class="shape_1 wow fadeInUp" data-wow-duration="1s" data-wow-delay=".2s">
                    <img src="img/shape/shape_1.svg" alt="">
                </div>
                <div class="shape_2 wow fadeInDown" data-wow-duration="1s" data-wow-delay=".2s">
                    <img src="img/shape/shape_2.svg" alt="">
                </div>
                <span class="wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".3s">12 Feb, 2020</span>
                <h3 class="wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".4s">Concert 2020</h3>
                <p class="wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".5s">Green Avenue, New York</p>
            </div>
        </div>
    </div>
</div> -->
<!-- brand_area_end  -->
@endsection('UserContent')
@push('js')

<script>
    $('#recipeCarousel').carousel({

        interval: 5000,
        slidesToShow: 3,

    })

    $('.carousel .carousel-item').each(function() {
        var minPerSlide = 3;
        var next = $(this).next();
        if (!next.length) {
            next = $(this).siblings(':first');
        }
        next.children(':first-child').clone().appendTo($(this));

        for (var i = 0; i < minPerSlide; i++) {
            next = next.next();
            if (!next.length) {
                next = $(this).siblings(':first');
            }

            next.children(':first-child').clone().appendTo($(this));
        }
    });

    $('#recipeCarousel1').carousel({

        interval: 5000,
        slidesToShow: 3,

    })

    $('.carousel .carousel-item').each(function() {
        var minPerSlide = 3;
        var next = $(this).next();
        if (!next.length) {
            next = $(this).siblings(':first');
        }
        next.children(':first-child').clone().appendTo($(this));

        for (var i = 0; i < minPerSlide; i++) {
            next = next.next();
            if (!next.length) {
                next = $(this).siblings(':first');
            }

            next.children(':first-child').clone().appendTo($(this));
        }
    });
</script>
@endpush
@push('css')
<style>
    @media (max-width: 768px) {
        .carousel-inner .carousel-item>div {
            display: none;
        }

        .carousel-inner .carousel-item>div:first-child {
            display: block;
        }
    }

    .carousel-inner .carousel-item.active,
    .carousel-inner .carousel-item-next,
    .carousel-inner .carousel-item-prev {
        display: flex;
    }

    /* display 3 */
    @media (min-width: 768px) {

        .carousel-inner .carousel-item-right.active,
        .carousel-inner .carousel-item-next {
            transform: translateX(33.333%);
        }

        .carousel-inner .carousel-item-left.active,
        .carousel-inner .carousel-item-prev {
            transform: translateX(-33.333%);
        }
    }

    .carousel-inner .carousel-item-right,
    .carousel-inner .carousel-item-left {
        transform: translateX(0);
    }



    .card.card-body img {
        height: 300px;
    }

    .card.card-body.upcoming {
        height: 450px;
    }


    .swiper {
        width: 100%;
        height: 500px;
    }

    .swiper-slide {
        text-align: center;
        font-size: 18px;
        background: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .swiper-slide img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .swiper-button-next:after,
    .swiper-rtl .swiper-button-prev:after,
    .swiper-button-prev:after,
    .swiper-rtl .swiper-button-next:after {
        color: white;
        font-weight: bolder;
    }
</style>

@endpush