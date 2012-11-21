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
	 Bonjour<?php if (strlen($username) > 0) { ?> <?php echo $username; ?><?php } ?>,
      
     </span>
    </strong>
    
    <span style="font-size:12px;color:#2F2F2F">,<br><br>
    
    Vous venez de changer votre adresse email pour <?php echo $site_name; ?>.<br />
    
    Veuillez cliquer le lien suivant afin de confirmer votre nouvelle adresse email:<br />
    <br />
   <a href="<?php echo site_url('/auth/reset_email/'.$user_id.'/'.$new_email_key); ?>" style="color: #3366cc;">Confirm your new email</a><br>
    <br />
    Si le lien ci-haut ne fonctionne pas veuillez copier coller le lien suivant dans votre navigateur:<br />
    <nobr><a href="<?php echo site_url('/auth/reset_email/'.$user_id.'/'.$new_email_key); ?>" style="color: #3366cc;"><?php echo site_url('/auth/reset_email/'.$user_id.'/'.$new_email_key); ?></a></nobr><br />
    <br />
    <br />
    Votre adresse email: <?php echo $new_email; ?><br />
    <br />
    <br />
    Vous recevez ce email car il a eu un requète provenant d'un utilisateur de <a href="<?php echo site_url(''); ?>" style="color: #3366cc;"><?php echo $site_name; ?></a>. Si vous recevez ceci par erreur, veuillez ne pas cliquer sur le lien de confirmation et simplement supprimer ce message.
    <br />
    <br />
    </span>
	
	<?php /* Your new password: <?php echo $new_password; ?><br /> */ ?>
	<p style="line-height:18px">
        <span style="font-size:12px;color:#2F2F2F">
            Merci,<br>
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