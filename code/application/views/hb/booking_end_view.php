<div class="box_content box_round group booking booking_end">
		<?php if ($this->wordpress->get_option('aj_api_ascii')==""){$csspath = $this->wordpress->get_option('aj_api_name');}else{$csspath = $this->wordpress->get_option('aj_api_ascii');} ?>
    <?php /*?><img style="position:absolute; top:-133px; right:18px;" src="<?php echo secure_site_url();?>images/<?php echo $csspath; ?>/progress-3.png" alt="<?php echo _("Numéro de dossier et email de confirmation");?>" /><?php */?>
		<h1 class="content_title"><?php echo $this->wordpress->get_option('blogname');?></h1>
		<?php
		$settlecursymbol = currency_symbol($booking->payment->currencyPaymentTakenIn);
		$customercursymbol = currency_symbol($booking->payment->currencies->customer->currency);
		?>
		<div class="booking_section green_gradient_faded" id="booking_end_step1">
			<h2 class="checked"><?php echo _('Votre réservation est validée');?></h2>
			<a class="print_booking" href="#" onClick="window.print();return false;"><?php echo _('Print Now');?></a>
      <p><?php echo _("Votre réservation est maintenant confirmée. Nous vous conseillons d'imprimer cette page afin de conserver les détails de votre réservation. Vous devrez présenter cette confirmation lors de votre arrivée à "); echo '<strong><a href="'.site_url($this->Db_links->get_link("info").'/'.url_title($propertyName).'/'.$propertyNumber).'">'.$booking->property->name.'</a></strong>.';?></p>

      <p><strong><?php echo _("Vous allez recevoir un courriel d'ici peu avec toutes les informations contenu sur cette page.");?> <?php echo _("Vous pouvez aussi vous connecter à votre compte pour voir toutes vos réservation:");?></strong></p>

      <p><strong><a href="<?php echo site_url($this->Db_links->get_link("user")); ?>"><?php echo _("Vous connecter");?> &raquo;</a></strong></p>
     
		</div>
		<div class="booking_section" id="booking_end_step2">
			<h2 class="booking_section_title green_gradient_faded box_round"><?php echo _('Détails de la réservation');?></h2>
			<div class="booking_section_content" >
				<p><?php echo _('Votre Numéro de confirmation');?> : <strong>HB-<?php echo $booking->bookingRef;?></strong></p>
				<p><?php echo _("Nom de l'établissement");?>: <strong><?php echo $booking->property->name;?></strong>, <span><?php echo $booking->property->address->street1;?></span>
                <span><?php echo $booking->property->address->street2;?></span>
                <span><?php echo $booking->property->address->street3;?></span>
                <span>, <?php echo $booking->property->address->city;?></span></p>

      <p class="arrival-dep"><?php printf(gettext('Arrivée: %s à %s'),"<b>".date_conv($dateStart, $this->wordpress->get_option('aj_date_format'))."</b>","<b>".$book_arrival_time.":00</b>");?> &nbsp; &nbsp; <?php printf(gettext('Nombre de Nuits: %s'),"<b>".$numNights."</b>");?></b></p>

      <table class="review" cellpadding="0" cellspacing="0">
        <thead>
          <tr valign="middle" align="center">
            <th class="first-cell green-th"><?php echo _('Date');?></th>
            <th class="green-th"><?php echo _('Chambre');?></th>
            <th class="green-th"><?php echo _('Prix (lit)');?></th>
            <th class="green-th"><?php echo _('Nb. Personnes');?></th>
            <th width="18%" class="last-cell green-th"><?php echo _('Total');?></th>
          </tr>
        </thead>

        <tbody>
         <?php $total =0; foreach($roomsBookedSorted as $room): ?>
          <tr>
             <td class="first-cell"><?php $roomdate = new Datetime($room->date); echo date_conv($roomdate->format("Y-m-d"), $this->wordpress->get_option('aj_date_format'));?></td>
             <td>
              <?php
                 if(!empty($room->name_translated))
                 {
                  echo $room->name_translated.'<br />';
                  echo '(<small>'.$room->name.'</small>)';
                 }
                 else
                 {
                   echo $room->name;
                 }
								 if($breakfast_included == 1){
										echo '<span class="free-breakfast">';
										echo _('Breakfast Included');
										echo '</span>';
									}
               ?>
             </td>
             <td><?php echo $customercursymbol;?><?php echo number_format(floatval($room->priceCustomerCurrency),2,'.','');?></td>
             <td><?php echo $room->beds;?></td>
             <td class="value"><?php echo $customercursymbol;?><?php $total = $total + number_format((float)($room->priceCustomerCurrency)*($room->beds),2,'.',''); echo number_format((float)($room->priceCustomerCurrency)*($room->beds),2,'.','');?> </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
					</table>
					<table class="review-end" cellpadding="0" cellspacing="0">
            <tr class="dark">
             <td class="first" align="right" colspan="4"><b><?php echo _('Total');?>:</b>&nbsp;</td>
             <td class="total-value"><b><?php echo $customercursymbol;?><?php echo number_format($total,2,'.','');?></b></td>
            </tr>
            <tr class="light first">
             <td align="right" colspan="4">
             <?php printf(gettext('10%% Arrhes facturé en %s à votre carte'),$booking->payment->currencyPaymentTakenIn); ?>:
             </td>

             <td>
               <b><?php echo $settlecursymbol;?><?php echo number_format(floatval($booking->payment->currencies->gbp->amountTaken),2,'.','');?></b><br />
               <?php
               if($isCustomCurrency)
               {
                  echo "(~".$customercursymbol;?><?php echo number_format(floatval($booking->payment->currencies->customer->amountTaken),2,'.','') . ")";
               }
               ?>
             </td>
            </tr>

            <tr class="end-total dark">
              <td align="right" colspan="5">
                  <span id="bookingFeeDesc"><?php echo _('Montant restant à payer lors de votre arrivée');?> (<?php echo $booking->payment->currencies->property->currency;?>): </span>
                  <span style="display: inline;"><b><?php echo currency_symbol($booking->payment->currencies->property->currency);?><?php echo number_format(floatval($booking->payment->currencies->property->payableOnArrival),2,'.','');?> </b></span>
                  <?php if($isCustomCurrencyProperty):?>
                  <span class="custom-cur">(~ <?php echo $customercursymbol;?><?php echo number_format(floatval($booking->payment->currencies->customer->payableOnArrival),2,'.','');?>)</span>
                  <?php endif;?>
              </td>
            </tr>
         
        </table>
			</div>
    </div>
		
		<?php if (!empty($important_info->original)){?>
		<div class="booking_section">  
			<h2 class="booking_section_title box_round green_gradient_faded"><?php echo _("Informations Importantes");?></h2>			
			<div class="booking_section_content">
				<?php
      	 if(!empty($important_info->translation))
      	 {
      	   echo nl2p($important_info->translation,false,true).'<br />';
					 echo '<p><strong>'._("Version Originale").'</strong></p>';
					 echo nl2p($important_info->original,false,true);
      	 }
      	 else
      	 {
      	   echo nl2p($important_info->original,false,true);
      	 }
      	 ?>
			</div>
		</div>
		<?php }?>
		
    <div class="booking_section">
			<h2 class="booking_section_title green_gradient_faded box_round"><?php echo _("Information sur l'établissement");?></h2>
				<div class="booking_section_content">

            <p class="conf-detail"><?php echo _("Nom de l'établissement");?>:
                <span><?php echo $booking->property->name;?></span>
            </p>

            <p class="conf-detail"><?php echo _("Adresse");?>:
                <span><?php echo $booking->property->address->street1;?></span>
                <span><?php echo $booking->property->address->street2;?></span>
                <span><?php echo $booking->property->address->street3;?></span>
                <span><?php echo $booking->property->address->city;?>, <?php echo $booking->property->address->state;?></span>
                <span><?php echo $booking->property->address->country;?></span>
                <span><?php echo $booking->property->address->zip;?></span>
            </p>


            <p class="conf-detail"><?php echo _("Phone number")?>:
                <span><?php echo $booking->property->address->tel;?></span>
            </p>


            <p class="conf-detail"><?php echo _("Fax");?>:
                <span><?php echo $booking->property->address->fax;?></span>
            </p>
            
						<p class="conf-detail"><?php echo _("Email");?>:
                <span><a href="mailto:<?php echo $booking->property->address->email;?>"><?php echo $booking->property->address->email;?></a></span>
            </p>						
      	</div>
			</div>
			
			<div class="booking_section">
				<h2 class="booking_section_title green_gradient_faded box_round"><?php echo _("Directions");?></h2>
				<div class="booking_section_content">	
      	 <?php
      	 if(!empty($booking->property->address->directions_ranslated))
      	 {
      	   echo $booking->property->address->directions_ranslated.'<br />';
					 echo '<p><strong>'._("Version Originale").'</strong></p>';
					 echo nl2p($booking->property->address->directions,false,true);
      	 }
      	 else
      	 {
      	   echo nl2p($booking->property->address->directions,false,true);
      	 }
      	 ?>
				</div>
			</div>

			<?php
			//Cancellation policy
			if(!empty($booking->property->importantInfo->cancellationPolicy)){?>
			<div class="booking_section">
				<h2 class="booking_section_title green_gradient_faded box_round"><?php echo _("Cancellation Policy");?></h2>
				<div class="booking_section_content">
				<?php if(!empty($booking->property->importantInfo->cancellationPolicy_translated)){?>
					<p><?php echo $booking->property->importantInfo->cancellationPolicy_translated;?></p>
					<p><strong><?php echo _("Version Originale");?></strong></p>
				<?php }?>
				<p><?php echo $booking->property->importantInfo->cancellationPolicy;?></p>
				</div>
			</div>
			<?php }?>

			 <?php
			//Cancellation policy
			if(!empty($booking->property->importantInfo->taxes)){?>
			<div class="booking_section">
				<h2 class="booking_section_title green_gradient_faded box_round"><?php echo _("Taxes");?></h2>
				<div class="booking_section_content">
				<?php if(!empty($booking->property->importantInfo->taxes_translated)){?>
					<p><?php echo $booking->property->importantInfo->taxes_translated;?></p>
					<p><strong><?php echo _("Version Originale");?></strong></p>
				<?php }?>
				<p><?php echo $booking->property->importantInfo->taxes;?></p>
				</div>
			</div>
			<?php }?>

			<?php
			//Cancellation policy
			if(!empty($booking->property->importantInfo->extraInfo)){?>
			<div class="booking_section">
				<h2 class="booking_section_title green_gradient_faded box_round"><?php echo _("Extra Information");?></h2>
				<div class="booking_section_content">
				<?php if(!empty($booking->property->importantInfo->extraInfo_translated)){?>
					<p><?php echo $booking->property->importantInfo->extraInfo_translated;?></p>
					<p><strong><?php echo _("Version Originale");?></strong></p>
				<?php }?>
				<p><?php echo $booking->property->importantInfo->extraInfo;?></p>
				</div>
			</div>
			<?php }?>
			
			<div class="booking_section">
				<h2 class="booking_section_title green_gradient_faded box_round"><?php echo _("Conditions");?></h2>
				<div class="booking_section_content">
        <p class="conditions">
          <?php echo _("Vous devez payer pour votre chambre à votre arrivée. Veuillez, s'il vous plaît, vous assurer que votre carte de crédit est valide. Les cartes sont vérifiées la veille des arrivées et le fait de réserver à l'aide d'une carte invalide annulera votre réservation. Si vous avez réservé à l'aide de votre carte bancaire ou si vous doutez de la validité de votre carte de crédit, veuillez contacter l'établissement au moins 24 heures avant votre arrivée afin de confirmer votre réservation.");?></p><br>
          <ul class="conditions">
          <?php if ($this->wordpress->get_option('aj_red_email') != true){ ?>

            <li><?php echo _("Le montant restant (après déduction du dépôt) est dû à votre arrivée.");?> </li>
            <li><font color="#a90000"><?php echo _("Les annulations doivent être effectuées directement auprès de l'établissement.");?></font></li>
            <li><?php echo _("Si vous ne vous présentez pas à la date prévue et que vous n'avez pas annulé votre réservation, votre carte de crédit sera facturée pour le montant total de votre première nuit de réservation.");?> </li>
            <li><font color="#a90000"><?php echo _("Pour effectuer toute modification à une réservation, veuillez contacter directement l'établissement.");?></font></li>
            <li><?php echo _("Si votre carte de crédit devient invalide ou que des modifications sont apportées à votre compte avant votre arrivée, veuillez contacter directement l'établissement afin de prendre d'autres dispositions.");?> </li>
            <li><?php echo _("En cas d'annulation de votre réservation, le dépôt et les frais de réservation ne peuvent pas être pas remboursés.");?> </li>
            <li><?php echo _("Veuillez s'il vous plaît lire attentivement nos termes et conditions.");?> </li>
            <li><?php printf(gettext("Pour toute question, consultez notre %s."),"<a href=\"".$this->wordpress->get_option('aj_page_faq')."\">"._("Centre d'aide en ligne")."</a>");?></li>

					<?php }else{?>

            <li><?php echo _("Le montant restant (après déduction du dépôt) est dû à votre arrivée.");?> </li>
            <li><?php echo _("Les annulations doivent être effectuées directement auprès de l'établissement.");?></li>
            <li><?php echo _("Si vous ne vous présentez pas à la date prévue et que vous n'avez pas annulé votre réservation, votre carte de crédit sera facturée pour le montant total de votre première nuit de réservation.");?> </li>
            <li><?php echo _("Pour effectuer toute modification à une réservation, veuillez contacter directement l'établissement.");?></li>
            <li><?php echo _("Si votre carte de crédit devient invalide ou que des modifications sont apportées à votre compte avant votre arrivée, veuillez contacter directement l'établissement afin de prendre d'autres dispositions.");?> </li>
            <li><?php echo _("En cas d'annulation de votre réservation, le dépôt et les frais de réservation ne peuvent pas être pas remboursés.");?> </li>
            <li><?php echo _("Veuillez s'il vous plaît lire attentivement nos termes et conditions.");?> </li>
            <li><?php printf(gettext("Pour toute question, consultez notre %s."),"<a href=\"".$this->wordpress->get_option('aj_page_faq')."\">"._("Centre d'aide en ligne")."</a>");?></li>

					<?php }?>
					</ul>
				</div>
      </div>
    </div>
