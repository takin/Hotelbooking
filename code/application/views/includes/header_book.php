<!DOCTYPE html>
<html lang='<?php echo $this->html_lang_code; ?>'>
<head>
<meta charset="utf-8" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<title><?php echo isset($title) ? $title : ''; ?></title>
<?php if ($this->wordpress->get_option('aj_api_ascii')==""){$csspath = $this->wordpress->get_option('aj_api_name');}else{$csspath = $this->wordpress->get_option('aj_api_ascii');} ?>
<?php
$this->carabiner->css('reset.css','screen','reset.css',FALSE,FALSE,"full_site_global");
$this->carabiner->css('mainv2.css','screen','mainv2.css',FALSE,FALSE,"full_site_global");
$this->carabiner->css('print.css','print','print.css',FALSE,FALSE,"full_site_global");
$this->carabiner->css('tools.css','screen','tools.css',FALSE,FALSE,"full_site_global");
$this->carabiner->css('fancybox.css','screen','fancybox.css',FALSE,FALSE,"full_site_global");
?>
<?php if ($this->wordpress->get_option('aj_api_site_data') == 'hb'){
 //$this->carabiner->css('hostels.css','screen','hostels.css',FALSE,FALSE,"full_site_global");
}
//$this->carabiner->css($csspath.'/more.css');
if($this->api_used == HB_API)
{?>
<link rel="shortcut icon" href="<?php echo secure_base_url();?>images/favicon-hb.ico" />
<?php }else{?>
<link rel="shortcut icon" href="<?php echo secure_base_url();?>images/favicon.ico" />
<?php }?>
<?php
$this->carabiner->display('css');
$this->carabiner->display('full_site_global');?>
 <!--[if IE]>
		<link rel="stylesheet" type="text/css" href="<?php echo secure_base_url();?>css/ie.css" />
  <![endif]-->
	<!--[if IE 6]>
		<link rel="stylesheet" type="text/css" href="<?php echo secure_base_url();?>css/ie6.css" />
  <![endif]-->
  <!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="<?php echo secure_base_url();?>css/ie7.css" />
  <![endif]-->

<!--[if lt IE 9]>
<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?php

$this->carabiner->display('jqueryui');
$this->carabiner->js('imageload.js');
$this->carabiner->js('jtools.js');
$this->carabiner->js('popup.js');
//$this->carabiner->js('preview.js');
$this->carabiner->js('sitetools.js');
$this->carabiner->js('livevalidation_standalone.compressed.js');
//$this->carabiner->js('jquery.easing-1.3.pack.js');
//$this->carabiner->js('jquery.fancybox-1.3.4.pack.js');
//$this->carabiner->js('jquery.mousewheel.js');

?>
<script type="text/javascript" src="<?php echo secure_base_url();?>js/jquery.easing-1.3.pack.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo secure_base_url();?>js/jquery.fancybox-1.3.4.pack.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo secure_base_url();?>js/jquery.mousewheel.js" charset="UTF-8"></script>

<script type="text/javascript">

	$(document).ready(function(){
		$("body").addClass("has-script");
		$('a.screenshot').live('click',function(){
			$.fancybox({'href': $(this).attr('href')});
			return false;
		});
		$('a.popup').live('click',function(){
			$.fancybox({
				'width' : 680,
				'height' : 495,
				'autoScale' : false,
				'type' : 'iframe',
				'href': $(this).attr('href')
			});
			return false;
		});

		$("a.show-translate").live('click',function() {
			$("a.show-translate").toggleClass('active');
			$("a.show-original").toggleClass('active');
			$(".original").fadeOut(500, function () { $(".translated").fadeIn(500); });
			return false;
		});


		$("a.show-original").live('click',function() {
			$("a.show-original").toggleClass('active');
			$("a.show-translate").toggleClass('active');
			$(".translated").fadeOut(500, function () { $(".original").fadeIn(500); });
			return false;
		});

		$('.question_mark span').click(function() {
		return false;
		});
		$('.question_mark span').mouseover(function() {
		$(this).parent().next().show();
		});
		$('.question_mark span').mouseleave(function() {
		$(this).parent().next().hide();
		});

		$('a.show-room-info').click(function() {
			return false;
		});
		$('a.show-room-info').mouseover(function() {
			$(this).next().show();
		});
		$('a.show-room-info').mouseleave(function() {
			$(this).next().hide();
		});

	});

	function check_error(){
		$('#check_error').hide();
		$('#firstname').blur();
		if($('select').hasClass('LV_invalid_field') || $('input').hasClass('LV_invalid_field')){
			$('#check_error').show();
			return false;
		}else{
			return true;
		}
	}

	</script>

