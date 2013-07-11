<?php // Filter variables
$type = $filters['type'];
$landmark = $filters['landmark'];
$district = $filters['district'];
$long_title = '';
$small_title = '';
$single_city = false;
if(!empty($district)){
	$district_name = $district->district_name_ts;
	if(empty($district_name)){$district_name = $district->district_name;}
	switch($type){
		case 'hostel':
			$long_title = sprintf( gettext('%s – Hostels in the %s district. Maps for %s, Photos and Reviews for each Hostel in %s.'),$city_selected,$district_name,$city_selected,$city_selected);
			$small_title = sprintf( gettext('Hostels in the %s district'),$district_name);
			break;
		case 'hotel':
			$long_title = sprintf( gettext('%s – Hotels in the %s district. Maps for %s, Photos and Reviews for each Hotel in %s.'),$city_selected,$district_name,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Hotels in the %s district'),$district_name);
			break;
		case 'apartment':
			$long_title = sprintf( gettext('%s – Apartments in the %s district. Maps for %s, Photos and Reviews for each Apartment in %s.'),$city_selected,$district_name,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Apartments in the %s district'),$district_name);
			break;
		case 'guesthouse':
			$long_title = sprintf( gettext('%s – Rooms in the %s district. Maps for %s, Photos and Reviews for each room in %s.'),$city_selected,$district_name,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Rooms in the %s district'),$district_name);
			break;
		case 'campsite':
			$long_title = sprintf( gettext('%s – Camping in the %s district. Maps for %s, Photos and Reviews for each camping in %s.'),$city_selected,$district_name,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Camping in the %s district'),$district_name);
			break;
		default:
			$long_title = sprintf( gettext('%s – Properties in the %s district. Maps for %s, Photos and Reviews for each properties in %s.'),$city_selected,$district_name,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Properties in the %s district'),$district_name);
			break;
	}
}elseif(!empty($landmark)){
	if(!empty($landmark->landmark_name_ts)) 
	{
	  $landmark_name = $landmark->landmark_name_ts;
	}	
	else 
	{
	  $landmark_name = $landmark->landmark_name;
	}
	switch($type){
		case 'hostel':
			$long_title = sprintf( gettext('%s – Hostels close to %s. Maps for %s, Photos and Reviews for each Hostel in %s.'),$city_selected,$landmark_name,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Hostels close to %s'),$landmark_name);
			break;
		case 'hotel':
			$long_title = sprintf( gettext('%s – Hotels close to %s. Maps for %s, Photos and Reviews for each Hotel in %s.'),$city_selected,$landmark_name,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Hotels close to %s'),$landmark_name);
			break;
		case 'apartment':
			$long_title = sprintf( gettext('%s – Apartments close to %s. Maps for %s, Photos and Reviews for each Apartment in %s.'),$city_selected,$landmark_name,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Apartments close to %s'),$landmark_name);
			break;
		case 'guesthouse':
			$long_title = sprintf( gettext('%s – Rooms close to %s. Maps for %s, Photos and Reviews for each room in %s.'),$city_selected,$landmark_name,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Rooms close to %s'),$landmark_name);
			break;
		case 'campsite':
			$long_title = sprintf( gettext('%s – Camping close to %s. Maps for %s, Photos and Reviews for each camping in %s.'),$city_selected,$landmark_name,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Camping close to %s'),$landmark_name);
			break;
		default:
			$long_title = sprintf( gettext('%s – Properties close to %s. Maps for %s, Photos and Reviews for each properties in %s.'),$city_selected,$landmark_name,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Properties close to %s'),$landmark_name);
			break;
	}
}else{
	switch($type){
		case 'hostel':
			$long_title = sprintf( gettext('%s – Hostels in %s. Maps for %s, Photos and Reviews for each Hostel in %s.'),$city_selected,$city_selected,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Hostels in %s'),$city_selected);
			break;
		case 'hotel':
			$long_title = sprintf( gettext('%s – Hotels in %s. Maps for %s, Photos and Reviews for each Hotel in %s.'),$city_selected,$city_selected,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Hotels in %s'),$city_selected);
			break;
		case 'apartment':
			$long_title = sprintf( gettext('%s – Apartments in %s. Maps for %s, Photos and Reviews for each Apartment in %s.'),$city_selected,$city_selected,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Apartments in %s'),$city_selected);
			break;
		case 'guesthouse':
			$long_title = sprintf( gettext('%s – Rooms in %s. Maps for %s, Photos and Reviews for each room in %s.'),$city_selected,$city_selected,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Rooms in %s'),$city_selected);
			break;
		case 'campsite':
			$long_title = sprintf( gettext('%s – Camping in %s. Maps for %s, Photos and Reviews for each camping in %s.'),$city_selected,$city_selected,$city_selected,$city_selected);
			$small_title =  sprintf( gettext('Camping in %s'),$city_selected);
			break;
		default:
			$long_title = sprintf( gettext('%s – Properties in %s. Maps for %s, Photos and Reviews for each properties in %s.'),$city_selected,$city_selected,$city_selected,$city_selected);
			$single_city = true;
			$small_title =  sprintf( gettext('Properties in %s'),$city_selected);
			break;
	}
}

?>
<script>
$(document).ready(function(){
		$("#notification").delay(3500).fadeIn(400);
		$('a.show_search').click(function() {
		 	$('#search-submit').effect("bounce", { times:2, distance: 10 }, 300);
			$('#datepick').css('border-color','#C97C30');
		});
});
</script>
<div id="sidebar" class="grid_6 city_lp">
		<?php if(!empty($city_info->city_image)){?>
    		<div class="box_content box_round side_entry" id="city_intro">
			<div class="city_lp_img">
				<img src="<?php echo base_url();?>images/city/<?php echo $city_info->city_image;?>" alt="<?php echo ucfirst($city_selected).', '.ucfirst($country_selected);?>"/>
				<span><?php echo ucfirst($city_selected).', '.ucfirst($country_selected);?>
				<?php if (!$single_city){ echo '<strong>'.$small_title.'</strong>';}?>
				</span>

			</div>
		</div>
    		<?php }?>

		<?php if(isset($city_info->city_geo_lat)){?>
		<div class="box_content map_button_box box_round" id="map_button_side">
			<?php //Removed until we can really show the map ?>
			<?php /*?><a id="city_map_show_1" class="show_search" href="#wrap"><?php */?>
			<?php /*?><span><strong><?php echo _("Voir la carte");?></strong></span>		<?php */?>
                        <?php                           
                        
                        $markers = null;

                        if (!empty($property_geos)) {
                             $markers = "&markers=color:0x5C8CAB" . "%7C+";
                            foreach ($property_geos as $key => $property_geo) {
                                $markers .=  round($property_geo->geo_latitude,2) . "," . round($property_geo->geo_longitude,2) ."%7C+";
                            }
                        }
                    
                        if (!empty($featured_landmarks)) {
                            if (ISDEVELOPMENT) {
                                $static_map_icon_base_url = "http://www.aubergesdejeunesse.com/";
                            } else {
                                $static_map_icon_base_url = base_url();
                            }
                            $city_center_marker = "&markers=icon:". $static_map_icon_base_url."images/map/city_center.png" ."%7C+";
                            $train_station_marker = "&markers=icon:". $static_map_icon_base_url."images/map/train.png" ."%7C+";
                            $air_plane_marker = "&markers=icon:". $static_map_icon_base_url."images/map/air-plane.png" ."%7C+";
                           
                            
                             foreach ($featured_landmarks as $featured_landmark) {
                                switch ($featured_landmark->type) {
                                    case "city_center":
                                        $city_center_marker .= round($featured_landmark->geo_latitude,2) . "," . round($featured_landmark->geo_longitude,2) ."%7C+";
                                        break;
                                    
                                    case "train_station":
                                        $train_station_marker .= round($featured_landmark->geo_latitude,2) . "," . round($featured_landmark->geo_longitude,2) ."%7C+";
                                        break;
                                    
                                    default:
                                        $air_plane_marker .= round($featured_landmark->geo_latitude,2) . "," . round($featured_landmark->geo_longitude,2) ."%7C+";
                                        break;
                                }
                                
                            }
                            
                        $city_center_marker = rtrim($city_center_marker, "&markers=icon:". $static_map_icon_base_url."images/map/city_center.png" ."%7C+");
                        $train_station_marker = rtrim($train_station_marker, "&markers=icon:". $static_map_icon_base_url."images/map/train.png" ."%7C+");
                        $air_plane_marker = rtrim($air_plane_marker, "&markers=icon:". $static_map_icon_base_url."images/map/air-plane.png" ."%7C+");
                        
                        // remove last | in markers variable
                        $city_center_marker = rtrim($city_center_marker, '%7C+');
                        $train_station_marker = rtrim($train_station_marker, '%7C+');
                        $air_plane_marker = rtrim($air_plane_marker, '%7C+');
                        // add featured markers to main marker
                        $markers .= $city_center_marker;
                        $markers .= $train_station_marker;
                        $markers .= $air_plane_marker;
                        }
                        
                         // remove last | in markers variable
                        $markers = rtrim($markers, '%7C');
                        // remove &markers= if no markers
                        $markers = rtrim($markers, '&markers=');
                        ?>
			<a id="map_city_landing_page" href="javascript:void(0);" class="tooltip" title="<?php echo _('To view all available properties on the map, please enter your dates in the box on the top right.');?>">
                            <?php if(!empty($landmark->geo_latitude)){?>
			<img src="https://maps.google.com/maps/api/staticmap?center=<?php echo $landmark->geo_latitude;?>,<?php echo $landmark->geo_longitude;?>&markers=icon:<?php echo $static_map_icon_base_url;?>images/map_landmark_marker_blue.png%7C+<?php echo $landmark->geo_latitude;?>,<?php echo $landmark->geo_longitude;?>&zoom=13&size=392x194&sensor=false&language=<?php echo $this->wordpress->get_option('aj_lang_code2');?><?php echo $markers;?>" />
			<?php }else{?>
                        <img src="https://maps.google.com/maps/api/staticmap?center=<?php echo $city_info->city_geo_lat;?>,<?php echo $city_info->city_geo_lng;?>&zoom=10&size=392x194&sensor=false&language=<?php echo $this->wordpress->get_option('aj_lang_code2');?><?php echo $markers;?>" />
			<?php }?>
                        </a>
			<?php /*?></a><?php */?>
		</div>
		<?php }?>


    <?php $this->load->view('includes/group-booking'); ?>
		<?php $this->load->view('includes/video'); ?>

		<div class="box_content box_round side_entry" id="city_intro">
		<?php if (!$single_city){
			echo '<h1>'.$long_title.'</h1>';
		}else{
			echo '<h1>'.sprintf( gettext("Auberges à %s. Toutes les auberges à %s - %s "),ucfirst($city_selected),ucfirst($city_selected),ucfirst($country_selected)).'</h1>';
		}?>

		<p class="lp_text"><?php
		if(!empty($city_info->city_description))
		{
			echo $city_info->city_description;
		}
		else
		{
		printf( gettext("Vous recherchez une auberge de jeunesse, un hôtel pas cher, un appartement, une chambre d'hôtes, un Bed and Breakfast B&B ou une Pension à %s ?"),"<strong>".$city_selected."</strong>");?> <?php printf( gettext("N'allez pas plus loin, tous les bons plans à %s sont sur %s."),"<strong>".$city_selected."</strong>",$this->config->item('site_name'));?> <?php printf( gettext("Comme des milliers de jeunes et moins jeunes tous les mois, réservez vous aussi en toute sécurité le logement dernière minute idéal au meilleur prix dans tous les quartiers de %s : centre ville, quartier branché, quartier étudiant et universitaire, près des bus, de la gare, de l'aéroport ainsi que près de toutes les attractions de %s."),"<strong>".$city_selected."</strong>","<strong>".$city_selected."</strong>");?></p>
		<?php }?>
		</div>

	<?php $this->load->view('includes/testimonials'); ?>
	<?php $this->load->view('includes/siteinfo'); ?>
	<?php $this->load->view('includes/widget-qr-code'); ?>
	<?php //$this->load->view('includes/popular_city'); ?>

	<?php //$this->load->view('includes/widget-cours'); ?>
	<?php //$this->load->view('includes/year-10'); ?>
	<?php //$this->load->view('includes/groupe'); ?>

</div>

<div id="main" class="grid_10 city_lp">

	<div class="box_content box_round group main_search city_lp_search">
		<h2 class="search_title"><?php echo $long_title;?></h2>

	<?php
	// Load the search box for the city landing page
		if(!isset($date_selected))      $date_selected = NULL;
		if(!isset($numnights_selected)) $numnights_selected = NULL;
		if(!isset($bc_continent))       $bc_continent = NULL;
		if(!isset($bc_country))         $bc_country = NULL;
		if(!isset($bc_city))            $bc_city = NULL;
		$this->load->view('includes/city_lp_search_box',array('date_selected' => $date_selected, 'current_view' => $current_view,'numnights_selected' => $numnights_selected,'bc_continent' => $bc_continent,'bc_country' => $bc_country,'bc_city' => $bc_city)); ?>
	</div>
	<div id="notification" class="notification_search orange_gradient box_shadow_very_light box_round">
		<span class="notification-arrow"></span>
		<div class="notification_inside">
			<p><?php echo _("To view prices and availability, please enter your dates.");?></p>
		</div>
	</div>

		<?php /*?><h2><?php printf( gettext("Auberges à %s. Toutes les auberges à %s - %s "),ucfirst($city_selected),ucfirst($city_selected),ucfirst($country_selected));?></h2>
		<p class="lp_text"><?php printf( gettext("Vous recherchez une auberge de jeunesse, un hôtel pas cher, un appartement, une chambre d'hôtes, un Bed and Breakfast B&B ou une Pension à %s ?"),"<strong>".$city_selected."</strong>");?> <?php printf( gettext("N'allez pas plus loin, tous les bons plans à %s sont sur %s."),"<strong>".$city_selected."</strong>",$this->config->item('site_name'));?> <?php printf( gettext("Comme des milliers de jeunes et moins jeunes tous les mois, réservez vous aussi en toute sécurité le logement dernière minute idéal au meilleur prix dans tous les quartiers de %s : centre ville, quartier branché, quartier étudiant et universitaire, près des bus, de la gare, de l'aéroport ainsi que près de toutes les attractions de %s."),"<strong>".$city_selected."</strong>","<strong>".$city_selected."</strong>");?></p>	<?php */?>

	<div id="property_list">
		<div id="hostel-list">
			<?php            
			if(true)
			{
				$count = 0;
				foreach ($property_list["hostel_list"] as $hostel)
				{
					if($this->api_used == HB_API)
					{
							$count++;
							$this->load->view("hb/property_list",array(
                                "hostel" => $hostel,
                                'property_type' => $hostel["type"], 
                                "date_selected" => $date_selected,
                                "numnights_selected" => $numnights_selected));
					}
					else
					{
							$count++;
							$this->load->view("hw/property_list",array(
                                "hostel" => $hostel,
                                "date_selected" => $date_selected,
                                "numnights_selected" => $numnights_selected));
					}
				}

				if ($count==0)
				{
					?>
					<p class="no-result dotted-line-top"><?php echo _('Désolé aucun résultat pour ce critère');?></p>
					<?php
				}
			}
			?>

		</div>
	</div>
</div>
<?php if(!empty($city_info->city_code)){echo $city_info->city_code;}?>
