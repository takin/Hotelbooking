<?php
/**
 * @author Louis-Michel Raynauld
 *
 * References: none
 *
 */

/**
 * sms_menu
 *
 * Create a SMS option menu
 *
 */
function sms_menu($menu_id, $sms_value = NULL, $default_value = NULL)
{
  if(empty($sms_value)&&!empty($default_value))
  {
    $sms_value = $default_value;
  }

	?>
	<select id="<?echo $menu_id; ?>">
   <?php echo _('1 Day before arrival')?>
     <option value='none' <?php if($sms_value == 'none') echo " selected=\"selected\""?>><?php echo _('No thank you')?></option>
     <option value='now' <?php if($sms_value == 'now') echo " selected=\"selected\""?>><?php echo _("Send now"); ?></option>
     <option value='0' <?php if($sms_value == '0') echo " selected=\"selected\""?>><?php echo _("Arrival day"); ?></option>
     <option value='1' <?php if($sms_value == '1') echo " selected=\"selected\""?>><?php echo _("1 Day before arrival"); ?></option>
     <option value='2' <?php if($sms_value == '2') echo " selected=\"selected\""?>><?php echo _("2 Days before arrival"); ?></option>
     <option value='3' <?php if($sms_value == '3') echo " selected=\"selected\""?>><?php echo _("3 Days before arrival"); ?></option>
     <option value='4' <?php if($sms_value == '4') echo " selected=\"selected\""?>><?php echo _("4 Days before arrival"); ?></option>
     <option value='5' <?php if($sms_value == '5') echo " selected=\"selected\""?>><?php echo _("5 Days before arrival"); ?></option>
     <option value='6' <?php if($sms_value == '6') echo " selected=\"selected\""?>><?php echo _('6 Days before arrival')?></option>
     <option value='7' <?php if($sms_value == '7') echo " selected=\"selected\""?>><?php echo _("1 Week before arrival"); ?></option>
  </select>
	<?php
}