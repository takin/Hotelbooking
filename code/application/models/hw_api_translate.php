<?php

define('TRANSLATION_ERROR_LEVEL',"debug");

class Hw_api_translate extends Model
{

  var $toLang = "fr";
  var $fromLang = "";

  var $CI = NULL;

  function Hw_api_translate()
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
  /*
   * For performance purpose: We minimize the number of requests to google translate api as much as possible.
   *
   */
  function translate_PropertyInformation($objectHostel)
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

      $this->translation_api->StartBatch($this->toLang,$this->fromLang);

      if(!empty($objectHostel->description))
      {
        $this->translation_api->addTextForBatch(nl2p($objectHostel->description,false,true),"HW full description");
  //      $this->translation_api->addTextForBatch($objectHostel->description);
        array_push($translatedTags,"description");
      }

      if (isset($objectHostel->Facilities->facility)&&!empty($objectHostel->Facilities->facility))
      {
        foreach($objectHostel->Facilities->facility as $facility)
        {
          if(!empty($facility))
          {
            $this->translation_api->addTextForBatch($facility,"HW facility");
            array_push($translatedTags,"facility");
          }
        }
      }

      if (isset($objectHostel->conditions)&&!empty($objectHostel->conditions))
      {
        $this->translation_api->addTextForBatch(nl2p($objectHostel->conditions,false,true),"HW property conditions");
        array_push($translatedTags,"conditions");
      }
      if (isset($objectHostel->directions)&&!empty($objectHostel->directions))
      {
        $this->translation_api->addTextForBatch(nl2p($objectHostel->directions,false,true),"HW property directions");
        array_push($translatedTags,"directions");
      }


      $gtrans = $this->translation_api->end_batch();
      $facilitykey = 0;

      //Format Google translate API answer in hostel Object
      if(!empty($translatedTags))
      {
        for($i=0;$i<count($translatedTags);$i++)
        {
          switch($translatedTags[$i])
          {
            case "description":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $objectHostel->descriptionTranslated = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $trans_error = TRUE;
                  $objectHostel->descriptionTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_PropertyInformation: " .current_url()." -> [description] -> ".$objectHostel->descriptionTranslatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "facility":
                $objectHostel->Facilities->facilityTranslated[$facilitykey] = $objectHostel->Facilities->facility[$facilitykey];
                $objectHostel->Facilities->facilityTranslatedError[$facilitykey] = 0;

                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  $objectHostel->Facilities->facilityTranslated[$facilitykey] = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $trans_error = TRUE;
                  $objectHostel->Facilities->facilityTranslatedError[$facilitykey] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_PropertyInformation: " .current_url()." -> [facility] -> ".$objectHostel->Facilities->facilityTranslatedError[$facilitykey]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
                $facilitykey++;
              break;
            case "conditions":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $objectHostel->conditionsTranslated = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $trans_error = TRUE;
                  $objectHostel->conditionsTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_PropertyInformation: " .current_url()." -> [conditions] -> ".$objectHostel->conditionsTranslatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "directions":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $objectHostel->directionsTranslated = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $trans_error = TRUE;
                  $objectHostel->directionsTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_PropertyInformation: " .current_url()." -> [directions] -> ".$objectHostel->directionsTranslatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
          }
        }
      }
      //IF there is a translation error cancel caching
      if($trans_error === TRUE)
      {
        $this->CI->output->cache(0);
      }
    }
    return $objectHostel;
  }


  function translate_LocationSearch($objectHostelList)
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

      $this->CI->load->helper('text');

      foreach($objectHostelList as $hostel)
      {
        $textToTranslate = strip_tags(word_limiter((string)$hostel->shortDescription, 40));
        $this->translation_api->addTextForBatch($textToTranslate,"HW short description");
      }

      $gtrans = $this->translation_api->end_batch();
