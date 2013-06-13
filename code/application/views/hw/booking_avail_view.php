<?php
$maximum_guests = 8;
echo form_hidden('deposit_percent', ($booking_info->depositPercent / 100));
echo form_hidden('partially_available', _('Partially Available'));
echo form_hidden('total_guests', _('Total number of guests selected'));
echo form_hidden('maximum_guests', $maximum_guests);
echo form_hidden('maximum_guests_message', sprintf(gettext('You can only book up to %d guests at a time. You can book more than %d guests by going to our group section on top of this page.'), (int) $maximum_guests, (int) $maximum_guests));
?>

<h2 class="dotted-line margbot15"><?php echo _('Disponibilités'); ?> <span>(<?php echo $currency; ?>)</span></h2>

<?php
$date = clone $dateStart;
$datetop = date_conv($dateStart->format('Y-m-d'), $this->wordpress->get_option('aj_date_format'));
?>

<div class="top-table">
    <p>
        <?php echo _('Arrivée'); ?>: <b><?php echo $datetop; ?></b> &nbsp;&nbsp; <?php echo _('Nombre de Nuits'); ?>: <b><?php echo $numNights; ?></b><a id="change-dates" href="#">[<?php echo _('Change Dates'); ?>]</a>
        <span style="float: right;">
            <?php
            $data = array(
                'name' => 'fully_available',
                'id' => 'fully_available',
                'value' => 'accept',
                'checked' => FALSE,
                'style' => '',
            );
            echo form_label(anchor('#', _('Show fully available only'), array('class' => 'title', 'title' => _('Show fully available only') . ' | ' . _('We are displaying rooms with both full and partial availability for your dates. You can select this filter to only see rooms with full availability for your stay.'))) . ' &nbsp; ' . form_checkbox($data), 'fully_available');
            ?>
        </span>       
    </p>
</div>

