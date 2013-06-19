<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Hw_engine
 *
 * Hw_engine HW API library for code igniter
 *
 * @package   Hw_engine
 * @author    Louis-Michel Raynauld
 * @version   0.1
 */
class Hw_engine {

  var $CI;

  var $api_lang  = "en";
  var $api_functions_lang = "English";

  var $currency_from = "EUR";

  var $transaction_log_filename = "error_trans";

  function Hw_engine()
  {
    $this->CI =& get_instance();

    $this->initialize();

    log_message('debug', "Hw_engine Class Initialized");
  }

  function initialize()
  {
    $this->CI->load->model('Hostel_api_model');
    $this->CI->load->helper(array('text'));

    $this->_translation_init();
  }

  function _translation_init()
  {
    $this->CI->load->model('Hw_api_translate');

//     $this->api_functions_lang = $this->CI->Hostel_api_model->lang_code_convert($this->CI->site_lang);
    //Force english short description for better translation
    //Because we can not know the langage sent by HW if it is not English
    $this->api_functions_lang = "English";

    //If site is english, set from lang to english
    // else auto-detect because api langage return is not certain
    if(strcmp($this->api_functions_lang,"English")==0)
    {
      $this->CI->Hw_api_translate->setLanguage($this->CI->site_lang,"en");
    }
    else
    {
      $this->CI->Hw_api_translate->setLanguage($this->CI->site_lang);
    }
  }

  //TODO Get rid of call by reference &data
  // Not compatible with PHP 5.4
  function map_home_page(&$data)
  {
    $data['title'] = $this->CI->config->item('site_title');
    $data['load_jslib_jquery'] = true;

    $data['user_id'] = $this->CI->user_id;

    $data['google_map_enable']    = true;
    $data['google_map_country_list'] = true;

    $this->CI->load->model('Db_hw_city');

    $data['country_list'] = $this->CI->Db_hw_city->cityCountryList_DB(NULL,NULL,$this->CI->site_lang);

    $data['eu_country_list'] = $this->CI->Db_hw_city->cityCountryList_DB("europe",NULL,$this->CI->site_lang);

    $data['na_country_list'] = $this->CI->Db_hw_city->cityCountryList_DB("north america",NULL,$this->CI->site_lang);
    $data['sa_country_list'] = $this->CI->Db_hw_city->cityCountryList_DB("south america",NULL,$this->CI->site_lang);
    $data['as_country_list'] = $this->CI->Db_hw_city->cityCountryList_DB("asia",NULL,$this->CI->site_lang);
    $data['oc_country_list'] = $this->CI->Db_hw_city->cityCountryList_DB("oceania",NULL,$this->CI->site_lang);
    $data['af_country_list'] = $this->CI->Db_hw_city->cityCountryList_DB("africa",NULL,$this->CI->site_lang);

    return $data;

  }

  //TODO Get rid of call by reference &data
  // Not compatible with PHP 5.4
  function hw_site_search(&$data, $terms = NULL)
  {
    $data['results'] = array();
    $data['search_term'] = $terms;

    $this->CI->load->model('Db_hw_search');

    $data['results'] = $this->CI->Db_hw_search->search_hw_data($terms, $this->CI->site_lang, 150);
    if(!empty($data['results']))
    {
      $this->CI->Hw_api_translate->translate_search_results($data['results']);
    }

    return $data;
  }

  function hw_site_search_suggest($terms = NULL, $filter = 'all', $show_more_results_link = FALSE, $term_from_start = FALSE)
  {
    $data['results'] = array();
    $data['search_term'] = $terms;
    if(!empty($terms) && (strlen(trim($terms)) > 2 ))
    {
      $terms = utf8_strip_specials($terms);
      $data['search_term'] = $terms;
      $data['show_more_results_link'] = $show_more_results_link;
      $this->CI->load->model('Db_hw_search');
      $data['suggestions'] = $this->CI->Db_hw_search->suggest_hw_data($terms, $this->CI->site_lang, 10, $filter, $term_from_start);
    }

    return $data;
  }

  //TODO Get rid of call by reference &data
  // Not compatible with PHP 5.4
  function continent_data(&$data, $continent)
  {
    $this->CI->load->model('Db_hw_city');

    $continent = customurldecode($continent);

    $xmlcountries = $this->CI->Db_hw_city->cityCountryList_DB($continent,NULL,$this->CI->site_lang);

    $data['continent_selected_lang'] = $xmlcountries->Country[0]->countryContinentTranslated;

    $data['bc_continent']       = $data['continent_selected_lang'];

    $data['countries_of_continent'] = $xmlcountries;

    $data['title'] = sprintf(gettext("Hostels in %s - %s"),my_mb_ucfirst($continent),$this->CI->config->item('site_title'));

    $data['google_map_enable']    = true;
    $data['google_map_country_list'] = true;
    $data['country_list'] = $xmlcountries;

    return $data;
  }

  //TODO Get rid of call by reference &data
  // Not compatible with PHP 5.4
  function country_data(&$data, $continent, $country)
  {
    $this->CI->load->model('Db_hw_city');
    $continent = customurldecode($continent);
    $country   = customurldecode($country);

    $country   = my_mb_ucfirst(mb_strtolower($country,"UTF-8"));

    $xmlcities = $this->CI->Db_hw_city->cityCountryList_DB(NULL,$country,$this->CI->site_lang);

    $data['country_selected'] = $country;
    $data['bc_country']       = $country;
    $data['bc_continent']     = $xmlcities->Country->countryContinentTranslated;
    $data['continent_lang']   = $data['bc_continent'];

    $data['cities_of_country'] = $xmlcities;

    $data['title'] = sprintf(gettext("Hostels in %s - %s"),$country,$this->CI->config->item('site_title'));

    $data['google_map_enable']    = true;
    $data['google_map_city_list'] = true;

    return $data;
  }

  /*
   * location_search
   *
   */
  //TODO add facilities at appropriate place
  // $test = $this->CI->Db_hw_hostel->get_hostel_facilities($property_number);

