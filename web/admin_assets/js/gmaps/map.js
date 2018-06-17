Array.prototype.remove = function(from, to) {
	var rest = this.slice((to || from) + 1 || this.length);
	this.length = from < 0 ? this.length + from : from;
	return this.push.apply(this, rest);
};

var JSONfn;
if (!JSONfn) {
	JSONfn = {};
}

(function () {
	JSONfn.stringify = function(obj) {
		return JSON.stringify(obj,function(key, value){
			return (typeof value === 'function' ) ? value.toString() : value;
		});
	}

	JSONfn.parse = function(str) {
		return JSON.parse(str,function(key, value){
			if(typeof value != 'string') return value;
			return ( value.substring(0,8) == 'function') ? eval('('+value+')') : value;
		});
	}
}());

var fromadmin = true;

function window_location_origin() {
    if (!window.location.origin) {
        return window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
    }
    else
    {
        return window.location.origin;
    }
}


var cmarksMarks = new Array();
var orcmarksMarks = new Array();


var theMap;
var theMc;

var styles = [[{
	fontFamily:"'Open Sans',Arial",
	fontStyle:"normal",
	url: '../images/mark_cluster.png',
	height: 40,
	width: 40,
	//anchor: [16, 0],
	textColor: '#fff',
	textSize: 18,
}]];

function initialize(zoom){
    if ($('#map_canvas').length) {
		cmarksMarks = new Array();
        //var bounds = new google.maps.LatLngBounds();

        // 55.78025505881136, 37.6940100402832
        //48.484841 , 35.063889

		var midl = 0;
		var midg = 0;
		

		
       
		var cnt = 0;
		if (cmarks.length > 0) {
			$.each(cmarks, function (index, value) {
				if(value && !value.nocalc){
					cnt++;
					midl+=parseFloat(value.cmap_l);
					midg+=parseFloat(value.cmap_g);
			   }
            });
			midl = midl/cnt;
			midg = midg/cnt;
		}
		
		var secheltLoc = new google.maps.LatLng(midl, midg);

        var isiIpad = navigator.userAgent.match(/iPhone|iPad|iPod|Android|BlackBerry|IEMobile/i) !== null;
        var d = (!isiIpad) ? true : false;
		
		if(!zoom)
		zoom = 6;
		if($('#map_canvas').attr('defzoom')){
			zoom = parseInt($('#map_canvas').attr('defzoom'));
		}
		
        var myMapOptions = {
            scrollwheel: true
            , zoom: zoom
            , center: secheltLoc
            , minZoom: 2
            , maxZoom: 20
            , draggable: d
            , mapTypeId: google.maps.MapTypeId.ROADMAP
			,zoomControl: true
			,zoomControlOptions: {
				position: google.maps.ControlPosition.RIGHT_CENTER
			}
			,streetViewControl: false
        };      

        theMap = new google.maps.Map(document.getElementById("map_canvas"), myMapOptions);
		
		
		
		if (cmarks.length > 0) {
            $.each(cmarks, function (index, value) {
                addMark(index,value);
            });
			orcmarksMarks = cmarksMarks;
			
			google.maps.event.addListener(theMap, "zoom_changed", function (e) {
			
				if(!fromadmin){
					getPathes();					
					hidePlanPathes();
				}
				
				var prevzoom = $('#map_canvas').attr('prevzoom');
				if(!prevzoom){
					prevzoom = 0;
				}
				$('#map_canvas').addClass('zoom'+theMap.getZoom());
				$('#map_canvas').attr('prevzoom', theMap.getZoom());
				$('#map_canvas').removeClass('zoom'+prevzoom);
				
			});
			
			
			/*clusters*/
			setTimeout(function () {
			var style = 0;
			
			theMc = new MarkerClusterer(theMap, cmarksMarks, {
					maxZoom: 19,
					//gridSize: size,
					styles: styles[style]
				});
				setTimeout(function(){
					$('#map_canvas').removeClass('op0');
					blockmf = false;
				},250);
			},10);
			/*clusters*/
			
			
        }

		
		

        //google.maps.event.addDomListener(window, 'resize', function () {
            //cmap_g = cmap_g_tmp;
            /*
             if($(window).height() > 950){  cmap_l = 55.760819;  }
             else{ cmap_l = cmap_l_tmp; }
             */
            //var secheltLoc = new google.maps.LatLng(cmap_l,cmap_g);
            //theMap.setCenter(secheltLoc);
        //});
        /*google.maps.event.addDomListener(theMap, 'click', function (e) {
            if($('.map-popup-item:visible').length){
                if(!$('.map-popup-item:hover').length){
                    preventDefault(e);
                    $('.map-popup-item:visible').parent().find('.map-marker').show();
                    $('.map-popup-item:visible').hide();
                }
            }
            //cmap_g = cmap_g_tmp;
            
             //if($(window).height() > 950){  cmap_l = 55.760819;  }
             //else{ cmap_l = cmap_l_tmp; }
             
            //var secheltLoc = new google.maps.LatLng(cmap_l,cmap_g);
            //theMap.setCenter(secheltLoc);
        });*/
    }
    return true;
}

