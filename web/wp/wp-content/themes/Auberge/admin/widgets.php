<?php
/* Widgets ********************************************/
// This establishes the elements that wrap your widgets

/* Widgets ********************************************/
// This establishes the elements that wrap your widgets

if ( function_exists('register_sidebar') )
    register_sidebar(array(
		'name' => 'Home Page Sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div> ',
        'before_title' => '',
        'after_title' => '',
    ));

if ( function_exists('register_sidebar') )
    register_sidebar(array(
		'name' => 'Sidebar Widgets',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div> ',
        'before_title' => '<h2 class="widgettitle cufon-set">',
        'after_title' => '</h2>',
    ));

if ( function_exists('register_sidebar') )
    register_sidebar(array(
		'name' => 'Evenements Page Sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h2 class="green-bar-300">',
        'after_title' => '</h2><div class="widget-content">',
		
    ));

if ( function_exists('register_sidebar') )
    register_sidebar(array(
		'name' => 'Single Page Sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
       'after_widget' => '</div></div>',
        'before_title' => '<h2 class="dark-bar-300">',
        'after_title' => '</h2><div class="widget-content">',
		
    ));

if ( function_exists('register_sidebar') )
    register_sidebar(array(
		'name' => 'Single Article Sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h2 class="dark-bar-300">',
        'after_title' => '</h2><div class="widget-content">',
		
    ));

if ( function_exists('register_sidebar') )
    register_sidebar(array(
		'name' => 'Archive Page Sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h2 class="dark-bar-300">',
        'after_title' => '</h2><div class="widget-content">',
		
    ));

if ( function_exists('register_sidebar') )
    register_sidebar(array(
		'name' => 'Search Page Sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h2 class="dark-bar-300">',
        'after_title' => '</h2><div class="widget-content">',
		
    ));
	

if ( function_exists('register_sidebar') )
    register_sidebar(array(
		'name' => '404 Page Sidebar',
       'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h2 class="dark-bar-300">',
        'after_title' => '</h2><div class="widget-content">',
		
    ));
	
if ( function_exists('register_sidebar') )
    register_sidebar(array(
		'name' => 'LP Page Sidebar',
       'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h2 class="dark-bar-300">',
        'after_title' => '</h2><div class="widget-content">',
		
    ));

if ( function_exists('register_sidebar') )
    register_sidebar(array(
		'name' => 'Static Page Sidebar',
       'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h2 class="dark-bar-300">',
        'after_title' => '</h2><div class="widget-content">',
		
    ));
	

/* Creating the widget */

/**
 * Search_Hostels_Widget
 *
 */
