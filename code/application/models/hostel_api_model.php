<?php
/**
 * @author Louis-Michel Raynauld
 *
 * @license toset
 * References: Hostelworld.com document: WRI - Affiliate API - Developpers Guide - v3.0.8.doc
 *
 */

class Hostel_api_model extends CI_Model {

  var $hostelworld_API_url = "";

  function Hostel_api_model()
  {
    parent::__construct();

    $config =& get_config();

    if (is_numeric($config['log_threshold']))
    {
      $this->_threshold = $config['log_threshold'];
    }

    $this->log_path = ($config['log_path'] != '') ? $config['log_path'] : BASEPATH.'logs/';

  }

  /**
   *  cityCountryList
   *
   * Required Variables:
   *
   * UserID: As with calls to all scripts, you must pass through your UserID so that your connection can be
   *         authenticated.
   *
   * Optional variables:
   *
   * [Country]: If you only want to get information & cities for a particular country, then specify a Country.
   *
   */



  function cityCountryList($userID, $country = NULL)
  {

    $url_api = $this->config->item('hostelworld_API_url');
    $url_api.= "CityCountryList.php";
    $data = array ();
    $data['UserID'] = $userID;

    if($country != NULL)
    {
      $data['Country'] = $country;
    }

    $file_xml = $this->do_post_request($url_api,$data);

    return $this->_validate_api_data($file_xml, "/CityCountryList", "/SystemMessage", "cityCountryList");

  }

  /**
   *  PropertyLocationSearch
   *
   * Required Variables:
   *
   * UserID: As with calls to all scripts, you must pass through your UserID so that your connection can be
   *         authenticated.
   *
   * City: The City you are doing an availability search for.
   *
   * Country: The Country you are doing an availability search for.
   *
   * Optional variables:
   *
   * [State]: The State that the property is in. This is an optional variable and can either be the 2 letter ISO
   *          code or the full state name. Eg: “California” or “CA”.
   *
   * [DateStart]: The Start Date for the search. This will be the first date of the guest’s stay. The format for
   *              DateStart is always YYYY-MM-DD. If DateStart and NumNights are not passed in, a list of all
   *              properties will be returned regardless of availability.
   *
   * [NumNights]: The number of nights the guest wishes to stay.
   *
   * [Currency]: If no currency is specified, prices will be shown in the property’s own currency. If a currency is
   *             specified, the bed price will be converted from the property’s currency to the specified one at
   *             today’s rates*. Currency should be passed to the script as a three-character ISO currency code.
   *
   * [ShowRoomInfo]: When 1 is passed in for this variable, each <Property> element returned will also include a
   *                 <Rooms> element. This <Rooms> element is in a similar format to the one returned in the
   *                 PropertyBookingInformation call.
   *
   * [PropTypes]: This is an additional variable that can be passed in if you only want certain property types to
   *              be returned for your search. The property types available are HOSTEL, HOTEL, GUESTHOUSE, CAMPSITE
   *              and APARTMENT. For example, if you only wanted the search to return hotels and guesthouses, you
   *              would pass in the following: PropTypes=HOTEL,GUESTHOUSE
   *
   * [Language]: Optional variable to set the language of the results information. Current options are French,
   *             Spanish, German and Italian. The default language is English.
   *
   * [LimitResults]: Optional variable to limit the number of properties returned. If this option is used, the top
   *                 level <PropertyLocationSearch> element will have 2 attributes, size and numresults. Size will
   *                 be equal to the number of results returned, and numresults will be the total available for the
   *                 location if no limits were set.
   *
   * [ResultsStart]: Optional variable to start results from a certain number onwards. E.G. Used to display results
   *                 in pages rather than all at once when combined with LimitResults
   *
   * Behavior:
   *
   * 1. Post variables to hostelsworld.com API - propertyLocationSearch.php
   * 2. Get xml result from hostelsworld.com API
   * 2. Filter all special character that can cause problems to simplexml module ('&')
   * 3. Return property object
   */
  function propertyLocationSearch($userID,
                                  $city,
                                  $country,
                                  $dateStart = NULL,
                                  $numNights = NULL,
                                  $currency = NULL,
                                  $language = "English",
                                  $propTypes = NULL,
                                  $showRoomInfo = 0,
                                  $limit = NULL,
                                  $limitStart = NULL,
                                  $state = NULL
                                  )
  {

    $file_xml = $this->config->item('hostelworld_API_url');
    $file_xml.= "PropertyLocationSearch.php";
    $file_xml.= "?UserID=".$userID;
    $file_xml.= "&Country=".urlencode($country);
    $file_xml.= "&City=".urlencode($city);
    $file_xml.= "&ShowRoomInfo=".$showRoomInfo;
    $file_xml.= "&Language=".$language;

    if($dateStart != NULL)
    {
      $file_xml.= "&DateStart=".$dateStart;
    }
    if($numNights != NULL)
    {
      $file_xml.= "&NumNights=".$numNights;
    }
    if($currency != NULL)
    {
      $file_xml.= "&Currency=".$currency;
    }
    if($propTypes != NULL)
    {
      $file_xml.= "&PropTypes=".$propTypes;
    }
    if($limit != NULL)
    {
      $file_xml.= "&LimitResults=".$limit;
    }
    if($limitStart != NULL)
    {
      $file_xml.= "&ResultsStart=".$limitStart;
    }
    if($state != NULL)
    {
      $file_xml.= "&State=".urlencode($state);
    }

    $file_xml = $this->get_API_XML_and_Audit($file_xml, 'PropertyLocationSearch');

    return $this->_validate_api_data($file_xml, "/PropertyLocationSearch/Property", "/SystemMessage", "propertyLocationSearch");
  }