  function location_search($country, $city, $dateStart = NULL, $numNights = NULL, $include_availdata = FALSE, $prop_reviews = FALSE, $filters = array())
  {
    $this->CI->load->model('Db_country');

    $country    = urldecode (customurldecode($country));
    $city       = urldecode (customurldecode($city));

    //TODO Eventually use hw_city table to check if city is available through HW API instead of translation table
    $this->CI->load->model('Db_hw_city');
    $this->CI->load->model('Db_hw_hostel');

    $hw_city = $this->CI->Db_hw_city->get_city($country,$city,$this->CI->site_lang);

    $data["city_info"] = $hw_city;
    if(empty($hw_city))
    {
      return FALSE;
    }

    $country_api = $hw_city->country_name;
    $city_api    = $hw_city->city_name;
    $country     = $hw_city->country_name_translated;
    $city        = $hw_city->city_name_translated;

    $data["most_popular_amenities"] = array();
    $data['city_amenities'] = array();
    $data['city_districts'] = array();
    $data['city_landmarks'] = array();

    if(($dateStart !== NULL)&&($numNights===NULL))
    {
      //echo warning date and $numNights is disregard wrong format missing parameter.
      $dateStart = NULL;
      $numNights = NULL;
      $data['warning']         = true;
      $data['warning_message'] = _('Date et nombre de nuits invalide.  Valeurs ignorées lors de la recherche.');
    }

    $data['currency'] = $this->CI->site_currency;

    $dateToCheck = $dateStart;
    $numNightsToCheck = $numNights;
    $results = Array();

    if(($dateStart === NULL)&&($numNights === NULL))
    {

      $cache_time = $this->CI->wordpress->get_option("aj_cache_time_city_landing_pages",0);
      if(!empty($cache_time))
      {
        $this->CI->output->cache($cache_time);
      }

      //Landmark data for landmark landing page
      $data['filters'] = array( "type"     => NULL,
      													"landmark" => NULL,
      													"district" => NULL);

      if(!empty($filters["type"]))
      {
        $data['filters']["type"] = $filters["type"];
        //send to view that we select a type filter but do not filter by type
        unset($filters["type"]);
      }

      if(!empty($filters["landmark"]))
      {
        $this->CI->load->model('Db_landmarks');

        $data['filters']["landmark"] = $this->CI->Db_landmarks->get_city_landmark_from_slug($data["city_info"]->city_id,$filters["landmark"]);

        if(!empty($data['filters']["landmark"]->landmark_name))
        {
          $this->CI->load->model('i18n/db_translation_cache');
          $translation_result = $this->CI->db_translation_cache->get_translation($data['filters']["landmark"]->landmark_name,$this->CI->site_lang);
          if(!empty($translation_result))
          {
            $data['filters']["landmark"]->landmark_name_ts = $translation_result->translation;
          }
        }
      }

      //District data for district landing page
      if(!empty($filters["district"]))
      {
        $this->CI->load->model('Db_districts');
        $data['filters']["district"] = $this->CI->Db_districts->get_city_district_from_slug($data["city_info"]->city_id,$filters["district"]);
        if(!empty($data['filters']["district"]->district_name))
        {
          $filters["district"] = $data['filters']["district"]->district_name;

          $this->CI->load->model('i18n/db_translation_cache');
          $data['filters']["district"]->district_name_ts = $this->CI->db_translation_cache->get_translation($data['filters']["district"]->district_name,$this->CI->site_lang);
          $data['filters']["district"]->district_name_ts = $data['filters']["district"]->district_name_ts->translation;
        }
      }

      $data['searchmode'] = 0;
      $dateToCheck = get_date_default(30);
      $numNightsToCheck = 4;

      $currencyConvNeeded = FALSE;

      $this->CI->load->config('hw_cache',TRUE);

      if((strcasecmp($data['currency'],"EUR")!=0)&&
         (strcasecmp($data['currency'],"USD")!=0)&&
         (strcasecmp($data['currency'],"GBP")!=0)&&
         ((strcasecmp($this->CI->config->item('hw_currency_country','hw_cache'),$country_api)!=0)||
         (strcasecmp($this->CI->config->item('hw_currency_city','hw_cache'),$city_api)!=0)))
      {
        $currencyConvNeeded = TRUE;
      }
      $results[0] = FALSE;
      $results[1] = array();

      //Force english short description for better translation
      //Because we can not know the langage sent by HW if it is not English
      $this->api_functions_lang = "English";
      $this->_translation_init();

      //TONOTICE The case where currency city is in DB but have not been cached by location search cron job has not been tested
      //         as it should not happen but should work fine anyway

      if($currencyConvNeeded)
      {
        $results[1] = $this->CI->Db_hw_hostel->propertyLocationSearch_DB($city_api,$country_api,$this->currency_from,$this->api_functions_lang,25,$filters);
      }
      else
      {
        $results[1] = $this->CI->Db_hw_hostel->propertyLocationSearch_DB($city_api,$country_api,$data['currency'],$this->api_functions_lang,25,$filters);
      }

      if(empty($results[1]))
      {
//         $results = $this->CI->Hostel_api_model->propertyLocationSearch($this->CI->config->item('hostelworld_userID'), $city_api, $country_api, $dateToCheck, $numNightsToCheck,$data['currency'],$this->api_functions_lang);
        //For now if DB return no data return false to cause 404 error
        return FALSE;
      }
      else
      {
        if($currencyConvNeeded)
        {
          foreach($results[1] as $hostel)
          {
            foreach($hostel->BedPrices as $hostelprice)
            {
              $hostelprice->BedPrice->priceFROM = $hostelprice->BedPrice->price;
              $hostelprice->BedPrice->currencyFROM = $hostelprice->BedPrice->currency;
              $hostelprice->BedPrice->price = $this->CI->Db_currency->convert_from_hw_rates($hostelprice->BedPrice->currency,$data['currency'],$hostelprice->BedPrice->price);
              $hostelprice->BedPrice->currency = $data['currency'];

            }
          }
        }
      }
    }
    else
    {
      $data['searchmode'] = 1;
      if($include_availdata == TRUE)
      {
        $results = $this->CI->Hostel_api_model->propertyLocationSearch($this->CI->config->item('hostelworld_userID'), $city_api, $country_api, $dateToCheck, $numNightsToCheck,$data['currency'],$this->api_functions_lang,null,1);
      }
      $cache_time = $this->CI->wordpress->get_option("aj_cache_time_city_avail_pages",0);
      if(!empty($cache_time))
      {
        $this->CI->output->cache($cache_time);
      }

      //Only if not a call with data
      if($include_availdata !== true)
      {
        //Add district landmark of city
        $amenityGroups = $this->CI->Db_hw_hostel->get_amenities_city_for_filter();
        $data["most_popular_amenities"] = $amenityGroups["mostPopularAmenities"];
        $data['city_amenities'] = $amenityGroups["amenities"];
        $data['city_districts'] = $this->CI->Db_hw_hostel->get_districts_by_city_id($hw_city->city_id);
        $data['city_landmarks'] = $this->CI->Db_hw_hostel->get_landmarks_by_city_id($hw_city->city_id,2);

        //translate city landmarks
        $this->CI->load->model('i18n/db_translation_cache');
        foreach(array_merge($data['city_amenities'], $data["most_popular_amenities"]) as $i => $amenity)
        {
          $translation = $this->CI->db_translation_cache->get_translation($amenity->facility_name,$this->CI->site_lang);
          $amenity->original_name = $amenity->facility_name;
          if(!empty($translation))
          {
            $amenity->facility_name = $translation->translation;
          }

        }
        foreach($data['city_landmarks'] as $i => $landmark)
        {
          $translation = $this->CI->db_translation_cache->get_translation($landmark->landmark_name,$this->CI->site_lang);
          $data['city_landmarks'][$i]->original_name = $data['city_landmarks'][$i]->landmark_name;
          if(!empty($translation))
          {
            $data['city_landmarks'][$i]->original_name = $data['city_landmarks'][$i]->landmark_name;
            $data['city_landmarks'][$i]->landmark_name = $translation->translation;
          }
        }
        //translate city districts
        foreach($data['city_districts'] as $i => $district)
        {
          $translation = $this->CI->db_translation_cache->get_translation($district->district_name,$this->CI->site_lang);
          if(!empty($translation))
          {
            $data['city_districts'][$i]->original_name = $data['city_districts'][$i]->district_name;
            $data['city_districts'][$i]->district_name = $translation->translation;
          }
        }
      }
    }

    $data['property_list'] = array( "property_count" => 0,
            												"hostel_list" => array(),
                                    "hostel_count" => 0,
                                    "guesthouse_list" => array(),
                                    "guesthouse_count" => 0,
                                    "hotel_list" => array(),
                                    "hotel_count" => 0,
                                    "apartment_list" => array(),
                                    "apartment_count" => 0,
                                    "campsite_list" => array(),
                                    "campsite_count" => 0,
                                    );

    $data['amenities']     = array();
    $data['districts']     = array();
    $data['landmarks']     = array();

    if(($data['searchmode'] == 0)||($include_availdata == TRUE))
    {
      if($results[0]==true)
      {
        $data['api_error_msg'] = $results[1];
      }
      else
      {
        $this->CI->load->model('Db_hw_rating');

		$data['user_reviews']  = array();
        $data['property_list'] = $this->CI->Hw_api_translate->translate_LocationSearch($results[1]);

        foreach($data['property_list'] as $hostel_id => $hostel)
        {
/*
                  $api = $this->CI->Hostel_api_model->PropertyInformation($this->CI->config->item('hostelworld_userID'), (int)$hostel->propertyNumber, $this->api_functions_lang);
                  $propInfoData = $api[0]==true ? array() : $this->CI->Hw_api_translate->translate_PropertyInformation($api[1][0]);

                  $PropertyImages = empty($propInfoData) ? array() : $propInfoData->PropertyImages;
                  if (!empty($PropertyImages)) {
                      $PropertyImages = xmlobj2arr($PropertyImages);

                      $PropertyImages = $PropertyImages["PropertyImage"];
                      foreach ($PropertyImages as $key => $image) {
                          $PropertyImages[$key] = (object) $image;
                          //PATCH to cover HW API wrong URLs
                          $PropertyImages[$key]->imageURL = str_replace("http://images.webresint.com", "", $PropertyImages[$key]->imageURL);
                      }
                  }

		  $data['propertyInfo'][(int)$hostel->propertyNumber] = empty($propInfoData)
                      ? array()
                      : array(
                          'PropertyImages'       => $PropertyImages,
                          'conditions'           => empty($propInfoData) ? '' : (string) domain_name_replace($propInfoData->conditions),
                          'conditionsTranslated' => empty($propInfoData) ? '' : (string) domain_name_replace($propInfoData->conditionsTranslated)
                      );
*/
		  $data['propertyType'][(int)$hostel->propertyNumber] = $hostel->propertyType;
		  $hostel->overallHWRating = $this->CI->Db_hw_rating->get_hw_rating((int)$hostel->propertyNumber);
          if($prop_reviews === TRUE)
          {
            $data['user_reviews'][(int)$hostel->propertyNumber] = $this->property_reviews((int)$hostel->propertyNumber, TRUE, 5, FALSE, FALSE);
          }

          $hostel->property_page_url = $this->CI->Db_links->build_property_page_link($hostel->propertyType,$hostel->propertyName,$hostel->propertyNumber,$this->CI->site_lang);

          if($include_availdata == TRUE)
          {
            // $data['property_list'] is an XMLSimpleObject, so it is not possible to insert an array as a property
            $data['amenities'][(int)$hostel->propertyNumber] = $this->CI->Db_hw_hostel->get_hostel_facilities($hostel->propertyNumber);
            $data['amenities_filter'][(int)$hostel->propertyNumber] = $this->CI->Db_hw_hostel->get_hostel_facilities_for_filter($hostel->propertyNumber);
            $data['districts'][(int)$hostel->propertyNumber] = $this->CI->Db_hw_hostel->get_property_districts_for_filter($hostel->propertyNumber);

             if (!empty($data['districts'][(int)$hostel->propertyNumber]))
              {

//               $this->load->model('i18n/db_translation_cache');

              foreach ( $data['districts'][(int)$hostel->propertyNumber] as $i => $district)
                  {
                  $translation = $this->CI->db_translation_cache->get_translation($district->district_name, $this->CI->site_lang);

                  if (!empty($translation))
                    {
                        $data['districts'][(int)$hostel->propertyNumber][$i]->district_name = $translation->translation;
                    }
                    else
                    {
                        $data['districts'][(int)$hostel->propertyNumber][$i]->district_name = $district->district_name;

                    }
                  $data['districts'][(int)$hostel->propertyNumber][$i]->original_name = $district->district_name;
                  }
              }

            // Second parameter is a range in KM
            $data['landmarks'][(int)$hostel->propertyNumber] = $this->CI->Db_hw_hostel->get_property_landmarks_for_filter($hostel->propertyNumber, 2);
          }
          else
          {
            $this->CI->load->model('Db_hw_hostel');
            foreach($data['property_list'] as $hostel_id => $hostel)
            {
              $data['main_services'][(int)$hostel->propertyNumber] = $this->CI->Db_hw_hostel->get_hostel_main_services((int)$hostel->propertyNumber);

              if(!empty($data['main_services'][(int)$hostel->propertyNumber]))
              {
                foreach($data['main_services'][(int)$hostel->propertyNumber] as $si => $service)
                {
                  $translation = $this->CI->db_translation_cache->get_translation($service->description,$this->CI->site_lang);
                  if(!empty($translation))
                  {
                    $data['main_services'][(int)$hostel->propertyNumber][$si]->description = $translation->translation;
                  }
                }
              }

            }
          }
        }

        if($prop_reviews === TRUE)
        {
          $this->CI->Hw_api_translate->translate_location_reviews($data["user_reviews"]);
        }

        //this is ugly PATCH please make it better soon!
        if($this->CI->user_agent_mobile && !$this->CI->user_agent_mobile_bypass)
        {
          $data['property_list'] = $this->properties_sort_by_price($data['property_list']);
        }
        //If search with date split in property types else keep one big list
        if($data['searchmode'] != 0)
        {
          //this is ugly PATCH please make it better soon!
          if($this->CI->user_agent_mobile && !$this->CI->user_agent_mobile_bypass)
          {
            $data['property_list'] = $this->properties_filter_by_prop_type($data['property_list']);
          }
        }
        else
        {
          $data['property_list'] = array("property_count" => count($data['property_list']), "hostel_list" => $data['property_list']);
        }

      }
    }
    //Sets cookies so we could access this from wordpress environment
    $this->CI->load->helper('cookie');
    $cookie = array('name'   => 'country_selected',
                    'value'  => $country,
                    'expire' => $this->CI->config->item('sess_expiration'));
    set_cookie($cookie);
    $cookie = array('name'   => 'city_selected',
                    'value'  => $city,
                    'expire' => $this->CI->config->item('sess_expiration'));
    set_cookie($cookie);
    $cookie = array('name'   => 'date_selected',
                    'value'  => $dateStart,
                    'expire' => $this->CI->config->item('sess_expiration'));
    set_cookie($cookie);
    $cookie = array('name'   => 'numnights_selected',
                    'value'  => $numNights,
                    'expire' => $this->CI->config->item('sess_expiration'));
    set_cookie($cookie);

    $data['country_selected'] = $country;
    $data['city_selected'] = $city;
    $data['date_selected'] = $dateStart;
    $data['numnights_selected'] = $numNights;

    if(is_null($dateStart))
    {
      $data['date_default'] = get_date_default();
    }

    $data['bc_continent']  = $this->CI->Db_country->get_continent_of_country($country,$this->CI->site_lang);

    if(is_null($data['bc_continent']))
    {
      log_message('error', 'This country is not in DB: '.$country);
    }
    $data['bc_country']    = $country;
    $data['bc_city']       = $city;

    $data['title'] = sprintf(gettext("Hostels in %s - %s"),$city,$this->CI->config->item('site_title'));

    if($data['searchmode'] == 0)
    {
      $data['google_map_enable'] = true;
      $data['google_map_hostel_list'] = true;
    }
    $data['user_id'] = $this->CI->user_id;

    return $data;
  }

