<h2 class="dotted-line margbot15"><?php echo _('Disponibilités'); ?> <span>(<?php echo $currency; ?>)</span></h2>
<?php
$date = clone $dateStart;
$datetop = date_conv($dateStart->format('Y-m-d'), $this->wordpress->get_option('aj_date_format'));?>
<div class="top-table">
<p><?php echo _('Arrivée');?>: <b><?php echo $datetop;?></b> &nbsp;&nbsp; <?php echo _('Nombre de Nuits');?>: <b><?php echo $numNights; ?></b><a id="change-dates" href="#">[<?php echo _('Change Dates/Currency'); ?>]</a></p>

</div>
<?php
if($api_error==false)
{
  $maxPersons = 10;
  if($booking_info->maxPax < $maxPersons)
  {
    $maxPersons = $booking_info->maxPax;
  }


?>

<?php
  $nbRoomType = 0;
  $sharedRoomsAvailable = 0;
	$min_price_shared = 0;
  $sharedRoomsTable = "";
  $sharedRoomsTableSelect = "";
  //Show shared rooms first
  foreach ($distinctRoomTypes as $hostel_room_type)
  {
    $availableBeds = $maxPersons;

	//debug_dump($hostel_room_type);

	//Check for dorms only
    if(substr_count($hostel_room_type['roomType'],"Private")==0)
    {

      $date->modify("-$numNights day");
      $nbRoomType++;
      if($nbRoomType % 2 != 0)
      {
        $sharedRoomsTable.= "<tr>";
		    $sharedRoomsTableSelect.= "<tr class=\"roomnb\" id=\"sroomnb_".$nbRoomType."\">";
      }
      else
      {
        $sharedRoomsTable.= "<tr class=\"odd\">";
		    $sharedRoomsTableSelect.= "<tr id=\"sroomnb_".$nbRoomType."\" class=\"roomnb\">";
      }

      $sharedRoomsTable.= '<td class="first">';
	    $sharedRoomsTableSelect.= '<td class="first">';
      if(!empty($hostel_room_type['roomTypeDescriptionTranslated']))
      {
        $sharedRoomsTable.= '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$hostel_room_type['roomTypeDescription'].'">'.$hostel_room_type['roomTypeDescriptionTranslated'].'</span>';
		    $sharedRoomsTableSelect.= '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$hostel_room_type['roomTypeDescription'].'">'.$hostel_room_type['roomTypeDescriptionTranslated'].'</span>';

      }
      else
      {
        $sharedRoomsTable.= $hostel_room_type['roomTypeDescription'];
		    $sharedRoomsTableSelect.= $hostel_room_type['roomTypeDescription'];
      }
			if($breakfast_included == 1){
				$sharedRoomsTable.= '<span class="free-breakfast">';
				$sharedRoomsTable.= _('Breakfast Included');
				$sharedRoomsTable.= '</span>';
				$sharedRoomsTableSelect.= '<span class="free-breakfast">';
				$sharedRoomsTableSelect.= _('Breakfast Included');
				$sharedRoomsTableSelect.= '</span>';
			}
//      if((($maxPersons < $hostel_room_type["bedsIncrement"])&&(strcmp($hostel_room_type['roomType'],"Private")==0)))
//      {
//       echo "!!!!!!";
//      }
//      print "<br>maxpax: ".$maxPersons." bedI: ".$hostel_room_type["bedsIncrement"]." ".$hostel_room_type['roomType']." <br>";
      $sharedRoomsTable.= "</td>";
	    $sharedRoomsTableSelect.= "</td>";

		// Max Guests per room
			$nb_guest_per_room = explode(':',$hostel_room_type['roomTypeCode']);
			
			$sharedRoomsTable.= '<td align="center" title="'._('Bed in a dorm. 1 person per bed maximum').'"><span class="nbpeople-table icon-nbpeople nbpeople-1">1</span></td>';
			$sharedRoomsTableSelect.= '<td align="center" title="'._('Bed in a dorm. 1 person per bed maximum').'"><span class="nbpeople-table icon-nbpeople nbpeople-1">1</span></td>';


      $date = clone $dateStart;
      $subtotal = 0;
      $num_nights_available_of_room = 0;

      for($i=0;$i<$numNights;$i++)
      {

					$date_available = false;
          $date_msg = '<span class="na-book price" title="'._('No dorms available').'">0</span>';

          foreach($hostel_room_type['AvailableDates']['AvailableDate'] as $date_ok)
          {
            if($date_ok['date'] == $date->format("Y-m-d"))
            {

							$num_nights_available_of_room++;
							$price_array = explode(' ',$date_ok['price']);
							if($price_array[0]=='From'){
								$price = $price_array[1];
							}else{
								$price = $price_array[0];
							}
							$subtotal = $subtotal+$price;
							//if ($numNights > 10){
							//	$date_msg = currency_symbol($date_ok['currency']).' <span class="price">'.number_format(round((float)$price),0).'</span>';
							//	break;
							//}else{
							$date_msg = currency_symbol($date_ok['currency']).' <span class="price">'.number_format($price,2,'.','').'</span>';
							//	break;
							//}

							// To check min version
							if($min_price_shared == 0){
								$min_price_shared = $date_ok['price'];
							}elseif($min_price_shared > $date_ok['price']){
								$min_price_shared = $date_ok['price'];
							}
							$currency_formin = currency_symbol($date_ok['currency']);
            }
          }

          $sharedRoomsTable.= '<td align="center" title="'._('Price per bed (not per room)').'">'.$date_msg.'</td>';

          $date->modify("+1 day");

      }
			// Getting value from above to show prices

      $display_currency = currency_symbol($date_ok['currency']);
      $sharedRoomsTableSelect.= '<td id="snbguest_'.$nbRoomType.'" class="snbguest">'._('Personnes').': <strong></strong></td>';
	    $sharedRoomsTableSelect.= '<td align="center" id="ssubtotal_'.$nbRoomType.'" class="ssubtotal"><span class="calc_init" id="ssubtotal_init_'.$nbRoomType.'">'.number_format($subtotal,2,'.','').'</span>'.$display_currency.'  <span class="calc_sum" id="ssubtotal_calc_'.$nbRoomType.'"></span></td>';

      foreach($hostel_room_type['AvailableDates']['AvailableDate'] as $avail_date)
      {
        if((int)$avail_date['availableBeds'] < $availableBeds)
        {
          $availableBeds = (int)$avail_date['availableBeds'];
        }
      }

      //TODO javascript to prevent more than maxPax booking by adding select box value

      $sharedRoomsTable.= '<td align="center">';


      //If number of night avaible of the room is higher or equal to the property min night condition dispay the room selection menu
      if($num_nights_available_of_room >= $booking_info->minNights)
      {
        $sharedRoomsTable.= "<input type=\"hidden\" name=\"book-roomPreferences[]\" value=\"".$hostel_room_type['roomTypeCode']."\" />";
        $sharedRoomsTable.= "<input type=\"hidden\" name=\"book-roomType[]\" value=\"".$hostel_room_type['roomType']."\" />";
        $sharedRoomsTable.= "<input type=\"hidden\" name=\"book-roomDesc[]\" value=\"".$hostel_room_type['roomTypeDescription']."\" />";
        if(!empty($hostel_room_type['roomTypeDescriptionTranslated']))
        {
          $sharedRoomsTable.= "<input type=\"hidden\" name=\"book-roomDescTrans[]\" value=\"".$hostel_room_type['roomTypeDescriptionTranslated']."\" />";
        }
        else
        {
          $sharedRoomsTable.= "<input type=\"hidden\" name=\"book-roomDescTrans[]\" value=\"\" />";
        }

        $sharedRoomsAvailable++;
        $sharedRoomsTable.= "<select id=\"sharedsel_".$nbRoomType."\" class=\"sharedsel\" name=\"book-nbPersons[]\">";

        $sharedRoomsTable.= "<option value=\"0\" >0</option>\n";
        for($p=1;$p<=$availableBeds;$p++)
        {
          $sharedRoomsTable.= "<option value=\"$p\" >$p</option>\n";
        }
        $sharedRoomsTable.= "</select>";
      }
      else
      {

      }

      $sharedRoomsTable.= "</td>";

      $sharedRoomsTable.= "</tr>\n";
	    $sharedRoomsTableSelect.= "</tr>\n";
    }

  }
	$colspan = $numNights+2;

	//Count shared room that are displayed
	$sharedRoomCount = $nbRoomType;

  //Show private rooms
  $nbRoomType = 0;
  $privateRoomsAvailable = 0;
	$min_price_private = 0;
	$min_price_private_room = 0;
  $privateRoomsTable = "";
  $privateRoomsTableSelect = "";

  foreach ($distinctRoomTypes as $hostel_room_type)
  {
    $availableBeds = $maxPersons;
    $availableRooms = $maxPersons;

	//debug_dump($hostel_room_type);

    //Show private rooms with beds increment lower than maxpax because if it is higher it will be a group booking and will cause problem on booking
    if(($maxPersons > $hostel_room_type["bedsIncrement"])&&(substr_count($hostel_room_type['roomType'],"Private")>0))
    {

      $date->modify("-$numNights day");
      $nbRoomType++;
      if($nbRoomType % 2 == 0)
      {
        $privateRoomsTable.=  "<tr>";
				$privateRoomsTableSelect.= "<tr class=\"roomnb\" id=\"proomnb_".$nbRoomType."\">";
      }
      else
      {
        $privateRoomsTable.= "<tr class=\"odd\">";
				$privateRoomsTableSelect.= "<tr id=\"proomnb_".$nbRoomType."\" class=\"roomnb\">";
      }

      $privateRoomsTable.= '<td class="first">';
			$privateRoomsTableSelect.= '<td class="first">';
      if(!empty($hostel_room_type['roomTypeDescriptionTranslated']))
      {
        $privateRoomsTable.= '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$hostel_room_type['roomTypeDescription'].'">'.$hostel_room_type['roomTypeDescriptionTranslated'].'</span>';
				$privateRoomsTableSelect.= '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$hostel_room_type['roomTypeDescription'].'">'.$hostel_room_type['roomTypeDescriptionTranslated'].'</span>';
      }
      else
      {
        $privateRoomsTable.= $hostel_room_type['roomTypeDescription'];
				$privateRoomsTableSelect.= $hostel_room_type['roomTypeDescription'];
      }
//      if((($maxPersons < $hostel_room_type["bedsIncrement"])&&(strcmp($hostel_room_type['roomType'],"Private")==0)))
//      {
//       echo "!!!!!!";
//      }
//      print "<br>maxpax: ".$maxPersons." bedI: ".$hostel_room_type["bedsIncrement"]." ".$hostel_room_type['roomType']." <br>";
      if($breakfast_included == 1){
				$privateRoomsTable.= '<span class="free-breakfast">';
				$privateRoomsTable.= _('Breakfast Included');
				$privateRoomsTable.= '</span>';
				$privateRoomsTableSelect.= '<span class="free-breakfast">';
				$privateRoomsTableSelect.= _('Breakfast Included');
				$privateRoomsTableSelect.= '</span>';
			}
			$privateRoomsTable.= "</td>";
			$privateRoomsTableSelect.= "</td>";


		// Max Guests per room
		
		$privateRoomsTable.= '<td align="center" title="'._('Maximum number of guests in the room').'"><span class="nbpeople-table icon-nbpeople nbpeople-'.$hostel_room_type["bedsIncrement"].'">'.$hostel_room_type["bedsIncrement"].' x</span></td>';
		$privateRoomsTableSelect.= '<td align="center" title="'._('Maximum number of guests in the room').'"><span class="nbpeople-table icon-nbpeople nbpeople-'.$hostel_room_type["bedsIncrement"].'">'.$hostel_room_type["bedsIncrement"].' x</span></td>';



      $date = clone $dateStart;
      $subtotal = 0;
      $num_nights_available_of_room = 0;

      for($i=0;$i<$numNights;$i++)
      {
          $date_available = false;
          $date_msg = '<span class="na-book price" title="'._('No private room available').'">0</span>';

          foreach($hostel_room_type['AvailableDates']['AvailableDate'] as $date_ok)
          {
            if($date_ok['date'] == $date->format("Y-m-d"))
            {

							$num_nights_available_of_room++;

            	$price_array = explode(' ',$date_ok['price']);
							if($price_array[0]=='From'){
								$price = $price_array[1];
							}else{
								$price = $price_array[0];
							}
							$subtotal = $subtotal+($price*(int)$hostel_room_type['bedsIncrement']);
							//if ($numNights > 10){
							//	$date_msg = currency_symbol($date_ok['currency']).' <span class="price">'.number_format(round((float)$price),0)*(int)$hostel_room_type['bedsIncrement'].'</span>';
							//	break;
							//}else{
							$date_msg = currency_symbol($date_ok['currency']).' <span class="price">'.number_format($price*(int)$hostel_room_type['bedsIncrement'],2,'.','').'</span>';
							//	 break;
							//}

							// Determined the minimum price

							if($min_price_private == 0){
								$min_price_private = $date_ok['price'];
								//$min_price_private_room = number_format($subtotal,2,'.','');
							}elseif($date_ok['price'] < $min_price_private){
								$min_price_private = $date_ok['price'];
								//$min_price_private_room = number_format($subtotal,2,'.','');
							}
							$currency_formin = currency_symbol($date_ok['currency']);

            }
          }

          $privateRoomsTable.= '<td align="center" title="'._('Price per room (not per person)').'">'.$date_msg.'</td>';

          $date->modify("+1 day");


      }
		$display_currency = currency_symbol($date_ok['currency']);
		$privateRoomsTableSelect.= '<td id="pnbguest_'.$nbRoomType.'" class="pnbguest">'._('Rooms').': <strong></strong></td>';
		$privateRoomsTableSelect.= '<td align="center" id="psubtotal_'.$nbRoomType.'" class="psubtotal"><span class="calc_init" id="psubtotal_init_'.$nbRoomType.'">'.number_format($subtotal,2,'.','').'</span>'.$display_currency.'  <span class="calc_sum" id="psubtotal_calc_'.$nbRoomType.'"></span></td>';


      foreach($hostel_room_type['AvailableDates']['AvailableDate'] as $avail_date)
      {
        if((int)$avail_date['availableBeds'] < $availableBeds)
        {
          $availableBeds = (int)$avail_date['availableBeds'];
        }

        if((int)$avail_date['availableRooms'] < $availableRooms)
        {
          $availableRooms = (int)$avail_date['availableRooms'];
        }
      }

      //TODO javascript to prevent more than maxPax booking by adding select box value

      $privateRoomsTable.= '<td align="center">';


      //If number of night avaible of the room is higher or equal to the property min night condition dispay the room selection menu
      if($num_nights_available_of_room >= $booking_info->minNights)
      {
        $privateRoomsTable.= "<input type=\"hidden\" name=\"book-roomPreferences[]\" value=\"".$hostel_room_type['roomTypeCode']."\" />";

        $privateRoomsTable.= "<input type=\"hidden\" name=\"book-roomType[]\" value=\"".$hostel_room_type['roomType']."\" />";
        $privateRoomsTable.= "<input type=\"hidden\" name=\"book-roomDesc[]\" value=\"".$hostel_room_type['roomTypeDescription']."\" />";
        if(!empty($hostel_room_type['roomTypeDescriptionTranslated']))
        {
          $privateRoomsTable.= "<input type=\"hidden\" name=\"book-roomDescTrans[]\" value=\"".$hostel_room_type['roomTypeDescriptionTranslated']."\" />";
        }
        else
        {
          $privateRoomsTable.= "<input type=\"hidden\" name=\"book-roomDescTrans[]\" value=\"\" />";
        }


        $privateRoomsAvailable++;
        $privateRoomsTable.= "<select id=\"privatesel_".$nbRoomType."\" class=\"privatesel\" name=\"book-nbPersons[]\">";

        $privateRoomsTable.= "<option value=\"0\" >0</option>\n";
        for($p=1;$p<=$availableRooms;$p++)
        {
          if($p*$hostel_room_type['bedsIncrement'] <= $maxPersons)
          {
            $privateRoomsTable.= "<option value=\"".$p*$hostel_room_type['bedsIncrement']."\" >$p</option>\n";
          }
        }
        $privateRoomsTable.= "</select>";
      }
//      $privateRoomsTable.= "N/A";
      $privateRoomsTable.= "</td>";

      $privateRoomsTable.= "</tr>\n";
	  $privateRoomsTableSelect.= "</tr>\n";
    }

  }

	$colspan = $numNights+2;
  $privateRoomCount = $nbRoomType;


  if( ($sharedRoomCount === 0) && ($privateRoomCount === 0 ))
  {
    ?>
    <div class="dispo-error">
    <p>
      <?php
      echo _("No Beds Found");
      ?>
    </p>
    <p>
      <?php
      echo _("No Beds could be found for your search criteria. Please change your dates and try again.");
      ?>
    </p>
    </div>
    <?php
  }
  else
  {
    ?>

    <form class="group" method="post" action="<?php echo secure_site_url($this->Db_links->get_link("booking")); ?>">

    <table border="0" cellpadding="0" cellspacing="0">
    <tbody>

  		<tr>
        <th class="title">
					<div class="room-type">
						<a class="show-room-info" href="#"><?php echo _('Chambres partagées - Dortoirs'); ?></a>
						<div class="room-type-info">
							<h5><?php echo _('Chambres partagées - Dortoirs'); ?></h5>
							<p><?php echo _('Price per person (Dorm shared with others).'); ?> <?php echo _('You must share the room (unless you purchase all the beds in the dorm).'); ?></p>
							<span class="room-info-arrow"></span>

						</div>
					</div>
				</th>

        <?php
        $date = clone $dateStart;

				// Max Guests per room, column title
				echo "<th>&nbsp;</th>";

        for($i=0;$i<$numNights;$i++)
        {
          echo "<th>";
          echo my_mb_ucfirst(mb_substr(strftime("%A",$date->format('U')),0,3, 'UTF-8'));
          echo strftime("<br />%e",$date->format('U'));
          $date->modify("+1 day");
          echo "</th>";
        }
        ?>
        <th class="last"><?php echo _('Personnes'); ?></th>
      </tr>

		 <?php
			if ($sharedRoomCount > 0)
			{
			  /*
			  ?>
				<tr>
				<td class="first min_price" colspan="<?php echo $numNights+3;?>">
				<span><?php printf( gettext('First price per person from: %s'),$currency_formin.$min_price_shared);?></span>
				</td>
			  </tr>
				<?php
				*/
			  echo $sharedRoomsTable;
			}else
			{
				?>
				<tr>
					<td class="first" colspan="<?php echo $numNights+3;?>">
					<?php echo _("No dorms available");?>
					</td>
				</tr>
				<?php
			}
			?>
      <tr>
        <th class="title">
          <div class="room-type">
						<a class="show-room-info" href="#"><?php echo _('Chambres privées'); ?></a>
						<div class="room-type-info">
							<h5><?php echo _('Chambres privées'); ?></h5>
							<p><?php echo _('Price per room (not per person).'); ?> <?php echo _('You must pay for the whole private room, even if you do not need all the beds. The room cannot be shared.'); ?></p>
							<span class="room-info-arrow"></span>

						</div>
					</div>
				</th>

        <?php
        $date = clone $dateStart;

				// Max Guests per room, column title
				echo "<th>&nbsp;</th>";

        for($i=0;$i<$numNights;$i++)
        {
					echo "<th>";
					echo my_mb_ucfirst(mb_substr(strftime("%A",$date->format('U')),0,3, 'UTF-8'));
					echo strftime("<br />%e",$date->format('U'));
					$date->modify("+1 day");
					echo "</th>";
        }
        ?>
        <th class="last"><?php echo _('Rooms'); ?></th>
      </tr>
      <?php
			if ($privateRoomCount > 0)
			{
			  /*
			  ?>
				<tr>
  				<td class="first min_price" colspan="<?php echo $numNights+3;?>">
  				<span><?php printf( gettext('First price per person from: %s'),$currency_formin.$min_price_private);?></span>
  				</td>
			  </tr>
				<?php
				*/
				echo $privateRoomsTable;
			}else
			{
				?>
				<tr>
					<td class="first" colspan="<?php echo $numNights+3;?>">
					<?php echo _("No private room available");?>
					</td>
				</tr>
				<?php
			}
			?>
    </tbody>
    </table>


	<table id="selection" border="0" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <th class="title">
				<div class="room-type">
					<a class="show-room-info" href="#"><?php echo _('Your Selection'); ?></a>
					<div class="room-type-info">

						<h5><?php echo _("Notes Importantes");?></h5>
						<p><?php printf( gettext("You only pay the deposit (10%% of total amount) to confirm and secure your reservation now. The remaining amount (90%%) is payable upon arrival. You will find the hotel's contact information (email, address, telephone number…) in your confirmation email after you have made your reservation."),$this->config->item('site_name'));?></p>
						<span class="room-info-arrow"></span>

					</div>
				</div>
				</th>
				<th>&nbsp;</th>
				<th colspan="2" class="last">&nbsp;

				</th>
		</tr>
		<?php
    if ($sharedRoomCount > 0)
  	{
      echo $sharedRoomsTableSelect;
  	}

    if ($privateRoomCount > 0)
  	{
  		echo $privateRoomsTableSelect;
  	}
    ?>
		<tr>
        <td class="first" align="right" colspan="3"><strong><?php echo _('Total'); ?></strong></td>
				<td align="center"><?php echo $display_currency;?> <strong id="bigTotal">0.00</strong></td>
		</tr>
		<tr>
        <td class="first" align="right" colspan="3"><span class="best_price left"><?php echo _('You got the best price')?></span><strong class="right deposit_bottom"><?php echo _('10% Arrhes / Dépôt sera facturé en');?></strong></td>
				<td align="center"><?php echo $display_currency;?> <strong id="depositTotal">0.00</strong></td>
		</tr>
    </tbody>
    </table>

    <input type="hidden" name="book-propertyName" value="<?php echo $propertyName; ?>" />
    <input type="hidden" name="book-propertyNumber" value="<?php echo $propertyNumber; ?>" />
    <input type="hidden" name="book-dateStart" value="<?php echo $dateStart->format('Y-m-d'); ?>" />
    <input type="hidden" name="book-numNights" value="<?php echo $numNights; ?>" />
    <input type="hidden" name="book-currency" value="<?php echo $currency; ?>" />

    <?php
    if(($privateRoomsAvailable + $sharedRoomsAvailable) > 0)
    {
      ?>
      <div class="bottom-table group" id="book-now">
        <?php /*?><img class="ccard" src="<?php echo site_url();?>images/ccard.gif" alt="<?php echo _("carte de crédit");?>" /><?php */?>

        <input type="submit" onfocus="this.blur()" name="booking-form" id="booking-form-submit" class="button-green box_round hoverit" value="<?php echo _("Réserver Maintenant"); ?>" />
        <img src="<?php echo site_url();?>images/padlock.png" alt="<?php echo _("sécurisé");?>" />
        <span><?php echo _('Best price. We guarantee it.')?></span>
				<span><?php echo _('It only takes 2 minutes')?></span>
      </div>
			<script>
			$(function(){
					$("#dispo-form").hide();
					$("#change-dates").show();
				});
			</script>
      <?php
    }
    else
    {
      ?>
      <p class="orange-error"><?php echo _('No rooms are available for all the nights you selected.');?></p>
      <?php
    }
    ?>

    </form>

	<p class="red-error" id="formerror"><?php echo _('Please enter at least one choice in the above table to book a room.');?></p>
    <?php
  }?>

<?php
}
elseif($api_error_msg==false)
{
  //No response from server
  echo _('Serveur inaccessible en ce moment.');
}
else
{
  //error message = $api_error_msg->UserMessage->message
  //error details (en anglais) = $api_error_msg->UserMessage->detail
  ?>
  <div class="dispo-error group">
		<img class="arrow-error" src="<?php echo site_url();?>images/V2/arrow-error.png" alt="" />
		<div<?php if ($api_error_msg->message == 'No Beds Found'){echo ' class="half"';}?>>

			<h3>
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
			</h3>

			<p><strong><?php echo _("Détails:");?> </strong>
			<?php
				if(!empty($api_error_msg->detailTranslated))
				{
					//print_r($api_error_msg);
					echo $api_error_msg->detailTranslated;
				}
				else
				{
					echo $api_error_msg->detail;
				}
			?>
			</p>
		</div>
		<?php if ($api_error_msg->message == 'No Beds Found'){?>
		<?php $dateurl = $dateStart->format('Y-m-d');?>
		<a class="alternative button-green hoverit box_round" href="<?php echo site_url();?><?php echo $country_selected;?>/<?php echo $city_selected;?>/<?php echo $dateurl;?>/<?php echo $numNights; ?>">
		<?php printf( gettext('Search for more properties in %s'),$city_selected);?></a>
		<?php }?>


  </div>

  <?php
  //echo $api[1][0]->UserMessage->message.'<br>';
  //echo $api[1][0]->UserMessage->detail.'<br>';
  //erreur de l'api traduite
  //echo $this->lang->line(get_error_key($api[1]->UserMessage->message));
//  echo "<textarea cols=\"80\" rows=\"30\">";
//  print_r($api[1][0]);
//  echo "</textarea>";
}

?>

<script type="text/javascript" src="<?php echo base_url();?>js/calcprice.js"></script>
<script type="text/javascript">

    $(function(){$("#booking-table").show();});
		$("#booking-table form").submit(function() {
			var noerror = false;
			$("#formerror").hide();
      $("#booking-table select").each(function () {
				if ($(this).val() != 0){
					noerror = true;
				}
			});

			if (noerror == true){
				noerror = true;
				return true;
			}else{
				$("#formerror").show();
				return false;
			}

    });

		$('a.show-room-info').click(function() {
			return false;
		});
		$('a.show-room-info').mouseover(function() {
			$(this).next().show();
		});
		$('a.show-room-info').mouseleave(function() {
			$(this).next().hide();
		});

		$('a#change-dates').click(function() {
			$("#dispo-form").toggle();
			$("#booking-table").toggle();
			return false;
		});


</script>
