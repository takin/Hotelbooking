<?php
/*variables
$user['email']
$user['id']
$user_profile
stdClass Object
(
    [id] => 11
    [user_id] => 16
    [user_level_id] => 1
    [gender_id] => 2
    [first_name] => Louis
    [last_name] => Ray
    [phone_number] => 555-4444
    [home_country] => Bangladesh
    [favorite_currency] => 4
    [website] => 
    [mail_subscription] =>
)

*/
?>
<div id="sidebar" class="grid_4">
	<?php $this->load->view('includes/navuser'); ?> 
</div>
<div id="main" class="grid_12 user-auth">
	<div class="box_content box_round group">
		<h1 class="content_title"><?php echo _('Votre Profil');?></h1>
      <p><?php echo _('Mettre à jour votre profil');?></p>
			<form action="" method="POST">
			<table cellpadding="0" cellspacing="0" width="100%" class="profile">
			
			
			<tbody>
			<tr>
			 <td class="first"><label for=""><?php echo _('Prénom');?>:</label></td>
			 <td><input type="text" id="firstname" name="firstname" value="<?php echo $user_profile->first_name?>" maxlength="40"></td>            
			</tr>
			<tr>
			<td class="first"><label for=""><?php echo _('Nom'); ?>:</label></td>
			<td><input type="text" id="lastname" name="lastname" value="<?php echo $user_profile->last_name?>" maxlength="40"></td>     
			</tr>
			
			<tr>
				<td class="first"><label for=""><?php echo _('Nationalité'); ?>:</label></td>
				<td>
					<?php 
					 $this->Db_country->select_country("Nationality","nationality",$user_profile->home_country,"style=\"width: 175px;\"","en",$this->site_lang); 
					?>    
				</td>
			</tr>
			
			<tr>
				<td class="first"><label for=""><?php echo _('Langue de correspondance'); ?>:</label></td>
				<td>
					<?php 
					 $this->Db_links->select_lang("Language","language",$user_profile->favorite_lang_id,"style=\"width: 175px;\""); 
					?>    
				</td>
			</tr>
			
			<tr>
			<td  class="first"><label for="gender"><?php echo _("Sexe"); ?>:</label></td>
			<td>
					<select id="gender" name="gender" style="width: 175px;">
					<option <?php if($user_profile->gender_id == 1){?> selected="selected"<?php }?> value="Male"><?php echo _("Masculin");?></option>
					<option <?php if($user_profile->gender_id == 2){?> selected="selected"<?php }?> value="Female"><?php echo _("Féminin");?></option>
					</select>
			</td>
			</tr>
			
			<tr>
				<td class="first"><label for=""><?php echo _('Téléphone'); ?>:</label></td>
				<td><input type="text" value="<?php echo $user_profile->phone_number?>" id="phone_number" name="phone_number"></td>
				
			</tr>
			<tr>
				<td class="first"><label for=""><?php echo _("Devise");?> :</label></td>
				<td>
				<?php $this->Db_currency->select_currency("favorite_currency","favorite_currency",$user_profile->favorite_currency_code,"",$this->site_lang); ?>
				</td>
				
			</tr>
			<tr>
				<td class="first"><label style="float: left;" for=""><?php echo _('Abonnement newsletter'); ?></label></td>
				<td><input style="width:auto;" class="checkbox" type="checkbox" name="mail_subscribe" id="mail_subscribe" value="1" <?php if($user_profile->mail_subscription==true) echo "checked=\"checked\"";?> ></td>
			</tr>
			</tbody>
			</table>
			
			<input id="profile-submit" type="submit" value="<?php echo _('Modifier'); ?>" name="submit">
			</form>  
    </div>
</div>