<?php
/**
 * @author Louis-Michel Raynauld
 *
 * @license toset
 * References:
 *
 * - Hostelbookers.com contentcurrency document: Web Service Integration v 2.1.1 June 2010
 * - Hostelbookers.com booking document: Web Service Integration v 2.1.1 June 2010
 *
 * languages supported by API for all functiond according to Appendix C of above reference
 *
 * EN	English
 * ES	Spanish
 * DE	German
 * FR	French
 * IT	Italian
 * PL	Polish
 * PT	Portuguese
 * DK	Danish
 * NL	Dutch
 * NW	Norwegian
 * SW	Swedish
 *
 */

class Hostelbookers_api extends Model {

  //location of the api wsdl
  var $live_wsdl    = "http://v1.hb-api.com/api/remote/hbcontentcurrency.cfc?wsdl";
  var $live_wsdl_booking    = "https://v1.hb-api.com/api/remote/hbbooking.cfc?wsdl";
  var $live_apikey    = "146588823";
//   var $live_wsdl    = "http://www.hbstaging.net/api/remote/hbcontentcurrency.cfc?wsdl";
//   var $live_wsdl_booking    = "http://www.hbstaging.net/api/remote/hbbooking.cfc?wsdl";
//   var $live_apikey    = "HostelBookers19367";

  var $staging_wsdl = "http://www.hbstaging.net/api/remote/hbcontentcurrency.cfc?wsdl";
  var $staging_wsdl_booking = "http://www.hbstaging.net/api/remote/hbbooking.cfc?wsdl";
  var $staging_apikey = "HostelBookers19367";

  var $apikey         = "HostelBookers19367";
  var $affiliate_name = "mcweb";
  var $affiliate_id   = "19367";

  var $hbapi = NULL;

  var $hbbookingapi = NULL;

  var $CI = NULL;

  var $testmode = 0;

  function Hostelbookers_api()
  {
    parent::Model();

    $config =& get_config();

    if (is_numeric($config['log_threshold']))
    {
      $this->_threshold = $config['log_threshold'];
    }

    $this->log_path = ($config['log_path'] != '') ? $config['log_path'] : BASEPATH.'logs/';

    $this->CI =& get_instance();
    $this->CI->load->library('tank_auth');

    $this->testmode = $this->CI->config->item('booking_test_mode');
    $user_id = $this->CI->tank_auth->get_user_id();
    if(($user_id !== false))
    {
      $uprof = $this->CI->tank_auth->get_profile($user_id);
      if($uprof->user_level_id > 1)
      {
        $this->testmode = 1;
      }
    }
    //Set up live key and live soap client
    $this->apikey = $this->live_apikey;
    $this->hbapi = new SoapClient($this->live_wsdl);

    //Setup staging site if test mode is detected
    if($this->testmode > 0)
    {
      log_message('debug', "Using test mode for HB call");
      $this->apikey = $this->staging_apikey;
      $this->hbapi = new SoapClient($this->staging_wsdl, array('trace' => 1));
    }

  }

