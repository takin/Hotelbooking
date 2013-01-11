<?php
/**
 * @author Louis-Michel
 *
 */
class Db_hw_rating extends CI_Model
{
  const HW_RATING_TABLE      = 'hw_rating';
  const HW_HOSTEL_TABLE    = 'hw_hostel';


  function Db_hw_rating()
  {
      parent::__construct();

      $this->db->simple_query("SET NAMES 'utf8'");
  }

  function get_hw_rating($property_number)
  {
    $this->db->select("rating");
    $this->db->where("property_number",$property_number);
    $query = $this->db->get(self::HW_RATING_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->rating;
    }
    return NULL;
  }
}
?>