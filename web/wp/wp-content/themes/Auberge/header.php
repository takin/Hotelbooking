<?php adword_init(); ?>
<?php
$GLOBALS['print'] = NULL;
if(isset($_GET['print']))
{
  $GLOBALS['print'] = $_GET['print'];
}
if($GLOBALS['print']=='nostyle'){
include( TEMPLATEPATH . '/header-nostyle.php' );
}elseif($GLOBALS['print']=='mobile'){
include( TEMPLATEPATH . '/header-mobile.php' );
}else{?>
<!DOCTYPE html>
<html lang="<?php echo get_option('aj_lang_code')?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php dynamictitles();?></title>

<!-- <meta http-equiv="Content-Language" content="<?php //echo get_option('aj_lang_code')?>" /> -->
<?php echo get_option('aj_special_meta')?>
<?php wp_head(); ?>
<?php if (is_single() || get_option('aj_block_bot') || is_page(array(get_option('aj_no_seo')))){
echo '<meta name="robots" content="noindex,follow" />';
}?>
<?php
$apiurl = get_option('aj_api_url');
// if https protocol set https for dev HTTPS should be set, with the proxy HTTP_HTTPS
$apiurl = isset($_SERVER['HTTPS'])?str_replace("http:","https:",$apiurl):$apiurl;
$apiurl = isset($_SERVER['HTTP_HTTPS'])?str_replace("http:","https:",$apiurl):$apiurl;
?>
<link rel="stylesheet" href="<?php echo $apiurl; ?>css/reset.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="<?php echo $apiurl; ?>css/mainv2.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="<?php echo $apiurl; ?>css/tools.css" type="text/css" media="screen,projection" />
<?php if (get_option('aj_api_ascii')==""){$csspath = get_option('aj_api_name');}else{$csspath = get_option('aj_api_ascii');} ?>
<?php /*?><link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/cms.css" type="text/css" media="screen,projection" /><?php */?>
<?php if (get_option('aj_api_site_data') == 'hb'){?>
<?php /*?><link rel="stylesheet" href="<?php echo $apiurl; ?>css/hostels.css" type="text/css" media="screen" charset="utf-8" /><?php */?>
<?php }?>
<link rel="stylesheet" href="<?php echo $apiurl; ?>css/jquery.fancybox.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo $apiurl; ?>css/smoothness/jquery-ui.css" type="text/css" media="screen"/>

<link rel="stylesheet" href="<?php echo $apiurl; ?>css/jquery.dropdown.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="<?php echo $apiurl; ?>css/jquery.msdropdown.css" type="text/css" media="screen"/>

<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="<?php echo $apiurl; ?>css/ie.css" />
<![endif]-->
<!--[if IE 6]>
	<link rel="stylesheet" type="text/css" href="<?php echo $apiurl; ?>css/ie6.css" />
<![endif]-->

<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="<?php echo $apiurl; ?>css/ie7.css" />
<![endif]-->

<?php if (get_option('aj_api_site_data') == 'hb'){?>
<link rel="shortcut icon" href="<?php echo $apiurl; ?>images/favicon-hb.ico" />
<?php }else{?>
<link rel="shortcut icon" href="<?php echo $apiurl; ?>images/favicon.ico" />
<?php }?>

<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?>" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php /*?><?php register_scripts(); ?><?php */?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $apiurl; ?>js/imageload.js"></script>
<script type="text/javascript" src="<?php echo $apiurl; ?>js/jtools.js"></script>
<script type="text/javascript" src="<?php echo $apiurl; ?>js/date-lib.js"></script>
<script type="text/javascript" src="<?php echo $apiurl; ?>js/search_box.js"></script>
<script type="text/javascript" src="<?php echo $apiurl; ?>js/mobile/suggest.js"></script>
<script type="text/javascript" src="<?php echo $apiurl; ?>js/sitetools.js"></script>
<script type="text/javascript" src="<?php echo $apiurl; ?>js/slide.js"></script>
<script type="text/javascript" src="<?php echo $apiurl; ?>js/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="<?php echo $apiurl; ?>js/ui-lang/jquery.ui.datepicker-<?php echo get_option("aj_lang_code2");?>.js"></script>

<script type="text/javascript" src="<?php echo $apiurl; ?>js/jquery.dd.js"></script>
<script type="text/javascript" src="<?php echo $apiurl; ?>js/jquery.dropdown.js"></script>
<script type="text/javascript" src="<?php echo $apiurl; ?>js/saf.localization.js"></script>

<?php if (is_page_template('contact.php')){?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/submitform.js"></script>
<?php }?>
<?php /*?><?php wp_enqueue_script("jquery"); ?>
<?php wp_enqueue_script("translate"); ?>
<?php wp_enqueue_script("jtools"); ?><?php */?>

<script type="text/javascript">

	$(function() {
		$("body").addClass("has-script");
		$(".archives-post .entry").addClass('collapse');
		$(".archives-post h2").click(function(){
			$(this).next('div.entry').slideToggle(300);
			$(this).toggleClass('expand');
		});

                if (document.location.protocol === 'https:') {

                        // change all img src to secure connection https
                        $('a').each(function() {
                            if ( this.host === location.hostname ) {
                                this.href = this.href.replace('http:', 'https:');
                            }
                        });

                        // change all img src to secure connection https
                        $('img').each(function() {
                            if ( this.src.indexOf(location.hostname) > -1 ) {
                                this.src = this.src.replace('http:', 'https:');
                            }
                        });

                        // all links to load in secure connection https
                        $('link').each(function() {
                            var href = $(this).attr('href');
                            if (href.indexOf('http:') > -1) {
                                href = href.replace('http:', 'https:');
                                $(this).attr('href', href);
                            }
                        });

                    }
	});
</script>

<script type="text/javascript">
	<!--
	function MM_jumpMenu(targ,selObj,restore){ //v3.0
	  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
	  if (restore) selObj.selectedIndex=0;
	}
	//-->
</script>
<?php //if ((is_home() || is_page_template('canada-page.php') || is_page_template('page-recherche.php') || is_page_template('lp-page.php') || is_page_template('contact.php') || is_page_template('pagecopy.php') || is_page_template('pagehelp.php') ||  is_page_template('single-page-destination.php') || is_page_template('subpage-destinations.php') || in_category('evenements') || is_page_template('page-eve.php') || in_category('destinations') || is_page_template('page-destination.php'))&& !is_page('barcelone')){?>
<script type="text/javascript">

  //City lists
  //Cities array must be a global variable
  var cities = new Array();

<?php
  $countryEmptyVal = __('Choisir le pays','auberge');
  $cityEmptyVal    = __('Choisir la ville','auberge');

  //Variable to set language
  $lang = get_option("aj_lang_code2");
?>
  var nocountryval = '<?php echo $countryEmptyVal;?>';
  var nocityval = '<?php echo $cityEmptyVal;?>';

  cities['<?php echo $countryEmptyVal;?>'] = ['<?php echo $countryEmptyVal;?>',new Array('<?php echo $cityEmptyVal;?>')];

	$(document).ready(function(){
		<?php
		  if (is_page_template('lp-page.php') || is_page_template('page-recherche.php') || is_page_template('subpage-destinations.php') || is_page_template('single-page-destination.php'))
		  {
  			$theid = get_id_out();
  			$selcountry = get_post_meta($theid, 'preselect_country', true);
  			$selcity = get_post_meta($theid, 'preselect_city', true);

  			?>
  			loadCitiesMenu("<?php echo get_ajax_url(); ?>","<?php _e('Chargement...','auberge');?>",'cities',cities,'search-country','search-city','<?php echo $selcountry  ;?>','<?php echo $selcity;?>');
		<?php
      }
      else
      {

		  ?>
			  loadCitiesMenu("<?php echo get_ajax_url(); ?>","<?php _e('Chargement...','auberge');?>",'cities',cities,'search-country','search-city');
      <?php
      }
      ?>

			 $("#search-submit").mousedown(function() {
					$(this).css("background-position","bottom left");

			 });

			 $("#search-submit").mouseup(function() {
					$(this).css("background-position","top left");
			 });

			 $('a.flags-trigger').click(function() {
				$('#flags').toggle();
				return false;
			});

			$('#flags').mouseleave(function() {
				$(this).toggle();

			});

			$("#datepick").datepicker({ dateFormat: 'd MM, yy', minDate: 0});

			$('#search-custom').focus(function() {
				$(this).removeClass('disabled');
				if (this.value == this.defaultValue){
					this.value = '';
				}
				if(this.value != this.defaultValue){
				this.select();
				}
				$("#search-city").addClass('disabled');
				$("#search-country").addClass('disabled');
			});

			$('#search-country').click(function() {
				$('#search-custom').addClass('disabled');
				$("#search-city").removeClass('disabled');
				$("#search-country").removeClass('disabled');
			});

			$('#search-city').click(function() {
				$('#search-custom').addClass('disabled');
				$("#search-city").removeClass('disabled');
				$("#search-country").removeClass('disabled');
			});

			$("a.openup").fancybox();

     });
</script>
<?php //}?>

<script type="text/javascript">
    function toggleById(){

      var display=document.getElementById("top-login-form").style.display;

      if(display=="block")
      {
        document.getElementById("top-login-form").style.display = "none";
      }
      else
      {
        document.getElementById("top-login-form").style.display = "block";
      }
    }

</script>

<script type="text/javascript">
  window.name = 'Wordpress View';
</script>

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php echo get_option('aj_google_analytic');?>

</head>

<body <?php if (get_option('aj_api_site_data') == 'hb'){body_class('hb_frame');}else{body_class();}?>>
<div id="fb-root"></div>
    <div id="top_bar">
		<div id="top_bar_inner" class="container_16 group">
			<div class="grid_6">
			<?php $code=get_option('aj_lang_code');
			$shortcode = strtolower(substr($code,0,2));
			$code=str_replace('-','_',$code);
			if($code=='' || $shortcode =='en'){$code="en_US";}
		?>
			<div class="fblike">
				<script  type="text/javascript" src="https://connect.facebook.net/<?php echo $code;?>/all.js#xfbml=1"></script>
				<div class="fb-like" data-href="<?php echo $apiurl; ?>" data-send="false" data-width="450" data-show-faces="false" data-layout="button_count"></div>
			</div>
			</div>
			<div class="grid_10">
                <!-- <span id="top_hd_currency" data-dropdown="#search-currency" class="dropdown-title">&nbsp;
                </span>
                
                <span id="site_language" data-dropdown="#language-dropdown" class='dropdown-title' style="margin-left:10px;">
                    <span class=""  lang-flag  style="padding-left:12px">&nbsp;</span>
                </span> -->
                <?php 
                    select_currency_list("search-currency","search-currency",get_selected_currency(),"",get_site_lang()); 
                    ?>
                <?php
                    include(TEMPLATEPATH . '/flags_header.php'); 
                ?>
                <script>
                    $(function(){
                    //-------- dropdown currency
                    drop = $("#top_hd_currency").dropdown({
                        onchange: function(ui, item){
                            $(ui).html($(item).data('symbol'));
                            cur_code = item.data("code");
                            saf_changeCurrency(cur_code);
                        }
                    });
                    //set default
                    $("#top_hd_currency").html(drop.getActive().attr('data-symbol'));
                    
                    //----- drop floag        
                    $("#language-dropdown ul li a").html('&nbsp;');
                    drop2 = $("#site_language").dropdown({
                        onchange: function(ui, item){
                            ui.find('[lang-flag]').attr('class', item.find('a').attr('class'));
                            //cur_code = item.data("code");
                            //saf_changeCurrency(cur_code);
                        }
                    });
                    $("#site_language").find("[lang-flag]").attr('class', drop2.getActive().find('a').attr('class'));
                    
                })
                </script>
                
                
                 
				<ul class="user_meta_top group">
					 <?php /*$about = get_option('aj_page_about'); if (!empty($about)){?><li><a class="meta_about" href="<?php echo $about; ?>"><?php _e('About us','auberge');?></a></li>
					 <li><a class="meta_help" href="<?php echo get_option('aj_page_faq');?>"><?php _e('Aide / FAQ / Nous Joindre','auberge');?></a></li>
					 <?php } */?>
                    <li><a id="top_hd_currency" data-dropdown="#search-currency" class="dropdown-title">&nbsp;</a></li>
            
                    <li><a id="site_language" data-dropdown="#language-dropdown" class="dropdown-title"><span class=""  lang-flag  style="padding-left:12px">&nbsp;</span></a></li>
                
					 <?php /*$about = get_option('aj_page_about'); if (!empty($about)){?><li><a class="meta_about" href="<?php echo $about; ?>"><?php _e('About us','auberge');?></a></li><?php } */?>
					 <!-- <li><a class="meta_help" href="<?php echo get_option('aj_page_faq');?>"><?php _e('Aide / FAQ / Nous Joindre','auberge');?></a></li> -->
					 <li>
							<?php if(is_logged_in()):?>
								<a class="meta_account" href="<?php echo get_option('aj_api_url'). get_ci_link('user');?>"><?php _e('Mon Compte','auberge');?></a>
							<?php else:?>
								<!--<a class="meta_login" href="<?php echo get_option('aj_api_url'). get_ci_link('connect');?>" onClick="toggleById(); return false;"><?php _e('Se connecter','auberge');?></a>-->
                                <!--modify to remove js error as right  id "top-login-form" is comment at line no 283.-->
                                <a class="meta_login" href="<?php echo get_option('aj_api_url'). get_ci_link('connect');?>"><?php _e('Se connecter','auberge');?></a>
							<?php endif;?>
						</li>
						<li class="last">
							<?php if(is_logged_in()):?>
								<a class="meta_logout" href="<?php echo get_option('aj_api_url'). get_ci_link('logout');?>"><?php _e('Se déconnecter','auberge');?></a>
							<?php else:?>
								<a class="meta_register" href="<?php echo get_option('aj_api_url'). get_ci_link('register');?>"><?php _e("S'enregistrer",'auberge');?></a>
							<?php endif;?>
						</li>
				</ul>
			</div>
		</div>
	</div>
	<div id="wrapper" class="container_16 group">

	<header class="grid_16 header_v2">
			<a class="logo" title="<?php _e('Plus de 30,000 Auberges de Jeunesse disponible en ligne','auberge');?>" href="<?php echo $apiurl; ?>"><img src="<?php echo $apiurl; ?>/images/<?php echo $csspath; ?>/logo.png" class="logo" alt="<?php echo get_option('aj_api_name');?>"></a>

			<div class="bubble_blue_position<?php if(get_option('aj_api_site_data') == 'hb'){echo ' hb_bubble';}?>">
				<div class="bubble_blue">
					<span class="bubble_blue_inner"><?php _e('Free SMS','auberge');?></span>
				</div>
			</div>

			<div class="bubble_blue_right_position<?php if(get_option('aj_api_site_data') == 'hb'){echo ' hb-bubble';}?>">
				<div class="bubble_blue_right">
					<span class="bubble_blue_right_inner"><?php if(get_option('aj_api_site_data') == 'hb'){?>
                                            <?php if(get_option('aj_hb_charge_booking_fees') != 'true'): ?>
						<?php _e('No Booking fees','auberge');?>
                                            <?php else: ?>
                                            <?php echo sprintf(__('100%% sécurisé.','auberge')); ?>
                                            <?php endif; ?>
						<?php }else{?>
						<?php _e('Check your reservation on your mobile','auberge');?>
						<?php }?></span>
				</div>
			</div>

		</header>

		<nav class="main grid_16 box_round box_shadow box_gradient_dark_blue">
			<ul class="group">
				<li class="first"><a<?php if(is_home()){?> class="current_page_item"<?php }?> href="/"><?php _e("Accueil",'auberge');?></a></li>
				<li><a href="<?php echo get_option('aj_api_search'); ?>"><?php _e('Auberges et logements pas chers','auberge');?></a></li>
				<?php if(get_option('aj_group_url') != ''){?>
				<li><a title="<?php _e("Réservation d'auberges de jeunesse pour les groupes","auberge");?>" href="<?php echo get_option('aj_group_url'); ?>"><?php _e('Groupes 10+','auberge');?></a></li>
				<?php } ?>
				<?php if(get_option('aj_page_events') != ''){?>
				<li><a<?php if ((in_category('evenements') || is_page_template('page-eve.php'))&&!is_home()){?> class="current_page_item"<?php }?> href="<?php echo get_option('aj_page_events');?>"><?php _e('Événements','auberge');?></a></li>
				<?php } ?>
				<?php if (is_page_template('lp-page.php')){?>
				<li class="current_page_item"><a href=""><?php echo get_post_meta(get_id_out(), 'LP_ville', true);?></a></li>
				<?php }else{?>
					<?php if(get_option('aj_page_guides') != ''){?>
				 <li><a<?php if ((is_page_template('single-page-destination.php') || is_page_template('page-destination.php') || is_page_template('subpage-destinations.php'))&&!is_home()){?> class="current_page_item"<?php }?> href="<?php echo get_option('aj_page_guides');?>"><?php _e('Destinations','auberge');?></a></li>
				<?php }}?>
                <?php

                if(DISPLAY_VELARO == 1)
                {

					if (get_option('aj_velaro_id') !='')
					{

				?>
				<li class="right"><a class="chat_support" href="http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&deptid=<?php echo get_option('aj_velaro_id');?>&showwhen=inqueue" target="OnlineChatSoftware"  onClick="this.newWindow = window.open('http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&deptid=<?php echo get_option('aj_velaro_id');?>&showwhen=inqueue', 'OnlineChatSoftware', 'toolbar=no,location=no,directories=no,menubar=no,status=no,scrollbars=no,resizable=yes,replace=no');this.newWindow.focus();this.newWindow.opener=window;return false;"><img alt="OnlineChatSoftware" src="http://service.velaro.com/visitor/check.aspx?siteid=7548&deptid=<?php echo get_option('aj_velaro_id');?>&showwhen=inqueue" border="0"></a></li>
				<?php }else{?>
				<li class="right"><a class="chat_support" href="http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&showwhen=inqueue" target="OnlineChatSoftware"  onClick="this.newWindow = window.open('http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&showwhen=inqueue', 'OnlineChatSoftware', 'toolbar=no,location=no,directories=no,menubar=no,status=no,scrollbars=no,resizable=yes,replace=no');this.newWindow.focus();this.newWindow.opener=window;return false;"><img alt="OnlineChatSoftware" src="http://service.velaro.com/visitor/check.aspx?siteid=7548&showwhen=inqueue" border="0"></a></li>
				<?php }
				           }?>

			</ul>
		</nav>
		<div id="warning" class="grid_16"></div>
<?php }?>
