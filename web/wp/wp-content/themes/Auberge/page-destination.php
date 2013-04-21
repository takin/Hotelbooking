<?php
/*
Template Name: Destination
*/
?><?php get_header(); ?>
<?php get_sidebar(); ?>
<div id="main" class="grid_12">

		 <h1 class="title_outside"><?php _e('Destinations - Les villes les plus populaires','auberge');?></h1>
		 <?php if(have_posts()): while (have_posts()) : the_post(); $parentID = $post->ID;?>
				 <?php if (get_the_content()!=''){?>
				 <div class="entry">
					<?php the_content();?>
				 </div>
		 <?php }endwhile;endif; ?>


    	<?php

			$posts = get_posts('&orderby=name&numberposts=-1&post_type=page&post_parent='.$parentID);
			$counter=0;
			foreach($posts as $post) :
			$counter++;?>
      <div class="box_content box_round group list_box">
				<div id="post-<?php echo $post->ID; ?>" class="entry">
				<?php if(!get_thumb_url($post->ID)==''){?>
				<a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="130" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/t.php?zc=1&amp;w=130&amp;h=85&amp;src='.get_thumb_url($post->ID);?>" /></a>
				<?php }else{?>
				<a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="130" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/t.php?zc=1&amp;w=130&amp;h=85&amp;src='.get_bloginfo('template_url').'/images/na-small.jpg';?>" /></a>
				<?php }?>

				<h2><a href="<?php the_permalink() ?>"><?php the_title();?></a></h2>

				<?php if (get_option('aj_guide_excerpt') == true){?>
				<p><?php echo string_limit_words_content($post->post_excerpt, 35); ?></p>
				<?php }else{?>
				<p><?php echo string_limit_words_content($post->post_content, 35); ?></p>
				<?php }?>
				<p><a class="read_more" href="<?php the_permalink() ?>"><?php _e('Voir le Guide','auberge');?> &raquo;</a></p>
				</div>

			</div>
		<?php endforeach; ?>

</div>


<?php get_footer(); ?>