<div id="content" class="avail-view">
		<div class="page-meta group">
			<h1 class="text-shadow-wrapper icon-calendar"><?php echo _('Disponibilités'); ?></h1>
		</div>

      <?php 
      if(($api_error === FALSE) && empty($distinctRoomTypes["sharedRooms"]) && empty($distinctRoomTypes["privateRooms"]))
      {
        ?>
        <div class="white-back round-corner5 form-error">
					<p class="red">
					<strong><?php echo _("Erreur:");?> </strong> 
					<?php echo _('No rooms are available for all the nights you selected.');?>
					</p>
				</div>
        <?php
      }
      //If API no answer
      elseif(($api_error === TRUE) && ($api_error_msg === FALSE))
      {?>
        <div class="white-back round-corner5 form-error">
        <p class="red">
				<strong><?php echo _("Erreur:");?></strong> <?php echo _('Serveur inaccessible en ce moment.');?>
				</p>
				</div>
      <?php
			}
      elseif($api_error === TRUE)
      {
        ?>
        <div class="white-back round-corner5 form-error">
        <p class="red">
          <strong><?php echo _("Erreur:");?> </strong> 
          <?php 
            if(!empty($api_error_msg->messageTranslated))
            {
              echo $api_error_msg->messageTranslated;
            }
            else
            {
              echo $api_error_msg->message;
            }
          ?>
        </p>
        <br />
				<p class="red">
          <strong><?php echo _("Détails:");?> </strong>
          <?php 
            if(!empty($api_error_msg->detailTranslated))
            {
              echo $api_error_msg->detailTranslated;
            }
            else
            {
              echo $api_error_msg->detail;
            }
          ?>
        </p>
        </div>
        <?php
      }
      ?>
      
      <div class="white-back round-corner5 border-around form">
			<p style="margin-bottom:10px;">
      <?php 
      if(!empty($propertyName))
      {
        ?>
        <strong><?php echo $propertyName;?></strong>, <?php echo $propertyCity;?>, <?php echo $propertyCountry;?>
        <?php
      }
      ?>
			</p>
			<p class="no-margin"><?php echo _('Arrivée');?>: <b><?php echo strftime("%e %B %Y",$dateStart->format('U'));?></b><br /><?php echo _('Nombre de Nuits');?>: <b><?php echo $numNights;?></b></p>
			
			<a href="#" class="white-button round-corner5 change-dates"><span class="arrow"><?php echo _('Change Dates');?></span></a>	
			<div class="change-dates-select"<?php if(empty($distinctRoomTypes["sharedRooms"]) && empty($distinctRoomTypes["privateRooms"]))echo ' style="display:block"';?>>
        <form id="avail-form" action="<?php echo site_url("ma/".$propertyNumber);?>" method="post" onSubmit="avail_redirect();" >
          <input type="hidden" name="propertyName"   value="<?php echo $propertyName; ?>"/>
          <input type="hidden" name="propertyNumber" value="<?php echo $propertyNumber; ?>"/>
          <input type="hidden" name="propertyCity" value="<?php echo $propertyCity; ?>"/>
          <input type="hidden" name="propertyCountry" value="<?php echo $propertyCountry; ?>"/>
  
  				<ul>
  					<li>
    					<label for="search-date"><?php echo _('Arrivée le:');?></label><br />
              <?php 
    					select_day("search-day","search-day",$dateStart->format('d'));
              select_month_year("search-year-month","search-year-month","",0,12,$dateStart->format('Y-m'));
              ?>
  					</li>
  					<li>
              <label for="search-night"><?php echo _('Nuits:');?></label><br />
							<?php
							$hb_api_used = ($this->api_used == HB_API) ? TRUE : FALSE;
              select_nights('',"search-night","search-night",$numNights, $hb_api_used);
              ?>
						</li>
            <li class="reduce"><label for="search-currency"><?php echo _("Devise:");?></label><br />
              <?php 
              $this->Db_currency->select_currency("search-currency","search-currency",$this->site_currency,"",$this->site_lang);
              ?>
  				  </li>
            <li>
            <script>
            function avail_redirect()
            {
                document.getElementById('avail-form').action = '<?php echo site_url("ma/".$propertyNumber);?>' + "/" + document.getElementById('search-year-month').value+"-"+document.getElementById('search-day').value+"/"+document.getElementById('search-night').value + '?currency='+document.getElementById('search-currency').value;
            }
            </script>
            <input type="hidden" name="book-propertyName" value="<?php echo $propertyName; ?>" />
            <input type="hidden" name="book-propertyNumber" value="<?php echo $propertyNumber; ?>" />
            <input type="hidden" name="propertyCity" value="<?php echo $propertyCity; ?>"/>
            <input type="hidden" name="propertyCountry" value="<?php echo $propertyCountry; ?>"/>
            <div class="submit-button">
							<input type="submit" class="submit-green green-button" value="<?php echo _('Soumettre');?> &raquo;" />
							
						</div>
            </li>
  				</ul>
        </form>
			</div>
    </div>  
		<?php if(!empty($distinctRoomTypes["sharedRooms"]) || !empty($distinctRoomTypes["privateRooms"])):?>
		<div class="white-back round-corner5 border-around form">
      <form id="rooms_selections" class="basic" method="post" action="<?php echo site_url("mbooking_confirmation"); ?>">
		
			<h2><?php echo _("Room type and number of guests");?>:</h2>

      <?php 
      if(!empty($distinctRoomTypes["sharedRooms"]))
      {
        ?>
  			
  			<div class="inner-table round-corner5">
  			<h3><img src="<?php echo site_url("images/mobile/user-share.png");?>" alt="" /><?php echo _('Chambres partagées - Dortoirs'); ?> <span><?php echo _('Price per person (Dorm shared with others).'); ?></span></h3>
  			<table class="avail" cellspacing="0" cellpadding="0">			
  				<tbody>
          <?php
          foreach($distinctRoomTypes["sharedRooms"] as $roomIndex => $sharedRoom)
          {
            ?>
            <input type="hidden" name="book-roomPreferences[]" value="<?php echo $sharedRoom["roomTypeCode"]; ?>" />
            <tr>
              <td class="room-name"><?php echo $sharedRoom["description"]; ?></td>
              <td class="price"><?php echo $sharedRoom["currency"]." ".$sharedRoom["total_price"]; ?></td>
              <td class="select-guest">
                <select id="sharedsel_<?php echo $roomIndex; ?>" class="sharedsel" name="book-nbPersons[]">
                <option value="0" >-</option>
                <?php 
                for($p=1;$p<=$sharedRoom["availableBeds"];$p++)
                {
                  ?>
                  <option value="<?php echo $p;?>" ><?php echo $p;?></option>
                  <?php
                }
                ?>
                </select>
              </td>
            </tr>
            <?php
          }
          ?>
  				</tbody>
  			</table>			
  			</div>
  			<?php 
      }
      
      if(!empty($distinctRoomTypes["privateRooms"]))
      {
        ?>
        <div class="inner-table round-corner5">
          <h3><img src="<?php echo site_url("images/mobile/user-room.png");?>" alt="" /><?php echo _('Chambres privées'); ?><span><?php echo _('Price per room (not per person).'); ?></span></h3>
          <table class="avail" cellspacing="0" cellpadding="0">     
            <tbody>
            <?php
            foreach($distinctRoomTypes["privateRooms"] as $roomIndex => $privateRoom)
            {
              ?>
              <input type="hidden" name="book-roomPreferences[]" value="<?php echo $privateRoom["roomTypeCode"]; ?>" />
              <tr>
                <td class="room-name"><?php echo $privateRoom["description"]; ?></td>
                <td class="price"><?php echo $privateRoom["currency"]." ".$privateRoom["total_price"]; ?></td>
                <td class="select-guest">
                  <select id="privatesel_<?php echo $roomIndex; ?>" class="privatesel" name="book-nbPersons[]">
                  <option value="0" >-</option>
                  <?php 
                  for($p=1;$p<=$privateRoom["availableRooms"];$p++)
                  {
                    ?>
                    <option value="<?php echo $p*$privateRoom['bedsPerRoom'];?>" ><?php echo $p;?></option>
                    <?php
                  }
                  ?>
                  </select>
                </td>
              </tr>
              <?php
            }
            ?>
            </tbody>
          </table>
        </div>
        <?php 
      }
      
      //If no error
      if($api_error == FALSE)
      {
        ?>
        <input type="hidden" name="book-propertyName" value="<?php echo $propertyName; ?>" />
        <input type="hidden" name="book-propertyNumber" value="<?php echo $propertyNumber; ?>" />
        <input type="hidden" name="propertyCity" value="<?php echo $propertyCity; ?>"/>
        <input type="hidden" name="propertyCountry" value="<?php echo $propertyCountry; ?>"/>
        <input type="hidden" name="book-dateStart" value="<?php echo $dateStart->format('Y-m-d'); ?>" />
        <input type="hidden" name="book-numNights" value="<?php echo $numNights; ?>" />
        <input type="hidden" name="book-currency" value="<?php echo $currency; ?>" />
        <?php 
        
      }
      
      ?>
      
		</div>  
		<?php // Endif for the whole table ?>
		<?php endif;?>
		
		<?php if(($api_error == FALSE) &&
		         (!empty($distinctRoomTypes["sharedRooms"]) || !empty($distinctRoomTypes["privateRooms"]))): ?>
		<div class="submit-button">
      <script>
      function validate_avail()
      { 
        var total_persons = new Number(0);
        var selectPersons = document.getElementsByName("book-nbPersons[]");
        
        for(var i=0; i < selectPersons.length; i++)
        {
        	total_persons += new Number(selectPersons[i].value);
        }
        if(total_persons > 0)
        {
            return true;
        }
        
        alert('<?php echo _('Please enter at least one choice in the above table to book a room.');?>');

        return false;
      }
      </script>
			<input type="submit" class="submit-green green-button" value="<?php echo _('Book Now');?> &raquo;" onClick="return validate_avail();"/>
			
		</div>
		<?php endif; ?>
		</form>
		<?php if(!empty($distinctRoomTypes["sharedRooms"]) || !empty($distinctRoomTypes["privateRooms"])):?>
		<div class="white-back round-corner5 border-around form">
			<p><b><span class="notes"><?php echo _("Notes Importantes");?></span></b><br />
				<?php printf( gettext("Sur %s, vous payez seulement les arrhes (10%% du montant total) et les frais de service minimum et non remboursables afin de confirmer et garantir votre réservation. Le montant restant vous sera demandé à votre arrivée."),$this->config->item('site_name'));?></p>			
		</div>
		<?php endif; ?>
	
	
 
</div>