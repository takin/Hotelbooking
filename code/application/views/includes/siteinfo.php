<?php if ($this->wordpress->get_option('aj_api_ascii')==""){$csspath = $this->wordpress->get_option('aj_api_name');}else{$csspath = $this->wordpress->get_option('aj_api_ascii');} ?>
<div class="box_content group box_round site-info-box">
	 <ul class="site-info">
		 <?php if ($this->wordpress->get_option('aj_api_site_data')=="hw"){?>
		 
		 <li class="first" id="rules"><img src="<?php echo site_url();?>images/<?php echo $csspath; ?>/sideinfo-rules.png" alt="" /><span> <?php printf(gettext("%s est réglementé par l'Union Européenne."),"<strong>".$this->config->item('site_name')."</strong>");?></span></li>
		 <li id="secure"><img src="<?php echo site_url();?>images/sideinfo-secure.png" alt="" /><span><strong><?php printf(gettext("100%% sécurisé."));?></strong> <?php echo _("Paiements sécurisés et encryptés pour votre sécurité.");?></span></li>
		 <li id="bestprice"><img src="<?php echo site_url();?>images/sideinfo-10percent.png" alt="" /><span><?php echo _("Seulement 10% pour garantir votre réservation.");?></span></li>
		<?php /*?> <li id="support"><img src="<?php echo site_url();?>images/sideinfo-support.png" alt="" /><span><?php printf(gettext("Un service clientèle de qualité disponible %s prêt à vous guider à tout moment."),"<b>"._("24h/24, 7j/7")."</b>");?></span></li><?php */?>
		 <li id="support"><img src="<?php echo site_url();?>images/sideinfo-support.png" alt="" /><br /><span><?php echo _('Text/SMS (FREE)')?></span></li>
		 <?php /*?><li id="forall"><img src="<?php echo site_url();?>images/sideinfo-forall.png" alt="" /><span><?php echo _("Pour tous les âges: ni maximum ni minimum.");?></span></li><?php */?>
		 <li id="forall"><img src="<?php echo site_url();?>images/sideinfo-forall.png" alt="" /><span><?php echo _('Check your reservation on your Mobile')?></span></li>
		 <li class="last" id="member"><img src="<?php echo site_url();?>images/sideinfo-member.png" alt="" /><span><?php printf(gettext("%s: Pas besoin de carte de membre pour recevoir les meilleurs prix du Net."),"<strong>".$this->config->item('site_name')."</strong>");?></span></li>
		 
		 <?php }elseif ($this->wordpress->get_option('aj_api_site_data')=="hb"){?>
		 
		 <li class="first" id="rules"><img src="<?php echo site_url();?>images/<?php echo $csspath; ?>/sideinfo-rules.png" alt="" /><span> <?php printf(gettext("%s est réglementé par l'Union Européenne."),"<strong>".$this->config->item('site_name')."</strong>");?></span></li>
		 <li id="secure"><img src="<?php echo site_url();?>images/hb-icons-secure.png" alt="" /><span><strong><?php printf(gettext("100%% sécurisé."));?></strong> <?php echo _("Paiements sécurisés et encryptés pour votre sécurité.");?></span></li>
		 <li id="bestprice"><img src="<?php echo site_url();?>images/hb-icons-10percent.png" alt="" /><span><?php echo _("Seulement 10% pour garantir votre réservation.");?></span></li>
		<?php /*?> <li id="support"><img src="<?php echo site_url();?>images/sideinfo-support.png" alt="" /><span><?php printf(gettext("Un service clientèle de qualité disponible %s prêt à vous guider à tout moment."),"<b>"._("24h/24, 7j/7")."</b>");?></span></li><?php */?>
		 <li id="support"><img src="<?php echo site_url();?>images/hb-icons-cell.png" alt="" /><br /><span><?php echo _('Text/SMS (FREE)')?></span></li>
		 <?php /*?><li id="forall"><img src="<?php echo site_url();?>images/sideinfo-forall.png" alt="" /><span><?php echo _("Pour tous les âges: ni maximum ni minimum.");?></span></li><?php */?>
		 <li id="nofee"><img src="<?php echo site_url();?>images/hb-icons-nofee.png" alt="" /><br /><span><?php echo _('No Booking fees')?></span></li>
		 <li class="last" id="member"><img src="<?php echo site_url();?>images/hb-icons-save.png" alt="" /><span><?php printf(gettext("%s: Pas besoin de carte de membre pour recevoir les meilleurs prix du Net."),"<strong>".$this->config->item('site_name')."</strong>");?></span></li>
		 
		 <?php }?>
		 
	 </ul>
</div>