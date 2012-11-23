<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Wordpress Model
 *
 *
 * @package   Wordpress
 * @author    Louis-Michel Raynauld
 * @version   0.1
 * @license   Commercial
 */

class Wordpress extends Model
{
  const OPTION_TABLE = 'wp_options';

  var $wpDB = NULL;

  var $options = array();

  function Wordpress()
  {
    parent::Model();
  }

  /*
   *
   */
  function load_wordpress_db($db_var_name)
  {
    $this->wpDB = $this->load->database($db_var_name, TRUE);
    $this->wpDB->simple_query("SET NAMES 'utf8'");

    //reset option memory cache
    $this->options = array();
  }

  /*
   *
   */
  function get_option($option_name, $default = "")
  {
    if(is_null($this->wpDB))
    {
      log_message('error','Wordpress DB not set. Please run load_wordpress_db() first.');
    }

    if(empty($this->options))
    {
      $this->fetch_options("aj_");
    }

    if(isset($this->options[$option_name]))
    {
      return $this->options[$option_name];
    }

    $this->wpDB->where('option_name', $option_name);
    $query = $this->wpDB->get('wp_options');

    if ($query->num_rows() == 1)
    {
      $this->options[$option_name] = $query->row()->option_value;
      return $this->options[$option_name];
    }
    elseif(!empty($default))
    {
      $this->options[$option_name] = $default;
      return $this->options[$option_name];
    }

    return NULL;
  }

  function fetch_options($site_prefix = "")
  {
    $this->wpDB->where("option_name LIKE'$site_prefix%'");
    $query = $this->wpDB->get('wp_options');

    if ($query->num_rows() > 0)
    {
      foreach($query->result() as $option)
      {
        $this->options[$option->option_name] = $option->option_value;
      }
    }
  }

  function set_option($option_name, $option_value)
  {
    if(is_null($this->get_option($option_name)))
    {
      $data = array(
                  'option_name' => $option_name,
                  'option_value' => $option_value
              );

      $this->wpDB->insert(self::OPTION_TABLE, $data);
    }
    else
    {
      $this->wpDB->where('option_name', $option_name);
      $this->wpDB->update(self::OPTION_TABLE, array('option_value' => $option_value));
    }
  }
}