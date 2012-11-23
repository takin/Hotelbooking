<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Urban_mapping
 *
 * Urban_mapping Rest API library for code igniter
 *
 * @author    Louis-Michel Raynauld
 * @version   0.1
 * @license   Commercial
 * $reference: http://developer.urbanmapping.com/docs/mapfluence/js/2.0/guides
 *
 *	DATE: Requests for periodic data must explicitly set the date/time for the request using the date parameter.
 *				It is useful to think of the date parameter as setting the “as of” date for the geometry.
 */
class Urban_mapping
{

  private $CI = NULL;

  private $api_url = 'http://query.mapfluence.com/';
  private $api_ver = '2.0';
  private $api_key = '3e582082263ec602d607b16a3d1b21fb';

  private $module_spatial = "spatialquery.json";

  private $as_of_date = NULL;

  public function __construct()
  {
      $this->CI =& get_instance();
      $this->CI->load->library('Json');
      $this->CI->load->library('Rest');

      $this->as_of_date = gmdate('Y-m-d\TH:i:s\Z');
  }

  private function get_root_url()
  {
    return $this->api_ver.'/'.$this->api_key.'/';
  }

  /*
   * spatial_query
   *
   * select: http://developer.urbanmapping.com/docs/mapfluence/js/2.0/guides/select/#guide-select-statements
   *
   * $order_by: By default, the sort order is ascending; prefix a column name with - to sort in descending order
   */
  private function spatial_query($select,$from,$where,$order_by = "")
  {
    $params = array("select" => $select,
                    "from"   => $from,
                    "where"  => $where,
                    "date"   => $this->as_of_date);

    if(!empty($order_by))
    {
      $params["order_by"] = $order_by;
    }

    $this->CI->rest->initialize(array('server' => $this->api_url));

    $this->CI->rest->option('cookiesession', FALSE);
    $return = $this->CI->rest->get($this->get_root_url().$this->module_spatial, $params,"json");

    if($return === false)
    {
      throw new Exception('Urban Mapping API spatial query error: no answer - '.print_r($this->CI->curl->info,true));
    }
    elseif(!empty($return->error))
    {
      throw new Exception('Urban Mapping API spatial query error: '.$return->error.' - '.$return->message);
    }
    elseif(!isset($return->features))
    {
      throw new Exception('Urban Mapping API spatial query error: unknown return error - '.print_r($this->CI->curl->info,true));
    }

    return $return;

  }
  public function get_districts_of_geolocation($geo_lat,$geo_lng,$range = 0.01)
  {

    //select: one or more of the following are permitted
    //        id,centroid,name,area,country,admin1,geometryTable,start,end,bounds,geometry
    return $this->fix_charset($this->spatial_query('id,name,area,country,admin1',
    										 'umi.neighborhoods.geometry',
                         'intersects(range('.$range.'km,{"type":"Point","coordinates":['.$geo_lng.','.$geo_lat.']}))',
                         'area'));
  }

  /*
   * fix charset
   *
   * it seems that mysql encode an already utf8 character
   *
   * This ensure all encoding are the same and mysql can behave correctly
   *
   *  Not sure if this is a long term solution though.
   */
  private function fix_charset($response)
  {
    if(!empty($response->features))
    {
      foreach($response->features as $feature)
      {
        $feature->properties->name = utf8_decode($feature->properties->name);
      }
    }
    return $response;
  }
}
?>