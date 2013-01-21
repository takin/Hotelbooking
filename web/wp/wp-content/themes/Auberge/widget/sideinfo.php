<?php 
if (get_option('aj_api_ascii')==""){$csspath = get_option('aj_api_name');}else{$csspath = get_option('aj_api_ascii');}
$aj_api_url = get_option('aj_api_url');

if (get_option('aj_api_site_data')=="hw"){
echo '
<div class="box_content box_round group">
       <ul class="site-info">
         <li id="rules" class="first"><img src="'.$aj_api_url.'images/'.$csspath.'/sideinfo-rules.png" alt="" /><span><strong>'.__('AubergesDeJeunesse.com','auberge').'</strong> '.__('est réglementé par l\'Union Européenne.','auberge').'</span></li>
         <li id="secure"><img src="'.$aj_api_url.'images/sideinfo-secure.png" alt="" /><span><strong>'.sprintf(__('100%% sécurisé.','auberge')).'</strong> '.__('Paiements sécurisés et encryptés pour votre sécurité.','auberge').'</span></li>
         <li id="bestprice"><img src="'.$aj_api_url.'images/sideinfo-10percent.png" alt="" /><span>'.__('Seulement 10% pour garantir votre réservation.','auberge').'</span></li>
         <li class="support" style="display:none;"><img src="'.$aj_api_url.'images/sideinfo-support.png" alt="" /><span>'.__('Un service clientèle de qualité disponible','auberge').' <b>'.__('24h/24, 7j/7','auberge').'</b> '.__('prêt à vous guider à tout moment.','auberge').'</span></li>
				 
				 <li class="support" ><img src="'.$aj_api_url.'images/sideinfo-support.png" alt="" /><br><span>'.__('Text/SMS (Free)','auberge').'</span></li>
				 
         <li id="forall" style="display:none;"><img src="'.$aj_api_url.'images/sideinfo-forall.png" alt="" /><span>'.__('Pour tous les ages: ni maximum ni minimum.','auberge').'</span></li>
				 
				 <li id="forall"><img src="'.$aj_api_url.'images/sideinfo-forall.png" alt="" /><span>'.__('Check your reservation on your mobile','auberge').'</span></li>
				 
         <li id="member" class="last"><img src="'.$aj_api_url.'images/sideinfo-member.png" alt="" /><span><strong>'.__('AubergesDeJeunesse.com','auberge').'</strong> : '.__('Pas besoin de carte de membre pour recevoir les meilleurs prix du Net.','auberge').'</span></li>
       </ul> 
    
</div>';

}else{
	
echo '
<div class="box_content box_round group">
       <ul class="site-info">
         <li id="rules" class="first"><img src="'.$aj_api_url.'images/'.$csspath.'/sideinfo-rules.png" alt="" /><span><strong>'.__('AubergesDeJeunesse.com','auberge').'</strong> '.__('est réglementé par l\'Union Européenne.','auberge').'</span></li>
         <li id="secure"><img src="'.$aj_api_url.'images/hb-icons-secure.png" alt="" /><span><strong>'.sprintf(__('100%% sécurisé.','auberge')).'</strong> '.__('Paiements sécurisés et encryptés pour votre sécurité.','auberge').'</span></li>
         <li id="bestprice"><img src="'.$aj_api_url.'images/hb-icons-10percent.png" alt="" /><span>'.__('Seulement 10% pour garantir votre réservation.','auberge').'</span></li>
         <li class="support" style="display:none;"><img src="'.$aj_api_url.'images/sideinfo-support.png" alt="" /><span>'.__('Un service clientèle de qualité disponible','auberge').' <b>'.__('24h/24, 7j/7','auberge').'</b> '.__('prêt à vous guider à tout moment.','auberge').'</span></li>
				 
				 <li class="support" ><img src="'.$aj_api_url.'images/hb-icons-cell.png" alt="" /><br><span>'.__('Text/SMS (Free)','auberge').'</span></li>
				 
         <li id="forall" style="display:none;"><img src="'.$aj_api_url.'images/sideinfo-forall.png" alt="" /><span>'.__('Pour tous les ages: ni maximum ni minimum.','auberge').'</span></li>
				 
				 <li id="nofee"><img src="'.$aj_api_url.'images/hb-icons-nofee.png" alt="" /><span>'.__('No Booking fees','auberge').'</span></li>
				 
         <li id="member" class="last"><img src="'.$aj_api_url.'images/hb-icons-save.png" alt="" /><span><strong>'.__('AubergesDeJeunesse.com','auberge').'</strong> : '.__('Pas besoin de carte de membre pour recevoir les meilleurs prix du Net.','auberge').'</span></li>
       </ul> 
   
</div>';
}
?>
