<div id="sidebar" class="grid_4">
	
<?php if($GLOBALS['print']!='nostyle'){?>
<?php include 'widget/map-search.php';?>
<?php include 'widget/group-booking.php';?>
<?php include 'widget/video-popup.php';?>
<?php include 'widget/video.php';?>
<?php if (is_home() || is_page_template('canada-page.php')) { if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Home Page Sidebar') ) : endif;}
		elseif (is_page_template('page-eve.php')) { if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Evenements Page Sidebar') ) : endif;}
		elseif (is_page_template('contact.php') || is_page_template('pagecopy.php') || is_page_template('pagehelp.php')) { if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Static Page Sidebar') ) : endif;}
		elseif (is_page_template('page-media.php')) { if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Media Page Sidebar') ) : endif; }
		elseif (is_page_template('page-blog.php')) { if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Blog Page Sidebar') ) : endif; }
		elseif (is_page()) { if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Single Page Sidebar') ) : endif;	 }
		elseif (is_single()) { if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Single Article Sidebar') ) : endif;	 }
		elseif (is_archive() ) { if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Archive Page Sidebar') ) : endif;	 }
		elseif (is_search()) { if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Search Page Sidebar') ) : endif;	 }
		elseif (is_404()) {if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('404 Page Sidebar') ) : endif; }
		else{?>		
		<?php }
}
?>
</div><!--/sidebar-->
