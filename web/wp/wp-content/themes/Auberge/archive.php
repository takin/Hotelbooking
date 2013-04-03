<?php if (is_category('guides')){?>
<?php wp_redirect(get_option('siteurl') . '/guides'); ?>

<?php }else{?>

<?php get_header(); $term = get_term_by( 'slug', get_query_var( __('ville','auberge') ), get_query_var( 'taxonomy' ) );?>
<?php get_sidebar(); ?>

	 <div id="main" class="grid_12">

		  <?php // Category Guide ?>

		  <?php if (is_category()) { ?>

			<h1 class="title_outside"><?php _e('Catégorie','auberge');?> <?php single_cat_title(); ?></h1>

			<?php } elseif( is_tag() ) { ?>
			<h1 class="title_outside"><?php _e('Archive pour le mot clé','auberge'); ?> &#8216;<?php single_tag_title(); ?>&#8217; <a title="<?php _e('Suivre cette catégorie','auberge');?>" class="archive-rss" href="<?php echo get_category_feed_link('category_rss2_url'); ?>feed/"><img src="<?php bloginfo('template_url'); ?>/images/archive-rss.png" id="archive-rss" alt="<?php _e('Archive RSS','auberge');?>" /></a></h1>

			<?php } elseif (is_day()) { ?>
			<h1 class="title_outside"><?php _e('Archive pour','auberge'); ?> <?php echo strftime("%B %e %Y",get_the_time('U')); ?></h1>

			<?php } elseif (is_month()) { ?>
			<h1 class="title_outside"><?php _e('Archive pour','auberge'); ?> <?php echo strftime("%B %Y",get_the_time('U')); ?></h1>

			<?php } elseif (is_year()) { ?>
			<h1 class="title_outside"><?php _e('Archive pour','auberge'); ?> <?php echo strftime("%Y",get_the_time('U')); ?></h1>

			<?php } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<h1 class="title_outside"><?php _e('Archives du Blog','auberge'); ?></h1>

			<?php } elseif (is_taxonomy(__('ville','auberge'))){ ?>
			<h1 class="title_outside"><?php _e('Tous savoir sur','auberge');?> <?php echo $term->name; ?></h1>
			<?php } ?>

		    <?php  if (have_posts()) : $post = $posts[0];  while (have_posts()) : the_post(); ?>
					<div class="box_content box_round group list_box<?php if(get_thumb_url($post->ID)==''){echo ' no_thumb';}?>">
            <div id="post-<?php the_ID(); ?>" class="post">
              <div class="entry">
							<?php if(!get_thumb_url($post->ID)==''){?>
							<a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="130" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/t.php?zc=1&amp;w=130&amp;h=85&amp;src='.get_thumb_url($post->ID);?>" /></a>
							<?php }?>
							<h2><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
								<p class="post-meta dotted-line"><?php _e('Type d\'articles:','auberge');?> <?php the_category(', ') ?></p>

								<p class="excerpt"><?php print string_limit_words(strip_tags(get_the_content()), 50); ?>...</p>
								<p><a href="<?php the_permalink() ?>"><?php _e('Voir l\'article au complet','auberge');?> &raquo;</a></p>
							</div>
            </div>
					</div>
		<?php endwhile; ?>

		<?php include (TEMPLATEPATH . '/pagination.php'); ?>

	<?php else : ?>

		<div class="box_content box_round group">
        	<p><?php _e('Aucun articles correspond à vos critères','auberge'); ?></p>
		</div>
	<?php endif; ?>


</div>

<?php get_footer();} ?>