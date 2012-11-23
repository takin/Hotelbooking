<?php if(!$isAjax):?>
<div id="content" class="booking-view">
<?php endif;?>


	<div class="page-meta group">
		<h1 class="text-shadow-wrapper padit dot-icon"><?php echo _("Réserver Maintenant");?></h1>
	</div>
	<form class="basic" action="" method="post" onSubmit="booking_confirm('<?php echo secure_site_url(); ?>',false,'<?php echo $settleCurrency;?>'); return false;">
	<div class="white-back round-corner5 border-around form">
			<h2><span class="secure"><?php echo _('Informations personnelles - Sécurisées et Encryptées');?></span></h2>
			<ul class="group">
			<?php echo form_error('firstname','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
			<?php echo form_error('lastname','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
			<?php echo form_error('nationality','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
			<?php echo form_error('gender','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
			<?php echo form_error('arrival_time','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
			<?php echo form_error('email','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
			<?php echo form_error('email2','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
			<?php echo form_error('phone_number','<li class="error round-corner5 group"><span>', '</span></li>'); ?>

			<li>
			<label for="firstname"><?php echo _('Prénom:');?></label>
      <input type="text" value="<?php echo $book_firstname;?>" name="firstname" id="firstname" class="text">
			</li>

			<li>
			<label for="lastname"><?php echo _('Nom:'); ?></label>
      <input type="text" value="<?php echo $book_lastname;?>" name="lastname" id="lastname" class="text">
			</li>

			<li>
			<label for="nationality"><?php echo _('Nationalité :'); ?></label>
      <?php $this->Db_country->select_country("Nationality","nationality",set_value('nationality', $book_nationality),"style=\"width: 95%;\"","en",$this->site_lang,_('Choisir le pays'));  ?>
			</li>

			<li class="quarter">
			<label for="gender"><?php echo _("Sexe:"); ?></label>
        <select name="gender" id="gender">
        <option <?php if(strcasecmp($book_gender,"Male")==0) echo "selected=\"selected\""; ?> value="Male"><?php echo _("Masculin"); ?></option>
        <option <?php if(strcasecmp($book_gender,"Female")==0) echo "selected=\"selected\""; ?> value="Female"><?php echo _("Féminin"); ?></option>
        </select>
			</li>

			<li class="quarter">
			<label for="lastname"><?php echo _("Heure d'arrivée:");?></label>
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
			</li>

			<li class="clear">
			<label for="lastname"><?php echo _("Adresse Email");?></label>
      <input type="text" value="<?php echo $book_email_address;?>" id="EmailAddress" name="email" class="text">
			</li>

			<li>
			<label for="lastname"><?php echo _("Confirmation Email");?> </label>
      <input type="text" value="<?php echo $book_email_address2;?>" id="EmailAddress2" name="email2" class="text">
			</li>

			<li>
			<label for="lastname"><?php echo _('Mobile Phone (please include the country code)'); ?></label>
      <input type="tel" value="<?php echo $book_phone_number;?>" id="phone_number" name="phone_number" class="text">
      <small><?php echo _('Country code, Area code (without first 0) and mobile number; no spaces, brackets or dashes.')?><br /><?php echo _('UK Example: 44 2012341234')?></small>
			</li>
      <li>
        <label for="sms"><?php echo _('Text/SMS (FREE)')?> :</label>
        <?php echo sms_menu("sms",$sms,'none');?>
      </li>

			<li class="group checkbox-group">
      <input style="width:auto;" class="checkbox" type="checkbox" name="mail_subscribe" id="mail_subscribe" value="1" <?php if($mail_subscribe==true) echo "checked=\"checked\"";?> />
			<label class="checkbox-label" for=""><?php echo _('Abonnement newsletter'); ?></label>
			<br />
			<a class="popup" target="_blank" href="<?php echo $this->wordpress->get_option('aj_page_conf'); ?>?print=mobile"><?php echo _('cliquer ici pour notre charte');?></a>

			</li>
			</ul>

	</div>

	<div class="white-back round-corner5 border-around form">
			<h2><span class="secure"><?php echo _('Méthode de Paiement - Sécurisée et Encryptée'); ?></span></h2>
			<ul class="group">
				<?php echo form_error('cctype','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
        <?php echo form_error('ccname','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
        <?php echo form_error('ccnumber','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
        <?php echo form_error('ccexpiry_m','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
        <?php echo form_error('ccexpiry_y','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
        <?php echo form_error('cvv','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
        <?php echo form_error('conditions_agree','<li class="error round-corner5 group"><span>', '</span></li>'); ?>
        <?php
        if(!empty($api_booking_error)&&($api_booking_error=="api_msg"))
        {
          foreach($api_booking_error_msg as $error)
          {
            if(!empty($error->messageTranslated))
            {
  						?>
              <li class="error round-corner5 group"><span><?php echo $error->messageTranslated. " (".$error->message.")";?></span></li>
              <?php
            }
            else
            {
              ?>
              <li class="error round-corner5 group"><span><?php echo $error->message;?></span></li>
              <?php
            }
          }
        }
        elseif(!empty($api_booking_error)&&($api_booking_error=="api_out"))
        {
          ?>
          <li class="error round-corner5 group"><span><?php echo $api_booking_error_msg[0]; ?></span></li>
          <?php
        }
        ?>
				<li>
				<label for="cctype"><?php printf(gettext("Type de carte : %s(acceptée par l'établissement)"),""); ?></label>
        <?php
          $card_type_menu = "<select name=\"cctype\" id=\"cctype\">\n";
          $card_type_menu.= "<option value=\"\" class=\"hideall\" >--</option>\n";

          $jvar_exclude_card = "[ ";
          foreach($card_supported as $card)
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
            function showissueno()
            {
            	$('li.issue_no').show();
            }
            function hideissueno()
            {
            	$('li.issue_no').hide();
            }

            function showvalidfrom()
            {
            	$('.valid_from').show();
            }
            function hidevalidfrom()
            {
            	$('.valid_from').hide();
            }
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
                          //TODO select different currency
//                          booking_confirm('<?php echo secure_site_url(); ?>',true,cctypeCur);
                        }

                    }
            );
          </script>
				</li>
				<li>
				<label for="ccname"><?php printf(gettext('sur la carte %s de crédit :'),""); ?></label>
				<input type="text" value="<?php echo $book_ccname;?>" name="ccname" id="ccname" class="text" />
				</li>
				<li>
				<label for="ccnumber"><?php echo _('Numéro'); ?> <?php printf(gettext('de la carte %s de crédit :'),""); ?></label>
				<input type="tel" value="<?php echo $book_ccnumber;?>" name="ccnumber" id="ccnumber" class="text" />
				</li>
				<li>
					<label for=""><?php echo _("Date d'expiration :"); ?></label>
				</li>
				<li class="quarter">
					<select name="ccexpiry_m" id="ccexpiry_m">
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
				</li>
				<li class="quarter3">
					<select name="ccexpiry_y" id="ccexpiry_y">

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
				</li>
			<li class="clear">
				<label for="cvv"><?php echo _("Code de sécurité :"); ?></label>
				<input type="tel" value="" name="cvv" id="cvv" class="text">
			</li>

      <li class="valid_from">
          <label for=""><?php echo _("Valide depuis :"); ?></label>
                <select id="ccvalidfrom_m" name="ccvalidfrom_m">
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
                <select id="ccvalidfrom_y" name="ccvalidfrom_y">
                <option value="">- - - -</option>

                <?php for($date_qty=0;$date_qty<10;$date_qty++)
                      {
                        $year  = mktime(0, 0, 0, date("m") , date("d"), date("Y")-$date_qty);?>

                        <option value="<?php echo date("y",$year);?>"><?php echo date("Y",$year);?></option>
                <?php } ?>
                </select>
      </li>

      <li class="issue_no">
        <label for=""><?php echo _("Numéro d'émission :"); ?></label>
        <input type="text" autocomplete="off" name="issueno" id="issueno" />
      </li>

      <li class="group checkbox-group validation">
				<input style="width:auto;" class="checkbox" type="checkbox" checked="checked"  name="conditions_agree" id="conditions_agree" value="1" <?php if($conditions_agree==true) echo "checked=\"checked\"";?> />
        <label class="checkbox-label"><?php printf( gettext("J'accepte %s les termes et conditions %s"),"<a class=\"popup\" target=\"_blank\" href=\"".$this->wordpress->get_option('aj_page_cond')."?print=mobile\">","</a>");?></label>
      </li>

			</ul>
			<p><?php echo _("* En cliquant sur le bouton de Confirmation ci-dessous, vous acceptez les modalités et les conditions et autorisez la facturation d'un montant non remboursable de");?>
			<strong style="font-size:1.4em;">
			<?php
			if(!empty($settle_bill_total)) echo currency_symbol($settleCurrency)." ".$settle_bill_total;
			?>
			</strong>
			</p>
	</div>
	<script type="text/javascript">
    var firstname = new LiveValidation('firstname', { validMessage: ' ', onlyOnBlur: true});
    firstname.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});

    var lastname = new LiveValidation('lastname', { validMessage: ' ', onlyOnBlur: true});
    lastname.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});

    var Nationality = new LiveValidation('Nationality', { validMessage: ' ', onlyOnBlur: true});
    Nationality.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});

    var EmailAddress = new LiveValidation('EmailAddress', { validMessage: ' ', onlyOnBlur: true});
    EmailAddress.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
    EmailAddress.add(Validate.Email, {failureMessage: "<?php echo _('Insérer un courriel valide'); ?>"});

    var EmailAddress2 = new LiveValidation('EmailAddress2', { validMessage: ' ', onlyOnBlur: true});
    EmailAddress2.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
    EmailAddress2.add(Validate.Email, {failureMessage: "<?php echo _('Insérer un courriel valide'); ?>"});
    EmailAddress2.add( Validate.Confirmation, { match: 'EmailAddress' } );

    var phone_number = new LiveValidation('phone_number', { validMessage: ' ', onlyOnBlur: true});
    phone_number.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
    phone_number.add(Validate.Numericality, {notANumberMessage: "<?php echo _('Invalid phone number format'); ?>"});

    var cctype = new LiveValidation('cctype', { validMessage: ' ', onlyOnBlur: true});
    cctype.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
    cctype.add(Validate.Exclusion, { within: <?php echo $jvar_exclude_card; ?> });

    var ccname = new LiveValidation('ccname', { validMessage: ' ', onlyOnBlur: true});
    ccname.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});

    var ccnumber = new LiveValidation('ccnumber', { validMessage: ' ', onlyOnBlur: true});
    ccnumber.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
    ccnumber.add( Validate.Format, { pattern:/^[0-9 ]*$/, failureMessage: "<?php echo _('Champ obligatoire'); ?>"} );

    var ccexpiry_m = new LiveValidation('ccexpiry_m', { validMessage: ' ', onlyOnBlur: true});
    var ccexpiry_y = new LiveValidation('ccexpiry_y', { validMessage: ' ', onlyOnBlur: true});

    ccexpiry_m.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
    ccexpiry_y.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});

    var checkval = new LiveValidation('conditions_agree', { validMessage: ' ', onlyOnBlur: true});
    checkval.add( Validate.Acceptance, {failureMessage: '<?php echo _('Champ obligatoire'); ?>'} );
  </script>
  <input type="hidden" id="propertyNumber" name="propertyNumber" value="<?php echo $propertyNumber; ?>" />
  <input type="hidden" id="propertyCity" name="propertyCity" value="<?php echo $propertyCity; ?>" />
  <input type="hidden" id="propertyCountry" name="propertyCountry" value="<?php echo $propertyCountry; ?>" />
  <input type="hidden" id="book-currency" name="book-currency" value="<?php echo $bookCurrency; ?>">
  <input type="hidden" id="settle_bill_total" name="settle_bill_total" value="<?php echo $settle_bill_total; ?>">
  <input type="hidden" name="bsid" id="bsid" value="<?php echo $bsid; ?>" />
  <input type="hidden" name="sign_me_up" id="sign_me_up" value="1">
  <?php
    foreach($card_supported as $card)
    {
      ?>
      <input type="hidden" name="cardsupported[]" value="<?php echo (string)$card->type."|".(string)$card->name."|".(string)$card->currency."|".(string)$card->IssueNO."|".(string)$card->CCValidFrom; ?>" />
      <?php
    }
    ?>

	<div class="submit-button">
		<input id="submit-payment" type="submit" class="submit-green green-button" value="<?php echo _("Confirmer");?>" />
		
	</div>

	<div id="loading_message" class="loading_book" style="display:none;">
		<img src="<?php echo secure_base_url();?>images/mobile/loading-transaction.gif" alt="" />
		<p><?php echo _('Traitement de la demande...'); ?></p>
	</div>

	</form>


<?php if(!$isAjax):?>
</div>
<?php endif;?>