<?php
if ($api_error == false) {

    $maxPersons = 10;
    if ($booking_info->maxPax < $maxPersons) {
        $maxPersons = $booking_info->maxPax;
    }
    ?>
    <?php
    $nbRoomType = 0;
    $sharedRoomsAvailable = 0;
    $min_price_shared = 0;
    $min_price_private = 0;
    $sharedRoomsTable = "";
    $sharedRoomsCluetipTable = "";
    $sharedRoomsTableSelect = "";
    $sharedreservationTable = "";
    $privatereservationTable = "";
    $ajaxTableID = 0;

    foreach ($distinctRoomTypes as $hostel_room_type) {
        if (substr_count($hostel_room_type['roomType'], "Private") == 0) {
            $date = clone $dateStart;
            for ($i = 0; $i < $numNights; $i++) {
                foreach ($hostel_room_type['AvailableDates']['AvailableDate'] as $date_ok) {
                    if ($date_ok['date'] == $date->format("Y-m-d")) {
                        // To check min version
                        if ($min_price_shared == 0) {
                            $min_price_shared = $date_ok['price'];
                        } elseif ($min_price_shared > $date_ok['price']) {
                            $min_price_shared = $date_ok['price'];
                        }
                    }
                }
                $date->modify("+1 day");
            }
        }

        //Show private rooms with beds increment lower than maxpax because if it is higher it will be a group booking and will cause problem on booking
        if (($maxPersons > $hostel_room_type["bedsIncrement"]) && (substr_count($hostel_room_type['roomType'], "Private") > 0)) {
            $date = clone $dateStart;
            for ($i = 0; $i < $numNights; $i++) {
                foreach ($hostel_room_type['AvailableDates']['AvailableDate'] as $date_ok) {
                    if ($date_ok['date'] == $date->format("Y-m-d")) {

                        if ($min_price_private == 0) {
                            $min_price_private = $date_ok['price'];
                        } elseif ($date_ok['price'] < $min_price_private) {
                            $min_price_private = $date_ok['price'];
                        }
                    }
                }
                $date->modify("+1 day");
            }
        }
    }

    //Show shared rooms first
    foreach ($distinctRoomTypes as $hostel_room_type) {

        $availableBeds = $maxPersons;
        $availableRooms = 0;

        //Check for dorms only
        if (substr_count($hostel_room_type['roomType'], "Private") == 0) {

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
                    if (floor($numNights / 2) - 1) {
                        $sharedRoomsCluetipTable .= "<th class='last'>";
                    } else {
                        $sharedRoomsCluetipTable .= "<th>";
                    }
                }

                $sharedRoomsCluetipTable .= my_mb_ucfirst(mb_substr(strftime("%A", $_date->format('U')), 0, 3, 'UTF-8'));
                $sharedRoomsCluetipTable .= strftime("<br /> %d", $_date->format('U'));
                $sharedRoomsCluetipTable .= "</th>";

                if ($numNights > 13) {
                    if ($i == floor($numNights / 2)) {
                        $sharedRoomsCluetipTable .= "</tr><tr>";
                    }
                }
                $_date->modify("+1 day");
            }

            if ($numNights > 13) {
                if ($numNights % 2 != 0) {
                    $sharedRoomsCluetipTable .= "<th class='last'></th>";
                } else {
                    $sharedRoomsCluetipTable .= "<th></th>";
                    $sharedRoomsCluetipTable .= "<th class='last'></th>";
                }
            }

            $sharedRoomsCluetipTable .= "</tr><tr>";

            $date = clone $dateStart;
            $subtotal = 0;
            $num_nights_available_of_room = 0;
            $sum_available = 0;
            $currency_formin = '';
            $lowest_night = '';
            $lowest_style = '';

            $dormTitle = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights;

            for ($i = 0; $i < $numNights; $i++) {

                $date_available = false;
                $date_msg = '<span class="na-book price" title="' . _('No dorms available') . '">0</span>';

                foreach ($hostel_room_type['AvailableDates']['AvailableDate'] as $date_ok) {
                    if ($date_ok['date'] == $date->format("Y-m-d")) {

                        $num_nights_available_of_room++;
                        $price_array = explode(' ', $date_ok['price']);

                        if ($price_array[0] == 'From') {
                            $price = $price_array[1];
                        } else {
                            $price = $price_array[0];
                        }

                        $subtotal = $subtotal + $price;

                        $currency_formin = currency_symbol($date_ok['currency']);
                        $sum_available += $price;

                        $sharedreservationTable.= '<tr class="sreservation sreservation_' . $nbRoomType . '">
                            <td class="first">' . date_conv($date_ok['date'], $this->wordpress->get_option('aj_date_format')) . '</td>
                            <td><p>' . $hostel_room_type['roomTypeDescriptionTranslated'] . ' </p> (' . $hostel_room_type['roomTypeDescription'] . ')</td>
                            <td>' . currency_symbol($date_ok['currency']) . ' <span>' . $price . '</span></td>
                            <td><span></span></td>
                            <td class="" style="text-align : right;">' . currency_symbol($date_ok['currency']) . ' <span></span></td>
                            </tr>';

                        if ($min_price_shared == $price) {

                            $lowest_night = _('Lowest night:') . ' <span style="color: #6DA903;">' . $currency_formin . ' ' . number_format($min_price_shared, 2, '.', '') . '</span>';
                            $lowest_style = 'style="color: #6DA903;"';

                            $dormTitle = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;&nbsp;  ' . _('Lowest night:') . ' ' . $currency_formin . ' ' . $min_price_shared;
                        } else {
                            $lowest_style = '';
                        }

                        $date_msg = currency_symbol($date_ok['currency']) . ' <span class="price" ' . $lowest_style . '>' . number_format($price, 2, '.', '') . '</span>';
                    }
                }

                if ($i == 0) {
                    $sharedRoomsCluetipTable.= '<td align="center" class="first" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                } else {
                    if ($i == floor($numNights / 2) + 1) {
                        $sharedRoomsCluetipTable.= '<td align="center" class="first" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                    } else {
                        $sharedRoomsCluetipTable.= '<td align="center" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                    }
                }

                if ($numNights > 13) {
                    if ($i == floor($numNights / 2)) {
                        $sharedRoomsCluetipTable .= "</tr><tr>";
                    }
                }

                $date->modify("+1 day");
            }

            $sharedRoomsTable.= '<td class="first" style="border-right: none;">';
            $sharedRoomsTableSelect.= '<td class="first" style="border-right: none;">';

            if (!empty($hostel_room_type['roomTypeDescriptionTranslated'])) {
                $sharedRoomsTable.= '<span class="tooltip" title="' . _("VERSION ORIGINALE :") . ' ' . $hostel_room_type['roomTypeDescription'] . '">' . $hostel_room_type['roomTypeDescriptionTranslated'] . '</span>';
                $sharedRoomsTableSelect.= '<span class="tooltip" title="' . _("VERSION ORIGINALE :") . ' ' . $hostel_room_type['roomTypeDescription'] . '">' . $hostel_room_type['roomTypeDescriptionTranslated'] . '</span>';
            } else {
                $sharedRoomsTable.= $hostel_room_type['roomTypeDescription'];
                $sharedRoomsTableSelect.= $hostel_room_type['roomTypeDescription'];
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
            $nb_guest_per_room = explode(':', $hostel_room_type['roomTypeCode']);
            $nb_guest_per_room = $nb_guest_per_room[0];

            if ($nb_guest_per_room == 1) {
                $sharedRoomsTable.= '<td align="center" width="50" title="' . sprintf(gettext('This type of dorm can accommodate up to 1 guest per dorm.')) . '"><span class="nbpeople-table icon-nbpeople">' . (($nb_guest_per_room > 1) ? ('<span>' . $nb_guest_per_room . '</span>') : ('<span>' . $nb_guest_per_room . '</span>')) . '</span></td>';
                $sharedRoomsTableSelect.= '<td align="center" width="50" title="' . sprintf(gettext('This type of dorm can accommodate up to 1 guest per dorm.')) . '"><span class="nbpeople-table icon-nbpeople">' . (($nb_guest_per_room > 1) ? ('<span>' . $nb_guest_per_room . '</span>') : ('<span>' . $nb_guest_per_room . '</span>')) . '</span></td>';
            } else {
                $sharedRoomsTable.= '<td align="center" width="50" title="' . sprintf(gettext('This type of dorm can accommodate up to %d guests per dorm.'), (int) $nb_guest_per_room) . '"><span class="nbpeople-table icon-nbpeople">' . (($nb_guest_per_room > 1) ? ('<span>' . $nb_guest_per_room . '</span>') : ('<span>' . $nb_guest_per_room . '</span>')) . '</span></td>';
                $sharedRoomsTableSelect.= '<td align="center" width="50" title="' . sprintf(gettext('This type of dorm can accommodate up to %d guests per dorm.'), (int) $nb_guest_per_room) . '"><span class="nbpeople-table icon-nbpeople">' . (($nb_guest_per_room > 1) ? ('<span>' . $nb_guest_per_room . '</span>') : ('<span>' . $nb_guest_per_room . '</span>')) . '</span></td>';
            }

            $display_currency = currency_symbol($date_ok['currency']);
            $sharedRoomsTableSelect.= '<td id="snbrooms_' . $nbRoomType . '" align="center" class="snbrooms"><a href="" class="title" title="' . _('Availability') . ' | ' . sprintf(gettext('Dorm Availability')) . '">' . _('Dorms you will occupy:') . ' <strong></strong></a></td>';
            $sharedRoomsTableSelect.= '<td id="snbguest_' . $nbRoomType . '" align="center" class="snbguest"><strong></strong> x <span  class="nbpeople-table icon-nbpeople nbpeople-1"></span></td>';
            $sharedRoomsTableSelect.= '<td align="center" id="ssubtotal_' . $nbRoomType . '" class="ssubtotal"><span class="calc_init" id="ssubtotal_init_' . $nbRoomType . '">' . number_format($subtotal, 2, '.', '') . '</span>' . $display_currency . '  <span class="calc_sum" id="ssubtotal_calc_' . $nbRoomType . '"></span></td>';

            foreach ($hostel_room_type['AvailableDates']['AvailableDate'] as $avail_date) {
                if ((int) $avail_date['availableBeds'] < $availableBeds) {
                    $availableBeds = (int) $avail_date['availableBeds'];
                    $availableRooms = (int) $avail_date['availableRooms'];
                }
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
                $sharedRoomsTable.= '<span style="font-weight: bold;">' . $currency_formin . ' ' . number_format(($sum_available / $num_nights_available_of_room), 2, '.', '') . '</span>';
                if ($lowest_night != '') {
                    $lowest_title = sprintf(gettext('The lowest price per person per night in a dorm in this property: %s'), $display_currency . ' ' . $min_price_shared);
                    $sharedRoomsTable.= '<span style="display: block; float: none;">' . $lowest_night . '</span>';
                }
                $sharedRoomsTable.= '</a>';
                $sharedRoomsTable.= '</td>';
            } else {
                $sharedRoomsTable.= '<td align="center" style="font-weight: bold;" title="' . _('Price') . '">';
                $sharedRoomsTable.= $currency_formin . ' ' . number_format(($sum_available / $num_nights_available_of_room), 2, '.', '');
                $sharedRoomsTable.= '</td>';
            }

            $sharedRoomsTable.= '<td align="center" id="snbroom_' . $nbRoomType . '" roombeds="' . $nb_guest_per_room . '">
                <div title="" style="font-weight : 600;">
                <span class="complete" complete="' . ($nb_guest_per_room * floor($availableBeds / $nb_guest_per_room)) . '" not_complete="' . $availableBeds . '">' . $availableBeds . '</span> 
                ' . ($availableBeds == 1 ? _('Guest') : _('Guests') ) . ' 
                </div>
                <a class="title" href="#" title="' . _('Availability') . '|' . $dormText . '">
                <span class="complete" style="font-size : 11px; margin-top : 5px;" complete="' . floor($availableBeds / $nb_guest_per_room) . '" not_complete="' . ceil($availableBeds / $nb_guest_per_room) . '">' . ceil($availableBeds / $nb_guest_per_room) . '</span> 
                ' . ( (ceil($availableBeds / $nb_guest_per_room) == 1) ? _('Dorm') : _('Dorms') ) . '
                </a>
                </td>';

            //TODO javascript to prevent more than maxPax booking by adding select box value

            $sharedRoomsTable.= '<td align="center">';

            //If number of night avaible of the room is higher or equal to the property min night condition dispay the room selection menu
            if ($num_nights_available_of_room >= $booking_info->minNights) {

                // If number of nights of the room is equal to total number of nights then fully available else paritally available
                if ($num_nights_available_of_room == $numNights) {

                    $sharedRoomsTable.= "<input type=\"hidden\" name=\"book-roomPreferences[]\" value=\"" . $hostel_room_type['roomTypeCode'] . "\" />";
                    $sharedRoomsTable.= "<input type=\"hidden\" name=\"book-roomType[]\" value=\"" . $hostel_room_type['roomType'] . "\" />";
                    $sharedRoomsTable.= "<input type=\"hidden\" name=\"book-roomDesc[]\" value=\"" . $hostel_room_type['roomTypeDescription'] . "\" />";

                    if (!empty($hostel_room_type['roomTypeDescriptionTranslated'])) {
                        $sharedRoomsTable.= "<input type=\"hidden\" name=\"book-roomDescTrans[]\" value=\"" . $hostel_room_type['roomTypeDescriptionTranslated'] . "\" />";
                    } else {
                        $sharedRoomsTable.= "<input type=\"hidden\" name=\"book-roomDescTrans[]\" value=\"\" />";
                    }

                    $sharedRoomsAvailable++;

                    $sharedRoomsTable.= "<select id=\"sharedsel_" . $nbRoomType . "\" class=\"sharedsel\" name=\"book-nbPersons[]\" style=\"width:150px; color:#3087C9;\">";

                    $sharedRoomsTable.= "<option value=\"0\">" . _('Select') . "</option>\n";
                    $sharedRoomsTable.= "<option value=\"0\">0</option>\n";

                    for ($p = 1; $p <= $availableBeds; $p++) {

                        $selection_title = '';

                        if ($p % $nb_guest_per_room == 0) {

                            if (($p / $nb_guest_per_room) == 1) {
                                $selection_title = _('Availability') . ' | ' . sprintf(gettext('You will use 1 full dorm.'));
                            } else {
                                $selection_title = _('Availability') . ' | ' . sprintf(gettext('You will use %d full dorms.'), (int) ($p / $nb_guest_per_room));
                            }
                            $sharedRoomsTable.= "<option value=\"$p\" complete=\"true\" selection_title=\"$selection_title\">" . sprintf(gettext('%d %s ( %s )'), (int) $p, ( $p == 1 ? _('Guest') : _('Guests')), $currency_formin . ( $subtotal * $p )) . "</option>\n";
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
                            $sharedRoomsTable.= "<option value=\"$p\" complete=\"false\" selection_title=\"$selection_title\">" . sprintf(gettext('%d %s ( %s )'), (int) $p, ( $p == 1 ? _('Guest') : _('Guests')), $currency_formin . ( $subtotal * $p )) . "</option>\n";
                        }
                    }
                    $sharedRoomsTable.= "</select>";
                } else {
                    $sharedRoomsTable.= '<a class="ajaxTable" href="#sajaxTable' . $ajaxTableID . '" rel="#sajaxTable' . $ajaxTableID . '" style="display : block; padding : 5px;" title="' . $dormTitle . '">' . _('Partially Available') . '</a>';
                }
            } else {
                $sharedRoomsTable.= '<a class="ajaxTable" href="#sajaxTable' . $ajaxTableID . '" rel="#sajaxTable' . $ajaxTableID . '" style="display : block; padding : 5px;" title="' . $dormTitle . '">' . _('Partially Available') . '</a>';
            }

            $sharedRoomsTable.= "</td>";
            $sharedRoomsTable.= "</tr>\n";
            $sharedRoomsTableSelect.= "</tr>\n";
            if ($numNights > 13) {
                if ($numNights % 2 != 0) {
                    $sharedRoomsCluetipTable .= "<td class='last'></td>";
                } else {
                    $sharedRoomsCluetipTable .= "<td></td>";
                    $sharedRoomsCluetipTable .= "<td class='last'></td>";
                }
            }
            $sharedRoomsCluetipTable.= "</tr></table>";
        }
        $ajaxTableID++;
    }

    $sharedRoomsTable .= '<tr class="no_dorms"><td class="first" colspan="' . ( ($numNights != 1) ? 6 : 5 ) . '">' . _("No dorms available") . '</td></tr>';

    //Count shared room that are displayed
    $sharedRoomCount = $nbRoomType;

    //Show private rooms
    $nbRoomType = 0;
    $privateRoomsAvailable = 0;
    $privateRoomsTable = "";
    $privateRoomsCluetipTable = "";
    $privateRoomsTableSelect = "";
    $ajaxTableID = 0;

    foreach ($distinctRoomTypes as $hostel_room_type) {

        $availableBeds = $maxPersons;
        $availableRooms = $maxPersons;

        //Show private rooms with beds increment lower than maxpax because if it is higher it will be a group booking and will cause problem on booking
        if (($maxPersons > $hostel_room_type["bedsIncrement"]) && (substr_count($hostel_room_type['roomType'], "Private") > 0)) {

            $date->modify("-$numNights day");
            $nbRoomType++;

            if ($nbRoomType % 2 == 0) {
                $privateRoomsTable.= "<tr class=\"room_row\">";
                $privateRoomsTableSelect.= "<tr class=\"roomnb\" id=\"proomnb_" . $nbRoomType . "\">";
            } else {
                $privateRoomsTable.= "<tr class=\"odd room_row\">";
                $privateRoomsTableSelect.= "<tr id=\"proomnb_" . $nbRoomType . "\" class=\"roomnb\">";
            }

            $privateRoomsCluetipTable .= '<table class="ajaxTable" id="pajaxTable' . $ajaxTableID . '"><tr>';

            $_date = clone $dateStart;

            for ($i = 0; $i < $numNights; $i++) {

                if ($i == ($numNights - 1)) {
                    $privateRoomsCluetipTable .= "<th class='last'>";
                } else {
                    if (floor($numNights / 2) - 1) {
                        $privateRoomsCluetipTable .= "<th class='last'>";
                    } else {
                        $privateRoomsCluetipTable .= "<th>";
                    }
                }

                $privateRoomsCluetipTable .= my_mb_ucfirst(mb_substr(strftime("%A", $_date->format('U')), 0, 3, 'UTF-8'));
                $privateRoomsCluetipTable .= strftime("<br /> %d", $_date->format('U'));
                $privateRoomsCluetipTable .= "</th>";

                if ($numNights > 13) {
                    if ($i == floor($numNights / 2)) {
                        $privateRoomsCluetipTable .= "</tr><tr>";
                    }
                }
                $_date->modify("+1 day");
            }

            if ($numNights > 13) {
                if ($numNights % 2 != 0) {
                    $privateRoomsCluetipTable .= "<th class='last'></th>";
                } else {
                    $privateRoomsCluetipTable .= "<th></th>";
                    $privateRoomsCluetipTable .= "<th class='last'></th>";
                }
            }

            $privateRoomsCluetipTable .= "</tr><tr>";

            $date = clone $dateStart;
            $subtotal = 0;
            $num_nights_available_of_room = 0;
            $lowest_night = '';
            $lowest_style = '';
            $sum_available = 0;
            $currency_formin = '';

            $roomTitle_PP = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;( ' . _('Price per Person') . ' )';
            $roomTitle_PR = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;( ' . _('Price per Room') . ' )';

            for ($i = 0; $i < $numNights; $i++) {

                $date_available = false;
                $date_msg = '<span class="na-book price" title="' . _('No private room available') . '">0</span>';

                foreach ($hostel_room_type['AvailableDates']['AvailableDate'] as $date_ok) {
                    if ($date_ok['date'] == $date->format("Y-m-d")) {

                        $price_array = explode(' ', $date_ok['price']);

                        if ($price_array[0] == 'From') {
                            $price = $price_array[1];
                        } else {
                            $price = $price_array[0];
                        }

                        $subtotal = $subtotal + $price;

                        $currency_formin = currency_symbol($date_ok['currency']) . " ";
                        $sum_available += $price * (int) $hostel_room_type['bedsIncrement'];

                        $privatereservationTable.= '<tr class="preservation preservation_' . $nbRoomType . '">
                            <td class="first">' . date_conv($date_ok['date'], $this->wordpress->get_option('aj_date_format')) . '</td>
                            <td><p>' . $hostel_room_type['roomTypeDescriptionTranslated'] . ' </p> (' . $hostel_room_type['roomTypeDescription'] . ')</td>
                            <td>' . currency_symbol($date_ok['currency']) . ' <span>' . $price . '</span></td>
                            <td><span></span></td>
                            <td class="" style="text-align : right;">' . currency_symbol($date_ok['currency']) . ' <span></span></td>
                            </tr>';

                        if ($min_price_private == $price) {

                            $lowest_night = _('Lowest night:') . ' <span style="color: #6DA903;">' . $currency_formin . ' ' . number_format($min_price_private, 2, '.', '') . '</span>';
                            $lowest_style = 'style="color: #6DA903;"';

                            $roomTitle_PP = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;( ' . _('Price per Person') . ' )' . ' &nbsp;&nbsp;  ' . _('Lowest night:') . ' ' . $currency_formin . ' ' . $min_price_private;
                            $roomTitle_PR = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;( ' . _('Price per Room') . ' )';
                        } else {

                            $lowest_style = '';
                        }

                        $date_msg = currency_symbol($date_ok['currency']) . ' <span class="price private" per_person="' . number_format($price, 2, '.', '') . '" per_room="' . number_format($price * (int) $hostel_room_type['bedsIncrement'], 2, '.', '') . '"  ' . $lowest_style . '>' . number_format($price * (int) $hostel_room_type['bedsIncrement'], 2, '.', '') . '</span>';

                        $num_nights_available_of_room++;
                    }
                }

                if ($i == 0) {
                    $privateRoomsCluetipTable.= '<td align="center" class="first" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                } else {
                    if ($i == floor($numNights / 2) + 1) {
                        $privateRoomsCluetipTable.= '<td align="center" class="first" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                    } else {
                        $privateRoomsCluetipTable.= '<td align="center" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                    }
                }

                if ($numNights > 13) {
                    if ($i == floor($numNights / 2)) {
                        $privateRoomsCluetipTable .= "</tr><tr>";
                    }
                }

                $date->modify("+1 day");
            }

            $privateRoomsTable.= '<td class="first" style="border-right: none;">';
            $privateRoomsTableSelect.= '<td class="first" style="border-right: none;">';
            if (!empty($hostel_room_type['roomTypeDescriptionTranslated'])) {
                $privateRoomsTable.= '<span class="tooltip" title="' . _("VERSION ORIGINALE :") . ' ' . $hostel_room_type['roomTypeDescription'] . '">' . $hostel_room_type['roomTypeDescriptionTranslated'] . '</span>';
                $privateRoomsTableSelect.= '<span class="tooltip" title="' . _("VERSION ORIGINALE :") . ' ' . $hostel_room_type['roomTypeDescription'] . '">' . $hostel_room_type['roomTypeDescriptionTranslated'] . '</span>';
            } else {
                $privateRoomsTable.= $hostel_room_type['roomTypeDescription'];
                $privateRoomsTableSelect.= $hostel_room_type['roomTypeDescription'];
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
            if ($hostel_room_type["bedsIncrement"] == 1) {
                $privateRoomsTable.= '<td align="center" width="50" title="' . sprintf(gettext('This type of bedroom can accommodate up to 1 guest per room.')) . '"><span class="nbpeople-table icon-nbpeople">' . (($hostel_room_type["bedsIncrement"] > 1) ? ('<span>' . $hostel_room_type["bedsIncrement"] . '</span>') : ('<span>' . $hostel_room_type["bedsIncrement"] . '</span>')) . '</span></td>';
                $privateRoomsTableSelect.= '<td align="center" width="50" title="' . sprintf(gettext('This type of bedroom can accommodate up to 1 guest per room.')) . '"><span class="nbpeople-table icon-nbpeople">' . (($hostel_room_type["bedsIncrement"] > 1) ? ('<span>' . $hostel_room_type["bedsIncrement"] . '</span>') : ('<span>' . $hostel_room_type["bedsIncrement"] . '</span>')) . '</span></td>';
            } else {
                $privateRoomsTable.= '<td align="center" width="50" title="' . sprintf(gettext('This type of bedroom can accommodate up to %d guests per room.'), (int) $hostel_room_type["bedsIncrement"]) . '"><span class="nbpeople-table icon-nbpeople">' . (($hostel_room_type["bedsIncrement"] > 1) ? ('<span>' . $hostel_room_type["bedsIncrement"] . '</span>') : ('<span>' . $hostel_room_type["bedsIncrement"] . '</span>')) . '</span></td>';
                $privateRoomsTableSelect.= '<td align="center" width="50" title="' . sprintf(gettext('This type of bedroom can accommodate up to %d guests per room.'), (int) $hostel_room_type["bedsIncrement"]) . '"><span class="nbpeople-table icon-nbpeople">' . (($hostel_room_type["bedsIncrement"] > 1) ? ('<span>' . $hostel_room_type["bedsIncrement"] . '</span>') : ('<span>' . $hostel_room_type["bedsIncrement"] . '</span>')) . '</span></td>';
            }

            $display_currency = currency_symbol($date_ok['currency']);
            $privateRoomsTableSelect.= '<td id="pnbrooms_' . $nbRoomType . '" align="center" class="pnbrooms"><a href="" class="title" title="' . _('Availability') . ' | ' . sprintf(gettext('Dorm Availability')) . '">' . _('Bedrooms you will occupy:') . ' <strong></strong></a></td>';
            $privateRoomsTableSelect.= '<td id="pnbguest_' . $nbRoomType . '" align="center" class="pnbguest"><strong></strong> x <span  class="nbpeople-table icon-nbpeople nbpeople-1"></span></td>';
            $privateRoomsTableSelect.= '<td align="center" id="psubtotal_' . $nbRoomType . '" class="psubtotal"><span class="calc_init" id="psubtotal_init_' . $nbRoomType . '">' . number_format($subtotal, 2, '.', '') . '</span>' . $display_currency . '  <span class="calc_sum" id="psubtotal_calc_' . $nbRoomType . '"></span></td>';

            foreach ($hostel_room_type['AvailableDates']['AvailableDate'] as $avail_date) {
                if ((int) $avail_date['availableBeds'] < $availableBeds) {
                    $availableBeds = (int) $avail_date['availableBeds'];
                }

                if ((int) $avail_date['availableRooms'] < $availableRooms) {
                    $availableRooms = (int) $avail_date['availableRooms'];
                }
            }

            $nb_guest_per_room = explode(":", $hostel_room_type['roomTypeCode']);
            $nb_guest_per_room = $nb_guest_per_room[0];

            $availableBeds = (int) (floor($availableBeds / $nb_guest_per_room) * $nb_guest_per_room);

            $roomText = '';

            if (($availableBeds == 1) && ceil($availableBeds / $nb_guest_per_room) == 1) {
                $roomText = sprintf(gettext('1 guest in 1 Bedroom.'));
            } elseif ($availableBeds > 1 && ceil($availableBeds / $nb_guest_per_room) == 1) {
                $roomText = ($nb_guest_per_room > 1 ) ? sprintf(gettext('%d guests in 1 Bedroom.'), (int) $availableBeds) : sprintf(gettext('%d guests in %d Bedroom (1 guest in each room).'), (int) $availableBeds, (int) ceil($availableBeds / $nb_guest_per_room));
            } else {
                $roomText = ($nb_guest_per_room > 1 ) ? sprintf(gettext('%d guests in %d Bedrooms (%d guests in each room).'), (int) $availableBeds, (int) ceil($availableBeds / $nb_guest_per_room), (int) $nb_guest_per_room) : sprintf(gettext('%d guests in %d Bedrooms (%d guest in each room).'), (int) $availableBeds, (int) ceil($availableBeds / $nb_guest_per_room), (int) $nb_guest_per_room);
            }

            if ($numNights != 1) {
                $privateRoomsTable.= '<td align="center" title="">';
                $privateRoomsTable.= '<a class="ajaxTable per_tooltip" href="#pajaxTable' . $ajaxTableID . '" rel="#pajaxTable' . $ajaxTableID . '" style="display : block;" title="' . $roomTitle_PP . '"  per_person="' . $roomTitle_PP . '"  per_room="' . $roomTitle_PR . '">';
                $privateRoomsTable.= '<strong>' . $currency_formin . '</strong> <span class="private" style="font-weight: bold;" per_person="' . number_format(round((float) ($sum_available / $num_nights_available_of_room) / $hostel_room_type["bedsIncrement"], 2), 2) . '" per_room="' . number_format(round((float) ($sum_available / $num_nights_available_of_room), 2), 2) . '">' . number_format(($sum_available / $num_nights_available_of_room), 2, '.', '') . '</span>';
                if ($lowest_night != '') {
                    $lowest_title = sprintf(gettext('The lowest price per person per night in a private room at this property: %s'), $display_currency . ' ' . $min_price_private);
                    $privateRoomsTable.= '<span class="lowest_night" style="display: block; float: none;">' . $lowest_night . '</span>';
                }
                $privateRoomsTable.= '</a>';
                $privateRoomsTable.= '</td>';
            } else {
                $privateRoomsTable.= '<td align="center" style="font-weight: bold;" title="">';
                $privateRoomsTable.= $currency_formin . ' <span class="private" per_person="' . number_format(round((float) ($sum_available / $num_nights_available_of_room) / $hostel_room_type["bedsIncrement"], 2), 2) . '" per_room="' . number_format(round((float) ($sum_available / $num_nights_available_of_room), 2), 2) . '">' . number_format(($sum_available / $num_nights_available_of_room), 2, '.', '') . '</span>';
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

            //TODO javascript to prevent more than maxPax booking by adding select box vfalue
            $privateRoomsTable.= '<td align="center">';

            //If number of night avaible of the room is higher or equal to the property min night condition dispay the room selection menu
            if ($num_nights_available_of_room >= $booking_info->minNights) {

                // If number of nights of the room is equal to total number of nights then fully available else paritally available
                if ($num_nights_available_of_room == $numNights) {

                    $privateRoomsTable.= "<input type=\"hidden\" name=\"book-roomPreferences[]\" value=\"" . $hostel_room_type['roomTypeCode'] . "\" />";

                    $privateRoomsTable.= "<input type=\"hidden\" name=\"book-roomType[]\" value=\"" . $hostel_room_type['roomType'] . "\" />";
                    $privateRoomsTable.= "<input type=\"hidden\" name=\"book-roomDesc[]\" value=\"" . $hostel_room_type['roomTypeDescription'] . "\" />";

                    if (!empty($hostel_room_type['roomTypeDescriptionTranslated'])) {
                        $privateRoomsTable.= "<input type=\"hidden\" name=\"book-roomDescTrans[]\" value=\"" . $hostel_room_type['roomTypeDescriptionTranslated'] . "\" />";
                    } else {
                        $privateRoomsTable.= "<input type=\"hidden\" name=\"book-roomDescTrans[]\" value=\"\" />";
                    }

                    $privateRoomsAvailable++;

                    $privateRoomsTable.= "<select id=\"privatesel_" . $nbRoomType . "\" class=\"privatesel\" name=\"book-nbPersons[]\" style=\"width:150px; color:#3087C9;\">";
                    $privateRoomsTable.= "<option value=\"0\">" . _('Select') . "</option>\n";
                    $privateRoomsTable.= "<option value=\"0\">0</option>\n";

                    for ($p = 1; $p <= $availableRooms; $p++) {
                        if ($p * $hostel_room_type['bedsIncrement'] <= $maxPersons) {

                            if ($p == 1) {
                                if (($p * $hostel_room_type['bedsIncrement']) == 1) {
                                    $selection_title = _('Availability') . ' | ' . sprintf(gettext('1 guest in 1 Bedroom.'));
                                } else {
                                    $selection_title = _('Availability') . ' | ' . sprintf(gettext('%d guests in 1 Bedroom.'), (int) $p * $hostel_room_type['bedsIncrement']);
                                }
                            } else {
                                if (($hostel_room_type['bedsIncrement']) == 1) {
                                    $selection_title = _('Availability') . ' | ' . sprintf(gettext('%d guests in %d Bedrooms (1 guest in each room).'), (int) $p * $hostel_room_type['bedsIncrement'], (int) $p);
                                } else {
                                    $selection_title = _('Availability') . ' | ' . sprintf(gettext('%d guests in %d Bedrooms (%d guests in each room).'), (int) $p * $hostel_room_type['bedsIncrement'], (int) $p, (int) $hostel_room_type['bedsIncrement']);
                                }
                            }
                            $privateRoomsTable.= "<option value=\"" . $p * $hostel_room_type['bedsIncrement'] . "\" selection_title=\"" . $selection_title . "\">" . sprintf(gettext('%d %s ( %s )'), ( $p * (int) $hostel_room_type['bedsIncrement']), ( ($p * (int) $hostel_room_type['bedsIncrement']) == 1 ? _('Guest') : _('Guests')), $currency_formin . ( $sum_available * $p )) . "</option>\n";
                        }
                    }
                    $privateRoomsTable.= "</select>";
                } else {
                    $privateRoomsTable.= '<a class="ajaxTable per_tooltip" href="#pajaxTable' . $ajaxTableID . '" rel="#pajaxTable' . $ajaxTableID . '" style="display : block; padding : 5px;" title="' . $roomTitle_PP . '"  per_person="' . $roomTitle_PP . '"  per_room="' . $roomTitle_PR . '">' . _('Partially Available') . '</a>';
                }
            } else {
                $privateRoomsTable.= '<a class="ajaxTable per_tooltip" href="#pajaxTable' . $ajaxTableID . '" rel="#pajaxTable' . $ajaxTableID . '" style="display : block; padding : 5px;" title="' . $roomTitle_PP . '"  per_person="' . $roomTitle_PP . '"  per_room="' . $roomTitle_PR . '">' . _('Partially Available') . '</a>';
            }

            $privateRoomsTable.= "</td>";

            $privateRoomsTable.= "</tr>\n";
            $privateRoomsTableSelect.= "</tr>\n";
            if ($numNights > 13) {
                if ($numNights % 2 != 0) {
                    $privateRoomsCluetipTable .= "<td class='last'></td>";
                } else {
                    $privateRoomsCluetipTable .= "<td></td>";
                    $privateRoomsCluetipTable .= "<td class='last'></td>";
                }
            }
            $privateRoomsCluetipTable .= "</tr></table>";
        }
        $ajaxTableID++;
    }

    $privateRoomsTable .= '<tr class="no_rooms"><td class="first" colspan="' . ( ($numNights != 1) ? 6 : 5 ) . '">' . _("No private room available") . '</td></tr>';

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
            <?php
            if ($this->session->userdata('switch_api')) {
                echo form_hidden('api_shortname', 'hw');
            }
            ?>
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
                        <td class="first" colspan="<?php echo ($numNights != 1) ? 6 : 5; ?>">
                            <?php echo _("No dorms available"); ?>
                        </td>
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
                        <td class="first" colspan="<?php echo ($numNights != 1) ? 6 : 5; ?>">
                            <?php echo _("No private room available"); ?>
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
                        <th class="title" colspan="3" width="275">
                            <?php echo anchor('#', _('Your Selection'), array('class' => 'title', 'style' => 'display: block; float: left;', 'title' => _('Notes Importantes') . ' | ' . sprintf(gettext("You only pay the deposit (%d%% of total amount) to confirm and secure your reservation now. The remaining amount (%d%%) is payable upon arrival. You will find the hotel's contact information (email, address, telephone number…) in your confirmation email after you have made your reservation."), (int) $booking_info->depositPercent, (int) 100 - $booking_info->depositPercent))); ?>
                            <span style="float: right; margin-right: 20px;">
                                <?php echo _('Arrivée'); ?> : <b><?php echo $datetop; ?> </b> &nbsp;&nbsp; <?php echo _('Nombre de Nuits'); ?> : <b><?php echo $numNights; ?> </b>
                            </span>
                        </th>
                        <th><?php echo _("Number of guests selected"); ?></th>
                        <th class="last">
                            <?php echo _("Price"); ?>
                        </th>
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
                        <td align="center"><?php echo is_array($display_currency) ? currency_symbol($display_currency[0]) : $display_currency; ?> <strong id="bigTotal">0.00</strong></td>
                    </tr>
                    <tr>
                        <td class="first" align="right" colspan="4"><span class="best_price left"><?php echo _('You got the best price') ?></span><strong class="right deposit_bottom"><?php echo sprintf(gettext('Deposit to be paid now (%d%%)'), (int) $booking_info->depositPercent); ?>: </strong></td>
                        <td align="center"><?php echo is_array($display_currency) ? currency_symbol($display_currency[0]) : $display_currency; ?> <strong id="depositTotal">0.00</strong></td>
                    </tr>
                </tbody>
            </table>

            <input type="hidden" name="book-propertyName" value="<?php echo $propertyName; ?>" />
            <input type="hidden" name="book-propertyNumber" value="<?php echo $propertyNumber; ?>" />
            <input type="hidden" name="book-dateStart" value="<?php echo $dateStart->format('Y-m-d'); ?>" />
            <input type="hidden" name="book-numNights" value="<?php echo $numNights; ?>" />
            <input type="hidden" name="book-currency" value="<?php echo $currency; ?>" />

            <?php
            if (($privateRoomsAvailable + $sharedRoomsAvailable) > 0) {
                ?>
                <div class="bottom-table group" id="book-now">
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
                                <?php if (!empty($print)) { ?>
                                    <strong id="booking-form-submit"><?php echo _('PLEASE NOTE THIS IS NOT A CONFIRMED BOOKING'); ?></strong>
                                <?php } else { ?>
                                    <input type="submit" onfocus="this.blur()" name="booking-form" id="booking-form-submit" class="button-green box_round hoverit" value="<?php echo _("Réserver Maintenant"); ?>" />
                                <?php } ?>
                                <img src="<?php echo site_url(); ?>images/padlock.png" alt="<?php echo _("sécurisé"); ?>" /> 
                            </td>
                        </tr>
                    </table>
                </div>
                <script>
                                    $(function() {
                                        $("#dispo-form").hide();
                                        $("#change-dates").show();
                                    });
                </script>
                <?php
            } else {
                ?>
                <p class="orange-error"><?php echo _('No rooms are available for all the nights you selected.'); ?></p>
                <?php
            }
            ?>

        </form>

        <p class="red-error" id="formerror"><?php echo _('Please enter at least one choice in the above table to book a room.'); ?></p>

    <?php }
    ?>

    <?php
} elseif ($api_error_msg == false) {
    echo _('Serveur inaccessible en ce moment.');
} else {
    ?>
    <div class="dispo-error group">
        <img class="arrow-error" src="<?php echo site_url(); ?>images/V2/arrow-error.png" alt="" />
        <div<?php
        if ($api_error_msg->message == 'No Beds Found') {
            echo ' class="half"';
        }
        ?>>

            <h3>
                <?php
                if (!empty($api_error_msg->messageTranslated)) {
                    echo $api_error_msg->messageTranslated;
                } else {
                    echo $api_error_msg->message;
                }
                ?>
            </h3>

            <p><strong><?php echo _("Détails:"); ?> </strong>
                <?php
                if (!empty($api_error_msg->detailTranslated)) {
                    echo $api_error_msg->detailTranslated;
                } else {
                    echo $api_error_msg->detail;
                }
                ?>
            </p>
        </div>
        <?php if ($api_error_msg->message == 'No Beds Found') { ?>
            <?php $dateurl = $dateStart->format('Y-m-d'); ?>
            <a class="alternative button-green hoverit box_round" href="<?php echo site_url(); ?><?php echo $country_selected; ?>/<?php echo $city_selected; ?>/<?php echo $dateurl; ?>/<?php echo $numNights; ?>">
                <?php printf(gettext('Search for more properties in %s'), $city_selected); ?></a>
            <?php
        }
        ?>
    </div>
    <?php
}
?>
<?php
$csspath = $this->wordpress->get_option('aj_api_ascii');
if (empty($csspath)) {
    $csspath = $this->wordpress->get_option('aj_api_name');
}
?>

<?php if (empty($print)) { ?>
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
                    <th class="first-cell green-th"><?php echo _('Date'); ?></th>
                    <th class="green-th"><?php echo _('Chambres partagées - Dortoirs'); ?></th>
                    <th class="green-th">
                        <?php echo _('Prix (lit)'); ?>										
                    </th>
                    <th class="green-th"><?php echo _('Number of guests'); ?></th>
                    <th width="18%" class="last-cell green-th" style="text-align : right;"><?php echo _('Total'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php echo $sharedreservationTable; ?>
            </tbody>
        </table>

        <table id="privateemailreservationView" class="emailpreview" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
            <thead>
                <tr valign="middle" align="center">
                    <th class="first-cell green-th"><?php echo _('Date'); ?></th>
                    <th class="green-th"><?php echo _('Chambres privées'); ?></th>
                    <th class="green-th">
                        <?php echo _('Prix (lit)'); ?>										
                    </th>
                    <th class="green-th"><?php echo _('Number of guests'); ?></th>
                    <th width="18%" class="last-cell green-th" style="text-align : right;"><?php echo _('Total'); ?></th>
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
                            <?php echo _('Total'); ?>:
                        </strong>
                    </p>
                </td>
                <td width="120" style="border:none; color: black; padding:4.5pt 6.75pt 4.5pt 6.75pt">
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
                            <?php echo sprintf(gettext('Deposit (%d%%)'), (int) $booking_info->depositPercent); ?>:
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
<?php } ?>
<br />

<?php
echo isset($sharedRoomsCluetipTable) ? $sharedRoomsCluetipTable : '';
echo isset($privateRoomsCluetipTable) ? $privateRoomsCluetipTable : '';
?>

<script type="text/javascript" src="<?php echo base_url(); ?>js/calcprice.js"></script>
<script type="text/javascript">

    $('a.basic-modal').bind('click', function() {

        var srows = 0;

        $('table tr[class^="sreservation sreservation_"] td:nth-child(4) span').each(function() {

            if (parseInt($(this).html()) > 0) {
                srows++;
            } else {
                $(this).parent().parent().hide();
            }

        });

        if (srows == 0) {
            $('table#sharedemailreservationView').hide();
        }

        var prows = 0;

        $('table tr[class^="preservation preservation_"] td:nth-child(4) span').each(function() {

            if (parseInt($(this).html()) > 0) {
                prows++;
            } else {
                $(this).parent().parent().hide();
            }

        });

        if (prows == 0) {
            $('table#privateemailreservationView').hide();
        }

        $('#basic-modal-content').modal();

        return false;

    });

    var sharedrowCount = $('table#sharedemailreservationView tr').length;
    if (sharedrowCount > 1) {
        $("table#sharedemailreservationView").tablesorter({
            // sort on the first column and third column, order asc 
            sortList: [[0, 0]]
        });
    }


    var privaterowCount = $('table#privateemailreservationView tr').length;
    if (privaterowCount > 1) {
        $("table#privateemailreservationView").tablesorter({
            // sort on the first column and third column, order asc 
            sortList: [[0, 0]]
        });
    }

    $('table#sharedemailreservationView tr.preservation').each(function() {
        $(this).hide();
    });

    $('table#privateemailreservationView tr.preservation').each(function() {
        $(this).hide();
    });

    $('table#sharedemailreservationView tr.sreservation').each(function() {
        $(this).hide();
    });

    $('table#privateemailreservationView tr.sreservation').each(function() {
        $(this).hide();
    });

    $('div.confirmationEmail').hide();

    $('a.title').cluetip({
        width: '400px',
        splitTitle: '|',
        local: true,
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
        width: '640px',
        local: true,
        cursor: 'pointer',
        arrows: false,
        dropShadow: false,
        sticky: false,
        positionBy: 'bottomTop',
        cluetipClass: 'mcweb',
        tracking: true,
        topOffset: 10
    });

    $(function() {
        $("#booking-table").show();
    });

    $("#booking-table form").submit(function() {

        var noerror = false;

        $("#formerror").hide();
        $("#booking-table select").each(function() {
            if ($(this).val() != 0) {
                noerror = true;
            }
        });

        if (noerror == true) {
            noerror = true;
            return true;
        } else {
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
        $("#dispo-form").show();
        $("#booking-table").toggle();
        return false;
    });

    $('#complete_dorms').bind('click', function() {

        if ($(this).is(':checked')) {

            $('span.complete').each(function() {
                $(this).html($(this).attr('complete'));
                if ($(this).attr('complete') == 0) {
                    $(this).parent().parent().parent().hide();
                }
            });

            $("select.sharedsel > option[complete$='false']").hide();

        } else {

            $('span.complete').each(function() {
                $(this).html($(this).attr('not_complete'));
                if ($(this).attr('complete') == 0) {
                    $(this).parent().parent().parent().show();
                }
            });

            $("select.sharedsel > option[complete$='false']").show();

        }

        var dorm_rows = $('tr.dorm_row').filter(function() {
            return this.style.display !== "none";
        }).length;

        if (dorm_rows == 0) {
            $('tr.no_dorms').show();
        } else {
            $('tr.no_dorms').hide();
        }

    });


    $('#fully_available').bind('click', function() {

        if ($(this).is(':checked')) {

            $("tr").find("td:eq(4):contains('" + $("input[type='hidden'][name='partially_available']").val() + "')").each(function(i, v) {
                $(v).parent().hide();
            });

        } else {

            $("tr").find("td:eq(4):contains('" + $("input[type='hidden'][name='partially_available']").val() + "')").each(function(i, v) {
                $(v).parent().show();
            });

        }

        var dorm_rows = $('tr.dorm_row').filter(function() {
            return this.style.display !== "none";
        }).length;

        if (dorm_rows == 0) {
            $('tr.no_dorms').show();
        } else {
            $('tr.no_dorms').hide();
        }

        var room_rows = $('tr.room_row').filter(function() {
            return this.style.display !== "none";
        }).length;

        if (room_rows == 0) {
            $('tr.no_rooms').show();
        } else {
            $('tr.no_rooms').hide();
        }

    });


    if ($('input[name="price_selection"]').val() == 'per_person') {

        $('span.private').each(function() {
            $(this).html($(this).attr('per_person'));
        });

        $('th.per_title').each(function() {
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

        if ($(this).val() == 'per_person') {

            $('span.private').each(function() {
                $(this).html($(this).attr('per_person'));
            });

            $('th.per_title').each(function() {
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
            local: true,
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
    $('tr.no_rooms').hide();

    $('table.ajaxTable').each(function() {
        $(this).hide();
    });

    $('table.ajaxTable').find('tr').each(function() {
        if ($(this).index() == 2) {
            $(this).insertBefore($(this).prev());
        }
    });

</script>
