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
		
		$this->sitemap_continents();
		$this->update_robots();
					
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
		$file_name = $this->sitemaps->build("sitemap_continents.xml");
		$reponses = $this->sitemaps->ping(site_url($file_name));
		
	}
	
	function sitemap_countries()
	{
		
		$countries = $this->db_hb_city->get_all_countries();
			
		foreach($countries AS $country)
		{
			$item = array(
				"loc" => site_url("blog/" . $post->slug),
				// ISO 8601 format - date("c") requires PHP5
				"lastmod" => date("c", strtotime($post->last_modified)),
				"changefreq" => "hourly",
				"priority" => "0.8"
			);
			
			$this->sitemaps->add_item($item);
			
		}
		
		
	}
}