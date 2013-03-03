<div id="sidebar" class="grid_4">
	<?php $this->load->view('includes/side_search_box'); ?>
	<?php /*?><?php $this->load->view('includes/groupe'); ?>
	<?php $this->load->view('includes/siteinfo'); ?>
	<?php $this->load->view('includes/widget-cours'); ?>
	<?php $this->load->view('includes/year-10'); ?><?php */?>
</div>
<div id="main" class="grid_12">
  <div class="box_content box_round group">
    <h1 class="content_title"><?php echo _('Erreur 400 - Page Introuvable');?></h1>

        <div class="entry copy">
        <p><?php echo _("The request cannot be fulfilled due to bad syntax");?>.</p>

        <p><?php echo _("Merci");?>,<br /><strong><?php echo $this->config->item('site_name');?></strong></p>
        </div>
 	</div>
</div>