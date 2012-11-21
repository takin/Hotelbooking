</div>
<div id="footer">
	<div class="footer-content">
		<ul class="group">
			<li><a href="<?php echo get_option('aj_api_url'); ?>m"><?php echo _("Trouver une auberge");?></a></li>
			<li class="dot"><a href="<?php echo get_option('aj_api_url'); ?>?site=full"><?php echo _("View Full Site");?></a></li>
			<?php $about = get_option('aj_page_about'); if (!empty($about)){?><li class="dot"><a href="<?php echo $about; ?>"><?php echo _("About us");?></a></li><?php }?>
			<li class="dot"><a href="<?php echo get_option('aj_page_faq');?>"><?php _e('Aide / FAQ / Nous Joindre','auberge');?></a></li>
			<li class="dot"><a href="<?php echo get_option('aj_page_conf');?>"><?php _e('Confidentialité','auberge');?></a></li>
			<li class="dot"><a href="<?php echo get_option('aj_page_cond');?>"><?php _e('Conditions','auberge');?></a></li>
		</ul>
	</div>
</div>
<?php echo get_option('aj_google_analytic');?>
</body>
</html>