  /**
   * location_json_format: Generate JSON data
   */
  //TODO Get rid of call by reference &data
  // Not compatible with PHP 5.4
  function location_json_format(&$data)
  {
    $wp_date_format = $this->CI->wordpress->get_option('aj_date_format_city_search');

    //Ensure that Numeric values are cast to a numeric type
    //because JSON encoding turn non numeric value into JS string
    //Also if not in an array everything is turn into strings, yeah really! array needed! for json_encode
    $json_data["property_list"] = xmlobj2arr($data["property_list"]);

    $json_data["request"] = array(
                                  'date_selected'      => $data["date_selected"],
                                  'numnights_selected' => $data["numnights_selected"],
                                  'display_currency' => $this->CI->site_currency
                                 );

    $json_data["city_info"] = $data["city_info"];
    $json_data['api_error_msg'] = false;
    if(!empty($data['api_error_msg']))
    {
//       $json_data['api_error_msg'] = $data['api_error_msg'];
      $json_data['api_error_msg'] = true;
      $json_data["property_list"] = array();
    }
//     debug_dump($data);exit;
//     debug_dump($json_data);

    //TODO manage API error!
    $deal_property = array(0 => null,
                           1 => null);

    foreach($json_data["property_list"] as $i => $prop)
    {
      //TODO insert this into HW and HB lib

      $json_data["property_list"][$i]['savedToFavorites']   = empty($prop["savedToFavorites"]) ? false : true;
      $json_data["property_list"][$i]['saveToFavorites']    = empty($prop["savedToFavorites"]) ? true : false;
      $json_data["property_list"][$i]['amenities'] = $data['amenities'][$prop["propertyNumber"]];
      $json_data["property_list"][$i]['amenities_filter'] = $data['amenities_filter'][$prop["propertyNumber"]];
      if (!empty($json_data["property_list"][$i]['PropertyImages']) && !empty($json_data["property_list"][$i]['PropertyImages']['PropertyImage']['imageURL']))
      {
        $original_image_url = $json_data["property_list"][$i]['PropertyImages']['PropertyImage']['imageURL'];
        $image_thumbnail_url = $original_image_url;
        $image_url = str_replace("mini_",'',$original_image_url);
    	if (strpos($image_url,'http://images.webresint.com') !== false)
	    {
	      $image_list_url = base_url().'assets/hw/100/100'.str_replace("http://images.webresint.com", "", $image_url);
	    }
	    else
	    {
          $image_list_url = base_url().'info/wp-content/themes/Auberge/scripts/t.php?zc=1&amp;w=100&h=100&src='.$image_url;
        }


		$json_data["property_list"][$i]['PropertyImages']['PropertyImage']['imageThumbnailURL'] = $image_thumbnail_url;
		$json_data["property_list"][$i]['PropertyImages']['PropertyImage']['imageURL'] = $image_url;
		$json_data["property_list"][$i]['PropertyImages']['PropertyImage']['imageListURL'] = $image_list_url;
	  }

	   // -------Translate the propertyType----------------------------------//
	    $this->CI->load->model('Db_term_translate');
	  $json_data["property_list"][$i]['propertyTypeTranslate'] = (string)$this->CI->Db_term_translate->get_term_translation($data['propertyType'][$prop["propertyNumber"]],$this->CI->site_lang);
	  // $json_data["property_list"][$i]['propertyTypeTranslate'] = $propertyType;
       $json_data["property_list"][$i]["city_name"]   = $data["city_info"]->city_name; // set the city name
	  foreach($json_data["property_list"][$i]['amenities'] as $a => $amenity)
      {

        if(($amenity->description == 'Breakfast Included')||
           ($amenity->description == 'Breakfast'))
        {
          $json_data["property_list"][$i]['amenities'][$a]->slug = "free-breakfast";
        }
        else
        {
          $json_data["property_list"][$i]['amenities'][$a]->slug = "";
        }

        $translation = $this->CI->db_translation_cache->get_translation($amenity->description,$this->CI->site_lang);
        if(!empty($translation))
        {
          $json_data["property_list"][$i]['amenities'][$a]->description = $translation->translation;
        }
      }

      $json_data["property_list"][$i]["has_amenities"] = 0;
      if(!empty($json_data["property_list"][$i]['amenities']))
      {
        $json_data["property_list"][$i]["has_amenities"] = 1;
      }

      $json_data["property_list"][$i]['districts'] = $data['districts'][$prop["propertyNumber"]];
      $json_data["property_list"][$i]["landmarks"] = $data['landmarks'][$prop["propertyNumber"]];

    foreach($json_data["property_list"][$i]["landmarks"] as $pl => $prop_landmark)
      {
        $json_data["property_list"][$i]["landmarks"][$pl]->to_display = 0;


          $json_data["property_list"][$i]["landmarks"][$pl]->original_name = $json_data["property_list"][$i]["landmarks"][$pl]->landmark_name;
          $json_data["property_list"][$i]["landmarks"][$pl]->translation_name = $json_data["property_list"][$i]["landmarks"][$pl]->landmark_name;

          $translation = $this->CI->db_translation_cache->get_translation($prop_landmark->landmark_name,$this->CI->site_lang);
          if(!empty($translation))
          {
           $json_data["property_list"][$i]["landmarks"][$pl]->landmark_name = $translation->translation;
           $json_data["property_list"][$i]["landmarks"][$pl]->translation_name = $translation->translation;
          }

          if($prop_landmark->slug === 'City-Center')
        {
              $json_data["property_list"][$i]["landmarks"][$pl]->to_display = 1;
        }
        else
        {
          //delete useless data to save on bandwith
          $json_data["property_list"][$i]["landmarks"][$pl]->slug = "";
          $json_data["property_list"][$i]["landmarks"][$pl]->landmark_name = "";
        }
      }

      if(!empty($json_data["property_list"][$i]["shortDescriptionTranslated"]))
      {
        $json_data["property_list"][$i]["shortDescription"] = strip_tags(word_limiter($json_data["property_list"][$i]["shortDescriptionTranslated"], 30,"..."));
      }
      else
      {
        $json_data["property_list"][$i]["shortDescription"] = strip_tags(word_limiter($json_data["property_list"][$i]["shortDescription"], 30,"..."));
      }

      $json_data["property_list"][$i]["overall_rating"] = $json_data["property_list"][$i]["overallHWRating"];
      settype($json_data["property_list"][$i]["overall_rating"],"integer");
      $json_data["property_list"][$i]["overall_rating"] = sprintf($json_data["property_list"][$i]["overallHWRating"]);
        $json_data["property_list"][$i]["rating"]='';

        if(($json_data["property_list"][$i]["overall_rating"]>59) &&
            ($json_data["property_list"][$i]["overall_rating"]<70) ) {

            $json_data["property_list"][$i]["rating"] = _("Good");
        }
        else if(($json_data["property_list"][$i]["overall_rating"]>69) &&
                ($json_data["property_list"][$i]["overall_rating"]<80) ) {
            $json_data["property_list"][$i]["rating"] = _("Very good");
        }
        else if(($json_data["property_list"][$i]["overall_rating"]>79) &&
                ($json_data["property_list"][$i]["overall_rating"]<90) ) {
            $json_data["property_list"][$i]["rating"] = _("Great");
        }
        else if($json_data["property_list"][$i]["overall_rating"]>89) {
            $json_data["property_list"][$i]["rating"] = _("Fantastic");
        }
        else if ($json_data["property_list"][$i]["overall_rating"] == 0) {
            $json_data["property_list"][$i]["overall_rating"] = '';
        }

      $json_data["property_list"][$i]["isMinNightNeeded"] = false;
      if(isset($prop["minNights"]))
      {
        settype($json_data["property_list"][$i]["minNights"],"integer");
        if($prop["minNights"]>0)
        {
          $json_data["property_list"][$i]["isMinNightNeeded"] = true;
          $json_data["property_list"][$i]["minNightsMessage"] = sprintf(ngettext("This property requires a minimum stay of %d night", "This property requires a minimum stay of %d nights", $prop["minNights"]), $prop["minNights"]);

        }
      }
      else
      {
        $json_data["property_list"][$i]["minNights"] = 0;
        settype($json_data["property_list"][$i]["minNights"],"integer");
      }
      settype($json_data["property_list"][$i]["maxNights"],"integer");

      settype($json_data["property_list"][$i]["maxPax"],"integer");
      $prices = $this->property_cheapest_prices($json_data["property_list"][$i]);
	  // validate the price is set otherwise remove the record from list as it's was discuss to skip the propery
	  if(empty($prices['min_price']))
	  {
		  unset($json_data["property_list"][$i]); // just remove the record from the list
		  continue; //
	  }
      $json_data["property_list"][$i]["dual_price"]            = 1;
      $json_data["property_list"][$i]["display_price"]         = floatval($prices['min_price']);
      $json_data["property_list"][$i]["display_shared_price"]  = floatval($prices['min_dorm_price']);
      $json_data["property_list"][$i]["display_private_price"] = floatval($prices['min_room_per_person_price']);
      $json_data["property_list"][$i]["display_private_people"] = intval($prices['min_room_people']);

      $json_data["property_list"][$i]["currency_code"] = $json_data["property_list"][$i]["BedPrices"]["BedPrice"]["currency"];
      $json_data["property_list"][$i]["display_currency"] = currency_symbol($json_data["property_list"][$i]["BedPrices"]["BedPrice"]["currency"]);
      $json_data["property_list"][$i]["original_price"] = null;
      settype($json_data["property_list"][$i]["display_price"],"float");
      $json_data["property_list"][$i]["display_price_formatted"]        = number_format($json_data["property_list"][$i]["display_price"], 2, '.', '');
      $json_data["property_list"][$i]["display_shared_price_formatted"] = number_format($json_data["property_list"][$i]["display_shared_price"], 2, '.', '');
      $json_data["property_list"][$i]["display_private_formatted"]      = number_format($json_data["property_list"][$i]["display_private_price"], 2, '.', '');

      //Compute deal properties
      if(empty($deal_property[0]))
      {
      	$deal_property[0] = new stdClass();
        $deal_property[0]->display_price = $json_data["property_list"][$i]["display_price"];
        $deal_property[0]->index = $i;
      }
      elseif(empty($deal_property[1]))
      {
      	$deal_property[1] = new stdClass();
        $deal_property[1]->display_price = $json_data["property_list"][$i]["display_price"];
        $deal_property[1]->index = $i;
      }
      elseif($json_data["property_list"][$i]["display_price"] < $deal_property[0]->display_price)
      {
      	$deal_property[1] = new stdClass();
        $deal_property[1]->display_price = $deal_property[0]->display_price;
        $deal_property[1]->index = $deal_property[0]->index;

	$deal_property[0] = new stdClass();
        $deal_property[0]->display_price = $json_data["property_list"][$i]["display_price"];
        $deal_property[0]->index = $i;
      }
      elseif($json_data["property_list"][$i]["display_price"] < $deal_property[1]->display_price)
      {
      	$deal_property[1] = new stdClass();
        $deal_property[1]->display_price = $json_data["property_list"][$i]["display_price"];
        $deal_property[1]->index = $i;
      }

      $json_data["property_list"][$i]["isGeoValid"] = false;
      if(($prop["Geo"]["Latitude"] != 0)&&($prop["Geo"]["Longitude"] != 0))
      {
        $json_data["property_list"][$i]["isGeoValid"] = true;
      }
      if(!is_array($prop["AvailableDates"]["availableDate"]))
      {
        $json_data["property_list"][$i]["AvailableDates"]["availableDate"] = array($prop["AvailableDates"]["availableDate"]);
      }

      //date format
      foreach($json_data["property_list"][$i]["AvailableDates"]["availableDate"] as $d => $avail_date)
      {
        if(!empty($wp_date_format))
        {
          $json_data["property_list"][$i]["AvailableDates"]["availableDate"][$d] = date_conv($avail_date, $wp_date_format);
        }
      }

      //Unset useless data to keep JSON object as small as possible
      unset($json_data["property_list"][$i]["BedPrices"]);
      unset($json_data["property_list"][$i]["@attributes"]);
    }

    //set deals prices
    if(!empty($deal_property[0]))
    {
      $json_data["property_list"][$deal_property[0]->index]["original_price"] = number_format($json_data["property_list"][$deal_property[0]->index]["display_price"]*1.25, 2, '.', '');;
    }

    if(!empty($deal_property[1]))
    {
      $json_data["property_list"][$deal_property[1]->index]["original_price"] = number_format($json_data["property_list"][$deal_property[1]->index]["display_price"]*1.25, 2, '.', '');;
    }

//     debug_dump($json_data,"67.68.71.139");
    $data["json_data"] = json_encode($json_data);

    return $data;
  }

