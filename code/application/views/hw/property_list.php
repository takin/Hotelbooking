<?php
// Link to hostel page below if needed
// echo $this->Db_links->build_property_page_link($hostel->propertyType,$hostel->propertyName,$hostel->propertyNumber[0],$this->site_lang); ?>
<div class="hostel_list search_list">
	<div class="box_content box_round">
		<div class="city_hostel group" id="city_info_<?php echo $hostel->propertyNumber; ?>">
			<div class="info">
				<div class="left info_pic">
				<!--<a href="<?php echo $this->Db_links->build_property_page_link($hostel->propertyType,$hostel->propertyName,$hostel->propertyNumber[0],$this->site_lang);?>">-->
				<img alt="" src="<?php echo base_url().'info/wp-content/themes/Auberge/scripts/t.php?zc=1&amp;w=100&h=100&src='.str_replace("mini_",'',$hostel->PropertyImages->PropertyImage->imageURL); ?>" />
				<!--</a>-->
				</div>
				<div class="info_indent">
					<!--<h2><a href="<?php echo $this->Db_links->build_property_page_link($hostel->propertyType,$hostel->propertyName,$hostel->propertyNumber[0],$this->site_lang);?>"><?php echo $hostel->propertyName[0]; ?>, <?php echo $city_selected;?></a> <span class="info_type">(<?php echo $this->Db_term_translate->get_term_translation($hostel->propertyType,$this->site_lang); ?>)</span></h2>-->
					<h2><?php echo $hostel->propertyName[0]; ?>, <?php echo $city_selected;?> <span class="info_type">(<?php echo $this->Db_term_translate->get_term_translation($hostel->propertyType,$this->site_lang); ?>)</span></h2>
					<p>
						<?php
						if (isset($searchmode) && $searchmode == 1){$word = 20;}else{$word = 30;}
						if(!empty($hostel->shortDescriptionTranslated))
						{
						    $short_de=domain_name_replace($hostel->shortDescriptionTranslated);
							echo strip_tags(word_limiter($short_de, $word));
						}
						else
						{
						    $short_de=domain_name_replace($hostel->shortDescription);
							echo strip_tags(word_limiter($short_de, $word));
						}
						?>
					</p>
					<?php /*?><ul class="amenities-list group">
						<li class="icon-coffee">Déjeuner inclus</li>
						<li class="icon-mouse">Internet Gratuit</li>
					</ul><?php */?>
				</div>
				<?php
				if(!empty($main_services[(int)$hostel->propertyNumber]))
				{
				  ?>
				  <div class="amenities group">
				  <?php
				  foreach($main_services[(int)$hostel->propertyNumber] as $service)
				  {
				    if($service->service_type == 'facility')
				    {
              ?>
              <span class="icon_facility icon_facility_<?php echo $service->service_id; ?> group"><span><?php echo$service->description; ?></span></span>
              <?php
				    }
				    else
				    {
				      ?>
				      <span class="icon_facility icon_landmark group"><span><?php echo $service->description; ?></span></span>
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
				<?php if(isset($hostel->BedPrices->BedPrice->price)){?>
				<div class="price group">
					<span><?php echo _('à partir de');?></span>
					&nbsp;<?php echo $cur = currency_symbol($hostel->BedPrices->BedPrice->currency);?> <strong><?php echo $hostel->BedPrices->BedPrice->price;?></strong>
				</div>
				<?php }?>
				<?php if(!empty($user_reviews)&&($user_reviews[(int)$hostel->propertyNumber]['our_review_count'] > 0)){
				$count_review = $user_reviews[(int)$hostel->propertyNumber]['our_review_count'];?>
				<p class="comment"><a href="#" rel="<?php echo $hostel->propertyNumber; ?>" class="review_static"><?php echo _('Latest Reviews')?></a></p>
				<?php }?>
			</div>
		</div>

		<?php if(!empty($user_reviews)&&($user_reviews[(int)$hostel->propertyNumber]['our_review_count'] > 0)){?>
		<div class="review_wrap" id="review_wrap_<?php echo $hostel->propertyNumber; ?>">
			<a href="#" rel="<?php echo $hostel->propertyNumber; ?>" class="review_wrap_close">[<?php echo _('close'); ?>]</a>
			<?php foreach($user_reviews[(int)$hostel->propertyNumber]['user_reviews'] as $user_review){?>
      <div class="review_city_block">
				<div class="review_content">
				<?php if (!empty($user_review['review_translated'])){?>
				<?php echo nl2p(var_check($user_review['review_translated'],""),false,true);?>
				<?php }else{?>
				<?php echo nl2p(var_check($user_review['review'],""),false,true);?>
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
