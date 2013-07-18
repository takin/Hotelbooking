<div id="map_filter_popup" style="display: none;">
	<input type="hidden" id="city_geo_lat" value="<?php echo $city_info->city_geo_lat; ?>">
	<input type="hidden" id="city_geo_lng" value="<?php echo $city_info->city_geo_lng; ?>">

	<span class="free" style="display: none"><span class="yellow-bg"><?php echo _('Free'); ?></span></span>
<?php
if ( !empty($city_districts) || !empty($city_landmarks) ) { ?>
	<div id="filter_map_leftSide">
		<nav id="city_map_filter_tabs" class="city_filter_tabs city_tabs  group popup_leftSide">
			<ul id="ul_map_filter_tabs" class="box_round popup_filter_tabs">
			<?php if (!empty($city_districts)) {    ?>
				<li id="li_popup_filter_districts" class="first ui-tabs-selected">
					<a id="tab_map_filter_districts" href="#filter_content_districts_popup">
						<?php echo _("Filter by Districts"); ?>
					</a>
				</li>
			<?php }
			if (!empty($city_landmarks)) { ?>
				<li id="li_popup_filter_landmarks">
					<a id="tab_map_filter_landmarks" href="#filter_content_landmarks_popup">
						<?php echo _('Filter by Landmarks (within 2km)'); ?>
					</a>
				</li>
			<?php } ?>
			</ul>
		</nav>

		<?php
		$district_count = 0;
		$total_dsitrict = count($city_districts);

		if (!empty($city_districts)) { ?>
		<div id="filter_content_districts_popup" class="filter_content box_content box_round ui-tabs">
			<ul id="cb_group_districts_filter">
			<?php
			foreach ($city_districts as $district) {
				$district_count++;
			?>
				<li>
					<input type="checkbox" class="checkbox"  <?php echo ( ($filters_init["district"]["id"] == $district->district_id) ? "checked=\"checked\"" : ""); ?>id="district-<?php echo $district->um_id; ?>" value="<?php echo $district->district_id; ?>" name="districts" /> <?php echo $district->district_name; ?> <?php ?>(<span id="district-count-<?php echo $district->district_id; ?>">0</span>)<?php ?><input type="hidden" id="hidden_district_<?php echo $district->district_id; ?>" value="<?php echo $district->um_id; ?>" name="hidden_districts_<?php echo $district->district_id; ?>" />
				</li>
			<?php } ?>
			</ul>
		</div>
		<?php }

		if (!empty($city_landmarks)) {
			$land_count = 0;
			$total_land = count($city_landmarks);
		?>
            <div id="filter_content_landmarks_popup" class="filter_content box_content box_round ui-tabs ui-tabs-hide">
                        <ul id="cb_group_landmarks_filter">
                            <?php
                            foreach ($city_landmarks as $landmark) {
                                $land_count++;
                                ?>
                                <li>
                                    <input type="checkbox" class="checkbox" <?php echo ( ($filters_init["landmark"]["id"] == $landmark->landmark_id) ? "checked=\"checked\"" : ""); ?> id="landmark-<?php echo ($landmark->original_name == 'City Center') ? 'downtown' : $landmark->landmark_id; ?>" value="<?php echo $landmark->landmark_id; ?>" name="landmarks" /> 
                                    <?php
                                    $type = null;
                                    if(strtolower($landmark->type) === "train_station"){
                                        echo '<img src="'.base_url().'images/map/train.png" class="filter_landmark_image">';
                                        $type = "train_station";
                                    }
                                    else if ( strtolower($landmark->type) === "airport" ){
                                         echo '<img src="'.base_url().'images/map/air-plane.png" class="filter_landmark_image">';
                                         $type = "airport";
                                    }
                                    else if ( strtolower($landmark->landmark_name) === "city center" ){
                                         echo '<img src="'.base_url().'images/map/city_center.png" class="filter_landmark_image">';
                                         $type = "city_center";
                                    }
                                    ?>
                                    <input type="hidden" id="hidden_landmarks_type_<?php echo $landmark->landmark_id; ?>" value="<?php echo $type; ?>" name="hidden_landmarks_type_<?php echo $landmark->landmark_id; ?>" />
                                    <span id="landmark_title_<?php echo $landmark->landmark_id; ?>"><?php echo $landmark->landmark_name; ?></span> (<span id="landmark-count-<?php echo $landmark->landmark_id; ?>">0</span>)
                                    <input type="hidden" id="hidden_landmarks_<?php echo $landmark->landmark_id; ?>" value="<?php echo $landmark->geo_latitude; ?>,<?php echo $landmark->geo_longitude; ?>" name="hidden_landmarks_<?php echo $landmark->landmark_id; ?>" />
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
		<?php } ?>
	</div>
<?php } ?>

	<div id="filter_map_rightSide_container" class="tabs_exist">
		<div id="filter_map_rightSide"></div>
		<div class="cls_showProperties">
			<button id="filter_map_showProperties" onclick="parent.$.fancybox.close();"><?php echo _('Show properties'); ?></button>
		</div>
	</div>
</div>

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
        $filterBy_flag = "both";
        if (empty($city_landmarks) && empty($city_districts)) {

            $filterBy_flag = "none";
        } elseif (empty($city_landmarks)) {
            $filterBy_flag = "districts";
        } elseif (empty($city_districts)) {
            $filterBy_flag = "landmarks";
        }
       
        $first_filter_div_container = '<div id="leftside_filter_links_container">';
        $last_filter_div_container = '</div>';
        
        $filter_by_districts_link = '<div id="filter_district_link" class="box_content box_round group side_search">
            <ul class="group_filter_links_container">
            <li class="leftside_filter_links">
            <a id="city_map_filter_districts" class="city_map_filter" href="#">' 
                . _("Filter by districts") . '</a></li>
                    </ul></div>';
        
        $filter_by_landmarks_link = '<div id="filter_landmark_link" class="box_content box_round group side_search">
                    <ul class="group_filter_links_container">
            <li class="leftside_filter_links">
            <a id="city_map_filter_landmarks" class="city_map_filter" href="#">' 
                . _("Filter by Landmarks") . 
                '</a></li>
                    </ul></div>';
        switch ($filterBy_flag) {
            case "both":
                echo $first_filter_div_container . $filter_by_districts_link . 
                    $filter_by_landmarks_link . $last_filter_div_container;

                break;
            case "districts":
                echo $first_filter_div_container . $filter_by_districts_link . $last_filter_div_container;
                break;

            case "landmarks":
                echo $first_filter_div_container . $filter_by_landmarks_link . $last_filter_div_container;
                break;

            default:
                break;
        } ?>
	<?php if(isset($city_info->city_geo_lat)) { ?>
            <div id="city_side_map_container">
                    <div id="show_expanded_map" class="box_content box_round group side_search">
                        <a href="javascript:void(0);">
                                <?php echo _("Click here to expand map"); ?>
                        </a>
                    </div>  
                    <div class="box_content map_button_box box_round" id="city_side_map"></div>
             </div>
	<?php } ?>
                
	<div id="search_load">
		<div class="filter_block box_content box_round" id="filter_choices">
			<span class="filter_title box_round"><strong><?php echo _('Property type')?></strong></span>
			<div class="filter_content">
				<ul id="cb_group_type_filter">
                                   <div class="mostPopular">
                                        <span><?php echo _("The most popular"); ?></span>
					<li><input type="checkbox" class="checkbox" <?php echo $filters_init["type"]["hostels"]; ?> name="prop_types" value="type_hostels" id="type_hostels" /> <?php echo _("Auberges de jeunesse")?> (<span id="prop-types-count-1">0</span>)</li>
                                   </div>
					<li><input type="checkbox" class="checkbox" <?php echo $filters_init["type"]["hotels"]; ?> name="prop_types" value="type_hotels" id="type_hotels" /> <?php echo _("Hôtels pas chers")?> (<span id="prop-types-count-2">0</span>)</li>
					<li><input type="checkbox" class="checkbox" <?php echo $filters_init["type"]["apartments"]; ?> name="prop_types" value="type_apartments" id="type_apartments"/> <?php echo _("Appartements")?> (<span id="prop-types-count-3">0</span>)</li>
					<li><input type="checkbox" class="checkbox" <?php echo $filters_init["type"]["bbs"]; ?> name="prop_types" value="type_bbs" id="type_bbs" /> <?php echo _("Chambres - B&B - Pensions")?> (<span id="prop-types-count-4">0</span>)</li>
					<li><input type="checkbox" class="checkbox" <?php echo $filters_init["type"]["campings"]; ?> name="prop_types" value="type_campings" id="type_campings" /> <?php echo _("Camping")?> (<span id="prop-types-count-5">0</span>)</li>
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
			<?php if(!empty($city_amenities) || !empty($most_popular_amenities)) {?>
			<span class="filter_title box_round"><strong><?php echo _('Facilities')?></strong></span>
			<div class="filter_content">
				<ul id="cb_group_facilities_filter">

                                    <?php if (!empty($most_popular_amenities)): ?>
                                    <div class="mostPopular">
                                        <span><?php echo _("Most popular"); ?></span>

                                        <?php foreach ($most_popular_amenities as $amenity): ?>
                                            <li>
                                                <input type="checkbox" class="checkbox"
                                                       id="facility-<?php echo $amenity->facility_id; ?>"
                                                       value="<?php echo $amenity->facility_id;?>"
                                                       name="facilities" />

                                                        <?php echo $amenity->facility_name;?>
                                                        (<span id="facility-count-<?php echo $amenity->facility_id;?>">0</span>)
                                            </li>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>

					<?php foreach ($city_amenities as $amenity): ?>
                                            <li>
                                                <input type="checkbox" class="checkbox"
                                                       id="facility-<?php echo $amenity->id_to_display; ?>"
                                                       value="<?php echo $amenity->facility_id;?>"
                                                       name="facilities" />

                                                        <?php echo $amenity->facility_name;?>
                                                        (<span id="facility-count-<?php echo $amenity->facility_id;?>">0</span>)
                                            </li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php }$district_count=0;$total_dsitrict = count($city_districts);?>

			<div class="filter_content">
				<p class="group">
					<label for="filter_price" class="slide_filter"><?php echo _('Price Range:')?></label>
					<span id="filter_price" class="slide_filter"/></span>
				</p>
				<div id="slider_price"></div>
			</div>

			<div class="filter_content">
				<p class="group">
					<label for="filter_rating" class="slide_filter"><?php echo _('Rating Range:')?></label>
					<span id="filter_rating" class="slide_filter"/></span>
				</p>
				<div id="slider_rating"></div>
			</div>

			<a href="#" id="reset_filters">[<?php echo _('Reset filters')?>]</a>
		</div>
                <?php $this->load->view('includes/group-booking'); ?>
	</div>
	<?php }?>

	<?php $this->load->view('includes/recently_viewed_properties'); ?>
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
		<h1 class="title_outside"><?php printf( gettext('Liste des logements pas chers à %s'), '<span class="city_selected">' . $city_selected . '</span>');?> - <?php echo '<span class="country_selected">' . $country_selected . '</span>';?></h1>
		<span id="city_results_counter">
			<!-- top city result counter-->
			<div id="resu" class="left_pagi" style="display:none;">
					<span class="resultcount"></span>
					<?php echo _('of');?>
					<span class="resulttotal"></span>
					<?php echo _('Results');?>
			</div>
			<!-- top city result counter-->

			<?php //printf( gettext('Showing %s results out of %s'),'<span id="city_results_count_current">0</span>','<span id="city_results_count_total">0</span>');?>
		</span>
	</div>


		  <div id="city_results_count" class="group">
                    <span id="city_results_arrive" class="top_search_result"><?php echo _('Arrivée');?>:</span>
                    <span id="city_results_arrive_date" class="top_search_result"><?php echo date_conv($date_selected, $this->wordpress->get_option('aj_date_format')); ?></span>
                    <?php printf( '<span id="city_results_numnights" class="top_search_result">'.gettext('Nombre de Nuits: %s').'</span>', '<span id="city_results_numnights_selected">'.$numnights_selected.'</span>');?>
                    <a id="change-dates" href="#" class="top_search_result">[<?php echo _('Change Dates'); ?>]</a>
				<?php /*?>Showing <span id="city_results_count_current">0</span> results out of <span id="city_results_count_total">0</span><?php */?>
		  </div>

		<?php   $this->load->view('includes/city_search_box',array('date_selected' => $date_selected, 'current_view' => $current_view,'numnights_selected' => $numnights_selected,'bc_continent' => $bc_continent,'bc_country' => $bc_country,'bc_city' => $bc_city)); ?>

					<!--property compare code start-->
					<?php  $displayCompareProperty =  $this->config->item('displayCompareProperty') ;
						if($displayCompareProperty == 1) { ?>
						<div id="property_compare" class="quick-data" style="display:none;">
							<input type="hidden" name="total_com_property" id="total_com_property" value="0"/>
							<div class="head123"><p><?php echo _('Quick Compare (5 properties maximum)');?></p>
							<span id="comparelink" class="comparelink" style="display:none;"><a class="compare_displaypopup" href="#property_compare_data" onclick="property_compare_popup();"><?php echo _('Compare');?></a></span> </div>
							<div id="compare_data"></div>
							<div class="remove_div"><a href="#" onclick="remove_pro('');"><?php echo _('Remove All');?></a></div>
						</div>
						<div style="display: none;">
							 <div id="property_compare_data" style="min-height:600px;overflow:hidden; width:970px;">
					   		 </div>
						</div>
					<?php } ?>
					<!--property compare code close-->

			<nav class="city-tools box_round group green_gradient_faded box_shadow_very_light" id="data_sort_controls" style="display:none">
				<ul class="sorting">
					<li class="title"><?php echo _("Classer par:");?></li>
					<li><a class="sorting" id="sortname-tous" href="#"><span class="asc">&nbsp;<?php echo _("Hostel Name");?></span></a></li>
					<li><a class="sorting activesort asc" id="sortprice-tous" href="#"><?php echo _("Best price");?></a></li>
					<li><a class="sorting desc" id="sortcote-tous" href="#"><?php echo _("Best rating");?></a></li>

				<?php if ($this->api_used == HB_API) { ?>
					<li><a class="sorting desc" id="sortsafest-tous" href="#"><?php echo _("Safest");?></a></li>
					<li><a class="sorting desc" id="sortbestlocation-tous" href="#"><?php echo _("Best location"); ?></a></li>
				<?php } ?>

				<li class="inputs" style="padding-top: 3px; padding-bottom: 3px; width: 160px; float: right; text-align: right; padding-right: 2px; padding-left: 0;">
					<div>
						<span class="type_hostels yellow-bg"><span><?php echo _("Youth hostels only");?></span></span>
						<input type="checkbox" class="checkbox" id="hostels_2nd_filter" value="" name="hostels_2nd_filter" />
					</div>
					<div style="clear: both">
						<span class="icon_facility_extra3d yellow-bg"><span><?php echo _("Only free breakfast");?></span></span>
	  					<input type="checkbox" class="checkbox" id="breakfast_2nd_filter" value="" name="breakfast_2nd_filter" />
					</div>
				</li>

				</ul>

			</nav>
                                        
                            <div id="expanded_city_map_container"
                                 class="city-tools box_round group green_gradient_faded box_shadow_very_light">
                                <a href="javascript:void(0);" class="remove_from_search_trigger" id="close_expanded_map">
						<img src="<?php echo site_url(); ?>/images/cls_button.2.png" alt="remove" style="vertical-align:middle" class="remove_from_search_trigger_icon" />
					</a>
                                <div id="expanded_city_map"></div>
                            </div>       
                                        <!-- Next 20 result code start-->
                                        <div class="pagination_pro main_pagination_div" style="display:none;">
                                            <div  class="left_pagi pagination_result">
                                                <span class="resultcount"></span>
                                                <?php echo _('of'); ?>
                                                <span class="resulttotal"></span>
                                                <?php echo _('Results'); ?>
                                            </div>
                                            <div class="page_navigation"></div>
                                        </div>
			 <!-- filer search box -->
			<div class="panel-padding" id="results_filters" style="display: block;">
			<div id="filters_text" style="display:none"><?php echo _("Selected filters:")?></div>
			<ul class="unstyled" id="applied_filters">
			<li class="label label-lightblue" id="applied_filter_hosting_price" style="display:none;">
			<span><?php echo _('Price')?></span>
			<a class="filter_x_container" href="javascript:void(0);" onclick="pweb_filter.closeFilter('price');"></a>
			</li>
			<li class="label label-lightblue" id="applied_filter_hosting_rating" style="display:none;">
			<span><?php echo _('Rating')?></span>
			<a class="filter_x_container" href="javascript:void(0);" onclick="pweb_filter.closeFilter('rating');"></a>
			</li>
			<li class="label label-lightblue" id="applied_filter_hosting_property" style="display:none;">
			<span><?php echo _('Property type')?></span>
			<a class="filter_x_container" href="javascript:void(0);" onclick="pweb_filter.closeFilter('prop_types');"></a>
			</li>
			<li class="label label-lightblue" id="applied_filter_hosting_facilities" style="display:none;">
			<span><?php echo _('Facilities')?></span>
			<a class="filter_x_container" href="javascript:void(0);" onclick="pweb_filter.closeFilter('facilities');"></a>
			</li>
			<li class="label label-lightblue" id="applied_filter_hosting_districts" style="display:none;">
			<span><?php echo _('Districts')?></span>
			<a class="filter_x_container" href="javascript:void(0);" onclick="pweb_filter.closeFilter('districts');"></a>
			</li>
			<li class="label label-lightblue" id="applied_filter_hosting_landmarks" style="display:none;">
			<span><?php echo _('Landmarks (within 2km)')?></span>
			<a class="filter_x_container" href="javascript:void(0);" onclick="pweb_filter.closeFilter('landmarks');"></a>
			</li>
			</ul>
			</div>
			<!-- End search box -->
			<div id="no_data_msg" class="box_content box_round group" style="display: none">
				<p class="no_result"><?php echo _('Désolé aucun résultat pour ce critère');?></p>
			</div>

			<input type="hidden" id="current_page" value="0">
			<input type="hidden" id="show_per_page" value="0">
			<div id="property_list"></div>