  /**
   * PropertyInformation
   *
   * Required Variables:
   *
   * UserID: As with calls to all scripts, you must pass through your UserID so that your connection can be
   *         authenticated.
   *
   * PropertyNumber: The PopertyNumber of the property you wish to retrieve further information for. Usually this
   *                 will be taken from the results of a City Availability Search.
   *
   * Optional variables:
   *
   * [Language]: Optional variable to set the language of the results information. Current options are French,
   *             Spanish, German and Italian. The default language is English.
   *
   * Behavior:
   *
   * 1. Post variables to hostelsworld.com API - propertyLocationSearch.php
   * 2. Get xml result from hostelsworld.com API
   * 3. Filter all special character that can cause problems to simplexml module ('&')
   * 4. Return property object
   *
   */

  function PropertyInformation($userID, $propertyNumber, $language = "French")
  {


    log_message('debug', 'Entering HW API Model Property Information Method');

    $file_xml_url = $this->config->item('hostelworld_API_url');
    $file_xml_url.= "PropertyInformation.php?Language=$language&PropertyNumber=$propertyNumber&UserID=$userID";


    $file_xml = $this->get_API_XML_and_Audit($file_xml_url,'PropertyInformation');

    return $this->_validate_api_data($file_xml, "/PropertyInformation/Property", "/SystemMessage", "PropertyInformation");

  }

  /**
   * PropertyReviews
   *
   * Required Variables:
   *
   * UserID: As with calls to all scripts, you must pass through your UserID so that your connection can be
   *         authenticated.
   *
   * PropertyNumber: The PopertyNumber of the property you wish to retrieve further information for. Usually this
   *                 will be taken from the results of a City Availability Search.
   *
   * Behavior:
   *
   * 1. Post variables to hostelsworld.com API - PropertyReviews.php
   * 2. Get xml result from hostelsworld.com API
   * 3. Filter all special character that can cause problems to simplexml module ('&')
   * 4. Return property object
   *
   */
  function PropertyReviews($userID, $propertyNumber)
  {
    $file_xml_url = $this->config->item('hostelworld_API_url');
    $file_xml_url.= "PropertyReviews.php?PropertyNumber=$propertyNumber&UserID=$userID";

    $file_xml = $this->get_API_XML_and_Audit($file_xml_url,'PropertyReviews');

    return $this->_validate_api_data($file_xml, "/PropertyReviews", "/SystemMessage", "PropertyInformation");

  }

