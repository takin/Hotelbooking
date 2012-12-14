<?php

define('HB_TRANSLATION_ERROR_LEVEL',"debug");

class Hb_api_translate extends Model
{

  var $toLang = "fr";
  var $fromLang = "";

  var $CI = NULL;

  function Hb_api_translate()
  {
    parent::Model();

//     $this->load->model('google/Gtranslateapi', 'translation_api');
    //Replace google translate API by Microsoft translator API
    $this->load->model('microsoft/Microsofttranslator', 'translation_api');

  }

  function setLanguage($toLang = "fr",$fromLang = "")
  {
    $this->toLang = $toLang;
    $this->fromLang = $fromLang;
  }

  function translate_text($text,$tag = "",$from_lang = NULL, $to_lang = NULL)
  {
    $custom_from_lang = $this->fromLang;
    $custom_to_lang   = $this->toLang;

    if(!is_null($from_lang))
    {
      $custom_from_lang = $from_lang;
    }
    if(!is_null($to_lang))
    {
      $custom_to_lang   = $to_lang;
    }

    //Translate
    if(strcasecmp($custom_to_lang,$custom_from_lang)!=0)
    {

      $this->translation_api->StartBatch($custom_to_lang,$custom_from_lang);

      $this->translation_api->addTextForBatch($text,$tag);

      $gtrans = $this->translation_api->end_batch();

      //TONOTICE The detected langage by google is not really reliable if the text is too short
      if($gtrans["responseData"][0]["responseStatus"] == 200)
      {
        if((!isset($gtrans["responseData"][0]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][0]["responseData"]["detectedSourceLanguage"],$custom_to_lang)!=0) )
        {
          $text = $gtrans["responseData"][0]["responseData"]["translatedText"];
        }
      }
      else
      {
        log_message(HB_TRANSLATION_ERROR_LEVEL,"Translating some text error: " .current_url()." -> google status -> ".$gtrans["responseData"][0]["responseStatus"]);
      }

    }
    return $text;
  }


  function translate_LocationData(&$hbresults)
  {
    //Translate
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {

      $this->translation_api->StartBatch($this->toLang,$this->fromLang);

      if(is_null($this->CI))
      {
        $this->CI =& get_instance();
      }
      $trans_error = FALSE;

      foreach($hbresults["response"]["properties"] as $hostel)
      {
        $textToTranslate = strip_tags($hostel["intro"]);
        $this->translation_api->addTextForBatch($textToTranslate,"HB short description");
      }

      $gtrans = $this->translation_api->end_batch();

      //Update object list
      $i=0;
      foreach($hbresults["response"]["properties"] as $hostel)
      {
        //TONOTICE The detected langage by google is not really reliable because de description is to short
        if(!empty($gtrans["responseData"][$i]) && $gtrans["responseData"][$i]["responseStatus"] == 200)
        {
          if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
          {
            $hbresults["response"]["properties"][$i]["introTranslated"] = html_entity_decode($gtrans["responseData"][$i]["responseData"]["translatedText"],ENT_QUOTES);
          }
        }
        elseif(!empty($gtrans["responseData"][$i]))
        {
          $trans_error = TRUE;
          $hbresults["response"]["properties"][$i]["introTranslatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
          log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_LocationData: " .current_url()." -> [shortDescription] -> ".$hbresults["response"]["properties"][$i]["introTranslatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
        }
        $i++;
      }
      //IF there is a translation error cancel caching
      if($trans_error === TRUE)
      {
        $this->CI->output->cache(0);
      }
    }
    return $hbresults;
  }

  function translate_LocationAvailability(&$hbresults)
  {

    //Translate
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {

      $this->translation_api->StartBatch($this->toLang,$this->fromLang);

      foreach($hbresults["response"] as $hostel)
      {
        $textToTranslate = strip_tags($hostel["shortdescription"]);
        $this->translation_api->addTextForBatch($textToTranslate,"HB short description");
      }

      $gtrans = $this->translation_api->end_batch();
      //Update object list
      $i=0;
      foreach($hbresults["response"] as $hostel)
      {
        //TONOTICE The detected langage by google is not really reliable because de description is to short
        if($gtrans["responseData"][$i]["responseStatus"] == 200)
        {
          if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
          {
            $hbresults["response"][$i]["shortdescriptionTranslated"] = html_entity_decode($gtrans["responseData"][$i]["responseData"]["translatedText"],ENT_QUOTES);
          }
        }
        else
        {
          $hbresults["response"][$i]["shortdescriptionTranslatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
          log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_LocationAvailability: " .current_url()." -> [shortDescription] -> ".$hbresults["response"][$i]["shortdescriptionTranslatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
        }
        $i++;
      }

    }
    return $hbresults;
  }

  function translate_PropertyData(&$hbresults)
  {
    //Translate
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {
      if(is_null($this->CI))
      {
        $this->CI =& get_instance();
      }

      $translatedTags = Array();
      $trans_error = FALSE;
      $extra_names = array();

      $this->translation_api->StartBatch($this->toLang,$this->fromLang);

      if(!empty($hbresults["LOCALATTRACTIONS"]))
      {
        $this->translation_api->addTextForBatch(nl2p($hbresults["LOCALATTRACTIONS"],false,true),"HB attraction");
        array_push($translatedTags,"LOCALATTRACTIONS");
      }

      if (isset($hbresults["FEATURES"])&&!empty($hbresults["FEATURES"]))
      {
        foreach($hbresults["FEATURES"] as $feature)
        {
          if(!empty($feature))
          {
            $this->translation_api->addTextForBatch($feature,"HB feature");
            array_push($translatedTags,"FEATURES");
          }
        }
      }

      if (isset($hbresults["LONGDESCRIPTION"])&&!empty($hbresults["LONGDESCRIPTION"]))
      {
        $this->translation_api->addTextForBatch(nl2p($hbresults["LONGDESCRIPTION"],false,true),"HB full description");
        array_push($translatedTags,"LONGDESCRIPTION");
      }

      if (isset($hbresults["DIRECTIONS"])&&!empty($hbresults["DIRECTIONS"]))
      {
        $this->translation_api->addTextForBatch(nl2p($hbresults["DIRECTIONS"],false,true),"HB property directions");
        array_push($translatedTags,"DIRECTIONS");
      }

      if (isset($hbresults["ACCOMMODATIONDESCRIPTION"])&&!empty($hbresults["ACCOMMODATIONDESCRIPTION"]))
      {
        $this->translation_api->addTextForBatch(nl2p($hbresults["ACCOMMODATIONDESCRIPTION"],false,true),"HB accomodation description");
        array_push($translatedTags,"ACCOMMODATIONDESCRIPTION");
      }

      if (isset($hbresults["CANCELLATIONINFORMATION"]["CANCELLATIONPOLICY"])&&!empty($hbresults["CANCELLATIONINFORMATION"]["CANCELLATIONPOLICY"]))
      {
        $this->translation_api->addTextForBatch(nl2p($hbresults["CANCELLATIONINFORMATION"]["CANCELLATIONPOLICY"],false,true),"HB property cancellation policy");
        array_push($translatedTags,"CANCELLATIONPOLICY");
      }

      if (isset($hbresults["IMPORTANTINFORMATION"])&&!empty($hbresults["IMPORTANTINFORMATION"]))
      {
        $this->translation_api->addTextForBatch(nl2p($hbresults["IMPORTANTINFORMATION"],false,true),"HB property important info");
        array_push($translatedTags,"IMPORTANTINFORMATION");
      }

      if (isset($hbresults["PROPERTYEXTRAS"])&&!empty($hbresults["PROPERTYEXTRAS"]))
      {
        foreach($hbresults["PROPERTYEXTRAS"] as $extra => $price)
        {
          if(!empty($extra))
          {
            $this->translation_api->addTextForBatch($extra,"HB property extra");
            $extra_names[] = $extra;
            array_push($translatedTags,"PROPERTYEXTRAS");
          }
        }
      }

      if (isset($hbresults["TYPE"])&&!empty($hbresults["TYPE"]))
      {
        $this->translation_api->addTextForBatch(nl2p($hbresults["TYPE"],false,true),"HB property type");
        array_push($translatedTags,"TYPE");
      }

      $gtrans = $this->translation_api->end_batch();

      $featurekey = 0;
      $extrakey = 0;

      //Format Google translate API answer in hb hostel Object
      if(!empty($translatedTags))
      {
        for($i=0;$i<count($translatedTags);$i++)
        {
          switch($translatedTags[$i])
          {
            case "LOCALATTRACTIONS":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $hbresults["LOCALATTRACTIONS_translated"] = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $trans_error = TRUE;
                  $hbresults["LOCALATTRACTIONS_translatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_PropertyData: " .current_url()." -> [LOCALATTRACTIONS] -> ".$hbresults["LOCALATTRACTIONS_translatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "FEATURES":
                $hbresults["FEATURES_translated"][$featurekey] = $hbresults["FEATURES"][$featurekey];
                $hbresults["FEATURES_translatedError"][$featurekey] = "";
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  {
                    $hbresults["FEATURES_translated"][$featurekey] = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                  }
                }
                else
                {
                  $trans_error = TRUE;
                  $hbresults["FEATURES_translatedError"][$featurekey] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_PropertyData: " .current_url()." -> [FEATURES] -> ".$hbresults["FEATURES_translatedError"][$featurekey]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
               $featurekey++;
              break;
            case "LONGDESCRIPTION":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $hbresults["LONGDESCRIPTION_translated"] = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $trans_error = TRUE;
                  $hbresults["LONGDESCRIPTION_translatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_PropertyData: " .current_url()." -> [LONGDESCRIPTION] -> ".$hbresults["LONGDESCRIPTION_translatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "DIRECTIONS":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $hbresults["DIRECTIONS_translated"] = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $trans_error = TRUE;
                  $hbresults["DIRECTIONS_translatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_PropertyData: " .current_url()." -> [DIRECTIONS] -> ".$hbresults["DIRECTIONS_translatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "ACCOMMODATIONDESCRIPTION":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $hbresults["ACCOMMODATIONDESCRIPTION_translated"] = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $trans_error = TRUE;
                  $hbresults["ACCOMMODATIONDESCRIPTION_translatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_PropertyData: " .current_url()." -> [ACCOMMODATIONDESCRIPTION] -> ".$hbresults["ACCOMMODATIONDESCRIPTION_translatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "CANCELLATIONPOLICY":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $hbresults["CANCELLATIONPOLICY_translated"] = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $trans_error = TRUE;
                  $hbresults["CANCELLATIONPOLICY_translatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_PropertyData: " .current_url()." -> [CANCELLATIONPOLICY] -> ".$hbresults["CANCELLATIONPOLICY_translatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "IMPORTANTINFORMATION":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $hbresults["IMPORTANTINFORMATION_translated"] = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $trans_error = TRUE;
                  $hbresults["IMPORTANTINFORMATION_translatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_PropertyData: " .current_url()." -> [IMPORTANTINFORMATION] -> ".$hbresults["IMPORTANTINFORMATION_translatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "PROPERTYEXTRAS":
                $hbresults["PROPERTYEXTRAS_translated"][$extrakey] = $extra_names[$extrakey];
                $hbresults["PROPERTYEXTRAS_translatedError"][$extrakey] = "";
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  {
                    $hbresults["PROPERTYEXTRAS_translated"][$extrakey] = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                  }
                }
                else
                {
                  $trans_error = TRUE;
                  $hbresults["PROPERTYEXTRAS_translatedError"][$extrakey] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_PropertyData: " .current_url()." -> [PROPERTYEXTRAS] -> ".$hbresults["PROPERTYEXTRAS_translatedError"][$extrakey]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
                $extrakey++;
              break;
            case "TYPE":
              if($gtrans["responseData"][$i]["responseStatus"] == 200)
              {
                if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                $hbresults["TYPE_translated"] = $gtrans["responseData"][$i]["responseData"]["translatedText"];
              }
              else
              {
                $trans_error = TRUE;
                $hbresults["TYPE_translatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_PropertyData: " .current_url()." -> [TYPE] -> ".$hbresults["TYPE_translatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
              }
            break;
          }
        }
        $hbresults["PROPERTYEXTRAS_translated"]= array_combine($hbresults["PROPERTYEXTRAS_translated"],$hbresults["PROPERTYEXTRAS"]);
      }

      //IF there is a translation error cancel caching
      if($trans_error === TRUE)
      {
        $this->CI->output->cache(0);
      }
    }

    return $hbresults;
  }

  function translate_PropertyAvailability(&$hbresults)
  {
  //Translate
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {
      $this->translation_api->StartBatch($this->toLang,$this->fromLang);

      $numbers = array();

      foreach($hbresults as $room)
      {
        $texttoTrans = (string) $room["NAME"];

        $numbers[] = $this->extract_number($texttoTrans);
        $texttoTrans = $this->replace_number_of_string_by_d($texttoTrans);
//         $texttoTrans = mb_strtolower($texttoTrans, 'UTF-8');

        $this->translation_api->addTextForBatch($texttoTrans,"HB room type");
      }

      $gtrans = $this->translation_api->end_batch();
//       debug_dump($gtrans,"184.161.43.99");

      //Update object list
      $i=0;
      foreach($hbresults as $room)
      {
        //TONOTICE The detected langage by google is not really reliable because de description is to short
        if($gtrans["responseData"][$i]["responseStatus"] == 200)
        {
          if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
          {
            $hbresults[$i]["NAME_TRANSLATED"] = $this->replace_number($numbers[$i], $gtrans["responseData"][$i]["responseData"]["translatedText"]);
          }
        }
        else
        {
          $hbresults[$i]["NAME_TRANSLATED_ERROR"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
          log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_PropertyAvailability: " .current_url()." -> [room name] -> ".$hbresults[$i]["NAME_TRANSLATED_ERROR"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
        }
        $i++;
      }

    }
    return $hbresults;
  }

  function translate_PropertyRoomPricingPerDate(&$hbresults)
  {
  //Translate
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {
      $this->translation_api->StartBatch($this->toLang,$this->fromLang);

      $numbers = array();
      foreach($hbresults as $date)
      {
        foreach($date as $room)
        {
          $texttoTrans = (string) $room["NAME"];

          $numbers[] = $this->extract_number($texttoTrans);
          $texttoTrans = $this->replace_number_of_string_by_d($texttoTrans);
          //         $texttoTrans = mb_strtolower($texttoTrans, 'UTF-8');
//           debug_dump($texttoTrans,"184.161.43.99");
          $this->translation_api->addTextForBatch($texttoTrans,"HB room type");
        }

      }

      $gtrans = $this->translation_api->end_batch();
//       debug_dump($gtrans,"184.161.43.99");

      //Update object list
      $count = 0;
      foreach($hbresults as $d => $date)
      {
        foreach($date as $i => $room)
        {
          //TONOTICE The detected langage by google is not really reliable because de description is to short
          if($gtrans["responseData"][$i]["responseStatus"] == 200)
          {
            if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$count]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
            {
              $hbresults[$d][$i]["NAME_TRANSLATED"] = $this->replace_number($numbers[$i], $gtrans["responseData"][$count]["responseData"]["translatedText"]);
            }
          }
          else
          {
            $responseStatus = $gtrans["responseData"][$count]["responseStatus"];
            $responseDetails = $gtrans["responseData"][$count]["responseDetails"];
            if(empty($responseStatus))
            {
              $responseStatus = $gtrans["responseData"]["responseStatus"];
            }
            if(empty($responseDetails))
            {
              $responseDetails = $gtrans["responseData"]["responseDetails"];
            }
            $hbresults[$d][$i]["NAME_TRANSLATED_ERROR"] = "Translation error ".$responseStatus.": ".$responseDetails;
            log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_PropertyAvailability: " .current_url()." -> [room name] -> ".$hbresults[$d][$i]["NAME_TRANSLATED_ERROR"]. " | google status -> ".$gtrans["responseData"][$count]["responseStatus"]);
          }
          $count++;
        }
      }

    }
    return $hbresults;
  }

  function translate_make_booking(&$hbresults)
  {
    //Translate
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {
      $translatedTags = Array();

      $this->translation_api->StartBatch($this->toLang,$this->fromLang);

      if (isset($hbresults->property->address->directions)&&!empty($hbresults->property->address->directions))
      {
        $this->translation_api->addTextForBatch(nl2p($hbresults->property->address->directions,false,true),"HB property directions");
        array_push($translatedTags,"DIRECTIONS");
      }

      if (isset($hbresults->property->importantInfo->cancellationPolicy)&&!empty($hbresults->property->importantInfo->cancellationPolicy))
      {
        $this->translation_api->addTextForBatch(nl2p($hbresults->property->importantInfo->cancellationPolicy,false,true),"HB property cancellation policy");
        array_push($translatedTags,"CANCELLATIONPOLICY");
      }

      if (isset($hbresults->property->importantInfo->taxes)&&!empty($hbresults->property->importantInfo->taxes))
      {
        $this->translation_api->addTextForBatch(nl2p($hbresults->property->importantInfo->taxes,false,true),"HB taxes info");
        array_push($translatedTags,"TAXES");
      }

      if (isset($hbresults->property->importantInfo->extraInfo)&&!empty($hbresults->property->importantInfo->extraInfo))
      {
        $this->translation_api->addTextForBatch(nl2p($hbresults->property->importantInfo->extraInfo,false,true),"HB extra info");
        array_push($translatedTags,"EXTRAINFO");
      }

      $numbers = array();
      for($k=0;$k<count($hbresults->property->roomsBooked->room);$k++)
      {
        $texttoTrans = (string) $hbresults->property->roomsBooked->room[$k]->name;

        $numbers[] = $this->extract_number($texttoTrans);
        $texttoTrans = $this->replace_number_of_string_by_d($texttoTrans);
        //         $texttoTrans = mb_strtolower($texttoTrans, 'UTF-8');
//         debug_dump($texttoTrans,"184.161.43.99");
        $this->translation_api->addTextForBatch($texttoTrans,"HB room type");
        array_push($translatedTags,"ROOMNAME");
      }

      $gtrans = $this->translation_api->end_batch();
//       debug_dump($gtrans,"184.161.43.99");

      $room_key = 0;
      //Format Google translate API answer in hb hostel Object
      if(!empty($translatedTags))
      {
        for($i=0;$i<count($translatedTags);$i++)
        {
          switch($translatedTags[$i])
          {
            case "DIRECTIONS":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $hbresults->property->address->directions_translated = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $hbresults->property->address->directions_translatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_make_booking: " .current_url()." -> [DIRECTIONS] -> ".$hbresults->property->address->directions_translatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "CANCELLATIONPOLICY":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $hbresults->property->importantInfo->cancellationPolicy_translated = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $hbresults->property->importantInfo->cancellationPolicy_translatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_make_booking: " .current_url()." -> [CANCELLATIONPOLICY] -> ".$hbresults->property->importantInfo->cancellationPolicy_translatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "TAXES":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $hbresults->property->importantInfo->taxes_translated  = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $hbresults->property->importantInfo->taxes_translatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_make_booking: " .current_url()." -> [TAXES] -> ".$hbresults->property->importantInfo->taxes_translatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "EXTRAINFO":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $hbresults->property->importantInfo->extraInfo_translated  = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $hbresults->property->importantInfo->extraInfo_translatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_make_booking: " .current_url()." -> [EXTRAINFO] -> ".$hbresults->property->importantInfo->extraInfo_translatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "ROOMNAME":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $hbresults->property->roomsBooked->room[$room_key]->name_translated  = $this->replace_number($numbers[$room_key], $gtrans["responseData"][$i]["responseData"]["translatedText"]);
                }
                else
                {
                  $hbresults->property->roomsBooked->room[$room_key]->name_translatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_make_booking: " .current_url()." -> [ROOMNAME] -> ".$hbresults->property->roomsBooked->room[$room_key]->name_translatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
                $room_key++;
              break;
          }
        }
      }
    }

    return $hbresults;
  }

  function translate_location_reviews(&$properties_reviewed)
  {
    $reviews_translated = false;
    //Translate

    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {

      if(!empty($properties_reviewed))
      {
        $this->translation_api->StartBatch($this->toLang,$this->fromLang);


        foreach($properties_reviewed as $properties)
        {
          foreach($properties['user_reviews'] as $review)
          {
            $textToTranslate = strip_tags($review["review_likebest"]);
            $this->translation_api->addTextForBatch($textToTranslate, "HB review");
          }
        }

        $gtrans = $this->translation_api->end_batch();

        //Update object list
        if( !isset($gtrans["responseData"]["responseStatus"]) ||
        ($gtrans["responseData"]["responseStatus"] != 403) ||
        ($gtrans["responseData"]["responseStatus"] == 200))
        {
          $i=0;
          foreach($properties_reviewed as $prop_number => $properties)
          {
            foreach($properties['user_reviews'] as $r => $review)
            {
              if(!empty($gtrans["responseData"][$i]))
              {
                //TONOTICE The detected langage by google is not really reliable because the text is too short
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  {
                    $properties_reviewed[$prop_number]['user_reviews'][$r]["review_likebest_translated"] = html_entity_decode($gtrans["responseData"][$i]["responseData"]["translatedText"],ENT_QUOTES);
                    $reviews_translated = true;
                  }
                }
                elseif(!empty($gtrans["responseData"][$i]))
                {
                  $properties_reviewed[$prop_number]['user_reviews'][$r]["review_likebest_translatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_mixed_reviews: " .current_url()." -> [review] -> ".$properties_reviewed[$prop_number]['user_reviews'][$r]["review_likebest_translatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
                else
                {
                  log_message(TRANSLATION_ERROR_LEVEL,"translation missing in translation API reply: " .current_url()." -> [review]");
                }
                $i++;;
              }
            }
          }
        }
      }

    }
    return $reviews_translated;
  }

  function translate_reviews(&$reviews)
  {
    $reviews_translated = false;

    //Translate
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {

      if(!empty($reviews))
      {
        $this->translation_api->StartBatch($this->toLang,$this->fromLang);


        foreach($reviews as $review)
        {
          $textToTranslate = strip_tags($review["review_likebest"]);
          $this->translation_api->addTextForBatch($textToTranslate, "HB review");
        }

        $gtrans = $this->translation_api->end_batch();

        //Update object list
        $i=0;
        foreach($reviews as $review)
        {
          //TONOTICE The detected langage by google is not really reliable because de description is to short
          if($gtrans["responseData"][$i]["responseStatus"] == 200)
          {
            if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
            {
              $reviews[$i]["review_likebest_translated"] = html_entity_decode($gtrans["responseData"][$i]["responseData"]["translatedText"],ENT_QUOTES);
              $reviews_translated = true;
            }
          }
          else
          {
            $reviews[$i]["likedBest_translatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
            log_message(HB_TRANSLATION_ERROR_LEVEL,"translate_reviews: " .current_url()." -> [likedBest] -> ".$reviews[$i]["likedBest_translatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
          }
          $i++;
        }
      }

    }
    return $reviews_translated;
  }

  function translate_search_results(&$searchResults)
  {
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {

      $this->translation_api->StartBatch($this->toLang,$this->fromLang);

      foreach($searchResults as $res)
      {
        if(empty($res->translated_desc) || (strcmp($res->short_desc,$res->translated_desc)==0))
        {
          $textToTranslate = strip_tags(word_limiter($res->short_desc, 40));
          $this->translation_api->addTextForBatch($textToTranslate,"HB short description");
        }
      }

      $gtrans = $this->translation_api->end_batch();
      //      print_r($gtrans);

      //Update object list
      $i=0;
      foreach($searchResults as $res)
      {
        if(empty($res->translated_desc) || (strcmp($res->short_desc,$res->translated_desc)==0))
        {
          //TONOTICE The detected langage by google is not really reliable because de description is to short
          if($gtrans["responseData"][$i]["responseStatus"] == 200)
          {
            if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
            {
              $res->translated_desc = $gtrans["responseData"][$i]["responseData"]["translatedText"];
            }
          }
          else
          {
            $hostel->translated_descError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
            log_message(TRANSLATION_ERROR_LEVEL,"translate_search_results: " .current_url()." -> [shortDescription] -> ".$hostel->translated_descError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
          }
          $i++;
        }
      }

    }

    return $searchResults;
  }

  /*
   *
   */
  function translate_BookingAPIError(&$hbresults)
  {
    //Error messages are always in english
    $from_error_lang = "en";

    if(strcasecmp($this->toLang,$from_error_lang)!=0)
    {
      $translatedTags = Array();
      $this->translation_api->StartBatch($this->toLang,$from_error_lang);

      foreach($hbresults->errors as $error)
      {
        $textToTranslate = $error->error->description;
        $this->translation_api->addTextForBatch($textToTranslate, "HB error description");
        array_push($translatedTags,"description");

        $textToTranslate = $error->error->extraInfo;
        $this->translation_api->addTextForBatch($textToTranslate, "HB error info");
        array_push($translatedTags,"extrainfo");

        array_push($translatedTags,"nexterror");
      }
      $gtrans = $this->translation_api->end_batch();
//      print_r($gtrans);

      $error_index = 0;
      //Update object list
      //Format Google translate API answer in hb error Object
      if(!empty($translatedTags))
      {
        foreach($translatedTags as $i => $tag)
        {
          switch($tag)
          {
            case "description":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $hbresults->errors[$error_index]->error->description_translated = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $hbresults->errors[$error_index]->error->description_translatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"HB translate_BookingAPIError: " .current_url()." -> [description] -> ".$hbresults->errors[$error_index]->error->description_translatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "extrainfo":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  {
                    $hbresults->errors[$error_index]->error->extraInfo_translated = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                  }
                }
                else
                {
                  $hbresults->errors[$error_index]->error->extraInfo_translatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(HB_TRANSLATION_ERROR_LEVEL,"HB translate_BookingAPIError: " .current_url()." -> [extrainfo] -> ".$hbresults->errors[$error_index]->error->extraInfo_translatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "nexterror":
              $error_index++;
              break;
          }
        }
      }
    }

    return $hbresults;
  }

  //These 3 function should be put elsewhere for reusage
  function replace_number_of_string_by_d($string)
  {
    //Do not replace number 1 for translation
    if($this->extract_number($string) != 1)
    {
      $string = preg_replace("/(\d+)/", '%d',$string, 1);
    }
    return $string;
  }

  function extract_number($string)
  {
    $number = preg_replace("/[^0-9]/", '',$string);
    if(empty($number))
    {
      $number = NULL;
    }
    return $number;
  }

  function replace_number($number,$string)
  {
    if((!is_null($number)) || ($number != ""))
    return str_replace(array("%d","%D"), array($number,$number), $string);
    else
    return $string;
  }
}