function addMark(index,value,add){
	if(!value)return;
	var label_view = ''+value.label+'';
	var position = new google.maps.LatLng(value.cmap_l, value.cmap_g);
	
	//bounds.extend(position);
	setTimeout(function () {
		  cmarks[index].marker =  new MarkerWithLabel({
			position: position,
			draggable: value.draggable,
			map: theMap,
			icon: window_location_origin(),
			labelText: label_view,
			labelAnchor: new google.maps.Point(0, 0),
			labelClass: "labels labels-"+index+" transition",
			labelVisible: true
		});
		//cmarksTypes[value.type][index] = cmarks[index].marker;
		cmarksMarks.push(cmarks[index].marker);
		//cmarksTypes.type

		//theMap.fitBounds(bounds);
	}, 0);
}

function removeMarker(el){
	el.each(function(){
		var cl = $(this).attr('class');
		cl = cl.split('labels-');
		cl = cl[1].split(' ');
		var ind = cl[0];
		delete(cmarks[ind]);
	});
}

function updateMarker(el){
	el.each(function(){
		var cl = $(this).attr('class');
		cl = cl.split('labels-');
		cl = cl[1].split(' ');
		var ind = cl[0];
		cmarks[ind].marker.labelClass = $(this).attr('class');
		cmarks[ind].marker.labelText = $(this).html();
	});
}

function setMapCenter(cmap_l, cmap_g){
	theMap.setZoom(18);
	var secheltLoc = new google.maps.LatLng(cmap_l,cmap_g)
	theMap.setCenter(secheltLoc);
}

function hidePlanPathes(){
	var plan = parseInt($('input[name="filter_type"]:checked').val());
	
	
	var nav = new Array();
	var sel = $('.filters_wrap .one.act');
	if (!sel.length) {
		sel = $('.filters_wrap .one');
	}
	sel.each(function () {
		var type = parseInt($(this).attr('type'));
		nav.push(type);
	});
	var toremove = [];
	
	 
	$.each(pathes, function(index, path){
	
		if(plan==pathes[index].data.plan && $.inArray(pathes[index].data.org_type, nav) != -1){
			
			if(pathes[index].startmarker && !pathes[index].startmarker.starter)
				pathes[index].startmarker.setMap(theMap);
			if(pathes[index].finishmarker && !pathes[index].finishmarker.starter)
				pathes[index].finishmarker.setMap(theMap);
			pathes[index].setMap(theMap);
					
		}else{
		
			if(pathes[index].startmarker && !pathes[index].startmarker.starter)
				pathes[index].startmarker.setMap(null);
			if(pathes[index].finishmarker && !pathes[index].finishmarker.starter)
				pathes[index].finishmarker.setMap(null);
			pathes[index].setMap(null);
			
		}
		
	});
}

