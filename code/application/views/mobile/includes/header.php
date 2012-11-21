<!DOCTYPE html>
<html lang="<?php echo $this->html_lang_code; ?>" xmlns="http://www.w3.org/1999/xhtml">
<head id="headcontainer">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />

<title><?php echo isset($title) ? my_mb_ucfirst($title) : $this->config->item('site_title'); ?></title>
	<?php if ($this->wordpress->get_option('aj_block_bot')){
		echo '<meta name="robots" content="noindex,follow" />';
	}?>
  
<meta name="copyright" content="<?php echo $this->config->item('site_name');?>" />
<meta name="keywords" content="<?php echo _("reservation auberge de jeunesse,auberge de jeunesse,auberges de jeunesse,voyage jeunesse europe,voyage jeunesse,voyage europe,hébergement voyage europe,réserver auberge,voyage europe logement,logement voyage france,logement voyage europe,trip jeunesse euro,euro youth hostel,révervation hôtel londres,reservation hôtel paris");?>" />
<?php if($current_view == "city_view"){?>
<?php if (isset($bc_city)){?>
<meta name="description" content="<?php printf( gettext("Auberges à %s incluant Auberges de Jeunesse à %s. 30,000 Auberges de jeunesses et logements pas chers à %s et dans le monde entier. Aussi, cartes des villes, photos, conseils, événements et guides des Auberges de Jeunesse à %s."),my_mb_ucfirst($bc_city),my_mb_ucfirst($bc_city),my_mb_ucfirst($bc_city),my_mb_ucfirst($bc_city));?>"/>  
<?php }}?>
<?php if($current_view == "hostel_view"){?>
<?php if(($api_error==false) && isset($hostel->propertyName[0])){?>
<meta name="description" content="<?php printf( gettext("Réservation immédiate et garantie de l’auberge %s à %s. Prix imbattables de %s, photos, cartes & guides sur %s."),$hostel->propertyName[0],my_mb_ucfirst($bc_city),$hostel->propertyName[0],$this->config->item('site_name'));?>"/> 
<?php }}?>
<?php if ($this->wordpress->get_option('aj_api_ascii')==""){$csspath = $this->wordpress->get_option('aj_api_name');}else{$csspath = $this->wordpress->get_option('aj_api_ascii');} ?>
<?php $this->carabiner->display('css');?>
<?php $this->carabiner->display('jquery');?>
<?php $this->carabiner->display('mobile_main_site');?>
<?php $this->carabiner->display('js');?>

</head>

<body>
<?php if ($current_view != 'map_view'){?>
<div id="wrapper">
<div id="header">
	<a href="<?php echo site_url();?>"><img class="logo" src="<?php echo secure_base_url();?>images/mobile/logo/<?php echo $csspath;?>.png" alt=""/></a>
	<a id="show-menu" class="show-menu" href="#">Menu</a>
</div>
<div class="main-menu">
	<a class="white green-button full-width large-button"  href="<?php echo site_url();?>"><span class="light"></span><span class="link"><?php echo _('Home page')?></span></a>
	<?php /*?><a class="white green-button full-width large-button"  href="<?php echo site_url('m');?>"><span class="light"></span><span class="link"><?php echo _("Trouver une auberge");?></span></a><?php */?>
	<?php echo login_check($this->tank_auth->is_logged_in(),"<a class=\"white green-button full-width large-button\" href=\"".site_url($this->Db_links->get_link("user"))."\"><span class=\"light\"></span><span class=\"link\">"._("Bienvenue!")."</span></a>","<a class=\"white green-button full-width large-button\" href=\"".site_url($this->Db_links->get_link("connect"))."\"><span class=\"light\"></span><span class=\"link\">"._("Se connecter")."<span></a>"); ?>      
		
</div>
<?php }?>