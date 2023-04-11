@extends('user/layouts/parts/master')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" />

<!-- banner -->
<div>
    @include('user/layouts/bannerSlider/bannerSlider')
</div>
{{ app('request')->input('city') }}
<div class="container">
    <div class="properties-listing spacer">
        <div class="row destination">
            <div class="col-lg-3 col-sm-4 ">
                @include('user/layouts/search')
            </div>

            <div class="col-lg-9 col-sm-8 destination-details">
                @foreach ($city as $data)

                @if($dest[0] == $data->title)
                <div id="slider" class="carousel slide" data-bs-ride="carousel">

                    <div class="carousel-inner">
                        <?php foreach (json_decode($data->image) as $pictures) { ?>
                            <div class="carousel-item active">
                                <img src="{{ asset('assets/images/destinations/' . $pictures) }}" class="d-block w-100" alt="Wild Landscape" height="350px" width="100px">
                            </div>
                        <?php } ?>
                    </div>

                </div>
                <b>
                    <h5 class="text" style="text-align: center;">{!! $data->description !!}</h5>
                </b>
                @endif
                @endforeach
            </div>


        </div>

    </div>


    <div class="container">
        <p class="maps"><a href="#"> Map View of Properties</p></a>
        <div class="properties-listing spacer">
            @if($searchCity->count())
            @foreach($searchCity as $citydata)
            @if($citydata->city == $dest[0])
            <div class="col-lg-3 col-sm-6">
                <div class="properties">
                    <div class="image-holder">
                        @if($citydata->image && File::exists(public_path('assets/images/property/'.$citydata->image)) )
                        <img src="{{ asset('assets/images/property/'.$citydata->image) }}" class="img-responsive" alt="properties">
                        @else
                        <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" class="img-responsive" alt="properties">
                        @endif
                    </div>
                    <h4 class="property-h4"><a href="{{ route('propertydetail', ['id' => $citydata->id]) }}">{{ $citydata->title }}</a></h4>
                    <p class="price"> {{ $citydata->totalPrice }}

                    </p>
                    <div class="listing-detail"><span class="fa fa-bed" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $citydata->bedroom }} bedrooms"></span> <span class="fa fa-bath" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $citydata->bathroom }} Bathroom"> </span></div>
                    <a class="btn btn-primary" href="{{ route('propertydetail', ['id' => $citydata->id]) }}">View Details</a>
                </div>
            </div>
            @endif
            @endforeach
            @else
            <b>
                <p style="text-align: center;">No data found.</p>
            </b>
            @endif
            <!-- properties -->
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

    body {
        font-size: 13px !important;
    }
</style>
@endsection('content')