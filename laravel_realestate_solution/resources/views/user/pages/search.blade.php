@extends('user/layouts/parts/master')
@section('content')
<!-- banner -->
<div>
    @include('user/layouts/bannerSlider/bannerSlider')
</div>


<div class="container">
    <div class="properties-listing spacer">
        <div class="row">

            <p class="maps"><a href="#"> Map View of Properties</p></a>
        </div>
        <div class="row">
            <div class="col-lg-3 col-sm-4 ">
             @include('user/layouts/search')
            </div>

            <div class="col-lg-9 col-sm-8">
                <div class="row">
                    @if($products->count())
                    @foreach($products as $key => $item)
                    <div class="col-lg-4 col-sm-6">
                        <div class="properties">
                            <div class="image-holder">
                                @if($item->image && File::exists(public_path('assets/images/property/'.$item->image)) )
                                <img src="{{ asset('assets/images/property/'.$item->image) }}" class="img-responsive" alt="properties">
                                @else
                                <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" class="img-responsive" alt="properties" />
                                @endif
                            </div>
                            <h4 class="property-h4"><a href="{{ route('propertydetail', ['id' => $item->id]) }}"> {{ Str::length($item->title) > 20 ? substr($item->title, 0,22) : $item->title }}</a></h4>
                            <p class="price">{{ $currency->currency_symbol }} {{ $item->totalPrice }}

                            </p>
                            <div class="listing-detail"><span class="fa fa-bed" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bedroom }} bedrooms"></span> <span class="fa fa-bath" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bathroom }} Bathroom"> </span></div>
                            <a class="btn btn-primary" href="{{ route('propertydetail', ['id' => $item->id]) }}">View Details</a>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <b>
                        <h1 style="text-align: center;">No Data Found</h1>
                        <p style="text-align: center;"><a href="{{ route('home') }}" class="btn btn-sucess">Home</a></p>
                    </b>
                    @endif
                    <!-- properties -->
                </div>
            </div>
        </div>
    </div>
</div>
<script>
  const value = document.querySelector("#value")
  const input = document.querySelector("#price")
  value.textContent = input.value
  input.addEventListener("input", (event) => {
    value.textContent = event.target.value
  })
</script>
<style>
    .pagig {
        float: right;
    }

    .maps {
        float: right;
    }

    .priceRange {
        text-align: center;
    }
</style>
@endsection('content')