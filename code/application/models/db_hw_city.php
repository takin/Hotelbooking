<?php
/**
 * @author Louis-Michel
 *
 */
class Db_hw_city extends CI_Model
{
  const HW_CITY_TABLE      = 'hw_city';
  const HW_COUNTRY_TABLE   = 'hw_country';
  const CONTINENT_TABLE    = 'continents';
  const COUNTRY_OF_CONTINENT_TABLE = 'country_continent';

  const CITY_DOMAIN_DATA_TABLE = 'city_by_domain';
  const CITY_TRANSLATION_TABLE = 'cities2';

  var $CI;

  function Db_hw_city()
  {
      parent::__construct();

      $this->CI =& get_instance();
      $this->CI->load->library('code_tracker');

      $this->db->simple_query("SET NAMES 'utf8'");
  }

  function hw_update_city_country($xml_data_object)
  {
//    print_r($xml_data_object);
    foreach($xml_data_object->Country as $country)
    {
      //If country with same iso code is not in DB
      if(is_null($country_id = $this->get_country_by_iso($country->countryCode)))
      {
        //If continent of country is in DB Add country to DB
        if(is_null($continent_id = $this->get_continent_en_id($country->countryContinent)))
        {
          $this->CI->code_tracker->add_trace("Continent ".$country->countryContinent. " not in DB could not add country ".$country->countryCode." -> ".$country->countryName);
        }
        else
        {
          //If country added correctly in DB
          if($this->insert_hw_country($country->countryName, $continent_id, $country->countryCode))
          {
            $this->CI->code_tracker->add_trace("Added country ".$country->countryName." (".$country->countryCode."), ".$country->countryContinent." to DB");

            $country_id = $this->db->insert_id();

            //Add all cities of new country also
            foreach($country->Cities->city as $city)
            {
              if($this->insert_hw_city($city,$country_id))
              {
                $this->CI->code_tracker->add_trace("Added city ".$city.", ".$country->countryName." to DB");
              }
              else
              {
                $this->CI->code_tracker->add_trace("Error inserting city ".$city.", ".$country->countryName." to DB");
              }
            }
          }
          else
          {
            $this->CI->code_tracker->add_trace("Error inserting country ".$country->countryCode.", ".$country->countryName." to DB");
          }
        }

      }
      else
      {
        //If countrynname has change update it
        if(($old_country_name = $this->update_hw_country_from_iso($country->countryCode,$country->countryName)) !== FALSE)
        {
          $this->CI->code_tracker->add_trace("Updated country name to ".$country->countryName." for ".$old_country_name." (".$country->countryCode."), ".$country->countryContinent);
        }

        //if continent of country has changed update it
        if(($old_continent_id = $this->update_hw_continent_from_iso($country->countryCode,$country->countryContinent)) !== FALSE)
        {
          $this->CI->code_tracker->add_trace("Updated country continent to ".$country->countryContinent." for ".$country->countryName." (".$country->countryCode."), ".$this->get_continent($old_continent_id));
        }

        //If country already in DB with ID $country_id
        //Verify if cities are up to date and modify if needed
        foreach($country->Cities->city as $city)
        {
          //If city is not there add it
          if(is_null($this->get_hw_city($city,$country_id)))
          {
            if($this->insert_hw_city($city,$country_id))
            {
              $this->CI->code_tracker->add_trace("Added city ".$city.", ".$country->countryName." to DB");
            }
            else
            {
              $this->CI->code_tracker->add_trace("Error inserting city ".$city.", ".$country->countryName." to DB");
            }
          }
        }

        //check if all cities in DB are valid for this country
        foreach($this->get_all_hw_cities_of_country($country_id) as $db_city)
        {
          //If db city not in xml
          if(!$this->city_in_xml($xml_data_object, $country->countryCode,$db_city->hw_city))
          {
            //delete city from DB
            //hw_hostel associated with this hw_city_id ate deleted via the ON DELETE CASCADE database mechanism
            $this->db->where('hw_city_id', $db_city->hw_city_id);
            if($this->db->delete(self::HW_CITY_TABLE))
            {
              $this->CI->code_tracker->add_trace("Deleted city ".$db_city->hw_city.", ".$country->countryName." from DB");
            }
            else
            {
              $this->CI->code_tracker->add_trace("Error deleting city ".$db_city->hw_city.", ".$country->countryName." from DB");
            }
          }
        }
      }

    }

    //check all country and cities in db and delete if it is not in the list
    if(!is_null($countries_in_db = $this->get_all_countries()))
    {
      foreach($countries_in_db as $db_country)
      {
        //If country not in list delete country and all its cities
        // note: the case where country is in list is taking care above when xml country is in DB
        if(!$this->country_in_xml($xml_data_object,$db_country->country_iso_code_2))
        {
          $this->db->where('hw_country_id', $db_country->hw_country_id);
          $query = $this->db->get(self::HW_CITY_TABLE);

          foreach($query->result() as $city)
          {
            //hw_hostel associated with this hw_city_id are deleted via the ON DELETE CASCADE database mechanism
            $this->db->where('hw_city_id', $city->hw_city_id);
            if($this->db->delete(self::HW_CITY_TABLE))
            {
              $this->CI->code_tracker->add_trace("Deleted city ".$city->hw_city.", ".$db_country->hw_country." from DB");
            }
            else
            {
              $this->CI->code_tracker->add_trace("Error deleting city ".$city->hw_city.", ".$db_country->hw_country." from DB");
            }

          }

          $this->db->where('hw_country_id', $db_country->hw_country_id);
          if($this->db->delete(self::HW_COUNTRY_TABLE))
          {
            $this->CI->code_tracker->add_trace("Deleted country ".$db_country->hw_country." (".$db_country->country_iso_code_2."), ".$db_country->continent_en." from DB");
          }
          else
          {
            $this->CI->code_tracker->add_trace("Error deleting country ".$db_country->hw_country." (".$db_country->country_iso_code_2."), ".$db_country->continent_en." from DB");
          }
        }
      }
    }

  }

