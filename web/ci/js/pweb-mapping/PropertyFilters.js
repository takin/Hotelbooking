//PWeb map wrapper for map in filter
function PWebFilterMap(default_div, lang, default_lat, default_lng)
{
	this.map_lang    = lang;
	this.default_lat = default_lat;
	this.default_lng = default_lng;
	this.enabled     = false;
	this.infow_template = document.getElementById('template-infow').innerHTML;
	this.prop_number_to_focus = -1;
	
	this.gmap = new GoogleMap(default_div, lang, default_lat, default_lng);
}

PWebFilterMap.prototype.reDraw = function ()
{
	if(this.enabled === true)
	{
		this.gmap.drawMap();
	}
};

PWebFilterMap.prototype.toggle = function ()
{
	if(this.enabled === false)
	{
		this.enableMap();
	}
	else
	{
		this.disableMap();
	}
};

PWebFilterMap.prototype.enableMap = function() {
	
//	this.updateMap(map_slug);
	this.gmap.drawMap();
	this.enabled = true;
};

PWebFilterMap.prototype.disableMap = function() {
	
	this.gmap.removeMap();
	this.prop_number_to_focus = -1;
	this.gmap.setFocusMarkerID(-1);
	this.enabled = false;
//	this.pweb_maps[map_slug]  = null;
};

PWebFilterMap.prototype.isMapEnable = function() {
	return this.enabled;
};

//UPDATE map data
PWebFilterMap.prototype.updateMarkers = function(markers_data) { 
	
	//clear all previous added marker and focus
	this.gmap.clearMap();
	
	//Add filtered markers to map
//	for (var i = 0; i < markers_data.length; i++) {
	for (var i in markers_data) {
		if(parseFloat(markers_data[i].Geo.Latitude) != 0.00 &&
		   parseFloat(markers_data[i].Geo.Longitude) != 0.00)
		{
			var content = Mustache.to_html(this.infow_template, { "property": markers_data[i]});
			this.gmap.addMarker(i,markers_data[i].Geo.Latitude,markers_data[i].Geo.Longitude,markers_data[i].propertyName, content);
			
			if((this.prop_number_to_focus > 0) && (markers_data[i].propertyNumber == this.prop_number_to_focus))
			{
				//set focus to last insert marker
				this.gmap.setFocusMarkerID(i);
			}
		}
	}
};
//PWeb filter app
function PWebFilterApp()
{
	this.request;
	
	this.jtable;
	this.jtable_hits;
	this.jtable_hits_sorted;
	this.results_limit;
	
	this.template;
	
	this.$sort_controls_div;
	this.$data_empty_msg;
	this.$data_loading_msg;
	
	//Filter controls
	this.TypeFilterCheckBoxes;
	this.FacilitiesFilterCheckBoxes;
	this.DistrictsCheckBoxes;
	this.LandmarksCheckBoxes;
	
	this.DowntownExtraCheckId;
	this.BreakfastExtraCheckId;
	
	this.FiltersCounts;
	this.FiltersInitValues;
	
	this.PriceFilterMin;
	this.PriceFilterMax;
	this.RatingFilterMin;
	this.RatingFilterMax;
	
	this.PriceCurrencySymbol;
	this.PriceRangeMin;
	this.PriceRangeMax;
	this.RatingRangeMin;
	this.RatingRangeMax;
	
	this.actual_sort_index;
	this.actual_sort_order;
	this.indexes;
	
	this.city_map_toggle;
	this.pweb_maps;
	
	this.init(); 
}

//init after document ready

PWebFilterApp.prototype.init = function() { 

	this.template           = document.getElementById('template').innerHTML; 
	
	this.$data_div          = $('#property_list');
	this.$sort_controls_div = $('#data_sort_controls');
	this.$data_empty_msg    = $('#no_data_msg');
	this.$data_loading_msg  = $('#loading_data_msg');
	
	//Filter controls init
	this.TypeFilterCheckBoxes = new GroupCheckBoxes("cb_group_type_filter",true);
	this.FacilitiesFilterCheckBoxes = new GroupCheckBoxes("cb_group_facilities_filter");
	this.DistrictsCheckBoxes  = new GroupCheckBoxes("cb_group_districts_filter");
	this.LandmarksCheckBoxes  = new GroupCheckBoxes("cb_group_landmarks_filter");
	
	this.DowntownExtraCheckId  = 'landmark-downtown';
	this.BreakfastExtraCheckId = 'facility-free-breakfast';
	this.hasDowntownFilter  = false;
	this.hasBreakfastFilter = false;
	
	this.FiltersCounts     = new Array();
	this.FiltersInitValues = new Array();
	
	//Filter counts init
	this.FiltersCounts = new Array();
	this.FiltersCounts['city_results_count_total'] = 0;
	this.init_counts();
	
	//Set these range to not set
	this.PriceRangeMin  = -1;
	this.PriceRangeMax  = -1;
	this.RatingRangeMin = -1;
	this.RatingRangeMax = -1;
	
	this.PriceCurrencySymbol = '$';
	
	this.indexes = [
						{
							"row": "propertyNumber",
							"grouped": false,
							"ordered": true,
							"type": jOrder.number
						},
						{
							"row": "propertyName",
							"grouped": true,
							"ordered": true,
							"type": jOrder.string
						},
						{
							"row": "propertyType",
							"grouped": true,
							"ordered": true,
							"type": jOrder.string
						},
						{
							"row": "display_price",
							"grouped": true,
							"ordered": true,
							"type": jOrder.number
						},
						{
							"row": "overall_rating",
							"grouped": true,
							"ordered": true,
							"type": jOrder.number
						}
				];
	this.actual_sort_index = this.indexes[3];
	this.actual_sort_order = jOrder.asc;
	
	this.results_limit = 20;
	
	this.pweb_maps = new Array();
	
}; // end init()

