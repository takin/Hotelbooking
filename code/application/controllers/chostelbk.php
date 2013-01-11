<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CHostelbk extends I18n_site
{

	var $api_lang  = "en";
	var $api_functions_lang = "en";

	var $user_id = 0;

	var $currency_from = "EUR";

	var $api_view_dir = "hb/";

  function CHostelbk()
  {
    parent::I18n_site();

    $this->load->helper(array('misc_tools','text'));

    $this->load->library('tank_auth');

    $this->load->model('Hostelbookers_api');
    $this->load->model('Db_currency');
    $this->load->model('Db_model');
    $this->load->model('Hb_api_translate');

    $this->lang->load('tank_auth','multi');

//     $this->api_functions_lang = $this->Hostelbookers_api->lang_code_convert($this->site_lang);
    $this->api_functions_lang = "en";

    //If site is english, set from lang to english
    // else auto-detect because api langage return is not certain
    if(strcmp($this->api_functions_lang,"en")==0)
    {
      $this->Hb_api_translate->setLanguage($this->site_lang,"en");
    }
    else
    {
      $this->Hb_api_translate->setLanguage($this->site_lang);
    }

    if($this->tank_auth->is_logged_in())
    {
      $this->user_id = $this->tank_auth->get_user_id();
    }
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


//    $this->output->enable_profiler(TRUE);
  }


  function error404()
  {
    $this->load->model('Db_country');

    $this->_searchBoxInit($data);
    $data['title'] = $this->config->item('site_title');

    $data['user_id'] = $this->user_id;

    header("HTTP/1.0 404 Not Found");
    $data['current_view_dir'] = "";
    $data['current_view']     = "error404";
    $this->load->view('includes/template',$data);
  }

  //Ajax access only
  function citylistdb()
  {
    $this->load->model('Db_hb_country');

    $this->output->enable_profiler(FALSE);

    $data['data_source_api'] = FALSE;

//    $data['cityCountryList'] = $this->Db_country->get_all_hw_cities_translated($this->site_lang);

    $data['javascript_varname'] = $this->input->get("citiesVarName", TRUE);
    if($data['javascript_varname'] === false)
    {
      $data['javascript_varname'] = 'citiesVarName';
    }

    $englishNames = FALSE;
    if(strcasecmp($this->wordpress->get_option("aj_show_encity"),"true")==0)
    {
      $englishNames = TRUE;
    }

    header('Content-type: text/xml; charset=utf-8');
    header("Pragma: public");
    header("Cache-Control: max-age=86400");
    header('Expires: ' . gmdate('D, d M Y H:i:s', time()+86400) . ' GMT');

    echo $this->Db_hb_country->get_cached_xml_cities_data($data['javascript_varname'],$this->site_lang,$englishNames);
  }


  function booking_avail()
  {
    $this->output->enable_profiler(FALSE);

    $propertyName   = $this->input->post("propertyName", TRUE);
    $propertyNumber = $this->input->post("propertyNumber",TRUE);
    $dateStart      = new DateTime($this->input->post("dateStart",TRUE));
    $numNights      = $this->input->post("numNights",TRUE);
    $currency       = $this->input->post("currency",TRUE);

    //Get main services and breakfast included
    $this->load->model('Db_hb_hostel');
    $this->load->model('i18n/db_translation_cache');
    $data['main_services'] = $this->Db_hb_hostel->get_hostel_main_services($propertyNumber);
    $data['breakfast_included'] = 0;
    if(!empty($data['main_services']))
    {
      foreach($data['main_services'] as $si => $service)
      {
        if($service->service_type == 'breakfast')
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

    $data["country_selected"] = $this->input->post("country_selected",TRUE);
    $data["city_selected"]    = $this->input->post("city_selected",TRUE);

    $data["property_cards"] = $this->input->post("propertyCards",TRUE);

    $response = $this->Hostelbookers_api->getPropertyPricingPerDate($propertyNumber,$dateStart->format('d-M-Y'),$numNights, $this->api_functions_lang, $currency);
    $userdata = array(
                   'date_selected'      => $dateStart->format('Y-m-d'),
                   'numnights_selected' => $numNights
               );

    $this->session->set_userdata($userdata);
    set_cookie('currency_selected', $currency, 2592000);
    $cookie = array('name'   => 'date_selected',
                      'value'  => $dateStart->format('Y-m-d'),
                      'expire' => $this->config->item('sess_expiration'));
    set_cookie($cookie);
    $cookie = array('name'   => 'numnights_selected',
                    'value'  => $numNights,
                    'expire' => $this->config->item('sess_expiration'));
    set_cookie($cookie);

    $data['propertyName']   = $propertyName;
    $data['propertyNumber'] = $propertyNumber;
    $data['dateStart']      = $dateStart;
    $data['numNights']      = $numNights;
    $data['currency']       = $currency;

    if($response === false)
    {
      //API unreachable
      $data["error_msg"] = _('Serveur inaccessible en ce moment.');

      $this->load->view('hb/booking_avail_error_view',$data);
      return;
    }
    elseif(isset($response["ERROR"]))
    {
      //API return error
      $data["error_msg"] = $this->Hb_api_translate->translate_text($response["ERROR"],"HB avail error msg","en");

      $this->load->view('hb/booking_avail_error_view',$data);
      return;
    }
    elseif(empty($response["RESPONSE"]))
    {
      //API return no availability
      $data["error_msg"] = _('No rooms available for the selected nights.');

      $this->load->view('hb/booking_avail_error_view',$data);
      return;
    }
    else
    {
      //API return OK
      $data['booking_rooms'] = $this->Hb_api_translate->translate_PropertyAvailability($response["RESPONSE"]);
      $this->load->view('hb/booking_avail_view',$data);
    }

  }

  function booking_process($isajax = false, $ajaxdata = NULL)
  {
    //Load for nationalities
    $this->load->model("Db_hb_country");

    //For hostel bookers settle currency is always GBP
    $settleCurrency = "GBP";

    if($isajax)
    {
      $data['booking_hostel_name'] = $ajaxdata['propertyName'];
      $propertyNumber    = $ajaxdata['propertyNumber'];
      $dateStart         = new DateTime($ajaxdata['dateStart']);
      $numNights         = $ajaxdata['numNights'];
      $roomPreferences   = $ajaxdata['roomPreferences'];
      $nbPersons         = $ajaxdata['nbPersons'];
      $bookCurrency      = $ajaxdata['bookCurrency'];
      $propertyCardTypes      = $ajaxdata['propertyCardTypes'];
    }
    else
    {
      $propertyNumber = $this->input->post('book-propertyNumber',TRUE);
      $dateStart      = new DateTime($this->input->post("book-dateStart",TRUE));
      $numNights      = $this->input->post('book-numNights',TRUE);

      $roomPreferences = $this->input->post('book-roomPreferences',TRUE);
      $nbPersons       = $this->input->post('book-nbPersons',TRUE);
      $bookCurrency    = $this->input->post('book-currency',TRUE);
      $propertyCardTypes = $this->input->post('book-property-cards',TRUE);

      $data['booking_hostel_name'] = $this->input->post('book-propertyName',TRUE);

    }
    $data['propertyCardTypes'] = explode(",",$propertyCardTypes);

    $this->load->model("Db_hb_hostel");
    //TODO translate
    $data['important_info'] = new stdClass();
    $data['important_info']->original    = $this->Db_hb_hostel->get_hostel_important_info($propertyNumber);
    $data['important_info']->translation = null;

    if(!empty($data['important_info']->original) && ($this->site_lang != 'en'))
    {
      $this->load->model('hb_api_translate');
      $data['important_info']->translation = $this->Hb_api_translate->translate_text($data['important_info']->original,"HB property important info","en",$this->site_lang);
    }

    //Get main services and breakfast included
    $this->load->model('i18n/db_translation_cache');
    $data['main_services'] = $this->Db_hb_hostel->get_hostel_main_services($propertyNumber);
    $data['breakfast_included'] = 0;
    if(!empty($data['main_services']))
    {
      foreach($data['main_services'] as $si => $service)
      {
        if($service->service_type == 'breakfast')
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

    $inputok = false;

    $data['persons_per_rooms'] = array();
    $data['booking_request'] = array();

    $data['api_error'] = true;
    $data['api_error_msg'] = _('Serveur inaccessible. Veuillez réessayer plus tard.');


    $data['property_type'] = $this->Db_hb_hostel->get_property_type($propertyNumber);

    if(isset($propertyNumber)&&!empty($propertyNumber)&&
       isset($dateStart)&&!empty($dateStart)&&
       isset($numNights)&&!empty($numNights)&&
       isset($roomPreferences)&&!empty($roomPreferences)&&
       isset($nbPersons)&&!empty($nbPersons))
    {

      $roomsIDS = "";
      foreach($roomPreferences as $i => $roomID)
      {
        if($nbPersons[$i] > 0 )
        {
          $roomsIDS.= $roomID."|";
          $data['persons_per_rooms'][$roomID] = $nbPersons[$i];
        }
      }

      $data['api_error'] = true;

      //remove last pipe  character
      if(!empty($roomsIDS)) $roomsIDS = substr($roomsIDS, 0, -1);

      $response = $this->Hostelbookers_api->getPropertyRoomPricingPerDate( $propertyNumber,
                                                                           $roomsIDS,
                                                                           $dateStart->format('d-M-Y'),
                                                                           $numNights,
                                                                           $this->api_functions_lang,
                                                                           $bookCurrency);

      $inputok = true;
      if($response === false)
      {
        $data['api_error'] = true;
        $data['api_error_msg'] = _('Serveur inaccessible. Veuillez réessayer plus tard.');
      }
      elseif(isset($response["ERROR"]))
      {
        //API return error
        $data['api_error'] = true;
        $data["api_error_msg"] = $this->Hb_api_translate->translate_text($response["ERROR"],"HB avail error msg","en");
      }
      else
      {
        if(empty($response["RESPONSE"]))
        {
          $data['api_error'] = true;
          $data['api_error_msg'] = _('No rooms available for this property.');;
        }
        else
        {
          $data['api_error'] = false;
          $data['api_error_msg'] = "";
          //Ensure array of room per date is sorted by soonest date first
//          ksort($response["RESPONSE"]);
          $this->_sort_hb_rooms_response($response["RESPONSE"]);
          $this->Hb_api_translate->translate_PropertyRoomPricingPerDate($response["RESPONSE"]);
        }

        $data['booking_request'] = $response["RESPONSE"];
      }

    }

    if($inputok===false)
    {
      $data['booking_request'] = array();
      $data['api_error'] = true;
      $data['api_error_msg'] = _('Données de réservations incomplètes.');
    }

    $data['title'] = $data['booking_hostel_name']." | ".$this->config->item('site_title');

//    debug_dump($data);
    $data['propertyNumber']   = $propertyNumber;
    $data['dateStart']        = $dateStart->format('Y-m-d');
    $data['numNights']        = $numNights;
    $data['settleCurrency']   = $settleCurrency;
    $data['bookCurrency']     = $bookCurrency;
    $data['roomPreferences']  = $roomPreferences;
    $data['nbPersons']        = $nbPersons;

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
      $data['female_count']       = $ajaxdata['female_count'];
      $data['male_count']         = $ajaxdata['male_count'];
      $data['book_arrival_time']  = $ajaxdata['book_arrival_time'];
      $data['book_email_address'] = $ajaxdata['book_email_address'];
      $data['book_phone_number']  = $ajaxdata['book_phone_number'];
      $data['sms']                = $ajaxdata['sms'];
      $data['book_sign_me_up']    = $ajaxdata['book_sign_me_up'];
      $data['mail_subscribe']     = $ajaxdata['mail_subscribe'];

      $data['book_ccname']        = $ajaxdata['book_ccname'];
      $data['book_ccnumber']      = $ajaxdata['book_ccnumber'];
      $data['book_cctype']        = $ajaxdata['book_cctype'];
      $data['book_ccexpiry_m']    = $ajaxdata['book_ccexpiry_m'];
      $data['book_ccexpiry_y']    = $ajaxdata['book_ccexpiry_y'];
      $data['book_ccexpiry_y']    = $ajaxdata['book_ccexpiry_y'];
      $data['cvv']                = $ajaxdata['cvv'];
//      $data['issueno']            = $ajaxdata['issueno'];

      $data['conditions_agree']    = $ajaxdata['conditions_agree'];
      $this->load->view($this->api_view_dir.'/booking_view',$data);
    }
    else
    {
      $this->carabiner->load_group_assets('hb_booking');

      $data['api_booking_error'] = false;
      $data['api_booking_error_msg'] = array();

      $data['book_firstname']     = "";
      $data['book_lastname']      = "";
      $data['book_nationality']   = "";
      $data['female_count']       = 0;
      $data['male_count']         = 0;
      $data['book_arrival_time']  = "11";
      $data['book_email_address'] = "";
      $data['book_phone_number']  = "";
      $data['sms']                = "none";
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

  function booking_check()
  {
    $this->output->enable_profiler(FALSE);

    $ccvalidfrom = NULL;
    if($this->input->post('ccvalidfrom_m',TRUE) != NULL)
    {
      $postdata['ccvalidfrom_m'] = $this->input->post('ccvalidfrom_m',TRUE);
      $postdata['ccvalidfrom_y'] = $this->input->post('ccvalidfrom_y',TRUE);
      $ccvalidfrom = $postdata['ccvalidfrom_m'].$postdata['ccvalidfrom_y'];
    }

    $postdata["testmode"] = $this->config->item('booking_test_mode');
    $user_id = $this->tank_auth->get_user_id();
    if(($user_id !== false))
    {
      $uprof = $this->tank_auth->get_profile($user_id);
      if($uprof->user_level_id > 1)
      {
        $postdata["testmode"] = 1;
      }
    }
    $settlecurrency = "GBP";

    $postdata['propertyCardTypes']   = $this->input->post('propertyCardTypes',TRUE);
    $postdata['propertyNumber']   = $this->input->post('propertyNumber',TRUE);
    $postdata['propertyName']     = $this->input->post('propertyName',TRUE);
    $postdata['dateStart']        = new DateTime($this->input->post('dateStart',TRUE));
    $postdata['numNights']        = $this->input->post('numNights',TRUE);
    $postdata['bookCurrency']     = $this->input->post('bookCurrency',TRUE);
    $postdata['settleCurrency']     = $settlecurrency;
    $postdata['roomPreferences']  = explode(",",$this->input->post('roomPreferences',TRUE));
    $postdata['nbPersons']        = explode(",",$this->input->post('nbPersons',TRUE));

    $postdata['propertyCards']    = $this->input->post('book-property-cards',TRUE);

    $postdata['book_firstname']     = $this->input->post('firstname',TRUE);
    $postdata['book_lastname']      = $this->input->post('lastname',TRUE);
    $postdata['book_nationality']   = $this->input->post('nationality',TRUE);
    $postdata['female_count']       = $this->input->post('female_count',TRUE);
    $postdata['male_count']         = $this->input->post('male_count',TRUE);
    $postdata['book_arrival_time']  = $this->input->post('arrival_time',TRUE);
    $postdata['book_email_address'] = $this->input->post('email_address',TRUE);
    $postdata['book_phone_number']  = $this->input->post('phone_number',TRUE);
    $postdata['sms']                = $this->input->post('sms',TRUE);
    $postdata['book_sign_me_up']    = (($this->input->post('sign_me_up',TRUE)=="true") || ($this->input->post('sign_me_up',TRUE)=="1"));
    $postdata['mail_subscribe']     = $this->input->post('mail_subscribe',TRUE)=="true";

    $postdata['CADDepositAmount']       = $this->input->post('CADDepositAmount',TRUE);

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

    $booking = $this->Hostelbookers_api->make_booking(
                                              $postdata["testmode"],
                                              $postdata['book_firstname'],
                                              $postdata['book_lastname'],
                                              $postdata['book_nationality'],
                                              $postdata['male_count'],
                                              $postdata['female_count'],
                                              $postdata['book_phone_number'],
                                              $postdata['book_email_address'],
                                              $postdata['bookCurrency'],
                                              $postdata['book_ccname'],
                                              $postdata['book_cctype'],
                                              $postdata['book_ccnumber'],
                                              $postdata['book_ccexpiry_m'].$postdata['book_ccexpiry_y'],
                                              $postdata['cvv'],
                                              $postdata['propertyNumber'],
                                              $postdata['dateStart']->format('d-M-Y'),
                                              $postdata['book_arrival_time'],
                                              $postdata['numNights'],
                                              $postdata['roomPreferences'],
                                              $postdata['nbPersons'],
                                              $this->api_functions_lang,
                                              $postdata['issueno'],
                                              $ccvalidfrom);

      $postdata['dateStart'] = $postdata['dateStart']->format('Y-m-d');

      if($booking === false)
      {

        $postdata['api_booking_error'] = "api_out";
        $postdata['api_booking_error_msg'] =  _("Serveur inaccessible pour le moment. Veuillez réessayer de nouveau.");

        $this->booking_process(true,$postdata);
      }
      elseif($booking->success == "false")
      {
        $postdata['api_booking_error'] = "api_msg";
        $postdata['api_booking_error_msg'] = $this->Hb_api_translate->translate_BookingAPIError($booking);
        $this->booking_process(true,$postdata);
      }
      else
      {
        unset($postdata['book_ccname']);
        unset($postdata['book_ccnumber']);
        unset($postdata['book_ccexpiry_m']);
        unset($postdata['book_ccexpiry_y']);
        $this->_booking_confirmation($booking,$postdata);
      }
  }

  function _booking_confirmation($booking, $ajaxdata)
  {
    $data['booking']          = $this->Hb_api_translate->translate_make_booking($booking);
    $data['booking']->CADDepositAmount->value = $ajaxdata['CADDepositAmount'];

    $data['firstname']        = $ajaxdata['book_firstname'];
    $data['lastname']         = $ajaxdata['book_lastname'];
    $data['dateStart']        = $ajaxdata['dateStart'];
    $data['numNights']        = $ajaxdata['numNights'];
    $data['book_arrival_time']  = $ajaxdata['book_arrival_time'];
    $data['propertyNumber']     = $ajaxdata['propertyNumber'];
    $data['propertyName']       = $ajaxdata['propertyName'];
    $data['bookCurrency']       = $ajaxdata['bookCurrency'];
    $data['settleCurrency']       = $ajaxdata['settleCurrency'];
    $data['isCustomCurrency']   = (strcasecmp($data['settleCurrency'],$data['bookCurrency'])!=0);
    $data['isCustomCurrencyProperty']   = (strcasecmp($booking->payment->currencies->property->currency,$data['bookCurrency'])!=0);
    $data["roomsBookedSorted"] = array();

    $this->load->model("Db_hb_hostel");

    $data['important_info']->original = $this->Db_hb_hostel->get_hostel_important_info($data['propertyNumber']);
   
    //Get main services and breakfast included
    $this->load->model('i18n/db_translation_cache');
    $data['main_services'] = $this->Db_hb_hostel->get_hostel_main_services($data['propertyNumber']);
    $data['breakfast_included'] = 0;
    if(!empty($data['main_services']))
    {
      foreach($data['main_services'] as $si => $service)
      {
        if($service->service_type == 'breakfast')
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

    if(!empty($data['important_info']->original) && ($this->site_lang != 'en'))
    {
      $this->load->model('hb_api_translate');
      $data['important_info']->translation = $this->Hb_api_translate->translate_text($data['important_info']->original,"HB property important info","en",$this->site_lang);
    }
    //Sort roomsBooked object of API response
    //Ensure array of rooms is ready for sorting.  Without this the array is not readable by usort function
    for($k=0;$k<count($data['booking']->property->roomsBooked->room);$k++)
    {
      $data["roomsBookedSorted"][$k] = $data['booking']->property->roomsBooked->room[$k];
    }

    function cmp($a, $b)
    {
      $ad = new Datetime($a->date);
      $bd = new Datetime($b->date);

      if($ad->format("U") < $bd->format("U"))
      {
        return -1;
      }
      elseif($ad->format("U") > $bd->format("U"))
      {
        return 1;
      }

      if($a->id < $b->id)
      {
        return -1;
      }
      elseif($a->id > $b->id)
      {
        return 1;
      }

      return 0;
    }

    usort($data["roomsBookedSorted"], "cmp");

    //Send confirmation email
    $this->load->library('email');
    $emailsent = FALSE;

    $emailcontent = $this->load->view('email/new_transaction_hb', $data, true);

    $this->email->from($this->config->item('admin_booking_email'),$this->config->item('site_name'));
    $this->email->to($ajaxdata['book_email_address']);

    if($this->config->item('admin_booking_conf') > 0)
    {
      $this->email->bcc($this->config->item('admin_booking_conf_email'));
    }

    $this->email->subject(sprintf(gettext('Votre réservation sur %s!'),$this->config->item('site_name'))." - #HB ".$data['booking']->bookingRef);
    $this->email->message($emailcontent);
    $emailsent = $this->email->send();

    if(! $emailsent)
    {
      log_message('error',"Error sending confirmation email to ".$ajaxdata['book_email_address']." -> ".$this->email->print_debugger());
    }

    //clear email data for next email to be sent ok
    $this->email->clear();
    $this->email->_bcc_array = array();

    //Store transaction in local databases
    $booking_time = date('Y-m-d H:i:s');
    $arrival_date_time = $data['dateStart']." ".$ajaxdata['book_arrival_time'].":00:00";

    $chargedCurrency = $booking->payment->currencyPaymentTakenIn;
    $chargedCurrencyField = strtolower($chargedCurrency);

    $trans_id = $this->Db_model->add_hb_transaction(
                        $ajaxdata["testmode"],
                        $booking_time,
                        $booking->bookingRef,
                        $ajaxdata['book_firstname'],
                        $ajaxdata['book_lastname'],
                        $ajaxdata['book_nationality'],
                        $ajaxdata['male_count'],
                        $ajaxdata['female_count'],
                        $ajaxdata['book_phone_number'],
                        $arrival_date_time,
                        $ajaxdata['propertyNumber'],
                        $ajaxdata['propertyName'],
                        $data['numNights'],
                        $booking->payment->currencies->$chargedCurrencyField->amountTaken,
                        $chargedCurrency,
                        $booking->payment->currencies->property->payableOnArrival,
                        $booking->payment->currencies->property->amountTaken,
                        $booking->payment->currencies->property->currency,
                        $ajaxdata['book_email_address'],
                        $emailsent,
                        $booking->property->roomsBooked);


    //Add user to db
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
                      'gender_id' => NULL,
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
    $this->load->model('Db_hb_hostel');
    $hb_hostel_id = $this->Db_hb_hostel->get_hostel_id($ajaxdata['propertyNumber']);
    if(!empty($hb_hostel_id))
    {
      $hbemail    = NULL;
      $hbpostCode = NULL;
      $hbphone    = NULL;
      $hbfax      = NULL;
      $hbcurrency = NULL;

      if(!empty( $booking->property->address->email          )) $hbemail    = (string) $booking->property->address->email;
      if(!empty( $booking->property->address->zip            )) $hbpostCode = (string) $booking->property->address->zip;
      if(!empty( $booking->property->address->tel            )) $hbphone    = (string) $booking->property->address->tel;
      if(!empty( $booking->property->address->fax            )) $hbfax      = (string) $booking->property->address->fax;
      if(!empty( $booking->payment->currencies->property->currency  )) $hbcurrency = (string) $booking->payment->currencies->property->currency;

      $this->Db_hb_hostel->update_hostel_booking_info($ajaxdata['propertyNumber'],
                                                      $hbemail,
                                                      $hbpostCode,
                                                      $hbphone,
                                                      $hbfax,
                                                      $hbcurrency );
    }

    $this->load->view($this->api_view_dir.'/booking_end_view',$data);

  }

  function testgb($date)
  {
    $data['book_firstname']        = "bob";
    $data['book_lastname']         = "boblast";
    $data['dateStart']        = new Datetime($date);
    $data['numNights']        = 3;
    $data['book_arrival_time']  = 11;
    $data['propertyNumber']     = 61009;
    $data['propertyName']       = "Sunny queck chose";
    $data['bookCurrency']       = "CAD";
    $data['settleCurrency']       = "GBP";

    $booking = $this->Hostelbookers_api->make_booking(
                                              1,
                                              "bob",
                                              "last",
                                              "English",
                                              5,
                                              2,
                                              "555555555",
                                              "louimichel@pweb.ca",
                                              "CAD",
                                              "bob",
                                              "Mastercard",
                                              "22222",
                                              "0912",
                                              "222",
                                              61009,
                                              $data['dateStart']->format('d-M-Y'),
                                              "11",
                                              3,
                                              explode(",","150689,150687,150692"),
                                              explode(",","3,2,2"),
                                              $this->api_functions_lang,
                                              "",
                                              "");

                                              $data['dateStart'] = $data['dateStart']->format('Y-m-d');
                                              $this->load->view('includes/header_book',$data);
                                              debug_dump($booking);
                                              exit;
     $this->_booking_confirmation($booking,$data);
     $this->load->view('includes/footer_book',$data);
  }

  function test($date)
  {
    $postdata['dateStart'] = new Datetime($date);

    $booking = $this->Hostelbookers_api->make_booking(
                                              1,
                                              "bob",
                                              "last",
                                              "English",
                                              5,
                                              2,
                                              "555555555",
                                              "louimichel@pweb.ca",
                                              "CAD",
                                              "bob",
                                              "Mastercard",
                                              "22222",
                                              "0912",
                                              "222",
                                              61009,
                                              $postdata['dateStart']->format('d-M-Y'),
                                              "11",
                                              3,
                                              explode(",","150689,150687,150692"),
                                              explode(",","3,2,2"),
                                              $this->api_functions_lang,
                                              "",
                                              "");
  $this->Hb_api_translate->setLanguage('ko');
    debug_dump($this->Hb_api_translate->translate_make_booking($booking));
    exit;
    $this->_booking_confirmation($booking, array());
  }
  function test2()
  {
    $hb_results = $this->Hostelbookers_api->getCountryLocationList();
    debug_dump($hb_results);
  }
  function _sort_hb_rooms_response(&$hbresponse)
  {
    function cmp($a, $b)
    {
      $add = substr($a[0]["NIGHT"],0,2);
      $adm = substr($a[0]["NIGHT"],3,2);
      $ady = substr($a[0]["NIGHT"],7,4);

      $bdd = substr($b[0]["NIGHT"],0,2);
      $bdm = substr($b[0]["NIGHT"],3,2);
      $bdy = substr($b[0]["NIGHT"],7,4);


      $ad = new Datetime("$ady-$adm-$add");
      $bd = new Datetime("$bdy-$bdm-$bdd");

      if($ad->format("U") < $bd->format("U"))
      {
        return -1;
      }
      elseif($ad->format("U") > $bd->format("U"))
      {
        return 1;
      }

      if($a->id < $b->id)
      {
        return -1;
      }
      elseif($a->id > $b->id)
      {
        return 1;
      }

      return 0;
    }

    usort($hbresponse, "cmp");
  }

  function _searchBoxInit(&$data)
  {
    echo "deprecated!";

    $this->carabiner->load_group_assets('search_box_scripts');

    if(!isset($data)) $data = array();

    $country   = $this->session->userdata('country_selected');
    $city      = $this->session->userdata('city_selected');
    $dateStart = $this->session->userdata('date_selected');
    $numNights = $this->session->userdata('numnights_selected');

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
  }

  function _set_tr_continent_data(&$data)
  {
    $this->load->model('Db_country');
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
