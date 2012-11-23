<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Code_tracker
 *
 * Code_tracker library for code igniter
 *
 * @package   Code_tracker
 * @author    Louis-Michel Raynauld
 * @version   0.1
 * @license   Commercial
 */
class Code_tracker {

  const FEED_DEBUG = 0;
  const FEED_INFO = 1;
  const FEED_ERROR = 2;
  const FEED_ALWAYS = 10;

  private $feed_error_level_treshold = 2;

  private $CI;
  private $cache_modifications = array();
  private $log_file = "";
  private $echo_instead = FALSE;

  private $mem_limit = 0;
  /**
   * Constructor
   *
   * @access  public
   */
  function __construct()
  {

    $this->CI =& get_instance();
    $this->CI->load->helper('memory');
    $this->CI->load->library('custom_log');
    $this->cache_modifications = array();
    $this->mem_limit = floatval (ini_get('memory_limit')) * 0.9;

    log_message('debug', "Code Tracker Class Initialized");
  }

  public function set_logfile($filename)
  {
    $this->log_file = $filename;
  }

  public function html_report()
  {
    $report = "<table>";
    if(empty($this->cache_modifications))
    {
      $report.= "<tr><td>No modifications</td></tr>";
    }
    else
    {
      foreach($this->cache_modifications as $modif)
      {
        $report.= "<tr><td>".$modif."</td></tr>";
      }
      $report.= "</table>";
    }

    return $report;
  }

  public function set_echo($toggle = TRUE)
  {
    $this->echo_instead = $toggle;
  }

  public function set_feed_treshold($level)
  {
    $this->feed_error_level_treshold = $level;
  }

  public function feed_trace($level, $text)
  {
    if($level >= $this->feed_error_level_treshold)
    {
      if($this->echo_instead === TRUE)
      {
        echo $text."<br>\n";
      }
      else
      {
        $this->add_trace($text);
      }
    }
  }

  /*
  *  prevent memory leak problems.
  */
  public function add_trace($track_text)
  {

    array_push($this->cache_modifications,$track_text);
    if(!empty($this->log_file))
    {
      $this->CI->custom_log->log($this->log_file,$track_text);
    }
    else
    {
      log_message('debug', $track_text);
    }

    if(memory_usage_in_mb() > $this->mem_limit)
    {
      $this->clear_mod_cache();
      $track_text = "Cleared code tracker modifications array to prevent memory from exceeding limit.";
      array_push($this->cache_modifications,$track_text);
      if(!empty($this->log_file))
      {
        $this->CI->custom_log->log($this->log_file,$track_text);
      }
      else
      {
        log_message('debug', $track_text);
      }
    }
  }

  public function clear_mod_cache()
  {
    $this->cache_modifications = array();
  }

  public function is_mod_added()
  {
    if(empty($this->cache_modifications))
    {
      return FALSE;
    }

    return TRUE;
  }
}

?>