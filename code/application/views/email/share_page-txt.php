<?php echo sprintf(_('%s has sent you a message:'), var_check($from_name, '')), "\n"; ?>

	"<?php echo str_replace(array("\r", "\r\n"), "\r\n", $message), "\n"; ?>"

<?php echo _('Here is the link to see the prices, description, maps and much more:'), "\n"; ?>

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

<?php echo _('Thank you'), "\n"; ?>
<?php echo $site_name, "\n"; ?>


<?php echo _('PLEASE NOTE THIS IS NOT A CONFIRMED BOOKING'); ?>
