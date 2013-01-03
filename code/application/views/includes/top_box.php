<div id="top-menu" class="clearfix">
 <?php /*?> <ul>
    <?php $about = $this->wordpress->get_option('aj_page_about'); if (!empty($about)){?><li><a href="<?php echo $about; ?>"><?php echo _("About us");?></a></li><?php }?>
		<li><a href="<?php echo $this->wordpress->get_option('aj_page_faq'); ?>"><?php echo _("Aide / FAQ / Nous Joindre");?></a></li>
    <li><a href="<?php echo $this->wordpress->get_option('aj_page_conf'); ?>"><?php echo _("Confidentialité");?></a></li>
    <li class="last"><a href="<?php echo $this->wordpress->get_option('aj_page_cond'); ?>"><?php echo _("Conditions");?></a></li>

  </ul><?php */?>
  <ul>
    <li>
      <?php echo login_check($this->tank_auth->is_logged_in(),"<a href=\"".site_url($this->Db_links->get_link("user"))."\">"._("Bienvenue!")."</a>","<a href=\"".site_url($this->Db_links->get_link("connect"))."\" onclick=\"toggleById(); return false;\">"._("Se connecter")."</a>"); ?>
    </li>
    <li class="last">
      <?php echo login_check($this->tank_auth->is_logged_in(),"<a href=\"".site_url($this->Db_links->get_link("logout"))."\">"._("Se déconnecter")."</a>","<a href=\"".site_url($this->Db_links->get_link("register"))."\">"._("S'enregistrer")."</a>"); ?>
    </li>
  </ul>
</div>

<div id="header" class="clearfix">
  <?php if ($this->wordpress->get_option('aj_api_ascii')==""){$csspath = $this->wordpress->get_option('aj_api_name');}else{$csspath = $this->wordpress->get_option('aj_api_ascii');} ?>
  <h1><a title="<?php echo _("Plus de 30,000 Auberges de Jeunesse disponible en ligne");?>" href="<?php echo site_url(); ?>"><?php echo $this->wordpress->get_option('aj_api_name');?></a></h1>
  
	<div class="bubble-blue-position<?php if($this->wordpress->get_option('aj_api_site_data') == 'hb'){echo ' hb-bubble';}?>">
		<div class="bubble-blue">
			<span class="bubble-blue-inner">
			<?php echo _('Free SMS')?>
			</span>
		</div>
	</div>
	
	<div class="bubble-green-position<?php if($this->wordpress->get_option('aj_api_site_data') == 'hb'){echo ' hb-bubble';}?>">
		<div class="bubble-green">
			<span class="bubble-green-inner">
			<?php if($this->wordpress->get_option('aj_api_site_data') == 'hb'){?>
			<?php echo _('No Booking fees')?>
			<?php }else{?>
			<?php echo _('Book on your mobile')?>
			<?php }?>
			</span>
		</div>
	</div>
	
	
	<div id="top-pic">
    <p><?php echo _("Réservation mondiale et immédiate, 30 000 Auberges de jeunesse et hôtels pas chers.");?></p>
  </div>
	
</div>
<div id="menu">
  <ul>
    <li class="first"><a href="/"><?php echo _("Accueil");?></a></li>
    <li class="current_page_item"><a href="<?php echo site_url($this->Db_links->get_link("homepage")); ?>"><?php echo _("Auberges et logements pas chers");?></a></li>

    <?php if($this->wordpress->get_option('aj_group_url') != ''){?>
    <li><a title="<?php echo _("Réservation d'auberges de jeunesse pour les groupes");?>" href="<?php echo $this->wordpress->get_option('aj_group_url'); ?>" class="group-reserve"><?php echo _("Groupes 10+");?></a></li>
    <?php } ?>
    <?php if($this->wordpress->get_option('aj_page_events') != ''){?>
    <li><a href="<?php echo $this->wordpress->get_option('aj_page_events'); ?>"><?php echo _("Événements");?></a></li>
    <?php } ?>
    <?php if($this->wordpress->get_option('aj_page_guides') != ''){?>
    <li><a href="<?php echo $this->wordpress->get_option('aj_page_guides'); ?>"><?php echo _("Destinations");?></a></li>
    <?php } ?>
    <!--[if lt IE 8]>
    <![endif]-->
   
		<?php if ($this->wordpress->get_option('aj_api_ascii')==""){?>
		<li class="shareit">
		<!-- Place this tag where you want the +1 button to render -->
		<g:plusone size="medium" href="<?php echo base_url();?>"></g:plusone>
		
		<!-- Place this render call where appropriate -->
		<script type="text/javascript">
			(function() {
				var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
				po.src = 'https://apis.google.com/js/plusone.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			})();
		</script>

    </li>
		
		<?php }?>

    <li class="flags-holder"><a class="flags-trigger" href="">Internationale</a>
    <div id="flags">

      <?php $this->load->view('includes/flags'); ?>

    </div>

    </li>

  </ul>
</div><!-- End Menus -->
