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
?>

<?php // Sidebar ?>
<?php if(!$isAjax):?>
<div class="grid_4" id="sidebar">	
	<div class="booking_end_widget">
	<a class="button-green box_round box_shadow_hard new_booking hoverit" href="<?php echo base_url();?>"><?php echo _('New booking')?> &raquo;</a>
	</div>
	<?php if ($this->wordpress->get_option('aj_social_facebook') != ''){ ?>
	<div class="box_content group box_round booking_end_widget facebook_join">
		<a class="facebook_link" href="<?php echo $this->wordpress->get_option('aj_social_facebook')?>"><?php echo _('Join us on Facebook')?></a>
		<a class="" href="<?php echo $this->wordpress->get_option('aj_social_facebook')?>"><img src="<?php echo secure_site_url();?>images/V2/icon_large_facebook.png" alt="<?php echo _('Join us on Facebook')?>" /></a>
	</div>
	<?php }?>
	<div class="box_content group box_round booking_widget">
		<p style="margin-bottom:0px;"><b><?php echo _('Méthodes de paiement acceptée');?>:</b>
		<img class="ccard" style="padding-left:10px;" src="<?php echo secure_site_url();?>images/ccard.gif" alt="<?php echo _("carte de crédit");?>" /><br />
		<?php echo _("ainsi que plusieurs autres en fonction de l'établissement choisi");?>.
		</p>
	</div>
	<?php
	if(!empty($booking_request->Message->messageTextTranslated) || !empty($room->roomTypeDescriptionTranslated) || !empty($booking_request->TermsAndConditionsTranslated->value))
	{?>
	<div class="box_content group box_round booking_widget">
	
    <div class="switch-translate clearfix">
        <p><?php echo _("Certains extraits du texte de cette page ont été traduits automatiquement. Cliquer sur les liens suivants changer les version :"); ?>
        <br /><a class="show-original" href="#"><?php echo _("Voir l'original"); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="show-translate active" href="#"><?php echo _("Voir la version traduite"); ?></a>
        </p>
    </div>
   
	</div>
	<?php }?>
	
	<div class="box_content group box_round site-info-box">
	<ul class="site-info group">
	 <?php if ($this->wordpress->get_option('aj_api_ascii')==""){$csspath = $this->wordpress->get_option('aj_api_name');}else{$csspath = $this->wordpress->get_option('aj_api_ascii');} ?>
	 <li class="first" id="rules"><img height="38px" src="<?php echo secure_site_url();?>images/<?php echo $csspath; ?>/sideinfo-rules.png" alt="" /><span><?php printf(gettext("%s est réglementé par l'Union Européenne."),"<strong>".$this->config->item('site_name')."</strong>");?></span></li>
	 <li id="secure"><img height="38px" src="<?php echo secure_site_url();?>images/GandiSSL2.png" alt="" /><span><strong><?php printf(gettext("100%% sécurisé."));?></strong> <?php echo _("Paiements sécurisés et encryptés pour votre sécurité.");?></span></li>
	 <li id="bestprice"><img height="38px" src="<?php echo secure_site_url();?>images/sideinfo-10percent.png" alt="" /><span><?php echo _("Seulement 10% pour garantir votre réservation.");?></span></li>
	 <?php /*?><li id="support"><img height="38px" src="<?php echo secure_site_url();?>images/sideinfo-support.png" alt="" /><br /><span><?php printf(gettext("Un service clientèle de qualité disponible %s prêt à vous guider à tout moment."),"<b>"._("24h/24, 7j/7")."</b>");?></span></li><?php */?>
	 <li id="support"><img height="38px" src="<?php echo secure_site_url();?>images/sideinfo-support.png" alt="" /><br /><span><?php echo _('Text/SMS (FREE)')?></span></li>
	 <?php /*?><li id="forall"><img height="38px" src="<?php echo secure_site_url();?>images/sideinfo-forall.png" alt="" /><br /><span><?php echo _("Pour tous les âges: ni maximum ni minimum.");?></span></li><?php */?>
	 <li id="forall"><img height="38px" src="<?php echo secure_site_url();?>images/sideinfo-forall.png" alt="" /><span><?php echo _('Check your reservation on your Mobile')?></span></li>
	 <li class="last" id="member"><img height="38px" src="<?php echo secure_site_url();?>images/sideinfo-member.png" alt="" /><span><?php printf(gettext("%s Pas besoin de carte de membre pour recevoir les meilleurs prix du Net."),"<strong>".$this->config->item('site_name')."</strong>");?></span></li>
	</ul>
	</div>
</div>

