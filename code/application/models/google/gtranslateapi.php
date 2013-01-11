<?php
/**
 * @author Louis-Michel
 *
 */
define('GOOGLE_MAX_QUERY_LENGTH', 5000);

class Gtranslateapi extends CI_Model
{
  var $googleKey    = "AIzaSyAkhkddCyJnFOI1YBpqmpgRo_SF5cZLAQc";

  var $TranslateUrl = "http://ajax.googleapis.com/ajax/services/language/translate";
  var $DetectUrl    = "http://ajax.googleapis.com/ajax/services/language/detect";
  var $FromLang     = "";
  var $ToLang       = "fr";
  var $Version      = "1.0";
  var $HostLang     = "fr";

  var $Post_data = Array();
  var $batch_query = "";

  var $batch_text_length = 0;

  var $batch_json = Array();

  var $Text = "";

  var $batchText = Array();

  var $DebugMsg;
  var $DebugStatus;


  function Gtranslateapi()
  {
    parent::__construct();
    $this->load->library('Http_request');
    $this->load->library('Json');
  }

  function startBatch($toLang="", $fromLang="")
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

  function addTextForBatch($text = "")
  {
    $text_length = strlen($text);
    if($text_length > GOOGLE_MAX_QUERY_LENGTH)
    {
      log_message("debug",current_url().": MAXIMUM GOOGLE QUERY Reached: $text_length characters");
      $text = mb_substr($text, 0, GOOGLE_MAX_QUERY_LENGTH-10,"UTF-8");
      $text_length = strlen($text);
    }

    if(($this->batch_text_length + $text_length) > GOOGLE_MAX_QUERY_LENGTH)
    {
      $this->batch_translate();
      $this->batch_text_length = $text_length;
    }
    else
    {
      $this->batch_text_length += $text_length;
    }
    array_push($this->batchText,$text);
  }

  function clearBatchText()
  {
    $this->batchText = Array();
  }

  function clearBatch()
  {
    $this->clearBatchText();
    $this->batch_json = Array();
  }

  function makeBatchTranslateUrl()
  {
    $this->batch_query = "v=".$this->Version;
    $this->batch_query.= "&key=".$this->googleKey;
    $this->batch_query.= "&userip=".$_SERVER["REMOTE_ADDR"];
//    $this->batch_query.= "&format=html";
    $this->batch_query.= "&langpair=".$this->FromLang."%7C".$this->ToLang;

    //print("<BR>batch count:<BR>".count($this->batchText)."<br>");
    foreach($this->batchText as $text)
    {
      $this->batch_query.= "&q=".urlencode($text);
    }

  }
  function makeTranslateUrl()
  {
    $this->Post_data["v"]        = $this->Version;
    $this->Post_data["userip"]   = $_SERVER["REMOTE_ADDR"];
    $this->Post_data["key"]      = $this->googleKey;
    $this->Post_data["q"]        = $this->Text;
    $this->Post_data["langpair"] = $this->FromLang."|".$this->ToLang;
  }

  function makeDetectUrl()
  {
    $this->CallUrl = $this->DetectUrl;
    $this->CallUrl.= "?v=".$this->Version;
    $this->CallUrl.= "&q=".urlencode($this->Text);
  }

  function batch_translate()
  {

    $this->makeBatchTranslateUrl();
//print $this->batch_query ."<br><br>";
//    echo "<br><br>".strlen($this->batch_query).", q length:".$this->batch_text_length."<br><br>";

    if(count($this->batchText) > 0 )
    {

      $contents = $this->http_request->post_request($this->TranslateUrl,$this->batch_query,true,"Content-Type: application/x-www-form-urlencoded\r\n charset=utf-8");

      $json = NULL;
      try
      {
        $json = $this->json->JDecode($contents, true);
      }
      catch(Exception $e)
      {
        log_message("error",$e->getMessage());
        log_message("error","GOOGLE TRANSLATE QUERY: ".$this->batch_query);
        log_message("error","GOOGLE TRANSLATE RESPONSE: ".$contents);
        log_message("error","GOOGLE TRANSLATE JSON: ".$json);
      }

      //On single result convert to array result so that output is always an array to facilitate handling of results
      if(!empty($json["responseData"]["translatedText"]))
      {
        $json["responseData"] = array(0 => $json);
      }
      $this->batch_json = $this->append_json_response($this->batch_json,$json);

      $this->clearBatchText();

      return $this->batch_json;
    }

    return NULL;
  }

  function append_json_response($json_array1,$json_array2)
  {

    if(empty($json_array1))
    {
      $json_array1 = $json_array2;
    }
    else
    {
      //If responseData is not an array add put the single result at the end of the batch query array
      if(!empty($json_array2["responseData"]["translatedText"]) || empty($json_array2["responseData"]))
      {
        //TONOTICE
        //array_push Warning is issued -> Severity: Warning  --> array_push() expects parameter 1 to be array, null given
        // This happen when
        // $json_array1 is
//         Array
//         (
//         [responseData] =>
//         [responseDetails] => Quota Exceeded.  Please see http://code.google.com/apis/language/translate/overview.html
//         [responseStatus] => 403
//         )
        array_push($json_array1["responseData"],$json_array2);
//         $json_array1["responseData"][] = $json_array2;
      }
      else
      {
        $json_array1["responseData"] = array_merge($json_array1["responseData"],$json_array2["responseData"]);
      }
    }
    return $json_array1;
  }

  function end_batch()
  {

    $batch_result = $this->batch_translate();
    $this->clearBatch();
    return $batch_result;
  }

  function translate($text="", $toLang="", $fromLang="")
  {
    if($text == ""){
      return false;
    }
    else
    {
      $this->Text = $text;
    }

    if($toLang != "")
    {
      $this->ToLang = $toLang;
    }
    if($fromLang != "")
    {
      $this->FromLang = $fromLang;
    }

    $this->makeTranslateUrl();
    if($this->Post_data["q"] != "" )
    {

      $contents = $this->http_request->post_request($this->TranslateUrl,$this->Post_data);

      $json = json_decode($contents, true);

      return $json;

    }
  }

//  function detect($text)
//  {
//    if($text == ""){
//      return false;
//    }
//    else
//    {
//      $this->Text = $text;
//    }
//    $this->makeDetectUrl();
//    if($this->Text != "" && $this->CallUrl != ""){
//      $handle = fopen($this->CallUrl, "rb");
//      $contents = "";
//      while (!feof($handle)) {
//      $contents .= fread($handle, 8192);
//      }
//      fclose($handle);
//
//      $json = json_decode($contents, true);
//
//      if($json["responseStatus"] == 200){ //If request was ok
//        $this->DetectedLanguage = $json["responseData"]["language"];
//        $this->IsReliable = $json["responseData"]["isReliable"];
//        $this->Confidence = $json["responseData"]["confidence"];
//        $this->DebugMsg = $json["responseDetails"];
//        $this->DebugStatus = $json["responseStatus"];
//        return $this->DetectedLanguage;
//      } else { //Return some errors
//        return false;
//        $this->DebugMsg = $json["responseDetails"];
//        $this->DebugStatus = $json["responseStatus"];
//      }
//    } else {
//      return false;
//    }
//  }
}
?>