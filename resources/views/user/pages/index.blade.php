@extends('user/layouts/parts/master')
@section('content')
<div class="container-fluid">
  @if ($message = Session::get('success'))
  <div class="alert alert-success">
    <p>{{ $message }}</p>
  </div>
  @endif
  @if ($message = Session::get('error'))
  <div class="alert alert-danger">
    <p>{{ $message }}</p>
  </div>
  @endif
</div>

<picture class="banner_image">
  <img src="{{asset('assets/images/banner/banner.jpg') }}" alt="">
  <div class="container-fluid search-bar" id="search_bar">
    <div class="frontPanel">
      <div class="tab">
        <div class="dropdown">
          <button data-bs-toggle="dropdown" class="dropbtn">--Select Tab--</button>
          <div class="drop-tabs">
            <button class="tablinks active" onclick="openCity(event, 'buy')">Buy</button>
            <button class="tablinks" onclick="openCity(event, 'rent')">Rent</button>
            <button class="tablinks" onclick="openCity(event, 'pg')">PG</button>
            <button class="tablinks" onclick="openCity(event, 'commercial')">Commercial</button>
          </div>
          <div class="dropdown-menu">
            <button class="tablinks active" onclick="openCity(event, 'buy')">Buy</button>
            <button class="tablinks" onclick="openCity(event, 'rent')">Rent</button>
            <button class="tablinks" onclick="openCity(event, 'pg')">PG</button>
            <button class="tablinks" onclick="openCity(event, 'commercial')">Commercial</button>
          </div>
        </div>
      </div>
      <div id="buy" class="tabcontent" style="display: block;">
        <div class="row">
          <form method="GET" action="{{ route('search-buy') }}">
            <div class="col-sm-8">
              <div style="display: flex; align-items: center;" class="form-control">
                <div>
                  <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input style="width: 100%; height: 100%; border: 0px;" type="search" name="product_search" id="address" placeholder="Search by Name,Address and City" value="{{ old('product_search') }}">
              </div>
            </div>
            <div class="col-sm-2">
              <button class="btn btn-primary">Search</button>
            </div>
          </form>
          <button class="btn SearchBtn" onclick="togglemodel()"><i class="fa-sharp fa-solid fa-filter"></i></button>
        </div>
      </div>
      <div id="rent" class="tabcontent">
        <div class="row">
          <form method="GET" action="{{ route('search-rent') }}">
            <div class="col-sm-8">
              <div style="display: flex; align-items: center;" class="form-control">
                <div>
                  <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input style="width: 100%; height: 100%; border: 0px;" type="search" name="product_search" id="address" placeholder="Search by Name,Address and City" value="{{ old('product_search') }}">
              </div>
            </div>
            <div class="col-sm-2">
              <button class="btn btn-primary">Search</button>
            </div>
          </form>
          <button class="btn SearchBtn" onclick="togglemodel()"><i class="fa-sharp fa-solid fa-filter"></i></button>
        </div>
      </div>
      <div id="pg" class="tabcontent">
        <div class="row">
          <form method="GET" action="{{ route('search-buy') }}">
            <div class="col-sm-8">
              <div style="display: flex; align-items: center;" class="form-control">
                <div>
                  <i class="fa-solid fa-magnifying-glass">&nbsp;</i>
                </div>
                <input style="width: 100%; height: 100%; border: 0px;" type="search" name="product_search" id="address" placeholder="Search by Name,Address and City" value="{{ old('product_search') }}">
              </div>
            </div>
            <div class="col-sm-2">
              <button class="btn btn-primary">Search</button>
            </div>
          </form>
          <button class="btn SearchBtn" onclick="togglemodel()"><i class="fa-sharp fa-solid fa-filter"></i></button>
        </div>
      </div>
      <div id="commercial" class="tabcontent">
        <div class="row">
          <form method="GET" action="{{ route('search-buy') }}">
            <div class="col-sm-8">
              <div style="display: flex; align-items: center;" class="form-control">
                <div>
                  <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input style="width: 100%; height: 100%; border: 0px;" type="search" name="product_search" id="address" placeholder="Search by Name,Address and City" value="{{ old('product_search') }}">
              </div>
            </div>
            <div class="col-sm-2">
              <button class="btn btn-primary">Search</button>
            </div>
          </form>
          <button class="btn SearchBtn" onclick="togglemodel()"><i class="fa-sharp fa-solid fa-filter"></i></button>
        </div>
      </div>
    </div>
    <form method="get" action="{{ route('search-perameter') }}">
      <div id="myModel" class="modal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><b>Filters</b></h5>
            </div>
            <div class="modal-body">
              <div class="row filterRow">
                <div class="col-sm-12 col-md-12">
                  <label for="ptype">Property Type</label>
                  <select class="form-control " name="ptype" id="ptype">
                    <option>Select Property Type</option>
                    @if(isset($ptype))
                    @if(count($ptype)>0)
                    @foreach($ptype as $protype)
                    <option value="{{ $protype->id }}">{{$protype->name}}</option>
                    @endforeach
                    @endif
                    @endif
                  </select>
                </div>
              </div>
              <div class="row filterRow">
                <div class="col-md-12 col-sm-12">
                  <header><b>Price</b></header>
                </div>
                <div class="col-md-12 priceRange">
                  @if(isset($currency->currency_symbol)==1)
                  {{$currency->currency_symbol}} <span id="value"></span>
                  @else
                  ₹<span id="value"></span>
                  @endif
                </div>
                <div class="col-md-1 col-sm-1">
                  <b>0</b>
                </div>
                <div class="col-md-9 col-sm-9">
                  <input type="range" name="price" id="price" min="0" max="10000000" step=10>
                </div>
                <div class="col-md-2 col-sm-2">
                  <b>1 cr+</b>
                </div>

              </div>
              <div class="row filterRow">
                <div class="col-md-12 col-sm-12">
                  <header><b>Bedroom</b></header>
                </div>
                <div class="col-md-6 col-sm-6">
                  <label for="agent">Minimum</label>
                  <select class="form-control " name="minbedroom" id="minbedroom">
                    <option value=" ">Any</option>
                    <option value="2">2</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                  </select>
                </div>
                <div class="col-md-6 col-sm-6">
                  <label for="agent">Maximum</label>
                  <select class="form-control" name="maxbedroom" id="maxbedroom">
                    <option value=" ">Any</option>
                    <option value="2">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                  </select>
                </div>
              </div>
              <div class="row filterRow">
                <div class="col-md-6 col-sm-6">
                  <label for="agent">No. of Bathroom</label>
                  <select class="form-control " name="bathroom" id="bathroom">
                    <option value="1">Any</option>
                    <option value="2">2</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                  </select>
                </div>
              </div>
              <div class="row filterRow">
                <div class="col-sm-12 col-md-12">
                  <label for="amenities">Additional Facilities</label>
                  <select class="form-control " name="amenities" id="amenities">
                    <option>--Select Ameneties--</option>
                    @if(isset($ameneties))
                    @if(count($ameneties)>0)
                    @foreach($ameneties as $item)
                    <option value="{{$item->id}}">{{$item->amenities}}</option>
                    @endforeach
                    @endif
                    @endif
                  </select>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" id="deleteButton" onclick="closeModel()" class="btn btn-danger">Close</button>
              <button type="submit" id="savebutton" onclick="closeModel()" class="btn btn-primary">Search</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</picture>