<script type="text/javascript">
	var availibility_url = '<?php echo site_url("/location_avail/".customurlencode($country_selected)."/".customurlencode($city_selected)."/$date_selected/$numnights_selected?currency=".$currency);?>';
</script>

			<!-- Next 20 result code start-->
			<div class="pagination_pro main_pagination_div" style="display:none;">
				<div  class="left_pagi pagination_result">
					<span class="resultcount"></span>
					<?php echo _('of');?>
					<span class="resulttotal"></span>
					<?php echo _('Results');?>
				</div>
				<div class="page_navigation"></div>
			</div>

</div>
<?php
if(isset($_COOKIE["compare"]) && $_COOKIE["compare"]!=''){
	if($this->uri->segment(2)==$_COOKIE["citysearch"]){
	$cookieproid = $_COOKIE["compare"];
?>
	<script type="text/javascript">
	var compareproperty = '<?php echo $cookieproid; ?>';
	display_compare_box(compareproperty);
	</script>
<?php
 }
}
else { ?>
	<script type="text/javascript">
			$('#total_com_property').val(0);
	</script>
<?php } ?>
<?php
if(isset($_COOKIE["citysearch"]) && $_COOKIE["citysearch"]!=''){
	if($this->uri->segment(2)!=$_COOKIE["citysearch"]){
		?>
		<script>
			pweb_setCookie("citysearch","<?php echo $this->uri->segment(2);?>",24);
			pweb_setCookie("compare","",24);
		</script>
		<?php
	}
}else{
?>
<script>
pweb_setCookie("citysearch","<?php echo $this->uri->segment(2);?>",24);
</script>
<?php
}
?>

