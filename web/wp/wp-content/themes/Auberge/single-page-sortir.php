<?php /*

Template Name: Page Sortir Destination

*/?><?php get_header(); ?>

<div id="sidebar">
<?php 
$GLOBALS['current_id']=$post->ID; 
$children = wp_list_pages('&child_of='. $GLOBALS['current_id'] .'&echo=0&title_li=&depth=1');  
if($children) {?>
<div class="widget">
			 <h2 class="blue-bar-300">Plus sur <?php echo $taxterm;?></h2>
			 
					 <ul class="submenu">
					 <li class="current_page_item"><a href="<?php echo $firstpostlink;?>">Information g&eacute;n&eacute;ral sur <?php echo $taxterm;?></a></li>
					 <?php echo $children; ?>
				</ul>
			 
</div><?php  
}else{
$children= "";
						
$children = wp_list_pages('&child_of='. $parent .'&echo=0&title_li=&depth=1');  
if($children) {?>
					<div class="widget">
					<ul class="submenu"><?php   
					 echo $children; if ($root1 == 0 || $root1 == 1){ ?><li><a href="<?php echo get_permalink($parent1);?>">Information g&eacute;n&eacute;ral sur <?php echo get_page_name($parent1);?></a></li>		
<?php } ?>
	</ul>
	</div><?php 
 }
}?>
</div>

<div id="main" class="grid_12 destination">
	
	<div class="box_content box_round group">
    
    
	<?php if(have_posts()): while (have_posts()) : the_post(); $firstpostlink=get_permalink();?>
    
    <?php $ancestors=get_post_ancestors($GLOBALS['current_id']);
			$root1=count($ancestors)-1;
			switch ($root1) {
				case 0:
				$root=count($ancestors)-1;
				$parent = $ancestors[$root];
				break;
				case 1:
				$root=count($ancestors)-2;
				$parent = $ancestors[$root];
				break;
			}
				
			$parent1 = $ancestors[$root1];
	 ?>
       
        <h2 class="green-bar"><?php the_title();?></h2>
        
		<div class="entry">
			<?php the_content();?>
        </div>
        
        <?php endwhile; ?>			  
        <?php else:?>
       
        <?php endif; ?>
   
    <h2 class="blue-bar">Sortir - &agrave; <?php echo get_page_name($parent1);?></h2>
    
    </div>
    
     <?php 
	 
		
		$posts = get_posts('exclude='.$firstpost.'&numberposts=4&ville='.$taxterm);
		//$destination = new WP_Query('showposts=-1&cat=-5&ville='.$taxterm);
		$counter=0;		
		foreach($posts as $post) :
		//if ($destination->have_posts()): while ($destination->have_posts()) : $destination->the_post();
		$do_not_duplicate = $post->ID;  $counter++;?>
		
	   <?php // echo strip_tags(get_the_term_list($destination->post->ID, 'ville', '', '')); ?>
		<div id="post-<?php the_ID(); ?>" class="col2 more-info <?php if($counter == 1){?>first<?php }?>">
			 <div class="entry">
			  <span class="article-cat"><?php
					$count = count(get_the_category());
					$counter2 = 0;
					foreach((get_the_category()) as $category) { 
						$counter2++;
						if($counter2 == $count){
							echo $category->cat_name; 
						}else{
							echo $category->cat_name . ', '; 
						}
					} 
			   ?></span>
			   <h1><a href="<?php the_permalink(); ?>" title="Lire la suite de &quot;<?php the_title();?>&quot;"><?php the_title();?></a></h1>                    
				 <?php if(!get_thumb_url($post->ID)==''){?>
                      <a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img class="alignleft" width="85" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=85&amp;h=85&amp;src='.get_thumb_url($post->ID);?>" /></a>
                   <?php }else{?>
                      <a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img class="alignleft" width="85" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=85&amp;h=85&amp;src='.get_bloginfo('template_url').'/images/na-small.jpg';?>" /></a>
                   <?php }?>
				 
				 <p><?php print string_limit_words(get_the_excerpt(), 40); ?>... </p>
                 <a href="<?php the_permalink();?>">Lire la suite &raquo;</a>
				  
			  </div>
		</div> 
        <?php if($counter == 2){$counter = 0;?><div style="height:5px; clear:both; margin:0 20px 12px 20px;" class="dotted-line"></div><?php }?>          
    <?php  endforeach; ?>
	<?php //endwhile; endif; ?>

</div>



</div>

<?php get_footer(); ?>