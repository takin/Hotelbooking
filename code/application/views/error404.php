<div id="sidebar" class="grid_4">
	<?php $this->load->view('includes/side_search_box'); ?>	
	<?php /*?><?php $this->load->view('includes/groupe'); ?>
	<?php $this->load->view('includes/siteinfo'); ?>
	<?php $this->load->view('includes/widget-cours'); ?>
	<?php $this->load->view('includes/year-10'); ?><?php */?>
</div>
<div id="main" class="grid_12">
  <div class="box_content box_round group">  
    <h1 class="content_title"><?php echo _('Erreur 404 - Page Introuvable');?></h1>
    
        <div class="entry copy">
        <p><?php echo _("Nous ne pouvons trouver la page demandée. Veuillez utiliser le menu du haut pour naviguer sur le site ou simplement faite une recherche d'hébergements à l'aide de l'outil de recherche ci-dessus.");?></p>
       
        <p><?php echo _("Merci");?>,<br /><strong><?php echo $this->config->item('site_name');?></strong></p>
        </div>
 	</div>
</div>