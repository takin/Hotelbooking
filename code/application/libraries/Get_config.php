<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Get_config
 *
 * Get_config library for code igniter
 *
 * @package   Get_config
 * @author    Louis-Michel Raynauld
 * @version   0.1
 * @license   Commercial
 */
class Get_config {
  
  var $CI;
  var $config_cookie_exp = 2592000;
  
  function Get_config()
  {
    $this->CI =& get_instance();
    $this->CI->load->helper('cookie');
    
    log_message('debug', "Get_config Class Initialized");
  }
  
  function set_config_from_get($get_var_name, $config_var_name, $cookie_enable, $cookie_name, $validated_value = NULL)
  {
    $value = $this->CI->input->get($get_var_name, TRUE);
    if(!empty($validated_value)&&!empty($value))
    {
      $value = $validated_value;

      if($cookie_enable)
      {
        $cookie = array(
                     'name'   => $cookie_name,
                     'value'  => $value,
                     'expire' => $this->config_cookie_exp,
                 );
        set_cookie($cookie);
      }
    }
    elseif(empty($value)&&$cookie_enable)
    {
      $value = $this->CI->input->cookie($cookie_name, TRUE);
    }
    
    if(empty($value))
    {
      $value = $validated_value;
    }
    
    $this->CI->config->set_item($config_var_name,$value);
    
    
  } 
  
}