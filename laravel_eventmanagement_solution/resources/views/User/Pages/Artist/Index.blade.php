@extends('User/Layouts/Layout/HomeLayout')
@section('UserContent')
<div class="container-fluid bg-light ">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 94px">
    </div>
</div>
<div class="category-browse--header">
    <div class="category-browse--header-">
        <div class="container" style="padding-top: 1rem;padding-bottom: 0.2px;">
            <div class="category-browse--header-text">
                <div class="category-browse--header-text__wrapper">
                    <h1 class="category-browse__header--content">Artists <div class="eds-text-bl" style="color:#FFF58C;padding-top:8px"></div>
                    </h1>
                    <p></p>
                </div>
            </div>
            <!-- <aside class="category-browse--header-image category-browse--header-image--square"><img fetchpriority="high" class="full-width-img" loading="eager" src="" alt="[object Object]"></aside> -->
        </div>
    </div>
</div>
<div class="performar_area ">
    <div class="container">
        <div class="row justify-content">
            <div class="col-sm-12">
                <div class="row">
                    @foreach($artist as $art)
                    <div class="col-lg-4 col-sm-6 mb-5">
                        <div class="single_performer wow fadeInUp" data-wow-duration="1s" data-wow-delay=".3s">
                            <div class="thumb">
                                <a href="{{route('ArtistShowDetail' , ['id' => $art->id])}}"><img src="{{asset('Assets/images/' .$art->image)}}" width="300px" height="250px" alt=""></a>
                                @if(Auth::user())
                                <?php $counts = getfavcount($art->id); ?>
                                @if(!$counts->isEmpty())
                                <a href="{{ route('FavoutiteArtist', ['id' => $art->id]) }}"><i class="fa fa-heart wishlist red"></i></a>
                                @else
                                <a href="{{ route('FavoutiteArtist', ['id' => $art->id]) }}"><i class="fa fa-heart wishlist"></i></a>
                                @endif
                                @else
                                <a href="{{ route('FavoutiteArtist', ['id' => $art->id]) }}"><i class="fa fa-heart wishlist"></i></a>
                                @endif
                            </div>
                            <div class="performer_heading">
                                <a href="{{route('ArtistShowDetail', ['id' => $art->id])}}">
                                    <h4>{{ ucfirst($art->name)}}</h4>
                                </a>
                                <p>
                                    @if(Str::length($art->bio) > 40)
                                    {!!str::substr($art->bio , 0 , 90 )!!}
                                    <a href="{{route('ArtistShowDetail' , ['id' => $art->id])}}" onclick='togglemodel(<?php echo $art->id  ?>)'>View More</a>
                                    @else
                                    {!!$art->bio!!}
                                    @endif
                                </p>
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('UserContent')