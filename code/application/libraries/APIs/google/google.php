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
 */

class Google
{
  protected $CI = NULL;
  protected $api_server = "https://maps.googleapis.com/";
  protected $api_key    = "AIzaSyA6W5ggZkkmg73HyDqbYocjUd2EREJVB4E";

  public function __construct()
  {
    $this->CI =& get_instance();
  }
}