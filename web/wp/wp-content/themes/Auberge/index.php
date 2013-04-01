<?php get_header(); ?>
<?php get_sidebar(); ?>

<div id="main" class="grid_12">
	<div class="group">
		<?php include (TEMPLATEPATH . '/search_box.php');?>
		<?php /*?><div class="home_title">
			<?php // include 'widget/map-search.php';?>
			<div class="box_content box_round">
			<img src="<?php echo $apiurl; ?>/images/V2/home_bed.jpg" alt="<?php echo get_option('aj_api_name');?>">
			<h1><?php echo get_option('aj_api_name');?><span><?php _e("Auberges de jeunesse, Hôtels, Appartements, Chambres d'hôtes, Bed and Breakfast, Pensions - Plus de 30000!!",'auberge');?></span></h1>
			</div>
		</div><?php */?>
	</div>
	<div class="group top_hostels">
		<h1 class="border_title"><?php _e('Auberges et logements dernière minute pas chers les plus populaires','auberge');?></h1>

			  <?php
			  $counter = 0;
			  $topauberges = get_top_hostels();

			  if(!empty($topauberges))
			  {

			    foreach($topauberges as $property)
			    {
			      $property_url = build_property_page_link($property->property_type, $property->property_name, $property->property_number);
			      $property_text = $property->hostel_desc_en;
			      if(!empty($property->translated_desc))
			      {
			        $property_text = $property->translated_desc;
			      }
			      $display_country = $property->property_country;
			      $display_city    = $property->property_city;

			      if(!empty($property->translated_country))
			      {
			        $display_country = $property->translated_country;
			      }
			      if(!empty($property->translated_city))
			      {
			        $display_city = $property->translated_city;
			      }
			      $hostel_image = get_option('aj_api_url').'/images/na_small.jpg';
			      if(!empty($property->image_url))
			      {
			        if (substr($property->image_url, 0, 4 ) === "http")
			        {
			          $hostel_image = $property->image_url;
			        }
			        else
			        {
			          $hostel_image = "http://assets.hb-assets.com".$property->image_url;
			        }
			      }
			      $counter++;
			      ?>
						<?php if($counter==1){echo '<div class="post_column">';}elseif($counter==4){echo '<div class="post_column last">';}?>
            <div id="post_<?php echo $property->property_number; ?>" class="home_list box_content box_round<?php if($counter==3 || $counter==6){echo ' last';}?>">
              <div class="group">
                  <a class="thumb_link" title="<?php echo $property->property_name;?>" href="<?php echo $property_url;?>"><img width="61" height="59" alt="<?php echo $property->property_name;?>" src="<?php echo $hostel_image;?>" /></a>
										<h2>

											<a href="<?php echo $property_url;?>" title="<?php _e('Plus de détails sur','auberge');?> &quot;<?php echo $property->property_name;?>&quot;"><?php echo $property->property_name;?></a>
										</h2>

                  	<p><?php print string_limit_char(strip_tags($property_text), 175, false); ?> </p>

                  	<p class="bottom_price"><span><?php _e('À partir de :','auberge');?> <strong><?php echo currency_symbol($property->converted_currency). " ".$property->converted_price;?></strong></span></p>
										<a class="city_uplink button-green hoverit box_round" title="<?php _e('Toutes les auberges à','auberge');?> <?php echo $display_city;?>" href="<?php echo get_option('aj_api_url').$display_country."/".$display_city;?>"><?php echo $display_city;?> &raquo;</a>
                </div>
            </div>
            <?php
			      if($counter==3 || $counter==6){echo '</div>';}
			    }
			  }?>
        </div>
			</div>

			<h1 class="border_title top_cities"><?php _e('Destinations - Les villes les plus populaires','auberge');?></h1>

			<div class="box_content box_round group">
				<div class="top_cities">

				<div class="group">
				<?php

          $posts = get_posts('&orderby=name&numberposts=6&post_type=page&post_parent=42');
          $counter=0;
          foreach($posts as $post) :
          $counter++;
        ?>

        <div id="post_<?php echo $post->ID; ?>" class="post_home col2 <?php if($counter == 1){?>first<?php }?>">
					 <?php if(!get_thumb_url($post->ID)==''){?>
						<a class="city_preview" title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/t.php?zc=1&amp;w=378&amp;h=120&amp;src='.get_thumb_url($post->ID);?>" />
						<h2><?php the_title();?></h2>
						</a>
					 <?php }else{?>
						<a class="city_preview" title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/t.php?zc=1&amp;w=378&amp;h=120&amp;src='.get_bloginfo('template_url').'/images/na.jpg';?>" />
						<h2><?php the_title();?></h2>
						</a>
					 <?php }?>
					 <?php if (get_option('aj_guide_excerpt') == true){?>
					 <p><?php echo string_limit_words_content($post->post_excerpt, 30); ?>...<br /><a href="<?php the_permalink();?>"><?php _e('Lire la suite','auberge');?> &raquo;</a></p>
					 <?php }else{?>
					 <p><?php echo string_limit_words_content($post->post_content, 30); ?>...<br /><a href="<?php the_permalink();?>"><?php _e('Lire la suite','auberge');?> &raquo;</a></p>
					 <?php }?>

        </div>
        <?php if($counter == 2){?>
				</div>
				<div class="group">
				<?php $counter = 0;}?>
        <?php endforeach; ?>
				</div>
      	</div>

     </div>

  </div>

<?php get_footer(); ?>
