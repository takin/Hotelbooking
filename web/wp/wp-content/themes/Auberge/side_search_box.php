<div class="box_content box_round group side_search">

<?php
	if(isset($date_selected))
	{
		;
	}
	elseif(isset($_COOKIE["date_selected"]))
	{
		$date_selected = $_COOKIE["date_selected"];
	}
	else
	{
		$date_selected = get_date_default();
	}
	if(isset($numnights_selected))
	{
		;
	}
	elseif(isset($_COOKIE["numnights_selected"]))
	{
		$numnights_selected = $_COOKIE["numnights_selected"];
	}
	else
	{
		$numnights_selected = 2;
	}
	if(!empty($search_term))
	{
		;
	}
	//        elseif(!empty($_COOKIE["search_input_terms"]))
	//        {
	//          $search_term = urldecode($_COOKIE["search_input_terms"]);
	//        }

	if (isset($search_term))
	{
		$input_value = $search_term;
		$class = ''
		?>
		<script type="text/javascript">
		 $(function() {$("#search-city").addClass('disabled');$("#search-country").addClass('disabled');});
		</script>
		<?php
	}
	else
	{
		$input_value = _('Enter a city name or hostel name');
		$class = ' disabled';
	}
	?>
<script>
function keyaction(e)
{
	if (!$('ul#suggestion').is(':visible') && e.keyCode == 13)
	{
		goToSearchPage('<?php echo get_option('aj_api_url');?>','<?php echo _('Choisir le pays'); ?>','<?php _e('Pays introuvable','auberge');?>','<?php echo _('Choisir la ville'); ?>','<?php _e('Ville introuvable','auberge');?>','<?php _e('Date invalide','auberge');?>','search-country','search-city','datepick','search-night','search-currency','search-custom');
	}
}
</script>
<span class="search_title"><?php _e('Search Now','auberge');?></span>
<form class="group side_search" id="side_search" action="" method="post">



		<label class="notshow" for="search-country"><?php _e('Spécifier le pays','auberge');?></label>
		<select id="search-country" name="search-country" class="search_country" onchange="setCities('<?php echo _('Choisir la ville'); ?>','search-country','search-city');">
		<option value="no_country_selected"><?php echo _('Choisir le pays'); ?></option>
		</select>

		<label class="notshow" for="search-city"><?php _e('Spécifier la ville','auberge');?></label>
		<select id="search-city" name="search-city" class="search_city">
		<option value="no_city_selected"><?php echo _('Choisir la ville'); ?></option>
		</select>

		<?php /*?><div class="search_suggest_block">
			<label class="notshow" for="search-custom"><?php _e('Search by city or hostel name:','auberge');?></label>
			<input class="textinput text_suggest <?php echo $class;?>" type="text" id="search-custom" name="search-custom" onkeypress="keyaction(event)" onkeyup="searchSuggest(event,'<?php echo get_option('aj_api_url');?>','all',1,0);" autocomplete="off" value="<?php _e('Enter a city name or hostel name','auberge');?>" />
			<img style="display:none;" id="input-loading" src="<?php echo get_option('aj_api_url');?>images/input-loading.gif" alt="" />
			<span id="search-suggest"></span>
		</div><?php */?>

		<div class="group">
			<div class="left">
			<label for="search-date"><?php _e("Arrivée le:","auberge");?></label>
			<input type="text" id="datepick" name="search-date" class="search_date" value="<?php echo $date_selected;?>" />
			</div>
			<div class="left">
			<?php
 			$api_used = (get_option('aj_api_site_data')=='hb') ? TRUE : FALSE;
			select_nights(__('Nuits:','auberge'),"search-night","search-night",$numnights_selected, $api_used);?>
			</div>
		</div>
		<div class="more_choices" id="more_choices_side">
		<label for="search-currency"><?php _e("Currency:","auberge");?></label>
		<?php select_currency("search-currency","search-currency",get_selected_currency(),"",get_site_lang()); ?>
		</div>
		<div class="searchcenter">
		<input onfocus="this.blur()" type="button" name="search-submit" id="search-submit" class="box_round button-blue side_submit hoverit" onclick="goToSearchPage('<?php echo get_option('aj_api_url'); ?>','<?php echo _('Choisir le pays'); ?>','<?php _e('Pays introuvable','auberge');?>','<?php echo _('Choisir la ville'); ?>','<?php _e('Ville introuvable','auberge');?>','<?php _e('Date invalide','auberge');?>','search-country','search-city','datepick','search-night','search-currency','search-custom');" value="<?php _e('New Search');?>"/>
		</div>

		<input type="hidden" id="custom-type" name="custom-type" value =""/>
		<input type="hidden" id="custom-url"  name="custom-url" value =""/>

</form>
</div>
