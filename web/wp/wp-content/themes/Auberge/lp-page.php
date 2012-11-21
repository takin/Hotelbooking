<?php /*

Template Name: Landing Page

*/?><?php get_header(); ?>

<div id="main" style="position:relative;">
	
	<div class="box_content box_round group">
    
    
	<?php if(have_posts()): while (have_posts()) : the_post();?>
        
    <h1 class="green-bar"><?php echo get_post_meta($post->ID, 'LP_titlebar', true);?></h1>
		<div class="entry">
			<?php the_content();?>
    </div>
    
    <?php echo get_post_meta($post->ID, 'LP_content', true);	?>
    <?php $page_name = get_post_meta($post->ID, 'LP_ville', true);	?>
    <?php $city_url = get_post_meta($post->ID, 'City_url', true);	?>
		
    <div class="price-tag" style="position:absolute;top:<?php echo get_post_meta($post->ID, 'top_position', true);	?>px; left:<?php echo get_post_meta($post->ID, 'left_position', true);?>px;background:url(<?php echo get_option('aj_api_url'); ?>images/price-tag.png) no-repeat top left;">
    	<p align="center"><a href="<?php echo $city_url;?>/<?php echo get_date_default($day_offset = 2)?>/3">&Agrave; partir de<br /><span><?php echo get_post_meta($post->ID, 'lowest_price', true);	?></span></a></p>
    </div>
    
    <?php endwhile; ?><?php endif; ?>
    
    </div>

</div>