<div class="container">
  @if(isset($property))
  @if(count($property)>0)
  <div class="properties-listing spacer  {{ count($property)>0 ? 'DisplayData' : 'NoData' }}"> <a href="{{ url('/propertylisting') }}" class="pull-right viewall">View All Listing</a>
    <h2>Ready To Shift Properties</h2>
    <!-- {{$location}} -->
    <br>
    <div id="owl-example" class="owl-carousel">
      @foreach($property as $item)
      <div class="properties">
        <div class="image-holder">
          @if($item->image && File::exists(public_path('assets/images/property/'.$item->image)) )
          <img src="{{ asset('assets/images/property/'.$item->image) }}" class="img-responsive" alt="properties" /><a class="fa fa-heart wishlist" href="{{ route('wishlist', ['id' => $item->id]) }}"></a>
          @else
          <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" class="img-responsive" alt="properties" /><a class="fa fa-heart wishlist" href="{{ route('wishlist', ['id' => $item->id]) }}"></a>
          <!-- <div class="status sold">{{ $item->reason }}</div> -->
          @endif
        </div>
        <h4 class="property-h4"><a href="{{ route('propertydetail', ['id' => $item->id]) }}">{{ Str::length($item->title) > 20 ? substr($item->title, 0,22) . '...' : $item->title }}</a>
        </h4>

        <p class="price">{{ $currency->currency_symbol }} {{ $item->totalPrice }}
        </p>
        <div class="listing-detail"><span class="fa fa-bed" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bedroom }} bedrooms"></span> <span class="fa fa-bath" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bathroom }} Bathroom"> </span></div>

        <a class="btn btn-primary" href="{{ route('propertydetail', ['id' => $item->id]) }}">View Details</a>
      </div>
      @endforeach
    </div>
    @endif
    @endif
  </div>