function initializePathes(){
	var data;
	$.each(pathes, function(index, path){
	
		data = pathes[index].data;
		
		pathes[index] = new google.maps.Polyline(path);
		pathes[index].setMap(theMap);
		
		pathes[index].index = index;
		pathes[index].addListener("click", function (e) {
			var ind = this.index;
			var targ = $('.data_path[data-path="'+ind+'"] .map-marker').click();
		});
		pathes[index].data = data;
		
		
		pathes[index].startmarker = {
			position: new google.maps.LatLng(data.firstlat, data.firstlng),
			draggable: false,
			map: theMap,
			icon: window_location_origin(),
			labelText: '<div class="dni data_path" data-path="'+index+'">'+data.label_view+'</div>',
			labelAnchor: new google.maps.Point(0, 0),
			labelClass: "contract_marker contract_marker_"+data.contract_type+" transition",
			labelVisible: true,
			starter:true
		};
		
		setTimeout(function () {
			pathes[index].startmarker = new MarkerWithLabel(pathes[index].startmarker);
			pathes[index].startmarker.starter = false;
		},0);
		
		
		var label_view = '';
		pathes[index].finishmarker = {
			position: new google.maps.LatLng(data.lastlat, data.lastlng),
			draggable: false,
			map: theMap,
			icon: window_location_origin(),
			labelText: '<div class="dni data_path" data-path="'+index+'">'+data.label_view+'</div>',
			labelAnchor: new google.maps.Point(0, 0),
			labelClass: "contract_marker contract_marker_"+data.contract_type+" transition",
			labelVisible: true,
			starter:true
		};
		setTimeout(function () {
			pathes[index].finishmarker = new MarkerWithLabel(pathes[index].finishmarker);
			pathes[index].finishmarker.starter = false;
		},0);
		
	});
}
var lat0_prev = 0;//topleft
var lng0_prev = 0;//topleft
var lat1_prev = 0;//bottomright
var lng1_prev = 0;//bottomright
var old_pathes = [];

function getPathes(){
	var zoom = theMap.getZoom();
	
	if(zoom>13){
		var lat0 = theMap.getCenter().lat() - (theMap.getBounds().getNorthEast().lat() - theMap.getCenter().lat());//topleft
		var lng0 = theMap.getCenter().lng() - (theMap.getBounds().getNorthEast().lng() - theMap.getCenter().lng());//topleft
		var lat1 = theMap.getCenter().lat() + (theMap.getBounds().getSouthWest().lat() - theMap.getCenter().lat());//bottomright
		var lng1 = theMap.getCenter().lat() + (theMap.getBounds().getSouthWest().lng() - theMap.getCenter().lng());//bottomright
		
		if(!lat0_prev){
			lat0_prev = lat0;
			if(old_pathes.length){
				$.each(pathes, function(i,v){
					v.setMap(null);
				});
				pathes = old_pathes;
				initializePathes();
				hidePlanPathes();
			}else{
				$.get('getpathes', {lat0:lat0, lng0:lng0, lat1:lat1, lng1:lng1}, function(data){
					$.each(pathes, function(i,v){
						v.setMap(null);
					});
					pathes = [];
					$('.forscripts').html(data);
					initializePathes();
					old_pathes = pathes;
					hidePlanPathes();
					setTimeout(function(){
						hidePlanPathes();
					},1000);
				});
			}
		}
	}else{
		lat0_prev = 0;
		$.each(pathes, function(i,v){
			v.startmarker.setMap(null);
			v.finishmarker.setMap(null);
			v.setMap(null);
		});
		pathes = [];
	}
}


function initGmaps(){
	setTimeout(function(){
		if ($('#map_wrapper').length) {
			var zoom = 9;
			if($('.cmarks_lat').length && !$('.cmarks_lat').val()){
				zoom = 1;
			}
			initialize(zoom);
			initAutocomplete();
			initMapPath();
		}
		if($('.cmarks_lat').length && $('.cmarks_lat').parents('form').length){
			$('.cmarks_lat').parents('form').submit(function(){
				setCmarksCoords();
			});
		}
	},0);
}



var orcmarks = new Array();
var cmarksTypes = new Array();
var orcmarksTypes = new Array();
var onemark = null;
if(typeof cmarks=='undefined')cmarks = [];
orcmarks = JSON.stringify(cmarks, null);


