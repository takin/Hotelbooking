<?php $this->load->view('includes/header'); ?>

	
		<?php // $this->load->view('includes/top_box'); ?>
		
		<?php if ($this->wordpress->get_option('aj_show_stamp')){?>
		<?php /*?>
		<div id="mainbar" class="clearfix" style="height:0;">
			
			<p class="mainbar-message"><?php echo _('Booking Guarantee')?>: <?php echo _('Up to $100')?></p>
			<img class="message-img" src="<?php echo base_url();?>images/message-mainbar.png" alt="" />
			<p class="mainbar-message-desc"><?php echo _('In the very unlikely event your selected property does not make a bed available to you, we will refund your deposit twice up to $100. * See Details.')?> <a class="openup" style="color:#9FCE20; font-size:1.3em;" href="<?php echo base_url(); ?>guarantee"><strong>&raquo;&raquo;</strong></a></p>
		</div>
		<?php */?>
		<?php }?>
		
    <?php
    //$this->load->view('includes/bottom_box');
    ?>
    <div id="content" class="clearfix">
      <?php
      if(isset($current_view_dir))
      {
        $this->load->view($current_view_dir.$current_view);
      }
      else
      {
        $this->load->view($current_view);
      }
      ?>

<?php $this->load->view('includes/footer'); ?>