</div>

<div class="container">
  @if(isset($property2))
  @if(count($property2)>0)
  <div class="properties-listing spacer"> <a href="{{ url('/propertylisting') }}" class="pull-right viewall">View All Listing</a>
    <h2>Under Development Projects</h2>
    <br>
    <div id="owl-example">
      <div class="row">
        @foreach($property2 as $item)
        <div class="col-md-3 col-sm-6">
          <div class="properties">
            <div class="image-holder">
              @if($item->image && File::exists(public_path('assets/images/property/'.$item->image)) )
              <img src="{{ asset('assets/images/property/'.$item->image) }}" class="img-responsive" alt="properties" /><a class="fa fa-heart wishlist" href="{{ route('wishlist', ['id' => $item->id]) }}"></a>
              @else
              <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" class="img-responsive" alt="properties" /><a class="fa fa-heart wishlist" href="{{ route('wishlist', ['id' => $item->id]) }}"></a>
              <!-- <div class="status sold">{{ $item->reason }}</div> -->
              @endif
            </div>
            <h4 class="property-h4"><a href="{{ route('propertydetail', ['id' => $item->id]) }}">{{ Str::length($item->title) > 20 ? substr($item->title, 0,22) . '...' : $item->title }}</a>
            </h4>

            <p class="price">{{ $currency->currency_symbol }} {{ $item->totalPrice }}
            </p>
            <div class="listing-detail"><span class="fa fa-bed" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bedroom }} bedrooms"></span> <span class="fa fa-bath" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bathroom }} Bathroom"> </span></div>

            <a class="btn btn-primary" href="{{ route('propertydetail', ['id' => $item->id]) }}">View Details</a>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif
  @endif

  @if(isset($property3))
  @if(count($property3)>0)
  <div class="properties-listing spacer"> <a href="{{ url('/propertylisting') }}" class="pull-right viewall">View All Listing</a>
    <h2>Commercial</h2>
    <br>
    <div id="owl-example">
      <div class="row">
        @foreach($property3 as $item)
        <div class="col-md-3 col-sm-6">
          <div class="properties">
            <div class="image-holder">
              @if($item->image && File::exists(public_path('assets/images/property/'.$item->image)) )
              <img src="{{ asset('assets/images/property/'.$item->image) }}" class="img-responsive" alt="properties" /><a class="fa fa-heart wishlist" href="{{ route('wishlist', ['id' => $item->id]) }}"></a>
              @else
              <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" class="img-responsive" alt="properties" /><a class="fa fa-heart wishlist" href="{{ route('wishlist', ['id' => $item->id]) }}"></a>
              <!-- <div class="status sold">{{ $item->reason }}</div> -->
              @endif
            </div>
            <h4 class="property-h4"><a href="{{ route('propertydetail', ['id' => $item->id]) }}">{{ Str::length($item->title) > 20 ? substr($item->title, 0,15) . '...' : $item->title }}</a>
            </h4>

            <p class="price">{{ $currency->currency_symbol }} {{ $item->totalPrice }}
            </p>
            <div class="listing-detail"><span class="fa fa-bed" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bedroom }} bedrooms"></span> <span class="fa fa-bath" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bathroom }} Bathroom"> </span></div>

            <a class="btn btn-primary" href="{{ route('propertydetail', ['id' => $item->id]) }}">View Details</a>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif
  @endif
</div>

@if(isset($city))
@if(count($city)>0)
<div class="container">
  <div id="carousel-example" class="carousel slide hidden-xs" data-ride="carousel">
    <div class="carousel-inner">
      <h2>Popular Destinations</h2>
      <br>
      <div class="item active">
        <div class="row">
          @foreach($city as $city)
          <div class="col-md-3 col-sm-4">
            <div class="col-item">
              <div class="photo">
                <img src="{{ asset('assets/images/destinations/'.$city->image) }}" class="img-responsive" alt="alt" />
              </div>
              <div class="info">
                <div class="separator">
                  <p>
                  <form method="get" action="{{ route('searchCity') }}">
                    <input type="text" name="city" id="city_search" value="{{ $city->title }}" hidden />
                    <a href="{{route('searchCity',['id'=>$city->id] )}}" class="btn btn primary">
                      <h3>{{ $city->title }}</h3>
                    </a>
                  </form>
                  </p>
                </div>
                <div class="clearfix">
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@endif

