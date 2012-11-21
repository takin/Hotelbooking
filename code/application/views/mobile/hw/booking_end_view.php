<!--<div id="content" class="booking-end-view">-->
	<div class="page-meta group centerit">
		<h1 class="text-shadow-wrapper"><?php echo _('Confirmation de Votre Réservation');?></h1>
	</div>
	
		<div class="white-back round-corner5 border-around form">
			<h2 class="dashed-title mark"><?php echo _('Votre réservation est validée');?></h2>
			<?php /*?><p><?php echo _("Votre réservation est maintenant confirmée. Nous vous conseillons d'imprimer cette page afin de conserver les détails de votre réservation. Vous devrez présenter cette confirmation lors de votre arrivée à "); echo '<strong><a href="'.site_url($this->Db_links->get_link("info").'/'.url_title($propertyName).'/'.$propertyNumber).'">'.$booking->PropertyDetails->propertyName.'</a></strong>.';?>
      </p><?php */?>
			
			<p><?php echo _("Vous allez recevoir un courriel d'ici peu avec toutes les informations contenu sur cette page.");?> <?php echo _("Vous pouvez aussi vous connecter à votre compte pour voir toutes vos réservation:");?></p>
			
			<?php /*?><p><strong><a href="<?php echo site_url($this->Db_links->get_link("user")); ?>"><?php echo _("Vous connecter");?> &raquo;</a></strong></p>
			
			<?php if (!empty($booking->ChargedCurrencyWarning->note)):?>
			<h3 class="dashed-title"><?php echo _("Informations Importantes");?></h2>
			
			<?php if (!empty($booking->ChargedCurrencyWarning->noteTranslated)){?>
				<p><?php echo $booking->ChargedCurrencyWarning->noteTranslated;?>				
			<?php }else{?>
				<p><?php echo $booking->ChargedCurrencyWarning->note;?>			
			<?php }?>
			&nbsp;(<strong><?php echo $booking->ChargedCurrencyWarning->oldCurrency;?> &rArr; <?php echo $booking->ChargedCurrencyWarning->newCurrency;?></strong>)</p>						
		
      <?php endif;?><?php */?>
			
			<h2 class="dashed-title"><?php echo _('Détails de la réservation');?></h2>
      <div class="inner-table round-corner5">
				<p><?php echo _('Votre Numéro de confirmation');?> :<br /> <strong>HW-<?php echo $booking->CustomerReference->value;?></strong></p>
				<p><strong><a href="<?php echo site_url($this->Db_links->get_link("info").'/'.url_title($booking->PropertyDetails->propertyName).'/'.$booking_data["propertyNumber"]);?>"><?php echo $booking->PropertyDetails->propertyName;?>, <?php echo $booking_data["propertyCity"]; ?>, <?php echo $booking_data["propertyCountry"]; ?></a></strong></p>
				<p><?php echo _('Arrivée le:');?><br /> <strong><?php echo $dateStart_calculated; ?></strong></p>
				<p><?php echo _('Nuits:');?>:<br /> <strong><?php echo $numNights_calculated; ?></strong></p>
			</div>
			<br />
			<h2 class="dashed-title"><?php echo _("Room type and number of guests");?>:</h2>
			
      <?php 
      $total = 0;
      $dormroomcount = 0;
      
      $cur = currency_symbol($booking->ChargedCurrency->value);
      
      //Output dorm rooms booked
      foreach($booking->RoomDetails as $room)
      {
       if(substr_count($countdorm = $room->roomType,"Private") <= 0)
       {
         if($dormroomcount == 0)
         {
           ?>
            <div class="inner-table round-corner5">
            <h3 class="dashed-title"><?php echo _('Chambres partagées - Dortoirs'); ?>: <?php echo _('Price per person (Dorm shared with others).'); ?></h3>
            <table class="avail" cellspacing="0" cellpadding="0">     
              <tbody>
           <?php
         }
         $dormroomcount++;
         ?>
          <tr>
           <?php 
             $room_type_display = $room->roomType;
            if(!empty($room->roomTypeTranslated))
            {
              $room_type_display = $room->roomTypeTranslated." (".$room->roomType.")";
            }
          ?>
            <td class="room-name"><?php echo $room_type_display;?></td>
            <td class="price"><?php echo $cur;?>  <?php echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?>
            <td class="select-guest">
              <img src="<?php echo site_url("images/mobile/user-share.png");?>" alt="" /> x <?php echo (int)$room->beds;?>  
            </td>
          </tr>
         <?php 
         $total = $total + number_format((float)($room->priceSettle)*($room->beds),2,'.','');
       }
      }
      if($dormroomcount > 0)
      {
        ?>
          </tbody>
        </table>      
        </div>
        <?php
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
           <div class="inner-table round-corner5">
            <h3 class="dashed-title"><?php echo _('Chambres privées'); ?>: <?php echo _('Price per room (not per person).'); ?></h3>
            <table class="avail" cellspacing="0" cellpadding="0">     
              <tbody>
           <?php
         }
         $privateroomcount++;
         ?>
         <tr>
           <?php 
             $room_type_display = $room->roomType;
            if(!empty($room->roomTypeTranslated))
            {
              $room_type_display = $room->roomTypeTranslated." (".$room->roomType.")";
            }
          ?>
          <td class="room-name"><?php echo $room_type_display;?></td>
          <td class="price"><?php echo $cur;?> <?php echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?></td>
          <td class="select-guest">
            <img src="<?php echo site_url("images/mobile/user-room.png");?>" alt="" /> x 1  
          </td>
        </tr>
         <?php 
         $total = $total + number_format((float)($room->priceSettle)*($room->beds),2,'.','');
       }
      }
      
      if($privateroomcount > 0)
      {
        ?>
          </tbody>
          </table>    
        </div>
        <?php
      }
      ?>
			
      
			<div class="inner-table round-corner5">
			<table class="price-review" cellspacing="0" cellpadding="0">			
					<tbody>
						<tr>
							<td valign="top" class="row-name"><strong><?php echo _('Total');?></strong></td>
							<td valign="top" class="total-price"><span class="value"><?php echo $cur;?> <?php echo number_format($total,2,'.','');?></span></td>							
						</tr>
						<tr>
							<td valign="top" class="row-name even"><?php printf(gettext('10%% Arrhes + frais de service facturé en %s à votre carte'),$cur); ?></td>
							<td valign="top" class="total-price even"><span class="value"><?php echo $cur;?> <?php echo $booking->AmountCharged->value;?></span></td>							
						</tr>
						<tr>
							<td valign="top" class="row-name last"><?php echo _('Montant restant à payer lors de votre arrivée');?></td>
							<td valign="top" class="total-price last"><span class="value"><?php echo currency_symbol($booking->PropertyDetails->currency);?> <?php echo $booking->PropertyAmountDue->value;?></span>
              <?php if($isCustomCurrency):?> 
                  <span class="foreign">(~ <?php echo currency_symbol($booking_data['bookCurrency']);?> <?php echo $booking->$bookAmountDueField->value;?>)</span>
                  <?php endif;?>
              </td>							
						</tr>
					</tbody>
				</table>		
			</div>
			<br />
			<h2 class="dashed-title"><?php echo _("Information sur l'établissement");?></h2>
      <div class="inner-table round-corner5">  
				<p><?php echo _("Nom de l'établissement");?>:<br />
						<span><?php echo (string)$booking->PropertyDetails->propertyName;?></span>
				</p>
		
				<p><?php echo _("Adresse");?>:<br />
						<span><?php echo (string)$booking->PropertyDetails->address;?></span>
						<span><?php echo (string)$booking->PropertyDetails->city;?>, <?php echo $booking->PropertyDetails->state;?>
            <?php 
            if(!empty($booking->PropertyDetails->state))
            {
              echo ", ".$booking->PropertyDetails->state;
            }
            ?></span>
						<span><?php echo (string)$booking->PropertyDetails->postCode;?></span>
				</p>
		
				<p class="conf-detail"><?php echo _("Téléphone");?>:<br />
						<span><?php echo (string)$booking->PropertyDetails->phone;?></span>
				</p>
				
				<?php
				if(!empty($booking->PropertyDetails->fax))
				{
				  ?>
          <p class="conf-detail"><?php echo _("Fax");?>:<br />
            <span><?php echo (string)$booking->PropertyDetails->fax;?></span>
          </p>
          <?php
				}
				?>
			
				<p class="conf-detail"><?php echo _("Email");?>:<br />
						<span><a href="mailto:<?php echo (string)$booking->PropertyDetails->email;?>"><?php echo (string)$booking->PropertyDetails->email;?></a></span>
				</p>
			</div>		
			<br />
			<h2 class="dashed-title"><?php echo _("Directions");?></h2>
			<p> 
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
      </p>
	
			<h2 class="dashed-title"><?php echo _("Conditions");?></h2>
			 
			  <?php if (!empty($booking->TermsAndConditions->value)){?>
                    
          <?php echo _("Conditions");?> - <?php echo $booking->PropertyDetails->propertyName;?>
          
					<?php if (!empty($booking->TermsAndConditionsTranslated->value)){?>
         	<?php echo nl2p(var_check($booking->TermsAndConditionsTranslated->value,""),false,true);?>          
          <?php }else{?>
					<?php echo nl2p(var_check($booking->TermsAndConditions->value,""),false,true);?>
					<?php }?>
					
       <?php }else{?>
			 
			 <p><?php echo _("Vous devez payer pour votre chambre à votre arrivée. Veuillez, s'il vous plaît, vous assurer que votre carte de crédit est valide. Les cartes sont vérifiées la veille des arrivées et le fait de réserver à l'aide d'une carte invalide annulera votre réservation. Si vous avez réservé à l'aide de votre carte bancaire ou si vous doutez de la validité de votre carte de crédit, veuillez contacter l'établissement au moins 24 heures avant votre arrivée afin de confirmer votre réservation.");?></p>
			 <?php }?>
			 
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
		
		<div class="book-now">
			<a class="white green-button"  href="<?php echo site_url('m');?>"><span class="link"><?php echo _("Search for another hostel");?></span></a>
		</div>
		<?php echo $this->wordpress->get_option('aj_google_adword'); ?>
	
<!--</div>-->
