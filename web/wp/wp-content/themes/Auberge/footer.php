<?php if($GLOBALS['print']=='nostyle'){
include( TEMPLATEPATH . '/footer-nostyle.php' );
}elseif($GLOBALS['print']=='mobile'){
include( TEMPLATEPATH . '/footer-mobile.php' );
}else{?>
<footer class="grid_16 box_round box_shadow">
	<div class="footer-inner">
		<div class="footer-block grid_6">	
			<h3><?php _e('Destinations Populaires','auberge');?></h3>
          <p class="populaire">          
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Londres','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('England/London','auberge');?>"><?php _e('Londres','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Rome','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Italy/Rome','auberge');?>"><?php _e('Rome','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Barcelone','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Spain/Barcelona','auberge');?>"><?php _e('Barcelone','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Paris','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('France/Paris','auberge');?>"><?php _e('Paris','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Amsterdam','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Netherlands/Amsterdam/','auberge');?>"><?php _e('Amsterdam','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Dublin','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Ireland/Dublin','auberge');?>"><?php _e('Dublin','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Madrid','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Spain/Madrid','auberge');?>"><?php _e('Madrid','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Prague','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Czech%20Republic/Prague/','auberge');?>"><?php _e('Prague','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Berlin','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Germany/Berlin','auberge');?>"><?php _e('Berlin','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Venise','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Italy/Venice','auberge');?>"><?php _e('Venise','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Florence','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Italy/Florence','auberge');?>"><?php _e('Florence','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Vienne','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Austria/Vienna','auberge');?>"><?php _e('Vienne','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Budapest','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Hungary/Budapest','auberge');?>"><?php _e('Budapest','auberge');?></a>,
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('New York','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('USA/New%20York','auberge');?>"><?php _e('New York','auberge');?></a>,  
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Edimbourg','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Scotland/Edinburgh','auberge');?>"><?php _e('Edimbourg','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Stockholm','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Sweden/Stockholm/','auberge');?>"><?php _e('Stockholm','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Munich','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Germany/Munich','auberge');?>"><?php _e('Munich','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Nice','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('France/Nice','auberge');?>"><?php _e('Nice','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Athenes','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Greece/Athens','auberge');?>"><?php _e('Athenes','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Valence','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('italy/milan','auberge');?>"><?php _e('Milan','auberge');?></a>, 
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Grenade','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('portugual/lisbon','auberge');?>"><?php _e('Lisbonne','auberge');?></a>
						<a style="font-size:<?php echo rand(8,16) ?>pt;" title="<?php _e('Grenade','auberge');?>" href="<?php echo get_option('aj_api_url')?><?php _e('Belgium/Brussels/','auberge');?>"><?php _e('Bruxelles','auberge');?></a>              
         	</p>
		</div>
		<div class="footer-block grid_4">
			<h3><?php _e('Navigation','auberge');?></h3>
			<ul class="dots">         
				<li><a href="<?php bloginfo('url'); ?>"><?php _e('Accueil','auberge');?></a></li>
				<li><a href="<?php echo get_option('aj_api_url');?>"><?php _e('Trouver une auberge','auberge');?></a></li>
				<li><a href="<?php echo get_option('aj_page_guides');?>"><?php _e('Guides','auberge');?></a></li>
				<li><a href="<?php echo get_option('aj_page_events');?>"><?php _e('Événements','auberge');?></a></li>
			</ul>
		</div>        
		<div class="footer-block grid_3">
			<h3><?php _e('Information','auberge');?></h3>
			<ul class="dots">
				<li><a href="<?php echo get_option('aj_page_faq');?>"><?php _e('Aide / FAQ / Nous Joindre','auberge');?></a></li>
				<li><a href="<?php echo get_option('aj_page_conf');?>"><?php _e('Confidentialité','auberge');?></a></li>
				<li><a href="<?php echo get_option('aj_page_cond');?>"><?php _e('Conditions','auberge');?></a></li>
				<?php $about = get_option('aj_page_about'); if (!empty($about)){?><li><a href="<?php echo $about; ?>"><?php _e('About us','auberge');?></a></li><?php }?>				
			</ul>
		</div>
		<div class="footer-block grid_3">
			<?php include (TEMPLATEPATH . '/flags.php');?>
		</div>
	</div>
</footer>
<section class="grid_16">
	<div class="outside-inner">
		<p class="last-part"><strong><?php _e('Vous recherchez une <strong>auberge</strong>, une <strong>auberge de jeunesse</strong>, un <strong>appartement</strong>, un <strong>Bed And Breakfast B&B</strong>, une<strong> pension</strong>, une <strong>chambre d’hôtes</strong>, une <strong>Hostel</strong> ? <strong>AubergesDeJeunesse.com</strong> est le site en ligne n°1 en France, Belgique, Suisse et Canada pour tous les logements à bas prix dernière minute. AubergesDeJeunesse.com propose la réservation dernière minute de chambres et logements à bas prix dans des villes comme <strong>Paris</strong> en France, <strong>Londres</strong> en Angleterre, <strong>Barcelone </strong>en Espagne, <strong>New York</strong> aux Etats-Unis, <strong>Rome</strong> en Italie, <strong>Amsterdam</strong> en Hollande, <strong>Dublin</strong> en Irlande, <strong>Madrid</strong> en Espagne, <strong>Prague</strong> en République tchèque ….Préparez votre voyage en toute simplicité, nous proposons également des informations de dernières minutes, des guides et commentaires.','auberge');?></p>
	</div> 
 </section>
</div>

<?php wp_footer(); ?>  

<div id="lf_div_invite" name="lf_div_invite" style="display:none;position:absolute;left:0px;top:0px;width:0px;height:0px;">
<iframe name="lf_iframe_invite" id="lf_iframe_invite" frameborder="0" src="about:blank" 
style="width:100%;height:100%;border:none;" allowtransparency="true" scrolling="no"></iframe></div>

<?php  if(DISPLAY_VELARO == 1)  {  ?>
<!-- Velaro Weblink Code -->
<script type='text/javascript'>
var pt='http';
var qs=escape(window.location.search);
var ti=new Date();
if(location.href.substr(0,5).toLowerCase()=='https') pt='https';
var ed = new Date();
ed.setHours(23); ed.setMinutes(59); ed.setSeconds(59);
ed.setFullYear(ed.getFullYear()+1);
var la=''; if (navigator.appName == 'Netscape') la=navigator.language; else la=navigator.systemLanguage;
var pn = ''; //'&pn='+location.pathname; // set to a human readable pagename if desired
var rm = escape(window.document.referrer);
var pm = window.document.URL.replace(/&/g,"*");
var sm = pt+'://v.velaro.com/lf/monitor2.aspx?siteid=7548&secure=yes';
sm=sm+'&qs='+qs+'&ti='+ti.getTime()+'&tz='+ti.getTimezoneOffset()+'&an='+escape(navigator.appName)+'&co='+escape(navigator.cookieEnabled);
sm=sm+'&la='+escape(la)+'&pl='+escape(navigator.platform)+'&pal='+screen.colorDepth+'&sw='+escape(screen.width+'x'+screen.height);
sm=sm+ pn+'&je='+navigator.javaEnabled()+'&origin=';
sm=sm+rm+'&pa='+pm;
document.write('<script src="'+sm+'"></scr'+'ipt>');

</script>
<!-- End Velaro Weblink Code -->
<?php  }  ?> 
</body>
</html>
<?php } ?>