<!-- <div class="container-fluid SliderBootom">
  <div class="col-md-3 col-sm-4">
    <div class="row">
      <button onclick="openCity(event, 'buy')">Buy</button>
    </div>

    <div class="row">
      <button onclick="openCity(event, 'rent')">Rent</button>
    </div>

    <div class="row">
      <button onclick="openCity(event, 'pg')">PG</button>
    </div>

  </div>
  <div class="col-md-9 col-sm-8">

    <div id="buy" class="tabcontent" style="display: block;">
        <div class="row">
          <div class="col-md-3">
            <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/15/0d/e3/4b/img-20180921-184349-largejpg.jpg?w=1000&h=-1&s=1" alt="">
          </div>
        </div>
    </div>
    <div id="rent" class="tabcontent">
      <div class="row">
        <div id="buy" class="tabcontent">
          <div class="row">
            <div class="col-md-3">
              <img src="https://imgcld.yatra.com/ytimages/image/upload/t_yt_blog_c_fill_q_auto:good_f_auto_w_800_h_500/v1495524602/Hawa_Mahal_is_a_beautiful_palace_in_Jaipur.jpg" alt="">
              <p>hello</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="pg" class="tabcontent">
      <div class="row">
        <form method="GET" action="{{ route('search-buy') }}">
          <div class="col-md-8">
            <div style="display: flex; align-items: center;" class="form-control">
              <div>
                <i class="fa-solid fa-magnifying-glass">&nbsp;</i>
              </div>
              <input style="width: 100%; height: 100%; border: 0px;" type="search" name="product_search" id="address" placeholder="Search by Name,Address and City" value="{{ old('product_search') }}">
            </div>
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary">Search</button>
          </div>
        </form>
        <button class="btn SearchBtn" onclick="togglemodel()"><i class="fa-sharp fa-solid fa-filter"></i></button>
      </div>
    </div>
  </div>
</div> -->

<div class="container-fluid SliderBootom">
  <div class="row">
    <div class="col-md-3 col-sm-4 padding-0">
      <div class="tabs">
        <button type="button" class='btn btn-sell active'>BUY</button>
        <button type="button" class='btn btn-sell'>RENT</button>
        <button type="button" class='btn btn-sell'>PG</button>
      </div>
    </div>
    <div class="col-md-9 col-sm-8 padding-0">
      <div class="tabs-content">
        <div id="buy" class="tabcontent active swiper mySwiper">

          <div class="buy-carousel swiper-wrapper">
            @if(isset($propertyBuytab))
            @foreach($propertyBuytab as $buy)
            <div class="swiper-slide">
              <div>
                @if($buy->image && File::exists(public_path('assets/images/property/'.$buy->image)) )
                <img src="{{ asset('assets/images/property/'.$buy->image) }}" alt="">
                @else
                <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" alt="properties" />
                @endif
              </div>
              <h4><a href="{{ route('propertydetail', ['id' => $buy->id]) }}">{{ Str::length($buy->title) > 20 ? substr($buy->title, 0,15) . '...' : $buy->title }}</a>
            </div>
            @endforeach
            @endif
          </div>
        </div>
        <div id="rent" class="tabcontent swiper mySwiper">
          <div class="buy-carousel  swiper-wrapper">
            @if(isset($propertyRenttab))
            @foreach($propertyRenttab as $rent)
            <div class="swiper-slide">
              <div>
                @if($rent->image && File::exists(public_path('assets/images/property/'.$rent->image)) )
                <img src="{{ asset('assets/images/property/'.$rent->image) }}" alt="">
                @else
                <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" alt="properties" />
                @endif
              </div>
              <h4><a href="{{ route('propertydetail', ['id' => $rent->id]) }}">{{ Str::length($rent->title) > 20 ? substr($rent->title, 0,15) . '...' : $rent->title }}</a>
            </div>
            @endforeach
            @endif
          </div>
        </div>
        <div id="pg" class="tabcontent swiper mySwiper">
          <div class="buy-carousel  swiper-wrapper">
            @if(isset($propertyPGtab))
            @foreach($propertyPGtab as $pg)
            <div class="swiper-slide">
              <div>
                @if($pg->image && File::exists(public_path('assets/images/property/'.$pg->image)) )
                <img src="{{ asset('assets/images/property/'.$pg->image) }}" alt="">
                @else
                <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" alt="properties" />
                @endif
              </div>
              <h4><a href="{{ route('propertydetail', ['id' => $pg->id]) }}">{{ Str::length($pg->title) > 20 ? substr($pg->title, 0,15) . '...' : $pg->title }}</a>
            </div>
            @endforeach
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<div class="container">
  @if(isset($propertyRent))
  @if(count($propertyRent)>0)
  <div class="properties-listing spacer"> <a href="{{ url('/propertylisting') }}" class="pull-right viewall">View All Listing</a>
    <h2>Rent </h2>
    <br>
    <div id="owl-example">
      <div class="row">
        @foreach($propertyRent as $item)
        <div class="col-md-3 col-sm-6">
          <div class="properties">
            <div class="image-holder">
              @if($item->image && File::exists(public_path('assets/images/property/'.$item->image)) )
              <img src="{{ asset('assets/images/property/'.$item->image) }}" class="img-responsive" alt="properties" /><a class="fa fa-heart wishlist" href="{{ route('wishlist', ['id' => $item->id]) }}"></a>
              @else
              <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" class="img-responsive" alt="properties" /><a class="fa fa-heart wishlist" href="{{ route('wishlist', ['id' => $item->id]) }}"></a>
              <!-- <div class="status sold">{{ $item->reason }}</div> -->
              @endif
            </div>
            <h4 class="property-h4"><a href="{{ route('propertydetail', ['id' => $item->id]) }}">{{ Str::length($item->title) > 20 ? substr($item->title, 0,22) . '...' : $item->title }}</a>
            </h4>

            <p class="price">{{ $currency->currency_symbol }} {{ $item->totalPrice }}
            </p>
            <div class="listing-detail"><span class="fa fa-bed" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bedroom }} bedrooms"></span> <span class="fa fa-bath" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bathroom }} Bathroom"> </span></div>

            <a class="btn btn-primary" href="{{ route('propertydetail', ['id' => $item->id]) }}">View Details</a>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif
  @endif
