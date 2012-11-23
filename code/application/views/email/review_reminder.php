<?php 
/* DATA
$site_name
$email
$firstname
$lastname
$property_number
$property_name

*/
?>

<html>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor='#ffffff'>

<table border=0 cellspacing=0 cellpadding=0 width="98%" style="width:98.0%; font-family:arial;">
 <tr>
  <td valign="top">
  <div align="center">
  <table border=0 cellspacing=0 cellpadding=0 width=650>
   <tr >
    <td valign=top >
    <p style="line-height:18px">
    
    <img style="margin-top:20px;" border=0 src="<?php echo base_url();?>images/<?php echo $site_name; ?>/email-head.gif" alt="<?php echo $site_name; ?>" />
    
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
    <span style="font-size:12px; color:#2F2F2F; font-family:Arial, Helvetica, sans-serif;">
      
        <?php printf( gettext("Bonjour %s,"),$firstname);?>
        
    </span>
    </strong>
    
    <span style="font-size:12px; color:#2F2F2F; font-family:Arial, Helvetica, sans-serif;"><br><br>
    
    <?php printf( gettext("Nous espérons que vous avez passé un bon séjour à %s."),$property_name);?> <?php echo _("Nous vous invitons à prendre quelques minutes pour laisser vos impressions sur l'établissement dans la section \"commentaires\".");?> <?php echo _("Veuillez simplement cliquer sur le lien ci-dessous pour accéder au formulaire.");?><br /><br />
    <a href="<?php echo site_url($this->Db_links->get_link("info").'/'.url_title($property_name)."/$property_number?comment=insert#tab3");?>"><?php echo site_url($this->Db_links->get_link("info").'/'.url_title($property_name)."/$property_number?comment=insert#tab3");?></a>
   
     <?php // echo _("Si le lien ci-haut ne fonctionne pas veuillez le copier coller dans votre navigateur.");?>
     <?php //<br /><br /> ?>
     <?php //echo _("De plus, jusqu'au 30 juin 2010, vous pouvez gagner un IPOD Nano lorsque vous laissez un commentaire ou si vous devenez un de nos fans sur Facebook :");?><br /><br />     
     <?php /*?><a href="http://www.facebook.com/pages/AubergesDeJeunessecom/333281255938">http://www.facebook.com/pages/AubergesDeJeunessecom/333281255938</a>
     <br /><br /><?php */?>
     <?php echo _("N'hésitez pas à revenir nous voir pour planifier votre prochain voyage.");?><br /><br />
  </span>
        
    <p style="line-height:18px">
        <span style="font-size:12px; color:#2F2F2F; font-family:Arial, Helvetica, sans-serif;">
            <?php echo _("Merci,");?><br>
            <strong>
                <?php echo $site_name; ?>
            </strong>
            <br /><br />
            <?php echo _("Si vous l'avez déjà fait pour cet établissement ou la si la réservation avait été annulée , veuillez simplement ignorer ce message.");?>
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