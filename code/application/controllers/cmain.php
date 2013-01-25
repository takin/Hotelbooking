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

  function error404()
  {

    $data['title'] = $this->config->item('site_title');

    $data['user_id'] = $this->user_id;

    header("HTTP/1.0 404 Not Found");

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
      $data['current_view'] = "error404";

      $this->load->view('includes/template',$data);
    }

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

  function property_search($country = NULL, $city = NULL, $dateStart = NULL, $numNights = NULL)
  {
    $this->_currency_init();

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
    else
    {

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
            $filter["type"] = $this->Db_links->get_property_type_term(urldecode($this->uri->segment(4)),$this->site_lang);
            break;
        }
      }

      $url_segment_5 = strtolower($this->uri->segment(5));
      if(!empty($url_segment_5))
      {
        switch($url_segment_5)
        {
          case 'type':
            $dateStart = NULL;
            $numNights = NULL;
            $filter["type"] = $this->Db_links->get_property_type_term(urldecode($this->uri->segment(6)),$this->site_lang);
            break;
        }
      }
      if($this->api_used == HB_API)
      {
        $this->load->library('hb_engine');
        $data = $this->hb_engine->location_search($country, $city, $dateStart, $numNights, FALSE, TRUE, $filter);
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
          $this->carabiner->js('pweb-mapping/PropertyFilters.js');
          $this->carabiner->js('pweb/libs/GoogleMap.js');

          $this->load->view('includes/template',$data);
        }
      }
      else
      {
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
          $data = $this->hw_engine->location_search($country, $city, $dateStart, $numNights, false, true, $filter);
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
            $this->carabiner->js('pweb-mapping/PropertyFilters.js');
            $this->carabiner->js('pweb/libs/GoogleMap.js');

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

  function property_page($property_type, $property_name = "", $property_number = NULL)
  {
    log_message('debug', 'Entering main controller property page method');

    $this->_currency_init();
    $data = array();
    if(empty($property_number))
    {
      $this->error404();
      return;
    }

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
//     $this->hostel_controller = "chostelbk";
    if($this->api_used == HB_API)
    {
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

  /*
   * ajax_location_avail function to update location available properties list by ajax
   */
  function ajax_location_avail($country = NULL, $city = NULL, $dateStart = NULL, $numNights = NULL)
  {
    if(empty($country)||empty($city)||empty($dateStart)||empty($numNights))
    {
      return false;
    }
    $this->_currency_init();
//     $this->output->set_header('Cache-Control: public');
    //7200 sec = 2 hours
//     $this->output->set_header('Expires: '.gmdate('D, d M Y H:i:s',gmdate("U")+7200).' GMT');

    if($this->api_used == HB_API)
    {
      $this->load->library('hb_engine');
      $data = $this->hb_engine->location_search($country, $city, $dateStart, $numNights, TRUE);
      $data = $this->hb_engine->location_json_format($data);
//       $this->load->view('debug/hb_debug_rome');
    }
    else
    {
      $this->load->library('hw_engine');
      $data = $this->hw_engine->location_search($country, $city, $dateStart, $numNights, TRUE);
      $data = $this->hw_engine->location_json_format($data);
//       $this->load->view('debug/debug_view');
    }

    $this->load->view('includes/template-json',$data);
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
//         unset($data['distinctRoomTypes']);
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
    $search_term = $this->input->cookie('search_input_terms',TRUE);

//    $country   = $this->session->userdata('country_selected');
//    $city      = $this->session->userdata('city_selected');
//    $dateStart = $this->session->userdata('date_selected');
//    $numNights = $this->session->userdata('numnights_selected');

    //TONOTICE Remember to Search in cookie, if those values becomes to be set outside CI
     
        $urldate = $this->uri->segment(4);
        $units = $this->uri->segment(5);
  
      $chkdate = $this->checkData($urldate);
  
	if($dateStart!=false)
    {
	  if(($chkdate == true) && (!empty($urldate)))
		 $data['date_selected'] = $urldate;
		else
        $data['date_selected'] = $dateStart;
    }else{
		 $data['date_selected'] = $urldate;
		 }
   
    if($numNights!=false)
     {
		if((is_numeric($units)) && (!empty($units)))
		$data['numnights_selected'] = $units;
		else
        $data['numnights_selected'] = $numNights;
    }else{
		$data['numnights_selected'] = $units;
		}
    	    
    if($country!=false)
    {
      $data['country_selected'] = $country;
    }
    if($city!=false)
    {
      $data['city_selected'] = $city;
    }
    if($search_term!=false)
    {
//      $data['search_term'] = urldecode($search_term);
    }
    
    
  }

	function checkData($mydate) {
		list($yy,$mm,$dd)=explode("-",$mydate);
		if (is_numeric($yy) && is_numeric($mm) && is_numeric($dd))
		{
			return true;
		}
		return false;           
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
}
