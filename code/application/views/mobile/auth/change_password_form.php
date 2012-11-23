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

<div id="content" class="user-view">

	<div class="page-meta group">
		<h1 class="text-shadow-wrapper"><?php echo _('Changement de mot de passe');?></h1>
	</div>
	<?php echo form_open($this->uri->uri_string()); ?>
	<div class="white-back round-corner5 border-around basic form">
		<ul class="group">
		<?php if (form_error($old_password['name']) != ''){?>
			<li class="error round-corner5 group"><span><?php echo form_error($old_password['name']);?></span></li>
		<?php }if (form_error($new_password['name']) != ''){?>
			<li class="error round-corner5 group"><span><?php echo form_error($new_password['name']);?></span></li>
		<?php }if (form_error($confirm_new_password['name']) != ''){?>
			<li class="error round-corner5 group"><span><?php echo form_error($confirm_new_password['name']);?></span></li>
		<?php }?>
		
			<li>
				<?php echo form_label($this->lang->line('auth_field_old_password'), $old_password['id']); ?>
				<input type="password" class="text" id="old_password" value="" name="old_password">
			</li>
			<li>
				<?php echo form_label($this->lang->line('auth_field_new_password'), $new_password['id']); ?>
				<input type="password" class="text" id="new_password" value="" name="new_password">
			</li>
			<li>
				<?php echo form_label($this->lang->line('auth_field_confirm_new_password'), $confirm_new_password['id']); ?>
				<input type="password" class="text" id="confirm_new_password" value="" name="confirm_new_password">
			</li>
		</ul>    

	</div>
	<div class="submit-button">
		<input type="submit" class="submit-green green-button" value="<?php echo _("Modifier"); ?>" />
		
	</div>
	
	<?php echo form_close(); ?>	
</div>     
