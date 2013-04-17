<?php
$randProperty = rand(100000, 999999);
echo form_hidden('partially_available', _('Partially Available'));
?>
<?php if ($property_api == 'HW'): ?>
    <span style="float: right; padding-bottom: 5px; padding-top: 5px;">
        <?php
        $data = array(
            'name' => 'fully_available_' . $randProperty,
            'id' => 'fully_available_' . $randProperty,
            'value' => 'accept',
            'checked' => FALSE,
            'style' => '',
        );
        echo form_label(anchor('#', _('Show fully available only'), array('class' => 'title', 'title' => _('Show fully available only') . ' | ' . _('We are displaying rooms with both full and partial availability for your dates. You can select this filter to only see rooms with full availability for your stay.'))) . ' &nbsp; ' . form_checkbox($data), 'fully_available_' . $randProperty);
        ?>
    </span>
<?php endif; ?>
<script type="text/javascript">
    $('a.show-room-info').click(function() {
        return false;
    });
    $('a.show-room-info').mouseover(function() {
        $(this).next().show();
    });
    $('a.show-room-info').mouseleave(function() {
        $(this).next().hide();
    });
</script>
<style type="text/css">

    a.sharedTable:hover {
        text-decoration: underline;
    }

    a.privateTable:hover {
        text-decoration: underline;
    }

    a.sharedTable p:hover {
        text-decoration: underline;
    }

    a.privateTable span:hover {
        text-decoration: underline;
    }

</style>
<?php
$min_price_shared = 0;
$min_price_private = 0;

$max_guest_per_unity_enable = false;
if (!empty($property_rooms["sharedRooms"]) &&
        !empty($property_rooms["sharedRooms"][0]['max_guest_per_unity'])) {
    $max_guest_per_unity_enable = true;
}

if (!empty($property_rooms["sharedRooms"])) {
    foreach ($property_rooms["sharedRooms"] as $chepestroom) {

        $cheapestdate = clone $dateStart;

        for ($i = 0; $i < $numNights; $i++) {

            if (!empty($chepestroom["availableDates"][$cheapestdate->format("Y-m-d")])) {

                if ($min_price_shared == 0) {
                    $min_price_shared = $chepestroom["availableDates"][$cheapestdate->format("Y-m-d")]["price"];
                } elseif ($min_price_shared > $chepestroom["availableDates"][$cheapestdate->format("Y-m-d")]["price"]) {
                    $min_price_shared = $chepestroom["availableDates"][$cheapestdate->format("Y-m-d")]["price"];
                }
            }

            $cheapestdate->modify("+1 day");
        }
    }
}

if (!empty($property_rooms["privateRooms"])) {
    foreach ($property_rooms["privateRooms"] as $chepestroom) {

        $cheapestdate = clone $dateStart;

        for ($i = 0; $i < $numNights; $i++) {

            if (!empty($chepestroom["availableDates"][$cheapestdate->format("Y-m-d")])) {

                if ($min_price_private == 0) {
                    $min_price_private = $chepestroom["availableDates"][$cheapestdate->format("Y-m-d")]["price"] / $chepestroom['max_guest_per_unity'];
                } elseif ($min_price_private > ($chepestroom["availableDates"][$cheapestdate->format("Y-m-d")]["price"] / $chepestroom['max_guest_per_unity'])) {
                    $min_price_private = $chepestroom["availableDates"][$cheapestdate->format("Y-m-d")]["price"] / $chepestroom['max_guest_per_unity'];
                }
            }

            $cheapestdate->modify("+1 day");
        }
    }
}
?>

