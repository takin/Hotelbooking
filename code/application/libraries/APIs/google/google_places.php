<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Google
 *
 * Google parent lib for code igniter
 *
 * @package   Google
 * @author    Louis-Michel Raynauld
 * @version   0.1
 * @license   Commercial
 * @reference https://developers.google.com/maps/documentation/places/
 */

// make it possible to extend custom lib
load_class('APIs/google/google', false);

class Google_places extends Google
{

  private $api_path = "maps/api/place/search/";
  private $output = "json"; // or xml

  private $types = array();

  public function __construct()
  {
    parent::__construct();
    $this->CI->load->library('Rest');

    $this->types = array( 'point_of_interest',
                          'train_station',
                          'airport',
                          'amusement_park',
                          'aquarium',
                          'art_gallery',
                          'liquor_store',
                          'museum',
                          'establishment',  //Eiffel tower is establishment only
                          'night_club',
                          'shopping_mall',
                          'stadium',
                          'subway_station',
                          'taxi_stand',
                          'university',
                          'zoo',
                          'park',
                          'natural_feature');
  }

  public function get_request_url()
  {
    return $this->api_path.$this->output;
  }
  /*
   	key — Your application's API key. This key identifies your application for purposes of quota management and so that Places added from your application are made immediately available to your app. Visit the APIs Console to create an API Project and obtain your key.
    location — The latitude/longitude around which to retrieve Place information. This must be specified as latitude,longitude.
    radius — Defines the distance (in meters) within which to return Place results. The maximum allowed radius is 50 000 meters. Note that radius must not be included if rankby=distance (described under Optional parameters below) is specified.
    sensor — Indicates whether or not the Place request came from a device using a location sensor (e.g. a GPS) to determine the location sent in this request. This value must be either true or false.
   */
  public function search($geo_lat, $geo_lng, $radius_in_m = 500, $sensor = "false", $rankby = null, $keyword = null, $include_types = true)
  {

    //Required parameters
    $params = array("key" => $this->api_key,
    								"language" => "en",
                    "location"   =>$geo_lat.",".$geo_lng);

    if(strcasecmp($sensor,"true")==0)
    {
      $params['sensor'] = "true";
    }
    else
    {
      $params['sensor'] = "false";
    }
    //Rankby special parameter
    // If rankby is prominence radius should not be included
    if(!empty($rankby) && (strcasecmp($rankby,"distance")==0))
    {
      $params['rankby'] = "distance";
    }
    else
    {
      $params['rankby'] = "prominence";
      $params['radius'] = $radius_in_m; //in meters
    }

    //Optional paramters
    if(!empty($this->types) && ($include_types === true))
    {
      $params["types"] = implode("|",$this->types);
    }

    if(!empty($keyword))
    {
      $params["keyword"] = $keyword;
    }

    $this->CI->rest->initialize(array('server' => $this->api_server));
    $return = $this->CI->rest->get($this->get_request_url(), $params,"json");

    if(empty($return)||empty($return->status))
    {
      throw new Exception('Google places API error: no answer - '.print_r($this->CI->curl->info,true));
    }
    elseif((strcasecmp((string)$return->status,'OK')!=0) &&
           (strcasecmp((string)$return->status,'ZERO_RESULTS')!=0))
    {
      throw new Exception('Google places API error: '.$return->status.' - '.print_r($this->CI->curl->info,true));
    }

    return $return;
  }
}