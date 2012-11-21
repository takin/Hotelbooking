<div id="main" class="search-page">
	<div class="col1">
		<?php //TODO get the search term
		//$search_term = 'search term';
		?>
		<h1 class="green-bar-view"><?php echo _('Results for your research:')?> "<?php echo $search_term;?>" </h1>	
		<?php 
		$is_city = false;
		$is_country = false;
		$is_property = false;
		?>
		<?php if ($results){?>
		
		<?php
		//debug_dump($results);
		
		$city_results = "";
		$country_results = "";
		$property_results = "";
		$allcount = count($results);
		$citycount = 0;
		$countrycount = 0;
		$propertycount = 0;
		$keys = implode('|', explode(' ', $search_term_stripped));
		
		foreach($results as $result)
		{
			if($result->link_type == LINK_PROPERTY)
			{
				
			}
			switch($result->link_type)
			{
				case LINK_PROPERTY:
					$is_property = true;
					$propertycount++;
					$title = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $result->property_name.", ".$result->city_lang.", ". $result->country_lang);
					$property_results .= "<div class='property'><a href='".$this->Db_links->build_property_page_link($result->property_type, $result->property_name, $result->property_number, $this->site_lang)."'>".$title."</a>";
					$property_results .= "<p>".strip_tags(word_limiter($result->translated_desc, 40))."</p></div>";
					break;
				case LINK_CITY:
					$is_city = true;
					$citycount++;
					$cityname = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $result->city_lang);
					$countryname = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $result->country_lang);
					$city_results .= "<div class='city'><a href='".site_url(customurlencode($result->country_lang)."/".customurlencode($result->city_lang))."'>".$cityname.", ". $countryname."</a>";
					$city_results .= "<p>".sprintf(gettext('Auberges à %s incluant Auberges de Jeunesse à %s. 30,000 Auberges de jeunesses et logements pas chers à %s et dans le monde entier. Aussi, cartes des villes, photos, conseils, événements et guides des Auberges de Jeunesse à %s.'),$cityname,$cityname,$cityname,$cityname)."</p></div>";
					break;
				case LINK_COUNTRY:
					$is_country = true;
					$countrycount++;
					$continentname = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $result->continent_lang);
					$countryname = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $result->country_lang);
					$country_results .= "<div class='country'><a href='".site_url(customurlencode($result->continent_lang)."/".customurlencode($result->country_lang))."'>".$countryname.", ". $continentname."</a>";
					$country_results .= "<p>".sprintf(gettext('Auberges en %s incluant Auberges de Jeunesse en %s. 30,000 Auberges de jeunesses et logements pas chers en %s et dans le monde entier. Aussi, cartes des villes, photos, conseils, événements et guides des Auberges de Jeunesse en %s.'),$countryname,$countryname,$countryname,$countryname)."</p></div>";
				
					break;
			}
		}
		?>
		
		<ul class="tabing view-menu clearfix">
			<li><a id="#tous" href="#tous-list"><span><?php echo _("Tous")?> (<?php echo $allcount;?>)</span></a></li>
			<?php /*?><li><a id="#map" href="#city-map"><span><?php echo _("Carte")?></span></a></li> <?php */?>
			<?php if ($is_city){?>
			<li><a id="#city" href="#city-list"><span><?php echo _("City")?> (<?php echo $citycount;?>)</span></a></li>
			<?php }?>
			<?php if ($is_country){?>
			<li><a id="#country" href="#country-list"><span><?php echo _("Country")?> (<?php echo $countrycount;?>)</span></a></li>
			<?php }?>
			<?php if ($is_property){?>
			<li><a id="#property" href="#property-list"><span><?php echo _("Property")?> (<?php echo $propertycount;?>)</span></a></li>			
			<?php }?>
		</ul>		
		<?php }?>	
		<?php if ($results){?>
		<div id="tous-list" class="tabdiv ui-tabs-hide">              
			<div class="paging" id="paging1" style="clear:both;">
				
				<a rel="prev" href="#" class="page-prev action1"><?php echo _("précédente");?></a>
				<div class="state"><span class="count1">1</span> <?php echo _("de");?> <span class="total1">1</span></div>
				<a rel="next" href="#" class="page-next action1"><?php echo _("suivante");?></a> 
				      
									 
				<span class="sort-label"><?php echo _("Classer par:");?> </span>                
				<a class="sorting" id="sortname-tous" href="#"><span class="asc"><?php echo _("Hostel Name");?></span></a>			
												
				<select name="perpage1">                                  
					<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
					<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
					<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
				</select>			
	
			 </div>
										
			<div id="tous-page" class="show-results">	
				
				<?php echo $city_results;?>
				<?php echo $country_results;?>
				<?php echo $property_results;?>
				
			</div>
		</div>
		<?php }else{?>
		<div id="tous-list">      
		<div id="tous-page" class="show-results">	
		<p><strong><?php echo _('Sorry but we have not found any results. Please try again.')?></strong></p>
		</div>
		</div>
		<?php }?>
		<?php if ($is_city){?>
		<div id="city-list" class="tabdiv ui-tabs-hide">              
			<div class="paging" id="paging6" style="clear:both;">
				<?php /*?><a rel="first" href="#" class="page-first action1"><?php echo _("première");?></a><?php */?>
				<a rel="prev" href="#" class="page-prev action6"><?php echo _("précédente");?></a>
				<div class="state"><span class="count6">1</span> <?php echo _("de");?> <span class="total6">1</span></div>
				<a rel="next" href="#" class="page-next action6"><?php echo _("suivante");?></a> 
				<?php /*?><a rel="last" href="#" class="page-last action1"><?php echo _("dernière");?></a> <?php */?>               
									 
				<span class="sort-label"><?php echo _("Classer par:");?> </span>                
				<a class="sorting" id="sortname-city" href="#"><span class="asc"><?php echo _("Hostel Name");?></span></a>			
												
				<select name="perpage6">                                  
					<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
					<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
					<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
				</select>			
	
			 </div>
										
			<div id="city-page" class="show-results">	
				<?php echo $city_results;?>				
			</div>
			
		</div>
		<?php }?>
		<?php if ($is_country){?>
		<div id="country-list" class="tabdiv ui-tabs-hide">              
			<div class="paging" id="paging7" style="clear:both;">
				<?php /*?><a rel="first" href="#" class="page-first action1"><?php echo _("première");?></a><?php */?>
				<a rel="prev" href="#" class="page-prev action7"><?php echo _("précédente");?></a>
				<div class="state"><span class="count7">1</span> <?php echo _("de");?> <span class="total7">1</span></div>
				<a rel="next" href="#" class="page-next action7"><?php echo _("suivante");?></a> 
				<?php /*?><a rel="last" href="#" class="page-last action1"><?php echo _("dernière");?></a> <?php */?>               
									 
				<span class="sort-label"><?php echo _("Classer par:");?> </span>                
				<a class="sorting" id="sortname-country" href="#"><span class="asc"><?php echo _("Hostel Name");?></span></a>			
												
				<select name="perpage7">                                  
					<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
					<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
					<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
				</select>		
			</div>
										
			<div id="country-page" class="show-results">	
				<?php echo $country_results;?>				
			</div>
			
		</div>
		<?php }?>
		<?php if ($is_property){?>
		<div id="property-list" class="tabdiv ui-tabs-hide">              
			<div class="paging" id="paging8" style="clear:both;">
				<?php /*?><a rel="first" href="#" class="page-first action1"><?php echo _("première");?></a><?php */?>
				<a rel="prev" href="#" class="page-prev action8"><?php echo _("précédente");?></a>
				<div class="state"><span class="count8">1</span> <?php echo _("de");?> <span class="total8">1</span></div>
				<a rel="next" href="#" class="page-next action8"><?php echo _("suivante");?></a> 
				<?php /*?><a rel="last" href="#" class="page-last action1"><?php echo _("dernière");?></a> <?php */?>               
									 
				<span class="sort-label"><?php echo _("Classer par:");?> </span>                
				<a class="sorting" id="sortname-property" href="#"><span class="asc"><?php echo _("Hostel Name");?></span></a>			
												
				<select name="perpage8">                                  
					<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
					<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
					<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
				</select>		
			</div>
										
			<div id="property-page" class="show-results">	
				<?php echo $property_results;?>				
			</div>
			
		</div>
		<?php }?>
	</div>
</div>
<div id="sidebar">
	<?php $this->load->view('includes/popular_city'); ?>
	<?php $this->load->view('includes/siteinfo'); ?>
	<?php $this->load->view('includes/currency'); ?>
	<?php $this->load->view('includes/testimonials'); ?>
	<?php $this->load->view('includes/year-10'); ?>
	<?php $this->load->view('includes/groupe'); ?>
	
</div>