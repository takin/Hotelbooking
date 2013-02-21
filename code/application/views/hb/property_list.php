<?php
// Link to hostel page below if needed
// echo $this->Db_links->build_property_page_link($hostel->propertyType,$hostel->propertyName,$hostel->propertyNumber[0],$this->site_lang); ?>
<div class="hostel_list search_list">
	<div class="box_content box_round">
		<div class="city_hostel group" id="city_info_<?php echo $hostel["id"]; ?>">
			<div class="info">
				<div class="left info_pic">
				<?php
				$hb_list_image = site_url('images/na_small.jpg');
				if(isset($hostel["image"]))
				{
				  $hb_list_image = $this->Hostelbookers_api->build_list_url($hostel["image"]);
				}
				?>
				<a href="<?php echo $this->Db_links->build_property_page_link($hostel["type"],$hostel["name"],$hostel["id"],$this->site_lang); ?>">
				<img width="inherit" height="inherit" src="<?php echo $hb_list_image; ?>"  title="" style="width: 100px; height: 100px;">
				</a>
				</div>
				<div class="info_indent">
					<h2><a href="<?php echo $this->Db_links->build_property_page_link($hostel["type"],$hostel["name"],$hostel["id"],$this->site_lang); ?>"><?php echo $hostel["name"]; ?>, <?php echo $city_selected;?></a>	 <span class="info_type">(<?php echo $this->Db_term_translate->get_term_translation($hostel["type"],$this->site_lang); ?>)</span></h2>
				<p>
				<?php
				//TONOTICE Unfortunately, HB API does not use the same variables names for a get location data VS a get location availability

				//Short description mapping
				$hb_description = "";
				if(!empty($hostel["intro"]))
				{
					$hb_description = htmlspecialchars(strip_tags(word_limiter($hostel["intro"], 40, "...")));
				}
				elseif(!empty($hostel["shortdescription"]))
				{
					$hb_description = htmlspecialchars(strip_tags(word_limiter($hostel["shortdescription"], 40, "...")));
				}

				//Minimum price mapping
				$hb_price = NULL;
				if(!empty($hostel["prices"]["customer"]["minprice"]))
				{
					$hb_price = $hostel["prices"]["customer"]["minprice"];
				}

				//Rating mapping
				$hostel_rating = NULL;
				if(!empty($hostel["rating"]))
				{
					$hostel_rating = $hostel["rating"];
				}
				elseif(!empty($hostel["percentagerating"]))
				{
					$hostel_rating = ceil($hostel["percentagerating"]);
				}

				//Short description display
				if(!empty($hostel["shortdescriptionTranslated"]))
				{
					echo '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$hb_description.'">'.htmlspecialchars(strip_tags(word_limiter($hostel["shortdescriptionTranslated"], 40, "..."))).'</span>';
				}
				elseif(!empty($hostel["introTranslated"]))
				{
					echo '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$hb_description.'">'.htmlspecialchars(strip_tags(word_limiter($hostel["introTranslated"], 40, "..."))).'</span>';
				}
				else
				{
					echo $hb_description;
				}
				?>

				</p>
				</div>
				<?php
				if(!empty($main_services[(int)$hostel["id"]]))
				{
				  ?>
				  <div class="amenities group">
				  <?php
				  foreach($main_services[(int)$hostel["id"]] as $service)
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
				  ?>
				  </div>
				  <?php
				}
				?>
			</div>

			<div class="rating">
			<?php
			if(isset($hostel["prices"]["customer"]))
			{
				?>
				<div class="price group">
				<span><?php echo _('à partir de');?></span> &nbsp;<?php echo $cur = currency_symbol($hostel["prices"]["customer"]["currency"]);?> <strong><?php echo $hb_price;?></strong></div>
				<?php
			}
			?>
			<?php
				if(!empty($user_reviews)&&($user_reviews[(int)$hostel["id"]]['our_review_count'] > 0)){
					$count_review = $user_reviews[(int)$hostel["id"]]['our_review_count'];?>
					<p class="comment"><a href="#" rel="<?php echo $hostel["id"]; ?>" class="review_static"><?php echo _('Latest Reviews')?></a></p>
				<?php }?>
			</div>
		</div>

		<?php if(!empty($user_reviews)&&($user_reviews[(int)$hostel["id"]]['our_review_count'] > 0))
		{?>
		<div class="review_wrap" id="review_wrap_<?php echo $hostel["id"]; ?>">
			<a href="#" rel="<?php echo $hostel["id"]; ?>" class="review_wrap_close">[<?php echo _('close'); ?>]</a>

			<?php foreach($user_reviews[(int)$hostel["id"]]['user_reviews'] as $user_review){?>
      <div class="review_city_block">
				<div class="review_content">
				<?php if (!empty($user_review['review_likebest_translated'])){?>
				<?php echo nl2p(var_check($user_review['review_likebest_translated'],""),false,true);?>
				<?php }else{?>
				<?php echo nl2p(var_check($user_review['review_likebest'],""),false,true);?>
				<?php }?>
				</div>
				<span class="review_author"><?php echo $user_review['author_name'];?></span>, <span class="review_date"><?php echo date_conv($user_review['review_date'], $this->wordpress->get_option('aj_date_format'));?></span>

				<?php if ($user_review['review_rating'] != ''){?>
				- <span class="review_rating"><?php echo _("Cote");?>: <strong><?php echo $user_review['review_rating'];?>%</strong></span>
				<?php }?>
				<?php //debug_dump($user_review);?>
			</div>

  		<?php }?>
		</div>
		<?php }?>
	</div>
</div>