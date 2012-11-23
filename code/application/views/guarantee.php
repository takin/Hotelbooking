<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Guarantee</title>
<style type="text/css">
	#conditions {
		width:800px;
		padding:5px;
		background:#f9f9f9;
		border:1px solid #dddddd;
	}
	#conditions ul{
		list-style:disc outside;
		margin-left:25px;
		margin-top:10px;
		margin-bottom:10px;
	}
	
	#conditions ul li{
		list-style:disc outside;
		line-height:1.6em;
		margin-bottom:8px;
	}
	#conditions p.large-text{
		font-size:1.2em;
	}
	
</style>
</head>

<body>
	<div id="conditions">
		
		<?php $csspath = $this->wordpress->get_option('aj_api_ascii');?>
		<div style="float:left; width:145px;">
			<img src="<?php echo base_url();?>images/<?php echo $this->config->item('site_name'); ?>/guarantee.png" alt="" />
		</div>
		<div style="float:left; width:650px;">
			<h1><?php echo _('Booking Guarantee')?> <?php echo _('Up to $100')?></h1>
			<p class="large-text"><?php echo _('In the very unlikely event your selected property does not make a bed available to you, we will refund your deposit twice up to $100. * See Details.')?></p>
			<h2><?php echo _('Details:')?></h2>
			<ul>
			<li><?php echo _('You must request by email a refund within 24 hours of the arrival day noted in the confirmation email with the booking reference.')?></li>
			<li><?php echo _('Refund of the deposit will be made by credit card and/or by Paypal. Customer must have a valid Paypal account.')?></li>
			<li><?php echo _('Maximum refund per booking and per customer: US$100')?></li>
			<li><?php printf( gettext('All claims are subject to verification and/or final approval by %s.'),$this->config->item('site_name'));?></li>
			<li><?php printf( gettext('Offer only valid for deposits made by 08/31/2011 on %s.'),$this->config->item('site_name'));?></li>
			</ul>
		</div>
		<div class="clear"></div>
	</div>

</body>
</html>