  /**
   * PropertyBookingInformation
   *
   * Required Variables:
   *
   * UserID: As with calls to all scripts, you must pass through your UserID so that your connection can be
   * authenticated.
   *
   * PropertyNumber: The PropertyNumber of the property you wish to retrieve booking information for.
   *
   * DateStart: The Start Date for the search. This will be the first date of the guests stay. The format for
   *            DateStart is always YYYY-MM-DD. So for the 20th of April 2003, you would use 2003-04-20.
   *
   * NumNights: The number of nights the guest wishes to stay.
   *
   * Optional variables:
   *
   * [ShowRoomInfo]: ShowRoomInfo is an optional variable that will cause the script to return more detailed
   *                 information about rooms. It is recommended to use this variable if you implement the multiple
   *                 room type booking method (Section 4.4). Valid values are 1 or 0.
   *
   * [Currency]: Currency is an optional variable for this step. If no currency is specified, prices will be shown
   *             in the property’s own currency. If a currency is specified, the bed prices will be converted from
   *             the property’s currency to the specified one at today’s rates*. Currency should be passed to the
   *             script as a three-character ISO currency code. Note: Converted prices should only be used as a
   *             guide for the user. The actual price the user will be charged will be in US Dollars. The user will
   *             be shown the full amount to be charged on their card in US Dollars before they confirm their
   *             booking.
   *
   * [language]: Not in API documentation. But work with French, Spanish, German and Italian!
   *
   * Behavior:
   *
   * 1. Post variables to hostelsworld.com API - propertyLocationSearch.php
   * 2. Get xml result from hostelsworld.com API
   * 3. Filter all special character that can cause problems to simplexml module ('&')
   * 4. Return property object
   *
   * Returns:
   *
   * SimpleXMLElement ...
   *
   */
  function propertyBookingInformation($userID, $propertyNumber, $dateStart, $numNights, $currency = NULL,$language = "French" , $showRoomInfo = 1)
  {

    $file_xml_url = $this->config->item('hostelworld_API_url');
    $file_xml_url.= "PropertyBookingInformation.php";
    $file_xml_url.= "?UserID=".$userID;
    $file_xml_url.= "&PropertyNumber=".$propertyNumber;
    $file_xml_url.= "&DateStart=".$dateStart;
    $file_xml_url.= "&NumNights=".$numNights;
    $file_xml_url.= "&ShowRoomInfo=".$showRoomInfo;
    $file_xml_url.= "&Language=".$language;

    if($currency != NULL)
    {
      $file_xml_url.= "&Currency=$currency";
    }
//    print $file_xml;
    $file_xml = $this->get_API_XML_and_Audit($file_xml_url, 'PropertyBookingInformation');

    return $this->_validate_api_data($file_xml, "/PropertyBookingInformation", "/SystemMessage", "get_property_availability");
  }

  /**
   * propertyBookingRequest
   *
   * Required Variables:
   *
   * UserID: As with calls to all scripts, you must pass through your UserID so that your connection can be
   * authenticated.
   *
   * PropertyNumber: The PropertyNumber of the property you wish to retrieve booking information for.
   *
   * DateStart: The Start Date for the search. This will be the first date of the guests stay. The format for
   *            DateStart is always YYYY-MM-DD. So for the 20th of April 2003, you would use 2003-04-20.
   *
   * NumNights: The number of nights the guest wishes to stay.
   *
   * RoomPreference: This is the code for the type of room the guest wishes to stay in. When
   *                 PropertyBookingInformation.php returns a list of available room types, each room type will
   *                 have the variable roomTypeCode with it. These codes are what you need to be pass as
   *                 RoomPreference.
   *
   * Persons: The number of people that guest wishes to book accommodation for. This must be a number between 1 and
   *          8. 8 is the maximum amount of people that can be booked at once, although some properties have a lower
   *          limit. This will be indicated if it is exceeded.

   * Optional variables:
   *
   * [RoomPreference1],[RoomPreference2],[RoomPreference3]… : These variables may be passed instead of
   *                    RoomPreference, to facilitate multi-room type bookings. Each one should be set to a
   *                    relevant roomTypeCode from a PropertyBookingInformation.php result. Each of these variables
   *                    must have a corresponding Persons1, Persons2 etc variable set as well. A maximum of 8 room
   *                    types can be chosen for one booking.
   *
   * [Persons1],[Persons2],[Persons3]… : The corresponding variables for RoomPreference1 etc to specify how many
   *                                     people should be booked into each room type.
   *
   * [Currency]: If provided to this script, it will return all bill details in USD and the provided currency.
   *             If not provided, the currency of the property will be used instead.
   *
   * [BSID]: BSID is the session id for the current customer. It is created and returned the first time this script
   *         is called by a customer. If the same customer calls this script repeatedly, BSID should always be
   *         passed for smoother functioning.
   *
   * [SettleCurrency]: SettleCurrency is another optional variable for this step. It should be either USD, EUR or
   *                   GBP. If it is provided, the prices per room, beds total, booking fee, deposit, bill total
   *                   and amount due will all be returned in this currency. If this variable is not provided,
   *                   these additional fields will not be returned.
   *
   * [language]: Not in API documentation. But work with French, Spanish, German and Italian!
   *
   * Behavior:
   *
   * 1. Post variables to hostelsworld.com API - PropertyBookingRequest.php
   * 2. Get xml result from hostelsworld.com API
   * 3. Filter all special character that can cause problems to simplexml module ('&')
   * 4. Return PropertyBookingRequest object
   *
   * @return SimpleXMLElement object PropertyBookingRequest
   *
   */

