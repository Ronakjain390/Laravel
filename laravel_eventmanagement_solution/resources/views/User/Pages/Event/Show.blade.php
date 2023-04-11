<?php

$currency = currency();

$currency_symbol = $currency[0]->currency_symbol;

?>
@extends('User/Layouts/Layout/HomeLayout')
@section('UserContent')
<div class="container-fluid bg-light ">
   <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 94px">
   </div>
</div>
<!-- bradcam_area end -->
<div class="category-browse--header">
   <div class="category-browse--header-">
      <div class="container" style="padding: 1rem;">
         <div class="category-browse--header-text">
            <div class="category-browse--header-text__wrapper">
               <h1 class="category-browse__header--content">Events/{{ ucfirst($eventDetail->title) }}
                  <div class="eds-text-bl"></div>
               </h1>
               <p>Discover the best Performence &amp; Events in your area and online</p>
            </div>
         </div>
         <!-- <aside class="category-browse--header-image category-browse--header-image--square"><img fetchpriority="high" class="full-width-img" loading="eager" src="{{asset('Assets/images/' .$eventDetail->image)}}" alt="[object Object]"></aside> -->
      </div>
   </div>
</div>

<!-- Payment Modal -->

<div class="modal fade  " id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
   <div class="modal-dialog modal-xl">
      <div class="modal-content">
         <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Checkout </h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <div class="container-fluid">
               <div class="row" style="height: 350px;">
                  <div class="col-md-8">
                     <div class="spinner">
                     </div>
                     @csrf
                     <div class="mb-3 col-sm-8">
                        <label for="exampleFormControlSelect1" class="form-label">Select Tickets </label>
                        <div class="card w-100">
                           @foreach($eventTicket as $eventTicket1)
                           <div class="card-content" style="display: flex;">
                              <div class="container">
                                 <h5 value="{{$eventTicket1->ticket_category_id}}">Category : {{ $eventTicket1->ticketCategories->name}} </h5>
                                 <div class="card-price">
                                    <span>Price: {{ $currency[0]->currency_symbol }} {{ $eventTicket1->ticket_price }} </span>
                                 </div>
                                 <hr>
                              </div>
                              <div class="container-card">
                                 <div>
                                    <select name="total_tickets[{{ $eventTicket1->id }}]" data-catid="{{ $eventTicket1->id }}" class="quantity">
                                       <option value="0">0</option>
                                       <option value="1">1</option>
                                       <option value="2">2</option>
                                       <option value="3">3</option>
                                       <option value="4">4</option>
                                       <option value="5">5</option>
                                       <option value="6">6</option>
                                       <option value="7">7</option>
                                       <option value="8">8</option>
                                       <option value="9">9</option>
                                       <option value="10">10</option>
                                    </select>
                                 </div>
                              </div>
                           </div>
                           @endforeach
                        </div>
                     </div>
                  </div>
                  <div class="ticket-page col-md-4 ms-auto">
                     <div class="summary">
                        <table class="table table-bordered">
                           <tbody class="summary-content">


                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button id="rzp-button1" class="genric-btn info radius mb-2">Pay</button>

            @if($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade in" role="alert">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
               </button>
               <strong>Error!</strong> {{ $message }}
            </div>
            @endif
            {!! Session::forget('error') !!}
            @if($message = Session::get('success'))
            <div class="alert alert-info alert-dismissible fade in" role="alert">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
               </button>
               <strong>Success!</strong> {{ $message }}
            </div>
            @endif
            {!! Session::forget('success') !!}

         </div>
      </div>
   </div>
</div>



<!-- Payment Modal end -->


<!--================Blog Area =================-->
<section class="blog_area event-description single-post-area ">
   <div class="container">
      <div class="row">
         <div class="col-lg-8 posts-list">
            <div class="single-post">
               <div class="feature-img">
                  <div class="swiper mySwiper">
                     <div class="swiper-wrapper">
                        @if(!is_null($eventDetail->gallary_images))
                        @if(is_array(json_decode($eventDetail->gallary_images)))
                        <?php foreach (json_decode($eventDetail->gallary_images) as $pictures) { ?>
                           <div class="swiper-slide">
                              <img class="single-gallery-image" src="{{asset('Assets/images/'.$pictures) }}" />

                           </div>
                        <?php } ?>
                        @endif
                        @else
                        <img class="single-gallery-image" src="{{asset('Assets/DefaultImage/DefaultEvent.png') }}" />
                        @endif
                     </div>
                     <div class="swiper-button-next"></div>
                     <div class="swiper-button-prev"></div>
                  </div>

               </div>

               <div class="blog_details">
                  <div class="d-flex justify-content-between align-items">
                     <h2>{{$eventDetail->title}}</h2>
                     <div></div>
                     @if(Auth::check())
                     <a type="button" class="boxed-btn3" data-bs-toggle="modal" data-bs-target="#exampleModalToggle" data-wow-duration="1s" data-wow-delay=".7s" href="{{route('eventsBuy', ['id' => $eventDetail->id ])}}">
                        Buy Tickets
                     </a>
                     @else
                     <a href="{{route('login')}}" id="popup-button" class="boxed-btn3" data-wow-duration="1s" data-wow-delay=".7s">Buy Tickets</a>

                     @endif

                  </div>
                  <hr>
                  <h3>When and where</h3>
                  <div class="card-header when-where d-flex justify-content-between align-items-center">
                     <aside class="single_sidebar_widget search_widget col-sm-6" style="margin-bottom: 0;">
                        <!-- <h5>When </h5> -->
                        <i class="fa-solid fa-calendar"></i><strong> <span>{{date('h:i A', strtotime($hours))}} |
                              {{date('D, d M Y', strtotime( $eventDetail->start_date ));}}</span> </strong>
                     </aside>
                     @if(!is_null($eventDetail->address))
                     <aside class="single_sidebar_widget search_widget col-sm-6" style="margin-bottom: 0;">
                        <!-- <h5>Where </h5> -->
                        <i class="fa-solid fa-location-dot"></i><strong> <span>
                              {{ $eventDetail->address }}</span> </strong>
                     </aside>
                     @else
                     @endif
                  </div>
               </div>
               <hr>
               <p class="excert">
                  {!!$eventDetail->about!!}
               </p>

            </div>
            <!-- Artist Listing  -->
            @if(count($artists)>0)
            <div class="blog-author">
               <h2>Artists</h2><br>
               <div class="media align-items-center row">
                  @foreach($artists as $artist)
                  <div class="text-center col-md-4 mb-4">
                     <img src="{{asset('Assets/images/' .$artist->image)}}" alt="">
                     <div class="media-body mt-4">
                        <a href="{{ route('ArtistShowDetail' ,['id' => $artist->id])  }}">
                           <h3>{{ $artist->name }}</h3>
                        </a> <br>
                     </div>
                  </div>
                  @endforeach
               </div>
            </div>
            @endif
            <!-- <div class="navigation-top">
               <div class="d-sm-flex justify-content-between text-center">
                  <p class="like-info"><span class="align-middle"><i class="fa fa-heart"></i></span> Lily and 4
                     people like this</p>
                  <div class="col-sm-4 text-center my-2 my-sm-0">
                     <p class="comment-count"><span class="align-middle"><i class="fa fa-comment"></i></span> 06 Comments</p>
                  </div>
                  <ul class="social-icons">
                     <li><a href="#"><i class="fa fa-facebook-f"></i></a></li>
                     <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                     <li><a href="#"><i class="fa fa-dribbble"></i></a></li>
                     <li><a href="#"><i class="fa fa-behance"></i></a></li>
                  </ul>
               </div>
               <div class="navigation-area">
                  <div class="row">
                     <div class="col-lg-6 col-md-6 col-12 nav-left flex-row d-flex justify-content-start align-items-center">
                        <div class="thumb">
                           <a href="#">
                              <img class="img-fluid" src="img/post/preview.png" alt="">
                           </a>
                        </div>
                        <div class="arrow">
                           <a href="#">
                              <span class="lnr text-white ti-arrow-left"></span>
                           </a>
                        </div>
                        <div class="detials">
                           <p>Prev Post</p>
                           <a href="#">
                              <h4>Space The Final Frontier</h4>
                           </a>
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-12 nav-right flex-row d-flex justify-content-end align-items-center">
                        <div class="detials">
                           <p>Next Post</p>
                              <a href="#">
                                 <h4>Telescopes 101</h4>
                              </a>
                        </div>
                        <div class="arrow">
                           <a href="#">
                              <span class="lnr text-white ti-arrow-right"></span>
                           </a>
                        </div>
                        <div class="thumb">
                           <a href="#">
                              <img class="img-fluid" src="img/post/next.png" alt="">
                           </a>
                        </div>
                     </div>
                  </div>
               </div>
            </div> -->
            @if(count($foodStore)>0)
            <div class="blog-author">
               <h2>Available Food Stores</h2><br>
               <div class="media align-items-center row">
                  @foreach($foodStore as $foodStore1)
                  <div class="col-md-4 mb-4">
                     <img src="{{asset('Assets/images/' .$foodStore1->image)}}" alt="">
                     <div class="media-body mt-4">
                        <a href="{{ route('FoodStoreDetail' ,['id' => $foodStore1->id])  }}">
                           <h3>{{ $foodStore1->name }}</h3>
                        </a> <br>
                        <span> Detail : {!! Str::length($foodStore1->description) > 25 ? substr($foodStore1->description, 0,25) . '...' : $foodStore1->description !!}
                        </span> <br>
                        <span><i class="fa-solid fa-city"></i> {{ $foodStore1->city }}</span> <br>
                        <span><i class="fa-solid fa-phone"></i> : {{ $foodStore1->contact_number }}</span>
                     </div>
                  </div>
                  @endforeach
               </div>
            </div>
            @endif
            <div class="comment-form">
               <!-- <h4>Leave a Reply</h4>
               <form class="form-contact comment_form" action="#" id="commentForm">
                  <div class="row">
                     <div class="col-12">
                        <div class="form-group">
                           <textarea class="form-control w-100" name="comment" id="comment" cols="30" rows="9" placeholder="Write Comment"></textarea>
                        </div>
                     </div>
                     <div class="col-sm-6">
                        <div class="form-group">
                           <input class="form-control" name="name" id="name" type="text" placeholder="Name">
                        </div>
                     </div>
                     <div class="col-sm-6">
                        <div class="form-group">
                           <input class="form-control" name="email" id="email" type="email" placeholder="Email">
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="form-group">
                           <input class="form-control" name="website" id="website" type="text" placeholder="Website">
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <button type="submit" class="button button-contactForm btn_1 boxed-btn">Send Message</button>
                  </div>
               </form> -->
            </div>
         </div>
         <div class="col-lg-4">
            <div class="blog_right_sidebar">
               <!-- <aside class="single_sidebar_widget search_widget">
                  <form action="#">
                     <div class="form-group">
                        <div class="input-group mb-3">
                           <input type="text" class="form-control" placeholder='Search Keyword' onfocus="this.placeholder = ''" onblur="this.placeholder = 'Search Keyword'">
                           <div class="input-group-append">
                              <button class="btn" type="button"><i class="ti-search"></i></button>
                           </div>
                        </div>
                     </div>
                     <button class="button rounded-0 primary-bg text-white w-100 btn_1 boxed-btn" type="submit">Search</button>
                  </form>
               </aside> -->

               <aside class="single_sidebar_widget post_category_widget">
                  <h4 class="widget_title"><i class="fa-solid fa-location-dot"> Location</h4>
                  <h5>{{ $eventDetail->city }}</h5>
                  <h6>{{ $eventDetail->address }}</h6>
                  <!-- map_area_start  -->

                  <div class="map_area">
                     <div id="googleMap" style="height: 300px; position: relative; overflow: hidden;">
                     </div>
                     <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_ADDRESS_KEY') }}&callback=Function.prototype"></script>
 
                     <script>
                        var geocoder = new google.maps.Geocoder();

                        //alert(locv);
                        //convert location into longitude and latitude
                        geocoder.geocode({}, function(locResult) {
                           var lat1 = 26.859604366001562;
                           var lng1 = 75.75616812575058;
                           const pos = {
                              lat: lat1,
                              lng: lng1
                           };
                           var mapProp = {
                              center: new google.maps.LatLng(lat1, lng1),
                              zoom: 18,
                           };
                           var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
                           const marker = new google.maps.Marker({
                              position: pos,
                              map: map,
                           });
                           map.addListener("center_changed", () => {
                              // 3 seconds after the center of the map has changed, pan back to the
                              // marker.
                              window.setTimeout(() => {
                                 map.panTo(marker.getPosition());
                              }, 3000);
                           });
                           marker.addListener("click", () => {
                              map.setZoom(18);
                              map.setCenter(marker.getPosition());
                           });
                        });
                     </script>

                     </script>

                  </div>
                  <!-- map_area_end  -->

                  <!-- <ul class="list cat-list">
                     <li>
                        <a href="#" class="d-flex">
                           <p>Resaurant food</p>
                           <p>(37)</p>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex">
                           <p>Travel news</p>
                           <p>(10)</p>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex">
                           <p>Modern technology</p>
                           <p>(03)</p>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex">
                           <p>Product</p>
                           <p>(11)</p>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex">
                           <p>Inspiration</p>
                           <p>(21)</p>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex">
                           <p>Health Care</p>
                           <p>(21)</p>
                        </a>
                     </li>
                  </ul> -->
               </aside>
               <aside class="single_sidebar_widget popular_post_widget">
                  <h3 class="widget_title">Recent Events</h3>
                  @foreach($recentEventDetail as $eventData)
                  <div class="media post_item">
                     <img src="{{asset('Assets/images/' .$eventData->image)}}" height="50px" width="80px" alt="post">
                     <div class="media-body">
                        <a href="{{route('EventShowDetail', ['id' => $eventData->id])}}">
                           <h3>{{ $eventData->title }}</h3>
                        </a>
                        <a>{{ $eventData->start_date}}</a>
                     </div>
                  </div>
                  @endforeach
               </aside>
               <!-- <aside class="single_sidebar_widget tag_cloud_widget">
                  <h4 class="widget_title">Tag Clouds</h4>
                  <ul class="list">
                     <li>
                        <a href="#">project</a>
                     </li>
                     <li>
                        <a href="#">love</a>
                     </li>
                     <li>
                        <a href="#">technology</a>
                     </li>
                     <li>
                        <a href="#">travel</a>
                     </li>
                     <li>
                        <a href="#">restaurant</a>
                     </li>
                     <li>
                        <a href="#">life style</a>
                     </li>
                     <li>
                        <a href="#">design</a>
                     </li>
                     <li>
                        <a href="#">illustration</a>
                     </li>
                  </ul>
               </aside> -->
               <!-- <aside class="single_sidebar_widget instagram_feeds">
                  <h4 class="widget_title">Instagram Feeds</h4>
                  <ul class="instagram_row flex-wrap">
                     <li>
                        <a href="#">
                           <img class="img-fluid" src="img/post/post_5.png" alt="">
                        </a>
                     </li>
                     <li>
                        <a href="#">
                           <img class="img-fluid" src="img/post/post_6.png" alt="">
                        </a>
                     </li>
                     <li>
                        <a href="#">
                           <img class="img-fluid" src="img/post/post_7.png" alt="">
                        </a>
                     </li>
                     <li>
                        <a href="#">
                           <img class="img-fluid" src="img/post/post_8.png" alt="">
                        </a>
                     </li>
                     <li>
                        <a href="#">
                           <img class="img-fluid" src="img/post/post_9.png" alt="">
                        </a>
                     </li>
                     <li>
                        <a href="#">
                           <img class="img-fluid" src="img/post/post_10.png" alt="">
                        </a>
                     </li>
                  </ul>
               </aside> -->
               <!-- <aside class="single_sidebar_widget newsletter_widget">
                  <h4 class="widget_title">Newsletter</h4>
                  <form action="#">
                     <div class="form-group">
                        <input type="email" class="form-control" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter email'" placeholder='Enter email' required>
                     </div>
                     <button class="button rounded-0 primary-bg text-white w-100 btn_1 boxed-btn" type="submit">Subscribe</button>
                  </form>
               </aside> -->
            </div>
         </div>
      </div>
   </div>



   <!-- JavaScript to show and hide the popup -->

</section>

@endsection('UserContent')
@push('js')
<div id="razorpayBtn"></div>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script async>
   $(document).ready(function() {
      $(".spinner").attr("disabled", 'disabled');
      $(document).find('select').niceSelect('destroy');
      $(".quantity").on('change', function() {
         let arr = [];
         $.each($(".quantity"), function(key, ele) {
            arr.push({
               'event_cat_id': $(ele).data('catid'),
               'event_cat_tickets': $(ele).val(),
            });
         });
         console.log(arr);
         let formData = {
            "_token": "{{csrf_token()}}",
            'data': arr,
            'event_id': "{{$eventId}}",
         }

         $.ajax({
            type: "POST",
            url: "{{route('addTicket')}}",
            data: formData,
            dataType: "json",
            success: function(res) {
               let table = $(".summary-content");
               let tr = '';
               tr += ` <h3> Order Summary </h3>`;
               $.each(res.items, function(key, val) {
                  if (val.event_category_tickets > 0) {


                     tr += `<tr> 
                           <th>${val.event_cat_name} : (${val.event_category_tickets}X₹ ${val.ticket_price/val.event_category_tickets})</th>
                           <td class="text-end">₹ ${val.ticket_price}</td>
                        </tr>`;
                  }
               });
               tr += `<tr>
                        <th>Fee :</th>
                        <td class="text-end">₹${res.fee}</td>
                     </tr>
                     <tr class="bg-light">
                        <th>Grand Total :</th>
                        <td class="text-end">₹${res.grandTotal}</td>
                     </tr>`;
               $(table).html(tr);
            }
         });
      });

      $('.quantity').on('change', function() {
         let arr = [];
         $.each($(".quantity"), function(key, ele) {
            arr.push({
               'event_cat_id': $(ele).data('catid'),
               'event_cat_tickets': $(ele).val(),
            });
         });
         let formData = {
            "_token": "{{csrf_token()}}",
            'data': arr,
            'event_id': "{{$eventId}}"
         }

         $.ajax({
            type: "POST",
            url: "{{route('checkoutPayment')}}",
            data: formData,
            dataType: "json",
            success: function(res) {
               console.log(res.view)
               $("#razorpayBtn").html(res.view);
            }
         });
      });
   });
</script>


<!-- CSS to style the popup -->
<style>
   .modal-content {
      height: 530px;
   }

   select.quantity {
      padding: 0.4rem;
      margin-bottom: 2px;
      margin-right: 11px;
      margin-top: 5px;

   }

   p {
      color: black;
   }

   .swiper {
      width: 100%;
      height: 80%;
   }

   .swiper-slide {
      text-align: center;
      font-size: 18px;
      background: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
   }

   .swiper-slide img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
   }
   .swiper-button-next:after,
    .swiper-rtl .swiper-button-prev:after,
    .swiper-button-prev:after,
    .swiper-rtl .swiper-button-next:after {
        color: white;
        font-weight: bolder;
    }
</style>
@endpush