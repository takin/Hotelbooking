<?php /*

Template Name: Canada Home Page

*/?><?php get_header(); ?>

		<div id="main" class="grid_12">
            <div class="box_content box_round group">
              <h1 class="green-bar">Articles r&eacute;cents</h1>
			
			  <?php 
             
				$latestnews = new WP_Query('showposts=2&cat='.get_option('aj_cathome_exclude'));
				$counter=0;
				if ($latestnews->have_posts()): while ($latestnews->have_posts()) : $latestnews->the_post();
				$do_not_duplicate = $post->ID;  $counter++;
				$ville = strip_tags(get_the_term_list($destination->post->ID, __('ville','auberge'), '', ''));?>
				
              
        <div id="post-<?php the_ID(); ?>" class="post-home col2 <?php if($counter == 1){?>first<?php }?>">
             <div class="entry">
               <span class="article-cat"><a href="<?php echo get_permalink(get_page_id($ville)); ?>"><?php echo $ville; ?></a></span>
               <h2><a href="<?php the_permalink(); ?>" title="Lire la suite de &quot;<?php the_title();?>&quot;"><?php the_title();?></a></h2>                    
     <?php if(!get_thumb_url($post->ID)==''){?>
                  <a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="272" height="75" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=272&amp;h=75&amp;src='.get_thumb_url($post->ID);?>" /></a>
                 <?php }else{?>
                  <a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="272" height="75" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=272&amp;h=75&amp;src='.get_bloginfo('template_url').'/images/na.jpg';?>" /></a>
                 <?php }?>
                 
                 <p><?php print string_limit_words(get_the_excerpt(), 20); ?>... <br /><a href="<?php the_permalink();?>">Lire la suite &raquo;</a></p>
                  
              </div>
        </div>           
        
				<?php endwhile; endif; ?>
    
    	
         
        <div class="col2 first">
               <?php /*?> <?php 
             
              	$vedette = new WP_Query('showposts=1&category_name=en-vedette');
				$counter=0;
                if ($vedette->have_posts()): while ($vedette->have_posts()) : $vedette->the_post();
				$do_not_duplicate = $post->ID;  $counter++;?>
				<?php $custom_fields = get_post_custom();?>
                <h2 class="dark-bar-300 margbot10">En vedette: <?php the_title(); ?> </h2>
                <div id="post-<?php the_ID(); ?>" class="post-home col2">
                     <div class="entry">
                     	
                                                                 
						 <?php if(!get_thumb_url($post->ID)==''){?>
                         	<a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="272" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=272&amp;h=85&amp;src='.get_thumb_url($post->ID);?>" /></a>
                         <?php }else{?>
                         	<a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="272" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=272&amp;h=85&amp;src='.get_bloginfo('template_url').'/images/na.jpg';?>" /></a>
                         <?php }?>
                      
                      
                          <p><?php print string_limit_words(get_the_excerpt(), 50); ?>... <a href="<?php the_permalink();?>">Lire la suite &raquo;</a></p>
                        
                      </div>
                      
                      <div class="top-auberges-vedette">
                      
                      	<h3>Auberges Recommand&eacute;es</h3>
                      	
                        <ul>
  							 <?php if(isset($custom_fields['Auberge1_name'])):?>
                             <li><strong><a href="<?php echo get_option('aj_api_url');?>info/auberge/<?php echo $custom_fields['Auberge1_id'][0] ?>"><?php echo $custom_fields['Auberge1_name'][0] ?></a></strong> <em><?php echo $custom_fields['Auberge1_price'][0] ?> &euro;</em></li>
                             <?php endif;?>
                             
                             <?php if(isset($custom_fields['Auberge2_name'])):?>
                             <li><strong><a href="<?php echo get_option('aj_api_url');?>info/auberge/<?php echo $custom_fields['Auberge2_id'][0] ?>"><?php echo $custom_fields['Auberge2_name'][0] ?></a></strong> <em><?php echo $custom_fields['Auberge2_price'][0] ?> &euro;</em></li>
                             <?php endif;?>
                             
                             <?php if(isset($custom_fields['Auberge3_name'])):?>
                             <li><strong><a href="<?php echo get_option('aj_api_url');?>info/auberge/<?php echo $custom_fields['Auberge3_id'][0] ?>"><?php echo $custom_fields['Auberge3_name'][0] ?></a></strong> <em><?php echo $custom_fields['Auberge3_price'][0] ?> &euro;</em></li>
                             <?php endif;?>
                             
                             <?php if(isset($custom_fields['Auberge4_name'])):?>
                             <li><strong><a href="<?php echo get_option('aj_api_url');?>info/auberge/<?php echo $custom_fields['Auberge4_id'][0] ?>"><?php echo $custom_fields['Auberge4_name'][0] ?></a></strong> <em><?php echo $custom_fields['Auberge4_price'][0] ?> &euro;</em></li>
                             <?php endif;?>
                           
                        </ul>

                      
                      </div>
                      
                </div> 
                
                <?php endwhile; endif; ?> <?php */?>
                
                
			 <?php 
             
				$vedettes = get_posts('post_type=page&include='.get_option('aj_home_vedette'));
				$counter=0;
				foreach($vedettes as $post):
				 $counter++;?>
				<?php $custom_fields = get_post_custom();?>
        <h1 class="blue-bar-300 margbot10">En vedette: <?php the_title(); ?> </h1>
        <div id="post-<?php $post->ID; ?>" class="post-home col2">
          <div class="entry">
                                                                 
						<?php if(!get_thumb_url($post->ID)==''){?>
            <a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="272" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=272&amp;h=85&amp;src='.get_thumb_url($post->ID);?>" /></a>
            <?php }else{?>
            <a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="272" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=272&amp;h=85&amp;src='.get_bloginfo('template_url').'/images/na.jpg';?>" /></a>
            <?php }?>
            <p><?php print string_limit_words($post->post_excerpt, 35); ?>... <a href="<?php the_permalink();?>">Lire la suite &raquo;</a></p>
                          
          </div>
                      
          <div class="top-auberges-vedette">
                      
            <h3>Auberges Recommand&eacute;es</h3>
                      	
            <ul>
     <?php if(get_post_meta($post->ID, 'Auberge1_name', true) !=''):?>
                 <li><strong><a href="<?php echo get_post_meta($post->ID, 'Auberge1_url', true) ?>"><?php echo get_post_meta($post->ID, 'Auberge1_name', true) ?></a></strong> <em><?php echo get_post_meta($post->ID, 'Auberge1_price', true) ?></em></li>
                 <?php endif;?>
                 
                  <?php if(get_post_meta($post->ID, 'Auberge2_name', true) !=''):?>
                 <li><strong><a href="<?php echo get_post_meta($post->ID, 'Auberge2_url', true) ?>"><?php echo get_post_meta($post->ID, 'Auberge2_name', true) ?></a></strong> <em><?php echo get_post_meta($post->ID, 'Auberge2_price', true) ?></em></li>
                 <?php endif;?>
                 
                 <?php if(get_post_meta($post->ID, 'Auberge3_name', true) !=''):?>
                 <li><strong><a href="<?php echo get_post_meta($post->ID, 'Auberge3_url', true) ?>"><?php echo get_post_meta($post->ID, 'Auberge3_name', true) ?></a></strong> <em><?php echo get_post_meta($post->ID, 'Auberge3_price', true) ?></em></li>
                 <?php endif;?>
                 
                 <?php if(get_post_meta($post->ID, 'Auberge4_name', true) !=''):?>
                 <li><strong><a href="<?php echo get_post_meta($post->ID, 'Auberge4_url', true) ?>"><?php echo get_post_meta($post->ID, 'Auberge4_name', true) ?></a></strong> <em><?php echo get_post_meta($post->ID, 'Auberge4_price', true) ?></em></li>
                 <?php endif;?>
               
            </ul>
                      
          </div>
                      
        </div> 
                
				<?php endforeach; ?>          
                
      </div>
            
      <div class="col2 home-eve">
        <h1 class="blue-bar-300 margbot10">&Eacute;v&eacute;nements &agrave; surveiller</h1>
          
           <?php 
         
					$latestevent = new WP_Query('showposts=2&category_name=evenements');
					$counter=0;
					if ($latestevent->have_posts()): while ($latestevent->have_posts()) : $latestevent->the_post();
					$do_not_duplicate = $post->ID;  $counter++;?>
            
           <div class="entry">
            <div class="dotted-line">
    <?php if(!get_thumb_url($post->ID)==''){?>
                <a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="85" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=85&amp;h=85&amp;src='.get_thumb_url($post->ID);?>" /></a>
             <?php }else{?>
                <a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img width="85" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=85&amp;h=85&amp;src='.get_bloginfo('template_url').'/images/na-small.jpg';?>" /></a>
             <?php }?>
   
            
                <h2><a href="<?php the_permalink() ?>"><?php the_title();?></a></h2>
              
                <p><?php print string_limit_words(get_the_excerpt(), 20); ?>... <a href="<?php the_permalink() ?>">Suite &raquo;</a></p>
                </div>
            
             </div>
            
            <?php endwhile; endif; ?>
          
      </div>
            
      <h1 class="green-bar" style="margin-bottom:20px;">Auberges de jeunesse les plus populaires</h1>
			
			  <?php 
				$topauberges = new WP_Query('showposts=6&category_name=top-auberges');
				$counter=0;
				if ($topauberges->have_posts()): while ($topauberges->have_posts()) : $topauberges->the_post();
				$do_not_duplicate = $post->ID;  $counter++;?>
				<?php $custom_fields = get_post_custom();?>
              
        <div id="post-<?php the_ID(); ?>" class="post-home col2 top-auberges <?php if($counter == 1){?>first<?php }?>">
          <div class="entry">
            <div class="dotted-line">  
              <h2>
                <a href="<?php echo $custom_fields['Auberge_url'][0];?>" title="Plus de d&eacute;tails sur &quot;<?php the_title();?>&quot;"><?php the_title();?></a>
              </h2>
              <span class="ville"><?php echo $custom_fields['Auberge_ville'][0];?></span>
              <div class="clear"></div>
              <a title="<?php the_title();?>" href="<?php echo $custom_fields['Auberge_url'][0];?>"><img width="61" height="59" alt="<?php the_title();?>" src="<?php echo $custom_fields['Pic_url'][0];?>" /></a>
              
              
              <p><?php print string_limit_words(get_the_excerpt(), 17); ?>... </p>
              
              <p class="bottom-price"><span>&Agrave; partir de: <strong><?php echo $custom_fields['Auberge_price'][0];?></strong></span> <a href="<?php echo $custom_fields['Auberge_url'][0];?>">Plus de d&eacute;tails &raquo;</a></p>
                      
              <div class="clear"></div>
            </div>
          </div>
        </div> 
                
				<?php if($counter == 2){$counter = 0;}?>          
  
        <?php endwhile; endif; ?>
         
     </div>

  </div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>