  function propertyBookingRequest($userID,
                                  $propertyNumber,
                                  $dateStart,
                                  $numNights,
                                  $roomPreferences,
                                  $nbPersons,
                                  $settleCurrency = NULL,
                                  $bsid = NULL,
                                  $currency = NULL,
                                  $language = "")
  {

    $file_xml_url = $this->config->item('hostelworld_API_url');
    $file_xml_url.= "PropertyBookingRequest.php";
    $file_xml_url.= "?UserID=".$userID;
    $file_xml_url.= "&PropertyNumber=".$propertyNumber;
    $file_xml_url.= "&DateStart=".$dateStart;
    $file_xml_url.= "&NumNights=".$numNights;
    $file_xml_url.= "&Language=".$language;

    if(is_array($roomPreferences)&&is_array($nbPersons))
    {
      //number of room type
      $i=0;

      //number of room type with at least 1 guest
      $r=1;

      foreach($roomPreferences as $roomTypeCode)
      {
        if($nbPersons[$i] > 0)
        {
          $file_xml_url.= "&RoomPreference$r=".urlencode($roomTypeCode);
          $file_xml_url.= "&Persons$r=".$nbPersons[$i];
          $r++;
        }
        $i++;
      }

    }
    else
    {
      $file_xml_url.= "&RoomPreference=".$roomPreferences;
      $file_xml_url.= "&Persons=".$nbPersons;
    }


    if($settleCurrency != NULL)
    {
      $file_xml_url.= "&SettleCurrency=$settleCurrency";
    }

    if($bsid != NULL)
    {
      $file_xml_url.= "&BSID=$bsid";
    }

    if($currency != NULL)
    {
      $file_xml_url.= "&Currency=$currency";
    }
    $file_xml = $this->get_API_XML_and_Audit($file_xml_url,'PropertyBookingRequest');

    return $this->_validate_api_data($file_xml, "/PropertyBookingRequest", "/SystemMessage", "propertyBookingRequest");

  }


