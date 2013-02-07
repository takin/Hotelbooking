<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Clickatell Class
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	  Libraries
 * @author        	Louis-Michel Raynauld
 *
 */
class Clickatell {

  const SMS_COVERAGE_TABLE = 'sms_coverage';

  var $wsdl = "http://api.clickatell.com/soap/webservice.php?WSDL";

  //International account
  var $user = "mcwebmanagement";
  var $pass = "FRANCE2008";
  var $api_id = 3324882;

  //USA account
  var $usa_user   = "mcwebUSA";
  var $usa_pass   = "FRANCE2008";
  var $usa_api_id = 3328392;

  var $clickatell_api = NULL;

  var $session_id     = NULL;

  var $zone_coverage           = NULL;
  var $previous_zone_coverage  = NULL;

  var $CI;

  function Clickatell()
  {
    $this->clickatell_api = new SoapClient($this->wsdl);
  }

  function auth()
  {
    //If session already established in the same zone coverage account
    if((!empty($this->session_id)) && ($this->zone_coverage == $this->previous_zone_coverage))
    {
      //If ping succeed skip authentication else proceed with auth
      if($this->ping())
      {
        return TRUE;
      }
    }

    if($this->zone_coverage == 'usa')
    {
      $response = $this->clickatell_api->auth($this->usa_api_id, $this->usa_user, $this->usa_pass);

    }
    else
    {
      $response = $this->clickatell_api->auth($this->api_id, $this->user, $this->pass);
    }

    $this->previous_zone_coverage = $this->zone_coverage;

    if((stristr($response, 'OK: ')) === FALSE)
    {
       $this->session_id = NULL;
       throw new Exception("Authentication failed","001");
    }
    else
    {
      $this->session_id = trim($response,"OK: ");
    }
    return TRUE;

  }

  function ping()
  {
    if(!empty($this->session_id))
    {
      $response = $this->clickatell_api->ping($this->session_id);
      if((stristr($response, 'OK: ')) === FALSE)
      {
        return FALSE;
      }
      return TRUE;
    }
  }

  function prepare_number($number)
  {
    $number = str_replace(" ", "", $number);
    $number = str_replace("-", "", $number);
    $number = ltrim($number,"+");
    if(substr($number,0,2) == '00')
    {
      $number = substr($number,2);
    }
    return $number;
  }
  function send_single_sms($to, $text)
  {
    if(!is_array($to))
    {
      $to = array(0 => $to);
    }

    $to[0] = $this->prepare_number($to[0]);

    $this->detect_coverage($to[0]);
    if(empty($this->zone_coverage))
    {
      throw new Exception("Number out of clickatell coverage -> ".$to[0],114);
      return NULL;
    }

    $this->auth();

    //$to need to be an array
    $responses = $this->clickatell_api->sendmsg($this->session_id, NULL, NULL, NULL, $to, NULL, $text);

    if((stristr($responses[0], 'ID: ')) === FALSE)
    {
      $error_number = substr($responses[0], 5, 3);
      throw new Exception($responses[0],$error_number);
    }

    return trim($responses[0],"ID: ");
  }

  //string routeCoverage(string $session_id, int $api_id, string $user, string $password, string $msisdn)
  function route_coverage($number, $zone = 'int')
  {
    $this->zone_coverage = $zone;
    $this->auth();

    $response = $this->clickatell_api->routeCoverage($this->session_id, NULL, NULL, NULL, $number);

    if((stristr($response, 'OK: ')) === FALSE)
    {
      return FALSE;
    }
    else
    {
      return TRUE;
    }
  }

  function detect_coverage($number)
  {
    //If coverage available in DB return it
    $cached_coverage = $this->get_prefix_coverage($number);
    if(!empty($cached_coverage))
    {
      $this->zone_coverage = $cached_coverage;
      return $cached_coverage;
    }

    //Try clickatell route coverage USA and then INT
    if($this->route_coverage($number,'usa'))
    {
      $this->zone_coverage = 'usa';
      $this->save_prefix_coverage($number, $this->zone_coverage);
      return 'usa';
    }

    if($this->route_coverage($number,'int'))
    {
      $this->zone_coverage = 'int';
      $this->save_prefix_coverage($number, $this->zone_coverage);
      return 'int';
    }

    $this->zone_coverage = NULL;
    return NULL;
  }

  function save_prefix_coverage($number, $zone)
  {
    $this->CI =& get_instance();

    $prefix = substr($number, 0, 4);

    $data = array(
       'prefix' => $prefix,
       'zone' => $zone
    );

    $cached_coverage = $this->get_prefix_coverage($prefix);
    if(!empty($cached_coverage))
    {
      $this->CI->db->where('prefix',$prefix);
      return $this->CI->db->update(self::SMS_COVERAGE_TABLE, $data);
    }
    else
    {
      return $this->CI->db->insert(self::SMS_COVERAGE_TABLE, $data);
    }
  }

  function get_prefix_coverage($number)
  {
    $this->CI =& get_instance();

    $prefix = substr($number, 0, 4);

    $this->CI->db->where('prefix',$prefix);
    $query = $this->CI->db->get(self::SMS_COVERAGE_TABLE, 1);

    if ($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->zone;
    }
    return NULL;
  }
}
?>
