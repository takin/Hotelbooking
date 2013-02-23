<html>
<head></head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor='#e3ecf2'>
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
								<img style="margin-top:20px;" border=0 src="<?php echo base_url();?>images/email-head.gif" alt="Auberges De Jeunesse" />
							</p>
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
								<strong style="font-size:18px;color: #003580"><?php echo var_check($from_name, ''); ?> <?php echo _('send you a message:'); ?></strong>
								<br /><br />
								<i style="font-size:16px;color:#003580; margin-left:20px">"<?php echo var_check($message, ''); ?>"</i>
								<br /><br />
								<?php $property_url = site_url("/{$property_type}/{$property_name}/{$property_number}"); ?>
								<a href="<?php echo var_check($property_url, ''); ?>" style="font-size:16px;color:#00a2e8"><?php echo var_check($property_url, ''); ?></a>

								<br /><br />
								<br /><br />
								<span style="color: #003580"><strong><?php echo _('Like what you see?'); ?></strong></span><br />
								<span style="color: #003580"><strong>Visit <a style="color:#00a2e8" href="<?php echo site_url('/'); ?>"><?php echo site_url('/'); ?></a> or <a style="color:#00a2e8" href="<?php echo site_url('/bienvenue'); ?>">signup</a></strong> to get 70% off of select hotels.</span><br />
								<div background="<?php echo base_url();?>images/subscribe.png" style="background-image: url('<?php echo base_url();?>images/subscribe.png'); width:680px; height: 87px"><a style="text-decoration: none" href=""><span style="line-height: 87px; margin-left: 50px;width: 380px; overflow: hidden;display: block;color: #666"><?php echo var_check($to_email, '') ?></span></a></div>
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
