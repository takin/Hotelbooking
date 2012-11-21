<?php $this->load->view('includes/header'); ?> 
<div id="content">
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
</div>
<?php $this->load->view('includes/footer'); ?>