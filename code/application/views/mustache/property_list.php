{{#properties}}
<div id="prop_tab_box_{{propertyNumber}}" class="hostel_list search_list" rel="{{propertyNumber}}">
	<nav class="city_tabs group" id="city_tabs_{{propertyNumber}}">
		<ul class="box_round ui-tabs-nav">
			<li class="first ui-tabs-selected"><a id="first_tab_{{propertyNumber}}" class="tab_price" href="#city_info_{{propertyNumber}}"><?php echo _("Info");?></a></li>
			<li><a class="tab_pic" href="#city_pictures_{{propertyNumber}}" onClick='display_property_pics("{{propertyNumber}}","{{propertyName}}","property_pics_{{propertyNumber}}");return false;'><?php echo _('Pictures'); ?></a></li>
			<li><a class="tab_avail" href="#city_avail_{{propertyNumber}}" onClick='checkPropertyRoomsAvail("<?php echo site_url(); ?>","{{propertyNumber}}","datepick",document.getElementById("search-night").value,"","{{currency_code}}","<?php echo _("Date invalide"); ?>","{{minNights}}","city_avail_table_{{propertyNumber}}");return false;'><?php echo _('Disponibility and Price'); ?></a></li>
			{{#isGeoValid}}
			<li><a name="city_map_show_property" rel="{{propertyNumber}}" class="tab_map box_round" href="#city_map_{{propertyNumber}}" title="<?php echo _("Cartes et Directions");?>"><?php echo _("See Map");?></a></span>
			{{/isGeoValid}}
			<li class="last"><a name="review_show_property" rel="{{propertyNumber}}" class="tab_review" href="#city_comment_{{propertyNumber}}"><?php echo _('Latest Reviews')?></a></li>
		</ul>
		{{#overall_rating}}
		<ul class="box_round rating">
			<li class="first last"><span class="" title="<?php echo _("évaluation moyenne");?> - <?php echo _("As rated by bookers like you"); ?>"><strong>{{overall_rating}} %</strong></span></li>
		</ul>
		{{/overall_rating}}
	</nav>
	<div class="box_content box_round ui-tabs" id="prop_box_{{propertyNumber}}">
		<div class="city_hostel group" id="city_info_{{propertyNumber}}">
			<div class="info">
				<div class="left info_pic">
				<a href="{{property_page_url}}">
				{{#PropertyImages}}<img alt="" src="{{#PropertyImage}}{{imageURL}}{{/PropertyImage}}" />{{/PropertyImages}}
				</a>
				<span class="info_type">{{propertyType}}</span>
				</div>
				<div class="info_indent">
					<h2><a href="{{property_page_url}}">{{propertyName}}</a></h2>
					<p class="address">{{address1}}</p>
					<p>{{shortDescription}}</p>
					{{#has_amenities}}
					<p><a href="#" rel="{{propertyNumber}}" class="prop_more_info"><?php echo _('Read more…'); ?></a></p>
					{{/has_amenities}}
					<ul class="avail group">
					{{#AvailableDates}}
					{{#availableDate}}
					<li>{{.}}</li>
					{{/availableDate}}
					{{/AvailableDates}}
					</ul>
					{{#isMinNightNeeded}}
					<p class="minnight">{{minNightsMessage}}</p>
					{{/isMinNightNeeded}}
				</div>
				<div class="amenities group">
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
					<span class="icon_facility icon_safety group"><span><?php echo _("Safety");?></span></span>
					{{/safety80}}
				</div>
				<div class="prop_more_info_wrap content_block" id="prop_more_info_wrap_{{propertyNumber}}">
					<h2 class="margbot10"><?php echo _("Commodité");?></h2>
					<a href="#" rel="{{propertyNumber}}" class="prop_more_info_close">[<?php echo _('close'); ?>]</a>
					<div class="group">
					<ul class="float-list green-li increase1 translated">
					{{#amenities}}
					<li>{{description}}</li>
					{{/amenities}}
					</ul>
					</div>
				</div>
			</div>

			<div class="rating">
				<div class="price group">
					{{^dual_price}}
						<span><?php echo _('à partir de');?></span>
						{{#original_price}}
						<span class="rebate-price">{{display_currency}} {{original_price}}</span>
						{{/original_price}}
						{{display_currency}} <strong>{{display_price_formatted}}</strong>
						{{#original_price}}
						<div class="group deal"><p class="deal"><?php echo _('Deal of the Day');?></p></div>
						{{/original_price}}
					{{/dual_price}}

					{{#dual_price}}

						{{#display_shared_price}}
  						<div class="group">
							<span class="nbpeople">
							<span class="icon-nbpeople nbpeople-1" title="<?php echo _('Bed in a dorm. 1 person per bed maximum')?>"><?php printf( gettext('Dorms from %s'),'');?></span>
							</span>
							</div>
							{{#original_price}}
							<span class="rebate-price">{{display_currency}} {{original_price}}</span>
							{{/original_price}}
							{{display_currency}} <strong>{{display_shared_price_formatted}}</strong>
							{{#original_price}}
							<div class="group deal"><p class="deal"><?php echo _('Deal of the Day');?></p></div>
							{{/original_price}}
  					{{/display_shared_price}}
  					{{#display_private_price}}
  					  <div class="group">
							<span class="nbpeople" title="<?php echo _('Maximum number of guests in the room')?>"><span class="private-people icon-nbpeople{{#display_private_people}} nbpeople-{{display_private_people}}{{/display_private_people}}">{{#display_private_people}}{{display_private_people}} x{{/display_private_people}}</span> 
							<span class="nbpeople-text"><?php printf( gettext('Private rooms from %s'),'');?></span>
							<span class="display-currrency">{{display_currency}} <strong>{{display_private_formatted}}</strong></span>
							</span>
							</div>
  					{{/display_private_price}}

					{{/dual_price}}

					</div>

			</div>
		</div>

		<div class="city_hostel ui-tabs-hide" id="city_pictures_{{propertyNumber}}">
			<h3><a href="{{property_page_url}}">{{propertyName}}</a> - <?php echo _('Pictures'); ?></h3>
			<div class="loading-dispo-city" id="loading-pics-{{propertyNumber}}">
				<p></p>
			</div>
			<div id="property_pics_{{propertyNumber}}"></div>
		</div>

		<div class="city_hostel ui-tabs-hide" id="city_avail_{{propertyNumber}}">
			<h3><a href="{{property_page_url}}">{{propertyName}}</a> - <?php echo _('Disponibilités'); ?> <span>(<?php echo $currency; ?>)</span></h3>
			<div class="amenities group no-indent">
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
				<span class="icon_facility icon_safety group"><span><?php echo _("Safety");?></span></span>
				{{/safety80}}
			</div>
			<div class="loading-dispo-city" id="loading-dispo-{{propertyNumber}}">
				<p><?php echo _('Recherche de disponibilités...'); ?></p>
			</div>
			<div class="booking_table_city" id="city_avail_table_{{propertyNumber}}"></div>
		</div>
		<div class="city_hostel ui-tabs-hide" id="city_comment_{{propertyNumber}}">
			<h3><a href="{{property_page_url}}">{{propertyName}}</a> - <?php echo _('Latest Reviews')?></h3>
			<div id="city_comments_{{propertyNumber}}"></div>
		</div>

		<div class="city_hostel ui-tabs-hide city_map_tab" id="city_map_{{propertyNumber}}">
			<h3><a href="{{property_page_url}}">{{propertyName}}</a>, {{address1}}</h3>
			<div class="city_map_view_block" id="city_map_view_{{propertyNumber}}"></div>
		</div>
        
        <div class="content_block amenities_included">
            <h2 class="margbot10" style="border-bottom: 1px dashed #AAAAAA;padding-bottom: 3px;"><?php echo _("What's Included");?></h2>
           
            <div class="group">
              <ul class="float-list green-li increase1 translated">
					
					{{#extras}}
                    {{#extra}}
					<li>{{.}} <?php echo ': <strong>'._("Free").'</strong>';?></li>
                    {{/extra}}
					{{/extras}}
					
				</ul>
            </div>
	    </div>
    
		<a href="{{property_page_url}}" class="reserve button-green hoverit" title="<?php echo _("Plus sur ce logement");?>"><?php echo _("Réserver");?></a>
	
    
    
    </div>
</div>

{{/properties}}