function initAutocomplete() {
	var map = theMap;

	// Create the search box and link it to the UI element.
	var input = document.getElementById('pac-input');
	var searchBox = new google.maps.places.SearchBox(input);
	theMap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

	// Bias the SearchBox results towards current map's viewport.
	theMap.addListener('bounds_changed', function() {
		searchBox.setBounds(theMap.getBounds());
	});

	var markers = [];
	// [START region_getplaces]
	// Listen for the event fired when the user selects a prediction and retrieve
	// more details for that place.
	searchBox.addListener('places_changed', function() {
		var places = searchBox.getPlaces();

		if (places.length == 0) {
			return;
		}

		// Clear out the old markers.
		markers.forEach(function(marker) {
			marker.setMap(null);
		});
		markers = [];

		// For each place, get the icon, name and location.
		var bounds = new google.maps.LatLngBounds();
		places.forEach(function(place) {
			var icon = {
				url: place.icon,
				size: new google.maps.Size(71, 71),
				origin: new google.maps.Point(0, 0),
				anchor: new google.maps.Point(17, 34),
				scaledSize: new google.maps.Size(25, 25)
			};

			// Create a marker for each place.
			markers.push(new google.maps.Marker({
				map: theMap,
				icon: icon,
				title: place.name,
				position: place.geometry.location
			}));

			if (place.geometry.viewport) {
				// Only geocodes have viewport.
				bounds.union(place.geometry.viewport);
			} else {
				bounds.extend(place.geometry.location);
			}
		});
		theMap.fitBounds(bounds);
	});
	// [END region_getplaces]
}
var path_selection_step = 0;
function initMapPath(){

	if($('#contracts-path_results').val()){
		results = JSONfn.parse($('#contracts-path_results').val());
	}else{
		$('#contracts-path_results').val(JSONfn.stringify(results));
	}

	theMap.addListener('click', function(event) {
		var lat = event.latLng.lat();
		var lng = event.latLng.lng();
		if(path_selection_step){

			$('#path_status').data('lat'+path_selection_step, lat);
			$('#path_status').data('lng'+path_selection_step, lng);

			path_selection_status(path_selection_step+1);
			var title = 'РЎС‚Р°СЂС‚';
			if(current_path_markers.length%2!=0){
				title = 'Р¤РёРЅРёС€';
			}
			current_path_markers.push(new google.maps.Marker({
				position: {lat: lat, lng: lng},
				map: theMap,
				draggable: true,
				title : title
			}));

		}

		if(setBaseMarkerInit){
			setBaseMarkerInit = false;
			theMap.setOptions({ draggableCursor: 'url(http://maps.google.com/mapfiles/openhand.cur), move' });
			cmarks[0].marker.setPosition({lat: lat, lng: lng});
		}

	});

	$.each(results, function(i,v){
		setDirection(v);
	});

	updateAddresses();

}

var path_markers = [];
var current_path_markers = [];
var directions_display = [];
var results = [];
var dirService = new google.maps.DirectionsService();

function path_selection_status(status){
	theMap.setOptions({ draggableCursor: 'crosshair' });
	if(status==3){
		status = 0;
		theMap.setOptions({ draggableCursor: 'url(http://maps.google.com/mapfiles/openhand.cur), move' });
		/*setting path*/



		// highlight a street
		var request = {
			origin: $('#path_status').data('lat1') + "," + $('#path_status').data('lng1'),
			destination: $('#path_status').data('lat2') + "," + $('#path_status').data('lng2'),
			travelMode: google.maps.TravelMode.DRIVING
		};

		dirService.route(request, function(result, status) {
			if (status == google.maps.DirectionsStatus.OK) {
				setDirection(result);
			}
		});
		/*setting path*/

	}
	path_selection_step = status;
	$('#path_status').html($('#path_status').data('status'+status));
}

