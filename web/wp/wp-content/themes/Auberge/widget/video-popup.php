<?php if (get_option('aj_api_ascii')==""){$csspath = get_option('aj_api_name');}else{$csspath = get_option('aj_api_ascii');}
$apiurl = get_option('aj_api_url');?>
<?php if (get_option('aj_promo_video')!=""){?>
<div class="box_content box_round group">	
		<a title="<?php echo _("Watch the Video");?>" href="http://www.youtube.com/embed/<?php echo get_option('aj_promo_video');?>?autoplay=1" class="iframe video-popup">
		<img src="<?php echo $apiurl;?>images/V2/video-popup.jpg" alt="<?php echo _("Watch the Video");?>" />
		</a>		
</div>
<?php }?>