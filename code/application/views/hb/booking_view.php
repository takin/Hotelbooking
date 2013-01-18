<?php
$cur                   = currency_symbol($settleCurrency);
$bookCurSymbol         = currency_symbol($bookCurrency);
$isCustomCurrency      = (strcasecmp($settleCurrency,$bookCurrency)!=0);
$hb_arrhes_rate        = 0.1;
$SPACE = '&nbsp;';
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
	if(!empty($booking_room["NAME_TRANSLATED"]) || !empty($booking_request->TermsAndConditionsTranslated->value))
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
		 <li class="first" id="rules"><img src="<?php echo secure_site_url();?>images/<?php echo $csspath; ?>/sideinfo-rules.png" alt="" /><span><?php printf(gettext("%s est réglementé par l'Union Européenne."),"<strong>".$this->config->item('site_name')."</strong>");?></span></li>
		 <li id="secure"><img src="<?php echo secure_site_url();?>images/hb-icons-secure.png" alt="" />
		 <span><strong><?php printf(gettext("100%% sécurisé."));?></strong> <?php echo _("Paiements sécurisés et encryptés pour votre sécurité.");?></span></li>
		 <li id="bestprice"><img src="<?php echo secure_site_url();?>images/hb-icons-10percent.png" alt="" /><span><?php echo _("Seulement 10% pour garantir votre réservation.");?></span></li>
		<?php /*?><li id="support"><img height="38px" src="<?php echo secure_site_url();?>images/sideinfo-support.png" alt="" /><br /><span><?php printf(gettext("Un service clientèle de qualité disponible %s prêt à vous guider à tout moment."),"<b>"._("24h/24, 7j/7")."</b>");?></span></li><?php */?>
		 <li id="support"><img src="<?php echo secure_site_url();?>images/hb-icons-cell.png" alt="" /><span><br /><?php echo _('Text/SMS (FREE)')?></span></li>
		 <?php /*?><li id="forall"><img height="38px" src="<?php echo secure_site_url();?>images/sideinfo-forall.png" alt="" /><br /><span><?php echo _("Pour tous les âges: ni maximum ni minimum.");?></span></li><?php */?>
		 <li id="forall"><img src="<?php echo secure_site_url();?>images/hb-icons-nofee.png" alt="" /><span><br /><?php echo _('No Booking fees')?></span></li>
		 <li class="last" id="member"><img src="<?php echo secure_site_url();?>images/hb-icons-save.png" alt="" /><span><?php printf(gettext("%s Pas besoin de carte de membre pour recevoir les meilleurs prix du Net."),"<strong>".$this->config->item('site_name')."</strong>");?></span></li>
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
	
  <?php if ($this->wordpress->get_option('aj_api_ascii')==""){$csspath = $this->wordpress->get_option('aj_api_name');}else{$csspath = $this->wordpress->get_option('aj_api_ascii');} ?>
	
	<?php if($api_booking_error=="api_msg"):?>	
	<div class="booking_section">  
		<div class="transaction_error api_error">
		<p><?php printf( gettext("Il y a eu une erreur lors du traitement de votre demande, veuillez vous assurer que vos informations sur la méthode de paiement sont exactes. Si le problème persiste, veuillez communiquer avec nous à l'adresse suivante : %s"),'<a target="_blank" href="'.$this->wordpress->get_option('aj_page_faq').'">'.$this->wordpress->get_option('aj_page_faq').'</a>');?></p>	
		</div>
	</div>			
	<?php endif;?>
	
	<?php if($api_error==false): ?>
	
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
			<?php echo _('Arrivée');?>: <b><?php echo date_conv($dateStart, $this->wordpress->get_option('aj_date_format')); ?></b> &nbsp; &nbsp; <?php echo _('Nombre de Nuits');?>: <b><?php echo $numNights; ?></b>
		</p>		
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
       <?php if($api_error==false): ?>

           <tbody>
           <?php
