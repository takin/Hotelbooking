<div class="main-cotainer"  id="main-div" >
<div>
	<div style="float: left;margin: 10px 10px;">
	<h2 style="display:inline-block; margin:0;"><a style="text-decoration:none; color: #3087C9; font-size:18px;" href="<?php echo $propertyurl;?>"><?php if($this->api_used == HB_API){ echo $hostel['NAME']; }else{ echo $hostel->property_name;  } ?></a></h2>
	<?php if ( $this->api_used == HB_API && !empty($hostel['ADDRESS']) ){?>
			
					<div class="content_block">
						<p>
						<?php 
						if (!empty($hostel["ADDRESS"]["STREET1"]))echo var_check($hostel["ADDRESS"]["STREET1"],"");
						if (!empty($hostel["ADDRESS"]["STREET3"]))echo ' '.var_check($hostel["ADDRESS"]["STREET2"],"");
						if (!empty($hostel["ADDRESS"]["STREET3"]))echo ' '.var_check($hostel["ADDRESS"]["STREET3"],"");
						if (!empty($hostel["ADDRESS"]["CITY"]))echo ', '.var_check($hostel["ADDRESS"]["CITY"],"");
						if (!empty($hostel["ADDRESS"]["STATE"]))echo ', '.var_check($hostel["ADDRESS"]["STATE"],"");
						if (!empty($hostel["ADDRESS"]["COUNTRY"]))echo ', '.var_check($hostel["ADDRESS"]["COUNTRY"],"") ;
						if (!empty($hostel["ADDRESS"]["ZIP"]))echo ', '.var_check($hostel["ADDRESS"]["ZIP"],"") ;?>
						</p>
					</div>
					<?php }else{ ?>
					<div class="content_block">
					<p><?php echo var_check($hostel->address1,""); echo ' '.var_check($hostel->address2,""); echo ', '.var_check($hostel->city,"");  echo ', '.var_check($hostel->country,"") ;?></p>
					</div>
					<?php } ?>
	</div>
	<div style="float: right;margin: 10px 0;width: 48px;">
		<div id="preurl" style="display: inline;float: left;"></div>
		<div id="nexturl" style="display: inline;float: right;"></div>
	</div>
</div>	
  <div class="top-map">
    <div class="top-map-left">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/quick_view.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/jquery.ad-gallery.css" media="all" />
	<script type="text/javascript" src="<?php echo base_url();?>js/ad-gallery.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/property_quickview.js"></script>
	<div id="gallery" class="ad-gallery">
      <div class="ad-image-wrapper">
      </div>

