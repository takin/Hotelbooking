<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Http_request
 *
 * Http_request library for code igniter
 *
 * @package   Http_request
 * @author    Louis-Michel Raynauld
 * @version   0.1
 * @license   Commercial
 */
class Http_request {

/**
   * post_request
   * 
   * return false when no response
   */

  function post_request($url, $data, $string_data = false, $optional_headers = null)
  {
    
    if($string_data == false)
    {
      $data = http_build_query($data);
    }
    
     $params = array('http' => array(
                  'method' => 'POST',
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
        log_message('error', 'post_request() error:'.$e->getMessage());
      }
      
     return $response;
  }
}

?>