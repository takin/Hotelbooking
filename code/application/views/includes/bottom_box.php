<div id="warning" class="warning" style="display: <?php echo isset($warning) ? 'block;' : 'none;';?>">
  <p><?php if(isset($warning_message)) echo $warning_message; ?></p>
  </div>


  <div id="second-container">

	<?php if(!empty($bc_continent)):?>
	<ul id="bread-crumb" class="clearfix">

		<?php /*?><li class="first"><a href="<?php echo base_url(); ?>"><?php echo $this->config->item('site_name');?></a></li><?php */?>
		<li class="first"><a href="<?php echo site_url(customurlencode($bc_continent));?>"><?php printf(gettext('%s-Map'),my_mb_ucfirst($bc_continent));?><?php //echo my_mb_ucfirst($bc_continent); ?></a></li>

			<?php if(!empty($bc_country)):?>
			<li><a href="<?php echo site_url(customurlencode($bc_continent)."/".customurlencode($bc_country));?>"><?php printf(gettext('%s-Map'),my_mb_ucfirst($bc_country)); ?></a></li>

				<?php if(!empty($bc_city)):?>
					<li><a href="<?php echo site_url(customurlencode($bc_country)."/".customurlencode($bc_city)."/"); ?>"><?php printf(gettext('%s-Map'),my_mb_ucfirst($bc_city));?></a></li>
				<?php endif; //ville?>

			<?php endif; //pays?>


	</ul>
	<?php else:?>

	<ul id="bread-crumb" class="clearfix">
		 <!-- Bread Crumb function-->
		<li style="background:none; list-style:none;">&nbsp;</li>
		<?php /*?><li class="first"><a href="<?php echo site_url();?>"><?php printf(gettext("Bienvenue sur %s !"),$this->config->item('site_name'));?></a></li><?php */?>
	</ul>

	<?php endif; //continent?>
	<ul id="account-mainbar">
           
		<?php 
		$displayVelaro = $this->config->item('displayVelaro');
		if($displayVelaro==1)
		{
			if ($this->wordpress->get_option('aj_velaro_id') !='')
			{
		?>
		<li><a style="padding:0px;" href="http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue" target="OnlineChatSoftware"  onClick="this.newWindow = window.open('http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue', 'OnlineChatSoftware', 'toolbar=no,location=no,directories=no,menubar=no,status=no,scrollbars=no,resizable=yes,replace=no');this.newWindow.focus();this.newWindow.opener=window;return false;"><img alt="OnlineChatSoftware" src="http://service.velaro.com/visitor/check.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue" border="0"></a></li>
		<?php }else{?>
		<li><a style="padding:0px;" href="http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&showwhen=inqueue" target="OnlineChatSoftware"  onClick="this.newWindow = window.open('http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&showwhen=inqueue', 'OnlineChatSoftware', 'toolbar=no,location=no,directories=no,menubar=no,status=no,scrollbars=no,resizable=yes,replace=no');this.newWindow.focus();this.newWindow.opener=window;return false;"><img alt="OnlineChatSoftware" src="http://service.velaro.com/visitor/check.aspx?siteid=7548&showwhen=inqueue" border="0"></a></li>
		<?php }  } ?>

		<?php /*?><li><a class="your-account" href="<?php echo site_url($this->Db_links->get_link("user")); ?>"><?php echo _("Mon Compte");?></a></li>
		<?php  if($this->wordpress->get_option('aj_group_url') != ''){?>
		<li><a title="<?php echo _("Réservation d'auberges de jeunesse pour les groupes");?>" href="<?php echo $this->wordpress->get_option('aj_group_url'); ?>" class="group-reserve"><?php echo _("Groupes");?></a></li>
		<?php } ?><?php */?>

		</ul>
