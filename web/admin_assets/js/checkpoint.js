$(document).ready(function () {
    var lang = $("html").attr("lang");
    loadGoogle(lang);
});

var autocomplete;
var zoomLevel;
var map;
var marker;

$("form").keydown(function (event) {
    if (event.keyCode == 13) {
        event.preventDefault();
        return false;
    }
});

function initMap() {

    var lat = $("#map").data('latitude');
    var lng = $("#map").data('longitude');
    var zoom = $("#map").data('zoom');
    var form = $('#checkpoint_form');

    if (lat == '' || lng == '') {
        lat = 48.136207;
        lng = 67.153550;
    }

    if (zoom == '') {
        zoom = 5;
    }

    var optionsMap = {
        zoom: zoom,
        draggable: true,
        center: {lat: lat, lng: lng},
        scrollwheel: false,
        styles: [{
            featureType: "poi",
            stylers: [
                {visibility: "off"}
            ]
        }]
    };

    map = new google.maps.Map(document.getElementById('map'), optionsMap);

    autocomplete = new google.maps.places.Autocomplete((document.getElementById('checkpoint-address')), {types: ['geocode']});
    autocomplete.addListener('place_changed', fillInAddress);

    marker = new google.maps.Marker({
        draggable: true,
        animation: google.maps.Animation.DROP,
        map: map,
        anchorPoint: new google.maps.Point(0, -29)
    });

    google.maps.event.addListener(marker, "dragend", function (event) {
        var geometry = marker.getPosition();

        $("#checkpoint-latitude").prop('value', geometry.lat());
        $("#checkpoint-longitude").prop('value', geometry.lng());
        //$("#checkpoint-latitude").blur();

        setAddress();

        setTimeout(function () {
            form.yiiActiveForm('validateAttribute', 'checkpoint-address');
        }, 500);
        marker.setPosition(geometry);
    });

    google.maps.event.addListener(map, 'click', function (event) {
        var geometry = event.latLng;

        $("#checkpoint-latitude").prop('value', geometry.lat());
        $("#checkpoint-longitude").prop('value', geometry.lng());
        //$("#checkpoint-latitude").blur();

        setAddress();

        setTimeout(function () {
            form.yiiActiveForm('validateAttribute', 'checkpoint-address');
        }, 500);
        marker.setPosition(geometry);
    });

    google.maps.event.addListener(map, 'zoom_changed', function () {
        zoomLevel = map.getZoom();
        $("#map").data('zoom', zoomLevel);
    });

    marker.setPosition({lat: lat, lng: lng});

    $("#checkpoint-latitude").on('change keyup paste', function () {
        setAddress();
    });
    $("#checkpoint-longitude").on('change keyup paste', function () {
        setAddress();
    });
    setAddress();

}

function fillInAddress() {

    var place = autocomplete.getPlace();
    var geometry = place.geometry.location;

    $("#checkpoint-latitude").prop('value', geometry.lat());
    $("#checkpoint-longitude").prop('value', geometry.lng());
    //$("#checkpoint-latitude").blur();

    marker.setVisible(false);

    if (place.geometry.viewport) {
        map.fitBounds(place.geometry.viewport);
    } else {
        map.setCenter(place.geometry.location);
        map.setZoom(17);
    }

    marker.setPosition(place.geometry.location);
    marker.setVisible(true);

}

function setAddress() {

    var geocoder = new google.maps.Geocoder();
    var latitude = $("#checkpoint-latitude").val();
    var longitude = $("#checkpoint-longitude").val();
    var latlng = {lat: Number(latitude), lng: Number(longitude)};
    geocoder.geocode({'location': latlng}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            $("#checkpoint-address").prop('value', results[0].formatted_address);
        }else{
            $("#checkpoint-address").prop('value', 'Не найдено');
        }
    });
    //map.setCenter(latlng);
    marker.setPosition(latlng);

}

function loadGoogle(lang) {
    $("body").append('<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyALfPPffcWHUHCDKccaIlBj5kLfQjIcD9w&libraries=places&callback=initMap&hl=' + lang + '&language=' + lang + '">');
}
