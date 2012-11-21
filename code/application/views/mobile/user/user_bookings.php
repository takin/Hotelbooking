<div id="content" class="user-view">

	<div class="page-meta group">
		<h1 class="text-shadow-wrapper icon-booking"><?php echo _('Vos Réservations');?></h1>
	</div>

	<?php if(count($bookings->result()) != 0){ ?>
	<div class="user-booking">

		<?php foreach ($bookings->result() as $row){?>

		<h2 class="trigger box-shadow-wrapper round-corner5"><span class="text-shadow-white"><?php echo $row->property_name;?></span></h2>
		<div class="trigger-content white-back">
			<div class="group">
			<a href="<?php echo $hostelurl = $this->Db_links->build_property_page_link($row->property_type,$row->property_name,$row->property_number,$this->site_lang);?>"><img class="alignleft border-img" alt="" src="<?php echo $row->property_thumb_url;?>" width="61px"></a>
			<h3><a href="<?php echo $hostelurl;?>"><?php echo $row->property_name;?></a></h3>
			<p class="no-margin gray line-height-reduce">
			<?php echo $row->property_address1;?><?php if(!empty($row->property_address2)) echo ', '.$row->property_address2;?>, <?php echo $row->property_city;?>, <?php echo $row->property_country;?>
			</p>
			<a class="marg5top block-a" href="<?php echo $this->mobile->map_link($row->property_name,$row->geo_latitude,$row->geo_longitude); ?>"><?php echo _("View On Map");?></a>
			</div>
			<div class="booking-info group">
				<table class="col2" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td class="info-type"><?php echo _("Numéro de référence");?></td>
					<td class="info-value">#HW-<?php echo $row->customer_booking_reference;?></td>
				</tr>
				<tr>
					<td class="info-type"><?php echo _("Réservé le");?></td>
					<td class="info-value"><?php echo date_conv($row->booking_time, $this->wordpress->get_option('aj_date_format'));?></td>
				</tr>
				<tr>
					<td class="info-type"><?php echo _("Arrive le");?></td>
					<td class="info-value"><?php echo date_conv($row->arrival_date_time, $this->wordpress->get_option('aj_date_format'));?></td>
				</tr>
				<tr>
					<td class="info-type"><?php echo _("Nuits");?></td>
					<td class="info-value"><?php echo $row->num_nights;?></td>
				</tr>
				<tr>
					<td class="info-type"><?php echo _("Total");?></td>
					<td class="info-value"><?php echo $row->amount_charged_currency." ".$row->property_grand_total; ?></td>
				</tr>
				<tr>
					<td class="info-type"><?php echo _("Arrhes et frais de service");?></td>
					<td class="info-value"><?php echo $row->amount_charged_currency." ".$row->amount_charged;?></td>
				</tr>
				<tr>
					<td class="info-type"><?php echo _("Total du à l'arrivée");?></td>
					<td class="info-value"><?php echo $row->property_currency." ".$row->property_amount_due;?></td>
				</tr>

				<tr>
					<td class="info-type"><?php echo _("Email");?></td>
					<td class="info-value"><a href="mailto:<?php echo $row->property_email;?>"><?php echo $row->property_email;?></a></td>
				</tr>

				<tr>
					<td class="info-type"><?php echo _("Téléphone");?></td>
					<td class="info-value"><?php echo $row->property_tel;?></td>
				</tr>


				</table>
			</div>

		</div>


	<?php }?>
	</div>
	<?php }else{?>
	<div class="white-back round-corner5 border-around basic content-block">
	<p style="margin-bottom:0px;"><?php echo _("Vous n'avez aucune réservation à ce jour.");?></p>
	</div>
	<?php }?>


</div>