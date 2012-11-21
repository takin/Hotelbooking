<div class="contact-form basic">
	<form action="" method="post" class="cform" id="sendEmail">
	
	<ul>
			<li><label for="name"><span id="name1"><?php _e('Nom','auberge');?></span></label><input class="text" name="name" id="name" type="text" /></li>
			<li><label for="email"><span id="email1"><?php _e('Email','auberge');?></span></label><input class="text" name="email" id="email" type="text" /></li>
			<li><label for="subject"><span id="subject1"><?php _e('Sujet','auberge');?></span></label><input class="text" name="subject" id="subject" type="text" /></li>
			<li><label for="numreserv"><span id="subject1"><?php _e('Numéro de réservation','auberge');?></span></label><input class="text" name="numreserv" id="numreserv" type="text" /></li>
			<li><label for="message"><span id="message1"><?php _e('Message','auberge');?></span></label><textarea name="message" id="message" class="text" rows="10" cols="30"></textarea></li>
			<li><label for="verif"><span id="verif1"><?php _e('Sécurité: combien font 7 plus 2?','auberge');?></span></label><input class="text" name="verif" id="verif" type="text" /></li>
			<li class="buttons">
			<div class="submit-button" style="margin-right:0px; margin-left:0px;">   
			<input type="submit" id="sendemail-submit" class="submit-green green-button" value="<?php _e('Submit','auberge');?>"/>
			<span class="light"></span>
			</div>
			
			<input type="hidden" name="submitted" id="submitted" value="true"/>
			<input type="hidden" name="adminemail" id="adminemail" value="<?php bloginfo('admin_email') ?>" />
			<input type="hidden" name="templateurl" id="templateurl" value="<?php bloginfo('template_url') ?>" />
			<input type="hidden" name="permalink" id="permalink" value="<?php echo $perma; ?>" />
			<input type="hidden" name="sitename" id="sitename" value="<?php bloginfo('name') ?>" />
			<input type="hidden" name="validation" id="validation" value="<?php _e('Merci pour votre commentaire. Nous vous répondrons d\'ici peu.','auberge');?>" />
			<input type="hidden" name="errorstring" id="errorstring" value="<?php _e('Veuillez remplir les champs requis correctement.','auberge');?>" />
			
			
			</li>
	</ul>
	<div style="clear:both;"></div>
	</form>
	
</div>