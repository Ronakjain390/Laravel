@extends('user/layouts/parts/master')
@section('content')
<!-- banner -->
@if ($message = Session::get('Success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif
@foreach($propertylist as $item)
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
  <div class="properties-listing spacer">
    <div class="row">
      <div class="col-lg-3 col-sm-4 hidden-xs">
        <div class="hot-properties hidden-xs">
          <h4><b>Hot Properties</b></h4>
          @foreach($hotProperty as $hotProperty)
          <div class="row hot-properties-container">
            <div class="property-img">
              @if($hotProperty->image && File::exists(public_path('assets/images/property/'.$hotProperty->image)))
              <img src="{{asset('assets/images/property/'.$hotProperty->image)}}" class="img-responsive img-circle" alt="properties" >
              @else
              <img src="{{ asset('assets/images/property/defaultProperty.jpeg') }}" class="img-responsive img-circle" alt="properties" />
              @endif
            </div>
            <div class="property-details">
              <h5><a href="{{ route('propertydetail', ['id' => $hotProperty->id]) }}">{{ $hotProperty->title }}</a></h5>
              <p class="price">{{ $currency->currency_symbol }} {{ $hotProperty->totalPrice }}</p>
            </div>
          </div>
          @endforeach
        </div>
        <div>
          <h4><span class="glyphicon glyphicon-map-marker"></span><b> Location</b></h4>
          <div class="well"><iframe width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q={{ $item->address }}&amp;aq=0&amp;oq=pulch&amp;sll=37.0625,-95.677068&amp;sspn=39.371738,86.572266&amp;ie=UTF8&amp;hq=&amp;hnear={{ $item->address }}&amp;ll={{ $item->longitude }},{{ $item->latitutde }}&amp;spn=0.001347,0.002642&amp;t=m&amp;z=14&amp;output=embed"></iframe></div>
        </div>
      </div>
      <div class="col-lg-9 col-sm-8 ">
        <h2>{{ $item->title}}</h2>
        <div class="row">
          <div class="col-lg-8">
            <div class="property-images">
              <!-- Slider Starts -->
              <div id="myCarousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                  <div class="item active">
                    @if($item->image && File::exists(public_path('assets/images/property/'.$item->image)))
                    <img src="{{ asset('assets/images/property/'.$item->image) }}" class="properties" alt="Image"  width="100%"/>
                    @else
                    <img src="{{ asset('assets/images/property/defaultPropertyDetail.jpg') }}" class="properties" alt="properties" width="100%"/>
                    @endif
                  </div>
                </div>
                <!-- <a class="left carousel-control" href="#myCarousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
                <a class="right carousel-control" href="#myCarousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a> -->
              </div>
              <!-- #Slider Ends -->

            </div>
            <div class="spacer">
              <div class="row">
                <div class="col-sm-12">
                  <h4><span class="glyphicon glyphicon-th-list"></span><b> Property Details</b></h4><br>
                  <p>{!! $item->description !!}</p>
                </div>
                <div class="row property-data">
                  <div class="col-sm-6">
                    <ul class="property-type">
                      <li><h5><b>Carpet Area : </b>
                      {{ $item->carpetArea}}</h5></li>
                      <li><h5><b>Built Area : </b>
                      {{ $item->builtArea}}</h5></li>
                      <li><h5><b>Bedroom : </b>
                      {{ $item->bedroom}}</h5></li>
                      <li><h5><b>Bathroom : </b>
                      {{ $item->bathroom}}</h5></li>
                    </ul>
                  </div>
                  
                  <div class="col-sm-6">
                    <ul class="property-type">
                      <li><h5><b>General Amenities : </b>

                      {{ $item->generalAmenities}}</h5></li>
                    </ul>
                  </div>
                  
                </div>
                <div class="row property-area">
                  <div class="col-sm-12">
                    @if($item->description)
                    <h5><b>Description : </b><br><br>
                    {!! $item->description !!}</h5>
                    @endif
                  </div>
                </div>

              </div>
            </div>

          </div>
          <div class="col-lg-4">
            <div class="col-lg-12  col-sm-6 pb-4">
              <div class="property-info">
                <div class="row">
                  <div class="col-sm-1 currency-symbol"> {{ $currency->currency_symbol }}
                  </div>
                  <div class="col-sm-10">
                    <p class="price">{{ $item->totalPrice}} </p>
                  </div>
                </div>
                <p class="area"><span class="glyphicon glyphicon-map-marker"></span>{{ $item->address}}, {{ $item->city}}, {{ $item->state}} {{ $item->postal_code}}</p>
              </div>
              <h6><span class="glyphicon glyphicon-home"></span><b>{{ $item->reason }}</b></h6>
              
              <div class="listing-detail"><span class="fa fa-bed" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bedroom }} bedrooms"></span> <span class="fa fa-bath" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $item->bathroom }} Bathroom"> </span></div>
              
              <div>
                <h6 class="text-uppercase"><b> <span>Listed by :</span> </b><span> {{ $item->Utype }}</span></h6>
                <h6><b> <span>Contact : </span> </b><span>{{ $item->contactNumber }}</span></h6>
              </div>
            </div>
            @endforeach
            <div class="col-lg-12 col-sm-6 ">
              <div class="enquiry">
                <h5><span class="glyphicon glyphicon-envelope"></span><b> Post Enquiry</b></h5>
                <form method="get" class="form-inline" action="{{ route('propertyenquiry.store')}}" name="formPrevent">
                  <input type="hidden" name="propertyId" id="propertyId" value="{{ $item->id }}">
                  @if(Auth::user() == '')
                  <input type="text" class="form-control" name="name" required="" />
                  @error('name')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
                  <input type="email" class="form-control" name="email" required="" />
                  @error('email')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
                  <input type="text" class="form-control" name="contact" required="" />
                  @error('contact')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
                  @else
                  <input type="hidden" name="userId" id="userId" value="{{ Auth::user()->id }}">
                  <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required="" />
                  @error('name')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
                  <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" required="" />
                  @error('email')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
                  <input type="text" class="form-control" name="contact" value="{{ Auth::user()->contact }}" required="" />
                  @error('contact')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
                  @endif
                  <textarea rows="6" type="text" class="textarea form-control" name="description" placeholder="Whats on your mind?" required=""></textarea>
                  @error('description')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
                  <br>
                  <button type="submit" class="btn btn-primary read-more-btn" name="Submit">Send Message</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js" integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.js"></script>