  function property_images($property_number)
  {
    $api = $this->CI->Hostel_api_model->PropertyInformation($this->CI->config->item('hostelworld_userID'), $property_number, $this->api_functions_lang);

    $data['thumbnails'] = array();
    $data['main_pics'] = array();

    if($api[0]!=true)
    {
      if (!empty($api[1][0]->PropertyImages))
      {
        foreach($api[1][0]->PropertyImages->PropertyImage as $image)
        {
          if ($image->imageType == 'Main'){
            $data['main_pics'][] = (string)$image->imageURL;
          }
          elseif ($image->imageType == 'Thumbnail'){
            $data['thumbnails'][] = (string)$image->imageURL;
          }
        }
      }
    }
    return $data;
  }

  //TODO Get rid of call by reference &data
  // Not compatible with PHP 5.4
  function property_info(&$data, $property_number)
  {
    log_message('debug', 'Entering HW Engine property_info method');

    $api = $this->CI->Hostel_api_model->PropertyInformation($this->CI->config->item('hostelworld_userID'), $property_number, $this->api_functions_lang);

    $data['hostel_data'] = array();
    $data['hostel_comments'] = NULL;

    $data['api_error'] = $api[0];
    $data['api_error_msg'] = false;

    if($api[0]==true)
    {
      //API return error
      log_message('error', 'HW API returned an error ('.$api[0].') for property '.$property_number);
      throw new Exception("api returned error");
    }
    else
    {
      $cache_time = $this->CI->wordpress->get_option("aj_cache_time_property_pages",0);
      if(!empty($cache_time))
      {
        $this->CI->output->cache($cache_time);
      }

      $data['hostel_data'] = $this->CI->Hw_api_translate->translate_PropertyInformation($api[1][0]);
      $data['property_number'] = $data['hostel_data']->propertyNumber;

      $this->CI->load->model('Db_country');
      $this->CI->load->model('Db_reviews');

      $property_name = $data['hostel_data']->propertyName;
      $hostel_city = $this->CI->Db_country->get_city($data['hostel_data']->country,$data['hostel_data']->city,$this->CI->site_lang);

      $data['availability_check'] = true;
      $data['google_map_enable']  = true;
      $data['google_map_address']  = $data['hostel_data']->address1.", ".$data['hostel_data']->city.", ".$data['hostel_data']->country.", ".$data['hostel_data']->postCode;

      $data['bc_continent']  = $this->CI->Db_country->get_continent_of_country($data['hostel_data']->country,$this->CI->site_lang);
      if(is_null($data['bc_continent']))
      {
        log_message('error', 'This country is not in DB: '.$data['hostel_data']->country);
      }
      $data['bc_country']    = $this->CI->Db_country->get_country($data['hostel_data']->country,$this->CI->site_lang);
      $data['bc_city']       = $this->CI->Db_country->get_city($data['hostel_data']->country,$data['hostel_data']->city,$this->CI->site_lang);

      //Map all hostel fata
      $this->CI->load->model('Db_hw_rating');
      // Load a helper
	  $this->CI->load->helper('domain_replace');

      $data['hostel']=new stdClass();
      $data['hostel']->property_number        = (int) $data['hostel_data']->propertyNumber;
      $data['hostel']->property_name          = (string) $property_name;
      $data['hostel']->property_type          = (string)$data['hostel_data']->propertyType;
      $data['hostel']->rating                 = $this->CI->Db_hw_rating->get_hw_rating($data['hostel_data']->propertyNumber);
      $data['hostel']->PropertyImages         = $data['hostel_data']->PropertyImages;
      $data['hostel']->geolatitude            = 0;
      $data['hostel']->geolongitude           = 0;

      if(( floatval($data['hostel_data']->Geo->Latitude) != 0) && ( floatval($data['hostel_data']->Geo->Longitude) != 0))
      {
        $data['google_map_geo_latlng'] = $data['hostel_data']->Geo->Latitude .", " . $data['hostel_data']->Geo->Longitude ;
        $data['hostel']->geolatitude             = (string) $data['hostel_data']->Geo->Latitude;
        $data['hostel']->geolongitude            = (string) $data['hostel_data']->Geo->Longitude;
      }
        else{
                // load hw_hostel mode
                $this->CI->load->model('Db_hw_hostel');
                $property_geos = $this->CI->Db_hw_hostel->get_hostel_geos($data['property_number']);

                if ($property_geos != false) {
                    if (( floatval($property_geos->geo_latitude) != 0) && ( floatval($property_geos->geo_longitude) != 0)) {

                        $data['google_map_geo_latlng'] = $property_geos->geo_latitude . ", " . $property_geos->geo_longitude;
                        $data['hostel']->geolatitude            = (string) $property_geos->geo_latitude;
                        $data['hostel']->geolongitude           = (string) $property_geos->geo_longitude;
                    }
                }
            }

      if(!empty($data['hostel']->PropertyImages))
      {
        $data['hostel']->PropertyImages = xmlobj2arr($data['hostel_data']->PropertyImages);
        $data['hostel']->PropertyImages = $data['hostel']->PropertyImages["PropertyImage"];
        foreach($data['hostel']->PropertyImages as $key => $image)
        {
          $data['hostel']->PropertyImages[$key] = (object) $image;
          //PATCH to cover HW API wrong URLs
          $data['hostel']->PropertyImages[$key]->imageURL = str_replace("http://images.webresint.com","",$data['hostel']->PropertyImages[$key]->imageURL);
        }
      }
      $data['hostel']->description            = (string) domain_name_replace($data['hostel_data']->description);
      $data['hostel']->descriptionTranslated  = (string) domain_name_replace($data['hostel_data']->descriptionTranslated);
      $data['hostel']->facilities             = xmlobj2arr($data['hostel_data']->Facilities);

      if(!empty($data['hostel']->facilities["facilityTranslated"]))
      {
        $data['hostel']->facilitiesTranslated   = $data['hostel']->facilities["facilityTranslated"];
      }
      $data['hostel']->facilities             = $data['hostel']->facilities["facility"];

      $data['hostel']->conditions             = (string) domain_name_replace($data['hostel_data']->conditions);
      $data['hostel']->conditionsTranslated   = (string) domain_name_replace($data['hostel_data']->conditionsTranslated);
      $data['hostel']->directions             = (string) $data['hostel_data']->directions;
      $data['hostel']->directionsTranslated   = (string) $data['hostel_data']->directionsTranslated;
      $data['hostel']->address1               = (string) $data['hostel_data']->address1;
      $data['hostel']->address2               = (string) $data['hostel_data']->address2 ;
      $data['hostel']->city                   = (string) $data['hostel_data']->city;
      $data['hostel']->country                = (string) $data['hostel_data']->country;

      //TEMP variable to support backward compatibility
//      $data['propertyNumber'] = $data['hostel_data']->propertyNumber;

      //Name for meta description
      $data['property_name'] = $property_name;
      unset($data['hostel_data']);
//      $data['hostel'] = $data['hostel_data'];
//      debug_dump($data['hostel']);

    }

    if(empty($data['city_selected']))
    {
      if(!empty($data['bc_country']))
      {
        $data['country_selected'] = $data['bc_country'];
      }

      if(!empty($data['bc_city']))
      {
        $data['city_selected'] = $data['bc_city'];
      }
    }

    $this->CI->load->model('Db_currency');
    $data['currency'] = $this->CI->config->item('site_currency_selected');
//    $cur_selected = $this->CI->input->cookie('currency_selected',TRUE);
    if(!empty($cur_selected))
    {
      $data['currency'] = $cur_selected;
    }
    else
    {
      $this->CI->load->library('tank_auth');
      $user_id = $this->CI->tank_auth->get_user_id();

      if($user_id!= false)
      {
        $this->CI->load->model('Db_model');
        $user_info = array();
        $user_info = $this->CI->Db_model->get_user_profile($user_id);
        $data['currency'] = $this->CI->Db_currency->get_currency_code($user_info['favorite_currency']);
      }
    }
    $this->CI->load->model('Db_hw_hostel');

    //Updating facility in DB to make sure it is up to date on cached page
    $this->CI->Db_hw_hostel->update_hw_hostel_facilities($property_number, $data["hostel"]->facilities);

    $hwhostel_data = $this->CI->Db_hw_hostel->get_hostel_data_from_number($data['property_number'],$data['currency']);

    $data['hostel_min_price'] = NULL;
    if(!is_null($hwhostel_data))
    {
      if(strcasecmp($data['currency'],$hwhostel_data->currency_price)!=0)
      {

        $data['hostel_min_price'] = currency_symbol($data['currency']) ." ". floor($this->CI->Db_currency->convert_from_hw_rates($hwhostel_data->currency_price, $data['currency'], $hwhostel_data->bed_price));
      }
      else
      {
        $data['hostel_min_price'] = currency_symbol($hwhostel_data->currency_price). " ".floor($hwhostel_data->bed_price);
      }
    }

    //our local reviews
    $data["user_reviews"] = array();
    $data["reviews_translation_available"] = false;

    $data["user_reviews"] = $this->CI->Db_reviews->get_property_reviews($data['property_number']);
    $data['hostel_rating']   = $this->CI->Db_reviews->get_property_avg_rating($data['property_number']);

    if(!empty($data["user_reviews"]))
    {
      $data["reviews_translation_available"] = $this->CI->Hw_api_translate->translate_mixed_reviews($data["user_reviews"]);
    }

    //Main services
    $data['main_services'] = $this->CI->Db_hw_hostel->get_hostel_main_services($data['property_number']);

    if(!empty($data['main_services']))
    {
      foreach($data['main_services'] as $si => $service)
      {
        $translation = $this->CI->db_translation_cache->get_translation($service->description,$this->CI->site_lang);
        if(!empty($translation))
        {
          $data['main_services'][$si]->description = $translation->translation;
        }
      }
    }

    $data['country_selected'] = $data['bc_country'];
    $data['city_selected'] = $data['bc_city'];

    $data['title'] = sprintf(gettext("%s - %s"),$property_name,$this->CI->config->item('site_title'));

    $data['load_js_livevalidation'] = true;

    return $data;
  }

