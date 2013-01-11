<?php

class Ggeocodeapi extends CI_Model
{
  var $key = "";
  var $base_url = "http://maps.google.com/maps/geo?output=xml";// . "&key=" . KEY;
  var $maxtries = 5;
  
  
  function Ggeocodeapi()
  {
    parent::__construct();
  }
  
  function geocode($address)
  {
    $geocodes = NULL;
    $tries = 0;
    $geocode_pending = true;
    
    while($geocode_pending == true)
    {
      if($tries > $this->maxtries) break;
      
      $request_url = $this->base_url . "&q=" . urlencode($address);
      $xml = simplexml_load_file($request_url);
      
      if($xml === false)
      {
        $tries++;
        $geocodes->error = "failed to load google geocode API URL";
        log_message("debug","Geocoding error: url not loading -> ".$request_url);
        continue;
      }
      $status = $xml->Response->Status->code;
      
      if (strcmp($status, "200") == 0)
      {
          // Successful geocode
          $geocode_pending = false;
          $coordinates = $xml->Response->Placemark->Point->coordinates;
          $coordinatesSplit = split(",", $coordinates);
          // Format: Longitude, Latitude, Altitude
          $lat = $coordinatesSplit[1];
          $lng = $coordinatesSplit[0];
//        print "<pre>";
//        print_r($xml);
//        print "</pre>";
          $countryFound = $xml->Response->Placemark->AddressDetails->Country->CountryName;
          $countryCode  = $xml->Response->Placemark->AddressDetails->Country->CountryNameCode;
          
          $geocodes->lat = $lat;
          $geocodes->lng = $lng;
          $geocodes->country = (string)$countryFound;
          $geocodes->countryCode = (string)$countryCode;
          if(isset($geocodes->error)) unset($geocodes->error);
          
          return $geocodes;
          
      }
      elseif (strcmp($status, "620") == 0)
      {
          // sent geocodes too fast
          sleep(3);
      }
      else
      {
        // failure to geocode
        $geocode_pending = false;
        $geocodes->error = "failed to geocode address $address";
      }
      $tries++;
      
    }
    
    return $geocodes;
  }
    
    
}
?>