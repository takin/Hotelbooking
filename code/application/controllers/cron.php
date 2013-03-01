<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends I18n_site
{
  const CRON_CODE = 'aEc3FvF6f754Bjida2QMp7gR';

  function Cron()
  {
    parent::I18n_site();

    //Ensure this controller is called by server and that cron code is good
//    if(strcmp($this->uri->segment(2),"send_sms_reminder")!=0)
//    if((strcmp($this->uri->segment(2),"sendTestReviewReminder")==0) AND (strcmp($this->uri->segment(3,""),self::CRON_CODE)!=0))
   if((strcmp($_SERVER["REMOTE_ADDR"],$_SERVER["SERVER_ADDR"])!=0) OR
      (strcmp($this->uri->segment(3,""),self::CRON_CODE)!=0))
   {
     $this->_log_cron_job(date("Y-m-d h:i:s A").": Bad attempt to run cron jobs from ".$_SERVER["REMOTE_ADDR"]." at ".current_url()."\n");
     show_404();
     exit();
   }
  }

  function hw_currency_cache()
  {
    $this->load->config('hw_cache',TRUE);
    $this->load->library('custom_log');
    $this->load->model('Hostel_api_model');
    $this->load->model('Db_hw_hostel');
    $this->load->model('Db_hb_hostel');
    $this->load->model('Db_hw_city');
    $this->load->model('Db_currency');

    $this->Db_hw_hostel->set_logfile($this->config->item('hw_log_filename','hw_cache')."_hostels");

    $this->custom_log->set_freq("Y-m-d");
    $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_hostels","Updating API hostel currency data");


    $country = $this->Db_hw_city->get_country_by_name($this->config->item('hw_currency_country','hw_cache'));

    if(empty($country))
    {
      $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_hostels","Wrong country to cache hostel currency data");
      exit;
    }
    $city = $this->config->item('hw_currency_city','hw_cache');
    $city_id =  $this->Db_hw_city->get_hw_city($city,$country->hw_country_id);
    if(empty($city_id))
    {
      $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_hostels","Wrong city to cache hostel currency data");
      exit;
    }
    $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_hostels","Caching all currency data for $city, $country->hw_country");

    foreach($this->Db_currency->get_all_currencies() as $db_currency)
    {
      set_time_limit(100);
      $this->_update_hw_city_hostels($city_id,$country->hw_country,$city,get_date_default(30),4,"English",$db_currency->currency_code,FALSE);
      sleep($this->config->item('hw_sec_between_req','hw_cache'));
    }
  }

  function hw_city_list()
  {
    $this->load->config('hw_cache',TRUE);

    $this->load->library('email');
    $this->load->library('custom_log');

    $this->load->model('Hostel_api_model');
    $this->load->model('Db_country');
    $this->load->model('Db_hw_city');

    $this->Db_hw_city->set_logfile($this->config->item('hw_log_filename','hw_cache')."_cities");

    $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_cities","Updating API countries and cities");

    $api_try = 0;
    $hw_results[0] = true;

    while($hw_results[0] == true)
    {
      $hw_results = $this->Hostel_api_model->cityCountryList($this->config->item('hostelworld_userID'));

      if($hw_results[0] == true)
      {
        $this->Db_hw_city->add_trace_to_report("Unable to reach API");
        $api_try++;
        sleep($this->config->item('hw_api_time_delay','hw_cache'));
      }

      if($api_try > $this->config->item('hw_api_max_tries','hw_cache'))
      {
        break;
      }
    }

    if($hw_results[0] == true)
    {
      $this->Db_hw_city->add_trace_to_report("Unable to reach API after $api_try connections");
    }
    else
    {
      $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_cities","HostelWorld API reached");
      $this->Db_hw_city->hw_update_city_country($hw_results[1][0]);
      if($this->Db_hw_city->is_cities_modified())
      {
//        $this->Db_country->clear_js_cities_data();
        //compute js city data for all lang
        $this->update_js_city_data();
      }
    }

    $email_adress = $this->config->item('email_to_report_city','hw_cache');
    if(!empty($email_adress))
    {
      $emailcontent = $this->Db_hw_city->modification_html_report();

      //Send report to appropriate admin
      $this->email->from($this->config->item('admin_booking_email'),"Auberges.com cron job");
      $this->email->to($email_adress);
      $this->email->subject("Cache report for HW API cities");
      $this->email->message($emailcontent);

      if ($this->email->send())
      {
        $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_cities","Report sent to ".$email_adress);
      }
      else
      {
        $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_cities","Failed to send report to ".$email_adress);
      }
    }
  }

  function update_js_city_data()
  {
    $this->load->model('Db_country');
    foreach($this->Db_links->get_all_domains_distinct_lang() as $domain)
    {
      parent::load_lang_context($domain->site_domain);
      $this->Db_country->fwrite_xml_cities_data("cities", $domain->lang, FALSE);
      $this->Db_country->fwrite_xml_cities_data("cities", $domain->lang, TRUE);
    }
  }

  function hw_location_search()
  {
//    $this->output->set_header("HTTP/1.0 204 No Content");
//    $this->output->set_header("Content-Length: 0");
//    $this->output->set_header("Content-Type: text/html");
//    flush();

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $this->load->config('hw_cache',TRUE);

    $this->load->helper('misc_tools');

    $this->load->library('email');
    $this->load->library('custom_log');

    $this->load->model('Hostel_api_model');
    $this->load->model('Db_hw_city');
    $this->load->model('Db_hw_hostel');

    $this->Db_hw_hostel->set_logfile($this->config->item('hw_log_filename','hw_cache')."_hostels");

    $this->custom_log->set_freq("Y-m-d");
    $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_hostels","Updating API hostel data");

    $numberofcity = ceil($this->Db_hw_city->get_city_count() / ($this->config->item('target_time','hw_cache') * $this->config->item('cron_job_freq_by_hour','hw_cache')));
//    $numberofcity = 1;
    foreach($this->Db_hw_city->get_hw_city_for_cache_search($numberofcity) as $cache_city)
    {
      $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_hostels","Updating properties of ".$cache_city->hw_country.", ".$cache_city->hw_city);
      foreach($this->config->item('hw_api_langages','hw_cache') as $api_lang)
      {
        $this->_update_hw_city_hostels($cache_city->hw_city_id,$cache_city->hw_country,$cache_city->hw_city,NULL,NULL,$api_lang);
        sleep($this->config->item('hw_sec_between_req','hw_cache'));
      }

      foreach($this->config->item('hw_api_currencies','hw_cache') as $api_currency)
      {
        $this->_update_hw_city_hostels($cache_city->hw_city_id,$cache_city->hw_country,$cache_city->hw_city,get_date_default(15),4,"English",$api_currency);
        sleep($this->config->item('hw_sec_between_req','hw_cache'));
      }
      $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_hostels","DONE Update of properties of ".$cache_city->hw_country.", ".$cache_city->hw_city);
    }

    $email_adress = $this->config->item('email_to_report_hostel','hw_cache');
    if(!empty($email_adress))
    {
      $emailcontent = $this->Db_hw_hostel->modification_html_report();

      //Send report to appropriate admin
      $this->email->from($this->config->item('admin_booking_email'),"Auberges.com cron job");
      $this->email->to($email_adress);
      $this->email->subject("Cache report for HW API hostels");
      $this->email->message($emailcontent);

      if ($this->email->send())
      {
        $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_hostels","Report sent to ".$email_adress);
      }
      else
      {
        $this->custom_log->log($this->config->item('hw_log_filename','hw_cache')."_hostels","Failed to send report to ".$email_adress);
      }
    }
  }

  function hw_properties_facilities_update($limit = 1)
  {

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $this->load->config('hw_cache',TRUE);
    $this->load->library('custom_log');
    $this->load->library('hw_engine');

    $this->load->model('Hostel_api_model');
    $this->load->model('Db_hw_city');
    $this->load->model('Db_hw_hostel');

    $log_info = $this->config->item('hw_log_filename','hw_cache')."_infohostels";
    $this->Db_hw_hostel->set_logfile($log_info);
    $this->custom_log->log($log_info,"Updating API propeties facilites");

    $target_days_for_all = 3;
    $cron_per_days       = 24;
    $numberofhostel = ceil($this->Db_hw_hostel->get_hostel_count() / ($target_days_for_all * $cron_per_days));

    foreach($this->Db_hw_hostel->get_last_facilites_updated_properties($numberofhostel) as $cache_hostel)
    {
      $avail_success = FALSE;
      $avail_tries = 0;
      $daysoffset = 15;

      $dateStart = get_date_default($daysoffset);
      $numNights = 7;

      //avail
      while($avail_success == FALSE)
      {
        if(($avail_success == FALSE) && ($avail_tries > 3) )
        {
          $this->custom_log->log($log_info, "Max property info tries reached for ".$cache_hostel->property_number);

          $this->Db_hw_hostel->update_hostel_facilities_timestamp($cache_hostel->property_number);
          break;
        }

        try
        {
          $this->hw_engine->store_property_facilities($cache_hostel->property_number,$log_info);
          $avail_success = true;
          //sleep(0.1);
        }
        catch(Exception $e)
        {
          $this->custom_log->log($log_info,"Failed to get property info and store facilities of property ".$cache_hostel->property_number);
          $avail_success = FALSE;
          $avail_tries++;
        }

      }
    }
  }

  //TONOTICE 2012-03-28 Put higher delay because it seems HW now detect our complete (ripoff!)
  function get_hostel_info_on_booking()
  {
//    error_reporting(E_ALL);

    $this->load->config('hw_cache',TRUE);
    $this->load->library('custom_log');

    $this->load->model('Hostel_api_model');
    $this->load->model('Db_hw_city');
    $this->load->model('Db_hw_hostel');

    $log_info = $this->config->item('hw_log_filename','hw_cache')."_infohostels";
    $this->Db_hw_hostel->set_logfile($log_info);
    $this->custom_log->log($log_info,"Updating API hostel booking info data");

    $numberofhostel = ceil($this->Db_hw_hostel->get_hostel_count() / ($this->config->item('target_days_to_cache_all','hw_cache') * $this->config->item('hostel_info_cron_per_day','hw_cache')));
//    $numberofhostel = 5;

    foreach($this->Db_hw_hostel->get_hostel_for_cache_data($numberofhostel) as $cache_hostel)
    {
      $avail_success = FALSE;
      $avail_tries = 0;
      $daysoffset = 15;

      $dateStart = get_date_default($daysoffset);
      $numNights = 7;

      //avail
      while($avail_success == FALSE)
      {

        $this->load->library('hw_engine');
        try
        {
          $this->hw_engine->store_property_facilities($cache_hostel->property_number,$log_info);
        }
        catch(Exception $e)
        {
          $this->custom_log->log($log_info,"Failed to get property info and store facilities of property ".$cache_hostel->property_number);
        }


        //
        $api = $this->Hostel_api_model->propertyBookingInformation($this->config->item('hostelworld_userID'),
                                                                         $cache_hostel->property_number,
                                                                         $dateStart,
                                                                         $numNights,
                                                                         "EUR",
                                                                         "English");
        $avail_tries++;

        //Booking request
        //If API availibility request returns bad
        if($api[0] == true)
        {
          $daysoffset+= 30;
          $dateStart = get_date_default( $daysoffset );

          $errormsg = "Unknown error";
          if(isset($api[1][0]->Error))
          {
             $errormsg =  $api[1][0]->Error->message." - ".$api[1][0]->Error->detail;
          }
          elseif(isset($api[1][0]->UserMessage))
          {
            $errormsg =  $api[1][0]->UserMessage->message." - ".$api[1][0]->UserMessage->detail;
            if(strcasecmp($api[1][0]->UserMessage->message,"Some Nights Unavailable")==0)
            {
              $daysoffset+= 60;
              $dateStart = get_date_default( $daysoffset );
            }
          }

          $this->custom_log->log($log_info,"availability check failed for property #".$cache_hostel->property_number ." on ".$dateStart." for $numNights nights -> ".$errormsg);
        }
        //If API availibility request returns OK
        else
        {
          $roomtobook = $api[1][0]->Rooms->RoomType[0]->roomTypeCode;
          //Book a room
          $api = $this->Hostel_api_model->propertyBookingRequest($this->config->item('hostelworld_userID'),
                                                               $cache_hostel->property_number,
                                                               $dateStart,
                                                               $numNights,
                                                               array($roomtobook),
                                                               array(1),
                                                               "EUR",
                                                               NULL,
                                                               "EUR",
                                                               "English");

        if($api[0] == true)
        {
          $errormsg = "Unknown error";
          if(isset($api[1][0]->Error))
          {
             $errormsg =  $api[1][0]->Error->message." - ".$api[1][0]->Error->detail;
          }
          elseif(isset($api[1][0]->UserMessage))
          {
            $errormsg =  $api[1][0]->UserMessage->message." - ".$api[1][0]->UserMessage->detail;
          }

          $this->custom_log->log($log_info, "booking request failed for property #".$cache_hostel->property_number." on ".$dateStart." for $numNights nights at room ".$roomtobook ." -> ".$errormsg);
        }
        //If API booking request returns OK
        else
          {

            $ccyeartext = date("y")+3;
            $bsid = $api[1][0]->BSID->value;
            settype($bsid,"string");
            $cardtype = $api[1][0]->CardInfo->card[0]->type;
            settype($cardtype,"string");

            //Confirm fake booking
            $api = $this->Hostel_api_model->bookingConfirmationRequest(
                                              $this->config->item('hostelworld_userID'),
                                              $bsid,
                                              "Test",
                                              "Test",
                                              "France",
                                              "Male",
                                              "testemail@test.ttt",
                                              "12321212",
                                              "13",
                                              "Test",
                                              123,
                                              '01/'.$ccyeartext,
                                              $cardtype,
                                              "EUR",
                                              "EUR",
                                              NULL,
                                              NULL,
                                              NULL,
                                              NULL,
                                              NULL,
                                              NULL,
                                              NULL,
                                              NULL,
                                              NULL,
                                              1);
            if($api[0] == true)
            {
              $errormsg = "Unknown error";
              if(isset($api[1][0]->Error))
              {
                 $errormsg =  $api[1][0]->Error->message." - ".$api[1][0]->Error->detail;
              }
              elseif(isset($api[1][0]->UserMessage))
              {
                $errormsg =  $api[1][0]->UserMessage->message." - ".$api[1][0]->UserMessage->detail;
              }

              $this->custom_log->log($log_info, "booking confirmation failed for property #".$cache_hostel->property_number." on ".$dateStart." for $numNights nights at room ".$api[1][0]->Rooms->RoomType[0]->roomTypeCode." -> ".$errormsg);
            }
            //If API booking confirmation returns OK
            else
            {
              $avail_success = TRUE;

              $email    = NULL;
              $postCode = NULL;
              $state    = NULL;
              $phone    = NULL;
              $fax      = NULL;
              $currency = NULL;
              $deposit  = NULL;

              if(!empty( $api[1][0]->PropertyDetails->email          )) $email    = (string) $api[1][0]->PropertyDetails->email;
              if(!empty( $api[1][0]->PropertyDetails->postCode       )) $postCode = (string) $api[1][0]->PropertyDetails->postCode;
              if(!empty( $api[1][0]->PropertyDetails->state          )) $state    = (string) $api[1][0]->PropertyDetails->state;
              if(!empty( $api[1][0]->PropertyDetails->phone          )) $phone    = (string) $api[1][0]->PropertyDetails->phone;
              if(!empty( $api[1][0]->PropertyDetails->fax            )) $fax      = (string) $api[1][0]->PropertyDetails->fax;
              if(!empty( $api[1][0]->PropertyDetails->currency       )) $currency = (string) $api[1][0]->PropertyDetails->currency;
              if(!empty( $api[1][0]->PropertyDetails->depositPercent )) $deposit  = floatval ($api[1][0]->PropertyDetails->depositPercent);

              $this->Db_hw_hostel->update_hostel_booking_info($cache_hostel->property_number,
                                                             $email,
                                                             $postCode,
                                                             $state,
                                                             $phone,
                                                             $fax,
                                                             $currency,
                                                             $deposit );
            }
          }
        }


        if(($avail_success == FALSE) && ($avail_tries > $this->config->item('hostel_info_avail_tries','hw_cache')) )
        {
          $this->custom_log->log($log_info, "Max availability tries reached");

          //update last booking info timestamp so that it does not have always the same non available hostels to load each cron job
          $this->Db_hw_hostel->update_hostel_booking_info($cache_hostel->property_number );
          break;
        }
        //wait before trying another avail check
        sleep(1);
      }

      //time between fake bookings
//      break;
      sleep(3);

    }

  }

  function _update_hw_city_hostels($city_id, $country, $city, $dateStart, $numNights = 4, $langage = "English", $currency = "EUR", $timestampSearch = TRUE)
  {
    $api_try = 0;
    $hw_results[0] = true;

    while($hw_results[0] == true)
    {
      $hw_results = $this->Hostel_api_model->propertyLocationSearch($this->config->item('hostelworld_userID'),
                                                                    $city,
                                                                    $country,
                                                                    $dateStart,
                                                                    $numNights,
                                                                    $currency,
                                                                    $langage,
                                                                    NULL,
                                                                    0);

//      print_r($hw_results);
      if($hw_results[0] === true)
      {
        if(!empty($hw_results[1][0]->UserMessage->message) &&
           (strcasecmp($hw_results[1][0]->UserMessage->message,"No Properties Found") == 0))
        {
          //no hostels
          break;
        }
        elseif(!empty($hw_results[1][0]->UserMessage->message))
        {
          //other User message error
          break;
        }
        elseif(!empty($hw_results[1][0]->Error->message))
        {
          //other error
          break;
        }
        else
        {
          //Serveur inaccessible
          $this->Db_hw_hostel->add_trace_to_report("Unable to reach API ");
        }

        $api_try++;
        sleep($this->config->item('hw_api_time_delay','hw_cache'));
      }

      if($api_try >= $this->config->item('hw_api_max_tries','hw_cache'))
      {
        break;
      }
    }

    if($hw_results[0] === true)
    {

      if(is_null($dateStart) && is_null($numNights))
      {
        if(!empty($hw_results[1][0]->UserMessage->message) &&
           (strcasecmp($hw_results[1][0]->UserMessage->message,"No Properties Found") == 0))
        {
          //no hostels
          $this->Db_hw_hostel->add_trace_to_report($hw_results[1][0]->UserMessage->message." -> property location search of $city, $country in $langage");

          //Delete all hostels of city no more hostels for this city
          $this->Db_hw_hostel->delete_hw_hostels_of_city($city_id,$city,$country);
          if($timestampSearch)
          {
            $this->Db_hw_city->update_city_last_search_time($city_id);
          }
          return TRUE;
        }
        elseif(!empty($hw_results[1][0]->UserMessage->message))
        {
          $this->Db_hw_hostel->add_trace_to_report($hw_results[1][0]->UserMessage->message." -> property location search of $city, $country in $langage");
        }
        elseif(!empty($hw_results[1][0]->Error->message))
        {
          $this->Db_hw_hostel->add_trace_to_report($hw_results[1][0]->Error->message." -> property location search of $city, $country in $langage");
        }
        else
        {
          $this->Db_hw_hostel->add_trace_to_report("Unable to reach API after $api_try connections -> property location search of $city, $country in $langage");
        }
      }
      else
      {
        if(!empty($hw_results[1][0]->UserMessage->message) &&
           (strcasecmp($hw_results[1][0]->UserMessage->message,"No Properties Found") == 0))
        {
          //no hostels
          $this->Db_hw_hostel->add_trace_to_report($hw_results[1][0]->UserMessage->message." -> property location search availability of $city, $country in $currency");

          //For now keep old hostels prices of hostels of this city even if for now it return no more availability
          $this->Db_hw_hostel->add_trace_to_report("Any previous hostels availability prices were kept");
          return TRUE;
        }
        elseif(!empty($hw_results[1][0]->UserMessage->message))
        {
          $this->Db_hw_hostel->add_trace_to_report($hw_results[1][0]->UserMessage->message." -> property location search availability of $city, $country in $currency");
        }
        elseif(!empty($hw_results[1][0]->Error->message))
        {
          $this->Db_hw_hostel->add_trace_to_report($hw_results[1][0]->Error->message." -> property location search availability of $city, $country in $currency");
        }
        else
        {
          $this->Db_hw_hostel->add_trace_to_report("Unable to reach API after $api_try connections: -> property location search availability of $city, $country in $currency");
        }
      }


      return FALSE;
    }
    else
    {
      if(is_null($dateStart) && is_null($numNights))
      {
        $this->Db_hw_hostel->add_trace_to_report("HostelWorld API reached -> property location search of $city, $country in $langage");
      }
      else
      {
        $this->Db_hw_hostel->add_trace_to_report("HostelWorld API reached -> property location search availability of $city, $country in $currency");
      }

      $this->Db_hw_hostel->hw_update_city_hostels($city_id, $country, $city, $dateStart, $numNights, $langage, $hw_results[1]);

      if($timestampSearch)
      {
        $this->Db_hw_city->update_city_last_search_time($city_id);
      }
      return TRUE;
    }

    return FALSE;
  }

  function sendTestReviewReminder()
  {
//    error_reporting(E_ALL);
    $this->load->library('email');

    $domains = $this->Db_links->get_all_domains();

    if(!empty($domains))
    {
      foreach ($domains as $domain)
      {
//        echo $domain->site_domain."<br>";
        $data["email"]           = "chrismorisseau@shaw.ca";
        $data["firstname"]       = "Firstname";
        $data["lastname"]        = "Lastname";
        $data["property_number"] = 9999;
        $data["property_name"]   = "Hostel name";

        $domain_context = $domain->site_domain;

        parent::load_lang_context($domain_context);
        sleep(1);
        $data['site_name'] = $this->config->item('site_name');
        $emailcontent = $this->load->view('email/review_reminder', $data, true);

        $this->email->from($this->config->item('admin_booking_email'),$data['site_name']);
        $this->email->to($data["email"]);
        $this->email->subject(_("Donnez nous des nouvelles de votre séjour!"));
        $this->email->message($emailcontent);

        if (! $this->email->send())
        {
          $this->_log_cron_job(date("Y-m-d h:i:s A").": Failed to send comment reminder for $domain_context in ".$this->html_lang_code." via ".$data['site_name']." to ".$data["email"]." at hostel #".$data["property_number"]." -> ".$data["property_name"]. "\n");
        }
        else
        {
          $this->_log_cron_job(date("Y-m-d h:i:s A").": Test email comment reminder sent to admin for $domain_context in ".$this->html_lang_code." via ".$data['site_name']." to ".$data["email"]." at hostel #".$data["property_number"]." -> ".$data["property_name"]. "\n");
        }
      }
    }
    else
    {
     $this->_log_cron_job(date("Y-m-d h:i:s A").": No test comment reminder sent to admin\n");
    }

  }

  function sendReviewReminder()
  {
    $this->load->library('email');
    $this->load->model('Db_model');
    $this->load->model('tank_auth/user_profiles');

    $query = $this->Db_model->get_email_of_expired_trip(3,100);
//     $query = $this->Db_model->get_email_of_expired_trip(-1,1500);

    if($query->num_rows() > 0)
    {
      foreach ($query->result() as $row)
      {
        $data["email"]           = $row->email;
        $data["firstname"]       = trim($row->first_name);
        $data["lastname"]        = trim($row->last_name);
        $data["property_number"] = $row->property_number;
        $data["property_name"]   = $row->property_name;

        $domain_context = $this->Db_links->get_domain_name($row->site_domain_id);

        parent::load_lang_context($domain_context);
        $data['site_name'] = $this->config->item('site_name');
        $emailcontent = $this->load->view('email/review_reminder', $data, true);

        $this->email->clear();
        $this->email->from($this->config->item('admin_booking_email'),$data['site_name']);
        $this->email->reply_to($this->config->item('admin_booking_email'),$data['site_name']);
        $this->email->to($data["email"]);
        $this->email->subject(_("Donnez nous des nouvelles de votre séjour!"));
        $this->email->message($emailcontent);

        if (! $this->email->send())
        {
          $this->_log_cron_job(date("Y-m-d h:i:s A").": Failed to send comment reminder in ".$this->html_lang_code." via ".$data['site_name']." to ".$data["email"]." for transaction ".$row->customer_booking_reference . " at hostel #".$data["property_number"]." -> ".$data["property_name"]. "\n");
        }
        else
        {
          $this->_log_cron_job(date("Y-m-d h:i:s A").": Email comment reminder sent in ".$this->html_lang_code." via ".$data['site_name']." to ".$data["email"]." for transaction ".$row->customer_booking_reference . " at hostel #".$data["property_number"]." -> ".$data["property_name"]. "\n");
          $this->Db_model->markReviewEmailSent($row->email,$row->property_number,$row->comeback_days);
        }
      }
    }
    else
    {
     $this->_log_cron_job(date("Y-m-d h:i:s A").": No comment reminder sent today\n");
    }

  }

  function send_sms_reminder()
  {
    $this->load->model('Db_sms');
    $this->load->library('clickatell');

    foreach($this->Db_sms->get_sms_list(0) as $transaction_sms_data)
    {
//       debug_dump($transaction_sms_data);
      $msg_id = NULL;
      $error_number = NULL;
      $status = 0;
//       parent::load_lang_context($transaction_sms_data->site_domain);
      $this->config->set_item('site_name',$transaction_sms_data->site_domain);
      $booking = NULL;

      $sms_text = "";
      if($transaction_sms_data->API_booked == 'HB')
      {
        $booking->bookingRef               = $transaction_sms_data->customer_booking_reference;
        $booking->property->name           = $transaction_sms_data->property_name;
        $booking->property->address->tel   = $transaction_sms_data->property_phone;
        $booking->property->address->email = $transaction_sms_data->property_email;
        $sms_text = $this->load->view('hb/sms_view', array("booking" => $booking), TRUE);
      }
      else
      {
        $booking->CustomerReference->value      = $transaction_sms_data->customer_booking_reference;
        $booking->PropertyDetails->propertyName = $transaction_sms_data->property_name;
        $booking->PropertyDetails->phone        = $transaction_sms_data->property_phone;
        $booking->PropertyDetails->email        = $transaction_sms_data->property_email;
        $sms_text = $this->load->view('hw/sms_view', array("booking" => $booking), TRUE);
      }

      try
      {
        $msg_id = $this->clickatell->send_single_sms($transaction_sms_data->phone_number,$sms_text);
        $status = 1;
        $this->_log_cron_job(date("Y-m-d h:i:s A").":SMS sending succeed for transaction #".$transaction_sms_data->transaction_id." and phone number ".$transaction_sms_data->phone_number."\n");
      }
      catch(Exception $e)
      {
        $error_number = $e->getCode();
        $this->_log_cron_job(date("Y-m-d h:i:s A").": SMS sending failed for transaction #".$transaction_sms_data->transaction_id." and phone number ".$transaction_sms_data->phone_number." -> ".$e->getMessage()."\n");
      }

      $this->Db_sms->update_sms($transaction_sms_data->transaction_id, $status, $transaction_sms_data->days_before_arrival, $msg_id, $error_number);

    }
  }

  function match_hb_properties()
  {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    ini_set('memory_limit', '512M');

    $this->load->model('Db_hb_hostel');
    $this->load->model('Db_hw_hb_properties_match');
    $this->load->helper('memory_helper');
    $this->load->library('Geos');

    echo memory_usage_in_mb()."<br>";
    $hw_properties = $this->Db_hw_hb_properties_match->get_unmatched_hw_prop();
    $hb_properties = $this->Db_hb_hostel->get_all_properties();

    $potential_match_count = 0;
    $match = 0;

    $hw_count = count($hw_properties);

    foreach($hw_properties as $hwi => $hw_property)
    {
      set_time_limit(30);

      if(empty($hw_property->geo_latitude) &&
        (empty($hw_property->geo_longitude)))
      {
        continue;
      }

      foreach($hb_properties as $hbi => $hb_property)
      {
        if(empty($hb_property->geo_latitude) &&
        (empty($hb_property->geo_longitude)))
        {
          continue;
        }

        //This to prevent error: MySQL server has gone away
        $this->db->reconnect();
        $cmp = $this->Db_hw_hb_properties_match->compare_properties($hw_property,$hb_property);
        if($cmp !== false)
        {
          $potential_match_count++;
          debug_dump($cmp);
          debug_dump($hw_property->property_name." | ".$hw_property->address1." | ".$hw_property->address2." | ".$hw_property->phone);
          debug_dump($hb_property->property_name." | ".$hb_property->address1." | ".$hb_property->address2." | ".$hb_property->phone);

          if((!is_null($cmp['name'])       && ($cmp['name'] ===0 )) ||
             (!is_null($cmp['name']) && ($cmp['name'] <= 2) && !is_null($cmp['address_no']) && $cmp['address_no'] < 0) ||
             (!is_null($cmp['address_no']) && ($cmp['address_no'] === 0)) ||
             (!is_null($cmp['phone'])      && ($cmp['phone'] === 0))
             )
          {

            if($this->Db_hw_hb_properties_match->insert_match($hw_property->property_number,$hb_property->property_number) !== false)
            {
              debug_dump("match?");
              echo "$hwi / $hw_count<br>";
            }
            else
            {
              debug_dump("match but error to insert in DB");
              $this->_log_cron_job(date("Y-m-d h:i:s A").":match but error to insert in DB\n");
              $this->_log_cron_job(date("Y-m-d h:i:s A").$this->db->last_query()."\n");
              $this->_log_cron_job(date("Y-m-d h:i:s A").$this->db->_error_message()."\n");
            }
            //remove the matched HB property to not compare again an already matched property
            unset($hb_properties[$hbi]);
            echo count($hb_properties)."<br>";
            $match++;
            break;
          }

        }
      }
    }
    echo "<br>protential match: ".$potential_match_count."<br>";
    echo "<br>protential match with same name: ".$match."<br>";

    echo memory_usage_in_mb()."<br>";
  }

  function db_backup()
  {
    $this->load->config('db_backup');

    include(APPPATH.'config/database'.EXT);

    $this->load->dbutil();
    $all_dbs = $this->dbutil->list_databases();

    foreach($all_dbs as $dbname)
    {

      if( $dbname == "aj_translation") continue;
      $dbkey = "";
      foreach($db as $dbkeylist => $dbkeyfields)
      {
        if(strcmp($dbkeyfields['database'],$dbname)==0)
        {
          $dbkey = $dbkeylist;
          break;
        }
      }

      if(empty($db[$dbkey]))
      {
        $this->_log_backup_job(date("Y-m-d h:i:s A").": Trying to backup an invalid database -> $dbkey\n");
      }
      else
      {
        $bckupfilename = $db[$dbkey]['database']."--".date("Y-m-d_H:i:s").".gz";
        $cmdreturn = $this->_mysql_backup($this->config->item('db_backup_dir'),$bckupfilename,$db[$dbkey]['database'],$db[$dbkey]['hostname'],$db[$dbkey]['username'],$db[$dbkey]['password']);

        if(($cmdreturn === FALSE)||
           (file_exists($this->config->item('db_backup_dir')."/".$bckupfilename)===FALSE)||
           (filesize($this->config->item('db_backup_dir')."/".$bckupfilename) < 51))
        {
          $this->_log_backup_job(date("Y-m-d h:i:s A").": MySQL Auto backup failed for database ".$db[$dbkey]['database']."\n");
        }
        else
        {
          $this->_log_backup_job(date("Y-m-d h:i:s A").": MySQL Auto backup for database ".$db[$dbkey]['database']." in ".$this->config->item('db_backup_dir')."/".$bckupfilename."\n");

          //if files reach limit delete oldest
          $this->_clearOldDatabaseBackup($this->config->item('db_backup_dir'),$db[$dbkey]['database'],$this->config->item('DbFilesQty'));
        }

      }
    }
  }

  function _mysql_backup($backupFileDir, $backupFileName, $dbname, $dbhost, $dbuser, $dbpass)
  {
    $command = "mysqldump --extended-insert --opt --single-transaction -h $dbhost -u $dbuser -p$dbpass $dbname | gzip > $backupFileDir/$backupFileName";
    return system($command);
  }

  function _clearOldDatabaseBackup($backupDir,$dbname,$nbFilesToKeep)
  {
    // Grab all backup files of database
    $files = glob( "$backupDir/$dbname--*" );

    if(count($files) > $nbFilesToKeep )
    {
      // Sort files by modified time, oldest to earliest
      // Use SORT_ASC in place of SORT_DESC for earliest to latest
      array_multisort( array_map( 'filemtime', $files ), SORT_NUMERIC, SORT_ASC, $files );

      if(unlink($files[0])===FALSE)
      {
        $this->_log_backup_job(date("Y-m-d h:i:s A").": Failed to delete oldest file ".$files[0]."\n");
      }
      else
      {
        $this->_log_backup_job(date("Y-m-d h:i:s A").": Maximum files reached deleting -> ".$files[0]."\n");
      }
    }
  }

  /**
   *
   */
  function _log_cron_job($job_details)
  {

    $log_path = ($this->config->item('log_path') != '') ? $this->config->item('log_path') : BASEPATH.'logs/';

    try
    {
      $cron_file = $log_path."cronjobs-".date("Y-m").".php";
      $fp = fopen($cron_file, 'a');
      if (!$fp) {

          throw new Exception("Problem with opening of $cron_file");
      }
      else
      {
        $fwrite = fwrite($fp, $job_details);
        if ($fwrite === false) {
            throw new Exception("Problem writing data to $cron_file");
         }
        fclose($fp);
      }
    }
    catch(Exception $e)
    {
      log_message('error', 'log_cron_file:'.$e->getMessage());
    }
  }
  /**
   *
   */
  function _log_backup_job($job_details)
  {

    $log_path = ($this->config->item('log_path') != '') ? $this->config->item('log_path') : BASEPATH.'logs/';

    try
    {
      $cron_file = $log_path."backups-".date("Y-m").".php";
      $fp = fopen($cron_file, 'a');
      if (!$fp) {

          throw new Exception("Problem with opening of $cron_file");
      }
      else
      {
        $fwrite = fwrite($fp, $job_details);
        if ($fwrite === false) {
            throw new Exception("Problem writing data to $cron_file");
         }
        fclose($fp);
      }
    }
    catch(Exception $e)
    {
      log_message('error', 'log_cron_file:'.$e->getMessage());
    }
  }

  public function district_update($cron_code, $days_too_old = 30, $limit = 1000)
  {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    ini_set('memory_limit', '512M');
    // load library code_tracker
    $this->load->library('code_tracker');

    // set name of the file for code_tracker
    $this->code_tracker->set_logfile("districts");

    // set echo to follow code_tracker without log_file
//     $this->code_tracker->set_echo(TRUE);

    // To set the priority: FEED_INFO (0), FEED_DEBUG (1), FEED_ERROR (2), FEED_ALWAYS (10)
    $this->code_tracker->set_feed_treshold(code_tracker::FEED_INFO);

    //disable query history because with multiple query it will cause memory problems
    $this->db->save_queries = false;

    $this->_district_hw_update($days_too_old, $limit);
    $this->_district_hb_update($days_too_old, $limit);

  }

  private function _district_hw_update($days_too_old = NULL, $limit = 1000)
  {
    $this->load->library('APIs/urban_mapping');
    $this->load->model('Db_hw_hostel');

    $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Updating hw districts");

    // get hw properties to update
    $properties = $this->Db_hw_hostel->get_properties_with_last_x_update("last_district_update", $days_too_old, $limit);

    $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Updating districts for a selection of ".count($properties)." properties");

    $property_count = 0;

    $delay_time = 0.5;
    $delay_inc  = 0.5;
    $delay_max  = 2;

    foreach ($properties as $property)
    {
      if(empty($property->geo_latitude) || $property->geo_latitude<-90 || $property->geo_latitude>90
      		|| empty($property->geo_longitude) || $property->geo_longitude<-180 || $property->geo_longitude>180
      		|| ($property->geo_latitude==0 && $property->geo_longitude==0))
      {
        $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Cannot update districts for HW property ".$property->property_number.' ('.$property->geo_latitude.','.$property->geo_longitude.')');
        continue;
      }

      $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Updating districts for HW property ".$property->property_number.' ('.$property->geo_latitude.','.$property->geo_longitude.')');

      set_time_limit(20);
      try
      {
        //GET district from Urban Mapping API
        $districts = $this->urban_mapping->get_districts_of_geolocation($property->geo_latitude,$property->geo_longitude);

        $this->Db_hw_hostel->update_property_district($property->property_number, $districts->features);
        $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON $property_count: districts updated for property ".$property->property_number);
        $property_count++;
        sleep($delay_time);
      }
      catch(Exception $e)
      {
        $this->code_tracker->feed_trace(code_tracker::FEED_ERROR,$e->getMessage());
        if($delay_time < $delay_max)
        {
          $delay_time+=$delay_inc;
        }
        else
        {
          $this->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Too much Urban Mapping API error stopping districts updates");
          break;
        }
        sleep(30);
      }
    }
    $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Done Updating hw districts of $property_count properties");
  }

  private function _district_hb_update($days_too_old = NULL, $limit = 1000)
  {
    $this->load->library('APIs/urban_mapping');
    $this->load->model('Db_hb_hostel');

    $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Updating hb district");

    // get hb properties to update
    $properties = $this->Db_hb_hostel->get_properties_with_last_x_update("last_district_update", $days_too_old, $limit);

    $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Updating districts for a selection of ".count($properties)." properties");

    $property_count = 0;

    $delay_time = 0.5;
    $delay_inc  = 0.5;
    $delay_max  = 2;

    foreach ($properties as $property)
    {
      if(empty($property->geo_latitude) || $property->geo_latitude<-90 || $property->geo_latitude>90
      		|| empty($property->geo_longitude) || $property->geo_longitude<-180 || $property->geo_longitude>180
      		|| ($property->geo_latitude==0 && $property->geo_longitude==0))
      {
        $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Cannot update districts for HB property ".$property->property_number.' ('.$property->geo_latitude.','.$property->geo_longitude.')');
        continue;
      }

      $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Updating districts for HB property ".$property->property_number.' ('.$property->geo_latitude.','.$property->geo_longitude.')');

      set_time_limit(20);
      try
      {
        //GET district from Urban Mapping API
        $districts = $this->urban_mapping->get_districts_of_geolocation($property->geo_latitude,$property->geo_longitude);

        $this->Db_hb_hostel->update_property_district($property->property_number, $districts->features);

        $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON $property_count: districts updated for property ".$property->property_number);
        $property_count++;
        sleep($delay_time);
      }
      catch(Exception $e)
      {
        $this->code_tracker->feed_trace(code_tracker::FEED_ERROR,$e->getMessage());
        if($delay_time < $delay_max)
        {
          $delay_time+=$delay_inc;
        }
        else
        {
          $this->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Too much Urban Mapping API error stopping districts updates");
          break;
        }
        sleep(30);
      }
    }

    $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Done Updating HB districts of $property_count properties");
  }

  public function landmark_update($cron_code, $days_too_old = 30, $limit = 400)
  {
    // load library code_tracker
    $this->load->library('log');
    $this->load->library('code_tracker');
    $this->load->library('mutex');

    $this->log->set_logfilename("landmarks");
    $this->log->set_file_date_fmt("Y-m");

    //prevent two cron to run at the same time on same server
    $this->mutex->lock("landmark_update");

    // set name of the file for code_tracker
    //TODO remove usage of bad lib custom_log. Must use extended log class of CI Log instead
    $this->code_tracker->set_logfile("landmarks");

    // set echo to follow code_tracker without log_file
//     $this->code_tracker->set_echo(TRUE);
//     $this->output->enable_profiler(TRUE);

    // To set the priority: FEED_INFO (0), FEED_DEBUG (1), FEED_ERROR (2), FEED_ALWAYS (10)
    $this->code_tracker->set_feed_treshold(code_tracker::FEED_INFO);

    //disable query history because with multiple query it will cause memory problems
    $this->db->save_queries = false;

    ini_set('memory_limit', '512M');

    $this->_landmark_hw_update($days_too_old, $limit);
    $this->_landmark_hb_update($days_too_old, $limit);
    $this->mutex->unlock();

  }

  private function _landmark_hw_update($days_too_old = NULL, $limit = 1000)
  {
    //To retest changes has been made to Db_landmarks
    //Changes should be supported by this method, but no test has been made
    return FALSE;
    $this->load->library('APIs/google/google_places');
    $this->load->model('Db_hw_hostel');

    $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Updating hw landmarks");

    // get hw properties to update
    $properties = $this->Db_hw_hostel->get_properties_with_last_x_update("last_landmark_update", $days_too_old, $limit);

    $delay_time = 0;
    $delay_inc  = 0.5;
    $delay_max  = 2;

    $property_count = 0;

    foreach ($properties as $property)
    {
      if(empty($property->geo_latitude)  && empty($property->geo_longitude))
      {
        continue;
      }

      try
      {
        set_time_limit(20);
        $landmarks = $this->google_places->search($property->geo_latitude,$property->geo_longitude);

        if(!empty($landmarks->html_attributions))
        {
          $this->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"update attribution of property ".$property->property_number.": ".print_r($landmarks->html_attributions,TRUE));
          $this->Db_hw_hostel->update_property_landmark_attribution($property->property_number,$landmarks->html_attributions);
        }
        $this->Db_hw_hostel->update_property_landmark($property->property_number, $property->geo_latitude, $property->geo_longitude, $landmarks->results);
        $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON $property_count: landmark updated for property ".$property->property_number);
        $property_count++;
        //Wait to give a chance to google API
        sleep($delay_time);
      }
      catch(Exception $e)
      {
        $this->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Landmark API error -> ".$e->getMessage());
        if($delay_time < $delay_max)
        {
          $delay_time+=$delay_inc;
        }
        else
        {
          $this->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Too much google place API error stopping landmark updates");
          break;
        }
        sleep(30);
      }
    }
    $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Done Updating hw landmarks of $property_count properties");
  }

  private function _landmark_hb_update($days_too_old = NULL, $limit = 1000)
  {
    //To retest changes has been made to Db_landmarks
    //Changes should be supported by this method, but no test has been made
    return FALSE;
    $this->load->library('APIs/google/google_places');
    $this->load->model('Db_hb_hostel');

    $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Updating hb landmarks");

    // get hw properties to update
    $properties = $this->Db_hb_hostel->get_properties_with_last_x_update("last_landmark_update", $days_too_old, $limit);

    $delay_time = 0;
    $delay_inc  = 0.5;
    $delay_max  = 2;

    $property_count = 0;

    foreach ($properties as $property)
    {
      if(empty($property->geo_latitude)  && empty($property->geo_longitude))
      {
        continue;
      }

      try
      {
        set_time_limit(20);
        $this->benchmark->mark('google_start');
        $landmarks = $this->google_places->search($property->geo_latitude,$property->geo_longitude);
        $this->benchmark->mark('google_end');
        $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON $property_count: received google response after ".$this->benchmark->elapsed_time('google_start', 'google_end') ." sec");

        $attri_time = 0;
        if(!empty($landmarks->html_attributions))
        {
          $this->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"update attribution of property ".$property->property_number.": ".print_r($landmarks->html_attributions,TRUE));
          $this->benchmark->mark('attribution_start');
          $this->Db_hb_hostel->update_property_landmark_attribution($property->property_number,$landmarks->html_attributions);
          $this->benchmark->mark('attribution_end');
          $attri_time = $this->benchmark->elapsed_time('attribution_start', 'attribution_end');
        }
        $this->benchmark->mark('landmark_start');
        $this->Db_hb_hostel->update_property_landmark($property->property_number, $property->geo_latitude, $property->geo_longitude, $landmarks->results);
        $this->benchmark->mark('landmark_end');
        $landmark_time = $this->benchmark->elapsed_time('landmark_start', 'landmark_end');
        $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON $property_count: landmark updated for property ".$property->property_number." - $attri_time sec for DB attri and $landmark_time sec for landmark DB update");
        $property_count++;
        //Wait to give a chance to google API
        sleep($delay_time);
      }
      catch(Exception $e)
      {
        $this->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Landmark API error -> ".$e->getMessage());
        if($delay_time < $delay_max)
        {
          $delay_time+=$delay_inc;
        }
        else
        {
          $this->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Too much google place API error stopping landmark updates");
          break;
        }
        sleep(30);
      }

    }
    $this->code_tracker->feed_trace(code_tracker::FEED_INFO,"CRON Done Updating hb landmarks of $property_count properties");
  }

  public function update_city_search_page_cache()
  {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $this->config->set_item('log_threshold',2);

    //disable query history because with multiple query it will cause memory problems
    $this->db->save_queries = false;

    $this->load->config('hw_cache',TRUE);
    $this->load->model('Db_hw_city');
    $this->load->model('Db_links');
    $this->load->library('curl');

    $this->load->library('Log');
    $this->log->set_logfilename($this->config->item('hw_log_filename','hw_cache')."_hostels");

    $this->log->write_log('debug',"Updating city landing pages cache");

    $oldest_searches = $this->Db_hw_city->get_hw_city_for_cache_search();

    $cache_dir = $this->output->list_cache_files();

    //Ensure to put current server dir to top to avoid context change issue
    $current_server_url = parse_url(base_url());
    $current_server_url = $current_server_url['host'];
    foreach($cache_dir as $i => $file)
    {
      if(basename($file) === $current_server_url)
      {
        array_unshift($cache_dir,$file);
        break;
      }
    }

    $domain_processed = array();
    //remove
    foreach($cache_dir as $i => $file)
    {
      if(!empty($file) && is_dir($file))
      {
        $domain_dir = $file;
        $domain = basename($file);
        $domain = $this->output->validate_http_host($domain);

        //if not a www. cache dir or a mobile cache dir jump it
        if((substr(basename($file),0,3) != 'www') ||
           (substr($file,-6) == 'mobile'))
        {
          continue;
        }

        //TONOTICE Timezone in DB depends on he server timezone that insert the time... :(
        // Because, the location search cronjob is run on a CEST server the time in DB are CEST.
        // Note that if the server that runs the cronjob changes of timezone, this will mess some checks
        // of this cache refresh cron job.
        date_default_timezone_set('Europe/Brussels');


        $domain = $this->Db_links->get_domain($domain);
        if(!empty($domain) && !in_array($domain->site_domain_id,$domain_processed))
        {
          parent::load_lang_context($domain->site_domain);

          //Check if HW site
          if($this->wordpress->get_option('aj_api_site_data') == 'hw')
          {

            $this->log->write_log('debug',"Updating HW site ".$domain->site_domain ." landing page cache");

            //check if site landing page cache dir exists and get currencies of cache
            $all_currencies_dir = array();
            if(is_dir($file.'/cmain-property_search'))
            {
              $all_currencies_dir = glob($file.'/cmain-property_search/*');
            }
            else
            {
              $this->log->write_log('debug',"Skipping cache update - no cache directory cmain-property_search found for ".$file);
              continue;
            }

            foreach($oldest_searches as $hw_city)
            {
              set_time_limit(60);
              //fetch lang data of city
              $hw_city_lang = $this->Db_hw_city->get_hw_city_by_id($hw_city->hw_city_id, $domain->lang);

              //Do not update cache of never search city as it most probably will be a 404
              if(empty($hw_city->last_search_on))
              {
                $this->log->write_log('debug',"Skipping cache update of city ".$hw_city->hw_country. ", ".$hw_city->hw_city." - never searched");
                continue;
              }


              //for all price dir
              foreach($all_currencies_dir as $lp_currency_dir)
              {
                $country_url = customurlencode($hw_city_lang->country_name_translated);
                $city_url    = customurlencode($hw_city_lang->city_name_translated);

                $cache_filter_key = $this->output->get_city_cache_key($country_url,$city_url);
                $cache_filter_key = str_replace(' ','*', $cache_filter_key);
                $cache_filter_key = $lp_currency_dir."/".$cache_filter_key."";
                $city_cache_files = glob($cache_filter_key);

                if(empty($city_cache_files)) continue;

                $city_refresh_needed = false;
                $filetimes = array();
                //For all city cache file for this currency delete them
                foreach($city_cache_files as $city_cache_file)
                {

                  if(date('U',filemtime($city_cache_file)) < $hw_city->last_search_on_timestamp )
                  {
                    $city_refresh_needed = true;
                    $filetimes[$city_cache_file] = filemtime($city_cache_file);
                  }
                  else
                  {
                    //no cache refresh needed;
                    $this->log->write_log('debug',"Skipping cache file update file is more recent than last search ".$city_cache_file);
                    continue;
                  }

                  if (unlink($city_cache_file) === true)
                  {
                    $this->log->write_log('debug',"Deleted cache file ".$city_cache_file);
                  }
                  else
                  {
                    $this->log->write_log('error',"Problem deleting cache file ".$city_cache_file);
                  }
                }

                //If cache refresh not needed continue;
                if($city_refresh_needed === false)
                {
                  $this->log->write_log('debug',"Skipping cache refresh for city ".$hw_city->hw_country. ", ".$hw_city->hw_city." - cache done past last search");
                  continue;
                }

                set_time_limit(60);
                $url_to_refresh = "http://".$domain->site_domain."/".$country_url."/".$city_url."/?currency=".basename($lp_currency_dir);
                $this->curl->create($url_to_refresh);
                if($this->curl->execute() === false)
                {
                  $this->log->write_log('error',"Problem updating cache of url ".$url_to_refresh);
                }
                else
                {
                  $this->log->write_log('debug',"Updated cache file of url $url_to_refresh");

                  //check if cache file exists
//                   $city_cache_files = glob($cache_filter_key);
                  if(!empty($city_cache_files))
                  {
                    foreach($city_cache_files as $city_cache_file)
                    {
                      if($filetimes[$city_cache_file] == filemtime($city_cache_file) )
                      {
                        $this->log->write_log('error',"Failed to refresh caching of file $city_cache_file - timestamp still the same: ".filemtime($city_cache_file));
                      }
                      else
                      {
                        $this->log->write_log('debug',"Successfully cached -> ".$url_to_refresh);
                      }
                    }
                  }
                  else
                  {
                    $this->log->write_log('error',"New cache file not found after update via ".$url_to_refresh);
                  }
                }

                //For now only delete cache file of landing page with filter will see later if refresh is needed
                $city_secondary_cache_files = glob($cache_filter_key."-type*");
                foreach($city_secondary_cache_files as $city_cache_file)
                {
                  if (unlink($city_cache_file) === TRUE)
                  {
                    $this->log->write_log('debug',"Deleted secondary cache file ".$city_cache_file );
                  }
                  else
                  {
                    $this->log->write_log('error',"Problem deleting secondary cache file ".$city_cache_file);
                  }
                }

              }
            }
            $this->log->write_log('debug',"Done updating cache of ".$domain->site_domain);
          }
          else
          {
            $this->log->write_log('debug',"Skip site not HW ".$domain->site_domain ." - cache update prevented");
          }
          $domain_processed[] = $domain->site_domain_id;

        }
      }
    }
    $this->log->write_log('debug',"Done with cache refresh process");
    exit;

  }

}
?>