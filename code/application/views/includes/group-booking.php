<?php
$group_widget_url = $this->wordpress->get_option("aj_group_url");
if(!empty($group_widget_url))
{
	
?>
<div class="box_content box_round group">
		<div class="group_book_side">
		<a title="Réservation d'auberges de jeunesse pour les groupes" href="<?php echo $group_widget_url; ?>">
		<img class="responsive" src="<?php echo site_url();?>images/V2/group-booking.jpg" alt="<?php echo _('Groupes 10+')?>" />
		<span><?php echo _('Groupes 10+')?></span>
		</a> 
		</div>
</div>
<?php }  ?>
