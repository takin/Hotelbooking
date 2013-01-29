<div id="content" class="confirm-view">
	
	<div class="page-meta group">
		<h1 class="text-shadow-wrapper dot-icon"><?php echo _('Détails de la réservation'); ?></h1>
	</div>
	
	<div class="submit-button">
		<input type="button" onClick="document.getElementById('booking-form').submit();" class="submit-green green-button" value="<?php echo _("Proceed To Payment"); ?> &raquo;" />
		
	</div>
  
	<div class="white-back round-corner5 border-around form">
		
		<h2><?php echo _("Nom de l'établissement");?> :</h2>
		<p><strong><?php echo $booking_hostel_name;?></strong>, <?php echo $propertyCity; ?>, <?php echo $propertyCountry; ?>
		<br /><?php echo _('Arrivée');?>: <b><?php echo $dateStart_calculated->format("d F Y");?></b> &nbsp; &nbsp; <?php echo _('Nombre de Nuits');?>: <b><?php echo $numNights_calculated; ?></b></p>
		
		<?php /*?><a href="#" class="white-button round-corner5 change-dates"><span class="arrow">Change the dates</span></a><?php */?>
		
    <?php 
      if(!empty($booking_request->Message->messageTextTranslated))
      {
        ?>
        <p class="message-booking">
        <?php
        echo '<span class="translated">'.$booking_request->Message->messageTextTranslated.'</span>';
        echo '<span class="original" style="display:none;">'.$booking_request->Message->messageText.'</span>';
        ?>
        </p>
        <?php
      }
      elseif(!empty($booking_request->Message->messageText))
      {
        ?>
        <p class="message-booking">
        <?php
        echo $booking_request->Message->messageText;
        ?>
        </p>
        <?php
      }
      ?>
			
		<table cellspacing="0" cellpadding="0" class="review">
        <?php 
        $cur                   = currency_symbol($settleCurrency);
        $bookCurSymbol         = currency_symbol($bookCurrency);
        $bookRoomPriceField    = "price$bookCurrency";
        $bookTotalPriceField   = $bookCurrency."BedsTotal";
        $bookDepositPriceField = $bookCurrency."Deposit";
        $bookBookFeeField      = $bookCurrency."BookingFee";
        $bookBillTotalField    = $bookCurrency."BillTotal";
        $bookAmountDueField    = $bookCurrency."AmountDue";
        $isCustomCurrency      = (strcasecmp($settleCurrency,$bookCurrency)!=0);
        $dormroomcount = 0;
        $SPACE = '&nbsp;';
        //Output dorm rooms booked
        foreach($booking_request->RoomDetails as $room)
        {
          if($countdorm = substr_count($room->roomType,"Private") <= 0)
          {
            if($dormroomcount == 0)
            {
              ?>
               <thead>
         				<tr valign="middle" align="center">
         					<th class="white-gradient first-cell"><?php echo _('Day');?></th>
         					<th class="white-gradient"><?php echo _('Chambres partagées - Dortoirs');?></th>
         					<th class="white-gradient"><?php echo _('Personnes');?></th>
         					<th class="white-gradient"><?php echo _('Total');?></th>
         				</tr>
         			 </thead>
               <tbody>
              <?php
            }
            $dormroomcount++;
            ?>
            <tr>
               <td class="first-cell"><?php echo substr($room->date, -2);?></td>
               <td>
                 <?php 
                   if(!empty($room->roomTypeTranslated))
                   {
                    echo '<span>'.$room->roomTypeTranslated.'</span>';
                   echo '<span style="display:block;">('.$room->roomType.')</span>';
                   }
                   else
                   {
                     echo $room->roomType;
                   }
                 ?>
               </td>
               <td><?php echo $room->beds;?></td>
               
               <td class="value">
                <?php 
                if($isCustomCurrency)
                {
                  echo $bookCurSymbol.$SPACE.number_format((float)($room->$bookRoomPriceField)*($room->beds),2,'.','');
                }
                else 
                {
                  echo $cur.$SPACE;?>  <?php echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');
                }
                ?>
               </td>
            </tr>
            <?php 
          }
        }
         $privateroomcount = 0;
        //Output private rooms booked
        foreach($booking_request->RoomDetails as $room)
        {
          if($countprivate = substr_count($room->roomType,"Private") > 0)
          {
            if($privateroomcount == 0)
            {
              ?>
              <thead>
                <tr valign="middle" align="center">
                  <th class="white-gradient first-cell"><?php echo _('Day');?></th>
                  <th class="white-gradient"><?php echo _('Chambres privées');?></th>
                  <th class="white-gradient"><?php echo _('Rooms');?></th>
                  <th class="white-gradient"><?php echo _('Total');?></th>
                </tr>
               </thead>
               <tbody>
              <?php
            }
            $privateroomcount++;
            ?>
            <tr>
               <td class="first-cell"><?php echo substr($room->date, -2);?></td>
               <td>
                 <?php 
                   if(!empty($room->roomTypeTranslated))
                   {
                    echo '<span>'.$room->roomTypeTranslated.'</span>';
                   echo '<span style="display:block;">('.$room->roomType.')</span>';
                   }
                   else
                   {
                     echo $room->roomType;
                   }
                 ?>
               </td>
               <td>1<?php //echo $room->beds;?></td>
               <td class="value">
               <?php 
                if($isCustomCurrency)
                {
                  echo $bookCurSymbol.$SPACE.number_format((float)($room->$bookRoomPriceField)*($room->beds),2,'.','');
                }
                else 
                {
                  echo $cur.$SPACE;?>  <?php echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');
                }
                ?>
               </td>
            </tr>
            <?php 
          }
        }
        ?>
				</tbody>
		</table>	
		<table cellspacing="0" cellpadding="0" class="review-price"> 
			<tfoot>
			
			<tr class="dark">
				<td align="right"><b><?php echo _('Total en');?> <?php if($isCustomCurrency):?><?php echo $bookCurrency;?><?php else:?><?php echo $settleCurrency;?><?php endif; ?>:</b></td>
				<td class="total-value price">
					<b>
          <?php if($isCustomCurrency):?> 
          <?php echo $bookCurSymbol.$SPACE.$booking_request->$bookTotalPriceField->value;?>
          <?php else:?>
          <?php echo $cur.$SPACE;?> <?php echo $booking_request->SettleBedsTotal->value;?>
          <?php endif; ?>
					</b>
				</td>
			 </tr>
			<tr >
			 <td align="right"><?php echo _('10% Arrhes / Dépôt sera facturé en');?> <?php echo $settleCurrency;?>:</td> 
			 <td class="price">
					<b>
          <?php echo $cur.$SPACE;?> <?php echo $booking_request->SettleDeposit->value;?> 
					</b>
			 </td>
			</tr>
			
			<tr class="dark">
			 <td align="right">
			 
					 <span id="bookingFeeDesc"><?php echo _('Frais de Service')?>:</span></td> 
					 <td class="price">
					 <span style="display: inline;">
						 <b>
             <?php echo $cur.$SPACE;?> <?php echo $booking_request->SettleBookingFee->value;?> 
						 </b>
					 </span>
					 
			 </td>
			</tr>
			
			<tr >
			 <td align="right">
			 
					 <span id="bookingFeeDesc">
           <strong>
															
					 <?php switch($settleCurrency){
                   case 'EUR':
                   $member = '14.00 '.$cur;
                   break;
                   
                   case 'USD':
                   $member = $cur.'20.00';
                   break;
                   
                   case 'GBP':
                   $member = $cur.'12.00';
                   break;
                   
                   default:
                   $member = '14.00'.$cur;
                   break;
           }?>
           
           <?php printf(gettext("%s yearly membership card - waived:"),$member); ?>
           </strong>
           </span>
           </td> 
					 <td class="price">
					 <span style="display: inline;">
						 <b><?php echo $cur.$SPACE;?> 0.00</b>
					 </span>
					 
			 </td>
			</tr>
			
										 
			<tr class="end-total">
			 <td align="right" class="white-gradient"><?php echo _('Total à payer maintenant');?> (<?php echo $settleCurrency;?>):</td>
			 <td class="price white-gradient">
					<span style="display: inline;">
					<b>
						<?php echo $cur.$SPACE;?> <?php echo $booking_request->SettleBillTotal->value;?> 
					</b>
					</span>
					
				</td>          
			</tr>
			
						
			<tr class="last-cell">
			 <td colspan="2" class="dotted">
				 <b><?php echo $booking_request->PropertyCurrency->value;?> <?php echo $booking_request->PropertyAmountDue->value;?></b>  
				 <span style="display: inline;">                 
				  <?php if($isCustomCurrency):?>
           	<?php if ($booking_request->PropertyCurrency->value == $bookCurrency):?>
            	(<?php echo $booking_request->SettleAmountDue->value;?> <?php echo $cur;?>)
            <?php else:?>
            	(<?php echo '~ '.$booking_request->$bookAmountDueField->value.$SPACE.$bookCurSymbol;?>)
            <?php endif;?>
            
           <?php else :?>
						 <?php if(strcasecmp($booking_request->PropertyCurrency->value,$settleCurrency)!=0):?>
              (<?php echo $booking_request->SettleAmountDue->value;?> <?php echo $SPACE.$cur;?>)
             <?php endif; ?> 
					 <?php endif;?>
           </span>
                 
           <?php echo _('est le montant total à payer à votre arrivée');?>  
			 </td>
			</tr>
			
			
			</tfoot>
            
      </table>
			
		</div>
		<div class="white-back round-corner5 border-around form change-currency group">	
			
			<form id="change-settle-form" action="<?php echo site_url("mbooking_confirmation"); ?>" method="post" >
				<input type="hidden" name="book-propertyName" value="<?php echo $booking_hostel_name; ?>" />
				<input type="hidden" name="propertyCity" value="<?php echo $propertyCity; ?>" />
				<input type="hidden" name="propertyCountry" value="<?php echo $propertyCountry; ?>" />
				<input type="hidden" name="book-propertyNumber" value="<?php echo $propertyNumber; ?>" />
				<input type="hidden" name="book-dateStart" value="<?php echo $dateStart; ?>" />
				<input type="hidden" name="book-numNights" value="<?php echo $numNights; ?>" />
				<?php 
				foreach($roomPreferences as $index => $roompref)
				{
					?>
					<input type="hidden" name="book-roomPreferences[]" value="<?php echo $roompref; ?>" />
					<input type="hidden" name="book-nbPersons[]" value="<?php echo $nbPersons[$index]; ?>" />
					<?php
				}
				?>
				
				<input type="hidden" name="book-currency" value="<?php echo $bookCurrency; ?>" />
				
				<select id="settle-currency" name="settle-currency">
					<option <?php if(strcasecmp($settleCurrency,"EUR")==0) echo "selected=\"selected\""; ?> value="EUR">EUR</option>
					<option <?php if(strcasecmp($settleCurrency,"GBP")==0) echo "selected=\"selected\""; ?> value="GBP">GBP</option>
					<option <?php if(strcasecmp($settleCurrency,"USD")==0) echo "selected=\"selected\""; ?> value="USD">USD</option>
				</select>
				<script type="text/javascript">
				var selectmenu = document.getElementById("settle-currency");
				
				selectmenu.onchange = function()
				{
				//set cookie
					var exdate=new Date();
					exdate.setDate(exdate.getDate() + 60);
					var c_value=escape(selectmenu.value) + '; expires='+exdate.toUTCString();
					document.cookie = "settle_currency=" + c_value + '; path=/';
          
				  document.getElementById('change-settle-form').submit();
				}
				</script>
			</form>
			<span class="cur-title"><?php echo _("Choose the currency of your payment:");?></span>
		</div>
		<div class="white-back round-corner5 border-around form">	
			
		  <h2><?php echo _("Changements - Annulations"); ?></h2>
			<div class="important-info">	
				<?php if(empty($booking_request->TermsAndConditions->value)){?>
				<p><strong><?php echo _("Les changements et annulations sont possibles, faciles à faire et se font directement avec l‘établissement choisi qui en est responsable. Changements de dates/chambres : possible selon disponibilités. Annulations : au moins 72 heures en avance pour ne pas être facturé la première nuit.");?></strong></p>
				<?php }?>
				<p><b><span class="notes"><?php echo _("Notes Importantes");?></span></b>
				<?php printf( gettext("Sur %s, vous payez seulement les arrhes (10%% du montant total) et les frais de service minimum et non remboursables afin de confirmer et garantir votre réservation. Le montant restant vous sera demandé à votre arrivée."),$this->config->item('site_name'));?></p>
				
				<?php if (!empty($booking_request->TermsAndConditionsTranslated->value)){?>
				<?php echo nl2p(var_check($booking_request->TermsAndConditionsTranslated->value,""),false,true)?>
				<?php }elseif(!empty($booking_request->TermsAndConditions->value)){ ?>
				<?php echo nl2p(var_check($booking_request->TermsAndConditions->value,""),false,true)?>
				<?php }?>
				
			</div>
		</div>
			
  <form id="booking-form" class="basic" action="<?php echo secure_site_url('mbooking');?>" method="post">
    
    <input type="hidden" name="bsid" value="<?php echo (string) $booking_request->BSID->value; ?>" />
    <input type="hidden" name="propertyNumber" value="<?php echo $propertyNumber; ?>" />
    <input type="hidden" name="propertyCity" value="<?php echo $propertyCity; ?>" />
    <input type="hidden" name="propertyCountry" value="<?php echo $propertyCountry; ?>" />
    <input type="hidden" name="settleCurrency" value="<?php echo $settleCurrency; ?>" />
    <input type="hidden" name="bookCurrency" value="<?php echo $bookCurrency; ?>" />
    <input type="hidden" name="settle_bill_total" value="<?php echo $booking_request->SettleBillTotal->value;?>" />
		<?php 
		foreach($booking_request->CardInfo->card as $card)
		{
		  ?>
      <input type="hidden" name="cardsupported[]" value="<?php echo (string)$card->type."|".(string)$card->name."|".(string)$card->currency."|".(string)$card->IssueNO."|".(string)$card->CCValidFrom; ?>" />
      <?php
		}
		?>
    
	<div class="submit-button">
		<input type="submit" class="submit-green green-button" value="<?php echo _("Proceed To Payment"); ?> &raquo;" />
		
	</div>
	</form>
  
	
 
</div>
