<?php
$login = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'value' => set_value('login'),
	'maxlength'	=> 80,
	'size'	=> 30,
	'class' => 'inputsize',
);
if ($login_by_username AND $login_by_email) {
	$login_label = $this->lang->line('auth_field_login');
} else if ($login_by_username) {
	$login_label = $this->lang->line('auth_field_login');
} else {
	$login_label = $this->lang->line('auth_field_email');
}
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'size'	=> 30,
	'class' => 'inputsize',
);
$remember = array(
	'name'	=> 'remember',
	'id'	=> 'remember',
	'value'	=> 1,
	'checked'	=> set_value('remember'),
	'style' => 'margin:0;padding:0',
);
$captcha = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8,
);

$register_attributes = array();
$form_attributes = array(
	'method' => 'POST'
);

if ($is_ajax) {
?>
<style type="text/css">
	#login-connect-page,
	#register-page {
		color: #fff;
		border: none;
		background-color: #3087C9;
		padding: 5px 15px;
		font-weight: bold;
		text-transform: uppercase;
		border-radius: 5px;
		-moz-border-radius: 5px;
	}
</style>
<?php

	$form_attributes = array(
		'onsubmit' => 'SaveProperty.login(this); return false;'
	);

	$register_attributes = array(
		'onclick' => 'SaveProperty.getRegisterForm(); return false;'
	);

	echo '<br /><span style="color: #000;">', _('To save a property as a favorite, you must be login to your account.'), '</span><br /><br />';
	echo $showForm
		? '<h2><center>' . _('Existing account') . '</center></h2><br />'
		: '<h2><a href="#" onclick="$(\'.content_container\').css(\'height\', \'630px\'); $(\'#login_form_container\').show(); return false;"><center>' . _('Existing account') . '</center></a></h2><br />';
	echo $showForm
		? '<div id="login_form_container">'
		: '<div id="login_form_container" style="display: none">';
}
else {
?>
<div id="sidebar" class="grid_4">
	<div class="box_content box_round side_entry">
	<span class="title"><strong><?php echo _("Les avantanges d'avoir un compte");?></strong></span>   
		<ul>             	
			<li><span><?php echo _("Voir l'historique de vos réservations");?></span></li>
			<li><span><?php echo _("Évaluer les auberges visitées");?></span></li>
			<li><span><?php echo _("Plus rapide lors de la réservation");?></span></li>
			<li><span><?php echo _("Modifier vos informations personnelles");?></span></li>
		</ul>
	</div>
</div>
<div id="main" class="grid_12 user-auth">
	<div class="box_content box_round group">
		<h1 class="content_title"><?php echo _('Connexion à votre compte');?></h1>
		<p><?php printf(gettext("Bienvenue dans la section \"%s\". Depuis celui-ci, vous avez accès à vos réservations d'Auberges de Jeunesse, à vos évaluations, à tous les contacts (emails, numéros de téléphone..) et à votre profil d'usager."),"<b>"._("Mon Compte")."</b>");?></p>
		<p><?php echo _("Votre compte usager vous permet aussi d'effectuer plus rapidement une réservation en utilisant vos informations déjà entrées.");?></p>
				
<?php } ?>
		<?php echo form_open($this->uri->uri_string(), $form_attributes); ?>
		
		<table>
				<tr>
						<td class="first label1" valign="middle"><?php echo form_label($login_label, $login['id']); ?></td>
						<td valign="middle"><?php echo form_input($login); ?></td>
						<td valign="middle" style="color: red;"><?php echo form_error($login['name']); ?><?php echo isset($errors[$login['name']])?$errors[$login['name']]:''; ?></td>
				</tr>
				<tr>
						<td class="first label1" valign="middle"><?php echo form_label($this->lang->line('auth_field_password'), $password['id']); ?></td>
						<td valign="middle"><?php echo form_password($password); ?></td>
						<td valign="middle" style="color: red;"><?php echo form_error($password['name']); ?><?php echo isset($errors[$password['name']])?$errors[$password['name']]:''; ?></td>
				</tr>
		
				<?php if ($show_captcha) {
						if ($use_recaptcha) { ?>
				<tr>
						<td colspan="2">
								<div id="recaptcha_image"></div>
						</td>
						<td>
								<a href="javascript:Recaptcha.reload()"><?php echo _("Nouveau CAPTCHA");?></a>
								<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')"><?php echo _("Audio CAPTCHA");?></a></div>
								<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')"><?php echo _("Image CAPTCHA");?></a></div>
						</td>
				</tr>
				<tr>
						<td class="first" valign="middle">
								<div class="recaptcha_only_if_image"><?php echo _("Entrer le mot ci-dessus");?></div>
								<div class="recaptcha_only_if_audio"><?php echo _("Entrer les numéros entendus");?></div>
						</td>
						<td><input type="text" id="recaptcha_response_field" name="recaptcha_response_field" /></td>
						<td style="color: red;"><?php echo form_error('recaptcha_response_field'); ?></td>
						<?php echo $recaptcha_html; ?>
				</tr>
				<?php } else { ?>
				<tr>
						<td class="first" colspan="3">
								<p><?php echo _("Entrer le code exactement comme il apparaît:"); ?></p>
								<?php echo $captcha_html; ?>
						</td>
				</tr>
				<tr>
						<td class="first"><?php echo form_label($this->lang->line('auth_field_confirmation_code'), $captcha['id']); ?></td>
						<td><?php echo form_input($captcha); ?></td>
						<td valign="middle" style="color: red;"><?php echo form_error($captcha['name']); ?></td>
				</tr>
				<?php }
				} ?>
		
				<tr>
						<td class="first" colspan="3">
								<?php echo form_checkbox($remember); ?>
								<?php echo form_label($this->lang->line('auth_field_remember_me'), $remember['id']); ?>
								
						</td>
				</tr>
		</table>
		<input id="login-connect-page" type="submit" value="<?php echo _("Se connecter"); ?>" name="submit">
		<div class="clearfix" style="margin-top:10px;">
			<?php echo anchor($this->Db_links->get_link("user_forgot_pass"), _("Mot de passe oublié")); if (!$is_ajax) { ?> | 
			<?php if ($this->config->item('allow_registration', 'tank_auth')) echo anchor($this->Db_links->get_link("register"), _("S'enregister"), $register_attributes); } ?>
		</div>
		<?php echo form_close(); ?>
<?php if (!$is_ajax) { ?>
	</div>  
</div>
<?php } else { 
	echo '</div><br /><br /><h2><a onclick="SaveProperty.getRegisterForm(); return false;" href="', $this->Db_links->get_link("register"),'"><center>', _('Create account'), '</center></a></h2>';
	echo '<span style="color: #000">', _('By creating an account you will be able to save properties as favorites, get access to your bookings and ratings, and many more benefits.'), '</span>';
}?>
