<div id="content" class="user-profile">
	
	<div class="page-meta group">
		<h1 class="text-shadow-wrapper icon-user"><?php echo _('Mettre à jour votre profil');?></h1>
	</div>
	<form class="basic" action="" method="post">
	<div class="white-back round-corner5 border-around form">
		
			<ul class="group">
			<?php /*?><li class="error round-corner5 group"><span>Error while modifying your profile</span></li><?php */?>
			<li>
			<label for=""><?php echo _('Prénom');?>:</label>
      <input type="text" id="firstname" name="firstname" value="<?php echo $user_profile->first_name?>" class="text">
			</li>
			
			<li>
			<label for=""><?php echo _('Nom'); ?>:</label>
      <input type="text" id="lastname" name="lastname" value="<?php echo $user_profile->last_name?>" class="text">   
			</li>
			
			<li>
			<label for=""><?php echo _('Nationalité'); ?>:</label>
      <?php $this->Db_country->select_country("Nationality","nationality",$user_profile->home_country,"style=\"width: 175px;\"","en",$this->site_lang); ?>  
			
			</li>
			
			<li>
			<label for=""><?php echo _('Langue de correspondance'); ?>:</label>
      <?php $this->Db_links->select_lang("Language","language",$user_profile->favorite_lang_id,"style=\"width: 175px;\""); ?> 
			</li>
			
			<li>
			<label for="gender"><?php echo _("Sexe"); ?>:</label>
      <select id="gender" name="gender">
				<option <?php if($user_profile->gender_id == 1){?> selected="selected"<?php }?> value="Male"><?php echo _("Masculin");?></option>
				<option <?php if($user_profile->gender_id == 2){?> selected="selected"<?php }?> value="Female"><?php echo _("Féminin");?></option>
			</select>
			</li>
			
			<li>
			<label for=""><?php echo _('Téléphone'); ?>:</label>
      <input type="tel" value="<?php echo $user_profile->phone_number?>" id="phone_number" name="phone_number" class="text">
			</li>
			
			<li>
			<label for=""><?php echo _("Devise");?> :</label>
      <?php $this->Db_currency->select_currency("favorite_currency","favorite_currency",$user_profile->favorite_currency_code,"",$this->site_lang); ?>
			</li>
			
			<li class="group">
			<input style="width:auto;" class="checkbox" type="checkbox" name="mail_subscribe" id="mail_subscribe" value="1" <?php if($user_profile->mail_subscription==true) echo "checked=\"checked\"";?>/>
			<label class="checkbox-label" for=""><?php echo _('Abonnement newsletter'); ?>:</label>
			
			</li>
			
			<li><a href="<?php echo site_url($this->Db_links->get_link("user_change_pass"));?>"><?php echo _("Changer de mot de passe");?> &raquo;</a></li>
			
			
			</ul>
		
	</div>
	
	<div class="submit-button">
		<input type="submit" class="submit-green green-button" value="<?php echo _('Modifier'); ?>" />
		
	</div>
	</form>
	
	<div class="bottom-nav">
	<a href="<?php echo site_url($this->Db_links->get_link("user"));?>" class="change-search text-shadow-wrapper">&laquo; <?php echo _('Back to account home page'); ?></a>
	</div>
 
</div>