  function cityCountryList_DB($continent = NULL, $country = NULL, $lang = "en")
  {
    $this->CI->load->helper('xml');

    $citylistobject = new SimpleXMLElement("<?xml version='1.0' ?>\n<CityCountryList></CityCountryList>");

    $last_country_id = 0;
    $cities = NULL;

    foreach($this->get_hw_cities_of_country_name($continent, $country, $lang) AS $hw_city)
    {

      if(($last_country_id != $hw_city->hw_country_id) ||
          ($last_country_id == 0))
      {

        $country_object = new SimpleXMLElement("<Country></Country>");
        $country_object = $citylistobject->addChild("Country", $country_object);

        $country_object->addChild("countryContinent", $hw_city->continent_name);
        $country_object->addChild("countryContinentTranslated", $hw_city->continent_name_translated);
        $country_object->addChild("countryName",      xml_convert($hw_city->country_name));
        $country_object->addChild("countryNameTranslated",     xml_convert($hw_city->country_name_translated));
        $country_object->addChild("countryCode",      $hw_city->country_iso_code_2);
        $country_object->addChild("countryLatitude",      $hw_city->country_geo_latitude);
        $country_object->addChild("countryLongitude",     $hw_city->country_geo_longitude);

        $cities = new SimpleXMLElement("<Cities></Cities>");
        $cities = $country_object->addChild("Cities", $cities);

        $cities->addChild("cityName", xml_convert($hw_city->city_name));
        if(!empty($hw_city->city_name_translated))
        {
          $cities->addChild("cityNameTranslated", xml_convert($hw_city->city_name_translated));
        }
        else
        {
          $cities->addChild("cityNameTranslated", NULL);
        }

        if(!empty($hw_city->city_geo_latitude) && !empty($hw_city->city_geo_longitude))
        {
          $cities->addChild("cityLatitude", $hw_city->city_geo_latitude);
          $cities->addChild("cityLongitude", $hw_city->city_geo_longitude);
        }
        else
        {
          $cities->addChild("cityLatitude", NULL);
          $cities->addChild("cityLongitude", NULL);
        }

      }
      else
      {
        //Add city to actual country
        $cities = new SimpleXMLElement("<Cities></Cities>");
        $cities = $country_object->addChild("Cities", $cities);

        $cities->addChild("cityName", xml_convert($hw_city->city_name));
        if(!empty($hw_city->city_name_translated))
        {
          $cities->addChild("cityNameTranslated", xml_convert($hw_city->city_name_translated));
        }
        else
        {
          $cities->addChild("cityNameTranslated", NULL);
        }

        if(!empty($hw_city->city_geo_latitude) && !empty($hw_city->city_geo_longitude))
        {
          $cities->addChild("cityLatitude", $hw_city->city_geo_latitude);
          $cities->addChild("cityLongitude", $hw_city->city_geo_longitude);
        }
        else
        {
          $cities->addChild("cityLatitude", NULL);
          $cities->addChild("cityLongitude", NULL);
        }
      }

      $last_country_id = $hw_city->hw_country_id;
    }

    return $citylistobject;
  }

