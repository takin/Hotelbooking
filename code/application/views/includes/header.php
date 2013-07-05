<!DOCTYPE html>
<html lang="<?php echo $this->html_lang_code; ?>">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=10; IE=EDGE" />
	<meta charset="utf-8" />
  <meta http-equiv="Content-Language" content="<?php echo $this->html_lang_code; ?>" />
  <title><?php echo isset($title) ? my_mb_ucfirst($title) : $this->config->item('site_title'); ?></title>
	<?php if ($this->wordpress->get_option('aj_block_bot')){
		echo '<meta name="robots" content="noindex,follow" />';
	}?>

  <meta name="copyright" content="<?php echo $this->config->item('site_name');?>" />
  <meta name="keywords" content="<?php echo _("reservation auberge de jeunesse,auberge de jeunesse,auberges de jeunesse,voyage jeunesse europe,voyage jeunesse,voyage europe,hébergement voyage europe,réserver auberge,voyage europe logement,logement voyage france,logement voyage europe,trip jeunesse euro,euro youth hostel,révervation hôtel londres,reservation hôtel paris");?>" />
	<?php

	switch($current_view)
	{
	  case "continent_view":
	    ?>
	    <meta name="description" content="<?php printf( gettext("Auberges en %s incluant Auberges de Jeunesse en %s. 30,000 Auberges de jeunesses et logements pas chers en %s et dans le monde entier. Aussi, cartes des villes, photos, conseils, événements et guides des Auberges de Jeunesse en %s."),my_mb_ucfirst($bc_continent),my_mb_ucfirst($bc_continent),my_mb_ucfirst($bc_continent),my_mb_ucfirst($bc_continent));?>"/>
	    <?php
	    break;
	  case "country_view":
	    ?>
	    <meta name="description" content="<?php printf(gettext("Auberges en %s incluant Auberges de Jeunesse en %s. 30,000 Auberges de jeunesses et logements pas chers en %s et dans le monde entier. Aussi, cartes des villes, photos, conseils, événements et guides des Auberges de Jeunesse en %s."),my_mb_ucfirst($bc_country),my_mb_ucfirst($bc_country),my_mb_ucfirst($bc_country),my_mb_ucfirst($bc_country));?>"/>
	    <?php
	    break;
	  case "city_view":
	    ?>
	    <meta name="description" content="<?php printf( gettext("Auberges à %s incluant Auberges de Jeunesse à %s. 30,000 Auberges de jeunesses et logements pas chers à %s et dans le monde entier. Aussi, cartes des villes, photos, conseils, événements et guides des Auberges de Jeunesse à %s."),my_mb_ucfirst($bc_city),my_mb_ucfirst($bc_city),my_mb_ucfirst($bc_city),my_mb_ucfirst($bc_city));?>"/>
	    <?php
	    break;
	  case "group_request":
	    ?>
	    <meta name="description" content="<?php echo $meta_desc;?>"/>
	    <?php
	    break;
	  case "hostel_view":
	    $property_name_escaped = htmlspecialchars($property_name,ENT_COMPAT);
	    ?>
	    <meta name="description" content="<?php printf( gettext("Réservation immédiate et garantie de l’auberge %s à %s. Prix imbattables de %s, photos, cartes & guides sur %s."),$property_name_escaped,my_mb_ucfirst($bc_city),$property_name_escaped,$this->config->item('site_name'));?>"/>
	    <?php
	    break;
	}

  $csspath = $this->wordpress->get_option('aj_api_ascii');
  if (empty($csspath))
  {
    $csspath = $this->wordpress->get_option('aj_api_name');
  }
  //$this->carabiner->css('style.css');
//   $this->carabiner->css('common.css');
  //$this->carabiner->css('common.css','screen','common.css',FALSE,FALSE,"full_site_global");

        if (!empty($print) && $print == 'pdf') {
    	    $this->carabiner->css('reset.css','all','reset.css',FALSE,FALSE,"full_site_global");
            $this->carabiner->css('mainv2.css?v=' . time(),'all','mainv2.css?v=' . time(),FALSE,FALSE,"full_site_global");
	    $this->carabiner->css('tools.css','all','tools.css',FALSE,FALSE,"full_site_global");

	    $this->carabiner->css('pdf.css');
            
            $this->carabiner->css('jquery.cluetip.css', 'screen', 'jquery.cluetip.css', FALSE, FALSE, "full_site_global");
            $this->carabiner->css('jquery.toastmessage.css', 'screen', 'jquery.toastmessage.css', FALSE, FALSE, "full_site_global");
            $this->carabiner->css('jquery.simplemodal.css', 'screen', 'jquery.simplemodal.css', FALSE, FALSE, "full_site_global");
        }
        else {
	    $this->carabiner->css('reset.css','screen','reset.css',FALSE,FALSE,"full_site_global");
            $this->carabiner->css('mainv2.css?v=' . time(),'screen','mainv2.css?v=' . time(),FALSE,FALSE,"full_site_global");
	    $this->carabiner->css('tools.css','screen','tools.css',FALSE,FALSE,"full_site_global");
            $this->carabiner->css('compare_property_print.css','screen','compare_property_print.css',FALSE,FALSE,"full_site_global");
            
            $this->carabiner->css('jquery.cluetip.css', 'screen', 'jquery.cluetip.css', FALSE, FALSE, "full_site_global");
            $this->carabiner->css('jquery.toastmessage.css', 'screen', 'jquery.toastmessage.css', FALSE, FALSE, "full_site_global");
            $this->carabiner->css('jquery.simplemodal.css', 'screen', 'jquery.simplemodal.css', FALSE, FALSE, "full_site_global");
        }

	if($this->api_used == HB_API)
	{
	  //$this->carabiner->css('hostels.css','screen','hostels.css',FALSE,FALSE,"full_site_global");
	}
	//$this->carabiner->css($csspath.'/more.css');
	$this->carabiner->css('jquery.fancybox.css');
	$this->carabiner->css('smoothness/jquery-ui.css');

  if($this->api_used == HB_API)
	{?>
	<link rel="shortcut icon" href="<?php echo site_url('images/favicon-hb.ico');?>" />
  <?php }else{?>
	<link rel="shortcut icon" href="<?php echo site_url('images/favicon.ico');?>" />
	<?php }?>

	<?php
  $this->carabiner->display('full_site_global');
  $this->carabiner->display('css');
  ?>
  <!--[if IE]>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/ie.css" />
  <![endif]-->
	<!--[if IE 6]>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/ie6.css" />
  <![endif]-->
  <!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/ie7.css" />
  <![endif]-->

  <script type="text/javascript">
