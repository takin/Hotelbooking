<?php if ($this->wordpress->get_option('aj_promo_video')!=""){?>
<div class="box_content box_round group">	
		<a href="http://www.youtube.com/embed/<?php echo $this->wordpress->get_option('aj_promo_video');?>?autoplay=1" class="iframe video-popup">
		<img src="<?php echo site_url();?>images/V2/video-popup.jpg" alt="<?php echo _("Watch the Video");?>" />
		</a>		
</div>
<?php }?>