PWebFilterApp.prototype.set_init_filters_value = function() {
	this.FiltersInitValues[this.TypeFilterCheckBoxes.$checkall_li[0].firstChild.id] = this.TypeFilterCheckBoxes.$checkall_li[0].firstChild.checked;
	for (var i = 0; i < this.TypeFilterCheckBoxes.$checkboxes_li.length; i++)
	{
		this.FiltersInitValues[this.TypeFilterCheckBoxes.$checkboxes_li[i].firstChild.id] = this.TypeFilterCheckBoxes.$checkboxes_li[i].firstChild.checked;
	}
	
	for (var i = 0; i < this.FacilitiesFilterCheckBoxes.$checkboxes_li.length; i++)
	{
		this.FiltersInitValues[this.FacilitiesFilterCheckBoxes.$checkboxes_li[i].firstChild.id] = this.FacilitiesFilterCheckBoxes.$checkboxes_li[i].firstChild.checked;
	}
	for (var i = 0; i < this.DistrictsCheckBoxes.$checkboxes_li.length; i++)
	{
		this.FiltersInitValues[this.DistrictsCheckBoxes.$checkboxes_li[i].firstChild.id] = this.DistrictsCheckBoxes.$checkboxes_li[i].firstChild.checked;
	}
	for (var i = 0; i < this.LandmarksCheckBoxes.$checkboxes_li.length; i++)
	{
		this.FiltersInitValues[this.LandmarksCheckBoxes.$checkboxes_li[i].firstChild.id] = this.LandmarksCheckBoxes.$checkboxes_li[i].firstChild.checked;
	}
};

PWebFilterApp.prototype.reset_filters = function() {
	var that = this;
	this.PriceFilterMin = this.PriceRangeMin;
	this.PriceFilterMax = this.PriceRangeMax;
	this.RatingFilterMin = this.RatingRangeMin;
	this.RatingFilterMax = this.RatingRangeMax;
	
	//Change value without filtering results
	$( "#slider_price" ).slider( {change: null} );
	$( "#slider_price" ).slider({values: [ that.PriceRangeMin, that.PriceRangeMax ]});
	
	document.getElementById('filter_price').innerHTML = 
							that.PriceCurrencySymbol + $( "#slider_price" ).slider( "values", 0 ) +
								" - "+that.PriceCurrencySymbol + $( "#slider_price" ).slider( "values", 1 );
	
	$( "#slider_price" ).slider( {
			change: function( event, ui ) {
			that.change_price_filter(event, ui);
		}
	} );
	
	$( "#slider_rating" ).slider( {change: null} );
	$( "#slider_rating" ).slider({values: [ that.RatingRangeMin, that.RatingRangeMax ]});
	
	document.getElementById('filter_rating').innerHTML = 
							$( "#slider_rating" ).slider( "values", 0 ) +
								"% - " + $( "#slider_rating" ).slider( "values", 1 )+"%";
	
	$( "#slider_rating" ).slider( {
			change: function( event, ui ) {
			that.change_rating_filter(event, ui);
		}
	} );
	
	for (var id in this.FiltersInitValues)
	{ 
		$('#'+id).attr('checked',this.FiltersInitValues[id]);
	}
};

PWebFilterApp.prototype.addFilterMap = function(map_slug, city_map_div_id, map_lang, lat, lng) {
	this.pweb_maps[map_slug] = new PWebFilterMap(city_map_div_id, map_lang, lat, lng);
};

