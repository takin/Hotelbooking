<?php adword_init(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php dynamictitles();?></title>
<meta http-equiv="Content-Language" content="<?php echo get_option('aj_lang_code')?>" />
<?php wp_head(); ?>
<?php if (is_single() || is_page(array(get_option('aj_no_seo')))){
echo '<meta name="robots" content="noindex,follow" />';
}?>
<?php /*?><?php if((is_home() || is_single() || is_page()) && (!(is_page_template('canada-page.php'))))
{ echo '<meta name="robots" content="index,follow" />'; }
else { echo '<meta name="robots" content="noindex,follow" />'; } ?><?php */?>

<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/favicon.ico" />
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/nostyle.css" type="text/css" media="screen,projection" />

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js"></script>
<?php if (is_page_template('contact.php')){?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/submitform.js"></script>
<?php }?>

</head>

<body <?php body_class(); ?>>

<div id="content" class="clearfix">
