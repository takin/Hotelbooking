<?php /*

Template Name: Page Ville Destination

*/?>
<?php get_header(); ?>

<div id="sidebar" class="grid_4">
	<?php
		  $GLOBALS['current_id']=$post->ID;
			$taxterm = get_the_title();
			$children = wp_list_pages('&child_of='. $GLOBALS['current_id'] .'&echo=0&title_li=&depth=1');
		  if($children) {?>
		 <div class="box_content box_round group">
			<h2 class="border_title"><?php _e('Plus sur','auberge');?> <?php echo $taxterm;?></h2>
			<ul class="submenu">
			<li class="current_page_item"><a href="<?php echo $firstpostlink;?>"><?php _e('Informations générales sur','auberge');?> <?php echo $taxterm;?></a></li>
			<?php echo $children; ?>
			</ul>
		 </div>
		 <?php }?>
		 <?php include (TEMPLATEPATH . '/side_search_box.php');?>
</div>

<div id="main" class="grid_12 destination">

	<div class="box_content box_round group">

	<?php if(have_posts()): while (have_posts()) : the_post();  $firstpostlink=get_permalink();?>

        <?php // get all custom field
			$custom_fields = get_post_custom();
			$city_url = $custom_fields['City_url'][0];

		?>

        <h1 class="border_title"><?php _e('Auberges de Jeunesse à','auberge');?> <?php the_title();?></h1>

        <div class="entry">
					<?php the_content();?>
        </div>

        <?php endwhile; ?>
        <?php else:?>

        <?php endif; ?>
    </div>
		<?php $posts = get_posts('exclude='.$firstpost.'&orderby=rand&numberposts=-1&'.__('ville','auberge').'='.$taxterm);?>

		<?php if (!empty($posts)){?>

			<h1 class="title_outside"><?php _e('Nos suggestions pour','auberge');?> <?php echo $taxterm;?></h1>

    <?php
		foreach($posts as $post) :
		//if ($destination->have_posts()): while ($destination->have_posts()) : $destination->the_post();
		$counter++; $loopcounter++;?>
		<div class="box_content box_round group list_box">
			<div id="post-<?php echo $post->ID; ?>">
				<div class="entry group">
				<span class="article-cat"><?php
					$count = count(get_the_category());
					$counter2 = 0;
					foreach((get_the_category()) as $category) {
						$counter2++;
						if($counter2 == $count){
							echo $category->cat_name;
						}else{
							echo $category->cat_name . ', ';
						}
					}
				 ?></span>

				<?php if(!get_thumb_url($post->ID)==''){$no_margin=0;?>
				<a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img class="alignleft" width="130" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/t.php?zc=1&amp;w=130&amp;h=85&amp;src='.get_thumb_url($post->ID);?>" /></a>
					<?php }else{$no_margin=1;}?>
					<h2<?php if($no_margin==1){echo ' style="margin-left:0px;"';}?>><a href="<?php the_permalink(); ?>" title="<?php _e('Lire la suite de','auberge');?> &quot;<?php the_title();?>&quot;"><?php the_title();?></a></h2>

          <p<?php if($no_margin==1){echo ' style="margin-left:0px;"';}?>><?php print string_limit_words(strip_tags($post->post_content), 40); ?>... </p>
          <p<?php if($no_margin==1){echo ' style="margin-left:0px;"';}?>><a href="<?php the_permalink();?>"><?php _e('Lire la suite','auberge');?> &raquo;</a></p>

				</div>
			</div>
    </div>
    <?php  endforeach; ?>

		<?php }?>
	<?php //endwhile; endif; ?>

</div>


<?php get_footer(); ?>