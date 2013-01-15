<div id="sidebar" class="grid_4 city_view_search">
	<?php if($searchmode > 0){?>
	<?php if(!isset($date_selected))      $date_selected = NULL;
	if(!isset($numnights_selected)) $numnights_selected = NULL;
	if(!isset($bc_continent))       $bc_continent = NULL;
	if(!isset($bc_country))         $bc_country = NULL;
	if(!isset($bc_city))            $bc_city = NULL;
	$this->load->view('includes/side_search_box',array('date_selected' => $date_selected, 'current_view' => $current_view,'numnights_selected' => $numnights_selected,'bc_continent' => $bc_continent,'bc_country' => $bc_country,'bc_city' => $bc_city));
	?>
	<?php
	//This is dependant to PWEB JS files being loaded
	//JS pweb/jlibs/GroupCheckBoxes.js
	//JS pweb-mapping/PrpoertyFilters.js

	?>
	<div id="search_load">
		<?php if(isset($city_info->city_geo_lat)){?>
		<div class="box_content map_button_box box_round" id="map_button_side">
			<a id="city_map_show_1" href="#">
			<span><strong><?php echo _("Voir la carte");?></strong></span>
			<img class="" src="http://maps.google.com/maps/api/staticmap?center=<?php echo $city_info->city_geo_lat;?>,<?php echo $city_info->city_geo_lng;?>&zoom=10&size=253x125&sensor=false&language=<?php echo $this->wordpress->get_option('aj_lang_code2');?>" />
			</a>
		</div>
		<?php }?>
		<div class="filter_block box_content box_round" id="filter_choices">
			<?php //TODO show filter reset;?>
			<span class="filter_title_top"><?php echo _('Filter by:')?></span>
			<a href="#" id="reset_filters">[<?php echo _('Reset filters')?>]</a>
			<span class="filter_title box_round"><strong><?php echo _('Price')?></strong></span>
			<div class="filter_content">
				<p class="group">
					<label for="filter_price" class="slide_filter"><?php echo _('Price Range:')?></label>
					<span id="filter_price" class="slide_filter"/></span>
				</p>
			<div id="slider_price"></div>
			</div>

			<span class="filter_title box_round"><strong><?php echo _('Rating')?></strong></span>
				<div class="filter_content">
				<p class="group">
					<label for="filter_rating" class="slide_filter"><?php echo _('Rating Range:')?></label>
					<span id="filter_rating" class="slide_filter"/></span>
				</p>
				<div id="slider_rating"></div>
			</div>

			<span class="filter_title box_round"><strong><?php echo _('Property type')?></strong></span>
			<div class="filter_content">
				<ul id="cb_group_type_filter">
					<li><input type="checkbox" class="checkbox" <?php echo $filters_init["type"]["all"]; ?> name="prop_types" value="type_all" id="type_all" /> <?php echo _("All")?> <?php /*?>(<span id="prop-types-count-0"></span>)<?php */?></li>
					<li><input type="checkbox" class="checkbox" <?php echo $filters_init["type"]["hostels"]; ?> name="prop_types" value="type_hostels" id="type_hostels" /> <?php echo _("Auberges de jeunesse")?> <?php /*?>(<span id="prop-types-count-1"></span>)<?php */?></li>
					<li><input type="checkbox" class="checkbox" <?php echo $filters_init["type"]["hotels"]; ?> name="prop_types" value="type_hotels" id="type_hotels" /> <?php echo _("Hôtels pas chers")?> <?php /*?>(<span id="prop-types-count-2"></span>)<?php */?></li>
					<li><input type="checkbox" class="checkbox" <?php echo $filters_init["type"]["apartments"]; ?> name="prop_types" value="type_apartments" id="type_apartments"/> <?php echo _("Appartements")?> <?php /*?>(<span id="prop-types-count-3"></span>)<?php */?></li>
					<li><input type="checkbox" class="checkbox" <?php echo $filters_init["type"]["bbs"]; ?> name="prop_types" value="type_bbs" id="type_bbs" /> <?php echo _("Chambres - B&B - Pensions")?> <?php /*?>(<span id="prop-types-count-4"></span>)<?php */?></li>
					<li><input type="checkbox" class="checkbox" <?php echo $filters_init["type"]["campings"]; ?> name="prop_types" value="type_campings" id="type_campings" /> <?php echo _("Camping")?> <?php /*?>(<span id="prop-types-count-5"></span>)<?php */?></li>
				</ul>
			</div>
			<?php /*?><span class="filter_title box_round expand"><strong><?php echo _('Facilities')?></strong></span>
			<div class="filter_content" style="display:none;">
				<ul>
					<li><input type="checkbox" class="checkbox" id="" name="amenities" /> <?php echo _("Internet")?></li>
					<li><input type="checkbox" class="checkbox" id="" name="amenities" /> <?php echo _("Luggage Room")?></li>
					<li><input type="checkbox" class="checkbox" id="" name="amenities" /> <?php echo _("Towels")?></li>
					<li><input type="checkbox" class="checkbox" id="" name="amenities" /> <?php echo _("Linen")?></li>
					<li><input type="checkbox" class="checkbox" id="" name="amenities" /> <?php echo _("Free Breakfast")?></li>
					<li><input type="checkbox" class="checkbox" id="" name="amenities" /> <?php echo _("Bar")?></li>
				</ul>
			</div><?php */?>
			<?php if(!empty($city_amenities)){?>
			<span class="filter_title box_round"><strong><?php echo _('Facilities')?></strong></span>
			<div class="filter_content">
				<ul id="cb_group_facilities_filter">
					<?php foreach ($city_amenities as $amenity){?>
					<li><input type="checkbox" class="checkbox" id="facility-<?php echo ( ($amenity->original_name == 'Breakfast Included'|| $amenity->original_name == 'Breakfast') ) ? 'free-breakfast' : $amenity->facility_id;?>" value="<?php echo $amenity->facility_id;?>" name="facilities" /> <?php echo $amenity->facility_name;?> <?php /*?>(<span id="facility-count-<?php echo $amenity->facility_id;?>"></span>)<?php */?></li>
					<?php }?>
				</ul>
			</div>
			<?php }$district_count=0;$total_dsitrict = count($city_districts);?>
			<?php if(!empty($city_districts)){?>
			<span class="filter_title box_round expand"><strong><?php echo _('Districts')?></strong></span>
			<div class="filter_content">
				<ul id="cb_group_districts_filter">
					<?php foreach ($city_districts as $district){$district_count++;?>
					<?php if ($district_count == 11){?>
					<li><a id="show_more_district" class="right show_choices" href="#">+ <?php echo _('More Options')?></a></li>
					<div id="more_district" class="more_choice_filter">
					<?php }?>
					<li><input type="checkbox" class="checkbox"  <?php echo ( ($filters_init["district"]["id"] == $district->district_id) ? "checked=\"checked\"" : ""); ?>id="district-<?php echo $district->district_id;?>" value="<?php echo $district->district_id;?>" name="districts" /> <?php echo $district->district_name;?> <?php /*?>(<span id="district-count-<?php echo $district->district_id;?>"></span>)<?php */?></li>
					<?php if ($district_count >= 11 && $district_count == $total_dsitrict){?>
					<li><a id="show_less_district" class="right less_choices" href="#">- <?php echo _('Less Options')?></a></li>
					</div>
					<?php }?>
					<?php }?>
				</ul>
			</div>
			<?php }?>
			<?php if(!empty($city_landmarks)){$land_count=0;$total_land = count($city_landmarks);?>
			<span class="filter_title box_round expand"><strong><?php echo _('Landmarks (within 2km)')?></strong></span>
			<div class="filter_content">
				<ul id="cb_group_landmarks_filter">
					<?php //TODO show english in tool tip for landmark ;?>
					<?php foreach ($city_landmarks as $landmark){$land_count++;?>
					<?php if ($land_count == 11){?>
					<li><a id="show_more_land" class="right show_choices" href="#">+ <?php echo _('More Options')?></a></li>
					<div id="more_land" class="more_choice_filter">
					<?php }?>
					<li><input type="checkbox" class="checkbox" <?php echo ( ($filters_init["landmark"]["id"] == $landmark->landmark_id) ? "checked=\"checked\"" : ""); ?> id="landmark-<?php echo ($landmark->original_name == 'City Center') ? 'downtown' : $landmark->landmark_id;?>" value="<?php echo $landmark->landmark_id;?>" name="landmarks" /> <?php echo $landmark->landmark_name;?> <?php /*?>(<span id="landmark-count-<?php echo $landmark->landmark_id;?>"></span>)<?php */?></li>
					<?php if ($land_count >= 11 && $land_count == $total_land){?>
					<li><a id="show_less_land" class="right less_choices" href="#">- <?php echo _('Less Options')?></a></li>
					</div>
					<?php }?>
					<?php }?>
				</ul>
			</div>
			<?php }?>
		</div>
	</div>
	<?php }?>
	<?php $this->load->view('includes/video-popup'); ?>
	<?php $this->load->view('includes/testimonials'); ?>
	<?php $this->load->view('includes/siteinfo'); ?>
	<?php //$this->load->view('includes/popular_city'); ?>

	<?php /* if($searchmode!=1){?>
	<?php $this->load->view('includes/search-info'); ?>
	<?php }*/?>

	<?php //$this->load->view('includes/siteinfo'); ?>
	<?php //$this->load->view('includes/widget-cours'); ?>
	<?php //$this->load->view('includes/testimonials'); ?>
	<?php //$this->load->view('includes/widget-qr-code'); ?>
	<?php //$this->load->view('includes/year-10'); ?>
	<?php //$this->load->view('includes/groupe'); ?>
	<?php //$this->load->view('includes/popular_city'); ?>

