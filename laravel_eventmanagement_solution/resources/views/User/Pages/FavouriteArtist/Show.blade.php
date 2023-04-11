@extends('User/Layouts/Layout/HomeLayout')
@section('UserContent')
<div class="container-fluid bg-light ">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 94px">
    </div>
</div>
<div class="category-browse--header">
    <div class="category-browse--header-">
        <div class="container mb-5" style="padding:1rem";>
            <div class="category-browse--header-text">
                <div class="category-browse--header-text__wrapper">
                    <h1 class="category-browse__header--content">Favourite Artists <div class="eds-text-bl" style="color:#FFF58C;padding-top:8px"></div>
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-10">
    <div class="row">

        @foreach($artist as $art)
        <div class="col-sm-3 mb-sm-0">
            <div class="card">
                <div class="card-body">
                    <img src="{{asset('Assets/images/' .$art->image)}}" alt="">
                </div>
            </div>
            <a href="{{  route('ArtistShowDetail' , $art->id) }}">
                <h2 class="font-weight- text-uppercase mb-3">{{$art->name}}</h2>
            </a>
            <span>{{ Str::length($art->bio) > 50 ? substr($art->bio, 0,50) . '...' : $art->bio }}</span>
            <div class="col-lg-6 py-5 text-left">
                <h3 class="font-weight-semi-bold mb-4">{{$art->title}}</h3>
            </div>
        </div>
        @endforeach

    </div>
</div>
@endsection('UserContent')

<style>
    .card .card-body {
        height: 300px;
        width: 300px;
    }

    .card .card-body img {
        height: 280px !important;
        width: 250px !important;
    }
</style>