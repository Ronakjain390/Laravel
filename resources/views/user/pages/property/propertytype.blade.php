@extends('user/layouts/parts/master')
@section('content')
<!-- banner -->
<div>
    @include('user/layouts/bannerSlider/bannerSlider')
</div>
<!-- banner -->


<div class="container">
  <div class="properties-listing spacer">
    <div class="row">
      <div class="col-lg-3 col-sm-4 ">
      @include('user/layouts/search')

      </div>

      <div class="col-lg-9 col-sm-8">
        <div class="row">
          <!-- properties -->
          @foreach($propertytype as $item)
          <div class="col-lg-4 col-sm-6">
            <div class="properties">
              <div class="image-holder">
                @if($item->image == '')
                <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" class="img-responsive" alt="properties" />
                @else
                <img src="{{ asset('assets/images/property/'.$item->image) }}" class="img-responsive" alt="properties">
                <!-- <div class="status sold">Sold</div> -->
                @endif
              </div>
              <h4 class="property-h4"><a href="{{ route('propertydetail', ['id' => $item->id]) }}">{{ $item->title }}</a></h4>
              <p class="price">{{ $currency[0]->currency_symbol }} {{ $item->totalPrice }}

              </p>
              <div class="listing-detail"><span class="fa fa-bed" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bedroom }} bedrooms"></span> <span class="fa fa-bath" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bathroom }} Bathroom"> </span></div>
              <a class="btn btn-primary" href="{{ route('propertydetail', ['id' => $item->id]) }}">View Details</a>
            </div>
          </div>
          @endforeach

          <!-- properties -->
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .pagig {
    float: right;
  }
</style>
@endsection('content')