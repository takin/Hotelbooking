<?php get_header(); ?>
<?php get_sidebar(); ?>
<div id="main" class="grid_12 destination">

	<div class="box_content box_round group">

	<?php if(have_posts()): while (have_posts()) : the_post(); $taxterm = get_the_title(); $firstpost=$post-ID;?>

        <h2 class="green-bar"><?php _e('Tout ce que vous devez savoir sur','auberge');?> <?php the_title();?></h2>

		<div class="entry">
			<?php the_content();?>
        </div>

        <?php endwhile; ?>
        <?php else:?>

        <?php endif; ?>


    <h2 class="green-bar"><?php _e('Quoi faire à','auberge');?> <?php echo $taxterm;?></h2>

    </div>

     <?php


		$posts = get_posts('exclude='.$firstpost.'&numberposts=-1&ville='.$taxterm);
		//$destination = new WP_Query('showposts=-1&cat=-5&ville='.$taxterm);
		$counter=0;
		foreach($posts as $post) :
		//if ($destination->have_posts()): while ($destination->have_posts()) : $destination->the_post();
		$do_not_duplicate = $post->ID;  $counter++;?>

	   <?php // echo strip_tags(get_the_term_list($destination->post->ID, 'ville', '', '')); ?>
		<div id="post-<?php the_ID(); ?>" class="col2 more-info <?php if($counter == 1){?>first<?php }?>">
			 <div class="entry">
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
			   <h1><a href="<?php the_permalink(); ?>" title="<?php _e('Lire la suite de','auberge');?> &quot;<?php the_title();?>&quot;"><?php the_title();?></a></h1>
				 <?php if(!get_thumb_url($post->ID)==''){?>
                      <a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img class="alignleft" width="85" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/t.php?zc=1&amp;w=85&amp;h=85&amp;src='.get_thumb_url($post->ID);?>" /></a>
                   <?php }else{?>
                      <a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img class="alignleft" width="85" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/t.php?zc=1&amp;w=85&amp;h=85&amp;src='.get_bloginfo('template_url').'/images/na-small.jpg';?>" /></a>
                   <?php }?>

				 <p><?php print string_limit_words(get_the_excerpt(), 40); ?>... </p>
                 <a href="<?php the_permalink();?>"><?php _e('Lire la suite','auberge');?> &raquo;</a>

			  </div>
		</div>
        <?php if($counter == 2){$counter = 0;?><div style="height:5px; clear:both; margin:0 20px 12px 20px;" class="dotted-line"></div><?php }?>
    <?php  endforeach; ?>
	<?php //endwhile; endif; ?>

</div>

<?php get_footer(); ?>