function setDirection(result, rid){
	var forpageload = false;

	var dirRenderer = new google.maps.DirectionsRenderer({suppressMarkers: true, draggable: false});
	dirRenderer.setMap(theMap);

	dirRenderer.setDirections(result);



	var lastpath = result.routes[0].overview_path.length-1;

	var latlng = result.routes[0].overview_path[0];

	if(current_path_markers[0]){
		current_path_markers[0].setPosition(latlng);
	}else{//for page load
		forpageload = true;
		current_path_markers.push(new google.maps.Marker({
			position: {lat: latlng.lat, lng: latlng.lng},
			map: theMap,
			draggable: true,
			title : 'РЎС‚Р°СЂС‚'
		}));
		current_path_markers[0].address = result.startaddress;
	}


	var latlng = result.routes[0].overview_path[lastpath];
	if(current_path_markers[1]){
		current_path_markers[1].setPosition(latlng);
	}else{//for page load
		forpageload = true;
		current_path_markers.push(new google.maps.Marker({
			position: {lat: latlng.lat, lng: latlng.lng},
			map: theMap,
			draggable: true,
			title : 'Р¤РёРЅРёС€'
		}));
		current_path_markers[1].address = result.finishaddress;
	}

	current_path_markers[0].key = path_markers.length;
	current_path_markers[1].key = path_markers.length+1;



	current_path_markers[0].addListener('dragend', function(event){dragendStartFinish(event,this)});
	current_path_markers[1].addListener('dragend', function(event){dragendStartFinish(event,this)});

	path_markers.push(current_path_markers[0]);
	path_markers.push(current_path_markers[1]);

	var k1 = path_markers.length-2;
	var k2 = path_markers.length-1;

	if(forpageload){

	}else{
		$.getJSON('https://maps.googleapis.com/maps/api/geocode/json?latlng='+path_markers[k1].getPosition().lat()+','+path_markers[k1].getPosition().lng(),function(data){
			path_markers[k1].address = data.results[0].address_components[1].long_name + ', ' + data.results[0].address_components[0].long_name;
			updateAddresses();
		});

		$.getJSON('https://maps.googleapis.com/maps/api/geocode/json?latlng='+path_markers[k2].getPosition().lat()+','+path_markers[k2].getPosition().lng(),function(data){
			path_markers[k2].address = data.results[0].address_components[1].long_name + ', ' + data.results[0].address_components[0].long_name;
			updateAddresses();
		});
	}

	current_path_markers = [];

	dirRenderer.key = directions_display.length;
	directions_display.push(dirRenderer);

	if(!forpageload){
		results.push(result);
	}

	directions_display[directions_display.length-1].addListener('directions_changed', function() {
		var dir = this.getDirections();
		//console.log(results[this.key]);
		dir.routes[0].legs[0].via_waypoints = [];
		dir.routes[0].legs[0].start_location = {lat:dir.routes[0].legs[0].start_location.lat(), lng:dir.routes[0].legs[0].start_location.lng()};
		dir.routes[0].legs[0].end_location = {lat:dir.routes[0].legs[0].end_location.lat(), lng:dir.routes[0].legs[0].end_location.lng()};
		results[this.key] = dir;
		//console.log(results[this.key].routes[0].legs[0]);
		//results[this.key] = JSONfn.parse(JSONfn.stringify(dir));
		//console.log(results[this.key]);
		//updateAddresses();
	});

	if(!forpageload){
		updateAddresses();
	}
}

function chcmarks(event, marker){
	var lat = event.latLng.lat();
	var lng = event.latLng.lng();
	$('#contracts-marker_lat_lng').val(lat+','+lng);
}

