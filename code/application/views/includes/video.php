<?php if ($this->wordpress->get_option('aj_promo_video')!=""){?>
<div class="box_content box_round group">
	<div id="videoblock">
		<div class="video-wrapper">
		<object width="372" height="209" type="application/x-shockwave-flash" data="http://www.youtube.com/v/<?php echo $this->wordpress->get_option('aj_promo_video');?>?showinfo=0&modestbranding=1&autohide=1&rel=0">
			<param name="movie" value="http://www.youtube.com/v/<?php echo $this->wordpress->get_option('aj_promo_video');?>?showinfo=0&modestbranding=1&autohide=1&rel=0"></param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowscriptaccess" value="always"></param>
			<param name="wmode" value="transparent"></param>            
		</object>			
		</div>
	</div>
</div>
<?php }?>