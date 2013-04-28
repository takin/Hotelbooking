// Function GoogleMap() is a class constructor for the implementation of a Google map widget. 
// GroupCheckBoxes() requires an unordered list structure, with the first list entry being the group 
// checkbox and the remaining entries being the checkboxes controlled by the group. Each list entry 
// must contain an image tag that will be used to display the state of the checkbox. 
// 
// @param() 
// 
// @return N/A 
//
// @author Louis-Michel Raynauld
//
// TODO support multiple infowindow and multiple map in one document
// 
function GoogleMap(map_div_id, lang , default_lat, default_lng, default_zoom) {

	this.map_div = document.getElementById(map_div_id);
	
	this.map_lang   = lang || 'en';
	
	this.default_lat   = default_lat || 0;
	this.default_lng   = default_lng || 0;
	this.default_zoom  = default_zoom || 8;
	
	window.gmap       = null;
        window.cityCircle = null;
	this.markers    = Array();
	this.gbounds    = null;
	
	this.marker_id_to_focus  = -1;
	
	this.glib_loaded = false;
	
	//info window should be global too bad!
	window.gInfoWin   = null;

} // end GoogleMap() constructor 

// Function init() is a member function to initialize the Google Map object.
// Make sure this is run after google map script has loaded
// return N/A 
// 
GoogleMap.prototype.init = function() { 
	
	this.map_div.style.display = "block";
	this.map_div.style.width = "100%";
	this.map_div.style.height = "400px";

    if (this.map_div.id === "filter_map_rightSide"){
        this.map_div.style.height = "100%";
//        this.default_zoom = 10;
    }
        	
    var myOptions = {
	      zoom:      this.default_zoom,
	      center:    new google.maps.LatLng(this.default_lat, this.default_lng),
	      mapTypeId: google.maps.MapTypeId.ROADMAP
	    };
    
        window.gmap    = new google.maps.Map(this.map_div, myOptions);
	this.gbounds = new google.maps.LatLngBounds();
	
	//add infowindow to map
	this.initInfoWin();

	this.drawMarkers();
    
	this.marker_focus();
	
	if((this.marker_id_to_focus < 0) && !this.gbounds.isEmpty())
	{
		window.gmap.setCenter(this.gbounds.getCenter());
	    window.gmap.fitBounds(this.gbounds);
	}	
	
     // first get the property number
        var property_number = this.map_div.id.substr(this.map_div.id.lastIndexOf("_") + 1);

        // check if there is any radio button
        if ($("#frmDistrict_"+property_number+" input:radio:first").length > 0)
            {
                 // make first district checked
                $("#frmDistrict_"+property_number+" input:radio:first").attr('checked', true);
            }
       

        // check if there is a district radio button and checked
        // if yes call the district function to show district boundries
        if($("#frmDistrict_"+property_number+" input:radio:checked").length > 0)
            {
              var district_um_ids =   $("#frmDistrict_"+property_number+" input:radio:checked").val();
              // call the function to show the district
             this.changeDistrictLayer(district_um_ids);
            }
           else
            {
                 $("#frmDistrict_"+property_number).hide();
            }
            
            // Start  Landmark Shows on map
               // check if there is any radio button
        if ($("#divLandmark_"+property_number+" input:radio:first").length > 0)
            {
                 // make first district checked
                $("#divLandmark_"+property_number+" input:radio:first").attr('checked', true);
            }
       

        // check if there is a district radio button and checked
        // if yes call the district function to show district boundries
        if($("#divLandmark_"+property_number+" input:radio:checked").length > 0)
            {
              var landmark_latlng =   $("#divLandmark_"+property_number+" input:radio:checked").val();
              // call the function to show the district
             this.changeLandmarkLayer(landmark_latlng);
            }
           else
            {
                 $("#divLandmark_"+property_number).hide();
            }
          // End  Landmark Shows on map 
}; // end init() 

