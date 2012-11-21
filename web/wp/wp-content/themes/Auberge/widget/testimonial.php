<?php $rand = rand(1,10);?>
<div class="testimonial box_content box_round" id="slide-test">
	<?php if($rand==1){?>
	<div>
  	<img class="box_shadow_light" src="<?php echo get_option('aj_api_url')?>images/V2/slide-yasmin-riodejaneiro.jpg" alt="<?php _e("Yasmine M, Rio De Janeiro","auberge");?>" />
    <?php /*?><p>"<?php  _e("Merci %s pour votre aide.",get_option('aj_api_name'),"auberge",);?>"<?php */?>
    <p>"<?php  _e("Merci pour votre aide.",get_option('aj_api_name'),"auberge");?>"
      <span class="green">~ <?php _e("Yasmine M, Rio De Janeiro","auberge");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==2){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo get_option('aj_api_url')?>images/V2/slide-diego-mexico.jpg" alt="<?php _e("Diego R, Mexico","auberge");?>" />
    <p>"<?php _e("Génial le site. Et les prix sont imbattables. Merci !","auberge");?>"
      <span class="green">~ <?php _e("Diego R, Mexico","auberge");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==3){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo get_option('aj_api_url')?>images/V2/slide-lesley-boston.jpg" alt="<?php _e("Lesley F, Boston","auberge");?>" />
    <p>"<?php _e("Mes Vacances sont réussies grâce à vous. Les Auberges étaient comme promises.","auberge");?> "
      <span class="green">~ <?php _e("Lesley F, Boston","auberge");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==4){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo get_option('aj_api_url')?>images/V2/slide-jerome-paris.jpg" alt="<?php _e("Jérome H, Marseille","auberge");?>" />
    <p>"<?php _e("Notre école a réservé pour 35 personnes sur votre site et pas de surprise. Parfait !!! ","auberge");?>"
      <span class="green">~ <?php _e("Jérome H, Marseille ","auberge");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==5){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo get_option('aj_api_url')?>images/V2/slide-carmen-lima.jpg" alt="<?php _e("Carmen J, Lima","auberge");?>" />
    <p>"<?php _e("Choix, prix, sécurité, confirmation. Rien a dire : site super ! ","auberge");?>"
      <span class="green">~ <?php _e("Carmen J, Lima","auberge");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==6){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo get_option('aj_api_url')?>images/V2/slide-jowar-newdelhi.jpg" alt="<?php _e("Jowar, New Dehli","auberge");?>" />
    <p>"<?php _e("Les directions étaient bonnes et les prix des plus bas… Voyage réussie. Encore Merci.","auberge");?>"
      <span class="green">~ <?php _e("Jowar, New Dehli","auberge");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==7){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo get_option('aj_api_url')?>images/V2/slide-olivia-london.jpg" alt="<?php _e("Olivia S, Londres","auberge");?>" />
    <p>"<?php _e("Les cartes et les guides m'ont aidée. Votre site est facile a naviguer.","auberge");?>"
      <span class="green">~ <?php _e("Olivia S, Londres","auberge");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==8){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo get_option('aj_api_url')?>images/V2/slide-michal-varsovie.jpg" alt="<?php _e("Michal E, Varsovie","auberge");?>" />
    <p>"<?php _e("Ma fiancée et moi ont passe des supers vacances en réservant sur votre site.","auberge");?>"
      <span class="green">~ <?php _e("Michal E, Varsovie","auberge");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==9){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo get_option('aj_api_url')?>images/V2/slide-hiroto-seoul.jpg" alt="<?php _e("Hiroto V, Seoul","auberge");?>" />
    <p>"<?php _e("Très content de vos services.","auberge");?>"
      <span class="green">~ <?php _e("Hiroto V, Seoul","auberge");?></span>
    </p>
  </div>
  <?php }?>
	<?php if($rand==10){?>
  <div>
  	<img class="box_shadow_light" src="<?php echo get_option('aj_api_url')?>images/V2/slide-ali-casablanca.jpg" alt="<?php _e("Ali S, Casablanca","auberge");?>" />
    <p>"<?php _e("Les cartes sont indispensables et très pratiques.","auberge");?>"
      <span class="green">~ <?php _e("Ali S, Casablanca","auberge");?></span>
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