   /**
    * bookingConfirmationRequest
    *
    * @variables
    *
    * @access public
    *
    * @param UserID: As with calls to all scripts, you must pass through your UserID so that your connection can be
    * authenticated.
    *
    * @param BSID: This is the session id provided by the previous script.
    *
    * @param FirstName: The first name of the customer.
    *
    * @param LastName: The last name / surname of the customer.
    *
    * @param Nationality: The nationality of the customer. This should be the name of the country, i.e. ‘Canada’,
    *              not ‘Canadian’.
    *
    * @param Gender: Gender of the customer, ‘Male’ or ‘Female’.
    *
    * @param EmailAddress: Email address of the customer.
    *
    * @param PhoneNumber: Phone number of the customer, including country code.
    *
    * @param ArrivalTime: The hour of arrival of the customer at the property. Provide in 24-hour format, with values of
    *              0 to 23.
    *
    * @param CCName: The name as it appears on the credit card the customer is using.
    *
    * @param CCNumber: The number of the credit card the customer is using.
    *
    * @param CCExpiry: The expiry date of the credit card the customer is using, in MM/YY format.
    *
    * @param CCType: The type of credit card the customer is using. The list of card types that can be used are returned in
    *         section 4.4. The value for CCType must be one of these values.
    *
    * @param [SettleCurrency]: The transaction on the credit card the customer is using will be carried out in this
    *                   currency. The currencies that can be used are: [EUR,GBP,USD]. The value for SettleCurrency
    *                   must be one of these values.
    *                   Please note: If the SettleCurrency provided is not supported by the type of the credit card
    *                   used then the default currency will be automatically used to process the transaction. The
    *                   warning message will be provided in field <ChargedCurrencyWarning>
    *
    * @param [Currency]: If provided, bill details will be shown in this currency instead of the property’s currency.
    *             Details will also provided in USD.
    *
    * @param [IssueNO]: Some debit cards require an issue number to be provided for processing. This will be indicated by
    *            the <CardInfo> element returned by the previous step.
    *
    * @param [CCValidFrom]: Some debit cards require a ‘valid from’ date to be provided in MM/YY format. This will be
    * indicated by the <CardInfo> element returned by the previous step.
    *
    * @param [CVV]: Some cards require a ‘CVV’ security code to be provided in 3 or 4 digits format. Parameter is optional
    *        and is not being verified if left out or submitted empty.
    *
    * @param [TestMode]: If TestMode is set to 1 a real booking will not be made. Instead a fake test booking will be made,
    *             allowing developers to fully test their integration before going live.
    *                *
    * @return SimpleXMLElement object PropertyBookingRequest
    *
    * Post request example:
    *
    * POST /bookhostels/xmlapi/SubmitCustomerDetails.php HTTPS/1.0
    * Host: secure.hostelworld.com
    * Content-Type: application/x-www-form-urlencoded
    * Content-Length: 265
    *
    * FirstName=test&LastName=test&Nationality=Ireland&Gender=Male&EmailAddress=test@hostelworld.com&PhoneNumber=none&ArrivalTime=11&CCName=test+test&CCNumber=xxxxxxxxxxxxxxxx&CCExpiry=04/04&CCType=mastercard&BSID=e06cc5f441805f43371c189f93734fef&UserID=testaffiliate.com&SettleCurrency=GBP
    *
    */

