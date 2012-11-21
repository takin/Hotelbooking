<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Custom_log
 *
 * Custom_log library for code igniter
 *
 * @package   Custom_log
 * @author    Louis-Michel Raynauld
 * @version   0.1
 * @license   Commercial
 */
class Custom_log 
{
  var $log_path;
  
  var $freq;
  /**
   * Constructor
   *
   * @access  public
   */
  function Custom_log()
  {
    $config =& get_config();
    $this->log_path = ($config['log_path'] != '') ? $config['log_path'] : BASEPATH.'logs/';
    $this->set_freq();
    
    log_message('debug', "Custom log Class Initialized");
  }
  
  function set_freq($file_date_tag = "Y-m")
  {
    switch($file_date_tag)
    {
      case "Y-m-d":
        
        break;
      case "Y-m":
        
        break;
      case "Y":
        
        break;
      default:
        $file_date_tag = "Y-m";        
    }
    $this->freq = $file_date_tag;
  }
  
  function log($log_name,$log_line)
  { 
    try
    {
      $log_file = $this->log_path."$log_name-".date($this->freq).".php";
      $fp = fopen($log_file, 'a');
      if (!$fp) {
         
          throw new Exception("Problem with opening of $log_file");
      }
      else
      {
        $fwrite = fwrite($fp, date("Y-m-d h:i:s A").": ".$log_line."\n");
        if ($fwrite === false) {
            throw new Exception("Problem writing data to $log_file");
         }
        fclose($fp);
      }
    }
    catch(Exception $e)
    {
      log_message('error', 'log_file:'.$e->getMessage());
    }
  }
}

?>