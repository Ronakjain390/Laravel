@extends('user/layouts/parts/master')

@section('content')
<!-- banner -->
<div>
    @include('user/layouts/bannerSlider/bannerSlider')
</div>
<div class="news-container container">
    <div class="heading">
        <h1 class="heading-title">Latest Property News</h1>
    </div>
    <div class="spacer blog">
        <div class="row">
            <div class="col-lg-9 col-sm-12 ">
                <div class="row news">
                    @foreach($data as $item)
                    <!-- blog list -->
                    <div class="col-md-4 col-sm-6 news-blog">
                        <div class="card">
                            <div class="card-img-top"><a href="{{ route('newsdetail', ['id' => $item->id]) }}" >
                                    @if($item->image && File::exists(public_path('assets/images/news/'.$item->image)) )
                                    <img src="{{ url('/assets/images/news/'.$item->image) }}"  alt="img">
                                    @else
                                    <img src="{{ asset('assets/images/news/default-news.png') }}" alt="img">
                                    @endif
                                </a>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title news-title"><a href="{{ route('newsdetail', ['id' => $item->id]) }}">{{ $item->title }}</a></h3>
                                <div class="info">Posted on: {{ $item->created_at->format(config('app.dateFormate')) }}</div>
                                @if($item->description)
                                <p class="card-text">{!! substr($item->description, 0, 150) !!} </p>
                                @endif
                                <a href="{{ route('newsdetail', ['id' => $item->id]) }}" class="card-link btn read-more-btn btn-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                    <!-- blog list -->
                    @endforeach
                </div>

            </div>
            <div class="col-lg-3 visible-lg">
                <div class="tabbable">
                    <ul class="nav nav-tabs">
                        <li class=""><a href="" data-toggle="tab">Recent Post</a></li>
                    </ul>
                    @foreach($hotNews as $hotNews)

                    <div class="tab-content">
                        <div>
                            <ul class="list-unstyled">
                                <li>
                                    <h5><a href="{{ route('newsdetail', ['id' => $hotNews->id]) }}">{{ $hotNews->title }}</a></h5>
                                    <div class="info">Posted on: {{ $hotNews->created_at->format(config('app.dateFormate')) }}</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection('content')