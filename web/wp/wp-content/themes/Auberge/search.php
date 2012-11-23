<?php get_header(); ?>
<?php get_sidebar(); ?>
<div id="main" class="grid_12">	
    
		<h1 class="title_outside"><?php _e('Résultats de recherche pour','auberge'); ?> &#8216;<em><?php the_search_query() ?></em>&#8217;</h2>
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        	<div class="box_content box_round group list_box no_thumb">          
              <div id="post-<?php the_ID(); ?>" class="post entry">                
								<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php search_title_highlight(); ?></a></h2>
                <p class="post-meta dotted-line"><?php _e("Type d'articles :",'auberge'); ?> <?php the_category(', ') ?></p>
               
									<p class="excerpt"><?php search_excerpt_highlight(); ?></p>
									<p><a href="<?php the_permalink() ?>"><?php _e("Voir l'article au complet",'auberge'); ?> &raquo;</a></p>
               
              </div>
						</div>
              <?php endwhile; ?>
		
		<?php include (TEMPLATEPATH . '/pagination.php'); ?>
        	
		<?php else : ?>
          <div class="box_content box_round group">           
            <p><?php _e('Désolé aucun résultat ne répond à vos critères.','auberge');?></p>
            <?php get_search_form(); ?>
            </div>
        <?php endif; ?>
	
	</div>

<?php get_footer(); ?>