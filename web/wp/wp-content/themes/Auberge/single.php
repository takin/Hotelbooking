<?php
  $post = $wp_query->post;
   if ( in_category('destinations') ) {
	   include(TEMPLATEPATH . '/single-destination.php');
   }else{
?>

<?php get_header(); ?>

	<?php if (have_posts()) : ?>

	<?php while (have_posts()) : the_post();
        $custom_fields = get_post_custom();
        $taxterm = strip_tags(get_the_term_list($destination->post->ID, __('ville','auberge'), '', ''));
        $taxid = get_page_id($taxterm);?>
        <div id="sidebar" class="grid_4">
        <?php if(in_category('top-auberges')){
         if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Single Article Sidebar') ) : endif;
				}else{?>
					<?php if ($taxid != ""){?>
            <div class="box_content box_round group">
            <h2><?php _e('Plus sur','auberge');?> <?php echo ucfirst($taxterm);?></h2>
            <?php $children = wp_list_pages('&child_of='. $taxid .'&echo=0&title_li=&depth=1'); ?>

              <ul class="submenu">
                <li><a href="<?php echo get_permalink($taxid);?>"><?php _e('Informations générales sur','auberge');?> <?php echo $taxterm;?></a></li>
                <?php echo $children; ?>
              </ul>
            </div>
          <?php  }}?>

        </div>

        <div id="main" class="grid_12">
            <div class="box_content box_round group">

        <?php if(in_category('guides')){?>

         <h1 class="border_title"><?php _e('Tous ce que vous devez savoir sur','auberge');?> <?php the_title(); ?></h1>
        <?php }elseif(in_category('evenements')){?>

         <h1 class="border_title"><?php _e('À ne pas manquer :','auberge');?> <?php the_title(); ?></h1>
        <?php }elseif(in_category('top-auberges')){?>

         <h1 class="border_title"><?php _e('Auberges Populaires','auberge');?></h1>
        <?php }else{ ?>

         <h1 class="border_title"><?php the_title(); ?> - <?php echo strip_tags(get_the_term_list($destination->post->ID, __('ville','auberge'), '', '')); ?></h1>
        <?php } ?>
        <div class="entry group" id="post-<?php the_ID(); ?>">
            <?php if(!in_category('top-auberges') ){?>

             <?php if((!get_thumb_url($post->ID)=='') && (get_post_image($post->ID) =='')){?>
                <img class="alignleft" width="200" height="140" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/t.php?zc=1&amp;w=200&amp;h=140&amp;src='.get_thumb_url($post->ID);?>" />
            <?php }?>

              <?php the_content();?>

            <?php }?>

        </div>
        <?php if(!in_category('top-auberges') ){?>
        <?php //comments_template(); ?>
        <?php }?>
    <?php endwhile; else: ?>

    </div>
<?php endif; ?>

</div>
</div>

<?php get_footer(); ?>
<?php }?>