<div id="sidebar" class="grid_4 hostel_view_side">
	<a id="back_to_results" title="<?php echo _('Back to search results')?>" class="back_to_results expand" href="<?php echo base_url().$hostel["ADDRESS"]["COUNTRY"].'/'.$hostel["ADDRESS"]["CITY"];?>"><strong>&laquo; <?php echo _('Back to search results')?></strong></a>
	<?php if(!isset($date_selected))      $date_selected = NULL;
	if(!isset($numnights_selected)) $numnights_selected = NULL;
	if(!isset($bc_continent))       $bc_continent = NULL;
	if(!isset($bc_country))         $bc_country = NULL;
	if(!isset($bc_city))            $bc_city = NULL;
	$this->load->view('includes/side_search_box',array('date_selected' => $date_selected, 'current_view' => $current_view,'numnights_selected' => $numnights_selected,'bc_continent' => $bc_continent,'bc_country' => $bc_country,'bc_city' => $bc_city));
	?>
        <?php
        if(is_array($hostel) && array_key_exists("GPS", $hostel)){  ?>
		<div class="box_content map_button_box box_round" id="map_button_side">
			<a id="city_map_show_hostel" href="javascript:void(0);" onclick="$('#show_full_map').trigger('click'); $(document).scrollTop( $('#show_full_map').offset().top );  ">
			<span><strong><?php echo _("Voir la carte");?></strong></span>
			<img class="" src="https://maps.google.com/maps/api/staticmap?center=<?php echo $hostel["GPS"]["LAT"];?>,<?php echo $hostel["GPS"]["LON"];?>&zoom=10&size=253x125&sensor=false&language=<?php echo $this->wordpress->get_option('aj_lang_code2');?>&markers=<?php echo $hostel["GPS"]["LAT"];?>,<?php echo $hostel["GPS"]["LON"];?>" />
                        </a>
		</div>
		<?php }?>
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
			<div class="bar-top green"<?php if(!empty($rating_value)){?> style="width:<?php echo $rating_value?>%"<?php }?>></div>
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


	<?php //$this->load->view('includes/widget-cours'); ?>
	<?php $this->load->view('includes/video-popup'); ?>
	<?php $this->load->view('includes/testimonials'); ?>
	<?php $this->load->view('includes/widget-qr-code'); ?>
	<?php $this->load->view('includes/siteinfo'); ?>
</div>
<?php
//error handling standard
if($api_error==false)
{
	//$hostel is set
}
elseif($api_error_msg==false)
{
	//serveur inaccessible en ce moment (HW only)
}
else
{
	//error message = $api_error_msg->UserMessage->message
	//error details (en anglais) = $api_error_msg->UserMessage->detail
	?>
<div id="main" class="grid_12">
	<div class="box_content box_round group">
		<h1 class="content_title"><?php echo _("Erreur de requête");?></h1>
		<div class="dispo-error">
			<p><strong><?php echo _("Erreur:");?></strong>
			<?php
				if(!empty($api_error_msg->messageTranslated))
				{
					echo $api_error_msg->messageTranslated;
				}
				else
				{
					echo $api_error_msg->message;
				}
			?>
			<br>
			</p>
			<p><strong><?php echo _("Détails:");?></strong>
			<?php
				if(!empty($api_error_msg->detailTranslated))
				{
					echo $api_error_msg->detailTranslated;
				}
				else
				{
					echo $api_error_msg->detail;
				}
			?>
			<br>
			</p>
		</div>
	</div>
</div>
<?php } ?>
<?php if($api_error==false): //print_r($hostel->PropertyImages)?>

