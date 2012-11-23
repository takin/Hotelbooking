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
    	
        <?php printf( gettext("Bonjour et bienvenue sur %s,"),$site_name);?>
        
    </span>
    </strong>
    
    <span style="font-size:12px;color:#2F2F2F"><br><br>
    
   	<?php printf( gettext("Merci de vous joindre à nous sur %s. Voici les information pour accèder à votre compte."),$site_name);?><br /><br>
    
	<?php if (strlen($username) > 0) { ?><?php printf( gettext("Votre nom d'usager: %s"),$username);?><br /><?php } ?>
	<?php printf( gettext("Votre nom d'usager (adresse email): %s"),$email);?><br />
	<?php printf( gettext("Votre mot de passe: %s"),"<strong>".$password."</strong>");?><br />
	<br />
    
	<?php printf( gettext("Pour vous connecter à %s, veuillez utiliser le lien ci-dessous:"),$site_name);?><br />
	<br />
	<b><a href="<?php echo site_url($this->Db_links->get_link("connect")); ?>" style="color: #3366cc;"><?php echo site_url($this->Db_links->get_link("connect")); ?></a></b>
  <br />
		<br />
	
    </span>
        
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