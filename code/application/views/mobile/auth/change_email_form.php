<?php
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'size'	=> 30,
);
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value'	=> set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
?>
<div id="content" class="user-view">

	<div class="page-meta group">
		<h1 class="text-shadow-wrapper"><?php echo _('Changement de mot de passe');?></h1>
	</div>
	<?php echo form_open($this->uri->uri_string()); ?>
	<div class="white-back round-corner5 border-around basic form">
		<ul class="group">
			<?php if (form_error($password['name']) != ''){?>
			<li class="error round-corner5 group"><span><?php echo form_error($password['name']);?></span></li>
			<?php }if (form_error($email['name']) != ''){?>
			<li class="error round-corner5 group"><span><?php echo form_error($email['name']);?></span></li>
			<?php }?>
			<li>
				<?php echo form_label('Password', $password['id']); ?>
				<input type="password" class="text" id="password" value="" name="password">
			</li>
			<li>
				<?php echo form_label('New email address', $email['id']); ?>
				<input type="text" class="text" id="email" value="" name="email">
			</li>			
		</ul>  
	</div>
	
	<div class="submit-button">
		<input type="submit" class="submit-green green-button" value="<?php echo _("Send confirmation email"); ?>" />
		
	</div>
	
	<?php echo form_close(); ?>	
</div>  