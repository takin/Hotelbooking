<?php get_header(); $term = get_term_by( 'slug', get_query_var( __('ville','auberge') ), get_query_var( 'taxonomy' ) );?>
	
	 <div id="main">
        <div class="col1 blog">
        	
		  <?php if (is_category()) { ?>
            
            <h2 class="dark-bar"><?php _e('Catégorie','auberge');?> <?php single_cat_title(); ?><a title="<?php _e('Suivre cette catégorie','auberge');?>" class="archive-rss" href="<?php echo get_category_feed_link('category_rss2_url'); ?>feed/"><img src="<?php bloginfo('template_url'); ?>/images/archive-rss.png" id="archive-rss" alt="<?php _e('Archive RSS','auberge');?>" /></a></h2>
          
          <?php } elseif( is_tag() ) { ?>
            <h2 class="dark-bar"><?php _e('Archive pour le mot clé','auberge'); ?> &#8216;<?php single_tag_title(); ?>&#8217; <a title="<?php _e('Suivre cette catégorie','auberge');?>" class="archive-rss" href="<?php echo get_category_feed_link('category_rss2_url'); ?>feed/"><img src="<?php bloginfo('template_url'); ?>/images/archive-rss.png" id="archive-rss" alt="<?php _e('Archive RSS','auberge');?>" /></a></h2>
          
          <?php } elseif (is_day()) { ?>
            <h2 class="dark-bar"><?php _e('Archive pour','auberge'); ?> <?php echo strftime("%B %e %Y",get_the_time('U')); ?></h2>
          
          <?php } elseif (is_month()) { ?>
            <h2 class="dark-bar"><?php _e('Archive pour','auberge'); ?> <?php echo strftime("%B %Y",get_the_time('U')); ?></h2>
          
          <?php } elseif (is_year()) { ?>
            <h2 class="dark-bar"><?php _e('Archive pour','auberge'); ?> <?php echo strftime("%Y",get_the_time('U')); ?></h2>
          
          <?php } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
            <h2 class="dark-bar"><?php _e('Archives du Blog','auberge'); ?></h2>
          <?php } elseif (is_taxonomy(__('ville','auberge'))){ ?>
          <h2 class="blue-bar"><?php _e('Tous savoir sur','auberge');?> <?php echo $term->name; ?></h2>
          <?php } ?>
		  <div class="entry">  
		    <?php if (have_posts()) : $post = $posts[0];  while (have_posts()) : the_post(); ?>
		
            <div id="post-<?php the_ID(); ?>" class="post">
              <h1 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
              <p class="post-meta dotted-line"><?php _e('Publié par','auberge');?> <?php the_author();?> | <?php echo strftime("%B %Y",get_the_time('U')); ?> | <?php _e('Type d\'articles:','auberge');?> <?php the_category(', ') ?></p>
             
              <p class="excerpt"><?php print string_limit_words(strip_tags(get_the_content()), 80); ?>...</p>
              <p><a href="<?php the_permalink() ?>"><?php _e('Voir l\'article au complet','auberge');?> &raquo;</a></p>
         
            </div>

		<?php endwhile; ?>
			</div>
		<?php include (TEMPLATEPATH . '/pagination.php'); ?>
		
	<?php else : ?>

		
        	<p><?php _e('Aucun articles correspond à vos critères','auberge'); ?></p>
		</div>
	<?php endif; ?>
    
	</div>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>