class Search_Hostels_Widget extends WP_Widget {
  /**
   * Widget setup.
   */
  function Search_Hostels_Widget() {
    /* Widget settings. */
    $widget_ops = array( 'classname' => 'widget_search_hostels', 'description' => "Recherche d'auberges" );
    /* Widget control settings. */
    $control_ops = array( 'id_base' => 'search-hostels-widget' );
    /* Create the widget. */
    $this->WP_Widget( 'search-hostels-widget', 'Recherche auberges', $widget_ops, $control_ops );
  }
  /**
   */
  function widget( $args, $instance ) {
    
    
    extract( $args );
    /* Our variables from the widget settings. */
    $title = apply_filters('widget_title', $instance['title'] );
    
    //make sure jtools script is loaded
    wp_enqueue_script( 'jtools' ); 
    
//    $categoriesID = $instance['categoriesID'];
//    $photosCatID = $instance['photosCatID'];

    /* Before widget (defined by themes). */
    
?>
<script type="text/javascript">
//City lists
//Cities array must be a global variable
var cities_widget = new Array();

<?php 
$countryEmptyVal = __('Choisir un pays');
$cityEmptyVal    = __('Choisir une ville');

echo "cities_widget['".$countryEmptyVal."'] = new Array('".$cityEmptyVal."');\n";
?>
$(document).ready(function() {
	  //alert('search widget loaded');
	  loadCitiesMenu('<?php bloginfo('url');?>/auberges/','<?php echo _('Chargement...');?>','cities_widget',cities_widget,'widget-search-country','widget-search-city');
	});
</script>
<div class="widget">
    <h2 class="blue-bar-300"><?php echo $title; ?></h2>
      <form id="search-form" action="" method="">
          <ul>
            <li>
              <label for="widget-search-country"><?php _e('Spécifier Pays:','auberge');?></label>
              <select id="widget-search-country" name="widget-search-country" onchange="setCities(cities_widget,'widget-search-country','widget-search-city');">
              <option value="no_country_selected"><?php _e('Choisir un pays','auberge');?></option>
              </select>
              
            </li>          
            <li>
              <label for="widget-search-city"><?php _e('Spécifier Ville:','auberge');?></label>
              <select id="widget-search-city" name="widget-search-city">
              <option value="no_city_selected"><?php _e('Choisir une ville','auberge');?></option>
              </select>
            </li>    
            <li>
              <?php
              if(isset($date_selected))
              {
                ;
              }
              elseif(isset($_COOKIE["date_selected"]))
              {
                $date_selected = $_COOKIE["date_selected"];
              }
              else
              {
                $date_selected = NULL;
              }
              
              select_date(__("Arrivée le:"),"widget-search-date-day","widget-search-date-day","widget-search-date-month","widget-search-date-month",$date_selected);
              ?>
            </li>      
            <li>
               <?php 
              if(isset($numnights_selected))
              {
                ;
              }
              elseif(isset($_COOKIE["numnights_selected"]))
              {
                $numnights_selected = $_COOKIE["numnights_selected"];
              }
              else
              {
                $numnights_selected = NULL;
              }
              select_nights(__("Nuits:"),"widget-search-night","widget-search-night",$numnights_selected);
              ?>
            </li>          
            
            <li>
              <input onfocus="this.blur()" type="button" name="search-submit" id="search-submit" onClick="goToSearchPage('<?php bloginfo('url');?>/auberges/','<?php echo $countryEmptyVal; ?>','<?php _e('Pays introuvable','auberge');?>','<?php echo $cityEmptyVal; ?>','<?php _e('Ville introuvable','auberge');?>','<?php _e('Date invalide','auberge');?>','widget-search-country','widget-search-city','widget-search-date-month','widget-search-date-day','widget-search-night');"/>
            </li>
          </ul>
        </form>
    </div>
<?php 
    /* After widget (defined by themes). */
   

  }
  /**
   * Update the widget settings.
   */
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    /* Strip tags for title and name to remove HTML (important for text inputs). */
    $instance['title'] = strip_tags( $new_instance['title'] );
    return $instance;
  }
  /**
   * Displays the widget settings controls on the widget panel.
   */
  function form( $instance ) {
    /* Set up some default widget settings. */
    $defaults = array( 'title' =>__('Trouver une Auberge','auberge'));
    $instance = wp_parse_args( (array) $instance, $defaults ); ?>
    <!-- Widget Title: Text Input -->
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
    </p>
    
  <?php
  }
}
register_widget('Search_Hostels_Widget');

/****************************************************/
class Social_Widget extends WP_Widget {

	function Social_Widget() {
		$widget_ops = array('classname' => 'widget_social', 'description' => 'Social icon, faceboo, twitter and flickr' );
		$this->WP_Widget('social', 'Social Links', $widget_ops);

	}
 
	function widget($args, $instance) {
	
		extract($args, EXTR_SKIP);
			
		if ((get_option('aj_social_facebook')!='') && (get_option('aj_social_twitter')=='') && (get_option('aj_social_flickr')=='')){
		
		echo '<div class="widget">
						<div class="gray-block">
							<a class="sidebar-facebook" href="'.get_option('aj_social_facebook').'">'.__('Joignez-vous à nous sur Facebook','auberge').'</a>
						</div>
					</div>';
		
		}else{
				
		if((get_option('aj_social_facebook')!='') || (get_option('aj_social_twitter')!='') || (get_option('aj_social_flickr')!='')){
		echo '<div class="widget social-icon">
			  <div class="widget-content">
			  <div class="dotted-line-top" style="padding-top:15px;">';
		if (get_option('aj_social_facebook')!=''){
	   		echo '<a title="Facebook" href="'.get_option('aj_social_facebook').'"><img src="'.get_option('aj_api_url').'images/facebook.png" alt="Facebook" /></a>';
	   }
	   
	    if (get_option('aj_social_twitter')!=''){
	   		echo '<a title="Twitter" href="'.get_option('aj_social_twitter').'"><img src="'.get_option('aj_api_url').'images/twitter.png" alt="Twitter" /></a>';
	   }
	   
	    if (get_option('aj_social_flickr')!=''){
	   		echo '<a title="Flickr" href="'.get_option('aj_social_flickr').'"><img src="'.get_option('aj_api_url').'images/flickr.png" alt="Flickr" /></a>';
	   }
				  
			
	   echo ' </div>
			  <div class="dotted-line" style="height:2px; padding-top:11px;"></div>
			  </div>
			  </div>
			  ';
		}}

	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
				
		return $instance;

	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
		
						
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
            <p>If you want to modiy the links setting, please go to the theme settings in the Appearance menu.</p>
            
          
           			
<?php
	}
}

