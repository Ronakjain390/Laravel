@extends('user/layouts/profileLayout/baseProfile')
@section('baseprofile')
<!-- banner -->
<!-- <div>
    @include('user/layouts/bannerSlider/bannerSlider')
</div> -->

@if (Session::has('success'))
<div class="alert alert-success">
    <p>{{ Session::get('success') }}</p>
</div>
@endif
<h1 class="heading-title">Wishlist</h1>
<div class="property-container">
    <div class="spacer blog">
        <div class="row">
            <div class="col-sm-12 ">
                @if($property == '')
                <div class="row">
                    There is no Data.
                </div>
                @else
                <div class="row">
                @foreach($property as $item)
                <!-- blog list -->
                <div class="col-md-6 col-sm-6">
                    <div class="card">
                        <div class="card-img-top "><a href="{{ route('userPropertyShow', ['id' => $item->id]) }}">
                                @if($item->image && File::exists(public_path('assets/images/property/'.$item->image)))
                                <img src="{{ asset('assets/images/property/'.$item->image) }}" style="height: 160px; width: 100%;" alt="img">
                                @else
                                <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" style="height: 160px; width: 100%;" alt="properties" />
                                @endif
                            </a>
                            <div class="wishlist">
                                <a href="{{ route('wishlistdelete',['id' => $item->id]) }}" class="btnWishlist">
                                    <i class='fa fa-heart'></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body ">
                            <h3 class="card-title"><a href="{{ route('userPropertyShow', ['id' => $item->id]) }}">{{ $item->title }}</a></h3>
                            @if($item->description)
                            <p class="card-text">{!! substr($item->description, 0, 150) !!} </p>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- blog list -->
                @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    /* .btnWishlist {
        background-color: #e4002b;
        border-radius: 20px;
    } */
    .btnWishlist {
    position: absolute;
    z-index: 1000;
    top: 5px;
    right: 10px;
    color: #000;
    font-size: 15px;
}
.btnWishlist:hover{
    color: red;
}
.card-body p{
    font-size: 14px;
}
</style>
@endsection('baseprofile')