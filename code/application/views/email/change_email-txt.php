Bonjour<?php if (strlen($username) > 0) { ?> <?php echo $username; ?><?php } ?>,

Vous venez de changer votre adresse email pour <?php echo $site_name; ?>.
Veuillez cliquer le lien suivant afin de confirmer votre nouvelle adresse email:

<?php echo site_url('/auth/reset_email/'.$user_id.'/'.$new_email_key); ?>


Votre adresse email: <?php echo $new_email; ?>


Vous recevez ce email car il a eu un requète provenant d'un utilisateur de <?php echo $site_name; ?>. Si vous recevez ceci par erreur, veuillez ne pas cliquer sur le lien de confirmation et simplement supprimer ce message.

Merci,
<?php echo $site_name; ?>