function dragendStartFinish(event, marker) {
	var lat = event.latLng.lat();
	var lng = event.latLng.lng();
	var pathID = 0;

	var origin = '0,0';
	var destination = '0,0';

	var startMarkerKey = 0;
	var finishMarkerKey = 0;

	if(marker.key%2==0){//editing start point
		startMarkerKey = marker.key;
		finishMarkerKey = marker.key+1;

		pathID = marker.key/2;
		origin = lat+','+lng;
		destination = path_markers[marker.key+1].getPosition().lat() + ',' + path_markers[marker.key+1].getPosition().lng();
		k1 = marker.key+1;
		$.getJSON('https://maps.googleapis.com/maps/api/geocode/json?latlng='+path_markers[k1].getPosition().lat()+','+path_markers[k1].getPosition().lng(),function(data){
			path_markers[k1].address = data.results[0].address_components[1].long_name + ', ' + data.results[0].address_components[0].long_name;
			updateAddresses();
		});
	}else{//editing finish point
		pathID = (marker.key-1)/2;

		startMarkerKey = marker.key-1;
		finishMarkerKey = marker.key;

		origin = path_markers[marker.key-1].getPosition().lat() + ',' + path_markers[marker.key-1].getPosition().lng();
		destination = lat+','+lng;
		k1 = marker.key-1;
		$.getJSON('https://maps.googleapis.com/maps/api/geocode/json?latlng='+path_markers[k1].getPosition().lat()+','+path_markers[k1].getPosition().lng(),function(data){
			path_markers[k1].address = data.results[0].address_components[1].long_name + ', ' + data.results[0].address_components[0].long_name;
			updateAddresses();
		});
	}




	var request = {
		origin: origin,
		destination: destination,
		travelMode: google.maps.TravelMode.DRIVING
	};

	dirService.route(request, function(result, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			directions_display[pathID].setDirections(result);
			results[pathID] = result;

			var lastpath = result.routes[0].overview_path.length-1;

			var latlng = result.routes[0].overview_path[0];
			path_markers[startMarkerKey].setPosition(latlng);

			var latlng = result.routes[0].overview_path[lastpath];
			path_markers[finishMarkerKey].setPosition(latlng);

			$.getJSON('https://maps.googleapis.com/maps/api/geocode/json?latlng='+path_markers[startMarkerKey].getPosition().lat()+','+path_markers[startMarkerKey].getPosition().lng(),function(data){
				path_markers[startMarkerKey].address = data.results[0].address_components[1].long_name + ', ' + data.results[0].address_components[0].long_name;
				updateAddresses();
			});

			$.getJSON('https://maps.googleapis.com/maps/api/geocode/json?latlng='+path_markers[finishMarkerKey].getPosition().lat()+','+path_markers[finishMarkerKey].getPosition().lng(),function(data){
				path_markers[finishMarkerKey].address = data.results[0].address_components[1].long_name + ', ' + data.results[0].address_components[0].long_name;
				updateAddresses();
			});

		}
	});

	updateAddresses();
}

var interval;
function updateAddresses(){
	$('#addresses').html('');
	var newresults = [];
	$.each(results, function(i, v){
		if(v){
			console.log(v);
			var m1 = path_markers[i*2];
			var m2 = path_markers[i*2+1];
			results[i].startaddress = m1.address;
			results[i].finishaddress = m2.address;
			var txt = '<b>'+m1.address+'</b> &#8594; <b>'+m2.address+'</b>';
			$('#addresses').append('<div class="one">'+txt+' (<a href="javascript:void(0)" onclick="theMap.setCenter(path_markers['+(i*2)+'].getPosition())">РїСЂРѕСЃРјРѕС‚СЂРµС‚СЊ</a> | <a href="javascript:void(0)" onclick="deletePath('+i+')">СѓРґР°Р»РёС‚СЊ</a>)</div>');
			newresults.push(results[i]);
		}
	});

	interval = setInterval(function(){
		if(cmarks[0].marker){
			clearInterval(interval);
			$('#contracts-marker_lat_lng').val(cmarks[0].marker.getPosition().lat() + ',' + cmarks[0].marker.getPosition().lng());
			$('#contracts-path_results').val(JSONfn.stringify(results));
		}
	},500);




}

function setCmarksCoords(){
	$.each(cmarks, function(i,v){
		if(cmarks[i].marker){
			var lat = cmarks[i].marker.getPosition().lat();
			var lng = cmarks[i].marker.getPosition().lng();
			$('.cmarks_lat').val(lat);
			$('.cmarks_lng').val(lng);
		}
	});
}

var setBaseMarkerInit = false;
function setBaseMarker(){
	setBaseMarkerInit = true;
	theMap.setOptions({ draggableCursor: 'crosshair' });
}

function deletePath(pathID){
	path_markers[pathID*2].setMap(null);
	path_markers[pathID*2+1].setMap(null);
	//delete(path_markers[pathID*2]);
	//delete(path_markers[pathID*2+1]);
	path_markers.remove(pathID*2);
	path_markers.remove(pathID*2+1);


	directions_display[pathID].setMap(null);
	//delete(directions_display[pathID]);
	directions_display.remove(pathID);

	//delete(results[pathID]);
	results.remove(pathID);

	updateAddresses();
}