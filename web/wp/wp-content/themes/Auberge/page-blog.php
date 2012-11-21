<?php
/*
Template Name: Blog
*/
?><?php get_header(); ?>
<?php get_sidebar(); ?>
	<div id="main" class="grid_12">
		<div class="box_content box_round group">
		<h1 class="green-bar"><?php _e('Derniers articles','auberge');?></h1>
		
		<?php 
		
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$temp = $wp_query;
		$wp_query= null;
		$wp_query = new WP_Query();		
		$wp_query->query("category_name=blog&paged=$paged");
		
		if($wp_query->have_posts()):?>
		<?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
		
		<div id="post-<?php the_ID(); ?>" class="post-block">
				<div class="post-block-top"></div>
				<div class="post-block-content clearfix">
						
						<div class="post-image">
							 
		<?php if(!get_thumb_url($post->ID)==''){?>
								<a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="192" height="152" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=192&amp;h=152&amp;src='.get_thumb_url($post->ID);?>" /></a>
							 <?php }else{?>
								<a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="192" height="152" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=192&amp;h=152&amp;src='.get_bloginfo('template_url').'/images/na.jpg';?>" /></a>
							 <?php }?>
		
								<p class="comment-tag">
										<?php comments_popup_link(__('Laisser une réponse','auberge'), __('1 Commentaire','auberge'), __('% Commentaires','auberge')); ?>
								</p>
								<p class="tags">
										<?php the_tags('', ', ', ''); ?>
								</p>
								
						</div>
						
						<div class="entry">
								<h2><a href="<?php the_permalink(); ?>" title="><?php _e('Lire la suite de','auberge');?> &quot;<?php the_title();?>&quot;"><?php the_title();?></a></h2>
								<p class="post-meta dotted-line"><?php _e('Publié par','auberge');?> <?php the_author();?> | <?php the_time('j F Y') ?></p>
								<?php the_excerpt();?>
						</div>
		
				</div>
				
				<a class="read-more-blog" href="<?php the_permalink();?>">><?php _e('Lire la suite','auberge');?> &raquo;</a>
				
			</div>           
		
		<?php endwhile; ?>
		
		<?php include (TEMPLATEPATH . '/pagination.php');
		$wp_query = null; $wp_query = $temp;
		?>
		
		<?php else : ?>
		
		<p><?php _e('Aucun articles correspond à vos critères','auberge');?></p>
		
		<?php endif; ?>
		
		</div>

</div>

<?php get_footer(); ?>