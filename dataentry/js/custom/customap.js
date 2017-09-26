

function initMap()
{
    var map = new google.maps.Map(document.getElementById('map'), {

        center: {lat: 31.768319 , lng: 35.21371},
        zoom: 13

    });


    var input = document.getElementById('area_en');

    var options =
    {
        componentRestrictions: {country: 'il'}
    };

    autocomplete = new google.maps.places.Autocomplete(input, options);


    autocomplete.bindTo('bounds', map);

    var marker = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29)
    });

    autocomplete.addListener('place_changed', function () {

        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
        }
//                marker.setIcon(/** @type {google.maps.Icon} */({
//                    url: place.icon,
//                    size: new google.maps.Size(71, 71),
//                    origin: new google.maps.Point(0, 0),
//                    anchor: new google.maps.Point(17, 34),
//                    scaledSize: new google.maps.Size(35, 35)
//                }));
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);


        //alert(place.place_id);

        var placeId = "";
        placeId = place.place_id;

        document.getElementById('lat').value = place.geometry.location.lat();
        document.getElementById('lng').value = place.geometry.location.lng();
        $.getJSON('https://maps.googleapis.com/maps/api/geocode/json?place_id=' + placeId + '&sensor=false&language=he&key=AIzaSyB2Sz2Tlw0N-sNSdfYIod5P9wS7MfV_35E', function (data) {


            document.getElementById('area_he').value = data.results[0].formatted_address;


        });
    });


    var input2 = document.getElementById('area_he');
    var options2 = {

        componentRestrictions: {country: 'il'}
    };

    autocomplete2 = new google.maps.places.Autocomplete(input2, options2);

    autocomplete2.addListener('place_changed', function () {

        var place = autocomplete2.getPlace();
        if (!place.geometry) {
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
        }
//                marker.setIcon(/** @type {google.maps.Icon} */({
//                    url: place.icon,
//                    size: new google.maps.Size(71, 71),
//                    origin: new google.maps.Point(0, 0),
//                    anchor: new google.maps.Point(17, 34),
//                    scaledSize: new google.maps.Size(35, 35)
//                }));
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        //alert(place.place_id);

        var placeId = "";
        placeId = place.place_id;



        document.getElementById('lat').value = place.geometry.location.lat();
        document.getElementById('lng').value = place.geometry.location.lng();

        $.getJSON('https://maps.googleapis.com/maps/api/geocode/json?place_id=' + placeId + '&sensor=false&language=en&key=AIzaSyB2Sz2Tlw0N-sNSdfYIod5P9wS7MfV_35E', function (data) {


            document.getElementById('area_en').value = data.results[0].formatted_address;


        });
    });

}

