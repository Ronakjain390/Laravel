@extends('user/layouts/parts/master')
@section('content')
<style>
    .text-center {
        text-align: center;
    }

    #map {
        width: "100%";
        height: 400px;
    }

    .maps {
        padding-top: 40px;
    }
</style>
<div class="inside-banner">
    <div class="container">
        <span class="pull-right"><a href="/">Home</a> / Properties on Map</span>
        <h2>Properties</h2>
    </div>
</div>
<div class="container maps">
    <div class="col-lg-3 col-sm-4 ">
        <div class="search-form">
            <h4><span class="glyphicon glyphicon-search"></span> Search for</h4>
            <form method="get" action="{{ route('search-perameter') }}">
                <input type="text" id="address" class="form-control" placeholder="Search of Properties">
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Property Type</label>
                        <select class="form-control" name="type" id="type">
                            <option>{{ app('request')->input('type') }}</option>
                            <option value="alltype">All Type</option>
                            <option value="villa">villa</option>
                            <option value="commercial">Commercial</option>
                            <option value="apartment">Apartment</option>
                            <option value="house">House</option>
                            <option value="rural">Rural</option>
                            <option value="land">Land</option>
                            <option value="townhouse">Townhouse</option>
                            <option value="retire">Retirement Living</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Minimum Price</label>
                        <select class="form-control" name="minprice" id="minprice">
                            <option>{{ app('request')->input('minprice') }}</option>
                            <!-- <option value="*">Any</option> -->
                            <option value="0">0</option>
                            <option value="500000">500000</option>
                            <option value="1000000">1000000</option>
                            <option value="2000000">2000000</option>
                            <option value="5000000">5000000</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Maximum Price</label>
                        <select class="form-control" name="maxprice" id="maxprice">
                            <option>{{ app('request')->input('maxprice') }}</option>
                            <!-- <option value="*">Any</option> -->
                            <option value="500000">500000</option>
                            <option value="1000000">1000000</option>
                            <option value="2000000">2000000</option>
                            <option value="5000000">5000000</option>
                            <option value="5000000">10000000</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Minimum Bedroom</label>
                        <select class="form-control" name="minbedroom" id="minbedroom">
                            <option>{{ app('request')->input('minbedroom') }}</option>
                            <!-- <option value="*">Any</option> -->
                            <option value="2">2</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Maximum Bedroom</label>
                        <select class="form-control" name="maxbedroom" id="maxbedroom">
                            <option>{{ app('request')->input('maxbedroom') }}</option>
                            <!-- <option value="*">Any</option> -->
                            <option value="2">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Bathroom</label>
                        <select class="form-control" name="bathroom" id="bathroom">
                            <option>{{ app('request')->input('bathroom') }}</option>
                            <!-- <option value="*">Any</option> -->
                            <option value="2">2</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Outdoor Feature</label>
                        <select class="form-control">
                            <option>Price</option>
                            <option>$150,000 - $200,000</option>
                            <option>$200,000 - $250,000</option>
                            <option>$250,000 - $300,000</option>
                            <option>$300,000 - above</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-primary">Find Now</button>
            </form>
        </div>
    </div>
    <div class="col-lg-9 col-sm-8 ">
        <div id="map"></div>
    </div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_ADDRESS_KEY') }}&callback=initMap" async></script>
<script>
    let map, activeInfoWindow, markers = [];

    /* ----------------------------- Initialize Map ----------------------------- */
    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: {
                lat: 28.626137,
                lng: 79.821603,
            },
            zoom: 15
        });

        map.addListener("click", function(event) {
            mapClicked(event);
        });

        initMarkers();
    }

    /* --------------------------- Initialize Markers --------------------------- */


    function initMarkers() {
        const initialMarkers = <?php echo json_encode($data); ?>;
        for (let index = 0; index < initialMarkers.length; index++) {
            let drivermarker = await new google.maps.Marker({
                position: new google.maps.LatLng(initialMarkers[index].latitude, initialMarkers[index].longitude),
                title: initialMarkers[index].title,
                icon: {
                    url: 'https://api.iconify.design/fxemoji:recreationalvehicle.svg',
                    scaledSize: new google.maps.Size(60, 40)
                }
            })
            drivermarker.setMap(map)
        }
        // for (let index = 0; index < initialMarkers.length; index++) {
        //     const markerData = initialMarkers[index];
        //     console.log(index);
        //     const marker = new google.maps.Marker({
        //         position: markerData.position,
        //         label: markerData.label,
        //         draggable: markerData.draggable,
        //         map
        //     });
        //     markers.push(marker);

        //     const infowindow = new google.maps.InfoWindow({
        //         content: `<b>${markerData.position.lat}, ${markerData.position.lng}</b>`,
        //     });
        //     marker.addListener("click", (event) => {
        //         if (activeInfoWindow) {
        //             activeInfoWindow.close();
        //         }
        //         infowindow.open({
        //             anchor: marker,
        //             shouldFocus: false,
        //             map
        //         });
        //         activeInfoWindow = infowindow;
        //         markerClicked(marker, index);
        //     });

        //     marker.addListener("dragend", (event) => {
        //         markerDragEnd(event, index);
        //     });
        // }
    }
</script>
@endsection('content')