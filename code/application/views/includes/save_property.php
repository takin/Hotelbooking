<script id="template-save-favorite" type="text/html">
<?php
  $this->load->view('mustache/save_property_dialog');
?>
</script>

<div id="save_property_dialog">
	<div class="content_container">
        	<div class="title"><?php echo _('Save this property as favorite'); ?></div>
        	<div class="close"><a href="#">close</a></div>

		<div class="content"></div>
	</div>
</div>
