<?php if (strlen($username) > 0) : ?>
<?php printf( gettext("Bonjour %s,"),$username);?>
<?php else:?>
<?php echo _("Bonjour,");?>
<?php endif;?>

<?php echo _("Vous venez de changer votre mot de passe.");?>

<?php echo _("Veuillez le garder dans vos archives pour ne pas l'oublier.");?>
<?php if (strlen($username) > 0) { ?>

<?php printf( gettext("Votre nom d'usager: %s"),$username);?>
<?php } ?>

<?php printf( gettext("Votre nom d'usager (adresse email): %s"),$email);?>

<?php printf( gettext("Votre nouveau mot de passe pour %s: %s"),$site_name,$new_password);?>

<?php echo _("Merci,");?>
<?php echo $site_name; ?>