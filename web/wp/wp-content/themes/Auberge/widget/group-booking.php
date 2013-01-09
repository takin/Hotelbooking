<?php
$group_widget_url = site_url('group');
if(!empty($country_selected)&&!empty($city_selected))
{
  $group_widget__booking_url = site_url('group/'.$country_selected."/".$city_selected);
}
elseif(!empty($country_selected))
{
  $group_widget__booking_url = site_url('group/'.$country_selected);
}

if(!empty($group_widget__booking_url))
{
?>
<div class="box_content box_round group">
		<div class="group_book_side">
		<a title="<?php echo _('Group Booking'); ?>" href="<?php echo $group_widget__booking_url; ?>">
		<img class="responsive" src="<?php echo site_url();?>/../images/V2/group-booking.jpg" alt="<?php echo _('Group Booking')?>" />
		<span><?php echo _('Group Booking')?></span>
		</a>
		<a class="group-reserve" href="<?php echo $group_widget_url; ?>" title="Réservation d'auberges de jeunesse pour les groupes">
		<span style=" background-color: black;width:111px;top: 29px;"><?php echo _('Groupes 10+')?></span></a>
		</div>
</div>
<?php }  ?>
