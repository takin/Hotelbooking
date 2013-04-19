<?php
$maximum_guests = 1000000;
echo form_hidden('deposit_percent', 0.1);
echo form_hidden('total_guests', _('Total number of guests selected'));
echo form_hidden('maximum_guests', $maximum_guests);
echo form_hidden('maximum_guests_message', sprintf(gettext('You can only book up to %d guests at a time. You can book more than %d guests by going to our group section on top of this page.'), (int) $maximum_guests, (int) $maximum_guests));
?>

<h2> <?php echo _('Disponibilités'); ?> <span>(<?php echo $currency; ?>) </span> </h2>

<?php
$date = clone $dateStart;
$datetop = date_conv($dateStart->format('Y-m-d'), $this->wordpress->get_option('aj_date_format'));
?>

<div class="top-table">
    <p> <?php echo _('Arrivée'); ?> : <b><?php echo $datetop; ?> </b> &nbsp;&nbsp; <?php echo _('Nombre de Nuits'); ?> : <b><?php echo $numNights; ?> </b>
        <a id="change-dates" href="javascript:void(0);">[<?php echo _('Change Dates'); ?>] </a> </p>
</div>

<?php
$date = clone $dateStart;
$min_price_shared = 0;

foreach ($booking_rooms as $hostel_room) {
    if ($hostel_room["BLOCKBEDS"] == 0) {

        if (isset($hostel_room['NIGHTS'])) {
            ksort($hostel_room['NIGHTS']);
            foreach ($hostel_room['NIGHTS'] as $dateint => $room_night) {

                if ($min_price_shared == 0) {
                    $min_price_shared = $room_night["CUSTOMER"]["MINPRICE"];
                } elseif ($min_price_shared > $room_night["CUSTOMER"]["MINPRICE"]) {
                    $min_price_shared = $room_night["CUSTOMER"]["MINPRICE"];
                }

                $date->modify("+1 day");
            }
        } else {
            for ($i = 0; $i < $numNights; $i++) {

                $date->modify("+1 day");
            }
        }
    }
}

$date = clone $dateStart;
$min_price_private = 0;

foreach ($booking_rooms as $hostel_room) {
    if ($hostel_room["BLOCKBEDS"] > 0) {

        if (isset($hostel_room['NIGHTS'])) {
            ksort($hostel_room['NIGHTS']);
            foreach ($hostel_room['NIGHTS'] as $dateint => $room_night) {

                if ($min_price_private == 0) {
                    $min_price_private = $room_night["CUSTOMER"]["MINPRICE"];
                } elseif ($min_price_private > $room_night["CUSTOMER"]["MINPRICE"]) {
                    $min_price_private = $room_night["CUSTOMER"]["MINPRICE"];
                }

                $date->modify("+1 day");
            }
        } else {
            for ($i = 0; $i < $numNights; $i++) {
                
            }
        }
    }
}
?>

<?php
$nbRoomType = 0;
$sharedRoomsTable = "";
$sharedRoomsTableSelect = "";
$sharedRoomsCluetipTable = "";
$sharedreservationTable = "";
$privatereservationTable = "";
$privateRoomsCluetipTable = "";
$privateRoomsTableSelect = "";
$ajaxTableID = 0;