  function bookingConfirmationRequest(
                              $userID,
                              $BSID,
                              $FirstName,
                              $LastName,
                              $Nationality,
                              $Gender,
                              $EmailAddress,
                              $PhoneNumber,
                              $ArrivalTime,
                              $CCName,
                              $CCNumber,
                              $CCExpiry,
                              $CCType,
                              $SettleCurrency = NULL,
                              $Currency = NULL,
                              $IssueNO = NULL,
                              $CCValidFrom = NULL,
                              $CVV = NULL,
                              $paresponse = NULL,
                              $cookie = NULL,
                              $transactionId = NULL,
                              $NewSessionID = NULL,
                              $IPAddress = NULL,
                              $UserSessionID = NULL,
                              $TestMode = NULL)
  {

    $url_api = $this->config->item('hostelworld_API_url_secure_booking');
    $url_api.= "SubmitCustomerDetails.php";

    $data = $this->build_confirmation_data( $userID,
                                            $BSID,
                                            $FirstName,
                                            $LastName,
                                            $Nationality,
                                            $Gender,
                                            $EmailAddress,
                                            $PhoneNumber,
                                            $ArrivalTime,
                                            $CCName,
                                            $CCNumber,
                                            $CCExpiry,
                                            $CCType,
                                            $SettleCurrency,
                                            $Currency,
                                            $IssueNO,
                                            $CCValidFrom,
                                            $CVV,
                                            $paresponse,
                                            $cookie,
                                            $transactionId,
                                            $NewSessionID,
                                            $IPAddress,
                                            $UserSessionID,
                                            $TestMode);

    $file_xml = $this->do_post_request($url_api,$data);

    $return = $this->_validate_api_data($file_xml, "/SubmitCustomerDetails", "/SystemMessage", "bookingConfirmationRequest");

    //If card issuer declined, try with another currency
    if(($return[1]!=FALSE)
        && !empty($return[1][0]->Error->detail)
        && strcasecmp(substr($return[1][0]->Error->detail,0,71),"Declined. Sorry this transaction has been declined by your card issuer.")==0)
    {
      $firstCurrency = $SettleCurrency;
      if($firstCurrency == "EUR")
      {
        $SettleCurrency = "GBP";
      }
      elseif($firstCurrency == "GBP")
      {
        $SettleCurrency = "EUR";
      }
      elseif($firstCurrency == "USD")
      {
        $SettleCurrency = "EUR";
      }
      $data = $this->build_confirmation_data( $userID,
                                              $BSID,
                                              $FirstName,
                                              $LastName,
                                              $Nationality,
                                              $Gender,
                                              $EmailAddress,
                                              $PhoneNumber,
                                              $ArrivalTime,
                                              $CCName,
                                              $CCNumber,
                                              $CCExpiry,
                                              $CCType,
                                              $SettleCurrency,
                                              $Currency,
                                              $IssueNO,
                                              $CCValidFrom,
                                              $CVV,
                                              $paresponse,
                                              $cookie,
                                              $transactionId,
                                              $NewSessionID,
                                              $IPAddress,
                                              $UserSessionID,
                                              $TestMode);

      $file_xml = $this->do_post_request($url_api,$data);
      $second_return = $this->_validate_api_data($file_xml, "/SubmitCustomerDetails", "/SystemMessage", "bookingConfirmationRequest");

      if($second_return[0] == FALSE)
      {
        $second_return[1][0]->ChargedCurrencyWarning->note = "Currency used to charge your card was automatically changed to match the card type ";
        $second_return[1][0]->ChargedCurrencyWarning->oldCurrency = $firstCurrency;
        $second_return[1][0]->ChargedCurrencyWarning->newCurrency = $SettleCurrency;

        return $second_return;
      }
      //If declined again try in the third currency
      elseif(($second_return[1]!=FALSE)
        && !empty($return[1][0]->Error->detail)
        && strcasecmp(substr($second_return[1][0]->Error->detail,0,71),"Declined. Sorry this transaction has been declined by your card issuer.")==0)
      {
        if($firstCurrency == "EUR")
        {
          $SettleCurrency = "USD";
        }
        elseif($firstCurrency == "GBP")
        {
          $SettleCurrency = "USD";
        }
        elseif($firstCurrency == "USD")
        {
          $SettleCurrency = "GBP";
        }
        $data = $this->build_confirmation_data( $userID,
                                                $BSID,
                                                $FirstName,
                                                $LastName,
                                                $Nationality,
                                                $Gender,
                                                $EmailAddress,
                                                $PhoneNumber,
                                                $ArrivalTime,
                                                $CCName,
                                                $CCNumber,
                                                $CCExpiry,
                                                $CCType,
                                                $SettleCurrency,
                                                $Currency,
                                                $IssueNO,
                                                $CCValidFrom,
                                                $CVV,
                                                $paresponse,
                                                $cookie,
                                                $transactionId,
                                                $NewSessionID,
                                                $IPAddress,
                                                $UserSessionID,
                                                $TestMode);

        $file_xml = $this->do_post_request($url_api,$data);
        $second_return = array();
        $second_return = $this->_validate_api_data($file_xml, "/SubmitCustomerDetails", "/SystemMessage", "bookingConfirmationRequest");

        if($second_return[0] == FALSE)
        {
          $second_return[1][0]->ChargedCurrencyWarning->note = "Currency used to charge your card was automatically changed to match the card type ";
          $second_return[1][0]->ChargedCurrencyWarning->oldCurrency = $firstCurrency;
          $second_return[1][0]->ChargedCurrencyWarning->newCurrency = $SettleCurrency;

          return $second_return;
        }
      }

    }
    return $return;

  }
  function build_confirmation_data(
                              $userID,
                              $BSID,
                              $FirstName,
                              $LastName,
                              $Nationality,
                              $Gender,
                              $EmailAddress,
                              $PhoneNumber,
                              $ArrivalTime,
                              $CCName,
                              $CCNumber,
                              $CCExpiry,
                              $CCType,
                              $SettleCurrency = NULL,
                              $Currency = NULL,
                              $IssueNO = NULL,
                              $CCValidFrom = NULL,
                              $CVV = NULL,
                              $paresponse = NULL,
                              $cookie = NULL,
                              $transactionId = NULL,
                              $NewSessionID = NULL,
                              $IPAddress = NULL,
                              $UserSessionID = NULL,
                              $TestMode = NULL)
  {
    $data = array ();
    $data['TestMode'] = $TestMode;
    $data['NoConfirmationEmail'] = $this->config->item('HW_no_confirmation_email');
    $data['UserID'] = $userID;
    $data['BSID'] = $BSID;
    $data['FirstName'] = $FirstName;
    $data['LastName'] = $LastName;
    $data['Nationality'] = $Nationality;
    $data['Gender'] = $Gender;
    $data['EmailAddress'] = $EmailAddress;
    $data['PhoneNumber'] = $PhoneNumber;
    $data['ArrivalTime'] = $ArrivalTime;
    $data['CCName'] = $CCName;
    $data['CCNumber'] = $CCNumber;
    $data['CCExpiry'] = $CCExpiry;
    $data['CCType'] = $CCType;

    if(!is_null($TestMode))
    {
      $data['TestMode'] = $TestMode;
    }

    if($SettleCurrency != NULL)
    {
      $data['SettleCurrency'] = $SettleCurrency;
    }

    if($Currency != NULL)
    {
      $data['Currency'] = $Currency;
    }

    if($IssueNO != NULL)
    {
      $data['IssueNO'] = $IssueNO;
    }

    if($CCValidFrom != NULL)
    {
      $data['CCValidFrom'] = $CCValidFrom;
    }

    if($CVV != NULL)
    {
      $data['CVV'] = $CVV;
    }

    //handling of secure 3D request parameters
    if($paresponse != NULL)
    {
      $data['paresponse'] = $paresponse;
    }
    if($cookie != NULL)
    {
      $data['cookie'] = $cookie;
    }
    if($transactionId != NULL)
    {
      $data['transactionId'] = $transactionId;
    }
    if($NewSessionID != NULL)
    {
      $data['NewSessionID'] = $NewSessionID;
    }
    if($IPAddress != NULL)
    {
      $data['IPAddress'] = $IPAddress;
    }
    if($UserSessionID != NULL)
    {
      $data['UserSessionID'] = $UserSessionID;
    }
    return $data;
  }
  function count_numnights($roomsdetails)
  {
    $numNights_calculated = 0;
    $night1 = 0;
    $night2 = 0;

    $dateStart = $roomsdetails[0]->date;

    foreach($roomsdetails as $room)
    {
      $night1 = strtotime($room->date);

      //if night1 prior of dateStart
      if($night1 < strtotime($dateStart))
      {
        $dateStart = $room->date;
      }

      if ($night1 != $night2)
      {
        $numNights_calculated++;
      }
      $night2 =  strtotime($room->date);
    }
    return $numNights_calculated;
  }

