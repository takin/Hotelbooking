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
$first_name = array(
	'name'	    => 'first_name',
	'id'	    => 'first_name',
	'value'	    => set_value('first_name'),
	'maxlength' => 40,
	'size'	    => 30,
);
$last_name = array(
	'name'	    => 'last_name',
	'id'	    => 'last_name',
	'value'	    => set_value('last_name'),
	'maxlength' => 40,
	'size'	    => 30,
);
$mail_subscription = array(
	'name'	=> 'mail_subscription',
	'id'	=> 'mail_subscription',
	'value'	=> set_value('mail_subscription'),
	'checked' => true
);

$login_attributes = array();
$form_attributes  = array(
	'action' => 'POST'
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
		'onsubmit' => 'SaveProperty.register(this); return false;'
	);

	$login_attributes = array(
		'onclick' => 'SaveProperty.getLoginForm(true); return false;'
	);

	echo '<br /><span style="color: #000;">', _('To save a property as a favorite, you must be login to your account.'), '</span><br />';
	echo '<h2><a href="#" onclick="SaveProperty.getLoginForm(true); return false;"><center>', _('Existing account'), '</center></a></h2>';

	echo '<h2><center>', _('Create account'), '</center></h2>';
	echo '<span style="color: #000">', _('By creating an account you will be able to save properties as favorites, get access to your bookings and ratings, and many more benefits.'), '</span><br /><br />';
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
		<h1 class="content_title"><?php echo _('Vous enregistrer');?> - <?php echo _('Créer votre compte');?></h1>		
		<p><?php echo _("La création d'un compte vous donnera accès à votre historique de réservations et d'évaluations pour les auberges de jeunesse. Veuillez entrer votre adresse de courriel et nous vous enverrons un mot de passe.");?>
<?php } ?>
		<?php echo form_open($this->uri->uri_string(), $form_attributes); ?>
		<table>
				<?php if ($is_ajax) { ?>
					<tr>
						<td valign="top" class="first"><?php echo form_label(_('First name'), $first_name['id']); ?></td>
						<td valign="top"><?php
							echo form_input($first_name); ?>
							<span style="color: red;"><?php echo form_error($first_name['name']); ?><?php echo isset($errors[$first_name['name']]) ? $errors[$first_name['name']] : '' ; ?></span>
						</td>
					</tr>
					<tr>
						<td valign="top" class="first"><?php echo form_label(_('Nom'), $last_name['id']); ?></td>
						<td valign="top"><?php
							echo form_input($last_name); ?>
							<span style="color: red;"><?php echo form_error($last_name['name']); ?><?php echo isset($errors[$last_name['name']]) ? $errors[$last_name['name']] : '' ; ?></span>
						</td>
					</tr>
                                <?php } ?>

				<?php if ($use_username) { ?>
				<tr>
						<td valign="top" class="first"><?php echo form_label("Nom d'usager", $username['id']); ?></td>
						<td valign="top"><?php
							echo form_input($username); ?>
							<span style="color: red;"><?php echo form_error($username['name']); ?><?php echo isset($errors[$username['name']])?$errors[$username['name']]:''; ?></span>
						</td>
				</tr>
				<?php } ?>
				<tr>
						<td valign="top" class="first"><?php echo form_label($this->lang->line('auth_field_email'), $email['id']); ?></td>
						<td valign="top"><?php
							echo form_input($email); ?>
							<span style="color: red;"><?php echo form_error($email['name']); ?><?php echo isset($errors[$email['name']])?$errors[$email['name']]:''; ?></span>
						</td>
				</tr>
				<?php if($choose_password == TRUE):?>
					<tr>
							<td class="first"><?php echo form_label($this->lang->line('auth_field_password'), $password['id']); ?></td>
							<td><?php
								echo form_password($password); ?>
								<span style="color: red;"><?php echo form_error($password['name']); ?></span>
							</td>
					</tr>
					<tr>
							<td class="first"><?php echo form_label($this->lang->line('auth_field_confirm_new_password'), $confirm_password['id']); ?></td>
							<td><?php
								echo form_password($confirm_password); ?>
								<span style="color: red;"><?php echo form_error($confirm_password['name']); ?></span>
							</td>
					</tr>
				<?php  endif;?>
				<?php if ($captcha_registration) {
						if ($use_recaptcha) { ?>
				<tr>
						<td>
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
						<td>
							<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />
							<span style="color: red;"><?php echo form_error('recaptcha_response_field'); ?></span>
						</td>
						<?php echo $recaptcha_html; ?>
				</tr>
				<?php } else { ?>
				<tr>
						<td class="first" colspan="2">
								<p><?php echo _("Entrer le code exactement comme il apparaît:"); ?></p>
								<?php echo $captcha_html; ?>
						</td>
				</tr>
				<tr>
						<td class="first"><?php echo form_label($this->lang->line('auth_field_confirmation_code'), $captcha['id']); ?></td>
						<td><?php
							echo form_input($captcha); ?>
							<span style="color: red;"><?php echo form_error($captcha['name']); ?></span>
						</td>
				</tr>
				<?php }
				}

				if ($is_ajax) { ?>
					<tr>
						<td valign="top" class="first"><?php echo form_label(_('Abonnement newsletter'), $mail_subscription['id']); ?></td>
						<td valign="top"><?php
							echo form_checkbox($mail_subscription); echo _('We will never sell your personal information - You can unsubscribe anytime.'); ?>
							<span style="color: red;"><?php echo form_error($mail_subscription['name']); ?><?php echo isset($errors[$mail_subscription['name']]) ? $errors[$mail_subscription['name']] : '' ; ?></span>
						</td>
					</tr>

                                <?php }
                                ?>
		</table>
		<?php
			if ($is_ajax) {
				echo '<div style="margin-left:10px; color: #000; margin-top: 20px">', _('By clicking "Create Account" you confirm that you accept the Terms of Service and Privacy Policy.'), '</div><br />';
			}
		?>
		<input id="register-page" type="submit" value="<?php echo _("S'enregistrer");?>" name="register">
		<?php echo form_close(); ?><br />
<?php if (!$is_ajax) { ?>
		<?php echo anchor($this->Db_links->get_link("connect"), _("Se connecter"), $login_attributes); ?>
	</div>
</div>
<?php } ?>
