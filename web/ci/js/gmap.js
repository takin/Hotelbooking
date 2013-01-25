var geocoder = null;
var custom_gmap = null;
var InfoW = {
		map: null,
		infoWindow: null
};

InfoW.closeInfoWindow = function() {
	InfoW.infoWindow.close();
};

InfoW.openInfoWindow = function(marker,content) {
	var markerLatLng = marker.getPosition();
	InfoW.infoWindow.setContent([content].join(''));
	InfoW.infoWindow.open(InfoW.map, marker);
};

InfoW.init = function(map) {
	// Create single instance of a Google Map.
	InfoW.map = map;
	InfoW.infoWindow = new google.maps.InfoWindow({
//		maxWidth: 100
	});
//	InfoW.map.setZoom(12);
	google.maps.event.addListener(InfoW.map, 'click', InfoW.closeInfoWindow);
};
function Custom_gmap(div, title, lat, lng, address)
{
	this.lat    = lat;
	this.lng    = lng;
	this.div    = div;
	this.address = address;
	this.title  = title;

	this.append_gscript = function(lang, callback) {
		var script = document.createElement("script");
		script.type = "text/javascript";
		script.src = "https://maps.google.com/maps/api/js?sensor=false&language="+lang+"&callback="+callback;
		document.body.appendChild(script);
	};
}
Custom_gmap.prototype.map = null; 
Custom_gmap.prototype.div = "map_canvas"; 

function single_map_init(map_div_id, title, lat, lng, address ) {
	custom_gmap = new Custom_gmap(map_div_id, title, lat, lng, address);
	custom_gmap.append_gscript(site.lang,'single_map');
}

function single_map() {
	geocoder = new google.maps.Geocoder();

	custom_gmap.latlng = new google.maps.LatLng(custom_gmap.lat, custom_gmap.lng);

	var myOptions = {
			zoom: 14,
			center: custom_gmap.latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	custom_gmap.map = new google.maps.Map(document.getElementById(custom_gmap.div), myOptions);

	custom_gmap.marker_image = new google.maps.MarkerImage(site.make_url('images/map-marker.png'),
			new google.maps.Size(28, 28),
			new google.maps.Point(0,0),
			new google.maps.Point(0, 29));

	custom_gmap.marker_shadow = new google.maps.MarkerImage(site.make_url('images/map-marker-shadow.png'),
			new google.maps.Size(43, 28),
			new google.maps.Point(0,0),
			new google.maps.Point(0, 28));

	custom_gmap.add_marker = function(latlng){
		var marker = new google.maps.Marker({
			position: latlng,
			map: custom_gmap.map,
			icon: custom_gmap.marker_image,
			shadow: custom_gmap.marker_shadow
		});
		InfoW.init(custom_gmap.map);
		
//		InfoW.openInfoWindow(marker,custom_gmap.title);
		google.maps.event.addListener(marker, 'click', function() {
	        InfoW.openInfoWindow(marker,'<div>'+custom_gmap.title+'<div>');
	      });
	};

	if(custom_gmap.address)
	{
		codeAddress();
		custom_gmap.map.setCenter(custom_gmap.latlng);
		custom_gmap.add_marker(custom_gmap.latlng);
	}
	else
	{
		custom_gmap.add_marker(custom_gmap.latlng);
	}
}

function codeAddress() {

	if (geocoder) {
		geocoder.geocode( { 'address': custom_gmap.address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				custom_gmap.latlng = results[0].geometry.location;
			}
		});
	}
}