  /**
   * get_API_XML
   *
   */
  function get_API_XML_and_Audit($xml_file_url, $auditLogStatement)
  {
	$request_time= microtime(true);
    $file_xml = $this->get_API_XML($xml_file_url);
    $response_time=microtime(true);
	$total_time = ($response_time - $request_time) * 1000;
	$total_time = floor($total_time);
	$total_time =  $total_time." ms ";
    $this->custom_log->log("audit", 'HW API '.$auditLogStatement.' '.$total_time);

    return $file_xml;
  }

  /**
   * get_API_XML
   *
   */
  function get_API_XML($xml_file_url)
  {
    //TODO XML check charsets errors Alexandrie-Montréal
    log_message('debug', "Reading $xml_file_url");
  	try
  	{
  		$xml_file = @file_get_contents($xml_file_url);

      if ($xml_file  === false)
      {
       log_message('error', "get_API_XML() problem reading $xml_file_url");
        throw new Exception("Problem reading $xml_file_url");
      } else {
         log_message('debug', "get_API_XML() $xml_file_url result : $xml_file");
      }
  	}
    catch(Exception $e)
    {
      log_message('error', 'get_API_XML(): '.$e->getMessage());
    }

    $this->_log_xml_debug($xml_file);

    return $xml_file;
  }


