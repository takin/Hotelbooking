<?php
class Microsofttranslator extends CI_Model {

 /**
  * The URL that we use to access the HTTP service wsdl.
  *
  * @const MS_API_HTTP_URL
  */
  const MS_API_HTTP_URL = 'http://api.microsofttranslator.com/V2/Http.svc';
  //const MS_API_HTTP_URL = 'http://localhost:8080/V2/Http.svc';

  /**
  * The application scope (the URL of the application endpoint)
  *
  * @var String
  */
  private $str_application_scope = "http://api.microsofttranslator.com";

  /**
  * The application token
  *
  * @var String
  */
  private $app_token = "";
  /*
   *
   */
  private $CI;


  const MAX_QUERY_LENGTH = 5000;
  const DB_CACHE_ENABLE  = TRUE;

  private $FromLang     = "";
  private $ToLang       = "fr";

  private $batch_results = Array();
  private $results_count = 0;
  private $translation_text_count = 0;

  private $batchText     = Array();
  private $batchTranslationText = Array();
  private $batchTextType = Array();
  private $batch_text_length = 0;

  private $db_cache_enable = TRUE;

  private $source_account = NULL;
  private $default_source_slug = 'default';

  private $quota_reached = FALSE;

  /**
	* The constructor - pass your Azure credentials in here
	*
	* @param $str_azure_client_id
	* @param $str_azure_client_secret
	*/
  public function __construct()
  {
    parent::__construct();
    $this->load->library('custom_log');
    $this->initialize();
  }

  /**
  * initialize the connection
  *
	* @param String $str_method
	*/
  protected function initialize()
  {
    $this->CI = &get_instance();
    $this->CI->load->model('microsoft/Azuremarketplaceauthenticator');

    $this->CI->load->model('i18n/db_translation_cache');

    if(empty($this->CI->translation_source_slug))
    {
      $this->CI->translation_source_slug = $this->default_source_slug;
    }

    $this->source_account = $this->CI->db_translation_cache->get_source($this->CI->translation_source_slug);

    if(empty($this->source_account))
    {
      log_message('error','Invalid translation source slug -> '.$this->CI->translation_source_slug);
      //Taking default account
      $this->source_account = $this->CI->db_translation_cache->get_source($this->default_source_slug);
    }

    log_message('debug', "MS Translator source account key slug: " . $this->source_account->key_slug);

    $this->CI->Azuremarketplaceauthenticator->initialize($this->source_account->client_id,
                                                         $this->source_account->client_secret,
                                                         $this->str_application_scope);

    if(is_null($this->CI->translation_quota))
    {
      $this->quota_reached = FALSE;
    }
    else
    {
      $this->quota_reached = TRUE;
    }

    $this->enable_db_cache();
//     $this->disable_db_cache();
  }

  public function enable_db_cache()
  {
    $this->db_cache_enable = TRUE;
  }
  public function disable_db_cache()
  {
    $this->db_cache_enable = FALSE;
  }
/*
  private function make_get_request($api_function_name, $data)
  {
    $this->app_token = $this->CI->Azuremarketplaceauthenticator->get_token();

    $data = http_build_query($data);
    debug_dump($data);
    $response = false;
    $url = self::MS_API_HTTP_URL.'/'.$api_function_name.'?'.$data;
// debug_dump($url);
    $obj_connection = curl_init();
    curl_setopt($obj_connection, CURLOPT_URL, $url);
    curl_setopt($obj_connection, CURLOPT_HEADER, 0);
    curl_setopt($obj_connection,CURLOPT_HTTPHEADER,array (
                "Authorization: Bearer ". $this->app_token."\r\n"
    ));
    curl_setopt($obj_connection, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($obj_connection, CURLOPT_RETURNTRANSFER, 1);
    //    curl_setopt($obj_connection, CURLOPT_POSTFIELDS, $str_query);
    curl_setopt($obj_connection, CURLOPT_SSL_VERIFYPEER, FALSE);
    //    curl_setopt($obj_connection, CURLOPT_POST, TRUE);

    curl_setopt($obj_connection, CURLOPT_VERBOSE, TRUE);

    $str_response = curl_exec($obj_connection);
    curl_close($obj_connection);
    return $str_response;
  }
*/
  public function make_xml_request($api_function_name, $data)
  {
    $request_time= microtime(true);
    $this->app_token = $this->CI->Azuremarketplaceauthenticator->get_token();

    $this->CI->benchmark->mark('start');

    $http_header = array ('Authorization: Bearer '. $this->app_token,
                          'Content-Type: application/xml; charset=UTF-8');
    $ch = curl_init ();
    curl_setopt($ch, CURLOPT_URL, self::MS_API_HTTP_URL.'/'.$api_function_name );
    curl_setopt($ch, CURLOPT_HTTPHEADER,$http_header);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_FAILONERROR, false );
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
    curl_setopt($ch, CURLOPT_HEADER, FALSE );