//Show shared rooms first
foreach ($booking_rooms as $hostel_room) {

    $availableBeds = $hostel_room["BEDS"];
    //Check for dorms only
    if ($hostel_room["BLOCKBEDS"] == 0) {

        $date->modify("-$numNights day");
        $nbRoomType++;

        if ($nbRoomType % 2 != 0) {
            $sharedRoomsTable.= "<tr class=\"dorm_row\">";
            $sharedRoomsTableSelect.= "<tr class=\"roomnb\" id=\"sroomnb_" . $nbRoomType . "\">";
        } else {
            $sharedRoomsTable.= "<tr class=\"odd dorm_row\">";
            $sharedRoomsTableSelect.= "<tr id=\"sroomnb_" . $nbRoomType . "\" class=\"roomnb\">";
        }

        $sharedRoomsCluetipTable .= '<table class="ajaxTable" id="sajaxTable' . $ajaxTableID . '"><tr>';

        $_date = clone $dateStart;

        for ($i = 0; $i < $numNights; $i++) {

            if ($i == ($numNights - 1)) {
                $sharedRoomsCluetipTable .= "<th class='last'>";
            } else {
                $sharedRoomsCluetipTable .= "<th>";
            }

            $sharedRoomsCluetipTable .= my_mb_ucfirst(mb_substr(strftime("%A", $_date->format('U')), 0, 3, 'UTF-8'));
            $sharedRoomsCluetipTable .= strftime("<br /> %d", $_date->format('U'));
            $_date->modify("+1 day");
            $sharedRoomsCluetipTable .= "</th>";
        }

        $sharedRoomsCluetipTable .= "</tr><tr>";

        $date = clone $dateStart;
        $subtotal = 0;
        $display_currency = "";
        $nights_count = 0;
        $lowest_night = '';
        $lowest_style = '';

        $dormTitle = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights;

        //Ensure array of nights is sorted by soonest date first
        if (isset($hostel_room['NIGHTS'])) {
            ksort($hostel_room['NIGHTS']);
            foreach ($hostel_room['NIGHTS'] as $dateint => $room_night) {

                $date_msg = "";
                $display_currency = currency_symbol($room_night["CUSTOMER"]["CURRENCY"]);
                $subtotal = $subtotal + $room_night["CUSTOMER"]["MINPRICE"];

                $date_msg.= currency_symbol($room_night["CUSTOMER"]["CURRENCY"]) . " ";
                $date_msg.= number_format(round((float) $room_night["CUSTOMER"]["MINPRICE"], 2), 2);

                $currency_formin = currency_symbol($room_night["CUSTOMER"]["CURRENCY"]);

                $sharedreservationTable.= '<tr class="sreservation sreservation_' . $nbRoomType . '">
                            <td class="first">' . date_conv($date->format('Y-m-d'), $this->wordpress->get_option('aj_date_format')) . '</td>
                            <td><p>' . $hostel_room['NAME_TRANSLATED'] . ' </p> (' . $hostel_room['NAME'] . ')</td>
                            <td>' . currency_symbol($room_night["CUSTOMER"]["CURRENCY"]) . ' <span>' . number_format(round((float) $room_night["CUSTOMER"]["MINPRICE"], 2), 2) . '</span></td>
                            <td><span></span></td>
                            <td class="" style="text-align : right;">' . currency_symbol($room_night["CUSTOMER"]["CURRENCY"]) . ' <span></span></td>
                            </tr>';

                $nights_count++;

                if ($min_price_shared == $room_night["CUSTOMER"]["MINPRICE"]) {

                    $lowest_night = _('Lowest night:') . ' <span style="color: #6DA903;">' . $display_currency . ' ' . number_format($min_price_shared, 2, '.', '').'</span>';
                    $lowest_style = 'style="color: #6DA903;"';

                    $dormTitle = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;&nbsp;  ' . _('Lowest night:') . ' ' . $display_currency . ' ' . $min_price_shared;
                } else {
                    $lowest_style = '';
                }

                if ($i == 0) {
                    $sharedRoomsCluetipTable.= '<td align="center" class="first" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                } else {
                    $sharedRoomsCluetipTable.= '<td align="center" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                }

                $date->modify("+1 day");
            }
        } else {
            for ($i = 0; $i < $numNights; $i++) {

                $date_msg = "";
                $subtotal = $subtotal + $hostel_room['PRICES']["CUSTOMER"]["MINPRICE"];
                $display_currency = currency_symbol($hostel_room['PRICES']["CUSTOMER"]["CURRENCY"]);

                if ($numNights < 8) {
                    $date_msg.= currency_symbol($hostel_room['PRICES']["CUSTOMER"]["CURRENCY"]) . " ";
                    $date_msg.= number_format(round((float) $hostel_room['PRICES']["CUSTOMER"]["MINPRICE"], 2), 2);
                } else {
                    $date_msg.= number_format(round((float) $hostel_room['PRICES']["CUSTOMER"]["MINPRICE"], 2), 0);
                }

                $sharedreservationTable.= '<tr class="sreservation sreservation_' . $nbRoomType . '">
                            <td class="first">' . date_conv($date->format('Y-m-d'), $this->wordpress->get_option('aj_date_format')) . '</td>
                            <td><p>' . $hostel_room['NAME_TRANSLATED'] . ' </p> (' . $hostel_room['NAME'] . ')</td>
                            <td>' . currency_symbol($hostel_room['PRICES']["CUSTOMER"]["CURRENCY"]) . ' <span>' . number_format(round((float) $hostel_room['PRICES']["CUSTOMER"]["MINPRICE"], 2), 2) . '</span></td>
                            <td><span></span></td>
                            <td class="" style="text-align : right;">' . currency_symbol($hostel_room['PRICES']["CUSTOMER"]["CURRENCY"]) . ' <span></span></td>
                            </tr>';

                if ($i == 0) {
                    $sharedRoomsCluetipTable.= '<td align="center" class="first" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                } else {
                    $sharedRoomsCluetipTable.= '<td align="center" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                }

                $nights_count++;
                $date->modify("+1 day");
            }
        }

        $sharedRoomsTable.= '<td class="first" style="border-right: none;">';
        $sharedRoomsTableSelect.= '<td class="first" style="border-right: none;">';

        if (!empty($hostel_room['NAME_TRANSLATED'])) {
            $sharedRoomsTable.= '<span class="tooltip" title="' . _("VERSION ORIGINALE :") . ' ' . $hostel_room['NAME'] . '">' . $hostel_room['NAME_TRANSLATED'] . '</span>';
            $sharedRoomsTableSelect.= '<span class="tooltip" title="' . _("VERSION ORIGINALE :") . ' ' . $hostel_room['NAME'] . '">' . $hostel_room['NAME_TRANSLATED'] . '</span>';
        } else {
            $sharedRoomsTable.= $hostel_room['NAME'];
            $sharedRoomsTableSelect.= $hostel_room['NAME'];
        }

        if ($breakfast_included == 1) {
            $sharedRoomsTable.= '<span class="free-breakfast">';
            $sharedRoomsTable.= _('Breakfast Included');
            $sharedRoomsTable.= '</span>';
            $sharedRoomsTableSelect.= '<span class="free-breakfast">';
            $sharedRoomsTableSelect.= _('Breakfast Included');
            $sharedRoomsTableSelect.= '</span>';
        }

        $sharedRoomsTable.= "</td>";
        $sharedRoomsTableSelect.= "</td>";

        // Getting value from above to show prices
        // Max Guests per room
        $nb_guest_per_room = preg_replace("/[^0-9]/", '', $hostel_room['NAME']);

        if ($nb_guest_per_room == 1) {
            $sharedRoomsTable.= '<td align="center" width="50" title="' . sprintf(gettext('This type of dorm can accommodate up to 1 guest per dorm.')) . '"><span class="nbpeople-table icon-nbpeople">' . (($nb_guest_per_room > 1) ? ('<span>' . $nb_guest_per_room . '</span>') : ('<span>' . $nb_guest_per_room . '</span>')) . '</span></td>';
            $sharedRoomsTableSelect.= '<td align="center" width="50" title="' . sprintf(gettext('This type of dorm can accommodate up to 1 guest per dorm.')) . '"><span class="nbpeople-table icon-nbpeople">' . (($nb_guest_per_room > 1) ? ('<span>' . $nb_guest_per_room . '</span>') : ('<span>' . $nb_guest_per_room . '</span>')) . '</span></td>';
        } else {
            $sharedRoomsTable.= '<td align="center" width="50" title="' . sprintf(gettext('This type of dorm can accommodate up to %d guests per dorm.'), (int) $nb_guest_per_room) . '"><span class="nbpeople-table icon-nbpeople">' . (($nb_guest_per_room > 1) ? ('<span>' . $nb_guest_per_room . '</span>') : ('<span>' . $nb_guest_per_room . '</span>')) . '</span></td>';
            $sharedRoomsTableSelect.= '<td align="center" width="50" title="' . sprintf(gettext('This type of dorm can accommodate up to %d guests per dorm.'), (int) $nb_guest_per_room) . '"><span class="nbpeople-table icon-nbpeople">' . (($nb_guest_per_room > 1) ? ('<span>' . $nb_guest_per_room . '</span>') : ('<span>' . $nb_guest_per_room . '</span>')) . '</span></td>';
        }

        $dormText = '';

        if (($availableBeds % $nb_guest_per_room) == 0) {
            if (($availableBeds / $nb_guest_per_room) == 1) {
                $dormText .= sprintf(gettext('1 fully available dorm.'));
            } else {
                $dormText .= sprintf(gettext('%d fully available dorms.'), (int) ($availableBeds / $nb_guest_per_room));
            }
        } else {
            if (floor($availableBeds / $nb_guest_per_room) == 1) {
                $dormText .= sprintf(gettext('1 fully available dorm and 1 already partially occupied (beds already occupied: %d).'), (int) ( $nb_guest_per_room - ($availableBeds % $nb_guest_per_room)));
            } else if (floor($availableBeds / $nb_guest_per_room) < 1) {
                $dormText .= sprintf(gettext('This dorm is already partially occupied (beds already occupied: %d).'), (int) ( $nb_guest_per_room - ($availableBeds % $nb_guest_per_room)));
            } else {
                $dormText .= sprintf(gettext('%d dorms fully available and 1 dorm already partially occupied (beds already occupied: %d).'), floor($availableBeds / $nb_guest_per_room), (int) ( $nb_guest_per_room - ($availableBeds % $nb_guest_per_room)));
            }
        }

        if ($numNights != 1) {
            $sharedRoomsTable.= '<td align="center" title="">';
            $sharedRoomsTable.= '<a class="ajaxTable" href="#sajaxTable' . $ajaxTableID . '" rel="#sajaxTable' . $ajaxTableID . '" style="display : block;" title="' . $dormTitle . '">';
            $sharedRoomsTable.= '<span style="font-weight: bold;">' . $display_currency . ' ' . number_format(($subtotal / $nights_count), 2, '.', '') . '</span>';
            if ($lowest_night != '') {
                $lowest_title = sprintf(gettext('The lowest price per person per night in a dorm in this property: %s'), $display_currency . ' ' . $min_price_shared);
                $sharedRoomsTable.= '<span style="display: block; float: none;">' . $lowest_night . '</span>';
            }
            $sharedRoomsTable.= '</a>';
            $sharedRoomsTable.= '</td>';
        } else {
            $sharedRoomsTable.= '<td align="center" style="font-weight: bold;" title="' . _('Price') . '">';
            $sharedRoomsTable.= $display_currency . ' ' . number_format(($subtotal / $nights_count), 2, '.', '');
            $sharedRoomsTable.= '</td>';
        }

        $sharedRoomsTable.= '<td align="center" id="snbroom_' . $nbRoomType . '" roombeds="' . $nb_guest_per_room . '">
                <div title="' . _('Maximum number of guests per dorm') . '" style="font-weight : 600;">
                <span class="complete" complete="' . ($nb_guest_per_room * floor($availableBeds / $nb_guest_per_room)) . '" not_complete="' . $availableBeds . '">' . $availableBeds . '</span> 
                ' . ($availableBeds == 1 ? _('Guest') : _('Guests') ) . ' 
                </div>
                <a class="title" href="#" title="' . _('Availability') . '|' . $dormText . '">
                <span class="complete" style="font-size : 11px; margin-top : 5px;" complete="' . floor($availableBeds / $nb_guest_per_room) . '" not_complete="' . ceil($availableBeds / $nb_guest_per_room) . '">' . ceil($availableBeds / $nb_guest_per_room) . '</span> 
                ' . ( (ceil($availableBeds / $nb_guest_per_room) == 1) ? _('Dorm') : _('Dorms') ) . '
                </a>
                </td>';

        $sharedRoomsTableSelect.= '<td id="snbrooms_' . $nbRoomType . '" align="center" class="snbrooms"><a href="" class="title" title="' . _('Availability') . ' | ' . sprintf(gettext('Dorm Availability')) . '">' . _('Dorms you will occupy:') . ' <strong></strong></a></td>';
        $sharedRoomsTableSelect.= '<td id="snbguest_' . $nbRoomType . '" align="center" class="snbguest"><strong></strong> x <span  class="nbpeople-table icon-nbpeople nbpeople-1"></span></td>';
        $sharedRoomsTableSelect.= '<td align="center" id="ssubtotal_' . $nbRoomType . '" class="ssubtotal"><span class="calc_init" id="ssubtotal_init_' . $nbRoomType . '">' . number_format($subtotal, 2, '.', '') . '</span>' . $display_currency . '  <span class="calc_sum" id="ssubtotal_calc_' . $nbRoomType . '"></span></td>';

        //TODO javascript to prevent more than available beds booking by adding select box value

        $sharedRoomsTable.= "<td align=\"center\">";
        $sharedRoomsTable.= "<input type=\"hidden\" name=\"book-roomPreferences[]\" value=\"" . $hostel_room['ID'] . "\" />";
        $sharedRoomsTable.= "<select id=\"sharedsel_" . $nbRoomType . "\" num_nights=\"" . $nights_count . "\" class=\"sharedsel\" name=\"book-nbPersons[]\" style=\"width:150px; color:#3087C9;\">";

        $sharedRoomsTable.= "<option value=\"0\" style=\"color:#3087C9;\">" . _('Select') . "</option>\n";
        $sharedRoomsTable.= "<option value=\"0\">0</option>\n";

        for ($p = 1; $p <= $availableBeds; $p++) {

            $selection_title = '';

            if ($p % $nb_guest_per_room == 0) {
                if (($availableBeds / $nb_guest_per_room) == 1) {
                    $selection_title = _('Availability') . ' | ' . sprintf(gettext('You will use 1 full dorm.'));
                } else {
                    $selection_title = _('Availability') . ' | ' . sprintf(gettext('You will use %d full dorms.'), (int) ($p / $nb_guest_per_room));
                }
                $sharedRoomsTable.= "<option value=\"$p\" complete=\"true\" selection_title=\"$selection_title\">" . sprintf(gettext('%d %s ( %s )'), (int) $p, ( $p == 1 ? _('Guest') : _('Guests')), $display_currency . ( $subtotal * $p )) . "</option>\n";
            } else {

                if (floor($p / $nb_guest_per_room) < 1) {
                    $selection_title = _('Availability') . ' | ' . _('You will partially use 1 dorm.');
                } else {
                    if (floor($p / $nb_guest_per_room) == 1) {
                        $selection_title = _('Availability') . ' | ' . sprintf(gettext('You will use 1 full dorm and 1 partially.'));
                    } else {
                        $selection_title = _('Availability') . ' | ' . sprintf(gettext('You will use %d full dorms and 1 partially.'), (int) floor($p / $nb_guest_per_room));
                    }
                }

                $sharedRoomsTable.= "<option value=\"$p\" complete=\"false\" selection_title=\"$selection_title\">" . sprintf(gettext('%d %s ( %s )'), (int) $p, ( $p == 1 ? _('Guest') : _('Guests')), $display_currency . ( $subtotal * $p )) . "</option>\n";
            }
        }

        $sharedRoomsTable.= "</select>";
        $sharedRoomsTable.= "</td>";

        $sharedRoomsTable.= "</tr>\n";
        $sharedRoomsTableSelect.= "</tr>\n";
        $sharedRoomsCluetipTable.= "</tr></table>";
    }
    $ajaxTableID++;
}

$sharedRoomsTable .= '<tr class="no_dorms"><td class="first" colspan="' . ( ($numNights != 1) ? 6 : 5 ) . '">' . _("No dorms available") . '</td></tr>';

//Count shared room that are displayed
$sharedRoomCount = $nbRoomType;

//Show private rooms
$nbRoomType = 0;
$privateRoomsTable = "";
$ajaxTableID = 0;

foreach ($booking_rooms as $hostel_room) {

    $availableBeds = $hostel_room["BEDS"];

    //Check for dorms only
    if ($hostel_room["BLOCKBEDS"] > 0) {

        $availableRooms = $hostel_room["BEDS"] / $hostel_room["BLOCKBEDS"];

        $date->modify("-$numNights day");
        $nbRoomType++;

        if ($nbRoomType % 2 != 0) {
            $privateRoomsTable.= "<tr>";
            $privateRoomsTableSelect.= "<tr class=\"roomnb\" id=\"proomnb_" . $nbRoomType . "\">";
        } else {
            $privateRoomsTable.= "<tr class=\"odd\">";
            $privateRoomsTableSelect.= "<tr id=\"proomnb_" . $nbRoomType . "\" class=\"roomnb\">";
        }

        $privateRoomsCluetipTable .= '<table class="ajaxTable" id="pajaxTable' . $ajaxTableID . '"><tr>';

        $_date = clone $dateStart;

        for ($i = 0; $i < $numNights; $i++) {

            if ($i == ($numNights - 1)) {
                $privateRoomsCluetipTable .= "<th class='last'>";
            } else {
                $privateRoomsCluetipTable .= "<th>";
            }

            $privateRoomsCluetipTable .= my_mb_ucfirst(mb_substr(strftime("%A", $_date->format('U')), 0, 3, 'UTF-8'));
            $privateRoomsCluetipTable .= strftime("<br /> %d", $_date->format('U'));
            $_date->modify("+1 day");
            $privateRoomsCluetipTable .= "</th>";
        }

        $privateRoomsCluetipTable .= "</tr><tr>";

        $date = clone $dateStart;
        $subtotal = 0;
        $display_currency = '';
        $nights_count = 0;
        $lowest_night = '';
        $lowest_style = '';

        $roomTitle_PR = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;( ' . _('Price per Room') . ' )';
        $roomTitle_PP = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;( ' . _('Price per Person') . ' )';

        //Ensure array of nights is sorted by soonest date first
        if (isset($hostel_room['NIGHTS'])) {
            ksort($hostel_room['NIGHTS']);
            foreach ($hostel_room['NIGHTS'] as $dateint => $room_night) {

                $display_currency = currency_symbol($room_night["CUSTOMER"]["CURRENCY"]);
                $subtotal = $subtotal + $room_night["CUSTOMER"]["MINPRICE"];
                $date_msg = "";
                $date_msg_pp = "";
                $date_msg_pr = "";

                $date_msg_pp = currency_symbol($room_night["CUSTOMER"]["CURRENCY"]) . " " . number_format(round((float) $room_night["CUSTOMER"]["MINPRICE"], 2), 2);
                $date_msg_pr = currency_symbol($room_night["CUSTOMER"]["CURRENCY"]) . " " . number_format(round((float) $room_night["CUSTOMER"]["MINPRICE"] * $hostel_room["BLOCKBEDS"], 2), 2);

                if ($numNights < 8) {
                    $date_msg.= currency_symbol($room_night["CUSTOMER"]["CURRENCY"]) . " ";
                    $date_msg.= number_format(round((float) $room_night["CUSTOMER"]["MINPRICE"] * $hostel_room["BLOCKBEDS"], 2), 2);
                } else {
                    $date_msg.= number_format(round((float) $room_night["CUSTOMER"]["MINPRICE"] * $hostel_room["BLOCKBEDS"], 2), 0);
                }

                $currency_formin = currency_symbol($room_night["CUSTOMER"]["CURRENCY"]);

                $privatereservationTable.= '<tr class="preservation preservation_' . $nbRoomType . '">
                            <td class="first">' . date_conv($date->format('Y-m-d'), $this->wordpress->get_option('aj_date_format')) . '</td>
                            <td><p>' . $hostel_room['NAME_TRANSLATED'] . ' </p> (' . $hostel_room['NAME'] . ')</td>
                            <td>' . currency_symbol($room_night["CUSTOMER"]["CURRENCY"]) . ' <span>' . number_format(round((float) $room_night["CUSTOMER"]["MINPRICE"], 2), 2) . '</span></td>
                            <td><span></span></td>
                            <td class="" style="text-align : right;">' . currency_symbol($room_night["CUSTOMER"]["CURRENCY"]) . ' <span></span></td>
                            </tr>';

                $nights_count++;

                if ($min_price_private == $room_night["CUSTOMER"]["MINPRICE"]) {

                    $lowest_night = _('Lowest night:') . ' <span style="color: #6DA903;">' . $display_currency . ' ' . number_format($min_price_private, 2, '.', '').'</span>';
                    $lowest_style = 'style="color: #6DA903;"';

                    $roomTitle_PR = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;( ' . _('Price per Room') . ' )';
                    $roomTitle_PP = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;( ' . _('Price per Person') . ' )' . ' &nbsp;&nbsp;  ' . _('Lowest night:') . ' ' . $display_currency . ' ' . $min_price_private;
                } else {
                    $lowest_style = '';
                }

                if ($i == 0) {
                    $privateRoomsCluetipTable.= '<td align="center" class="first private" per_person="' . $date_msg_pp . '" per_room="' . $date_msg_pr . '" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg_pr . '</td>';
                } else {
                    $privateRoomsCluetipTable.= '<td align="center" class="private" per_person="' . $date_msg_pp . '" per_room="' . $date_msg_pr . '" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg_pr . '</td>';
                }

                $date->modify("+1 day");
            }
        } else {
            for ($i = 0; $i < $numNights; $i++) {

                $date_msg = "";
                $subtotal = $subtotal + $hostel_room['PRICES']["CUSTOMER"]["MINPRICE"];
                $display_currency = currency_symbol($hostel_room['PRICES']["CUSTOMER"]["CURRENCY"]);

                $date_msg.= currency_symbol($hostel_room['PRICES']["CUSTOMER"]["CURRENCY"]) . " ";
                $date_msg.= number_format(round((float) $hostel_room['PRICES']["CUSTOMER"]["MINPRICE"] * $hostel_room["BLOCKBEDS"], 2), 2);

                $privatereservationTable.= '<tr class="preservation preservation_' . $nbRoomType . '">
                            <td class="first">' . date_conv($date->format('Y-m-d'), $this->wordpress->get_option('aj_date_format')) . '</td>
                            <td><p>' . $hostel_room['NAME_TRANSLATED'] . ' </p> (' . $hostel_room['NAME'] . ')</td>
                            <td>' . currency_symbol($hostel_room['PRICES']["CUSTOMER"]["CURRENCY"]) . ' <span>' . number_format(round((float) $hostel_room['PRICES']["CUSTOMER"]["MINPRICE"], 2), 2) . '</span></td>
                            <td><span></span></td>
                            <td class="" style="text-align : right;">' . currency_symbol($hostel_room['PRICES']["CUSTOMER"]["CURRENCY"]) . ' <span></span></td>
                            </tr>';

                $date->modify("+1 day");
                $nights_count++;
            }
        }

        $privateRoomsTable.= '<td class="first" style="border-right: none;">';
        $privateRoomsTableSelect.= '<td class="first" style="border-right: none;">';

        if (!empty($hostel_room['NAME_TRANSLATED'])) {
            $privateRoomsTable.= '<span class="tooltip" title="' . _("VERSION ORIGINALE :") . ' ' . $hostel_room['NAME'] . '">' . $hostel_room['NAME_TRANSLATED'] . '</span>';
            $privateRoomsTableSelect.= '<span class="tooltip" title="' . _("VERSION ORIGINALE :") . ' ' . $hostel_room['NAME'] . '">' . $hostel_room['NAME_TRANSLATED'] . '</span>';
        } else {
            $privateRoomsTable.= $hostel_room['NAME'];
            $privateRoomsTableSelect.= $hostel_room['NAME'];
        }

        if ($breakfast_included == 1) {
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
        if ($hostel_room["BLOCKBEDS"] == 1) {
            $privateRoomsTable.= '<td align="center" width="50" title="' . sprintf(gettext('This type of bedroom can accommodate up to 1 guest per room.')) . '"><span class="nbpeople-table icon-nbpeople">' . (($hostel_room["BLOCKBEDS"] > 1) ? ('<span>' . $hostel_room["BLOCKBEDS"] . '</span>') : ('<span>' . $hostel_room["BLOCKBEDS"] . '</span>')) . '</span></td>';
            $privateRoomsTableSelect.= '<td align="center" width="50" title="' . sprintf(gettext('This type of bedroom can accommodate up to 1 guest per room.')) . '"><span class="nbpeople-table icon-nbpeople">' . (($hostel_room["BLOCKBEDS"] > 1) ? ('<span>' . $hostel_room["BLOCKBEDS"] . '</span>') : ('<span>' . $hostel_room["BLOCKBEDS"] . '</span>')) . '</span></td>';
        } else {
            $privateRoomsTable.= '<td align="center" width="50" title="' . sprintf(gettext('This type of bedroom can accommodate up to %d guests per room.'), (int) $hostel_room["BLOCKBEDS"]) . '"><span class="nbpeople-table icon-nbpeople">' . (($hostel_room["BLOCKBEDS"] > 1) ? ('<span>' . $hostel_room["BLOCKBEDS"] . '</span>') : ('<span>' . $hostel_room["BLOCKBEDS"] . '</span>')) . '</span></td>';
            $privateRoomsTableSelect.= '<td align="center" width="50" title="' . sprintf(gettext('This type of bedroom can accommodate up to %d guests per room.'), (int) $hostel_room["BLOCKBEDS"]) . '"><span class="nbpeople-table icon-nbpeople">' . (($hostel_room["BLOCKBEDS"] > 1) ? ('<span>' . $hostel_room["BLOCKBEDS"] . '</span>') : ('<span>' . $hostel_room["BLOCKBEDS"] . '</span>')) . '</span></td>';
        }

        $nb_guest_per_room = $hostel_room["BLOCKBEDS"];

        $roomText = "";

        if (ceil($availableBeds / $nb_guest_per_room) == 1) {
            if ($availableBeds == 1) {
                $roomText = sprintf(gettext('1 guest in 1 Bedroom.'));
            } else {
                $roomText = sprintf(gettext('%d guests in 1 Bedroom.'), (int) $availableBeds);
            }
        } else {
            if ($nb_guest_per_room == 1) {
                $roomText = sprintf(gettext('%d guests in %d Bedrooms (1 guest in each room).'), (int) $availableBeds, (int) ceil($availableBeds / $nb_guest_per_room));
            } else {
                $roomText = sprintf(gettext('%d guests in %d Bedrooms (%d guests in each room).'), (int) $availableBeds, (int) ceil($availableBeds / $nb_guest_per_room), (int) $nb_guest_per_room);
            }
        }

        if ($numNights != 1) {
            $privateRoomsTable.= '<td align="center" title="">';
            $privateRoomsTable.= '<a class="ajaxTable per_tooltip" href="#pajaxTable' . $ajaxTableID . '" rel="#pajaxTable' . $ajaxTableID . '" style="display : block;" title="' . $roomTitle_PP . '"  per_person="' . $roomTitle_PP . '"  per_room="' . $roomTitle_PR . '">';
            $privateRoomsTable.= '<strong>' . $display_currency . '</strong> <span class="private" style="font-weight: bold;" per_person="' . number_format(round((float) ($subtotal / $nights_count), 2), 2) . '" per_room="' . number_format(round((float) ($subtotal / $nights_count) * $hostel_room["BLOCKBEDS"], 2), 2) . '">' . number_format(($subtotal / $nights_count), 2, '.', '') . '</span>';
            if ($lowest_night != '') {
                $lowest_title = sprintf(gettext('The lowest price per person per night in a private room at this property: %s'), $display_currency . ' ' . $min_price_private);
                $privateRoomsTable.= '<span class="lowest_night" style="display: block; float: none;">' . $lowest_night . '</span>';
            }
            $privateRoomsTable.= '</a>';
            $privateRoomsTable.= '</td>';
        } else {
            $privateRoomsTable.= '<td align="center" title="" style="font-weight: bold;">';
            $privateRoomsTable.= $display_currency . ' <span class="private" per_person="' . number_format(round((float) ($subtotal / $nights_count), 2), 2) . '" per_room="' . number_format(round((float) ($subtotal / $nights_count) * $hostel_room["BLOCKBEDS"], 2), 2) . '">' . number_format(($subtotal / $nights_count), 2, '.', '') . '</span>';
            $privateRoomsTable.= '</td>';
        }

        $privateRoomsTable.= '<td align="center" id="pnbroom_' . $nbRoomType . '" roombeds="' . $nb_guest_per_room . '" title="">
                <div style="font-weight : 600;">' . $availableBeds . ' 
                ' . ( $availableBeds == 1 ? _('Guest') : _('Guests') ) . '
                </div>
                <a class="title" href="#" title="' . _('Availability') . '|' . $roomText . '">
                <span style="font-size : 11px; margin-top : 5px;">' . ceil($availableBeds / $nb_guest_per_room) . ' </span>
                ' . ( (ceil($availableBeds / $nb_guest_per_room) == 1) ? _('Bedroom') : _('Bedrooms') ) . '
                </a>
                </td>';

        $privateRoomsTableSelect.= '<td id="pnbrooms_' . $nbRoomType . '" align="center" class="pnbrooms"><a href="" class="title" title="">' . _('Bedrooms you will occupy:') . ' <strong></strong></a></td>';
        $privateRoomsTableSelect.= '<td id="pnbguest_' . $nbRoomType . '" align="center" class="pnbguest"><strong></strong> x <span  class="nbpeople-table icon-nbpeople nbpeople-1"></span></td>';
        $privateRoomsTableSelect.= '<td align="center" id="psubtotal_' . $nbRoomType . '" class="psubtotal"><span class="calc_init" id="psubtotal_init_' . $nbRoomType . '">' . number_format($subtotal, 2, '.', '') . '</span>' . $display_currency . '  <span class="calc_sum" id="psubtotal_calc_' . $nbRoomType . '"></span></td>';
        //TODO javascript to prevent more than available rooms booking by adding select box value

        $privateRoomsTable.= "<td align=\"center\">";
        $privateRoomsTable.= "<input type=\"hidden\" name=\"book-roomPreferences[]\" value=\"" . $hostel_room['ID'] . "\" />";
        $privateRoomsTable.= "<select id=\"privatesel_" . $nbRoomType . "\" num_nights=\"" . $nights_count . "\" class=\"privatesel\" name=\"book-nbPersons[]\" style=\"width:150px; color:#3087C9;\">";
        $privateRoomsTable.= "<option value=\"0\">" . _('Select') . "</option>\n";
        $privateRoomsTable.= "<option value=\"0\">0</option>\n";

        for ($p = 1; $p <= $availableRooms; $p++) {

            if ($p == 1) {
                if (($p * $hostel_room["BLOCKBEDS"]) == 1) {
                    $selection_title = _('Availability') . ' | ' . sprintf(gettext('1 guest in 1 Bedroom.'));
                } else {
                    $selection_title = _('Availability') . ' | ' . sprintf(gettext('%d guests in 1 Bedroom.'), (int) $p * $hostel_room["BLOCKBEDS"]);
                }
            } else {
                if ($hostel_room["BLOCKBEDS"] == 1) {
                    $selection_title = _('Availability') . ' | ' . sprintf(gettext('%d guests in %d Bedrooms (%d guest in each room).'), (int) $p * $hostel_room["BLOCKBEDS"], (int) $p, (int) $hostel_room["BLOCKBEDS"]);
                } else {
                    $selection_title = _('Availability') . ' | ' . sprintf(gettext('%d guests in %d Bedrooms (%d guests in each room).'), (int) $p * $hostel_room["BLOCKBEDS"], (int) $p, (int) $hostel_room["BLOCKBEDS"]);
                }
            }

            $privateRoomsTable.= "<option value=\"" . $p * $hostel_room["BLOCKBEDS"] . "\" selection_title=\"" . $selection_title . "\">
                " . sprintf(gettext('%d %s ( %s )'), ( $p * (int) $hostel_room["BLOCKBEDS"]), ( ($p * (int) $hostel_room["BLOCKBEDS"]) == 1 ? _('Guest') : _('Guests')), $display_currency . ( $subtotal * $p * $hostel_room["BLOCKBEDS"] )) . "
                </option>\n";
        }

        $privateRoomsTable.= "</select>";
        $privateRoomsTable.= "</td>";
        $privateRoomsTable.= "</tr>\n";
        $privateRoomsTableSelect.= "</tr>\n";
        $privateRoomsCluetipTable .= "</tr></table>";
    }
    $ajaxTableID++;
}

$privateRoomCount = $nbRoomType;

if (($sharedRoomCount === 0) && ($privateRoomCount === 0 )) {
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
} else {
    ?>

    <form class="group" method="post" action="<?php echo secure_site_url($this->Db_links->get_link("booking")); ?>">
        <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <th class="title" colspan="2"> 
                        <?php echo anchor('#', _('Type of dorms'), array('class' => 'title', 'style' => 'margin-top: 5px; display: block;', 'title' => _('Chambres partagées - Dortoirs') . ' | ' . _('You must share the room (unless you purchase all the beds in the dorm).'))); ?>
            <div style="text-align: right; font-size: 11px; font-weight: normal;">
                <?php
                $data = array(
                    'name' => 'complete_dorms',
                    'id' => 'complete_dorms',
                    'value' => 'accept',
                    'checked' => FALSE,
                    'style' => '',
                );
                echo form_label(anchor('#', _('Dorms for your group only'), array('class' => 'title', 'title' => _('Complete dorm only') . ' | ' . _('We will only show you dorms you can fully book. You will need to take all the beds in that particular dorm even if you do not need all of them. This is often used by groups.'))) . ' &nbsp; ' . form_checkbox($data), 'complete_dorms');
                ?>
            </div>
            </th>
            <?php if ($numNights != 1): ?>
                <th width="150">
                    <?php echo anchor('#', _('Average per night per person'), array('class' => 'title', 'title' => _('Average Price') . ' | ' . _('Average price per night for requested arrival date and length of stay. Actual day-by-day prices may vary. However, our prices are quaranteed and you will never pay more.'))); ?>
                </th>
            <?php else: ?>
                <th width="150"><?php echo _('Price'); ?></th>
            <?php endif; ?>
            <th width="80"><?php echo _('Maximum'); ?></th>
            <?php if ($numNights == 1): ?>
                <th class="last" width="170"><?php echo _('Number of guests'); ?></th>
            <?php else: ?>
                <th class="last" width="170"><?php printf(gettext('Number of guests for %d nights'), (int) $numNights); ?></th>
            <?php endif; ?>
            </tr>
            <?php
            if ($sharedRoomCount > 0) {
                echo $sharedRoomsTable;
            } else {
                ?>
                <tr>
                    <td class="first" colspan="<?php echo ($numNights != 1) ? 6 : 5; ?>"><?php echo _("No dorms available"); ?></td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <th class="title" width="" colspan="2"> 
                    <?php echo anchor('#', _('Type of private bedrooms'), array('class' => 'title', 'style' => 'margin-top: 4px; display: block; float: left;', 'title' => _('Type of private bedrooms') . ' | ' . _('You must pay for the whole private room, even if you do not need all the beds. The room cannot be shared.'))); ?>
            <div style="text-align: right; font-size: 11px; font-weight: normal; clear: both;">
                <?php
                $data = array(
                    'name' => 'price_selection',
                    'class' => 'price_selection',
                    'value' => 'per_person',
                    'checked' => TRUE,
                    'style' => '',
                );
                echo form_label(form_radio($data) . _('Price per Person'), 'price_per_room');

                echo "&nbsp;&nbsp;&nbsp;&nbsp;";

                $data = array(
                    'name' => 'price_selection',
                    'class' => 'price_selection',
                    'value' => 'per_room',
                    'checked' => FALSE,
                    'style' => '',
                );
                echo form_label(form_radio($data) . _('Price per Room'), 'price_per_person');
                ?>
            </div>
            </th>
            <?php if ($numNights != 1): ?>
                <th width="150" class="">
                    <?php echo anchor('#', _('Average per night per room'), array('class' => 'title per_title', 'per_room' => _('Average per night per room'), 'per_person' => _('Average per night per person'), 'title' => _('Average Price') . ' | ' . _('Average price per night for requested arrival date and length of stay. Actual day-by-day prices may vary. However, our prices are quaranteed and you will never pay more.'))); ?>
                </th>
            <?php else: ?>
                <th width="150" class="per_title" per_room="<?php echo _('Price'); ?>" per_person="<?php echo _('Price'); ?>"><?php echo _('Price'); ?></th>
            <?php endif; ?>
            <th width="80"><?php echo _('Maximum'); ?></th>
            <?php if ($numNights == 1): ?>
                <th class="last" width="170"><?php echo _('Number of guests'); ?></th>
            <?php else: ?>
                <th class="last" width="170"><?php printf(gettext('Number of guests for %d nights'), (int) $numNights); ?></th>
            <?php endif; ?>
            </tr>
            <?php
            if ($privateRoomCount > 0) {
                echo $privateRoomsTable;
            } else {
                ?>
                <tr>
                    <td class="first" colspan="<?php echo ( ($numNights != 1) ? 6 : 5 ); ?>"><?php echo _("No private room available"); ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>

        <table id="selection" border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <th class="title" colspan="3" width="240"> 
                        <?php echo anchor('#', _('Your Selection'), array('class' => 'title', 'style' => 'display: block; float: left;', 'title' => _('Notes Importantes') . ' | ' . sprintf(gettext("You only pay the deposit (10%% of total amount) to confirm and secure your reservation now. The remaining amount (90%%) is payable upon arrival. You will find the hotel's contact information (email, address, telephone number…) in your confirmation email after you have made your reservation."), $this->config->item('site_name')))); ?>
                        <span style="float: right; margin-right: 20px;">
                            <?php echo _('Arrivée'); ?> : <b><?php echo $datetop; ?> </b> &nbsp;&nbsp; <?php echo _('Nombre de Nuits'); ?> : <b><?php echo $numNights; ?> </b>
                        </span>
                    </th>
                    <th><?php echo _("Number of guests selected"); ?></th>
                    <th class="last"> <?php echo _("Price"); ?> </th>
                </tr>
                <?php
                if ($sharedRoomCount > 0) {
                    echo $sharedRoomsTableSelect;
                }

                if ($privateRoomCount > 0) {
                    echo $privateRoomsTableSelect;
                }
                ?>
                <tr>
                    <td class="first" align="right" colspan="3"><strong><?php echo _('Total'); ?></strong></td>
                    <td class="total_people" align="center" colspan="" style="font-size: 14px; padding: 10px;"><strong></strong> x <span  class="nbpeople-table icon-nbpeople nbpeople-1"></span></td>
                    <td align="center"><?php echo $display_currency; ?> <strong id="bigTotal">0.00</strong></td>
                </tr>
                <tr>
                    <td class="first" align="right" colspan="4"><span class="best_price left"><?php echo _('You got the best price') ?> </span><strong class="right deposit_bottom"><?php echo _('10% Arrhes / Dépôt sera facturé en'); ?></strong></td>
                    <td align="center"><?php echo $display_currency; ?> <strong id="depositTotal">0.00</strong></td>
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
            <table style="border: none;">
                <tr>
                    <td style="border: none; background: none;">
                        <div class="confirmationEmail" style="width: 450px; text-align: center;">
                            <a href='#' class='basic-modal' style="font-weight: bold; text-decoration: none;">
                                <img src="<?php echo site_url(); ?>images/send-to-friend.png" alt="<?php echo _("Confirmation Email Preview"); ?>" style="float: left;" />
                                <p style="float: left; margin-top: 10px;">
                                    <?php echo _("Please click here to preview your confirmation email"); ?>
                                </p>
                            </a>
                        </div>
                        <span style="float: left; clear: left;"><?php echo _('Best price. We guarantee it.') ?></span>
                        <span style="float: left; clear: left;"><?php echo _('It only takes 2 minutes') ?></span>
                    </td>
                    <td style="border: none; background: none;">
                        <?php if (empty($print)) { ?>
                            <input type="submit" onfocus="this.blur()" name="booking-form" id="booking-form-submit" class="button-green box_round hoverit" value="<?php echo _("Réserver Maintenant"); ?>" />
                        <?php } else { ?>
                            <strong id="booking-form-submit"><?php echo _('PLEASE NOTE THIS IS NOT A CONFIRMED BOOKING'); ?></strong>
                        <?php } ?>
                        <img src="<?php echo site_url(); ?>images/padlock.png" alt="<?php echo _("sécurisé"); ?>" />
                    </td>
                </tr>
            </table>
        </div>

    </form>

    <?php
    $csspath = $this->wordpress->get_option('aj_api_ascii');
    if (empty($csspath)) {
        $csspath = $this->wordpress->get_option('aj_api_name');
    }
    ?>

    <!-- modal content -->
    <div id="basic-modal-content">
        <h3 style="color: #6DA903; text-align: center;">
            <strong>
                <?php echo _("Confirmation Email Preview"); ?>
            </strong>            
        </h3>
        <div style="padding: 10px; border-bottom: solid 5px grey;">
            <p><img class="logo" src="<?php echo site_url(); ?>images/<?php echo $csspath; ?>/logo.png" alt="<?php echo $this->wordpress->get_option('aj_api_name'); ?>"/></p>
        </div>
        <div style="padding: 10px;">
            <h3 style="color: #6DA903;"><?php echo _("Your unique booking number will be provided right after your reservation. Your reservation will be immediate and guaranteed."); ?></h3>
        </div>
        <div style="padding: 10px;">
            <p><?php echo _('Arrivée'); ?>: <b><?php echo $datetop; ?></b> &nbsp;&nbsp; <?php echo _('Nombre de Nuits'); ?>: <b><?php echo $numNights; ?></b></p>
        </div>

        <style type="text/css">
            table.emailpreview {
                background: white;
                color: gray;
            }

            table.emailpreview thead {
                background: gray;
                color: white;
            }

            table.emailpreview td {
                padding: 10px;
                text-align: center;
            }

            table.emailpreview th {
                padding: 10px;
                text-align: center;
            }

        </style>
        <table id="sharedemailreservationView" class="emailpreview" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
            <thead>
                <tr valign="middle" align="center">
                    <th width="150" class="first-cell green-th"><?php echo _('Date'); ?></th>
                    <th width="350" class="green-th"><?php echo _('Chambres partagées - Dortoirs'); ?></th>
                    <th class="green-th">
                        <?php echo _('Prix (lit)'); ?>										
                    </th>
                    <th class="green-th"><?php echo _('Number of guests'); ?></th>
                    <th width="80" class="last-cell green-th" style="text-align : right;"><?php echo _('Total'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php echo $sharedreservationTable; ?>
            </tbody>
        </table>

        <table id="privateemailreservationView" class="emailpreview" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
            <thead>
                <tr valign="middle" align="center">
                    <th width="150" class="first-cell green-th"><?php echo _('Date'); ?></th>
                    <th width="350" class="green-th"><?php echo _('Chambres privées'); ?></th>
                    <th class="green-th">
                        <?php echo _('Prix (lit)'); ?>										
                    </th>
                    <th class="green-th"><?php echo _('Number of guests'); ?></th>
                    <th width="80" class="last-cell green-th" style="text-align : right;"><?php echo _('Total'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php echo $privatereservationTable; ?>
            </tbody>
        </table> 

        <table>  
            <tr>
                <td colspan=4 style="border:none; color: black; padding:4.5pt 6.75pt 4.5pt 6.75pt">
                    <p align=right style="text-align:right;line-height:18px; font-size:13px;">
                        <strong>
                            <?php echo _('Total'); ?> (<?php echo $currency; ?>):
                        </strong>
                    </p>
                </td>
                <td width="80" style="border:none; color: black; padding:4.5pt 6.75pt 4.5pt 6.75pt">
                    <p align=right style="text-align:right;line-height:18px">
                        <b>
                            <span style="font-size:10.0pt;">
                                <?php echo currency_symbol($currency); ?> <span id="totalarrival_email"></span>
                            </span>
                        </b>
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan=4 style="background:#eaeff1;border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
                    <p align=right style="text-align:right;line-height:18px">
                        <span style="font-size:12px;color:#2F2F2F;font-weight:bold;">
                            <?php echo _('10% Arrhes / Dépôt sera facturé en'); ?> <strong><?php echo $currency; ?></strong>:
                        </span>
                    </p>
                </td>
                <td style="background:#eaeff1;border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
                    <p align=right style="text-align:right;line-height:18px">
                        <span style="font-size:12px; color:#2F2F2F">
                            <b>
                                <?php echo currency_symbol($currency); ?> <span id="totaldeposit_email" style="color: #175291; font-size: 18px;"></span>
                            </b>    	
                        </span>
                    </p>
                </td>
            </tr>
        </table>

        <div style="border: solid 1px gray; background: gray; padding: 10px; margin-top: 20px; color: white;">
            <?php echo _("Information sur l'établissement"); ?>:
        </div>
        <div style="border: solid 1px gray; padding: 10px;">
            <div style="padding: 10px;">
                <span style="padding: 10px; font-weight: bold; color: black;"><?php echo _("Nom de l'établissement"); ?>:</span>        
                <span style="padding: 10px; color: #6DA903;">
                    <?php echo $propertyName; ?>
                </span>
            </div> 
            <div style="padding: 10px;">
                <span style="padding: 10px; font-weight: bold; color: black;"><?php echo _("Phone number"); ?>:</span>        
                <span style="padding: 10px; color: #6DA903;">
                    <?php echo _("Provided after reservation"); ?>
                </span>
            </div>
            <div style="padding: 10px;">
                <span style="padding: 10px; font-weight: bold; color: black;"><?php echo _("Adresse"); ?>:</span>        
                <span style="padding: 10px; color: #6DA903;">
                    <?php echo _("Provided after reservation"); ?>
                </span>
            </div>
            <div style="padding: 10px;">
                <span style="padding: 10px; font-weight: bold; color: black;"><?php echo _("Email"); ?>:</span>        
                <span style="padding: 10px; color: #6DA903;">
                    <?php echo _("Provided after reservation"); ?>
                </span>
            </div>
            <div style="padding: 20px; color: #6DA903;">
                <?php echo _("With full contact information, you will be able to contact the property in case you have any special requests."); ?>
            </div>
        </div>
        <div style="border: solid 1px gray; background: gray; padding: 10px; margin-top: 20px; color: white;">
            <?php echo _("Directions"); ?>:
        </div>
        <div style="border: solid 1px gray; padding: 30px; color: #6DA903;">
            <?php echo _("When available, we will provide directions on how to get to the property including public transportation and airport information."); ?>
        </div>

        <div style="border: solid 1px gray; background: gray; padding: 10px; margin-top: 20px; color: white;">
            <?php echo _("Informations Importantes"); ?>:
        </div>
        <div style="border: solid 1px gray; padding: 30px; color: #6DA903;">
            <?php echo _("We will provide all important information to make sure you really enjoy your stay."); ?>
        </div>

    </div>

    <script>
        $(function(){
            $("#dispo-form").hide();
            $("#change-dates").show();
        });
    </script>
    <p class="red-error" id="formerror"> <?php echo _('Please enter at least one choice in the above table to book a room.'); ?> </p>
    <?php
}
?>
<?php
echo isset($sharedRoomsCluetipTable) ? $sharedRoomsCluetipTable : '';
echo isset($privateRoomsCluetipTable) ? $privateRoomsCluetipTable : '';
?>
<script type="text/javascript" src="<?php echo base_url(); ?>js/calcprice.js"></script> 
<script type="text/javascript">
    
    $('a.basic-modal').bind('click', function() {
        
        var srows = 0;
            
        $('table tr[class^="sreservation sreservation_"] td:nth-child(4) span').each(function () {
                
            if( parseInt($(this).html()) > 0 ) {
                srows++;
            } else {
                $(this).parent().parent().hide();
            }
   
        });
            
        if(srows == 0) {
            $('table#sharedemailreservationView').hide();
        }
            
        var prows = 0;     
            
        $('table tr[class^="preservation preservation_"] td:nth-child(4) span').each(function () {
                
            if( parseInt($(this).html()) > 0 ) {
                prows++;
            } else {
                $(this).parent().parent().hide();
            }
   
        });
            
        if(prows == 0) {
            $('table#privateemailreservationView').hide();
        }
        
        $('#basic-modal-content').modal();
                    
        return false;
        
    });
        
    var sharedrowCount = $('table#sharedemailreservationView tr').length;
    if(sharedrowCount > 1) {
        $("table#sharedemailreservationView").tablesorter({ 
            // sort on the first column and third column, order asc 
            sortList: [[0,0]] 
        });
    }
    

    var privaterowCount = $('table#privateemailreservationView tr').length;
    if(privaterowCount > 1) {
        $("table#privateemailreservationView").tablesorter({ 
            // sort on the first column and third column, order asc 
            sortList: [[0,0]] 
        });
    }    
    
    $('table#sharedemailreservationView tr.preservation').each(function () {
        $(this).hide();
    });
    
    $('table#privateemailreservationView tr.preservation').each(function () {
        $(this).hide();
    });
    
    $('table#sharedemailreservationView tr.sreservation').each(function () {
        $(this).hide();
    });
    
    $('table#privateemailreservationView tr.sreservation').each(function () {
        $(this).hide();
    });
    
    $('div.confirmationEmail').hide();
    
    $('a.title').cluetip({
        width: '400px',
        splitTitle: '|', 
        local:true, 
        cursor: 'pointer',
        arrows: false,
        dropShadow: false,
        sticky: false,
        positionBy: 'bottomTop',
        cluetipClass: 'mcweb',
        tracking: true,
        topOffset: 10
    });
    
    $('a.ajaxTable').cluetip({
        width: '600px', 
        local:true, 
        cursor: 'pointer',
        arrows: false,
        dropShadow: false,
        sticky: false,
        positionBy: 'bottomTop',
        cluetipClass: 'mcweb',
        tracking: true,
        topOffset: 10
    });

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
    
    $('#complete_dorms').bind('click', function() {
        
        if($(this).is(':checked')) {
           
            $('span.complete').each(function() {
                $(this).html($(this).attr('complete'));
                if($(this).attr('complete') == 0) {
                    $(this).parent().parent().parent().hide();
                }
            });
            
            $("select.sharedsel > option[complete$='false']").hide();
            
        } else {
            
            $('span.complete').each(function() {
                $(this).html($(this).attr('not_complete'));
                if($(this).attr('complete') == 0) {
                    $(this).parent().parent().parent().show();
                }
            });            
            
            $("select.sharedsel > option[complete$='false']").show();
            
        }
        
        var dorm_rows = $('tr.dorm_row').filter(function() {
            return this.style.display !== "none";    
        }).length;
        
        if(dorm_rows == 0) {
            $('tr.no_dorms').show();
        } else {
            $('tr.no_dorms').hide();
        }
        
    });
    
    if( $('input[name="price_selection"]').val() == 'per_person' ) {
        
        $('span.private').each(function() {
            $(this).html($(this).attr('per_person'));
        });  
            
        $('th.per_title').each(function() {
            $(this).html($(this).attr('per_person'));
        });
        
        $('td.private').each(function() {
            $(this).html($(this).attr('per_person'));
        });
        
        $('a.per_title').each(function() {
            $(this).html($(this).attr('per_person'));
        });
        
        $('a.per_tooltip').each(function() {            
            $(this).attr('title', $(this).attr('per_person'));
        });
        
        $('span.lowest_night').each(function() {
            $(this).show();
        });
    
    }    
    
    $('input[name="price_selection"]').bind('click', function() {

        if($(this).val() == 'per_person') {
            
            $('span.private').each(function() {
                $(this).html($(this).attr('per_person'));
            });  
            
            $('th.per_title').each(function() {
                $(this).html($(this).attr('per_person'));
            });
            
            $('td.private').each(function() {
                $(this).html($(this).attr('per_person'));
            });
            
            $('a.per_title').each(function() {
                $(this).html($(this).attr('per_person'));
            });
            
            $('a.per_tooltip').each(function() {
                $(this).attr('title', $(this).attr('per_person'));
            });
            
            $('span.lowest_night').each(function() {
                $(this).show();
            });
            
        } else if ($(this).val() == 'per_room') {
            
            $('span.private').each(function() {
                $(this).html($(this).attr('per_room'));
            });
            
            $('th.per_title').each(function() {
                $(this).html($(this).attr('per_room'));
            });
            
            $('td.private').each(function() {
                $(this).html($(this).attr('per_room'));
            });
            
            $('a.per_title').each(function() {
                $(this).html($(this).attr('per_room'));
            });
            
            $('a.per_tooltip').each(function() {
                $(this).attr('title', $(this).attr('per_room'));
            });
            
            $('span.lowest_night').each(function() {
                $(this).hide();
            });
            
        }
        
        $('a.ajaxTable').cluetip({
            width: '600px', 
            local:true, 
            cursor: 'pointer',
            arrows: false,
            dropShadow: false,
            sticky: false,
            positionBy: 'bottomTop',
            cluetipClass: 'mcweb',
            tracking: true,
            topOffset: 10
        });
            
    });
    
    $('tr.no_dorms').hide();
    
    $('table.ajaxTable').each(function() {
        $(this).hide();
    });

</script> 