var urbanmapping_key = "<?php echo $this->config->item('urbanmapping_key');  ?>";
</script>

  <?php if(isset($google_map_enable)&&($google_map_enable===true)): ?>

<?php if((isset($google_map_hostel_list)&&$google_map_hostel_list==true&&(isset($property_list["property_count"]))&&($property_list["property_count"]>0))||
         (!empty($google_map_city_list))||
         (!empty($google_map_country_list)&&!empty($country_list))):?>
<?php $this->carabiner->js('markerclusterer_packed.js');?>
<?php endif; ?>
<script type="text/javascript">
  var map;
  var streetService;
	var streetPanorama;
  var streetPanelId;
  var markerCluster = null;
  var geocoder;
  var map_div = null;
  var hostel_target_id = 0;
  var InfoW = {
      map: null,
      infoWindow: null
    };

// this is used to create circles in map (landmark)
var cityCircle = null;
var landmark_cityMarkers = [];

  InfoW.closeInfoWindow = function() {
    InfoW.infoWindow.close();
  };

  InfoW.openInfoWindow = function(marker,content) {
    var markerLatLng = marker.getPosition();
    InfoW.infoWindow.setContent([content].join(''));
    InfoW.infoWindow.open(InfoW.map, marker);
  };

  InfoW.init = function() {
    // Create single instance of a Google Map.
    InfoW.map = map;
    InfoW.infoWindow = new google.maps.InfoWindow({
       maxWidth: 300
    });
    InfoW.map.setZoom(12);
    google.maps.event.addListener(InfoW.map, 'click', InfoW.closeInfoWindow);
  };

  function appendBootstrap(map_div_id,callbackFunction) {
    callbackFunction = typeof(callbackFunction) != 'undefined' ? callbackFunction : 'initialize';

    if(!map_div_id) map_div_id = "map_canvas";
	  map_div = map_div_id;

	  var script = document.createElement("script");
    script.type = "text/javascript";
    script.src = "https://maps.google.com/maps/api/js?sensor=false&language=<?php echo $this->site_lang; ?>&callback="+callbackFunction;
    document.body.appendChild(script);
  }

  <?php if(isset($google_map_geo_latlng)||isset($google_map_address)):?>
  function streetViewPanInit(divID, geoposition)
  {
	  streetPanelId = divID;
	  var panoramaOptions = {
		      position: geoposition,
		      pov: {
  		      heading: 270,
  		      pitch: 0,
  		      zoom: 1
		      },
          visible: false
		    };
	  panorama = new  google.maps.StreetViewPanorama(document.getElementById(streetPanelId),panoramaOptions);

	  streetService = new google.maps.StreetViewService();
	  streetService.getPanoramaByLocation(geoposition, 50, processSVData);
  }

  function processSVData(data, status)
  {
	  if (status == google.maps.StreetViewStatus.OK)
    {
      panorama.setPov({
    	    heading: data.links[0].heading,
		      pitch: 0,
		      zoom: 1
      });
      map.setStreetView(panorama);
		  panorama.setVisible(true);

	    document.getElementById("street_panel_title").style.display = "block";
	    document.getElementById("street_panel-wrap").style.display = "block";
	    document.getElementById(streetPanelId).style.display = "block";
    }
	  else
	  {
      document.getElementById("street_panel-wrap").style.display = "none";
			document.getElementById("street_panel_title").style.display = "none";
			document.getElementById(streetPanelId).style.display = "none";
      panorama.setVisible(false);
	  }
  }
  <?php endif;?>

  function initialize() {
	  geocoder = new google.maps.Geocoder();
	  <?php if(isset($google_map_geo_latlng)):?>
	  var latlng = new google.maps.LatLng(<?php echo $google_map_geo_latlng; ?>);
    <?php else:?>
    var latlng = new google.maps.LatLng(0, 0);
    <?php endif;?>
    var myOptions = {
      zoom: 14,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById(map_div), myOptions);

    <?php if(isset($google_map_geo_latlng)):?>

//    var image = new google.maps.MarkerImage('<?php echo site_url('images/map-marker.png'); ?>',
//        new google.maps.Size(28, 28),
//        new google.maps.Point(0,0),
//        new google.maps.Point(0, 29));
    
    var image = "http://" + window.location.host + '/images/map_markers/selected/marker_selected_0.png';

      var iconshadow = new google.maps.MarkerImage('<?php echo site_url('images/map-marker-shadow.png'); ?>',
              new google.maps.Size(43, 28),
              new google.maps.Point(0,0),
              new google.maps.Point(0, 28));

      var marker = new google.maps.Marker({
          position: latlng,
          map: map,
          icon: image
//        ,shadow: iconshadow
    });

    streetViewPanInit("street_panel",latlng);
    <?php elseif(isset($google_map_address)):?>
    codeAddress();
    <?php elseif(isset($google_map_hostel_list)&&$google_map_hostel_list==true):?>
    markHostelList();
    <?php elseif(!empty($google_map_city_list)):?>
    markCityList();
    <?php elseif(!empty($google_map_country_list)):?>
    markCountryList();
    <?php endif;?>
  window.setTimeout(function() { loadchanged_landmarkAndDistrict(); }, 2200);
//  loadchanged_landmarkAndDistrict();
// show featured landmark like train , city center and airport
    show_featured_landmarks();
  }
