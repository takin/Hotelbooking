<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CHostel extends I18n_site
{

	var $api_lang  = "en";
	var $api_functions_lang = "English";

	var $user_id = 0;

	var $currency_from = "EUR";

	var $api_view_dir = "hw/";

	var $transaction_log_filename = "error_trans";

  function CHostel()
  {

    parent::I18n_site();

    $this->load->helper(array('text','misc_tools','string','cookie'));

    $this->load->library('email');
    $this->load->library('tank_auth');
    $this->load->library('form_validation');

    $this->load->model('Hostel_api_model');
    $this->load->model('Hw_api_translate');
    $this->load->model('Db_model');
    $this->load->model('Db_country');
    $this->load->model('Db_currency');

    $this->lang->load('tank_auth','multi');

//    $this->output->enable_profiler(TRUE);
    $this->api_functions_lang = $this->Hostel_api_model->lang_code_convert($this->site_lang);

    //If site is english, set from lang to english
    // else auto-detect because api langage return is not certain
    if(strcmp($this->api_functions_lang,"English")==0)
    {
      $this->Hw_api_translate->setLanguage($this->site_lang,"en");
    }
    else
    {
      $this->Hw_api_translate->setLanguage($this->site_lang);
    }

    if($this->tank_auth->is_logged_in())
    {
      $this->user_id = $this->tank_auth->get_user_id();
    }

//    $this->_adwordsInit();

    $this->load->library('get_config');

    $currency_validated = $this->input->get("currency", TRUE);
    if(!empty($currency_validated))
    {
      $currency_validated = $this->Db_currency->validate_currency($currency_validated);
    }
    elseif($this->tank_auth->is_logged_in())
    {
      $currency_validated = $this->config->item('site_currency_default');

      $user_id = $this->tank_auth->get_user_id();

      if($user_id !== false)
      {
        $user_info = array();
        $user_info = $this->Db_model->get_user_profile($user_id);
        $currency_validated = $this->Db_currency->get_currency_code($user_info['favorite_currency']);
      }
    }
    elseif(!empty($this->site_user->CurrencyCode))
    {
      $currency_validated = $this->Db_currency->validate_currency($this->site_user->CurrencyCode);
    }
    else
    {
      $currency_validated = $this->config->item('site_currency_default');
    }
    $this->get_config->set_config_from_get("currency","site_currency_selected",TRUE,"currency_selected",$currency_validated);

  }

  /*
   * obsolete
   *
   * Now refer to cmain index
   */
  function index()
  {
    exit;
    $cache_time = $this->wordpress->get_option("aj_cache_time_ci_home",0);
    if(!empty($cache_time))
    {
      $this->output->cache($cache_time);
    }

    $this->_searchBoxInit($data);

    $data['title'] = $this->config->item('site_title');
    $data['load_jslib_jquery'] = true;

    $data['user_id'] = $this->user_id;

    $this->_set_tr_continent_data($data);

    $data['google_map_enable']    = true;
    $data['google_map_country_list'] = true;

    $this->load->model('Db_hw_city');

    $data['country_list'] = $this->Db_hw_city->cityCountryList_DB(NULL,NULL,$this->site_lang);

    $data['eu_country_list'] = $this->Db_hw_city->cityCountryList_DB("europe",NULL,$this->site_lang);

    $data['na_country_list'] = $this->Db_hw_city->cityCountryList_DB("north america",NULL,$this->site_lang);
    $data['sa_country_list'] = $this->Db_hw_city->cityCountryList_DB("south america",NULL,$this->site_lang);
    $data['as_country_list'] = $this->Db_hw_city->cityCountryList_DB("asia",NULL,$this->site_lang);
    $data['oc_country_list'] = $this->Db_hw_city->cityCountryList_DB("oceania",NULL,$this->site_lang);
    $data['af_country_list'] = $this->Db_hw_city->cityCountryList_DB("africa",NULL,$this->site_lang);

    $data['current_view_dir'] = "";
    $data['current_view'] = "home_view";
    $this->load->view('includes/template',$data);
  }

  function error404()
  {
    $this->_searchBoxInit($data);
    $data['title'] = $this->config->item('site_title');

    $data['user_id'] = $this->user_id;

    header("HTTP/1.0 404 Not Found");

    $data['current_view_dir'] = "";
    $data['current_view'] = "error404";
    $this->load->view('includes/template',$data);
  }

  function map()
  {
    echo "deprecated";
    exit;

    if((strcasecmp($this->uri->segment(1),"hostel")==0)&&(strcasecmp($this->uri->segment(2),"map")==0))
    {
      redirect($this->uri->segment(3).'/'.$this->uri->segment(4), 'refresh');
    }

    $continent = $this->uri->segment(1,NULL);
    $country   = $this->uri->segment(2,NULL);

    $continent = customurldecode($continent);

    $this->_searchBoxInit($data);

    if(($country != NULL)&&($continent!=NULL))
    {
      $cache_time = $this->wordpress->get_option("aj_cache_time_country_pages",0);
      if(!empty($cache_time))
      {
        $this->output->cache($cache_time);
      }

      $this->load->model('Db_hw_city');

      $country   = my_mb_ucfirst(mb_strtolower(customurldecode($country),"UTF-8"));

      $xmlcities = $this->Db_hw_city->cityCountryList_DB(NULL,$country,$this->site_lang);

      $data['country_selected'] = $country;
      $data['bc_country']       = $country;
      $data['bc_continent']     = $xmlcities->Country->countryContinentTranslated;
      $data['continent_lang']   = $data['bc_continent'];

      $data['cities_of_country'] = $xmlcities;

      $data['title'] = sprintf(gettext("Hostels in %s - %s"),$country,$this->config->item('site_title'));

      $data['google_map_enable']    = true;
      $data['google_map_city_list'] = true;

      $data['current_view_dir'] = "";
      $data['current_view'] = "country_view";
      $this->load->view('includes/template',$data);

    }
    elseif(!is_null($continent))
    {
      $cache_time = $this->wordpress->get_option("aj_cache_time_country_pages",0);
      if(!empty($cache_time))
      {
        $this->output->cache($cache_time);
      }

      $this->load->model('Db_hw_city');

      $xmlcountries = $this->Db_hw_city->cityCountryList_DB($continent,NULL,$this->site_lang);

      $data['continent_selected_lang'] = $xmlcountries->Country[0]->countryContinentTranslated;

      $data['bc_continent']       = $data['continent_selected_lang'];

      $data['countries_of_continent'] = $xmlcountries;

      $data['title'] = sprintf(gettext("Hostels in %s - %s"),my_mb_ucfirst($continent),$this->config->item('site_title'));

      $data['google_map_enable']    = true;
      $data['google_map_country_list'] = true;
      $data['country_list'] = $xmlcountries;

      $data['current_view_dir'] = "";
      $data['current_view'] = "continent_view";
      $this->load->view('includes/template',$data);
    }
    else
    {
      $this->error404();
      return;
    }

  }

  //Ajax access only
  function citylist()
  {
    $this->output->enable_profiler(FALSE);

    $data['data_source_api'] = TRUE;

    $data['cityCountryList'] = $this->Hostel_api_model->cityCountryList($this->config->item('hostelworld_userID'));

    $data['api_error'] = $data['cityCountryList'][0];
    $data['api_error_msg'] = "";

    if($data['api_error'] == true)
    {
      $data['api_error_msg'] = $data['cityCountryList'][1][0];
    }
    else
    {
      $data['cityCountryList'] = $data['cityCountryList'][1][0];
    }

    $data['javascript_varname'] = $this->input->get("citiesVarName", TRUE);
    if($data['javascript_varname'] === false)
    {
      $data['javascript_varname'] = 'citiesVarName';
    }

    $this->load->view('hw/citylist_view',$data);
  }

//Ajax access only
  function citylistdb()
  {
    $this->output->enable_profiler(FALSE);

    $data['data_source_api'] = FALSE;

//    $data['cityCountryList'] = $this->Db_country->get_all_hw_cities_translated($this->site_lang);

    $data['javascript_varname'] = $this->input->get("citiesVarName", TRUE);
    if($data['javascript_varname'] === false)
    {
      $data['javascript_varname'] = 'citiesVarName';
    }

    $data['content_type'] = 'text/xml';

    $this->load->view('hw/citylist_view',$data);
  }

  /*
   * obosolete
   *
   * Now refer to hw engine librairy and cmain controller
   */
  function search()
  {
    exit;

    if((strcasecmp($this->uri->segment(1),"hostel")==0)&&(strcasecmp($this->uri->segment(2),"search")==0))
    {
      redirect($this->uri->segment(3).'/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6), 'refresh');
    }
    $country    = $this->uri->segment(1,NULL);
    $city       = $this->uri->segment(2,NULL);
    $dateStart  = $this->uri->segment(3,NULL);
    $numNights  = $this->uri->segment(4,NULL);

    if($country === NULL)
    {
      //Display home page
      $this->index();
    }
    elseif($city === NULL)
    {
      //Display home page
      $this->error404();
    }
    else
    {
      $country    = urldecode (customurldecode($country));
      $city       = urldecode (customurldecode($city));

      $country = $this->Db_country->get_country($country,$this->site_lang);
      $city    = $this->Db_country->get_city($country,$city,$this->site_lang);

      if(($dateStart !== NULL)&&($numNights===NULL))
      {
        //echo warning date and $numNights is disregard wrong format missing parameter.
        $dateStart = NULL;
        $numNights = NULL;
        $data['warning']         = true;
        $data['warning_message'] = _('Date et nombre de nuits invalide.  Valeurs ignorées lors de la recherche.');

      }

      $data['currency'] = $this->config->item('site_currency_selected');

      $dateToCheck = $dateStart;
      $numNightsToCheck = $numNights;
      $country_api = $this->Db_country->get_country($country,$this->api_lang);
      $city_api    = $this->Db_country->get_city($country,$city,$this->api_lang);

      $results = Array();

      if(($dateStart === NULL)&&($numNights === NULL))
      {

        $cache_time = $this->wordpress->get_option("aj_cache_time_city_landing_pages",0);
        if(!empty($cache_time))
        {
          $this->output->cache($cache_time);
        }

        $data['searchmode'] = 0;
        $dateToCheck = get_date_default(30);
        $numNightsToCheck = 4;

        $currencyConvNeeded = FALSE;

        $this->load->config('hw_cache',TRUE);

        if((strcasecmp($data['currency'],"EUR")!=0)&&
           (strcasecmp($data['currency'],"USD")!=0)&&
           (strcasecmp($data['currency'],"GBP")!=0)&&
           ((strcasecmp($this->config->item('hw_currency_country','hw_cache'),$country_api)!=0)||
           (strcasecmp($this->config->item('hw_currency_city','hw_cache'),$city_api)!=0)))
        {
          $currencyConvNeeded = TRUE;
        }
        $this->load->model('Db_hw_hostel');
        $results[0] = FALSE;
        $results[1] = array();

        //TONOTICE The case where currency city is in DB but have not been cached by location search cron job has not been tested
        //         as it should not happen but should work fine anyway

        if($currencyConvNeeded)
        {
          $results[1] = $this->Db_hw_hostel->propertyLocationSearch_DB($city_api,$country_api,$this->currency_from,$this->api_functions_lang);
        }
        else
        {
          $results[1] = $this->Db_hw_hostel->propertyLocationSearch_DB($city_api,$country_api,$data['currency'],$this->api_functions_lang);
        }

        if(empty($results[1]))
        {
          $results = $this->Hostel_api_model->propertyLocationSearch($this->config->item('hostelworld_userID'), $city_api, $country_api, $dateToCheck, $numNightsToCheck,$data['currency'],$this->api_functions_lang);
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
                $hostelprice->BedPrice->price = $this->Db_currency->convert_from_hw_rates($hostelprice->BedPrice->currency,$data['currency'],$hostelprice->BedPrice->price);
                $hostelprice->BedPrice->currency = $data['currency'];

              }
            }
          }
        }
      }
      else
      {
        $data['searchmode'] = 1;
        $results = $this->Hostel_api_model->propertyLocationSearch($this->config->item('hostelworld_userID'), $city_api, $country_api, $dateToCheck, $numNightsToCheck,$data['currency'],$this->api_functions_lang);

        $cache_time = $this->wordpress->get_option("aj_cache_time_city_avail_pages",0);
        if(!empty($cache_time))
        {
          $this->output->cache($cache_time);
        }
      }

//      debug_dump($results);
      $data['hostel_list'] = array();

      if($results[0]==true)
      {
        $data['api_error_msg'] = $results[1];
//        $data['warning']         = true;
//        $data['warning_message'] = $results[1];
      }
      else
      {
//        $this->load->model('Db_reviews');
        $this->load->model('Db_hw_rating');

        $data['hostel_list'] = $this->Hw_api_translate->translate_LocationSearch($results[1]);
        foreach($data['hostel_list'] as $hostel)
        {
          $hostel->overallHWRating = $this->Db_hw_rating->get_hw_rating((int)$hostel->propertyNumber);
        }
      }

      $userdata = array(
                   'country_selected'  => $country,
                   'city_selected'     => $city,
                   'date_selected'     => $dateStart,
                   'numnights_selected'     => $numNights
               );

      $this->session->set_userdata($userdata);
      //Sets cookies so we could access this from wordpress environment
      $cookie = array('name'   => 'country_selected',
                      'value'  => $country,
                      'expire' => $this->config->item('sess_expiration'));
      set_cookie($cookie);
      $cookie = array('name'   => 'city_selected',
                      'value'  => $city,
                      'expire' => $this->config->item('sess_expiration'));
      set_cookie($cookie);
      $cookie = array('name'   => 'date_selected',
                      'value'  => $dateStart,
                      'expire' => $this->config->item('sess_expiration'));
      set_cookie($cookie);
      $cookie = array('name'   => 'numnights_selected',
                      'value'  => $numNights,
                      'expire' => $this->config->item('sess_expiration'));
      set_cookie($cookie);

      $data['country_selected'] = $country;
      $data['city_selected'] = $city;
      $data['date_selected'] = $dateStart;
      $data['numnights_selected'] = $numNights;
//      $search_term = $this->input->cookie('search_input_terms',TRUE);
//
//      if($search_term!=false)
//      {
//        $data['search_term'] = urldecode($search_term);
//      }



      if(is_null($dateStart))
      {
        $data['date_default'] = get_date_default();
      }

      $data['bc_continent']  = $this->Db_country->get_continent_of_country($country,$this->site_lang);

      if(is_null($data['bc_continent']))
      {
        log_message('error', 'This country is not in DB: '.$country);
      }
      $data['bc_country']    = $country;
      $data['bc_city']       = $city;

      $data['title'] = sprintf(gettext("Hostels in %s - %s"),$city,$this->config->item('site_title'));

      $data['google_map_enable'] = true;
      $data['google_map_hostel_list'] = true;

      $data['user_id'] = $this->user_id;

      $data['current_view_dir'] = "";
      $data['current_view'] = "city_view";
      $this->load->view('includes/template',$data);
    }

  }

  function reviews_map($property_id, $max_review_count = 3)
  {
     redirect('/reviews_map/'.$property_id.'/'.$max_review_count, 'location', 301);
  }
  function reviews($property_id)
  {
     redirect('/property_reviews/'.$property_id, 'location', 301);
  }

  function hw_reviews($property_id)
  {
    redirect('/property_reviews/'.$property_id, 'location', 301);
  }

  function site_search($terms = NULL)
  {
    redirect('/', 'location', 307);
    exit;
    //THIS IS BAD for performance and does not support HB!
    $data['results'] = array();
    $data['search_term'] = $terms;
    if(!empty($terms))
    {
      $terms = customurldecode($terms);
      $data['search_term'] = $terms;
      $terms = utf8_strip_specials($terms);
      $data['search_term_stripped'] = $terms;
      $this->load->model('Db_hw_search');
      $data['results'] = $this->Db_hw_search->search_hw_data($terms, $this->site_lang, 150);
      if(!empty($data['results']))
      {
        $this->Hw_api_translate->translate_search_results($data['results']);
      }
    }
    $this->carabiner->load_group_assets('search_box_scripts');
    $this->carabiner->js('mobile/suggest.js');

    $data['current_view_dir'] = "";
    $data['current_view'] = "search_results";
    $this->load->view('includes/template',$data);

  }

  /*
   * Deprecared now refer to cmain property page
   */
  function hostel_info()
  {
    echo "deprecated";
    exit;
    // dispaly hostel info
    if((strcasecmp($this->uri->segment(1),"hostel")==0)&&(strcasecmp($this->uri->segment(2),"hostel_info")==0))
    {
      redirect('/info/'.$this->uri->segment(3).'/'.$this->uri->segment(4), 'refresh');
    }

    $this->load->model('Db_reviews');
    $this->load->model('Db_hw_rating');

    $form_sent = $this->input->post('comment-submit',true);
    if(!empty($form_sent))
    {
          //hotel comments
          $this->form_validation->set_rules('firstname', _('Prénom'), 'trim|required|min_length[2]|max_length[100]|xss_clean');
          $this->form_validation->set_rules('lastname', _('Nom'), 'trim|required|min_length[2]|max_length[100]|xss_clean');
          $this->form_validation->set_rules('email', _('Courriel'), 'trim|required|xss_clean|valid_email');
          $this->form_validation->set_rules('nationality', _('Nationalité'), 'trim|required|xss_clean');
          $this->form_validation->set_rules('comment', _('Commentaires'), 'trim|required|xss_clean');
          $this->form_validation->set_rules('star-rating', _('Évaluation'), 'trim|xss_clean');
          $this->form_validation->set_rules('month_comment', _('Mois'), 'trim|required|xss_clean');
          $this->form_validation->set_rules('year_comment', _('Année'), 'trim|required|xss_clean');

          if ($this->form_validation->run() == FALSE) {                // validation not ok

//            $data['warning']         = true;
//            $data['warning_message'] = _('Erreur');
          }
          else
          {
            $rating = NULL;
            if($this->input->post('star-rating')!=false)
            {
              $rating = $this->input->post('star-rating');
            }

            $commentID = $this->Db_reviews->add_property_review($this->input->post('email'),
                                                   $this->input->post('firstname'),
                                                   $this->input->post('lastname'),
                                                   $this->input->post('nationality'),
                                                   $this->input->post('property_number'),
                                                   $this->input->post('property_name'),
                                                   $this->input->post('property_city'),
                                                   $this->input->post('property_country'),
                                                   $this->input->post('property_type'),
                                                   $this->input->post('comment'),
                                                   $rating,
                                                   $this->input->post('year_comment')."-".$this->input->post('month_comment')."-01",
                                                   $_SERVER['REMOTE_ADDR'],
                                                   $_SERVER['HTTP_USER_AGENT']
                                                   );

            //Send new review notice to admin
            if($this->config->item('admin_review_conf') > 0)
            {
              $emaildata["commentID"] = $commentID;
              $emaildata["email"]     = $this->input->post('email');
              $emaildata["firstname"] = $this->input->post('firstname');
              $emaildata["lastname"]  = $this->input->post('lastname');
              $emaildata["nationality"]  = $this->input->post('nationality');
              $emaildata["property_number"] = $this->input->post('property_number');
              $emaildata["property_name"]   = $this->input->post('property_name');
              $emaildata["property_city"]   = $this->input->post('property_city');
              $emaildata["property_country"] = $this->input->post('property_country');
              $emaildata["property_type"]   = $this->input->post('property_type');
              $emaildata["comment"]   = $this->input->post('comment');
              $emaildata["rating"]    = $rating;
              $emaildata["author_ip"] = $_SERVER['REMOTE_ADDR'];

              $emailcontent = $this->load->view('email/new_review_admin', $emaildata, true);
              $this->email->from($this->config->item('admin_review_conf_email'),$this->config->item('site_name'));
              $this->email->to($this->config->item('admin_review_conf_email'));
              $this->email->subject("[".$this->config->item('site_name')."] ".sprintf(gettext("Demande de modération pour la propriété %s"),$emaildata["property_name"]));
              $this->email->message($emailcontent);
              $this->email->send();
              unset($emaildata);
            }
            //Clear form data now that it is stored in DB to prevent double post
            unset($_POST['email']);
            unset($_POST['name']);
            unset($_POST['nationality']);
            unset($_POST['property_number']);
            unset($_POST['property_name']);
            unset($_POST['property_city']);
            unset($_POST['property_country']);
            unset($_POST['property_type']);
            unset($_POST['comment']);
            unset($_POST['month_comment']);
            unset($_POST['year_comment']);
            unset($_POST['star-rating']);
            unset($_POST['comment-submit']);
            $this->form_validation->_field_data = array();

            $data['warning']         = true;
            $data['warning_message'] = _('Votre commentaire a été envoyé à notre équipe pour approbation. Merci!');
          }
    }

    $this->_searchBoxInit($data);

    $hostel_name = $this->uri->segment(2,"");
    $hostel_number = $this->uri->segment(3,NULL);
    $hostel_city = "";

    //If property number not set return 404 error
    if(empty($hostel_number))
    {
      $this->error404();
      return;
    }

    $api = $this->Hostel_api_model->PropertyInformation($this->config->item('hostelworld_userID'), $hostel_number, $this->api_functions_lang);

    $data['hostel_data'] = array();
    $data['hostel_comments'] = NULL;

    $data['api_error'] = $api[0];
    $data['api_error_msg'] = false;

    if($api[0]==true)
    {
      $this->error404();
      return;
      //API return error
//      $data['api_error_msg'] = $this->Hw_api_translate->translate_APIError($api[1][0]);
//      if(isset($data['api_error_msg']->Error))
//      {
//         $data['api_error_msg'] = $data['api_error_msg']->Error;
//      }elseif(isset($data['api_error_msg']->UserMessage))
//      {
//        $data['api_error_msg'] = $data['api_error_msg']->UserMessage;
//      }
    }
    else
    {
      $cache_time = $this->wordpress->get_option("aj_cache_time_property_pages",0);
      if(!empty($cache_time))
      {
        $this->output->cache($cache_time);
      }

      $data['hostel_data'] = $this->Hw_api_translate->translate_PropertyInformation($api[1][0]);
      $data['property_number'] = $data['hostel_data']->propertyNumber;

      $hostel_name = $data['hostel_data']->propertyName;
      $hostel_city = $this->Db_country->get_city($data['hostel_data']->country,$data['hostel_data']->city,$this->site_lang);

      $data['availability_check'] = true;
      $data['google_map_enable']  = true;
      $data['google_map_address']  = $data['hostel_data']->address1.", ".$data['hostel_data']->city.", ".$data['hostel_data']->country.", ".$data['hostel_data']->postCode;

      if(($data['hostel_data']->Geo->Latitude != 0) && ($data['hostel_data']->Geo->Longitude != 0))
      {
        $data['google_map_geo_latlng'] = $data['hostel_data']->Geo->Latitude .", ". $data['hostel_data']->Geo->Longitude;
      }

      $data['bc_continent']  = $this->Db_country->get_continent_of_country($data['hostel_data']->country,$this->site_lang);
      if(is_null($data['bc_continent']))
      {
        log_message('error', 'This country is not in DB: '.$data['hostel_data']->country);
      }
      $data['bc_country']    = $this->Db_country->get_country($data['hostel_data']->country,$this->site_lang);
      $data['bc_city']       = $this->Db_country->get_city($data['hostel_data']->country,$data['hostel_data']->city,$this->site_lang);

      $data['hostel_comments'] = $this->Db_reviews->get_property_reviews($data['property_number']);
      $data['hostel_rating']   = $this->Db_reviews->get_property_avg_rating($data['property_number']);

      //Map all hostel fata
      $data['hostel']->property_number        = (int) $data['hostel_data']->propertyNumber;
      $data['hostel']->property_name          = (string) $hostel_name;
      $data['hostel']->property_type          = (string)$data['hostel_data']->propertyType;
      $data['hostel']->rating                 = $this->Db_hw_rating->get_hw_rating($data['hostel_data']->propertyNumber);
      $data['hostel']->PropertyImages         = $data['hostel_data']->PropertyImages;
      if(!empty($data['hostel']->PropertyImages))
      {
        $data['hostel']->PropertyImages = xmlobj2arr($data['hostel_data']->PropertyImages);
        $data['hostel']->PropertyImages = $data['hostel']->PropertyImages["PropertyImage"];
        foreach($data['hostel']->PropertyImages as $key => $image)
        {
          $data['hostel']->PropertyImages[$key] = (object) $image;
        }
      }
      $data['hostel']->description            = (string) $data['hostel_data']->description;
      $data['hostel']->descriptionTranslated  = (string) $data['hostel_data']->descriptionTranslated;
      $data['hostel']->facilities             = xmlobj2arr($data['hostel_data']->Facilities);
      $data['hostel']->facilities             = $data['hostel']->facilities["facility"];
      $data['hostel']->facilitiesTranslated   = (string) $data['hostel_data']->Facilities->facilityTranslated;
      if(!empty($data['hostel']->facilitiesTranslated))
      {
        $data['hostel']->facilitiesTranslated = explode("|", $data['hostel']->facilitiesTranslated);
      }
      $data['hostel']->conditions             = (string) $data['hostel_data']->conditions;
      $data['hostel']->conditionsTranslated   = (string) $data['hostel_data']->conditionsTranslated;
      $data['hostel']->directions             = (string) $data['hostel_data']->directions;
      $data['hostel']->directionsTranslated   = (string) $data['hostel_data']->directionsTranslated;
      $data['hostel']->address1               = (string) $data['hostel_data']->address1;
      $data['hostel']->address2               = (string) $data['hostel_data']->address2 ;
      $data['hostel']->city                   = (string) $data['hostel_data']->city;
      $data['hostel']->country                = (string) $data['hostel_data']->country;

      //TEMP variable to support backward compatibility
//      $data['propertyNumber'] = $data['hostel_data']->propertyNumber;
//      $data['propertyName'] = $hostel_name;
//      unset($data['hostel']);
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

    $data['currency'] = $this->config->item('site_currency_selected');
//    $cur_selected = $this->input->cookie('currency',TRUE);
    if(!empty($cur_selected))
    {
      $data['currency'] = $cur_selected;
    }
    else
    {
      $user_id = $this->tank_auth->get_user_id();

      if($user_id!= false)
      {
       $user_info = array();
       $user_info = $this->Db_model->get_user_profile($user_id);
       $data['currency'] = $this->Db_currency->get_currency_code($user_info['favorite_currency']);
      }
    }
    $this->load->model('Db_hw_hostel');

    $hwhostel_data = $this->Db_hw_hostel->get_hostel_data_from_number($data['property_number'],$data['currency']);

    $data['hostel_min_price'] = NULL;
    if(!is_null($hwhostel_data))
    {
      if(strcasecmp($data['currency'],$hwhostel_data->currency_price)!=0)
      {

        $data['hostel_min_price'] = currency_symbol($data['currency']) ." ". floor($this->Db_currency->convert_from_hw_rates($hwhostel_data->currency_price, $data['currency'], $hwhostel_data->bed_price));
      }
      else
      {
        $data['hostel_min_price'] = currency_symbol($hwhostel_data->currency_price). " ".floor($hwhostel_data->bed_price);
      }
    }

    //our local reviews
    $data["user_reviews"] = array();
    $data["reviews_translation_available"] = false;

    $this->load->model('Db_reviews');

    $our_reviews = $this->Db_reviews->get_property_reviews($data['property_number']);

    if((!is_null($our_reviews))&&($our_reviews->num_rows()>0))
    {
      $index = 0;
      foreach($our_reviews->result() as $our_review)
      {
        $data["user_reviews"][$index]["author_name"]     = $our_review->firstname;
        $data["user_reviews"][$index]["review_date"]     = $our_review->comment_date;
        $data["user_reviews"][$index]["author_country"]  = $our_review->user_country;
        $data["user_reviews"][$index]["review"]          = $our_review->comment_content;
        $data["user_reviews"][$index]["review_translated"] = "";
        $data["user_reviews"][$index]["review_rating"]   = $our_review->user_rating*10;
        $data["user_reviews"][$index]["review_source"]   = "LOCAL";
        $index++;
      }

      $data["reviews_translation_available"] = $this->Hw_api_translate->translate_mixed_reviews($data["user_reviews"]);
    }

    $data['country_selected'] = $data['bc_country'];
    $data['city_selected'] = $data['bc_city'];

    $data['title'] = sprintf(gettext("%s - %s"),$hostel_name,$this->config->item('site_title'));

    $this->carabiner->load_group_assets('formvalidation');

    $data['current_view_dir'] = $this->api_view_dir;
    $data['current_view'] = "hostel_view";
    $this->load->view('includes/template',$data);

  }

  //Ajax access only
  function booking_avail()
  {
    $this->output->enable_profiler(FALSE);
    $data = array();

    $propertyName   = $this->input->post("propertyName", TRUE);
    $propertyNumber = $this->input->post("propertyNumber",TRUE);
    $dateStart      = $this->input->post("dateStart",TRUE);
    $numNights      = $this->input->post("numNights",TRUE);
    $currency       = $this->input->post("currency",TRUE);

    set_cookie('currency_selected', $currency, 2592000);
    set_cookie('date_selected',$dateStart,$this->config->item('sess_expiration'));
    set_cookie('numnights_selected',$numNights,$this->config->item('sess_expiration'));

    $this->load->library('hw_engine');

    $data = $this->hw_engine->property_avail_check($propertyName, $propertyNumber, $dateStart, $numNights, $currency);

    $data["country_selected"] = $this->input->post("country_selected",TRUE);
    $data["city_selected"]    = $this->input->post("city_selected",TRUE);

    $this->load->view('hw/booking_avail_view',$data);
  }

  function booking_process($isajax = false, $ajaxdata = NULL, $isSecure3dReturn = false, $securedata = NULL)
  {
    $data['api_3d_secure_request'] = array();

    if($isajax)
    {
      $data['booking_hostel_name'] = $ajaxdata['propertyName'];
      $propertyNumber    = $ajaxdata['propertyNumber'];
      $dateStart         = $ajaxdata['dateStart'];
      $numNights         = $ajaxdata['numNights'];
      $roomPreferences   = $ajaxdata['roomPreferences'];
      $nbPersons         = $ajaxdata['nbPersons'];
      $roomTypes         = $ajaxdata['avail-roomTypes'];
      $roomDescs         = $ajaxdata['avail-roomDescs'];
      $roomDescTrans     = $ajaxdata['avail-roomDescTrans'];
      $bookCurrency      = $ajaxdata['bookCurrency'];
      $settleCurrency    = $ajaxdata['settleCurrency'];

      if(isset($ajaxdata['api_3d_secure_request']))
      {
        $data['api_3d_secure_request'] = $ajaxdata['api_3d_secure_request'];
      }
    }
    elseif($isSecure3dReturn)
    {
      $data['booking_hostel_name'] = $securedata['propertyName'];
      $propertyNumber    = $securedata['propertyNumber'];
      $dateStart         = $securedata['dateStart'];
      $numNights         = $securedata['numNights'];
      $roomPreferences   = $securedata['roomPreferences'];
      $nbPersons         = $securedata['nbPersons'];
      $roomTypes         = $securedata['roomTypes'];
      $roomDescs         = $securedata['roomDescs'];
      $roomDescTrans     = $securedata['roomDescTrans'];
      $bookCurrency      = $securedata['bookCurrency'];
      $settleCurrency    = $securedata['settleCurrency'];
    }
    else
    {
      $propertyNumber = $this->input->post('book-propertyNumber',TRUE);
      $dateStart      = $this->input->post('book-dateStart',TRUE);
      $numNights      = $this->input->post('book-numNights',TRUE);

      $roomPreferences = $this->input->post('book-roomPreferences',TRUE);
      $nbPersons       = $this->input->post('book-nbPersons',TRUE);
      $bookCurrency    = $this->input->post('book-currency',TRUE);
      $settleCurrency  = $bookCurrency;

      $roomTypes         = $this->input->post('book-roomType',TRUE);
      $roomDescs         = $this->input->post('book-roomDesc',TRUE);
      $roomDescTrans     = $this->input->post('book-roomDescTrans',TRUE);

      $data['booking_hostel_name'] = $this->input->post('book-propertyName');

    }

    $this->load->model('Db_hw_hostel');
    $data['property_type'] = $this->Db_hw_hostel->get_property_type($propertyNumber);

    //Get main services and breakfast included
    $this->load->model('i18n/db_translation_cache');
    $data['main_services'] = $this->Db_hw_hostel->get_hostel_main_services($propertyNumber);
    $data['breakfast_included'] = 0;
    if(!empty($data['main_services']))
    {
      foreach($data['main_services'] as $si => $service)
      {
        if($service->service_id == 26)
        {
          $data['breakfast_included'] = 1;
        }
        $translation = $this->db_translation_cache->get_translation($service->description,$this->site_lang);
        if(!empty($translation))
        {
          $data['main_services'][$si]->description = $translation->translation;
        }
      }
    }

    $bsid = $this->session->userdata('BSID_'.$propertyNumber);

    if($bsid==false) $bsid = NULL;

    $settleCurrency = settle_currency_filter($settleCurrency,$this->config->item('site_settle_currency_default'));

    $inputok = false;

    //Force english short description for better translation
    //Because we can not know the langage sent by HW if it is not English
    $this->api_functions_lang = "English";
    $this->Hw_api_translate->setLanguage($this->site_lang,"en");

    if(isset($propertyNumber)&&!empty($propertyNumber)&&
       isset($dateStart)&&!empty($dateStart)&&
       isset($numNights)&&!empty($numNights)&&
       isset($roomPreferences)&&!empty($roomPreferences)&&
       isset($nbPersons)&&!empty($nbPersons))
    {
      $api = $this->Hostel_api_model->propertyBookingRequest($this->config->item('hostelworld_userID'),
                                                             $propertyNumber,
                                                             $dateStart,
                                                             $numNights,
                                                             $roomPreferences,
                                                             $nbPersons,
                                                             $settleCurrency,
                                                             $bsid,
                                                             $bookCurrency,
                                                             $this->api_functions_lang);
      $inputok = true;
      $data['booking_request'] = array();
      $data['api_error'] = $api[0];
      $data['api_error_msg'] = false;
    }

    $numNights_calculated = 0;
    $dateStart_calculated = $dateStart;

    if($inputok===false)
    {
      $data['booking_request'] = array();
      $data['api_error'] = true;
      $data['api_error_msg'] = _('Données de réservations incomplètes.');
    }
    elseif($data['api_error']==true)
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
        $data['api_error_msg'] = $this->Hw_api_translate->translate_APIError($data['api_error_msg']);
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
    else
    {
      //Add rooms description adn translation data posted from avail view or ajax post
      foreach($api[1][0]->RoomDetails as $room)
      {
        //get key of roomDescription
        if(empty($room->roomTypeDescription))
        {
          $room->roomTypeDescription = $room->roomType;
        }

        $trans = array_keys($roomDescs, $room->roomTypeDescription);
        if(!empty($trans))
        {
          $room->roomTypeDescriptionTranslated = $roomDescTrans[$trans[0]];
        }
      }

      // Find the CADDepositAMount to put in the hidden field 'analytic-value'
      $CADDepositAmount = ((float)$api[1][0]->SettleBillTotal->value - (float)$api[1][0]->SettleBookingFee->value) * 0.6;
      if ($settleCurrency != 'CAD')
      {
        $CADDepositAmount = $this->Db_currency->convert_from_hw_rates($settleCurrency, 'CAD', $CADDepositAmount);
      }
      $api[1][0]->CADDepositAmount->value = number_format($CADDepositAmount, 2);


      $data['booking_request'] = $this->Hw_api_translate->translate_bookingRequest($api[1][0],$propertyNumber,$dateStart,$numNights);
      $this->session->set_userdata('BSID_'.$propertyNumber, "".$data['booking_request']->BSID->value);

      $numNights_calculated = $this->Hostel_api_model->count_numnights($data['booking_request']->RoomDetails);
      $dateStart_calculated = $data['booking_request']->RoomDetails[0]->date;

    }

    $data['title'] = $data['booking_hostel_name']." | ".$this->config->item('site_title');

    $data['propertyNumber'] = $propertyNumber;
    $data['dateStart']      = $dateStart;
    $data['dateStart_calculated']      = $dateStart_calculated;
    $data['numNights']      = $numNights;
    $data['numNights_calculated']      = $numNights_calculated;
    $data['settleCurrency']   = $settleCurrency;
    $data['bookCurrency']     = $bookCurrency;
    $data['roomPreferences'] = $roomPreferences;
    $data['nbPersons']      = $nbPersons;

    $data['avail-roomTypes']        = $roomTypes;
    $data['avail-roomDescs']        = $roomDescs;
    $data['avail-roomDescTrans']    = $roomDescTrans;

    $data['isAjax']         = $isajax;

    $this->carabiner->load_group_assets('formvalidation');
    $this->load->helper('form_elements');

    if($isajax)
    {
      $data['api_booking_error']     = $ajaxdata['api_booking_error'];
      $data['api_booking_error_msg'] = $ajaxdata['api_booking_error_msg'];

      $data['book_firstname']     = $ajaxdata['book_firstname'];
      $data['book_lastname']      = $ajaxdata['book_lastname'];
      $data['book_nationality']   = $ajaxdata['book_nationality'];
      $data['book_gender']        = $ajaxdata['book_gender'];
      $data['book_arrival_time']  = $ajaxdata['book_arrival_time'];
      $data['book_email_address'] = $ajaxdata['book_email_address'];
      $data['book_phone_number']  = $ajaxdata['book_phone_number'];
      $data['sms']                = $ajaxdata['sms'];
      $data['book_sign_me_up']    = $ajaxdata['book_sign_me_up'];
      $data['mail_subscribe']     = $ajaxdata['mail_subscribe'];

      if(isset($ajaxdata['secure_parameters']))
      {
        $data['secure_parameters']     = $ajaxdata['secure_parameters'];
      }
      $data['book_ccname']        = $ajaxdata['book_ccname'];
      $data['book_ccnumber']      = $ajaxdata['book_ccnumber'];
      $data['book_cctype']        = $ajaxdata['book_cctype'];
      $data['book_ccexpiry_m']    = $ajaxdata['book_ccexpiry_m'];
      $data['book_ccexpiry_y']    = $ajaxdata['book_ccexpiry_y'];
      $data['book_ccexpiry_y']    = $ajaxdata['book_ccexpiry_y'];
      $data['cvv']                = $ajaxdata['cvv'];
//      $data['issueno']            = $ajaxdata['issueno'];

      $data['conditions_agree']    = $ajaxdata['conditions_agree'];

      $this->load->view('hw/booking_view',$data);
    }
    elseif($isSecure3dReturn)
    {
      $data['api_booking_error'] = false;
      $data['api_booking_error_msg'] = array();

      $data['secure3d_final'] = true;
      $data['secure_cookie']        = $securedata['secure_cookie'];
      $data['secure_transactionId'] = $securedata['secure_transactionId'];
      $data['secure_MD']            = $securedata['secure_MD'];
      $data['secure_PaRes']         = $securedata['secure_PaRes'];
      $data['secure_userSessionId'] = $this->session->userdata('session_id');

      $data['book_firstname']     = $securedata['book_firstname'];
      $data['book_lastname']      = $securedata['book_lastname'];
      $data['book_nationality']   = $securedata['book_nationality'];
      $data['book_gender']        = $securedata['book_gender'];
      $data['book_arrival_time']  = $securedata['book_arrival_time'];
      $data['book_email_address'] = $securedata['book_email_address'];
      $data['book_phone_number']  = $securedata['book_phone_number'];
      $data['sms']                = $securedata['sms'];
      $data['book_sign_me_up']    = $securedata['book_sign_me_up'];
      $data['mail_subscribe']     = $securedata['mail_subscribe'];


      $data['current_view_dir'] = $this->api_view_dir;
      $data['current_view'] = "booking_view";
      $this->load->view('includes/template_book',$data);
    }
    else
    {

      $data['api_booking_error'] = false;
      $data['api_booking_error_msg'] = array();

      $data['book_firstname']     = "";
      $data['book_lastname']      = "";
      $data['book_nationality']   = "";
      $data['book_gender']        = "";
      $data['book_arrival_time']  = "11";
      $data['book_email_address'] = "";
      $data['sms']                = "none";
      $data['book_phone_number']  = "";
      $data['book_sign_me_up']    = true;
      $data['mail_subscribe']     = false;

      $user_id = $this->tank_auth->get_user_id();

      if($user_id!= false)
      {
       $user_info = array();
       $user_info = $this->Db_model->get_user_profile($user_id);
       $data['book_email_address'] = $user_info['email'];
       $data['book_gender']        = $this->Db_model->get_gender_value($user_info['gender_id']);
       $data['book_firstname']     = $user_info['first_name'];
       $data['book_lastname']      = $user_info['last_name'];
       $data['book_phone_number']  = $user_info['phone_number'];
       $data['book_nationality']   = $user_info['home_country'];
      }
//      $data['book_ccname']        = $ajaxdata['book_ccname'];
//      $data['book_ccnumber']      = $ajaxdata['book_ccnumber'];
//      $data['book_cctype']        = $ajaxdata['book_cctype'];
//      $data['book_ccexpiry_m']    = $ajaxdata['book_ccexpiry_m'];
//      $data['book_ccexpiry_y']    = $ajaxdata['book_ccexpiry_y'];

      $data['current_view_dir'] = $this->api_view_dir;
      $data['current_view'] = "booking_view";
      $this->load->view('includes/template_book',$data);
    }
  }

  /* Ajax return function
   * //Ajax access only
   */
  function booking_check()
  {
    $this->output->enable_profiler(FALSE);

    $settlecurrency = settle_currency_filter($this->input->post('settleCurrency',TRUE),$this->config->item('site_settle_currency_default'));

    $ccvalidfrom = NULL;
    if($this->input->post('ccvalidfrom_m',TRUE) != NULL)
    {
      $postdata['ccvalidfrom_m'] = $this->input->post('ccvalidfrom_m',TRUE);
      $postdata['ccvalidfrom_y'] = $this->input->post('ccvalidfrom_y',TRUE);
      $ccvalidfrom = $postdata['ccvalidfrom_m'].'/'.$postdata['ccvalidfrom_y'];
    }

    $postdata["testmode"] = $this->config->item('booking_test_mode');
    $user_id = $this->tank_auth->get_user_id();
    if(($user_id !== false))
    {
      $uprof = $this->tank_auth->get_profile($user_id);
      if($uprof->user_level_id >= 8)
      {
        $postdata["testmode"] = 1;
      }
    }

    //Secure 3D data
    $secure_cookie      = NULL;
    $secure_pares       = NULL;
    $secure_transid     = NULL;
    $secure_newsession  = NULL;
    $secure_ip          = NULL;
    $secure_usersession = NULL;

    if($this->input->post('secure_final',true)==true)
    {
      $secure_cookie      = $this->input->post('secure_cookie',true);
      $secure_pares       = $this->input->post('secure_pares',true);
      $secure_transid     = $this->input->post('secure_transid',true);
      $secure_newsession  = $this->input->post('secure_newsession',true);
      $secure_ip          = $this->input->post('secure_ip',true);
      $secure_usersession = $this->input->post('secure_usersession',true);
    }

    $postdata['propertyNumber']   = $this->input->post('propertyNumber',TRUE);
    $postdata['propertyName']     = $this->input->post('propertyName',TRUE);
    $postdata['dateStart']        = $this->input->post('dateStart',TRUE);
    $postdata['numNights']        = $this->input->post('numNights',TRUE);
    $postdata['bookCurrency']     = $this->input->post('bookCurrency',TRUE);
    $postdata['settleCurrency']     = $settlecurrency;
    $postdata['roomPreferences']  = explode(",",$this->input->post('roomPreferences',TRUE));
    $postdata['nbPersons']        = explode(",",$this->input->post('nbPersons',TRUE));
    $postdata['avail-roomTypes']        = array(); //not available in JS for now
    $postdata['avail-roomDescs']        = explode(",",$this->input->post('roomTypeDescription',TRUE));
    $postdata['avail-roomDescTrans']    = explode(",",$this->input->post('roomTypeDescriptionTranslated',TRUE));


    $postdata['book_firstname']     = $this->input->post('firstname',TRUE);
    $postdata['book_lastname']      = $this->input->post('lastname',TRUE);
    $postdata['book_nationality']   = $this->input->post('nationality',TRUE);
    $postdata['book_gender']        = $this->input->post('gender',TRUE);
    $postdata['book_arrival_time']  = $this->input->post('arrival_time',TRUE);
    $postdata['book_email_address'] = $this->input->post('email_address',TRUE);
    $postdata['book_phone_number']  = $this->input->post('phone_number',TRUE);
    $postdata['sms']                = $this->input->post('sms',TRUE);
    $postdata['book_sign_me_up']    = $this->input->post('sign_me_up',TRUE)=="true";
    $postdata['mail_subscribe']     = $this->input->post('mail_subscribe',TRUE)=="true";

    $postdata['CADDepositAmount']     = $this->input->post('CADDepositAmount',TRUE);

    $postdata['book_ccname']        = $this->input->post('ccname',TRUE);
    $postdata['book_ccnumber']      = $this->input->post('ccnumber',TRUE);
    $postdata['book_cctype']        = $this->input->post('cctype',TRUE);
    $postdata['book_ccexpiry_m']    = $this->input->post('ccexpiry_m',TRUE);
    $postdata['book_ccexpiry_y']    = $this->input->post('ccexpiry_y',TRUE);
    $postdata['cvv']                = $this->input->post('cvv',TRUE);
    $postdata['issueno']            = $this->input->post('issueno',TRUE);

    $postdata['conditions_agree']   = TRUE;

    $postdata['api_booking_error_msg'] = array();
    $postdata['api_booking_error'] = "";

    if($this->input->post('refresh',TRUE) == "true")
    {
      $this->booking_process(true,$postdata);
    }
    else
    {

      $booking = $this->Hostel_api_model->bookingConfirmationRequest(
                                            $this->config->item('hostelworld_userID'),
                                            $this->input->post('bsid',TRUE),
                                            $postdata['book_firstname'],
                                            $postdata['book_lastname'],
                                            $postdata['book_nationality'],
                                            $postdata['book_gender'],
                                            $postdata['book_email_address'],
                                            $postdata['book_phone_number'],
                                            $postdata['book_arrival_time'],
                                            $postdata['book_ccname'],
                                            $postdata['book_ccnumber'],
                                            $postdata['book_ccexpiry_m'].'/'.$postdata['book_ccexpiry_y'],
                                            $postdata['book_cctype'],
                                            $postdata['settleCurrency'],
                                            $postdata['bookCurrency'],
                                            $postdata['issueno'],
                                            $ccvalidfrom,
                                            $postdata['cvv'],
                                            $secure_pares,
                                            $secure_cookie,
                                            $secure_transid,
                                            $secure_newsession,
                                            $secure_ip,
                                            $secure_usersession,
                                            $postdata["testmode"]);


      //if booking ok and confirm
      if(($booking[0] == false)&&(strcmp($booking[1][0]->BookingRequestResult->message,'Booking Confirmed')==0))
      {

        $roomdesc    = explode(",",$this->input->post('roomTypeDescription',TRUE));
        $roomdesctrans = explode(",",$this->input->post('roomTypeDescriptionTranslated',TRUE));
        $roomnumbers = explode(",",$this->input->post('roomNumber',TRUE));

        $roomTypeDescription = Array();
        foreach($roomnumbers as $i => $roomnumber)
        {
          $roomTypeDescription[$roomnumber] = new stdClass();
          $roomTypeDescription[$roomnumber]->roomDesc = $roomdesc[$i];
          $roomTypeDescription[$roomnumber]->roomDescTrans = $roomdesctrans[$i];
        }

        $booking = $booking[1][0];
        foreach($booking->RoomDetails as $r => $rooms)
        {
          if(!empty($roomTypeDescription[(string)$rooms->roomNumber]->roomDesc))
          {
            $rooms->roomTypeDescription = $roomTypeDescription[(string)$rooms->roomNumber]->roomDesc;
          }
          elseif(empty($rooms->roomTypeDescription))
          {
            $rooms->roomTypeDescription = $rooms->roomType;
          }

          if(!empty($roomTypeDescription[(string)$rooms->roomNumber]->roomDescTrans))
          {
            $rooms->roomTypeDescriptionTranslated = $roomTypeDescription[(string)$rooms->roomNumber]->roomDescTrans;
          }
          else
          {
            $rooms->roomTypeDescriptionTranslated = "";
          }
        }

        unset($postdata['book_ccname']);
        unset($postdata['book_ccnumber']);
        unset($postdata['book_ccexpiry_m']);
        unset($postdata['book_ccexpiry_y']);
        $this->_booking_confirmation($booking,$postdata);
      }
      //if booking ok but 3D secure request is asked
      elseif(($booking[0] == false)&&(strcmp($booking[1][0]->BookingRequestResult->message,'3D Secure Request')==0))
      {
        $postdata['api_booking_error'] = "3d_secure_request";

        $postdata['secure_parameters']['PaReq']   = get_object_vars($booking[1][0]->parequest);
        $postdata['secure_parameters']['PaReq']   = $postdata['secure_parameters']['PaReq']['message'];
        $postdata['secure_parameters']['TermUrl'] = site_url($this->Db_links->get_link('secure_validation'));
        $postdata['secure_parameters']['MD']      = get_object_vars($booking[1][0]->sessionId);
        $postdata['secure_parameters']['MD']      = $postdata['secure_parameters']['MD']['message'];
        $postdata['secure_parameters']['issuerURL'] = get_object_vars($booking[1][0]->issuerURL);
        $postdata['secure_parameters']['issuerURL'] = $postdata['secure_parameters']['issuerURL']['message'];
        $postdata['secure_parameters']['cookie']  = get_object_vars($booking[1][0]->cookie);
        $postdata['secure_parameters']['cookie']  = $postdata['secure_parameters']['cookie']['message'];
        $postdata['secure_parameters']['transactionId'] = get_object_vars($booking[1][0]->transactionId);
        $postdata['secure_parameters']['transactionId'] = $postdata['secure_parameters']['transactionId']['message'];

        $this->_storeBookingSession($postdata);
        $this->booking_process(true,$postdata);
      }
      //if API throws error message
      elseif($booking[1]!=false)
      {
        //log transaction error
        $this->load->library('custom_log');
        $this->custom_log->set_freq("Y-m-d");
        $this->custom_log->log($this->transaction_log_filename,"Transaction error dump for ".$postdata['book_email_address']." ( ".$postdata['book_lastname'].", ".$postdata['book_firstname']." ) with card ".$postdata['book_cctype']." in ".$postdata['settleCurrency'].": ".var_export($booking[1][0],true));

        $postdata['api_booking_error'] = "api_msg";

        $postdata['api_booking_error_msg'] = $this->Hw_api_translate->translate_APIError($booking[1][0]);
        if(isset($postdata['api_booking_error_msg']->Error))
        {
           $postdata['api_booking_error_msg'] =  $postdata['api_booking_error_msg']->Error;

        }
        elseif(isset($postdata['api_booking_error_msg']->UserMessage))
        {
          $postdata['api_booking_error_msg'] =  $postdata['api_booking_error_msg']->UserMessage;
        }
        else
        {
          $postdata['api_booking_error_msg'][0] =  _("Erreur lors de la réservation");
          $this->custom_log->log($this->transaction_log_filename,"Transaction error not set UserMessage or Error.");
        }
        //print_r($booking[1][0]);


        $this->booking_process(true,$postdata);
      }
      //if API not responding ok
      else
      {
        $postdata['api_booking_error'] = "api_out";
        $postdata['api_booking_error_msg'][0] =  _("Serveur inaccessible pour le moment. Veuillez réessayer de nouveau.");

        $this->booking_process(true,$postdata);

      }
    }
  }

  /*
   *
   *
   *
   *
   */

  function booking_return_3D()
  {
    $data = array();
    $this->_restoreBookingSession($data);
    $data['secure_MD'] = $this->input->post('MD',true);
    $data['secure_PaRes'] = $this->input->post('PaRes',true);

    $this->_clearBookingSession();

    $this->booking_process(false,NULL,true,$data);
  }

  /**
   * booking_confirmed
   *
   * @access public
   * @param string  the email address
   * @param string  the link title
   * @param mixed   any attributes
   *
   * @return  view user transaction (for test phase)
   *
   * Behavior:
   *
   *  - send email confirmation to client and review
   *
   *  - add transaction to database associated with inserted email address
   *  - cookie tracking handling
   *
   *  if user has no account and request one:
   *    - call to sign up function
   *
   */
  function _booking_confirmation($booking,$ajaxdata)
  {

    $data['booking']          = $this->Hw_api_translate->translate_BookingConfirmation($booking);
    $data['booking']->CADDepositAmount->value = $ajaxdata['CADDepositAmount'];
    $data['firstname']        = $ajaxdata['book_firstname'];
    $data['lastname']         = $ajaxdata['book_lastname'];
    $data['book_email_address']  = $ajaxdata['book_email_address'];
    $data['dateStart']        = $ajaxdata['dateStart'];
    $data['dateStart_calculated']  = $booking->RoomDetails[0]->date;
    $data['numNights']        = $ajaxdata['numNights'];
    $data['numNights_calculated'] = $this->Hostel_api_model->count_numnights($booking->RoomDetails);
    $data['book_arrival_time']  = $ajaxdata['book_arrival_time'];
    $data['propertyNumber']     = $ajaxdata['propertyNumber'];
    $data['propertyName']       = $ajaxdata['propertyName'];
    $data['bookCurrency']       = $ajaxdata['bookCurrency'];
    $data['settleCurrency']       = $ajaxdata['settleCurrency'];
    $data['bookAmountDueField'] = $data['bookCurrency']."AmountDue";
    $data['isCustomCurrency']   = (strcasecmp($data['settleCurrency'],$data['bookCurrency'])!=0);

    $this->load->model('Db_hw_hostel');

    //Get main services and breakfast included
    $this->load->model('i18n/db_translation_cache');
    $data['main_services'] = $this->Db_hw_hostel->get_hostel_main_services($data['propertyNumber']);
    $data['breakfast_included'] = 0;
    if(!empty($data['main_services']))
    {
      foreach($data['main_services'] as $si => $service)
      {
        if($service->service_id == 26)
        {
          $data['breakfast_included'] = 1;
        }
        $translation = $this->db_translation_cache->get_translation($service->description,$this->site_lang);
        if(!empty($translation))
        {
          $data['main_services'][$si]->description = $translation->translation;
        }
      }
    }

    //Send confirmation email
    $emailsent = FALSE;

    $emailcontent = $this->load->view('email/new_transaction', $data, true);
    $this->email->from($this->config->item('admin_booking_email'),$this->config->item('site_name'));
    $this->email->to($ajaxdata['book_email_address']);

    if($this->config->item('admin_booking_conf') > 0)
    {
      $this->email->bcc($this->config->item('admin_booking_conf_email'));
    }

    $this->email->subject(sprintf(gettext('Votre réservation sur %s!'),$this->config->item('site_name'))." - #HW ".$data['booking']->CustomerReference->value);
    $this->email->message($emailcontent);
    $emailsent = $this->email->send();

    if(! $emailsent)
    {
      log_message('error',"Error sending confirmation email to ".$ajaxdata['book_email_address']." -> ".$this->email->print_debugger());
    }

    //clear email data for next email to be sent ok
    $this->email->clear();
    $this->email->_bcc_array = array();

   //Send confirmation email to property
    $prop_emailsent = NULL;

    if(false)
    {
      if($ajaxdata["testmode"] < 1)
      {

        $prop_adress = (string) $booking->PropertyDetails->email;
        if(!empty($prop_adress))
        {
          $prop_emailsent = FALSE;

          $emailcontent = $this->load->view('email/property_email', $data, true);
          $this->email->from($this->config->item('admin_booking_email'),$this->config->item('site_name'));

          $this->email->to($prop_adress);
          if($this->config->item('admin_booking_conf') > 0)
          {
            $this->email->bcc($this->config->item('admin_booking_conf_email'));
          }

          $this->email->subject("Reservation HW ".$data['booking']->CustomerReference->value. " on ".$this->config->item('site_name'));
          $this->email->message($emailcontent);

          $prop_emailsent = $this->email->send();

          if(! $prop_emailsent)
          {
            log_message('error',"Error sending property email to ".$prop_adress." -> ".$this->email->print_debugger());
          }
          $this->email->clear();
          $this->email->_bcc_array = array();

        }

      }
      else
      {
        $prop_emailsent = FALSE;
      }
    }


    //Store transaction in local databases

    $booking_time = date('Y-m-d H:i:s');
    $arrival_date_time = $data['dateStart_calculated']." ".$ajaxdata['book_arrival_time'].":00:00";

    $property_currency = NULL;
    if($booking->PropertyDetails->currency)
    {
      $property_currency = $booking->PropertyDetails->currency;
    }

    $trans_id = $this->Db_model->add_hw_transaction(
                        $ajaxdata["testmode"],
                        $booking_time,
                        (string)$booking->CustomerReference->value,
                        $ajaxdata['book_firstname'],
                        $ajaxdata['book_lastname'],
                        $ajaxdata['book_nationality'],
                        $ajaxdata['book_gender'],
                        $ajaxdata['book_phone_number'],
                        $arrival_date_time,
                        $ajaxdata['propertyNumber'],
                        $ajaxdata['propertyName'],
                        $data['numNights_calculated'],
                        $booking->AmountCharged->value,
                        $booking->ChargedCurrency->value,
                        $booking->PropertyAmountDue->value,
                        $property_currency,
                        $ajaxdata['book_email_address'],
                        $emailsent,
                        $prop_emailsent,
                        $booking->RoomDetails);

    //Store keywords of transaction to database
    $query = $this->db->get('refer_keywords');

    foreach ($query->result() as $row)
    {
      $word_value = get_cookie($this->config->item('adword_cookie_prefix').$row->keyword, TRUE);
      if($word_value!==false)
      {
        $this->Db_model->add_keyword_to_transaction($trans_id, $row->keyword_id, $word_value);
      }
    }

    //SMS send or delay set
    if(isset($ajaxdata['sms']) && ($ajaxdata['sms'] != 'none'))
    {
      if(is_numeric($ajaxdata['sms']))
      {
        $this->load->model('Db_sms');
        $this->Db_sms->add_sms($trans_id, 0, $ajaxdata['sms']);
      }
      elseif($ajaxdata['sms'] == 'now')
      {
        $this->load->library('clickatell');
        $this->load->model('Db_sms');

        $msg_id = NULL;
        $error_number = NULL;
        $status = 0;
        $sms_text = $this->load->view($this->api_view_dir.'/sms_view', $data, true);
        try
        {
          $msg_id = $this->clickatell->send_single_sms($ajaxdata['book_phone_number'],$sms_text);
          $status = 1;
        }
        catch(Exception $e)
        {
          $error_number = $e->getCode();
          log_message('error',"SMS sending failed for transaction #$trans_id and phone number ".$ajaxdata['book_phone_number']." -> ".$e->getMessage());
        }

        $this->Db_sms->add_sms($trans_id, $status, NULL, $msg_id, $error_number);
      }
    }
    //Add hostel booking data to DB if hostel is cached
    $this->load->model('Db_hw_hostel');
    $hw_hostel_id = $this->Db_hw_hostel->get_hostel_id($ajaxdata['propertyNumber']);
    if(!empty($hw_hostel_id))
    {
      $hwemail    = NULL;
      $hwpostCode = NULL;
      $hwstate    = NULL;
      $hwphone    = NULL;
      $hwfax      = NULL;
      $hwcurrency = NULL;
      $hwdeposit  = NULL;

      if(!empty( $booking->PropertyDetails->email          )) $hwemail    = (string) $booking->PropertyDetails->email;
      if(!empty( $booking->PropertyDetails->postCode       )) $hwpostCode = (string) $booking->PropertyDetails->postCode;
      if(!empty( $booking->PropertyDetails->state          )) $hwstate    = (string) $booking->PropertyDetails->state;
      if(!empty( $booking->PropertyDetails->phone          )) $hwphone    = (string) $booking->PropertyDetails->phone;
      if(!empty( $booking->PropertyDetails->fax            )) $hwfax      = (string) $booking->PropertyDetails->fax;
      if(!empty( $booking->PropertyDetails->currency       )) $hwcurrency = (string) $booking->PropertyDetails->currency;
      if(!empty( $booking->PropertyDetails->depositPercent )) $hwdeposit  = floatval ($booking->PropertyDetails->depositPercent);

      $this->Db_hw_hostel->update_hostel_booking_info($ajaxdata['propertyNumber'],
                                                      $hwemail,
                                                      $hwpostCode,
                                                      $hwstate,
                                                      $hwphone,
                                                      $hwfax,
                                                      $hwcurrency,
                                                      $hwdeposit );
    }

    if($ajaxdata['book_sign_me_up']==true)
    {

      //Create user with random password and send email
      if($this->tank_auth->is_email_available($ajaxdata['book_email_address']))
      {
        $user_data = $this->tank_auth->create_user('', $ajaxdata['book_email_address'], random_string('alnum', $this->config->item('generate_password_length', 'tank_auth')), $this->config->item('email_activation', 'tank_auth'));

        //Send welcome email data
        $user_data['site_name'] = $this->config->item('site_name');
        $this->_send_email('welcome', $user_data['email'], $user_data);
        unset($user_data['password']); // Clear password (just for any case)

        $profile = array(
                      'first_name' => $ajaxdata['book_firstname'],
                      'last_name' => $ajaxdata['book_lastname'],
                      'gender_id' => $this->Db_model->get_gender_id($ajaxdata['book_gender']),
                      'phone_number' => $ajaxdata['book_phone_number'],
                      'home_country' => $ajaxdata['book_nationality'],
                      'mail_subscription' => $ajaxdata['mail_subscribe'],
                      'favorite_currency' => $this->Db_currency->get_currency_id($ajaxdata['bookCurrency']),
                      );
        $this->tank_auth->set_profile($user_data['user_id'],$profile);
      }
      else
      {
        //this email is already register to login click here:
        //$data['login_warning'] = _("Ce courriel possède déja un compte.");
      }

    }

    $this->load->view('hw/booking_end_view',$data);

  }

  /*
   *
   */
  function _adwordsInit()
  {
    $query = $this->db->get('refer_keywords');

    foreach ($query->result() as $row)
    {
      $word_value = $this->input->get($row->keyword, TRUE);
      if($word_value!==false)
      {
        $cookie = array(
                   'name'   => $this->config->item('adword_cookie_prefix').$row->keyword,
                   'value'  => $word_value,
                   'expire' => $this->config->item('adword_cookie_expiration'),
               );

        set_cookie($cookie);
      }
    }
  }
  /*
   *
   */
  function _searchBoxInit(&$data)
  {
    $this->carabiner->load_group_assets('search_box_scripts');

    //initialization
    $data['country_selected']   = NULL;
    $data['city_selected']      = NULL;
    $data['date_selected']      = NULL;
    $data['numnights_selected'] = NULL;
    $data['bc_continent']       = NULL;
    $data['bc_country']         = NULL;
    $data['bc_city']            = NULL;

    if(!isset($data)) $data = array();

    $country   = $this->input->cookie('country_selected',TRUE);
    $city      = $this->input->cookie('city_selected',TRUE);
    $dateStart = $this->input->cookie('date_selected',TRUE);
    $numNights = $this->input->cookie('numnights_selected',TRUE);
    $search_term = $this->input->cookie('search_input_terms',TRUE);

//    $country   = $this->session->userdata('country_selected');
//    $city      = $this->session->userdata('city_selected');
//    $dateStart = $this->session->userdata('date_selected');
//    $numNights = $this->session->userdata('numnights_selected');

    //TONOTICE Remember to Search in cookie, if those values becomes to be set outside CI
    if($country!=false)
    {
      $data['country_selected'] = $country;
    }
    if($city!=false)
    {
      $data['city_selected'] = $city;
    }
    if($dateStart!=false)
    {
      $data['date_selected'] = $dateStart;
    }
    if($numNights!=false)
    {
      $data['numnights_selected'] = $numNights;
    }
    if($search_term!=false)
    {
//      $data['search_term'] = urldecode($search_term);
    }
  }

  function _storeBookingSession($items)
  {
      $this->session->set_userdata(array('secure_cookie'  => $items['secure_parameters']['cookie']));
      $this->session->set_userdata(array('secure_transactionId'  => $items['secure_parameters']['transactionId']));

      $this->session->set_userdata(array('book_firstname'     => $items['book_firstname']));
      $this->session->set_userdata(array('book_lastname'      => $items['book_lastname']));
      $this->session->set_userdata(array('book_nationality'   => $items['book_nationality']));
      $this->session->set_userdata(array('book_gender'        => $items['book_gender']));
      $this->session->set_userdata(array('book_arrival_time'  => $items['book_arrival_time']));
      $this->session->set_userdata(array('book_email_address' => $items['book_email_address']));
      $this->session->set_userdata(array('book_phone_number'  => $items['book_phone_number']));
      $this->session->set_userdata(array('sms'                => $items['sms']));
      $this->session->set_userdata(array('book_sign_me_up'    => $items['book_sign_me_up']));
      $this->session->set_userdata(array('mail_subscribe'     => $items['mail_subscribe']));

      $this->session->set_userdata(array('propertyNumber'   => $items['propertyNumber']));
      $this->session->set_userdata(array('propertyName'     => $items['propertyName']));
      $this->session->set_userdata(array('dateStart'        => $items['dateStart']));
      $this->session->set_userdata(array('numNights'        => $items['numNights']));
      $this->session->set_userdata(array('bookCurrency'     => $items['bookCurrency']));
      $this->session->set_userdata(array('roomPreferences'  => implode(",", $items['roomPreferences'])));
      $this->session->set_userdata(array('nbPersons'        => implode(",", $items['nbPersons'])));
  }
  function _restoreBookingSession(&$data_array)
  {
    $data_array['secure_cookie']        = $this->session->userdata('secure_cookie');
    $data_array['secure_transactionId'] = $this->session->userdata('secure_transactionId');

    $data_array['book_firstname']    = $this->session->userdata('book_firstname');
    $data_array['book_lastname']     = $this->session->userdata('book_lastname');
    $data_array['book_nationality']  = $this->session->userdata('book_nationality');
    $data_array['book_gender']       = $this->session->userdata('book_gender');
    $data_array['book_arrival_time'] = $this->session->userdata('book_arrival_time');
    $data_array['book_email_address'] = $this->session->userdata('book_email_address');
    $data_array['book_phone_number'] = $this->session->userdata('book_phone_number');
    $data_array['sms']               = $this->session->userdata('sms');
    $data_array['book_sign_me_up']   = $this->session->userdata('book_sign_me_up');
    $data_array['mail_subscribe']    = $this->session->userdata('mail_subscribe');

    $data_array['propertyNumber']  = $this->session->userdata('propertyNumber');
    $data_array['propertyName']    = $this->session->userdata('propertyName');
    $data_array['dateStart']       = $this->session->userdata('dateStart');
    $data_array['numNights']       = $this->session->userdata('numNights');
    $data_array['bookCurrency']    = $this->session->userdata('bookCurrency');
    $data_array['roomPreferences'] = explode(",",$this->session->userdata('roomPreferences'));
    $data_array['nbPersons']       = explode(",",$this->session->userdata('nbPersons'));

  }
  function _clearBookingSession()
  {
    $datatoclear = array('secure_cookie' => '',
                         'secure_transactionId' => '',
                         'book_firstname' => '',
                         'book_lastname' => '',
                         'book_nationality' => '',
                         'book_gender' => '',
                         'book_arrival_time' => '',
                         'book_email_address' => '',
                         'book_phone_number' => '',
                         'sms' => '',
                         'book_sign_me_up' => '',
                         'mail_subscribe' => '',
                         'propertyNumber' => '',
                         'propertyName' => '',
                         'dateStart' => '',
                         'numNights' => '',
                         'bookCurrency' => '',
                         'roomPreferences' => '',
                         'nbPersons' => ''
                         );
    $this->session->unset_userdata($datatoclear);
  }
  /*
   *
   */
  function _validate_continent(&$continent)
  {
    if(is_null($this->Db_country->get_continent($continent,$this->api_lang)))
    {
      $continent = NULL;
    }
  }
  /*
   *
   */
  function _set_tr_continent_data(&$data)
  {
    $data['tr_EU'] = $this->Db_country->get_continent("Europe",$this->site_lang);
    $data['tr_NA'] = $this->Db_country->get_continent("Amérique du Nord",$this->site_lang);
    $data['tr_AS'] = $this->Db_country->get_continent("Asie",$this->site_lang);
    $data['tr_SA'] = $this->Db_country->get_continent("Amérique du Sud",$this->site_lang);
    $data['tr_OC'] = $this->Db_country->get_continent("Océanie",$this->site_lang);
    $data['tr_AF'] = $this->Db_country->get_continent("Afrique",$this->site_lang);
  }

  /**
   * Send email message of given type (activate, forgot_password, etc.)
   *
   * @param string
   * @param string
   * @param array
   * @return  void
   */
  function _send_email($type, $email, &$data)
  {
    //$this->load->library('email');
    $this->email->from($this->config->item('email_users_admin'), $this->config->item('site_name'));
    $this->email->reply_to($this->config->item('email_users_admin'), $this->config->item('site_name'));
    $this->email->to($email);
    $this->email->subject(sprintf($this->lang->line('auth_subject_'.$type), $this->config->item('site_name')));
    $this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
    $this->email->set_alt_message($this->load->view('email/'.$type.'-txt', $data, TRUE));
    $this->email->send();
  }

}
?>
