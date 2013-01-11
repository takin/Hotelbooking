<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author Louis-Michel Raynauld
 *
 */

class User_profiles extends CI_Model {

  const TABLE       = 'user_profiles';  // user profiles
  const TABLE_USERS = 'users';    // user accounts

  function User_profiles()
  {
    parent::__construct();
    $this->db->simple_query("SET NAMES 'utf8'");
  }

  /**
   * set profile data of a user
   *
   * @param int
   * @param array
   *
   * Example of array data:
   *
   * $data = array(
   *              'firstname' => 'Georges',
   *              'lastname' => 'Washington'
   *              );
   *
   * @return  false if failed
   */
  function set_profile_data($user_id,$data,$create = true)
  {
    if(!is_null($user_id))
    {
      $query = $this->db->get_where(self::TABLE, array('user_id' => $user_id));
      if(($query->num_rows() == 0)&&($create==true))
      {
        $this->create_profile($user_id);
      }
      elseif($query->num_rows() == 0)
      {
        return false;
      }

      $this->db->where('user_id', $user_id);
      return $this->db->update(self::TABLE, $data);
    }

    return false;
  }

  /**
   * get user level of a user
   *
   * @param int
   *
   * @return  object or NULL
   */
  function get_user_level($user_id)
  {
    $this->db->select('user_level_id');
    $this->db->where('user_id', $user_id);
    $query = $this->db->get(self::TABLE);

    if ($query->num_rows() == 1)
    {
      $user = $query->row();
      return $user->user_level_id;
    }
    return NULL;

  }

  /**
   * get profile data of a user
   *
   * @param int
   *
   * @return  object or NULL
   */
  function get_profile_data($user_id)
  {
    $this->db->select('*,currencies.currency_code AS favorite_currency_code');
    $this->db->join('currencies', 'currencies.currency_id = '.self::TABLE.'.favorite_currency', 'left');
    $this->db->where('user_id', $user_id);
    $query = $this->db->get(self::TABLE);

    if ($query->num_rows() == 1) return $query->row();
    return NULL;

  }

  /**
   * get profile data of a user
   *
   * @param email
   *
   * @return  object or NULL
   */
  function get_profile_data_from_email($user_email)
  {

    $this->db->select('*');
    $this->db->from(self::TABLE);
    $this->db->join(self::TABLE_USERS, self::TABLE.".user_id = ".self::TABLE_USERS.".id");
    $this->db->where('email', $user_email);
    $query = $this->db->get();

    if ($query->num_rows() == 1) return $query->row();
    return NULL;

  }
}