<div class="avail-wrap">
    <table border="0" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <th class="title" colspan="2">
                    <?php echo anchor('#', _('Type of dorms'), array('class' => 'title', 'style' => 'margin-top: 5px; display: block; float: left;', 'title' => _('Chambres partagées - Dortoirs') . ' | ' . _('You must share the room (unless you purchase all the beds in the dorm).'))); ?>
        <div style="text-align: right; float: right; font-size: 11px; font-weight: normal;">
            <?php
            $data = array(
                'name' => 'complete_dorms',
                'id' => 'complete_dorms',
                'value' => 'accept',
                'checked' => FALSE,
                'style' => '',
            );
            echo form_label(anchor('#', _('Dorms for your group only'), array('class' => 'title', 'style' => '', 'title' => _('Complete dorm only') . ' | ' . _('We will only show you dorms you can fully book. You will need to take all the beds in that particular dorm even if you do not need all of them. This is often used by groups.'))) . ' &nbsp; ' . form_checkbox($data), 'complete_dorms');
            ?>
        </div>
        </th>
        <?php if ($numNights != 1): ?>
            <th width="180">
                <?php echo anchor('#', _('Average per night per person'), array('class' => 'title', 'title' => _('Average Price') . ' | ' . _('Average price per night for requested arrival date and length of stay. Actual day-by-day prices may vary. However, our prices are quaranteed and you will never pay more.'))); ?>
            </th>
        <?php else: ?>
            <th width="150"><?php echo _('Price'); ?></th>
        <?php endif; ?>
        <th class="last" width="150"><?php echo _('Maximum'); ?></th>
        </tr>

        <?php
        $sharedRoomsCluetipTable = "";
        $sharedRoomsID = 0;

        if (!empty($property_rooms["sharedRooms"])) {
            foreach ($property_rooms["sharedRooms"] as $room) {

                $nb_guest_per_room = 0;

                if (isset($room['roomTypeCode']) && strpos($room['roomTypeCode'], ':') !== false) {
                    $nb_guest_per_room = explode(':', $room['roomTypeCode']);
                    $nb_guest_per_room = (int) $nb_guest_per_room[0];
                } else {
                    $nb_guest_per_room = (int) preg_replace("/[^0-9\.]/", '', $room['description']);
                }
                ?>
                <?php
                //dates columns
                $date = clone $dateStart;
                $sharedRoomsCluetipTable .= '<table class="sharedTable" id="sharedTable' . $sharedRoomsID . $randProperty . '"><tr>';

                for ($i = 0; $i < $numNights; $i++) {

                    if ($i == ($numNights - 1)) {
                        $sharedRoomsCluetipTable .= "<th class='last'>";
                    } else {
                        $sharedRoomsCluetipTable .= "<th>";
                    }

                    $sharedRoomsCluetipTable .= my_mb_ucfirst(mb_substr(strftime("%A", $date->format('U')), 0, 3, 'UTF-8'));
                    $sharedRoomsCluetipTable .= strftime("<br /> %d", $date->format('U'));
                    $date->modify("+1 day");
                    $sharedRoomsCluetipTable .= "</th>";
                }

                $sharedRoomsCluetipTable .= "</tr><tr>";
                $date = clone $dateStart;

                $display_currency = '';
                $subtotal = 0;
                $availableDays = 0;
                $lowest_night = '';
                $lowest_style = '';

                for ($i = 0; $i < $numNights; $i++) {

                    $date_msg = '<span class="na-book price" title="' . _('No dorms available') . '">0</span>';

                    if (!empty($room["availableDates"][$date->format("Y-m-d")])) {

                        $display_currency = $room["currency"];
                        $subtotal += $room["availableDates"][$date->format("Y-m-d")]["price"];

                        $currency_formin = $room["currency"];

                        if ($min_price_shared == $room["availableDates"][$date->format("Y-m-d")]["price"]) {
                            $lowest_night = _('Lowest night:') . ' ' . $room["currency"] . ' ' . number_format($min_price_shared, 2, '.', '');
                            $lowest_style = 'style="color: #6DA903;"';
                        } else {
                            $lowest_style = '';
                        }

                        $date_msg = $room["currency"] . ' <span class="price" ' . $lowest_style . '>' . number_format($room["availableDates"][$date->format("Y-m-d")]["price"], 2, '.', '') . '</span>';
                        $availableDays++;
                    }

                    if ($i == 0) {
                        $sharedRoomsCluetipTable.= '<td align="center" class="first" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                    } else {
                        $sharedRoomsCluetipTable.= '<td align="center" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                    }

                    $date->modify("+1 day");
                }

                $sharedRoomsCluetipTable .= "</tr>";
                $dormText = '';

                if (($room["availableBeds"] % $nb_guest_per_room) == 0) {
                    if (($room["availableBeds"] / $nb_guest_per_room) == 1) {
                        $dormText .= sprintf(gettext('1 fully available dorm.'));
                    } else {
                        $dormText .= sprintf(gettext('%d fully available dorms.'), (int) ($room["availableBeds"] / $nb_guest_per_room));
                    }
                } else {
                    if (floor($room["availableBeds"] / $nb_guest_per_room) == 1) {
                        $dormText .= sprintf(gettext('1 dorm fully available and 1 already partially occupied (beds already occupied: %d).'), (int) ( $nb_guest_per_room - ($room["availableBeds"] % $nb_guest_per_room)));
                    } else if (floor($room["availableBeds"] / $nb_guest_per_room) < 1) {
                        $dormText .= sprintf(gettext('This dorm is already partially occupied (beds already occupied: %d).'), (int) ( $nb_guest_per_room - ($room["availableBeds"] % $nb_guest_per_room)));
                    } else {
                        $dormText .= sprintf(gettext('%s dorms fully available and 1 dorm already partially occupied (beds already occupied: %d).'), ( floor($room["availableBeds"] / $nb_guest_per_room) != 0 ? floor($room["availableBeds"] / $nb_guest_per_room) : _('No')), (int) ( $nb_guest_per_room - ($room["availableBeds"] % $nb_guest_per_room)));
                    }
                }
                $datetop = date_conv($dateStart->format('Y-m-d'), $this->wordpress->get_option('aj_date_format'));

                $dormTitle = '';

                if ($lowest_style != '') {
                    $dormTitle = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;&nbsp;  ' . _('Lowest night:') . ' ' . $display_currency . ' ' . number_format($min_price_shared, 2, '.', '');
                } else {
                    $dormTitle = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights;
                }
                ?>
                <tr class="dorm_row_<?php echo $randProperty; ?>">
                    <td class="first">
                        <?php
                        if (!empty($room['descriptionTranslated'])) {
                            echo '<span class="tooltip" title="' . _("VERSION ORIGINALE :") . ' ' . $room['description'] . '">' . $room['descriptionTranslated'] . '</span>';
                        } else {
                            echo $room['description'];
                        }

                        if ($breakfast_included == 1) {
                            echo '<span class="free-breakfast">';
                            echo _('Breakfast Included');
                            echo '</span>';
                        }
                        ?>                        
                    </td>
                    <?php if ($nb_guest_per_room == 1): ?>
                        <td align="center" width="50" title="<?php printf(gettext('This type of dorm can accommodate up to 1 guest per dorm.')); ?>" style="border-left: none;"><span class="nbpeople-table icon-nbpeople"> <?php echo (($nb_guest_per_room > 1) ? ('<span>' . $nb_guest_per_room . '</span>') : ('<span>' . $nb_guest_per_room . '</span>')); ?> </span></td>
                    <?php else: ?>
                        <td align="center" width="50" title="<?php printf(gettext('This type of dorm can accommodate up to %d guests per dorm.'), (int) $nb_guest_per_room); ?>" style="border-left: none;"><span class="nbpeople-table icon-nbpeople"> <?php echo (($nb_guest_per_room > 1) ? ('<span>' . $nb_guest_per_room . '</span>') : ('<span>' . $nb_guest_per_room . '</span>')); ?> </span></td>
                    <?php endif; ?>                    

                    <?php if ($numNights != 1): ?>                    
                        <td align="center">
                            <span class="price">
                                <?php
                                echo '<a class="sharedTable" href="#sharedTable' . $sharedRoomsID . $randProperty . '" rel="#sharedTable' . $sharedRoomsID . $randProperty . '" style="display : block; padding : 5px;" title="' . $dormTitle . '"><span style="font-weight: 600;">' . $display_currency . ' ' . number_format(($subtotal / $availableDays), 2, '.', '') . '</span>';
                                if ($lowest_night != '') {
                                    echo '<p style="display: inline-block; font-size: 11px; clear: both;">' . $lowest_night . '</p>';
                                }
                                echo ( $availableDays != $numNights ) ? '<div>' . _('Partially Available') . '</div>' : '';
                                echo '</a>';
                                ?>
                            </span>
                        </td>
                    <?php else: ?>
                        <td align="center"><span class="price" style="font-weight: 600;"><?php echo $display_currency . ' ' . number_format(($subtotal / $availableDays), 2, '.', ''); ?></span></td>
                    <?php endif; ?>
                    <td align="center">
                        <div title="<?php echo _('Maximum number of guests per dorm'); ?>" style="font-weight : 600;">
                            <span class="complete" complete="<?php echo $nb_guest_per_room * floor($room["availableBeds"] / $nb_guest_per_room); ?>" not_complete="<?php echo $room["availableBeds"] ?>"><?php echo $room["availableBeds"] ?></span> 
                            <?php echo ($room["availableBeds"] == 1 ? _('Guest') : _('Guests')); ?>
                        </div>
                        <a class="title" href="#" title="<?php echo _('Availability') . '|' . $dormText ?>">
                            <span class="complete" style="font-size : 11px; margin-top : 5px;" complete="<?php echo floor($room["availableBeds"] / $nb_guest_per_room); ?>" not_complete="<?php echo ceil($room["availableBeds"] / $nb_guest_per_room); ?>"><?php echo ceil($room["availableBeds"] / $nb_guest_per_room); ?></span> 
                            <?php echo ((ceil($room["availableBeds"] / $nb_guest_per_room) == 1) ? _('Dorm') : _('Dorms')); ?>
                        </a>
                    </td>
                </tr>
                <?php
                $sharedRoomsCluetipTable.= "</table>";
                $sharedRoomsID++;
            }
            echo '<tr class="no_dorms"><td class="first" colspan="4">' . _("No dorms available") . '</td></tr>';
        } else {
            ?>
            <tr>
                <td class="first" colspan="<?php echo ( ($numNights != 1) ? 4 : 4 ); ?>">
                    <?php echo _("No dorms available"); ?>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <th class="title" colspan="2">
                <?php echo anchor('#', _('Type of private bedrooms'), array('class' => 'title', 'style' => 'margin-top: 4px; display: block; float: left;', 'title' => _('Type of private bedrooms') . ' | ' . _('You must pay for the whole private room, even if you do not need all the beds. The room cannot be shared.'))); ?>
        <div style="text-align: right; float: right; font-size: 11px; font-weight: normal;">
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
            <th width="180" class="">
                <?php echo anchor('#', _('Average per night per room'), array('class' => 'title per_title', 'per_room' => _('Average per night per room'), 'per_person' => _('Average per night per person'), 'title' => _('Average Price') . ' | ' . _('Average price per night for requested arrival date and length of stay. Actual day-by-day prices may vary. However, our prices are quaranteed and you will never pay more.'))); ?>
            </th>
        <?php else: ?>
            <th width="150" class="per_title" per_room="<?php echo _('Price'); ?>" per_person="<?php echo _('Price'); ?>"><?php echo _('Price'); ?></th>
        <?php endif; ?>
        <th width="" class="last"><?php echo _('Maximum'); ?></th>
        </tr>
        <?php
        $privateRoomsCluetipTable = "";
        $privateroomsID = 0;

        if (!empty($property_rooms["privateRooms"])) {
            foreach ($property_rooms["privateRooms"] as $room) {
                ?>

                <?php
                $privateRoomsCluetipTable .= '<table class="privateTable" id="privateTable' . $privateroomsID . $randProperty . '"><tr>';
                $date = clone $dateStart;

                for ($i = 0; $i < $numNights; $i++) {

                    if ($i == ($numNights - 1)) {
                        $privateRoomsCluetipTable .= "<th class='last'>";
                    } else {
                        $privateRoomsCluetipTable .= "<th>";
                    }

                    $privateRoomsCluetipTable .= my_mb_ucfirst(mb_substr(strftime("%A", $date->format('U')), 0, 3, 'UTF-8'));
                    $privateRoomsCluetipTable .= strftime("<br /> %d", $date->format('U'));
                    $date->modify("+1 day");
                    $privateRoomsCluetipTable .= "</th>";
                }

                $privateRoomsCluetipTable .= "</tr><tr>";

                //dates columns
                $date = clone $dateStart;
                $display_currency = '';
                $subtotal = 0;
                $availableDays = 0;
                $lowest_style = '';
                $lowest_night = '';

                for ($i = 0; $i < $numNights; $i++) {

                    $date_msg = '<span class="na-book price" title="' . _('No private room available') . '">0</span>';

                    if (!empty($room["availableDates"][$date->format("Y-m-d")])) {

                        $display_currency = $room["currency"];
                        $subtotal += $room["availableDates"][$date->format("Y-m-d")]["price"];

                        $currency_formin = $room["currency"];

                        if ($min_price_private == ($room["availableDates"][$date->format("Y-m-d")]["price"] / $room['max_guest_per_unity'])) {
                            $lowest_night = _('Lowest night:') . ' ' . $room["currency"] . ' ' . number_format($min_price_private, 2, '.', '');
                            $lowest_style = 'style="color: #6DA903;"';
                        } else {
                            $lowest_style = '';
                        }

                        $price_pp = number_format($room["availableDates"][$date->format("Y-m-d")]["price"] / $room['max_guest_per_unity'], 2, '.', '');
                        $price_pr = number_format($room["availableDates"][$date->format("Y-m-d")]["price"], 2, '.', '');

                        $date_msg = $room["currency"] . ' <span class="price private"  per_person="' . $price_pp . '" per_room="' . $price_pr . '"  ' . $lowest_style . '>' . number_format($room["availableDates"][$date->format("Y-m-d")]["price"], 2, '.', '') . '</span>';
                        $availableDays++;
                    }

                    if ($i == 0) {
                        $privateRoomsCluetipTable.= '<td align="center" class="first" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                    } else {
                        $privateRoomsCluetipTable.= '<td align="center" width="' . ((1 / $numNights) * 100) . '%;" title="' . _('Price per bed (not per room)') . '" ' . $lowest_style . '>' . $date_msg . '</td>';
                    }

                    $date->modify("+1 day");
                }

                $privateRoomsCluetipTable .= "</tr>";

                $availableBeds = (int) $room['max_guest_per_unity'] * $room["availableRooms"];
                $nb_guest_per_room = $room['max_guest_per_unity'];
                $roomText = '';

                if (($availableBeds == 1) && ceil($availableBeds / $nb_guest_per_room) == 1) {
                    $roomText = ($nb_guest_per_room > 1 ) ? sprintf(gettext('1 guest in 1 Bedroom.')) : sprintf(gettext('1 guest in 1 Bedroom.'));
                } elseif ($availableBeds == 1 && ceil($availableBeds / $nb_guest_per_room) > 1) {
                    $roomText = ($nb_guest_per_room > 1 ) ? sprintf(gettext('1 guest in %d Bedrooms (%d guests in each room).'), (int) ceil($availableBeds / $nb_guest_per_room), (int) $nb_guest_per_room) : sprintf(gettext('1 guest in %d Bedrooms (%d guest in each room).'), (int) ceil($availableBeds / $nb_guest_per_room), (int) $nb_guest_per_room);
                } elseif ($availableBeds > 1 && ceil($availableBeds / $nb_guest_per_room) == 1) {
                    $roomText = ($nb_guest_per_room > 1 ) ? sprintf(gettext('%d guests in 1 Bedroom.'), (int) $availableBeds, (int) $nb_guest_per_room) : sprintf(gettext('%d guests in 1 Bedroom.'), (int) $availableBeds, (int) $nb_guest_per_room);
                } else {
                    $roomText = ($nb_guest_per_room > 1 ) ? sprintf(gettext('%d guests in %d Bedrooms (%d guests in each room).'), (int) $availableBeds, (int) ceil($availableBeds / $nb_guest_per_room), (int) $nb_guest_per_room) : sprintf(gettext('%d guests in %d Bedrooms (%d guest in each room).'), (int) $availableBeds, (int) ceil($availableBeds / $nb_guest_per_room), (int) $nb_guest_per_room);
                }

                $datetop = date_conv($dateStart->format('Y-m-d'), $this->wordpress->get_option('aj_date_format'));

                $roomTitle_PR = '';
                $roomTitle_PP = '';

                if ($lowest_style != '') {
                    $roomTitle_PR = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;( ' . _('Price per Room') . ' )';
                    $roomTitle_PP = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;( ' . _('Price per Person') . ' )' . ' &nbsp;&nbsp;  ' . _('Lowest night:') . ' ' . $display_currency . ' ' . number_format($min_price_private, 2, '.', '');
                } else {
                    $roomTitle_PR = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;( ' . _('Price per Room') . ' )';
                    $roomTitle_PP = _('Arrivée') . ': ' . $datetop . ' &nbsp;&nbsp; ' . _('Nombre de Nuits') . ': ' . $numNights . ' &nbsp;( ' . _('Price per Person') . ' )';
                }
                ?>

                <tr class="room_row_<?php echo $randProperty; ?>">
                    <td class="first">
                        <?php
                        if (!empty($room['descriptionTranslated'])) {
                            echo '<span class="tooltip" title="' . _("VERSION ORIGINALE :") . ' ' . $room['description'] . '">' . $room['descriptionTranslated'] . '</span>';
                        } else {
                            echo $room['description'];
                        }

                        if ($breakfast_included == 1) {
                            echo '<span class="free-breakfast">';
                            echo _('Breakfast Included');
                            echo '</span>';
                        }
                        ?>
                    </td>
                    <?php if ($room['max_guest_per_unity'] == 1): ?>
                        <td align="center" width="50" title="<?php printf(gettext('This type of bedroom can accommodate up to 1 guest per room.')); ?>" style="border-left: none;"><span class="nbpeople-table icon-nbpeople"><?php echo (($room['max_guest_per_unity'] > 1) ? ('<span>' . $room['max_guest_per_unity'] . '</span>') : ('<span>' . $room['max_guest_per_unity'] . '</span>')); ?> </span></td>
                    <?php else: ?>
                        <td align="center" width="50" title="<?php printf(gettext('This type of bedroom can accommodate up to %d guests per room.'), (int) $room['max_guest_per_unity']); ?>" style="border-left: none;"><span class="nbpeople-table icon-nbpeople"><?php echo (($room['max_guest_per_unity'] > 1) ? ('<span>' . $room['max_guest_per_unity'] . '</span>') : ('<span>' . $room['max_guest_per_unity'] . '</span>')); ?> </span></td>
                    <?php endif; ?>

                    <?php if ($numNights != 1): ?>                    
                        <td align="center" title="">
                            <?php echo '<a class="privateTable per_tooltip" href="#privateTable' . $privateroomsID . $randProperty . '" rel="#privateTable' . $privateroomsID . $randProperty . '" style="display : block; padding : 5px;"  title="' . $roomTitle_PP . '"  per_person="' . $roomTitle_PP . '"  per_room="' . $roomTitle_PR . '"><strong>' . $display_currency . '</strong>'; ?> 
                            <span class="private" style="font-weight: 600;" per_person="<?php echo number_format(round((float) ($subtotal / $availableDays) / $room['max_guest_per_unity'], 2), 2); ?>" per_room="<?php echo number_format(round((float) ($subtotal / $availableDays), 2), 2); ?>"><?php echo number_format(($subtotal / $availableDays), 2, '.', ''); ?></span>
                            <?php
                            if ($lowest_night != '') {
                                echo '<span class="lowest_night" style="display: inline-block; margin: 3px; clear: both;">' . $lowest_night . '</span>';
                            }
                            echo ( $numNights != $availableDays ) ? '<div>' . _('Partially Available') . '</div>' : '';
                            echo '</a>';
                            ?>
                        </td>           
                    <?php else: ?>
                        <td align="center" style="font-weight: 600;" title="">
                            <?php echo $display_currency ?> 
                            <span class="private" per_person="<?php echo number_format(round((float) ($subtotal / $availableDays) / $room['max_guest_per_unity'], 2), 2); ?>" per_room="<?php echo number_format(round((float) ($subtotal / $availableDays), 2), 2); ?>"><?php echo number_format(($subtotal / $availableDays), 2, '.', ''); ?></span>
                        </td>
                    <?php endif; ?>
                    <td align="center">
                        <div style="font-weight : 600;">
                            <?php echo $availableBeds; ?> 
                            <?php echo ( $availableBeds == 1 ? _('Guest') : _('Guests') ); ?>
                        </div>
                        <a class="title" href="#" title="<?php echo _('Availability') . '|' . $roomText; ?>">
                            <span style="font-size : 11px; margin-top : 5px;"><?php echo ceil($availableBeds / $room['max_guest_per_unity']); ?> </span>
                            <?php echo ( (ceil($availableBeds / $room['max_guest_per_unity']) == 1) ? _('Bedroom') : _('Bedrooms') ); ?>
                        </a>
                    </td>
                </tr>
                <?php
                $privateRoomsCluetipTable.= "</table>";
                $privateroomsID++;
            }
            echo '<tr class="no_rooms"><td class="first" colspan="4">' . _("No private room available") . '</td></tr>';
        } else {
            ?>
            <tr>
                <td class="first" colspan="<?php echo ( ($numNights != 1) ? 4 : 4 ); ?>">
                    <?php echo _("No private room available"); ?>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <div class="bottom-table-city group">
        <span><?php echo _('Best price. We guarantee it.') ?></span>
        <span><?php echo _('It only takes 2 minutes') ?></span>
    </div>
</div>
<?php
echo $sharedRoomsCluetipTable;
echo $privateRoomsCluetipTable;
?>
<script type="text/javascript">
    
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
        topOffset: 10
    });

    $('a.privateTable').cluetip({
        width: '600px', 
        local:true, 
        cursor: 'pointer',
        arrows: false,
        dropShadow: false,
        sticky: false,
        positionBy: 'bottomTop',
        cluetipClass: 'mcweb',
        topOffset: 10
    });
    
    $('a.sharedTable').cluetip({
        width: '600px', 
        local:true, 
        cursor: 'pointer',
        arrows: false,
        dropShadow: false,
        sticky: false,
        positionBy: 'bottomTop',
        cluetipClass: 'mcweb',
        topOffset: 10
    });
    
    $('table.sharedTable').each(function () {
        $(this).hide();
    });
    
    $('table.privateTable').each(function () {
        $(this).hide();
    });
    
    $('input#complete_dorms').bind('click', function() {
        
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
        
        var dorm_rows = $('tr.dorm_row_<?php echo $randProperty; ?>').filter(function() {
            return this.style.display !== "none";    
        }).length;
        
        if(dorm_rows == 0) {
            $('tr.no_dorms').show();
        } else {
            $('tr.no_dorms').hide();
        }
        
    });
    
    $('#fully_available_<?php echo $randProperty; ?>').bind('click', function() {
        
        if($(this).is(':checked')) {
                        
            $("tr").find("td:eq(2):contains('"+$("input[type='hidden'][name='partially_available']").val()+"')").each(function(i, v){
                $(v).parent().hide();
            });
            
        } else {
            
            $("tr").find("td:eq(2):contains('"+$("input[type='hidden'][name='partially_available']").val()+"')").each(function(i, v){
                $(v).parent().show();
            });
            
        }
        
        var dorm_rows = $('tr.dorm_row_<?php echo $randProperty; ?>').filter(function() {
            return this.style.display !== "none";    
        }).length;
        
        if(dorm_rows == 0) {
            $('tr.no_dorms').show();
        } else {
            $('tr.no_dorms').hide();
        }
        
        var room_rows = $('tr.room_row_<?php echo $randProperty; ?>').filter(function() {
            return this.style.display !== "none";    
        }).length;
        
        if(room_rows == 0) {
            $('tr.no_rooms').show();
        } else {
            $('tr.no_rooms').hide();
        }        
        
    });
    
    if( $('input[name="price_selection"]').val() == 'per_person' ) {
        
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

        if($(this).val() == 'per_person') {
            
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
        
        $('a.privateTable').cluetip({
            width: '600px', 
            local:true, 
            cursor: 'pointer',
            arrows: false,
            dropShadow: false,
            sticky: false,
            positionBy: 'bottomTop',
            cluetipClass: 'mcweb',
            topOffset: 10
        });
        
    }); 
    
    $('tr.no_dorms').hide();
    $('tr.no_rooms').hide();
    
</script>