  function get_continent_en_id($continent)
  {
    $continent = $this->db->escape_str($continent);
    $this->db->where("LOWER(continent_en) LIKE LOWER('$continent')");
    $query = $this->db->get(self::CONTINENT_TABLE);

    if($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->continent_id;
    }
    log_message('error',"db_hw_data error -> continent $continent not found or found more than once.");
    return NULL;
  }

  function get_continent($continent_id)
  {
    $this->db->where("continent_id",$continent_id);
    $query = $this->db->get(self::CONTINENT_TABLE);

    if($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->continent_en;
    }
    log_message('error',"db_hw_data error -> continent ID $continent_id not found or found more than once.");
    return NULL;
  }

  function get_country_by_iso($country_iso_2)
  {
    $country_iso_2 = $this->db->escape_str($country_iso_2);
    $this->db->where("LOWER(country_iso_code_2) LIKE LOWER('$country_iso_2')");
    $query = $this->db->get(self::HW_COUNTRY_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hw_country_id;
    }
    return NULL;
  }

  function get_country_by_name($country_english_name)
  {
    $country_english_name = $this->db->escape_str($country_english_name);
    $this->db->where("LOWER(hw_country) LIKE LOWER('$country_english_name')");
    $query = $this->db->get(self::HW_COUNTRY_TABLE);

    if($query->num_rows() > 0)
    {
      return $query->row();
    }
    return NULL;
  }

  function get_all_countries()
  {
    $this->db->join(self::CONTINENT_TABLE, self::CONTINENT_TABLE.'.continent_id = '.self::HW_COUNTRY_TABLE.'.continent_id');
    $query = $this->db->get(self::HW_COUNTRY_TABLE);
    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return NULL;
  }

  function get_hw_city_by_id($city_id, $lang ="en")
  {
    $this->CI->load->model('Db_country');
    $lang = $this->CI->Db_country->lang_code_convert($lang);

    //Manual select because city with double space like "Milton  Keynes" is not found with active record
    $sql = "SELECT IFNULL(`city_$lang`,".self::HW_CITY_TABLE.".hw_city) AS city_name_translated,
                   IFNULL(`country_$lang`,IFNULL((SELECT `country_$lang` FROM ".self::CITY_TRANSLATION_TABLE." WHERE LOWER(".self::CITY_TRANSLATION_TABLE.".country_en) LIKE LOWER(".self::HW_COUNTRY_TABLE.".hw_country) LIMIT 1),hw_country)) AS country_name_translated,
                   hw_city AS city_name,
                   hw_country AS country_name
            FROM hw_city
            LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
            LEFT JOIN cities2 ON ".self::HW_COUNTRY_TABLE.".hw_country = ".self::CITY_TRANSLATION_TABLE.".country_en AND ".self::HW_CITY_TABLE.".hw_city = ".self::CITY_TRANSLATION_TABLE.".city_en
            WHERE hw_city_id = $city_id";

    $query = $this->db->query($sql);
    if($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row;
    }
    return NULL;
  }

