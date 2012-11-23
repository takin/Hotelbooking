<div class="dispo-error">
  <img class="arrow-error" src="<?php echo site_url();?>images/V2/arrow-error.png" alt="" />
	<?php /*?><h3><?php echo _("Erreur:");?></h3><?php */?>
  <p><?php 
      echo $error_msg;
    ?>
	</p>
</div>
<script type="text/javascript">
    $(function(){$("#booking-table").show(); $('#side_search_wrap').show();	});
</script>