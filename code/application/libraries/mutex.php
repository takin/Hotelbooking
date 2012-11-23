<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter MuTex Class
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	  Libraries
 * @author        	Louis-Michel Raynauld
 *
 * Note: This is mainly for cronjobs and will work for CLI processes like running via wget command
 * 			 Does not work it 2 different tabs of a browser
 */
class Mutex {

  const LOCKS_DIR = "cache_queries/staticfeeds";

  private $lockfile = "lock.txt";
  private $lockfp = NULL;
  private $already_running = FALSE;

  public function __construct()
  {
    $this->CI =& get_instance();
    $this->CI->load->library('Log');

    log_message('debug', "MuTex Class Initialized");
  }

  public function lock($lock_name = "lock", $exit_script_on_locked = TRUE)
  {
    $this->lockfile = $lock_name.".txt";
    if(!is_writable(self::LOCKS_DIR."/".$this->lockfile))
    {
      $this->CI->log->write_log('error',"Could not write to lock file ".self::LOCKS_DIR."/".$this->lockfile);
      return false;
    }

    $this->lockfp = fopen(self::LOCKS_DIR."/".$this->lockfile, "w+");

    if (flock($this->lockfp, LOCK_EX | LOCK_NB))
    {
      $this->CI->log->write_log('debug',"Acquired lock $lock_name");
    }
    else
    {
      $this->already_running = TRUE;
      fclose($this->lockfp);
      if($exit_script_on_locked === TRUE)
      {
        $this->CI->log->write_log('error',"MuTex lib prevent script from running because lock $lock_name already acquired");
        exit;
      }
    }
  }

  public function unlock()
  {
    if(!$this->already_running && !is_null($this->lockfp))
    {
      $this->CI->log->write_log('debug',"Releasing lock");
      flock($this->lockfp, LOCK_UN);
      fclose($this->lockfp);
    }
  }

  public function is_already_running()
  {
    return $this->already_running;
  }
}
?>