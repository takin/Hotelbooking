<?php
/*
 *
 * TONOTICE:
 *   This does not add translation in cache DB yet
 */
class Microsofttranslator {

  const TEXT_TABLE     = 'translations';
  const SOURCE_TABLE   = 'sources';
  const TAG_TABLE      = 'tags';
  const TAG_TRANSLATION_TABLE      = 'tags_of_translations';

 /**
  * The URL that we use to access the HTTP service wsdl.
  *
  * @const MS_API_HTTP_URL
  */
  const MS_API_HTTP_URL = 'http://api.microsofttranslator.com/V2/Http.svc';

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
  private $transDB;


  const MAX_QUERY_LENGTH = 5000;
  const DB_CACHE_ENABLE  = TRUE;

  private $FromLang     = "";
  private $ToLang       = "fr";

  private $batch_results = Array();
  private $results_count = 0;

  private $batchText     = Array();
  private $batchTextType = Array();
  private $batch_text_length = 0;

  private $db_cache_enable = TRUE;

  private $source_account = NULL;
  private $default_source_slug = 'default';

  private $azuremarketplaceauth;
  /**
	* The constructor - pass your Azure credentials in here
	*
	* @param $str_azure_client_id
	* @param $str_azure_client_secret
	*/
  public function __construct($translation_dbconn, $key_slug)
  {
    $this->initialize($translation_dbconn, $key_slug);
  }

  /**
  * initialize the connection
  *
	* @param String $str_method
	*/
  protected function initialize($translation_dbconn, $key_slug)
  {
    $this->transDB = $translation_dbconn;

    require_once("ms_azureauth.php");
    $this->azuremarketplaceauth = new Azuremarketplaceauthenticator();

    $this->source_account = $this->get_ms_source($key_slug);
    if(empty($this->source_account))
    {
//       log_message('error','Invalid translation source slug -> '.$key_slug);
//       Taking default account
      $this->source_account = $this->get_ms_source($this->default_source_slug);
    }
    $this->azuremarketplaceauth->initialize($this->source_account->client_id,
                                             $this->source_account->client_secret,
                                             $this->str_application_scope);

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
    $this->app_token = $this->azuremarketplaceauth->get_token();

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
    $this->app_token = $this->azuremarketplaceauth->get_token();

    $http_header = array ('Authorization: Bearer '. $this->app_token,
                          'Content-Type: application/xml; charset=UTF-8');
    $ch = curl_init ();
    curl_setopt($ch, CURLOPT_URL, self::MS_API_HTTP_URL.'/'.$api_function_name );
    curl_setopt($ch, CURLOPT_HTTPHEADER,$http_header);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_FAILONERROR, true );
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
    curl_setopt($ch, CURLOPT_HEADER, FALSE );

    $curl_result = curl_exec ( $ch );

    if (false === $curl_result)
    {
//       log_message('error', "MS Translator function $api_function_name error: " . curl_error ( $ch ). " -> request: ".$data );
//       debug_dump("MS Translator function $api_function_name error: " . curl_error ( $ch ). " -> request: ".$data ,"184.161.43.99");

      return FALSE;
    }

    curl_close ( $ch );
//     return FALSE;
    return $curl_result;
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
    foreach($translate_data as $key => $data)
    {
      $xmldata .= "<string xmlns=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\">".$this->xml_convert($data->text)."</string>";
    }
    $xmldata .= "</Texts>";
    $xmldata .= "<To>".$this->google_to_ms_lang($this->ToLang)."</To>";
    $xmldata .= "</TranslateArrayRequest>";

    $results['responseData'] = array();
    $results['responseDetails'] = NULL;
    $results['responseStatus']  = 200;

