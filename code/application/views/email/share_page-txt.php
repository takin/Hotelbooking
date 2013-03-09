<?php echo sprintf(_('%s has sent you a message:'), var_check($from_name, '')); ?>

	"<?php echo var_check($message, ''); ?>"

<?php echo _('Here is the link to see the prices, description, maps and much more:'); ?>

<?php 
	$property_url = site_url("/{$property_type}/{$property_name}/{$property_number}");

	$append = '';
	if (!empty($date)) {
		$append .= '/' . $date;

		if (!empty($nights) && is_numeric($nights)) {
			$append .= '/' . $nights;
		}
	}

	$property_url .= $append;
?>
<?php echo var_check($property_url, ''); ?>

<?php echo _('Like what you see?'); ?>
Visit <?php echo site_url('/'); ?> or signup to get 70% off of select hotels.
