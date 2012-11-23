<?php $rand = rand(1,10);?>
<div class="testimonial box_content box_round">
	
	<?php if($rand==1){?>
	<div>
  	<img class="box_shadow_light" src="<?php echo site_url();?>images/V2/slide-yasmin-riodejaneiro.jpg" alt="<?php echo _("Yasmine M, Rio De Janeiro");?>" />
    <p>"<?php printf( gettext("Merci %s pour votre aide."),$this->config->item('site_name'));?>"
      <span class="green">~ <?php echo _("Yasmine M, Rio De Janeiro");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==2){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo site_url();?>images/V2/slide-diego-mexico.jpg" alt="<?php echo _("Diego R, Mexico");?>" />
    <p>"<?php echo _("Génial le site. Et les prix sont imbattables. Merci !");?>"
      <span class="green">~ <?php echo _("Diego R, Mexico");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==3){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo site_url();?>images/V2/slide-lesley-boston.jpg" alt="<?php echo _("Lesley F, Boston");?>" />
    <p>"<?php echo _("Mes Vacances sont réussies grâce à vous. Les Auberges étaient comme promises.");?> "
      <span class="green">~ <?php echo _("Lesley F, Boston");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==4){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo site_url();?>images/V2/slide-jerome-paris.jpg" alt="<?php echo _("Jérome H, Marseille");?>" />
    <p>"<?php echo _("Notre école a réservé pour 35 personnes sur votre site et pas de surprise. Parfait !!! ");?>"
      <span class="green">~ <?php echo _("Jérome H, Marseille ");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==5){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo site_url();?>images/V2/slide-carmen-lima.jpg" alt="<?php echo _("Carmen J, Lima");?>" />
    <p>"<?php echo _("Choix, prix, sécurité, confirmation. Rien a dire : site super ! ");?>"
      <span class="green">~ <?php echo _("Carmen J, Lima");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==6){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo site_url();?>images/V2/slide-jowar-newdelhi.jpg" alt="<?php echo _("Jowar, New Dehli");?>" />
    <p>"<?php echo _("Les directions étaient bonnes et les prix des plus bas… Voyage réussie. Encore Merci.");?>"
      <span class="green">~ <?php echo _("Jowar, New Dehli");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==7){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo site_url();?>images/V2/slide-olivia-london.jpg" alt="<?php echo _("Olivia S, Londres");?>" />
    <p>"<?php echo _("Les cartes et les guides m'ont aidée. Votre site est facile a naviguer.");?>"
      <span class="green">~ <?php echo _("Olivia S, Londres");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==8){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo site_url();?>images/V2/slide-michal-varsovie.jpg" alt="<?php echo _("Michal E, Varsovie");?>" />
    <p>"<?php echo _("Ma fiancée et moi ont passe des supers vacances en réservant sur votre site.");?>"
      <span class="green">~ <?php echo _("Michal E, Varsovie");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==9){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo site_url();?>images/V2/slide-hiroto-seoul.jpg" alt="<?php echo _("Hiroto V, Seoul");?>" />
    <p>"<?php echo _("Très content de vos services.");?>"
      <span class="green">~ <?php echo _("Hiroto V, Seoul");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==10){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo site_url();?>images/V2/slide-ali-casablanca.jpg" alt="<?php echo _("Ali S, Casablanca");?>" />
    <p>"<?php echo _("Les cartes sont indispensables et très pratiques.");?>"
      <span class="green">~ <?php echo _("Ali S, Casablanca");?></span>
    </p>
  </div>
	
	<?php }?> 
  
</div>
<?php /*?><script type="text/javascript">
	$('#slide-test').cycle({
		fx:      'fade',
		timeout:  6000,
		speed:  2000	
		
	});

</script><?php */?>