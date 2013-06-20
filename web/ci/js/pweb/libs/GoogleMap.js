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

    this.map_lang = lang || 'en';

    this.default_lat = default_lat || 0;
    this.default_lng = default_lng || 0;
    this.default_zoom = default_zoom || 8;

    window.gmap = null;
    window.cityCircle = null;
    window.markers = Array();
    window.gmarkers = Array();
    this.gbounds = null;

    this.marker_id_to_focus = -1;

    //info window should be global too bad!
    window.gInfoWin = null;

} // end GoogleMap() constructor 

GoogleMap.markers = [];

// Function init() is a member function to initialize the Google Map object.
// Make sure this is run after google map script has loaded
// return N/A 
// 
GoogleMap.prototype.init = function() {
    if (!this.map_div) {
        return;
    }

    this.map_div.style.display = "block";
    this.map_div.style.width = "100%";
    this.map_div.style.height = "400px";

    if (this.map_div.id === "filter_map_rightSide") {
        this.map_div.style.height = "100%";
    }

    if (this.map_div.id === "city_side_map_container") {
        this.map_div.style.height = "280px";
        this.map_div.style.width = "auto";
    }

    if (this.map_div.className === "map_quickview") {
        this.map_div.style.height = "285px";
        this.map_div.style.width = "100%";
        this.map_div.style.width = "auto"; 
    }

    var myOptions = {
        zoom: this.default_zoom,
        center: new google.maps.LatLng(this.default_lat, this.default_lng),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    window.gmap = new google.maps.Map(this.map_div, myOptions);
    this.gbounds = new google.maps.LatLngBounds();

    // get map center before adding markers (map resize)
    var originalMapCenter = window.gmap.getCenter();

    //add infowindow to map
    this.initInfoWin();

    this.drawMarkers();

    this.marker_focus();

    if ((this.marker_id_to_focus < 0) && !this.gbounds.isEmpty())
    {
        window.gmap.setCenter(this.gbounds.getCenter());
        window.gmap.fitBounds(this.gbounds);
        if (this.map_div.id === "city_side_map_container") {
            if (window.gmap.getZoom() > 10)
            {
                window.gmap.setZoom(10);
            }
        }
    }
    // first get the property number
    var property_number = this.map_div.id.substr(this.map_div.id.lastIndexOf("_") + 1);

    // check if there is any radio button
    if ($("#frmDistrict_" + property_number + " input:radio:first").length > 0)
    {
        // make first district checked
        $("#frmDistrict_" + property_number + " input:radio:first").attr('checked', true);
    }


    // check if there is a district radio button and checked
    // if yes call the district function to show district boundries
    if ($("#frmDistrict_" + property_number + " input:radio:checked").length > 0)
    {
        var district_um_ids = $("#frmDistrict_" + property_number + " input:radio:checked").val();
        // call the function to show the district
        this.changeDistrictLayer(district_um_ids);
    }
    else
    {
        $("#frmDistrict_" + property_number).hide();
    }

    // Start  Landmark Shows on map
    // check if there is any radio button
    if ($("#divLandmark_" + property_number + " input:radio:first").length > 0)
    {
        // make first district checked
        $("#divLandmark_" + property_number + " input:radio:first").attr('checked', true);
    }


    // check if there is a district radio button and checked
    // if yes call the district function to show district boundries
    if ($("#divLandmark_" + property_number + " input:radio:checked").length > 0)
    {
        var landmark_latlng = $("#divLandmark_" + property_number + " input:radio:checked").val();
        // call the function to show the district
        this.changeLandmarkLayer(landmark_latlng);
    }
    else
    {
        $("#divLandmark_" + property_number).hide();
    }
    // End  Landmark Shows on map 
    
    if (this.map_div.id === "city_side_map_container") {
         google.maps.event.trigger(window.gmap, 'resize');
         window.gmap.panTo(originalMapCenter);
    }
   
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
    if (this.marker_id_to_focus > -1)
    {
        window.gmap.setZoom(14);
        window.gmap.setCenter(window.markers[this.marker_id_to_focus].gmarker.position);
        this.openInfoWindow(window.markers[this.marker_id_to_focus].gmarker, window.markers[this.marker_id_to_focus].gmarker.custom_content);
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

GoogleMap.prototype.addMarker = function (index, lat, lng, title, content, propertyNumber, propertyIndex)
{
    var marker = {
        title           :   title,
        lat             :   lat,
        lng             :   lng,
        content         :   content,
        propertyNumber  :   propertyNumber,
        propertyIndex   :   propertyIndex,
        gmarkvarer      :   null
    };
    window.markers[index] = marker;
};
GoogleMap.prototype.clearMap = function() //, image, iconshadow)
{
	this.clearMarkers();
	this.gbounds = null;
};
GoogleMap.prototype.clearMarkers = function() //, image, iconshadow)
{
    this.marker_id_to_focus = -1;

    if (window.gmarkers) {
        for (i in window.gmarkers) {
            window.gmarkers[i].setMap(null);
        }
        window.gmarkers.length = 0;
    }
    
    // Clear markers array
    window.markers = [];

};

GoogleMap.prototype.drawMarkers = function() //, image, iconshadow)
{
    // do this because sorting has problem in chrome
    // says getDiv on null in addMarkersToMap
    if ( window.gmap !== null ){
        this.clearMap();
        this.fillMakersArray();

        // draw markers 
        this.addMarkersToMap();
    }
};
GoogleMap.prototype.getItemsInPage = function() //, image, iconshadow)
{
    var result = [];
    if (window.gmap.getDiv().id === "filter_map_rightSide") {
        result = {
            property_list: $('#property_list').children(),
            start_from: 0
        };
    }
    else {
        // number of hostels to show per page
        var show_per_page = parseInt($('#show_per_page').val());
        // number of hostels currently shown
        var page_num = 0;
        if ($('#page_navigation .active_page').length > 0) {
            page_num = parseInt($('#page_navigation .active_page').attr("longdesc"));
        }

        // start hostel number like from 1 to 20
        var start_from = page_num * show_per_page;
        // end hostel number like from 1 to 20
        var end_on = start_from + show_per_page;
        result = {
//            property_list   : $('#property_list').children(":visible").slice(start_from, end_on),
            property_list   : $('#property_list').children(":visible"),
            start_from      : start_from,
            end_on          : end_on,
            page_num        : page_num,
            show_per_page   : show_per_page
        
        };
    }

    return result;
//    return $('#property_list').children().slice(start_from, end_on);
};
GoogleMap.prototype.fillMakersArray = function() 
{
    var that = this;
    // clear markers on the map
    // includes that.clearMarkers();
    that.clearMap();
    var resultInPage = that.getItemsInPage();

    var property_list = resultInPage.property_list;
    
    // for now just going to make markers always shows from 1 to 20
    // on side map
//    var start_from = resultInPage.start_from;
    var start_from = 0;
    
    $.each(property_list, function(index, value) {
// fill the window.markers array to be used to draw markers
        var property_number = $(value).attr("rel");
        var latitude = $("#input_geo_latitude_"+property_number).val();
        var longitude = $("#input_geo_longitude_"+property_number).val();
        //************* to solve problem when property is deleted from search
        var propertyIndex = parseInt($("#picture_number_"+property_number).html());

        if(resultInPage.page_num > 0){
            var real_property_number = $("#picture_number_"+property_number).html();
            // calculate propertyIndex to always start from 1
            propertyIndex = parseInt(parseInt(real_property_number) - ( parseInt(resultInPage.start_from) ));
        }
        
        var markerIndex = index +  parseInt(start_from); 
        that.addMarker( markerIndex 
                , latitude
                , longitude
                , $.trim($("#hostel_title_"+property_number).text())
                , $.trim($("#map_InfoWindow_"+property_number).html())
                , property_number
                , propertyIndex
                );   
    });

return window.markers;
};
GoogleMap.prototype.addMarkersToMap = function()
{       
     var comparePropertyLatLng = this.getComparePropertyLatlng();
     var compare_index = 0;
     
    if (window.markers.length < 1)
    {
        window.markers = this.fillMakersArray();
    }

    var that = this;

    if (this.gbounds === null)
    {
        this.gbounds = new google.maps.LatLngBounds();
    }
    //TODO support custom image in addMarker function
    for (var i in window.markers) {    
           
        if ( window.markers[i].lat === 0 || window.markers[i].lng === 0 ){
            window.markers[i].gmarker = null;
        }
        else{
        // check if it is a property used in compare
        var isCompare_property = false;
        var isQuickView_property = false;
        var image = that.getMarkerIcon(false, 0);
        var image_selected = that.getMarkerIcon(true, 0);
//          check if it is the tham map on the left
            if (window.gmap.getDiv().id === "city_side_map_container") {
                var imageIndex = window.markers[i].propertyIndex;
                image = that.getMarkerIcon(false, imageIndex);
                image_selected = that.getMarkerIcon(true, imageIndex);
            }
            else if (window.gmap.getDiv().id === "map_canvas_compareProperty") {

                for (var j in comparePropertyLatLng) {
                    if (comparePropertyLatLng[j].lat === window.markers[i].lat
                            && comparePropertyLatLng[j].lng === window.markers[i].lng) {
                        compare_index = compare_index + 1;
                        image = that.getMarkerIcon(true, compare_index);
                        image_selected = image;
                        // remove property detail from array
                        comparePropertyLatLng.splice(j, 1);
                        // make this marker as one of compared property
                        isCompare_property = true;
                    }
                }
            }
            else if (window.gmap.getDiv().className === "map_quickview") {
                var arrQuickViewLatLng = that.getQuickViewLatlng();

                if (arrQuickViewLatLng[0].lat === window.markers[i].lat
                        && arrQuickViewLatLng[0].lng === window.markers[i].lng) {

                    image = that.getMarkerIcon(true, 0);
                    image_selected = image;
                    // make this marker as one of quick view property
                    isQuickView_property = true;
                }
            }

        
        //Add marker to map
        window.gmarkers[i] = new google.maps.Marker({
            position: new google.maps.LatLng(window.markers[i].lat, window.markers[i].lng),
            map: window.gmap,
            title: window.markers[i].title,
            icon: image,
            custom_content: window.markers[i].content
        });

        window.markers[i].gmarker = window.gmarkers[i];
        
        if (isCompare_property === true || isQuickView_property === true) {
            window.gmarkers[i].setZIndex(200000);
        }
        
        if ( isQuickView_property === true ) {        
            window.gmap.setCenter( window.markers[i].gmarker.getPosition() );
        }
        
            
        //On marker click, open info window and set marker content
        google.maps.event.addListener(window.gmarkers[i], 'click', function() {

            if (window.gmap.getDiv().id === "city_side_map_container") {
                that.goToHostelDiv(this);
            }
            else {
                that.openInfoWindow(this, this.custom_content);
            }

        });
        
            if (isCompare_property === false) {
                google.maps.event.addListener(window.gmarkers[i], 'mouseover', function() {

                    that.changeHostelBackground(this, "mouseover");

                });

                google.maps.event.addListener(window.gmarkers[i], 'mouseout', function() {

                    that.changeHostelBackground(this, "mouseout");

                });
            }
        this.gbounds.extend(window.gmarkers[i].position);
        isCompare_property = false;
        isQuickView_property = false;
    }
  }
};
GoogleMap.prototype.removeMap = function(){
    this.map_div.style.display = "none";
};
GoogleMap.prototype.getMarkerIcon = function(pIsSelected, pIndex) {
    var image = "http://" + window.location.host + '/images/map_markers/unselected/marker_0.png';
    if (pIsSelected === true) {
        image = "http://" + window.location.host + '/images/map_markers/selected/marker_selected_' + pIndex + '.png';
    }
    else {
        image = "http://" + window.location.host + '/images/map_markers/unselected/marker_' + pIndex + '.png';

    }
    return image;
};
GoogleMap.prototype.closeInfoWindow = function() {
    window.gInfoWin.close();
};

GoogleMap.prototype.openInfoWindow = function(marker, content) {
//	var markerLatLng = marker.getPosition();

    window.gInfoWin.setContent([content].join(''));
    window.gInfoWin.open(window.gmap, marker);
};

GoogleMap.prototype.initInfoWin = function() {
    // Create single instance of a Google Map.
    window.gInfoWin = new google.maps.InfoWindow({
//		maxWidth: 300
    });
    google.maps.event.addListener(window.gmap, 'click', function() {
        window.gInfoWin.close();
    });
};

GoogleMap.clearDistrictLandmark = function() {
	if (window.gmap && window.gmap.overlayMapTypes) {
		window.gmap.overlayMapTypes.setAt(1, null); 
	}

	if (window.cityCircle !== null) {
		window.cityCircle.setMap(null);
	}

	for (var i = 0; i < GoogleMap.markers.length; i++ ) {
		GoogleMap.markers[i].setMap(null);
	}
};

GoogleMap.prototype.changeDistrictLayer = function(district_um_ids){

    // working with mapinfulence
    // Initialize Mapfluence with your API key.
    MF.initialize({
        apiKey: urbanmapping_key
    });
        // remove any old districts
        //map.overlayMapTypes.push(null);
   if (window.gmap && window.gmap.overlayMapTypes) {
      window.gmap.overlayMapTypes.setAt(1, null); 
   }
    // remove any old districts
    window.gmap.overlayMapTypes.setAt(0, null);

    if ($.isArray(district_um_ids)) {

            if(district_um_ids.length === 1){
                if (window.gmap.getZoom() > 12) {
                    // change map Zoom 
                    window.gmap.setZoom(12);
                }
        }
        // loop through districts um_ids
        var counter;
        for (counter = 0; counter < district_um_ids.length; ++counter) {
            this.addDistrictsBorder(MF, district_um_ids[counter], counter);
        }
    }
    else {
        this.addDistrictsBorder(MF, district_um_ids, 0);
    }

};
GoogleMap.prototype.addDistrictsBorder = function(MF, pDistricts_umIds, counter)
{
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

        if (landmark_LatLng.length === 1) {
            if (window.gmap.getZoom() > 12) {
                // change map Zoom 
                window.gmap.setZoom(12);
            }
        }
        // loop through districts um_ids
        var counter;
        for (counter = 0; counter < landmark_LatLng.length; ++counter) {
            this.addLandmarkLayer(landmark_LatLng[counter]);
        }
    }
    else {
        //    check zoom level and change it according to needed
        // just change zoom  if only one landmark to be shown
            if (window.gmap.getZoom() > 13) {
                // change map Zoom 
                window.gmap.setZoom(13);
            }
        this.addLandmarkLayer(landmark_LatLng);
    }
};

GoogleMap.setZoom = function(zoom) {
	window.gmap.setZoom(zoom || 13);
};

GoogleMap.prototype.addLandmarkLayer = function(landmark_LatLng) {
   
    var point = landmark_LatLng.split("###");
    var lat = point[0];
    var Lng = point[1];

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

	GoogleMap.markers.push(gmarker);            
};

GoogleMap.prototype.centerMapMarker = function() {

    if (window.markers.length !== 0) {

        for (var i in window.markers) {

            if (window.markers[i].gmarker !== null)
            {
                if (window.markers[i].gmarker.getZIndex() === 100000) {
                    // set map to center on marker
                    window.gmap.setCenter(window.markers[i].gmarker.getPosition());
                }

            }
        }
    }
};
GoogleMap.prototype.changeMarkerIcon = function(pDiv, pIconType) {

    var property_number = $(pDiv).attr("rel");
    var hostel_title = $.trim($("#hostel_title_" + property_number).text());
    
    var imagePath = null;

    if (pIconType === "selected")
    {
        imagePath = '/images/map_markers/selected/marker_selected_';
    }
    else
    {
        imagePath = '/images/map_markers/unselected/marker_';
    }

    $("#city_info_" + property_number).removeClass('property_info_hover');
    // change the marker that appears between the property image and the property name

    var imageSrc = null;
    
    if ( $("#property_marker_number_" + property_number).length > 0 ) {
        var imageSrc = $("#property_marker_number_" + property_number).attr('src');
        imageSrc = imageSrc.replace("selected/marker_selected_", "unselected/marker_");
    }
   
    $("#property_marker_number_" + property_number).attr("src", imageSrc);
    if (pIconType === "selected")
    {
        $("#city_info_" + property_number).addClass('property_info_hover');
        // change the marker that appears between the property image and the property name 
        imageSrc = imageSrc.replace("unselected/marker_", "selected/marker_selected_");
        $("#property_marker_number_" + property_number).attr("src", imageSrc);
    }
    
    
    if (window.markers.length !== 0) {

        for (var i in window.markers) {

            if (window.markers[i].gmarker !== null)
            {
                if (window.gmap.getDiv().id === "city_side_map_container" ||
                        window.gmap.getDiv().id === "filter_map_rightSide") {
                    if (window.markers[i].gmarker.getZIndex() === 100000) {
                        window.markers[i].gmarker.setZIndex(0);
                    }
                }
                if (hostel_title === $.trim(window.markers[i].gmarker.getTitle()))
                {
                    // index of property in page
                    var imageIndex = window.markers[i].propertyIndex;

                    var image = "http://" + window.location.host + imagePath + '0.png';
                    
                    if (window.gmap.getDiv().id === "city_side_map_container") {
//                         image = "http://" + window.location.host + imagePath + (parseInt(i) + 1) + '.png';
                         image = "http://" + window.location.host + imagePath + imageIndex + '.png';
                    }
                   // this map is the map that appears after click on Quick view
                    if (window.gmap.getDiv().className === "map_quickview") {
//                         window.gmap.setCenter( window.markers[i].gmarker.getPosition() );
                          image = "http://" + window.location.host + imagePath +  '0.png';
                    }
                    
                    window.markers[i].gmarker.setZIndex(100000);
                    window.markers[i].gmarker.setIcon(image);
                }
            }

        }
    }
};
GoogleMap.prototype.changeHostelBackground = function(pMarker, pDivEventToTrigger) {

    var that = this;

    var resultInPage = that.getItemsInPage();

    var property_list = resultInPage.property_list;

    $.each(property_list, function(index, value) {

        if ($.trim($(value).find(".hostel_title").text()) === pMarker.getTitle())
        {
            $(value).trigger(pDivEventToTrigger);
            }
    });

};
GoogleMap.prototype.goToHostelDiv = function(pMarker) {
// if div exists
// then animate to it
    var that = this;

    var resultInPage = that.getItemsInPage();

    var property_list = resultInPage.property_list;

    $.each(property_list, function(index, value) {

        if ($.trim($(value).find(".hostel_title").text()) === pMarker.getTitle())
        {
            $('html,body').animate({
                scrollTop: $(value).offset().top},
            'slow');
        }
    });

};
GoogleMap.prototype.removeMarker = function(property_number) {

    var hostel_title = $.trim($("#hostel_title_" + property_number).text());
    if (window.markers.length !== 0) {

        for (var i in window.markers) {

            if (window.markers[i].gmarker !== null)
            {
                if (window.markers[i].gmarker.getZIndex() === 100000 ||
                        hostel_title === $.trim(window.markers[i].gmarker.getTitle())) {
                    window.markers[i].gmarker.setMap(null);
                }

            }
        }
    }
};
GoogleMap.prototype.getComparePropertyLatlng = function(property_number) {
    // add compare properties if exists
    var compare_properties = [];
    if ($('.compareProperty_geoLatLng th').length > 0) {
        $('.compareProperty_geoLatLng th.control_button').each(function() {
            var geoLatLng = $(this).find("input").val();
            var LatLngPoints = geoLatLng.split(",");
            var lat = LatLngPoints[0];
            var lng = LatLngPoints[1];

            var newElement = {};
            newElement['lat'] = lat;
            newElement['lng'] = lng;

            compare_properties.push(newElement);

        });
    }
    return compare_properties;
};
GoogleMap.prototype.getQuickViewLatlng = function(property_number) {
    // add quick view property if exists
    var arrQuickView = [];
    if ( $('#quickView_geolatitude').length > 0 && $('#quickView_geolongitude').length > 0 ) {

            var lat = $('#quickView_geolatitude').val();
            var lng = $('#quickView_geolongitude').val();

            var newElement = {};
            newElement['lat'] = lat;
            newElement['lng'] = lng;

            arrQuickView.push(newElement);
    }
    return arrQuickView;
};