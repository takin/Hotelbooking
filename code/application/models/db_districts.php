<?php

class Db_districts extends Model
{
  const DISTRICTS_TABLE = 'districts';
  private $CI;

  function __construct()
  {
      parent::Model();
      $this->CI =& get_instance();
  }

  public function create_slug($district_name)
  {
    return mb_cleanURL(mb_stripAccent(trim($district_name)));
  }

  public function insert_district($district)
  {
    $this->CI->db->set('district_name', (string)$district->properties->name);
    $this->CI->db->set('slug', (string) $this->create_slug($district->properties->name));
    $this->CI->db->set('um_id', (integer)$district->id);
    $this->CI->db->set('area', (float) $district->properties->area);
    return $this->CI->db->insert(self::DISTRICTS_TABLE);
  }

  public function update_district($district)
  {
    $this->CI->db->set('district_name', (string)$district->properties->name);
    $this->CI->db->set('slug', (string) $this->create_slug($district->properties->name));
    $this->CI->db->set('area', (float)$district->properties->area);
    $this->CI->db->where('um_id', (integer)$district->id);
    return $this->CI->db->update(self::DISTRICTS_TABLE);
  }

  public function setup_district_slug()
  {
    $query = $this->CI->db->get(self::DISTRICTS_TABLE);
    if($query->num_rows() > 0)
    {
      foreach ($query->result() as $row)
      {
        $district = NULL;
        $district->id               = $row->um_id;
        $district->properties->name = $row->district_name;
        $district->properties->area = $row->area;
        $this->update_district($district);
      }
      return true;
    }
    return NULL;
  }

  public function get_district_id($district_um_id)
  {
    $this->CI->db->select("district_id");
    $this->CI->db->where("um_id",$district_um_id);

    $query = $this->CI->db->get(self::DISTRICTS_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->district_id;
    }
    return NULL;
  }
  public function get_city_district_from_slug($city_id, $slug)
  {
    settype($city_id,"integer");
    $slug    = $this->CI->db->escape_str($slug);
    $city_id = $this->CI->db->escape($city_id);

    $sql = "SELECT ".self::DISTRICTS_TABLE.".district_id,
          							 ".self::DISTRICTS_TABLE.".district_name,
          							 ".self::DISTRICTS_TABLE.".slug,
          							 ".self::DISTRICTS_TABLE.".area,
          							 ".self::DISTRICTS_TABLE.".um_id,
          							 city_hb_id AS city_id
                  FROM ".self::DISTRICTS_TABLE."
                  LEFT JOIN (`translation_links`) ON ".self::DISTRICTS_TABLE.".slug = translation_links.term
                  LEFT JOIN hb_hostel_district on districts.district_id = hb_hostel_district.district_id
                  LEFT JOIN hb_hostel ON hb_hostel_district.property_number = hb_hostel.property_number
                  WHERE (".self::DISTRICTS_TABLE.".slug = '$slug'
                  OR `term` = '$slug'
                  OR `term_en` = '$slug'
                  OR `term_fr` = '$slug'
                  OR `term_es` = '$slug'
                  OR `term_de` = '$slug'
                  OR `term_pt` = '$slug'
                  OR `term_zh-CN` = '$slug'
                  OR `term_it` = '$slug'
                  OR `term_pl` = '$slug'
                  OR `term_ru` = '$slug'
                  OR `term_no` = '$slug'
                  OR `term_fi` = '$slug'
                  OR `term_cs` = '$slug'
                  OR `term_ko` = '$slug'
                  OR `term_ja` = '$slug'
                  OR `term_hu` = '$slug'
                  )  AND city_hb_id = $city_id
                  GROUP BY term";

    if($this->CI->api_used != HB_API)
    {
      $sql = "SELECT ".self::DISTRICTS_TABLE.".district_id,
      							 ".self::DISTRICTS_TABLE.".district_name,
      							 ".self::DISTRICTS_TABLE.".slug,
      							 ".self::DISTRICTS_TABLE.".area,
      							 ".self::DISTRICTS_TABLE.".um_id,
      							 hw_city_id AS city_id
              FROM ".self::DISTRICTS_TABLE."
              LEFT JOIN (`translation_links`) ON ".self::DISTRICTS_TABLE.".slug = translation_links.term
              LEFT JOIN hw_hostel_district on districts.district_id = hw_hostel_district.district_id
              LEFT JOIN hw_hostel ON hw_hostel_district.property_number = hw_hostel.property_number
              WHERE (".self::DISTRICTS_TABLE.".slug = '$slug'
              OR `term` = '$slug'
              OR `term_en` = '$slug'
              OR `term_fr` = '$slug'
              OR `term_es` = '$slug'
              OR `term_de` = '$slug'
              OR `term_pt` = '$slug'
              OR `term_zh-CN` = '$slug'
              OR `term_it` = '$slug'
              OR `term_pl` = '$slug'
              OR `term_ru` = '$slug'
              OR `term_no` = '$slug'
              OR `term_fi` = '$slug'
              OR `term_cs` = '$slug'
              OR `term_ko` = '$slug'
              OR `term_ja` = '$slug'
              OR `term_hu` = '$slug'
              )  AND hw_city_id = $city_id
              GROUP BY term";
    }

    $query = $this->CI->db->query($sql);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row;
    }
    return NULL;
  }

}