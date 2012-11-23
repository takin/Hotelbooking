<html>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor='#ffffff'>
<style type="text/css">
    body,td { color:#2f2f2f; font:12px/1.35em Arial, Helvetica, sans-serif; }
</style>
<table border=0 cellspacing=0 cellpadding=0 width="98%" style="width:98.0%; font-family:arial;">
 <tr>
  <td valign="top">
  <div align="center">
  <table border=0 cellspacing=0 cellpadding=0 width=650>
   <tr >
    <td valign=top >
    <p style="line-height:18px">
    
    <img style="margin-top:20px;" border=0 src="<?php echo base_url();?>images/email-head.gif" alt="Auberges De Jeunesse">

    
    </p>
    </td>
   </tr>
  </table>
  </div>
  <br>
  <div align="center">
  <table  border=0 cellspacing=0 cellpadding=0 width=650 style="width:487.5pt;">
   <tr>
    <td valign=top>
    <p style="line-height:18px"><strong>
    <span style="font-size:12px; color:#2F2F2F">
    	
        <?php printf( gettext("Bonjour et bienvenue sur %s"),$site_name);?>
        
    </span>
    </strong>
    
    <span style="font-size:12px;color:#2F2F2F">,<br><br>
	<?php printf( gettext("Merci de vous être joint à nous sur %s. Voici les information pour accèder à votre compte."),$site_name);?><br /><br>
	
	<?php echo _("Pour confirmer votre adresse email, veuillez utiliser le lien suivant:");?><br />
	<br />
	<a href="<?php echo site_url('/auth/activate/'.$user_id.'/'.$new_email_key); ?>" style="color: #3366cc;"><?php echo _("Compléter votre enregistrement...");?></a><br>
	<br />
	<?php echo _("Si le lien ci-haut ne fonctionne pas veuillez copier coller le lien suivant dans votre navigateur:");?><br />
	<nobr><a href="<?php echo site_url('/auth/activate/'.$user_id.'/'.$new_email_key); ?>" style="color: #3366cc;"><?php echo site_url('/auth/activate/'.$user_id.'/'.$new_email_key); ?></a></nobr><br />
	<br />
	<?php printf( gettext("Veuillez confirmer votre adresse email dans les %s heures, autrement votre enregistrement sera annulé et vous devrez vous enregistrer de nouveau."),$activation_period);?>
	<br />
	<br />
	<?php if (strlen($username) > 0) { ?><?php printf( gettext("Votre nom d'usager: %s"),$username);?> } ?>
	<?php printf( gettext("Votre nom d'usager (adresse email): %s"),$email);?><br />
	<?php if (isset($password)) { /* ?>Your password: <?php echo $password; ?><br /><?php */ } ?>
    </span>
    <br>
    <br>    
    <p style="line-height:18px">
        <span style="font-size:12px;color:#2F2F2F">
            <?php echo _("Merci,");?><br>
            <strong>
                <?php echo $site_name; ?>
            </strong>
    	</span>
    </p>
    </td>
   </tr>
  </table>
  </div>
  </td>
 </tr>
</table>

<p >&nbsp;</p>

</body>
</html>