<?php get_header(); ?>
<?php
if($GLOBALS['print']=='mobile'){
include( TEMPLATEPATH . '/page-mobile.php' );
}else{?>
<?php if($GLOBALS['print']=='nostyle'){
}else{
get_sidebar();
}
?>
<div id="main" class="grid_12">
	
	<div class="box_content box_round group">
    
    
	<?php if(have_posts()): while (have_posts()) : the_post();?>
        
    <h1 class="border_title"><?php the_title();?></h1>
        
		<div class="entry copy">
			<?php the_content();?>
      
      <?php echo get_post_meta($post->ID, 'custom_html', $single = true);?>
      
    </div>
		<?php endwhile; endif; ?>
    
    
    </div>

</div>


<?php }?>
<?php get_footer(); ?>
