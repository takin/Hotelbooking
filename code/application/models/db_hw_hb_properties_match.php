<?php
class Db_hw_hb_properties_match extends CI_Model
{

  private $CI;

  function __construct()
  {
    parent::__construct();
    $this->CI =& get_instance();
    $this->db->simple_query("SET NAMES 'utf8'");
  }

  function get_unmatched_hw_prop()
  {
    $sql = "SELECT * FROM hw_hostel
            LEFT JOIN hw_hb_match ON hw_hostel.property_number = hw_hb_match.hw_property_number
            WHERE hw_hb_match.hw_property_number IS NULL";

    $query = $this->db->query($sql);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return array();
  }

  function insert_match($hw_property_number,$hb_property_number)
  {
    settype($hw_property_number,'integer');
    settype($hb_property_number,'integer');

//     $hw_property_number    = $this->db->escape($hw_property_number);
//     $hb_property_number    = $this->db->escape($hb_property_number);

//     Trying this because it fails witch active record
//     $sql = "INSERT INTO `hw_hb_match` (`hw_property_number`, `hb_property_number`)";
//     $sql.= " VALUES ('$hw_property_number', '$hb_property_number')";
//     return $this->db->query($sql);
    $this->db->set('hw_property_number', $hw_property_number);
    $this->db->set('hb_property_number', $hb_property_number);
    $this->db->set('human_valid', 'DEFAULT', false);
    $this->db->set('added', 'DEFAULT', false);
    return $this->db->insert('hw_hb_match');
  }

  function compare_properties($prop_a, $prop_b)
  {

    $this->CI->load->library('Geos');
    $this->CI->load->model('Db_hw_search');

    //TODO check in DB if human validation and return it if found

    $match_table = array("name" => null,
                           "address_no"   => null,
                           "address1" => null,
                           "address2" => null,
                           "address_all" => null,
                           "phone" => null,
                           "distance" => null
    );
    $match_table['distance'] = $this->CI->geos->get_distance($prop_a->geo_latitude, $prop_a->geo_longitude, $prop_b->geo_latitude, $prop_b->geo_longitude);
    if($match_table['distance'] < 1)
    {
      $match_table['name'] = levenshtein($this->CI->Db_hw_search->prepare_search_terms(strtolower($prop_a->property_name)),$this->CI->Db_hw_search->prepare_search_terms(strtolower($prop_b->property_name)));

      if($match_table['name'] == 0)
      {
        return $match_table;
      }

      //compare address number
      $address1_a = strtolower(trim($prop_a->address1));
      $address1_b = strtolower(trim($prop_b->address1));
      $address2_a = strtolower(trim($prop_a->address2));
      $address2_b = strtolower(trim($prop_b->address2));

      $address_a = extract_int_of_str($address1_a.' '.$address2_a);
      $address_b = extract_int_of_str($address1_b.' '.$address2_b);

      $match_table['address_no'] = 50;
      if($address_a == $address_b)
      {
        if(!empty($address_a))
        {
          $match_table['address_no'] = 0;
          return $match_table;
        }
        else
        {
          $match_table['address_no'] = -1;
        }
      }


      $match_table['address1'] = levenshtein($address1_a,$address1_b);

      $match_table['address2'] = levenshtein($address2_a,$address2_b);
      $match_table['address_all'] = levenshtein($address1_a.' '.$address2_a,$address1_b.' '.$address2_b);

      //compare phone if both are filled
      if(!empty($prop_a->phone) && !empty($prop_b->phone) )
      {
        $phone_a = str_replace(array('+',' ','-','(',')'),'',$prop_a->phone);
        $phone_b = str_replace(array('+',' ','-','(',')'),'',$prop_b->phone);
        $match_table['phone'] = levenshtein(trim($phone_a),trim($phone_b));
      }

      return $match_table;
    }

    return false;
  }
}