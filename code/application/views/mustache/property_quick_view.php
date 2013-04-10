<div class="main-cotainer"  id="main-div">
	<div>
		<div style="float: left;margin: 10px 0;width: 48px;">
			{{#prevIndex}}
				<div style="display: inline;float: left;">
					<a href="<?php echo base_url(); ?>" onclick="QuickView.moveToIndex('{{prevIndex}}'); return false;" class="pre_next_arrows"><img src="<?php echo base_url(); ?>images/left.png"/></a>
				</div>
			{{/prevIndex}}
			{{#nextIndex}}
				<div style="display: inline;float: right;">
					<a href="<?php echo base_url(); ?>" onclick="QuickView.moveToIndex('{{nextIndex}}'); return false;" class="pre_next_arrows"><img src="<?php echo base_url(); ?>images/right.png"/></a>
				</div>
			{{/nextIndex}}
		</div>

		<div style="float: left;margin: 10px 10px;">
			<h2 style="display:inline-block; margin:0;">
				<a style="text-decoration:none; color: #3087C9; font-size:18px;" href="{{propertyUrl}}">{{propertyNmae}}</a>
			</h2>

			<br style="clear: both" /> 
			<div class="content_block">{{address1}} - {{city_name}}</div>
		</div>

		<a href="{{propertyUrl}}" style="height: 25px; float: right; position: relative; line-height: 25px; padding: 9px 45px" class="reserve reserve-round button-green hoverit" title="<?php echo _("Plus sur ce logement");?>"><?php echo _("Réserver");?></a>
	</div>

	<div class="top-map">
		<div class="top-map-left">
			<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/quick_view.css" media="all" />
			<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/jquery.ad-gallery.css" media="all" />

			<div id="gallery" class="ad-gallery">
				<div class="ad-image-wrapper"></div>

				<div class="ad-nav">
					<div class="ad-thumbs">
						<ul class="ad-thumb-list">
							{{#IMAGES}}
							<li>
								<a href="{{.}}">
									<img src="{{.}}" height="53" width="74">
								</a>
							</li>
							{{/IMAGES}}

							{{#HW_IMAGES}}
							<li>
								<a href="{{imageURL}}">
									<img src="{{imageURL}}" height="53" width="74">
								</a>
							</li>
							{{/HW_IMAGES}}

						</ul>
					</div>
				</div>
			</div>

			<div class="bottom-feature">
				<div class="amenities no-indent">
					{{#allAmenities}}
						{{#to_display}}
							<span class="icon_facility icon_facility_{{facility_id}} group">
                        					<span>{{description}}</span>
                    					</span>
						{{/to_display}}
					{{/allAmenities}}

					{{#landmarks}}
						{{#to_display}}
							<span class="icon_facility icon_landmark group"><span>{{landmark_name}}</span></span>
						{{/to_display}}
					{{/landmarks}}
				</div>

				{{#hasIncludes}}
					<div class="bottom-feature-data">
						<p><b><?php echo _("What's Included");?></b></p>							
						<div class="group">
							<ul class="green-li increase1 translated">
								{{#includes}}
									<li>{{description}}{{#isHB}}: <?php echo '<strong>' . _("Free") . '</strong>'; ?>{{/isHB}}</li>
								{{/includes}}
							</ul>
						</div>
					</div>
				{{/hasIncludes}}

				{{#hasAmenities}}
					<div class="bottom-feature-data">
						<p><b><?php echo _("Commodité");?></b></p>
						<div class="list-left">
							{{#amenities}}
									<div class="check">{{description}}</div>
							{{/amenities}}
						</div>
					</div>
				{{/hasAmenities}}
			</div>
		</div>

		<div class="top-map-right">
			<div class="top-map-inn">
				<div class="top-map-inn1">
					<!--<div class="map" id="map_property" style="height:367px;">
					<?php _('Map');?></div> -->
					<div id="map-wrap" class="map">
						<div id="map_canvas"></div>
					</div>

					<!--location details added here -->	
					<div id="hostel_info_direction" class="hostels_tab_content ui-tabs-hide">
						<div class="content_block">
							{{#hasDistricts}}
								<div id="hostel_mapView_districts" class="hostel_mapView_districts">
									<b><span class="mapView_districtWord"><?php echo _('Districts');?>:</span></b>
									{{#districts}}
										<p>
											<input type="radio" name="distrinct" value="{{um_id}}" onchange="GoogleMap.prototype.changeDistrictLayer('{{um_id}}')">
											{{district_name}}
										</p>
									{{/districts}}
								</div>
							{{/hasDistricts}}

							{{#noDistricts}}
								<div id="hostel_mapView_districts" class="hostel_mapView_districts"></div>
							{{/noDistricts}}

							{{#hasLandmarks}}
								<div id="hostel_mapView_landmarks" class="hostel_mapView_districts">
									<b><span class="mapView_districtWord"><?php echo _('Districts');?>:</span></b>
									{{#landmarks}}
										<p>
											<input type="radio" name="landmark" value="{{geo_latitude}}###{{geo_longitude}}" onchange="GoogleMap.prototype.changeLandmarkLayer('{{geo_latitude}}###{{geo_longitude}}');">
											{{translation_name}}
										</p>
									{{/landmarks}}
								</div>
							{{/hasLandmarks}}

							{{#noLandmarks}}
								<div id="hostel_mapView_landmarks" class="hostel_mapView_landmarks"></div>
							{{/noLandmarks}}
						</div>
					</div>
				</div>
			</div>

			{{#propertyHasImportantInfo}}
				<div class="readmore readmore-image" id="showmore"><?php echo _('Informations Importantes'); ?> <span class="showmore_plus_sign"><strong>+</strong></span> <span class="showmore_minus_sign" style="display:none"><strong>-</strong></span></div>
				<div class="bottom-feature1" id="bottomfeature1">
					<div class="bottom-feature-data1">
						<div class="group">
							{{#propertyConditionsTranslated}}
								<div class="translated">{{propertyConditionsTranslatedText}}</div>
								<div class="original" style="display:none;">{{propertyConditionsOriginal}}</div>
							{{/propertyConditionsTranslated}}

							{{#hasPropertyConditions}}
								{{propertyConditions}}
							{{/hasPropertyConditions}}

							{{#hasPropertyInfo}}
								{{propertyInfo}}
							{{/hasPropertyInfo}}
						</div>
					</div>
				</div>
			{{/propertyHasImportantInfo}}
		</div>
	</div>
</div>