register_widget('Social_Widget');



class TopCity_Widget extends WP_Widget {

	function TopCity_Widget() {
		$widget_ops = array('classname' => 'TopCity', 'description' => 'Top City List linking to search hostel' );
		$this->WP_Widget('topcity', 'Top City', $widget_ops);

	}
 
	function widget($args, $instance) {
	
		extract($args, EXTR_SKIP);
		$title = empty($instance['title']) ? '&nbsp;' : apply_filters('title', $instance['title']);
		$color = empty($instance['title']) ? '&nbsp;' : apply_filters('color', $instance['color']);
		echo '<div class="widget">
              <h2 class="'.$color.'-bar-300">'.$title.'</h2>
                <div class="widget-content">
                <p class="populaire" style="line-height:1.8em;">';
          
    include(TEMPLATEPATH .'/widget/topcity.php');
              
    echo '       		</p>
                </div>
            </div>';
		
	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['color'] = strip_tags($new_instance['color']);
		return $instance;

	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'color' => 'blue' ) );
		$title = strip_tags($instance['title']);
		$color = strip_tags($instance['color']);
		
						
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
            
            <p><label for="<?php echo $this->get_field_id('color'); ?>">Color of the title bar (dark, green or blue): <input class="widefat" id="<?php echo $this->get_field_id('color'); ?>" name="<?php echo $this->get_field_name('color'); ?>" type="text" value="<?php echo attribute_escape($color); ?>" /></label></p>
            
         
           			
<?php
	}
}

register_widget('TopCity_Widget');



class Simple_Widget extends WP_Widget {

	function Simple_Widget() {
		$widget_ops = array('classname' => 'simple_social', 'description' => 'Simple Widget for pure HTML or flash banner' );
		$this->WP_Widget('simplel', 'Simple HTML', $widget_ops);

	}
 
	function widget($args, $instance) {
	
		extract($args, EXTR_SKIP);
		$code_field = empty($instance['code_field']) ? '&nbsp;' : apply_filters('code_field', $instance['code_field']);
		
		echo '<div class="widget">'.$code_field.'</div>';

	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['code_field'] = $new_instance['code_field'];	
		return $instance;

	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '','code_field' => '', ) );
		$code_field = $instance['code_field'];
		$title = strip_tags($instance['title']);
		
						
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title (internal use): <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
            
            <p><label for="<?php echo $this->get_field_id('code_field'); ?>">HTML Code: <textarea rows="10" class="widefat" id="<?php echo $this->get_field_id('code_field'); ?>" name="<?php echo $this->get_field_name('code_field'); ?>" ><?php echo $code_field; ?></textarea></label></p>
           			
<?php
	}
}

register_widget('Simple_Widget');


class PromoVol_Widget extends WP_Widget {

	function PromoVol_Widget() {
		$widget_ops = array('classname' => 'promovol_social', 'description' => 'Widget des promos vol' );
		$this->WP_Widget('promol', 'Promo Vol', $widget_ops);

	}
 
	function widget($args, $instance) {
	
		extract($args, EXTR_SKIP);
		$rooturl = get_bloginfo('template_url');	
		include(TEMPLATEPATH .'/widget/volpromo.php');

	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);		
		return $instance;

	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
		
						
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title (internal use): <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
            
            <p>Please modify the file /widget/volpromo.php in the theme folder in order to add the new promo.</p>
            
          
           			
<?php
	}
}

register_widget('PromoVol_Widget');



class PromoCity_Widget extends WP_Widget {

	function PromoCity_Widget() {
		$widget_ops = array('classname' => 'promocity', 'description' => 'Widget for City Promotions' );
		$this->WP_Widget('promoc', 'Promo City', $widget_ops);

	}
 