PWebFilterApp.prototype.apply_filters = function() {
	
	this.results_limit = 20;
	
	this.$data_empty_msg.hide();
	this.$sort_controls_div.hide();
	this.$data_div.html("");
	this.$data_loading_msg.show();
	$('#show_more_results').hide();
	
	this.init_counts();
	
	this.jtable_hits = this.jtable.filter(this.get_filters());
	
//	this.compute_counts();
	this.update_counts();
	
	this.sort_hits(this.actual_sort_index.row, this.actual_sort_order);
    this.update();
    this.updateMap();
}; // end apply_filters() 

PWebFilterApp.prototype.updateMap = function() { 
	//Re initiatilize prop_number_to_focus of property map
	this.pweb_maps['property'].prop_number_to_focus = -1;
	
	if(this.pweb_maps['city'].enabled === true)
	{
		this.pweb_maps['city'].updateMarkers(this.jtable_hits);
	}
	this.pweb_maps['city'].reDraw();
};
PWebFilterApp.prototype.update = function() { 
	var that = this;
	
	//Re initiatilize prop_number_to_focus of property map
	this.pweb_maps['property'].prop_number_to_focus = -1;

	if(this.jtable_hits_sorted.length <= 0)
	{
		this.$data_loading_msg.hide();
		this.$data_empty_msg.show();
		this.$sort_controls_div.hide();
		this.$data_div.html("");
	}
	else
	{
		var output = Mustache.to_html(this.template, { "properties": this.jtable_hits_sorted});
		
		this.$data_loading_msg.hide();
		this.$sort_controls_div.show();
		this.$data_div.html(output);
		
		//Init jquery UI tabs
		$('.hostel_list').tabs();
		
		//Map tab events
		that.tabs_map_binded = new Array();
		$('a[name=city_map_show_property]').click(function()
			{
				that.tabs_count = 0;
			
				if(that.tabs_map_binded[this.rel] !== true)
				{
					
					$('#prop_tab_box_'+this.rel).bind( "tabsshow", 
						function(event, ui) {
							var prop_number;
							if(that.tabs_count > 2)
							{
								prop_number = $("#"+ui.target.id).attr("rel");
								//If map tab is selected
								if(!$('#city_map_'+prop_number).hasClass('ui-tabs-hide'))
								{
									that.changeMapProperty('property',prop_number);
								}
							}
								 
							that.tabs_count = that.tabs_count + 1;
						});
					that.tabs_map_binded[this.rel] = true;
				}
				else
				{
					
				}
				return false;
			});
		this.display_extra_filters();
	}
	
	//update count
	this.FiltersCounts['city_results_count_current'] = this.jtable_hits_sorted.length;
	this.FiltersCounts['city_results_count_total']   = this.FiltersCounts['city_results_filtered'];
	this.update_counts();
	
	
	if(this.FiltersCounts['city_results_count_current'] < this.FiltersCounts['city_results_count_total'])
	{
		$('#show_more_results').show();
	}
	else
	{
		$('#show_more_results').hide();
	}
	//Review tab events
	$('a[name=review_show_property]').click(function()
	{
		var prop_number = this.rel;
		
		$("#city_comments_"+prop_number).html('<p><img src="http://'+window.location.host+'/images/V2/loading-squares.gif" alt="" /></p>');
		$.ajax(
			{
				type:"POST",
				url:"http://"+window.location.host+"/reviews_map/"+prop_number+"/2",
				success:function(data)
						{
							$("#city_comments_"+prop_number).html(data);
						}
			});
	});
	
	$("a.prop_more_info").click(function (){
		var ID = $(this).attr('rel');
		$("#prop_more_info_wrap_"+ID).toggle();
		return false;
	});
	
	$("a.prop_more_info_close").click(function (){
		var ID = $(this).attr('rel');
		$("#prop_more_info_wrap_"+ID).toggle();
		return false;
	});
}; // end init() 

PWebFilterApp.prototype.changeMapProperty = function(map_slug, prop_number) {
	
	if(this.pweb_maps[map_slug].prop_number_to_focus !== prop_number)
	{
		//close last opened map tab & clear map from that div
		if(this.pweb_maps[map_slug].prop_number_to_focus > 0)
		{
			$("#first_tab_"+this.pweb_maps[map_slug].prop_number_to_focus).click();
			this.pweb_maps[map_slug].gmap.clearMapDiv();
		}
		
		//Change div to display map
		if(prop_number > 0)
		{
			this.pweb_maps[map_slug].gmap.setMapDivId("city_map_view_"+prop_number);
		}
		
		//Set markers and focus
		this.pweb_maps[map_slug].prop_number_to_focus = prop_number;
		this.pweb_maps[map_slug].updateMarkers(this.jtable_hits);
		
		//Enable and draw map
		this.pweb_maps[map_slug].enableMap();
		
	}
};
PWebFilterApp.prototype.fetch_index = function(rowname) {
	var index = false;
	jQuery.each(this.indexes, function() {
		if(rowname.toString() === this.row.toString())
		{
			index = this;
		}
	});
	return index;
};

