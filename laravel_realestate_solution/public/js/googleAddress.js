  $(document).ready(function() {
        $("#lat_area").addClass("d-none");
        $("#long_area").addClass("d-none");
    });

    google.maps.event.addDomListener(window, 'load', function() {
        var places = new google.maps.places.Autocomplete(document.getElementById('address'));
        google.maps.event.addListener(places, 'place_changed', function() {
            var place = places.getPlace();
            var address = place.formatted_address;
            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();
            var latlng = new google.maps.LatLng(latitude, longitude);
            var geocoder = geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'latLng': latlng
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    //console.log(results[0]);
                    if (results[0]) {
                        var address = results[0].formatted_address;
                        var pin = results[0].address_components[results[0].address_components.length - 1].long_name;
                        var country = results[0].address_components[results[0].address_components.length - 2].long_name;
                        var state = results[0].address_components[results[0].address_components.length - 3].long_name;
                        var city = results[0].address_components[results[0].address_components.length - 4].long_name;
                        var countrycode = results[0].address_components[results[0].address_components.length - 2].long_name;
                        var statecode = results[0].address_components[results[0].address_components.length - 3].long_name;
                        var latitude = place.geometry.location.lat();
                        var longitude = place.geometry.location.lng();
                        //var name= name1.substr(0, name1.indexOf(','));

                        document.getElementById('country').value = country;
                        document.getElementById('state').value = state;
                        document.getElementById('city').value = city;
                        document.getElementById('postal_code').value = pin;
                        document.getElementById('state').value = statecode;
                        document.getElementById('country').value = countrycode;
                        document.getElementById('latitude').value = latitude;
                        document.getElementById('longitude').value = longitude;
                        //document.getElementById('hotel_name').value = name;

                    }
                }
            });
        });
    });
