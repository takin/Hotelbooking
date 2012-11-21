<?php $this->load->view('includes/header'); ?>
	
		<?php // $this->load->view('includes/top_box'); ?>

    <?php
    if(!isset($date_selected))      $date_selected = NULL;
    if(!isset($numnights_selected)) $numnights_selected = NULL;
    if(!isset($bc_continent))       $bc_continent = NULL;
    if(!isset($bc_country))         $bc_country = NULL;
    if(!isset($bc_city))            $bc_city = NULL;

    /*$this->load->view("includes/search_box", array('date_selected' => $date_selected,
    																							 'current_view' => $current_view,
    																							 'numnights_selected' => $numnights_selected,
    																							 'bc_continent' => $bc_continent,
    																							 'bc_country' => $bc_country,
    																							 'bc_city' => $bc_city));*/
    //$this->load->view('includes/bottom_box');
    ?>
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