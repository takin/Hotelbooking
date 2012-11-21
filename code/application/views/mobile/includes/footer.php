<?php if ($current_view != 'map_view'){?>
</div>
<div id="footer">
	<div class="footer-content">
		<ul class="group">
			<li class="dot"><a href="<?php echo site_url();?>"><?php echo _('Home page')?></a></li>
			<?php /*?><li class="dot"><a href="<?php echo site_url();?>?site=full"><?php echo _("View Full Site");?></a></li><?php */?>
			<?php $about = $this->wordpress->get_option('aj_page_about'); if (!empty($about)){?><li class="dot"><a href="<?php echo $about; ?>?print=mobile"><?php echo _("About us");?></a></li><?php }?>
			<li class="dot"><a href="<?php echo $this->wordpress->get_option('aj_page_faq'); ?>?print=mobile"><?php echo _("Aide / FAQ / Nous Joindre");?></a></li>
			<li class="dot"><a href="<?php echo $this->wordpress->get_option('aj_page_conf'); ?>?print=mobile"><?php echo _("Confidentialité");?></a></li>
			<li class="dot"><a href="<?php echo $this->wordpress->get_option('aj_page_cond'); ?>?print=mobile"><?php echo _("Conditions");?></a></li>
			<li class="dot"><?php echo login_check($this->tank_auth->is_logged_in(),"<a href=\"".site_url($this->Db_links->get_link("logout"))."\">"._("Se déconnecter")."</a>","<a href=\"".site_url($this->Db_links->get_link("connect"))."\" onclick=\"toggleById(); return false;\">"._("Se connecter")."</a>"); ?></li>
		</ul>
	</div>
</div>
<?php }?>
<?php echo $this->wordpress->get_option('aj_google_analytic'); ?>
</body>
</html>