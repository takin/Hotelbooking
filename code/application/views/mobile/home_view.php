<div id="content">
	<div class="button">
		<a class="white green-button large-button home-link round-corner5"  href="<?php echo site_url('m');?>"><?php echo _("Trouver une auberge");?></a>
		
		<?php echo login_check($this->tank_auth->is_logged_in(),"<a class=\"white green-button large-button home-link round-corner5\" href=\"".site_url($this->Db_links->get_link("user"))."\">"._("Bienvenue!")."</a>","<a class=\"white green-button large-button home-link round-corner5\" href=\"".site_url($this->Db_links->get_link("connect"))."\">"._("Se connecter")."</a>"); ?>    
		<h2 class="home-title"><?php echo _("Auberges de jeunesse, Hôtels, Appartements, Chambres d'hôtes, Bed and Breakfast, Pensions - Plus de 30000!!");?></h2>     
				
	</div>
</div>