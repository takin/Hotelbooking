<div class="comment_list group">
	<div class="comment_content box_round">
		<?php if(!empty($review_rating)){?>
			<div class="rating_user">
			<?php echo $review_rating;?>%
			</div>   
		<?php }?>             
    <div<?php if (!empty($review_likebest_translated)){?> class="original" style="display:none;"<?php }?>>
		<?php echo nl2p(var_check($review_likebest,""),false,true);?>
		</div>
		
		<?php if (!empty($review_likebest_translated)){?>
		<div class="translated">
		<?php echo nl2p(var_check($review_likebest_translated,""),false,true);?>
		</div>
		<?php }?>
	</div>
	
	<p class="comment_author">
	<?php if(!empty($review_rating)){
		if($review_rating <= 60 && $review_rating >= 40){ $class = ' is_medium';}
		elseif($review_rating < 40){$class = ' is_low';}
		else{$class = '';}
	?>
	<span class="icon_user_review<?php echo $class;?>"><?php echo $review_rating;?>%</span>
	<?php }?>
	<?php if ($author_name == 'Anonymous'){?>
		<?php printf(gettext("Le %s"), date_conv($review_date, $this->wordpress->get_option('aj_date_format'))); ?>	
	<?php }else{?>
		<?php printf(gettext("Par %s | Le %s"), $author_name,date_conv($review_date, $this->wordpress->get_option('aj_date_format'))); ?>			
	<?php }?>
	</p>
</div>