  function get_city($country_search,$city_search, $lang ="en")
  {
    $country_search = $this->db->escape_str($country_search);
    $city_search    = $this->db->escape_str($city_search);
    $base_url = str_replace('http://', '', str_replace('www.', '', substr(base_url(), 0, -1)));

    //If city translation is not found in db try to macth english country name to api english country name
    $sql = "SELECT IFNULL(`city_$lang`,".self::HW_CITY_TABLE.".hw_city) AS city_name_translated,
                       IFNULL(`country_$lang`,IFNULL((SELECT `country_$lang` FROM ".self::CITY_TRANSLATION_TABLE." WHERE LOWER(".self::CITY_TRANSLATION_TABLE.".country_en) LIKE LOWER(".self::HW_COUNTRY_TABLE.".hw_country) LIMIT 1),hw_country)) AS country_name_translated,
                       hw_city AS city_name,
                       hw_country AS country_name,
                       hw_city_id AS city_id,
                       lat as city_geo_lat,
                       lng as city_geo_lng,
                       IFNULL(city_by_domain.description,cities2.description) as city_description,
                       IFNULL(city_by_domain.image,cities2.image) as city_image,
                       city_by_domain.city_market_code as city_code
                FROM hw_city
                LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
                LEFT JOIN cities2 ON ".self::HW_COUNTRY_TABLE.".hw_country = ".self::CITY_TRANSLATION_TABLE.".country_en AND ".self::HW_CITY_TABLE.".hw_city = ".self::CITY_TRANSLATION_TABLE.".city_en
                LEFT JOIN ".self::CITY_DOMAIN_DATA_TABLE." ON cities2.city_id = ".self::CITY_DOMAIN_DATA_TABLE.".city_id AND ".self::CITY_DOMAIN_DATA_TABLE.".site_domain_id = ".$this->site_domain_id."
                WHERE ( LOWER(hw_country.hw_country) LIKE LOWER(IFNULL((SELECT `country_en` FROM cities2 WHERE LOWER(cities2.`country_$lang`) LIKE LOWER('$country_search') LIMIT 1), '$country_search'))
                        AND LOWER(hw_city.`hw_city`) LIKE LOWER('$city_search'))";

    $sql_where = "( ";
    $nci = 0;
    foreach($this->Db_country->get_city_fields() as $city_field)
    {
      if($nci==0)
      {
        $sql_where.= "LOWER(`$city_field`) LIKE LOWER('$city_search')";
      }
      else
      {
        $sql_where.= " OR LOWER(`$city_field`) LIKE LOWER('$city_search')";
      }
      $nci++;
    }

    $sql_where.= " ) AND ( ";

    $nci = 0;
    foreach($this->Db_country->get_country_fields() as $country_field)
    {
      if($nci==0)
      {
        $sql_where.= "LOWER(`$country_field`) LIKE LOWER('$country_search')";
      }
      else
      {
        $sql_where.= " OR LOWER(`$country_field`) LIKE LOWER('$country_search')";
      }
      $nci++;
    }
    $sql_where.= " )";


    $sql .= "OR (".$sql_where.")";
// debug_dump($sql);
    //    $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
    $query = $this->db->query($sql);

    if ($query->num_rows() == 1)
    {
      return $query->row();
    }
    log_message('info', 'City translation error: '.$country_search.', '.$city_search);
    return NULL;
  }

  function get_hw_city($city, $country_id)
  {
    $city = $this->db->escape_str($city);

    //Manual select because city with double space like "Milton  Keynes" is not found with active record
    $sql = "SELECT * FROM hw_city WHERE LOWER(hw_city) LIKE LOWER('$city') AND `hw_country_id` = $country_id";
    $query = $this->db->query($sql);
    if($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->hw_city_id;
    }
    return NULL;
  }

