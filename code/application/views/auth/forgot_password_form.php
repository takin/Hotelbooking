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
<div id="main" class="grid_16 user-auth">
	<div class="box_content box_round group">
		<h1 class="content_title"><?php echo _('Mot de passe oublié');?></h1>
		<p><?php echo _('Veuillez entrer le courriel associé à votre compte');?></p>

		<?php echo form_open($this->uri->uri_string()); ?>
		<table>
			<tr>
				<td class="first"><?php echo form_label($login_label, $login['id']); ?></td>
				<td><?php echo form_input($login); ?></td>
				<td style="color: red;"><?php echo form_error($login['name']); ?><?php echo isset($errors[$login['name']])? $errors[$login['name']]:''; ?></td>
			</tr>
		</table>
		<input id="password-submit" type="submit" value="<?php echo _("Obtenir nouveau mot de passe"); ?>" name="submit">
		<?php echo form_close(); ?>  <br />
		<a href="<?php echo site_url($this->Db_links->get_link("connect"));?>">&laquo; <?php echo _("Se connecter");?></a>
    </div>
</div>