	function widget($args, $instance) {
	
		extract($args, EXTR_SKIP);
		$rooturl = get_bloginfo('template_url');	
		include(TEMPLATEPATH .'/widget/citywidget.php');

	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);		
		return $instance;

	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
		
						
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title (internal use): <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
            
            <p>Please modify the file /widget/citywidget.php in the theme folder in order to add the new promo.</p>
            
          
           			
<?php
	}
}

register_widget('PromoCity_Widget');


class Siteinfo_Widget extends WP_Widget {

	function Siteinfo_Widget() {
		$widget_ops = array('classname' => 'siteinfo_social', 'description' => 'Widget avec les info sur le site' );
		$this->WP_Widget('siteinfol', 'Site Info', $widget_ops);

	}
 
	function widget($args, $instance) {
	
		extract($args, EXTR_SKIP);
		$rooturl = get_bloginfo('template_url');	
		include(TEMPLATEPATH .'/widget/sideinfo.php');

	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);		
		return $instance;

	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
		
						
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title (internal use): <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
            
            <p>Please modify the file /widget/siteinfo.php in the theme folder in order to change the values.</p>
            
          
           			
<?php
	}
}

register_widget('Siteinfo_Widget');


class Testimonial_Widget extends WP_Widget {

	function Testimonial_Widget() {
		$widget_ops = array('classname' => 'testimonial', 'description' => 'Widget with the sliding testimonials' );
		$this->WP_Widget('testimoniall', 'Testimonials', $widget_ops);

	}
 
	function widget($args, $instance) {
	
		extract($args, EXTR_SKIP);
		$rooturl = get_bloginfo('template_url');	
		include(TEMPLATEPATH .'/widget/testimonial.php');

	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);		
		return $instance;

	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
		
						
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title (internal use): <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
            
            <p>Please modify the file /widget/testimonial.php in the theme folder in order to change the text or refer to the po translated files.</p>
            
          
           			
<?php
	}
}

register_widget('Testimonial_Widget');


class Groupbook_Widget extends WP_Widget {

	function Groupbook_Widget() {
		$widget_ops = array('classname' => 'widget_groupboo', 'description' => 'Group booking link' );
		$this->WP_Widget('groupbook', 'Group Booking', $widget_ops);

	}
 
	function widget($args, $instance) {
	
		extract($args, EXTR_SKIP);
		
		echo '<div class="widget"><a title="'.__('Réserver une auberge de jeunesse pour un groupe').'" class="sidebar-groupe" href="'.get_option('aj_group_url').'">'.__('Réservation de groupes').'</a></div>';

	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
				
		return $instance;

	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
		
						
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
            <p>If you want to modiy the links setting, please go to the theme settings in the Appearance menu.</p>
            
          
           			
<?php
	}
}

register_widget('Groupbook_Widget');


class Helpcenter_Widget extends WP_Widget {

	function Helpcenter_Widget() {
		$widget_ops = array('classname' => 'widget_helpcenter', 'description' => 'Help center link' );
		$this->WP_Widget('helpcenter', 'Help Center Link', $widget_ops);

	}
 
	function widget($args, $instance) {
	
		extract($args, EXTR_SKIP);
		
		echo '<div class="widget"><a class="sidebar-help" href="'.get_option('aj_page_faq').'">'.__("Visiter notre centre d'aide").'</a></div>';

	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
				
		return $instance;

	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
		
						
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
            <p>If you want to modiy the links setting, please go to the theme settings in the Appearance menu.</p>
            
          
           			
<?php
	}
}

register_widget('Helpcenter_Widget');


class Guides_Widget extends WP_Widget {

	function Guides_Widget() {
		$widget_ops = array('classname' => 'widget_guides', 'description' => 'Guides page link' );
		$this->WP_Widget('guides', 'Guides Page Link', $widget_ops);

	}
 
	function widget($args, $instance) {
	
		extract($args, EXTR_SKIP);
		
		echo '<div class="widget"><a href="'.get_option('aj_page_guides').'"><img src="'.get_option('aj_api_url').'images/guide-link.jpg" alt="'.__("Visiter nos guides").'" /></a></div>';

	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
				
		return $instance;

	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
		
						
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
            <p>If you want to modiy the links setting, please go to the theme settings in the Appearance menu.</p>
            
          
           			
<?php
	}
}

register_widget('Guides_Widget');


?>