<div id="main" class="grid_12">
<?php endif;?>
	
  <div class="box_content box_round group booking"> 

	<?php if($api_error==false){ ?>
  <h1 class="content_title"><?php echo _('Réserver maintenant pour garantir votre chambre');?></h1>
	<?php /* if ($this->wordpress->get_option('aj_velaro_id') !=''){?>
	<a href="https://service.velaro.com/visitor/requestchat.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue" target="OnlineChatSoftware"  onClick="this.newWindow = window.open('http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue', 'OnlineChatSoftware', 'toolbar=no,location=no,directories=no,menubar=no,status=no,scrollbars=no,resizable=yes,replace=no');this.newWindow.focus();this.newWindow.opener=window;return false;"><img alt="OnlineChatSoftware" src="https://service.velaro.com/visitor/check.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue" border="0" class="chat-top"></a>
	<?php }else{?>
	<a onclick="this.newWindow = window.open('https://service.velaro.com/visitor/requestchat.aspx?siteid=7548&amp;showwhen=inqueue', 'OnlineChatSoftware', 'toolbar=no,location=no,directories=no,menubar=no,status=no,scrollbars=no,resizable=yes,replace=no');this.newWindow.focus();this.newWindow.opener=window;return false;" target="OnlineChatSoftware" href="http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&amp;showwhen=inqueue"><img border="0" src="https://service.velaro.com/visitor/check.aspx?siteid=7548&amp;showwhen=inqueue" alt="OnlineChatSoftware" class="chat-top"></a>
	<?php } */?>
  <?php /*?><a target="_blank" title="<?php echo _("Transaction 100% sécurisée et encryptée");?>" href="<?php echo $this->wordpress->get_option('aj_ssl_url');?>"><img class="ssl-image-top" src="<?php echo secure_site_url();?>images/GandiSSL2.png" alt="<?php echo _("sécurisé");?>" /></a><?php */?>
  <?php }else{ ?>
  <h1 class="content_title"><?php echo _('Erreur lors du traitement de votre demande');?></h1>

  <div class="entry margtop10">
    <p class="api-error"><?php echo $api_error_msg;?></p>
    <p><strong><a href="javascript:history.back()">&laquo; <?php echo _("Revenir en arrière");?></a></strong></p>
  </div>
  <?php } ?>
	
  <?php if($api_error==false): ?>
  <?php if ($this->wordpress->get_option('aj_api_ascii')==""){$csspath = $this->wordpress->get_option('aj_api_name');}else{$csspath = $this->wordpress->get_option('aj_api_ascii');} ?>
	
	<?php if($api_booking_error=="api_msg"):?>	
	<div class="booking_section">  
	<div class="transaction_error api_error">
	<p><?php printf( gettext("Il y a eu une erreur lors du traitement de votre demande, veuillez vous assurer que vos informations sur la méthode de paiement sont exactes. Si le problème persiste, veuillez communiquer avec nous à l'adresse suivante : %s"),'<a target="_blank" href="'.$this->wordpress->get_option('aj_page_faq').'">'.$this->wordpress->get_option('aj_page_faq').'</a>');?></p>
	<span class="error_title"><?php echo _('Errors')?>:</span>
	<ul><?php foreach($api_booking_error_msg as $error):?>							
					<?php
					if(!empty($error->messageTranslated))
					{
						echo '<li class="translated">'.$error->messageTranslated.'</li>';
						echo '<li class="original" style="display:none;">'.$error->message.'</li>';
					}
					else
					{
						echo $error->message;
					}
					?>							
				<?php endforeach;?>
				<?php if($api_booking_error=="api_out"):?>
					<li class="api_error"><?php echo $api_booking_error_msg[0]; ?></li>
				<?php endif;?>
		</ul>
	</div>
	</div>			
	<?php endif;?>
	<div class="booking_section">  
		<div class="popup-info-wrap">
			<h2 id="step1-title" class="booking_section_title box_round green_gradient_faded question_mark"><span>1. <?php echo _('Détails de la réservation'); ?></span></h2>
			<div class="popup-info booking_view">
				<h4><?php echo _("Notes Importantes");?></h4>
				<p><?php printf( gettext("You only pay the deposit (10%% of total amount) to confirm and secure your reservation now. The remaining amount (90%%) is payable upon arrival. You will find the hotel's contact information (email, address, telephone number…) in your confirmation email after you have made your reservation."),$this->config->item('site_name'));?></p>
				<span class="popup-info-arrow"></span>
			</div>
		</div>
		
		<div class="booking_section_content" id="step1">  
		<p class="arrival-dep">
		<?php echo _("Nom de l'établissement");?> : <strong><?php echo stripslashes($booking_hostel_name); ?></strong><br />
		<?php echo _('Arrivée');?>: <b><?php echo date_conv($dateStart_calculated, $this->wordpress->get_option('aj_date_format')); ?></b> &nbsp; &nbsp; <?php echo _('Nombre de Nuits');?>: <b><?php echo $numNights_calculated; ?></b>
		</p>    
		<?php if (isset($booking_request->Message->messageText)){?>
		<p class="message-booking">
			<?php
			if(!empty($booking_request->Message->messageTextTranslated))
			{
				echo '<span class="translated">'.$booking_request->Message->messageTextTranslated.'</span>';
				echo '<span class="original" style="display:none;">'.$booking_request->Message->messageText.'</span>';
			}
			else
			{
				echo $booking_request->Message->messageText;
			}
			?>
		</p>
		<?php }?>
    <table class="review" cellpadding="0" cellspacing="0">
       <?php if($api_error==false):
           $dormroomcount = 0;
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
                    <th class="first-cell green-th"><?php echo _('Date');?></th>
                    <th class="green-th"><?php echo _('Chambres partagées - Dortoirs');?></th>
                    <th class="green-th">
										<div class="room-type">
											<a class="show-room-info" href="#"><?php echo _('Prix (lit)');?></a>
											<div class="room-type-info">
												<h5><?php echo _("Notes Importantes");?></h5>
												<p><?php echo _("Price per bed (not per room)");?></p>
												<span class="room-info-arrow"></span>
					
											</div>
										</div>										
										</th>
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

                   <?php if($isCustomCurrency):?>
                   <?php echo $bookCurSymbol.$room->$bookRoomPriceField;?>
                   <?php else:?>
                   <?php echo $cur;?><?php echo $room->priceSettle;?>
                   <?php endif;?>
                  </td>
                  <td><?php echo $room->beds;?></td>
                  <td class="value">

                   <?php if($isCustomCurrency):?>
                   <?php echo $bookCurSymbol.number_format((float)($room->$bookRoomPriceField)*($room->beds),2,'.','');?>
                   <?php else: ?>
                   <?php echo $cur;?><?php echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?>
                   <?php endif; ?>
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
                    <th class="first-cell green-th<?php if ($countdorm > 0){?> noround<?php }?>"><?php echo _('Date');?></th>
                    <th class="green-th"><?php echo _('Chambres privées');?></th>
                    <th class="green-th">
										<div class="room-type">
											<a class="show-room-info" href="#"><?php echo _('Prix');?></a>
											<div class="room-type-info">
												<h5><?php echo _("Notes Importantes");?></h5>
												<p><?php echo _("Price per room (not per person)");?></p>
												<span class="room-info-arrow"></span>					
											</div>
										</div>
										</th>
                    <th class="green-th"><?php echo _('Rooms');?></th>
                    <th width="18%" class="last-cell green-th<?php if ($countdorm > 0){?> noround<?php }?>"><?php echo _('Total');?></th>
                 </tr>
                 </thead>
                 <tbody>
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

                   <?php if($isCustomCurrency):?>
                   <?php echo $bookCurSymbol.number_format((float)($room->$bookRoomPriceField)*($room->beds),2,'.','');?>
                   <?php else:?>
                   <?php echo $cur;?><?php echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?>
                   <?php endif;?>
                  </td>
                  <td>1<?php //echo $room->beds;?></td>
                  <td class="value">

                   <?php if($isCustomCurrency):?>
                   <?php echo $bookCurSymbol.number_format((float)($room->$bookRoomPriceField)*($room->beds),2,'.','');?>
                   <?php else: ?>
                   <?php echo $cur;?><?php echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?>
                   <?php endif; ?>
                  </td>
               </tr>
               <?php
             }
           }
           ?>
           </tbody>
          </table>
          <table class="review-end" cellpadding="0" cellspacing="0">
             <tfoot>

              <tr class="dark">
                <td class="first" align="right"><?php echo _('Total en');?> <?php if($isCustomCurrency):?><?php echo $bookCurrency;?><?php else:?><?php echo $settleCurrency;?><?php endif; ?>:</td>
                <td class="total-value">
                  <?php if($isCustomCurrency):?>
                  <?php echo $bookCurSymbol.$booking_request->$bookTotalPriceField->value;?>
                  <?php else:?>
									<?php echo $cur;?><?php echo $booking_request->SettleBedsTotal->value;?>
									<?php endif; ?>                  
                </td>
               </tr>
              <tr class="light">
               <td class="first" align="right"><?php echo _('10% Arrhes / Dépôt sera facturé en');?> <?php echo $settleCurrency;?>:</td>
               <td>
                  <?php echo $cur;?><?php echo $booking_request->SettleDeposit->value;?>
                  <?php /* if($isCustomCurrency):?>
                  <span class="totaluser-currency">(<?php echo '~'.$booking_request->$bookDepositPriceField->value. ''.$bookCurSymbol;?>)</span>
                  <?php endif; */?>
               </td>
              </tr>

              <tr class="dark">
               <td class="first" align="right">

                   <span id="bookingFeeDesc"><?php echo _('Frais de Service')?>:</span></td>
                   <td>
                   <span style="display: inline;">
                     <?php echo $cur;?><?php echo $booking_request->SettleBookingFee->value;?>
                     <?php /* if($isCustomCurrency):?>
                     <span class="totaluser-currency">(<?php echo '~'.$booking_request->$bookBookFeeField->value. ''.$bookCurSymbol;?>)</span>
                     <?php endif;*/?>
                   </span>

               </td>
              </tr>

              <tr class="light">
               <td class="first" align="right">

                   <span id="bookingFeeDesc">
                   <?php switch($settleCurrency){
                           case 'EUR':
                           $member = $cur.'14.00';
                           break;

                           case 'USD':
                           $member = $cur.'20.00';
                           break;

                           case 'GBP':
                           $member = $cur.'12.00';
                           break;

                           default:
                           $member =  $cur.'14.00';
                           break;
                   }?>

                   <?php printf(gettext("%s yearly membership card - waived:"),$member); ?></span></td>
                   <td>
                   <span style="display: inline;">
                     <?php echo $cur;?>0.00
                       <?php /* if($isCustomCurrency):?>
                       <span class="totaluser-currency">(0.00<?php echo $bookCurSymbol;?>)</span>
                       <?php endif;*/?>
                   </span>

               </td>
              </tr>


              <tr class="end-total">
               <td class="first" align="right"><strong><?php echo _('Total à payer maintenant');?> (<?php echo $settleCurrency;?>):</strong></td>
               <td>
                  <span style="display: inline;">
                  <b>

										<?php echo $cur;?><?php echo $booking_request->SettleBillTotal->value;?>
                    <?php /* if($isCustomCurrency):?>
                    <span class="totaluser-currency">(<?php echo '~ '.$booking_request->$bookBillTotalField->value. ''.$bookCurSymbol;?>)</span>
                    <?php endif; */?>
                  </b>
                  </span>

                </td>
              </tr>

              <tr>
                <td valign="top" class="dotted" colspan="2">

                  <div class="booking_currency_choice">
                    <a<?php if(strcmp($settleCurrency,"USD")==0) echo " class=\"cur-selected\""; ?> onclick="booking_confirm('<?php echo secure_site_url(); ?>',true,'USD');return false;" href="#" id="cur-usd">$</a>
                    <a<?php if(strcmp($settleCurrency,"GBP")==0) echo " class=\"cur-selected\""; ?> onclick="booking_confirm('<?php echo secure_site_url(); ?>',true,'GBP'); return false;" href="#" id="cur-gbp">£</a>
                    <a<?php if(strcmp($settleCurrency,"EUR")==0) echo " class=\"cur-selected\""; ?> onclick="booking_confirm('<?php echo secure_site_url(); ?>',true,'EUR'); return false;" href="#" id="cur-eur">€</a>
                  </div>
                  <span class="cur-title"><?php echo _("Choose the currency of your payment:");?></span>
									<?php /*?><select id="book-settle-currency" name="book-settle-currency" onChange="booking_confirm('<?php echo secure_site_url(); ?>',true); return false;">
                      <option <?php if(strcmp($settleCurrency,"GBP")==0) echo "selected=\"selected\""; ?> value="GBP"><?php echo _("Livres sterling"); ?></option>
                      <option <?php if(strcmp($settleCurrency,"EUR")==0) echo "selected=\"selected\""; ?> value="EUR"><?php echo _("Euro"); ?></option>
                      <option <?php if(strcmp($settleCurrency,"USD")==0) echo "selected=\"selected\""; ?> value="USD"><?php echo _("Dollar US");?></option>
                  </select><?php */?>
                  <span id="loading_message_cur" class="loading_cur" style="display: none;">
                    <?php echo _('Updating...'); ?>
                  </span>

                </td>
              </tr>

              <tr class="last-cell">
               <td class="dotted" colspan="2">
                 <b><?php echo $booking_request->PropertyCurrency->value;?> <?php echo $booking_request->PropertyAmountDue->value;?></b>
                 <span style="display: inline;">
                 <?php if($isCustomCurrency):?>
                 	<?php if ($booking_request->PropertyCurrency->value == $bookCurrency):?>
                  	(<?php echo $booking_request->SettleAmountDue->value;?> <?php echo $cur;?>)
                  <?php else:?>
                  	(<?php echo '~ '.$booking_request->$bookAmountDueField->value. ' '.$bookCurSymbol;?>)
                  <?php endif;?>

                 <?php else :?>
									 <?php if(strcasecmp($booking_request->PropertyCurrency->value,$settleCurrency)!=0):?>
                    (<?php echo $booking_request->SettleAmountDue->value;?> <?php echo $cur;?>)
                   <?php endif; ?>
								 <?php endif;?>
                 </span>

                 <?php echo _('est le montant total à payer à votre arrivée');?>
               </td>
              </tr>

              <tr>
              	<td colspan="2">
                  <a class="modify-book" href="<?php echo $this->Db_links->build_property_page_link($property_type,$booking_hostel_name,$propertyNumber,$this->site_lang);?>"><?php echo _('Modify this booking');?>  &raquo;</a>
              	</td>
              </tr>
          </tfoot>

      <?php endif; // end if api error?>

      </table>
			</div>
    </div>

			<?php if($api_booking_error=="3d_secure_request"):?>
      <h3><?php echo _('Méthode de Paiement'); ?></h3>
      <table class="payment">
      <tbody>
       <tr>
        <td colspan="2"><p><strong><?php echo _("La méthode de paiement utilisée nécessite une validation en mode 3D.");?></strong><?php echo _("Veuillez cliquer sur le boutton ci-dessous pour procéder vers le site de votre fournisseur. Vous serez ensuite automatiquement redirigé vers cette page pour procéder au paiement final. Merci.");?></p>
          </td>
       </tr>
       <tr><td>
            </form>
            <form action="<?php echo $secure_parameters['issuerURL'];?>" method="post">
            <input type="hidden" name="PaReq" value="<?php echo $secure_parameters['PaReq']; ?>">
            <input type="hidden" name="MD" value="<?php echo $secure_parameters['MD']; ?>">
            <input type="hidden" name="TermUrl" value="<?php echo $secure_parameters['TermUrl']; ?>">

            <input id="submit-secure-request" type="submit" value="<?php echo _("Validation 3D");?>" onClick="javascript: pageTracker._trackPageview('/click/validation3D');" />
            </form>
            </td>

       </tr>
       </tbody>
       </table>

       <?php else: ?>
			<form action="<?php echo secure_site_url(); ?>" method="post" onSubmit="booking_confirm('<?php echo secure_site_url(); ?>',false,'<?php echo $settleCurrency;?>'); return false;">
			
			<div class="booking_section">  
				<div class="popup-info-wrap">
					<h2 id="step2-title" class="booking_section_title box_round green_gradient_faded question_mark"><span>2. <?php echo _('Informations personnelles - Sécurisées et Encryptées');?></span></h2>
					<div class="popup-info booking_view">
						<h4><?php echo _("Notes Importantes");?></h4>
						<p><?php echo _('We will never sell your personal information and we use secure transmission and encrypted storage to protect your personal information.')?></p>
						<span class="popup-info-arrow"></span>
					</div>
				</div>
				<div class="booking_section_content" id="step2">    
				
				<?php // echo form_open("",array("onSubmit" => "booking_confirm('".secure_base_url()."'); return false;"));?>

				<?php echo form_hidden('dateStart',set_value('dateStart','0000-00-00'));?>
				<div class="group">
					<div class="payment_info_block">
						<label for=""><?php echo _('Prénom');?> <span class="mandatory">*</span></label>
						<input type="text" maxlength="40" value="<?php echo $book_firstname; ?>" name="firstname" id="firstname">
						<script type="text/javascript">
						var firstname = new LiveValidation('firstname', { validMessage: ' ', onlyOnBlur: true});
						firstname.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						</script>
					</div>
					<div class="payment_info_block">
						<label for=""><?php echo _('Nom'); ?> <span class="mandatory">*</span></label>
						<input type="text" maxlength="40" value="<?php echo $book_lastname; ?>" name="lastname" id="lastname">
						<script type="text/javascript">
						var lastname = new LiveValidation('lastname', { validMessage: ' ', onlyOnBlur: true});
						lastname.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						</script>
					</div>
				</div>
				
				<div class="group">
					<div class="payment_info_block">
					<label for=""><?php echo _('Nationalité'); ?> <span class="mandatory">*</span></label>					
					<?php $this->Db_country->select_country("Nationality","nationality",set_value('nationality', $book_nationality),"","en",$this->site_lang,_('Choisir le pays'));  ?>					
					<script type="text/javascript">
					//$(document).ready(function(){$('#Nationality').translate('en','fr');});
						var Nationality = new LiveValidation('Nationality', { validMessage: ' ', onlyOnBlur: true});
						Nationality.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
					</script>
					</div>
					<div class="payment_info_block">
						<label for="gender"><?php echo _("Sexe"); ?> <span class="mandatory">*</span></label>						
						<select name="gender" id="gender">
						<option <?php if(strcasecmp($book_gender,"Male")==0) echo "selected=\"selected\""; ?> value="Male"><?php echo _("Masculin"); ?></option>
						<option <?php if(strcasecmp($book_gender,"Female")==0) echo "selected=\"selected\""; ?> value="Female"><?php echo _("Féminin"); ?></option>
						</select>		
					</div>		
				</div>
				
				<div class="group">
					<div class="payment_info_block">
						<label for=""><?php echo _("Adresse Email");?> <span class="mandatory">*</span></label>
						<input type="text" name="email" id="EmailAddress" value="<?php echo $book_email_address; ?>"/>
						<small><?php echo _("You will receive a confirmation email");?></small>
						
						<script type="text/javascript">
						var EmailAddress = new LiveValidation('EmailAddress', { validMessage: ' ', onlyOnBlur: true});
						EmailAddress.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						EmailAddress.add(Validate.Email, {failureMessage: "<?php echo _('Insérer un courriel valide'); ?>"});
						</script>
					</div>
					<div class="payment_info_block">
						<label for=""><?php echo _("Confirmation Email");?> <span class="mandatory">*</span></label>
						<input type="text" name="email2" id="EmailAddress2" value="<?php echo $book_email_address; ?>"/>
						<script type="text/javascript">
						var EmailAddress2 = new LiveValidation('EmailAddress2', { validMessage: ' ', onlyOnBlur: true});
						EmailAddress2.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						EmailAddress2.add(Validate.Email, {failureMessage: "<?php echo _('Insérer un courriel valide'); ?>"});
						EmailAddress2.add( Validate.Confirmation, { match: 'EmailAddress', failureMessage: "<?php echo _('Both emails must be the same'); ?>" } );
						</script>
					</div>
				</div>	
				
				<div class="group">
					<div class="payment_info_block">
						<label for=""><?php echo _('Téléphone'); ?> <span class="mandatory">*</span></label>					
						<input type="text" name="phone_number" id="phone_number" value="<?php echo $book_phone_number; ?>"/>
						<small><?php echo _('Country code, Area code (without first 0) and mobile number; no spaces, brackets or dashes.')?> <?php echo _('UK Example: 442012341234')?></small>					
						<script type="text/javascript">
						var phone_number = new LiveValidation('phone_number', { validMessage: ' ', onlyOnBlur: true});
						phone_number.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						phone_number.add(Validate.Numericality, {notANumberMessage: "<?php echo _('Invalid phone number format'); ?>"});
						</script>
					</div>				
				
					<?php
					if($this->wordpress->get_option('aj_enable_sms_reminder'))
					{
					?>
					<div class="payment_info_block">
						<label for="sms"><?php echo _('Text/SMS (FREE)')?></label>
						<?php echo sms_menu("sms",$sms,'none');?>
					</div>					
					<?php
					}
					else
					{
					?>
					<input type="hidden" name="sms" id="sms" value="none" />
					<?php
					}
					?>
				</div>
				
				<div class="group">
					<div class="payment_info_block">
					<label for=""><?php echo _("Heure d'arrivée:");?> <span class="mandatory">*</span></label>
					<select name="arrival_time" id="arrival_time">
					<option <?php if(strcmp($book_arrival_time,"0")==0) echo "selected=\"selected\""; ?> value="0">00:00</option>
					<option <?php if(strcmp($book_arrival_time,"1")==0) echo "selected=\"selected\""; ?> value="1">01:00</option>
					<option <?php if(strcmp($book_arrival_time,"2")==0) echo "selected=\"selected\""; ?> value="2">02:00</option>
					<option <?php if(strcmp($book_arrival_time,"3")==0) echo "selected=\"selected\""; ?> value="3">03:00</option>
					<option <?php if(strcmp($book_arrival_time,"4")==0) echo "selected=\"selected\""; ?> value="4">04:00</option>
					<option <?php if(strcmp($book_arrival_time,"5")==0) echo "selected=\"selected\""; ?> value="5">05:00</option>
					<option <?php if(strcmp($book_arrival_time,"6")==0) echo "selected=\"selected\""; ?> value="6">06:00</option>
					<option <?php if(strcmp($book_arrival_time,"7")==0) echo "selected=\"selected\""; ?> value="7">07:00</option>
					<option <?php if(strcmp($book_arrival_time,"8")==0) echo "selected=\"selected\""; ?> value="8">08:00</option>
					<option <?php if(strcmp($book_arrival_time,"9")==0) echo "selected=\"selected\""; ?> value="9">09:00</option>
					<option <?php if(strcmp($book_arrival_time,"10")==0) echo "selected=\"selected\""; ?> value="10">10:00</option>
					<option <?php if(strcmp($book_arrival_time,"11")==0) echo "selected=\"selected\""; ?> value="11">11:00</option>
					<option <?php if(strcmp($book_arrival_time,"12")==0) echo "selected=\"selected\""; ?> value="12">12:00</option>
					<option <?php if(strcmp($book_arrival_time,"13")==0) echo "selected=\"selected\""; ?> value="13">13:00</option>
					<option <?php if(strcmp($book_arrival_time,"14")==0) echo "selected=\"selected\""; ?> value="14">14:00</option>
					<option <?php if(strcmp($book_arrival_time,"15")==0) echo "selected=\"selected\""; ?> value="15">15:00</option>
					<option <?php if(strcmp($book_arrival_time,"16")==0) echo "selected=\"selected\""; ?> value="16">16:00</option>
					<option <?php if(strcmp($book_arrival_time,"17")==0) echo "selected=\"selected\""; ?> value="17">17:00</option>
					<option <?php if(strcmp($book_arrival_time,"18")==0) echo "selected=\"selected\""; ?> value="18">18:00</option>
					<option <?php if(strcmp($book_arrival_time,"19")==0) echo "selected=\"selected\""; ?> value="19">19:00</option>
					<option <?php if(strcmp($book_arrival_time,"20")==0) echo "selected=\"selected\""; ?> value="20">20:00</option>
					<option <?php if(strcmp($book_arrival_time,"21")==0) echo "selected=\"selected\""; ?> value="21">21:00</option>
					<option <?php if(strcmp($book_arrival_time,"22")==0) echo "selected=\"selected\""; ?> value="22">22:00</option>
					<option <?php if(strcmp($book_arrival_time,"23")==0) echo "selected=\"selected\""; ?> value="23">23:00</option>
					</select>
					</div>
								
				<?php $user_id = $this->tank_auth->get_user_id(); ?>
				<?php if($user_id === false):?>
				<?php /*?><tr>
				<td width="130"><label style="float: left;" for=""><?php echo _('Je suis nouveau, créez moi un compte'); ?></label></td>
				<td><input class="checkbox" type="checkbox" name="sign_me_up" id="sign_me_up" onClick="toggleMailCheckbox();" value="1" <?php echo ($book_sign_me_up==true) ?  "checked=\"checked\"":"";?>></td>
				<script type="text/javascript">
				function toggleMailCheckbox()
				{
				var sign_me_up  = document.getElementById('sign_me_up').checked;
				if(sign_me_up == false)
				{
				document.getElementById('mail_subscribe').disabled = true;
				}
				else
				{
				document.getElementById('mail_subscribe').disabled = false;
				}
				}
				$(document).ready(function(){toggleMailCheckbox();});
				</script>
				</tr> <?php */?>	
					<div class="payment_info_block" style="margin-bottom:0px;">
					<input class="checkbox" style="margin-top:20px;" type="checkbox" name="mail_subscribe" id="mail_subscribe" value="1" onMouseOver="toggleMailCheckbox();" checked="checked" />
					<label class="checkbox" style="margin-top:23px" for="mail_subscribe"><?php echo _('Abonnement newsletter'); ?></label>	
					<input type="hidden" name="sign_me_up" id="sign_me_up" value="1">				
				  <span class="separate"><a class="popup" target="_blank" href="<?php echo $this->wordpress->get_option('aj_page_conf'); ?>?print=nostyle"><?php echo _('cliquer ici pour notre charte');?></a></span>	
					</div>								
				<?php endif; ?>
				</div>
				<?php if($user_id !== false):?>
				<input type="hidden" name="sign_me_up" id="sign_me_up" value="0" />
				<input type="hidden" name="mail_subscribe" id="mail_subscribe" value="0" />
				<?php endif; ?>
				<input type="hidden" name="numNights" value="<?php echo $numNights; ?>" />
				<input type="hidden" id="propertyNumber" name="propertyNumber" value="<?php echo $propertyNumber; ?>" />
				<input type="hidden" name="bsid" id="bsid" value="<?php if(isset($booking_request->BSID->value)) echo $booking_request->BSID->value; ?>" />
				
				<?php for($i=0;$i<count($roomPreferences);$i++):?>
				<input type="hidden" name="book-roomPreferences[]" value="<?php echo $roomPreferences[$i];?>" />
				<input type="hidden" name="book-nbPersons[]" value="<?php echo $nbPersons[$i];?>" />
				<?php endfor;?>
	
				<?php
				$roomnumberlist = array();
				foreach($booking_request->RoomDetails as $room)
				{
					if(empty($roomnumberlist[(string)$room->roomNumber]))
					{
						$roomTypeTranslated = "";
						if(!empty($room->roomTypeDescriptionTranslated))
						{
							$roomTypeTranslated = (string)$room->roomTypeDescriptionTranslated;
						}
						?>
						<input type="hidden" name="book-roomNumber[]" value="<?php echo  (string)$room->roomNumber;?>" />
						<input type="hidden" name="book-roomTypeDescription[]" value="<?php echo  (string)$room->roomTypeDescription;?>" />
						<input type="hidden" name="book-roomTypeDescriptionTranslated[]" value="<?php echo $roomTypeTranslated;?>" />
						<?php
						$roomnumberlist[(string)$room->roomNumber] = (string)$room->roomTypeDescription;
					}
				}
				?>
				<input type="hidden" id="book-propertyName" name="book-propertyName" value="<?php echo $booking_hostel_name; ?>">
	
				<input type="hidden" id="book-dateStart" name="book-dateStart" value="<?php echo $dateStart; ?>">
				<input type="hidden" id="book-numNights" name="book-numNights" value="<?php echo $numNights; ?>">
				<input type="hidden" id="book-currency" name="book-currency" value="<?php echo $bookCurrency; ?>">
				
				<input type="hidden" id="secure-final" name="secure-final" value="<?php echo isset($secure3d_final) ? $secure3d_final:"false"; ?>">
				<input type="hidden" id="secure-cookie" name="secure-cookie" value="<?php echo isset($secure_cookie) ? $secure_cookie:""; ?>">
				<input type="hidden" id="secure-pares" name="secure-pares" value="<?php echo isset($secure_PaRes) ? $secure_PaRes:""; ?>">
				<input type="hidden" id="secure-transid" name="secure-transid" value="<?php echo isset($secure_transactionId) ? $secure_transactionId:""; ?>">
				<input type="hidden" id="secure-newsessionid" name="secure-newsessionid" value="<?php echo isset($secure_MD) ? $secure_MD:""; ?>">
				<input type="hidden" id="secure-ip" name="secure-ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>">
				<input type="hidden" id="secure-usersessionid" name="secure-usersessionid" value="<?php echo isset($secure_userSessionId) ? $secure_userSessionId:""; ?>">
	
				</div>
			</div>
			
			<div class="booking_section">  
				<div class="popup-info-wrap">
					<h2 id="step2-title" class="booking_section_title box_round green_gradient_faded question_mark"><span>3. <?php echo _('Méthode de Paiement - Sécurisée et Encryptée'); ?></span></h2>
					<div class="popup-info booking_view">
						<h4><?php echo _("Notes Importantes");?></h4>
						<p><?php echo _('A friend or a family member can lend you his/her credit card for the reservation.')?> <?php echo _('You can use a different credit card to pay the balance upon arrival at the property.')?></p>
						<span class="popup-info-arrow"></span>
					</div>
				</div>
				
				<div class="booking_section_content" id="step2">    
		
				<?php if (isset($secure3d_final)&& ($secure3d_final==true)){?>
				<p class="validation3d"><strong><?php echo _("La validation 3D à été complétée. Veuillez entrer l'information ci-dessous pour confirmer le paiement.");?></strong></p>
				<?php } ?>
				
				<div class="group">
					<div class="payment_info_block">
						<label for=""><?php printf(gettext('sur la carte %s de crédit :'),""); ?> <span class="mandatory">*</span></label>
						<input type="text" autocomplete="off" name="ccname" id="ccname" value="<?php echo isset($book_ccname) ? $book_ccname : ""; ?>"/>
						<script type="text/javascript">
							var ccname = new LiveValidation('ccname', { validMessage: ' ', onlyOnBlur: true});
							ccname.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						</script>
					</div>
					<div class="payment_info_block">
						<label for=""><?php echo _('Numéro'); ?> <?php printf(gettext('de la carte %s de crédit :'),""); ?> <span class="mandatory">*</span></label>
						<input type="text" autocomplete="off" name="ccnumber" id="ccnumber" value="<?php echo isset($book_ccnumber) ? $book_ccnumber : ""; ?>"/>
						<small><?php echo _("(sans tiret)");?></small>
						<script type="text/javascript">
							var ccnumber = new LiveValidation('ccnumber', { validMessage: ' ', onlyOnBlur: true});
							ccnumber.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
							ccnumber.add( Validate.Format, { pattern:/^[0-9 ]*$/, failureMessage: "<?php echo _('Champ obligatoire'); ?>"} );
							<?php /*?>ccnumber.add( Validate.Numericality, { notANumberMessage: "<?php echo _('Chiffres seulement'); ?>", notAnIntegerMessage: "<?php echo _('Chiffres seulement'); ?>", onlyInteger: true } );<?php */?>
						</script>
					</div>
				</div>
				
				<div class="group">
					<div class="payment_info_block">
				
					<label for=""><?php echo _("Type de carte :");?> <span class="mandatory">*</span></label>
					<?php
					/* --------------------------------------------
					 * Card type menu
					 */
					
					$card_type_menu = "<select name=\"cctype\" id=\"cctype\" >\n";
					$card_type_menu.= "<option value=\"\" class=\"hideall\" selected>--</option>\n";
					
					$jvar_exclude_card = "[ ";
					foreach($booking_request->CardInfo->card as $card)
					{
						$card_display = $card->name;
						$card_value   = "$card->type-$card->currency";
						if(strcasecmp($card->currency,"All") != 0 )
						{
							$card_display.= " (".$card->currency.")";
							if(strcasecmp($card->currency,$settleCurrency)!= 0)
							{
								$jvar_exclude_card.= "'$card_value' ,";
							}
						}
					
						$class= "class=\"hideall\"";
					
						if(($card->IssueNO==1)&&($card->CCValidFrom==1))
						{
							$class= "class=\"showall\"";
						}
						elseif($card->CCValidFrom==1)
						{
							$class= "class=\"showvalid\"";
						}
						elseif($card->IssueNO==1)
						{
							$class= "class=\"showissueno\"";
						}
						//onclick="booking_confirm('<?php echo secure_site_url(); ',true,'GBP'); return false;"
						$cctype_selected = "";
						//if book type is set and card accepts currency
						if(!empty($book_cctype)&&(strcasecmp($book_cctype,$card->type)==0)
																	 &&((strcasecmp($card->currency,"All") == 0) || (strcasecmp($card->currency,$settleCurrency)==0)))
						{
							$cctype_selected = "selected=\"selected\"";
						}
						$card_type_menu.= "<option value=\"$card_value\" $class $cctype_selected>$card_display</option>\n";
					}
					$card_type_menu.= "</select>\n";
					
					$jvar_exclude_card = substr($jvar_exclude_card, 0, -1);
					$jvar_exclude_card.= "]";
					
					echo $card_type_menu;
					?>
					<script>
					$(document).ready(
					
							function () {
								var class_type = $("#cctype :selected").attr('class');
					
								if(class_type=="hideall")
								{
									hideissueno();
									hidevalidfrom();
								}
								else if(class_type=="showall")
								{
									showissueno();
									showvalidfrom();
								}
								else if(class_type=="showvalid")
								{
									showvalidfrom();
								}
								else if(class_type=="showissueno")
								{
									showissueno();
								}
						}
					);
					
					$("#cctype").change(
						function () {
								var class_type = $("#cctype :selected").attr('class');
		
		
								if(class_type=="hideall")
								{
									hideissueno();
									hidevalidfrom();
								}
								else if(class_type=="showall")
								{
									showissueno();
									showvalidfrom();
								}
								else if(class_type=="showvalid")
								{
									showvalidfrom();
								}
								else if(class_type=="showissueno")
								{
									showissueno();
								}
								var posCur    = $("#cctype :selected").val().indexOf('-');
								var cctypeCur = $("#cctype :selected").val().substring(posCur+1);
		
								var settlecurselected = "EUR";
								if( $("#cur-eur").attr('class') == 'cur-selected')
								{
									settlecurselected = 'EUR';
								}
								if( $("#cur-gbp").attr('class') == 'cur-selected')
								{
									settlecurselected = 'GBP';
								}
								if( $("#cur-usd").attr('class') == 'cur-selected')
								{
									settlecurselected = 'USD';
								}
		
								if((cctypeCur != 'All') && (settlecurselected != cctypeCur))
								{
									booking_confirm('<?php echo secure_site_url(); ?>',true,cctypeCur);
								}
		
						}
					);
					</script>
					
					<script type="text/javascript">
						var cctype = new LiveValidation('cctype', { validMessage: ' ', onlyOnBlur: true});
						cctype.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						cctype.add(Validate.Exclusion, { within: <?php echo $jvar_exclude_card; ?>, failureMessage: "<?php echo _('Updating Currency ...'); ?>" });
					</script>		
					<small><?php echo _("Important information: Maestro cards must be from Great-Britain.");?></small>
					<?php /*?><small><?php echo _("acceptée par l'établissement");?></small><?php */?>
					</div>
					<div class="payment_info_block dontshowerror">
						<label for=""><?php echo _("Date d'expiration :"); ?> <span class="mandatory">*</span></label></td>
						<select class="standard" id="ccexpiry_m" name="ccexpiry_m">
							<option value="">- -</option>
							<option value="01" <?php if(isset($book_ccexpiry_m)&&strcasecmp($book_ccexpiry_m,"01")==0) echo "selected=\"selected\"";?>>01</option>
							<option value="02" <?php if(isset($book_ccexpiry_m)&&strcasecmp($book_ccexpiry_m,"02")==0) echo "selected=\"selected\"";?>>02</option>
							<option value="03" <?php if(isset($book_ccexpiry_m)&&strcasecmp($book_ccexpiry_m,"03")==0) echo "selected=\"selected\"";?>>03</option>
							<option value="04" <?php if(isset($book_ccexpiry_m)&&strcasecmp($book_ccexpiry_m,"04")==0) echo "selected=\"selected\"";?>>04</option>
							<option value="05" <?php if(isset($book_ccexpiry_m)&&strcasecmp($book_ccexpiry_m,"05")==0) echo "selected=\"selected\"";?>>05</option>
							<option value="06" <?php if(isset($book_ccexpiry_m)&&strcasecmp($book_ccexpiry_m,"06")==0) echo "selected=\"selected\"";?>>06</option>
							<option value="07" <?php if(isset($book_ccexpiry_m)&&strcasecmp($book_ccexpiry_m,"07")==0) echo "selected=\"selected\"";?>>07</option>
							<option value="08" <?php if(isset($book_ccexpiry_m)&&strcasecmp($book_ccexpiry_m,"08")==0) echo "selected=\"selected\"";?>>08</option>
							<option value="09" <?php if(isset($book_ccexpiry_m)&&strcasecmp($book_ccexpiry_m,"09")==0) echo "selected=\"selected\"";?>>09</option>
							<option value="10" <?php if(isset($book_ccexpiry_m)&&strcasecmp($book_ccexpiry_m,"10")==0) echo "selected=\"selected\"";?>>10</option>
							<option value="11" <?php if(isset($book_ccexpiry_m)&&strcasecmp($book_ccexpiry_m,"11")==0) echo "selected=\"selected\"";?>>11</option>
							<option value="12" <?php if(isset($book_ccexpiry_m)&&strcasecmp($book_ccexpiry_m,"12")==0) echo "selected=\"selected\"";?>>12</option>
						</select>
						
						<select class="standard" id="ccexpiry_y" name="ccexpiry_y">
						
						<option value="">- - - -</option>
				
						<?php
						for($date_qty=0;$date_qty<10;$date_qty++)
				
						{
							$year  = mktime(0, 0, 0, date("m") , date("d"), date("Y")+$date_qty);
				
							?>
							 <option value="<?php echo date("y",$year);?>" <?php if(isset($book_ccexpiry_y)&&strcasecmp($book_ccexpiry_y,date("y",$year))==0) echo "selected=\"selected\"";?>><?php echo date("Y",$year);?></option>
				
				
							<?php
						}
						?>
				
						</select>
						<script type="text/javascript">
							var ccexpiry_m = new LiveValidation('ccexpiry_m', { validMessage: ' ', onlyOnBlur: true});
							var ccexpiry_y = new LiveValidation('ccexpiry_y', { validMessage: ' ', onlyOnBlur: true});
				
							ccexpiry_m.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
							ccexpiry_y.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						</script>
					</div>
				</div>
				
				<div class="group">
					<div class="payment_info_block dontshowerror">
					<label for=""><?php echo _("Code de sécurité :"); ?> <span class="mandatory">*</span></label>
					<input type="text" class="small_text" autocomplete="off" name="cvv" id="cvv" maxlength="4" size="4" value="<?php if(!empty($cvv)) echo $cvv;?>"/><a href="<?php echo secure_site_url();?>images/ccard.png" class="screenshot" rel="<?php echo secure_site_url();?>images/ccard.png"><?php echo _("Qu'est-ce que c'est ?");?></a>
					<script type="text/javascript">
						var cc_security = new LiveValidation('cvv', { validMessage: ' ', onlyOnBlur: true});
						cc_security.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						cc_security.add(Validate.Numericality, {notANumberMessage: "<?php echo _('Invalid format'); ?>"});
					</script>
					</div>
				</div>
				<div class="group">
					<div class="payment_info_block issue_no" style="display:none;">
						<label for=""><?php echo _("Numéro d'émission :"); ?></label>
						<input type="text" autocomplete="off" name="issueno" id="issueno" />
					</div>
					<div class="payment_info_block issue_no" style="display:none;">
						<label for=""><?php echo _("Valide depuis :"); ?></label>					
						<select class="standard" id="ccvalidfrom_m" name="ccvalidfrom_m">
								<option value="">- -</option>
								<option value="01">01</option>
								<option value="02">02</option>
								<option value="03">03</option>
								<option value="04">04</option>
								<option value="05">05</option>
								<option value="06">06</option>
								<option value="07">07</option>
								<option value="08">08</option>
								<option value="09">09</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
								</select>
						<select class="standard" id="ccvalidfrom_y" name="ccvalidfrom_y">
						<option value="">- - - -</option>
		
						<?php for($date_qty=0;$date_qty<10;$date_qty++)
									{
										$year  = mktime(0, 0, 0, date("m") , date("d"), date("Y")-$date_qty);?>
		
										<option value="<?php echo date("y",$year);?>"><?php echo date("Y",$year);?></option>
						<?php } ?>
						</select>
					</div>
					
				</div>
			</div>
		</div>
		<div class="booking_section green_gradient_faded highlight box_round">  
			<h2 id="step4-title" class="booking_section_title">4. <?php echo _('Final Step'); ?></h2>			
				<div class="booking_section_content" id="step4">    				
					<div class="group">
						<div class="payment_info_block terms_conditions">
							<input class="checkbox" type="checkbox" name="conditions_agree" id="conditions_agree" checked="checked" <?php if(isset($conditions_agree)&&($conditions_agree === TRUE)) echo "checked";?>/>
							<span><?php printf( gettext("J'accepte %s les termes et conditions %s"),"<a class=\"popup\" href=\"".$this->wordpress->get_option('aj_page_cond')."?print=nostyle\">","</a>");?></span> <span class="mandatory" style="margin-left:5px;">*</span>
							<script type="text/javascript">
								var checkval = new LiveValidation('conditions_agree', { validMessage: ' ', onlyOnBlur: true});
								checkval.add( Validate.Acceptance );
							</script>
						</div>
					</div>
					
					<div class="reminders">
						<span class="last_reminder_price_now"><strong><?php echo _('Total à payer maintenant');?>: <?php echo $cur;?><?php echo $booking_request->SettleBillTotal->value;?></strong></span>
						<span class="last_reminder_price_full">
						<b><?php echo $booking_request->PropertyCurrency->value;?> <?php echo $booking_request->PropertyAmountDue->value;?></b>						
						<?php if($isCustomCurrency):?>
						<?php if ($booking_request->PropertyCurrency->value == $bookCurrency):?>
							(<?php echo $booking_request->SettleAmountDue->value;?> <?php echo $cur;?>)
						<?php else:?>
							(<?php echo '~ '.$booking_request->$bookAmountDueField->value. ' '.$bookCurSymbol;?>)
						<?php endif;?>
						
						<?php else :?>
						 <?php if(strcasecmp($booking_request->PropertyCurrency->value,$settleCurrency)!=0):?>
							(<?php echo $booking_request->SettleAmountDue->value;?> <?php echo $cur;?>)
						 <?php endif; ?>
						<?php endif;?>	
						<?php echo _('est le montant total à payer à votre arrivée');?>
						</span>
						<span class="last_reminder_confirmation"><?php echo _("* En cliquant sur le bouton de Confirmation ci-dessous, vous acceptez les modalités et les conditions et autorisez la facturation d'un montant non remboursable de");?> : <?php echo $cur;?> <?php echo $booking_request->SettleBillTotal->value;?></span>
					</div>
					
					<div class="payment_button group">
					 <input type="hidden" id="analytic-value" name="analytic-value" value="<?php echo $booking_request->CADDepositAmount->value;?>" />
					 <input id="submit-payment" type="submit" class="button-green box_shadow_hard box_round hoverit" value="<?php echo _("Confirmer");?>" onfocus="this.blur()" onClick="check_error();"<?php /*?>onClick="javascript: pageTracker._trackPageview('/click/confirmation');" <?php */?>/>
						
						<?php if ($this->wordpress->get_option('aj_ssl_url') ==""){?>
						<img title="<?php printf(gettext("Transaction 100%% sécurisée et encryptée"));?>" id="ssl-img" class="ssl-image" height="45" width="45" src="<?php echo secure_site_url();?>images/GandiSSL2.png" alt="<?php echo _("sécurisé");?>" />
						<?php }else{?>
						<a target="_blank" title="<?php printf(gettext("Transaction 100%% sécurisée et encryptée"));?>" href="<?php echo $this->wordpress->get_option('aj_ssl_url');?>"><img class="ssl-image" id="ssl-img" height="45" width="45" src="<?php echo secure_site_url();?>images/GandiSSL2.png" alt="<?php echo _("sécurisé");?>" /></a>
						<?php }?>
						
						<p id="check_error" style="display:none;"><?php echo _('Please make sure all required fields are filled out correctly'); ?></p>
						
						<?php /*?><img class="lock-book" src="<?php echo secure_site_url();?>images/padlock.png" alt="sécurisé" /><?php */?>
						<p id="loading_message" class="loading_book" style="display: none;">
							<img src="<?php echo secure_site_url();?>images/V2/loading-squares-greenback.gif" alt=""/>
							<span><?php echo _('Traitement de la demande...'); ?></span>
						</p>
				
					</div>
				
				<?php if($api_booking_error=="api_msg"):?>
				 
				<div class="transaction_error api_error">
					<p><?php printf( gettext("Il y a eu une erreur lors du traitement de votre demande, veuillez vous assurer que vos informations sur la méthode de paiement sont exactes. Si le problème persiste, veuillez communiquer avec nous à l'adresse suivante : %s"),'<a target="_blank" href="'.$this->wordpress->get_option('aj_page_faq').'?print=nostyle">'.$this->wordpress->get_option('aj_page_faq').'</a>');?></p>
					<span class="error_title"><?php echo _('Errors')?>:</span>
					<ul><?php foreach($api_booking_error_msg as $error):?>							
									<?php
									if(!empty($error->messageTranslated))
									{
										echo '<li class="translated">'.$error->messageTranslated.'</li>';
										echo '<li class="original" style="display:none;">'.$error->message.'</li>';
									}
									else
									{
										echo $error->message;
									}
									?>							
								<?php endforeach;?>
								<?php if($api_booking_error=="api_out"):?>
									<li class="api_error"><?php echo $api_booking_error_msg[0]; ?></li>
								<?php endif;?>
						</ul>
				</div>				
				<?php endif;?>	
			</div>
		</div>
	</form>

	<?php 
	$displayVelaro = $this->config->item('displayVelaro');
	if($displayVelaro==1)
	{
		if ($this->wordpress->get_option('aj_velaro_id') !='')
		{
	?>
		<div class="chat_help_booking box_round">
		<a href="https://service.velaro.com/visitor/requestchat.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue" target="OnlineChatSoftware"  onClick="this.newWindow = window.open('http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue', 'OnlineChatSoftware', 'toolbar=no,location=no,directories=no,menubar=no,status=no,scrollbars=no,resizable=yes,replace=no');this.newWindow.focus();this.newWindow.opener=window;return false;"><img alt="OnlineChatSoftware" src="https://service.velaro.com/visitor/check.aspx?siteid=7548&deptid=<?php echo $this->wordpress->get_option('aj_velaro_id');?>&showwhen=inqueue" border="0"></a>
		</div>
		
		<?php }else{?>
		<div class="chat_help_booking box_round">
		<a onclick="this.newWindow = window.open('https://service.velaro.com/visitor/requestchat.aspx?siteid=7548&amp;showwhen=inqueue', 'OnlineChatSoftware', 'toolbar=no,location=no,directories=no,menubar=no,status=no,scrollbars=no,resizable=yes,replace=no');this.newWindow.focus();this.newWindow.opener=window;return false;" target="OnlineChatSoftware" href="http://service.velaro.com/visitor/requestchat.aspx?siteid=7548&amp;showwhen=inqueue"><img border="0" src="https://service.velaro.com/visitor/check.aspx?siteid=7548&amp;showwhen=inqueue" alt="OnlineChatSoftware"></a>
		</div>
		<?php }  }?>	 


   <?php endif; //if secure not 3d request ?>
 
 	<div class="booking_section">  
		<h2 id="step5-title" class="booking_section_title box_round green_gradient_faded"><?php echo _("Changements - Annulations"); ?></h2>			
		<div class="booking_section_content" id="step5">   
			<?php if (!empty($booking_request->TermsAndConditionsTranslated->value)){?>
			<div class="switch-translate clearfix">
			<p><?php echo _("Certains extraits du texte de cette page ont été traduits automatiquement. Cliquer sur les liens suivants changer les version :"); ?><br /><a class="show-original" href="#"><?php echo _("Voir l'original"); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="show-translate active" href="#"><?php echo _("Voir la version traduite"); ?></a>
			</p>
			</div>
			<?php }elseif(empty($booking_request->TermsAndConditions->value)){?>
				<p><strong><?php echo _("Les changements et annulations sont possibles, faciles à faire et se font directement avec l‘établissement choisi qui en est responsable. Changements de dates/chambres : possible selon disponibilités. Annulations : au moins 72 heures en avance pour ne pas être facturé la première nuit.");?></strong></p>
			 <?php }?>
			
			<p><b><span class="notes"><?php echo _("Notes Importantes");?></span></b>
			<?php printf( gettext("Sur %s, vous payez seulement les arrhes (10%% du montant total) et les frais de service minimum et non remboursables afin de confirmer et garantir votre réservation. Le montant restant vous sera demandé à votre arrivée."),$this->config->item('site_name'));?></p>
			
			<?php if (!empty($booking_request->TermsAndConditionsTranslated->value)){?>
			
			<div class="translated"><?php echo nl2p(var_check($booking_request->TermsAndConditionsTranslated->value,""),false,true)?></div>
			<div class="original" style="display:none;"><?php echo nl2p(var_check($booking_request->TermsAndConditions->value,""),false,true)?></div>
			
			<?php }elseif(!empty($booking_request->TermsAndConditions->value)){ ?>
			
			<div class="original"><?php echo nl2p(var_check($booking_request->TermsAndConditions->value,""),false,true)?></div>
			
			<?php } ?>	
			
			</div>
		</div>
	</div>
	<?php endif; //endif api error = false ?>
 <?php if(!$isAjax):?>
</div>
 <?php endif;?>