  function get_hw_cities_of_country_name($continent_name = NULL, $country_name = NULL, $lang = "en")
  {
     $this->CI->load->model('Db_country');

    $lang = $this->CI->Db_country->lang_code_convert($lang);

    $this->db->select(self::CONTINENT_TABLE.".continent_code as continent_code");
    $this->db->select(self::HW_CITY_TABLE.".hw_city_id");
    $this->db->select(self::HW_COUNTRY_TABLE.".hw_country_id");
    $this->db->select(self::CITY_TRANSLATION_TABLE.".lng AS city_geo_longitude");
    $this->db->select(self::CITY_TRANSLATION_TABLE.".lat AS city_geo_latitude");
    $this->db->select(self::CITY_TRANSLATION_TABLE.".lng AS country_geo_longitude");
    $this->db->select(self::CITY_TRANSLATION_TABLE.".lat AS country_geo_latitude");
    $this->db->select(self::HW_CITY_TABLE.".hw_city AS city_name");
    $this->db->select(self::HW_COUNTRY_TABLE.".hw_country AS country_name");

    //TONOTICE If translation of country and city does not exist return the api normal english name, this prevent null values that affect the sorting on order by
//    $this->db->select(self::CITY_TRANSLATION_TABLE.".`city_$lang` AS city_name_translated");
    $this->db->select("IFNULL(`city_$lang`,".self::HW_CITY_TABLE.".hw_city) AS city_name_translated",false);
//    $this->db->select(self::CITY_TRANSLATION_TABLE.".`country_$lang` AS country_name_translated");
    $this->db->select("IFNULL(`country_$lang`,IFNULL((SELECT `country_$lang` FROM ".self::CITY_TRANSLATION_TABLE." WHERE LOWER(".self::CITY_TRANSLATION_TABLE.".country_en) LIKE LOWER(".self::HW_COUNTRY_TABLE.".hw_country) LIMIT 1),hw_country)) AS country_name_translated",false);
    $this->db->select(self::CONTINENT_TABLE.".`continent_en` AS continent_name");
    $this->db->select(self::CONTINENT_TABLE.".`continent_$lang` AS continent_name_translated");
    $this->db->select(self::HW_COUNTRY_TABLE.".country_iso_code_2");

    $this->db->join(self::HW_COUNTRY_TABLE, self::HW_COUNTRY_TABLE.'.hw_country_id = '.self::HW_CITY_TABLE.'.hw_country_id', 'left');
    $this->db->join(self::CONTINENT_TABLE, self::CONTINENT_TABLE.'.continent_id = '.self::HW_COUNTRY_TABLE.'.continent_id', 'left');
    $this->db->join(self::CITY_TRANSLATION_TABLE,self::HW_COUNTRY_TABLE.".hw_country = ".self::CITY_TRANSLATION_TABLE.".country_en AND ".self::HW_CITY_TABLE.".hw_city = ".self::CITY_TRANSLATION_TABLE.".city_en","left");

    if(!empty($country_name))
    {
      $this->db->where(self::HW_COUNTRY_TABLE.".hw_country",$country_name);

      $country_name_escaped = $this->db->escape_str($country_name);
      foreach($this->CI->Db_country->get_country_fields() as $country_field)
      {
          $this->db->or_where("LOWER(`$country_field`) LIKE LOWER('$country_name_escaped')");
      }
    }
    elseif(!empty($continent_name))
    {
      $this->db->where(self::CONTINENT_TABLE.".continent_en",$continent_name);

      $continent_name_escaped = $this->db->escape_str($continent_name);
      foreach($this->CI->Db_country->get_continent_fields() as $continent_field)
      {
          $this->db->or_where("LOWER(`$continent_field`) LIKE LOWER('$continent_name_escaped')");
      }
    }

    $this->db->order_by("country_name_translated ASC, city_name_translated ASC");

//     $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
    $query = $this->db->get(self::HW_CITY_TABLE);
//    debug_dump( $this->db->last_query() );
    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return NULL;
  }

  function get_all_hw_cities_of_country($country_id)
  {
    $this->db->where("hw_country_id",$country_id);
    $query = $this->db->get(self::HW_CITY_TABLE);
    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return NULL;
  }

  function get_city_count()
  {
    return $this->db->count_all(self::HW_CITY_TABLE);
  }

  function get_hw_city_for_cache_search($limit = NULL)
  {

    $sql = "SELECT *,UNIX_TIMESTAMP(last_search_on) as last_search_on_timestamp FROM ".self::HW_CITY_TABLE;
    $sql.= "  LEFT JOIN ".self::HW_COUNTRY_TABLE." ON ".self::HW_CITY_TABLE.".hw_country_id = ".self::HW_COUNTRY_TABLE.".hw_country_id";
    $sql.= " ORDER BY last_search_on ASC";

    if(!is_null($limit))
    {
      $sql.= " LIMIT ".$limit;
    }
    $query = $this->db->query($sql);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return NULL;
  }

