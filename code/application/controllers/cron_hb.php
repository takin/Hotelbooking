<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cron_hb extends I18n_site
{
  const CRON_CODE = 'aEc3FvF6f754Bjida2QMp7gR';
  const DOWNLOAD_DIR = "cache_queries/staticfeeds";

  private $log_filename = "hb_cache";
  private $api_delay_retries = 2;
  private $api_max_retries   = 5;

  private $email_to_report_city = "technical@mcwebmanagement.com";

  private $static_feeds_to_keep = 12;

  private $lockfile = "lock.txt";
  private $lockfp = NULL;
  public $already_running = FALSE;

  function __construct()
  {
    parent::I18n_site();

    //Ensure this controller is called by server and that cron code is good
//     if(strcmp($_SERVER["REMOTE_ADDR"],"208.113.48.86")!=0)
    if((strcmp($_SERVER["REMOTE_ADDR"],$_SERVER["SERVER_ADDR"])!=0) OR
      (strcmp($this->uri->segment(3,""),self::CRON_CODE)!=0))
    {
      $this->_log_cron_job(date("Y-m-d h:i:s A").": Bad attempt to run cron jobs from ".$_SERVER["REMOTE_ADDR"]." at ".current_url()."\n");
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
		$request_time= microtime();
      $response = $this->Hostelbookers_api->getNationalities("en");
      if($response !== false)
      { 
		$response_time=microtime()-$request_time." ms ";
		
		$this->Db_hb_country->parse_nationalities($response, "en");
        $this->custom_log->log($this->log_filename,"HB API nationalities update for language -> "."en");
        $this->custom_log->log("audit", ' > hb  api > HB API nationalities update for language '.$response_time);
      }
      else
      {
		 
		  
        $this->custom_log->log($this->log_filename,"HB API bad response for nationalities of language -> "."en");
      }
//     }

  }

  public function hb_hostels_get()
  {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    //To force update of feed file and parsing even it the file is the same put this to TRUE
    $forceupdate = FALSE;

    $this->wordpress->load_wordpress_db('wpblog_reviews');

    $latestfeedurl = $this->wordpress->get_option('aj_hb_static_feed_url');
    $lastfeedindb  = $this->wordpress->get_option('aj_hb_static_feed_url_in_db');

//	$latestfeedurl = "http://feeds.hostelbookers.com/affiliate/mcweb/20121201.zip";

    if(empty($latestfeedurl) ||
        ((!$forceupdate) && strcasecmp($latestfeedurl,$lastfeedindb)==0))
    {
      //parsing not needed if URL is empty or not changed
      $this->custom_log->log($this->log_filename,"HB API data from static update not needed.");
      return TRUE;
    }

    $this->load->library('custom_log');
    $this->log_filename.= "_staticfeeds".date("Y");
    $this->custom_log->log($this->log_filename,"Updating HB API data from static feed");

    $this->lock();
    if($this->already_running)
    {
      $this->custom_log->log($this->log_filename,__FUNCTION__." already running exitting...");
      exit;
    }

    ini_set('memory_limit', '512M');
//     ini_set('memory_limit', '1024M');

    $this->load->helper('memory_helper');
    $this->load->helper('file');

//     $link = 'http://feeds.hostelbookers.com/affiliate/mcweb/20110601.zip';

    $localdest = FCPATH.self::DOWNLOAD_DIR;
    $zipfilename = "staticfeed".date("YMd").".zip";

    $this->custom_log->log($this->log_filename,"Downloading feed from ".$latestfeedurl);
    if(stream_copy($latestfeedurl,$localdest."/".$zipfilename) > 0)
    {
      $this->custom_log->log($this->log_filename,"Feed download complete");
      if ($zip = zip_open($localdest."/".$zipfilename))
      {
        $translation_zip_entry = array('en' => null,
                                       'fr' => null,
                                       'es' => null,
                                       'de' => null,
                                       'it' => null,
                                       'pt' => null
                                       );

        $xml_file = false;
        //Taking biggest file as static feed to import
        //and other files as translation files
        while(($zip_entry = zip_read($zip)) !== false)
        {
          if(($xml_file === false) ||
             (zip_entry_filesize($zip_entry) > zip_entry_filesize($xml_file)))
          {
            $xml_file = $zip_entry;
          }

          if( preg_match("/EN.xml$/",zip_entry_name($zip_entry)) &&
              ( is_null($translation_zip_entry['en']) || (zip_entry_filesize($zip_entry) < zip_entry_filesize($translation_zip_entry['en']))) )
          {
            $translation_zip_entry['en'] = $zip_entry;
          }
          elseif(preg_match("/FR.xml$/",zip_entry_name($zip_entry)))
          {
            $translation_zip_entry['fr'] = $zip_entry;
          }
          elseif(preg_match("/ES.xml$/",zip_entry_name($zip_entry)))
          {
            $translation_zip_entry['es'] = $zip_entry;
          }
          elseif(preg_match("/DE.xml$/",zip_entry_name($zip_entry)))
          {
            $translation_zip_entry['de'] = $zip_entry;
          }
          elseif(preg_match("/IT.xml$/",zip_entry_name($zip_entry)))
          {
            $translation_zip_entry['it'] = $zip_entry;
          }
          elseif(preg_match("/PT.xml$/",zip_entry_name($zip_entry)))
          {
            $translation_zip_entry['pt'] = $zip_entry;
          }
        }

//         $this->_import_hb_translations($translation_zip_entry);
//         exit;

        $xml_file = $translation_zip_entry['en'];
        if($xml_file)
        {
          $this->custom_log->log($this->log_filename,"Parsing as static feed file ".zip_entry_name($xml_file));

          //TONOTICE! the file can be HEAVY and cause memory exceeding limit
          // Might be nice to consider a methos consuming less memory
          // Like extracting via command line and reading by step
          $xml_file = zip_entry_read ($xml_file,zip_entry_filesize($xml_file));

          $this->load->model("Db_hb_hostel");
          $this->Db_hb_hostel->set_logfile($this->log_filename);
          $this->Db_hb_hostel->parse_static_feed($xml_file);
          $this->wordpress->set_option('aj_hb_static_feed_url_in_db',$latestfeedurl);
        }
        else
        {
          $this->custom_log->log($this->log_filename,"Error reading zip file ".$zipfilename);
        }

        zip_close($zip);
      }
      else
      {
        $this->custom_log->log($this->log_filename,"Error opening zip file ".$zipfilename);
      }

      limit_file_count_of_name($localdest,"staticfeed",$this->static_feeds_to_keep);
    }
    else
    {
      $this->custom_log->log($this->log_filename,"Error downloading zip file from ".$latestfeedurl);
    }

    $this->unlock();
  }

  function _xml_get_country_node($dom, $xmlobject, $country_id)
  {
    $country_node = null;

    while ($xmlobject->name === 'country')
    {
      $country_node = simplexml_import_dom($dom->importNode($xmlobject->expand(), true));
      if((int)$country_node["id"] === (int)$country_id)
      {
        return $country_node;
      }

      $xmlobject->next('country');
    }

    return $country_node;
  }

  function _detect_google_source($translation_text)
  {

    $googletags = array("<strong>Ce texte a été traduit en utilisant Google Translator</strong>",
                        "<strong>Este texto ha sido traducido mediante Google Translator</strong>",
                        "<strong>Dieser Text wurde mit Google Translator übersetzt</strong>",
                        "<strong>Questo testo è stato tradotto con Google Translator</strong>",
                        "<strong>Este texto foi traduzido usando Google Translator</strong>"
    );

    foreach($googletags as $googletag)
    {
      if(stristr($translation_text,$googletag) !== false)
      {
        return true;
      }
    }
    return false;
  }

  function _detect_bing_source($translation_text)
  {

    $bingtags = array("Ce texte a été traduit en utilisant Bing Translator",
                        "Este texto ha sido traducido mediante Bing Translator",
                        "Dieser Text wurde mit Bing Translator übersetzt",
                        "Questo testo è stato tradotto con Bing Translator",
                        "Este texto foi traduzido usando Bing Translator"
    );

    foreach($bingtags as $bingtag)
    {
      if(stristr($translation_text,$bingtag) !== false)
      {
        return true;
      }
    }
    return false;
  }

  function _strip_bing_tag($translation_text)
  {
    $bingtags = array("Ce texte a été traduit en utilisant Bing Translator",
                      "Este texto ha sido traducido mediante Bing Translator",
                      "Dieser Text wurde mit Bing Translator übersetzt",
                      "Questo testo è stato tradotto con Bing Translator",
                      "Este texto foi traduzido usando Bing Translator"
    );

    $translation_text = str_replace($bingtags,"",$translation_text);
    return $translation_text;
  }

  function _strip_google_tag($translation_text)
  {
    $googletags = array("<strong>Ce texte a été traduit en utilisant Google Translator</strong>",
                        "<strong>Este texto ha sido traducido mediante Google Translator</strong>",
                        "<strong>Dieser Text wurde mit Google Translator übersetzt</strong>",
                        "<strong>Questo testo è stato tradotto con Google Translator</strong>",
                        "<strong>Este texto foi traduzido usando Google Translator</strong>"
									    );

    $translation_text = str_replace($googletags,"",$translation_text);
    return $translation_text;
  }


  //Did not fully test probably certain this does not works right
  function _trim_translation($string)
  {
    $string = trim($string);
    $string = preg_replace('/\<br\>$/i'  , '', $string);
    $string = preg_replace('/\<br \/\>$/i', '', $string);
    $string = preg_replace('/\<br\/\>$/i' , '', $string);

    return trim($string);
  }

  function _cache_field_translation($text, $orig_lang, $trans_lang, $tag)
  {
    $text[$orig_lang]  = $this->_trim_translation((string) $text[$orig_lang]);
    $text[$trans_lang] = $this->_trim_translation((string) $text[$trans_lang]);

//     debug_dump($text);
    if( !empty($text) &&
        !empty($text[$orig_lang]) &&
        !empty($text[$trans_lang]) &&
        (strcmp($text[$orig_lang],$text[$trans_lang])!=0))
    {
      $source_id = 14;
      if($this->_detect_google_source($text[$trans_lang]))
      {
        $text[$trans_lang] = $this->_strip_google_tag($text[$trans_lang]);
        $text[$trans_lang] = $this->_trim_translation($text[$trans_lang]);
        $source_id = 15;
      }
      elseif($this->_detect_bing_source($text[$trans_lang]))
      {
        $text[$trans_lang] = $this->_strip_bing_tag($text[$trans_lang]);
        $text[$trans_lang] = $this->_trim_translation($text[$trans_lang]);
        $source_id = 16;
      }

      if(strcmp(br2nl($text[$orig_lang]),br2nl($text[$trans_lang])) != 0 )
      {
        $this->load->model('i18n/db_translation_cache');
        $this->db_translation_cache->cache_lang_array($text, $orig_lang, $orig_lang, $tag, $source_id);
      }
      else
      {
//         echo "no translation detected<br>";
      }
    }
  }

  function _cache_hb_property_translation($orig_property, $orig_lang, $trans_property, $trans_lang)
  {
    if((int)$orig_property["id"] === (int)$trans_property["id"])
    {

      debug_dump("-------------------------------------------------");
      debug_dump($orig_property["id"]." - $trans_lang - ".$orig_property->name);

      $tag = "HB short description";
      $text[$orig_lang]  = (string) $orig_property->shortdescription;
      $text[$trans_lang] = (string) $trans_property->shortdescription;
      $this->_cache_field_translation($text,$orig_lang,$trans_lang,$tag);

      $tag = "HB full description";
      $text[$orig_lang]  = (string) $orig_property->longdescription;
      $text[$trans_lang] = (string) $trans_property->longdescription;
      $this->_cache_field_translation($text,$orig_lang,$trans_lang,$tag);

      $tag = "HB property directions";
      $text[$orig_lang]  = (string) $orig_property->direction;
      $text[$trans_lang] = (string) $trans_property->direction;
      $this->_cache_field_translation($text,$orig_lang,$trans_lang,$tag);

      $tag = "HB location info";  //added custom
      $text[$orig_lang]  = (string) $orig_property->locationinfo;
      $text[$trans_lang] = (string) $trans_property->locationinfo;
      $this->_cache_field_translation($text,$orig_lang,$trans_lang,$tag);

      $tag = "HB accomodation description";
      $text[$orig_lang]  = (string) $orig_property->accommodationinfo;
      $text[$trans_lang] = (string) $trans_property->accommodationinfo;

      $tag = "HB property important info";
      $text[$orig_lang]  = (string) $orig_property->importantinfo;
      $text[$trans_lang] = (string) $trans_property->importantinfo;
      $this->_cache_field_translation($text,$orig_lang,$trans_lang,$tag);

      return true;
    }
    else
    {
      $this->custom_log->log($this->log_filename,"Error inserting translation $trans_lang of property ".$orig_property["id"]);
      return false;
    }
  }

  function _xml_get_property_node_from_country($country_node, $property_number)
  {
    $property_node = null;
    foreach($country_node->location as $city)
    {
      foreach($city->property as $property_orig_node)
      {
        if((int)$property_orig_node['id'] === (int)$property_number )
        {
          return $property_orig_node;
        }
      }
    }
    return $property_node;
  }

  function _import_hb_translations($zip_entries)
  {

    $this->CI->db->save_queries = false;

    ini_set('memory_limit', '512M');
    set_error_handler(array(&$this, 'my_error_handler'));
    $this->load->helper('xml');

    //unzip using less mem
    $localdest = FCPATH.self::DOWNLOAD_DIR;
    foreach($zip_entries as $lang => $zip_entry)
    {
      if(file_exists($localdest."/last_".$lang."_feed.xml")) continue;
      $unzipped = fopen($localdest."/last_".$lang."_feed.xml",'wb');
      $size = zip_entry_filesize($zip_entry);
      while($size > 0){
        $chunkSize = ($size > 10240) ? 10240 : $size;
        $size -= $chunkSize;
        $chunk = zip_entry_read($zip_entry, $chunkSize);
        if($chunk !== false) fwrite($unzipped, $chunk);
      }

      fclose($unzipped);
    }
//     $en_file_content = zip_entry_read ($zip_entries['en'],zip_entry_filesize($zip_entries['en']));
    $en_file_content = $localdest."/last_en_feed.xml";
    $en_file = new XMLReader;
    if($en_file->open($en_file_content) === false)
    {
      $this->custom_log->log($this->log_filename,"Error getting XML of EN file");
      exit;
    }
    unset($en_file_content);
    $doc_en = new DOMDocument;
    while ($en_file->read() && $en_file->name !== 'country');
//     while ($en_file->read() && $en_file->name !== 'location');

//     $fr_file_content = zip_entry_read ($zip_entries['fr'],zip_entry_filesize($zip_entries['fr']));
    $fr_file_content = $localdest."/last_fr_feed.xml";
    $fr_file = new XMLReader;
    if($fr_file->open($fr_file_content) === false)
    {
      $this->custom_log->log($this->log_filename,"Error getting XML of FR file");
      exit;
    }
    unset($fr_file_content);
    $doc_fr = new DOMDocument;
    while ($fr_file->read() && $fr_file->name !== 'country');

//     $es_file_content = zip_entry_read ($zip_entries['es'],zip_entry_filesize($zip_entries['es']));
    $es_file_content = $localdest."/last_es_feed.xml";
    $es_file = new XMLReader;
    if($es_file->open($es_file_content) === false)
    {
      $this->custom_log->log($this->log_filename,"Error getting XML of ES file");
      exit;
    }
    unset($es_file_content);
    $doc_es = new DOMDocument;
    while ($es_file->read() && $es_file->name !== 'country');

//     $de_file_content = zip_entry_read ($zip_entries['de'],zip_entry_filesize($zip_entries['de']));
    $de_file_content = $localdest."/last_de_feed.xml";
    $de_file = new XMLReader;
    if($de_file->open($de_file_content) === false)
    {
      $this->custom_log->log($this->log_filename,"Error getting XML of DE file");
      exit;
    }
    unset($de_file_content);
    $doc_de = new DOMDocument;
    while ($de_file->read() && $de_file->name !== 'country');

//     $it_file_content = zip_entry_read ($zip_entries['it'],zip_entry_filesize($zip_entries['it']));
    $it_file_content = $localdest."/last_it_feed.xml";
    $it_file = new XMLReader;
    if($it_file->open($it_file_content) === false)
    {
      $this->custom_log->log($this->log_filename,"Error getting XML of IT file");
      exit;
    }
    unset($it_file_content);
    $doc_it = new DOMDocument;
    while ($it_file->read() && $it_file->name !== 'country');

//     $pt_file_content = zip_entry_read ($zip_entries['pt'],zip_entry_filesize($zip_entries['pt']));
    $pt_file_content = $localdest."/last_pt_feed.xml";
    $pt_file = new XMLReader;
    if($pt_file->open($pt_file_content) === false)
    {
      $this->custom_log->log($this->log_filename,"Error getting XML of PT file");
      exit;
    }
    unset($pt_file_content);
    $doc_pt = new DOMDocument;
    while ($pt_file->read() && $pt_file->name !== 'country');

    echo "mem: ".memory_usage_in_mb()."<br>";
    $count = 0;
    while ($en_file->name === 'country')
    {
        $country_node = simplexml_import_dom($doc_en->importNode($en_file->expand(), true));

        $country_fr_node = $this->_xml_get_country_node($doc_fr, $fr_file, (int)$country_node["id"]);
        $country_es_node = $this->_xml_get_country_node($doc_es, $es_file, (int)$country_node["id"]);
        $country_de_node = $this->_xml_get_country_node($doc_de, $de_file, (int)$country_node["id"]);
        $country_it_node = $this->_xml_get_country_node($doc_it, $it_file, (int)$country_node["id"]);
        $country_pt_node = $this->_xml_get_country_node($doc_pt, $pt_file, (int)$country_node["id"]);

        echo "country: ".memory_usage_in_mb()."<br>";
        debug_dump("en: ".(string)$country_node->name);
        debug_dump("fr: ".(string)$country_fr_node->name);
        debug_dump("es: ".(string)$country_es_node->name);
        debug_dump("de: ".(string)$country_de_node->name);
        debug_dump("it: ".(string)$country_it_node->name);
        debug_dump("pt: ".(string)$country_pt_node->name);
        foreach($country_node->location as $city)
        {
            foreach($city->property as $property_orig_node)
            {
              set_time_limit(30);
              $count++;

              $property_fr_node = $this->_xml_get_property_node_from_country($country_fr_node,(int)$property_orig_node["id"]);
              $property_es_node = $this->_xml_get_property_node_from_country($country_es_node,(int)$property_orig_node["id"]);
              $property_de_node = $this->_xml_get_property_node_from_country($country_de_node,(int)$property_orig_node["id"]);
              $property_it_node = $this->_xml_get_property_node_from_country($country_it_node,(int)$property_orig_node["id"]);
              $property_pt_node = $this->_xml_get_property_node_from_country($country_pt_node,(int)$property_orig_node["id"]);

              echo "mem: ".memory_usage_in_mb()."<br>";
              set_time_limit(30);
              $this->_cache_hb_property_translation($property_orig_node,'en',$property_fr_node,'fr');
              set_time_limit(30);
              $this->_cache_hb_property_translation($property_orig_node,'en',$property_es_node,'es');
              set_time_limit(30);
              $this->_cache_hb_property_translation($property_orig_node,'en',$property_de_node,'de');
              set_time_limit(30);
              $this->_cache_hb_property_translation($property_orig_node,'en',$property_it_node,'it');
              set_time_limit(30);
              $this->_cache_hb_property_translation($property_orig_node,'en',$property_pt_node,'pt');

            }
        }

      $en_file->next('country');
    }
    echo "end of xmls $count properties ".memory_usage_in_mb()."<br>";
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
