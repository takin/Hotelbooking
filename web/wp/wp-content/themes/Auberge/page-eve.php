<?php
/*
Template Name: Événements
*/
?><?php get_header(); ?>

<?php get_sidebar(); ?>

<div id="main" class="grid_12">
    
	  <?php $temp = $wp_query;
	  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	  $temp = $wp_query;
	  $wp_query= null;
	  $wp_query = new WP_Query();		
	  $wp_query->query("category_name=evenements&paged=$paged");
	  
	  if($wp_query->have_posts()):?>
      
	  <h1 class="title_outside"><?php _e('Les événements à surveiller','auberge');?></h1>
      
	  <?php while ($wp_query->have_posts()) : $wp_query->the_post(); $custom_fields = get_post_custom();
	  $ville = strip_tags(get_the_term_list($post->ID, __('ville','auberge'), '', ''));?>              
		<div class="box_content box_round group list_box">      
			<div id="post-<?php the_ID(); ?>">
						
			<?php if(!get_thumb_url($post->ID)==''){?>
			<a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="130" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=130&amp;h=85&amp;src='.get_thumb_url($post->ID);?>" /></a>
		 <?php }else{?>
			<a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="130" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=130&amp;h=85&amp;src='.get_bloginfo('template_url').'/images/na-small.jpg';?>" /></a>
		 <?php }?>
				
			<h2><a href="<?php the_permalink() ?>"><?php the_title();?></a></h2>
			<p class="post-meta dotted-line"><?php if(isset($custom_fields['date'])){?><?php _e('Date :','auberge');?> <strong><?php echo $custom_fields['date'][0];?></strong> |<?php }?> 
			<?php _e('Endroit :','auberge');?> <a href="<?php bloginfo('url');?><?php _e('/destinations/','auberge');?><?php echo strtolower($ville);?>"><?php echo $ville; ?></a></p>
			
			<p><?php print string_limit_words(get_the_excerpt(), 25); ?> ...</p>
			<p><a class="read_more" href="<?php the_permalink() ?>"><?php _e('Lire la suite','auberge');?> &raquo;</a></p>
			
			</div>		
		  
		</div>
		
		<?php endwhile; ?>
								 
		<?php include (TEMPLATEPATH . '/pagination.php');
		$wp_query = null; $wp_query = $temp;
		?>
		
		<?php endif; ?>	

  
</div>

<?php get_footer(); ?>