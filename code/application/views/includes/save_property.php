<script id="template-save-favorite" type="text/html">
<?php
  $this->load->view('mustache/save_property_dialog');
?>
</script>

<script type="text/javascript">
        var userIsLoggedIn = <?php echo empty($userIsLoggedIn) ? 'false' : 'true'; ?>;
</script>

<div id="save_property_dialog">
	<div class="content_container">
        	<div class="title"><?php echo _('Save this property as a favorite'); ?></div>
        	<div class="close"><a href="#"><img src="<?php echo site_url('images/modal_close.png'); ?>" alt="close" /></a></div>

		<div class="content"></div>
	</div>
</div>
