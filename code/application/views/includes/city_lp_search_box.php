  <form class="group lp_search" id="lp_search" action="" method="post">
		<script>
		$(document).ready(function(){
				<?php if(!isset($country_selected)||($country_selected===NULL)):?>
				 loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>","<?php echo _('Chargement...');?>",'cities',cities,'search-country','search-city');
				<?php elseif(!isset($city_selected)||($city_selected===NULL)):?>
				 loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>","<?php echo _('Chargement...');?>",'cities',cities,'search-country','search-city',"<?php echo $country_selected;?>");
				<?php else:?>
				 loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>","<?php echo _('Chargement...');?>",'cities',cities,'search-country','search-city',"<?php echo $country_selected;?>","<?php echo $city_selected; ?>");
				<?php endif;?>
		});
		function keyaction(e)
		{
			if (!$('ul#suggestion').is(':visible') && e.keyCode == 13)
			{
				goToSearchPage('<?php echo site_url();?>','<?php echo _('Choisir le pays'); ?>','<?php echo _('Pays introuvable'); ?>','<?php echo _('Choisir la ville'); ?>','<?php echo _('Ville introuvable'); ?>','<?php echo _('Date invalide'); ?>','search-country','search-city','datepick','search-night','search-currency','search-custom');
			}
		}
		</script>
		<?php
		if(!empty($date_selected))
		{
			;
		}
		elseif(!empty($_COOKIE["date_selected"]))
		{
			$date_selected = $_COOKIE["date_selected"];
		}
		else
		{
			$date_selected = get_date_default();
		}
		if(!empty($numnights_selected))
		{
			;
		}
		elseif(!empty($_COOKIE["numnights_selected"]))
		{
			$numnights_selected = $_COOKIE["numnights_selected"];
		}
		else
		{
			$numnights_selected = 2;
		}
		?>
		<script>
		$(document).ready(

			function()
			{
				var date_cookie = getCookie('date_selected');
				if(isValidDate(date_cookie))
				{
					var date_array = date_cookie.split('-');
					var date_avail 	= new Date(date_array[0],date_array[1]-1,date_array[2]);
					$("#datepick").datepicker( "setDate" , date_avail );
				}
				else
				{
					var date_avail = new Date();
					date_avail.setDate(date_avail.getDate()+10);
					$("#datepick").datepicker( "setDate" , date_avail );
				}

				var numnight_avail = getCookie('numnights_selected');
				if(numnight_avail)
				{
					document.getElementById('search-night').value = numnight_avail;
				}
				else
				{
					numnight_avail = 2;
					document.getElementById('search-night').value = numnight_avail;
				}

				<?php /*?>var search_input_terms = decodeURI(getCookie('search_input_terms'));
				if(search_input_terms)
				{
					document.getElementById('search-custom').value = search_input_terms;
				}
				else
				{
					document.getElementById('search-custom').value = '<?php echo _('Enter a city name or hostel name');?>';
				}<?php */?>

				function getURLParameter(name) {
						return decodeURI(
								(RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
						);
				}
				var currency_value = getURLParameter('currency');
				if(!currency_value || currency_value == "null")
				{
					currency_value = getCookie('currency_selected');
				}
				if(currency_value)
				{
					//document.getElementById('search-currency').value = currency_value;
				}
				else
				{
					currency_value = '<?php echo $this->config->item('site_currency_default')?>';
					//document.getElementById('search-currency').value = currency_value;
				}
			}
			);
		</script>
		<div class="group">
			<div class="small_block">
			<input id="type_search_choice" class="type_search" type="radio" name="type_search" value="1" checked="checked">
			</div>
			<div class="medium_block" style="margin-right:2%;">
			<label class="notshow" for="search-country"><?php echo _('Spécifier le pays');?></label>
			<select id="search-country" name="search-country" class="search_country" onchange="setCities('<?php echo _('Choisir la ville'); ?>','search-country','search-city');">
			<option value="no_country_selected"><?php echo _('Choisir le pays'); ?></option>
			</select>
			</div>
			<div class="medium_block">
			<label class="notshow" for="search-city"><?php echo _('Spécifier la ville');?></label>
			<select id="search-city" name="search-city" class="search_city">
			<option value="no_city_selected"><?php echo _('Choisir la ville'); ?></option>
			</select>
			</div>
		</div>

		<div class="group">
			<div class="small_block">
				<input id="type_search_keyword" class="type_search" type="radio" name="type_search" value="2">
			</div>
			<div class="large_block search_suggest_block">
				<input class="textinput text_suggest disabled" type="text" id="search-custom" name="search-custom" onkeypress="keyaction(event)" onkeyup="searchSuggest(event,'<?php echo site_url();?>','all',1,0);" autocomplete="off" value="<?php echo _('Enter a city name or hostel name');?>" />
				<img style="display:none;" id="input-loading" src="<?php echo base_url();?>images/input-loading.gif" alt="" />
				<span id="search-suggest"></span>
			</div>
		</div>

		<div class="group" style="margin-left:6%;">
			<div class="left">
			<label for="search-date"><?php echo _('Arrivée le:');?></label>
			<input type="text" id="datepick" name="datepick" class="search_date" value="<?php echo $date_selected;?>" />
			</div>
			<div class="left">
			<?php
			$hb_api_used = ($this->api_used == HB_API) ? TRUE : FALSE;
			select_nights(_('Nuits:'),"search-night","search-night",$numnights_selected, $hb_api_used);
			?>
			</div>
			<?php if($current_view != "auth/reset_password_form"){?>
			<!-- // this goes to header (@sasya karpin) 
            <div class="left">
			<label for="search-currency"><?php echo _("Devise:");?></label>
			<?php $this->Db_currency->select_currency("search-currency","search-currency",$this->config->item('site_currency_selected'),"",$this->site_lang); ?>
			</div> -->
			<?php }?>
			<div class="full">
			<input onfocus="this.blur()" type="button" name="search-submit" class="box_round button-green side_submit hoverit" id="search-submit" onclick="goToSearchPage('<?php echo site_url();?>','<?php echo _('Choisir le pays'); ?>','<?php echo _('Pays introuvable'); ?>','<?php echo _('Choisir la ville'); ?>','<?php echo _('Ville introuvable'); ?>','<?php echo _('Date invalide'); ?>','search-country','search-city','datepick','search-night','search-currency','search-custom')" value="<?php echo _('Search Now')?>"/>
			</div>
		</div>

		<input type="hidden" id="custom-type" name="custom-type" value =""/>
		<input type="hidden" id="custom-url"  name="custom-url" value =""/>
		<input type="hidden" id="district_id"  name="district_id" value ="<?php echo (!empty($filters["district"]) ? $filters["district"]->district_id : 0);?>"/>
		<input type="hidden" id="landmark_id"  name="landmark_id" value ="<?php echo (!empty($filters["landmark"]) ? $filters["landmark"]->landmark_id : 0);?>"/>
		<input type="hidden" id="type_filter"  name="type_filter" value ="<?php echo (!empty($filters["type"]) ? $filters["type"] : "");?>"/>
</form>