// show featured landmark like train , city center and airport
function show_featured_landmarks(){
        var city_landmarks = [];
        
        $('#ul_city_landmarks li').each(function(){
                var landmark_id = $(this).find(".city_landmark_ids").html();
                var landmark_type = $("#city_landmark_type_"+landmark_id).val();
                var landmark_title = $("#city_landmark_title_" + landmark_id).html();
                var latlng = null;
                
        if ( landmark_type === "train_station"
            || landmark_type === "airport"
            || landmark_title === "city_center") {
        
            latlng = $("#city_landmark_"+landmark_id).val();
        }

            if(landmark_title === "city_center"){
                landmark_type = "city_center";
            }

        if (latlng !== null) {
    
            var point = latlng.split(",");
            var lat = point[0];
            var lng = point[1];

            var newElement = {};
            newElement['lat'] = lat;
            newElement['lng'] = lng;
            newElement['title'] = landmark_title;
            newElement['type'] = landmark_type;

            city_landmarks.push(newElement);
        }
   });

        var lmarkers = [];
        
        for (var i in city_landmarks) {

        var image = "";
        if (city_landmarks[i].type === "train_station") {

            image = {
                url: 'http://' + window.location.host + '/images/map/train.png',
                size: new google.maps.Size(25, 31),
                origin: new google.maps.Point(0, 0),
                scaledSize: new google.maps.Size(20, 25)
            };
        }
        else if (city_landmarks[i].type === "city_center") {
            image = {
                url: 'http://' + window.location.host + '/images/map/city_center.png',
                size: new google.maps.Size(21, 21),
                origin: new google.maps.Point(0, 0),
                scaledSize: new google.maps.Size(21, 21)
            };

        }
        else {
            image = {
                url: 'http://' + window.location.host + '/images/map/air-plane.png',
                size: new google.maps.Size(28, 25),
                origin: new google.maps.Point(0, 0),
                scaledSize: new google.maps.Size(28, 25)
            };
        }

        //Add marker to map
        lmarkers[i] = new google.maps.Marker({
            position: new google.maps.LatLng(city_landmarks[i].lat, city_landmarks[i].lng),
            map: map,
            title: city_landmarks[i].title,
            icon: image
        });

        lmarkers[i].setZIndex(10000);

     }
}
        function loadchanged_landmarkAndDistrict(){
      // check if there is a district or landmark radio button and checked
        if($("input:radio[name='landmarkAndDistrict']:checked").length > 0){

            var LandmarkOrDistrictValue = $("input:radio[name='landmarkAndDistrict']:checked").val();

                 if(LandmarkOrDistrictValue.indexOf(",") === -1){
                     changeDistrictLayer(LandmarkOrDistrictValue);
                 }
                 else{
                     changeLandmarkLayer(LandmarkOrDistrictValue);
                 }
            }
      
     }
        
    function changeDistrictLayer(district_um_id){

    // working with mapinfulence
    // Initialize Mapfluence with your API key.
    MF.initialize({
        apiKey: urbanmapping_key
    });

        // remove any old districts
        //map.overlayMapTypes.push(null);
        map.overlayMapTypes.setAt(1, null);

//get district area from mapfluence
    var filter = MF.filter.Data({
        column: 'umi.neighborhoods.attributes.hood_id',
        operator: '=',
        value: parseInt(district_um_id)

    });


        var hoodsLayer = MF.layer.tile.Simple({
            from : 'umi.neighborhoods.geometry',
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
       map.overlayMapTypes.setAt(1, adaptedLayer);
  }

 function ClearlandmarkAndDistrict(){
     // clear any landmark circle
        if(cityCircle !== null)
        {
            cityCircle.setMap(null);
        }
        
        // check if landmark bin exists
        // if exist remove them
        if (landmark_cityMarkers.length > 0)
        {
            for (var i in landmark_cityMarkers ) 
            { 
                landmark_cityMarkers[i].setMap(null);
            }
        }
        // clear any district
        map.overlayMapTypes.setAt(1, null);
        }
        
  function changeLandmarkLayer (landmark_LatLng_type) {

    if (cityCircle !== null)
    {
        cityCircle.setMap(null);
    }

    if ($.isArray(landmark_LatLng_type)) {
        // loop through districts um_ids
        var counter;
        for (counter = 0; counter < landmark_LatLng_type.length; ++counter) {
            this.addLandmarkLayer(landmark_LatLng_type[counter]);
        }
    }
    else {
        this.addLandmarkLayer(landmark_LatLng_type);
    }
}

 function addLandmarkLayer(landmark_LatLng_type){

    var point = landmark_LatLng_type.latlng.split(",");
    var lat = point[0];
    var lng = point[1];

    var landmark_type = landmark_LatLng_type.type;

    var citymap = {
        center: new google.maps.LatLng(lat, lng)
    };
//var circle_color  = "#4E89C9";
    var circle_color = "#FF0000";

    var LandmarkOptions = {
        strokeColor: circle_color,
        strokeOpacity: 0.8,
        strokeWeight: 2,
//      fillColor: "#FF0000",
        fillColor: circle_color,
        fillOpacity: 0.35,
        map: map,
        center: citymap.center,
        radius: 2000
    };
    cityCircle = new google.maps.Circle(LandmarkOptions);

    //landmark_marker_blue.png
    image = {
        url: 'http://' + window.location.host + '/images/map_landmark_marker_blue.png',
        size: new google.maps.Size(28, 28),
        origin: new google.maps.Point(0, 0),
//                anchor: new google.maps.Point(17, 34),
        scaledSize: new google.maps.Size(28, 28)
    };

    if (landmark_type === "train_station") {
        image = {
            url: 'http://' + window.location.host + '/images/map/train.png',
            size: new google.maps.Size(25, 31),
            origin: new google.maps.Point(0, 0),
            scaledSize: new google.maps.Size(20, 25)
        };
    }
    else if (landmark_type === "city_center") {
        
          image = {
            url: 'http://' + window.location.host + '/images/map/city_center.png',
            size: new google.maps.Size(21, 21),
            origin: new google.maps.Point(0, 0),
            scaledSize: new google.maps.Size(21, 21)
        };
    }
    else if (landmark_type === "airport") {
        image = {
            url: 'http://' + window.location.host + '/images/map/air-plane.png',
            size: new google.maps.Size(28, 25),
            origin: new google.maps.Point(0, 0),
//                anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(28, 25)
        };
    }

    var gmarker = new google.maps.Marker({
        position: new google.maps.LatLng(lat, lng),
        map: map,
        icon: image
    });

    gmarker.setZIndex(15000);
    landmark_cityMarkers.push(gmarker);
      }

  <?php if(isset($google_map_address)):?>
  function codeAddress() {
	    var address = "<?php echo $google_map_address;?>";

//	    var image = new google.maps.MarkerImage('<?php echo site_url('images/map-marker.png'); ?>',
//	        new google.maps.Size(28, 28),
//	        new google.maps.Point(0,0),
//	        new google.maps.Point(0, 29));

            var image = "http://" + window.location.host + '/images/map_markers/unselected/marker_0.png';

	    var iconshadow = new google.maps.MarkerImage('<?php echo site_url('images/map-marker-shadow.png'); ?>',
	            new google.maps.Size(43, 28),
	            new google.maps.Point(0,0),
	            new google.maps.Point(0, 28));

	    if (geocoder) {
	      geocoder.geocode( { 'address': address}, function(results, status) {
	        if (status == google.maps.GeocoderStatus.OK) {
	          map.setCenter(results[0].geometry.location);

	          var marker = new google.maps.Marker({
	              map: map,
	              position: results[0].geometry.location,
	              icon: image
//                      ,shadow: iconshadow
	          });
	          streetViewPanInit("street_panel",results[0].geometry.location);
	        } else {
	          triggerWarning("<?php echo _("Localisation invalide sur Google Map");?>");
	        }
	      });
	    }
  }
  <?php endif; ?>
  <?php if(!empty($google_map_hostel_list)&&!empty($property_list)):?>

  function update_review_box(target_property_number)
  {
	  $('#fancybox-content h2.gradient-back').show();
	  $("#map_box_reviews").html('<p style="text-align:center; padding:30px 0px; margin-bottom:0px;"><img src="<?php echo base_url();?>images/loading-round.gif" alt="" /></p>');
  	$.ajax(
				{
						type:"POST",
						url:"<?php echo site_url();?>reviews_map/"+target_property_number+"/2",
						success:function(data)
						{
										$("#map_box_reviews").html(data);
										$.fancybox.resize();
            }

				});
  }

  var specific_index = null;

  function addPropertyToMap(infocontent,hostelMarker,lat, lng, bounds, image, iconshadow, propertyName, propertyNumber, infoHTML)
  {
    infocontent.push(infoHTML);
    var newmarker = new google.maps.Marker({
        position: new google.maps.LatLng(lat, lng),
        map: map,
        icon: image,
//        shadow: iconshadow,
        title: name
    });

    var lastIndex = hostelMarker.push(newmarker);
    lastIndex = lastIndex - 1;
    bounds.extend(hostelMarker[lastIndex].position);

    google.maps.event.addListener(hostelMarker[lastIndex], 'click', function() {
        InfoW.openInfoWindow(hostelMarker[lastIndex],infocontent[lastIndex]);
        update_review_box(propertyNumber);
      });

    if((hostel_target_id > 0) && (hostel_target_id == propertyNumber)) specific_index = lastIndex;
  }

  function markHostelList()
  {
	  var hostelMarker = Array();
	  var infocontent = Array();
	  specific_index = null;

    InfoW.init();

		var bounds = new google.maps.LatLngBounds();

//		var image = new google.maps.MarkerImage('<?php echo site_url('images/map-marker.png'); ?>',
//	      new google.maps.Size(28, 28),
//	      new google.maps.Point(0,0),
//	      new google.maps.Point(0, 29));
            

    var image = "http://" + window.location.host + '/images/map_markers/selected/marker_selected_0.png';

	  var iconshadow = new google.maps.MarkerImage('<?php echo site_url('images/map-marker-shadow.png'); ?>',
	          new google.maps.Size(43, 28),
	          new google.maps.Point(0,0),
	          new google.maps.Point(0, 28));

    <?php
    foreach($property_list as $prop_type)
    {
      if(is_array($prop_type))
      foreach($prop_type as $i => $property)
      {
        $rating_text = "";

        if(($this->api_used == HW_API) &&($property->Geo->Latitude!=0)&&($property->Geo->Longitude!=0))
        {
          if(!empty($property->overallHWRating))
          {
            $rating_text = _("évaluation moyenne"). " ".$property->overallHWRating."%";
          }

          $thumbnail_url = site_url('images/na_small.jpg');
          $cur = currency_symbol($property->BedPrices->BedPrice->currency);

          if (!empty($property->PropertyImages->PropertyImage->imageURL))
          {
            $thumbnail_url = $property->PropertyImages->PropertyImage->imageURL;
          }

          $property_url = $this->Db_links->build_property_page_link($property->propertyType,$property->propertyName,$property->propertyNumber[0],$this->site_lang);

          if(isset($property->BedPrices->BedPrice->price))
          {
            if(!empty($rating_text)) $rating_text = " - ".$rating_text;
            $infoHTML = "<div class=\"mapbubble\">";
            $infoHTML.= "<a href=\"".$property_url."\">";
            $infoHTML.= "<img class=\"alignleft\" src=\"".$thumbnail_url."\" alt=\"".addslashes($property->propertyName)."\" />";
            $infoHTML.= "</a>";
            $infoHTML.= "<h2>";
            $infoHTML.= "<a href=\"". $property_url ."\">". addslashes($property->propertyName)."</a>";
            $infoHTML.= "</h2>";
            $infoHTML.= "<p class=\"price\">". _('à partir de')."<span> ". $property->BedPrices->BedPrice->price."</span> ". $cur .$rating_text."</p>";
            $infoHTML.= "<a href=\"". $property_url ."\" class=\"more-info\">".addslashes(_("Plus d'information"))." &raquo;</a>";
            $infoHTML.= "<div class=\"clear\"></div>";
            $infoHTML.= "</div>";
    		  }
    		  else
      		{
      		  if(!empty($rating_text)) $rating_text = "<p>".$rating_text."</p>";
      		  $infoHTML = "<div class=\"mapbubble\">";
      		  $infoHTML.= "<a href=\"".$property_url."\">";
            $infoHTML.= "<img class=\"alignleft\" src=\"".$thumbnail_url."\" alt=\"".addslashes($property->propertyName)."\" />";
            $infoHTML.= "</a>";
      		  $infoHTML.= "<h2>";
            $infoHTML.= "<a href=\"". $property_url ."\">". addslashes($property->propertyName)."</a>";
            $infoHTML.= "</h2>";
      		  $infoHTML.= $rating_text;
      		  $infoHTML.= "<a href=\"". $property_url ."\" class=\"more-info\">".addslashes(_("Plus d'information"))." &raquo;</a>";
      		  $infoHTML.= "<div class=\"clear\"></div>";
      		  $infoHTML.= "</div>";
          }
          ?>
          addPropertyToMap(infocontent,hostelMarker,<?php echo $property->Geo->Latitude; ?>, <?php echo $property->Geo->Longitude; ?>, bounds, image, iconshadow, '<?php echo addslashes($property->propertyName); ?>', <?php echo (string)$property->propertyNumber; ?>, '<?php echo $infoHTML; ?>');
          <?php
        } //if HW_API AND lat !=0 AND lng !=0

        if(($this->api_used == HB_API) && isset($property["geo_latitude"]) && isset($property["geo_longitude"]) && ($property["geo_latitude"]!=0) && ($property["geo_longitude"]!=0))
        {

          $property_url = $this->Db_links->build_property_page_link($property["type"],$property["name"],$property["id"],$this->site_lang);

          if(!empty($property["rating"]))
          {
            $rating_text = _("évaluation moyenne"). " ".$property["rating"]."%";
          }

          if(!empty($property["prices"]["customer"]["minprice"]))
          {
            $cur = currency_symbol($property["prices"]["customer"]["currency"]);
            if(!empty($rating_text)) $rating_text = " - ".$rating_text;
            $infoHTML = "<div class=\"mapbubble\">";
            $infoHTML.= "<a href=\"".$property_url."\">";
            $infoHTML.= "<img class=\"alignleft\" src=\"".$property["image_thumbnail"]."\" alt=\"".addslashes($property["name"])."\" />";
            $infoHTML.= "</a>";
            $infoHTML.= "<h2>";
            $infoHTML.= "<a href=\"". $property_url ."\">". addslashes($property["name"])."</a>";
            $infoHTML.= "</h2>";
            $infoHTML.= "<p class=\"price\">". _('à partir de')."<span> ". $property["prices"]["customer"]["minprice"]."</span> ". $cur .$rating_text."</p>";
            $infoHTML.= "<a href=\"". $property_url ."\" class=\"more-info\">".addslashes(_("Plus d'information"))." &raquo;</a>";
            $infoHTML.= "<div class=\"clear\"></div>";
            $infoHTML.= "</div>";
          }
          else
          {
            if(!empty($rating_text)) $rating_text = "<p>".$rating_text."</p>";
            $infoHTML = "<div class=\"mapbubble\">";
            $infoHTML.= "<a href=\"".$property_url."\">";
            $infoHTML.= "<img class=\"alignleft\" src=\"".$property["image_thumbnail"]."\" alt=\"".addslashes($property["name"])."\" />";
            $infoHTML.= "</a>";
            $infoHTML.= "<h2>";
            $infoHTML.= "<a href=\"". $property_url ."\">". addslashes($property["name"])."</a>";
            $infoHTML.= "</h2>";
            $infoHTML.= $rating_text;
            $infoHTML.= "<a href=\"". $property_url ."\" class=\"more-info\">".addslashes(_("Plus d'information"))." &raquo;</a>";
            $infoHTML.= "<div class=\"clear\"></div>";
            $infoHTML.= "</div>";
          }
          ?>
          addPropertyToMap(infocontent,hostelMarker,<?php echo $property["geo_latitude"]; ?>, <?php echo $property["geo_longitude"]; ?>, bounds, image, iconshadow, '<?php echo addslashes($property["name"]); ?>', <?php echo (string)$property["id"]; ?>, '<?php echo $infoHTML; ?>');
          <?php
         } //if lat !=0 and lng !=0
       }//Enf foreach $prop_type
     } //Enf foreach propertylist ?>

    if(specific_index == null)
    {
      InfoW.map.fitBounds(bounds);
    }
    else
    {
      InfoW.map.setZoom(16);
      InfoW.map.setCenter(hostelMarker[specific_index].position);
      InfoW.openInfoWindow(hostelMarker[specific_index],infocontent[specific_index]);
      specific_index = null;
      hostel_target_id = 0;
    }

    var styles =  [{
                    url: '<?php echo site_url("images/map-cluster.png"); ?>',
                    height: 50,
                    width: 50,
                    opt_anchor: [25, 0],
                    opt_textColor: '#ffffff',
                    opt_textSize: 14
                   },
                   {
                     url: '<?php echo site_url("images/map-cluster.png"); ?>',
                     height: 50,
                     width: 50,
                     opt_anchor: [25, 0],
                     opt_textColor: '#ffffff',
                     opt_textSize: 14
                    },
                    {
                      url: '<?php echo site_url("images/map-cluster.png"); ?>',
                      height: 50,
                      width: 50,
                      opt_anchor: [25, 0],
                      opt_textColor: '#ffffff',
                      opt_textSize: 14
                     }
                   ];

    var mcOptions = {maxZoom: 15,styles: styles};
    markerCluster = new MarkerClusterer(InfoW.map, hostelMarker, mcOptions);

  }
  <?php endif; ?>

  <?php if(!empty($google_map_city_list)&&!empty($cities_of_country)):?>

  var cityMarkers = Array();

  function markCityList()
  {

    InfoW.init();

    var bounds = new google.maps.LatLngBounds();

//    var image = new google.maps.MarkerImage('<?php echo site_url('images/map-marker.png'); ?>',
//        new google.maps.Size(28, 28),
//        new google.maps.Point(0,0),
//        new google.maps.Point(0, 29));
    
    var image = "http://" + window.location.host + '/images/map_markers/unselected/marker_0.png';

    var iconshadow = new google.maps.MarkerImage('<?php echo site_url('images/map-marker-shadow.png'); ?>',
            new google.maps.Size(43, 28),
            new google.maps.Point(0,0),
            new google.maps.Point(0, 28));

    <?php $i=0;
    foreach($cities_of_country->Country->Cities as $city)
    {
      $citypos = (string)$city->cityLatitude.",".(string)$city->cityLongitude;
      $city_name = $city->cityName;

      if(!empty($city->cityNameTranslated))
      {
        $city_name = $city->cityNameTranslated;
      }
      ?>
      <?php if((is_numeric((string)$city->cityLatitude)) && is_numeric((string)$city->cityLongitude )):?>
        var citypos = new google.maps.LatLng(<?php echo $citypos;?>);
        addCityMarker(<?php echo $i;?>,citypos,"<?php echo $city_name;?>",image,iconshadow,bounds);
        <?php $i++;?>
      <?php endif;
    }
    ?>
    InfoW.map.setCenter(bounds.getCenter());

    if(cityMarkers.length > 1)
    {
      InfoW.map.fitBounds(bounds);
    }
    else
    {
      InfoW.map.setZoom(7);
    }
//    addClusters();
  }

  function addCityMarker(i,pos,city,img,imgshadow,gbounds)
  {
    cityMarkers[i] = new google.maps.Marker({
        position: pos,
        map: map,
        icon: img,
        shadow: imgshadow,
        title: city
    });
    gbounds.extend(cityMarkers[i].position);
    google.maps.event.addListener(cityMarkers[i], 'click', function() {
      window.location = '<?php echo site_url(customurlencode($country_selected)."/"); ?>'+'/'+customurlencode(city);
    });
  }
  function addClusters()
  {
    var styles =  [{
      url: '<?php echo site_url("images/map-cluster.png"); ?>',
      height: 50,
      width: 50,
      opt_anchor: [25, 0],
      opt_textColor: '#ffffff',
      opt_textSize: 14
     },
     {
       url: '<?php echo site_url("images/map-cluster.png"); ?>',
       height: 50,
       width: 50,
       opt_anchor: [25, 0],
       opt_textColor: '#ffffff',
       opt_textSize: 14
      },
      {
        url: '<?php echo site_url("images/map-cluster.png"); ?>',
        height: 50,
        width: 50,
        opt_anchor: [25, 0],
        opt_textColor: '#ffffff',
        opt_textSize: 14
       }
     ];

    var mcOptions = {maxZoom: 16,styles: styles};
    markerCluster = new MarkerClusterer(InfoW.map, cityMarkers, mcOptions);
  }

  //window.onload = appendBootstrap;
  <?php endif; ?>

  <?php if(!empty($google_map_country_list)&&!empty($country_list)):?>

  var countryMarkers = Array();

  function markCountryList()
  {

    InfoW.init();

    var bounds = new google.maps.LatLngBounds();

//    var image = new google.maps.MarkerImage('<?php echo site_url('images/map-marker.png'); ?>',
//        new google.maps.Size(28, 28),
//        new google.maps.Point(0,0),
//        new google.maps.Point(0, 29));

    var image = "http://" + window.location.host + '/images/map_markers/unselected/marker_0.png';

    var iconshadow = new google.maps.MarkerImage('<?php echo site_url('images/map-marker-shadow.png'); ?>',
            new google.maps.Size(43, 28),
            new google.maps.Point(0,0),
            new google.maps.Point(0, 28));

    <?php $i=0;?>
    <?php
     foreach($country_list->Country as $country)
     {
        $countrypos = $country->countryLatitude.", ".$country->countryLongitude;

        $country_name = $country->countryName;
        if(!empty($country->countryNameTranslated))
        {
          $country_name = $country->countryNameTranslated;
        }

        $continent = $country->countryContinentTranslated; ?>
        <?php if((!empty($country->countryLatitude))&&(!empty($country->countryLongitude))):?>
          var countrypos = new google.maps.LatLng(<?php echo $countrypos;?>);
          addCountryMarker(<?php echo $i;?>,countrypos,"<?php echo $continent;?>","<?php echo $country_name;?>",image,iconshadow,bounds);
          <?php $i++;?>
        <?php endif;?>
        <?php
     }
     ?>
    InfoW.map.setCenter(bounds.getCenter());
    InfoW.map.fitBounds(bounds);
//    addClusters();
  }

  function addCountryMarker(i,pos,continent,country,img,imgshadow,gbounds)
  {
    countryMarkers[i] = new google.maps.Marker({
        position: pos,
        map: map,
        icon: img,
        shadow: imgshadow,
        title: country
    });
    gbounds.extend(countryMarkers[i].position);
    google.maps.event.addListener(countryMarkers[i], 'click', function() {
      window.location = '<?php echo site_url(""); ?>'+customurlencode(continent)+'/'+customurlencode(country);
    });
  }

  function addClusters()
  {
    var styles =  [{
      url: '<?php echo site_url("images/map-cluster.png"); ?>',
      height: 50,
      width: 50,
      opt_anchor: [25, 0],
      opt_textColor: '#ffffff',
      opt_textSize: 14
     },
     {
       url: '<?php echo site_url("images/map-cluster.png"); ?>',
       height: 50,
       width: 50,
       opt_anchor: [25, 0],
       opt_textColor: '#ffffff',
       opt_textSize: 14
      },
      {
        url: '<?php echo site_url("images/map-cluster.png"); ?>',
        height: 50,
        width: 50,
        opt_anchor: [25, 0],
        opt_textColor: '#ffffff',
        opt_textSize: 14
       }
     ];

    var mcOptions = {maxZoom: 16,styles: styles};
    markerCluster = new MarkerClusterer(InfoW.map, countryMarkers, mcOptions);
  }

  window.onload = function()
  {
    appendBootstrap('map_canvas','initialize');
  }
  <?php endif; ?>

</script>
  <?php endif; ?>
  <!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
<?php
  $this->carabiner->js('imageload.js');
  $this->carabiner->js('jtools.js');
  $this->carabiner->js('janim.js');
  $this->carabiner->js('tabs.js');
  if($current_view != "group_request")
  {
    $this->carabiner->js('sitetools.js?v=' . time());
  }
  $this->carabiner->js('map.js');
  $this->carabiner->js('slide.js');
  //$this->carabiner->js('jquery.evtpaginate.js','jquery.evtpaginate.js',TRUE);
  $this->carabiner->js('jquery.easing.1.3.js','jquery.easing.1.3.js', TRUE);
  $this->carabiner->js('jquery.fancybox.pack.js','jquery.fancybox.pack.js',TRUE);
  $this->carabiner->js('ui-lang/jquery.ui.datepicker-'.$this->site_lang.'.js','ui-lang/jquery.ui.datepicker-'.$this->site_lang.'.js',TRUE);
  //$this->carabiner->js('jquery.translate-1.3.9.js','jquery.translate-1.3.9.js',TRUE);
  ?>
  <?php
  if($current_view == "hostel_view")
  {
    $this->carabiner->js('jquery.rating.js','jquery.rating.js', TRUE);
    $this->carabiner->js('jquery.MetaData.js','jquery.MetaData.js',TRUE);
    $this->carabiner->js('jquery.mousewheel.js','jquery.mousewheel.js',TRUE);
    $this->carabiner->js('jquery.jscrollpane.min.js','jquery.jscrollpane.min.js',TRUE);
    $this->carabiner->js('jquery.calculation.js','jquery.calculation.js',TRUE);
    $this->carabiner->js('hostel_view.js','hostel_view.js',TRUE);
    $this->carabiner->js('jquery.tablesorter.js', 'jquery.tablesorter.js', TRUE);

  }
  elseif($current_view == "search_results")
  {
	  $this->carabiner->js('search-sort.js');
	  $this->carabiner->js('paginateview.js');
  }
  $this->carabiner->js('jquery.cluetip.all.js', 'jquery.cluetip.all.js', TRUE);
  $this->carabiner->js('jquery.toastmessage.js', 'jquery.toastmessage.js', TRUE);
  $this->carabiner->js('jquery.simplemodal.js', 'jquery.toastmessage.js', TRUE);

  ?>
<script src="http://static.mapfluence.com/mapfluence/2.0/mfjs.min.js" type="text/javascript"></script>

<?php if($current_view == "hostel_view"): ?>
<script type="text/javascript">
  window.name = 'Hostel View';
</script>
<?php else: ?>
	<?php if($current_view == "city_view"): ?>
		<script type="text/javascript">

		</script>
	<?php else: ?>
		<script type="text/javascript">
  			window.name = 'Other View';
		</script>
	<?php endif; ?>
<?php endif; ?>

  <?php
	$this->carabiner->display('jqueryui');
	$this->carabiner->display('js');

/* FOR change class in header manu */
$url =$_SERVER['PHP_SELF'];

$url_ex = explode('/',$url);

$pattern = '/(group)?$/';
preg_match($pattern, $url , $matches);

$sel_class = '';
	if($matches[0] == 'group')
	{
		$gorup_sel_class= 'current_page_item';
	}
	elseif($url_ex[3] == $this->Db_links->get_link("user") || $url_ex[3]==$this->Db_links->get_link("connect") || $url_ex[3] == $this->Db_links->get_link("register") ||  $url_ex[3] == $this->Db_links->get_link("logout") )
	{
		$sel_class= '';
		$gorup_sel_class='';
	}
	else
	{
		$sel_class= 'current_page_item';
	}

?>

  <script type="text/javascript">
  //City lists
  //Cities array must be a global variable
  var cities = new Array();

  var nocountryval = '<?php echo _('Choisir le pays');?>';
  var nocityval = '<?php echo _('Choisir la ville');?>';

  cities['<?php echo _('Choisir le pays');?>'] = ['<?php echo _('Choisir le pays');?>',new Array('<?php echo _('Choisir la ville');?>')];

	$(function() {
  		$("body").addClass("has-script");
		  //$('.translate').translate('en','fr');
    });

$(document).ready(function()
{

		$('a.openup').bind('mouseover',function(){
			$('a.openup').fancybox();
			return false;
		});

		$("#search-submit").mousedown(function() {
			$(this).css("background-position","bottom left");

		});

		$("#search-submit").mouseup(function() {
			$(this).css("background-position","top left");
		});

		$('a.flags-trigger').click(function() {
			$('#flags').toggle();
			return false;
		});

		$('#flags').mouseleave(function() {
			$(this).toggle();
		});

		$("#datepick").datepicker({ dateFormat: 'd MM, yy', minDate: 0});


	});
  </script>
  <script type="text/javascript">
    function toggleById(){

      var display=document.getElementById("top-login-form").style.display;

      if(display=="block")

      {
    	  document.getElementById("top-login-form").style.display = "none";
      }

      else

      {
    	  document.getElementById("top-login-form").style.display = "block";
      }

    }

	</script>

<?php echo $this->wordpress->get_option('aj_google_analytic'); ?>
</head>

<body class="auberges<?php if($current_view == "hostel_view"){echo ' view-hostel';}elseif($current_view == "city_view"){echo ' city-search';}if($this->api_used == HB_API){echo ' hb_frame';}?> lang-<?php echo $this->html_lang_code; ?>">
<?php if($current_view == "city_view"){?>
<div id="city_load" style="visibility:hidden;">
	<p><img class="logo" src="<?php echo site_url(); ?>images/<?php echo $csspath;?>/logo.png" alt="<?php echo $this->wordpress->get_option('aj_api_name');?>"/></p>
	<div class="box_content box_round group">
		<?php if(isset($city_selected) && isset($country_selected)){?>
		<p><span><?php printf( gettext('Searching for %s'),ucfirst($city_selected).', '.ucfirst($country_selected));?></span></p>
		<?php }?>
		<p><?php echo _("Please wait while we are finding the best deals for you ...")?></p>
		<p style="margin-bottom:0px;"><img src="<?php echo base_url();?>images/V2/loading-squares.gif" alt="<?php echo _("Loading")?>"/></p>
	</div>
</div>
<?php }?>
<div id="wrap">
<div id="top_bar">
	<div id="top_bar_inner" class="container_16 group">
		<div class="grid_6">
		<?php $code=$this->wordpress->get_option('aj_lang_code');
			$shortcode = strtolower(substr($code,0,2));
			$code=str_replace('-','_',$code);
			if($code=='' || $shortcode =='en'){$code="en_US";}
		?>
		<div class="fblike">
				<script src="https://connect.facebook.net/<?php echo $code;?>/all.js#xfbml=1"></script>
				<fb:like show_faces="false" layout="button_count" href="<?php echo strtolower(base_url()); ?>"></fb:like>
		</div>

		</div>
		<div class="grid_10">
			<span id="logged_in_link" style="display: none">
				<?php $logged_in_link = "<a class=\"meta_account\" href=\"".site_url($this->Db_links->get_link("user"))."\">"._("Mon Compte")."</a>"; ?>
				<?php echo $logged_in_link; ?>
			</span>
			<span id="log_in_link" style="display: none">
				<?php $log_in_link = "<a class=\"meta_login\" href=\"".site_url($this->Db_links->get_link("connect"))."\">"._("Se connecter")."</a>"; ?>
				<?php echo $log_in_link; ?>
			</span>
			<span id="log_out_link" style="display: none">
				<?php $logout_link = "<a class=\"meta_logout\" href=\"".site_url($this->Db_links->get_link("logout"))."\">"._("Se déconnecter")."</a>"; ?>
				<?php echo $logout_link; ?>
			</span>
			<span id="register_link" style="display: none">
				<?php $register_link = "<a class=\"meta_register\" href=\"".site_url($this->Db_links->get_link("register"))."\">"._("S'enregistrer")."</a>"; ?>
				<?php echo $register_link; ?>
			</span>

			<ul class="user_meta_top group">
				<?php $about = $this->wordpress->get_option('aj_page_about'); if (!empty($about)){?>
				<li><a class="meta_about" href="<?php echo $about; ?>"><?php echo _("About us");?></a></li>
				<?php }?>
				<li><a class="meta_help" href="<?php echo $this->wordpress->get_option('aj_page_faq'); ?>"><?php echo _("Aide / FAQ / Nous Joindre");?></a></li>
				<li class="account_login">
					<?php //echo login_check($this->tank_auth->is_logged_in(),"<a class=\"meta_account\" href=\"".site_url($this->Db_links->get_link("user"))."\">"._("Bienvenue!")."</a>","<a class=\"meta_login\" href=\"".site_url($this->Db_links->get_link("connect"))."\" onclick=\"toggleById(); return false;\">"._("Se connecter")."</a>");
					echo login_check($this->tank_auth->is_logged_in(), $logged_in_link, $log_in_link); // modify to remove js error as right  id "top-login-form" is comment at line no 916.
					 ?>
				</li>
				<li class="last logout_register">
					<?php echo login_check($this->tank_auth->is_logged_in(), $logout_link, $register_link); ?>
				</li>
			</ul>
		</div>
	</div>
</div>
<div id="main_container" class="wrapper container_16 group">
<?php if(isset($google_map_enable)&&($google_map_enable===true)): ?>
<img src="<?php echo site_url("images/map-marker.png"); ?>" style="display:none" />
<img src="<?php echo site_url("images/map-marker-shadow.png"); ?>" style="display:none" />
<img src="<?php echo site_url("images/map-cluster.png"); ?>" style="display:none" />
<?php endif; ?>

		<header class="grid_16 header_v2">
			<a class="logo" title="<?php echo _("Plus de 30,000 Auberges de Jeunesse disponible en ligne");?>" href="<?php echo site_url(); ?>"><img src="<?php echo site_url(); ?>images/<?php echo $csspath;?>/logo.png" class="logo" alt="<?php echo $this->wordpress->get_option('aj_api_name');?>"></a>

			<div class="bubble_blue_position<?php if($this->wordpress->get_option('aj_api_site_data') == 'hb'){echo ' hb_bubble';}?>">
				<div class="bubble_blue">
					<span class="bubble_blue_inner"><?php echo _('Free SMS')?></span>
				</div>
			</div>

			<div class="bubble_blue_right_position<?php if($this->wordpress->get_option('aj_api_site_data') == 'hb'){echo ' hb-bubble';}?>">
				<div class="bubble_blue_right">
					<span class="bubble_blue_right_inner">
                                            <?php if($this->wordpress->get_option('aj_api_site_data') == 'hb'){?>
                                            <?php if($this->wordpress->get_option('aj_hb_charge_booking_fees') == 'true'): ?>
                                            <?php printf(gettext("100%% sécurisé."));?>
                                            <?php else: ?>
                                            <?php echo _('No Booking fees')?>
                                            <?php endif; ?>
                                            <?php }else{?>
                                            <?php echo _('Book on your mobile')?>
                                            <?php }?>
                                        </span>
				</div>
			</div>

		</header>

		<nav class="main grid_16 box_shadow box_round">
			<ul class="group">

				<li class="first"><a href="/"><?php echo _("Accueil");?></a></li>
				<li><a class="<?php if(!empty($sel_class)){ echo $sel_class; } ?>" href="<?php echo site_url($this->Db_links->get_link("homepage")); ?>"><?php echo _("Auberges et logements pas chers");?></a></li>
				<?php if($this->wordpress->get_option('aj_group_url') != ''){?>
				<li><a title="<?php echo _("Réservation d'auberges de jeunesse pour les groupes");?>" href="<?php echo $this->wordpress->get_option('aj_group_url'); ?>" class="group-reserve <?php if(!empty($gorup_sel_class)){  echo $gorup_sel_class; } ?>"><?php echo _("Groupes 10+");?></a></li>
				<?php } ?>
				<?php if($this->wordpress->get_option('aj_page_events') != ''){?>
				<li><a href="<?php echo $this->wordpress->get_option('aj_page_events'); ?>"><?php echo _("Événements");?></a></li>
				<?php } ?>
				<?php if($this->wordpress->get_option('aj_page_guides') != ''){?>
				<li><a href="<?php echo $this->wordpress->get_option('aj_page_guides'); ?>"><?php echo _("Destinations");?></a></li>
				<?php } ?>
				<?php /*?><li class="right"><a class="icon-chathelp" href="">Live Chat Help</a></li><?php */?>
				<?php
				$displayVelaro = $this->config->item('displayVelaro');

				if($displayVelaro == 1)
		        {
					 if ($this->wordpress->get_option('aj_velaro_id') !='')
					 {
				?>
				<li class="right"><a class="chat_support" href="http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue" target="OnlineChatSoftware"  onClick="this.newWindow = window.open('http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue', 'OnlineChatSoftware', 'toolbar=no,location=no,directories=no,menubar=no,status=no,scrollbars=no,resizable=yes,replace=no');this.newWindow.focus();this.newWindow.opener=window;return false;"><img alt="OnlineChatSoftware" src="http://service.velaro.com/visitor/check.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue" border="0"></a></li>
				<?php }else{?>
				<li class="right"><a class="chat_support" href="http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&showwhen=inqueue" target="OnlineChatSoftware"  onClick="this.newWindow = window.open('http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&showwhen=inqueue', 'OnlineChatSoftware', 'toolbar=no,location=no,directories=no,menubar=no,status=no,scrollbars=no,resizable=yes,replace=no');this.newWindow.focus();this.newWindow.opener=window;return false;"><img alt="OnlineChatSoftware" src="http://service.velaro.com/visitor/check.aspx?siteid=7548&showwhen=inqueue" border="0"></a></li>
				<?php }
				          }?>
			</ul>
		</nav>

		<div id="warning" class="grid_16"></div>
