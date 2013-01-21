<?php 

if (get_option('aj_api_ascii')==""){$csspath = get_option('aj_api_name');}else{$csspath = get_option('aj_api_ascii');}
$apiurl = get_option('aj_api_url');
?>
<div class="box_content box_round group">

		<?php /*?><img alt="<?php echo get_option('aj_api_name')?>" src="<?php echo get_option('aj_api_url').'images/'.$csspath.'/offres-auberges.jpg'; ?>"><?php */?>
		
		<div class="top_cities_side">
		<img  class="box_round" src="<?php echo $apiurl;?>/images/V2/offers1.jpg" alt="<?php _e('Best Offers','auberge'); ?>"/>
		<h2><?php _e('Best Offers','auberge'); ?></h2>
		<small><?php _e('Prix par nuit par personne, à partir de :','auberge'); ?></small>
			<h3><?php _e('Europe','auberge'); ?></h3>
			<ul>
				<?php 
				$currency_symbol = currency_symbol(get_selected_currency());
				$topcities = get_top_cities('europe');
				foreach($topcities as $topcity)
				{
				  if(!empty($topcity->converted_city_min_price) && ($topcity->converted_city_min_price > 0))
          {
				  ?>
          <li><strong><a href="<?php echo get_option('aj_api_url').urlencode($topcity->translated_country)."/".urlencode($topcity->translated_city); ?>"><?php echo $topcity->translated_city; ?></a></strong> <em><?php echo $currency_symbol." ".$topcity->converted_city_min_price;?></em></li>  
          <?php 
          }
				}
				?>
			</ul>
			<h3><?php _e('Amérique du Nord','auberge'); ?></h3>
			<ul>
				<?php 
        $topcities = get_top_cities('north america');
        foreach($topcities as $topcity)
        {
          if(!empty($topcity->converted_city_min_price) && ($topcity->converted_city_min_price > 0))
          {
          ?>
          <li><strong><a href="<?php echo get_option('aj_api_url').urlencode($topcity->translated_country)."/".urlencode($topcity->translated_city); ?>"><?php echo $topcity->translated_city; ?></a></strong> <em><?php echo $currency_symbol." ".$topcity->converted_city_min_price;?></em></li>  
          <?php 
          }
        }
        ?>
			</ul>
			
			<h3><?php _e('Amérique du Sud','auberge'); ?></h3>
			<ul>
        <?php 
        $topcities = get_top_cities('south america');
        foreach($topcities as $topcity)
        {
          if(!empty($topcity->converted_city_min_price) && ($topcity->converted_city_min_price > 0))
          {
          ?>
          <li><strong><a href="<?php echo get_option('aj_api_url').urlencode($topcity->translated_country)."/".urlencode($topcity->translated_city); ?>"><?php echo $topcity->translated_city; ?></a></strong> <em><?php echo $currency_symbol." ".$topcity->converted_city_min_price;?></em></li>  
          <?php 
          }
        }
        ?>
			</ul>
			
			<h3><?php _e('Asie','auberge'); ?></h3>
			<ul>
        <?php 
        $topcities = get_top_cities('asia');
        foreach($topcities as $topcity)
        {
          if(!empty($topcity->converted_city_min_price) && ($topcity->converted_city_min_price > 0))
          {
          ?>
          <li><strong><a href="<?php echo get_option('aj_api_url').urlencode($topcity->translated_country)."/".urlencode($topcity->translated_city); ?>"><?php echo $topcity->translated_city; ?></a></strong> <em><?php echo $currency_symbol." ".$topcity->converted_city_min_price;?></em></li>  
          <?php 
          }
        }
        ?>
			</ul>		
      
			<h3><?php _e('Océanie','auberge'); ?></h3>
			<ul>
        <?php 
        $topcities = get_top_cities('Oceania');
        foreach($topcities as $topcity)
        {
          if(!empty($topcity->converted_city_min_price) && ($topcity->converted_city_min_price > 0))
          {
          ?>
          <li><strong><a href="<?php echo get_option('aj_api_url').urlencode($topcity->translated_country)."/".urlencode($topcity->translated_city); ?>"><?php echo $topcity->translated_city; ?></a></strong> <em><?php echo $currency_symbol." ".$topcity->converted_city_min_price;?></em></li>  
          <?php 
          }
        }
        ?>
			</ul>		
    
		</div>
</div>
