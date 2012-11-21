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
    
    <?php printf( gettext("Votre nouveau mot de passe pour %s: %s"),$site_name,$new_password);?><br><br>
	
    <?php echo _("Vous venez de changer votre mot de passe.");?><br><br>
	
    <?php echo _("Veuillez le garder dans vos archives pour ne pas l'oublier.");?><br><br>
	
    <br />
	
	<?php if (strlen($username) > 0) { ?> <?php printf( gettext("Votre nom d'usager: %s"),$username);?><br /><?php } ?>
	
    <?php printf( gettext("Votre nom d'usager (adresse email): %s"),$email);?><br /></br>
    
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

<p>&nbsp;</p>

</body>
</html>