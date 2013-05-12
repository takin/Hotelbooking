<script type="text/javascript" src="<?php echo site_url('js/save_property.js'); ?>" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo site_url('js/pweb/includes/jorder-1.2.1.js'); ?>" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo site_url('js/pweb/includes/mustache.js'); ?>" charset="UTF-8"></script>

<?php $this->load->view('includes/save_property.php'); ?> 

<div id="sidebar" class="grid_4">
	<?php $this->load->view('includes/navuser'); ?> 
</div>

<script type="text/html" id="template-saved_property_item">
	<?php $this->load->view('mustache/saved_property_item'); ?> 
</script>

<div id="main" class="grid_12 user-auth">
	<div class="box_content box_round group">
		<h1 class="content_title"><?php echo _('My favorite properties');?></h1>

		<nav class="city-tools box_round group green_gradient_faded box_shadow_very_light" id="data_sort_controls">
			<ul class="sorting">
				<li class="title"><?php echo _("Classer par:");?></li>
				<li><a class="sorting" id="sortcity-tous" href="#"><span class="asc"><?php echo _("City");?></span></a></li>
				<li><a class="sorting activesort" id="sortdate-tous" href="#"><span class="asc"><?php echo _("Date");?></span></a></li>
				<li><a class="sorting" id="sortname-tous" href="#"><span class="asc"><?php echo _("Property name");?></span></a></li>
			</ul>
		</nav>

		<script type="text/javascript">
			var favorite_properties_url = '<?php echo site_url('user/favorite_properties_list'); ?>';
		</script>

		<input type="hidden" id="current_page" value="0">
		<input type="hidden" id="show_per_page" value="0">

		<div id="missing_hostels" style="display: none"><?php echo _('No properties were saved.'); ?></div>
		<div id="favorite_properties"></div>

		<div id="navi" class="pagination_pro" style="display:none;">
			<div id="resu" class="left_pagi">
				<span class="resultcount"></span>
				<?php echo _('of');?>
				<span class="resulttotal"></span>
				<?php echo _('Results');?>
			</div>
			<div id="page_navigation" class="page_navigation"></div>
		</div>
	</div>
</div>
