<div class="hostel-list-item box-shadow-wrapper group">
	<a class="full-link" href="<?php echo $this->Db_links->build_property_page_link($property->propertyType,$property->propertyName,$property->propertyNumber,$this->site_lang); ?>">
		<div class="group">
		<img alt="" width="54" height="53" src="<?php if (isset($property->PropertyImages->PropertyImage->imageURL)){echo $property->PropertyImages->PropertyImage->imageURL;}else{ echo site_url('images/na_small.jpg');} ?>">
		<div class="info">
			<h2><?php echo $property->propertyName; ?></h2>
			<p class="type"><?php echo $this->Db_term_translate->get_term_translation($property->propertyType,$this->site_lang); ?></p>
      <?php
      //Property price
      if(isset($property->BedPrices->BedPrice->price))
      {
        ?>
        <p class="price"><span><?php echo _('à partir de');?></span> <?php echo currency_symbol($property->BedPrices->BedPrice->currency);?> <strong><?php echo $property->BedPrices->BedPrice->price;?></strong></p>
        <?php
      }?>
     </div>
		<div class="rating">
      <?php //Property review rating
			if(empty($property->overallHWRating))
      {
        ?>
        <p class="rating"><span><?php echo _("Not Rated Yet!"); ?></span><strong class="no-rating">0</strong></p>
        <?php
      }
      else
      {
        ?>
        <p class="rating"><strong title="<?php echo _("As rated by bookers like you"); ?>"><?php echo $property->overallHWRating;?></strong> <span>%</span></p>
        <?php
      }
      ?>
		</div>
		</div>

	</a>
  <?php
  if(!empty($property->Geo->Longitude) && !empty($property->Geo->Latitude))
  {
    ?>
	  <a class="view-map" href="<?php echo $this->Db_links->build_property_page_link($property->propertyType,$property->propertyName,$property->propertyNumber,$this->site_lang); ?>"><?php echo strtolower(_("Réserver"));?></a>
    <?php
  }
  ?>
</div>