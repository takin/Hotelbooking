<div class="thumbnail_list" id="thumbnail_list_<?php echo $property_number;?>">
<?php
foreach ($thumbnails as $i => $image)
{
  ?>
  <a class="openup" rel="<?php echo $property_number;?>" href="<?php echo $main_pics[$i];?>">

    <img height="45px" data-href="<?php echo $image; ?>" src="<?php echo site_url("images/V2/blank.gif"); ?>" alt="" />
  	<noscript>
  		<img height="45px" src="<?php echo $image; ?>" alt="" />
  	</noscript>

  </a>
  <?php
}
?>
</div>
<?php /*?><div class="box_round image_container">
	<div class="slideshow" id="slideshow_<?php echo $property_number;?>">
			<div class="slides">
				<div class="main-pic" id="main-pic-<?php echo $property_number;?>">
					<?php foreach ($main_pics as $image):?>
					<a class="openup" rel="<?php echo $property_name; ?>" href="<?php echo var_check($image,"/test.jpg"); ?>" alt="<?php echo $property_name; ?>">
					
					<img class="main" width="210" data-href="<?php echo $image; ?>" src="<?php echo site_url("images/V2/blank.gif"); ?>" alt="<?php echo $property_name; ?>" />
					<img class="main" width="210" src="<?php echo $image; ?>" alt="<?php echo $property_name; ?>" /></a>
					<?php endforeach;?>
				</div>
				
		</div>
	</div>
</div><?php */?>
