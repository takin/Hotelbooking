<?php
$group_widget_url = site_url('group');
if(!empty($country_selected)&&!empty($city_selected))
{
  $group_widget_url = site_url('group/'.$country_selected."/".$city_selected);
}
elseif(!empty($country_selected))
{
  $group_widget_url = site_url('group/'.$country_selected);
}
if(!empty($group_widget_url))
{
?>
<div class="box_content box_round group">
		<div class="group_book_side">
		<a title="<?php echo _('Groupes 10+'); ?>" href="<?php echo $group_widget_url; ?>">
		<img class="responsive" src="<?php echo site_url();?>images/V2/group-booking.jpg" alt="<?php echo _('Group Booking')?>" />
		<span><?php echo _('Group Booking')?></span>
		</a>
		</div>
</div>
<?php }  ?>
