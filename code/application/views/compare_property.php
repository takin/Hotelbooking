<?php

$one            = '';
$propertyno     = '';
$proname        = '';
$propertyimage  = '';
$dorms          = '';
$Privates       = '';
$rating_over    = '';
$Atmosphere     = '';
$Staff          = '';
$Site           = '';
$Cleanliness    = '';
$Services       = '';
$Security       = '';
$Value          = '';
$Facilities     = '';
$nine           = '';
$eleven         = '';
$space          = '&nbsp;';
$maplink        = '';
$question_alert = '????';
 
for ($i = 0; $i < count($compare_data); $i++) {
	$protype = $compare_data[$i]['property_type'];

        // TODO
	if (0 && $compare_data[$i]['symbol'] != '' && $compare_data[$i]['symbol'] != 'NULL') {
		$symbol = $compare_data[$i]['symbol'];
	}
	else {
		$symbol = '&eur;'; //$compare_data[$i]['currency'];
	}

	$a = $i + 1;

	$one           .= '<th  class="control_button"><div class="close_btn" id="' . $a . '">x</div></th>';
	$propertyno    .= '<th  class="control_button" > <span class="column_order">' . $a . '</span> </th>';
 
	$proname       .= '<td  class="control_button" style="width: 115px"><a href="' . $compare_data[$i]['property_url'] . '" class="micro_site_Link" target="_blank">' . $compare_data[$i]['property_name'] . '</a></td>';
	$propertyimage .= '<td  class="control_button"><div class="quick_compare_image"><a href="' . $compare_data[$i]['images'] . '" class="micro_site_Link" > <img src="' . $compare_data[$i]['images'] . '" width="120" height="80"/></a> <span>' . _($protype) . '</span></div></td>';
	$maplink       .= '<td  class="control_button"><span class="link_color"><a href="#map_td">' . _('See Map') . '</a><span></td>';

        $property_number = $compare_data[$i]['property_number'];

	if ($this->api_used == HB_API) {
		if (0 && $compare_data[$i]['type'] == 'private') {
			$dorms    .= '<td  class="control_button">' . _('Not available') . '</td>';
			$Privates .= '<td  class="control_button">' . $symbol . ' ' . $compare_data[$i]['bed_price'] . '</td>';
		}
		elseif (0 && $compare_data[$i]['type'] == 'dorm') {
			$dorms    .= '<td  class="control_button">' . $symbol . ' ' . $compare_data[$i]['bed_price'] . '</td>';
			$Privates .= '<td  class="control_button">' . _('Not available') . '</td>';
	 	}
	 	else {
//			$dorms    .= '<td  class="control_button">' . _('Not available') . '</td>';
//			$Privates .= '<td  class="control_button">' . _('Not available') . '</td>';

//			$dorms    .= '';
//			$Privates .= '';

			$dorms    .= '<td  class="control_button dorm_price_container_' . $property_number . '"><span>' . _('Not available') . '</spa></td>';
			$Privates .= '<td  class="control_button private_price_container_' . $property_number . '"><span>' . _('Not available') . '</span></td>';	
	 	}
	}
	else {
		$dorms    .= '<td  class="control_button dorm_price_container_' . $property_number . '"><span>'. _('Not available') . '</span></td>';
		$Privates .= '<td  class="control_button private_price_container_' . $property_number . '"><span>'. _('Not available') . '</span></td>';
// . $symbol . ' ' . $compare_data[$i]['bed_price'] . '</td>';
	}

	if ($this->api_used == HB_API) {
		if ($compare_data[$i]['rating_overall'] != '' && (int)$compare_data[$i]['rating_overall']) {
			$rating_over .= '<td  class="control_button"><div class="bar-back1 group">
						<div class="bar-top1 green" style="width:' . $compare_data[$i]['rating_overall'] . '%"></div>
							<span class="rating-cat"></span>
							<span class="rating-value">' . $compare_data[$i]['rating_overall'] . '% </span>
					  </div></td>';
	 	}
		else {
			$rating_over .= '<td  class="control_button"></td>';
	 	}
	}
	else {
		if ($compare_data[$i]['rating'] != '' && (int)$compare_data[$i]['rating']) {
			$rating_over .= '<td  class="control_button"><div class="bar-back1 group">
					<div class="bar-top1 green" style="width:' . $compare_data[$i]['rating'] . '%"></div>
						<span class="rating-cat"></span>
						<span class="rating-value">' . $compare_data[$i]['rating'] . '% </span>
				  </div></td>';
	 	}
		else {
			$rating_over .= '<td  class="control_button"></td>';
	 	}
	}

	if ($this->api_used == HB_API) { 
		if ($compare_data[$i]['rating_atmosphere'] != '' && (int)$compare_data[$i]['rating_atmosphere']) {
			$Atmosphere .= '<td  class="control_button"><div class="bar-back1 group">
					<div class="bar-top1 green" style="width:' . $compare_data[$i]['rating_atmosphere'] . '%"></div>
						<span class="rating-cat"></span>
						<span class="rating-value">' . $compare_data[$i]['rating_atmosphere'] . '% </span>
				  </div></td>';
		}
		else {
			$Atmosphere .= '<td  class="control_button"></td>';
		}

		if ($compare_data[$i]['rating_staff'] != '' && (int)$compare_data[$i]['rating_staff']) {
			$Staff .= '<td  class="control_button"><div class="bar-back1 group">
					<div class="bar-top1 green" style="width:' . $compare_data[$i]['rating_staff'] . '%"></div>
						<span class="rating-cat"></span>
						<span class="rating-value">' . $compare_data[$i]['rating_staff'] . '% </span>
				  </div></td>';
		}
		else {
			$Staff .= '<td  class="control_button"></td>';
		}

		if ($compare_data[$i]['rating_location'] != '' && (int)$compare_data[$i]['rating_location']) { 
			$Site .= '<td  class="control_button"><div class="bar-back1 group">
					<div class="bar-top1 green" style="width:' . $compare_data[$i]['rating_location'] . '%"></div>
						<span class="rating-cat"></span>
						<span class="rating-value">' . $compare_data[$i]['rating_location'] . '% </span>
				  </div></td>';
		}
		else {
			$Site .= '<td  class="control_button"></td>';
		}

		if ($compare_data[$i]['rating_cleanliness'] != '' && (int)$compare_data[$i]['rating_cleanliness']) { 
			$Cleanliness .= '<td  class="control_button"><div class="bar-back1 group">
					<div class="bar-top1 green" style="width:' . $compare_data[$i]['rating_cleanliness'] . '%"></div>
						<span class="rating-cat"></span>
						<span class="rating-value">' . $compare_data[$i]['rating_cleanliness'] . '% </span>
				  </div></td>';
		}
		else {
			$Cleanliness.= '<td  class="control_button"></td>';
		}

		if ($compare_data[$i]['rating_facilities'] != '' && (int)$compare_data[$i]['rating_facilities']) {
			$Services .= '<td  class="control_button"><div class="bar-back1 group">
					<div class="bar-top1 green" style="width:' . $compare_data[$i]['rating_facilities'] . '%"></div>
						<span class="rating-cat"></span>
						<span class="rating-value">' . $compare_data[$i]['rating_facilities'] . '% </span>
				  </div></td>';
		}
		else {
			$Services .= '<td  class="control_button"></td>';
		}

		if ($compare_data[$i]['rating_safety'] != '' && (int)$compare_data[$i]['rating_safety']) {
			$Security .= '<td  class="control_button"><div class="bar-back1 group">
					<div class="bar-top1 green" style="width:' . $compare_data[$i]['rating_safety'] . '%"></div>
						<span class="rating-cat"></span>
						<span class="rating-value">' . $compare_data[$i]['rating_safety'] . '% </span>
				  </div></td>';
		}
		else {
			$Security .= '<td  class="control_button"></td>';
		}

		if ($compare_data[$i]['rating_value'] != '' && (int)$compare_data[$i]['rating_value']) {
			$Value .= '<td  class="control_button"><div class="bar-back1 group">
					<div class="bar-top1 green" style="width:' . $compare_data[$i]['rating_value'] . '%"></div>
						<span class="rating-cat"></span>
						<span class="rating-value">' . $compare_data[$i]['rating_value'] . '% </span>
				  </div></td>';
		}
		else {
			$Value .= '<td  class="control_button"></td>';
		}
	}

	$Facilities .= '<td  class="control_button">' . $space . '<span></span></td>';
}
?>

