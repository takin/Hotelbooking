<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sitemap extends I18n_site {

	function Sitemap()
	{
		parent::I18n_site();
		$this->load->model('Db_hb_city','db_hb_city');
		$this->load->library('sitemaps');
	}
	
	function index()
	{
		
	}
	
	function create()
	{
		
		$this->load->model('Db_hb_city'); 
		$this->load->model('Db_hb_country');
		$this->load->model('Db_hb_hostel');
		      
		$continents = $this->db_hb_city->get_all_continents($this->site_lang);
				
		foreach($continents AS $continent)
		{
			$segments = array(customurlencode($continent->continent_name));
			
			$item = array(
				"loc" => site_url($segments),
				// ISO 8601 format - date("c") requires PHP5
				"lastmod" => date("c", time()),
				"changefreq" => "hourly",
				"priority" => "0.8"
			);
			
			$this->sitemaps->add_item($item);
			
			$countries = $this->Db_hb_city->cityCountryList_DB($continent->continent_name, NULL, $this->site_lang);
			
			foreach($countries AS $country)
			{
				$segments = array(customurlencode($country->countryContinentTranslated), customurlencode($country->countryNameTranslated));
				
				$item = array(
					"loc" => site_url($segments),
					// ISO 8601 format - date("c") requires PHP5
					"lastmod" => date("c", time()),
					"changefreq" => "hourly",
					"priority" => "0.8"
				);
				
				$this->sitemaps->add_item($item);
				
				$cities = $this->Db_hb_city->cityCountryList_DB(NULL, $country->countryNameTranslated, $this->site_lang);
				
				if(count($cities->Country->Cities) > 0) {
					foreach($cities->Country->Cities AS $city)
					{
						$segments = array(customurlencode($country->countryNameTranslated), customurlencode($city->cityNameTranslated));
				
						$item = array(
							"loc" => site_url($segments),
							// ISO 8601 format - date("c") requires PHP5
							"lastmod" => date("c", time()),
							"changefreq" => "hourly",
							"priority" => "0.8"
						);
					
						$this->sitemaps->add_item($item);

						$country_select = urldecode(customurldecode(customurlencode($country->countryNameTranslated)));
        				$city_select = urldecode(customurldecode(customurlencode($city->cityNameTranslated)));

        				//Exception: support new york URL
        				if (strcasecmp($city_select, "new york") == 0) {
            				$city_select = "new york city";
        				}

        				$hb_city = $this->Db_hb_country->get_city($country_select, $city_select, $this->site_lang);
						/*
						if(is_object($hb_city)) {
							
							$categories = array(
								"hostel" => $this->Db_links->get_property_type_link('hostel', $this->site_lang),
								"hotel" => $this->Db_links->get_property_type_link('hotel', $this->site_lang),
								"apartment" => $this->Db_links->get_property_type_link('apartment', $this->site_lang),
								"guesthouse" => $this->Db_links->get_property_type_link('guesthouse', $this->site_lang),
								"campsite" => $this->Db_links->get_property_type_link('campsite', $this->site_lang),
								"property" => $this->Db_links->get_property_type_link('property', $this->site_lang)
							);
							
							$districts = $this->Db_hb_hostel->get_districts_by_city_id($hb_city->hb_id);
             				$landmarks = $this->Db_hb_hostel->get_landmarks_by_city_id($hb_city->hb_id, 2);
													
							foreach ($districts as $district) {
								
								$segments = array(
									customurlencode($country->countryNameTranslated), 
									customurlencode($city->cityNameTranslated),
									'district',
									customurlencode($district->district_name)
								);
				
								$item = array(
									"loc" => site_url($segments),
									// ISO 8601 format - date("c") requires PHP5
									"lastmod" => date("c", time()),
									"changefreq" => "hourly",
									"priority" => "0.8"
								);
								
								//echo site_url($segments);
					
								//$this->sitemaps->add_item($item);
						
								foreach ($categories as $category) {
								
									$segments = array(
										customurlencode($country->countryNameTranslated), 
										customurlencode($city->cityNameTranslated),
										'district',
										customurlencode($district->district_name),
										'type',
										customurlencode($category)
									);
				
									$item = array(
										"loc" => site_url($segments),
										// ISO 8601 format - date("c") requires PHP5
										"lastmod" => date("c", time()),
										"changefreq" => "hourly",
										"priority" => "0.8"
									);
					
									//$this->sitemaps->add_item($item);
								
								}	
							}
							
							foreach ($landmarks as $landmark) {
								foreach ($categories as $category) {
									
									$segments = array(
										customurlencode($country->countryNameTranslated), 
										customurlencode($city->cityNameTranslated),
										'landmark',
										customurlencode($landmark->landmark_name),
										'type',
										customurlencode($category)
									);
				
									$item = array(
										"loc" => site_url($segments),
										// ISO 8601 format - date("c") requires PHP5
										"lastmod" => date("c", time()),
										"changefreq" => "hourly",
										"priority" => "0.8"
									);
					
									//$this->sitemaps->add_item($item);
									
								}
							}
						}
*/
					}
				}
			}
		}
		
		// file name may change due to compression
		$file_name = $this->sitemaps->build("sitemaps/".$this->input->server('HTTP_HOST').".xml");
		$reponses = $this->sitemaps->ping(site_url($file_name));
				
	}
}