PWebFilterApp.prototype.sort_hits = function(indexname,dir,update) { 
	
	this.actual_sort_index = this.fetch_index(indexname);
	this.actual_sort_order = dir;
	
	if(this.actual_sort_index === false)
	{
		//log error in console
		return false;
	}
	
	this.jtable_hits_sorted = jOrder( this.jtable_hits )
	    .index('propertyNumber', ['propertyNumber'], { grouped: false, ordered: true, type: jOrder.number })
	    .index(this.actual_sort_index.row, [this.actual_sort_index.row], {grouped: true, ordered: true, type: this.actual_sort_index.type})
//	    .orderby([this.actual_sort_index.row], this.actual_sort_order);
	    .orderby([this.actual_sort_index.row], this.actual_sort_order,{ indexName: this.actual_sort_index.row,offset: 0, limit: this.results_limit });
	
	if(update !== undefined)
	{
		this.update();
	}
	
};// end sort_hits

PWebFilterApp.prototype.init_counts = function() {
	this.FiltersCounts['city_results_count_current'] = 0;
	this.FiltersCounts['city_results_filtered']      = 0;
	this.FiltersCounts['prop-types-count-0'] = 0;
	this.FiltersCounts['prop-types-count-1'] = 0;
	this.FiltersCounts['prop-types-count-2'] = 0;
	this.FiltersCounts['prop-types-count-3'] = 0;
	this.FiltersCounts['prop-types-count-4'] = 0;
	this.FiltersCounts['prop-types-count-5'] = 0;
//	for (var i = 0; i < this.FacilitiesFilterCheckBoxes.$checkboxes_li.length; i++)
//	{
//		this.FiltersCounts['facility-count-'+this.FacilitiesFilterCheckBoxes.$checkboxes_li[i].firstChild.value] = 0;
//	}
//	for (var i = 0; i < this.DistrictsCheckBoxes.$checkboxes_li.length; i++)
//	{
//		this.FiltersCounts['district-count-'+this.DistrictsCheckBoxes.$checkboxes_li[i].firstChild.value] = 0;
//	}
//	for (var i = 0; i < this.LandmarksCheckBoxes.$checkboxes_li.length; i++)
//	{
//		this.FiltersCounts['landmark-count-'+this.LandmarksCheckBoxes.$checkboxes_li[i].firstChild.value] = 0;
//	}
};

//Compute counts
PWebFilterApp.prototype.display_extra_filters = function() {

	this.hasDowntownFilter  = false;
	this.hasBreakfastFilter = false;
	
	for (var index in this.jtable_hits)
	{
		if(this.hasDowntownFilter === false)
		{
			for (var i = 0; i < this.jtable_hits[index].landmarks.length; i++) {
				
				if(this.jtable_hits[index].landmarks[i].slug  === "City-Center")
				{
					this.hasDowntownFilter = true;
				}
			}
		}
		
		if(this.hasBreakfastFilter === false)
		{
			for (var i = 0; i < this.jtable_hits[index].amenities.length; i++) {
				
				if(this.jtable_hits[index].amenities[i].slug == 'free-breakfast')
				{
					this.hasBreakfastFilter = true;
				}
			}
		}
		
		if((this.hasDowntownFilter === true) &&
		  (this.hasBreakfastFilter === true))
		{
			break;
		}
	}
	
	if(this.hasDowntownFilter === true)
	{
		$('#downtown_2nd_filter').parent().show();
	}
	else
	{
		$('#downtown_2nd_filter').parent().hide();
	}
	
	if(this.hasBreakfastFilter === true)
	{
		$('#breakfast_2nd_filter').parent().show();
	}
	else
	{
		$('#breakfast_2nd_filter').parent().hide();
	}
	
};
//Compute counts
PWebFilterApp.prototype.compute_counts = function() {
	//compute counts
	this.compute_district_counts();
};

PWebFilterApp.prototype.compute_district_counts = function() {
	for (var index in this.jtable_hits)
	{
		for (var di = 0; di < this.DistrictsCheckBoxes.$checkboxes_li.length; di++)
		{
			var current_district_id = parseInt(this.DistrictsCheckBoxes.$checkboxes_li[di].firstChild.value);
			for (var pdi = 0; pdi < this.jtable_hits[index].districts.length; pdi++)
			{
				if( current_district_id == parseInt(this.jtable_hits[index].districts[pdi].district_id))
				{
					this.FiltersCounts['district-count-'+current_district_id]++;
				}
			}
		}
		for (var di = 0; di < this.LandmarksCheckBoxes.$checkboxes_li.length; di++)
		{
			var current_landmark_id = parseInt(this.LandmarksCheckBoxes.$checkboxes_li[di].firstChild.value);
			for (var pdi = 0; pdi < this.jtable_hits[index].landmarks.length; pdi++)
			{
				if( current_landmark_id === parseInt(this.jtable_hits[index].landmarks[pdi].landmark_id))
				{
					this.FiltersCounts['landmark-count-'+current_landmark_id]++;
				}
			}
		}
		
		for (var di = 0; di < this.FacilitiesFilterCheckBoxes.$checkboxes_li.length; di++)
		{
			var current_facility_id = parseInt(this.FacilitiesFilterCheckBoxes.$checkboxes_li[di].firstChild.value);
			for (var pdi = 0; pdi < this.jtable_hits[index].amenities_filter.length; pdi++)
			{
				if( current_facility_id === parseInt(this.jtable_hits[index].amenities_filter[pdi]))
				{
					this.FiltersCounts['facility-count-'+current_facility_id]++;
				}
			}
		}
	}
};