GoogleMap.prototype.clearMapDiv = function()
{
	var parentDiv = this.map_div.parentNode;
	parentDiv.removeChild(this.map_div);
	this.map_div.setAttribute("style","");
	this.map_div.innerHTML = "";
	parentDiv.appendChild(this.map_div);
};
GoogleMap.prototype.setMapDivId = function(map_div_id)
{
	this.map_div = document.getElementById(map_div_id);
};
GoogleMap.prototype.setFocusMarkerID = function(id)
{
	this.marker_id_to_focus = id;
};
GoogleMap.prototype.marker_focus = function()
{
	if(this.marker_id_to_focus > -1)
	{
		window.gmap.setZoom(14);
		window.gmap.setCenter(this.markers[this.marker_id_to_focus].gmarker.position);
		this.openInfoWindow(this.markers[this.marker_id_to_focus].gmarker,this.markers[this.marker_id_to_focus].gmarker.custom_content);
	}
};
GoogleMap.prototype.drawMap = function()
{
	  var  that   = this;
	
  var script_id = "google_map_api_script";
    
    if ($("#" + script_id).length <= 0)
    {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.id = script_id;
        script.src = "https://maps.google.com/maps/api/js?sensor=false&language=" + this.map_lang + "&callback=gmap_start";

        //Create callback function that must be global
        window.gmap_start = function() {
            that.init();
        };

        document.body.appendChild(script);
    }
    else
    {
        this.init();
    }
};

GoogleMap.prototype.addMarker = function (index, lat, lng, title, content) //, image, iconshadow)
{
	var marker = {
                      title: title,
		      lat: lat,
		      lng: lng,
		      content: content,
		      gmarker: null
		    };
	this.markers[index] = marker ;
};
GoogleMap.prototype.clearMap = function () //, image, iconshadow)
{
	this.clearMarkers();
	this.gbounds = null;
};
GoogleMap.prototype.clearMarkers = function () //, image, iconshadow)
{
	this.markers = Array();
	this.marker_id_to_focus  = -1;
};

GoogleMap.prototype.drawMarkers = function () //, image, iconshadow)
{
	var that = this;

	//TODO support custom image in addMarker function
	for (var i in this.markers) {
		//initialize icon of marker
		var image = new google.maps.MarkerImage("http://"+window.location.host+'/images/map-marker.png',
			        new google.maps.Size(28, 28),
			        new google.maps.Point(0,0),
			        new google.maps.Point(0, 29));
		
		//Add marker to map
		var gmarker = new google.maps.Marker({
	        position: new google.maps.LatLng(this.markers[i].lat,this.markers[i].lng), 
	        map: window.gmap,
	        title:this.markers[i].title,
	        icon: image,
	        custom_content: this.markers[i].content
	        
	    }); 
		
		this.markers[i].gmarker = gmarker;
		
		//On marker click, open info window and set marker content
		google.maps.event.addListener(gmarker, 'click', function() {
			that.openInfoWindow(this,this.custom_content);
	      });
		
		this.gbounds.extend(gmarker.position);
	}
};
GoogleMap.prototype.removeMap = function () //, image, iconshadow)
{
	this.map_div.style.display = "none";
};

GoogleMap.prototype.closeInfoWindow = function() {
	window.gInfoWin.close();
};

GoogleMap.prototype.openInfoWindow = function(marker,content) {
//	var markerLatLng = marker.getPosition();
	
	window.gInfoWin.setContent([content].join(''));
	window.gInfoWin.open(window.gmap, marker);
};

