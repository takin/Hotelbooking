<?php
$login = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'value' => set_value('login'),
	'maxlength'	=> 80,
	'size'	=> 30,
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
?>
<div id="content" class="user-view">

	<div class="page-meta group">
		<h1 class="text-shadow-wrapper"><?php echo _('Connexion à votre compte');?></h1>
	</div>
  
	<?php echo form_open($this->uri->uri_string()); ?>
	<div class="white-back round-corner5 border-around basic form">
		<ul class="group">
			<?php if (isset($errors[$login['name']])){?>
				<li class="error round-corner5 group"><span><?php echo $errors[$login['name']];?></span></li>
			<?php }elseif (isset($errors[$password['name']])){?>
				<li class="error round-corner5 group"><span><?php echo $errors[$password['name']];?></span></li>
			<?php }?>
			
			<li>
			<label for="search-custom"><?php echo _("Courriel:");?></label>
      <input type="text" value="" name="login" id="login" class="text">				
			</li>
			
			<li>
			<label for="search-custom"><?php echo _("Mot de passe:");?></label>
			<input type="password" value="" name="password" id="password" class="text">
			</li>
			<li>
			<input type="checkbox" id="remember" value="1" name="remember" checked="checked">
      <?php echo $this->lang->line('auth_field_remember_me'); ?>
			</li>
			
			<li><?php echo anchor($this->Db_links->get_link("user_forgot_pass"), _("Mot de passe oublié")); ?></li>
	 	</ul>    

	</div>
	
	<div class="submit-button">
		<input type="submit" class="submit-green green-button" value="<?php echo _("Se connecter"); ?>" />
		
	</div>
	
	<?php echo form_close(); ?>	
 
</div>                    