<?php if (empty($quick_view)) { ?> 
      <div class="ad-controls">
      </div>
<?php } ?>
      <div class="ad-nav">
        <div class="ad-thumbs">
          <ul class="ad-thumb-list">
		  			
		  <?php if($this->api_used == HB_API) {
			$shortimages=$hostel['BIGIMAGES'];
			for($i=0;$i<count($shortimages);$i++)
			{
				?>	
            <li>
              <a href="<?php echo $shortimages[$i];?>">
                <img src="<?php echo $shortimages[$i];?>" class="image0" height="53" width="74">
              </a>
			  
            </li>
			<?php
			}
			}else{
			
			$shortimages=$hostel->PropertyImages;
			foreach ($shortimages as $image)
			{
				?>	
            <li>
              <a href="<?php echo $image->imageURL;?>">
                <img src="<?php echo $image->imageURL;?>" class="image0" height="53" width="74">
              </a>
            </li>
			<?php
			}
		
				
			}
		?>
            
          </ul>
        </div>
      </div>
    </div>

	      <div class="bottom-feature">
		  <?php if (!empty($propertyextras_included)){?>
						
							<div class="bottom-feature-data">
							<p><b><?php echo _("What's Included");?></b></p>							
							<div class="group">
								<?php if (!empty($propertyextras_included_translated)){?>
									<ul class="green-li increase1 original" style="display:none;">
									<?php foreach ($propertyextras_included as $id => $value){?>
											<li><?php echo $id.': <strong>'._("Free").'</strong>';?></li>
									<?php }?>
									</ul>
									<ul class="green-li increase1 translated">
									<?php foreach ($propertyextras_included_translated as $id => $value){?>
											<li><?php echo $id.': <strong>'._("Free").'</strong>';?></li>
									<?php }?>
									</ul>
								<?php }else{?>
									<ul class="green-li increase1 original">
									<?php foreach ($propertyextras_included as $id => $value){?>
											<li><?php echo $id.': <strong>'._("Free").'</strong>';?></li>
									<?php }?>
									</ul>
								<?php }?>
							</div>
							</div>
					
					<?php }?>
		  
        <div class="bottom-feature-data">
          <p><b><?php echo _("Commodité");?></b></p>
          <div class="list-left">
		  <?php if($this->api_used == HB_API) {
		  	 $feature= $features_translated	;
			for($a=0;$a<count($feature);$a++){
			?>
            <div class="check">
				<?php echo $feature[$a];?></div>
			<?php 
			} 
			}else{ 
			$facilities = (array) $hostel->facilities;
					if(!empty($hostel->facilitiesTranslated)){
						$facilities = (array) $hostel->facilitiesTranslated;
					}
					if(!empty($facilities)){
						foreach ($facilities as $facility){
							if(!empty($facility)){
								echo '<div class="check">'.stripslashes(var_check($facility,"")).'</div>';
							}
						}
					}
			}?>
        </div>
        </div>
		
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
					<?php if(!empty($district_info) || !empty($landmarks)) { ?>
					<div class="content_block">
                                                <?php
                                             if (is_array($district_info) && !empty($district_info))
                                                 { ?>
                                                <div id="hostel_mapView_districts" class="hostel_mapView_districts">
                                                    <b>
                                             <span class="mapView_districtWord"><?php echo _('Districts');?>:</span></b>

                                                 <?php
                                                 foreach ($district_info as $key => $district)
                                                     {
                                                      $checked = "";

                                                     if ($key == 0) {
                                                         $checked = "checked";
                                                     }

                                                     ?>
                                                      <p><input type="radio" id="distrinct" name="distrinct" <?php echo $checked; ?> value="<?php echo $district->um_id; ?>"
                                                  onchange="GoogleMap.prototype.changeDistrictLayer(<?php echo $district->um_id; ?>);"><?php echo $district->district_name; ?></p>

                                            <?php  }//end Foreach  ?>
                                       
                                             </div>
                                              <?php   }// end if ?>

                                                    <?php // start showing landmarks checkboxes
                                             if (is_array($landmarks) && !empty($landmarks))
                                                 { ?>
                                                <div id="hostel_mapView_landmarks" class="hostel_mapView_landmarks">
                                                    <b>
                                             <span class="mapView_landmarkWord"><?php echo _('Landmarks (within 2km)');?>:</span></b>

                                                 <?php
                                                 foreach ($landmarks as $key => $landmark)
                                                     {
                                                      $checked = "";

                                                     if ($key == 0) {
                                                         $checked = "checked";
                                                     }

                                                     ?>
                                                      <p><input type="radio" id="landmark" name="landmark" <?php echo $checked; ?> value="<?php echo $landmark->geo_latitude . "###". $landmark->geo_longitude; ?>"
                                                  onchange="GoogleMap.prototype.changeLandmarkLayer(<?php echo "'".$landmark->geo_latitude . "###". $landmark->geo_longitude . "'"; ?>);"><?php echo $landmark->landmark_name; ?></p>

                                            <?php  }//end Foreach  ?>
                                             </div>
                                              <?php   }// end if
                                              // end showing landmarks checkboxes
                                              ?>
						
					</div>
					<?php } ?>
				</div> 
			</div>				
		</div>
    </div>
  </div>
 
