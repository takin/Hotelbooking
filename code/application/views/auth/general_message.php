<div id="main" class="grid_16 user-auth">
	<div class="box_content box_round group">
		 <h1 class="content_title"><?php printf(gettext('Votre compte sur %s'),$this->config->item('site_name'));?></h1>
		 <p><strong><?php echo $message; ?></strong></p>
		 <p><?php echo _("Retour à la"); ?> <a href="<?php echo base_url();?>"><?php echo _("page d'accueil");?> &raquo;</a></p>
	</div>
</div>