</div>

<div id="main" class="grid_12 city_view_page">

	<?php
	// Load the search box for the city landing page
	if($searchmode == 0){
		if(!isset($date_selected))      $date_selected = NULL;
		if(!isset($numnights_selected)) $numnights_selected = NULL;
		if(!isset($bc_continent))       $bc_continent = NULL;
		if(!isset($bc_country))         $bc_country = NULL;
		if(!isset($bc_city))            $bc_city = NULL;
		$this->load->view('includes/city_lp_search_box',array('date_selected' => $date_selected, 'current_view' => $current_view,'numnights_selected' => $numnights_selected,'bc_continent' => $bc_continent,'bc_country' => $bc_country,'bc_city' => $bc_city));
	}?>
	<div id="city_search_title_bar">
		<h1 class="title_outside"><?php printf( gettext('Liste des logements pas chers à %s'),$city_selected);?> - <?php echo $country_selected;?></h1>
		<span id="city_results_counter">
			<?php printf( gettext('Showing %s results out of %s'),'<span id="city_results_count_current">0</span>','<span id="city_results_count_total">0</span>');?>
		</span>
	</div>
	
		
		  <div id="city_results_count" class="group">
                    <span id="city_results_arrive"><?php echo _('Arrivée');?></span>
                    <span id="city_results_arrive_date"><?php strftime("%d %B %Y", strtotime($date_selected));?></span>
                    <span id="city_results_numnights"><?php echo _('Nombre de Nuits: %s');?></span>
                    <span id="city_results_numnights_selected"><?php echo _($numnights_selected);?></span>)
				<?php /*?>Showing <span id="city_results_count_current">0</span> results out of <span id="city_results_count_total">0</span><?php */?>
				<a href="#" id="city_map_show_2" class="view_map"><?php echo _("Voir la carte");?></a>
				<a href="#" id="city_map_hide" class="view_map"><?php echo _("Close Map");?></a>
			</div>
			<div id="city_map_container" class="box_round box_shadow_very_light"></div>

			<nav class="city-tools box_round group green_gradient_faded box_shadow_very_light" id="data_sort_controls" style="display:none">
				<ul class="sorting">
					<li class="title"><?php echo _("Classer par:");?></li>
					<li><a class="sorting" id="sortname-tous" href="#"><span class="asc"><?php echo _("Hostel Name");?></span></a></li>
					<li><a class="sorting activesort" id="sortprice-tous" href="#"><span class="asc"><?php echo _("Prix");?></span></a></li>
					<li><a class="sorting" id="sortcote-tous" href="#"><span class="asc"><?php echo _("Cote");?></span></a></li>
  				<li class="inputs"><input type="checkbox" class="checkbox" id="breakfast_2nd_filter" value="" name="breakfast_2nd_filter" /> 
					<span class="icon_facility_extra3"><span><?php echo _("Only free breakfast");?></span></span></li>
					<li class="inputs"><input type="checkbox" class="checkbox" id="downtown_2nd_filter" value="" name="downtown_2nd_filter" /> 
					<span class="icon_landmark"><?php echo _("Only downtown");?></span></li>
				</ul>

			</nav>

			<div id="no_data_msg" class="box_content box_round group" style="display: none">
				<p class="no_result"><?php echo _('Désolé aucun résultat pour ce critère');?></p>
			</div>

			<div id="property_list">
			<script type="text/javascript">
			$(document).ready(function(){
				$.ajax(
				{
						type:"GET",
						url:'<?php echo site_url("/location_avail/".customurlencode($country_selected)."/".customurlencode($city_selected)."/$date_selected/$numnights_selected?currency=".$currency);?>',
						success:function(data)
						{
							setup_filters(data);
							$('#search_load').show();
							$('#city_results_count').show();
							$('#city_load').hide();
							$('#wrap').show();
						}
				});
			});
			</script>

			</div>
			<a href="#" id="show_more_results" class="button-green-faded hoverit box_round box_shadow_very_light"><?php echo _('See more results')?></a>

	</div>

	<script id="template-infow" type="text/html">
	<?php
	//TODO minify using jsmin library and cache
	$this->load->view('mustache/city_map_property_infow');
	?>
	</script>
	<script id="template" type="text/html">
		<?php
		//TODO minify using jsmin library and cache
		$this->load->view('mustache/property_list');
		?>
	</script>