    $curl_result = curl_exec ( $ch );
    $http_status = curl_getinfo($ch,CURLINFO_HTTP_CODE);
	if ($http_status == 200)
	{
	  $response_time=microtime(true)-$request_time;
      $response_time  = number_format($response_time,5,'.',' ');
      $response_time =  $response_time." ms ";
      $this->custom_log->log("audit", 'Microsoft Bing API make_xml_request '.$response_time);
	  curl_close ( $ch );
	  return $curl_result;
	}
	else
    {
      log_message('error', "MS Translator function $api_function_name error: " . $http_status);
//       debug_dump("MS Translator function $api_function_name error: " . curl_error ( $ch ). " -> request: ".$data ,"184.161.43.99");
      curl_close ( $ch );
      return FALSE;
    };
  }

  public function google_to_ms_lang($google_lang_letters)
  {
    switch(strtolower($google_lang_letters))
    {
      case strtolower("iw"):
        return "he";
      case strtolower("zh-CN"):
        return "zh-CHS";
      case strtolower("zh-TW"):
        return "zh-CHT";
    }
    return strtolower($google_lang_letters);
  }

  public function setLangs($to, $from = '')
  {
    $this->FromLang = $from;
    $this->ToLang   = $to;
  }
/*
  public function translate($text, $to = '', $from = '')
  {
    if(!empty($to) || !empty($from))
    {
      $this->setLangs($to,$from);
    }

	if ($this.FromLang === $this.ToLang)
	{
		log_message('error', "MS Translator function Request to translate to the same language");
		return $text;
	}

    if(is_array($text))
    {
      return $this->translateArray($text, $this->ToLang, $this->FromLang);
    }

    $data = array('appId' => '',
      						'from' => $this->google_to_ms_lang($this->FromLang),
                  'to' => $this->google_to_ms_lang($this->ToLang),
  								'text' => $text,
      						'contentType' => NULL,
                  'category' => NULL);

    return $this->make_request('Translate',$data);
  }
*/
  public function translateArray($translate_data, $to = '', $from = '')
  {
    if(!empty($to) || !empty($from))
    {
      $this->setLangs($to,$from);
    }

    $this->load->helper('xml');

    $xmldata = "<?xml version='1.0'?>";
    $xmldata .= "<TranslateArrayRequest>";
    $xmldata .= "<AppId></AppId>";
    $xmldata .= "<From>".$this->google_to_ms_lang($this->FromLang)."</From>";
    $xmldata .= "<Options>";
//     $xmldata .= "<Category xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\"></Category>";
    $xmldata .= "<ContentType xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\">";
//     $xmldata .= "text/plain";
    $xmldata .= "text/html";
    $xmldata .= "</ContentType>";
//     $xmldata .= "<ReservedFlags xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\"></ReservedFlags>";
//     $xmldata .= "<State xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\"></State>";
//     $xmldata .= "<Uri xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\"></Uri>";
//     $xmldata .= "<User xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\"></User>";
    $xmldata .= "</Options>";
    $xmldata .= "<Texts>";
    foreach($this->batchTranslationText as $data)
    {

		$this->CI->custom_log->log("bing-translation",": From ".$this->FromLang." -> To ".$this->ToLang." original text :".$data);
		$xmldata .= "<string xmlns=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\">".xml_convert($data)."</string>";
    }

    $xmldata .= "</Texts>";
    $xmldata .= "<To>".$this->google_to_ms_lang($this->ToLang)."</To>";
    $xmldata .= "</TranslateArrayRequest>";

    $results['responseData'] = array();
    $results['responseDetails'] = NULL;
    $results['responseStatus']  = 200;

    if(count($this->batchTranslationText)>0)
    {
      $remote_translations = $this->make_xml_request('TranslateArray',$xmldata);

      if($remote_translations === FALSE)
      {

        //TODO get real error ffrom curl_error
        $results['responseStatus']  = 400;
        return $results;
      }

      $z = new XMLReader;
      $doc = new DOMDocument;
      $z->xml($remote_translations);
      unset($remote_translations);

      $resultnode = 'TranslateArrayResponse';

      //Imitate google API output
      //and merge with already cache results
      $i = 0;

      while ($z->read() && ($z->name !== $resultnode));

      while (($z->name === $resultnode))
      {
        $results['responseData'][$i]['responseDetails'] = "From MS Translator";
        $results['responseData'][$i]['source'] = $this->source_account->source." - ".$this->source_account->key_slug;
        $results['responseData'][$i]['tag_linked'] = $translate_data[$i]->tag;

        $trans_object = simplexml_import_dom($doc->importNode($z->expand(), true));
       log_message("error","tr count $i");

        if($trans_object === FALSE)
        {
          $results['responseData'][$i]['responseStatus']  = 400;
        }
        else
        {
          $results['responseData'][$i]['responseStatus']  = 200;
          $results['responseData'][$i]['responseData']['translatedText'] = (string)$trans_object->TranslatedText;
          $results['responseData'][$i]['responseData']['detectedSourceLanguage'] = $this->google_to_ms_lang((string)$trans_object->From);
        }

        //cache translation
        if(($this->db_cache_enable === TRUE) &&
        (!empty($results['responseData'][$i]['responseData']['translatedText'])) &&
        (!empty($results['responseData'][$i]['responseData']['detectedSourceLanguage'])) )
        {
          //If From lang is set via human trust this over the detected language by machine
          $fromlang_cached = $results['responseData'][$i]['responseData']['detectedSourceLanguage'];
          if(!empty($this->FromLang))
          {
            $fromlang_cached = $this->FromLang;
          }
          $this->CI->db_translation_cache->cache_translation($translate_data[$i]->text,
                                                             $results['responseData'][$i]['responseData']['translatedText'],
                                                             $this->ToLang,
                                                             $fromlang_cached,
                                                             $this->source_account->source_id,
                                                             $translate_data[$i]->tag );
        }
        log_message("error","tr response st : ".$results['responseData'][$i]['responseStatus']);
        $i++;

        $z->next($resultnode);
      }
      unset($z);
    }

    return $results;
  }

  /*----- Translation functions -----*/

  public function startBatch($toLang="", $fromLang="")
  {
	  log_message("error",'start batch');

    if($toLang != "")
    {
      $this->ToLang = $toLang;
    }
    if($fromLang != "")
    {
      $this->FromLang = $fromLang;
    }

    $this->clearBatch();

  }
  function clearBatch()
  {
    $this->clearBatchText();
    $this->batch_results = Array();
    $this->batch_results['responseData'] = Array();
    $this->batch_results['responseDetails'] = NULL;
    $this->batch_results['responseStatus']  = 200;
    $this->results_count = 0;
    $this->translation_text_count=0;
  }
  function clearBatchText()
  {
    $this->batchText = Array();
    $this->batchTranslationText = Array();
    $this->batch_text_length = 0;
  }

  function addTextForBatch($text = "", $tag = "")
  {

    $text_length = strlen($text);

    //check if in translation cache else add text for remote API translation
    if($this->db_cache_enable === TRUE)
    {

      $textdb = $text;
      if((self::MAX_QUERY_LENGTH > 0) && ($text_length > self::MAX_QUERY_LENGTH))
      {
        $textdb = mb_substr($text, 0, self::MAX_QUERY_LENGTH-10,"UTF-8");
      }
log_message("debug"," Translation language From ".$this->FromLang.' To '. $this->ToLang." $text :::".$textdb);
      $trans_cached = $this->CI->db_translation_cache->get_translation($textdb,$this->ToLang);

      //If cache found put translation in batch results and return
      if(!empty($trans_cached))
      {
        $tags = explode("|",$trans_cached->tags);
        //If tag is not associate to translation link it
        if(!empty($tag) && !in_array($tag, $tags))
        {
          $this->CI->db_translation_cache->add_tag_to_translation($tag,$trans_cached->translation_id);
          $tags[] = $tag;
        }

        $this->batch_results['responseData'][$this->results_count]['responseDetails'] = "From DB cache";
        $this->batch_results['responseData'][$this->results_count]['source']  = $trans_cached->source." - ".$trans_cached->key_slug;
        $this->batch_results['responseData'][$this->results_count]['tags']     = $tags;
        $this->batch_results['responseData'][$this->results_count]['responseStatus']  = 200;
        $this->batch_results['responseData'][$this->results_count]['responseData']['translatedText']         = $trans_cached->translation;
        $this->batch_results['responseData'][$this->results_count]['responseData']['detectedSourceLanguage'] = $trans_cached->ref_lang_code;

        $this->results_count++;
        return TRUE;
      }
    }

    if((self::MAX_QUERY_LENGTH > 0) )
    {
      if($text_length > self::MAX_QUERY_LENGTH)
      {
        log_message("debug",current_url().": MAXIMUM MS translator QUERY Reached: $text_length characters");
        $text = mb_substr($text, 0, self::MAX_QUERY_LENGTH-10,"UTF-8");
        $text_length = strlen($text);
      }
      if(($this->batch_text_length + $text_length) > self::MAX_QUERY_LENGTH)
      {
        $this->batch_translate();
      }
    }

    //If NOT quota reached
    if($this->quota_reached === FALSE)
    {
      //Initialize response data
      $this->batch_results['responseData'][$this->results_count] = array();
      $this->batch_results['responseData'][$this->results_count]['responseDetails'] = "Remote Translation Needed";
      $this->batch_results['responseData'][$this->results_count]['responseStatus']  = 0;


      //Set data for remote translation request
      $this->batch_text_length += $text_length;
      $translation_data = new stdClass();
      $translation_data->text = $text;
      $translation_data->tag  = $tag;
      //array_push($this->batchTranslationText,$text);
      if(!in_array($text,$this->batchTranslationText)){
		  array_push($this->batchTranslationText,$text);

	  }
	  array_push($this->batchText,$translation_data);
      $this->batch_results['responseData'][$this->results_count]=$text;
       $this->results_count++;
    }
    else
    {
      //Initialize response data only
      $this->batch_results['responseData'][$this->results_count] = array();
      $this->batch_results['responseData'][$this->results_count]['data']['text'] = (string)$text;
      $this->batch_results['responseData'][$this->results_count]['data']['tag'] = $tag;
      $this->batch_results['responseData'][$this->results_count]['responseDetails'] = "The site reached your translation quota.";
      $this->batch_results['responseData'][$this->results_count]['responseStatus']  = 0;
      $this->results_count++;
    }
  }

  function end_batch()
  {

    $batch_result = $this->batch_translate();
//     debug_dump("count of results: :".$this->results_count,"184.161.43.99");
    $this->clearBatch();
    return $batch_result;
  }

  function batch_translate()
  {
	  if($this->config->item('bing_translation')==FALSE)
	  {
      return false;
      }

    if(count($this->batchText) > 0 )
    {

//       $this->benchmark->mark('google_req');
      $contents = $this->translateArray($this->batchText);
//       $this->benchmark->mark('google_req_end');
//       debug_dump("MS response time".$this->benchmark->elapsed_time('google_req', 'google_req_end'),"184.161.43.99");
// debug_dump($contents);

      $c = 0;
      foreach($this->batch_results['responseData'] as $i => $translation_data)
      {
		  if($this->config->item('translationLog')==TRUE){
          $this->CI->custom_log->log("bing-translation","From: ".$this->FromLang." To: ". $this->ToLang." string: ".$this->batchText[$c]->text);
          }
        if($translation_data['responseDetails'] === "Remote Translation Needed")
        {
		  if($contents === FALSE)
          {
            $this->batch_results['responseData'][$i]['responseDetails'] = "Error from MS translator API";
            $this->batch_results['responseData'][$i]['responseStatus']  = 400;
          }
          elseif(!empty($contents['responseData'][$c]))
          {
            $this->batch_results['responseData'][$i] =  $contents['responseData'][$c];

            $c++;
          }
        }
      }

      $this->clearBatchText();

      return $this->batch_results;
    }
    elseif($this->results_count > 0)
    {
      return $this->batch_results;
    }

    return NULL;
  }
}
?>