PWebFilterApp.prototype.update_counts = function() {
	
	for (var id in this.FiltersCounts)
	{ 
		
		$('#'+id).html(this.FiltersCounts[id]);
	}
//	$('#city_results_count_current').html(this.jtable_hits.count());
	//city_results_count_current
};
PWebFilterApp.prototype.get_filters = function() {

	//All filters values selected
	var that = this,
		types_filter      = this.TypeFilterCheckBoxes.getCheckedValues(),
	    facilities_filter = this.FacilitiesFilterCheckBoxes.getCheckedValues(),
	    districts_filter  = this.DistrictsCheckBoxes.getCheckedValues(),
	    landmarks_filter  = this.LandmarksCheckBoxes.getCheckedValues(),
		pricemin_filter   = this.PriceFilterMin || -1,
		pricemax_filter   = this.PriceFilterMax || -1,
		ratingmin_filter  = this.RatingFilterMin || -1,
		ratingmax_filter  = this.RatingFilterMax || -1,
		minnight_filter   = true;
	
	//Get data values matching filters
	//Set appropriate counts
	return function(property) {
	
		var match_type = false,
		    match_facility = false,
		    match_all_facility = true,
		    match_district = false,
		    match_landmark = false,
		    match_price = false,
			match_rating = false;
		
		//Filter out property that requires more night than user asked
		if((minnight_filter === true) &&
		   (property.minNights >= that.request.numnights_selected))
		{
			return false;
		}
		//compute Min avail price
		if(that.PriceRangeMin === -1)
		{
			that.PriceRangeMin = property.display_price;
		}
		else if(that.PriceRangeMin > property.display_price)
		{
			that.PriceRangeMin = property.display_price;
		}	
		//compute max avail price
		if(that.PriceRangeMax === -1)
		{
			that.PriceRangeMax = property.display_price;
		}
		else if(that.PriceRangeMax < property.display_price)
		{
			that.PriceRangeMax = property.display_price;
		}
		//compute min avail rating
		if(that.RatingRangeMin === -1)
		{
			that.RatingRangeMin = property.overall_rating;
		}
		else if(that.RatingRangeMin > property.overall_rating)
		{
			that.RatingRangeMin = property.overall_rating;
		}
		//compute max avail rating
		if(that.RatingRangeMax === -1)
		{
			that.RatingRangeMax = property.overall_rating;
		}
		else if(that.RatingRangeMax < property.overall_rating)
		{
			that.RatingRangeMax = property.overall_rating;
		}
		
		//Property type filter
	
		jQuery.each(types_filter, function() { 
			if((this.toString() === "type_hostels")&&
			  (property.propertyType === "Hostel"))
			{
				match_type =  true;
				return true;
			}
			else if((this.toString() === "type_hotels")&&
			  (property.propertyType === "Hotel"))
			{
				match_type =  true;
				return true;
			}
			else if((this.toString() === "type_apartments")&&
			  (property.propertyType === "Apartment"))
			{
				match_type =  true;
				return true;
			}
			
			else if((this.toString() === "type_bbs")&&
			  (property.propertyType === "Guesthouse"))	
			{
				match_type =  true;
				return true;
			}
			else if((this.toString() === "type_campings")&&
			  ((property.propertyType === "Camping")||((property.propertyType === "Campsite"))))
			{
				match_type =  true;
				return true;
			}
			
		});
		
		//If no district filter is selected match all
		//Else match only the checked filter
		
		if(facilities_filter.length === 0)
		{
			match_facility = true;
			
			//compute counts
//			for (var di = 0; di < that.FacilitiesFilterCheckBoxes.$checkboxes_li.length; di++)
//			{
//				var current_facility_id = parseInt(that.FacilitiesFilterCheckBoxes.$checkboxes_li[di].firstChild.value);
//				for (var pdi = 0; pdi < property.amenities_filter.length; pdi++) {
//					
//					if( current_facility_id=== parseInt(property.amenities_filter[pdi]))
//					{
//						that.FiltersCounts['facility-count-'+current_facility_id]++;
//					}
//				}
//			}
		}
		else
		{
			//Property district filter
			jQuery.each(facilities_filter, function() {
				
				var target_filter_id = this.toString();
				//if array property.amenities_filter is not empty
				// and contains ALL of the facilities filter match it and return
				for (var i = 0; i < property.amenities_filter.length; i++) {
					
					if(target_filter_id === property.amenities_filter[i].toString())
					{
						match_all_facility = match_all_facility && true;
						return true;
					}
				}
				//When the loop did 
				match_all_facility = false;
			});
			match_facility = match_all_facility;
		}
		//If no district filter is selected match all and compute count
		//Else match only the checked filter
		if(districts_filter.length === 0)
		{
			match_district = true;
			
			//compute counts
//			for (var di = 0; di < that.DistrictsCheckBoxes.$checkboxes_li.length; di++)
//			{
//				var current_district_id = parseInt(that.DistrictsCheckBoxes.$checkboxes_li[di].firstChild.value);
//				for (var pdi = 0; pdi < property.districts.length; pdi++) {
//					
//					if( current_district_id=== parseInt(property.districts[pdi].district_id))
//					{
//						that.FiltersCounts['district-count-'+current_district_id]++;
//					}
//				}
//			}
		}
		else
		{
			//Property district filter
			jQuery.each(districts_filter, function() {
				
				var target_filter_id = parseInt(this);
				//if array property.distritcts is not empty
				// and contains one of the district filter match it and return
				for (var i = 0; i < property.districts.length; i++) {
					
					if(target_filter_id === parseInt(property.districts[i].district_id))
					{
						match_district =  true;
//						that.FiltersCounts['district-count-'+target_filter_id]++;
						return true;
					}
				}
			});
		}
	
		//If no landmark filter is selected match all
		//Else match only the checked filter
		if(landmarks_filter.length === 0)
		{
			match_landmark = true;
			
			//compute counts
//			for (var di = 0; di < that.LandmarksCheckBoxes.$checkboxes_li.length; di++)
//			{
//				var current_landmark_id = parseInt(that.LandmarksCheckBoxes.$checkboxes_li[di].firstChild.value);
//				for (var pdi = 0; pdi < property.landmarks.length; pdi++) {
//					
//					if( current_landmark_id=== parseInt(property.landmarks[pdi].landmark_id))
//					{
//						that.FiltersCounts['landmark-count-'+current_landmark_id]++;
//					}
//				}
//			}
		}
		else
		{
			//Property district filter
			jQuery.each(landmarks_filter, function() {
				
				var target_filter_id = parseInt(this);
				//if array property.distritcts is not empty
				// and contains one of the district filter match it and return
				for (var i = 0; i < property.landmarks.length; i++) {
					
					if(target_filter_id === parseInt(property.landmarks[i].landmark_id))
					{
						match_landmark =  true;
//						that.FiltersCounts['landmark-count-'+current_landmark_id]++;
						return true;
					}
					
				}
			});
		}
		//Property price filter
		//if filter is not set automatically match
		if(pricemax_filter === -1)
		{
			match_price = true;
		}
		//filter is set AND and filter match
		else if((property.display_price <= pricemax_filter) &&
				(property.display_price >= pricemin_filter))
		{
			match_price = true;
		}
		
		//Property rating filter
		//if filter is not set automatically match
		if(ratingmax_filter === -1)
		{
			match_rating = true;
		}
		else if((property.overall_rating <= ratingmax_filter) &&
				(property.overall_rating >= ratingmin_filter))
		{
			match_rating = true;
		}
		
		/*if((match_type && match_facility && match_price && match_rating && match_district && match_landmark) === true)
		{
			that.FiltersCounts['city_results_filtered']++;
			return true;
		}else{
			return false;
		}*/
		
		if((match_type && match_facility && match_price && match_rating && match_district && match_landmark) === true)
		{
			
			if(match_type){
				that.FiltersCounts['city_results_filtered']++;
				that.FiltersCounts['prop-types-count-0']++;
				
			//return true;
				}
				
				
			
			
			if((property.propertyType === "Hostel"))
			{
				that.FiltersCounts['prop-types-count-1']++;
				return true;
			}
			else if((property.propertyType === "Hotel"))
			{
				that.FiltersCounts['prop-types-count-2']++;
				return true;
			}
			else if((property.propertyType === "Apartment"))
			{
				that.FiltersCounts['prop-types-count-3']++;
				return true;
			}
			else if((property.propertyType === "Guesthouse"))
			{
				that.FiltersCounts['prop-types-count-4']++;
				return true;
			}
			else if((property.propertyType === "Camping")||((property.propertyType === "Campsite")))
			{
				that.FiltersCounts['prop-types-count-5']++;
				return true;
			}
			
	}
		
		
		
	
	};
};


