<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head id="headcontainer">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<title><?php dynamictitles();?></title>
<meta name="copyright" content="<?php bloginfo('name');?>" />
<?php if (get_option('aj_api_ascii')==""){$csspath = get_option('aj_api_name');}else{$csspath = get_option('aj_api_ascii');} ?>
<link rel="stylesheet" href="<?php echo get_option('aj_api_url'); ?>css/mobile/main.css" type="text/css" media="screen" charset="utf-8" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo get_option('aj_api_url'); ?>js/mobile/tools.js"></script>
<?php if (is_page_template('contact.php')){?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/submitform.js"></script>
<?php }?>
</head>

<body>
<div id="wrapper">
<div id="header">
	<a href="<?php echo get_option('aj_api_url');?>"><img class="logo" src="<?php echo get_option('aj_api_url');?>images/mobile/logo/<?php echo $csspath;?>.png" alt=""/></a>
	<a id="show-menu" class="show-menu" href="#">Menu</a>
</div>
<div class="main-menu">
	<a class="white green-button full-width large-button"  href="<?php echo get_option('aj_api_search'); ?>"><span class="light"></span><span class="link"><?php echo _("Accueil");?></span></a>
	<a class="white green-button full-width large-button"  href="<?php echo get_option('aj_api_url'); ?>m"><span class="light"></span><span class="link"><?php echo _("Trouver une auberge");?></span></a>

	<?php if(is_logged_in()):?>
		<a class="white green-button full-width large-button" href="<?php echo get_option('aj_api_url'). get_ci_link('user');?>"><span class="light"></span><span class="link"><?php _e('Mon Compte','auberge');?></span></a>
	<?php else:?>
		<a class="white green-button full-width large-button" href="<?php echo get_option('aj_api_url'). get_ci_link('connect');?>"><span class="light"></span><span class="link"><?php _e('Se connecter','auberge');?></span></a>
	<?php endif;?>

</div>