<!-- jQuery UI 1.11.4 -->

<script>
  $(function() {
    $("form[name='formPrevent']").validate({
      rules: {
        name: {
          required: true,
          minlength: 2,
        },
        email: {
          required: true,
          email: true,
        },
        contact: {
          required: true,
          digits: true,
          minlength: 10,
          maxlength: 10
        },
        description: {
          required: true,
        }

      },
      messages: {
        email: {
          required: "Please enter a valid email address",
          email: "email must contain @ and . ",
        },
        contact_detail: {
          min: "length must be equal to 10",
          max: "length must be equal to 10",
          digits: "must contain only integer"
        }
      },
      submitHandler: function(form) {
        form.submit();
      }
    });
  });
</script>
<script>
  (function(w, d, s, l, i) {
    w[l] = w[l] || [];
    w[l].push({
      'gtm.start': new Date().getTime(),
      event: 'gtm.js'
    });
    var f = d.getElementsByTagName(s)[0],
      j = d.createElement(s),
      dl = l != 'dataLayer' ? '&l=' + l : '';
    j.async = true;
    j.src =
      'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
    f.parentNode.insertBefore(j, f);
  })(window, document, 'script', 'dataLayer', 'GTM-NSLHKCP');
</script>
<style>
  .error {
    color: red;
  }
</style>
@endsection('content')