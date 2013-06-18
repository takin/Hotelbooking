<h2 class="margbot15"><?php echo _("Commentaires et évaluations de voyageurs");?></h2>
<a href="#hostel_info_home" onclick="$('.tab_price').trigger('click'); return false;" class="booking-form-submit button-green box_round hoverit" style="display: inline; float: right"><?php echo _('Click here to see info and prices'); ?></a>
<br style="clear: both;" /><br />
<?php
if($reviews_translation_available){?>
  <select class="select-translate">
		<option value="translate"><?php echo _("Voir la version traduite"); ?></option>
		<option value="original"><?php echo _("Voir l'original"); ?></option>
	</select>
	<script type="text/javascript">
		$(function()
			{		
				$('#remote-comment-list-part .select-translate').change(function() {
					
						var api = $(this).data('jsp');						
						var version = $(this).val();
						if (version =='translate'){
							$("#remote-comment-list-part .original").hide();
							$("#remote-comment-list-part .translated").show();
						}
						if (version =='original'){
							$("#remote-comment-list-part .translated").hide();
							$("#remote-comment-list-part .original").show();							
						}
					});
			});
	</script>
<?php }?>
<?php
foreach($user_reviews as $review)
{
  $this->load->view("hb/review_list",$review);
}
?>