</div>

<div class="container">
  @if(isset($propertyBuy))
  @if(count($propertyBuy)>0)
  <div class="properties-listing spacer"> <a href="{{ url('/propertylisting') }}" class="pull-right viewall">View All Listing</a>
    <h2>Rent </h2>
    <br>
    <div id="owl-example">
      <div class="row">
        @foreach($propertyBuy as $item)
        <div class="col-md-3 col-sm-6">
          <div class="properties">
            <div class="image-holder">
              @if($item->image && File::exists(public_path('assets/images/property/'.$item->image)) )
              <img src="{{ asset('assets/images/property/'.$item->image) }}" class="img-responsive" alt="properties" /><a class="fa fa-heart wishlist" href="{{ route('wishlist', ['id' => $item->id]) }}"></a>
              @else
              <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" class="img-responsive" alt="properties" /><a class="fa fa-heart wishlist" href="{{ route('wishlist', ['id' => $item->id]) }}"></a>
              <!-- <div class="status sold">{{ $item->reason }}</div> -->
              @endif
            </div>
            <h4 class="property-h4"><a href="{{ route('propertydetail', ['id' => $item->id]) }}">{{ Str::length($item->title) > 20 ? substr($item->title, 0,22) . '...' : $item->title }}</a>
            </h4>

            <p class="price">{{ $currency->currency_symbol }} {{ $item->totalPrice }}
            </p>
            <div class="listing-detail"><span class="fa fa-bed" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bedroom }} bedrooms"></span> <span class="fa fa-bath" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bathroom }} Bathroom"> </span></div>

            <a class="btn btn-primary" href="{{ route('propertydetail', ['id' => $item->id]) }}">View Details</a>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif
  @endif
