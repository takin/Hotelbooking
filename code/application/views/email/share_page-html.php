<html>
<head></head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor='#fff'>
<style type="text/css">
	body,td { color:#2f2f2f; font:12px/1.35em Arial, Helvetica, sans-serif; }
</style>

<table border="0" cellspacing="0" cellpadding="0" width="98%" style="width:98.0%; font-family:arial;">
	<tr>
		<td valign="top">
			<div align="center">
				<table border="0" cellspacing="0" cellpadding="0" width="650">
					<tr>
						<td valign="top">
							<p style="line-height:18px">
								<img style="margin-top:20px;" border=0 src="<?php echo base_url();?>images/email-head-share.gif" alt="Auberges De Jeunesse" />
							</p>
						</td>
						<td valign="middle">
						</td>
						<td valign="middle">
						</td>
					</tr>
				</table>
			</div>

			<br />

			<div align="center">
				<table  border="0" cellspacing="0" cellpadding="0" width="650" style="width:487.5pt;">
					<tr>
						<td valign="top">
							<p style="line-height:18px">
								<br />
								<span style="color: #003580"><?php echo sprintf(_("%s has sent you a message:"), var_check($from_name, '')); ?></span>
								<br /><br />
								<span style="color:#003580; margin-left:20px">"<?php echo var_check($message, ''); ?>"</span>
								<br /><br />
								<?php echo '<br />' .  _("Here is the link to see the prices, description, maps and much more:"); ?>
								<br /><br />
								<?php
									$property_url = site_url("/{$property_type}/{$property_name}/{$property_number}");

									$append = '';
									if (!empty($date)) {
										$append .= '/' . $date;

										if (!empty($nights) && is_numeric($nights)) {
											$append .= '/' . $nights;
										}
									}

									$property_url .= $append;
								?>
								<a href="<?php echo var_check($property_url, ''); ?>" style="color:#00a2e8"><?php echo var_check($property_url, ''); ?></a>

								<br /><br />
								<br /><br />
								<span style="color: #003580"><?php echo _('Thank you'); ?></span><br />
								<span style="color: #003580"><?php echo $site_name; ?></span><br />
								<br /><br /><br /><br />
								<span><?php echo _('PLEASE NOTE THIS IS NOT A CONFIRMED BOOKING'); ?></span>
							</p>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>
</body>
</html>
