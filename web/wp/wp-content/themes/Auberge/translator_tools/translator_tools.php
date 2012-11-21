<?php
/*
Plugin Name: Translator Tool Kit
Plugin URI:
Description: Add different translating tools such as google translate API functions to helps with i18n development
Version: 1.0
Author: Louis-Michel Raynauld
Author URI: pweb.ca
License:

//TODO need to improve the error handling if google API does not answer
*/
class TranslatorTool
{
  private $translate_api;
  private $results_keys = array();
  private $number_of_requests = 0;

  private $translationdb = NULL;

  private $mem_cache = array();

  function TranslatorTool()
  {
//     $this->translationdb = new wpdb("aj_site", "2bVHhwjCGQrRnGW2", "aj_translation", "95.142.167.244");
    global $dev_site;
    if($dev_site)
    {
      $this->translationdb = new wpdb("aj_dev_site", "n<qaTj^`8i`rCi\(qc2bomBREqsajvFp", "aj_translation", "95.142.167.244");
    }
    else
    {
      $this->translationdb = new wpdb("aj_site", "2bVHhwjCGQrRnGW2", "aj_translation", "95.142.167.244");
    }

    $this->translationdb->hide_errors();

    require_once("lib/mstranslateapi.php");
//     $this->translate_api = new Microsofttranslator($this->translationdb,'bing2');
    $this->translate_api = new Microsofttranslator($this->translationdb,'dev_key');
//     require_once("lib/gtranslateapi.php");
//     $this->translate_api  = new Gtranslateapi();
    $this->mem_cache = array();
  }

//   function gtranslate($text_to_translate, $to_lang = "en", $from_lang = "")
//   {
//     $trans = $this->translate_api->translate($text_to_translate, $to_lang, $from_lang);
//     if($trans["responseStatus"] == 200)
//     {
//       if((!isset($trans["responseData"]["detectedSourceLanguage"]))||(strcasecmp($trans["responseData"]["detectedSourceLanguage"],$to_lang)!=0) )
//       {
//         return $trans["responseData"]["translatedText"];
//       }
//     }
//     else
//     {
//       trigger_error("google translation failed with response status ".$trans["responseStatus"], E_USER_WARNING );
//     }
//     return false;
//   }

  public function cache_key($text,$lang)
  {
    return md5($text.$lang);
  }
  function start_batch_translate($to_lang = "", $from_lang = "")
  {
    $this->translate_api->startBatch($to_lang, $from_lang);
  }

  function add_batch_translate($text_to_translate, $key = NULL)
  {
    $this->translate_api->addTextForBatch($text_to_translate);
    if(!is_null($key))
    {
      array_push($this->results_keys,$key);
    }
    else
    {
      array_push($this->results_keys,$this->number_of_requests);
    }
    $this->number_of_requests++;
  }

  function end_batch_translate()
  {
    $results = array();
    $i = 0;
    $trans = $this->translate_api->end_batch();

    if(!empty($trans))
    {
      foreach($trans["responseData"] as $translating_request)
      {
        //Check if detected language is different than original language?

        if($translating_request["responseStatus"] == 200)
        {
          $results[$this->results_keys[$i]] = $translating_request["responseData"]["translatedText"];
        }
        else
        {
          $results[$this->results_keys[$i]] = "";
        }
        $i++;
      }
    }

    unset($trans);
    return $results;
  }

  public function single_translate($text_to_translate, $to_lang = "", $from_lang = "")
  {
    $key = $this->cache_key($text_to_translate,$to_lang);

    if(empty($this->mem_cache[$key]))
    {
      $this->translate_api->startBatch($to_lang, $from_lang);
      $this->translate_api->addTextForBatch($text_to_translate);
      $result = $this->translate_api->end_batch();

      if(!empty($this->mem_cache[$key]["responseData"]))
      {
        $this->mem_cache[$key] = $result["responseData"][0]["responseData"]["translatedText"];
      }
      else
      {
        //Translation not in cache and remote translation was prevented or return error
        $this->mem_cache[$key] = array();
//         print_r($result);
        return array();
      }

    }

    return $this->mem_cache[$key];
  }
}
?>