PWebFilterApp.prototype.setData = function(json_data) {
	jOrder.logging = null;
	this.jtable = jOrder(json_data)
				    .index('propertyNumber', ['propertyNumber'], { grouped: false, ordered: true, type: jOrder.number })
				    .index('propertyType', ['propertyType'], { grouped: true , ordered: true, type: jOrder.string });
	this.FiltersCounts['city_results_count_total'] = json_data.length;
	
};

PWebFilterApp.prototype.setRequestData = function(json_request_data) {
	this.request = json_request_data;
	this.PriceCurrencySymbol = this.request.display_currency;
};

PWebFilterApp.prototype.change_price_filter = function(event, ui) {
	this.PriceFilterMin = ui.values[ 0 ];
	this.PriceFilterMax = ui.values[ 1 ];
	this.apply_filters();
};

PWebFilterApp.prototype.change_rating_filter = function(event, ui) {
	this.RatingFilterMin = ui.values[ 0 ];
	this.RatingFilterMax = ui.values[ 1 ];
	this.apply_filters();
};

PWebFilterApp.prototype.init_action_filters = function() {
	var that = this;
	this.PriceRangeMin = Math.floor(this.PriceRangeMin);
	this.PriceRangeMax = Math.ceil(this.PriceRangeMax);
	
	$( "#slider_price" ).slider({
		range: true,
		min: that.PriceRangeMin,
		max: that.PriceRangeMax,
		values: [ that.PriceRangeMin, that.PriceRangeMax ],
		slide: function( event, ui ) {
			document.getElementById('filter_price').innerHTML = that.PriceCurrencySymbol + ui.values[ 0 ] + " - "+that.PriceCurrencySymbol + ui.values[ 1 ] ;
		},
		change: function( event, ui ) {
			that.change_price_filter(event, ui);
		}
	});
	document.getElementById('filter_price').innerHTML = that.PriceCurrencySymbol + $( "#slider_price" ).slider( "values", 0 ) +
		" - "+that.PriceCurrencySymbol + $( "#slider_price" ).slider( "values", 1 );
	//$( "#filter_price" ).text( that.PriceCurrencySymbol + $( "#slider_price" ).slider( "values", 0 ) + " - "+that.PriceCurrencySymbol + $( "#slider_price" ).slider( "values", 1 ) );
	
	$( "#slider_rating" ).slider({
		range: true,
		min: that.RatingRangeMin,
		max: that.RatingRangeMax,
		values: [ that.RatingRangeMin, that.RatingRangeMax ],
		slide: function( event, ui ) {
			document.getElementById('filter_rating').innerHTML = ui.values[ 0 ] + "% - " + ui.values[ 1 ] + "%";
			that.RatingFilterMin = ui.values[ 0 ];
			that.RatingFilterMax = ui.values[ 1 ];
		},
		change: function( event, ui ) {
			that.change_rating_filter(event, ui);
		} 
	});
	document.getElementById('filter_rating').innerHTML = $( "#slider_rating" ).slider( "values", 0 ) +
		"% - " + $( "#slider_rating" ).slider( "values", 1 )+"%";
	
	$('#breakfast_2nd_filter').click(function ()
			{
				if($('#breakfast_2nd_filter').attr('checked') === true)
				{
					$('#'+that.BreakfastExtraCheckId).attr('checked', true);
				}
				else
				{
					$('#'+that.BreakfastExtraCheckId).attr('checked', false);
				}
				that.apply_filters();
			});
	
	$('#downtown_2nd_filter').click(function ()
			{
				if($('#downtown_2nd_filter').attr('checked') === true)
				{
					$('#'+that.DowntownExtraCheckId).attr('checked', true);
				}
				else
				{
					$('#'+that.DowntownExtraCheckId).attr('checked', false);
				}
				that.apply_filters();
			});
	
	//synchronize checkboxes
	$('#'+this.BreakfastExtraCheckId).click(function ()
			{
				if($('#'+that.BreakfastExtraCheckId).attr('checked') === true)
				{
					$('#breakfast_2nd_filter').attr('checked', true);
				}
				else
				{
					$('#breakfast_2nd_filter').attr('checked', false);
				}
					
			});
	$('#'+this.DowntownExtraCheckId).click(function ()
			{
				if($('#'+that.DowntownExtraCheckId).attr('checked') === true)
				{
					$('#downtown_2nd_filter').attr('checked', true);
				}
				else
				{
					$('#downtown_2nd_filter').attr('checked', false);
				}
			});
	
	this.TypeFilterCheckBoxes.clickAction(function (){
		that.apply_filters();
 	});
	this.FacilitiesFilterCheckBoxes.clickAction(function (){
		that.apply_filters();
	});
	this.DistrictsCheckBoxes.clickAction(function (){
		that.apply_filters();
	});
	this.LandmarksCheckBoxes.clickAction(function (){
		that.apply_filters();
	});
};
PWebFilterApp.prototype.setClickSort = function(divID, DOMNodeID, rowname) {
	var that = this;
	
	$('#'+DOMNodeID).click(function(){

		$('#'+divID+' .sorting').removeClass('activesort');
		$(this).addClass('activesort');

		if($(this).children().hasClass('asc'))
	{
			$(this).children().removeClass('asc');
			$(this).children().addClass('desc');
			that.sort_hits(rowname,jOrder.desc,true);
	}
		else
		{
			$(this).children().removeClass('desc');
			$(this).children().addClass('asc');

			that.sort_hits(rowname,jOrder.asc,true);
		}
		return false;
	});
};