<?php $googana_code = $this->wordpress->get_option('aj_google_analytic_account_no'); if(!empty($googana_code)){ ?>
<script>
	 var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '<?php echo $googana_code; ?>']);
		_gaq.push(['_trackPageview']);
	 _gaq.push(['_addTrans',
		'HB-<?php echo $booking->bookingRef;?>',           // order ID - required
		'HB',  // affiliation or store name
		'<?php echo $booking->CADDepositAmount->value;?>',          // total - required
		'',           // tax
		'',              // shipping
		'<?php echo $booking->property->address->city;?>',       // city
		'',     // state or province
		''             // country
	]);

	 // add item might be called for every item in the shopping cart
	 // where your ecommerce engine loops through each item in the cart and
	 // prints out _addItem for each
	_gaq.push(['_addItem',
		'HB-<?php echo $booking->bookingRef;?>',           // order ID - required
		'<?php echo $booking->property->id;?>',           // SKU/code - required
		'<?php echo $booking->property->name;?>',        // product name
		'Nights-<?php echo $numNights;?>',   // category or variation
		'<?php echo $booking->CADDepositAmount->value;?>',          // unit price - required
		'1'               // quantity - required
	]);
	_gaq.push(['_trackTrans']); //submits transaction to the Analytics servers
</script>
<?php }?>
<?php echo $this->wordpress->get_option('aj_google_adword'); ?>