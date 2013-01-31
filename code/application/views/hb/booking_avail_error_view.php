<h2><?php echo _('Disponibilités'); ?> <span>(<?php echo $currency; ?>)</span></h2>
<?php
$date = clone $dateStart;
$datetop = date_conv($dateStart->format('Y-m-d'), $this->wordpress->get_option('aj_date_format'));
?>
<div class="top-table">
<p><?php echo _('Arrivée');?>: <b><?php echo $datetop;?></b> &nbsp;&nbsp; <?php echo _('Nombre de Nuits');?>: <b><?php echo $numNights; ?></b><a id="change-dates" href="#">[<?php echo _('Change Dates'); ?>]</a></p>

</div>
<div class="dispo-error group">
  <img class="arrow-error" src="<?php echo site_url();?>images/V2/arrow-error.png" alt="" />
	<?php /*?><h3><?php echo _("Erreur:");?></h3><?php */?>
  <p style="border-bottom:1px dashed;font-size:15px;"><strong><?php
      echo _("No Beds Found");
      ?>
	</strong></p>
	
	<p style="margin-top:7px;">
	<strong><?php echo _("Détails:");?> </strong>
      <?php
      echo _("No Beds could be found for your search criteria. Please change your dates and try again.");
      ?>
    </p>
    <p style="margin-top:7px;">
		<?php $dateurl = $dateStart->format('Y-m-d');?>
		<a class="alternative button-green hoverit box_round" href="<?php echo site_url();?><?php echo $country_selected;?>/<?php echo $city_selected;?>/<?php echo $dateurl;?>/<?php echo $numNights; ?>">
		<?php printf(gettext('Search for more properties in %s'),$city_selected);?></a>
	</p>
</div>

<script type="text/javascript">
    $(function(){$("#booking-table").show(); $('#side_search_wrap').show();	});
</script>
