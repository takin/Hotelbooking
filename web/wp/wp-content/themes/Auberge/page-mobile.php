<?php get_header(); ?>
<div id="content" class="page-mobile">    
	<?php if(have_posts()): while (have_posts()) : the_post();?>
        
		<div class="page-meta group">
			<h1 class="text-shadow-wrapper dot-icon"><?php the_title();?></h1>
		</div>
		
		<div class="white-back round-corner5 border-around form wordpress">
			<?php the_content();?> 
			<?php if (is_page_template('contact.php')){?>
			<?php include( TEMPLATEPATH . '/mobile-form.php' );?>
			<?php }?>
		</div> 
	
	<?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>