</div>
<div class="container">
  @if(isset($propertyPG))
  @if(count($propertyPG)>0)
  <div class="properties-listing spacer"> <a href="{{ url('/propertylisting') }}" class="pull-right viewall">View All Listing</a>
    <h2>PG</h2>
    <br>
    <div id="owl-example">
      <div class="row">
        @foreach($propertyPG as $item)
        <div class="col-md-3 col-sm-6">
          <div class="properties">
            <div class="image-holder">
              @if($item->image && File::exists(public_path('assets/images/property/'.$item->image)) )
              <img src="{{ asset('assets/images/property/'.$item->image) }}" class="img-responsive" alt="properties" /><a class="fa fa-heart wishlist" href="{{ route('wishlist', ['id' => $item->id]) }}"></a>
              @else
              <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" class="img-responsive" alt="properties" /><a class="fa fa-heart wishlist" href="{{ route('wishlist', ['id' => $item->id]) }}"></a>
              <!-- <div class="status sold">{{ $item->reason }}</div> -->
              @endif
            </div>
            <h4 class="property-h4"><a href="{{ route('propertydetail', ['id' => $item->id]) }}">{{ Str::length($item->title) > 20 ? substr($item->title, 0,22) . '...' : $item->title }}</a>
            </h4>

            </p>
            <div class="listing-detail"><span class="fa fa-bed" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bedroom }} bedrooms"></span> <span class="fa fa-bath" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bathroom }} Bathroom"> </span></div>

            <a class="btn btn-primary" href="{{ route('propertydetail', ['id' => $item->id]) }}">View Details</a>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif
  @endif
</div>

<div class="container">
  <div class="spacer">
    <div id="owl-example">
      <div class="row">
        <div class="col-sm-12">
          <h2>Property News</h2>
        </div>
        <br>
        @if(isset($news))
        @foreach($news as $newses)
        <div class="col-md-3 col-sm-6">
          <div style="margin-bottom: 20px; padding:12px;" class="outerNews">
            <div class="contentNews ">
              @if($newses->image && File::exists(public_path('assets/images/news/'.$newses->image)) )
              <img src="{{ asset('assets/images/news/'.$newses->image) }}" class="img-responsive" alt="properties" />
              @else
              <img src="{{ asset('assets/images/news/default-news.png') }}" class="img-responsive" alt="properties" />
              @endif
            </div>
            <div class="contentNewsTitle">
              <h5><a href="{{ route('newsdetail', ['id' => $newses->id]) }}">{{ Str::length($newses->title)  > 70 ? substr($newses->title, 0,65) . '...' : $newses->title }}</a></h5>
            </div>
            <div class="contentNewsTitleButton">
              <a href="{{ route('newsdetail', ['id' => $newses->id]) }}" class="btn btn-primary pro_button">More Detail</a>
            </div>
          </div>
        </div>
        @endforeach
        @endif
      </div>
    </div>
  </div>

</div>

<section class="fdb-block">
  <div class="container">
    <h2>User Guides</h2>
    <br>
    <?php
    $CmsPageData = CmsGuidePages();
    ?>
    <div class="row text-lg-right guide">
      @foreach($CmsPageData as $links)
      <div class="col col-sm-4 col-lg-4">
        <img alt="image" class="fdb-icon" src="{{ asset('assets/images/portal/'.$links->title_image) }}">
        <h4 class="col"><a href="{{$links->slug}}" class="btn btn-primary guide_button"> <strong>{{ $links->name }}</strong></a></h4>
      </div>
      @endforeach
    </div>

  </div>
</section><br><br>

<script type="text/javascript">
  function togglemodel() {
    let mod = document.getElementById('myModel').style.display = "block"
  }

  function closeModel() {
    let clos = document.getElementById('myModel').style.display = "none"
  }
</script>
<script>
  // var dropbtn = document.getElementsByClassName("dropbtn");
  //   var i;

  //   for (i = 0; i < dropbtn.length; i++) {
  //       dropbtn[i].addEventListener("click", function() {
  //           /* Toggle between adding and removing the "active" class,
  //           to highlight the button that controls the panel */
  //           this.classList.toggle("active");
  //       });
  //   }
  function openCity(evt, reason) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace("active", "");
    }
    document.getElementById(reason).style.display = "block";
    evt.currentTarget.className += " active";
  }
  let tabs = document.querySelectorAll(".tabs button");
  let tabsContent = document.querySelectorAll(".tabs-content .tabcontent");
  tabs.forEach((tab, index) => {
    tab.addEventListener("click", () => {
      tabsContent.forEach((content) => {
        content.classList.remove("active");
      });
      tabs.forEach((tab) => {
        tab.classList.remove("active");
      });
      tabsContent[index].classList.add("active");
      tabs[index].classList.add("active");
    });
  });
</script>

<script>
  const value = document.querySelector("#value")
  const input = document.querySelector("#price")
  value.textContent = input.value
  input.addEventListener("input", (event) => {
    value.textContent = event.target.value
  })
