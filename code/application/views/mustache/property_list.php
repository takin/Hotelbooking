{{#properties}}
<div id="prop_tab_box_{{propertyNumber}}" class="hostel_list search_list openup" rel="{{propertyNumber}}" onmouseover="GoogleMap.prototype.changeMarkerIcon($(this), 'selected'); $('#prop_tab_box_{{propertyNumber}} .displaySaveProperty, #prop_tab_box_{{propertyNumber}} .prices_toggle, #prop_tab_box_{{propertyNumber}} .prices_border').show();" onmouseout="GoogleMap.prototype.changeMarkerIcon($(this),'original'); $('#prop_tab_box_{{propertyNumber}} .displaySaveProperty, #prop_tab_box_{{propertyNumber}} .prices_toggle, #prop_tab_box_{{propertyNumber}} .prices_border').hide();">
	<input type="hidden" value="{{propertyNumber}}" id="hostel_propertyNumber_{{propertyNumber}}" name="hostel_propertyNumber_{{propertyNumber}}" />

	{{#Geo}}
		<input type="hidden" value="{{Latitude}}" id="input_geo_latitude_{{propertyNumber}}" class="input_geo_latitude" name="input_geo_latitude_{{propertyNumber}}" />
		<input type="hidden" value="{{Longitude}}" id="input_geo_longitude_{{propertyNumber}}" class="input_geo_longitude" name="input_geo_longitude_{{propertyNumber}}" />
	{{/Geo}}

	<div class="box_content box_round ui-tabs" id="prop_box_{{propertyNumber}}" style="padding-bottom: 0">
		<div class="city_hostel group" id="city_info_{{propertyNumber}}">
			<div class="info">
				<div class="left info_pic">
					<div id="{{propertyNumber}}" class="picture_number">0</div>
					<a href="{{property_page_url}}" style="position:relative;">
						{{#PropertyImages}}
							<img alt="" src="{{#PropertyImage}}{{imageListURL}}{{/PropertyImage}}" />
						{{/PropertyImages}}

						<?php $displayQuickPreview = $this->config->item('displayQuickPreview');
						if ($displayQuickPreview == 1) { ?>
							<div class="quick_view_bg" id="quick_view_bg_{{propertyNumber}}" style="display:none;">
								<div id="quick_view_bg_link_{{propertyNumber}}" class="display_preview quick_view_bg_link" href="#quick_preview_div" value="{{propertyNumber}}"><?php echo _('Quick View'); ?></div>
							</div>
						<?php } ?>

						<input type="hidden" name="propertycur{{propertyNumber}}" id="propertycur_{{propertyNumber}}" value="{{currency_code}}"/>
					</a>

					<span class="info_type" style="color: #999">{{propertyTypeTranslate}}</span>
				</div>
				<div class="info_indent min-nights">
					<h2>
						<img id ="property_marker_number_{{propertyNumber}}" class="property_marker_number" src="<?php echo site_url(); ?>images/map_markers/unselected/marker_1.png" alt="" onmouseover="GoogleMap.prototype.centerMapMarker();" />
						<a href="{{property_page_url}}" style="vertical-align: middle">
							<span id="hostel_title_{{propertyNumber}}" class="hostel_title">{{propertyName}}</span>
						</a>

						<br />
						<span class="address" style="color: gray; font-size:0.7em; vertical-align: middle;">{{address1}} - {{city_name}}</span>
					</h2>


					{{#isMinNightNeeded}}
						<!-- <p class="minnight">{{minNightsMessage}}</p> -->
					{{/isMinNightNeeded}}
				</div>

				<div class="amenities group">
					{{#amenities}}
						{{#to_display}}
							<span class="icon_facility icon_facility_{{facility_id}} group">
								<span style="line-height: 20px">{{description}}</span>
							</span>
						{{/to_display}}
					{{/amenities}}
					{{#landmarks}}
						{{#to_display}}
							<span class="icon_facility icon_landmark group"><span>{{landmark_name}}</span></span>
						{{/to_display}}
					{{/landmarks}}
					{{#safety80}}
						<!-- <span class="icon_facility icon_safety group"><span><?php echo _("Safety"); ?></span></span> -->
					{{/safety80}}
				</div>

				<div class="city_hostel_districts_landmarks_zone">
					<div class="city_hostel_districts" id="city_hostel_districts_{{propertyNumber}}" name="city_hostel_districts_{{propertyNumber}}">
						<span class="city_hostel_districts_district"><?php echo _('Districts'); ?>:</span>
						<span id="city_hostel_districts_values_{{propertyNumber}}" class="city_hostel_districts_values">
							<span class="show_more show_more_districts" title="<?php echo _('Districts'); ?>|{{#districts}}{{district_name}}, {{/districts}}"><strong>+</strong></span>
							<span class="content">{{#districts}}{{district_name}}, {{/districts}}</span>
						</span>
					</div>

					<div class="city_hostel_landmarks" id="city_hostel_landmarks_{{propertyNumber}}" name="city_hostel_landmarks_{{propertyNumber}}">
						<span class="city_hostel_landmarks_landmark"><?php echo _('Landmarks (within 2km)'); ?>:</span>
						<span id="city_hostel_landmarks_values_{{propertyNumber}}" class="city_hostel_landmarks_values">
							<span class="show_more show_more_landmarks" title="<?php echo _('Landmarks (within 2km)'); ?>|{{#landmarks}}{{translation_name}}, {{/landmarks}}"><strong>+</strong></span>
							<span class="content">{{#landmarks}}{{translation_name}}, {{/landmarks}}</span>
						</span>
					</div>
				</div>

				<div class="info_indent displaySaveProperty" style="clear: both; margin-top: 10px; display: none">
					<?php if ($this->config->item('displaySaveProperty')) { ?>
					<div style="width: 250px; float: left; margin; 0">
						<a href="#" class="save_to_favorites" id="save_to_favorites_{{propertyNumber}}" style="vertical-align: middle; {{#savedToFavorites}}display: none;{{/savedToFavorites}}" rel="{{propertyName}}" title="<?php echo _('You can save this property as a favorite in your account so you can easily book it at a later date if you wish.'); ?>">
							<img style="vertical-align: middle" src="<?php echo site_url(); ?>/images/save_favorite.png" />
							<?php echo _('Add to my favorites'); ?>
						</a>

						<a href="<?php echo site_url('user/favorite_properties'); ?>" class="saved_to_favorites" id="saved_to_favorites_{{propertyNumber}}" style="{{#saveToFavorites}}display: none;{{/saveToFavorites}} vertical-align: middle" title='<?php echo _('This property has been saved in your "My account" section. You can now easily book it at a later date if you wish.'); ?>'>
							<img style="vertical-align: middle" src="<?php echo site_url(); ?>/images/saved_favorite.png" />
							<?php echo _('Saved to my favorites'); ?>
						</a>
					</div>
					<?php } ?>

					<?php $displayCompareProperty = $this->config->item('displayCompareProperty');
					if ($displayCompareProperty == 1) { ?>
						<div class="com_div" style="margin-top: 2px">
							<input type="checkbox" name="pro_compare" id="pro_compare_{{propertyNumber}}" value="{{propertyNumber}}" onclick="compare_property('{{propertyNumber}}', null,'{{propertyType}}');" class="propertycompare"/><label><?php echo _('Compare'); ?> (<span id="compare_count_{{propertyNumber}}" class="compare_count">0</span> <?php echo _('of'); ?> 5)</label>
						</div>
<?php } ?>
				</div>

				<?php if ($this->api_used == HB_API) { ?>
				{{^isRatingsEmpty}}
				<div id="property_ratings_{{propertyNumber}}" class="propertyRatingsBoxd" style="display: none">
					{{#Ratings}}
						<div class="propertyRatingsContainer">
							<!--<h3>
								<?php echo _("évaluation moyenne") . " - " . _("As rated by bookers like you") . ": "; ?>
								{{overall_rating}} %
							</h3> -->

							<?php
							$ratingCategories = array(
								"atmosphere", "staff", "location", "cleanliness",
								"facilities", "safety", "value"
							); ?>

							<?php foreach ($ratingCategories as $ratingCategory): ?>
								{{#<?php echo $ratingCategory; ?>}}
								<div class="bar-back group">
									<div class="bar-top darkYellow" style="width:{{<?php echo $ratingCategory; ?>}}%"></div>

									<?php $imgSrcUrl = base_url() . "images/rating-" . $ratingCategory . ".png"; ?>
									<img alt="" src="<?php echo $imgSrcUrl; ?>"/>

									<span class="rating-cat"><?php echo _(ucfirst($ratingCategory)); ?></span>
									<span class="rating-value">{{<?php echo $ratingCategory; ?>}} %</span>
								</div>
								{{/<?php echo $ratingCategory; ?>}}
							<?php endforeach; ?>
						</div>
					{{/Ratings}}
				</div>
				{{/isRatingsEmpty}}
				<?php } ?>
			</div>

			<?php if ($this->config->item('displayRemoveFromSearch')) { ?>
				<div class="displayRemoveFromSearch">
					<a href="javascript:void(0);" class="remove_from_search_trigger" id="remove_from_search_{{propertyNumber}}" onclick="$('.remove_from_search_options').hide(); $('#remove_from_search_options_{{propertyNumber}}').toggle();">
						<img src="<?php echo site_url(); ?>/images/cls_button.2.png" alt="remove" style="vertical-align:middle" class="remove_from_search_trigger_icon" />
					</a>

					<div class="remove_from_search_options" id="remove_from_search_options_{{propertyNumber}}">
						<ul class="remove_from_search_option">
							<li class="remove_from_search_option">
								<a href="javascript:void(0);" class="remove_from_search remove_property_permanentely" id="remove_property_permanentely_{{propertyNumber}}">
									<img src="<?php echo site_url(); ?>/images/remove_permanentely.png" alt="remove" class="remove_from_search_icon" />
									<?php echo _('Remove from this search'); ?>
								</a>
							</li>
							<li>
								<a href="javascript:void(0);" class="remove_from_search remove_property_one_day" id="remove_property_one_day_{{propertyNumber}}">
									<img src="<?php echo site_url(); ?>/images/remove_temporarly.png" alt="remove" />
									<?php echo _('Remove from any searches for next 24 hours'); ?>
								</a>
							</li>
							<li>
								<a href="javascript:void(0);" class="remove_from_search remove_property_one_week" id="remove_property_one_week_{{propertyNumber}}">
									<img src="<?php echo site_url(); ?>/images/remove_temporarly.png" alt="remove" />
									<?php echo _('Remove from any searches for 1 week'); ?>
								</a>
							</li>
						</ul>
					</div>
				</div>
			<?php } ?>

			<div class="rating">
				<div style="text-align: left; margin-bottom: 20px; height: 30px;">
				<?php $extraCaptionClass = $this->api_used == HB_API ? '' : ' averageRatingCaptionHW';?>
				{{#overall_rating}}
					{{#display_alternate_rating}}
						{{#ratings_safety_safe}}
							<span class="yellow-bg showRatings" rel="{{propertyNumber}}">{{ratings_safety}}%</span>
							<strong class="txt-mid green showRatings" rel="{{propertyNumber}}"><?php echo _('Safe'); ?></strong>
							<span class="averageRatingCaption showRatings<?php echo $extraCaptionClass; ?>" rel="{{propertyNumber}}"><?php echo _('Safety'); ?></span>
						{{/ratings_safety_safe}}
						{{#ratings_safety_very_safe}}
							<span class="yellow-bg showRatings" rel="{{propertyNumber}}">{{ratings_safety}}%</span>
							<strong class="txt-mid green showRatings" rel="{{propertyNumber}}"><?php echo _('Very safe'); ?></strong>
							<span class="averageRatingCaption showRatings<?php echo $extraCaptionClass; ?>" rel="{{propertyNumber}}"><?php echo _('Safety'); ?></span>
						{{/ratings_safety_very_safe}}
						{{#ratings_safety_under}}
							<span class="showRatings" rel="{{propertyNumber}}">{{ratings_safety}}%</span>
							<strong class="txt-mid green showRatings" rel="{{propertyNumber}}"></strong>
							<span class="averageRatingCaption showRatings<?php echo $extraCaptionClass; ?>" rel="{{propertyNumber}}"><?php echo _('Safety'); ?></span>
						{{/ratings_safety_under}}
						{{#ratings_location_good}}
							<span class="yellow-bg showRatings" rel="{{propertyNumber}}">{{ratings_location}}%</span>
							<strong class="txt-mid green showRatings" rel="{{propertyNumber}}"><?php echo _('Good location'); ?></strong>
							<span class="averageRatingCaption showRatings<?php echo $extraCaptionClass; ?>" rel="{{propertyNumber}}"><?php echo _('Location'); ?></span>
						{{/ratings_location_good}}
						{{#ratings_location_great}}
							<span class="yellow-bg showRatings" rel="{{propertyNumber}}">{{ratings_location}}%</span>
							<strong class="txt-mid green showRatings" rel="{{propertyNumber}}"><?php echo _('Great location'); ?></strong>
							<span class="averageRatingCaption showRatings<?php echo $extraCaptionClass; ?>" rel="{{propertyNumber}}"><?php echo _('Location'); ?></span>
						{{/ratings_location_great}}
						{{#ratings_location_under}}
							<span class="yellow-bg showRatings" rel="{{propertyNumber}}">{{ratings_location}}%</span>
							<strong class="txt-mid green showRatings" rel="{{propertyNumber}}"></strong>
							<span class="averageRatingCaption showRatings<?php echo $extraCaptionClass; ?>" rel="{{propertyNumber}}"><?php echo _('Location'); ?></span>
						{{/ratings_location_under}}
						{{/display_alternate_rating}}
					{{^display_alternate_rating}}

						<span>
							<span class="yellow-bg showRatings" rel="{{propertyNumber}}">{{overall_rating}}%</span>
							<strong class="txt-mid green showRatings" rel="{{propertyNumber}}">{{rating}}</strong>
							<span class="averageRatingCaption showRatings<?php echo $extraCaptionClass; ?>" rel="{{propertyNumber}}"><?php echo _('Overall evaluation'); ?></span>
						</span>
					{{/display_alternate_rating}}
				{{/overall_rating}}
				</div>

				<div class="price group">
					{{^dual_price}}
					<span><?php echo _('à partir de'); ?></span>
					{{#original_price}}
					<span class="rebate-price">{{display_currency}} {{original_price}}</span>
					{{/original_price}}

					{{display_currency}} <strong>{{display_price_formatted}}</strong>
					{{#original_price}}
					<!-- <div class="group deal"><p class="deal"><?php echo _('Deal of the Day'); ?></p></div> -->
					{{/original_price}}
					{{/dual_price}}
					{{#dual_price}}

					{{#display_shared_price}}
					<span><?php printf(gettext('Dorms from %s'), ''); ?></span>
					{{#original_price}}
					<span class="rebate-price" style="display: inline"> {{display_currency}} {{original_price}}</span>
					{{/original_price}}

					<span class="dorms_currency" style="display: inline">{{display_currency}}</span> <strong title="<?php echo _('Lowest price per night per person in a dorm'); ?>" class="dorms_price">{{display_shared_price_formatted}}</strong>

					{{#original_price}}
					<!-- <div class="group deal"><p class="deal"><?php echo _('Deal of the Day'); ?></p></div> -->
					{{/original_price}}
					{{/display_shared_price}}

					{{#display_private_price}}
						<br style="clear: both" />
						<span class="nbpeople-textd"><?php printf(gettext('Private rooms from %s'), ''); ?></span>
						<span class="display-currrency" title="<?php echo _('Lowest price per night per person in a private room'); ?>"><span style="display: inline" class="private_currency">{{display_currency}}</span> <strong class="private_price">{{display_private_formatted}}</strong></span>
					{{/display_private_price}}
					{{/dual_price}}
				</div>
			</div>

			<div class="prices_toggle" style="display:none">
				<a style="display: block;" id="show_city_avail_{{propertyNumber}}" href="#city_avail_{{propertyNumber}}" onClick='checkPropertyRoomsAvail("<?php echo site_url(); ?>","{{propertyNumber}}","datepick",document.getElementById("search-night").value,"","{{currency_code}}","<?php echo _("Date invalide"); ?>","{{minNights}}", "city_avail_table_{{propertyNumber}}"); $("#city_avail_table_{{propertyNumber}}").removeClass("ui-tabs-hide"); $("#prices_border_{{propertyNumber}}").hide(); return false;'>
					<img src="<?php echo site_url('/images/V2/icon_sort_down.png') ?>" />
					<?php echo _('Show prices'); ?>
				</a>

				<a href="#hide_city_avail_{{propertyNumber}}" id="hide_city_avail_{{propertyNumber}}" onClick='hidePropertyRoomsAvail("{{propertyNumber}}"); return false;' style="display: none">
					<img src="<?php echo site_url('/images/V2/icon_sort_up.png') ?>" />
					<?php echo _('Hide prices'); ?>
				</a>
			</div>

			<div class="prices_border" style="display: none" id="prices_border_{{propertyNumber}}"></div>
		</div>

		<div class="city_hostel" id="city_avail_{{propertyNumber}}" style="padding-top: 10px; border-top: 1px solid #ccc; display: none">
			<!-- <h3><a href="{{property_page_url}}">{{propertyName}}</a> - <?php echo _('Disponibilités'); ?> <span>(<?php echo $currency; ?>)</span></h3> -->
			<!-- <div class="amenities group no-indent">
				{{#amenities}}
					{{#to_display}}
					<span class="icon_facility icon_facility_{{facility_id}} group"><span>{{description}}</span></span>
					{{/to_display}}
				{{/amenities}}

				{{#landmarks}}
					{{#to_display}}
					<span class="icon_facility icon_landmark group"><span>{{landmark_name}}</span></span>
					{{/to_display}}
				{{/landmarks}}

				{{#safety80}}
					<span class="icon_facility icon_safety group"><span><?php echo _("Safety"); ?></span></span>
				{{/safety80}}
			</div>-->

			<div class="loading-dispo-city" id="loading-dispo-{{propertyNumber}}">
				<p><?php echo _('Recherche de disponibilités...'); ?></p>
			</div>

			<div class="booking_table_city" id="city_avail_table_{{propertyNumber}}"></div>
		</div>
	</div>
</div>
{{/properties}}
