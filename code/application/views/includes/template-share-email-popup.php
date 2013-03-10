<script type="text/javascript" src="/js/livevalidation_standalone.compressed.js" charset="UTF-8"></script>

<div id="share-overlay">
	<div class="content">
		<div class="confirmation">
			<p class="title"><?php echo sprintf(_('Your message has been sent to %s'), '<br /><br ><span id="email_recipient"></span>'); ?></p>
			<br /><br />

			<table>
				<tr>
					<td class="label"><?php echo _('From:'); ?></td>
					<td><strong id="from_feedback" style="width:300px; overflow:hidden;display:block"></strong></td>
				</tr>
				<tr>
					<td class="label"><?php echo _('To:'); ?></td>
					<td><strong id="to_feedback" style="width:300px; overflow:hidden;display:block"></strong></td>
				</tr>
				<tr>
					<td class="label"><?php echo _('Subject:'); ?></td>
					<td id="subject_feedback" style="width:300px; overflow:hidden;display:block"></td>
				</tr>
				<tr>
					<td class="label"><?php echo _('Message:'); ?></td>
					<td id="message_feedback" style="width:300px; overflow:hidden;display:block"></td>
				</tr>
			</table>

			<br />

			<p style="margin-left: 220px">
				<a href="/" id="show-share-overlay"><?php echo _('Send another message'); ?></a>
				<a href="/" id="close-share-conformation_overlay"><?php echo _('Close'); ?></a>
			</p>
		</div>

		<form action="<?php echo site_url("cmain/property_send_email"); ?>" method="post" id="share_email_form">
			<p class="title"><?php echo _('Mail this page to someone you know, or send it to yourself as a reminder.'); ?></p>

			<div class="form-item">
				<label><?php echo _('To (email)'); ?></label>
				<input type="text" name="to_email" id="to_email" class="input" />
			</div>

			<div class="form-item">
				<label><?php echo _('Subject'); ?></label>
				<input type="text" name="subject" id="subject" class="input" />
			</div>

			<div class="form-item">
				<label><?php echo _('Message'); ?></label>
				<textarea name="message" id="message" class="input textarea" rows="4" cols="60"></textarea>
			</div>

			<div class="form-item">
				<hr />
				<label><?php echo _('From (name)'); ?></label>
				<input type="text" name="from_name" id="from_name" class="input" />
			</div>

			<div class="form-item">
				<label><?php echo _('From (email)'); ?></label>
				<input type="text" name="from_email" id="from_email" class="input" />
			</div>

			<!-- <div class="form-item2">
				<label for="subscribe"><input type="checkbox" name="subscribe" id="subscribe" checked="checked" /> <?php echo _('Yes, I want to subscribe to the newsletter to get great deals. (You can unsubscribe anytime)'); ?></label>
			</div> -->

			<div class="form-item2">
				<hr />
				<input type="hidden" name="with_pdf" id="email_send_pdf" value="0" />
				<input type="hidden" name="property_type" value="<?php echo var_check($property_type, ''); ?>" id="property_type" />
				<input type="hidden" name="property_name" value="<?php echo var_check($property_name, ''); ?>" id="property_name" />
				<input type="hidden" name="property_number" value="<?php echo var_check($property_number, ''); ?>" id="property_number" />

				<input type="submit" name="submit" id="submit" value="<?php echo _('Send email'); ?>" />
				<a href="/" id="close-share-overlay"><?php echo _('Close'); ?></a>
			</div>
		</form>

		<img src="<?php echo site_url("images/share_pdf.png"); ?>" alt="<?php echo _('Share PDF'); ?>" id="email_show_pdf" style="margin-top: 30px" />
	</div>
</div> 
