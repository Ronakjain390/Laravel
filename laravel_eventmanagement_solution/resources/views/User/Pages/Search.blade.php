@extends('User/Layouts/Layout/HomeLayout')
@section('UserContent')
<div class="container-fluid bg-light ">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 120px">
    </div>
</div>
<div class="performar_area ">
    <div class="container">
        <div class="row justify-content">
            <div class="col-sm-12">
                <div class="row">
                    @if(!is_null($artist))
                    @foreach($artist as $artists)
                    <div class="col-sm-4">
                        <div class="single_performer wow fadeInUp" data-wow-duration="1s" data-wow-delay=".3s">
                            <div data-tilt class="thumb">
                                <img src="{{asset('Assets/images/' .$artists->image)}}" width="300px" height="250px" alt="">
                                <a class="fa fa-heart wishlist" href="{{ route('FavoutiteArtist', ['id' => $artists->id]) }}"></a>
                            </div>
                            <div class="performer_heading">
                                <a href="{{route('ArtistShowDetail', ['id' => $artists->id])}}">
                                    <h4>{{ $artists->name }}</h4>
                                </a>
                                <span> {{ Str::length($artists->bio) > 50 ? substr($artists->artists, 0,50) . '...' : $artists->bio }} <a href="{{route('ArtistShowDetail' , ['id' => $artists->id])}}">View more</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div>
                        No search result found
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="performar_area ">
    <div class="container">
        <div class="row justify-content">
            <div class="col-sm-12">
                <div class="row">
                    @if(!is_null($eventDetail))
                    @foreach($eventDetail as $eventDetail1)
                    <div class="col-sm-4">
                        <div class="single_performer wow fadeInUp" data-wow-duration="1s" data-wow-delay=".3s">
                            <div data-tilt class="thumb">
                                <img src="{{asset('Assets/images/' .$eventDetail1->image)}}" width="300px" height="250px" alt="">
                            </div>
                            <div class="performer_heading">
                                <a href="{{route('EventShowDetail', ['id' => $eventDetail1->id])}}">
                                    <h4>{{$eventDetail1->title}}</h4>
                                </a>
                                <div class="about">
                                    {!!$eventDetail1->about!!}
                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                    {{ $eventDetail1->start_date }} <br>
                                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                                    {{ $eventDetail1->address }}
                                </div>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div>
                        No search result found
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .about p {
        color: black;
    }

    .wishlist {
        position: absolute;
        z-index: 1000;
        top: 10px;
        right: 20px;
        font-size: 25px;
    }
    .wishlist.red::before{
        color: red!important;
    }
</style>
@endsection('UserContent')