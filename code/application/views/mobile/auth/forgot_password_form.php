<?php
$login = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'value' => set_value('login'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
if ($this->config->item('use_username', 'tank_auth')) {
	$login_label = $this->lang->line('auth_field_login');
} else {
	$login_label = $this->lang->line('auth_field_email');
}
?>
<div id="content" class="user-view">

	<div class="page-meta group">
		<h1 class="text-shadow-wrapper"><?php echo _('Mot de passe oublié');?></h1>
	</div>
  
	<?php echo form_open($this->uri->uri_string()); ?>
	<div class="white-back round-corner5 border-around basic form">
     
		<h2><?php echo _('Veuillez entrer le courriel associé à votre compte');?></h2>
		<ul class="group">
			<?php if (isset($errors[$login['name']])){?>
				<li class="error round-corner5 group"><span><?php echo $errors[$login['name']];?></span></li>
			<?php }?>
			<li>
			<?php echo form_label($login_label, $login['id']); ?>
			<input type="text" class="text" id="login" value="" name="login">
			</li>
			<li><a href="<?php echo site_url($this->Db_links->get_link("connect"));?>">&laquo; <?php echo _("Se connecter");?></a></li>
		</ul>
		
	</div>
	
	<div class="submit-button">
		<input type="submit" class="submit-green green-button" value="<?php echo _("Obtenir nouveau mot de passe"); ?>" />
		
	</div>
	
	<?php echo form_close(); ?>	
	
</div>