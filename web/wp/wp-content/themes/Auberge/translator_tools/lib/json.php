<?php

/**
 * JSON
 *
 * Json library
 *
 * @package   Json
 * @author    Louis-Michel Raynauld
 * @version   0.1
 */
class Json
{
  /*
   * Function that integrate error handling for json decode
   */
  function JDecode($json, $toAssoc = false)
  {
      $result = json_decode($json, $toAssoc);
      if(function_exists("json_last_error"))
      {
        switch(json_last_error())
        {
            case JSON_ERROR_DEPTH:
                $error =  ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $error = ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_NONE:
            default:
                $error = '';                    
        }
        if (!empty($error))
            throw new Exception('JSON Error: '.$error);   
      }
      else
      {
        if(is_null($result))
        {
          throw new Exception('JSON Error: JSON decode failed');
        }
      }     
      
      return $result;
  }

}

?>