    if(!empty($translate_data))
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
//         if(($this->db_cache_enable === TRUE) &&
//         (!empty($results['responseData'][$i]['responseData']['translatedText'])) &&
//         (!empty($results['responseData'][$i]['responseData']['detectedSourceLanguage'])) )
//         {
//           $this->CI->db_translation_cache->cache_translation($translate_data[$i]->text,
//                                                              $results['responseData'][$i]['responseData']['translatedText'],
//                                                              $this->ToLang,
//                                                              $results['responseData'][$i]['responseData']['detectedSourceLanguage'],
//                                                              $this->source_account->source_id,
//                                                              $translate_data[$i]->tag );
//         }
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
  }
  function clearBatchText()
  {
    $this->batchText = Array();
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

      $trans_cached = $this->get_db_translation($textdb,$this->ToLang);
      //If cache found put translation in batch results and return
      if(!empty($trans_cached))
      {
        $tags = explode("|",$trans_cached->tags);
        //If tag is not associate to translation link it
//         if(!empty($tag) && !in_array($tag, $tags))
//         {
//           $this->CI->db_translation_cache->add_tag_to_translation($tag,$trans_cached->translation_id);
//           $tags[] = $tag;
//         }

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
//         log_message("debug",current_url().": MAXIMUM MS translator QUERY Reached: $text_length characters");
        $text = mb_substr($text, 0, self::MAX_QUERY_LENGTH-10,"UTF-8");
        $text_length = strlen($text);
      }
      if(($this->batch_text_length + $text_length) > self::MAX_QUERY_LENGTH)
      {
        $this->batch_translate();
      }
    }

    $this->batch_results['responseData'][$this->results_count] = array();
    $this->batch_results['responseData'][$this->results_count]['responseDetails'] = "Remote Translation Needed";
    $this->batch_results['responseData'][$this->results_count]['responseStatus']  = 0;
    $this->results_count++;
    $this->batch_text_length += $text_length;
    $translation_data->text = $text;
    $translation_data->tag  = $tag;
    array_push($this->batchText,$translation_data);
  }

  function end_batch()
  {

    $batch_result = $this->batch_translate();
//     debug_dump("count of results: :".$this->results_count,"184.161.43.99");
    $this->clearBatch();
//     debug_dump("results: ".$batch_result,"184.161.43.99");
//     debug_dump($batch_result,"208.113.48.86");
    return $batch_result;
  }

  function batch_translate()
  {

    if(count($this->batchText) > 0 )
    {

//       $this->benchmark->mark('google_req');
      $contents = $this->translateArray($this->batchText);
//       $this->benchmark->mark('google_req_end');
//       debug_dump("MS response time".$this->benchmark->elapsed_time('google_req', 'google_req_end'),"184.161.43.99");
//       debug_dump($contents);

      $c = 0;
      foreach($this->batch_results['responseData'] as $i => $translation_data)
      {
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

  public function get_ms_source($source_slug)
  {
    if(empty($source_slug)) return NULL;
    $source_slug = $this->transDB->escape($source_slug);

    $sql = "SELECT * FROM ".self::SOURCE_TABLE." WHERE key_slug LIKE'$source_slug'";

    $query = $this->transDB->get_row($sql);
    return $query;
  }

  public function get_db_translation($orig_text, $lang_code)
  {
    $orig_text = $this->transDB->escape($orig_text);
    $lang_code = $this->transDB->escape($lang_code);

    $sql = "SELECT ".self::TEXT_TABLE.".translation_id, `translation`,`ref_lang_code`, source, key_slug,
              (
                SELECT group_concat(tag SEPARATOR '|') FROM tags_of_translations
                LEFT JOIN tags ON tags_of_translations.tag_id = tags.tag_id
                 WHERE tags_of_translations.translation_id = translations.translation_id
              ) as tags
      				FROM ".self::TEXT_TABLE."
      				LEFT JOIN sources ON translations.source_id = sources.source_id
      				WHERE lang_code = '$lang_code'
                AND ref_hash = COMPRESS('$orig_text')
      				LIMIT 1";

    $query = $this->transDB->get_row($sql);
    if (!empty($query))
    {
      return $query;
    }
    return FALSE;
  }

  public function xml_convert($str)
  {
    $temp = '__TEMP_AMPERSANDS__';

    // Replace entities to temporary markers so that
    // ampersands won't get messed up
    $str = preg_replace("/&#(\d+);/", "$temp\\1;", $str);
    $str = preg_replace("/&(\w+);/",  "$temp\\1;", $str);

    $str = str_replace(array("&","<",">","\"", "'", "-"),
    array("&amp;", "&lt;", "&gt;", "&quot;", "&#39;", "&#45;"),
    $str);

    // Decode the temp markers back to entities
    $str = preg_replace("/$temp(\d+);/","&#\\1;",$str);
    $str = preg_replace("/$temp(\w+);/","&\\1;", $str);

    return $str;
  }
}
?>