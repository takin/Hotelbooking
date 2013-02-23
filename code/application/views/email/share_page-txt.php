<?php echo var_check($from_name, ''); ?> <?php echo _('send you a message:'); ?>
	"<?php echo var_check($message, ''); ?>"

<?php $property_url = site_url("/{$property_type}/{$property_name}/{$property_number}"); ?>
<?php echo var_check($property_url, ''); ?>

<?php echo _('Like what you see?'); ?>
Visit <?php echo site_url('/'); ?> or signup to get 70% off of select hotels.
