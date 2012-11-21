<?php /*

Template Name: Page Aide

*/?><?php get_header(); ?>
<?php get_sidebar(); ?>
<div id="main" class="grid_12">
	
	<div class="box_content box_round group">
    
    
	<?php if(have_posts()): while (have_posts()) : the_post();?>
        
    <h1 class="green-bar"><?php the_title();?></h1>
        
		<div class="entry copy faq">
			<?php the_content();?>
    </div>
		<?php endwhile; endif; ?>
    
    
    </div>

</div>

<?php get_footer(); ?>