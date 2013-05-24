<div id="sidebar" class="grid_4">
	<?php $this->load->view('includes/navuser'); ?> 
</div>
<div id="main" class="grid_12">
	<div class="box_content box_round group">
		<h1 class="content_title"><?php echo _('Mon Compte - Réservation Auberges De Jeunesse');?></h1> 
		<?php if(isset($changes_success)){ ?>
		<div style="color:#80B422;"><?php echo _('Your changes has been saved successfully')?></div></br>
		<?php } ?>
		<p><?php echo _("Bienvenue dans votre compte. Vous avez maintenant accès à vos réservations d'Auberges de Jeunesse, à vos évaluations, à tous les contacts (emails, numéros de téléphone..) et à votre profil d'usager.");?></p>
         
      
         <ul class="account-nav clearfix">
         
           <li><a class="account-reserv" href="<?php echo site_url($this->Db_links->get_link("user_bookings"));?>"><?php echo _("Mes réservations"); ?></a></li>
           
           <li class="last-li"><a class="account-review" href="<?php echo site_url($this->Db_links->get_link("user_comments"));?>"><?php echo _("Mes évaluations"); ?></a></li>
           
           <li><a class="account-profile" href="<?php echo site_url($this->Db_links->get_link("user_profile"));?>"><?php echo _("Mon profil"); ?></a>
           <?php if ($this->config->item('displaySaveProperty')) { ?> 
           <li><a class="saved-properties" href="<?php echo site_url('user/favorite_properties'); ?>"><?php echo _("My favorite properties");?></a></li>
           <?php } ?>

           <li class="last-li"><a class="account-support" href="<?php echo $this->wordpress->get_option('aj_page_faq'); ?>"><?php echo _("Visiter notre centre d'aide");?></a></li>
           
           <?php /*?><li class="last-li"><span class="account-bus"><?php echo _("Bus"); ?></span></li><?php */?>
           
           <?php /*?><li><a class="account-groupe" href="http://reservations.bookhostels.com/aubergesdejeunesse.com/bookings/login.php"><?php echo _("Vos réservations de groupes"); ?></a></li>
           
           <li class="last-li"><a class="account-govoyages" href="mailto:infos@voyage-internet.com?subject=<?php echo _("Question sur ma réservation"); ?>&amp;body=<?php echo _("Mon numéro de dossier"); ?>:"><?php echo _("Réservation Hôtels, Avions, Week-end et Voitures de Location"); ?></a></li><?php */?>
         
                  
         </ul>
         
       <?php /*?><h2 class="blue-bar"><?php echo _('Votre Compte - R&eacute;servation H&ocirc;tels, Avions, Week-end et Voitures de Location');?></h2>
    	 <div class="entry">
          <?php echo utf8_encode("<p><b>Besoin d'aide pour les Réservations d’avions, d'hôtels, Weekend et voitures de location ?</b></p>
    
            	<p>Contactez nous par email à <b><a href=\"mailto:infos@voyage-internet.com\">infos@voyage-internet.com</a></b> ou par téléphone au  <b>+33 (0) 1 53 401 292</b> avec votre numéro de dossier (dans votre email de confirmation).</p>");?>
           
        </div><?php */?>
         
    </div>
</div>
