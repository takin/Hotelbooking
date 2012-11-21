<?php if(!empty($bc_continent)):?>
<ul id="bread-crumb" class="group">
<?php /*?><li class="first"><a href="<?php echo base_url(); ?>"><?php echo $this->config->item('site_name');?></a></li><?php */?>
<li class="first"><a href="<?php echo site_url(customurlencode($bc_continent));?>"><?php printf(gettext('%s-Map'),my_mb_ucfirst($bc_continent));?><?php //echo my_mb_ucfirst($bc_continent); ?></a></li>
<?php if(!empty($bc_country)):?>
<li><a href="<?php echo site_url(customurlencode($bc_continent)."/".customurlencode($bc_country));?>"><?php printf(gettext('%s-Map'),my_mb_ucfirst($bc_country)); ?></a></li>
<?php if(!empty($bc_city)):?>
<li><a href="<?php echo site_url(customurlencode($bc_country)."/".customurlencode($bc_city)."/"); ?>"><?php printf(gettext('%s-Map'),my_mb_ucfirst($bc_city));?></a></li>
<?php endif; //ville?>
<?php endif; //pays?>
</ul>
<?php else:?>
<ul id="bread-crumb" class="clearfix">
<!-- Bread Crumb function-->
<li style="background:none; list-style:none;">&nbsp;</li>
<?php /*?><li class="first"><a href="<?php echo site_url();?>"><?php printf(gettext("Bienvenue sur %s !"),$this->config->item('site_name'));?></a></li><?php */?>
</ul>
<?php endif; //continent?>