  /**
   * do_post_request
   *
   *
   */

  function do_post_request($url, $data, $optional_headers = null)
  {

    $data = http_build_query($data);

     $params = array('http' => array(
                  'method' => 'POST',
                  'header' => 'Content-type: application/x-www-form-urlencoded',
                  'content' => $data
               ));
     if ($optional_headers !== null) {
        $params['http']['header'] = $optional_headers;
     }
//     print_r($params);
     $response = false;
     try
     {
	     $ctx = stream_context_create($params);
	     $fp = @fopen($url, 'rb', false, $ctx);
	     if (!$fp) {

	        throw new Exception("Problem with $url");
	     }
	     else
	     {
         $response = @stream_get_contents($fp);
         if ($response === false) {
            throw new Exception("Problem reading data from $url");
         }
	     }
      }
	    catch(Exception $e)
	    {
	      log_message('error', 'do_post_request() error:'.$e->getMessage());
	    }

	    $this->_log_xml_debug($response);

     return $response;
  }

  /**
   * Api handle the following langages:
   * French, Spanish, German and Italian. The default language is English.
   */
  function lang_code_convert($lang_code)
  {
    switch(strtolower($lang_code))
    {
      case strtolower("fr"):
        return "French";
      case strtolower("es"):
        return "Spanish";
      case strtolower("de"):
        return "German";
      case strtolower("it"):
        return "Italian";
      case strtolower("en"):
        return "English";
      default:
        return "English";
    }

    return "English";
  }
  /**
   * validate_api_data
   *
   * @params $xml_file: an xml file
   * @params $xml_ok_path: xml expected path Ex.: /PropertyLocationSearch/Property
   * @params $xml_error_path: xml error path Ex.: /SystemMessage
   * @params $infunction: function name calling for error log
   *
   * @return array(error, xobject)
   */

  function _validate_api_data($xml_file, $xml_ok_path, $xml_error_path, $infunction)
  {
    $xobject = false;
    $error = true;
    $errorMsg = "";

    if($xml_file === false)
    {
      $errorMsg = _('Serveur indisponible en ce moment.');
      $xobject = $errorMsg;
      log_message('error', $infunction.': '.$errorMsg);
    }
    else
    {
      try
      {
        $xml = simplexml_load_string($xml_file);
      }
      catch(Exception $e)
      {
        log_message('error', $infunction.': simplexml_load_string: '.$e->getMessage());
      }

      if(!is_object($xml))
      {
        $errorMsg = _('Réponse inattendue du serveur.');
        $xobject = $errorMsg;
        log_message('error', $infunction.': '."Invalid API data. -> ".current_url());
      }
      else
      {
        $xobject = $xml->xpath($xml_ok_path);

        if($xobject == false)
        {
          $xobject = $xml->xpath($xml_error_path);
        }
        else
        {
          $error = false;
          //$properties = $properties[0];
        }
      }
    }
    return array($error,$xobject);
  }

  /**
   *
   */
  function _log_xml_debug($xml_file)
  {
    //if debug messages are enabled copy API XML to file
    if(($this->_threshold >= 2)&&($xml_file!==false))
    {
      log_message('debug', 'XML API return. See last_api_result.xml');

      try
      {
        $xml_api_file = $this->log_path.'last_api_result.xml';
        $fp = fopen($xml_api_file, 'a');
        if (!$fp) {

            throw new Exception("Problem with opening of $xml_api_file");
        }
        else
        {
          $fwrite = fwrite($fp, $xml_file);
          if ($fwrite === false) {
              throw new Exception("Problem writing data to $xml_api_file");
           }
          fclose($fp);
        }
      }
      catch(Exception $e)
      {
        log_message('error', 'API_XML_file:'.$e->getMessage());
      }
    }
  }
}?>