  function property_reviews($property_id, $include_our_reviews = false, $max_review_count = 0, $include_hw_reviews = TRUE, $include_translations = TRUE)
  {
    $data["user_reviews"] = array();
    $index = 0;

    //Our reviews
    $this->CI->load->model('Db_reviews');
    $our_reviews = $this->CI->Db_reviews->get_property_reviews($property_id);
    $our_reviews_count = 0;

    if(!empty($our_reviews))
    {

      $our_reviews_count = count($our_reviews);
      if($include_our_reviews == true)
      {
        foreach($our_reviews as $our_review)
        {
          $data["user_reviews"][$index] = $our_review;
          $index++;

          if(($max_review_count > 0)&&(($index+1) >= $max_review_count))
          {
            $our_reviews_count = $max_review_count;
            break;
          }
        }
      }
    }
    //If our local reviews DB have less than 10 reviews complete with HW reviews
    // However, HW reviews can not be use to have more than 10 reviews
    // And if max review count is not already met
    $hw_review_count = 0;
    $maxhwreviews = 10 - $our_reviews_count;

    if($include_hw_reviews == TRUE)
    {
      if(($maxhwreviews > 0) && (($max_review_count == 0)
                                  || (($max_review_count > 0) && ($index < $max_review_count))))
      {
        //HW reviews
        $hwreviews = $this->CI->Hostel_api_model->PropertyReviews($this->CI->config->item('hostelworld_userID'),$property_id);

        if($hwreviews[0]==true)
        {
          //No API response or API returned error
          $hwreviews = array();
        }
        else
        {
          $hwreviews = $hwreviews[1];
        }

        if(isset($hwreviews[0]->review) && !empty($hwreviews[0]->review))
        {
          foreach($hwreviews[0]->review as $review)
          {
            $data["user_reviews"][$index]["author_name"]     = (string)$review->Reviewer;
            $data["user_reviews"][$index]["review_date"]     = (string)$review->Stayed;
            $data["user_reviews"][$index]["author_country"]  = "";
            $data["user_reviews"][$index]["review"]          = (string)utf8_decode($review->Notes);
            $data["user_reviews"][$index]["review_translated"] = "";

            $data["user_reviews"][$index]["review_rating"]   = ((int)$review->Charac + (int)$review->Security + (int)$review->Location + (int)$review->Staff + (int)$review->Fun + (int)$review->Clean)/30*100;
            $data["user_reviews"][$index]["review_rating"] = number_format($data["user_reviews"][$index]["review_rating"],0,'.','');
            $data["user_reviews"][$index]["review_source"]   = "HW";

            $index++;
            $hw_review_count++;
            if(($index+1) > $maxhwreviews) break;

            if(($max_review_count > 0)&&($index >= $max_review_count)) break;
          }

        }
      }
    }

    $data["review_count"] = $our_reviews_count + $hw_review_count;
    $data["our_review_count"]   = $our_reviews_count;
    $data["reviews_translation_available"] = FALSE;

    if(($index > 0) && ($include_translations === TRUE) )
    {
      $data["reviews_translation_available"] = $this->CI->Hw_api_translate->translate_mixed_reviews($data["user_reviews"]);
    }

    return $data;
  }

  function properties_sort_by_price($property_array)
  {
    function cmpprice($a, $b)
    {
      $priceA = str_replace(",",".",$a->BedPrices->BedPrice->price);
      $priceB = str_replace(",",".",$b->BedPrices->BedPrice->price);
      $priceA = floatval($priceA);
      $priceB = floatval($priceB);

      if($priceA < $priceB)
      {
        return -1;
      }
      elseif($priceA > $priceB)
      {
        return 1;
      }

      return 0;
    }

    usort($property_array, "cmpprice");
    return $property_array;
  }

  function properties_filter_by_prop_type($property_array)
  {
    $hostels    = array();
    $guesthouse = array();
    $hotel      = array();
    $apartment  = array();
    $campsite   = array();

    foreach($property_array as $property)
    {
      if(strcasecmp($property->propertyType, "Hostel")==0)
      {
        array_push($hostels,$property);
      }
      elseif(strcasecmp($property->propertyType, "Guesthouse")==0)
      {
        array_push($guesthouse,$property);
      }
      elseif(strcasecmp($property->propertyType, "Hotel")==0)
      {
        array_push($hotel,$property);
      }
      elseif(strcasecmp($property->propertyType, "Apartment")==0)
      {
        array_push($apartment,$property);
      }
      elseif(strcasecmp($property->propertyType, "Campsite")==0)
      {
        array_push($campsite,$property);
      }
    }
    $property_array = array("property_count" => count($property_array),
    												"hostel_list" => $hostels,
                            "hostel_count" => count($hostels),
                            "guesthouse_list" => $guesthouse,
                            "guesthouse_count" => count($guesthouse),
                            "hotel_list" => $hotel,
                            "hotel_count" => count($hotel),
                            "apartment_list" => $apartment,
                            "apartment_count" => count($apartment),
                            "campsite_list" => $campsite,
                            "campsite_count" => count($campsite),
                            );
    return $property_array;
  }

  function property_avail_check($propertyName, $propertyNumber, $dateStart, $numNights, $currency)
  {
    $dateStart = new DateTime($dateStart);

    //Force english short description for better translation
    //Because we can not know the langage sent by HW if it is not English
    $this->api_functions_lang = "English";
    $this->_translation_init();

    $api = $this->CI->Hostel_api_model->propertyBookingInformation($this->CI->config->item('hostelworld_userID'),
                                                                       $propertyNumber,
                                                                       $dateStart->format('Y-m-d'),
                                                                       $numNights,
                                                                       $currency,
                                                                       $this->api_functions_lang);

    $data['propertyName']   = $propertyName;
    $data['propertyNumber'] = $propertyNumber;
    $data['dateStart']      = $dateStart;
    $data['numNights']      = $numNights;
    $data['currency']       = $currency;

    $this->CI->load->model('Db_hw_hostel');

    //Get main services and breakfast included
    $this->CI->load->model('i18n/db_translation_cache');
    $data['main_services'] = $this->CI->Db_hw_hostel->get_hostel_main_services($propertyNumber);
    $data['breakfast_included'] = 0;
    if(!empty($data['main_services']))
    {
      foreach($data['main_services'] as $si => $service)
      {
        if($service->service_id == 26)
        {
          $data['breakfast_included'] = 1;
        }
        $translation = $this->CI->db_translation_cache->get_translation($service->description,$this->CI->site_lang);
        if(!empty($translation))
        {
          $data['main_services'][$si]->description = $translation->translation;
        }
      }
    }
    $data['booking_info'] = array();
    $data['api_error'] = $api[0];
    $data['api_error_msg'] = FALSE;

    if($api[0]===TRUE)
    {
      $data['api_error'] = TRUE;
      //API return error
      $data['api_error_msg'] = $this->CI->Hw_api_translate->translate_APIError($api[1][0]);
      if(isset($data['api_error_msg']->Error))
      {
         $data['api_error_msg'] = $data['api_error_msg']->Error;
      }
      elseif(isset($data['api_error_msg']->UserMessage))
      {
        $data['api_error_msg'] = $data['api_error_msg']->UserMessage;
      }
    }
    elseif($api[1][0]->minNights > $data['numNights'])
    {
      $data['api_error'] = TRUE;
      $data['api_error_msg']->messageTranslated = _("Property availability error");
      $data['api_error_msg']->detailTranslated = sprintf(ngettext("This property requires a minimum stay of %d night", "This property requires a minimum stay of %d nights", (int)$api[1][0]->minNights), (int)$api[1][0]->minNights);
      //For view compatibility
      $data['api_error_msg']->message = $data['api_error_msg']->messageTranslated;
      $data['api_error_msg']->detail = $data['api_error_msg']->detailTranslated;
    }
    else
    {
       $data['api_error'] = FALSE;
      //API return OK
      $data['booking_info'] = $this->CI->Hw_api_translate->translate_BookingInformation($api[1][0]);
      $data['distinctRoomTypes'] = $this->distinct_hw_rooms_data($data['booking_info']);
    }
    return $data;
  }