//       debug_dump($gtrans,"184.161.43.99");
//       debug_dump($gtrans,"70.51.36.87");

      //Update object list
      //403 = Quota exceeded
      if( !isset($gtrans["responseData"]["responseStatus"]) ||
          ($gtrans["responseData"]["responseStatus"] != 403) ||
          ($gtrans["responseData"]["responseStatus"] == 200))
      {
        $i=0;
        foreach($objectHostelList as $hostel)
        {
          //TONOTICE The detected langage by remote translation API is not really reliable because the description might be too short
          if(!empty($gtrans["responseData"][$i]) && ($gtrans["responseData"][$i]["responseStatus"] == 200))
          {
            if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
            {
              $hostel->shortDescriptionTranslated = $gtrans["responseData"][$i]["responseData"]["translatedText"];
            }
          }
          elseif(!empty($gtrans["responseData"][$i]))
          {
            $trans_error = TRUE;
            $hostel->shortDescriptionTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
            log_message(TRANSLATION_ERROR_LEVEL,"translate_LocationSearch: " .current_url()." -> [shortDescription] -> ".$hostel->shortDescriptionTranslatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
          }
          $i++;
        }
      }
      else
      {
        $trans_error = TRUE;
        log_message(TRANSLATION_ERROR_LEVEL,"translate_LocationSearch: Google could not translate batch request" .current_url()." -> google status -> ".$gtrans["responseData"]["responseStatus"]);
      }

      //IF there is a translation error cancel caching
      if($trans_error === TRUE)
      {
        $this->CI->output->cache(0);
      }
    }

    return $objectHostelList;
  }

  function translate_BookingInformation($booking_info)
  {
  //Translate
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {
      $translatedTags = Array();
      $this->translation_api->StartBatch($this->toLang,$this->fromLang);

      $numbers = array();
      if (!empty($booking_info->Rooms->RoomType))
      {
        foreach($booking_info->Rooms->RoomType as $room)
        {
          if(empty($room->roomTypeDescriptionTranslated))
          {
            if(!empty($room->roomTypeDescription))
            {
              $texttoTrans = (string) $room->roomTypeDescription;

              $numbers[] = $this->extract_number($texttoTrans);
              $texttoTrans = $this->replace_number_of_string_by_d($texttoTrans);
              $texttoTrans = mb_strtolower($texttoTrans, 'UTF-8');
              $this->translation_api->addTextForBatch($texttoTrans,"HW room type description");
              array_push($translatedTags,"roomTypes");
            }
            elseif(!empty($room->roomType))
            {
              $texttoTrans = (string) $room->roomType;
              $numbers[] = $this->extract_number($texttoTrans);
              $text = $this->replace_number_of_string_by_d($texttoTrans);
              $texttoTrans = mb_strtolower($texttoTrans, 'UTF-8');
              $this->translation_api->addTextForBatch($texttoTrans,"HW room type");
              array_push($translatedTags,"roomTypes");
            }
          }

        }
      }

      $gtrans = $this->translation_api->end_batch();
      $roomkey = 0;

      //Update booking request object with translation results
      if((!empty($translatedTags)) &&(!empty($gtrans)))
      {
      for($i=0;$i<count($translatedTags);$i++)
        {
          switch($translatedTags[$i])
          {
            case "roomTypes":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if(((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||
                  (strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) ) &&
                  (strcmp($gtrans["responseData"][$i]["responseData"]["translatedText"],mb_strtolower($booking_info->Rooms->RoomType[$roomkey]->roomTypeDescription, 'UTF-8'))!= 0))
                  {
                    $booking_info->Rooms->RoomType[$roomkey]->roomTypeDescriptionTranslated = $this->replace_number($numbers[$roomkey], $gtrans["responseData"][$i]["responseData"]["translatedText"]);
                  }
                }
                else
                {
                  $booking_info->Rooms->RoomType[$roomkey]->roomTypeDescriptionTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_BookingInformation: " .current_url()." -> [roomTypes] -> ".$booking_info->Rooms->RoomType[$roomkey]->roomTypeDescriptionTranslatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
                $roomkey++;
                break;
          }
        }
      }
    }

    return $booking_info;
  }


  function translate_bookingRequest($booking_request, $property_number = "", $datestart = "", $numnights = "")
  {
    //Translate
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {
      $translatedTags = Array();
      $this->translation_api->StartBatch($this->toLang,$this->fromLang);

      if(!empty($booking_request->Message->messageText))
      {
        $this->translation_api->addTextForBatch($booking_request->Message->messageText,"HW Booking request message");
        array_push($translatedTags,"Message");
      }

      $numbers = array();
      if (!empty($booking_request->RoomDetails))
      {
        foreach($booking_request->RoomDetails as $room)
        {
          if(empty($room->roomTypeDescriptionTranslated))
          {
            if(!empty($room->roomTypeDescription))
            {
              $texttoTrans = (string) $room->roomTypeDescription;

              $numbers[] = $this->extract_number($texttoTrans);
              $texttoTrans = $this->replace_number_of_string_by_d($texttoTrans);
              $texttoTrans = mb_strtolower($texttoTrans, 'UTF-8');
              $this->translation_api->addTextForBatch($texttoTrans,"HW room type description");
              array_push($translatedTags,"roomTypes");
            }
            elseif(!empty($room->roomType))
            {
              $texttoTrans = (string) $room->roomType;

              $numbers[] = $this->extract_number($texttoTrans);
              $texttoTrans = $this->replace_number_of_string_by_d($texttoTrans);
              $texttoTrans = mb_strtolower($texttoTrans, 'UTF-8');
              $this->translation_api->addTextForBatch($texttoTrans,"HW room type");
              array_push($translatedTags,"roomTypes");
            }
          }

        }
      }

      if(!empty($booking_request->TermsAndConditions->note))
      {
        $this->translation_api->addTextForBatch($booking_request->TermsAndConditions->note, "HW term & condition note");
        array_push($translatedTags,"TermsAndConditionsNote");
      }
      if(!empty($booking_request->TermsAndConditions->value))
      {
        $this->translation_api->addTextForBatch("<pre>".$booking_request->TermsAndConditions->value."</pre>", "HW term & condition value");
        array_push($translatedTags,"TermsAndConditionsValue");
      }

      $gtrans = $this->translation_api->end_batch();
      $roomkey = 0;
      //Update booking request object with translation results
      if((!empty($translatedTags)) &&(!empty($gtrans)))
      {
      for($i=0;$i<count($translatedTags);$i++)
        {
          switch($translatedTags[$i])
          {
            case "Message":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $booking_request->Message->messageTextTranslated = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $booking_request->Message->messageTextTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_bookingRequest for property $property_number on $datestart for $numnights nights: " .current_url()." -> [Message] -> ".$booking_request->Message->messageTextTranslatedError . " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "roomTypes":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if(((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||
                     (strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) ) &&
                     (strcmp($gtrans["responseData"][$i]["responseData"]["translatedText"],mb_strtolower($booking_request->RoomDetails[$roomkey]->roomTypeDescription, 'UTF-8'))!= 0))
                  {
                      $booking_request->RoomDetails[$roomkey]->roomTypeDescriptionTranslated = $this->replace_number($numbers[$roomkey],$gtrans["responseData"][$i]["responseData"]["translatedText"]);
                  }
                }
                else
                {
                  $booking_request->RoomDetails[$roomkey]->roomTypeDescriptionTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_bookingRequest for property $property_number on $datestart for $numnights nights: " .current_url()." -> [roomTypes] -> ".$booking_request->RoomDetails[$roomkey]->roomTypeDescriptionTranslated. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
                $roomkey++;
              break;

            case "TermsAndConditionsNote":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $booking_request->TermsAndConditionsTranslated->note = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $booking_request->Message->messageTextTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_bookingRequest for property $property_number on $datestart for $numnights nights: " .current_url()." -> [Message] -> ".$booking_request->Message->messageTextTranslatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;

            case "TermsAndConditionsValue":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $booking_request->TermsAndConditionsTranslated->value = strip_tags($gtrans["responseData"][$i]["responseData"]["translatedText"]);
                }
                else
                {
                  $booking_request->Message->messageTextTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_bookingRequest for property $property_number on $datestart for $numnights nights: " .current_url()." -> [Message] -> ".$booking_request->Message->messageTextTranslatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
          }
        }
      }
    }

    return $booking_request;
  }

  function translate_BookingConfirmation($booking)
  {

    //Translate
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {
      $translatedTags = Array();
      $this->translation_api->StartBatch($this->toLang,$this->fromLang);

      if(!empty($booking->PropertyDetails->directions))
      {
        $this->translation_api->addTextForBatch(nl2p($booking->PropertyDetails->directions,false,true),"HW property directions");
        array_push($translatedTags,"directions");
      }

      $numbers = array();
      if (!empty($booking->RoomDetails))
      {
        foreach($booking->RoomDetails as $room)
        {
          if(empty($room->roomTypeDescriptionTranslated))
          {
            if(!empty($room->roomTypeDescription))
            {
              $texttoTrans = (string) $room->roomTypeDescription;
              $numbers[] = $this->extract_number($texttoTrans);
              $texttoTrans = $this->replace_number_of_string_by_d($texttoTrans);
              $texttoTrans = mb_strtolower($texttoTrans, 'UTF-8');

              $this->translation_api->addTextForBatch($texttoTrans,"HW room type description");
              array_push($translatedTags,"roomTypes");
            }
            elseif(!empty($room->roomType))
            {
              $texttoTrans = (string) $room->roomType;
              $numbers[] = $this->extract_number($texttoTrans);
              $texttoTrans = $this->replace_number_of_string_by_d($texttoTrans);
              $texttoTrans = mb_strtolower($texttoTrans, 'UTF-8');

              $this->translation_api->addTextForBatch($texttoTrans,"HW room type");
              array_push($translatedTags,"roomTypes");
            }
          }
        }
      }

      if(!empty($booking->TermsAndConditions->note))
      {
        $this->translation_api->addTextForBatch($booking->TermsAndConditions->note, "HW term & condition note");
        array_push($translatedTags,"TermsAndConditionsNote");
      }
      if(!empty($booking->TermsAndConditions->value))
      {
        $this->translation_api->addTextForBatch("<pre>".$booking->TermsAndConditions->value."</pre>", "HW term & condition value");
        array_push($translatedTags,"TermsAndConditionsValue");
      }
      if(!empty($booking->ChargedCurrencyWarning->note))
      {
        $this->translation_api->addTextForBatch($booking->ChargedCurrencyWarning->note, "HW charged currency warning");
        array_push($translatedTags,"ChargedCurrencyWarning");
      }

      $gtrans = $this->translation_api->end_batch();
//       debug_dump($gtrans);
      $roomkey = 0;
      //Update booking request object with translation results
      if((!empty($translatedTags)) &&(!empty($gtrans)))
      {
        for($i=0;$i<count($translatedTags);$i++)
        {
          switch($translatedTags[$i])
          {
            case "directions":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $booking->PropertyDetails->directionsTranslated = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $booking->PropertyDetails->directionsTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_BookingConfirmation at ".$booking->PropertyDetails->propertyName." in ".$booking->PropertyDetails->city.", ".$booking->PropertyDetails->state.": " .current_url()." -> [directions] -> ".$booking->PropertyDetails->directionsTranslatedError." -> ".$booking->PropertyDetails->directions. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "roomTypes":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if(((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||
                      (strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) ) &&
                      (strcmp($gtrans["responseData"][$i]["responseData"]["translatedText"],mb_strtolower($booking->RoomDetails[$roomkey]->roomTypeDescription, 'UTF-8'))!= 0))
                  {
                      $booking->RoomDetails[$roomkey]->roomTypeDescriptionTranslated = $this->replace_number($numbers[$roomkey],$gtrans["responseData"][$i]["responseData"]["translatedText"]);
                  }
                }
                else
                {
                  $booking->RoomDetails[$roomkey]->roomTypeDescriptionTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_BookingConfirmation at ".$booking->PropertyDetails->propertyName." in ".$booking->PropertyDetails->city.", ".$booking->PropertyDetails->state.": " .current_url()." -> [roomTypes] -> ".$booking->RoomDetails[$roomkey]->roomTypeDescriptionTranslatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
                $roomkey++;
              break;

            case "TermsAndConditionsNote":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $booking->TermsAndConditionsTranslated->note = $gtrans["responseData"][$i]["responseData"]["translatedText"];
                }
                else
                {
                  $booking_request->Message->messageTextTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_bookingRequest at ".$booking->PropertyDetails->propertyName." in ".$booking->PropertyDetails->city.", ".$booking->PropertyDetails->state.": " .current_url()." -> [Message] -> ".$booking_request->Message->messageTextTranslatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;

            case "TermsAndConditionsValue":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $booking->TermsAndConditionsTranslated->value = strip_tags($gtrans["responseData"][$i]["responseData"]["translatedText"]);
                }
                else
                {
                  $booking_request->Message->messageTextTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_bookingRequest at ".$booking->PropertyDetails->propertyName." in ".$booking->PropertyDetails->city.", ".$booking->PropertyDetails->state.": " .current_url()." -> [Message] -> ".$booking_request->Message->messageTextTranslatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
            case "ChargedCurrencyWarning":
                if($gtrans["responseData"][$i]["responseStatus"] == 200)
                {
                  if((!isset($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][$i]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
                  $booking->ChargedCurrencyWarning->noteTranslated = strip_tags($gtrans["responseData"][$i]["responseData"]["translatedText"]);
                }
                else
                {
                  $booking_request->ChargedCurrencyWarning->noteTranslatedError = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_bookingRequest at ".$booking->PropertyDetails->propertyName." in ".$booking->PropertyDetails->city.", ".$booking->PropertyDetails->state.": " .current_url()." -> [Message] -> ".$booking_request->ChargedCurrencyWarning->noteTranslatedError. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
              break;
          }
        }
      }
    }
    return $booking;
  }

  /*
   * @return true if at least one review has benn translated
   */
  function translate_mixed_reviews(&$reviews)
  {
    $reviews_translated = false;

    //For now force auto-detection of from languages for reviews

    //Translate
    if(strcasecmp($this->toLang,"")!=0)
    {

      if(!empty($reviews))
      {
        $this->translation_api->StartBatch($this->toLang,"");


        foreach($reviews as $review)
        {
          $textToTranslate = strip_tags($review["review"]);
          $this->translation_api->addTextForBatch($textToTranslate,"HW review");
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
              $reviews[$i]["review_translated"] = html_entity_decode($gtrans["responseData"][$i]["responseData"]["translatedText"],ENT_QUOTES);
              $reviews_translated = true;
            }
          }
          else
          {
            $reviews[$i]["review_translatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
            log_message(TRANSLATION_ERROR_LEVEL,"translate_mixed_reviews: " .current_url()." -> [review] -> ".$reviews[$i]["review_translatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
          }
          $i++;
        }
      }

    }
    return $reviews_translated;
  }

  function translate_location_reviews(&$properties_reviewed)
  {
    if(is_null($this->CI))
    {
      $this->CI =& get_instance();
    }

    $trans_error = FALSE;
    $reviews_translated = false;
    //Translate

    //For now force auto-detection of from languages for reviews
    if(strcasecmp($this->toLang,"")!=0)
    {

      if(!empty($properties_reviewed))
      {
        $this->translation_api->StartBatch($this->toLang,"");


        foreach($properties_reviewed as $properties)
        {
          foreach($properties['user_reviews'] as $review)
          {
            $textToTranslate = strip_tags($review["review"]);
            $this->translation_api->addTextForBatch($textToTranslate,"HW review");
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
                    $properties_reviewed[$prop_number]['user_reviews'][$r]["review_translated"] = html_entity_decode($gtrans["responseData"][$i]["responseData"]["translatedText"],ENT_QUOTES);
                    $reviews_translated = true;
                  }
                }
                elseif(!empty($gtrans["responseData"][$i]))
                {
                  $trans_error = TRUE;
                  $properties_reviewed[$prop_number]['user_reviews'][$r]["review_translatedError"] = "Translation error ".$gtrans["responseData"][$i]["responseStatus"].": ".$gtrans["responseData"][$i]["responseDetails"];
                  log_message(TRANSLATION_ERROR_LEVEL,"translate_mixed_reviews: " .current_url()." -> [review] -> ".$properties_reviewed[$prop_number]['user_reviews'][$r]["review_likebest_translatedError"]. " | google status -> ".$gtrans["responseData"][$i]["responseStatus"]);
                }
                else
                {
                  $trans_error = TRUE;
                  log_message(TRANSLATION_ERROR_LEVEL,"translation missing in translation API reply: " .current_url()." -> [review]");
                }
                $i++;
              }
            }
          }
        }
      }

      //IF there is a translation error cancel caching
      if($trans_error === TRUE)
      {
        $this->CI->output->cache(0);
      }
    }

    return $reviews_translated;
  }

  /*
   *
   */
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
          $this->translation_api->addTextForBatch($textToTranslate,"HW short description");
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

  function translate_Error($error)
  {
    //Translate
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {
      if(!empty($error->Error->detail))
      {
        //From API documentation, it is assumed that Error->detail and Error->message are always present together
        $this->translation_api->StartBatch($this->toLang,$this->fromLang);
        $this->translation_api->addTextForBatch((String)$error->Error->detail,"HW Error detail");
        $this->translation_api->addTextForBatch($error->Error->message,"HW Error message");
        $gtrans = $this->translation_api->end_batch();

//        print_r($gtrans);
        //Assumed that Error->message and Error->detail are always present
        //so 0 is always detail and 1 is always message
        if($gtrans["responseData"][0]["responseStatus"] == 200)
        {
          if((!isset($gtrans["responseData"][0]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][0]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
          {
            $error->Error->detailTranslated = $gtrans["responseData"][0]["responseData"]["translatedText"];
          }
        }
        else
        {
          $error->Error->detailTranslatedError = "Translation error ".$gtrans["responseData"][0]["responseStatus"].": ".$gtrans["responseData"][0]["responseDetails"];
          log_message(TRANSLATION_ERROR_LEVEL,"translate_Error: " .current_url()." -> [detail] -> ".$error->Error->detailTranslatedError." -> ".$error->Error->detail. " | google status -> ".$gtrans["responseData"][0]["responseStatus"]);

        }

        if($gtrans["responseData"][1]["responseStatus"] == 200)
        {
          if((!isset($gtrans["responseData"][1]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][1]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
          {
            $error->Error->messageTranslated = $gtrans["responseData"][1]["responseData"]["translatedText"];
          }
        }
        else
        {
          $error->Error->messageTranslatedError = "Translation error ".$gtrans["responseData"][1]["responseStatus"].": ".$gtrans["responseData"][1]["responseDetails"];
          log_message(TRANSLATION_ERROR_LEVEL,"translate_Error: " .current_url()." -> [message] -> ".$error->Error->messageTranslatedError." -> ".$error->Error->message. " | google status -> ".$gtrans["responseData"][1]["responseStatus"]);
        }
      }
    }

    return $error;
  }


  function translate_UserMessage($userMessage)
  {

    //Translate
    if(strcasecmp($this->toLang,$this->fromLang)!=0)
    {
      if(!empty($userMessage->UserMessage->detail))
      {
        //From API documentation, it is assumed that UserMessage->detail and UserMessage->message are always present together
        //
        $this->translation_api->StartBatch($this->toLang,$this->fromLang);
        $this->translation_api->addTextForBatch((String)$userMessage->UserMessage->detail,"HW UserMessage detail");
        $this->translation_api->addTextForBatch($userMessage->UserMessage->message,"HW UserMessage message");
        $gtrans = $this->translation_api->end_batch();

//        print_r($gtrans);
        //Assumed that UserMessage->message and UserMessage->detail are always present
        //so 0 is always detail and 1 is always message
        if($gtrans["responseData"][0]["responseStatus"] == 200)
        {
          if((!isset($gtrans["responseData"][0]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][0]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
          {
            $userMessage->UserMessage->detailTranslated = $gtrans["responseData"][0]["responseData"]["translatedText"];
          }
        }
        else
        {
          $userMessage->UserMessage->detailTranslatedError = "Translation error ".$gtrans["responseData"][0]["responseStatus"].": ".$gtrans["responseData"][0]["responseDetails"];
          log_message(TRANSLATION_ERROR_LEVEL,"translate_UserMessage: " .current_url()." -> [detail] -> ".$userMessage->UserMessage->detailTranslatedError." -> ".$userMessage->UserMessage->detail);
        }

        if($gtrans["responseData"][1]["responseStatus"] == 200)
        {
          if((!isset($gtrans["responseData"][1]["responseData"]["detectedSourceLanguage"]))||(strcasecmp($gtrans["responseData"][1]["responseData"]["detectedSourceLanguage"],$this->toLang)!=0) )
          {
            $userMessage->UserMessage->messageTranslated = $gtrans["responseData"][1]["responseData"]["translatedText"];
          }
        }
        else
        {
          $userMessage->UserMessage->messageTranslatedError = "Translation error ".$gtrans["responseData"][1]["responseStatus"].": ".$gtrans["responseData"][1]["responseDetails"];
          log_message(TRANSLATION_ERROR_LEVEL,"translate_UserMessage: " .current_url()." -> [message] -> ".$userMessage->UserMessage->messageTranslatedError." -> ".$userMessage->UserMessage->message);
        }
      }
    }

    return $userMessage;
  }

  function translate_APIError($error)
  {
    if(isset($error->UserMessage))
    {
      $error = $this->translate_UserMessage($error);
    }

    if(isset($error->Error))
    {
      $error = $this->translate_Error($error);
    }
    return $error;
  }

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