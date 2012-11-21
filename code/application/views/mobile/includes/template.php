<?php $this->load->view('mobile/includes/header'); ?>

<?php
 if(isset($current_view_dir))
{
  $this->load->view($current_view_dir.$current_view); 
}
else 
{
  $this->load->view($current_view); 
}
?>

<?php $this->load->view('mobile/includes/footer'); ?>