  function property_cheapest_prices($property)
  {
    $cheapest_prices = array();
    $cheapest_prices['min_price']      = "";
    $cheapest_prices['min_dorm_price'] = "";
    $cheapest_prices['min_room_price'] = "";
    $cheapest_prices['min_room_per_person_price'] = "";
    $cheapest_prices['min_room_people'] = "";

    $maxPersons = 10;
    if($property['maxPax'] < $maxPersons)
    {
      $maxPersons = $property['maxPax'];
    }

    if($property['Rooms']['@attributes']['size'] < 2)
    {
      $temp = $property['Rooms']['RoomType'];
      unset($property['Rooms']['RoomType']);
      $property['Rooms']['RoomType'][0] = $temp;
      unset($temp);
    }
    foreach($property['Rooms']['RoomType'] as $room)
    {

      $cheapest_room_date = $this->get_cheapest_date($room['AvailableDates']);
      //dorm
      if(substr_count($room['roomType'],"Private")==0)
      {
        if(empty($cheapest_prices['min_dorm_price']))
        {
          $cheapest_prices['min_dorm_price'] = $cheapest_room_date['price'];
        }
        elseif(($cheapest_room_date['price'] < $cheapest_prices['min_dorm_price']) )
        {
          $cheapest_prices['min_dorm_price'] = $cheapest_room_date['price'];
        }
      }
      //room
      else
      {
        //skip prices from rooms with > maxperson
        $bedsincrement = explode('_', $room['roomTypeCode'], 2);
        $bedsincrement = (int)$bedsincrement[0];
        if($maxPersons < $bedsincrement)
        {
          continue;
        }

        if(empty($cheapest_prices['min_room_price']))
        {
          $cheapest_prices['min_room_price'] = (float)$cheapest_room_date['price']*$bedsincrement;
          $cheapest_prices['min_room_people'] = $bedsincrement;
        }
        elseif((((float)$cheapest_room_date['price']*$bedsincrement) < $cheapest_prices['min_room_price']) )
        {
          $cheapest_prices['min_room_price'] = (float)$cheapest_room_date['price']*$bedsincrement;
          $cheapest_prices['min_room_people'] = $bedsincrement;
        }

        if(empty($cheapest_prices['min_room_per_person_price']))
        {
          $cheapest_prices['min_room_per_person_price'] = (float)$cheapest_room_date['price'];
        }
        elseif(( (float)$cheapest_room_date['price']) < $cheapest_prices['min_room_per_person_price'] )
        {
          $cheapest_prices['min_room_per_person_price'] = (float)$cheapest_room_date['price'];
        }

      }
    }

    $cheapest_prices['min_price'] = $cheapest_prices['min_room_price'];
    if(empty($cheapest_prices['min_room_price']))
    {
      $cheapest_prices['min_price'] = $cheapest_prices['min_dorm_price'];
    }
    elseif((!empty($cheapest_prices['min_dorm_price'])) &&
             ($cheapest_prices['min_dorm_price'] <=  $cheapest_prices['min_room_price']))
    {
      $cheapest_prices['min_price'] = $cheapest_prices['min_dorm_price'];
    }

    return $cheapest_prices;
  }

  function get_cheapest_date($hw_avail_dates)
  {
    $cheapest_date = array();
    if($hw_avail_dates['@attributes']['size'] < 2)
    {
      $temp = $hw_avail_dates['AvailableDate'];
      unset($hw_avail_dates['AvailableDate']);
      $hw_avail_dates['AvailableDate'][0] = $temp;
      unset($temp);
    }

    foreach($hw_avail_dates['AvailableDate'] as $avail_date)
    {
      if(empty($cheapest_date))
      {
        $cheapest_date = $avail_date;
      }
      elseif(($avail_date['price'] < $cheapest_date['price']) )
      {
        $cheapest_date = $avail_date;
      }
    }

    return $cheapest_date;
  }

  function prepare_distinct_rooms($booking_info, $distinctRoomTypes, $numNights, $onlyFullNights = TRUE)
  {
    $maxPersons = 10;
    if($booking_info->maxPax < $maxPersons)
    {
      $maxPersons = $booking_info->maxPax;
    }

    $sharedRoomsAvailable = 0;
    $sharedRooms = array();

    $privateRoomsAvailable = 0;
    $privateRooms = array();

    foreach ($distinctRoomTypes as $hostel_room_type)
    {
      $availableBeds = $maxPersons;
      $availableRooms = $maxPersons;

      $num_nights_available_of_room = 0;
      $room_all_nights_price = 0;

      $availdatesData = array();
      foreach($hostel_room_type['AvailableDates']['AvailableDate'] as $date_ok)
      {
        $num_nights_available_of_room++;

        //Some prices have 'From ' before the price remove it
        $price_of_room = explode(' ',$date_ok['price']);
        if(strcasecmp($price_of_room[0],'From')==0)
        {
					$date_ok['price'] = $price_of_room[1];
				}
				else
				{
					$date_ok['price'] = $price_of_room[0];
				}

        //Price calculation for entire trip in that room
        $room_all_nights_price += (double)$date_ok["price"];

        //Available beds for that room
        if((int)$date_ok['availableBeds'] < $availableBeds)
        {
          $availableBeds = (int)$date_ok['availableBeds'];
        }

        //Available rooms of that type of room
        if((int)$date_ok['availableRooms'] < $availableRooms)
        {
          $availableRooms = (int)$date_ok['availableRooms'];
        }

        //Available date price table
        $availdatesData[(string)$date_ok["date"]]["price"]          = number_format((double)$date_ok['price']*(int)$hostel_room_type['bedsIncrement'],2,'.','');
        $availdatesData[(string)$date_ok["date"]]["availableBeds"]  = (int)$date_ok['availableBeds'];
        $availdatesData[(string)$date_ok["date"]]["availableRooms"] = (int)$date_ok['availableRooms'];
      }

      $hostel_room_type["max_guest_per_unity"]   = explode(':',$hostel_room_type['roomTypeCode']);
      $hostel_room_type["max_guest_per_unity"]   = (int) $hostel_room_type["max_guest_per_unity"][0];

      //Check for dorms only
      if(substr_count($hostel_room_type['roomType'],"Private")==0)
      {
        //IF all night are available
        if(($onlyFullNights===FALSE) || ($num_nights_available_of_room >= $numNights))
        {
          $sharedRoomsAvailable++;
          $sharedRoom = array();
          $sharedRoom["roomTypeCode"]          = (string)$hostel_room_type['roomTypeCode'];
          $sharedRoom["max_guest_per_unity"]   = $hostel_room_type['max_guest_per_unity'];
          $sharedRoom["description"]           = (string)$hostel_room_type['roomTypeDescription'];
          $sharedRoom["descriptionTranslated"] = "";
          if(!empty($hostel_room_type['roomTypeDescriptionTranslated']))
          {
            $sharedRoom["descriptionTranslated"] = (string)$hostel_room_type['roomTypeDescriptionTranslated'];
          }
          $sharedRoom["total_price"]           = number_format((double)$room_all_nights_price,2,'.','');
          $sharedRoom["currency"]              = currency_symbol($hostel_room_type["BedPrice"]["currency"]);
          $sharedRoom["availableBeds"]         = (int)$availableBeds;
          $sharedRoom["availableRooms"]        = (int)$availableRooms;
          $sharedRoom["availableDates"]        = $availdatesData;
          array_push($sharedRooms, $sharedRoom);
        }
      }
      //Show private rooms with beds increment lower than maxpax because if it is higher it will be a group booking and will cause problem on booking
      elseif($maxPersons > $hostel_room_type["bedsIncrement"])
      {
      //IF all night are available
        if(($onlyFullNights===FALSE) || ($num_nights_available_of_room >= $numNights))
        {
          //Room price
          $room_all_nights_price = $room_all_nights_price * (int)$hostel_room_type['bedsIncrement'];

          $privateRoomsAvailable++;
          $privateRoom = array();
          $privateRoom["roomTypeCode"]          = (string)$hostel_room_type['roomTypeCode'];
          $privateRoom["max_guest_per_unity"]   = $hostel_room_type['max_guest_per_unity'];
          $privateRoom["description"]           = (string)$hostel_room_type['roomTypeDescription'];
          $privateRoom["descriptionTranslated"] = "";
          if(!empty($hostel_room_type['roomTypeDescriptionTranslated']))
          {
            $privateRoom["descriptionTranslated"] = (string)$hostel_room_type['roomTypeDescriptionTranslated'];
          }
          $privateRoom["total_price"]           = number_format((double)$room_all_nights_price,2,'.','');
          $privateRoom["currency"]              = currency_symbol((string)$hostel_room_type["BedPrice"]["currency"]);
          $privateRoom["bedsPerRoom"]           = (int)$hostel_room_type['bedsIncrement'];
          $privateRoom["availableRooms"]        = (int)$availableRooms;
          $privateRoom["availableDates"]        = $availdatesData;
          array_push($privateRooms, $privateRoom);

        }
      }
    }
    return array("sharedRooms" => $sharedRooms, "privateRooms" => $privateRooms);
  }