//           debug_dump($booking_request);
           $shared_room_selected = false;

           $booking_total_price     = 0;
           $booking_total_gbp_price = 0;
           $booking_total_hostel_price = 0;

           $booking_first_date   = true;
           $booking_total_people = 0;

           $property_currency = $settleCurrency;

           $roomPreferences = array();

           foreach($booking_request as $booking_date)
           {
             foreach($booking_date as $booking_room)
             {
               $persons_in_room = 0;

               if( isset($persons_per_rooms[$booking_room["ID"]]) )
               {
                 $persons_in_room = $persons_per_rooms[$booking_room["ID"]];
               }
               if($persons_in_room > 0)
               {
                 $night_date = slash_date_conv($booking_room["NIGHT"],'%Y-%m-%d');
                 $min_persons_room = $booking_room["BLOCKBEDS"];
                 //If blockbed is 0, it is a dorm room min person is 1
                 if($min_persons_room  < 1)
                 {
                   $shared_room_selected = true;
                   $min_persons_room = 1;
                 }
                 if($booking_first_date == true)
                 {
                   $booking_total_people += $persons_in_room;
                 }

                 $roomPreferences[$booking_room["ID"]] = $persons_in_room;

                 $night_total     = (float)($booking_room["PRICES"]["CUSTOMER"]["MINPRICE"])*($persons_in_room);
                 $night_total_gbp = (float)($booking_room["PRICES"]["PAYMENT"]["MINPRICE"])*($persons_in_room);
                 $night_total_hos = (float)($booking_room["PRICES"]["HOSTEL"]["MINPRICE"])*($persons_in_room);

                 $booking_total_price        += $night_total;
                 $booking_total_gbp_price    += $night_total_gbp;
                 $booking_total_hostel_price += $night_total_hos;

                 $property_currency = $booking_room["PRICES"]["HOSTEL"]["CURRENCY"];

                 ?>
                  <tr>
                     <td class="first-cell"><?php echo date_conv($night_date, $this->wordpress->get_option('aj_date_format'));?></td>
                     <td>
                       <?php
                         if(!empty($booking_room["NAME_TRANSLATED"]))
                         {
                          echo '<span>'.$booking_room["NAME_TRANSLATED"].'</span>';
                          //echo '<span style="display:block;">('.$booking_room["NAME"].')</span>';
                         }
                         else
                         {
                           echo $booking_room["NAME"];
                         }
												 if($breakfast_included == 1){
														echo '<span class="free-breakfast">';
														echo _('Breakfast Included');
														echo '</span>';
													}
                       ?>
                     </td>
                     <td>

                      <?php echo $bookCurSymbol.number_format((float)($booking_room["PRICES"]["CUSTOMER"]["MINPRICE"])*($min_persons_room),2,'.','');?>
                     </td>
                     <td><?php echo $persons_in_room;?></td>
                     <td class="value">

                      <?php echo $bookCurSymbol.$SPACE.number_format($night_total,2,'.','');?>
                      </td>
                  </tr>
                 <?php
               }
             }
             $booking_first_date   = false;
           }
           $booking_total_usd_price = $this->Db_currency->convert_from_hw_rates("GBP","USD",$booking_total_gbp_price);
           $booking_total_eur_price = $this->Db_currency->convert_from_hw_rates("GBP","EUR",$booking_total_gbp_price);
           $booking_total_cad_price = $this->Db_currency->convert_from_hw_rates("GBP","CAD",$booking_total_gbp_price);

           $settle_deposit_gbp        = number_format($booking_total_gbp_price*$hb_arrhes_rate,2,'.','');
           $settle_deposit_usd        = number_format($booking_total_usd_price*$hb_arrhes_rate,2,'.','');
           $settle_deposit_eur        = number_format($booking_total_eur_price*$hb_arrhes_rate,2,'.','');
           $settle_deposit_cad        = number_format($booking_total_cad_price*$hb_arrhes_rate,2,'.','');
           $settle_deposit_booking        = number_format($booking_total_price*$hb_arrhes_rate,2,'.','');

            // Find the CADDepositAMount to put in the hidden field 'analytic-value'
            $CADDepositAmount = (float)$settle_deposit_booking * 0.6;
            $CADDepositAmount = number_format($this->Db_currency->convert_from_hw_rates('GBP', 'CAD', $CADDepositAmount), 2);

           ?>

          	</tbody>
          </table>
          <table class="review-end" cellpadding="0" cellspacing="0">
             <tfoot>

              <tr class="dark">
                <td class="first" align="right"><b><?php echo _('Total en');?> <?php  echo $bookCurrency;?>:</b></td>
                <td class="total-value">
                  <?php echo $bookCurSymbol.$SPACE.number_format($booking_total_price,2,'.','');?>                 
                </td>
               </tr>
              <tr class="light">
               <td class="first" align="right"><?php echo _('10% Arrhes / Dépôt sera facturé en');?>:</td>
               <td><span class="cur book selected"><?php echo $bookCurSymbol.$SPACE.$settle_deposit_booking;?></span></td>
              </tr>

							<tr class="light">
               <td class="first" align="right"><span id="bookingFeeDesc"><?php echo _('No Booking fees')?>:</span></td>
							 <td><span style="display: inline;"><b><span class="cur book selected"><?php echo _('Free')?></span></b></span></td>
              </tr>

              <?php /*?><tr class="light">
               <td  align="right">

                   <span id="bookingFeeDesc"><strong>
									 <?php
										 $member_gbp = $cur.' 12.00';
										 $member_eur = currency_symbol('EUR').' 14.00 ';
										 $member_usd = currency_symbol('USD').' 20.00';
                  	?>
                   <span class="cur gbp selected"><?php printf(gettext("%s yearly membership card - waived:"),$member_gbp); ?></span>
									 <span class="cur usd"><?php printf(gettext("%s yearly membership card - waived:"),$member_usd); ?></span>
									 <span class="cur eur"><?php printf(gettext("%s yearly membership card - waived:"),$member_eur); ?></span>
									 </strong></span>
									 </td>
                   <td>
                   <span style="display: inline;">
                     <b>
										 <span class="cur book selected"><?php echo $bookCurSymbol;?> 0.00</span>
                     </b>
                   </span>

               </td>
              </tr><?php */?>


              <tr class="end-total">
               <td class="first" align="right"><strong><?php echo _('Total à payer maintenant');?>:</strong></td>
               <td><span style="display: inline;"><b><span class="cur book selected"><?php echo $bookCurSymbol.$SPACE.$settle_deposit_booking;?></span></b></span></td>
              </tr>

              <tr>
               <td valign="top" class="dotted" colspan="2">
								 <b><?php echo $property_currency;?> <?php echo number_format($booking_total_hostel_price*(1-$hb_arrhes_rate),2,'.','');?></b>
                 <span style="display: inline;">
                 <?php if(strcmp($property_currency,$bookCurrency)!=0):?>
                  	(<?php echo '~ '.$bookCurSymbol.$SPACE.number_format($booking_total_price*(1-$hb_arrhes_rate),2,'.','');?>)

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
       </table>
			
      <?php endif; // end if api error?>
			</div>
		</div>	
			
		<form action="<?php echo secure_site_url(); ?>" method="post" onSubmit="booking_confirm2('<?php echo secure_site_url(); ?>',false,'<?php echo $settleCurrency;?>'); return false;">
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
							<select name="Nationality" id="Nationality">
							<option <?php if(empty($book_nationality)) echo "selected=\"selected\" "; ?>value="">----- <?php echo _('Choisir le pays'); ?> -----</option>
							<?php
							$this->Db_hb_country->list_options_nationalities($this->site_lang,$book_nationality);
							?>
							</select>       
							<script type="text/javascript">
							var Nationality = new LiveValidation('Nationality', { validMessage: ' ', onlyOnBlur: true});
							Nationality.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
							</script>
						</div>
						<div class="payment_info_block dontshowerror">
							<?php //if($shared_room_selected === true):?>
							<label for="amount"><?php echo _("People"); ?> <span class="mandatory">*</span></label>
							<label class="small_label" for="female"><?php echo _("Female:"); ?></label>
							<select class="small_select" name="female_count" id="female_count">
							<?php for($f=0;$f<=$booking_total_people;$f++):?>
							<option <?php if($female_count == $f) echo "selected=\"selected\""; ?> value="<?php echo $f; ?>"><?php echo $f; ?></option>
							<?php endfor;?>
							</select>
							
							<label class="small_label" for="male"><?php echo _("Male:"); ?></label>
							<select class="small_select" name="male_count" id="male_count">
							<?php for($m=0;$m<=$booking_total_people;$m++):?>
							<option <?php if($male_count == $m) echo "selected=\"selected\""; ?> value="<?php echo $m; ?>"><?php echo $m; ?></option>
							<?php endfor;?>
							</select>
							<script type="text/javascript">
							var femaleselectmenu = document.getElementById("female_count");
							femaleselectmenu.onchange = function()
							{
							auto_persons_count('female_count','male_count',<?php echo $booking_total_people;?>);
							update_count_validations();
							}
							
							var maleselectmenu = document.getElementById("male_count");
							maleselectmenu.onchange = function(){
							auto_persons_count('male_count','female_count',<?php echo $booking_total_people;?>);
							update_count_validations();
							}
							
							var fem_count = new LiveValidation('female_count', { validMessage: ' ', onlyOnBlur: true});
							var mal_count = new LiveValidation('male_count',   { validMessage: ' ', onlyOnBlur: true});
							var validate_male_value = <?php echo $booking_total_people;?> - document.getElementById('female_count').value;
							var validate_female_value = <?php echo $booking_total_people;?> - document.getElementById('male_count').value;
							fem_count.add(Validate.Numericality, { minimum: validate_female_value, maximum: validate_female_value });
							mal_count.add(Validate.Numericality, { minimum: validate_male_value, maximum: validate_male_value });
							function update_count_validations()
							{
							var validate_male_value = <?php echo $booking_total_people;?> - document.getElementById('female_count').value;
							var validate_female_value = <?php echo $booking_total_people;?> - document.getElementById('male_count').value;
							mal_count.destroy();
							mal_count = new LiveValidation('male_count', { validMessage: ' '});
							mal_count.add(Validate.Numericality, { minimum: validate_male_value, maximum: validate_male_value });
							fem_count.destroy();
							fem_count = new LiveValidation('female_count', { validMessage: ' '});
							fem_count.add(Validate.Numericality, { minimum: validate_female_value, maximum: validate_female_value });
							}
							</script>
					
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
							EmailAddress2.add( Validate.Confirmation, { match: 'EmailAddress' } );
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
						<?php if($this->wordpress->get_option('aj_enable_sms_reminder')){?>
						<div class="payment_info_block">							
							<label for="sms"><?php echo _('Text/SMS (FREE)')?></label><?php echo sms_menu("sms",$sms,'none');?>
						</div>
						<?php }else{?>
						<input type="hidden" name="sms" id="sms" value="none" />
						<?php }	?>  
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
						<div class="payment_info_block">
							<input class="checkbox" type="checkbox" name="mail_subscribe" id="mail_subscribe" value="1" onMouseOver="toggleMailCheckbox();" checked="checked" style="margin-top:20px;" />
							<label style="margin-top:23px" for="mail_subscribe" class="checkbox"><?php echo _('Abonnement newsletter'); ?></label>
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
					<input type="hidden" id="propertyCardTypes" name="propertyCardTypes" value="<?php echo implode(",",$propertyCardTypes); ?>" />
					
					<?php foreach($roomPreferences as $room_id => $nb_person):?>
					<input type="hidden" name="book-roomPreferences[]" value="<?php echo $room_id;?>" />
					<input type="hidden" name="book-nbPersons[]" value="<?php echo $nb_person;?>" />
					<?php endforeach;?>
					
					<input type="hidden" id="book-propertyName" name="book-propertyName" value="<?php echo $booking_hostel_name; ?>">
					
					<input type="hidden" id="book-dateStart" name="book-dateStart" value="<?php echo $dateStart; ?>">
					<input type="hidden" id="book-numNights" name="book-numNights" value="<?php echo $numNights; ?>">
					<input type="hidden" id="book-currency" name="book-currency" value="<?php echo $bookCurrency; ?>">
					
					<input type="hidden" id="analytic-value" name="analytic-value" value="<?php echo $CADDepositAmount; ?>">
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

			<div class="booking_section_content" id="step3">
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
		
							foreach($propertyCardTypes as $cardtype)
							{
								//TODO if card Solo or UK maestro -> display issue no
								$card = new stdClass();
								$card->IssueNO = 0;
								$card->CCValidFrom = 0;
		
								if(($cardtype == "Solo") || ($cardtype == "UK Maestro"))
								{
									$card->IssueNO = 1;
									$card->CCValidFrom = 1;
								}
								$card_display = $card;
		
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
		
								$cctype_selected = "";
		
								if(!empty($book_cctype)&&(strcasecmp($book_cctype,$cardtype)==0))
								{
									$cctype_selected = "selected=\"selected\"";
								}
								$card_type_menu.= "<option value=\"$cardtype\" $class $cctype_selected>$cardtype</option>\n";
							}
							$card_type_menu.= "</select>\n";
		
							echo $card_type_menu;
							?>
						<script type="text/javascript">
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
											}
								);
						</script>
						<script type="text/javascript">
								var cctype = new LiveValidation('cctype', { validMessage: ' ', onlyOnBlur: true});
								cctype.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						</script>
						<?php /*?><small><?php echo _("acceptée par l'établissement");?></small><?php */?>
					</div>								
					<div class="payment_info_block dontshowerror">					
						<label for=""><?php echo _("Date d'expiration :"); ?> <span class="mandatory">*</span></label>
						<select id="ccexpiry_m" class="standard" name="ccexpiry_m">
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
						
						<select id="ccexpiry_y" class="standard" name="ccexpiry_y">
								<option value="">- - - -</option>
								<?php for($date_qty=0;$date_qty<10;$date_qty++){
										$year  = mktime(0, 0, 0, date("m") , date("d"), date("Y")+$date_qty);
								?>
								<option value="<?php echo date("y",$year);?>" <?php if(isset($book_ccexpiry_y)&&strcasecmp($book_ccexpiry_y,date("y",$year))==0) echo "selected=\"selected\"";?>><?php echo date("Y",$year);?></option>
								<?php }?>
		
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
						<input type="text" class="small_text" autocomplete="off" name="cvv" id="cvv" maxlength="4" size="4" value="<?php if(!empty($cvv)) echo $cvv;?>"/><a href="<?php echo secure_site_url();?>images/ccard.png" onclick="return false;" class="screenshot" rel="<?php echo secure_site_url();?>images/ccard.png"><?php echo _("Qu'est-ce que c'est ?");?></a>
						<script type="text/javascript">
								var cvv = new LiveValidation('cvv', { validMessage: ' ', onlyOnBlur: true});
								cvv.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
								cvv.add( Validate.Format, { pattern:/^[0-9 ]*$/, failureMessage: "<?php echo _('Champ obligatoire'); ?>"} );
								cvv.add( Validate.Length, { is: 3, failureMessage: "<?php echo _('Champ obligatoire'); ?>"} );
								<?php /*?>ccnumber.add( Validate.Numericality, { notANumberMessage: "<?php echo _('Chiffres seulement'); ?>", notAnIntegerMessage: "<?php echo _('Chiffres seulement'); ?>", onlyInteger: true } );<?php */?>
						</script>
					</div>
				</div>
						
				<div class="group">
					<div class="payment_info_block valid_from" style="display:none;">
						<label for=""><?php echo _("Numéro d'émission :"); ?> <span class="mandatory">*</span></label>
						<input type="text" autocomplete="off" name="issueno" id="issueno" />
					</div>
					<div class="payment_info_block valid_from" style="display:none;">
						<label for=""><?php echo _("Valide depuis :"); ?> <span class="mandatory">*</span></label>
						<select id="ccvalidfrom_m" class="standard" name="ccvalidfrom_m">
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
						<select id="ccvalidfrom_y" class="standard" name="ccvalidfrom_y">
						<option value="">- - - -</option>				
						<?php for($date_qty=0;$date_qty<10;$date_qty++){
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
						<span><?php printf( gettext("J'accepte %s les termes et conditions %s"),"<a class=\"popup\" target=\"_blank\" href=\"".$this->wordpress->get_option('aj_page_cond')."?print=nostyle\">","</a>");?></span>
						<script type="text/javascript">
								var checkval = new LiveValidation('conditions_agree', { validMessage: ' ', onlyOnBlur: true});
								checkval.add( Validate.Acceptance );
						</script>
					</div>
				</div>
				
				<div class="reminders">
					<span class="last_reminder_price_now"><strong><?php echo _('Total à payer maintenant');?>: <?php echo $bookCurSymbol;?> <?php echo $settle_deposit_booking;?></strong></span>
					<span class="last_reminder_price_full">
						<b><?php echo $property_currency;?> <?php echo number_format($booking_total_hostel_price*(1-$hb_arrhes_rate),2,'.','');?></b>
						<span style="display: inline;">
						<?php if(strcmp($property_currency,$bookCurrency)!=0):?>
							(<?php echo '~ '.$bookCurSymbol.' '.number_format($booking_total_price*(1-$hb_arrhes_rate),2,'.','');?>)
						
						<?php endif;?>
						</span>						
						<?php echo _('est le montant total à payer à votre arrivée');?>
					</span>					
					<span class="last_reminder_confirmation">
					<?php echo _("* En cliquant sur le bouton de Confirmation ci-dessous, vous acceptez les modalités et les conditions et autorisez la facturation d'un montant non remboursable de");?>: <?php echo $bookCurSymbol;?> <?php echo $settle_deposit_booking;?>
					</span>
				</div>	
					
				<div class="payment_button group">
					<input id="submit-payment" type="submit" class="button-green box_shadow_hard box_round hoverit" value="<?php echo _("Confirmer");?>" onClick="check_error();"<?php /*?>onClick="javascript: pageTracker._trackPageview('/click/confirmation');"<?php */?> />
          <?php if ($this->wordpress->get_option('aj_ssl_url') ==""){?>
          <img title="<?php printf(gettext("Transaction 100%% sécurisée et encryptée"));?>" id="ssl-img" class="ssl-image" height="45" width="45" src="<?php echo secure_site_url();?>images/GandiSSL2.png" alt="<?php echo _("sécurisé");?>" />
          <?php }else{?>
          <a target="_blank" title="<?php printf(gettext("Transaction 100%% sécurisée et encryptée"));?>" href="<?php echo $this->wordpress->get_option('aj_ssl_url');?>"><img class="ssl-image" id="ssl-img" height="45" width="45" src="<?php echo secure_site_url();?>images/GandiSSL2.png" alt="<?php echo _("sécurisé");?>" /></a>
          <?php }?>
          <?php /*?><img class="lock-book" src="<?php echo secure_site_url();?>images/padlock.png" alt="sécurisé" /><?php */?>
					<p id="check_error" style="display:none;"><?php echo _('Please make sure all required fields are filled out correctly'); ?></p>
					<p id="loading_message" class="loading_book" style="display: none;">
						<img src="<?php echo secure_site_url();?>images/V2/loading-squares-greenback.gif" alt=""/>
						<span><?php echo _('Traitement de la demande...'); ?></span>
					</p>
				</div>

				<?php if($api_booking_error=="api_msg"):?>
        <div class="transaction_error api_error">
					<p><?php printf( gettext("Il y a eu une erreur lors du traitement de votre demande, veuillez vous assurer que vos informations sur la méthode de paiement sont exactes. Si le problème persiste, veuillez communiquer avec nous à l'adresse suivante : %s"),'<a target="_blank" href="'.$this->wordpress->get_option('aj_page_faq').'?print=nostyle">'.$this->wordpress->get_option('aj_page_faq').'</a>');?></p>
					<span class="error_title"><?php echo _('Errors')?>:</span>
					<ul>
					<?php foreach($api_booking_error_msg->errors as $error):					
						if(!empty($error->error->extraInfo_translated))
						{
							echo '<li class="translated">'.$error->error->extraInfo_translated.'</li>';
							echo '<li class="original" style="display:none;">'.$error->error->extraInfo.'</li>';
						}
						elseif(!empty($error->error->extraInfo))
						{
							echo $error->error->extraInfo;
						}
						elseif(!empty($error->error->description_translated))
						{
							echo '<li class="translated">'.$error->error->description_translated.'</span>';
							echo '<li class="original" style="display:none;">'.$error->error->description.'</span>';
						}
						else
						{
							echo '<li>'.$error->error->description.'</li>';
						}
					endforeach;?>				
					<?php if($api_booking_error=="api_out"):?>
					<li class="api_error"><?php echo $api_booking_error_msg; ?></li>
					<?php endif;?>
					</ul>
				</div>
				<?php endif;?>
			</div>
			</div>
	</form>	 
		<?php 
		$displayVelaro =  $this->config->item('displayVelaro');
		
		if($displayVelaro == 1)
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
		<?php } 
		         } ?>	 
	 
	 <?php if (!empty($important_info->original)){?>
	 <div class="booking_section">  
		<h2 id="step5-title" class="booking_section_title box_round green_gradient_faded"><?php echo _("Informations Importantes");?></h2>			
		<div class="booking_section_content" id="step5">

      <?php if (!empty($important_info->translation)){?>
      <div class="switch-translate goup">
      <p><?php echo _("Certains extraits du texte de cette page ont été traduits automatiquement. Cliquer sur les liens suivants changer les version :"); ?><br /><a class="show-original" href="#"><?php echo _("Voir l'original"); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="show-translate" href="#"><?php echo _("Voir la version traduite"); ?></a>
      </p>
      </div>
      <?php }?>

      <?php if (!empty($important_info->translation)){?>

      <div class="translated"><?php echo nl2p(var_check($important_info->translation,""),false,true)?></div>
      <div class="original" style="display:none;"><?php echo nl2p(var_check($important_info->original,""),false,true)?></div>

      <?php }elseif(!empty($important_info->original)){ ?>

      <div class="original"><?php echo nl2p(var_check($important_info->original,""),false,true)?></div>

      <?php } ?>
		</div>
	</div>
	<?php }?>
	 
	 <div class="booking_section">  
		<h2 id="step6-title" class="booking_section_title box_round green_gradient_faded"><?php echo _("Changements - Annulations"); ?></h2>			
		<div class="booking_section_content" id="step6">

      <?php if (!empty($booking_request->TermsAndConditionsTranslated->value)){?>
      <div class="switch-translate goup">
      <p><?php echo _("Certains extraits du texte de cette page ont été traduits automatiquement. Cliquer sur les liens suivants changer les version :"); ?><br /><a class="show-original" href="#"><?php echo _("Voir l'original"); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="show-translate" href="#"><?php echo _("Voir la version traduite"); ?></a>
      </p>
      </div>
      <?php }elseif(empty($booking_request->TermsAndConditions->value)){?>
			<p><strong><?php echo _("Les changements et annulations sont possibles, faciles à faire et se font directement avec l‘établissement choisi qui en est responsable. Changements de dates/chambres : possible selon disponibilités. Annulations : au moins 24 heures en avance pour ne pas être facturé la première nuit.");?></strong></p>
       <?php }?>

      <p><b><span class="notes"><?php echo _("Notes Importantes");?></span></b>
      <?php printf( gettext("Sur %s, vous payez seulement les arrhes (10%% du montant total) afin de confirmer et garantir votre réservation. Le montant restant vous sera demandé à votre arrivée."),$this->config->item('site_name'));?></p>

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
