<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Hb_engine
 *
 * Hb_engine library for code igniter
 *
 * @package   Hb_engine
 * @author    Louis-Michel Raynauld
 * @version   0.1
 * @license   Commercial
 */
class Hb_engine {

  const LANDING_PAGES_DIR = "cache_queries/landing_pages";

  var $CI;
  var $api_functions_lang = "en";

  var $user_id = 0;

  function Hb_engine()
  {
    $this->CI =& get_instance();

    $this->initialize();

    log_message('debug', "Hb_engine Class Initialized");
  }

  function initialize()
  {
    $this->CI->load->model('Hostelbookers_api');
    $this->CI->load->helper(array('text'));

    $this->api_functions_lang = $this->CI->Hostelbookers_api->lang_code_convert($this->CI->site_lang);
    $this->_translation_init();

  }

  function _translation_init()
  {
    $this->CI->load->model('Hb_api_translate');

    //If site is english, set from lang to english
    // else auto-detect because api langage return is not certain

    if(strcmp($this->api_functions_lang,"en")==0)
    {
      $this->CI->Hb_api_translate->setLanguage($this->CI->site_lang,"en");
    }
    else
    {
      $this->CI->Hb_api_translate->setLanguage($this->CI->site_lang,"");
    }
  }

  /*
   * Converts hb int date to standard timestamp
   */
  function hb_date_int_convert($hb_date_int)
  {
    return mktime(0,0,0,1,$hb_date_int-1,1900);
  }

  function get_city_cache_filename($country, $city)
  {
    return self::LANDING_PAGES_DIR."/".$this->CI->site_lang."/".strtolower($country).hash('md5',strtolower($city)).'.html';
  }

  function _sort_reviews(&$reviews)
  {
    function reviewcmp($a, $b)
    {
      $ad = new Datetime($a["review_date"]);
      $bd = new Datetime($b["review_date"]);

      if($ad->format("U") < $bd->format("U"))
      {
        return 1;
      }
      elseif($ad->format("U") > $bd->format("U"))
      {
        return -1;
      }


      return 0;
    }

    usort($reviews, "reviewcmp");
  }

  function property_reviews($property_id, $max_review_count = 0)
  {

    $data["user_reviews"] = array();
    $data["reviews_translation_available"] = false;
    $index = 0;
    $this->CI->load->model('Db_reviews');
    $our_reviews = $this->CI->Db_reviews->get_property_reviews($property_id);

    if(!empty($our_reviews))
    {
      foreach($our_reviews as $our_review)
      {
        $data["user_reviews"][$index] = $our_review;
        $index++;

        if(($max_review_count > 0)&&($index >= $max_review_count))
        {
          $data["reviews_translation_available"] = $this->CI->Hb_api_translate->translate_reviews($data["user_reviews"]);

          $data["review_count"] = $index;
          return $data;
        }
      }
    }

    $remote_review_num = 25;
    if($max_review_count > 0)
    {
      $remote_review_num = $max_review_count - $index;
    }

    $reviews = $this->CI->Hostelbookers_api->getPropertyReviews($property_id, $remote_review_num);

    if(!empty($reviews->reviews->review))
    {
      foreach($reviews->reviews->review as $review)
      {
        $data["user_reviews"][$index]["author_name"]     = strtok((string)$review->name," ");
        $data["user_reviews"][$index]["review_date"]     = (string)$review->date;
        $data["user_reviews"][$index]["author_country"]  = "";
        $data["user_reviews"][$index]["review_likebest"] = (string)$review->likedBest;
        $data["user_reviews"][$index]["review_likebest_translated"] = "";
        $data["user_reviews"][$index]["review_rating"]   = round(floatval($review->overallRating));
        $index++;
      }

    }

    $data["reviews_translation_available"] = $this->CI->Hb_api_translate->translate_reviews($data["user_reviews"]);

    $data["review_count"] = $index;

    return $data;

  }

  function map_home_page(&$data)
  {

    $data['title'] = $this->CI->config->item('site_title');
    $data['load_jslib_jquery'] = true;

    $data['user_id'] = $this->CI->user_id;

    $data['google_map_enable']    = true;
    $data['google_map_country_list'] = true;

    $this->CI->load->model('Db_hb_city');

    $data['country_list'] = $this->CI->Db_hb_city->cityCountryList_DB(NULL,NULL,$this->CI->site_lang);

    $data['eu_country_list'] = $this->CI->Db_hb_city->cityCountryList_DB("europe",NULL,$this->CI->site_lang);
    $data['na_country_list'] = $this->CI->Db_hb_city->cityCountryList_DB("north america",NULL,$this->CI->site_lang);
    $data['sa_country_list'] = $this->CI->Db_hb_city->cityCountryList_DB("south america",NULL,$this->CI->site_lang);
    $data['as_country_list'] = $this->CI->Db_hb_city->cityCountryList_DB("asia",NULL,$this->CI->site_lang);
    $data['oc_country_list'] = $this->CI->Db_hb_city->cityCountryList_DB("oceania",NULL,$this->CI->site_lang);
    $data['af_country_list'] = $this->CI->Db_hb_city->cityCountryList_DB("africa",NULL,$this->CI->site_lang);

    return $data;
  }

  function continent_data(&$data, $continent)
  {
    $continent = customurldecode($continent);

    $this->CI->load->model('Db_hb_city');

    $xmlcountries = $this->CI->Db_hb_city->cityCountryList_DB($continent,NULL,$this->CI->site_lang);
    $data['continent_selected_lang'] = $xmlcountries->Country[0]->countryContinentTranslated;

    $data['bc_continent']       = $data['continent_selected_lang'];

    $data['countries_of_continent'] = $xmlcountries;

    $data['title'] = sprintf(gettext("Hostels in %s - %s"),my_mb_ucfirst($continent),$this->CI->config->item('site_title'));

    $data['google_map_enable']    = true;
    $data['google_map_country_list'] = true;
    $data['country_list'] =  $xmlcountries;

    return $data;
  }

