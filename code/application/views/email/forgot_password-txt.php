<?php if (strlen($username) > 0) : ?>
<?php printf( gettext("Bonjour %s,"),$username);?>
<?php else:?>
<?php echo _("Bonjour,");?>
<?php endif;?>

<?php echo _("Pour créer un nouveau mot de passe, veuillez utiliser le lien suivant:");?>

<?php echo site_url($this->Db_links->get_link("user_reset_pass").'/'.$user_id.'/'.$new_pass_key); ?>

<?php printf( gettext("Ce courriel vous est envoyé suite à une requête provenant d'un utilisateur de %s. Ceci fait partie de la procédure pour créer un nouveau mot de passe. Si vous n'avez pas demandé un nouveau mot de passe, veuillez simplement ignorer ce courriel."),$site_name);?>

<?php echo _("Merci,");?>
<?php echo $site_name; ?>