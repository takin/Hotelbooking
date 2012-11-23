<?php if ($this->wordpress->get_option('aj_group_url') != ''){ ?>
<div class="widget">
  <div class="gray-block">
    <a class="sidebar-groupe" title="<?php echo _("Réserver une auberge de jeunesse pour un groupe");?>"  href="<?php echo $this->wordpress->get_option('aj_group_url'); ?>"><?php echo _('Réservation de groupes');?></a>
  </div>  
</div>
<?php }?>
