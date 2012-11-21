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
    
    <?php if ($this->wordpress->get_option('aj_api_ascii')==""){$csspath = $this->wordpress->get_option('aj_api_name');}else{$csspath = $this->wordpress->get_option('aj_api_ascii');} ?>
    <img style="margin-top:20px;" border=0 src="<?php echo base_url();?>images/<?php echo $csspath; ?>/email-head.gif" alt="<?php echo $this->config->item('site_name'); ?>" />

    
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
   <?php if (strlen($username) > 0) : ?>
   <?php printf( gettext("Bonjour %s,"),$username);?>
   <?php else:?>
   <?php echo _("Bonjour,");?>
   <?php endif;?>
      
     </span>
    </strong>
    
    <span style="font-size:12px;color:#2F2F2F"><br><br>
    
	<?php echo _("Pour créer un nouveau mot de passe, veuillez utiliser le lien suivant:");?><br />
	<a href="<?php echo site_url($this->Db_links->get_link("user_reset_pass").'/'.$user_id.'/'.$new_pass_key); ?>" style="color: #3366cc;"><?php echo site_url($this->Db_links->get_link("user_reset_pass").'/'.$user_id.'/'.$new_pass_key); ?></a>
	<br />
	
	<br />
  <?php printf( gettext("Ce courriel vous est envoyé suite à une requête provenant d'un utilisateur de %s. Ceci fait partie de la procédure pour créer un nouveau mot de passe. Si vous n'avez pas demandé un nouveau mot de passe, veuillez simplement ignorer ce courriel."),"<a href=\"".site_url("")."\" style=\"color: #3366cc;\">$site_name</a>");?><br /><br>
    </span>
	
	<?php /* Your new password: <?php echo $new_password; ?><br /> */ ?>
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