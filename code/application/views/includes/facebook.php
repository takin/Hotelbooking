<?php if ($this->wordpress->get_option('aj_social_facebook') != ''){ ?>
<div class="widget">
  <div class="gray-block">
    <a class="sidebar-facebook" href="<?php echo $this->wordpress->get_option('aj_social_facebook')?>"><?php echo _("Joignez-vous à nous sur Facebook");?></a>
  </div>
</div>
<?php }?>