<?php $this->carabiner->display('js');?>
<?php echo $this->wordpress->get_option('aj_google_analytic'); ?>
</head>

<body class="booking-view<?php if($this->api_used == HB_API){echo ' hb_frame';}?>">
<div id="top_bar">
	<div id="top_bar_inner" class="container_16 group">
		<div class="grid_16">
			<ul class="user_meta_top group">
				<li><a class="meta_help" href="<?php echo $this->wordpress->get_option('aj_page_faq'); ?>"><?php echo _("Aide / FAQ / Nous Joindre");?></a></li>
				<li>
					<?php echo login_check($this->tank_auth->is_logged_in(),"<a class=\"meta_account\" href=\"".site_url($this->Db_links->get_link("user"))."\">"._("Bienvenue!")."</a>","<a class=\"meta_login\" href=\"".site_url($this->Db_links->get_link("connect"))."\">"._("Se connecter")."</a>"); ?>
				</li>
				<li class="last">
					<?php echo login_check($this->tank_auth->is_logged_in(),"<a class=\"meta_logout\" href=\"".site_url($this->Db_links->get_link("logout"))."\">"._("Se déconnecter")."</a>","<a class=\"meta_register\" href=\"".site_url($this->Db_links->get_link("register"))."\">"._("S'enregistrer")."</a>"); ?>
				</li>
			</ul>
		</div>
	</div>
</div>
	<div id="wrapper" class="container_16 group">
		<header class="grid_16 header_v2">
			<a class="logo" title="<?php echo _("Plus de 30,000 Auberges de Jeunesse disponible en ligne");?>" href="<?php echo site_url (); ?>"><img src="<?php echo secure_site_url (); ?>images/<?php echo $csspath;?>/logo.png" class="logo" alt="<?php echo $this->wordpress->get_option('aj_api_name');?>"></a>

			<div class="bubble_blue_position<?php if($this->wordpress->get_option('aj_api_site_data') == 'hb'){echo ' hb_bubble';}?>">
				<div class="bubble_blue">
					<span class="bubble_blue_inner"><?php echo _('Free SMS')?></span>
				</div>
			</div>

			<div class="bubble_blue_right_position<?php if($this->wordpress->get_option('aj_api_site_data') == 'hb'){echo ' hb-bubble';}?>">
				<div class="bubble_blue_right">
					<span class="bubble_blue_right_inner"><?php if($this->wordpress->get_option('aj_api_site_data') == 'hb'){?><?php echo _('No Booking fees')?><?php }else{?><?php echo _('Book on your mobile')?><?php }?></span>
				</div>
			</div>

		</header>

		<nav class="main grid_16 box_round box_shadow box_gradient_dark_blue">
			<ul class="group">
				<?php /*?><li class="right"><a class="icon-chathelp" href="">Live Chat Help</a></li>			<?php */?>
				<?php 
				$displayVelaro = $this->config->item('displayVelaro');
				if($displayVelaro == 1)
		         {
				    if ($this->wordpress->get_option('aj_velaro_id') !='')
				    {
				?>
				<li class="right"><a class="chat_support" href="https://service.velaro.com/visitor/requestchat.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue" target="OnlineChatSoftware"  onClick="this.newWindow = window.open('http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue', 'OnlineChatSoftware', 'toolbar=no,location=no,directories=no,menubar=no,status=no,scrollbars=no,resizable=yes,replace=no');this.newWindow.focus();this.newWindow.opener=window;return false;"><img alt="OnlineChatSoftware" src="https://service.velaro.com/visitor/check.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue" border="0" class="chat-top"></a></li>
				<?php }else{?>
				<li class="right"><a class="chat_support" onClick="this.newWindow = window.open('https://service.velaro.com/visitor/requestchat.aspx?siteid=7548&amp;showwhen=inqueue', 'OnlineChatSoftware', 'toolbar=no,location=no,directories=no,menubar=no,status=no,scrollbars=no,resizable=yes,replace=no');this.newWindow.focus();this.newWindow.opener=window;return false;" target="OnlineChatSoftware" href="http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&amp;showwhen=inqueue"><img border="0" src="https://service.velaro.com/visitor/check.aspx?siteid=7548&amp;showwhen=inqueue" alt="OnlineChatSoftware" class="chat-top"></a></li>
				<?php } 
				           } ?>
			</ul>
		</nav>

		<div id="warning" class="grid_16" style="display: <?php echo isset($warning) ? 'block;' : 'none;';?>">
		<p><?php if(isset($warning_message)) echo $warning_message; ?></p>
		</div>

		<div id="content">
