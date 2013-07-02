<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * I18n
 *
 * I18n library for code igniter
 *
 * @package   I18n
 * @subpackage  Libraries
 * @version   0.1
 * @license   Commercial
 */
class I18n
{
  var $lang_filename;
  var $lang_filedir;

  /**
   * Constructor
   *
   * @access  public
   */
  function I18n()
  {
    log_message('debug', "I18n Class Initialized");

    $CI =& get_instance();
    $CI->load->config('i18n', TRUE);

    $this->lang_filename = $CI->config->item('gettext_filename','i18n');
    $this->lang_filedir  = $CI->config->item('gettext_filedir','i18n');
  }

  // --------------------------------------------------------------------

  /**
   * Load a gettext language file
   *
   * @access  public
   * @param encoding
   *
   */
  function load_gettext($encoding = "UTF-8")
  {
    if (file_exists($this->lang_filedir))
    {
      bindtextdomain($this->lang_filename, $this->lang_filedir);
      bind_textdomain_codeset($this->lang_filename, $encoding);
      textdomain($this->lang_filename);
      log_message('debug', "I18n Directory ".$this->lang_filedir);
      log_message('debug', "I18n Filename ".$this->lang_filename);
    }
    else
    {
      show_error('Unable to load the requested language dir: '.$this->lang_filedir);
    }
  }

  function set_lang_filename($filename)
  {
    $this->lang_filename = $filename;
  }

  function set_lang_filedir($filedir)
  {
    $this->lang_filedir = $filedir;
  }

}
?>