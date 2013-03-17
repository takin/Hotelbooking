<h2><?php echo _('Disponibilités'); ?> <span>(<?php echo $currency; ?>)</span></h2>
<?php
$date = clone $dateStart;
$datetop = date_conv($dateStart->format('Y-m-d'), $this->wordpress->get_option('aj_date_format'));
?>
<div class="top-table">
<p><?php echo _('Arrivée');?>: <b><?php echo $datetop;?></b> &nbsp;&nbsp; <?php echo _('Nombre de Nuits');?>: <b><?php echo $numNights; ?></b><a id="change-dates" href="javascript:void(0);">[<?php echo _('Change Dates'); ?>]</a></p>

</div>
<?php
  $nbRoomType = 0;
  $sharedRoomsTable = "";
	$min_price_shared = 0;
	$sharedRoomsTableSelect = "";
  //Show shared rooms first
  foreach ($booking_rooms as $hostel_room)
  {
    $availableBeds = $hostel_room["BEDS"];
    //Check for dorms only
    if($hostel_room["BLOCKBEDS"]==0)
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
      if(!empty($hostel_room['NAME_TRANSLATED']))
      {
        $sharedRoomsTable.= '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$hostel_room['NAME'].'">'.$hostel_room['NAME_TRANSLATED'].'</span>';
				$sharedRoomsTableSelect.= '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$hostel_room['NAME'].'">'.$hostel_room['NAME_TRANSLATED'].'</span>';

      }
      else
      {
        $sharedRoomsTable.= $hostel_room['NAME'];
				$sharedRoomsTableSelect.= $hostel_room['NAME'];
      }

			if($breakfast_included == 1){
				$sharedRoomsTable.= '<span class="free-breakfast">';
				$sharedRoomsTable.= _('Breakfast Included');
				$sharedRoomsTable.= '</span>';
				$sharedRoomsTableSelect.= '<span class="free-breakfast">';
				$sharedRoomsTableSelect.= _('Breakfast Included');
				$sharedRoomsTableSelect.= '</span>';
			}

      $sharedRoomsTable.= "</td>";
			$sharedRoomsTableSelect.= "</td>";

			$sharedRoomsTable.= '<td align="center" title="'._('Bed in a dorm. 1 person per bed maximum').'"><span class="nbpeople-table icon-nbpeople nbpeople-1">1</span></td>';
			$sharedRoomsTableSelect.= '<td align="center" title="'._('Bed in a dorm. 1 person per bed maximum').'"><span class="nbpeople-table icon-nbpeople nbpeople-1">1</span></td>';


      $date = clone $dateStart;
			$subtotal = 0;
      //Ensure array of nights is sorted by soonest date first
      if(isset($hostel_room['NIGHTS']))
      {
        ksort($hostel_room['NIGHTS']);
        foreach($hostel_room['NIGHTS'] as $dateint => $room_night)
        {

          $date_msg = "";
					$display_currency = currency_symbol($room_night["CUSTOMER"]["CURRENCY"]);
          $subtotal = $subtotal+$room_night["CUSTOMER"]["MINPRICE"];

					//if ($numNights < 8){
						$date_msg.= currency_symbol($room_night["CUSTOMER"]["CURRENCY"])." ";
						$date_msg.= number_format(round((float)$room_night["CUSTOMER"]["MINPRICE"],2),2);

					//}else{
					//	$date_msg.= number_format(round((float)$room_night["CUSTOMER"]["MINPRICE"],2),0);
					//}

					$sharedRoomsTable.= '<td align="center" title="'._('Price per bed (not per room)').'">'.$date_msg.'</td>';
          $date->modify("+1 day");
					if($min_price_shared == 0){
						$min_price_shared = $room_night["CUSTOMER"]["MINPRICE"];
					}elseif($min_price_shared > $room_night["CUSTOMER"]["MINPRICE"]){
						$min_price_shared = $room_night["CUSTOMER"]["MINPRICE"];
					}
					$currency_formin = currency_symbol($room_night["CUSTOMER"]["CURRENCY"]);
        }
      }
      else
      {
        for($i=0;$i<$numNights;$i++)
        {

          $date_msg = "";
          $subtotal = $subtotal+$hostel_room['PRICES']["CUSTOMER"]["MINPRICE"];
          $display_currency = currency_symbol($hostel_room['PRICES']["CUSTOMER"]["CURRENCY"]);

					if ($numNights < 8){
						$date_msg.= currency_symbol($hostel_room['PRICES']["CUSTOMER"]["CURRENCY"])." ";
						$date_msg.= number_format(round((float)$hostel_room['PRICES']["CUSTOMER"]["MINPRICE"],2),2);

					}else{
						$date_msg.= number_format(round((float)$hostel_room['PRICES']["CUSTOMER"]["MINPRICE"],2),0);
					}
					$sharedRoomsTable.= '<td align="center" title="'._('Price per bed (not per room)').'">'.$date_msg.'</td>';
					$date->modify("+1 day");
        }
      }

      $sharedRoomsTableSelect.= '<td id="snbguest_'.$nbRoomType.'" class="snbguest">'._('Personnes').': <strong></strong></td>';
	  	$sharedRoomsTableSelect.= '<td align="center" id="ssubtotal_'.$nbRoomType.'" class="ssubtotal"><span class="calc_init" id="ssubtotal_init_'.$nbRoomType.'">'.number_format($subtotal,2,'.','').'</span>'.$display_currency.'  <span class="calc_sum" id="ssubtotal_calc_'.$nbRoomType.'"></span></td>';
      //TODO javascript to prevent more than available beds booking by adding select box value

      $sharedRoomsTable.= "<td align=\"center\">";
      $sharedRoomsTable.= "<input type=\"hidden\" name=\"book-roomPreferences[]\" value=\"".$hostel_room['ID']."\" />";
      $sharedRoomsTable.= "<select id=\"sharedsel_".$nbRoomType."\" class=\"sharedsel\" name=\"book-nbPersons[]\">";

      $sharedRoomsTable.= "<option value=\"0\" >0</option>\n";
      for($p=1;$p<=$availableBeds;$p++)
      {
        $sharedRoomsTable.= "<option value=\"$p\" >$p</option>\n";
      }
      $sharedRoomsTable.= "</select>";
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
  $privateRoomsTable = "";
	$min_price_private = 0;
	$privateRoomsTableSelect = "";

  foreach ($booking_rooms as $hostel_room)
  {
    $availableBeds  = $hostel_room["BEDS"];

    //Check for dorms only
    if($hostel_room["BLOCKBEDS"] > 0)
    {
      $availableRooms = $hostel_room["BEDS"] / $hostel_room["BLOCKBEDS"];

      $date->modify("-$numNights day");
      $nbRoomType++;
      if($nbRoomType % 2 != 0)
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
      if(!empty($hostel_room['NAME_TRANSLATED']))
      {
        $privateRoomsTable.= '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$hostel_room['NAME'].'">'.$hostel_room['NAME_TRANSLATED'].'</span>';
				$privateRoomsTableSelect.= '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$hostel_room['NAME'].'">'.$hostel_room['NAME_TRANSLATED'].'</span>';

      }
      else
      {
        $privateRoomsTable.= $hostel_room['NAME'];
				$privateRoomsTableSelect.= $hostel_room['NAME'];
      }

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
			$privateRoomsTable.= '<td align="center" title="'._('Maximum number of guests in the room').'"><span class="nbpeople-table icon-nbpeople nbpeople-'.$hostel_room["BLOCKBEDS"].'">'.$hostel_room["BLOCKBEDS"].' x</span></td>';
			$privateRoomsTableSelect.= '<td align="center" title="'._('Maximum number of guests in the room').'"><span class="nbpeople-table icon-nbpeople nbpeople-'.$hostel_room["BLOCKBEDS"].'">'.$hostel_room["BLOCKBEDS"].' x</span></td>';

      $date = clone $dateStart;
      $subtotal = 0;
    //Ensure array of nights is sorted by soonest date first
      if(isset($hostel_room['NIGHTS']))
      {
        ksort($hostel_room['NIGHTS']);
        foreach($hostel_room['NIGHTS'] as $dateint => $room_night)
        {
//          $tt = new Datetime($dateint);
//          echo $tt->format("Y-m-d") . " - ";
//          echo $dateint. " VS ".time() ." - ". date("d-M-Y",mktime(0,0,0,1,0+$dateint,1900)). "<br>";
					$display_currency = currency_symbol($room_night["CUSTOMER"]["CURRENCY"]);
					$subtotal = $subtotal+$room_night["CUSTOMER"]["MINPRICE"]*$hostel_room["BLOCKBEDS"];
					$date_msg = "";

          if ($numNights < 8){
						$date_msg.= currency_symbol($room_night["CUSTOMER"]["CURRENCY"])." ";
						$date_msg.= number_format(round((float)$room_night["CUSTOMER"]["MINPRICE"]*$hostel_room["BLOCKBEDS"],2),2);

					}else{

						$date_msg.= number_format(round((float)$room_night["CUSTOMER"]["MINPRICE"]*$hostel_room["BLOCKBEDS"],2),0);
					}

					$privateRoomsTable.= '<td align="center" title="'._('Price per room (not per person)').'">'.$date_msg.'</td>';
					$date->modify("+1 day");

					if($min_price_private == 0){
						$min_price_private = $room_night["CUSTOMER"]["MINPRICE"];
					}elseif($min_price_private > $room_night["CUSTOMER"]["MINPRICE"]){
						$min_price_private = $room_night["CUSTOMER"]["MINPRICE"];
					}
					$currency_formin = currency_symbol($room_night["CUSTOMER"]["CURRENCY"]);
        }
      }
      else
      {
        for($i=0;$i<$numNights;$i++)
        {

          $date_msg = "";
          $subtotal = $subtotal+$hostel_room['PRICES']["CUSTOMER"]["MINPRICE"]*$hostel_room["BLOCKBEDS"];
          $display_currency = currency_symbol($hostel_room['PRICES']["CUSTOMER"]["CURRENCY"]);

					//if ($numNights < 8){
						$date_msg.= currency_symbol($hostel_room['PRICES']["CUSTOMER"]["CURRENCY"])." ";
						$date_msg.= number_format(round((float)$hostel_room['PRICES']["CUSTOMER"]["MINPRICE"]*$hostel_room["BLOCKBEDS"],2),2);

					//}else{

					//	$date_msg.= number_format(round((float)$hostel_room['PRICES']["CUSTOMER"]["MINPRICE"]*$hostel_room["BLOCKBEDS"],2),0);
					//}

					$privateRoomsTable.= '<td align="center" title="'._('Price per room (not per person)').'">'.$date_msg.'</td>';
          $date->modify("+1 day");
        }
      }

      $privateRoomsTableSelect.= '<td id="pnbguest_'.$nbRoomType.'" class="pnbguest">'._('Rooms').': <strong></strong></td>';
			$privateRoomsTableSelect.= '<td align="center" id="psubtotal_'.$nbRoomType.'" class="psubtotal"><span class="calc_init" id="psubtotal_init_'.$nbRoomType.'">'.number_format($subtotal,2,'.','').'</span>'.$display_currency.'  <span class="calc_sum" id="psubtotal_calc_'.$nbRoomType.'"></span></td>';

      //TODO javascript to prevent more than available rooms booking by adding select box value

      $privateRoomsTable.= "<td align=\"center\">";
      $privateRoomsTable.= "<input type=\"hidden\" name=\"book-roomPreferences[]\" value=\"".$hostel_room['ID']."\" />";
      $privateRoomsTable.= "<select id=\"privatesel_".$nbRoomType."\" class=\"privatesel\" name=\"book-nbPersons[]\">";

      $privateRoomsTable.= "<option value=\"0\" >0</option>\n";
      for($p=1;$p<=$availableRooms;$p++)
      {
          $privateRoomsTable.= "<option value=\"".$p*$hostel_room["BLOCKBEDS"]."\" >$p</option>\n";
      }
      $privateRoomsTable.= "</select>";
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
				<th>&nbsp;</th>
        <?php
        $date = clone $dateStart;

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
			}
			else
			{
  	  ?>
  	  <tr>
    	  <td class="first" colspan="<?php echo $numNights+2;?>">
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
			<th>&nbsp;</th>
      <?php
      $date = clone $dateStart;

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
			/*?>
				<tr>
				<td class="first min_price" colspan="<?php echo $numNights+3;?>">
				<span><?php printf( gettext('First price per person from: %s'),$currency_formin.$min_price_private);?></span>
				</td>
			</tr>
				<?php
        */
				echo $privateRoomsTable;
  	}
  	else
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
					<th class="title last">
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
				<th colspan="3" class="last">&nbsp;

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
					<td class="first" align="right" colspan="3">
					<span class="best_price left"><?php echo _('You got the best price')?></span><strong class="right deposit_bottom"><?php echo _('10% Arrhes / Dépôt sera facturé en');?></strong></td>
					<td align="center"><?php echo $display_currency;?> <strong id="depositTotal">0.00</strong></td>
			</tr>
			</tbody>
    </table>
    <input type="hidden" name="book-propertyName" value="<?php echo $propertyName; ?>" />
    <input type="hidden" name="book-propertyNumber" value="<?php echo $propertyNumber; ?>" />
    <input type="hidden" name="book-dateStart" value="<?php echo $dateStart->format('Y-m-d'); ?>" />
    <input type="hidden" name="book-numNights" value="<?php echo $numNights; ?>" />
    <input type="hidden" name="book-currency" value="<?php echo $currency; ?>" />
    <input type="hidden" name="book-property-cards" value="<?php echo $property_cards; ?>" />
    <div class="bottom-table group">
      <?php /*?><img class="ccard" src="<?php echo site_url();?>images/ccard.gif" alt="<?php echo _("carte de crédit");?>" /><?php */?>

	<?php if (empty($print)) { ?>
      <input type="submit" onfocus="this.blur()" name="booking-form" id="booking-form-submit" class="button-green box_round hoverit" value="<?php echo _("Réserver Maintenant"); ?>" />
	<?php } else { ?>
            <strong id="booking-form-submit"><?php echo _('PLEASE NOTE THIS IS NOT A CONFIRMED BOOKING'); ?></strong>
       <?php  }?>
      <img src="<?php echo site_url();?>images/padlock.png" alt="<?php echo _("sécurisé");?>" />
			<span><?php echo _('Best price. We guarantee it.')?></span>
			<span><?php echo _('It only takes 2 minutes')?></span>
    </div>

    </form>
		<script>
		$(function(){
				$("#dispo-form").hide();
				$("#change-dates").show();
			});
		</script>
		<p class="red-error" id="formerror"><?php echo _('Please enter at least one choice in the above table to book a room.');?></p>
    <?php
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
			$("#dispo-form").hide();
			$("#dispo-form").show(100);
			$("#booking-table").hide();
			return false;
		});

</script>
