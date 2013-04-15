<?php
//------------check to display the box or not
if ($this->config->item('recent_view_number_cookies') > 0) { ?>
	<div id="recently_viewed_properties" style="display: none;"></div>

	<script type="text/javascript">
		$(document).ready(function() {
			$.ajax({
				type    : "POST",
				cache   : false,
				url     : '<?php echo site_url("cmain/ajax_recently_viewed_property/");?>',
				success : function(retdata) {
					$('#recently_viewed_properties').show();
					$('#recently_viewed_properties').html(retdata);
				}
			});
		});
	</script>
<?php } ?>