  function hbBookingConnect()
  {
    if(is_null($this->hbbookingapi))
    {
      try
      {
        $this->hbapibooking = new SoapClient($this->live_wsdl_booking);
        if($this->testmode > 0)
        {
          $this->hbapibooking = new SoapClient($this->staging_wsdl_booking, array('trace' => 1));
        }
      }
      catch(SoapFault $exception)
      {
        log_message("Error",__FUNCTION__ .' Could not reach HB booking API. error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
      }

    }
  }

  /**
   * getCountryList
   *
   * get array of bookable countries
   * in a given continent
   * country names in a given language
   *
   *
   * @param language_code    Optional string, 2-letter ISO language code. See Appendix C
   * @param continent_code   Optional string, 2-letter continent code
   *
   *
   * @return
   */
  function getCountryList($language_code = "en", $continent_code = "")
  {
    try
    {
      return $this->hbapi->getCountryList( $this->apikey, $language_code, $continent_code );
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
      return false;
    }
    return false;
  }

  /**
   * getContinentList
   *
   *
   * NOTE: API reference stated that this request is currently not fully implemented
   *
   * @param language_code    Optional string, 2-letter ISO language code. See Appendix C
   *
   *
   * @return
   */
  function getContinentList($language_code = "en")
  {
    try
    {
      return $this->hbapi->getContinentList( $this->apikey, $language_code);
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }

  /**
   * getCountryLocationList
   *
   * get nested array of bookable countries and cities
   * country & city names in a given language
   *
   *
   * @param language_code    Optional string, 2-letter ISO language code. See Appendix C
   * @param continent_code   Optional string, 2-letter continent code
   *
   *
   * @return
   */
  function getCountryLocationList($language_code = "en", $continent_code = "")
  {
    try
    {
      return $this->hbapi->getCountryLocationList( $this->apikey, $language_code, $continent_code );
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }

  /**
   * getLocationData
   *
   * get location description and array of bookable properties at location
   *
   * @param  language 	Optional string, 2-letter ISO language code. See Appendix C.
   * @param  location	String, system name of a bookable location in the country **
   * @param  country	String, system name of a bookable country **
   * @param  strCurrencyCode	Optional string, currency code for returned prices
   *
   *
   * @return
   */
  function getLocationData($country, $location, $language_code = "en", $strCurrencyCode = "")
  {
    try
    {
      return $this->hbapi->getLocationData( $this->apikey, $language_code, $location, $country,  $strCurrencyCode );
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }

  /**
   * getPropertyDataByID
   *
   * Get assorted information about a property. Note that if the property doesn't have the content in the language requested then it will be returned in English.
   *
   * @param  language 	Optional string, 2-letter ISO language code. See Appendix C. Features and Long Description returned translated where available.
   * @param  property	Int, property reference number
   *
   * @return
   */
  function getPropertyDataByID($property_number, $language_code = "en")
  {
    try
    {
       $return = $this->hbapi->getPropertyDataByID( $this->apikey, $language_code, $property_number );

       if($this->testmode > 0)
       {
          log_message('debug', "last API response ".$this->hbapi->__getLastResponse());
       }     
       return $return;
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }

  /**
   * getPropertyAvailability
   *
   * get bed availability and prices at a given property for a time period defined by arrival date and number of nights
   *
   * @param  language 	Optional string, 2-letter ISO language code. See Appendix C.
   * @param  property	Int, property ID number
   * @param  date	Date, arrival date in ‘dd-Mmm-yyyy’ format
   * @param  days	Int, number of days, 1 to 31
   * @param  strCurrencyCode	Optional string, currency code for returned prices
   *
   * @return
   */
  function getPropertyAvailability($property_number, $startDate, $numNights, $language_code = "en", $strCurrencyCode = "")
  {
    try
    {
      return $this->hbapi->getPropertyAvailability( $this->apikey, $language_code, $property_number, $startDate, $numNights, $strCurrencyCode );
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }


  /**
   * getPropertyAvailabilityCalendat
   *
   * get bed availability and prices at a given property for a time period defined by arrival date and number of nights
   *
   * @param  language 	Optional string, 2-letter ISO language code. See Appendix C.
   * @param  property	Int, property ID number
   * @param  date	Date, arrival date in ‘dd-Mmm-yyyy’ format
   * @param  days	Int, number of days, 1 to 31
   * @param  strCurrencyCode	Optional string, currency code for returned prices
   *
   * @return
   */
  function getPropertyAvailabilityCalendar($property_number, $startDate, $numNights, $language_code = "en", $strCurrencyCode = "")
  {
    try
    {
      return $this->hbapi->getPropertyAvailabilityCalendar( $this->apikey, $property_number, $startDate, $numNights, $strCurrencyCode );
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }

  /**
   * getLocationAvailability
   *
   * returns availability in a specified location across a specified timespan (max 30 days)
   *
   * @param  locationID	ID number of the location
   * @param  date	Date, arrival date in ‘dd-Mmm-yyyy’ format
   * @param  days	Int, number of days, 1 to 31
   * @param  strCurrencyCode	Optional string, currency code for returned prices
   * @param  language 	Optional string, 2-letter ISO language code. See Appendix C.
   *
   * @return
   */
  function getLocationAvailability($location_id, $startDate, $numNights, $language_code = "en", $strCurrencyCode = "")
  {
    try
    {
      return $this->hbapi->getPropertyAvailability4( $this->apikey, $location_id, $startDate, $numNights, $strCurrencyCode, $language_code );
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }

  /**
   * getLocationAvailability5
   *
   * returns availability in a specified location across a specified timespan (max 30 days)
   *
   * @param  locationID	ID number of the location
   * @param  date	Date, arrival date in ‘dd-Mmm-yyyy’ format
   * @param  days	Int, number of days, 1 to 31
   * @param  strCurrencyCode	Optional string, currency code for returned prices
   * @param  language 	Optional string, 2-letter ISO language code. See Appendix C.
   *
   * @return
   */
  function getLocationAvailabilityCheapRoom($location_id, $startDate, $numNights, $language_code = "en", $strCurrencyCode = "")
  {
    try
    {
      return $this->hbapi->getPropertyAvailability5( $this->apikey, $location_id, $startDate, $numNights, $strCurrencyCode, $language_code );
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }
  /**
   * getPropertyPricing
   *
   * @param  locationID	ID number of the location
   * @param  date	Date, arrival date in ‘dd-Mmm-yyyy’ format
   * @param  days	Int, number of days, 1 to 31
   * @param  strCurrencyCode	Optional string, currency code for returned prices
   * @param  language 	Optional string, 2-letter ISO language code. See Appendix C.
   *
   * @return
   */
  function getPropertyPricingPerDate($location_id, $startDate, $numNights, $language_code = "en", $strCurrencyCode = "")
  {
    try
    {
      return $this->hbapi->getPropertyPricing2( $this->apikey, $location_id, $startDate, $numNights, $strCurrencyCode, $language_code );
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }

  /**
   * getPropertyPricing
   *
   * @param  locationID	ID number of the location
   * @param  date	Date, arrival date in ‘dd-Mmm-yyyy’ format
   * @param  days	Int, number of days, 1 to 31
   * @param  strCurrencyCode	Optional string, currency code for returned prices
   * @param  language 	Optional string, 2-letter ISO language code. See Appendix C.
   *
   * @return
   */
  function getPropertyPricing($location_id, $startDate, $numNights, $language_code = "en", $strCurrencyCode = "")
  {
    try
    {
      return $this->hbapi->getPropertyPricing( $this->apikey, $location_id, $startDate, $numNights, $strCurrencyCode, $language_code );
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }

  /**
   * getPropertyRoomPricing2
   *
   * @param  propertyID	ID number of the property
   * @param  roomIds	String, list of room ids delimited by pipes (“|”)
   * @param  date	Date, arrival date in ‘dd-Mmm-yyyy’ format
   * @param  days	Int, number of days, 1 to 31
   * @param  strCurrencyCode	Optional string, currency code for returned prices
   * @param  language 	Optional string, 2-letter ISO language code. See Appendix C.
   *
   * @return
   */
  function getPropertyRoomPricingPerDate($property_id, $roomsIDs, $startDate, $numNights, $language_code = "en", $strCurrencyCode = "")
  {
    try
    {
      return $this->hbapi->getPropertyRoomPricing2( $this->apikey, $property_id, $roomsIDs, $startDate, $numNights, $strCurrencyCode, $language_code );
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }

  /**
   * getPropertyReviews
   *
   * @param  propertyID	  ID number of the property
   * @param  num_reviews	The number of reviews for the property. Maximum value for this field is 25.
   *
   * @return
   */
  function getPropertyReviews($property_id, $num_reviews = 25)
  {
    try
    {
      $return = $this->hbapi->getPropertyReviews( $this->apikey, $property_id, $num_reviews );

      if($return !== false)
      {
        $return = simplexml_load_string($return);
      }
      return $return;
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    catch(Exception $e)
    {
      log_message('error', __FUNCTION__.' : simplexml_load_string error: '.$e->getMessage());
    }
    return false;
  }

  /**
   * make_booking
   *
   * This method takes payment of a deposit for the reservation of multiple rooms, across a range of dates for a single property.
   * It reserves the rooms, and sends notification emails to the property, the customer, and the affiliate, informing them of the reservation.
   *
   * @param  language 	Optional string, 2-letter ISO language code. See Appendix C.
   * @param  property	Int, property ID number
   * @param  date	Date, arrival date in ‘dd-Mmm-yyyy’ format
   * @param  days	Int, number of days, 1 to 31
   * @param  strCurrencyCode	Optional string, currency code for returned prices
   *
   * @return
   */
  function make_booking($testmode = 0,
                        $firstname,
                        $lastname,
                        $nationality,
                        $male_count,
                        $female_count,
                        $tel,
                        $email,
                        $preferred_currency,
                        $card_holder,
                        $card_type,
                        $card_number,
                        $card_expiry_date,
                        $card_cvv,
                        $property_number,
                        $dateStart,
                        $arrival_time,
                        $numNights,
                        $room_selections,
                        $beds_count,
                        $language_code = "en",
                        $card_issue = "",
                        $card_start_Date = NULL,
                        $comment = "")
  {

    $booking_object = new SimpleXMLElement("<bookingData></bookingData>");
    $booking_object->addChild("acceptedTermsAndConditions", "true");
    $booking_object->addChild("property", $property_number);
    $booking_object->addChild("arrivalDate", $dateStart);
    $booking_object->addChild("arrivalTime", "$arrival_time:00:00");
    $booking_object->addChild("totalNumMales", $male_count);
    $booking_object->addChild("totalNumFemales", $female_count);
    $booking_object->addChild("nights", $numNights);
    $booking_object->addChild("comment", $comment);

    $client = new SimpleXMLElement("<customer></customer>");
    $client = $booking_object->addChild("customer", $client);
    $client->addchild("firstName",$firstname);
    $client->addchild("lastName",$lastname);
    $client->addchild("email",$email);
    $client->addchild("tel",$tel);
    $client->addchild("nationality",$nationality);
    $client->addchild("preferredCurrency",$preferred_currency);

    if($testmode > 0)
    {
      if(strcmp($card_type,"Mastercard") == 0)
      {
        $card_number = "5301250070000050";
      }
      elseif(strcmp($card_type,"Visa") == 0)
      {
        $card_number = "4715320629000001";
      }
      elseif(strcmp($card_type,"JCB") == 0)
      {
        $card_number = "3540599999991047";
      }
      elseif(strcmp($card_type,"Electron") == 0)
      {
        $card_number = "4917480000000008";
      }
      elseif(strcmp($card_type,"UK Maestro") == 0)
      {
        $card_number = "675938410597000022";
        $card_issue = 5;
      }
      elseif(strcmp($card_type,"Solo") == 0)
      {
        $card_number = "6767051323183400359";
        $card_issue = 1;
      }
    }

    $payment = new SimpleXMLElement("<payment></payment>");
    $payment = $booking_object->addChild("payment", $payment);
    $payment->addchild("cardtype",$card_type);
    $payment->addchild("cardNumber",$card_number);
    $payment->addchild("cvv",$card_cvv);
    $payment->addchild("cardHolder",$card_holder);
    $payment->addchild("expiry",$card_expiry_date);
    $payment->addchild("issue",$card_issue);

    if(!empty($card_start_Date))
    {
      $payment->addchild("start",$card_start_Date);
    }

    $rooms = new SimpleXMLElement("<rooms></rooms>");
    $rooms = $booking_object->addChild("rooms", $rooms);

    foreach($room_selections as $key => $room_id)
    {
      if($beds_count[$key] > 0)
      {
        $room = new SimpleXMLElement("<room></room>");
        $room = $rooms->addChild("room", $room);

        $room->addchild("id",$room_id);
        $room->addchild("beds",$beds_count[$key]);
      }
    }
    $this->hbBookingConnect();

    try
    {

      $return = $this->hbapibooking->makeBooking ( $this->affiliate_name, $this->apikey, $booking_object->asXML(), $language_code);

//      $this->load->helper('file');
//      $return = read_file('./apibookingreturn.xml');

      if($return !== false)
      {
        $return = simplexml_load_string($return);
      }
      return $return;
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
//       print(__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    catch(Exception $e)
    {
      log_message('error', __FUNCTION__.' : simplexml_load_string error: '.$e->getMessage());
    }

    return false;
  }

  /**
   * getNationalities
   *
   * It is the purpose of this method to return a list of nationalities that may be used as values for a select-box on the booking payment
   *  form (the nationality of the customer making the booking is a required piece of information to send to the makeBooking method in the
   *   xmlPacket parameter ).
   *
   * @param  affiliateName 	string, required, affiliate - Identifier for the affiliate
   * @param  key            string, required, key - HB web service auth key
   * @param  language	      string, optional, language code (defaults to ‘en’)
   *
   * @return
   */
  function getNationalities ($language_code = "en")
  {
    $this->hbBookingConnect();
    try
    {
      return $this->hbapibooking->getNationalities  ( $this->affiliate_name, $this->apikey, $language_code);
    }
    catch(SoapFault $exception)
    {
      log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }
  /**
   * getTermsAndConditions
   *
   * return the current terms and conditions which the user must agree to when making a booking through the makeBooking method.
   *
   * @param  affiliateName 	string, required, affiliate - Identifier for the affiliate
   * @param  key            string, required, key - HB web service auth key
   * @param  language	      string, optional, language code (defaults to ‘en’)
   *
   * @return
   */
  function getTermsAndConditions($language_code = "en")
  {
    $this->hbBookingConnect();
    try
    {
      return $this->hbapibooking->getTermsAndConditions ( $this->affiliate_name, $this->apikey, $language_code);
    }
    catch(SoapFault $exception)
    {
//       print(__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
     log_message("Error",__FUNCTION__ .' error: "' . $exception->faultcode . '" - ' . $exception->faultstring);
    }
    return false;
  }

  //adding s before extension returns thumbnail of image per HB server
  //No doc on this just happen to be this way could change....
  //possible .jpeg, .jpg
  function build_thumb_url($image_url)
  {
    $url_array = parse_url($image_url);

    $filename = explode(".",$url_array['path']);

    if(substr($filename[0], -1) == 's')
    {
      return $image_url;
    }
    return $url_array['scheme']."://".$url_array['host'].$filename[0]."s.".$filename[1];
  }

  function is_thumb_url($image_url)
  {
    $url_array = parse_url($image_url);

    $filename = explode(".",$url_array['path']);

    if(substr($filename[0], -1) == 's')
    {
      return true;
    }

    return false;
  }
  //Should not be used hoping eventually API will return this
  function getCardTypes_options($card_selected)
  {
    ?>
    <option <?php if(strcmp($card_selected,"Mastercard")==0) echo "selected=\"selected\""; ?> value="Mastercard">Mastercard</option>
    <option <?php if(strcmp($card_selected,"Visa")==0) echo "selected=\"selected\""; ?> value="Visa">Visa</option>
    <option <?php if(strcmp($card_selected,"Electron")==0) echo "selected=\"selected\""; ?> value="Electron">Electron</option>
    <option <?php if(strcmp($card_selected,"JCB")==0) echo "selected=\"selected\""; ?> value="JCB">JCB</option>
    <option <?php if(strcmp($card_selected,"Maestro")==0) echo "selected=\"selected\""; ?> value="Maestro">Maestro</option>
    <option <?php if(strcmp($card_selected,"Solo")==0) echo "selected=\"selected\""; ?> value="Solo">Solo</option>
    <option <?php if(strcmp($card_selected,"UK Maestro")==0) echo "selected=\"selected\""; ?> value="UK Maestro">UK Maestro</option>
    <?php
  }

/**
   * Api handle the following langages:
   * EN	English
   * ES	Spanish
   * DE	German
   * FR	French
   * IT	Italian
   * PL	Polish
   * PT	Portuguese
   * DK	Danish
   * NL	Dutch
   * NW	Norwegian
   * SW	Swedish
   *
   */
  function lang_code_convert($lang_code)
  {
    switch(strtolower($lang_code))
    {
      case strtolower("en"):
        return "en";
      case strtolower("es"):
        return "es";
      case strtolower("de"):
        return "de";
      case strtolower("it"):
        return "it";
      case strtolower("fr"):
        return "fr";
      case strtolower("pl"):
        return "pl";
      case strtolower("pt"):
        return "pt";
      case strtolower("dk"):
        return "dk";
      case strtolower("nl"):
        return "nl";
      case strtolower("nw"):
        return "nw";
      case strtolower("sw"):
        return "sw";
      default:
        return "en";
    }

    return "en";
  }
}