PWebFilterApp.prototype.refresh = function(more_results) {
	more_results   = more_results || 0;
	
	this.results_limit = this.results_limit + more_results;
	this.sort_hits(this.actual_sort_index.row, this.actual_sort_order,true);
};
PWebFilterApp.prototype.toggleMap = function(map_slug) {
	this.pweb_maps[map_slug].toggle();
	
	if(this.pweb_maps[map_slug].isMapEnable() === true)
	{
		this.pweb_maps[map_slug].updateMarkers(this.jtable_hits);
	}
};


var totalRecords = 0;
var hostelCount = 0;
var apartmentCount = 0;
var guesthouseCount = 0;
var hotelCount = 0;
var campCount = 0;
//Put setup filter in PWebFilterApp prototypes?
function setup_filters(data)
{
	data = jQuery.parseJSON(data);
	pweb_filter.setRequestData(data.request);
	pweb_filter.setData(data.property_list);
	
	totalRecords = data.property_list.length;
	
/*	//check , count and show property type.
	for(var i=0;i<data.property_list.length;i++)
	{
		if(data.property_list[i].propertyType=='Hostel')
		{
			hostelCount = hostelCount+1;
		}
		else if(data.property_list[i].propertyType=='Guesthouse')
		{
			
			guesthouseCount = guesthouseCount+1;
		}
		else if(data.property_list[i].propertyType=='Apartment')
		{
			apartmentCount = apartmentCount+1;
		}
		else if(data.property_list[i].propertyType=='Hotel')
		{
			hotelCount = hotelCount+1;
		}
		else if(data.property_list[i].propertyType=='Camping' || data.property_list[i].propertyType=='Campsite')
		{
			campCount = campCount+1;
		}
	}
	
	$('#prop-types-count-0').text(totalRecords);
	$('#prop-types-count-1').text(hostelCount);
	$('#prop-types-count-2').text(hotelCount);
	$('#prop-types-count-3').text(apartmentCount);
	$('#prop-types-count-4').text(guesthouseCount);
	$('#prop-types-count-5').text(campCount);
*/	
	
	pweb_filter.addFilterMap('city', 'city_map_container', 'en', data.city_info.city_geo_lat, data.city_info.city_geo_lng);
	pweb_filter.addFilterMap('property', "will_set_on_tab_click", 'en', data.city_info.city_geo_lat, data.city_info.city_geo_lng);

	pweb_filter.setClickSort('data_sort_controls','sortname-tous','propertyName');
	pweb_filter.setClickSort('data_sort_controls','sortprice-tous','display_price');
	pweb_filter.setClickSort('data_sort_controls','sortcote-tous','overall_rating');
	$('#data_sort_controls').show();
	pweb_filter.apply_filters();
	pweb_filter.set_init_filters_value();
	
	//Eventually create a addFilter function for time saving now everything is in init_filters
	pweb_filter.init_action_filters();
	
	$('#show_more_results').click(function()
	{
		pweb_filter.refresh(10);
		return false;
	});
	
	//TO MOVE IN pwe-mapping CitySearchMap.js ? 
	$('#city_map_show_1').click(function()
	{
		pweb_filter.toggleMap('city');
		$('#map_button_side').hide();
		$('#city_map_show_2').hide();
		$('#city_map_hide').show();
		return false;
	});
	$('#city_map_show_2').click(function()
	{
		pweb_filter.toggleMap('city');
		$(this).hide();
		$('#map_button_side').hide();
		$('#city_map_hide').show();
		return false;
	});
	$('#city_map_hide').click(function()
	{
		pweb_filter.toggleMap('city');
		$(this).hide();
		$('#city_map_show_2').show();
		$('#map_button_side').show();
		return false;
	});
	
	$('#reset_filters').click(function()
			{
				pweb_filter.reset_filters();
				pweb_filter.apply_filters();
				return false;
			});
	
}
$(document).ready(function() { 

	pweb_filter = new PWebFilterApp();
}); // end ready event 
