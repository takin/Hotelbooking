<div id="booking_confirm_dialog">
	<div class="content_container">
        	<div class="title"><?php echo _('Please read carefully'); ?></div>
        	<div class="close">
			<a onclick="closeBookingFormConfirm(); return false" href="#">
				<img src="<?php echo site_url('images/modal_close.png'); ?>" alt="close" />
			</a>
		</div>

		<div class="content">
			<div class="content_important_info"></div>

			<br />
			<label><input type="radio" name="agree" value="1" /> <?php echo _('I agree'); ?></label>
			<br style="clear: both" />
			<label><input type="radio" name="agree" value="0" /> <?php echo _('I do not agree and I will select another property'); ?></label>
		</div>
	</div>
</div>
