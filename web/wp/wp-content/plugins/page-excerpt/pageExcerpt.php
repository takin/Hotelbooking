<?php
/*
Plugin Name: Page Excerpt
Plugin URI: http://masseltech.com/plugins/page-excerpt/
Description: Adds support for page excerpts - uses WordPress code
Author: Jeremy Massel
Version: 1.0
Author URI: http://www.masseltech.com/
*/

add_action( 'edit_page_form', 'pe_add_box');


function pe_page_excerpt_meta_box($post) {
?>
<label class="hidden" for="excerpt"><?php _e('Excerpt') ?></label><textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt"><?php echo $post->post_excerpt ?></textarea>
<p><?php _e('Excerpts are optional hand-crafted summaries of your content. You can <a href="http://codex.wordpress.org/Template_Tags/the_excerpt" target="_blank">use them in your template</a>'); ?></p>
<?php
}


function pe_add_box()
{
	add_meta_box('postexcerpt', __('Page Excerpt'), 'pe_page_excerpt_meta_box', 'page', 'advanced', 'core');
}

?>