  function country_data(&$data, $continent, $country)
  {
    $continent = customurldecode($continent);
    $country   = customurldecode($country);

    $this->CI->load->model('Db_hb_city');

    $country   = my_mb_ucfirst(mb_strtolower($country,"UTF-8"));

    $xmlcities = $this->CI->Db_hb_city->cityCountryList_DB(NULL,$country,$this->CI->site_lang);

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

  function hb_site_search(&$data, $terms = NULL)
  {
    $data['results'] = array();
    $data['search_term'] = $terms;

    $this->CI->load->model('Db_hb_search');

    $data['results'] = $this->CI->Db_hb_search->search_hb_data($terms, $this->CI->site_lang, 150);
    if(!empty($data['results']))
    {
      $this->CI->Hb_api_translate->translate_search_results($data['results']);
    }

    return $data;
  }

  function hb_site_search_suggest($terms = NULL, $filter = 'all', $show_more_results_link = FALSE, $term_from_start = FALSE)
  {
    $data['results'] = array();
    $data['search_term'] = $terms;
    if(!empty($terms) && (strlen(trim($terms)) > 2 ))
    {
      $terms = utf8_strip_specials($terms);
      $data['search_term'] = $terms;
      $data['show_more_results_link'] = $show_more_results_link;
      $this->CI->load->model('Db_hb_search');
      $data['suggestions'] = $this->CI->Db_hb_search->suggest_hb_data($terms, $this->CI->site_lang, 10, $filter, $term_from_start);
    }

    return $data;
  }
  function location_search($country , $city, $dateStart = NULL, $numNights = NULL, $include_availdata = FALSE, $prop_reviews = FALSE, $filters = array())
  {
    $this->CI->load->model('Db_hb_country');

    $country_select    = urldecode (customurldecode($country));
    $city_select       = urldecode (customurldecode($city));

    //Exception: support new york URL
    if(strcasecmp($city_select,"new york")==0)
    {
      $city_select = "new york city";
    }

    $city    = $this->CI->Db_hb_country->get_city($country_select,$city_select,$this->CI->site_lang);
    if(empty($city))
    {
      return FALSE;
    }
    $data['city_info'] = $city;

    $data["most_popular_amenities"] = array();
    $data['city_amenities'] = array();
    $data['city_districts'] = array();
    $data['city_landmarks'] = array();

    if(!empty($dateStart)&& empty($numNights))
    {
      //echo warning date and $numNights is disregard wrong format missing parameter.
      $dateStart = NULL;
      $numNights = NULL;
      $data['warning']         = true;
      $data['warning_message'] = _('Date et nombre de nuits invalide.  Valeurs ignorées lors de la recherche.');
    }

    $data['currency'] = $this->CI->site_currency;

    $results = Array();

    $data['searchmode'] = 0;
    $this->CI->load->model('Db_hb_hostel');

    $data_from_live_api = true;

    if(empty($dateStart)&& empty($numNights))
    {
      $cache_time = $this->CI->wordpress->get_option("aj_cache_time_city_landing_pages",0);
      if(!empty($cache_time))
      {
        $this->CI->output->cache($cache_time);
      }

      //Landmark data for landmark landing page
      $data['filters'] = array( "type" => NULL,
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

        $data['filters']["landmark"] = $this->CI->Db_landmarks->get_city_landmark_from_slug($data["city_info"]->hb_id,$filters["landmark"]);

        if(!empty($data['filters']["landmark"]->landmark_name))
        {
          $this->CI->load->model('i18n/db_translation_cache');
          $data['filters']["landmark"]->landmark_name_ts = $this->CI->db_translation_cache->get_translation($data['filters']["landmark"]->landmark_name,$this->CI->site_lang);
          $data['filters']["landmark"]->landmark_name_ts = $data['filters']["landmark"]->landmark_name_ts->translation;
        }
      }

      //District data for district landing page
      if(!empty($filters["district"]))
      {
        $this->CI->load->model('Db_districts');
        $data['filters']["district"] = $this->CI->Db_districts->get_city_district_from_slug($data["city_info"]->hb_id,$filters["district"]);
        if(!empty($data['filters']["district"]->district_name))
        {
          $filters["district"] = $data['filters']["district"]->district_name;

          $this->CI->load->model('i18n/db_translation_cache');
          $data['filters']["district"]->district_name_ts = $this->CI->db_translation_cache->get_translation($data['filters']["district"]->district_name,$this->CI->site_lang);
          $data['filters']["district"]->district_name_ts = $data['filters']["district"]->district_name_ts->translation;
        }
      }

      $data['searchmode'] = 0;
      $results = $this->CI->Db_hb_hostel->get_location_properties(
            $city->country_system_name, $city->system_name,
            $this->api_functions_lang, $data['currency'], 25, $filters);
      $data_from_live_api = false;

      if(is_null($results))
      {
        //For now if DB return no data return false to cause 404 error
        return FALSE;

      //Standardize case of API array keys, because  API getLocationData returns UPPER CASE array keys
       // Performance takes ~0.003 sec for getLocationData returning array of 94 properties
        $results = $this->CI->Hostelbookers_api->getLocationData(
            $city->country_system_name, $city->system_name,
            $this->api_functions_lang, $data['currency']);
        $data_from_live_api = true;
      }
    }
    else
    {
      $data['searchmode'] = 1;

      if($include_availdata == TRUE)
      {
        $results = $this->CI->Hostelbookers_api->getLocationAvailability(
            $city->hb_id,$dateStart, $numNights, $this->api_functions_lang, $data['currency']);
      }

      $cache_time = $this->CI->wordpress->get_option("aj_cache_time_city_avail_pages",0);
      if(!empty($cache_time))
      {
        $this->CI->output->cache($cache_time);
      }

      if($include_availdata !== true)
      {
        //Add district landmark of city
        $amenityGroups = $this->CI->Db_hb_hostel->get_amenities_city_for_filter();
        $data["most_popular_amenities"] = $amenityGroups["mostPopularAmenities"];
        $data['city_amenities'] = $amenityGroups["amenities"];
        $data['city_districts'] = $this->CI->Db_hb_hostel->get_districts_by_city_id($city->hb_id);
        $data['city_landmarks'] = $this->CI->Db_hb_hostel->get_landmarks_by_city_id($city->hb_id,2);
        //translate city landmarks
        $this->CI->load->model('i18n/db_translation_cache');
             $tmp_city_landmarks=array();
        $tmp_landmarks=array();
        foreach($data['city_landmarks'] as $i => $landmark)
        {
          $translation = $this->CI->db_translation_cache->get_translation($landmark->landmark_name,$this->CI->site_lang);
          $data['city_landmarks'][$i]->original_name = $data['city_landmarks'][$i]->landmark_name;

          $tmp_city_landmarks[$i] = strtolower($data['city_landmarks'][$i]->landmark_name);

          if(!empty($translation))
          {
            $data['city_landmarks'][$i]->original_name = $data['city_landmarks'][$i]->landmark_name;
            $data['city_landmarks'][$i]->landmark_name = $translation->translation;
            $tmp_city_landmarks[$i] = strtolower($data['city_landmarks'][$i]->landmark_name);

          }
          $tmp_landmarks[$i] = $data['city_landmarks'][$i];

        }

      /* array data shot by city_landmarks pramod*/
      if(!empty($tmp_city_landmarks)){
		sort($tmp_city_landmarks);
		foreach($tmp_city_landmarks as $i=>$val){

			foreach($tmp_landmarks as $j=>$cities_original_data){
					if($val == strtolower($cities_original_data->landmark_name)){
						$data['city_landmarks'][$i] = $cities_original_data;
					}
			}
		}
	}

	 //translate city districts
        $tmp_city_districts=array();
        $tmp_districts=array();
        foreach($data['city_districts'] as $i => $district)
        {
          $translation = $this->CI->db_translation_cache->get_translation($district->district_name,$this->CI->site_lang);
          $tmp_city_districts[$i] = strtolower($data['city_districts'][$i]->district_name);

          if(!empty($translation))
          {
            $data['city_districts'][$i]->original_name = $data['city_districts'][$i]->district_name;
            $data['city_districts'][$i]->district_name = $translation->translation;
            $tmp_city_districts[$i] = strtolower($data['city_districts'][$i]->district_name);
          }
           $tmp_districts[$i] = $data['city_districts'][$i];
        }

        /* array data shot by city districts pramod */
      if(!empty($tmp_city_districts)){
		sort($tmp_city_districts);
		foreach($tmp_city_districts as $i=>$val){

			foreach($tmp_districts as $j=>$districts_original_data){
					if($val == strtolower($districts_original_data->district_name)){
						$data['city_districts'][$i] = $districts_original_data;
					}
			}
		}
	}


        //translate city amenities
         $tmp_city_name=array();
         $tmp_city=array();

        foreach(array_merge($data['city_amenities'], $data["most_popular_amenities"]) as $i => $amenity)
        {

          $translation = $this->CI->db_translation_cache->get_translation($amenity->facility_name,$this->CI->site_lang);
          $amenity->original_name = $amenity->facility_name;
          $tmp_city_name[$i]=strtolower($amenity->facility_name);
          if(!empty($translation))
          {
            $amenity->facility_name = $translation->translation;
            $tmp_city_name[$i]=strtolower($amenity->facility_name);
          }
          $tmp_city[$i] = $amenity;
		}

		/* array data sorted by Facilities pramod*/
		if(!empty($tmp_city_name)){
		sort($tmp_city_name);
		foreach($tmp_city_name as $i=>$val){

			foreach($tmp_city as $j=>$cities_original_data){
					if($val == strtolower($cities_original_data->facility_name)){
						$amenity = $cities_original_data;
					}
			}
		}
	}


      }
    }

    $data['property_list'] = array(
        "property_count" => 0,
        "hostel_list" => array(),
        "hostel_count" => 0,
        "guesthouse_list" => array(),
        "guesthouse_count" => 0,
        "hotel_list" => array(),
        "hotel_count" => 0,
        "apartment_list" => array(),
        "apartment_count" => 0,
        "campsite_list" => array(),
        "campsite_count" => 0,);

    if(($data['searchmode'] == 0)||($include_availdata == TRUE))
    {
      if($results === false)
      {
          //API unreachable

          //message not used yet
  //        $data['api_error_msg'] = "";
        $data['warning']         = true;
        $data['warning_message'] = _("Data unavailable. Please try again.");
      }
      elseif(isset($results["ERROR"]))
      {
        $data['warning']         = true;
        $data['warning_message'] = $results["ERROR"];
      }
      else
      {
        $this->CI->load->helper('array');

        if($data['searchmode'] == 1)
        {
          array_change_all_key_case($results,CASE_LOWER, true);
          $this->CI->Hb_api_translate->translate_LocationAvailability($results);
          $data['property_list'] = $this->CI->Db_hb_hostel->appendAdditionalPropertyData($results["response"]);
          $data['property_list'] = $this->properties_avail_prepare($data['property_list']);

          log_message("debug", "search mode = 1: " . print_r($data["property_list"], true));
          
          foreach($data['property_list'] as $property_id => $property)
          {
            $data['property_list'][$property_id]["property_page_url"] = $this->CI->Db_links->build_property_page_link(
                $property["type"],$property["name"],$property["id"],$this->CI->site_lang);
            $data['amenities'][(int)$property["id"]] = $this->CI->Db_hb_hostel->get_hostel_facilities($property["id"]);
            $data['amenities_filter'][(int)$property["id"]] = $this->CI->Db_hb_hostel->get_hostel_facilities_for_filter($property["id"]);
            $data['districts'][(int)$property["id"]] = $this->CI->Db_hb_hostel->get_property_districts_for_filter($property["id"]);

            if (!empty($data['districts'][(int)$property["id"]]))
            {
              foreach ($data['districts'][(int)$property["id"]] as $i => $district)
                  {
                  $translation = $this->CI->db_translation_cache->get_translation($district->district_name, $this->CI->site_lang);

                  if (!empty($translation))
                    {
                     $data['districts'][(int)$property["id"]][$i]->district_name = $translation->translation;
                    }
                    else
                    {
                          $data['districts'][(int)$property["id"]][$i]->district_name = $district->district_name;

                    }
                    $data['districts'][(int)$property["id"]][$i]->original_name = $district->district_name;
                  }
              }

               // Second parameter is a range in KM
            $data['landmarks'][(int)$property["id"]] = $this->CI->Db_hb_hostel->get_property_landmarks_for_filter($property["id"], 2);
          }
          
          log_message("debug", "search mode = 1 - post loop: " . print_r($data["property_list"], true));
        }
        else
        {

          if($data_from_live_api == true)
          {
            array_change_all_key_case($results,CASE_LOWER, true);
            $this->CI->Hb_api_translate->translate_LocationData($results);
            $data['property_list'] = $this->CI->Db_hb_hostel->appendAdditionalPropertyData($results["response"]["properties"]);
          }
          else
          {
            $this->CI->Hb_api_translate->translate_LocationData($results);
            $data['property_list'] = $results["response"]["properties"];
          }

          //Add property reviews if not search mode
          if(($data['searchmode'] == 0) && ($prop_reviews === TRUE))
          {
            //TODO To improve reusage of library, properties reviews functions should be move out of hw engine eventually
            $this->CI->load->library('hw_engine');
            $this->CI->load->model('Db_hb_hostel');
            foreach($data['property_list'] as $hostel)
            {
              $data['user_reviews'][(int)$hostel['id']] = $this->CI->hw_engine->property_reviews((int)$hostel['id'], TRUE, 5, FALSE, FALSE);

              //Main services
              $data['main_services'][(int)$hostel['id']] = $this->CI->Db_hb_hostel->get_hostel_main_services((int)$hostel['id']);
              if(!empty($data['main_services'][(int)$hostel['id']]))
              {
                foreach($data['main_services'][(int)$hostel['id']] as $si => $service)
                {
                  if($service->service_type != 'security_rating')
                  {
                    $translation = $this->CI->db_translation_cache->get_translation($service->description,$this->CI->site_lang);
                    if(!empty($translation))
                    {
                      $data['main_services'][(int)$hostel['id']][$si]->description = $translation->translation;
                    }
                  }
                }
              }

            }
            $this->CI->Hb_api_translate->translate_location_reviews($data["user_reviews"]);
          }
          $data['property_list'] = $this->properties_avail_prepare($data['property_list']);
          $data['property_list'] = $this->properties_sort_by_price($data['property_list']);

          //keep one big list instead of splitting list in property types
//           $data['property_list'] = $this->properties_filter_by_prop_type($data['property_list']);
          $data['property_list'] = array("property_count" => count($data['property_list']), "hostel_list" => $data['property_list']);
        }
      }
    }
    
    log_message("debug", "post ifs: " . print_r($data["property_list"], true));

//    debug_dump($data['property_list']);

    $userdata = array(
                 'country_selected'  => $country_select,
                 'city_selected'     => $city_select,
                 'date_selected'     => $dateStart,
                 'numnights_selected'     => $numNights
             );

    $this->CI->session->set_userdata($userdata);
    //Sets cookies so we could access this from wordpress environment
    $cookie = array('name'   => 'country_selected',
                    'value'  => $country_select,
                    'expire' => $this->CI->config->item('sess_expiration'));
    set_cookie($cookie);
    $cookie = array('name'   => 'city_selected',
                    'value'  => $city_select,
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

    $data['country_selected']   = $country_select;
    $data['city_selected']      = $city_select;
    $data['date_selected']      = $dateStart;
    $data['numnights_selected'] = $numNights;

    //Breadcrumb variables
    $data['bc_continent']  = $city->display_continent;
    $data['bc_country']    = $country_select;
    $data['bc_city']       = $city_select;

    //Title
    $data['title'] = sprintf(gettext("Hostels in %s - %s"),$city_select,$this->CI->config->item('site_title'));

    $data['google_map_enable'] = true;
    $data['google_map_hostel_list'] = true;

    $data['user_id'] = $this->user_id;
    
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
    $json_data["property_list"] = $data["property_list"];

    $json_data["request"] = array(
        'date_selected'      => $data["date_selected"],
        'numnights_selected' => $data["numnights_selected"],
        'display_currency' => $this->CI->site_currency
    );

    $json_data["city_info"] = $data["city_info"];
    $json_data['api_error_msg'] = false;
    if(!empty($data['warning_message']))
    {
      //       $json_data['api_error_msg'] = $data['api_error_msg'];
      $json_data['api_error_msg'] = true;
      $json_data["property_list"] = array();
    }
//     debug_dump($data);exit;
    //Date array
    $avail_dates = array();
    $dateStart = new DateTime($data["date_selected"]);

    for($ni = 0;$ni < $data["numnights_selected"];$ni++)
    {
      $avail_dates[] = $dateStart->format('Y-m-d');
      $dateStart->modify("+1 day");
    }

    //     debug_dump($data);
    //     debug_dump($json_data);
    //     exit;
    //TODO manage API error!

    $deal_property = array(0 => null,
                           1 => null);

    foreach($json_data["property_list"] as $i => $prop)
    {
      //Change keys to match HW data
      $json_data["property_list"][$i]['savedToFavorites']   = empty($prop["savedToFavorites"]) ? false : true;
      $json_data["property_list"][$i]['saveToFavorites']    = empty($prop["savedToFavorites"]) ? true : false;
      $json_data["property_list"][$i]['propertyNumber']     = $prop["id"];
      $json_data["property_list"][$i]['propertyName']       = $prop["name"];
      $json_data["property_list"][$i]['shortDescription']   = $prop["shortdescription"];
      $json_data["property_list"][$i]['propertyType']       = ucfirst($prop["type"]);
      $json_data["property_list"][$i]['minNights']          = $prop["minlengthofstay"];
      $json_data["property_list"][$i]["Geo"]["Latitude"]    = null;
      $json_data["property_list"][$i]["Geo"]["Longitude"]   = null;
	  $json_data["property_list"][$i]["city_name"]   = $data["city_info"]->city_lname_en; // set the city name

	 // -------Translate the propertyType----------------------------------//
	   $this->CI->load->model('Db_term_translate');
	  $json_data["property_list"][$i]['propertyTypeTranslate'] = $this->CI->Db_term_translate->get_term_translation($json_data["property_list"][$i]["propertyType"],$this->CI->site_lang);
	 //  $json_data["property_list"][$i]['propertyTypeTranslate']       = $propertyType;

	   $json_data["property_list"][$i]['propertyType']       = $json_data["property_list"][$i]["propertyType"];

	 // get address for each propety from the hostel table
	  $this->CI->load->model('Db_hb_hostel');
	  $json_data["property_list"][$i]["address1"] = $this->CI->Db_hb_hostel->get_property_address($prop["id"]);

      if(isset($prop["geo_latitude"]))
      {
        $json_data["property_list"][$i]["Geo"]["Latitude"]    = $prop["geo_latitude"];
      }
      if(isset($prop["geo_longitude"]))
      {
        $json_data["property_list"][$i]["Geo"]["Longitude"]   = $prop["geo_longitude"];
      }

      if(isset($prop["ratings"]))
      {
	      $json_data["property_list"][$i]["Ratings"] = $prop["ratings"];
              $json_data["property_list"][$i]["isRatingsEmpty"] = $this->isRatingsEmpty($prop["ratings"]);
      }
      $json_data["property_list"][$i]["PropertyImages"]["PropertyImage"]["imageListURL"]   = $prop["image_list"];
      $json_data["property_list"][$i]["PropertyImages"]["PropertyImage"]["imageURL"]   = $prop["image"];
      $json_data["property_list"][$i]["PropertyImages"]["PropertyImage"]["imageThumbnailURL"]   = $prop["image_thumbnail"];

      unset($json_data["property_list"][$i]["id"]);
      unset($json_data["property_list"][$i]["name"]);
      unset($json_data["property_list"][$i]["shortdescription"]);
      unset($json_data["property_list"][$i]["type"]);
      unset($json_data["property_list"][$i]["minlengthofstay"]);
      unset($json_data["property_list"][$i]["geo_latitude"]);
      unset($json_data["property_list"][$i]["geo_longitude"]);
      unset($json_data["property_list"][$i]["image_thumbnail"]);

      $json_data["property_list"][$i]['amenities']        = $data['amenities'][$json_data["property_list"][$i]["propertyNumber"]];
      $json_data["property_list"][$i]['amenities_filter'] = $data['amenities_filter'][$json_data["property_list"][$i]["propertyNumber"]];
      $j = 0 ;

	  foreach($json_data["property_list"][$i]['amenities'] as $a => $amenity)
      {
        if(($amenity->description == 'Breakfast Included')|| ($amenity->description == 'Breakfast'))
        {
          $json_data["property_list"][$i]['amenities'][$a]->slug = "free-breakfast";
        }
        else
        {
          $json_data["property_list"][$i]['amenities'][$a]->slug = "";
        }

        if($amenity->type == 'extra')
        {
          $json_data["property_list"][$i]["extras"]['extra'][$j] = $amenity->description;
          $j++;
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

      $json_data["property_list"][$i]['districts'] = $data['districts'][$json_data["property_list"][$i]["propertyNumber"]];
      $json_data["property_list"][$i]["landmarks"] = $data['landmarks'][$json_data["property_list"][$i]["propertyNumber"]];

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
      //safety tag
      $json_data["property_list"][$i]['safety80'] = false;
      $db_hostel = $this->CI->Db_hb_hostel->get_hostel($json_data["property_list"][$i]['propertyNumber']);
      if(!empty($db_hostel) &&
         !empty($db_hostel->rating_safety) &&
         $db_hostel->rating_safety >= 80)
      {
        $json_data["property_list"][$i]['safety80'] = true;
      }
      unset($db_hostel);

      if(!empty($json_data["property_list"][$i]["shortdescriptionTranslated"]))
      {
        $json_data["property_list"][$i]["shortDescription"] = strip_tags(word_limiter($json_data["property_list"][$i]["shortdescriptionTranslated"], 30,"..."));
        unset($json_data["property_list"][$i]["shortdescriptionTranslated"]);
      }
      else
      {
        $json_data["property_list"][$i]["shortDescription"] = strip_tags(word_limiter($json_data["property_list"][$i]["shortDescription"], 30,"..."));
      }

      $this->setJsonLocationRatingData($json_data, $i);

      $json_data["property_list"][$i]["isMinNightNeeded"] = false;
      if(isset($json_data["property_list"][$i]["minNights"]))
      {
        settype($json_data["property_list"][$i]["minNights"],"integer");

        if($json_data["property_list"][$i]["minNights"]>1)
        {
          $json_data["property_list"][$i]["isMinNightNeeded"] = true;
          $json_data["property_list"][$i]["minNightsMessage"] = sprintf(ngettext("This property requires a minimum stay of %d night", "This property requires a minimum stay of %d nights", $json_data["property_list"][$i]["minNights"]), $json_data["property_list"][$i]["minNights"]);

        }
      }
      else
      {
        $json_data["property_list"][$i]["minNights"] = 0;
        settype($json_data["property_list"][$i]["minNights"],"integer");
      }

      $json_data["property_list"][$i]["dual_price"] = 1;
      $json_data["property_list"][$i]["display_price"] = floatval(
            $json_data["property_list"][$i]["prices"]["customer"]["minprice"]);
      $json_data["property_list"][$i]["display_shared_price"] = floatval(
            $json_data["property_list"][$i]["prices"]["customer"]["minsharedprice"]);
      $json_data["property_list"][$i]["display_private_price"] = floatval(
            $json_data["property_list"][$i]["prices"]["customer"]["minprivateprice"]);

      $json_data["property_list"][$i]["currency_code"] = $json_data["property_list"][$i]["prices"]["customer"]["currency"];
      $json_data["property_list"][$i]["display_currency"] = currency_symbol($json_data["property_list"][$i]["prices"]["customer"]["currency"]);
      settype($json_data["property_list"][$i]["display_price"],"float");
      $json_data["property_list"][$i]["display_price_formatted"]        = number_format($json_data["property_list"][$i]["display_price"], 2, '.', '');
      $json_data["property_list"][$i]["display_shared_price_formatted"] = number_format($json_data["property_list"][$i]["display_shared_price"], 2, '.', '');
      $json_data["property_list"][$i]["display_private_formatted"]      = number_format($json_data["property_list"][$i]["display_private_price"], 2, '.', '');

      if($json_data["property_list"][$i]["display_shared_price"] == 0)
      {
        $json_data["property_list"][$i]["display_shared_price_formatted"] = "";
      }
      if($json_data["property_list"][$i]["display_private_price"] == 0)
      {
        $json_data["property_list"][$i]["display_private_formatted"] = "";
      }
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
      if(($json_data["property_list"][$i]["Geo"]["Latitude"] != 0)&&($json_data["property_list"][$i]["Geo"]["Longitude"] != 0))
      {
        $json_data["property_list"][$i]["isGeoValid"] = true;
      }

      $json_data["property_list"][$i]["AvailableDates"]["availableDate"] = $avail_dates;

      //date format
      foreach($json_data["property_list"][$i]["AvailableDates"]["availableDate"] as $d => $avail_date)
      {
        if(!empty($wp_date_format))
        {
          $json_data["property_list"][$i]["AvailableDates"]["availableDate"][$d] = date_conv($avail_date, $wp_date_format);
        }
      }

      //Unset useless data to keep JSON object as small as possible
      unset($json_data["property_list"][$i]["prices"]);
      if(!empty($json_data["property_list"][$i]["shortdescriptionTranslatedError"]))
      {
        unset($json_data["property_list"][$i]["shortdescriptionTranslatedError"]);
      }
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

    $data["json_data"] = json_encode($json_data);
    return $data;
  }
  
  private function isRatingsEmpty($propertyRatings) {     
      foreach ($propertyRatings as $rating) {
          if ($rating > 0) return false;
      }
      
      return true;
  }

    private function setJsonLocationRatingData(&$json_data, $i) {
        $json_data["property_list"][$i]["overall_rating"] = $json_data["property_list"][$i]["rating"];
        settype($json_data["property_list"][$i]["overall_rating"],"integer");
        $json_data["property_list"][$i]["overall_rating"] = sprintf($json_data["property_list"][$i]["overall_rating"]);
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
    }

  function property_images($property_number)
  {
    $this->CI->load->model('Db_hb_hostel');
    $pics = $this->CI->Db_hb_hostel->get_hostel_images($property_number);

    $data['thumbnails'] = array();
    $data['main_pics'] = array();

    foreach($pics as $image)
    {
      $img_url = (string) $image->url;
      $data['main_pics'][] = $img_url;

      //adding s before extension returns thumbnail of image per HB server
      //No doc on this just happen to be this way could change....
      $data['thumbnails'][] = $this->CI->Hostelbookers_api->build_thumb_url($img_url);
    }
    return $data;
  }

  function property_info(&$data,$property_number)
  {
    //Force everything in english Because of HB inconsistent translated data specially Important Information field
    //
    $this->CI->Hb_api_translate->setLanguage($this->CI->site_lang,"en");
    $response = $this->CI->Hostelbookers_api->getPropertyDataByID($property_number, "en");

    $data['hostel'] = array();
    $data['hostel_comments'] = NULL;

    $data['api_error'] = false;
    $data['api_error_msg'] = false;

    if($response === false)
    {
      //API unreachable
      $data['warning']         = true;
      $data['warning_message'] = _("Data unavailable. Please try again.");
    }
    elseif(isset($response["ERROR"]) && (strcasecmp($response["ERROR"],"PropertyID is invalid")==0))
    {
      //API return error
      throw new Exception("api returned error");
    }
    elseif(isset($response["ERROR"]))
    {
      //API return error
      $data['api_error_msg'] = $response["ERROR"];

    }
    else
    {
      // Load a helper
	  $this->CI->load->helper('domain_replace');

	  $cache_time = $this->CI->wordpress->get_option("aj_cache_time_property_pages",0);
      if(!empty($cache_time))
      {
        $this->CI->output->cache($cache_time);
      }

      $this->CI->load->model('Db_hb_country');

      $this->CI->Hb_api_translate->translate_PropertyData($response["RESPONSE"]);

      $data['hostel'] = $response["RESPONSE"];


	 //String replace code start----------------------
     $data['hostel']['SHORTDESCRIPTION'] = domain_name_replace($data['hostel']['SHORTDESCRIPTION']);
     $data['hostel']['LONGDESCRIPTION'] = domain_name_replace($data['hostel']['LONGDESCRIPTION']);
	 $data['hostel']['LONGDESCRIPTION_translated'] = domain_name_replace(!empty($data['hostel']['LONGDESCRIPTION_translated']) ? $data['hostel']['LONGDESCRIPTION_translated'] : '');
     $data['hostel']['IMPORTANTINFORMATION'] = domain_name_replace($data['hostel']['IMPORTANTINFORMATION']);
	 $data['hostel']['IMPORTANTINFORMATION_translated'] = domain_name_replace(!empty($data['hostel']['IMPORTANTINFORMATION_translated']) ? $data['hostel']['IMPORTANTINFORMATION_translated'] : '');

     //String replace code close----------------------

	 $data['property_number'] = $response["RESPONSE"]["ID"];



      $data['property_number'] = $response["RESPONSE"]["ID"];


      $hostel_name = $response["RESPONSE"]["NAME"];

      //Name for meta description
      $data['property_name'] = $hostel_name;

      $hostel_country = $response["RESPONSE"]["ADDRESS"]["COUNTRY"];
      $hostel_city = $this->CI->Db_hb_country->get_city($response["RESPONSE"]["ADDRESS"]["COUNTRY"],$response["RESPONSE"]["ADDRESS"]["CITY"],$this->CI->site_lang);

      if(!is_null($hostel_city))
      {
        $hostel_country = $hostel_city->display_country;
        $hostel_city = $hostel_city->display_city;
      }

      //TODO Translate country and city
      $data['hostel']["ADDRESS"]["COUNTRY"] = $response["RESPONSE"]["ADDRESS"]["COUNTRY"];
      $data['hostel']["ADDRESS"]["CITY"]    = $response["RESPONSE"]["ADDRESS"]["CITY"];

      $data['availability_check'] = true;
      $data['google_map_enable']  = true;
      $data['google_map_address']  = $response["RESPONSE"]["ADDRESS"]["STREET1"].", ".$response["RESPONSE"]["ADDRESS"]["CITY"].", ".$response["RESPONSE"]["ADDRESS"]["COUNTRY"].", ".$response["RESPONSE"]["ADDRESS"]["ZIP"];

      if(($response["RESPONSE"]["GPS"]["LAT"] != 0) && ($response["RESPONSE"]["GPS"]["LON"] != 0))
      {
        $data['google_map_geo_latlng'] = str_replace(",",".",$response["RESPONSE"]["GPS"]["LAT"]) .", ". str_replace(",",".",$response["RESPONSE"]["GPS"]["LON"]);
      }

      $data['bc_continent']  = $this->CI->Db_hb_country->get_continent($response["RESPONSE"]["ADDRESS"]["COUNTRY"],$this->CI->site_lang);
      if(empty($data['bc_continent']))
      {
        log_message('error', 'This country is not in DB: '.$response["RESPONSE"]["ADDRESS"]["COUNTRY"]);
      }
      $data['bc_country']    = $hostel_country;
      $data['bc_city']       = $hostel_city;

      $this->CI->load->model('Db_reviews');
      $data['hostel_rating']   = $this->CI->Db_reviews->get_property_avg_rating($data['property_number']);

      //Separate extra and included extra
      $data['hostel']["PROPERTYEXTRAS_included"] = array();
      $data['hostel']["PROPERTYEXTRAS_purchasable"] = array();

      if(!empty($data['hostel']["PROPERTYEXTRAS"]))
      {
        foreach($data['hostel']["PROPERTYEXTRAS"] as $extra => $price)
        {
          if($price > 0)
          {
            $data['hostel']["PROPERTYEXTRAS_purchasable"] = array_merge($data['hostel']["PROPERTYEXTRAS_purchasable"],array($extra => $price));
          }
          else
          {
            $data['hostel']["PROPERTYEXTRAS_included"] = array_merge($data['hostel']["PROPERTYEXTRAS_included"],array($extra => $price));
          }
        }
      }

      if(!empty($data['hostel']["PROPERTYEXTRAS_translated"]))
      {
        //Separate extra and included extra
        $data['hostel']["PROPERTYEXTRAS_included_translated"] = array();
        $data['hostel']["PROPERTYEXTRAS_purchasable_translated"] = array();

        foreach($data['hostel']["PROPERTYEXTRAS_translated"] as $extra => $price)
        {
          if($price > 0)
          {
            $data['hostel']["PROPERTYEXTRAS_purchasable_translated"] = array_merge($data['hostel']["PROPERTYEXTRAS_purchasable_translated"],array($extra => $price));
          }
          else
          {
            $data['hostel']["PROPERTYEXTRAS_included_translated"] = array_merge($data['hostel']["PROPERTYEXTRAS_included_translated"],array($extra => $price));
          }
        }
      }

      //Ratings
      $this->CI->load->model('Db_hb_hostel');
      $data["hostel_db_data"] = $this->CI->Db_hb_hostel->get_hostel($data['property_number']);

      //Put ratings in array to optimize deign
      $data["property_ratings"] = array();
      if(!empty($data["hostel_db_data"]))
      {
        foreach($data["hostel_db_data"] as $key => $value)
        {
          if(substr($key,0,6) == "rating")
          {
            $data["property_ratings"][substr($key,7)] = $value;
          }
        }
      }

//      debug_dump($data['hostel_db_data']);
//      debug_dump($data['property_ratings']);
//      debug_dump($response);
    }

    //Country and city selected initialization
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

    //Site Currency initialization
    $data['currency'] = $this->CI->site_currency;;

    //Min price initialization
//    $this->load->model('Db_hw_hostel');
//
//    $hwhostel_data = $this->Db_hw_hostel->get_hostel_data_from_number($data['property_number'],$data['currency']);

    //Hostel minimum price
    $hostel_data = $this->CI->Db_hb_hostel->get_hostel_prices($data['property_number'],$data['currency']);

    //Main services
    $data['main_services'] = $this->CI->Db_hb_hostel->get_hostel_main_services($data['property_number']);
    if(!empty($data['main_services']))
    {
      foreach($data['main_services'] as $si => $service)
      {
        $translation = $this->CI->db_translation_cache->get_translation($service->description,$this->CI->site_lang);
        if(!empty($translation))
        {
          $data['main_services'][$si]->description = !empty($translation->translation) ? $translation->translation : '';
        }
      }
    }

    $data['hostel_min_price'] = NULL;
    if(!empty($hostel_data))
    {
      $data['hostel_min_price'] = floor($hostel_data->bed_price)." ".currency_symbol($data['currency']);
    }
    else
    {

      $hostel_data = $this->CI->Db_hb_hostel->get_hostel_prices($data['property_number'],"EUR");
      if(!empty($hostel_data))
      {
        $this->CI->load->model('Db_currency');
        $data['hostel_min_price'] = floor($this->CI->Db_currency->convert_from_hb_rates("EUR",$data['currency'],$hostel_data->bed_price))." ".currency_symbol($data['currency']);
      }
    }

    $data['title'] = sprintf(gettext("%s - %s"),$hostel_name,$this->CI->config->item('site_title'));

    $data['load_js_livevalidation'] = true;

    unset($response);

    return $data;
  }

  function property_avail_check($propertyName, $propertyNumber, $dateStart, $numNights, $currency)
  {
    $response = $this->CI->Hostelbookers_api->getPropertyPricingPerDate($propertyNumber,$dateStart->format('d-M-Y'),$numNights, $this->api_functions_lang, $currency);

    $data['propertyName']   = $propertyName;
    $data['propertyNumber'] = $propertyNumber;
    $data['dateStart']      = $dateStart;
    $data['numNights']      = $numNights;
    $data['currency']       = $currency;

    //Get main services and breakfast included
    $this->CI->load->model('Db_hb_hostel');
    $this->CI->load->model('i18n/db_translation_cache');
    $data['main_services'] = $this->CI->Db_hb_hostel->get_hostel_main_services($propertyNumber);
    $data['breakfast_included'] = 0;
    if(!empty($data['main_services']))
    {
      foreach($data['main_services'] as $si => $service)
      {
        if($service->service_type == 'breakfast')
        {
          $data['breakfast_included'] = 1;
        }
        $translation = $this->CI->db_translation_cache->get_translation($service->description,$this->CI->site_lang);
        if(!empty($translation) && !empty($translation->translation))
        {
          $data['main_services'][$si]->description =
          		$translation->translation;
        }
      }
    }

    $data["error_msg"] = FALSE;

    if($response === false)
    {
      //API unreachable
      $data["error_msg"] = _('Serveur inaccessible en ce moment.');
    }
    elseif(isset($response["ERROR"]))
    {
      //API return error
      $data["error_msg"] = $this->Hb_api_translate->translate_text($response["ERROR"],"HB avail error msg","en");
    }
    elseif(empty($response["RESPONSE"]))
    {
      //API return no availability
      $data["error_msg"] = _('No rooms available for the selected nights.');
    }
    else
    {
      //API return OK
      $data['booking_rooms'] = $this->CI->Hb_api_translate->translate_PropertyAvailability($response["RESPONSE"]);
    }

    return $data;
  }

  function prepare_rooms($hb_rooms , $numNights, $onlyFullNights = TRUE, $maxPersons = 20)
  {

    $sharedRoomsAvailable = 0;
    $sharedRooms = array();

    $privateRoomsAvailable = 0;
    $privateRooms = array();

    foreach ($hb_rooms as $hostel_room)
    {
      $availableBeds = $hostel_room["BEDS"];
      $availableRooms = 1;

      $num_nights_available_of_room = 0;
      $room_all_nights_price = 0;

      $availdatesData = array();
      //sort by the date which is the key
      ksort($hostel_room['NIGHTS']);
      foreach($hostel_room['NIGHTS'] as $dateint => $room_night)
      {
//          echo $dateint. " VS ".time() ." - ". date("d-M-Y",mktime(0,0,0,1,$dateint-1,1900)). "<br>";
        $dateint = date("Y-m-d",$this->hb_date_int_convert($dateint));
        $num_nights_available_of_room++;

        //Price calculation for entire trip in that room
        $room_all_nights_price += (double)$room_night["CUSTOMER"]["MINPRICE"];

        if($hostel_room["BLOCKBEDS"]>0)
        {
          $availdatesData[(string)$dateint]["price"] = number_format(round((float)$room_night["CUSTOMER"]["MINPRICE"]*$hostel_room["BLOCKBEDS"],2),2);
        }
        else
        {
          //Available date price table
          $availdatesData[(string)$dateint]["price"] = number_format(round((float)$room_night["CUSTOMER"]["MINPRICE"],2),2);
        }
        $availdatesData[(string)$dateint]["availableBeds"]  = $availableBeds;
        //Not available for HB so 1 for now
        $availdatesData[(string)$dateint]["availableRooms"] = $availableRooms;
      }

      //Check for dorms only
      if($hostel_room["BLOCKBEDS"]==0)
      {
        //IF all night are available
        if(($onlyFullNights===FALSE) || ($num_nights_available_of_room >= $numNights))
        {
          $sharedRoomsAvailable++;
          $sharedRoom = array();
          $sharedRoom["roomTypeCode"]          = (string)$hostel_room['ID'];
          $sharedRoom["max_guest_per_unity"]   = "";
          $sharedRoom["description"]           = (string)$hostel_room['NAME'];
          $sharedRoom["descriptionTranslated"] = "";
          if(!empty($hostel_room['NAME_TRANSLATED']))
          {
            $sharedRoom["descriptionTranslated"] = (string)$hostel_room['NAME_TRANSLATED'];
          }
          $sharedRoom["total_price"]           = number_format((double)$room_all_nights_price,2,'.','');
          $sharedRoom["currency"]              = currency_symbol($room_night["CUSTOMER"]["CURRENCY"]);
          $sharedRoom["availableBeds"]         = (int)$availableBeds;
          $sharedRoom["availableRooms"]        = (int)$availableRooms;
          $sharedRoom["availableDates"]        = $availdatesData;
          array_push($sharedRooms, $sharedRoom);
        }
      }
      //Show private rooms with beds increment lower than maxpax because if it is higher it will be a group booking and will cause problem on booking
      elseif($maxPersons > $hostel_room["BLOCKBEDS"])
      {
        //IF all night are available
        if(($onlyFullNights===FALSE) || ($num_nights_available_of_room >= $numNights))
        {
          //Room price
          $room_all_nights_price = $room_all_nights_price * (int)$hostel_room["BLOCKBEDS"];

          $privateRoomsAvailable++;
          $privateRoom = array();
          $privateRoom["roomTypeCode"]          = (string)$hostel_room['ID'];
          $privateRoom["max_guest_per_unity"]   = (int)$hostel_room['BLOCKBEDS'];
          $privateRoom["description"]           = (string)$hostel_room['NAME'];
          $privateRoom["descriptionTranslated"] = "";
          if(!empty($hostel_room['NAME_TRANSLATED']))
          {
            $privateRoom["descriptionTranslated"] = (string)$hostel_room['NAME_TRANSLATED'];
          }
          $privateRoom["total_price"]           = number_format((double)$room_all_nights_price,2,'.','');
          $privateRoom["currency"]              = currency_symbol($room_night["CUSTOMER"]["CURRENCY"]);
          $privateRoom["availableBeds"]         = (int)$availableBeds;
          $privateRoom["availableRooms"]        = (int)$availableRooms;
          $privateRoom["availableDates"]        = $availdatesData;
          $privateRoom["bedsPerRoom"]           = (int)$hostel_room["BLOCKBEDS"];
          array_push($privateRooms, $privateRoom);

        }
      }
    }
    return array("sharedRooms" => $sharedRooms, "privateRooms" => $privateRooms);
  }

  function properties_sort_by_price($property_array)
  {
    function cmpprice($a, $b)
    {
      $priceA = str_replace(",",".",$a["prices"]["customer"]["minprice"]);
      $priceB = str_replace(",",".",$b["prices"]["customer"]["minprice"]);
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

  function properties_avail_prepare(&$property_array)
  {
    $this->CI->load->model("Db_hb_hostel");

    foreach($property_array as $i => $property)
    {

      //add property type
      if(empty($property_array[$i]["type"]))
      {
        $property_array[$i]["type"] = $this->CI->Db_hb_hostel->get_property_type($property["id"]);
      }

      //add minprice
      if(empty($property_array[$i]["prices"]["customer"]["minprice"]))
      {
        $property_array[$i]["prices"]["customer"]["minprice"] = NULL;
        if(!empty($property["prices"]["customer"]["minsharedprice"]) &&
           ($property["prices"]["customer"]["minsharedprice"] > 0))
        {
          //If t dorms are available (min shared rooms price > 0)
          $property_array[$i]["prices"]["customer"]["minprice"] = $property["prices"]["customer"]["minsharedprice"];
          //If private rooms are available  (price > 0) and private rooms prices are lower
          if(!empty($property["prices"]["customer"]["minprivateprice"]) &&
             ($property["prices"]["customer"]["minprivateprice"] > 0) &&
             ($property_array[$i]["prices"]["customer"]["minprice"] > $property["prices"]["customer"]["minprivateprice"]))
          {
            $property_array[$i]["prices"]["customer"]["minprice"] = $property["prices"]["customer"]["minprivateprice"];
          }
        }
        elseif(!empty($property["prices"]["customer"]["minprivateprice"]) &&
               ($property["prices"]["customer"]["minprivateprice"] > 0))
        {
          $property_array[$i]["prices"]["customer"]["minprice"] = $property["prices"]["customer"]["minprivateprice"];
        }
      }

      //add thumbnail url
      if(empty($property_array[$i]["image_thumbnail"]))
      {
        if(!empty($property["image"]))
        {
          $property_array[$i]["image_thumbnail"] = $this->CI->Hostelbookers_api->build_thumb_url($property["image"]);
        }
        else
        {
          $property_array[$i]["image_thumbnail"] = site_url('images/na_small.jpg');
        }
      }

      //add list url
      if(!empty($property["image"]))
      {
          $property_array[$i]["image_list"] = $this->CI->Hostelbookers_api->build_list_url($property["image"]);
      }

      //standardize rating field
      if(empty($property_array[$i]["rating"]) && !empty($property_array[$i]["percentagerating"]))
      {
        $property_array[$i]["rating"] = round($property_array[$i]["percentagerating"]);
      }

    }
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
      if(strcasecmp($property["type"], "hostel")==0)
      {
        array_push($hostels,$property);
      }
      elseif(strcasecmp($property["type"], "guesthouse")==0)
      {
        array_push($guesthouse,$property);
      }
      elseif(strcasecmp($property["type"], "hotel")==0)
      {
        array_push($hotel,$property);
      }
      elseif(strcasecmp($property["type"], "apartment")==0)
      {
        array_push($apartment,$property);
      }
      elseif(strcasecmp($property["type"], "campsites")==0)
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
  
   function propertyimg($prid)
  {
  	
  		$this->CI->load->model('Hostelbookers_api');
  		$results = $this->CI->Hostelbookers_api->getPropertyDataByID($prid);
		return $results;
  }
}
