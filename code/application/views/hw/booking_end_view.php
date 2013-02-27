<div class="box_content box_round group booking booking_end"> 
		<?php if ($this->wordpress->get_option('aj_api_ascii')==""){$csspath = $this->wordpress->get_option('aj_api_name');}else{$csspath = $this->wordpress->get_option('aj_api_ascii');} ?>
    <?php /*?><img style="position:absolute; top:-133px; right:18px;" src="<?php echo secure_site_url();?>images/<?php echo $csspath; ?>/progress-3.png" alt="<?php echo _("Numéro de dossier et email de confirmation");?>" /><?php */?>
    <h1 class="content_title"><?php echo $this->wordpress->get_option('blogname');?></h1>
		<?php $cur = currency_symbol($booking->ChargedCurrency->value);?>
		<div class="booking_section green_gradient_faded" id="booking_end_step1">
			<h2 class="checked"><?php echo _('Votre réservation est validée');?></h2>
			<a class="print_booking" href="#" onClick="window.print();return false;"><?php echo _('Print Now');?></a>
			<p><?php echo _("Votre réservation est maintenant confirmée. Nous vous conseillons d'imprimer cette page afin de conserver les détails de votre réservation. Vous devrez présenter cette confirmation lors de votre arrivée à "); echo '<strong><a href="'.site_url($this->Db_links->get_link("info").'/'.url_title($propertyName).'/'.$propertyNumber).'">'.$booking->PropertyDetails->propertyName.'</a></strong>.';?>
			</p>
	
			<p><strong><?php echo _("Vous allez recevoir un courriel d'ici peu avec toutes les informations contenu sur cette page.");?></strong> <?php echo _("Vous pouvez aussi vous connecter à votre compte pour voir toutes vos réservation:");?></p>
	
			<p><strong><a href="<?php echo site_url($this->Db_links->get_link("user")); ?>"><?php echo _("Vous connecter");?> &raquo;</a></strong></p>
			</div>
			
			<div class="booking_section" id="booking_end_step2">
				<?php if (!empty($booking->ChargedCurrencyWarning->note)):?>
				<h2 class="booking_section_title green_gradient_faded  box_round"><?php echo _("Informations Importantes");?></h2>
				<div class="booking_section_content">
					<?php if (!empty($booking->ChargedCurrencyWarning->noteTranslated)){?>
						<p><?php echo $booking->ChargedCurrencyWarning->noteTranslated;?>
					<?php }else{?>
						<p><?php echo $booking->ChargedCurrencyWarning->note;?>
					<?php }?>
					&nbsp;(<strong><?php echo $booking->ChargedCurrencyWarning->oldCurrency;?> &rArr; <?php echo $booking->ChargedCurrencyWarning->newCurrency;?></strong>)</p>
					</div>
					<?php endif;?>
					<h2 class="booking_section_title green_gradient_faded box_round"><?php echo _('Détails de la réservation');?></h2>
					<div class="booking_section_content" >
					<p><?php echo _('Votre Numéro de confirmation');?> : <strong>HW-<?php echo $booking->CustomerReference->value;?></strong></p>
			
					<p><?php echo _("Nom de l'établissement");?>: <strong><?php echo $booking->PropertyDetails->propertyName;?></strong>, <?php echo $booking->PropertyDetails->address;?>, <?php echo $booking->PropertyDetails->city;?></p>
			
					<p class="arrival-dep"><?php printf(gettext('Arrivée: %s à %s'),"<b>".date_conv($dateStart_calculated, $this->wordpress->get_option('aj_date_format'))."</b>","<b>".$book_arrival_time.":00</b>");?> &nbsp; &nbsp; <?php printf(gettext('Nombre de Nuits: %s'),"<b>".$numNights_calculated."</b>");?></b></p>
					
					
					<table class="review" cellpadding="0" cellspacing="0">
						<?php
						$total = 0;
						$dormroomcount = 0;
							 //Output dorm rooms booked
							 foreach($booking->RoomDetails as $room)
							 {
								 if(substr_count($countdorm = $room->roomType,"Private") <= 0)
								 {
									 if($dormroomcount == 0)
									 {
										 ?>
										 <thead>
										 <tr valign="middle" align="center">
												<th class="first-cell green-th"><?php echo _('Date');?></th>
												<th class="green-th"><?php echo _('Chambres partagées - Dortoirs');;?></th>
												<th class="green-th"><?php echo _('Prix');?></th>
												<th class="green-th"><?php echo _('Personnes');?></th>
												<th width="18%" class="last-cell green-th"><?php echo _('Total');?></th>
										 </tr>
										 </thead>
										 <tbody>
										 <?php
									 }
									 $dormroomcount++;
									 ?>
									 <tr>
											<td class="first-cell"><?php echo date_conv($room->date, $this->wordpress->get_option('aj_date_format'));?></td>
											<td>
												<?php
													if(!empty($room->roomTypeDescriptionTranslated))
													{
													 echo '<span>'.$room->roomTypeDescriptionTranslated.'</span>';
													echo '<span style="display:block;">('.$room->roomTypeDescription.')</span>';
													}
													else
													{
														echo $room->roomTypeDescription;
													}
													if($breakfast_included == 1){
														echo '<span class="free-breakfast">';
														echo _('Breakfast Included');
														echo '</span>';
													}
												?>
											</td>
											<td>
		
											 <?php echo $cur;?> <?php echo $room->priceSettle;?>
											</td>
											<td><?php echo $room->beds;?></td>
											<td class="value">
		
											 <?php echo $cur;?>  <?php echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?>
											</td>
									 </tr>
									 <?php
									 $total = $total + number_format((float)($room->priceSettle)*($room->beds),2,'.','');
								 }
							 }
		
							 $privateroomcount = 0;
							 //Output private rooms booked
							 foreach($booking->RoomDetails as $room)
							 {
								 if($countprivate = substr_count($room->roomType,"Private") > 0)
								 {
									 if($privateroomcount == 0)
									 {
										 ?>
										 <thead>
										 <tr valign="middle" align="center">
												<th class="first-cell green-th<?php if ($countdorm > 0){?> noround<?php }?>"><?php echo _('Date');?></th>
												<th class="green-th"><?php echo _('Chambres privées');;?></th>
												<th class="green-th"><?php echo _('Prix');?></th>
												<th class="green-th"><?php echo _('Rooms');?></th>
												<th width="18%" class="last-cell green-th<?php if ($countdorm > 0){?> noround<?php }?>"><?php echo _('Total');?></th>
										 </tr>
										 </thead>
										 <?php
									 }
									 $privateroomcount++;
									 ?>
									 <tr>
											<td class="first-cell"><?php echo date_conv($room->date, $this->wordpress->get_option('aj_date_format'));?></td>
											<td>
												<?php
													if(!empty($room->roomTypeDescriptionTranslated))
													{
													 echo '<span>'.$room->roomTypeDescriptionTranslated.'</span>';
													echo '<span style="display:block;">('.$room->roomTypeDescription.')</span>';
													}
													else
													{
														echo $room->roomTypeDescription;
													}
													if($breakfast_included == 1){
														echo '<span class="free-breakfast">';
														echo _('Breakfast Included');
														echo '</span>';
													}
												?>
											</td>
											<td>
		
											 <?php echo $cur;?> <?php echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?>
											</td>
											<td>1<?php //echo $room->beds;?></td>
											<td class="value">
		
											 <?php echo $cur;?>  <?php echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?>
											</td>
									 </tr>
									 <?php
									 $total = $total + number_format((float)($room->priceSettle)*($room->beds),2,'.','');
								 }
							 }
							 ?>
							 </tbody>
							</table>
							<table class="review-end" cellpadding="0" cellspacing="0">							
								<tr class="dark">
								 <td class="first" align="right" colspan="4"><b><?php echo _('Total');?>:</b>&nbsp;</td>
								 <td class="total-value"><b><?php echo $cur;?><?php echo number_format($total,2,'.','');?></b></td>
								</tr>
								<tr class="first">
								 <td align="right" colspan="4">
								 <?php printf(gettext('10%% Arrhes + frais de service facturé en %s à votre carte'),$booking->ChargedCurrency->value); ?>:
								 </td>
		
								 <td>
									 <b><?php echo $cur;?><?php echo $booking->AmountCharged->value;?></b>
								 </td>
								</tr>
		
								<tr class="end-total dark">
									<td class="first" align="right" colspan="4">
											<span id="bookingFeeDesc"><?php echo _('Montant restant à payer lors de votre arrivée');?> (<?php echo $booking->PropertyDetails->currency;?>): </span>
											</td>
											<td>
											<span style="display: inline;"><b><?php echo currency_symbol($booking->PropertyDetails->currency);?><?php echo $booking->PropertyAmountDue->value;?></b></span>
											<?php if($isCustomCurrency):?>
											<span class="custom-cur">(~<?php echo $bookCurrency;?><?php echo $booking->$bookAmountDueField->value;?>)</span>
											<?php endif;?>
									</td>
								</tr>
							
						</table>
				</div>
			</div>
   		
			<div class="booking_section">
				<h2 class="booking_section_title green_gradient_faded  box_round"><?php echo _("Information sur l'établissement");?></h2>
				<div class="booking_section_content">		
					<p class="conf-detail"><?php echo _("Nom de l'établissement");?>:
							<span><?php echo $booking->PropertyDetails->propertyName;?></span>
					</p>
		
					<p class="conf-detail"><?php echo _("Adresse");?>:
							<span><?php echo $booking->PropertyDetails->address;?></span>
							<span><?php if(!empty($booking->PropertyDetails->city)){ echo $booking->PropertyDetails->city.',';}?><?php echo $booking->PropertyDetails->state;?></span>
							<span><?php echo $booking->PropertyDetails->postCode;?></span>
					</p>
		
					<p class="conf-detail"><?php echo _("Phone number")?>:
							<span><?php echo $booking->PropertyDetails->phone;?></span>
					</p>
		
		
					<p class="conf-detail"><?php echo _("Fax");?>:
							<span><?php echo $booking->PropertyDetails->fax;?></span>
					</p>
		
					<p class="conf-detail"><?php echo _("Email");?>:
							<span><a href="mailto:<?php echo $booking->PropertyDetails->email;?>"><?php echo $booking->PropertyDetails->email;?></a></span>
					</p>
				</div>
			</div>
			
			<div class="booking_section">
      	 <h2 class="booking_section_title green_gradient_faded  box_round"><?php echo _("Directions");?></h2>
      	 <div class="booking_section_content">	
				 <?php
      	 if(!empty($booking->PropertyDetails->directionsTranslated))
      	 {
      	   echo $booking->PropertyDetails->directionsTranslated.'<br />';
					 echo '<p><strong>'._("Version Originale").'</strong></p>';
					 echo nl2p($booking->PropertyDetails->directions,false,true);
      	 }
      	 else
      	 {
      	   echo nl2p($booking->PropertyDetails->directions,false,true);
      	 }
      	 ?>
				 </div>
			</div>

			<?php if (!empty($booking->TermsAndConditions->value)){?>
			<div class="booking_section">
				<h2 class="booking_section_title green_gradient_faded box_round"><?php echo _("Conditions");?> - <?php echo $booking->PropertyDetails->propertyName;?></h2>
				<div class="booking_section_content">	
					<?php if (!empty($booking->TermsAndConditionsTranslated->value)){?>
					<div class="translated"><?php echo nl2p(var_check($booking->TermsAndConditionsTranslated->value,""),false,true)?></div>
					<br />
					<p><strong><?php echo _("Version Originale");?></strong></p>
					<?php }?>
	
					<div class="original"><?php echo nl2p(var_check($booking->TermsAndConditions->value,""),false,true)?></div>
				</div>
			</div>
			<div class="booking_section">
				<h2 class="booking_section_title green_gradient_faded box_round"><?php echo _("Conditions");?> - <?php echo $this->wordpress->get_option('aj_api_name');?></h2>
				<div class="booking_section_content">	
					<ul class="conditions">
					<?php if ($this->wordpress->get_option('aj_red_email') != true){ ?>
					
						<li><font color="#a90000"><?php echo _("Les annulations doivent être effectuées directement auprès de l'établissement.");?></font></li>
						<li><?php echo _("Si vous ne vous présentez pas à la date prévue et que vous n'avez pas annulé votre réservation, votre carte de crédit sera facturée pour le montant total de votre première nuit de réservation.");?> </li>
						<li><font color="#a90000"><?php echo _("Pour effectuer toute modification à une réservation, veuillez contacter directement l'établissement.");?></font></li>
						<li><?php echo _("Si votre carte de crédit devient invalide ou que des modifications sont apportées à votre compte avant votre arrivée, veuillez contacter directement l'établissement afin de prendre d'autres dispositions.");?> </li>
						<li><?php echo _("En cas d'annulation de votre réservation, le dépôt et les frais de réservation ne peuvent pas être pas remboursés.");?> </li>
						<li><?php echo _("Veuillez s'il vous plaît lire attentivement nos termes et conditions.");?> </li>
						<li><?php printf(gettext("Pour toute question, consultez notre %s."),"<a href=\"".$this->wordpress->get_option('aj_page_faq')."\">"._("Centre d'aide en ligne")."</a>");?></li>
					
					<?php }else{?>
					
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
			<?php }else{?>
			
			<div class="booking_section">
				<h2 class="booking_section_title green_gradient_faded box_round"><?php echo _("Conditions");?></h2>
				<div class="booking_section_content">	
					<p class="conditions">
		
						<?php echo _("Vous devez payer pour votre chambre à votre arrivée. Veuillez, s'il vous plaît, vous assurer que votre carte de crédit est valide. Les cartes sont vérifiées la veille des arrivées et le fait de réserver à l'aide d'une carte invalide annulera votre réservation. Si vous avez réservé à l'aide de votre carte bancaire ou si vous doutez de la validité de votre carte de crédit, veuillez contacter l'établissement au moins 24 heures avant votre arrivée afin de confirmer votre réservation.");?><br>
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
			<?php } ?>
		</div>
		<?php $googana_code = $this->wordpress->get_option('aj_google_analytic_account_no'); if(!empty($googana_code)){ ?>
		<script>
			 var _gaq = _gaq || [];
				_gaq.push(['_setAccount', '<?php echo $googana_code; ?>']);
				_gaq.push(['_trackPageview', '/click/complete']);
			 _gaq.push(['_addTrans',
				'HW-<?php echo $booking->CustomerReference->value;?>',           // order ID - required
				'HW',  // affiliation or store name
				'<?php echo $booking->CADDepositAmount->value;?>',          // total - required
				'',           // tax
				'',              // shipping
				'<?php echo $booking->PropertyDetails->city;?>',       // city
				'',     // state or province
				''             // country
			]);
		
			 // add item might be called for every item in the shopping cart
			 // where your ecommerce engine loops through each item in the cart and
			 // prints out _addItem for each
			_gaq.push(['_addItem',
				'HW-<?php echo $booking->CustomerReference->value;?>',           // order ID - required
				'<?php echo $propertyNumber;?>',           // SKU/code - required
				'<?php echo $booking->PropertyDetails->propertyName;?>',        // product name
				'Nights-<?php echo $numNights_calculated;?>',   // category or variation
				'<?php echo $booking->CADDepositAmount->value;?>',          // unit price - required
				'1'               // quantity - required
			]);
			_gaq.push(['_trackTrans']); //submits transaction to the Analytics servers
		</script>
		<?php }?>
		
   <?php echo $this->wordpress->get_option('aj_google_adword'); ?>