GoogleMap.prototype.initInfoWin = function() {
	// Create single instance of a Google Map.
	window.gInfoWin = new google.maps.InfoWindow({
//		maxWidth: 300
	});
	google.maps.event.addListener(window.gmap, 'click', function () {window.gInfoWin.close();});
};
GoogleMap.prototype.changeDistrictLayer = function(district_um_ids){

    // working with mapinfulence
    // Initialize Mapfluence with your API key.
    MF.initialize({
        apiKey: urbanmapping_key
    });
    // remove any old districts
    window.gmap.overlayMapTypes.setAt(0, null);

    if ($.isArray(district_um_ids)) {

        // loop through districts um_ids
        var counter;
        for (counter = 0; counter < district_um_ids.length; ++counter) {
            this.addDistrictsBorder(MF, district_um_ids[counter], counter);
        }
    }
    else {
        //    check zoom level and change it according to needed
        // just change zoom if only one district to be shown
        if (window.gmap.getZoom() > 12) {
            // change map Zoom 
            window.gmap.setZoom(12);
        }
        this.addDistrictsBorder(MF, district_um_ids, 0);
    }

};
GoogleMap.prototype.addDistrictsBorder = function(MF, pDistricts_umIds, counter)
{
//    check zoom level and change it according to needed
    if ( window.gmap.getZoom() > 12 ) {
        // change map Zoom 
        window.gmap.setZoom(12);
    } 
 
    // do something with `pDistricts_umIds[counter]`

    var filter = MF.filter.Data({
        column: 'umi.neighborhoods.attributes.hood_id',
        operator: '=',
        value: parseInt(pDistricts_umIds)

    });

    var hoodsLayer = MF.layer.tile.Simple({
        from: 'umi.neighborhoods.geometry',
        style: {
            color: 'feba02'
        },
        border: {
            color: 'black',
            size: 1.0
        },
        where: filter,
        opacity: .40
    });

    // Create the Mapfluence adapter for Google Maps
    var googleAdapter = MF.map.google.Adapter();

    // Adapt a Mapfluence layer for use with the Google Maps API
    var adaptedLayer = googleAdapter.adaptLayer(hoodsLayer);

    // Overlay the Mapfluence layer
//    map.overlayMapTypes.insertAt(0, adaptedLayer);
    window.gmap.overlayMapTypes.setAt((counter + 1), adaptedLayer);

};
GoogleMap.prototype.changeLandmarkLayer = function(landmark_LatLng) {

    if (window.cityCircle !== null)
    {
        window.cityCircle.setMap(null);
    }

    if ($.isArray(landmark_LatLng)) {

        // loop through districts um_ids
        var counter;
        for (counter = 0; counter < landmark_LatLng.length; ++counter) {
            this.addLandmarkLayer(landmark_LatLng[counter]);
        }
    }
    else {
        //    check zoom level and change it according to needed
        // just change zoom  if only one landmark to be shown
        if (window.gmap.getZoom() > 12) {
            // change map Zoom 
            window.gmap.setZoom(12);
        }
        this.addLandmarkLayer(landmark_LatLng);
    }

};
GoogleMap.prototype.addLandmarkLayer = function(landmark_LatLng) {
   
    var point = landmark_LatLng.split("###");
    var lat = point[0];
    var Lng = point[1];
        
//alert("lat="+lat+"::::Lng="+Lng+"::::");

    var citymap = {
        center: new google.maps.LatLng(lat, Lng)
    };
//var circle_color  = "#4E89C9";
var circle_color  = "#FF0000";

    var LandmarkOptions = {
        strokeColor: circle_color,
        strokeOpacity: 0.8,
        strokeWeight: 2,
//      fillColor: "#FF0000",
        fillColor: circle_color,
        fillOpacity: 0.35,
        map: window.gmap,
        center: citymap.center,
        radius: 2000
    };
    window.cityCircle = new google.maps.Circle(LandmarkOptions);

//landmark_marker_blue.png
var image = new google.maps.MarkerImage("http://"+window.location.host+'/images/map_landmark_marker_blue.png',
			        new google.maps.Size(28, 28),
			        new google.maps.Point(0,0),
			        new google.maps.Point(0, 29));
                                
	var gmarker = new google.maps.Marker({
	        position: new google.maps.LatLng(lat, Lng), 
	        map: window.gmap,
//	        title:this.markers[i].title,
	        icon: image	        
	    }); 
            
};