  function distinct_hw_rooms_data($booking_info)
  {
    //Merge all same room types and the available dates
    $distinctRoomTypes = array();
    $rti = 0;

    $roomsData = xmlobj2arr($booking_info->Rooms);

  	if ($roomsData["@attributes"]["size"] < 2)
  	{
  		$roomsData["RoomType"] = array( 0 => $roomsData["RoomType"]);
  	}

    foreach ($roomsData["RoomType"] as $hostel_room_type)
    {

      $isRoomDistinct = true;

      if($rti!=0)
      {
        foreach($distinctRoomTypes as &$distinctRoom)
        {
          //If roomType is already in array
          if(strcmp($hostel_room_type["roomTypeCode"],$distinctRoom["roomTypeCode"])==0)
          {
            $isRoomDistinct = false;

            //if more than one availableDate
            if(intval($hostel_room_type["AvailableDates"]["@attributes"]["size"]) == 1)
            {
              $newdate = true;
              foreach($distinctRoom["AvailableDates"]["AvailableDate"] as $oldDate)
              {
                if(strcmp($hostel_room_type["AvailableDates"]['AvailableDate']['date'],$oldDate['date'])==0)
                {
                  $newdate = false;
                }
              }

              if($newdate)
              {
                array_push($distinctRoom["AvailableDates"]["AvailableDate"],$hostel_room_type["AvailableDates"]['AvailableDate']);
              }
            }
            //If AvailableDate is an array
            else
            {
              //Add new distinct date only
              foreach($hostel_room_type["AvailableDates"]['AvailableDate'] as $maybeNewDate)
              {
                $newdate = true;
                //check all added date up to now
                foreach($distinctRoom["AvailableDates"]["AvailableDate"] as $oldDate)
                {
                  if(strcmp($maybeNewDate['date'],$oldDate['date'])==0)
                  {
                    $newdate = false;
                  }
                }

                if($newdate)
                {
                  array_push($distinctRoom["AvailableDates"]["AvailableDate"],$maybeNewDate);
                }

              }
            }
          }
        }
      }

      if($isRoomDistinct==true)
      {

        $distinctRoomTypes[$rti] = $hostel_room_type;
        if($distinctRoomTypes[$rti]["AvailableDates"]["@attributes"]["size"] < 2)
        {
           $distinctRoomTypes[$rti]["AvailableDates"]["AvailableDate"] = array(0 => $distinctRoomTypes[$rti]["AvailableDates"]["AvailableDate"]);
        }
        $rti++;
      }
    }

    return $distinctRoomTypes;
  }
  /*
   * Not finished
   */
  //TODO Get rid of call by reference &data
  // Not compatible with PHP 5.4
  function booking_request_data_init(&$booking_request_data = array())
  {
    /*
    $booking_data['bsid']             = $this->CI->input->post('book-propertyNumber',TRUE);
    if(($booking_data['bsid']) === FALSE)
    {
      $booking_data['bsid'] = "";
    }
    $propertyNumber = $this->input->post('book-propertyNumber',TRUE);
    $dateStart      = $this->input->post('book-dateStart',TRUE);
    $numNights      = $this->input->post('book-numNights',TRUE);

    $roomPreferences = $this->input->post('book-roomPreferences',TRUE);
    $nbPersons       = $this->input->post('book-nbPersons',TRUE);
    $bookCurrency    = $this->input->post('book-currency',TRUE);
    $settleCurrency  = $bookCurrency;
//     $settleCurrency = settle_currency_filter($settleCurrency,$this->config->item('site_settle_currency_default'));
    $data['booking_hostel_name'] = $this->input->post('book-propertyName');

    $bsid = $this->session->userdata('BSID_'.$propertyNumber);

    if($bsid==false) $bsid = NULL;
*/


  }

  function booking_request($propertyNumber,
                           $dateStart,
                           $numNights,
                           $roomPreferences,
                           $nbPersons,
                           $settleCurrency,
                           $bsid,
                           $bookCurrency )
  {

    $api = $this->CI->Hostel_api_model->propertyBookingRequest($this->CI->config->item('hostelworld_userID'),
                                                               $propertyNumber,
                                                               $dateStart,
                                                               $numNights,
                                                               $roomPreferences,
                                                               $nbPersons,
                                                               $settleCurrency,
                                                               $bsid,
                                                               $bookCurrency,
                                                               $this->api_functions_lang);
    $data['booking_request'] = array();
    $data['api_error'] = $api[0];
    $data['api_error_msg'] = false;

    $numNights_calculated = 0;
    $dateStart_calculated = $dateStart;
    //If error on API request
    if($data['api_error']==true)
    {
      $data['api_error_msg'] = $api[1][0];

      if(($api[1][0]==TRUE)&& is_string($data['api_error_msg']) )
      {
        //serveur inaccessible en ce moment
        $data['api_error_msg'] = _('Serveur inaccessible. Veuillez réessayer plus tard.');
      }
      else
      {
        //API error message
        $data['api_error_msg'] = $this->CI->Hw_api_translate->translate_APIError($data['api_error_msg']);

        if(isset($data['api_error_msg']->Error))
        {
          //erreur de l'api traduite
          if(!empty($data['api_error_msg']->Error->detailTranslated))
          {
            $data['api_error_msg'] = $data['api_error_msg']->Error->messageTranslated.": ".$data['api_error_msg']->Error->detailTranslated;
          }
          else
          {
            $data['api_error_msg'] = $data['api_error_msg']->Error->message.": ".$data['api_error_msg']->Error->detail;
          }
        }
        else
        {
          //erreur de l'api traduite
          if(!empty($data['api_error_msg']->UserMessage->detailTranslated))
          {
            $data['api_error_msg'] = $data['api_error_msg']->UserMessage->messageTranslated.": ".$data['api_error_msg']->UserMessage->detailTranslated;
          }
          else
          {
            $data['api_error_msg'] = $data['api_error_msg']->UserMessage->message.": ".$data['api_error_msg']->UserMessage->detail;
          }
        }
      }
    }
    //If API return is good
    else
    {
      $data['booking_request'] = $this->CI->Hw_api_translate->translate_bookingRequest($api[1][0],$propertyNumber,$dateStart,$numNights);
      $this->CI->session->set_userdata('BSID_'.$propertyNumber, $data['booking_request']->BSID->value);

      $numNights_calculated = $this->CI->Hostel_api_model->count_numnights($data['booking_request']->RoomDetails);
      $dateStart_calculated = (string)$data['booking_request']->RoomDetails[0]->date;
      $dateStart_calculated = new DateTime($dateStart_calculated);

    }

    $data['propertyNumber'] = $propertyNumber;
    $data['dateStart']      = $dateStart;
    $data['dateStart_calculated']      = $dateStart_calculated;
    $data['numNights']      = $numNights;
    $data['numNights_calculated']      = $numNights_calculated;
    $data['settleCurrency']   = $settleCurrency;
    $data['bookCurrency']     = $bookCurrency;
    $data['roomPreferences'] = $roomPreferences;
    $data['nbPersons']      = $nbPersons;

    return $data;
  }

