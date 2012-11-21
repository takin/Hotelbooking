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

<div id="content" class="user-view">

	<div class="page-meta group">
		<h1 class="text-shadow-wrapper"><?php echo _('Vous enregistrer');?></h1>
	</div>
	<?php echo form_open($this->uri->uri_string()); ?>
	<div class="white-back round-corner5 border-around basic form">
		
     <h2><?php echo _('Créer votre compte');?></h2>
     <p><?php echo _("La création d'un compte vous donnera accès à votre historique de réservations et d'évaluations pour les auberges de jeunesse. Veuillez entrer votre adresse de courriel et nous vous enverrons un mot de passe.");?>
		 <ul class="group">
				
				<?php if (form_error($email['name']) != ''){?>
					<li class="error round-corner5 group"><span><?php echo form_error($email['name']);?></span></li>
				<?php }if (form_error($password['name']) != ''){?>
					<li class="error round-corner5 group"><span><?php echo form_error($password['name']);?></span></li>
				<?php }if (form_error($confirm_password['name']) != ''){?>
					<li class="error round-corner5 group"><span><?php echo form_error($confirm_password['name']);?></span></li>
				<?php }?>
				<?php if ($use_username) { ?>
				<li>
					<?php echo form_label("Nom d'usager", $username['id']); ?>
					<input type="text" class="text" id="username" value="" name="username">
				</li>
				<?php } ?>
				<li>
					<?php echo form_label($this->lang->line('auth_field_email'), $email['id']); ?>
					<input type="text" class="text" id="email" value="" name="email">
				</li>
				<?php if($choose_password == TRUE):?>
				<li>
					<?php echo form_label($this->lang->line('auth_field_password'), $password['id']); ?>
					<input type="password" class="text" id="password" value="" name="password">
				</li>
				<li>
					<?php echo form_label($this->lang->line('auth_field_confirm_new_password'), $confirm_new_password['id']); ?>
					<input type="password" class="text" id="confirm_password" value="" name="confirm_password">
				</li>
				<?php endif;?>
				
				</ul>    

	</div>
	<div class="submit-button">
		<input type="submit" name="register" class="submit-green green-button" value="<?php echo _("S'enregistrer");?>" />
		
	</div>
	
	<?php echo form_close(); ?>	
</div>