<div id="div_print">
	<div class="quick_compare">
		<div class="quick_compare_scroll" id="quick_compare_scroll">
			<div class="quick_compare_head"> 
				<span class="head_remove_button"></span>
				<!-- <span class="printpage" id="printpage" onClick="printdiv('div_print');"><?php echo  _('Print');?></span> -->
				<!--<span class="restore" id="restore"><?php //echo  _('Restore Everything');?></span>-->

				<h4><?php echo _('Quick Compare');?></h4>
			</div>

			<div class="logo_div">
				<img class="logo" src="<?php echo site_url().'images/'.$this->wordpress->get_option('aj_api_name').'/logo.png"';?> />
			</div>

			<br style="clear: both;">

			<table width="900" class="quick_compare_data" id="quick_com_data" border="0" cellspacing="0" cellpadding="0">
				<tbody>
					<tr class="quick_compare_grey">
						<th class="heading" style="width: 110px"><?php echo $space;?></th>
						<?php echo $one; ?>
					</tr>
					<tr class="quick_compare_grey">
						<th class="heading" ><?php echo $space;?></th>
						<?php echo $propertyno; ?>
					</tr>
					<tr class="quick_compare_grey">
						<td class="heading" ><?php echo $space;?></td>
						<?php echo $proname; ?>
					</tr>
					<tr class="quick_compare_grey">
						<td class="heading" ><?php echo $space;?></td>
						<?php echo $propertyimage; ?>
					</tr>
					<tr class="quick_compare_grey">
						<td class="heading" ><?php echo $space;?></td>
						<?php echo $maplink; ?>
					</tr>
					<?php  if ($this->api_used == HB_API) { ?>
 					<tr class="quick_compare_grey row_white" id="row-1">
						<td class="heading" ><a class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a><?php echo _('Dorms From')?></td>
						<?php echo $dorms; ?>
					</tr>
					<tr class="quick_compare_grey row_white" id="row-2">
						<td class="heading" ><a class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a> <?php echo _('Privates From')?></td>
						<?php echo $Privates; ?>
					</tr>

					<?php if ($rating_over) { ?>
					<tr class="quick_compare_grey rating row_white" id="row-3">
						<td class="heading" ><a class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a><?php echo _('Rating Overall')?></td>
						<?php echo $rating_over; ?>
					</tr>
					<?php }

					if ($Atmosphere) { ?>
					<tr class="quick_compare_grey rating row_white" id="row-4">
						<td class="heading" ><a class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a><?php echo _('Atmosphere')?></td>
						<?php echo $Atmosphere; ?>
					</tr>
					<?php } 
					if ($Staff) { ?>
					<tr class="quick_compare_grey rating row_white" id="row-5">
						<td class="heading" ><a class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a><?php echo _('Staff')?></td>
						<?php echo $Staff; ?>
					</tr>
					<?php }
					if ($Site) { ?>
					<tr class="quick_compare_grey rating row_white" id="row-6">
						<td class="heading" ><a class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a><?php echo _('Site')?></td>
						<?php echo $Site; ?>
					</tr>
					<?php } 
					if ($Cleanliness) { ?>
					<tr class="quick_compare_grey rating row_white" id="row-7">
						<td class="heading" ><a class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a><?php echo _('Cleanliness')?></td>
						<?php echo $Cleanliness; ?>
					</tr>
					<?php }
					if ($Services) { ?>
					<tr class="quick_compare_grey rating row_white" id="row-8">
						<td class="heading" ><a class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a><?php echo _('Services')?></td>
						<?php echo $Services; ?>
					</tr>
					<?php } 
					if ($Security) { ?>
					<tr class="quick_compare_grey rating row_white" id="row-9">
						<td class="heading" ><a class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a><?php echo _('Safety')?></td>
						<?php echo $Security; ?>
					</tr>
					<?php }
					if ($Value) { ?>
					<tr class="quick_compare_grey rating row_white" id="row-10">
						<td class="heading" ><a class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a><?php echo _('Value')?></td>
						<?php echo $Value; ?>
					</tr>
					<?php } ?>
					<!--<tr class="quick_compare_grey row_white">
						<td class="heading" ><?php echo _('Facilities')?> ( <span class="show_facilities" id="showmore"><?php echo _('Show More')?></span> )</td>
						<?php echo $Facilities; ?>
					</tr>-->
					<?php 
					foreach ($property_extra as $extra) {
						$hascheckedfe = false;
						for ($i = 0; $i < count($compare_data); $i++) {
							if (in_array($extra->hb_extra_id, $compare_data[$i]['extra']))  {
								$hascheckedfe = true;
							}
						}

						if ($hascheckedfe == true) { ?>
							<tr class="quick_compare_grey rating row_white" id="row-extra-<?php echo $extra->hb_extra_id;?>">
								<td class="facility"><a class="hideRowButton" data-rowid="row-5" href="#"><?php echo $space;?></a> <?php echo _($extra->description); ?></td>
								<?php
									for ($i = 0; $i < count($compare_data); $i++) {
										if (in_array($extra->hb_extra_id, $compare_data[$i]['extra']))  {
											echo '<td  class="control_button"><img src="'.site_url().'images/valid.gif"/></td>';
										}
										else {
											echo '<td  class="control_button">' . $space . '</td>';
										}
									} ?>
							</tr>
							<?php
						}
					}
					$x = 1;
					$class = 'quick_compare_grey rating row_white';
					$displaytr = '';
					foreach($property_feature as $featur) { 
						if ($x == 5) {
							$class = 'quick_compare_grey rating row_white';
							$displaytr = "none";
						}
						$haschecked=false;
						for($i=0; $i< count($compare_data); $i++){
							if (in_array($featur->hb_feature_id, $compare_data[$i]['feature'])) {
								$haschecked=true;
								break;
							}
						}

						if($haschecked==true){
							echo '<tr class="'.$class.'" id="row-featur-'.$x.'">
								<td class="facility"><a class="hideRowButton" data-rowid="row-5" href="#">'.$space.'</a>'. _($featur->description) .'</td>';
								for($i=0; $i< count($compare_data); $i++){
									if (in_array($featur->hb_feature_id, $compare_data[$i]['feature']))  {
										echo '<td  class="control_button"><img src="'.site_url().'images/valid.gif"/></td>';
									}
									else {
										echo '<td  class="control_button">'.$space.'</td>';
									}
								}
								echo '</tr>';
						}
						$x++;
					}
				 }
				else { ?>
 					<tr class="quick_compare_grey row_white" id="row-1">
						<td class="heading" ><a class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a><?php echo _('Dorms From')?></td>
						<?php echo $dorms; ?>
					</tr>

					<tr class="quick_compare_grey row_white" id="row-2">
						<td class="heading" ><a  class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a><?php echo _('Privates From')?></td>
						<?php echo $Privates; ?>
					</tr>
                                        <?php if ($rating_over) { ?>
					<tr class="quick_compare_grey rating row_white" id="row-3">
						<td class="heading" ><a class="hideRowButton" data-rowid="row-1" href="#"><?php echo $space;?></a><?php echo _('Rating Overall')?></td>
						<?php echo $rating_over; ?>
					</tr>
					<?php }
					$x=1;
					$class='quick_compare_grey rating row_white';
					$displaytr='';
					foreach($property_facelity as $facelity) {
						$haschecked=false;
						for($i=0; $i< count($compare_data); $i++){
							if(in_array($facelity->hw_facility_id, $compare_data[$i]['facelity'])) {
								$haschecked=true;
								break;
							}
						}
						if($haschecked==true){
							echo '<tr class="'.$class.'" id="row-extra-'.$x.'"">
								<td class="facility"><a class="hideRowButton" data-rowid="row-5" href="#">'.$space.'</a>'._($facelity->description).'</td>';
								for($i=0; $i< count($compare_data); $i++){
									if(in_array($facelity->hw_facility_id, $compare_data[$i]['facelity']))  {
										echo '<td  class="control_button"><img src="'.site_url().'images/valid.gif"/></td>';
									}
									else{
										echo '<td  class="control_button">'.$space.'</td>';
									}
								}
								echo '</tr>';
						}
						$x++;
					}
				?>
			<?php } ?>

				<tr class="quick_compare_grey rating row_white" id="maprow">
					<td class="heading facility ">
						<a class="hideRowButton" data-rowid="row-51" href="#"><?php echo $space;?></a>
						<?php echo _('Map');?>
					</td>
					<td colspan="5" id="map_td">
						<div id="map_canvas" style="width:800px;height:350px;"><?php _('Map');?></div>
						<div id="static_map"></div>
						<input type="hidden" id="map_lat"/>
					</td>
				</tr>
			</tbody>
			</table>
		</div>
	</div>
</div>
