@extends('user/layouts/parts/master')

@section('content')
<!-- banner -->
<div>
<picture class="banner_image">
    <img src="https://realestate.addwebprojects.com/assets/images/banner/banner.jpg" alt="">
</picture>

<style>

    .banner_image img{
        height: 320px;
    }
    
</style>
</div>
<!-- banner -->

<div class="container">
    <br>
    <div class="col-lg-8 col-sm-4">
        @foreach($newsdata as $news)
        <h2>{{ $news->title }}</h2>
        @if($news->image && File::exists(public_path('assets/images/news/'.$news->image)) )
        <img src="{{ asset('assets/images/news/'.$news->image) }}" class="thumbnail img-responsive newsimag" alt="blog title">
        @else
        <img src="{{ asset('assets/images/news/default-news.png') }}" class="thumbnail img-responsive newsimag" alt="properties" />
        @endif
        <p>{!! $news->description !!}</p>
        @endforeach
        <br><br>
    </div>
</div>
<style>
    .newsimag {
        margin-top: 20px;
        width: 100%;
        height: 300px;
        object-fit: cover;
    }
</style>
@endsection('content')