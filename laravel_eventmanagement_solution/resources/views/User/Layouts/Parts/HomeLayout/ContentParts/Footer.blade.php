<footer class="footer">
    <!-- <div class="footer_top">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="footer_widget">
                            <div class="address_details text-center">
                                <h4 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay=".3s">12 Feb, 2020</h4>
                                <h3 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">Green Avenue, New York</h3>
                                <p class="wow fadeInUp" data-wow-duration="1s" data-wow-delay=".5s">The event regularly attracts a diverse range of attendees from around the world.</p>
                                <a href="#" class="boxed-btn3 wow fadeInUp" data-wow-duration="1s" data-wow-delay=".6s">Buy Tickets</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    <div class="container">
        <div class="row footer-container">
            <?php
            $CmsPageData = CmsPages();
            ?>
            <div class="col-lg-3 col-sm-6">
                <ul class="footer-links">
                    <h5 class="">INFORMATION</h5>
                    @foreach ($CmsPageData as $link)
                    <li><a class="" href="{{$link->slug}}">{{$link->name}}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-3 col-sm-6">
                <ul class="footer-links">
                    <h5 class=""> QUICK LINKS</h5>
                    <li><a class="" href="{{ route('EventShow') }}">Events</a></li>
                    <li><a class="" href="{{ route('ArtistShow') }}">Artist</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-sm-6">
                <ul class="footer-links mb-0">
                    <?php
                    $data = generalSetting();
                    ?>
                    <h5 class=""> CONTACT US</h5>
                    <div class="copy_right " data-wow-duration="1s" data-wow-delay=".5s">
                        <!-- <div class="contact-details"><a href="route('FaqDetails')"><i class="fa-solid fa-location-dot"></i></a><span> Faqs</span> </div> -->
                        @if(isset($data->address))
                        <div class="contact-details"><i class="fa-solid fa-location-dot"></i><span> {{$data->address}}</span> </div>
                        @endif
                        @if(isset($data->email))
                        <div class="contact-details"><i class="fa-solid fa-envelope"></i> <span> {{$data->email}} </span></div>
                        @endif
                        @if(isset($data->contact_number))
                        <div class="contact-details"><i class="fa-solid fa-phone"></i> <span> {{$data->contact_number}}</span></div>
                        @endif
                    </div>
                </ul>
                <ul class="footer-links" id="social_links">
                    <?php
                    $SocialLinksData = SocialLinks();
                    ?>
                    @if(count($SocialLinksData)>0)
                    @foreach ($SocialLinksData as $link)
                    <a class="" href="{{$link->url}}" target="blank"><i class="fa-brands fa-{{$link->name}} social-links"></i></a>
                    @endforeach
                    @endif
                </ul>
            </div>
            <div class="col-lg-3 col-sm-6">
                <ul class="footer-links" id="map">
                    <div class="mapp">
                       
                            <div id="googleMap" style="height: 220px; position: relative; overflow: hidden;">
                            </div>
                           
                    </div>
                </ul>
            </div>
            <div class="col-xl-12 footer-copyright">
                @if(isset($data->copyright_text))
                <p class="copy_right text-center" data-wow-duration="1s" data-wow-delay=".5s">
                    {{$data->copyright_text}}
                </p>
                @endif
            </div>
        </div>
    </div>
</footer>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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