</script>

<style>
  /* .owl-carousel {
    display: none;
    width: 100%;
    z-index: 1;
} */
  .sl-slide-inner h2 {
    margin-left: 300px;
  }

  .sl-slide-inner p {
    margin-left: 150px;
  }

  body {
    font-family: Arial;
  }

  .tab {
    overflow: visible;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
    display: flex;
  }

  /* Style the buttons inside the tab */
  .tab button {
    background-color: inherit;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    transition: 0.3s;
    font-size: 17px;
  }

  .tab button:first-child {
    border-radius: 26px 0 0 0;
  }

  /* Change background color of buttons on hover */
  .tab button:hover {
    background-color: #ddd;
  }


  /* Style the tab content */
  .tabs-content {
    background: white;
    min-height: 300px;
    padding: 20px;
  }

  .tabcontent {
    display: none;
    border: 1px solid #ccc;
    /* border-top: none; */
    padding: 10px;
    background-color: white;
    color: #f1f1f1;
  }

  .tabcontent .row {
    width: 100%;
    margin: auto;
  }

  .tabcontent .row img {
    width: 100%;
    height: 100px;
    object-fit: cover;
  }

  .tabcontent .row p {
    color: #fff;
    margin-top: 8px;
  }

  .tabcontent.active {
    display: block;
  }

  .modal-content {
    color: black;
    overflow-y: scroll;
    height: 700px;
    border-radius: 10px;
    overflow: hidden;
  }

  .container-fluid {
    overflow: hidden;
  }

  .tab .dropdown-menu {
    display: none !important;
    flex-direction: column;
  }

  .search-bar {
    position: absolute;
    z-index: 100;
    top: 30%;
    left: 15%;
    width: 70%;
    /* overflow: auto; */
    padding: 0
  }

  .search-bar .tab {
    border-radius: 26px 26px 0 0;
  }

  .search-bar .tabcontent {
    border-radius: 0 0 26px 26px;
  }

  .tab .dropbtn {
    display: none !important;
  }

  .tab .dropbtn:hover {
    background: initial;
    border-radius: 26px 0 0 0;
  }

  @media screen and (max-width:767px) {
    button.btn.btn-danger {
      margin-top: 10px;
    }

    .tab .dropbtn {
      display: inline-block !important;
    }

    .tab .drop-tabs {
      display: none;
    }

    /* .tab .dropbtn.show ~ .drop-tabs{
      display: flex!important;
    flex-direction: column;
    position: fixed!important;
    background: #f1f1f1;
    z-index: 1;
    } */
    .tab .drop-tabs {
      display: none !important;
    }

    .tab .dropdown-menu {
      padding: 0;
      border-radius: 0;
    }

    .tab .dropdown-menu.show {
      display: flex !important;
      flex-direction: column;
      inset: unset !important;
      margin: 0px;
      min-width: max-content;
      top: 30%;
      left: 15%;
      transform: none !important;
      background: #f1f1f1;
      z-index: 1;
    }

    .tab .dropdown-menu.show button:first-child {
      border-radius: 0;
    }
  }

  /* .search-bar::-webkit-scrollbar {
    width: 4px;
    height: 4px;
    border-radius: 2px;
  }
  .search-bar::-webkit-scrollbar-track{
      border-radius: 2px;
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
            box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
  }
  .search-bar::-webkit-scrollbar-thumb {
    background-color: #9b9a9a;
      border-radius: 2px;
    outline: 1px solid #737373; 
  } */

  .container-fluid.search-bar {
    border-radius: 10px;
    overflow: visible;
  }

  .wishlist {
    position: absolute;
    z-index: 1000;
    top: 0;
    right: 0;
    color: #000;
    font-size: 15px;
  }
  .wishlist:hover{
    color: red; 
    text-decoration: none;
  }

  button.tablinks.active {
    background-color: #d1c8c8;
  }

  .tab button {
    padding: 1.5rem 2.75rem;
  }

  .tabcontent {
    padding: 15px;
  }

  button.btn.btn-danger {
    border-radius: 30px;
    width: 90px;
    background: #e4002b;
  }

  button.btn.SearchBtn {
    width: 70px;
    margin-left: 15px;
  }

  .filterRow {
    padding: 10px;
  }


  .text-muted {
    color: black;
  }

  .contentNewsTitleButton {
    /* height: 20px; */
    display: block;
    text-align: center;
    margin-top: 10px;
  }

  .contentNews {
    height: 150px;
    display: block;
  }

  .contentNews img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .contentNewsTitle {
    text-align: center;
    display: block;
    height: 55px;
    padding: 0 8px;
    overflow: hidden;
  }

  .outerNews {
    height: auto;
    border: solid 0.5px grey;
    border-radius: 10px;
    overflow: hidden;
  }

  .card-body {
    text-align: center;
    margin-right: 60px;
    border-radius: 50px;
  }

  .card {
    /* background: #222; */
    border: 1px solid #c73333 !important;
    margin-bottom: 5rem;
    border-radius: 200px;
    overflow: hidden;
    background-color: #e4002b;
  }

  .card a {
    font-size: 30px;
    text-align: center;
    width: 50px;
    color: #ddd;
  }

  .frontPanel {
    width: 745px;
    max-width: 100%;
    margin: 0 auto;
    border-radius: 26px;
    overflow: visible;
    /* min-width: max-content; */
  }

  .properties {
    border: solid 0.5px grey;
    border-radius: 10px;
  }

  .properties h4 a {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }


  .col-item .photo img {
    margin: 0 auto;
    width: 100%;
    height: 160px;
    object-fit: cover;
  }

  .col-item .info {
    padding: 10px;
    border-radius: 0 0 5px 5px;
    margin-top: 1px;
  }

  .col-item:hover .info {
    background-color: rgba(215, 215, 244, 0.5);
  }

  .col-item .price {
    /*width: 50%;*/
    float: left;
    margin-top: 5px;
  }

  .col-item .price h5 {
    line-height: 20px;
    margin: 0;
  }

  .price-text-color {
    color: #00990E;
  }

  .col-item .info .rating {
    color: #003399;
  }

  .col-item .rating {
    /*width: 50%;*/
    float: left;
    font-size: 17px;
    text-align: right;
    line-height: 52px;
    margin-bottom: 10px;
    height: 52px;
  }

  .col-item .separator {
    border-top: 1px solid #FFCCCC;
  }

  .clear-left {
    clear: left;
  }

  .col-item .separator p {
    line-height: 20px;
    margin-bottom: 0;
    margin-top: 10px;
    text-align: center;
  }

  .col-item .separator form .btn {
    width: 100%;
    text-align: center;
  }

  .col-item .separator p i {
    margin-right: 5px;
  }

  .col-item .btn-add {
    width: 50%;
    float: left;
  }

  .col-item .btn-add {
    border-right: 1px solid #CC9999;
  }

  .col-item .btn-details {
    width: 50%;
    float: left;
    padding-left: 10px;
  }

  .controls {
    margin-top: 20px;
  }

  [data-slide="prev"] {
    margin-right: 10px;
  }

  .backgroundSlider {
    background: grey;
  }

  .active,
  .accordion:hover {
    background-color: white;
  }

  .col-item {
    border: 0.5px solid grey;
    border-radius: 10px;
    background: #FFF;
    overflow: hidden;
  }

  .SliderBootom {
    background-color: #eeeeee;
    margin-top: 20px;
    padding: 50px;
  }
  .SliderBootom .padding-0{
    padding: 0;
  }
  .SliderBootom .tabs {
    display: flex;
    flex-direction: column;
  }

  .SliderBootom .tabs button {
    margin: 20px 0 20px 10px;
    height: 60px;
    font-size: x-large;
    font-weight: 700;
    border-right: 0;
    border-radius: 0;
    background: white;
    color: #4491d3;
    /* box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px; */
  }

  .SliderBootom .tabs button:hover {
    box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
  }

  .SliderBootom .tabs button:active,
  .SliderBootom .tabs button:hover:focus,
  .SliderBootom .tabs button.active {
    outline: 0;
    color: white;
    background: #4491d3;
    /* border: 1px solid #4491d3; */
    box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
  }

  /* .SliderBootom .tabs button::after {
    background: #4491d3;
    content: "";
    display: block;
    height: 12px;
    left: 135px;
    right: -15px;
    opacity: 0;
    position: absolute;
    transition: all .3s ease;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
    -moz-transition: all .3s ease;
    -webkit-transition: all .3s ease;
  } */

  .SliderBootom .tabs button.active::after {
    opacity: 1;
  }

  .DisplayData {
    display: block;
  }

  .NoData {
    display: none;
  }

  .tabcontent .row img {
    width: 100%;
    height: 132px;
    border-radius: 20px;
  }
</style>


@endsection('content')