<div id="main" class="grid_12">

	<?php if (isset($_POST["submitted"])){?>
	<div class="box_content box_round group comment_sent">
	<h2 class="comment_sent"><?php echo _('Votre commentaire a été envoyé à notre équipe pour approbation. Merci!');?></h2>
	</div>
	<?php }else{?>
	<?php if ($this->input->get('comment', TRUE) == "insert"){?>
	<div class="box_content box_round group">
	<div class="enter-comment content_block">
		<h2 class="margbot15"><?php  printf(gettext("Laissez vos impressions sur %s"),var_check($hostel["NAME"],""));?></h2>
		<form action="" method="POST" id="comment-insert">
			<input type="hidden" name="property_number" value="<?php echo $hostel["ID"]?>">
			<input type="hidden" name="property_name" value="<?php echo $hostel["NAME"]?>">
			<input type="hidden" name="property_city" value="<?php echo $hostel["ADDRESS"]["CITY"]?>">
			<input type="hidden" name="property_country" value="<?php echo $hostel["ADDRESS"]["COUNTRY"]?>">
			<input type="hidden" name="property_type" value="<?php echo $hostel["TYPE"]?>">
			<table>
				<tr>
					<td><label for="firstname"><?php echo _("Votre Prénom:"); ?></label>
					<input type="text" id="comment_firstname" name="firstname" class="large-text" value="<?php echo set_value('firstname', ''); ?>"/></td>
					<td><?php echo form_error('firstname'); ?></td>
					<script type="text/javascript">
						var comment_firstname = new LiveValidation('comment_firstname', { validMessage: ' ', wait: 500});
						comment_firstname.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});

						comment_firstname.add( Validate.Length, { minimum: 2 } );
					</script>
				</tr>
				<tr>
					<td><label for="lastname"><?php echo _("Votre Nom (ne sera pas publié):");?></label>
					<input type="text" id="comment_name" name="lastname" class="large-text" value="<?php echo set_value('lastname', ''); ?>"/></td>
					<td><?php echo form_error('lastname'); ?></td>
					<script type="text/javascript">
						var comment_name = new LiveValidation('comment_name', { validMessage: ' ', wait: 500});
						comment_name.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});

						comment_name.add( Validate.Length, { minimum: 2 } );
					</script>
				</tr>
				<tr>
					<td>
					<label for="email"><?php echo _("Votre Email (ne sera pas publié):");?></label>
					<input type="text" id="comment_email" name="email" class="large-text" value="<?php echo set_value('email', ''); ?>"/><?php echo form_error('email'); ?>
					</td>
					<td><?php echo form_error('email'); ?></td>
					<script type="text/javascript">
						var comment_email = new LiveValidation('comment_email', { validMessage: ' ', wait: 500});
						comment_email.add(Validate.Email, {failureMessage: "<?php echo _('Insérer un courriel valide'); ?>"});
						comment_email.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
					</script>
				</tr>

				<tr>
					<td>
					<label for="nationality"><?php echo _("Nationalité :");?></label>
					<?php $this->Db_country->select_country("Nationality","nationality",set_value('nationality', ''),"style=\"width: auto;\"","en",$this->site_lang,_('Choisir le pays'));  ?>
					</td>
					<td><?php echo form_error('nationality'); ?></td>
					<script type="text/javascript">
						var Nationality = new LiveValidation('Nationality', { validMessage: ' ', wait: 500});
						Nationality.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
					</script>
				</tr>

				<tr>
					<td>
					 <label for=""><?php echo _("Veuillez évaluer votre expérience:");?></label>
							<div class="stars clearfix">
							<input id="star_radio" type="radio" class="star {split:2}" name="star-rating" value="1" <?php echo set_radio('star-rating', '1'); ?>/>
							<input type="radio" class="star {split:2}" name="star-rating" value="2" <?php echo set_radio('star-rating', '2'); ?>/>
							<input type="radio" class="star {split:2}" name="star-rating" value="3" <?php echo set_radio('star-rating', '3'); ?>/>
							<input type="radio" class="star {split:2}" name="star-rating" value="4" <?php echo set_radio('star-rating', '4'); ?>/>
							<input type="radio" class="star {split:2}" name="star-rating" value="5" <?php echo set_radio('star-rating', '5'); ?>/>
							<input type="radio" class="star {split:2}" name="star-rating" value="6" <?php echo set_radio('star-rating', '6'); ?>/>
							<input type="radio" class="star {split:2}" name="star-rating" value="7" <?php echo set_radio('star-rating', '7'); ?>/>
							<input type="radio" class="star {split:2}" name="star-rating" value="8" <?php echo set_radio('star-rating', '8'); ?>/>
							<input type="radio" class="star {split:2}" name="star-rating" value="9" <?php echo set_radio('star-rating', '9'); ?>/>
							<input type="radio" class="star {split:2}" name="star-rating" value="10" <?php echo set_radio('star-rating', '10'); ?>/>

							</div>
					 </td>
					 <td><?php echo form_error('star-rating'); ?></td>
				</tr>
				<tr>
					 <td>
					 <label for="comment"><?php printf(gettext("Vos commentaires sur %s :"),var_check($hostel["NAME"],""));?></label>
					 <textarea id="text_comment" name="comment" cols="50" rows="10"><?php echo set_value('comment', ''); ?></textarea>
					 </td>
					 <td><?php echo form_error('comment'); ?></td>
					 <script type="text/javascript">
						var text_comment = new LiveValidation('text_comment', { validMessage: ' ', wait: 500});
						text_comment.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						</script>
				</tr>

				<tr>
					<td>
					 <label style="padding-top:10px; padding-bottom:5px;" for=""><?php echo _("La date de votre dernier séjour à cet endroit :");?></label>

					 <?php select_month("month","month_comment","small-select",set_value("month_comment",NULL)); ?>

					 <?php select_year("year-comment","year_comment","small-select",0,-11,set_value("year_comment",NULL)); ?>
					 </td>
					 <td></td>
				</tr>
				<tr>
					<td>
							<input type="hidden" name="submitted" value="true" />
							<input type="submit" class="button-green box_round hoverit" name="comment-submit" id="comment-submit" value="<?php echo _("Soumettre");?>" />
					</td>
					<td></td>
				</tr>

			</table>
		</form>
		</div>
	</div>
	<?php  }}?>


	<div class="box_content box_round group">


		<?php if (!empty($hostel["IMAGES"])){?>
		<div class="box_round image_container">
			<div class="slideshow" id="slideshow">
				<div class="slides">
					<div class="main-pic">
						<?php foreach ($hostel["BIGIMAGES"] as $image) :?>
						<a class="openup" rel="<?php echo var_check($hostel["NAME"],""); ?>" href="<?php echo var_check($image,"/test.jpg"); ?>" alt="<?php echo $hostel["NAME"]; ?>">
						<?php /*?><img class="slide-zoom" title="<?php echo _("Click to enlarge"); ?>" width="290" height="210" src="<?php echo base_url();?>images/slide-zoom.png" alt="" /><?php */?>
						<img class="main" width="210" data-href="<?php echo $image; ?>" src="<?php echo site_url("images/V2/blank.gif"); ?>" alt="<?php echo $hostel["NAME"]; ?>" />
						<img class="main" width="210" src="<?php echo $image; ?>" alt="<?php echo $hostel["NAME"]; ?>" /></a>
						<?php endforeach;?>
					</div>
				</div>
			</div>
		</div>
		<?php }?>
		<div class="group">
		<h1><?php echo my_mb_ucfirst($bc_city);?> - <?php echo $this->Db_term_translate->get_term_translation($hostel["TYPE"],$this->site_lang); ?> - <?php echo var_check($hostel["NAME"],"");?></h1>
		<?php $prop_type = $hostel["TYPE"];
		if(!empty($hostel["TYPE_translated"]))
		{
			$prop_type = $hostel["TYPE_translated"];
		}
		?>
		<p class="address_title">
			<?php if (!empty($hostel["ADDRESS"]["STREET1"]))echo var_check($hostel["ADDRESS"]["STREET1"],"");
			if (!empty($hostel["ADDRESS"]["STREET3"]))echo ' '.var_check($hostel["ADDRESS"]["STREET2"],"");
			if (!empty($hostel["ADDRESS"]["STREET3"]))echo ' '.var_check($hostel["ADDRESS"]["STREET3"],"");
			if (!empty($hostel["ADDRESS"]["CITY"]))echo ', '.var_check($hostel["ADDRESS"]["CITY"],"");
			if (!empty($hostel["ADDRESS"]["STATE"]))echo ', '.var_check($hostel["ADDRESS"]["STATE"],"");
			if (!empty($hostel["ADDRESS"]["COUNTRY"]))echo ', '.var_check($hostel["ADDRESS"]["COUNTRY"],"") ;
			if (!empty($hostel["ADDRESS"]["ZIP"]))echo ', '.var_check($hostel["ADDRESS"]["ZIP"],"") ;?>
		</p>
                   <?php  
                        if (is_array($district_info) && !empty($district_info)) 
                            { ?>
                      <div id="hostel_districts" class="hostel_districts">
                        <p>
                        <span class="hostel_districts_district"><?php echo _('Districts');?>:</span>
                        <span class="hostel_districts_values">
                            <?php
                            foreach ($district_info as $key => $district) 
                                { 
//                                die(var_dump(count($district_info), $key));
                                echo $district->district_name;

                                if ( count($district_info) !=  $key+1 ) {
                                    echo ", ";
                                }
                                else{
                                    echo ".";
                                }
                      }//end Foreach  ?> 
                        </span> 
                       </p>
                     </div>            
                   <?php   }// end if ?>
                
                    <?php  
                        if (is_array($landmarks) && !empty($landmarks)) 
                            { ?>
                      <div id="hostel_landmarks" class="hostel_landmarks">
                        <p>
                        <span class="hostel_landmarks_landmark"><?php echo _('Landmarks (within 2km)');?>:</span>
                        <span class="hostel_landmarks_values">
                            <?php
                            foreach ($landmarks as $key => $landmark) 
                                { 
//                                die(var_dump($landmark, count($landmarks), $key, $landmarks));
                                echo $landmark->landmark_name;

                                if ( count($landmarks) !=  $key+1 ) {
                                    echo ", ";
                                }
                                else{
                                    echo ".";
                                }
                      }//end Foreach  ?> 
                        </span> 
                       </p>
                     </div>            
                   <?php   }// end if ?>

		<div class="top_info" id="top_info_short">
			<?php
			$word = 50;
			if (!empty($hostel["LONGDESCRIPTION_translated"]))
			{
				$description_first_lang =  word_limiter(strip_tags($hostel["LONGDESCRIPTION_translated"]), $word);
				echo '<div class="translated"><p>'.$description_first_lang.'</p></div>';
				$description_first =  word_limiter(strip_tags($hostel["LONGDESCRIPTION"]), $word);
				echo '<div class="original" style="display:none;"><p>'.$description_first.'</p></div>';
			}
			else
			{
				$description_first =  word_limiter(strip_tags($hostel["LONGDESCRIPTION"]), $word);
				echo '<div class="original"><p>'.$description_first.'</p></div>';
			}

			?>
		</div>
		<div class="top_info" id="top_info_long" style="display:none;">
		<?php
		if (!empty($hostel["LONGDESCRIPTION_translated"]))
		{
			echo '<div class="translated">'.strip_tags($hostel["LONGDESCRIPTION_translated"], '<p>').'</div>';
			echo '<div class="original" style="display:none;">'.nl2p(var_check(strip_tags($hostel["LONGDESCRIPTION"], '<p>'),""),false,true).'</div>';
		}
		else
		{
			echo nl2p(var_check($hostel["LONGDESCRIPTION"],""),false,true);
		}
		?>
		</div>
		<div style="float:left">
		<p><a id="read_more_hostel" href="#"><?php echo _('Read more…')?> &raquo;</a></p>
		<p><a id="read_less_hostel" style="display:none;" href="#">&laquo; <?php echo _('Close')?></a></p>
		</div>
		<?php if($this->site_lang !="en") { ?>
		<select name="translated_or_not" class="translation-toggle">
			<option value="translate"><?php echo _("Voir la version traduite"); ?></option>
			<option value="original"><?php echo _("Voir l'original"); ?></option>
		</select>
		<?php } ?>
		<br clear="all" />
		<?php $code=$this->wordpress->get_option('aj_lang_code');
			$shortcode = strtolower(substr($code,0,2));
			$code=str_replace('-','_',$code);
			if($code=='' || $shortcode =='en'){$code="en_US";}
		?>
		</div>
		<div class="group hostel-meta">
			<div class="fblike">
					<script src="https://connect.facebook.net/<?php echo $code;?>/all.js#xfbml=1"></script><fb:like data-layout="button_count" show_faces="false"></fb:like>
			</div>

			<div class="amenities no-indent">
			<?php
			if(!empty($main_services))
			{
			  foreach($main_services as $service)
			  {
			    if($service->service_type == 'internet')
			    {
            ?>
            <span class="icon_facility icon_facility_feature69 group"><span><?php echo$service->description; ?></span></span>
            <?php
			    }
			    elseif($service->service_type == 'breakfast')
			    {
            ?>
            <span class="icon_facility icon_facility_extra3 group"><span><?php echo$service->description; ?></span></span>
            <?php
			    }
			    elseif($service->service_type == 'downtown')
			    {
			      ?>
			      <span class="icon_facility icon_landmark group"><span><?php echo $service->description; ?></span></span>
			      <?php
			    }
			    elseif(($service->service_type == 'security_rating') &&
			           ((float)$service->description >= 80))
			    {?>
            <span class="icon_facility icon_safety group"><span><?php echo _("Safety");?></span></span>
            <?php
			    }

			  }
			}
			?>
			</div>

		</div>


		<?php if (isset($hostel["IMAGES"])){ $count = 0;?>
		<div class="thumbnail_list" id="thumbnail_list">
		<?php foreach ($hostel["IMAGES"] as $image):?>
		<a class="openup" rel="<?php echo var_check($hostel["NAME"],"");?>" href="<?php echo $hostel["BIGIMAGES"][$count];?>" alt="<?php echo var_check($hostel["NAME"],"");?>">
		  <img height="45px" data-href="<?php echo $image; ?>" src="<?php echo site_url("images/V2/blank.gif"); ?>" alt="<?php echo $hostel["NAME"]; ?>" />
			<noscript>
				<img height="45px" src="<?php echo $image; ?>" alt="<?php echo $hostel["NAME"]; ?>" />
			</noscript>


		</a>
		<?php $count++; endforeach;?>
		</div>
		<?php }?>
	</div>


	<nav class="hostel_tabs group" id="hostels_tabs">
		<ul class="box_round ui-tabs-nav green_gradient_faded">
			<li class="first"><a class="tab_price" href="#hostel_info_home"><?php echo _("Info & Prix");?></a></li>
			<li><a id="show_full_map" class="tab_direction" href="#hostel_info_direction" onClick="appendBootstrap()"><?php echo _("Cartes et Directions");?></a></li>
			<li class="last"><a id="tab_comment" class="tab_review" href="#hostel_info_reviews"><?php echo _("Commentaires");?></a></li>
		</ul>
			  <?php if(!empty($hostel["RATING"])){
				$rating ='';
				if(($hostel["RATING"]>59) && ($hostel["RATING"]<70) )
				{
					 $rating = _("Good");
	            }
	            elseif(($hostel["RATING"]>69) && ($hostel["RATING"]<80) )
	            {
					$rating = _("Very good");
				}
	            elseif(($hostel["RATING"]>79) && ($hostel["RATING"]<90) )
	            {
				$rating = _("Great");
			    }
				elseif(($hostel["RATING"]>89))
				{
				$rating = _("Fantastic");
			    }
				?>
				<ul class="box_round rating">
				<li class="first last">
				  <span class="" title="<?php echo _("évaluation moyenne");?>">
				  <?php if ($rating != '') { ?>
				  <strong class="txt-mid green"><?php echo $rating;?></strong>
				  <?php } ?>
				  <?php echo _("évaluation moyenne");?>
				  <strong style="color:#333333;"><?php echo $hostel["RATING"];?></strong>
				  </span>
				</li>
				</ul>
				<?php }?>
	</nav>
	<div class="box_content box_round group hostel_info ui-tabs">
		<div id="hostel_info_home" class="hostels_tab_content">
		   <?php
        if(!$this->api_forced)
        {
  			?>
			<div class="content_block">
					<form class="group box_round" id="dispo-form" action="" method="">
							<input type="hidden" id="book-property-number" name="book-property-number" value="<?php echo $hostel["ID"];?>" />
							<input type="hidden" id="book-property-name" name="book-property-name" value="<?php echo $hostel["NAME"];?>" />
							<input type="hidden" id="book-property-cards" name="book-property-cards" value="<?php echo $hostel["CARDTYPES"];?>" />


							<script>
							$(document).ready(

							 function()
							 {
								 jQuery("#book-pick").datepicker({ dateFormat: 'd MM, yy', minDate: 0});
								 var date_cookie = getCookie('date_selected');
								 if(isValidDate(date_cookie))
								 {
										var date_url = date_cookie;
										var date_array = date_cookie.split('-');
										var date_avail 	= new Date(date_array[0],date_array[1]-1,date_array[2]);
										$("#book-pick").datepicker( "setDate" , date_avail );
								 }
								 else
								{
									var date_avail = new Date();
									date_avail.setDate(date_avail.getDate()+10);
									$("#book-pick").datepicker( "setDate" , date_avail );
									date_avail = siteDateString(date_avail);
									var date_url = date_avail;
								}


								 var numnight_cookie = getCookie('numnights_selected');
								 if(numnight_cookie)
								 {
									 document.getElementById('book-night').value = numnight_cookie;
									 var night_url = numnight_cookie;
								 }
								 else
								{
									numnight_avail = 2;
									document.getElementById('book-night').value = numnight_avail;
									var night_url = numnight_avail;
								}


								function getURLParameter(name) {
										return decodeURI(
												(RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
										);
								}
								var currency_value = getURLParameter('currency');
								if(!currency_value || currency_value == "null")
								{
									currency_value = getCookie('currency_selected');
								}
								if(currency_value)
								{
									document.getElementById('book-property-currency').value = currency_value;
								}
								else
								{
									currency_value = 'EUR';
									document.getElementById('book-property-currency').value = currency_value;
								}
								var current_url = $("#back_to_results").attr("href");
								$('#back_to_results').attr("href",current_url+"/"+date_url+"/"+night_url);
								checkAvailability('<?php echo site_url($this->hostel_controller); ?>','<?php echo str_replace("'","\\'",$bc_country);?>','<?php echo str_replace("'","\\'",$bc_city);?>',<?php echo $hostel["ID"];?>,'book-pick',document.getElementById('book-night').value,'<?php echo addslashes($hostel["NAME"]);?>',document.getElementById('book-property-currency').value,'<?php echo _('Date invalide'); ?>','booking-table');
							 }
							);

							</script>
							<ul class="group">
								<?php
								if(!isset($date_selected))
								{
								$date_selected = get_date_default();
								}
								//select_date(_('Arrivée le:'),"book-date-day","book-date-day","book-date-month","book-date-month",$date_selected);
								?>
								<li>
								<label for="book-pick"><?php echo _('Arrivée le:');?></label>
								<input type="text" id="book-pick" name="book-pick" value="<?php echo $date_selected;?>" />
								</li>


								<li>
								<?php
								if(!isset($numnights_selected))
								{
								$numnights_selected = 2;
								}
								$hb_api_used = ($this->api_used == HB_API) ? TRUE : FALSE;
								select_nights(_('Nuits:'),"book-night","book-night",$numnights_selected, $hb_api_used); ?>
								</li>
								<li>
								<label for="book-property-currency"><?php echo _("Devise:");?></label>
								<?php $this->Db_currency->select_currency("book-property-currency","book-property-currency",$currency,"",$this->site_lang); ?>
								</li>
								<li class="last">
									<input onfocus="this.blur()" type="button" name="book-submit" id="book-submit" class="button-green box_round hoverit" value="<?php echo _("Rechercher");?>" OnClick=" $('ul.tabing').tabs('select', 0);checkAvailability('<?php echo site_url($this->hostel_controller); ?>','<?php echo str_replace("'","\\'",$bc_country);?>','<?php echo str_replace("'","\\'",$bc_city);?>',<?php echo $hostel["ID"];?>,'book-pick',document.getElementById('book-night').value,'<?php echo addslashes($hostel["NAME"]);?>',document.getElementById('book-property-currency').value,'<?php echo _('Date invalide'); ?>','booking-table');" />
								</li>


							</ul>


						</form>

						<p id="loading_dispo">
							<?php echo _('Recherche de disponibilités...'); ?>
						</p>
						<div id="booking-table"></div>


					</div>

			    <?php
          }
					?>
					<?php if (!empty($hostel['FEATURES'])){?>
					<div class="content_block">
						<h2 class="margbot10"><?php echo _("Commodité");?></h2>
						<?php if (!empty($hostel['FEATURES_translated'])){?>
						<select class="select-translate">
							<option value="translate"><?php echo _("Voir la version traduite"); ?></option>
							<option value="original"><?php echo _("Voir l'original"); ?></option>
						</select>
						<?php }?>
						<div class="group">
							<?php
							$numloop = count($hostel['FEATURES']);
							$breakloop = round($numloop/3);
							$loopcount = 0;


							$facilities_o = $hostel['FEATURES'];


							if(!empty($hostel['FEATURES_translated']))
							{
								$facilities_t = $hostel['FEATURES_translated'];
								echo '<ul class="float-list green-li increase1 translated">';
								foreach ($facilities_t as $facility){
									if(!empty($facility)){
										if ($loopcount == $breakloop){
											$loopcount = 0;
											echo '</ul><ul class="float-list green-li increase1 translated">';
										}
										$loopcount++;
										echo '<li>'.stripslashes(var_check($facility,"")).'</li>';
									}
								}

								echo '</ul>';
							}
							?>




						<?php if(!empty($facilities_o)){?>
						<ul class="float-list green-li increase1 original"<?php if (!empty($facilities_t)){echo ' style="display:none;"';}?>>
							<?php

							$loopcount = 0;
							foreach ($facilities_o as $facility){
								if(!empty($facility)){
									if ($loopcount == $breakloop){
										$loopcount = 0;
										echo '</ul><ul class="float-list green-li increase1 original"';
										if (!empty($facilities_t)){echo ' style="display:none;"';}
										echo '>';
									}
									$loopcount++;
									echo '<li>'.stripslashes(var_check($facility,"")).'</li>';
								}
							}

							echo '</ul>';
						}?>



						</div>
					</div>
					<?php }?>
					<?php //foreach ($propertyextra as $indice => $valeur)?>
					<?php if (!empty($hostel['PROPERTYEXTRAS_included']) || !empty($hostel['PROPERTYEXTRAS_purchasable']) ){?>

						<?php if (!empty($hostel['PROPERTYEXTRAS_included'])){?>
						<div class="content_block amenities_included">
							<h2 class="margbot10"><?php echo _("What's Included");?></h2>
							<?php if (!empty($hostel['PROPERTYEXTRAS_included_translated'])){?>
							<select class="select-translate">
								<option value="translate"><?php echo _("Voir la version traduite"); ?></option>
								<option value="original"><?php echo _("Voir l'original"); ?></option>
							</select>
							<?php }?>
							<div class="group">
								<?php if (!empty($hostel['PROPERTYEXTRAS_included_translated'])){?>
									<ul class="float-list green-li increase1 original" style="display:none;">
									<?php foreach ($hostel['PROPERTYEXTRAS_included'] as $id => $value){?>
											<li><?php echo $id.': <strong>'._("Free").'</strong>';?></li>
									<?php }?>
									</ul>
									<ul class="float-list green-li increase1 translated">
									<?php foreach ($hostel['PROPERTYEXTRAS_included_translated'] as $id => $value){?>
											<li><?php echo $id.': <strong>'._("Free").'</strong>';?></li>
									<?php }?>
									</ul>
								<?php }else{?>
									<ul class="float-list green-li increase1 original">
									<?php foreach ($hostel['PROPERTYEXTRAS_included'] as $id => $value){?>
											<li><?php echo $id.': <strong>'._("Free").'</strong>';?></li>
									<?php }?>
									</ul>
								<?php }?>
							</div>
						</div>
					<?php }?>

					<?php if (!empty($hostel['PROPERTYEXTRAS_purchasable'])){?>
						<div class="content_block">
							<h2 class="margbot10"><?php echo _("Purchasable Extras");?></h2>
							<?php if (!empty($hostel['PROPERTYEXTRAS_purchasable_translated'])){?>
							<select class="select-translate">
								<option value="translate"><?php echo _("Voir la version traduite"); ?></option>
								<option value="original"><?php echo _("Voir l'original"); ?></option>
							</select>
							<?php }?>
							<div class="group">
								<?php if (!empty($hostel['PROPERTYEXTRAS_purchasable_translated'])){?>
									<ul class="float-list green-li increase1 original" style="display:none;">
									<?php foreach ($hostel['PROPERTYEXTRAS_purchasable'] as $id => $value){?>
											<li><?php echo $id.': <strong>'.$value.' '.currency_symbol($hostel['CURRENCY']['CODE']).'</strong>';?></li>
									<?php }?>
									</ul>
									<ul class="float-list green-li increase1 translated">
									<?php foreach ($hostel['PROPERTYEXTRAS_purchasable_translated'] as $id => $value){?>
											<li><?php echo $id.': <strong>'.$value.' '.currency_symbol($hostel['CURRENCY']['CODE']).'</strong>';?></li>
									<?php }?>
									</ul>
								<?php }else{?>
									<ul class="float-list green-li increase1 original">
									<?php foreach ($hostel['PROPERTYEXTRAS_purchasable'] as $id => $value){?>
											<li><?php echo $id.': <strong>'.$value.' '.currency_symbol($hostel['CURRENCY']['CODE']).'</strong>';?></li>
									<?php }?>
									</ul>
								<?php }?>
							</div>
						</div>
					<?php }?>


					<?php }?>

					<?php if (!empty($hostel['CHECKINTIMES']['CHECKOUT']) || !empty($hostel['CHECKINTIMES']['CHECKIN'])){?>
						<div class="content_block">
							<h2 class="margbot10"><?php echo _("Check-In/Out Details");?></h2>
							<div class="group">
							<?php foreach ($hostel['CHECKINTIMES'] as $indice => $valeur){
								if ($indice == 'CHECKIN'){
									echo '<p>'._("Check-in:").' <strong>'.$valeur.'</strong></p>';
								}

								if ($indice == 'CHECKOUT'){
									echo '<p>'._("Check-out:").' <strong>'.$valeur.'</strong></p>';
								}
							?>

							<?php }?>
							</div>
						</div>
					<?php }?>

					<?php if (!empty($hostel['CANCELLATIONINFORMATION'])){?>

					<div class="content_block">
						<h2><?php echo _("Cancellation Policy");?></h2>
						<?php if (!empty($hostel['CANCELLATIONPOLICY_translated'])){?>
						<select class="select-translate">
							<option value="translate"><?php echo _("Voir la version traduite"); ?></option>
							<option value="original"><?php echo _("Voir l'original"); ?></option>
						</select>
						<?php }?>

						<div class="group">
							<?php if (!empty($hostel['CANCELLATIONPOLICY_translated'])){?>
							<p class="original" style="display:none;"><?php echo $hostel['CANCELLATIONINFORMATION']['CANCELLATIONPOLICY'];?></p>
							<p class="translated"><?php echo strip_tags($hostel['CANCELLATIONPOLICY_translated']);?></p>
							<?php }else{?>
							<p><?php echo $hostel['CANCELLATIONINFORMATION']['CANCELLATIONPOLICY'];?></p>
							<?php }?>
						</div>
					</div>
					<?php }?>
					<?php if (!empty($hostel['IMPORTANTINFORMATION'])){?>
					<div class="content_block">
						<h2><?php echo _("Informations Importantes");?></h2>
						<?php if (!empty($hostel['IMPORTANTINFORMATION_translated'])){?>
						<select class="select-translate">
							<option value="translate"><?php echo _("Voir la version traduite"); ?></option>
							<option value="original"><?php echo _("Voir l'original"); ?></option>
						</select>
						<?php }?>
						<div class="group">
							<?php
							if(!empty($hostel['IMPORTANTINFORMATION_translated']))
							{
								echo '<div class="translated">'.strip_tags($hostel['IMPORTANTINFORMATION_translated'], '<p>').'</div>';
								echo '<div class="original" style="display:none;">'.nl2p(var_check(strip_tags($hostel['IMPORTANTINFORMATION'], '<p>'),""),false,true).'</div>';
							}
							else
							{
								echo nl2p(var_check($hostel['IMPORTANTINFORMATION'],""),false,true);
							}
							?>
						</div>
					</div>
					<?php }?>

				</div>
				<div id="hostel_info_direction" class="hostels_tab_content ui-tabs-hide">
					<?php if (!empty($hostel['ADDRESS'])){?>
					<div class="content_block">
						<h2><?php echo _("Adresse");?></h2>
						<p>
						<?php if (!empty($hostel["ADDRESS"]["STREET1"]))echo var_check($hostel["ADDRESS"]["STREET1"],"");
						if (!empty($hostel["ADDRESS"]["STREET3"]))echo ' '.var_check($hostel["ADDRESS"]["STREET2"],"");
						if (!empty($hostel["ADDRESS"]["STREET3"]))echo ' '.var_check($hostel["ADDRESS"]["STREET3"],"");
						if (!empty($hostel["ADDRESS"]["CITY"]))echo ', '.var_check($hostel["ADDRESS"]["CITY"],"");
						if (!empty($hostel["ADDRESS"]["STATE"]))echo ', '.var_check($hostel["ADDRESS"]["STATE"],"");
						if (!empty($hostel["ADDRESS"]["COUNTRY"]))echo ', '.var_check($hostel["ADDRESS"]["COUNTRY"],"") ;
						if (!empty($hostel["ADDRESS"]["ZIP"]))echo ', '.var_check($hostel["ADDRESS"]["ZIP"],"") ;?>
						</p>
					</div>
					<?php }?>
					<div class="content_block">
						<h2><?php echo _("Cartes");?></h2>
                                                <?php
                                             if (is_array($district_info) && !empty($district_info))
                                                 { ?>
                                                <div id="hostel_mapView_districts" class="hostel_mapView_districts">
                                                    <p>
                                             <?php echo _('Districts');?>:

                                                 <?php
                                                 foreach ($district_info as $key => $district)
                                                     {
                                                      $checked = "";

                                                     if ($key == 0) {
                                                         $checked = "checked";
                                                     }

                                                     ?>
                                                      <input type="radio" id="distrinct" name="distrinct" <?php echo $checked; ?> value="<?php echo $district->um_id; ?>"
                                                  onchange="changeDistrictLayer(<?php echo $district->um_id; ?>);"><?php echo $district->district_name; ?>

                                            <?php  }//end Foreach  ?>
                                       </p>
                                             </div>
                                              <?php   }// end if ?>
						<div id="map-wrap" class="margbot20">
							<div id="map_canvas"></div>
						</div>
					</div>
					<div class="content_block">
						<h2 id="street_panel_title" style="display:none;"><img src="<?php echo base_url();?>images/street-view.png" alt="" /><?php echo _("Street View");?></h2>
						<div id="street_panel-wrap" class="margbot20" style="display:none;">
							<div id="street_panel" style="display:none;"></div>
						</div>
					</div>
					<div class="content_block">
						<h2><?php echo _("Directions");?></h2>
						<?php if (!empty($hostel['DIRECTIONS_translated'])){?>
						<select class="select-translate">
							<option value="translate"><?php echo _("Voir la version traduite"); ?></option>
							<option value="original"><?php echo _("Voir l'original"); ?></option>
						</select>
						<?php }?>
						<?php
						if(!empty($hostel['DIRECTIONS_translated']))
						{
							echo '<div class="translated">'.strip_tags($hostel['DIRECTIONS_translated'], '<p>').'</div>';
							echo '<div class="original" style="display:none;">'.nl2p(var_check(strip_tags($hostel['DIRECTIONS'], '<p>'),""),false,true).'</div>';
						}
						else
						{
							echo nl2p(var_check($hostel['DIRECTIONS'],""),false,true);
						}
							?>
					</div>
				</div>
				<div id="hostel_info_reviews" class="hostels_tab_content ui-tabs-hide">



				<script type="text/javascript">
				function update_review_list()
				{
					$.ajax(
						{
								type:"POST",
								url:"<?php echo site_url();?>property_reviews/<?php echo $hostel["ID"];?>",
								success:function(data)
								{
									//var title = "<h2 class=\"margbot15\"><?php echo _("Commentaires et évaluations de voyageurs");?></h2>";
									$("#remote-comment-list-part").html(data);
								}
						});
				}
				$(document).ready(function(){update_review_list();});
				</script>

				<div id="remote-comment-list-part" class="content_block">
					<p><img src="<?php echo base_url();?>images/V2/loading-squares.gif" alt="" /></p>
				</div>

		</div>
	</div>
</div>
<?php endif; //endif api error?>
<script type="text/javascript">
	$("select[name=translated_or_not]").change(function () {
		if($(this).find('option:selected').val() == "original") {
			$("#top_info_short .translated, #top_info_long .translated").hide();
			$("#top_info_short .original, #top_info_long .original").show();
		} else {
			$("#top_info_short .original, #top_info_long .original").hide();
			$("#top_info_short .translated, #top_info_long .translated").show();
		}
	});
</script>
<?php
/* Check the fourth param in URL */
if ($this->uri->segment(4, 0)) {
    /* Convert to lower so make it case in sensitive*/
    $uri_segement = strtolower($this->uri->segment(4));
    if ($uri_segement == "map") {
        // make the diection tab selected and triger the click event
        echo "<script type='text/javascript'>$(document).ready(function() { $('#hostels_tabs').tabs('select',1); $('#show_full_map').trigger('click'); });</script>";
    } else if ($uri_segement == 'comments') { // make the coments tab selected
        echo "<script type='text/javascript'>$(document).ready(function() { $('#hostels_tabs').tabs('select',2); });</script>";
    } else {
        // do nothing
    }
}
?>
