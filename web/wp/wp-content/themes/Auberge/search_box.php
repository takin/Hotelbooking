<div class="box_content box_round main_search">
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
	$date_selected = date_conv($date_selected, "%e %B, %Y");

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
<script type="text/javascript">
$(document).ready(
		function()
		{
			var date_cookie = getCookie('date_selected');
			if(isValidDate(date_cookie))
			{
				var date_avail 	= new Date(date_cookie.replace('-',',','g'));
				$("#datepick").datepicker( "setDate" , date_avail );

			}
			else
			{
				var date_avail = new Date();
				date_avail.setDate(date_avail.getDate()+10);
				$("#datepick").datepicker( "setDate" , date_avail );
			}
		}
	);
function keyaction(e)
{
	if (!$('ul#suggestion').is(':visible') && e.keyCode == 13)
	{
		goToSearchPage('<?php echo get_option('aj_api_url');?>','<?php echo _('Choisir le pays'); ?>','<?php _e('Pays introuvable','auberge');?>','<?php echo _('Choisir la ville'); ?>','<?php _e('Ville introuvable','auberge');?>','<?php _e('Date invalide','auberge');?>','search-country','search-city','datepick','search-night','search-currency','search-custom');
	}
}
</script>

<form class="clearfix" id="search_form_wp" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
	<span class="search_title"><?php _e("Auberges de jeunesse, Hôtels, Appartements, Chambres d'hôtes, Bed and Breakfast, Pensions - Plus de 30000!!",'auberge');?></span>
	<div class="group">
		<div class="small_block">
			<input id="type_search_choice" class="type_search" type="radio" name="type_search" value="1" checked="checked">
		</div>
		<div class="medium_block" style="margin-right:2%;">
		<label class="notshow" for="search-country"><?php _e('Spécifier le pays','auberge');?></label>
		<select tabindex="1" id="search-country" name="search-country" class="search_country" onchange="setCities('<?php echo _('Choisir la ville'); ?>','search-country','search-city');">
		<option value="no_country_selected"><?php echo _('Choisir le pays'); ?></option>
		</select>
		</div>
		<div class="medium_block">
		<label class="notshow" for="search-city"><?php _e('Spécifier la ville','auberge');?></label>
		<select tabindex="2" id="search-city" name="search-city" class="search_city">
		<option value="no_city_selected"><?php echo _('Choisir la ville'); ?></option>
		</select>
		</div>
	</div>
	<div class="group">
		<div class="small_block">
			<input id="type_search_keyword" class="type_search" type="radio" name="type_search" value="2">
		</div>
		<div class="large_block search_suggest_block">
			<label class="notshow" for="search-custom"><?php _e('Search by city or hostel name:','auberge');?></label>
			<input tabindex="3" class="textinput text_suggest <?php echo $class;?>" type="text" id="search-custom" name="search-custom" onkeypress="keyaction(event)" onkeyup="searchSuggest(event,'<?php echo get_option('aj_api_url');?>','all',1,0);" autocomplete="off" value="<?php _e('Enter a city name or hostel name','auberge');?>" />
			<img style="display:none;" id="input-loading" src="<?php echo get_option('aj_api_url');?>images/input-loading.gif" alt="" />
			<span id="search-suggest"></span>
		</div>
	</div>
	<?php /*?>
	<?php // TODO later, support filter preset ?>
	<div class="group checkbox_group">
		<span><?php _e("Property Types","auberge");?></span>
		<ul>
		<li><input type="checkbox" tabindex="4" id="type_hostel" name="types[]" value="hostel" checked="true"> <label for="type_hostel">Hostels</label></li>
		<li><input type="checkbox" tabindex="5" id="type_bb" name="types[]" value="bb" checked="true"> <label for="type_bb">Bed and Breakfasts</label></li>
		<li><input type="checkbox" tabindex="6" id="type_hotel" name="types[]" value="hotel" checked="true"> <label for="type_hotel">Hotels</label></li>
		<li><input type="checkbox" tabindex="7" id="type_apartment" name="types[]" value="apartment" checked="true"> <label for="type_apartment">Apartments</label></li>
		<li><input type="checkbox" tabindex="8" id="type_Campsite" name="types[]" value="campsite" checked="true"> <label for="type_Campsite">Campsites</label></li>
		</ul>
	</div><?php */?>
	<div class="group bottom_group">
		<div class="left">
		<label for="datepick"><?php _e("Arrivée le:","auberge");?></label>
		<input tabindex="9" type="text" id="datepick" name="search-date" class="search_date" value="<?php echo $date_selected;?>" />
		</div>
		<div class="left">
			<?php $api_used = (get_option('aj_api_site_data')=='hb') ? TRUE : FALSE;
				select_nights(__('Nuits:','auberge'),"search-night","search-night",$numnights_selected,$api_used);?>
		</div>
		<!-- <div class="left">
			<label for="search-currency"><?php _e("Currency:","auberge");?></label>
			<?php select_currency("search-currency","search-currency",get_selected_currency(),"",get_site_lang()); ?>
		</div> -->
		<div class="full">
			<input onfocus="this.blur()" type="button" name="search-submit" id="search-submit" class="box_round button-green side_submit hoverit" value="<?php _e('Search Now','auberge');?>" onclick="goToSearchPage('<?php echo get_option('aj_api_url'); ?>','<?php echo _('Choisir le pays'); ?>','<?php _e('Pays introuvable','auberge');?>','<?php echo _('Choisir la ville'); ?>','<?php _e('Ville introuvable','auberge');?>','<?php _e('Date invalide','auberge');?>','search-country','search-city','datepick','search-night','search-currency','search-custom');return false;"/>
			<?php /*?><p class="search_text">"<?php _e('Best price. We guarantee it.','auberge');?>"</p>	<?php */?>
		</div>


	<input type="hidden" id="custom-type" name="custom-type" value =""/>
	<input type="hidden" id="custom-url"  name="custom-url" value =""/>

</div>


	<?php
	/*
	$last_booking = get_last_booking_info();

	if(!is_null($last_booking))
	{
		?>
		<div class="last-booking">
		<p><strong><?php _e('Dernière Réservation :','auberge');?></strong></p>
		<p><?php //echo $last_booking->guests; echo 'pers - ';?><?php if($last_booking->num_nights ==1){ echo $last_booking->num_nights." ".__("nuit",'auberge');}else{echo $last_booking->num_nights." ".__("nuits",'auberge');}?> -
		<a href="<?php echo build_property_page_link($last_booking->property_type, $last_booking->property_name, $last_booking->property_number); ?>"><?php echo $last_booking->property_name;?></a></p>
		</div>
		<?php
	}
	*/
	?>

</form>

<?php
/*if (get_option('aj_show_stamp'))
{

	$csspath = get_option('aj_api_ascii');

	if(empty($csspath))
	{
		$csspath = get_option('aj_api_name');
	}
	?>
	<a class="openup" href="<?php echo get_option('aj_api_url');?>guarantee"><img class="guarantee" src="<?php echo get_option('aj_api_url');?>images/<?php echo $csspath; ?>/guarantee.png" alt="" /></a>
	<?php
}*/
?>

<!-- End conditions -->