<?php if (empty($quick_view)) { ?> 
  <div id="city_avail_table_74087" class="booking_table_city" style="margin-top:8px;">
  <?php
$min_price_shared = 0;
$min_price_private = 0;

$max_guest_per_unity_enable = false;

if(!empty($property_rooms["sharedRooms"]) &&
!empty($property_rooms["sharedRooms"][0]['max_guest_per_unity']))
{
  $max_guest_per_unity_enable = true;
}
?>
    <div class="avail-wrap">
      <table cellspacing="0" cellpadding="0" border="0">
        <tbody>
          <tr>
            <th class="title"> <div class="room-type"> <a href="#" class="show-room-info"><?php echo _('Chambres partagées - Dortoirs'); ?></a>
                <div class="room-type-info" style="display: none;">
                  <h5><?php echo _('Chambres partagées - Dortoirs'); ?></h5>
                  <p><?php echo _('Price per person (Dorm shared with others).'); ?> <?php echo _('You must share the room (unless you purchase all the beds in the dorm).'); ?></p>
                  <span class="room-info-arrow"></span> </div>
              </div>
            </th>
            <th>&nbsp;</th>
            <?php
    $date = clone $dateStart;

    for($i=0;$i<$numNights;$i++)
    {
      echo "<th>";
			echo my_mb_ucfirst(mb_substr(strftime("%a",$date->format('U')),0,2, 'UTF-8'));
      echo strftime("<br />%e",$date->format('U'));
      $date->modify("+1 day");
      echo "</th>";
    }
    ?>
            <th class="last"><?php echo _('Beds (max)'); ?></th>
          </tr>
          <?php

if(!empty($property_rooms["sharedRooms"]))
{
  foreach($property_rooms["sharedRooms"] as $room)
  {
    ?>
    <tr>
      <td class="first">
        <?php
        if(!empty($room['descriptionTranslated']))
        {
          echo '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$room['description'].'">'.$room['descriptionTranslated'].'</span>';
        }
        else
        {
          echo $room['description'];
        }
        if($breakfast_included == 1){
					echo '<span class="free-breakfast">';
					echo _('Breakfast Included');
					echo '</span>';
				}?>
				
      </td>      
				<td align="center" title="<?php echo _('Bed in a dorm. 1 person per bed maximum')?>"><span class="nbpeople-table icon-nbpeople nbpeople-1">1</span></td>
				
        <?php
      //dates columns
      $date = clone $dateStart;

      for($i=0;$i<$numNights;$i++)
      {
        ?>
        <td align="center">
          <?php
          if(!empty($room["availableDates"][$date->format("Y-m-d")]))
          {
            if ($numNights > 5)
            {
              ?>
              <span class="price">
              <?php
              echo $room["currency"].round($room["availableDates"][$date->format("Y-m-d")]["price"],0);
              ?>
              </span>
              <?php
            }
            else
            {
              ?>
              <span class="price">
              <?php
              echo $room["currency"].$room["availableDates"][$date->format("Y-m-d")]["price"];
              ?>
              </span>
              <?php
            }
						if($min_price_shared == 0){
							$min_price_shared = $room["availableDates"][$date->format("Y-m-d")]["price"];
						}elseif($min_price_shared > $room["availableDates"][$date->format("Y-m-d")]["price"]){
							$min_price_shared = $room["availableDates"][$date->format("Y-m-d")]["price"];
						}
						$currency_formin = $room["currency"];
          }
          else
          {
            ?>
            <span class="na-book price">0</span>
            <?php
          }
          ?>
        </td>
        <?php

        $date->modify("+1 day");
      }
      ?>
      <td align="center">
      <?php echo $room["availableBeds"]?>
      </td>
    </tr>
    <?php
  }
}
else
{

  ?>
  <tr>
  	<td class="first" colspan="<?php echo $numNights+3;?>">
  	<?php echo _("No dorms available");?>
  	</td>
  </tr>
  <?php
}
?>
          <tr>
            <th class="title"> 
			<div class="room-type"> 
				<a href="#" class="show-room-info"><?php echo _('Chambres privées'); ?></a>
            	<div class="room-type-info">
				<h5><?php echo _('Chambres privées'); ?></h5>
				<p><?php echo _('Price per room (not per person).'); ?> <?php echo _('You must pay for the whole private room, even if you do not need all the beds. The room cannot be shared.'); ?></p>
				<span class="room-info-arrow"></span>

			</div>
			</div>
            </th>
            <th>&nbsp;</th>
            <?php
  $date = clone $dateStart;

  for($i=0;$i<$numNights;$i++)
  {
    echo "<th>";
    echo my_mb_ucfirst(mb_substr(strftime("%a",$date->format('U')),0,2, 'UTF-8'));
    echo strftime("<br />%e",$date->format('U'));
    $date->modify("+1 day");
    echo "</th>";
  }
  ?>
            <th class="last"><?php echo _('Rooms (max)'); ?></th>
          </tr>
          	<?php
if(!empty($property_rooms["privateRooms"]))
{
  foreach($property_rooms["privateRooms"] as $room)
  {
    ?>
    <tr>
      <td class="first">
        <?php
        if(!empty($room['descriptionTranslated']))
        {
          echo '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$room['description'].'">'.$room['descriptionTranslated'].'</span>';
        }
        else
        {
          echo $room['description'];
        }
        if($breakfast_included == 1){
					echo '<span class="free-breakfast">';
					echo _('Breakfast Included');
					echo '</span>';
				}?>
      </td>
			<td align="center" title="<?php echo _('Maximum number of guests in the room')?>" ><span class="nbpeople-table icon-nbpeople nbpeople-<?php echo $room['max_guest_per_unity'];?>"><?php echo $room['max_guest_per_unity'];?> x</span></td>
        <?php
      //dates columns

      $date = clone $dateStart;
      for($i=0;$i<$numNights;$i++)
      {
        ?>
        <td align="center">
          <?php
          if(!empty($room["availableDates"][$date->format("Y-m-d")]))
          {
            if ($numNights > 5)
            {
              ?>
              <span class="price">
              <?php
              echo $room["currency"].round($room["availableDates"][$date->format("Y-m-d")]["price"],0);
              ?>
              </span>
              <?php
            }
            else
            {
              ?>
              <span class="price">
              <?php
              echo $room["currency"].$room["availableDates"][$date->format("Y-m-d")]["price"];
              ?>
              </span>
              <?php
            }
						if($min_price_private == 0){
							$min_price_private = $room["availableDates"][$date->format("Y-m-d")]["price"];
						}elseif($min_price_private > $room["availableDates"][$date->format("Y-m-d")]["price"]){
							$min_price_private = $room["availableDates"][$date->format("Y-m-d")]["price"];
						}
						$currency_formin = $room["currency"];
          }
          else
          {
            ?>
            <span class="na-book price">0</span>
            <?php
          }
          ?>
        </td>
        <?php

        $date->modify("+1 day");
      }
      ?>
      <td align="center">
      <?php echo $room["availableRooms"]?>
      </td>
    </tr>
    <?php
  }
}
else
{
  ?>
  <tr>
  	<td class="first" colspan="<?php echo $numNights+3;?>">
  	<?php echo _("No private room available");?>
  	</td>
  </tr>
  <?php
}
?>
        </tbody>
      </table>
      
    </div>
  </div>

   <div class="bottom-table group" id="book-now">
		<a href="<?php echo $propertyurl; ?>" class="reserve button-green hoverit" title="<?php echo _("Plus sur ce logement");?>"><?php echo _("Réserver");?></a>
        <span><?php echo _('Best price. We guarantee it.')?></span>
		<span><?php echo _('It only takes 2 minutes')?></span>
   </div>
<?php 
}
else { ?>
   <div class="bottom-table group" style="height: 25px" id="book-now">
      <a href="<?php echo $propertyurl; ?>" class="reserve button-green hoverit" title="<?php echo _("Plus sur ce logement");?>"><?php echo _("Réserver");?></a>
   </div>
<?php } ?> 

   <?php if( $this->api_used == HB_API && $hostel['IMPORTANTINFORMATION']!='' ){ ?>
   <div class="readmore readmore-image" id="showmore"><?php echo _('Read more…'); ?></div>
	  <div class="bottom-feature1" id="bottomfeature1">
	  <div class="bottom-feature-data1">
			<div class="content_block">
			<h2><?php echo _("Informations Importantes");?></h2>
				<div class="group">
					<p><?php echo $hostel['IMPORTANTINFORMATION'];?></p>
				</div>
	 		</div>
	 	</div>			
	</div>
	<?php }  ?>
	 
	<?php if (!empty($hostel->conditions)){ ?>  
	<div class="readmore readmore-image" id="showmore"><?php echo _('Read more…'); ?></div>
	  <div class="bottom-feature1" id="bottomfeature1">
	  	<div class="bottom-feature-data1">
				<div class="group">
					<h2><?php echo _("Informations Importantes");?></h2>
					<?php
					if(!empty($hostel->conditionsTranslated))
					{
						echo '<div class="translated">'.strip_tags($hostel->conditionsTranslated,'<p>').'</div>';
						echo '<div class="original" style="display:none;">'.nl2p(var_check(strip_tags($hostel->conditions,'<p>'),""),false,true).'</div>';
					}
					else
					{
						echo nl2p(var_check(strip_tags($hostel->conditions),""),false,true);
					}
					?>
				</div>
		</div>
	  </div>
	<?php }?>

