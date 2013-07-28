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
		redirect($this->input->server('HTTP_HOST')."_sitemap_continents.xml");
	}
	
	function create_sitemap()
	{
		$this->sitemap_continents();
		$this->sitemap_countries();
		//$this->update_robots();
					
		$sitemaps = array(
		array("loc" => site_url("sitemap_continents.xml.gz"), "lastmod" => date("c")),
		array("loc" => site_url("sitemap_pages.xml.gz"))
		);

		$index_file_name = $this->sitemaps->build_index($sitemaps, "sitemap_index.xml");
		$reponses = $this->sitemaps->ping(site_url($index_file_name));

		//redirect(site_url($index_file_name));
		
		// Debug by printing out the requests and status code responses
		// print_r($reponses);

		//redirect(site_url($file_name));
	}
	
	function update_robots()
	{
		
		$this->load->helper('file');
		$data = 'User-agent: *
Sitemap: '. site_url("sitemap_index.xml") .'
Disallow: /property_reviews/
Disallow: /ax/
Disallow: /info/wp-content/themes/Auberge/scripts/
Disallow: /reservation
Disallow: /location_avail/
Disallow: /cmain/ajax_recently_viewed_property
Disallow: /chostel/booking_avail/
Disallow: /css/
Disallow: /js/
Disallow: /images/
Disallow: /rooms_avail/
Disallow: /assets/';

		if ( ! write_file('../robots.txt', $data) )
		{
			
		}
		else
		{
			
		}
		
	}

	function sitemap_continents()
	{
		
		$continents = $this->db_hb_city->get_all_continents($this->site_lang);
				
		foreach($continents AS $continent)
		{
			$item = array(
				"loc" => site_url($continent->continent_name),
				// ISO 8601 format - date("c") requires PHP5
				"lastmod" => date("c", time()),
				"changefreq" => "hourly",
				"priority" => "0.8"
			);
			
			$this->sitemaps->add_item($item);
		}
		
		// file name may change due to compression
		$file_name = $this->sitemaps->build($this->input->server('HTTP_HOST')."_sitemap_continents.xml");
		$reponses = $this->sitemaps->ping(site_url($file_name));
		
	}
	
	function sitemap_countries()
	{
		
		$this->load->model('Db_hb_city'); 
		$this->load->model('Db_hb_country');
		$this->load->model('Db_hb_hostel');
		      
		$continents = $this->db_hb_city->get_all_continents($this->site_lang);
				
		foreach($continents AS $continent)
		{
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
				
				//echo count($cities->Country->Cities).' ';
				
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
						
						//echo customurldecode(customurlencode($country->countryNameTranslated));
						//echo customurlencode($city->cityNameTranslated);
						
						$country_select = urldecode(customurldecode(customurlencode($country->countryNameTranslated)));
        				$city_select = urldecode(customurldecode(customurlencode($city->cityNameTranslated)));

        				//Exception: support new york URL
        				if (strcasecmp($city_select, "new york") == 0) {
            				$city_select = "new york city";
        				}

        				$hb_city = $this->Db_hb_country->get_city($country_select, $city_select, $this->site_lang);
						
						//print_r($city);
						
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
					
								$this->sitemaps->add_item($item);
						
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
					
									$this->sitemaps->add_item($item);
								
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
					
									$this->sitemaps->add_item($item);
									
								}
							}
						}
					}
				}
				
				
				//echo site_url($segments);
				//print_r($country);
				//$item = array(
				//"loc" => site_url("blog/" . $post->slug),
				// ISO 8601 format - date("c") requires PHP5
				//"lastmod" => date("c", strtotime($post->last_modified)),
				//"changefreq" => "hourly",
				//"priority" => "0.8"
				//);
			
				//$this->sitemaps->add_item($item);
				//echo $country->{'continent_'.$this->site_lang};
				//print_r($countries);
				//print_r($country);
				//echo "<br /><br />";
				
			}		
			
		}
		
		// file name may change due to compression
		$file_name = $this->sitemaps->build("sitemap.xml");
		$reponses = $this->sitemaps->ping(site_url($file_name));
				
	}
}
