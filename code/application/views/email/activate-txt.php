<?php printf( gettext("Bonjour et bienvenue sur %s"),$site_name);?>

<?php printf( gettext("Merci de vous être joint à nous sur %s. Voici les information pour accèder à votre compte."),$site_name);?>
<?php echo _("Pour confirmer votre adresse email, veuillez utiliser le lien suivant:");?>

<?php echo site_url('/auth/activate/'.$user_id.'/'.$new_email_key); ?>


<?php printf( gettext("Veuillez confirmer votre adresse email dans les %s heures, autrement votre enregistrement sera annulé et vous devrez vous enregistrer de nouveau."),$activation_period);?>

<?php if (strlen($username) > 0) { ?>

<?php printf( gettext("Votre nom d'usager: %s"),$username);?>
<?php } ?>

<?php printf( gettext("Votre nom d'usager (adresse email): %s"),$email);?>
<?php if (isset($password)) { /* ?>

Your password: <?php echo $password; ?>
<?php */ } ?>


<?php echo _("Merci,");?>
<?php echo $site_name; ?>