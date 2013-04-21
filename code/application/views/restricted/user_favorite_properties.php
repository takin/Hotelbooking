<div id="sidebar" class="grid_4">
	<?php $this->load->view('includes/navuser'); ?> 
</div>

<div id="main" class="grid_12 user-auth">
	<div class="box_content box_round group">
		<h1 class="content_title"><?php echo _('My favorite properties');?></h1>

		<nav class="city-tools box_round group green_gradient_faded box_shadow_very_light" id="data_sort_controls">
			<ul class="sorting">
				<li class="title"><?php echo _("Classer par:");?></li>
				<li><a class="sorting" id="sortcity-tous" href="#"><span class="asc"><?php echo _("City");?></span></a></li>
				<li><a class="sorting activesort" id="sortdate-tous" href="#"><span class="asc"><?php echo _("Date");?></span></a></li>
				<li><a class="sorting" id="sortname-tous" href="#"><span class="asc"><?php echo _("Name");?></span></a></li>
			</ul>
		</nav>
	</div>
</div>