<?php if (empty($quick_view)) { ?> 
	 <div class="readmore" id="showmorereviews"><?php echo _('Latest Reviews'); ?></div>
<?php } ?>
	 <div class="bottom-feature1" id="bottomfeature2">
	 <?php if(!empty($property_ratings) && $this->api_used == HB_API) { ?>
  	<div class="bottom-feature-data1">
	<?php $empty_rating = 0;foreach($property_ratings as $rating_category => $rating_value){if($rating_value == ""){$empty_rating++;}}?>
	<?php if($empty_rating < 9){?>
	<div class="box_content box_round group rating_bars">
	<span class="title"><?php echo ucwords(_("évaluation moyenne"));?>
	<?php if(!empty($hostel_db_data->rating_overall)){
					echo ceil($hostel_db_data->rating_overall).' %';
				}elseif(!empty($hostel["RATING"])){
					echo ceil($hostel["RATING"]).' %';
				}?>
	</span>
	<div class="clearfix bar-overview">
	<?php $countrating = 0; foreach($property_ratings as $rating_category => $rating_value):if($rating_category =="overall") continue;?>
	<?php if($rating_category != 'fun'){?>
	<?php if ($countrating == 0) {?>
	<div class="bar-rating">
	<?php }?>


		<div class="bar-back group">
			<div class="bar-top yellow"<?php if(!empty($rating_value)){?> style="width:<?php echo $rating_value?>%"<?php }?>></div>
			<img alt="" src="<?php echo base_url();?>images/rating-<?php echo $rating_category;?>.png"/>
			<span class="rating-cat">
			<?php
			switch ($rating_category){
				case 'atmosphere':
					echo _("Atmosphere");
					break;
				case 'staff':
					echo _("Staff");
					break;
				case 'location':
					echo _("Location");
					break;
				case 'cleanliness':
					echo _("Cleanliness");
					break;
				case 'facilities':
					echo _("Facilities");
					break;
				case 'safety':
					echo _("Safety");
					break;
				case 'value':
					echo _("Value");
					break;
			}
			?>
			</span>
			<span class="rating-value">
			<?php
				if(!empty($rating_value))
					echo $rating_value."%";
				else
					echo _("N/A");
				?>
			</span>
		</div>
	<?php if ($countrating == 6) {?>
	</div>
	<?php }?>
	<?php $countrating++;}endforeach;?>
	</div>
	</div>
	<?php } //end if for display rating if non empty?>
					<div class="comment_list_main">
						<h2 style="margin-bottom:5px;" class="margbot15"><?php echo _("Commentaires et évaluations de voyageurs");?></h2>
						<?php for($z=0;$z<$user_reviews['review_count'];$z++){?>
				  			<div class="comment_list group">
							<div class="comment_content box_round">
							 <div class="rating_user"><?php echo $user_reviews['user_reviews'][$z]['review_rating'];?>%</div>           
				    <div>
						<p><?php echo $user_reviews['user_reviews'][$z]['review_likebest'];?></p></div>
							</div>
					
					<p class="comment_author">
						<span class="icon_user_review">94%</span>
								<?php printf(gettext("Par %s"), $user_reviews['user_reviews'][$z]['author_name']); ?> | <?php printf(gettext("Le %s"), date_conv($user_reviews['user_reviews'][$z]['review_date'], $this->wordpress->get_option('aj_date_format'))); ?>			
						</p>
				</div>
				<?php } ?>
  
		</div>
	</div>
  <?php }else{ ?>
  		  	<div class="bottom-feature-data1">
			<div class="comment_list_main">
					    <?php if(!empty($hostel->rating)){
						$rating ='';
						if(($hostel->rating>59) && ($hostel->rating<70) )
						{
						$rating = _("Good");
			            }
			            elseif(($hostel->rating>69) && ($hostel->rating<80) )
			            {
						$rating = _("Very good");
					    }
						elseif(($hostel->rating>79) && ($hostel->rating<90) )
						{
						$rating = _("Great");
						}
						elseif(($hostel->rating>89))
						{
						$rating = _("Fantastic");
						}
						?>
						<div class="rating_user">
							<span class="" title="<?php echo _("évaluation moyenne");?>"><strong class="txt-mid green"><?php echo $rating;?></strong><strong style="color:#333333;"><?php echo $hostel->rating;?> %</strong></span>
						</div>
						<?php } ?>
						<h2 style="margin-bottom:5px;" class="margbot15"><?php echo _("Commentaires et évaluations de voyageurs");?></h2>
						<?php for($z=0;$z<$user_reviews['review_count'];$z++){?>
					  		<div class="comment_list group">
									<div class="comment_content box_round">
									<div class="rating_user"><?php echo $user_reviews['user_reviews'][$z]['review_rating'];?>%</div>           
						    		<div>
										<p><?php if($user_reviews['user_reviews'][$z]['review_translated']!= ''){ echo $user_reviews['user_reviews'][$z]['review_translated']; }else { echo $user_reviews['user_reviews'][$z]['review']; }?></p></div>
									</div>						
									<p class="comment_author">
										<span class="icon_user_review">94%</span>
										<?php printf(gettext("Par %s"), $user_reviews['user_reviews'][$z]['author_name']); ?> | <?php printf(gettext("Le %s"), date_conv($user_reviews['user_reviews'][$z]['review_date'], $this->wordpress->get_option('aj_date_format'))); ?>			
									</p>
							</div>
						<?php } ?>
			</div>
	</div>
  <?php } ?>
  </div>
</div>

</div>




