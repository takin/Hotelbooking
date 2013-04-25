<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CMain extends I18n_site
{
  var $user_id = 0;

  var $api_view_dir = "hw/";

	var $transaction_log_filename = "error_trans";

	var $assets = NULL;

	var $site_currency = "EUR";

  function CMain()
  {
    parent::I18n_site();
    $this->load->helper('cookie');
    $this->load->library('tank_auth');

    if($this->tank_auth->is_logged_in())
    {
      $this->user_id = $this->tank_auth->get_user_id();
    }

    if($this->api_used == HB_API)
    {
      $this->api_view_dir = "hb/";
    }
    else
    {
      $this->api_view_dir = "hw/";
    }
  }

  function _currency_init()
  {
    $this->load->library('get_config');
    $this->load->model('Db_currency');

    $currency_validated = $this->input->get("currency", TRUE);

	// this line is modified for providing wrong currency parameters
	if(!empty($currency_validated))
    {
		// check for wrong currency parameter validation
		 $currency_validated = $this->Db_currency->validate_currency_parameter($currency_validated);
		 if(!$currency_validated) // find wrong currency parameter
		 {
			return true;
		 }
		// $currency_validated = $this->Db_currency->validate_currency($currency_validated);  // old function call
    }
    elseif($this->tank_auth->is_logged_in())
    {
      $currency_validated = $this->config->item('site_currency_default');

      $user_id = $this->tank_auth->get_user_id();

      if($user_id !== false)
      {
        $this->load->model('Db_model');

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

    $this->site_currency =  $this->config->item("site_currency_selected");
  }

  function guarantee()
  {
    $this->load->view("guarantee");
  }

  function error($header, $view)
  {
    $data['title'] = $this->config->item('site_title');
    $data['user_id'] = $this->user_id;

    header($header);

    if($this->user_agent_mobile && !$this->user_agent_mobile_bypass)
    {
      $data['current_view_dir'] = "mobile/";
      $data['current_view'] = "error404";

      $this->load->view('mobile/includes/template',$data);
    }
    else
    {
      $this->load->model('Db_country');
	  $this->_currency_init();
      $this->_searchBoxInit($data);

      $data['current_view_dir'] = "";

      $data['current_view'] = $view;
      $this->load->view('includes/template',$data);
    }

  }

  function error404()
  {
     $this->error("HTTP/1.0 404 Not Found", "error404");
  }

  function error400()
  {
     $this->error("HTTP/1.0 400 Bad Request", "error400");
  }

  function index()
  {
    log_message('debug', 'Entering main controller index method');

    $this->_currency_init();
    $cache_time = $this->wordpress->get_option("aj_cache_time_ci_home",0);
    if(!empty($cache_time))
    {
      $this->output->cache($cache_time);
    }

    $data = array();

    if($this->api_used == HB_API)
    {
      $this->load->model('Db_hb_country');

      $this->_searchBoxInit($data);
      $this->_set_tr_continent_data($data);

      $this->load->library('hb_engine');
      $data = $this->hb_engine->map_home_page($data);

      $data['current_view_dir'] = "";
      $data['current_view']     = "home_view";
      $this->load->view('includes/template',$data);
    }
    else
    {

      if($this->user_agent_mobile && !$this->user_agent_mobile_bypass)
      {
        //disable cache for this mobile view
        $this->output->cache(0);
//        echo "mobile view to come<br>";
//        echo anchor(uri_string().'?site=full', 'Click here to view full site');

        $data['current_view_dir'] = "mobile/";
        $data['current_view'] = "home_view";

        $this->carabiner->load_group_assets('mobile_main_menu');

        $this->load->view('mobile/includes/template',$data);
      }
      else
      {
        $this->_searchBoxInit($data);
        $this->_set_tr_continent_data($data);

        $this->load->library('hw_engine');
        $data = $this->hw_engine->map_home_page($data);

        $data['current_view_dir'] = "";
        $data['current_view'] = "home_view";

        $this->load->view('includes/template',$data);
      }
    }
  }

  function site_search($terms = NULL)
  {

	$this->_currency_init();

    //THIS IS BAD for performance and does not support HB!
    $data['results'] = array();
    $data['search_term'] = $terms;
    $this->_searchBoxInit($data);

    //For sidebar plugin to move someday to better place
    $this->load->model('Db_country');

    if(!empty($terms))
    {
      $terms = customurldecode($terms);
      $data['search_term'] = $terms;
      $terms = utf8_strip_specials($terms);
      $data['search_term_stripped'] = $terms;

      if($this->api_used == HB_API)
      {
        $this->load->library('hb_engine');
        $data = $this->hb_engine->hb_site_search($data, $terms);
      }
      else
      {
        $this->load->library('hw_engine');
        $data = $this->hw_engine->hw_site_search($data, $terms);
      }
    }

    $this->carabiner->load_group_assets('search_box_scripts');
    $this->carabiner->js('mobile/suggest.js');

    $data['current_view_dir'] = "";
    $data['current_view'] = "search_results";
    $this->load->view('includes/template',$data);
  }

  function site_search_suggest($terms = NULL, $filter = 'all', $show_more_results_link = FALSE, $term_from_start = FALSE)
  {
    //allow browser cache
//    echo 'Last-Modified: '.gmdate('D, d M Y H:i:s',gmdate("U")+60).' GMT';
    $this->output->set_header('Cache-Control: public');
    $this->output->set_header('Expires: '.gmdate('D, d M Y H:i:s',gmdate("U")+86400).' GMT');

    $terms = customurldecode($terms);
    $data = array();

    if($this->api_used == HB_API)
    {
      $this->load->library('hb_engine');
      $data = $this->hb_engine->hb_site_search_suggest($terms, $filter, $show_more_results_link, $term_from_start);
      $this->load->view('search_suggest',$data);
    }
    else
    {
      $this->load->library('hw_engine');
      $data = $this->hw_engine->hw_site_search_suggest($terms, $filter, $show_more_results_link, $term_from_start);
      $this->load->view('search_suggest',$data);
    }
  }

  /*
   * mobile search
   */
  function mobile_search()
  {
    //Before activating cache on this page changes are needed in view to preload input with cookies data
//    $cache_time = $this->wordpress->get_option("aj_cache_time_ci_home",0);
//    $cache_time = 3600;
//    if(!empty($cache_time))
//    {
//      $this->output->cache($cache_time);
//    }

    $this->_currency_init();

    $this->carabiner->load_group_assets('mobile_main_menu');
    $this->carabiner->load_group_assets('mobile_fancy_box');
    $this->carabiner->js('mobile/suggest.js');

    $default_date = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
    $data['day_selected'] = date("d",$default_date);
    $data['month_year_selected'] = date("Y-m",$default_date);
    $data['numnights_selected'] = 2;

    $dateStart      = $this->input->cookie("date_selected",TRUE);
    if(!empty($dateStart))
    {
      $data['month_year_selected'] = substr($dateStart,0,7);
      $data['day_selected'] = substr($dateStart,8,2);
    }

    $numNights      = $this->input->cookie("numnights_selected",TRUE);
    if(!empty($numNights))
    {
      $data['numnights_selected'] = $numNights;
    }

    $countrySelected   = $this->input->cookie("country_selected",TRUE);
    $citySelected      = $this->input->cookie("city_selected",TRUE);

    $data['custom_url'] = "";
    $data['city_country'] = "";
    if(!empty($countrySelected) && !empty($citySelected) && !empty($dateStart) && !empty($numNights) )
    {
      $data['custom_url'] = site_url($countrySelected."/".$citySelected);
      $data['city_country'] = "$citySelected, $countrySelected";
    }

    $data['current_view_dir'] = "mobile/";
    $data['current_view'] = "search_view";

    $this->load->view('mobile/includes/template',$data);
  }

  function mobile_avail_check($propertyNumber, $dateStart = NULL, $numNights = NULL)
  {
    if(empty($propertyNumber))
    {
      $this->error404();
      return;
    }
    $this->_currency_init();

    $data = array();

    $propertyName   = $this->input->post("propertyName", TRUE);
    $propertyCity   = $this->input->post("propertyCity", TRUE);
    $propertyCountry = $this->input->post("propertyCountry",TRUE);

    if(empty($dateStart))
    {
      $dateStart      = $this->input->cookie("date_selected",TRUE);
      if(empty($dateStart))
      {
       $dateStart = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
      }
    }

    if(empty($numNights))
    {
      $numNights      = $this->input->cookie("numnights_selected",TRUE);
      if(empty($numNights))
      {
        $numNights = 2;
      }
    }
    set_cookie('currency_selected', $this->site_currency, 2592000);
    set_cookie('date_selected',$dateStart,$this->config->item('sess_expiration'));
    set_cookie('numnights_selected',$numNights,$this->config->item('sess_expiration'));

    $this->carabiner->load_group_assets('mobile_main_menu');


    if($this->api_used == HB_API)
    {

    }
    else
    {
      if(empty($propertyName))
      {
        $this->load->model('Db_hw_hostel');
        $hostel_data = $this->Db_hw_hostel->get_hostel_data_from_number($propertyNumber);
        if(!empty($hostel_data))
        {
          $propertyName = $hostel_data->property_name;

          $this->load->model('Db_hw_city');
          $propertyCity = $this->Db_hw_city->get_hw_city_by_id($hostel_data->hw_city_id, $this->site_lang);
          $propertyCountry = $propertyCity->country_name_translated;
          $propertyCity = $propertyCity->city_name_translated;

        }
      }

      $this->load->library('hw_engine');
      $data = $this->hw_engine->property_avail_check($propertyName, $propertyNumber, $dateStart, $numNights, $this->site_currency);
      if($data['api_error']==FALSE)
      {
        $data["distinctRoomTypes"] = $this->hw_engine->prepare_distinct_rooms($data['booking_info'], $data['distinctRoomTypes'], $numNights);
      }

      $data['propertyCity'] = $propertyCity;
      $data['propertyCountry'] = $propertyCountry;

      //Needed in view select currency
      $this->load->model('Db_currency');

      $data['current_view_dir'] = "mobile/hw/";
      $data['current_view'] = "booking_avail_view";

      $this->load->view('mobile/includes/template',$data);
    }
  }

  function mobile_booking_confirmation()
  {
    $data = array();

    $propertyNumber = $this->input->post('book-propertyNumber',TRUE);
    $dateStart      = $this->input->post('book-dateStart',TRUE);
    $numNights      = $this->input->post('book-numNights',TRUE);

    $roomPreferences = $this->input->post('book-roomPreferences',TRUE);
    $nbPersons       = $this->input->post('book-nbPersons',TRUE);
    $bookCurrency    = $this->input->post('book-currency',TRUE);

    $settleCurrency  = $this->input->post('settle-currency',TRUE);
    if(empty($settleCurrency))
    {
      $settleCurrency  = $this->input->cookie('settle_currency',TRUE);
      if(empty($settleCurrency))
      {
        $settleCurrency = settle_currency_filter($bookCurrency,$this->config->item('site_settle_currency_default'));
      }
    }

    $bsid = $this->session->userdata('BSID_'.$propertyNumber);
    if($bsid==false) $bsid = NULL;

    $this->carabiner->load_group_assets('mobile_main_menu');

    if(isset($propertyNumber)&&!empty($propertyNumber)&&
       isset($dateStart)&&!empty($dateStart)&&
       isset($numNights)&&!empty($numNights)&&
       isset($roomPreferences)&&!empty($roomPreferences)&&
       isset($nbPersons)&&!empty($nbPersons))
    {
      if($this->api_used == HB_API)
      {

      }
      else
      {
        $this->load->library('hw_engine');
        $data = $this->hw_engine->booking_request( $propertyNumber,
                                                   $dateStart,
                                                   $numNights,
                                                   $roomPreferences,
                                                   $nbPersons,
                                                   $settleCurrency,
                                                   $bsid,
                                                   $bookCurrency);

        if($data['api_error'] === TRUE)
        {
          $data['current_view_dir'] = "mobile/hw/";
          $data['current_view'] = "booking_confirm_error";

          $this->load->view('mobile/includes/template',$data);
        }
        else
        {
          $data['booking_hostel_name'] = $this->input->post('book-propertyName');
          $data['propertyCity']    = $this->input->post('propertyCity');
          $data['propertyCountry'] = $this->input->post('propertyCountry');

          $data['current_view_dir'] = "mobile/hw/";
          $data['current_view'] = "booking_confirm";

          $this->load->view('mobile/includes/template',$data);
        }
      }
    }
    else
    {
      $data['booking_request'] = array();
      $data['api_error'] = true;
      $data['api_error_msg'] = _('Données de réservations incomplètes.');

      $data['current_view_dir'] = "mobile/hw/";
      $data['current_view'] = "booking_confirm_error";

      $this->load->view('mobile/includes/template',$data);
    }

  }

  function mobile_booking($isAjax = FALSE, $data = array())
  {

    if($this->api_used == HB_API)
    {

    }
    else
    {
      //Initialization
      if(empty($data))
      {
        $this->load->library('hw_engine');
        $this->hw_engine->booking_data_init($data);

        $user_id = $this->tank_auth->get_user_id();

        if($user_id!= false)
        {
         $this->load->model("Db_model");
         $user_info = array();
         $user_info = $this->Db_model->get_user_profile($user_id);

         $data['book_email_address'] = $user_info['email'];
         $data['book_email_address2'] = $user_info['email'];
         $data['book_gender']        = $this->Db_model->get_gender_value($user_info['gender_id']);
         $data['book_firstname']     = $user_info['first_name'];
         $data['book_lastname']      = $user_info['last_name'];
         $data['book_phone_number']  = $user_info['phone_number'];
         $data['book_nationality']   = $user_info['home_country'];
        }
      }

      $data["isAjax"] = $isAjax;

      $this->load->model('Db_country');
      $this->load->helper('form_elements');

      $data['current_view_dir'] = "mobile/hw/";
      $data['current_view'] = "booking_view";
      if($isAjax)
      {
        $this->load->view($data['current_view_dir'].$data['current_view'],$data);
      }
      else
      {
        $this->carabiner->load_group_assets('mobile_main_menu');
        $this->carabiner->load_group_assets('jquery');
        $this->carabiner->load_group_assets('formvalidation');
        $this->carabiner->js('mobile/booking_action.js');

        $this->load->view('mobile/includes/template',$data);
      }
    }

  }

  function mobile_booking_try()
  {
    if($this->api_used == HB_API)
    {

    }
    else
    {
      $this->load->library('form_validation');
      $this->form_validation->set_rules('firstname', _('Prénom'), 'trim|required|min_length[2]|max_length[100]|xss_clean');
      $this->form_validation->set_rules('lastname', _('Nom'), 'trim|required|min_length[2]|max_length[100]|xss_clean');
      $this->form_validation->set_rules('nationality', _('Nationalité :'), 'trim|required|xss_clean');
      $this->form_validation->set_rules('gender', _("Sexe:"), 'trim|required|xss_clean');
      $this->form_validation->set_rules('arrival_time', _("Heure d'arrivée:"), 'trim|required|less_than[24]|xss_clean');
      $this->form_validation->set_rules('email', _("Adresse Email"), 'trim|required|valid_email|xss_clean');
      $this->form_validation->set_rules('email2', _("Confirmation Email"), 'trim|required|matches[email]|valid_email|xss_clean');
      $this->form_validation->set_rules('phone_number', _('Téléphone :'), 'trim|required|alpha_numeric|xss_clean');
      $this->form_validation->set_rules('cctype', sprintf(gettext("Type de carte : %s(acceptée par l'établissement)"),""), 'trim|required|xss_clean');
      $this->form_validation->set_rules('ccname', sprintf(gettext('sur la carte %s de crédit :'),""), 'trim|required|alpha_numeric|xss_clean');
      $this->form_validation->set_rules('ccnumber', _('Numéro')." ".sprintf(gettext('de la carte %s de crédit :'),""), 'trim|required|numeric|xss_clean');
      $this->form_validation->set_rules('ccexpiry_m', _("Date d'expiration :"), 'trim|required|alpha_numeric|xss_clean');
      $this->form_validation->set_rules('ccexpiry_y', _("Date d'expiration :"), 'trim|required|alpha_numeric|xss_clean');
      $this->form_validation->set_rules('cvv', _("Code de sécurité :"), 'numeric|xss_clean');
      $this->form_validation->set_rules('conditions_agree', _("Terms and conditions agreement"), 'callback_condition_check');

      if($this->form_validation->run() == FALSE)
      {
         $this->load->library('hw_engine');
         $data = $this->hw_engine->booking_data_init();
         $this->mobile_booking(true,$data);
      }
      else
      {
        $this->load->library('hw_engine');

        $data = $this->hw_engine->booking_payment();

        if($data["booking_data"]["booking_status"] == "OK")
        {
          $this->mobile_booking_complete($data);
        }
        else
        {
          $this->mobile_booking(true,$data["booking_data"]);
        }
      }

    }
  }

  function mobile_booking_complete($data)
  {
    if($this->api_used == HB_API)
    {

    }
    else
    {
      $data['dateStart_calculated']  = $data["booking"]->RoomDetails[0]->date;
      $data['numNights_calculated'] = $this->Hostel_api_model->count_numnights($data["booking"]->RoomDetails);
      $data['isCustomCurrency']   = (strcasecmp($data["booking_data"]['settleCurrency'],$data["booking_data"]['bookCurrency'])!=0);
      $data['bookAmountDueField'] = $data["booking_data"]['bookCurrency']."AmountDue";

      $data['current_view_dir'] = "mobile/hw/";

      //Opertions to get compatible with former confirmation email view
      $data["booking_data"]['propertyName']  = (string) $data['booking']->PropertyDetails->propertyName;
      $data = array_merge($data,$data["booking_data"]);

      $data['firstname']        = $data['book_firstname'];
      $data['lastname']         = $data['book_lastname'];

      //Send confirmation email
      $this->load->library('email');
      $emailsent = FALSE;

      $emailcontent = $this->load->view('email/new_transaction', $data, true);
      $this->email->from($this->config->item('admin_booking_email'),$this->config->item('site_name'));
      $this->email->to($data['book_email_address']);

      if($this->config->item('admin_booking_conf') > 0)
      {
        $this->email->bcc($this->config->item('admin_booking_conf_email'));
      }

      $this->email->subject(sprintf(gettext('Votre réservation sur %s!'),$this->config->item('site_name'))." - #HW ".$data['booking']->CustomerReference->value);
      $this->email->message($emailcontent);
      $emailsent = $this->email->send();

      if(! $emailsent)
      {
        log_message('error',"Error sending confirmation email to ".$data["booking_data"]['book_email_address']." -> ".$this->email->print_debugger());
      }

      //clear email data for next email to be sent ok
      $this->email->clear();
      $this->email->_bcc_array = array();

     //Send confirmation email to property (not anymore)
      $prop_emailsent = NULL;

      //Store transaction in local databases
      $booking_time = date('Y-m-d H:i:s');
      $arrival_date_time = $data['dateStart_calculated']." ".$data["booking_data"]['book_arrival_time'].":00:00";

      $property_currency = NULL;
      if($data['booking']->PropertyDetails->currency)
      {
        $property_currency = $data['booking']->PropertyDetails->currency;
      }
      $this->load->model('Db_currency');
      $this->load->model('Db_model');
      $trans_id = $this->Db_model->add_hw_transaction(
                          $data["booking_data"]["testmode"],
                          $booking_time,
                          $data['booking']->CustomerReference->value,
                          $data["booking_data"]['book_firstname'],
                          $data["booking_data"]['book_lastname'],
                          $data["booking_data"]['book_nationality'],
                          $data["booking_data"]['book_gender'],
                          $data["booking_data"]['book_phone_number'],
                          $arrival_date_time,
                          $data["booking_data"]['propertyNumber'],
                          $data["booking_data"]['propertyName'],
                          $data['numNights_calculated'],
                          $data['booking']->AmountCharged->value,
                          (string)$data['booking']->ChargedCurrency->value,
                          $data['booking']->PropertyAmountDue->value,
                          $property_currency,
                          $data["booking_data"]['book_email_address'],
                          $emailsent,
                          $prop_emailsent,
                          $data['booking']->RoomDetails,
                          1);


      //SMS send or delay set
      if(isset($data["booking_data"]['sms']) && ($data["booking_data"]['sms'] != 'none'))
      {
        if(is_numeric($data["booking_data"]['sms']))
        {
          $this->load->model('Db_sms');
          $this->Db_sms->add_sms($trans_id, 0, $data["booking_data"]['sms']);
        }
        elseif($data["booking_data"]['sms'] == 'now')
        {
          $this->load->library('clickatell');
          $this->load->model('Db_sms');

          $msg_id = NULL;
          $error_number = NULL;
          $status = 0;
          $sms_text = $this->load->view('mobile/sms_view', $data, true);
          try
          {
            $msg_id = $this->clickatell->send_single_sms($data["booking_data"]['book_phone_number'],$sms_text);
            $status = 1;
          }
          catch(Exception $e)
          {
            $error_number = substr($e->getMessage(), 5, 3);
            log_message('error',"SMS sending failed for transaction #$trans_id and phone number ".$data["booking_data"]['book_phone_number']." -> ".$e->getMessage());
          }

          $this->Db_sms->add_sms($trans_id, $status, NULL, $msg_id, $error_number);
        }
      }
      //Add hostel booking data to DB if hostel is cached
      $this->load->model('Db_hw_hostel');
      $hw_hostel_id = $this->Db_hw_hostel->get_hostel_id($data["booking_data"]['propertyNumber']);
      if(!empty($hw_hostel_id))
      {
        $hwemail    = NULL;
        $hwpostCode = NULL;
        $hwstate    = NULL;
        $hwphone    = NULL;
        $hwfax      = NULL;
        $hwcurrency = NULL;
        $hwdeposit  = NULL;

        if(!empty( $data['booking']->PropertyDetails->email          )) $hwemail    = (string) $data['booking']->PropertyDetails->email;
        if(!empty( $data['booking']->PropertyDetails->postCode       )) $hwpostCode = (string) $data['booking']->PropertyDetails->postCode;
        if(!empty( $data['booking']->PropertyDetails->state          )) $hwstate    = (string) $data['booking']->PropertyDetails->state;
        if(!empty( $data['booking']->PropertyDetails->phone          )) $hwphone    = (string) $data['booking']->PropertyDetails->phone;
        if(!empty( $data['booking']->PropertyDetails->fax            )) $hwfax      = (string) $data['booking']->PropertyDetails->fax;
        if(!empty( $data['booking']->PropertyDetails->currency       )) $hwcurrency = (string) $data['booking']->PropertyDetails->currency;
        if(!empty( $data['booking']->PropertyDetails->depositPercent )) $hwdeposit  = floatval ($data['booking']->PropertyDetails->depositPercent);

        $this->Db_hw_hostel->update_hostel_booking_info($data["booking_data"]['propertyNumber'],
                                                        $hwemail,
                                                        $hwpostCode,
                                                        $hwstate,
                                                        $hwphone,
                                                        $hwfax,
                                                        $hwcurrency,
                                                        $hwdeposit );
      }

      //Sign up user if email not already in user table
      if(TRUE)
      {
        //Create user with random password and send email
        if($this->tank_auth->is_email_available($data["booking_data"]['book_email_address']))
        {
          $user_data = $this->tank_auth->create_user('', $data["booking_data"]['book_email_address'], random_string('alnum', $this->config->item('generate_password_length', 'tank_auth')), $this->config->item('email_activation', 'tank_auth'));

          //Send welcome email data
          $user_data['site_name'] = $this->config->item('site_name');
          $this->_send_email('welcome', $user_data['email'], $user_data);
          unset($user_data['password']); // Clear password (just for any case)

          $profile = array(
                        'first_name' => $data["booking_data"]['book_firstname'],
                        'last_name' => $data["booking_data"]['book_lastname'],
                        'gender_id' => $this->Db_model->get_gender_id($data["booking_data"]['book_gender']),
                        'phone_number' => $data["booking_data"]['book_phone_number'],
                        'home_country' => $data["booking_data"]['book_nationality'],
                        'mail_subscription' => $data["booking_data"]['mail_subscribe'],
                        'favorite_currency' => $this->Db_currency->get_currency_id($data["booking_data"]['bookCurrency']),
                        );
          $this->tank_auth->set_profile($user_data['user_id'],$profile);
        }
        else
        {
          //this email is already register to login click here:
          //$data['login_warning'] = _("Ce courriel possède déja un compte.");
        }

      }
    }
    $data['current_view'] = "booking_end_view";
    $this->load->view($data['current_view_dir'].$data['current_view'],$data);
  }

  function mobile_map()
  {
    $title = $this->input->get("title", TRUE);
    $lat   = $this->input->get("lat", TRUE);
    $long  = $this->input->get("lng", TRUE);

    $data = array();

    if($this->api_used == HB_API)
    {

    }
    else
    {
//       debug_dump($data["property"]);
      $data["latlng"] = "$lat, $long";
      $data["codeAddress"] = "";
      $data["title"] = urldecode($title);
//       if(!empty($data["property"]))
//       {
//         $data["latlng"] = $data["property"]->geo_latitude .", ".$data["property"]->geo_longitude;

//         if(empty($data["property"]->geo_latitude) || empty($data["property"]->geo_longitude))
//         {
//           $data["codeAddress"] = "put address here";
//         }
//       }
    }
    $this->carabiner->js('gmap.js');
    $data['current_view_dir'] = "mobile/";
    $data['current_view'] = "map_view";
    $this->load->view('mobile/includes/template',$data);
  }

  function booking($isAjax = FALSE, $data = array())
  {
    exit;
    /*
    if($this->api_used == HB_API)
    {

    }
    else
    {
      //Initialization
      if(empty($data))
      {
        $this->load->library('hw_engine');
        $this->hw_engine->booking_data_init($data);

        $user_id = $this->tank_auth->get_user_id();

        if($user_id!= false)
        {
         $this->load->model("Db_model");
         $user_info = array();
         $user_info = $this->Db_model->get_user_profile($user_id);

         $data['book_email_address'] = $user_info['email'];
         $data['book_email_address2'] = $user_info['email'];
         $data['book_gender']        = $this->Db_model->get_gender_value($user_info['gender_id']);
         $data['book_firstname']     = $user_info['first_name'];
         $data['book_lastname']      = $user_info['last_name'];
         $data['book_phone_number']  = $user_info['phone_number'];
         $data['book_nationality']   = $user_info['home_country'];
        }


        $data = $this->hw_engine->booking_request($propertyNumber,
                                                  $dateStart,
                                                  $numNights,
                                                  $roomPreferences,
                                                  $nbPersons,
                                                  $settleCurrency,
                                                  $bsid,
                                                  $bookCurrency);
      }

      $data["isAjax"] = $isAjax;

      $this->load->model('Db_country');
      $this->load->helper('form_elements');

      $data['current_view_dir'] = "mobile/hw/";
      $data['current_view'] = "booking_view";
      if($isAjax)
      {
        $this->load->view($data['current_view_dir'].$data['current_view'],$data);
      }
      else
      {
        $this->carabiner->load_group_assets('mobile_main_menu');
        $this->carabiner->load_group_assets('jquery');
        $this->carabiner->load_group_assets('formvalidation');
        $this->carabiner->js('mobile/booking_action.js');

        $this->load->view('mobile/includes/template',$data);
      }
    }
    */
  }

//   function booking_try()
//   {

//   }

//   function booking_complete()
//   {

//   }

  function continent_country_page($continent, $country = NULL)
  {

	$this->_currency_init();

    if(!empty($country)&&!empty($continent))
    {
      $this->country_page($continent, $country);
    }
    elseif(!empty($continent))
    {
      $this->continent_page($continent);
    }
    else
    {
      $this->error404();
    }
  }

  function continent_page($continent)
  {

    $cache_time = $this->wordpress->get_option("aj_cache_time_country_pages",0);
    if(!empty($cache_time))
    {
      $this->output->cache($cache_time);
    }

    $this->_searchBoxInit($data);

    if($this->api_used == HB_API)
    {
      $this->load->library('hb_engine');
      $data = $this->hb_engine->continent_data($data, $continent);
    }
    else
    {
      $this->load->library('hw_engine');
      $data = $this->hw_engine->continent_data($data, $continent);
    }

    $data['current_view_dir'] = "";
    $data['current_view'] = "continent_view";
    $this->load->view('includes/template',$data);
  }

  function country_page($continent, $country)
  {

    $cache_time = $this->wordpress->get_option("aj_cache_time_country_pages",0);
    if(!empty($cache_time))
    {
      $this->output->cache($cache_time);
    }

    $this->_searchBoxInit($data);

    if($this->api_used == HB_API)
    {
      $this->load->library('hb_engine');
      $data = $this->hb_engine->country_data($data, $continent, $country);
    }
    else
    {
      $this->load->library('hw_engine');
      $data = $this->hw_engine->country_data($data, $continent, $country);
    }

    $data['current_view_dir'] = "";
    $data['current_view'] = "country_view";
    $this->load->view('includes/template',$data);
  }

  function group_request()
  {

	$this->load->model('Db_country');

    $data['country_selected'] = $this->uri->segment(2,null);
    $data['city_selected']    = $this->uri->segment(3,null);

    $data['meta_desc'] = _('Group Booking');
    if(!empty($data['country_selected']) && !empty($data['city_selected']))
    {
      $data['meta_desc'].= " - ".my_mb_ucfirst($data['city_selected']).", ".my_mb_ucfirst($data['country_selected']);
    }
    elseif(!empty($data['country_selected']))
    {
      $data['meta_desc'].= " - ".my_mb_ucfirst($data['country_selected']);
    }
    $data['meta_desc'].= " - "._('If you are looking for a hostel, a Youth hostel, a hotel, a B&B or a cheap acommodation for your group, we are here to help.');

    $this->carabiner->js('date-lib.js');
    $this->carabiner->js('group_request.js');
    $this->carabiner->js('search_box.js');
    $this->carabiner->load_group_assets('formvalidation');

    $data['title'] = _('Group Booking') ." | ".$this->config->item('site_title');

    $data['current_view_dir'] = "";
    $data['current_view'] = "group_request";
    $this->load->view('includes/template',$data);
  }

  function property_search($country = NULL, $city = NULL, $urldate = NULL, $units = NULL)
  {
    log_message('debug', 'Entering main controller property_search method');

	$currency_error = false; // default currency paramete is correct
	$currency_error = $this->_currency_init();

    if(empty($country))
    {
      //Redirect to home page
      redirect();
    }
    elseif(empty($city))
    {
      //Display home page
      $this->error404();
      return;
    }
	elseif($currency_error) // add the currency parameter provided was wrong
	{
		//Display error page
      $this->error400();
      return;
	}
    else
    {
      $dateStart = NULL;
      $numNights = NULL;

      $chkdate = $this->checkData($urldate);
      if(($chkdate == true) && (!empty($urldate)))
      {
        $dateStart = $urldate;

      }
      if((is_numeric($units)) && (!empty($units)))
      {
        $numNights = $units;
	  }

      $filter = array("landmark" => NULL,
                      "district" => NULL,
                      "type"     => NULL);

      $url_segment_3 = strtolower($this->uri->segment(3));

      if(!empty($url_segment_3))
      {
        switch($url_segment_3)
        {
          case 'landmark':
            $dateStart = NULL;
            $numNights = NULL;
            $filter["landmark"] = $this->Db_links->get_translation_link_term($this->uri->segment(4));
            break;
          case 'district':
            $dateStart = NULL;
            $numNights = NULL;
            $filter["district"] = $this->uri->segment(4);
            break;
          case 'type':
            $dateStart = NULL;
            $numNights = NULL;
            $filter["type"] = $this->Db_links->get_property_type_term(
                urldecode($this->uri->segment(4)),$this->site_lang);
            break;
        }
      }

      $url_segment_5 = strtolower($this->uri->segment(5));
      if(!empty($url_segment_5))
      {
        switch($url_segment_5)
        {
          case 'landmark':
            $dateStart = NULL;
            $numNights = NULL;
            $filter["landmark"] = $this->Db_links->get_translation_link_term($this->uri->segment(6));
            break;
          case 'district':
            $dateStart = NULL;
            $numNights = NULL;
            $filter["district"] = $this->uri->segment(6);
            break;
          case 'type':
            $dateStart = NULL;
            $numNights = NULL;
            $filter["type"] = $this->Db_links->get_property_type_term(
                urldecode($this->uri->segment(6)),$this->site_lang);
            break;
        }
      }
      if($this->api_used == HB_API)
      {
	    $this->load->helper('domain_replace_helper');
        $this->load->library('hb_engine');
        $data = $this->hb_engine->location_search(
            $country, $city, $dateStart, $numNights, FALSE, TRUE, $filter);
        if($data === FALSE)
        {
          //cancel any caching
          $this->output->cache(0);
          $this->error404();
          return;
        }

        $this->carabiner->load_group_assets('search_box_scripts');
        $this->carabiner->js('avail_rooms.js');
        $this->carabiner->js('property_images.js');

        $data['current_view_dir'] = "";
        $data['current_view'] = "city_view";

        if(empty($dateStart))
        {
          $data['current_view'] = "city_lp";
          $this->load->view('includes/template-landing-city-page',$data);
        }
        else
        {
          $data["filters_init"] = $this->_init_filters();

          $this->carabiner->js('pweb/includes/jorder-1.2.1.js','pweb/includes/jorder-1.2.1-min.js');
          $this->carabiner->js('pweb/includes/mustache.js');

          $this->carabiner->js('pweb/jlibs/GroupCheckBoxes.js');
          $this->carabiner->js('save_property.js');
          $this->carabiner->js('pweb-mapping/PropertyFilters.js');
          $this->carabiner->js('pweb/libs/GoogleMap.js');
		   $this->carabiner->js('properties_compare.js');
		   $this->carabiner->js('compare_property.js');

          $this->load->view('includes/template',$data);
        }
      }
      else
      {
	    $this->load->helper('domain_replace_helper');
        $this->load->library('hw_engine');

        if($this->user_agent_mobile && !$this->user_agent_mobile_bypass)
        {
          $data = $this->hw_engine->location_search($country, $city, $dateStart, $numNights, true);

          if($data === FALSE)
          {
            //cancel any caching
            $this->output->cache(0);
            $this->error404();
            return;
          }
          $this->load->library('mobile');

          $default_date = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
          $data['day_selected'] = date("d",$default_date);
          $data['month_year_selected'] = date("Y-m",$default_date);
          $data['numnights_selected'] = 2;

          $data['current_view_dir'] = "mobile/";
          $data['current_view'] = "city_view";

          $this->carabiner->load_group_assets('mobile_main_menu');
          $this->carabiner->load_group_assets('mobile_city_property_list');

          $this->load->view('mobile/includes/template',$data);
        }
        else
        {
          $data = $this->hw_engine->location_search(
                $country, $city, $dateStart, $numNights, false, true, $filter);
          if($data === FALSE)
          {
            //cancel any caching
            $this->output->cache(0);
            $this->error404();
            return;
          }
          $this->carabiner->load_group_assets('search_box_scripts');
          $this->carabiner->js('avail_rooms.js');
          $this->carabiner->js('property_images.js');

          $data['current_view_dir'] = "";
          $data['current_view'] = "city_view";


          if(empty($dateStart))
          {
            $data['current_view'] = "city_lp";
            $this->load->view('includes/template-landing-city-page',$data);
          }
          else
          {
            $data["filters_init"] = $this->_init_filters();

            $this->carabiner->js('pweb/includes/jorder-1.2.1.js','pweb/includes/jorder-1.2.1-min.js');
            $this->carabiner->js('pweb/includes/mustache.js');

            $this->carabiner->js('pweb/jlibs/GroupCheckBoxes.js');
            $this->carabiner->js('save_property.js');
            $this->carabiner->js('pweb-mapping/PropertyFilters.js');
            $this->carabiner->js('pweb/libs/GoogleMap.js');
			$this->carabiner->js('properties_compare.js');
		    $this->carabiner->js('compare_property.js');

            $this->load->view('includes/template',$data);
          }
        }
      }
    }
  }

  function reviews_map($property_id, $max_review_count = 3)
  {
        //allow browser cache  24 hours
    $this->output->set_header('Cache-Control: public');
    $this->output->set_header('Expires: '.gmdate('D, d M Y H:i:s',gmdate("U")+86400).' GMT');
    if($this->api_used == HB_API)
    {
      $this->load->library('hb_engine');
      $data = $this->hb_engine->property_reviews($property_id,$max_review_count);
      if($data["review_count"] > 0)
      {
        $this->_sort_reviews($data["user_reviews"]);

        $this->load->view("hb/reviews-map",$data);
      }
      else
      {
        $this->load->view("review_empty-map");
      }
    }
    else
    {

      $this->load->library('hw_engine');
      $data = $this->hw_engine->property_reviews($property_id, true, $max_review_count);

      if($data["review_count"] > 0)
      {
        $this->_sort_reviews($data["user_reviews"]);
        $this->load->view("hw/reviews-map",$data);
      }
      else
      {
        $this->load->view("review_empty-map");
      }
    }
  }
 /*
 * Set cookies for last review properites
 * @access private
 * @param property id
 */
 function _property_recently_view($property_id) {
	if (!isset($_COOKIE['last_review_property'])) { //-- check if user first time viewing the property
		$property_cookie = array(
			'name'   => 'last_review_property',
			'value'  => $property_id,
			'expire' => time() + 1209600,
			'path'   => '/'
		);

		set_cookie($property_cookie); // set cookies name as array  and will expire in 2 weeks
		return;
	}
	else {
		$cookieArray = explode(",", $_COOKIE['last_review_property']);//-- the propery id is already in cookie

		if (in_array($property_id,  $cookieArray)) {
			return TRUE; // property is already in cookie string
		}

		// --- check we have already number of cookies set------////
		if (count($cookieArray) >= $this->config->item('recent_view_number_cookies')) {
			// remove the last one
			array_pop($cookieArray);
			// insert the new one at the beginning
			array_unshift($cookieArray, $property_id);

			$new_cookie_array = implode(',', $cookieArray); // make the array as comma seperated string

			$property_cookie = array(
				'name'   => 'last_review_property',
				'value'  =>  $new_cookie_array,
				'expire' => time() + 1209600,
				'path'   => '/'
			);

			set_cookie($property_cookie); // set cookies name as array  and will expire in 2 weeks
		}
		else {
			$get_last_cookie = $_COOKIE['last_review_property']; // so get last commad seperated values

			$property_cookie = array(
				'name'   => 'last_review_property',
				'value'  => $property_id . ',' . $get_last_cookie, // set it by comma seperated
				'expire' => time() + 1209600,
				'path'   => '/'
			);

			set_cookie($property_cookie); // set cookies name as array  and will expire in 2 weeks
		}
	}
 }


  /*
 * Remove cookie from the recently reviwed properties
 * @access private
 * @param property id
 */
 function ajax_review_remove_cookie() {
	if (!$this->input->post('property_id')) {
		echo json_encode(array('status'=>false));
		return false;
	}

	// converted cookies string to array
	$cookieArray = explode(",", $_COOKIE['last_review_property']);

	if (in_array($this->input->post('property_id'), $cookieArray)) {
		foreach($cookieArray as $key => $value) { // loop to remove the proper property from cooki
			if ($value == $this->input->post('property_id')) { // propery id match in the cookies
				unset($cookieArray[$key]);
			}
		}

		$cookieArray = array_values($cookieArray);

		$new_cookie_array = implode(',', $cookieArray); // make the array as comma seperated string

		$new_cookie_array = ltrim($new_cookie_array, ','); // just remove the first empty comma
		$new_cookie_array = rtrim($new_cookie_array, ','); // just remove the last empty comma

		// make new cookies array/////////////
		$property_cookie = array(
			'name'   => 'last_review_property',
			'value'  =>  $new_cookie_array,
			'expire' => time() + 1209600,
			'path'   => '/'
		);

		set_cookie($property_cookie); // set cookies name as array  and will expire in 2 weeks

		echo json_encode(array('status' => true)); // cookies succesfully removed
		return TRUE;
	}

	echo json_encode(array('status' => false)); // cookie not present return false
	return false;
 }


  function property_reviews($property_id)
  {
    //allow browser cache  24 hours
    $this->output->set_header('Cache-Control: public');
    $this->output->set_header('Expires: '.gmdate('D, d M Y H:i:s',gmdate("U")+86400).' GMT');

	if($this->api_used == HB_API)
    {
      $this->load->library('hb_engine');
      $data = $this->hb_engine->property_reviews($property_id);

      if($data["review_count"] > 0)
      {
        $this->_sort_reviews($data["user_reviews"]);
        $this->load->view("hb/reviews",$data);
      }
      else
      {
        $this->load->view("review_empty");
      }
    }
    else
    {
      $this->load->library('hw_engine');
      $data = $this->hw_engine->property_reviews($property_id);

      if($data["review_count"] > 0)
      {
        $this->_sort_reviews($data["user_reviews"]);

        //If our reviews > 0 means that view title is already present
        if($data["our_review_count"] > 0)
        {
          $data["isAjax"] = true;
        }

        $this->load->view("hw/reviews",$data);
      }
      elseif($data["our_review_count"] == 0)
      {
        $this->load->view("review_empty");
      }
    }
  }

  function property_page($property_type, $property_name = "", $property_number = NULL, $urldate = NULL, $units = NULL)
  {
    log_message('debug', 'Entering main controller property page method');

    $this->_currency_init();
    // add the current params in the "stash"
    $data = array(
        'property_type'   => $property_type,
        'property_name'   => $property_name,
        'property_number' => $property_number,
        'date'            => $urldate,
        'nights'          => $units,
        'print'           => $this->input->get('print', true),
        'showEmail'       => $this->config->item('displayShareEmail'),
        'showPDF'         => $this->config->item('displaySharePDF')
    );

    $date = $urldate;

    if (!empty($date)) {
        set_cookie('date_selected', $date,$this->config->item('sess_expiration'));
        if (!empty($nights) && is_numeric($nights)) {
            set_cookie('numnights_selected', $nights,$this->config->item('sess_expiration'));
        }
    }


    $district_umid = NULL;
     
    if(empty($property_number))
    {
      $this->error404();
      return;
    }

    $chkdate = $this->checkData($urldate);
    if(($chkdate == true) && (!empty($urldate)))
    {
      set_cookie('date_selected',$urldate,$this->config->item('sess_expiration'));
    }
    if((is_numeric($units)) && (!empty($units)))
    {
      set_cookie('numnights_selected',$units,$this->config->item('sess_expiration'));
	}

    // create an empty to avoid notice when no landmarks are found
    $data['landmarks'] = array();

    // create an empty to avoid notice when no districts are found
    $data['district_info'] = array();

    $force = $this->input->get('groupbkg',true);
    if(($force == 'A') && ($this->api_used === HW_API))
    {
      $this->api_used = HB_API;
      $this->api_view_dir = "hb/";
      $this->api_forced = true;
    }
    if(($force == 'B') && ($this->api_used === HB_API))
    {
      $this->api_used = HW_API;
      $this->api_view_dir = "hw/";
      $this->api_forced = true;
    }

      $this->load->model('i18n/db_translation_cache');

          if($this->api_used == HB_API)
    {
      $this->load->model('Db_hb_hostel');

      //get District details
       $data['district_info'] = $this->Db_hb_hostel->get_property_districts( $property_number );

       // Second parameter is a range in KM
       $data['landmarks'] = $this->Db_hb_hostel->get_property_landmarks_for_filter($property_number, 2);

    }
    else
        {
         $this->load->model('Db_hw_hostel');

      //get District details
       $data['district_info'] = $this->Db_hw_hostel->get_property_districts( $property_number );

       // Second parameter is a range in KM
       $data['landmarks'] = $this->Db_hw_hostel->get_property_landmarks_for_filter($property_number, 2);

        }
            // get district if exist and translate them
            if (!empty($data['district_info']))
              {


              foreach ($data['district_info'] as $i => $district)
                  {
                  $data['district_info'][$i]->original_name = $district->district_name;
                  $data['district_info'][$i]->um_id = $district->um_id;

                  $translation = $this->db_translation_cache->get_translation($district->district_name, $this->site_lang);

                  if (!empty($translation))
                    {
                      $data['district_info'][$i]->district_name = $translation->translation;

                    }
                    else
                    {
                          $data['district_info'][$i]->district_name = $district->district_name;
                    }
                  }
              }

              // get landmarks if exist and translate them
             if (!empty($data['landmarks']))
              {

              foreach ($data['landmarks'] as $i => $landmark)
                  {
                  $data['landmarks'][$i]->original_name = $landmark->landmark_name;

                  $translation = $this->db_translation_cache->get_translation($landmark->landmark_name, $this->site_lang);

                  if (!empty($translation))
                    {
                      $data['landmarks'][$i]->landmark_name = $translation->translation;

                    }
                    else
                    {
                          $data['landmarks'][$i]->landmark_name = $landmark->landmark_name;
                    }
                  }
              }

//     $this->hostel_controller = "chostelbk";
    if($this->api_used == HB_API)
    {
       $this->_property_recently_view($property_number); // set cookies for last reviewed
      //Check if property requested is HW property
      $this->load->model('Db_hw_hostel');
      $poperty_requested_hw = $this->Db_hw_hostel->get_hostel_data_from_number($property_number);

      //IF property is a HW property redirect
      if(!empty($poperty_requested_hw->property_name) && strcasecmp(url_title($poperty_requested_hw->property_name),$property_name)==0)
      {
        //IF HB city is available redirect to HB city landing page
        $this->load->model('Db_hw_city');
        $hw_city = $this->Db_hw_city->get_hw_city_by_id($poperty_requested_hw->hw_city_id);

        $this->load->model('Db_hb_country');
        $hb_eq_city = $this->Db_hb_country->get_city($hw_city->country_name, $hw_city->city_name,$this->site_lang);
        $hb_eq_country = $this->Db_hb_country->get_country($hw_city->country_name);

        if(!empty($hb_eq_city))
        {
          redirect("/".$hb_eq_city->display_country."/".$hb_eq_city->display_city,'location', 301);
        }
        //IF HB country is available redirect to HB country page
        elseif(!empty($hb_eq_country))
        {
          $country_field   = "country_".$this->site_lang;
          $continent_field = "continent_".$this->site_lang;
          redirect("/".$hb_eq_country->$continent_field."/".$hb_eq_country->$country_field,'location', 301);
        }
        //ELSE redirect to CI homepage
        else
        {
          $base_ci_url = $this->wordpress->get_option('aj_api_search');
          if(!empty($base_ci_url))
          {
            redirect($base_ci_url,'location', 301);
          }
          redirect('/','location', 301);
        }
      }

      $this->_new_review_form_process($data);
      $this->_searchBoxInit($data);

      $this->load->library('hb_engine');
      try
      {
        $data = $this->hb_engine->property_info($data,$property_number);
      }
      catch(Exception $e)
      {
        $this->error404();
        return;
      }

      if ($this->input->get('comment', TRUE) == "insert")
      {
        $this->carabiner->load_group_assets('formvalidation');
        $this->carabiner->js('jquery.rating.js');
      }

      $this->carabiner->js('avail_check.js');

      $data['current_view_dir'] = $this->api_view_dir;
      $data['current_view'] = "hostel_view";

      $this->load->view('includes/template-property',$data);
    }
    else
    {
       $this->_property_recently_view($property_number); // set cookies for last reviewed

      //Check if property requested is HB property
//       $this->load->model('Db_hb_hostel');
//       $poperty_requested_hb = $this->Db_hb_hostel->get_hostel_data($property_number);
      //     debug_dump($poperty_requested_hw);
      //     debug_dump($poperty_requested_hb);

      //     debug_dump(strcasecmp(url_title($poperty_requested_hb->property_name),$property_name));

      //Mobile
      if($this->user_agent_mobile && !$this->user_agent_mobile_bypass)
      {
        $this->load->library('hw_engine');

        try
        {
          $data = $this->hw_engine->property_info($data,$property_number);
        }
        catch(Exception $e)
        {
          $this->error404();
          return;
        }

        $this->load->library('mobile');

        $data['current_view_dir'] = "mobile/".$this->api_view_dir;
        $data['current_view'] = "hostel_view";

        $this->carabiner->load_group_assets('mobile_main_menu');
        $this->carabiner->load_group_assets('mobile_fancy_box');

        $this->load->view('mobile/includes/template',$data);
      }
      //Full site
      else
      {
        $this->_new_review_form_process($data);
        $this->_searchBoxInit($data);
        $this->load->library('hw_engine');
        try
        {
          $this->hw_engine->property_info($data,$property_number);
        }
        catch(Exception $e)
        {
          $this->error404();
          return;
        }

        if ($this->input->get('comment', TRUE) == "insert")
        {
          $this->carabiner->load_group_assets('formvalidation');
          $this->carabiner->js('jquery.rating.js');
        }
        $this->carabiner->js('avail_check.js');

        $data['current_view_dir'] = $this->api_view_dir;
        $data['current_view'] = "hostel_view";

        $this->load->view('includes/template-property',$data);
      }

    }

    //cancel any caching is api was forced
    if($this->api_forced === true)
    {
      $this->output->cache(0);
    }
  }

  // send email and pdf callback
  function property_send_email() {
    $to_email   = $this->input->post('to_email', true);
    $subject    = $this->input->post('subject', true);
    $message    = $this->input->post('message', true);
    $from_name  = $this->input->post('from_name', true);
    $from_email = $this->input->post('from_email', true);
    $subscribe  = $this->input->post('subscribe', true);
    $with_pdf   = $this->input->post('with_pdf', true);
    $property_type   = $this->input->post('property_type', true);
    $property_name   = $this->input->post('property_name', true);
    $property_number = $this->input->post('property_number', true);

    $date   = $this->input->post('date', true);
    $nights = $this->input->post('nights', true);


    $this->load->helper('email');

    $errors = array();

    if (empty($property_type) || empty($property_name) || empty($property_number)) {
        $errors[] = _('Not enough parameters');
    }

    if (empty($to_email)) {
        $errors[] = _('Fill in the email');
    }
    else {
        if (!valid_email($to_email)) {
            $errors[] = _('Invalid email recipient');
        }
    }
    if (empty($subject)) {
        $errors[] = _('Fill in the subject');
    }
    if (empty($message)) {
        $errors[] = _('Fill in the message');
    }
    if (empty($from_name)) {
        $errors[] = _('Fill in the "From" name');
    }
    if (empty($from_email)) {
        $errors[] = _('Fill in the "From" email');
    }
    else {
        if (!valid_email($from_email)) {
            $errors[] = _('Invalid "From" email');
        }
    }

    if (!empty($errors)) {
        $this->load->view('includes/template-json', array(
            'json_data' => json_encode(array(
                'ok'     => false,
                'errors' => $errors 
            ), true)
        ));

        return;
    }

    $this->load->library('email');

    $pdf_path = null;
    $temp_dir = null;

    // don't run this under windows and if it's not required
    if ($with_pdf && !ISWINDOWS) {
	$bookingTableSelect = $this->input->cookie('bookingTableSelect', TRUE);

        $string = $this->config->item('site_name') . ' ' . $property_name;

        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
                   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                   "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = preg_replace("/[^a-zA-Z0-9]/", "", $clean);

	// fallback
        $temp_dir = $this->config->item('temp_dir');
        $temp_dir = empty($temp_dir) ? '/tmp' : $this->config->item('temp_dir');

	$temp_dir = rtrim($temp_dir, '/') . '/dir_' . uniqid();

	// make the temp dir	
	mkdir($temp_dir, 0700);

        $pdf_path = $temp_dir . '/' . $string . '.pdf';

	$cookie_append = '';
	$append = '';
	if (!empty($date)) {
		$append .= '/' . $date;
		$cookie_append .= ' --cookie date_selected ' . escapeshellarg($date);

		if (!empty($nights) && is_numeric($nights)) {
			$append .= '/' . (int)$nights;
			$cookie_append .= ' --cookie numnights_selected ' . escapeshellarg((int)$nights);
		}
	}

	$commandCookies = empty($bookingTableSelect) ? '' : ' --cookie bookingTableSelect ' . escapeshellarg($bookingTableSelect);
	$commandCookies .= $cookie_append;

	$command = '/usr/bin/xvfb-run -a -s "-screen 0 640x480x16" /usr/bin/wkhtmltopdf --redirect-delay 10000 --quiet --ignore-load-errors -l ' . $commandCookies . ' ' . escapeshellarg( site_url("/{$property_type}/{$property_name}/{$property_number}{$append}") . '?print=pdf' ) . ' ' . escapeshellarg($pdf_path). ' > /dev/null 2>&1';

	log_message('debug', $command);

        // create PDF
	system($command);

        if (file_exists($pdf_path)) {
            $this->email->attach($pdf_path);
        }
    }

    $data = array(
       'to_email'   => $to_email,
       'subject'    => $subject,
       'message'    => $message,
       'from_name'  => $from_name,
       'from_email' => $from_email,
       'property_type'   => $this->input->post('property_type', true),
       'property_name'   => $this->input->post('property_name', true),
       'property_number' => $this->input->post('property_number', true),
       'date'       => $date,
       'nights'     => $nights,
       'site_name'  => $this->config->item('site_name'),
    );

    $this->email->from($from_email, $from_name);
    $this->email->reply_to($this->config->item('email_users_admin'), $this->config->item('site_name'));

    $this->email->to($to_email);
    $this->email->subject($subject);
    $this->email->message($this->load->view('email/share_page-html', $data, TRUE));
    $this->email->set_alt_message($this->load->view('email/share_page-txt', $data, TRUE));
    $this->email->send();

    if ($pdf_path) {
        unlink($pdf_path);
	rmdir($temp_dir);
    }

    $this->load->view('includes/template-json', array(
        'json_data' => json_encode(array(
            'ok' => true
        ), true)
    ));
  }

  /*
   * ajax_location_avail function to update location available properties list by ajax
   */
  function ajax_location_avail($country = NULL, $city = NULL, $dateStart = NULL, $numNights = NULL) {
    if (empty($country)||empty($city)||empty($dateStart)||empty($numNights)) {
      return false;
    }

    $this->_currency_init();

    $this->load->model('Db_favorite_hostels');
    $savedPropertiesNumbers = $this->Db_favorite_hostels->savedPropertiesNumbers(13);

    if ($this->api_used == HB_API) {
      $this->load->library('hb_engine');

      $data = $this->hb_engine->location_search($country, $city, $dateStart, $numNights, TRUE);
      if (!empty($data['property_list']) && is_array($data['property_list'])) {
          foreach ($data['property_list'] as $index => $property) {
              $data['property_list'][$index]['savedToFavorites'] = !empty($savedPropertiesNumbers[ $property['id'] ]);
          }
      }

      $data = $this->hb_engine->location_json_format($data);
    }
    else {
      $this->load->library('hw_engine');

      $data = $this->hw_engine->location_search($country, $city, $dateStart, $numNights, TRUE);
      if (!empty($data['property_list']) && is_array($data['property_list'])) {
          foreach ($data['property_list'] as $index => $property) {
              $data['property_list'][$index]['savedToFavorites'] = !empty($savedPropertiesNumbers[ $property['id'] ]);
          }
      }

      $data = $this->hw_engine->location_json_format($data);
    }

    $this->load->view('includes/template-json', $data);
  }

   /*
   * ajax_location_avail function to update location available properties list by ajax
   */
  function ajax_recently_viewed_property() {
    if ($this->api_used == HB_API) {
        $this->load->model('Db_hb_hostel');
    }
    else {
        $this->load->model('Db_hw_hostel');
    }

    $this->load->view('includes/recent_property_view_cookie');
  }

  function property_rooms_avail()
  {
//     $this->output->set_header('Content-Type: text/html; charset=utf-8');
    $this->output->set_header('Cache-Control: public');
    //7200 sec = 2 hours
    $this->output->set_header('Expires: '.gmdate('D, d M Y H:i:s',gmdate("U")+7200).' GMT');
    $data = array();

    $propertyName   = $this->input->post("propertyName", TRUE);
    $propertyNumber = $this->input->post("propertyNumber",TRUE);
    $dateStart      = $this->input->post("dateStart",TRUE);
    $numNights      = $this->input->post("numNights",TRUE);
    $currency       = $this->input->post("currency",TRUE);
    set_cookie('currency_selected', $currency, 2592000);
    set_cookie('date_selected',$dateStart,$this->config->item('sess_expiration'));
    set_cookie('numnights_selected',$numNights,$this->config->item('sess_expiration'));

    if($this->api_used == HB_API)
    {
      $dateStart = new DateTime($dateStart);

      $this->load->library('hb_engine');
      $data = $this->hb_engine->property_avail_check($propertyName, $propertyNumber, $dateStart, $numNights, $currency);
//TODO on error display error!!!
      if(empty($data['error_msg']))
      {
        $data["property_rooms"] = $this->hb_engine->prepare_rooms($data['booking_rooms'],$numNights);
        $data["property_api"] = 'HB';
        unset($data['booking_rooms']);
      }
      $this->load->view('property_rooms_avail',$data);
    }
    else
    {
      $this->load->library('hw_engine');
      $data = $this->hw_engine->property_avail_check($propertyName, $propertyNumber, $dateStart, $numNights, $currency);

      if($data['api_error']==FALSE)
      {
        $data["property_rooms"] = $this->hw_engine->prepare_distinct_rooms($data['booking_info'], $data['distinctRoomTypes'], $numNights, FALSE);
        $data["property_api"] = 'HW';
        //unset($data['distinctRoomTypes']);
        unset($data['booking_info']->Rooms);
      }
      $this->load->view('property_rooms_avail',$data);
    }
  }

  public function property_images()
  {
    $propertyNumber = $this->input->post("propertyNumber",TRUE);
    $propertyName = $this->input->post("propertyName",TRUE);

    if($this->api_used == HB_API)
    {
      $this->load->library('hb_engine');
      $data = $this->hb_engine->property_images($propertyNumber);
    }
    else
    {
      $this->load->library('hw_engine');
      $data = $this->hw_engine->property_images($propertyNumber);
    }
    $data['property_number'] = $propertyNumber;
    $data['property_name'] = $propertyName;
//     debug_dump($data);
    $this->load->view('property_images',$data);
  }

  public function property_infos($propertyNumber)
  {
    $propertyNumber = $this->input->post("propertyNumber",TRUE);
    echo "amenities $propertyNumber";
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

  function _new_review_form_process(&$data)
  {
    $form_sent = $this->input->post('comment-submit',true);
    if(!empty($form_sent))
    {
      $this->load->library('form_validation');
      //hotel comments
      $this->form_validation->set_rules('firstname', _('Prénom'), 'trim|required|min_length[2]|max_length[100]|xss_clean');
      $this->form_validation->set_rules('lastname', _('Nom'), 'trim|required|min_length[2]|max_length[100]|xss_clean');
      $this->form_validation->set_rules('email', _('Courriel'), 'trim|required|xss_clean|valid_email');
      $this->form_validation->set_rules('nationality', _('Nationalité'), 'trim|required|xss_clean');
      $this->form_validation->set_rules('comment', _('Commentaires'), 'trim|required|xss_clean');
      $this->form_validation->set_rules('star-rating', _('Évaluation'), 'trim|xss_clean');
      $this->form_validation->set_rules('month_comment', _('Mois'), 'trim|required|xss_clean');
      $this->form_validation->set_rules('year_comment', _('Année'), 'trim|required|xss_clean');

       // on validation not ok
      if ($this->form_validation->run() == FALSE)
      {
        ;
      }
      // on validation ok
      else
      {
        $rating = NULL;
        if($this->input->post('star-rating')!=false)
        {
          $rating = $this->input->post('star-rating');
        }
        $this->load->model('Db_reviews');

        $commentID = $this->Db_reviews->add_property_review(
            $this->input->post('email'),
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
            $_SERVER['HTTP_USER_AGENT']);

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

          $this->load->library('email');

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

        $data['warning']         = true;
        $data['warning_message'] = _('Votre commentaire a été envoyé à notre équipe pour approbation. Merci!');
      }
    }
  }

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

    if($dateStart!=false)
    {
      $data['date_selected'] = $dateStart;
    }
    if($numNights!=false)
    {
      $data['numnights_selected'] = $numNights;
    }

    if($country!=false)
    {
      $data['country_selected'] = $country;
    }
    if($city!=false)
    {
      $data['city_selected'] = $city;
    }
  }


  function checkData($mydate) {
    if((!empty($mydate)) && ((strpos($mydate,"-")) > 0)){

      $split = explode('-', $mydate);
      if(is_array($split) && count($split) == 3)
      {
        $yy = $split[0];
        $mm = $split[1];
        $dd = $split[2];

       if (is_numeric($yy) && is_numeric($mm) && is_numeric($dd))
       {
         return true;
       }
     }
     return false;
    }
  }



  function condition_check($str)
  {
    if(!empty($str) && ($str=='true'))
    {
      return TRUE;
    }
    $this->form_validation->set_message('condition_check', _("Accept terms and conditions agreement to proceed"));
    return FALSE;
  }
/*
   *
   */
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

  function test()
  {
    $this->error404();
    exit;
  }

  function test_sms()
  {
    $this->error404();
    exit;
  }

  public function _init_filters()
  {
    $district = $this->input->get("di", TRUE);
    $landmark = $this->input->get("la", TRUE);
    $category = $this->input->get("cat", TRUE);

    $filters = array();
    $filters["type"]["all"]        = "checked=\"checked\"";
    $filters["type"]["hostels"]    = "checked=\"checked\"";
    $filters["type"]["hotels"]     = "checked=\"checked\"";
    $filters["type"]["apartments"] = "checked=\"checked\"";
    $filters["type"]["bbs"]        = "checked=\"checked\"";
    $filters["type"]["campings"]   = "checked=\"checked\"";

    $filters["landmark"]["id"]   = 0;
    $filters["district"]["id"]   = 0;

    if(!empty($category))
    {
      switch(strtolower($category))
      {
        case "hostel":
          $filters["type"]["all"]        = "";
          $filters["type"]["hostels"]    = "checked=\"checked\"";
          $filters["type"]["hotels"]     = "";
          $filters["type"]["apartments"] = "";
          $filters["type"]["bbs"]        = "";
          $filters["type"]["campings"]   = "";
          break;
        case "hotel":
          $filters["type"]["all"]        = "";
          $filters["type"]["hostels"]    = "";
          $filters["type"]["hotels"]     = "checked=\"checked\"";
          $filters["type"]["apartments"] = "";
          $filters["type"]["bbs"]        = "";
          $filters["type"]["campings"]   = "";
          break;
        case "apartment":
          $filters["type"]["all"]        = "";
          $filters["type"]["hostels"]    = "";
          $filters["type"]["hotels"]     = "";
          $filters["type"]["apartments"] = "checked=\"checked\"";
          $filters["type"]["bbs"]        = "";
          $filters["type"]["campings"]   = "";
          break;
        case "guesthouse":
          $filters["type"]["all"]        = "";
          $filters["type"]["hostels"]    = "";
          $filters["type"]["hotels"]     = "";
          $filters["type"]["apartments"] = "";
          $filters["type"]["bbs"]        = "checked=\"checked\"";
          $filters["type"]["campings"]   = "";
          break;
        case "campsite":
          $filters["type"]["all"]        = "";
          $filters["type"]["hostels"]    = "";
          $filters["type"]["hotels"]     = "";
          $filters["type"]["apartments"] = "";
          $filters["type"]["bbs"]        = "";
          $filters["type"]["campings"]   = "checked=\"checked\"";
          break;
        default:
          $filters["type"]["all"]        = "checked=\"checked\"";
          $filters["type"]["hostels"]    = "checked=\"checked\"";
          $filters["type"]["hotels"]     = "checked=\"checked\"";
          $filters["type"]["apartments"] = "checked=\"checked\"";
          $filters["type"]["bbs"]        = "checked=\"checked\"";
          $filters["type"]["campings"]   = "checked=\"checked\"";
          break;
      }
    }

    if(!empty($landmark))
    {
      $filters["landmark"]["id"]   = $landmark;
    }
    if(!empty($district))
    {
      $filters["district"]["id"]   = $district;
    }

    return $filters;
  }
  
  
  //property detail page start
 function ajax_property_detail($property_number,$numnight,$allproids,$currency)
 {	
    $this->_currency_init();	
 	$this->layout= null;
 	$data = array();
    $alldata = array();
	// create an empty to avoid notice when no landmarks are found
    $locationdata['landmarks'] = array();

    // create an empty to avoid notice when no districts are found
    $locationdata['district_info'] = array();
	
	$data['property_number'] = $property_number;
	$data['currency'] = $currency;
   
    $this->load->model('i18n/db_translation_cache');

          if($this->api_used == HB_API)
	    {
	       $this->load->model('Db_hb_hostel');

	      //get District details
	       $locationdata['district_info'] = $this->Db_hb_hostel->get_property_districts( $property_number );

	       // Second parameter is a range in KM
	       $locationdata['landmarks'] = $this->Db_hb_hostel->get_property_landmarks_for_filter($property_number, 2);

	    }
    	else
        {
           $this->load->model('Db_hw_hostel');

      //get District details
       $locationdata['district_info'] = $this->Db_hw_hostel->get_property_districts( $property_number );

       // Second parameter is a range in KM
       $locationdata['landmarks'] = $this->Db_hw_hostel->get_property_landmarks_for_filter($property_number, 2);

        }
            // get district if exist and translate them
            if (!empty($locationdata['district_info']))
              {
              foreach ($locationdata['district_info'] as $i => $district)
                  {
                  $locationdata['district_info'][$i]->original_name = $district->district_name;
                  $locationdata['district_info'][$i]->um_id = $district->um_id;

                  $translation = $this->db_translation_cache->get_translation($district->district_name, $this->site_lang);

                  if (!empty($translation))
                    {
                      $locationdata['district_info'][$i]->district_name = $translation->translation;
                    }
                    else
                    {
                      $locationdata['district_info'][$i]->district_name = $district->district_name;
                    }
                  }
              }

              // get landmarks if exist and translate them
             if (!empty($locationdata['landmarks']))
              {

              foreach ($locationdata['landmarks'] as $i => $landmark)
                  {
                  $locationdata['landmarks'][$i]->original_name = $landmark->landmark_name;

                  $translation = $this->db_translation_cache->get_translation($landmark->landmark_name, $this->site_lang);

                  if (!empty($translation))
                    {
                      $locationdata['landmarks'][$i]->landmark_name = $translation->translation;
                    }
                    else
                    {
                      $locationdata['landmarks'][$i]->landmark_name = $landmark->landmark_name;
                    }
                  }
              }
   	$dateStart = '';
	if($this->api_used == HB_API)
    {   
        $this->load->library('hb_engine');
	    $data['current_view_dir'] = $this->api_view_dir;
		$this->load->model('db_hb_hostel');
		if(isset($_COOKIE['date_selected'])){
		$dateStart=  $_COOKIE['date_selected'];		
		}	
		$dateStart1 = new DateTime($dateStart);
        	$alldata = $this->hb_engine->property_info($data,$property_number);
		$details['hostel'] = $alldata['hostel'];
		$details['property_ratings'] = $alldata['hostel']['RATING'];
      		$details['propertyextras_included'] = $alldata['hostel']['PROPERTYEXTRAS_included'];
		$details['propertyextras_included_translated'] = $alldata['hostel']['PROPERTYEXTRAS_included_translated'];
		$details['features_translated'] = $alldata['hostel']['FEATURES_translated'];
		$details['hostel_min_price'] = $alldata['hostel_min_price'];
		$data = $this->hb_engine->property_avail_check('',$property_number,$dateStart1,$numnight,$currency);
		$data['property_rooms'] = @$this->hb_engine->prepare_rooms($data['booking_rooms'],$numnight);	    
		$data['propertyurl'] =$this->next_property_url($alldata['hostel']['TYPE'],$alldata['hostel']['NAME'],$property_number,$this->site_lang) ;
		$data['user_reviews']=$this->hb_engine->property_reviews($property_number);
		$data['numNights']=$numnight;
		$data['dateStart']=$dateStart1;
		 
		if(!empty($locationdata)) {
			$data = array_merge($data,$locationdata);
		}		
		if(!empty($details)) {
			$data = array_merge($data,$details);
		}
		 //$filter_array = $this->get_property_details($allproids);
		 $filter_array = $this->get_property_details($property_number);
	}else{
		$this->load->model('db_hw_hostel');
		$this->load->library('hw_engine');
		if(isset($_COOKIE['date_selected'])){
		$dateStart=  $_COOKIE['date_selected'];
		}
		$dateStart1 = new DateTime($dateStart);
		$alldata = $this->hw_engine->property_info($data,$property_number);
		$details['hostel'] = $alldata['hostel'];
		$details['property_ratings'] = $alldata['hostel']->rating;
		$details['hostel_min_price'] = $alldata['hostel_min_price'];
		$data = $this->hw_engine->property_avail_check('',$property_number,$dateStart,$numnight,$currency);
				
		$data["property_rooms"] = $this->hw_engine->prepare_distinct_rooms($data['booking_info'], $data['distinctRoomTypes'], $numnight, FALSE);
		$data['propertyurl'] =$this->next_property_url($details['hostel']->property_type,$details['hostel']->property_name,$property_number,$this->site_lang) ;
		$data['user_reviews1']=$this->hw_engine->property_reviews($property_number);
		$data['numNights']=$numnight;
		$data['dateStart']=$dateStart1;
		if(!empty($locationdata)) {
			$data = array_merge($data,$locationdata);
		}	
		if(!empty($details)) {
			$data = array_merge($data,$details);
		}
		// set data to add to marker
		//$filter_array = $this->get_property_details($allproids);
		$filter_array = $this->get_property_details($property_number);
	}

		// mark that is a quick view
		$data['quick_view'] = true;

		$jsondata = array();
		$jsondata['map_data'] = $filter_array ;
		$jsondata['html'] = $this->load->view("property_detail",$data,true);
		
		echo json_encode($jsondata);
 }
 //property detail page end
 
 //next property url function
  function next_property_url($propertytype,$propertyname,$propertyid,$site_lag)
  {
  	$this->load->model('db_links');
	$nextpropertyurl = $this->Db_links->build_property_page_link($propertytype,$propertyname,$propertyid,$site_lag);
	return $nextpropertyurl;
  }
  
  // Get data for add marker popup to be used with google map.
 function get_property_details($proids)
 {
 	$proid=explode(",",$proids);
	$data = array();
	$filter_array = array();
	$images = array();
	if($this->api_used == HB_API)
    	{   
		 	$this->load->model('db_hb_hostel');
			$this->load->library('hb_engine');
		 	foreach($proid as $key=>$property_number)
			{   $data['property_number'] = $property_number;
				$data = $this->hb_engine->property_info($data,$property_number);
				$images = $this->hb_engine->property_images($property_number);
				$data['propertyurl'] = $this->next_property_url($data['hostel']['TYPE'],$data['hostel']['NAME'],$property_number,$this->site_lang) ;
				$filter_array[$key]["Geo"]["Latitude"] = $data['hostel']['GPS']['LAT'];
				 $filter_array[$key]["Geo"]["Longitude"] = $data['hostel']['GPS']['LON'];
				 $filter_array[$key]["PropertyImages"]["PropertyImage"]["imageThumbnailURL"] = $images['thumbnails']['0'];
				 $filter_array[$key]["property_page_url"] = $data['propertyurl'];
				 $filter_array[$key]["display_price_formatted"]  = $data['hostel_min_price'];
				 $filter_array[$key]["propertyNumber"]  = $property_number;
				 $filter_array[$key]["propertyName"]  = $data['hostel']['NAME'];
				 $filter_array[$key]["overall_rating"]  = str_replace('%','',$data['hostel']['RATING']);				
			}
        }else{
        	
			$this->load->model('db_hw_hostel');
			$this->load->library('hw_engine');
			foreach($proid as $key=>$property_number)
			{    $data = $this->hw_engine->property_info($data,$property_number);
				 $images = $this->hw_engine->property_images($property_number);
			     $data['propertyurl'] =$this->next_property_url($data['hostel']->property_type,$data['hostel']->property_name,$property_number,$this->site_lang);
      			 $filter_array[$key]["Geo"]["Latitude"] = $data['hostel']->geolatitude;
				 $filter_array[$key]["Geo"]["Longitude"] = $data['hostel']->geolongitude;
				 $filter_array[$key]["PropertyImages"]["PropertyImage"]["imageThumbnailURL"] = @$images['thumbnails']['0'];
				 $filter_array[$key]["property_page_url"] = $data['propertyurl'];
				 $filter_array[$key]["display_price_formatted"]  = $data['hostel_min_price'];
				 $filter_array[$key]["propertyNumber"]  = $property_number;
				 $filter_array[$key]["propertyName"]  = $data['hostel']->property_name;
				 $filter_array[$key]["overall_rating"]  = $data['hostel']->rating;
			}
	   }
	   
	return $filter_array;
 }
 
 function ajax_compare_property_data($proid)
 {
	 if($this->api_used == HB_API)
    	{
		 	$this->load->model('db_hb_hostel');
			$result=$this->db_hb_hostel->compare_property_info($proid);
			$propertylink=$this->next_property_url($result['property_type'],$result['property_name'],$proid,$this->site_lang);
			$propertydata="<a href='".$propertylink."'>".$result['property_name']."<strong> ("._($result['property_type']).")<strong></a>";
        }else{
			$this->load->model('db_hw_hostel');
  			$result=$this->db_hw_hostel->compare_property_info($proid);
			$propertylink=$this->next_property_url($result['property_type'],$result['property_name'],$proid,$this->site_lang);
			$propertydata="<a href='".$propertylink."'>".$result['property_name']."<strong> ("._($result['property_type']).")<strong></a>";	
	    }	
	echo $propertydata;
 }
 
 function ajax_property_compare($proid)
 {
	$proid1=explode(",",$proid);
	$cookiepropertydata='';
	if($this->api_used == HB_API)
    	{
		 	$this->load->model('db_hb_hostel');
		 	for($i=0;$i<count($proid1);$i++)
			{
				$result=$this->db_hb_hostel->compare_cookie_property($proid1[$i]);
				$propertylink=$this->next_property_url($result['property_type'],$result['property_name'],$proid1[$i],$this->site_lang);
				$protype=_($result["property_type"]);
				$propertydata="<a href='".$propertylink."'>".$result['property_name']. " <strong>(".$protype.")</strong></a>";
				$cookiepropertydata.='<div id=property_'.$proid1[$i].' class="show-data"><div class="show-data-first-colum">'.$propertydata.'</div><div class="show-data-last-colum"><a href="javascript:void(0)" onclick="remove_pro('._($proid1[$i]).');">X</a></div><input type="hidden" name="property_id[]" id="property_id_'.$proid1[$i].'" value="'.$proid1[$i].'"/></div>';
			}
        }else{ 
			$this->load->model('db_hw_hostel');
			for($i=0;$i<count($proid1);$i++)
			{
      			$result=$this->db_hw_hostel->compare_cookie_property_hw($proid1[$i]);
				$propertylink=$this->next_property_url($result['property_type'],$result['property_name'],$proid1[$i],$this->site_lang);
				$protype=_($result["property_type"]);
				$propertydata="<a href='".$propertylink."'>".$result['property_name']. " <strong>(".$protype.")</strong></a>";
				$cookiepropertydata.='<div id=property_'.$proid1[$i].' class="show-data"><div class="show-data-first-colum">'.$propertydata.'</div><div class="show-data-last-colum"><a href="javascript:void(0)" onclick="remove_pro('.$proid1[$i].');">X</a></div><input type="hidden" name="property_id[]" id="property_id_'.$proid1[$i].'" value="'.$proid1[$i].'"/></div>';
			}
	    }
	echo $cookiepropertydata;
 }
 
 //compare property function
  function ajax_compare_property($pro_id)
  {
	$data=array();
	if($this->api_used == HB_API)
    {
			$this->load->model('db_hb_hostel');
			$data['property_extra']=$this->db_hb_hostel->property_extra();
			$data['property_feature']=$this->db_hb_hostel->property_feature();
		    $proid=explode(",",$pro_id);
			$compare_data=array();
			for($i=0;$i<count($proid);$i++){
				$result2='';
				$result4='';
				$result6='';
				$result=$this->db_hb_hostel->compare_property($proid[$i]);
				$result1=$this->db_hb_hostel->compare_property_extra($result[0]->property_number);
				$result3=$this->db_hb_hostel->compare_property_feature($result[0]->property_number);
				$result5=$this->db_hb_hostel->compare_property_image($result[0]->property_number);
				$property_url=$this->next_property_url($result[0]->property_type,$result[0]->property_name,$result[0]->property_number,$this->site_lang);
				foreach($result[0] as $key => $value)
				{
					$result6[$key]=$value;
				}
				foreach($result1 as $extra)
				{
					$result2[]=$extra->hb_extra_id;
				}
				foreach($result3 as $feat)
				{
					$result4[]=$feat->hb_feature_id;
				}
				$propertyimg=$this->property_image($result[0]->property_number);
				$compare_data[$i]=$result6;
				$compare_data[$i]['extra']=$result2;
				$compare_data[$i]['feature']=$result4;
				$compare_data[$i]['images']=$propertyimg;
				$compare_data[$i]['property_url']=$property_url;
			}
			$data['compare_data']=$compare_data;
	 }else{
	 		$this->load->model('db_hw_hostel');
			$data['property_facelity']=$this->db_hw_hostel->property_facelity();
		    $proid=explode(",",$pro_id);
			$compare_data=array();
			for($i=0;$i<count($proid);$i++){
				$result2='';
				$result4='';
				$result=$this->db_hw_hostel->compare_property($proid[$i]);
				$result1=$this->db_hw_hostel->compare_property_facelity($result['property_number']);
				$property_url=$this->next_property_url($result['property_type'],$result['property_name'],$result['property_number'],$this->site_lang);
				foreach($result1 as $facelty)
				{
					$result2[]=$facelty->hw_facility_id;
				}
				$propertyimg=$this->property_image($proid[$i]);
				$compare_data[$i]=$result;
				$compare_data[$i]['facelity']=$result2;
				$compare_data[$i]['images']=$propertyimg;
				$compare_data[$i]['property_url']=$property_url;
			}
			$this->carabiner->js('compare_property.js');
			$data['compare_data']=$compare_data;
			
	   }	
	    $filter_array = $this->get_property_details($pro_id);
		$jsondata = array();
		$jsondata['map_data'] = $filter_array ;
		$jsondata['html'] = $this->load->view("compare_property",$data,true);
		
		echo json_encode($jsondata);
 }

  function ajax_save_favorite_property() {
      header('Content-type: application/json');

      $id             = $this->input->post('id', true);
      $propertyNumber = $this->input->post('propertyNumber', true);
      $nights         = $this->input->post('nights', true);
      $date           = $this->input->post('date', true);
      $notes          = $this->input->post('notes', true);

      $propertyUrl  = '';
      $propertyName = '';
      $city         = '';
      $country      = '';

      $this->load->model('Db_favorite_hostels');
      $this->load->model('Db_links');

      $errors = array();

      if (empty($propertyNumber) || !preg_match('/^\d+$/', $propertyNumber)) {
          $errors[] = array(
              'field'   => 'propertyNumber',
              'message' => _('Invalid property')
          );
      }
      else {
          $hostelData = array();
          $data = array();

          // search for property
          if ($this->api_used == HB_API) {
              $this->load->library('hb_engine');

              $hostelData = $this->hb_engine->property_info($data, $propertyNumber);

              if (!empty($hostelData)) {
                  $propertyName = $hostelData['hostel_db_data']->property_name;
       	          $propertyUrl  = $this->Db_links->build_property_page_link($hostelData['hostel_db_data']->property_type, $propertyName, $propertyNumber, $this->site_lang);
                  $city         = $hostelData['bc_city'];
                  $country      = $hostelData['bc_country'];
              }
          }
          else {
              $this->load->library('hw_engine');

              $hostelData = $this->hw_engine->property_info($data, $propertyNumber);

              if (!empty($hostelData)) {
                  $propertyName = $hostelData['hostel']->property_name;
       	          $propertyUrl  = $this->Db_links->build_property_page_link($hostelData['hostel']->property_type, $propertyName, $propertyNumber, $this->site_lang);
                  $city         = $hostelData['hostel']->city;
                  $country      = $hostelData['hostel']->country;
              }
          }

          if (empty($hostelData)) {
             $errors[] = array(
                 'field'   => 'propertyNumber',
                 'message' => _('Property not found')
             );
          }
          else {
             $favHostelNo = $this->Db_favorite_hostels->countPropertyNumber($id, $propertyNumber, ($this->api_used == HB_API ? 1 : 0));

             if (!empty($favHostelNo)) {
                 $errors[] = array(
                     'field'   => 'propertyNumber',
                     'message' => _('Property is favorite already')
                 );
             }
          }
      }

      if (empty($nights) || !preg_match('/^\d+$/', $nights) || $nights <= 0) {
          $errors[] = array(
              'field'   => 'nights',
              'message' => _('Invalid number of nights')
          );
      }

      $dateIsValid = true;
      if (empty($date) || !preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $date)) {
          $dateIsValid = false;
      }
      else {
          $parsedDate = date_parse($date);

          $dateIsValid = empty($parsedDate) ? false : !(bool)$parsedDate['error_count'];
      }

      if (!$dateIsValid) {
          $errors[] = array(
              'field'   => 'date',
              'message' => _('Invalid date')
          );
      }

      if (!empty($notes) && mb_strlen($notes) > 75) {
          $errors[] = array(
              'field'   => 'date',
              'message' => sprintf(_('Notes are exceeding maximum of %d chars'), 75)
          );
      }

      if (!empty($errors)) {
          echo json_encode(array(
              'hasErrors' => 1,
              'errors'    => $errors
          ));

          exit();
      }

      // save the entry

      $this->Db_favorite_hostels->saveFav(array(
          'id'             => $id,
          'isHB'           => (bool)($this->api_used == HB_API),
          'propertyNumber' => $propertyNumber,
          'propertyUrl'    => str_replace(site_url('/'), '/', $propertyUrl),
          'propertyName'   => $propertyName,
          'city'           => $city,
          'country'        => $country,
          'nights'         => $nights,
          'date'           => $date,
          'notes'          => $notes,
          'userId'         => 13
      ));

      echo json_encode(array(
          'hasErrors' => 0
      ));

      exit();
  }

  function ajax_delete_favorite_property() {
      header('Content-type: application/json');

      $this->load->model('Db_favorite_hostels');

      $id = $this->input->post('id', true);

      if ($id) {
          if (!$this->Db_favorite_hostels->removeProperty($id, 13)) {
              echo json_encode(array('hasErrors' => 1));

              exit();
          }

          echo json_encode(array('hasErrors' => 0));

          exit();
      }

      echo json_encode(array('hasErrors' => 1));

      exit();
  }
 
  function property_image($pro_id)
 {
 	if($this->api_used == HB_API)
    {
 		$this->load->library('hb_engine');
   		$data = $this->hb_engine->propertyimg($pro_id);
		$propertyimg=$data['RESPONSE']['BIGIMAGES'][0];
		return $propertyimg;
	}
	else
	{
		$this->load->library('hw_engine');
   		$data = $this->hw_engine->propertyimg($pro_id);
		$propertyimg=$data[1][0]->PropertyImages->PropertyImage->imageURL;
		return $propertyimg;
	}	
 }
}
