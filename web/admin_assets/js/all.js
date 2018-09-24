$(document).ready(function(){

    initSelect2();

    /*
    $('.sortable').nestedSortable({
        handle: 'div',
        items: 'li',
        toleranceElement: '> div',
        stop: function(e) { save_order(e); }
    });
        */

    // if($('.rangepick').length){
    //    $('.rangepick').daterangepicker({
    //     locale: {
    //         format: 'DD/MM/YYYY'
    //     }
    //   });
    // }
    // if($('.tags').length) {
    //     $('.tags').tagsinput({
    //         tagClass: function (item) {
    //             return (item.length > 10 ? 'big' : 'small');
    //         }
    //     });
    // }
    // masks();

});

function initSelect2() {
    var selects = $(".sel2.mpsel.outst");
    $.each(selects, function () {
        var select = $(this);
        if ($(select).length) {
            var vn = parseInt($(select).attr('vn'));
            if (vn > 1) {
                $(select).val($(select).attr('selvalue').split(','));
                $(select).select2();
            } else {
                $(select).val($(select).attr('selvalue'));
                $(select).select2();
            }
        }
    });
}

function select2html(targ, html){
    targ
        .html(html)
        .select2('destroy')
        .show().css('display','block')
        .select2();
}

function intval( mixed_var, base ) {	// Get the integer value of a variable

    var tmp;

    if( typeof( mixed_var ) == 'string' ){
        tmp = parseInt(mixed_var);
        if(isNaN(tmp)){
            return 0;
        } else{
            return tmp.toString(base || 10);
        }
    } else if( typeof( mixed_var ) == 'number' ){
        return Math.floor(mixed_var);
    } else{
        return 0;
    }
}

function masks(){
    $(".phone-mask").inputmask("+99 (999) 999 99 99");
    $("input[type='tel']").inputmask("+99 (999) 999 99 99");
}

function update_search(key, el) {
    var value = $(el).val();

    key = encodeURI(key); value = encodeURI(value);

    var kvp = document.location.search.substr(1).split('&');

    var i=kvp.length; var x; while(i--)
{
    x = kvp[i].split('=');

    if (x[0]==key)
    {
        x[1] = value;
        kvp[i] = x.join('=');
        break;
    }
}

    if(i<0) {kvp[kvp.length] = [key,value].join('=');}

    //this will reload the page, it's likely better to store this until finished
    document.location.search = kvp.join('&');
}

function removecaseimage(im,el){
    $('#cim_'+el).remove();

    $.ajax({
        url: '/admin/cases/removecaseimage',
        type: 'POST',
        dataType: 'JSON',
        data: {
            im: im,
            _csrf:$('input[name="_csrf"]').val(),
        },
        success: function(data) {
        },
        error: function () {
        }
    });
}

var lang = $("html").attr("lang");
var map;
var marker;

function initMap()
{
    let lat = $("#map").data('latitude');
    let lng = $("#map").data('longitude');
    const zoom = 5;
    const form = $('#place_form');

    if (lat === '') {lat = 48.136207; lng = 67.153550;}

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
        $("#checkpoint-latitude").blur();
        marker.setPosition(geometry);
    });

    google.maps.event.addListener(map, 'click', function (event) {
        var geometry = event.latLng;
        $("#checkpoint-latitude").prop('value', geometry.lat());
        $("#checkpoint-longitude").prop('value', geometry.lng());
        $("#checkpoint-latitude").blur();
        marker.setPosition(geometry);
    });

    marker.setPosition({lat: lat, lng: lng});
}
