<?php printf( gettext("Bonjour et bienvenue sur %s,"),$site_name);?>

<?php printf( gettext("Merci de vous joindre à nous sur %s. Voici les information pour accèder à votre compte."),$site_name);?>

<?php if (strlen($username) > 0) { ?>

<?php printf( gettext("Votre nom d'usager: %s"),$username);?>

<?php } ?>

<?php printf( gettext("Votre nom d'usager (adresse email): %s"),$email);?>

<?php printf( gettext("Votre mot de passe: %s"),$password);?>

<?php printf( gettext("Pour vous connecter à %s, veuillez utiliser le lien ci-dessous:"),$site_name);?>

<?php echo site_url($this->Db_links->get_link("connect")); ?>


<?php echo _("Merci,");?>
<?php echo $site_name; ?>