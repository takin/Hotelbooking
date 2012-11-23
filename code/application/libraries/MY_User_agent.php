<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_User_agent extends CI_User_agent {

  var $is_tablet	= FALSE;

  var $tablets	= array();
  var $tablet		= '';

  /**
  * Compile the User Agent Data
  *
  * @access	private
  * @return	bool
  */
  function _load_agent_file()
  {
    if ( ! @include(APPPATH.'config/user_agents'.EXT))
    {
      return FALSE;
    }

    $return = FALSE;

    if (isset($platforms))
    {
      $this->platforms = $platforms;
      unset($platforms);
      $return = TRUE;
    }

    if (isset($browsers))
    {
      $this->browsers = $browsers;
      unset($browsers);
      $return = TRUE;
    }

    if (isset($tablets))
    {
      $this->tablets = $tablets;
      unset($tablets);
      $return = TRUE;
    }

    if (isset($mobiles))
    {
      $this->mobiles = $mobiles;
      unset($mobiles);
      $return = TRUE;
    }

    if (isset($robots))
    {
      $this->robots = $robots;
      unset($robots);
      $return = TRUE;
    }

    return $return;
  }

  /**
	 * Set the Mobile Device
	 *
	 * @access	private
	 * @return	bool
	 */
	function _set_mobile()
	{
		if (is_array($this->mobiles) AND count($this->mobiles) > 0)
		{
			foreach ($this->mobiles as $key => $val)
			{
				if (FALSE !== (strpos(strtolower($this->agent), $key)))
				{
					$this->is_mobile = TRUE;
					$this->mobile = $val;
					$this->_set_tablet();
					return TRUE;
				}
			}
		}
		return FALSE;
	}

  /**
  * Set the Tablet Device
  *
  * @access	private
  * @return	bool
  */
  function _set_tablet()
  {
    if (is_array($this->tablets) AND count($this->tablets) > 0)
    {
      foreach ($this->tablets as $key => $val)
      {
        if (FALSE !== (strpos(strtolower($this->agent), $key)))
        {
          $this->is_tablet = TRUE;
          $this->tablet = $val;
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
  * Is Tablet
  *
  * @access	public
  * @return	bool
  */
  function is_tablet()
  {
    return $this->is_tablet;
  }

  /**
  * Get the Tablet Device
  *
  * @access	public
  * @return	string
  */
  function tablet()
  {
    return $this->tablet;
  }
}