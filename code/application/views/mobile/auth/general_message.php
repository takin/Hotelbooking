<div id="content" class="user-view">
	
	<div class="page-meta group">
		<h1 class="text-shadow-wrapper"><?php printf(gettext('Votre compte sur %s'),$this->config->item('site_name'));?></h1>
	</div>
	
	<div class="white-back round-corner5 border-around form"></h1>
	<p><?php echo $message; ?></p>
  <p><?php echo _("Retour à la"); ?> <a href="<?php echo site_url($this->Db_links->get_link("user"));?>"><?php echo _("page d'accueil");?> &raquo;</a></p>
  </div>

</div>