  /**
   * @param $iso_code
   * @param $country_name
   * @return old country name if country name has been updated, False otherwise
   */
  function update_hw_country_from_iso($iso_code, $country_name)
  {
    $this->db->where("country_iso_code_2",(string)$iso_code);
    $query = $this->db->get(self::HW_COUNTRY_TABLE);

    if($query->num_rows() == 1)
    {
      $row = $query->row();
      //if country name has changed update it
      if(strcasecmp($row->hw_country,$country_name)!=0)
      {
        //update
        $this->db->set('hw_country', (string)$country_name);
        $this->db->where('country_iso_code_2', (string)$iso_code);
        if($this->db->update(self::HW_COUNTRY_TABLE))
        {
          return $row->hw_country;
        }
        log_message('error',"db_hw_data error -> Unable to update country name of country with iso $iso_code.");
      }
      return FALSE;
    }

    log_message('error',"db_hw_data error -> country with iso code $iso_code not found or found more than once. Unable to update country name.");

    return FALSE;
  }

  /**
   * @param $iso_code
   * @param $continent
   * @return old continent id if countinent has been updated, False otherwise
   */
  function update_hw_continent_from_iso($iso_code, $continent)
  {
    if(!is_null($continent_id = $this->get_continent_en_id($continent)))
    {

      $this->db->where("country_iso_code_2",(string)$iso_code);
      $query = $this->db->get(self::HW_COUNTRY_TABLE);

      if($query->num_rows() == 1)
      {
        $row = $query->row();
        //if continent has changed update it
        if($continent_id != $row->continent_id)
        {
          //update
          $this->db->set('continent_id', $continent_id);
          $this->db->where('country_iso_code_2', (string)$iso_code);
          if($this->db->update(self::HW_COUNTRY_TABLE))
          {
            return $row->continent_id;
          }
          log_message('error',"db_hw_data error -> Unable to update continent of country with iso $iso_code.");
        }
        return FALSE;
      }

      log_message('error',"db_hw_data error -> country with iso code $iso_code not found or found more than once. Unable to update continent.");

      return FALSE;
    }

    log_message('error',"db_hw_data error -> invalid continent -> $continent. Unable to update continent of country with iso $iso_code.");

    return FALSE;
  }

  function update_city_last_search_time($city_id, $date = NULL)
  {
    if(is_null($date))
    {
      $date = date('Y-m-d H:i:s');
    }

    $this->db->set('modified', 'modified', FALSE); //prevent timestamp update
    $this->db->set('last_search_on', $date);
    $this->db->where('hw_city_id', $city_id);

    return $this->db->update(self::HW_CITY_TABLE);
  }

  function insert_hw_country($country_name, $continent_id, $country_iso_code)
  {
    $this->db->set('hw_country', (string)$country_name);
    $this->db->set('continent_id', $continent_id);
    $this->db->set('country_iso_code_2', (string)$country_iso_code);
    return $this->db->insert(self::HW_COUNTRY_TABLE);
  }

  function insert_hw_city($city, $country_id)
  {
    settype($city, "string");
    $this->db->set('hw_city', $city);
    $this->db->set('hw_country_id', $country_id);
    return $this->db->insert(self::HW_CITY_TABLE);
  }

  function country_in_xml($xml_countries,$country_iso_code_lookup)
  {
    foreach($xml_countries->Country as $country)
    {
      if(strcasecmp($country->countryCode,$country_iso_code_lookup)==0)
      {
        return TRUE;
      }
    }

    return FALSE;
  }

  function city_in_xml($xml_countries,$country_iso_code_lookup, $city_lookup)
  {
    foreach($xml_countries->Country as $xmlcountry)
    {
      if(strcasecmp($xmlcountry->countryCode,$country_iso_code_lookup)==0)
      {
        foreach($xmlcountry->Cities->city as $xmlcity)
        {
          if(strcasecmp($xmlcity,$city_lookup)==0)
          {
            return TRUE;
          }
        }
        return FALSE;
      }
    }

    return FALSE;
  }

  function set_logfile($filename)
  {
    $this->CI->code_tracker->set_logfile($filename);
  }

  function add_trace_to_report($text)
  {
    $this->CI->code_tracker->add_trace($text);
  }

  function modification_html_report()
  {
    return $this->CI->code_tracker->html_report();
  }

  function is_cities_modified()
  {
    return $this->CI->code_tracker->is_mod_added();
  }

}