  //TODO Get rid of call by reference &data
  // Not compatible with PHP 5.4
  function booking_data_init(&$booking_data = array())
  {
    $booking_data['bsid']             = $this->CI->input->post('bsid',TRUE);
    if(($booking_data['bsid']) === FALSE)
    {
      $booking_data['bsid'] = "";
    }

    $booking_data['book_firstname']   = $this->CI->input->post('firstname',TRUE);
    if(($booking_data['book_firstname']) === FALSE)
    {
      $booking_data['book_firstname'] = "";
    }
    $booking_data['book_lastname']    = $this->CI->input->post('lastname',TRUE);
    if(($booking_data['book_lastname']) === FALSE)
    {
      $booking_data['book_lastname'] = "";
    }
    $booking_data['book_nationality'] = $this->CI->input->post('nationality',TRUE);
    if(($booking_data['book_nationality']) === FALSE)
    {
      $booking_data['book_nationality'] = "";
    }
    $booking_data['book_gender']      = $this->CI->input->post('gender',TRUE);
    if(($booking_data['book_gender']) === FALSE)
    {
      $booking_data['book_gender'] = "";
    }
    $booking_data['book_email_address'] = $this->CI->input->post('email',TRUE);
    if(($booking_data['book_email_address']) === FALSE)
    {
      $booking_data['book_email_address'] = "";
    }
    $booking_data['book_email_address2'] = $this->CI->input->post('email2',TRUE);
    if(($booking_data['book_email_address2']) === FALSE)
    {
      $booking_data['book_email_address2'] = "";
    }
    $booking_data['book_phone_number']  = $this->CI->input->post('phone_number',TRUE);
    if(($booking_data['book_phone_number']) === FALSE)
    {
      $booking_data['book_phone_number'] = "";
    }
    $booking_data['sms']  = $this->CI->input->post('sms',TRUE);
    if(($booking_data['sms']) === FALSE)
    {
      $booking_data['sms'] = "none";
    }
    $booking_data['book_arrival_time']  = $this->CI->input->post('arrival_time',TRUE);
    if(($booking_data['book_arrival_time']) === FALSE)
    {
      $booking_data['book_arrival_time'] = 11;
    }
    $booking_data['book_ccname']        = $this->CI->input->post('ccname',TRUE);
    if(($booking_data['book_ccname']) === FALSE)
    {
      $booking_data['book_ccname'] = "";
    }
    $booking_data['book_ccnumber']    = $this->CI->input->post('ccnumber',TRUE);
    if(($booking_data['book_ccnumber']) === FALSE)
    {
      $booking_data['book_ccnumber'] = "";
    }
    $booking_data['book_ccexpiry_m']  = $this->CI->input->post('ccexpiry_m',TRUE);
    if(($booking_data['book_ccexpiry_m']) === FALSE)
    {
      $booking_data['book_ccexpiry_m'] = date("m");
    }
    $booking_data['book_ccexpiry_y']  = $this->CI->input->post('ccexpiry_y',TRUE);
    if(($booking_data['book_ccexpiry_y']) === FALSE)
    {
      $booking_data['book_ccexpiry_y'] = date("y");
    }
    $booking_data['book_cctype']      = $this->CI->input->post('cctype',TRUE);
    if(($booking_data['book_cctype']) === FALSE)
    {
      $booking_data['book_cctype'] = "";
    }
    $booking_data['settleCurrency']   = $this->CI->input->post('settleCurrency',TRUE);
    if(($booking_data['settleCurrency']) === FALSE)
    {
      $booking_data['settleCurrency'] = "";
    }
    $booking_data['settle_bill_total']   = $this->CI->input->post('settle_bill_total',TRUE);
    if(($booking_data['settle_bill_total']) === FALSE)
    {
      $booking_data['settle_bill_total'] = NULL;
    }
    $booking_data['bookCurrency']     = $this->CI->input->post('bookCurrency',TRUE);
    if(($booking_data['bookCurrency']) === FALSE)
    {
      $booking_data['bookCurrency'] = "";
    }
    $booking_data['issueno']          = $this->CI->input->post('issueno',TRUE);
    if(($booking_data['issueno']) === FALSE)
    {
      $booking_data['issueno'] = "";
    }
    $booking_data['ccvalidfrom_m']    = $this->CI->input->post('ccvalidfrom_m',TRUE);
    if(($booking_data['ccvalidfrom_m']) === FALSE)
    {
      $booking_data['ccvalidfrom_m'] = date("m");
    }
    $booking_data['ccvalidfrom_y']    = $this->CI->input->post('ccvalidfrom_y',TRUE);
    if(($booking_data['ccvalidfrom_y']) === FALSE)
    {
      $booking_data['ccvalidfrom_y'] = date("Y");;
    }
    $booking_data['cvv']              = $this->CI->input->post('cvv',TRUE);
    if(($booking_data['cvv']) === FALSE)
    {
      $booking_data['cvv'] = "";
    }
    $booking_data["testmode"] = $this->CI->config->item('booking_test_mode');

    $this->CI->load->library('tank_auth');
    $user_id = $this->CI->tank_auth->get_user_id();
    if(($user_id !== false))
    {
      $uprof = $this->CI->tank_auth->get_profile($user_id);
      if($uprof->user_level_id >= 8)
      {
        $booking_data["testmode"] = 1;
      }
    }
    if (ISDEVELOPMENT)
	{
	    $booking_data["testmode"] = 1;
    }

    $booking_data['mail_subscribe']  = $this->CI->input->post('mail_subscribe')=="true";
    $booking_data['conditions_agree']= $this->CI->input->post('conditions_agree')=="true";
    $booking_data["propertyNumber"]  = $this->CI->input->post('propertyNumber',TRUE);
    if(($booking_data['propertyNumber']) === FALSE)
    {
      $booking_data['propertyNumber'] = NULL;
    }
    $booking_data["propertyCity"]    = $this->CI->input->post('propertyCity',TRUE);
    if(($booking_data['propertyCity']) === FALSE)
    {
      $booking_data['propertyCity'] = "";
    }
    $booking_data["propertyCountry"] = $this->CI->input->post('propertyCountry',TRUE);
    if(($booking_data['propertyCountry']) === FALSE)
    {
      $booking_data['book_firstname'] = "";
    }

    $booking_data["card_supported"] = array();
    $cards = $this->CI->input->post('cardsupported',TRUE);
    if(!empty($cards))
    {
      if(!is_array($cards))
      {
        $cards = explode(",",$cards);
      }

      foreach($cards as $i => $card)
      {
       $card = explode("|",$card);
       $booking_data["card_supported"][$i]->type        = $card[0];
       $booking_data["card_supported"][$i]->name        = $card[1];
       $booking_data["card_supported"][$i]->currency    = $card[2];
       $booking_data["card_supported"][$i]->IssueNO     = $card[3];
       $booking_data["card_supported"][$i]->CCValidFrom = $card[4];
      }
      unset($cards);
    }

    $booking_data['api_booking_error'] = false;
    $booking_data['api_booking_error_msg'] = array();

    return $booking_data;
  }

  function booking_payment()
  {
    $booking_data = $this->booking_data_init();

    $booking_data['secure_pares']       = NULL;
    $booking_data['secure_cookie']      = NULL;
    $booking_data['secure_transid']     = NULL;
    $booking_data['secure_newsession']  = NULL;
    $booking_data['secure_ip']          = NULL;
    $booking_data['secure_usersession'] = NULL;

    $booking = $this->CI->Hostel_api_model->bookingConfirmationRequest(
                                              $this->CI->config->item('hostelworld_userID'),
                                              $booking_data['bsid'],
                                              $booking_data['book_firstname'],
                                              $booking_data['book_lastname'],
                                              $booking_data['book_nationality'],
                                              $booking_data['book_gender'],
                                              $booking_data['book_email_address'],
                                              $booking_data['book_phone_number'],
                                              $booking_data['book_arrival_time'],
                                              $booking_data['book_ccname'],
                                              $booking_data['book_ccnumber'],
                                              $booking_data['book_ccexpiry_m'].'/'.$booking_data['book_ccexpiry_y'],
                                              $booking_data['book_cctype'],
                                              $booking_data['settleCurrency'],
                                              $booking_data['bookCurrency'],
                                              $booking_data['issueno'],
                                              $booking_data['ccvalidfrom_m'].'/'.$booking_data['ccvalidfrom_y'],
                                              $booking_data['cvv'],
                                              $booking_data['secure_pares'],
                                              $booking_data['secure_cookie'],
                                              $booking_data['secure_transid'],
                                              $booking_data['secure_newsession'],
                                              $booking_data['secure_ip'],
                                              $booking_data['secure_usersession'],
                                              $booking_data["testmode"]);


    //if booking ok and confirm
    if(($booking[0] == false)&&(strcmp($booking[1][0]->BookingRequestResult->message,'Booking Confirmed')==0))
    {
      $booking = $booking[1][0];
      unset($booking_data['book_ccname']);
      unset($booking_data['book_ccnumber']);
      unset($booking_data['book_ccexpiry_m']);
      unset($booking_data['book_ccexpiry_y']);
//      $this->_booking_confirmation($booking,$postdata);
      $booking_data["booking_status"] = "OK";

    }
    //if booking ok but 3D secure request is asked
    elseif(($booking[0] == false)&&(strcmp($booking[1][0]->BookingRequestResult->message,'3D Secure Request')==0))
    {
      $booking_data["booking_status"] = "3D";
      $booking_data['api_booking_error'] = "api_msg";

      $booking_data['api_booking_error_msg'] = _("Card not supported please choose another card");
//      $postdata['api_booking_error'] = "3d_secure_request";
//
//      $postdata['secure_parameters']['PaReq']   = get_object_vars($booking[1][0]->parequest);
//      $postdata['secure_parameters']['PaReq']   = $postdata['secure_parameters']['PaReq']['message'];
//      $postdata['secure_parameters']['TermUrl'] = site_url($this->Db_links->get_link('secure_validation'));
//      $postdata['secure_parameters']['MD']      = get_object_vars($booking[1][0]->sessionId);
//      $postdata['secure_parameters']['MD']      = $postdata['secure_parameters']['MD']['message'];
//      $postdata['secure_parameters']['issuerURL'] = get_object_vars($booking[1][0]->issuerURL);
//      $postdata['secure_parameters']['issuerURL'] = $postdata['secure_parameters']['issuerURL']['message'];
//      $postdata['secure_parameters']['cookie']  = get_object_vars($booking[1][0]->cookie);
//      $postdata['secure_parameters']['cookie']  = $postdata['secure_parameters']['cookie']['message'];
//      $postdata['secure_parameters']['transactionId'] = get_object_vars($booking[1][0]->transactionId);
//      $postdata['secure_parameters']['transactionId'] = $postdata['secure_parameters']['transactionId']['message'];
//
//      $this->_storeBookingSession($postdata);
//      $this->booking_process(true,$postdata);
    }
    //if API throws error message
    elseif($booking[1]!=false)
    {
      $booking_data["booking_status"] = "api_error";
      //log transaction error
      $this->CI->load->library('custom_log');
      $this->CI->custom_log->set_freq("Y-m-d");
      $this->CI->custom_log->log($this->transaction_log_filename,"Transaction error dump for ".$booking_data['book_email_address']." ( ".$booking_data['book_lastname'].", ".$booking_data['book_firstname']." ) with card ".$booking_data['book_cctype']." in ".$booking_data['settleCurrency'].": ".var_export($booking[1][0],true));

      $booking_data['api_booking_error'] = "api_msg";

      $booking_data['api_booking_error_msg'] = $this->CI->Hw_api_translate->translate_APIError($booking[1][0]);
      if(isset($booking_data['api_booking_error_msg']->Error))
      {
         $booking_data['api_booking_error_msg'] =  $booking_data['api_booking_error_msg']->Error;

      }
      elseif(isset($booking_data['api_booking_error_msg']->UserMessage))
      {
        $booking_data['api_booking_error_msg'] =  $booking_data['api_booking_error_msg']->UserMessage;
      }
      else
      {
        $booking_data['api_booking_error_msg'][0] =  _("Erreur lors de la réservation");
        $this->CI->custom_log->log($this->transaction_log_filename,"Transaction error not set UserMessage or Error.");
      }
      //print_r($booking[1][0]);
    }
    //if API not responding ok
    else
    {
      $booking_data["booking_status"] = "api_out";
      $booking_data['api_booking_error'] = "api_out";
      $booking_data['api_booking_error_msg'][0] =  _("Serveur inaccessible pour le moment. Veuillez réessayer de nouveau.");
    }

    $data["booking"] = $booking;
    $data["booking_data"] = $booking_data;
    return $data;
  }

  public function store_property_facilities($property_number,$log_filename = "", $logtreshold = 2)
  {
    //Force english facilities in DB
    $this->api_functions_lang = "en";
    $data = array();

    //Get all property info specially for facilities
    $data = $this->property_info($data,$property_number);

    $this->CI->load->model('Db_hw_hostel');

    //Set log file if needed
    if(!empty($log_filename))
    {
      $this->CI->code_tracker->set_logfile($log_filename);
      $this->CI->code_tracker->set_feed_treshold($logtreshold);
    }

    //update and return status
    return $this->CI->Db_hw_hostel->update_hw_hostel_facilities($property_number, $data["hostel"]->facilities);
  }

  function propertyimg($prid)
  {
  		$this->CI->load->model('Hostel_api_model');
  		$results = $this->CI->Hostel_api_model->PropertyInformation($this->CI->config->item('hostelworld_userID'),$prid, $this->api_functions_lang);
		return $results;
  }
}