<script type="text/javascript" src="<?php echo base_url();?>js/ad-gallery.js"></script>

<script id="template-infow" type="text/html">
<?php
  $this->load->view('mustache/city_map_property_infow');
?>
</script>
<script id="template" type="text/html">
<?php
  $this->load->view('mustache/property_list');
?>
</script>
<script id="template-property-quick-view" type="text/html">
<?php
  $this->load->view('mustache/property_quick_view');
?>
</script>

<?php $this->load->view('includes/save_property'); ?>

<input type="hidden" name="wait_message" id="wait_message" value="<?php echo _("Please Wait");?>"/>
<div style="display:none;">
			<div id="quick_preview_div" style="min-height:450px;overflow:hidden; width:1100px;">
			</div>
</div>
<input type= "hidden" name="var_from" value="<?php echo  _('From');?>" id= "var_from" />
<input type= "hidden" name="limit_compare_message" value="<?php echo  _('Only 5 properties can be compared. Please remove a property from list.');?>" id= "limit_compare_message" />

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/quick_view.css?v=<?php echo time(); ?>" media="all" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/jquery.ad-gallery.css?v=<?php echo time(); ?>" media="all" />

<script type="text/javascript">
   $(document).ready(function(){
 
        // on window resize
        $(window).resize(function() {
             changeSidebar_width();
         });
         // this part is related to fixing the map position
         $(window).scroll(function () {
        // check if left map is visible to fix it position
            if($("#expanded_city_map_container").is(':hidden')){
                changeSidebar_width();
            }
            else{
                $("#side_search_box").show();
                $("#leftside_filter_links_container").show();    
   
            $("#sidebar").removeClass("fix_sidebar_position");
            $("#sidebar").addClass("container_16");
            $("#sidebar").addClass("grid_4");
            $("#sidebar").css({'position' : ''});
            $("#sidebar").css({'top' : ''});
            $("#sidebar").css({'left' : ''});

            $("#main").css({'float' : 'auto'});
            }
        });
        
       function changeSidebar_width(){
        // fix sidebar to make side map always visible
        var page_height = $(document).height();
        var fix_height_position =  ( page_height - $("#sidebar").height() - 385 );
        var scroll_position = $(window).scrollTop();
        // whole div container
        var main_container_leftPosition = $("#main_container").offset().left;
        var main_container_width = $("#main_container").width();

        var sidebar_width = ( parseInt(main_container_width) * 0.23 );

        //we're scrolling our ,position is greater than 0 from the top of the page.
        if( scroll_position < 230 ){
            $("#side_search_box").show();
            $("#leftside_filter_links_container").show();    

            $("#sidebar").removeClass("fix_sidebar_position");
            $("#sidebar").addClass("container_16");
            $("#sidebar").addClass("grid_4");
            $("#sidebar").css({'position' : ''});
            $("#sidebar").css({'top' : ''});
            $("#sidebar").css({'left' : ''});

            $("#main").css({'float' : 'auto'});
         }
        else if( scroll_position > 230 && scroll_position < fix_height_position ){
            $("#side_search_box").hide();
            $("#leftside_filter_links_container").hide();    

            $("#sidebar").addClass("fix_sidebar_position");
            $("#sidebar").removeClass("container_16");
            $("#sidebar").removeClass("grid_4");
            $("#sidebar").css({'position' : ''});
            $("#sidebar").css({'width' : sidebar_width});
            $("#sidebar").css({'top' : ''});
            $("#sidebar").css({'left' : (parseInt(main_container_leftPosition) + 10 ) });

            $("#main").css({'float' : 'right'});
         }
         else  if( scroll_position > fix_height_position ){
            $("#side_search_box").show();

            $("#sidebar").css({'position' : 'relative'});
            $("#sidebar").addClass("container_16");
            $("#sidebar").addClass("grid_4");
            $("#sidebar").css({'left' : 0});
            if( fix_height_position > 0 ){
                $("#sidebar").css({'top' : fix_height_position - 245 });
            }
            $("#main").css({'float' : 'auto'});
         }
       } 
   });
</script>
