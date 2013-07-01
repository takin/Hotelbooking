<?php 
if(!isset($isAjax) || ($isAjax == false))
{
  ?>
  <h2><?php echo _("Commentaires et évaluations de voyageurs");?></h2>
  <a href="#hostel_info_home" onclick="$('.tab_price').trigger('click'); return false;" class="booking-form-submit button-green box_round hoverit" style="display: inline; float: right"><?php echo _('Click here to see info and prices'); ?></a>
  <br style="clear: both;" />

  <span id="comment-translate-menu"></span>
  <?php
}
  
  if($reviews_translation_available)
  {
    $selectlang = "<select class=\"select-translate\"><option value=\"translate\">". _("Voir la version traduite")."</option><option value=\"original\">". _("Voir l'original")."</option></select>";
    ?>
    
    <script type="text/javascript">
      $(function()
        {   
          $('#comment-translate-menu').html('<?php echo $selectlang; ?>');
          $('#comment-translate-menu .select-translate').change(function() {
            
              var api = $(this).data('jsp');            
              var version = $(this).val();
              if (version =='translate'){
                $("#comment_list_part .original").hide();
                $("#comment_list_part .translated").show();
                $("#remote_comment_list_part .original").hide();
                $("#remote_comment_list_part .translated").show();
              }
              if (version =='original'){
                $("#comment_list_part .translated").hide();
                $("#comment_list_part .original").show();             
                $("#remote_comment_list_part .translated").hide();
                $("#remote_comment_list_part .original").show();             
              }
            });         
        });
    </script>
    <?php 
  }
  ?>
	<script type="text/javascript">
    $(function()
      {
				$('#tab_comment').text('<?php echo _("Commentaires");?> (<?php echo $review_count;?>)');
      });
  </script>
<div id="comment_list_part">
<?php
$hw = false;
foreach($user_reviews as $review)
{
  $this->load->view("hw/review_list",$review);
	if ($review["review_source"] == 'HW'){$hw = true;}
}
if ($hw){
	//echo '<p align="right" style="margin-right:10px;padding-top:30px;">* '._("Hostelworld customer review").'</p>';
}
?>
</div>
