<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cron_hb extends I18n_site
{
  const CRON_CODE = 'aEc3FvF6f754Bjida2QMp7gR';
  const DOWNLOAD_DIR = "cache_queries/staticfeeds";

  private $log_filename = "hb_cache";
  private $api_delay_retries = 2;
  private $api_max_retries   = 5;

  private $email_to_report_city = "technical@mcwebmanagement.com";

  private $lockfile = "lock.txt";
  private $lockfp = NULL;
  public $already_running = FALSE;

  function __construct()
  {
    parent::I18n_site();

    //Ensure this controller is called by server and that cron code is good
    if (strcmp($this->uri->segment(3,""),self::CRON_CODE)!=0)
    {
      $this->_log_cron_job(date("Y-m-d h:i:s A").": Bad attempt to run cron jobs at ".current_url()."\n");
      show_404();
      exit();
    }

  }

  function __destruct()
  {
//     $this->unlock();
  }

  //TODO lock and unlock should be used via Mutex library in the future
  function lock()
  {
     $this->lockfp = $this->lockfp = fopen(self::DOWNLOAD_DIR."/".$this->lockfile, "w+");

    if (flock($this->lockfp, LOCK_EX | LOCK_NB))
    {
      $this->custom_log->log($this->log_filename,"Acquired lock");
    }
    else
    {
      $this->already_running = TRUE;
      $this->custom_log->log($this->log_filename,"Failed lock already acquired");
      fclose($this->lockfp);
    }
  }

  //TODO lock and unlock should be used via Mutex library in the future
  function unlock()
  {
    if(!$this->already_running && !is_null($this->lockfp))
    {
      $this->custom_log->log($this->log_filename,"Releasing lock");
      flock($this->lockfp, LOCK_UN);
      fclose($this->lockfp);
    }
  }

  function index()
  {
    show_404();
  }

  function hb_city_list()
  {

    $this->log_filename.= "_cities";

    $this->load->library('email');
    $this->load->library('custom_log');

    $this->load->model('Hostelbookers_api');
    $this->load->model('Db_country');
    $this->load->model('Db_hb_city');

    $this->Db_hb_city->set_logfile($this->log_filename);

    $this->custom_log->log($this->log_filename,"Updating HB API countries and cities");

    $this->lock();
    if($this->already_running)
    {
      $this->custom_log->log($this->log_filename,__FUNCTION__." already running exitting...");
      exit;
    }

    $api_try = 0;
    $hb_results = false;

    while($hb_results === false)
    {
      $hb_results = $this->Hostelbookers_api->getCountryLocationList();

      if($hb_results === false)
      {
        $this->Db_hb_city->add_trace_to_report("Unable to reach API");
        $api_try++;
        sleep($this->api_delay_retries);
      }

      if($api_try > $this->api_max_retries)
      {
        break;
      }
    }

    if($hb_results === false)
    {
      $this->Db_hb_city->add_trace_to_report("Unable to reach API after $api_try connections");
    }
    else
    {

      $this->custom_log->log($this->log_filename,"HostelWorld API reached");
      $this->Db_hb_city->hb_update_city_country($hb_results);
      if($this->Db_hb_city->is_cities_modified())
      {
        //compute xml city data for all lang
        $this->update_xml_hb_city_data();
      }

    }

    $email_adress = $this->email_to_report_city;
    if(!empty($email_adress))
    {
      $emailcontent = $this->Db_hb_city->modification_html_report();

      //Send report to appropriate admin
      $this->email->from($this->config->item('admin_booking_email'),"HB API cron job");
      $this->email->to($email_adress);
      $this->email->subject("Cache report for HB API cities");
      $this->email->message($emailcontent);

      if ($this->email->send())
      {
        $this->custom_log->log($this->log_filename,"Report sent to ".$email_adress);
      }
      else
      {
        $this->custom_log->log($this->log_filename,"Failed to send report to ".$email_adress);
      }
    }

    $this->unlock();

  }

  function update_xml_hb_city_data()
  {
    $this->load->model('Db_hb_country');
    foreach($this->Db_links->get_all_domains_distinct_lang() as $domain)
    {
      set_time_limit(30);
      parent::load_lang_context($domain->site_domain);
      $this->Db_hb_country->fwrite_xml_cities_data("cities", $domain->lang, FALSE);
      $this->Db_hb_country->fwrite_xml_cities_data("cities", $domain->lang, TRUE);
    }
  }

  //Should not be used without further testing
  function hb_landing_page_cache()
  {
    $this->load->model('Db_hb_city');
    $this->load->library('hb_engine');
    $this->load->library('custom_log');
    $this->load->helper('file');

    $this->custom_log->set_freq("Y-m");

    foreach($this->Db_links->get_all_domains_distinct_lang() as $domain)
    {
      $this->custom_log->log($this->log_filename."_city_landing","Updating API city landing page for ".$domain->site_domain);
      parent::load_lang_context($domain->site_domain);
      $this->hb_engine->initialize();

      foreach($this->Db_hb_city->get_hb_cities_of_country_name(NULL,NULL,$this->site_lang) as $hb_city)
      {
        $city_cache_file = $this->hb_engine->get_city_cache_filename($hb_city->country_system_name, $hb_city->city_system_name);
        $landing_page = $this->hb_engine->city_properties_view($hb_city->hb_country_name, $hb_city->hb_city_name, NULL, NULL, true, true);

        if ( ! write_file($city_cache_file, $landing_page))
        {
             $this->custom_log->log($this->log_filename."_city_landing","Error caching landing page of ".$hb_city->city_system_name.", ".$hb_city->country_system_name.". Unable to write the file ".$city_cache_file);
        }
      }
    }
    $this->custom_log->log($this->log_filename."_city_landing","Caching of city landing pages completed for ".$domain->site_domain);
  }

  function hb_get_nationalities()
  {
    $this->load->model('Db_hb_country');
    $this->load->model('Hostelbookers_api');

    $this->load->library('custom_log');
    $this->log_filename.= "_nationalities".date("Y");

    $this->custom_log->log($this->log_filename,"HB API nationalities update");
//     foreach($this->Db_links->get_all_domains_distinct_lang() as $domain)
//     {
//       $lang_code = $this->Hostelbookers_api->lang_code_convert($domain->lang, NULL);
//       if lang is not supported by HB API
//       if(is_null($lang_code)) continue;

      $response = $this->Hostelbookers_api->getNationalities("en");
      if($response !== false)
      {

		$this->Db_hb_country->parse_nationalities($response, "en");
        $this->custom_log->log($this->log_filename,"HB API nationalities update for language -> "."en");

      }
      else
      {


        $this->custom_log->log($this->log_filename,"HB API bad response for nationalities of language -> "."en");
      }
//     }

  }

    public function hb_hostels_get() {

        require_once(APPPATH . "/services/hostelbookers_feed_service.php");
        $hbFeedService = new Hostelbookers_feed_service();
        $serviceCallback = array($hbFeedService, "updateHbHostels");

        $emailSubject = "Email report for the hostelbookers feed service";
        $logFilename = "updateallproperties";

        $this->runXmlServiceCron($serviceCallback, $emailSubject, $logFilename);
    }

    public function update_hb_hostel_descriptions() {

        require_once(APPPATH . "/services/hostelbookers_property_content_service.php");
        $hbPropertyContentService = new Hostelbookers_Property_Content_Service();
        $emailSubject = "Email report for the hostelbookers property content service";
        $serviceCallback = array($hbPropertyContentService, "updateShortDescriptions");
        $logFilename = "updatepropertycontent";
        
        $urlInfo = $this->getShortDescriptionUrlAndLanguage();
        if (!empty($urlInfo)) {
            $this->runXmlServiceCron($serviceCallback, $emailSubject, $logFilename, $urlInfo);
        }
    }
    
    private function getShortDescriptionUrlAndLanguage() {
        $this->load->model("db_translation_langs");
        $supportedLanguages = $this->db_translation_langs->getSupportedLanguages();
        
        $urlsAndLanguages = array();
        foreach ($supportedLanguages as $langCode => $language) {
            $urlData = array(
                "url" => sprintf("%s-[%s]-[%s]-[%s].xml",
                            "http://feeds.hostelbookers.com/generic/PropertyContent",
                            $langCode, date("Y"), date("m")),
                "langCode" => strtolower($langCode),
            );
            
            $urlsAndLanguages[] = $urlData;
        }
        
        $todaysIndex = date("j") - 1;
        
        if ($todaysIndex >= count($supportedLanguages)) return array();
        else return $urlsAndLanguages[$todaysIndex];
    }

    private function runXmlServiceCron($serviceCallback, $emailSubject, $logFilename, $params=array()) {

        ini_set('memory_limit', "700M");
        set_time_limit(3000);

        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        $this->load->library('custom_log');
        $this->log_filename.= "_".$logFilename."_staticfeeds-" . date("Y-m");

        $errors = array();
        try {
            call_user_func($serviceCallback, $params);
        } catch (Exception $e) {
            $msg = sprintf("ERROR: Cron job: %s <br> %s",
                    $e->getMessage(), $e->getTraceAsString());
            log_message("error", $msg);
            $this->custom_log->log($this->log_filename, $msg);
            $errors[] = $msg;
        }
        
        $serviceObject = $serviceCallback[0];
        
        $reportInfo = array(
            "errors" => array_merge($errors, $serviceObject->getErrors()),
            "subject" => $emailSubject,
            "successCount" => $serviceObject->successCount,
            "failureCount" => $serviceObject->failureCount,
            "url" => $serviceObject->url
        );
        $this->emailCronReport($reportInfo);
    }

    private function emailCronReport(array $reportInfo) {
        require_once(APPPATH . "/services/mail_service.php");
        $mailService = new Mail_Service();

        try {
            $mailService->mailReport($reportInfo);
        } catch (Exception $e) {
            $msg = sprintf("Unable to email cron report. %s
                    Stacktrace: %s", $e->getMessage(), $e->getTraceAsString());
            log_message("error", $msg);
            $this->custom_log->log($this->log_filename, $msg);
        }

        if ($mailService->isMailSent()) {
            $this->custom_log->log(
                    $this->log_filename,"Report sent to " . $mailService->emailAddress);
        } else {
            $this->custom_log->log(
                    $this->log_filename,"Failed to send report to " . $mailService->emailAddress);
        }

    }

  function cache_exchange_rates()
  {
    $this->load->model('Db_currency');
    $this->load->model('Hostelbookers_api');

    $location_id = 1126;
    $hostel_id   = 0;
    $hostel_price_field = "MINPRIVATEPRICE";
    $cached_hostel = array();

    foreach($this->Db_currency->get_all_currencies() as $db_currency)
    {
      set_time_limit(100);
      $results = $this->Hostelbookers_api->getLocationAvailability($location_id, get_date_default(30),1, "en", $db_currency->currency_code);

      if(isset($results["RESPONSE"]))
      {
        if($hostel_id == 0)
        {
          $cached_hostel = $results["RESPONSE"][0];

          $hostel_id = $cached_hostel["id"];

          //set price field to take or change hostel if all fields are 0
          foreach($results["RESPONSE"] as $hostel)
          {
            if(!empty($hostel["prices"]["CUSTOMER"]["MINPRIVATEPRICE"]) &&
               ($hostel["prices"]["CUSTOMER"]["MINPRIVATEPRICE"] > 0.0))
            {
              $cached_hostel = $hostel;
              $hostel_price_field = "MINPRIVATEPRICE";
              break;
            }
            elseif(!empty($hostel["prices"]["CUSTOMER"]["MINSHAREDPRICE"]) &&
                  ($hostel["prices"]["CUSTOMER"]["MINSHAREDPRICE"] > 0.0))
            {
              $cached_hostel = $hostel;
              $hostel_price_field = "MINSHAREDPRICE";
              break;
            }

          }

        }
        else
        {
          foreach($results["RESPONSE"] as $hostel)
          {
            if($hostel["id"]===$hostel_id)
            {
              $cached_hostel = $hostel;
              break;
            }

          }
        }

        if(strcasecmp($cached_hostel["prices"]["CUSTOMER"]["CURRENCY"], $db_currency->currency_code)==0)
        {
          //Ensure new price is not 0!
          if($cached_hostel["prices"]["CUSTOMER"][$hostel_price_field] > 0)
          {
            $this->Db_currency->update_hb_equivalent($db_currency->currency_code, $cached_hostel["prices"]["CUSTOMER"][$hostel_price_field]);
          }
        }
        sleep(1);
      }
    }

  }
  function my_error_handler($errno, $errstr, $errfile, $errline){
    $errno = $errno & error_reporting();
    if($errno == 0) return;
    if(!defined('E_STRICT'))            define('E_STRICT', 2048);
    if(!defined('E_RECOVERABLE_ERROR')) define('E_RECOVERABLE_ERROR', 4096);
    print "<pre>\n<b>";
    switch($errno){
      case E_ERROR:               print "Error";                  break;
      case E_WARNING:             print "Warning";                break;
      case E_PARSE:               print "Parse Error";            break;
      case E_NOTICE:              print "Notice";                 break;
      case E_CORE_ERROR:          print "Core Error";             break;
      case E_CORE_WARNING:        print "Core Warning";           break;
      case E_COMPILE_ERROR:       print "Compile Error";          break;
      case E_COMPILE_WARNING:     print "Compile Warning";        break;
      case E_USER_ERROR:          print "User Error";             break;
      case E_USER_WARNING:        print "User Warning";           break;
      case E_USER_NOTICE:         print "User Notice";            break;
      case E_STRICT:              print "Strict Notice";          break;
      case E_RECOVERABLE_ERROR:   print "Recoverable Error";      break;
      default:                    print "Unknown error ($errno)"; break;
    }
    print ":</b> <i>$errstr</i> in <b>$errfile</b> on line <b>$errline</b>\n";
    if(function_exists('debug_backtrace')){
    //print "backtrace:\n";
    $backtrace = debug_backtrace();
    array_shift($backtrace);
    foreach($backtrace as $i=>$l){
    print "[$i] in function <b>{$l['class']}{$l['type']}{$l['function']}</b>";
    if($l['file']) print " in <b>{$l['file']}</b>";
    if($l['line']) print " on line <b>{$l['line']}</b>";
              print "\n";
          }
      }
      print "\n</pre>";
      if(isset($GLOBALS['error_fatal'])){
          if($GLOBALS['error_fatal'] & $errno) die('fatal');
      }
  }

}
