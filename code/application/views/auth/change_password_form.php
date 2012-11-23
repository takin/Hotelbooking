<?php
$old_password = array(
	'name'	=> 'old_password',
	'id'	=> 'old_password',
	'value' => set_value('old_password'),
	'size' 	=> 30,
);
$new_password = array(
	'name'	=> 'new_password',
	'id'	=> 'new_password',
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$confirm_new_password = array(
	'name'	=> 'confirm_new_password',
	'id'	=> 'confirm_new_password',
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size' 	=> 30,
);
?>
<div id="sidebar" class="grid_4">
	<?php $this->load->view('includes/navuser'); ?>
</div>

<div id="main" class="grid_12 user-auth">
	<div class="box_content box_round group">
    <h1 class="content_title"><?php echo _('Changement de mot de passe');?></h1>
   	<h3 class="dotted-line"><?php echo _('Veuillez remplir tous les champs');?></h3>
		<?php echo form_open($this->uri->uri_string()); ?>
		<table class="profile">
			<tr>
				<td class="first"><?php echo form_label($this->lang->line('auth_field_old_password'), $old_password['id']); ?></td>
				<td><?php echo form_password($old_password); ?></td>
				<td style="color: red;"><?php echo form_error($old_password['name']); ?><?php echo isset($errors[$old_password['name']])?$errors[$old_password['name']]:''; ?></td>
			</tr>
			<tr>
				<td class="first"><?php echo form_label($this->lang->line('auth_field_new_password'), $new_password['id']); ?></td>
				<td><?php echo form_password($new_password); ?></td>
				<td style="color: red;"><?php echo form_error($new_password['name']); ?><?php echo isset($errors[$new_password['name']])?$errors[$new_password['name']]:''; ?></td>
			</tr>
			<tr>
				<td class="first"><?php echo form_label($this->lang->line('auth_field_confirm_new_password'), $confirm_new_password['id']); ?></td>
				<td><?php echo form_password($confirm_new_password); ?></td>
				<td style="color: red;"><?php echo form_error($confirm_new_password['name']); ?><?php echo isset($errors[$confirm_new_password['name']])?$errors[$confirm_new_password['name']]:''; ?></td>
			</tr>
		</table>
		<input id="profile-submit" type="submit" value="<?php echo _("Modifier"); ?>" name="submit">
		<?php echo form_close(); ?>

    </div>
</div>