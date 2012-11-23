<div id="content" class="user-view">
	
	<div class="page-meta group">
		<h1 class="text-shadow-wrapper icon-user"><?php echo _("Bienvenue!");?><?php //echo $user_profile->first_name;?></h1>
	</div>
	
	
		<a class="white green-button large-button round-corner5 home-link"  href="<?php echo site_url($this->Db_links->get_link("user_bookings"));?>"><span class="link"><?php echo _("Voir vos réservation"); ?></span></a>
	
	
		<a class="white green-button large-button round-corner5 home-link" href="<?php echo site_url($this->Db_links->get_link("user_profile"));?>"><span class="link"><?php echo _("Voir votre profil"); ?></span></a>
	
 
</div>