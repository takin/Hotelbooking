<?php if ($this->wordpress->get_option('aj_enable_mobile') == true){
$csspath = $this->wordpress->get_option('aj_api_ascii');
  if (empty($csspath))
  {
    $csspath = $this->wordpress->get_option('aj_api_name');
  }
?>
<div class="box_content qr-code group box_round">
  <img src="<?php echo site_url();?>images/<?php echo $csspath;?>/qr-code.png" alt="QR code" />
	<span><?php echo _('Mobile Version')?></span>
</div>
<?php }?>