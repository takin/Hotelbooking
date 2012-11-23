<?php
if ($use_username) {
	$username = array(
		'name'	=> 'username',
		'id'	=> 'username',
		'value' => set_value('username'),
		'maxlength'	=> $this->config->item('username_max_length', 'tank_auth'),
		'size'	=> 30,
	);
}
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value'	=> set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'value' => set_value('password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$confirm_password = array(
	'name'	=> 'confirm_password',
	'id'	=> 'confirm_password',
	'value' => set_value('confirm_password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$captcha = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8,
);
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
		<h1 class="content_title"><?php echo _('Vous enregistrer');?> - <?php echo _('Créer votre compte');?></h1>		
		<p><?php echo _("La création d'un compte vous donnera accès à votre historique de réservations et d'évaluations pour les auberges de jeunesse. Veuillez entrer votre adresse de courriel et nous vous enverrons un mot de passe.");?>
		<?php echo form_open($this->uri->uri_string()); ?>
		<table>
				<?php if ($use_username) { ?>
				<tr>
						<td valign="top" class="first"><?php echo form_label("Nom d'usager", $username['id']); ?></td>
						<td valign="top"><?php echo form_input($username); ?></td>
						<td style="color: red;"><?php echo form_error($username['name']); ?><?php echo isset($errors[$username['name']])?$errors[$username['name']]:''; ?></td>
				</tr>
				<?php } ?>
				<tr>
						<td valign="top" class="first"><?php echo form_label($this->lang->line('auth_field_email'), $email['id']); ?></td>
						<td valign="top"><?php echo form_input($email); ?></td>
						<td style="color: red;"><?php echo form_error($email['name']); ?><?php echo isset($errors[$email['name']])?$errors[$email['name']]:''; ?></td>
				</tr>
				<?php if($choose_password == TRUE):?>
					<tr>
							<td class="first"><?php echo form_label($this->lang->line('auth_field_password'), $password['id']); ?></td>
							<td><?php echo form_password($password); ?></td>
							<td style="color: red;"><?php echo form_error($password['name']); ?></td>
					</tr>
					<tr>
							<td class="first"><?php echo form_label($this->lang->line('auth_field_confirm_new_password'), $confirm_password['id']); ?></td>
							<td><?php echo form_password($confirm_password); ?></td>
							<td style="color: red;"><?php echo form_error($confirm_password['name']); ?></td>
					</tr>
				<?  endif;?>
				<?php if ($captcha_registration) {
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
						<td class="first">
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
						<td style="color: red;"><?php echo form_error($captcha['name']); ?></td>
				</tr>
				<?php }
				} ?>
		</table>
		<input id="register-page" type="submit" value="<?php echo _("S'enregistrer");?>" name="register">
		<?php echo form_close(); ?><br />
		<a href="<?php echo site_url($this->Db_links->get_link("connect"));?>">&laquo; <?php echo _("Se connecter");?></a>
	</div>
</div>