<div id="sidebar">
  <?php $taxid = get_page_id($page_name);?>
  <?php if ($taxid != ""){?>
  <div class="widget">
  <h1 class="blue-bar-300">Plus sur <?php echo ucfirst($page_name);?></h1>
  <?php $children = wp_list_pages('&child_of='. $taxid .'&echo=0&title_li=&depth=1'); ?>
  
    <ul class="submenu">
      <li><a href="<?php echo get_permalink($taxid);?>"><?php _e('Informations générales sur','auberge');?> <?php echo $page_name;?></a></li>
      <?php echo $children; ?>
    </ul>
  </div>
   
   <?php 

    $auberge1_name = get_post_meta($taxid, 'Auberge1_name', true);	
    $auberge1_url = get_post_meta($taxid, 'Auberge1_url', true);	
    $auberge1_pic_url = get_post_meta($taxid, 'Auberge1_pic_url', true);	
    $auberge1_desc = get_post_meta($taxid, 'Auberge1_desc', true);	
    $auberge1_price = get_post_meta($taxid, 'Auberge1_price', true);	
    
    $auberge2_name = get_post_meta($taxid, 'Auberge2_name', true);	
    $auberge2_url = get_post_meta($taxid, 'Auberge2_url', true);	
    $auberge2_pic_url = get_post_meta($taxid, 'Auberge2_pic_url', true);	
    $auberge2_desc = get_post_meta($taxid, 'Auberge2_desc', true);	
    $auberge2_price = get_post_meta($taxid, 'Auberge2_price', true);	
    
    $auberge3_name = get_post_meta($taxid, 'Auberge3_name', true);	
    $auberge3_url = get_post_meta($taxid, 'Auberge3_url', true);	
    $auberge3_pic_url = get_post_meta($taxid, 'Auberge3_pic_url', true);	
    $auberge3_desc = get_post_meta($taxid, 'Auberge3_desc', true);	
    $auberge3_price = get_post_meta($taxid, 'Auberge3_price', true);	
    
    $auberge4_name = get_post_meta($taxid, 'Auberge4_name', true);	
    $auberge4_url = get_post_meta($taxid, 'Auberge4_url', true);	
    $auberge4_pic_url = get_post_meta($taxid, 'Auberge4_pic_url', true);	
    $auberge4_desc = get_post_meta($taxid, 'Auberge4_desc', true);	
    $auberge4_price = get_post_meta($taxid, 'Auberge4_price', true);	
    
    $city_url = get_post_meta($taxid, 'City_url', true);	?>
      
      
    <?php if(!empty($auberge1_name)):?>
    <div class="widget">
        <h1 class="green-bar-300"><?php _e('Coup de Coeur !','auberge');?></h1>
        <div class="widget-content">
        <ul class="side-hostel-list clearfix">
                                      
            <li>
                <a href="<?php echo $auberge1_url;?>"><img alt="<?php echo $auberge1_name;?>" src="<?php echo $auberge1_pic_url;?>" /></a>
                <a href="<?php echo $auberge1_url;?>"><?php echo $auberge1_name;?></a>
                <span class="desc"><?php echo $auberge1_desc;?></span>  
                <span class="price"><?php _e('À partir de','auberge');?> <strong><?php echo $auberge1_price;?></strong></span>
                <a href="<?php echo $auberge1_url;?>/<?php echo get_date_default($day_offset = 2)?>/3" class="reserv"><?php _e('Réserver Maintenant','auberge');?> &raquo;</a>                    
            </li>
            <?php if(!empty($auberge2_name)):?>
            <li>
                <a href="<?php echo $auberge2_url;?>"><img alt="<?php echo $auberge2_name;?>" src="<?php echo $auberge2_pic_url;?>" /></a>
                <a href="<?php echo $auberge2_url;?>"><?php echo $auberge2_name;?></a>
                <span class="desc"><?php echo $auberge2_desc;?></span>  
                <span class="price"><?php _e('À partir de','auberge');?> <strong><?php echo $auberge2_price;?></strong></span>
                <a href="<?php echo $auberge2_url;?>/<?php echo get_date_default($day_offset = 2)?>/3" class="reserv"><?php _e('Réserver Maintenant','auberge');?> &raquo;</a>                  
            </li> 
            <?php endif;?> 
            
            <?php if(!empty($auberge3_name)):?>
            <li>
                <a href="<?php echo $auberge3_url;?>"><img alt="<?php echo $auberge3_name;?>" src="<?php echo $auberge3_pic_url;?>" /></a>
                <a href="<?php echo $auberge3_url;?>"><?php echo $auberge3_name;?></a>
                <span class="desc"><?php echo $auberge3_desc;?></span>  
                <span class="price"><?php _e('À partir de','auberge');?> <strong><?php echo $auberge3_price;?></strong></span>   
                <a href="<?php echo $auberge3_url;?>" class="reserv"><?php _e('Réserver Maintenant','auberge');?> &raquo;</a>                         
            </li> 
            <?php endif;?>
            
            <?php if(!empty($auberge4_name)):?>
            <li>
                <a href="<?php echo $auberge4_url;?>"><img alt="<?php echo $auberge4_name;?>" src="<?php echo $auberge4_pic_url;?>" /></a>
                <a href="<?php echo $auberge4_url;?>"><?php echo $auberge4_name;?></a>
                
                <span class="desc"><?php echo $auberge4_desc;?></span>  
                <span class="price"><?php _e('À partir de','auberge');?> <strong><?php echo $auberge4_price;?></strong></span> 
                <a href="<?php echo $auberge4_url;?>" class="reserv"><?php _e('Réserver Maintenant','auberge');?> &raquo;</a>              
            </li>  
            <?php endif;?>         
                
        </ul>
        </div>
          
        <?php if(!empty($city_url)):?>
        <ul class="submenu">
            <li><a href="<?php echo $city_url;?>"><?php _e('Voir toutes les auberges à','auberge');?> <?php echo $page_name;?></a></li>
        </ul>
        <?php endif;?> 
      
      </div> 

	<?php endif;}?>  
  
  <?php /*?><div class="widget">
  	<div class="lp-search">
      <a title="Voir la liste compl&egrave;te des Auberges de Jeunesse &agrave; Paris" href="<?php echo $city_url;?>">Rechercher</a>
      <h2>Voir la liste compl&egrave;te des Auberges De Jeunesse &agrave; <?php echo $page_name;	?></h2>
      <p>Notre puissant moteur de recherche vous aidera &agrave; touver l'auberge de jeunesse idéale afin de profiter au maximum de votre séjour &agrave; <?php echo $page_name;	?></p>
    </div>
  </div><?php */?>

  <?php /*?><div class="widget">
    <h2 class="dark-bar-300">Notre guide sur <?php echo $page_name;	?></h2>
    <div class="widget-content">
    <?php 
		$pageid = get_page_id($page_name);
		$posts = get_posts('post_type=page&numberposts=1&include='.$pageid);
		foreach($posts as $post) :
		?>
    <?php if(!get_thumb_url($post->ID)==''){?>
        <a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><img class="alignleft borderimg" width="85" height="85" alt="<?php the_title();?>" src="<?php echo get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=85&amp;h=85&amp;src='.get_thumb_url($post->ID);?>" /></a>
     <?php }?>
		<?php echo apply_filters('the_content', $post->post_excerpt); ?>
    <a href="<?php echo get_permalink($post->ID);?>">Lire le guide complet sur <?php echo $page_name;?> &raquo;</a>
    <?php endforeach;?>
    </div>
  </div><?php */?>
  
  <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('LP Page Sidebar') ) : endif;	 ?>
  
</div>



<?php get_footer(); ?>