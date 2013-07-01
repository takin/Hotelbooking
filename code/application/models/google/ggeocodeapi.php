<?php

class Ggeocodeapi extends CI_Model
{
  function Ggeocodeapi()
  {
    parent::__construct();
  }

  function geocode($address)
  {
    $geocodes = new stdClass();

    $base_url = "https://maps.googleapis.com/maps/api/geocode/xml?sensor=false&address=";

    $url = $base_url . urlencode($address);

    $xml = simplexml_load_file($url);;

    if ($xml === false) {

        $geocodes->error = "failed to load google geocode API URL";
        log_message("debug","Geocoding error: url not loading, url $url, status $status");

    } else {

      $status = $xml->status;

      if (strcmp($status, "OK") == 0) {

        // Successful geocode
        $coordinates = $xml->result->geometry->location;
        $geocodes->lat = $coordinates->lat;
        $geocodes->lng = $coordinates->lng;

      } else {

        $geocodes->error = "failed to geocode address $address, url $url, status $status";
      }

    }

    return $geocodes;
 }

}
?>