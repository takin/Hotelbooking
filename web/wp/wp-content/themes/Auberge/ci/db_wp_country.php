<?php
/**
 * @author Louis-Michel
 *
 */
class Db_country
{
  const CITY_TABLE      = 'cities2';
  const COUNTRY_TABLE   = 'countries';
  const CONTINENT_TABLE = 'continents';
  const COUNTRY_OF_CONTINENT_TABLE = 'country_continent';
  
  var $continent_fields = Array();
  var $country_fields   = Array();
  var $city_fields      = Array();
  
  var $db = NULL;
  
  
  function Db_country($dbconn)
  {
      $this->db = $dbconn;
      //Initialize all langages continent fields
      $sql = "SHOW COLUMNS FROM continents WHERE Type LIKE'varchar(255)' AND Field LIKE'continent%'";

      foreach($this->db->get_results($sql) as $row)
      {
         array_push($this->continent_fields, $row->Field);
      }
      
      //Initialize all langages country fields
      $sql = "SHOW COLUMNS FROM ".self::CITY_TABLE." WHERE Type LIKE'varchar(255)' AND Field LIKE'country%'";

      foreach ($this->db->get_results($sql) as $row)
      {
         array_push($this->country_fields, $row->Field);
      }
      
      //Initialize all langages country fields
      $sql = "SHOW COLUMNS FROM ".self::CITY_TABLE." WHERE Type LIKE'varchar(255)' AND Field LIKE'city%'";

      foreach ($this->db->get_results($sql) as $row)
      {
         array_push($this->city_fields, $row->Field);
      }
      
  }
  
  
  function get_all_continents($lang = 'en')
  {
    $lang = $this->lang_code_convert($lang);
    
    $query = "SELECT `continent_".$lang."` AS continent FROM ".self::CONTINENT_TABLE;
    
    $query = $this->db->get_results($query);
    
    return $query;
  }
  
  function get_all_countries($lang = 'en')
  {
    $lang = $this->lang_code_convert($lang);
    
    $query = " SELECT `country_".$lang."` AS country, `continent_".$lang."` AS continent";
    $query.= " FROM ".self::CITY_TABLE;
    $query.= " JOIN `".self::COUNTRY_OF_CONTINENT_TABLE."` ON `".self::COUNTRY_OF_CONTINENT_TABLE."`.`country_iso_code_2` = ".self::CITY_TABLE.".country_iso_code_2";
    $query.= " JOIN `".self::CONTINENT_TABLE."` ON `".self::COUNTRY_OF_CONTINENT_TABLE."`.`continent_code` = `".self::CONTINENT_TABLE."`.`continent_code`";
    $query.= " GROUP BY country_en";
    $query.= " ORDER BY continent ASC,country ASC ";

    $query = $this->db->get_results($query);
    
    return $query;
  }
  
  function get_all_hb_countries($lang = 'en')
  {
    $lang = $this->lang_code_convert($lang);
    
    $query = "SELECT hb_country.lname_en as country_en, `country_$lang` AS country_lang, continent_en,`continent_$lang` AS continent_lang, hb_country.`continent_hb_code`
              FROM hb_city 
              LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
              LEFT JOIN `continents` ON hb_country.`continent_hb_code` = `continents`.`continent_hb_code` 
              LEFT JOIN cities2 ON (hb_city.lname_en = cities2.city_en AND hb_country.lname_en = cities2.country_en)
              GROUP BY hb_country.hb_id
              ORDER BY continent_lang ASC,country_lang ASC";
    
    $query = $this->db->get_results($query);
    
    return $query;
  }
  
  function get_country_array()
  {
    $array = array(array("No default country",""));
    $query = "SELECT hw_country_id, hw_country FROM hw_country ORDER BY hw_country ASC";
    $query = $this->db->get_results($query);
    foreach($query as $country)
    {
      array_push($array,array($country->hw_country,$country->hw_country));
    }
    return $array;
  }
  
  function get_all_cities($lang = 'en')
  {
    $lang = $this->lang_code_convert($lang);
    
    $query = " SELECT `country_".$lang."` AS country, `city_".$lang."` AS city";
    $query.= " FROM ".self::CITY_TABLE;
    $query.= " ORDER BY country ASC,city ASC ";

    $query = $this->db->get_results($query);
    
    return $query;
  }
  
  function get_all_hb_cities($lang = 'en')
  {
    $lang = $this->lang_code_convert($lang);
    
    $query = " SELECT hb_city_id, hb_country.hb_country_id, hb_city.system_name AS city_system_name, hb_country.system_name AS country_system_name,
                     `city_$lang` AS display_city ,
                     IF(`country_$lang` IS NULL,(SELECT `country_$lang` FROM cities2 WHERE LOWER(cities2.country_iso_code_2) LIKE LOWER(hb_country.country_iso_code_2) LIMIT 1),`country_$lang`) AS display_country,
                     hb_city.lname_en AS city_lname_en ,hb_country.lname_en AS country_lname_en,
                     hb_country.country_iso_code_2,      
                     continent_en, `continent_$lang` AS translated_continent  
              FROM hb_city 
              LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id 
              LEFT JOIN cities2 ON (hb_city.lname_en = cities2.city_en AND hb_country.lname_en = cities2.country_en)
              LEFT JOIN continents ON continents.continent_hb_code = hb_country.continent_hb_code 
              ORDER BY hb_country.system_name, hb_city.system_name ASC";

    $query = $this->db->get_results($query);
    
    return $query;
  }
  
  function get_city_array()
  {
    $array = array(array("No default city",""));
    $query = "SELECT hw_country,hw_city";
    $query.= " FROM hw_city";
    $query.= " LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id";
    $query.= " ORDER BY hw_country,hw_city ASC";
    
    $query = $this->db->get_results($query);
    foreach($query as $city)
    {
      array_push($array,array($city->hw_country." -> ".$city->hw_city,$city->hw_city));
    }
    return $array;
  }
  
  function get_continent($continent, $lang = 'en')
  {
    
    $lang = $this->lang_code_convert($lang);
    $country_search = addslashes($country_search);
    
    $query = "SELECT `continent_$lang` FROM ".self::CONTINENT_TABLE;
    
    $nbcontinentfield = 0;
    foreach($this->continent_fields as $continent_field)
    {
      if($nbcontinentfield==0)
      {
        $query.= " WHERE LOWER(`$continent_field`) LIKE LOWER('$continent')";
      }
      else
      {
        $query.= " OR LOWER(`$continent_field`) LIKE LOWER('$continent')";
      }
      $nbcontinentfield++;
    }
    
//     $query.= " GROUP BY continent_en"; 

     $continent_translated = $this->db->get_var($query);
    
    if(!empty($continent_translated)) return stripslashes($continent_translated);

    return stripslashes($continent);
  }
//  
//  function get_continent_of_country($country, $lang = 'en')
//  {
//    $this->db->cache_on();
//    
//    $lang = $this->lang_code_convert($lang);
//    $country = addslashes($country);
//    
//    $query = " SELECT * ";
//    $query.= " FROM ".self::CITY_TABLE;
//    $query.= " JOIN `".self::COUNTRY_OF_CONTINENT_TABLE."` ON `".self::COUNTRY_OF_CONTINENT_TABLE."`.`country_iso_code_2` = ".self::CITY_TABLE.".country_iso_code_2";
//    $query.= " JOIN `".self::CONTINENT_TABLE."` ON `".self::COUNTRY_OF_CONTINENT_TABLE."`.`continent_code` = `".self::CONTINENT_TABLE."`.`continent_code`";
//    
//    $cfi = 0;
//    foreach($this->country_fields as $continent_field)
//    {
//      if($cfi==0)
//      {
//        $query.= " WHERE LOWER(".self::CITY_TABLE.".`$continent_field`) LIKE LOWER('$country')";
//      }
//      else
//      {
//        $query.= "    OR LOWER(".self::CITY_TABLE.".`$continent_field`) LIKE LOWER('$country')";
//      }
//      $cfi++;
//    }
//
//    $query.= "    GROUP BY ".self::CITY_TABLE.".country_iso_code_2,".self::CITY_TABLE.".country_iso_code_3";
//    $query.= " LIMIT 1";
//    
//    $query = $this->db->query($query);
//    
//    $this->db->cache_off();
//    
//    if ($query->num_rows() == 1) 
//    {
//      $row = $query->row_array();
//      if(!empty($row["continent_".$lang]))
//      {
//        return $row["continent_".$lang];
//      }
//      elseif(!empty($row["continent_en"]))
//      {
//        return $row["continent_en"];
//      }
//    }
//    log_message('error', 'get_continent_of_country error: '.$country);
//    
//    return NULL;
//  }
//  
  function get_country($country_search, $lang = "en")
  {
    $lang = $this->lang_code_convert($lang);
    $country_search = addslashes($country_search);
    
    $query = "SELECT `country_$lang` FROM ".self::CITY_TABLE;
    
    $nbcontinentfield = 0;
    foreach($this->country_fields as $country_field)
    {
      if($nbcontinentfield==0)
      {
        $query.= " WHERE LOWER(`$country_field`) LIKE LOWER('$country_search')";
      }
      else
      {
        $query.= " OR LOWER(`$country_field`) LIKE LOWER('$country_search')";
      }
      $nbcontinentfield++;
    }
    
     $query.= " GROUP BY country_en"; 

     $country_translated = $this->db->get_var($query);
    
    if(!empty($country_translated)) return stripslashes($country_translated);

    return stripslashes($country_search);
  }
  
//  
//  function get_city($country_search,$city_search, $lang = "en")
//  {
//    $this->db->cache_on();
//    
//    $lang = $this->lang_code_convert($lang);
//    
//    $country_search = addslashes($country_search);
//    $city_search    = addslashes($city_search);
//    
//    $sql_where = "( ";
//    $nci = 0;
//    foreach($this->city_fields as $city_field)
//    {
//      if($nci==0)
//      {
//        $sql_where.= "LOWER(`$city_field`) LIKE LOWER('$city_search')";
//      }
//      else
//      {
//        $sql_where.= " OR LOWER(`$city_field`) LIKE LOWER('$city_search')";
//      }
//      $nci++;
//    }
//    
//    $sql_where.= " ) AND ( ";
//    
//    $nci = 0;
//    foreach($this->country_fields as $country_field)
//    {
//      if($nci==0)
//      {
//        $sql_where.= "LOWER(`$country_field`) LIKE LOWER('$country_search')";
//      }
//      else
//      {
//        $sql_where.= " OR LOWER(`$country_field`) LIKE LOWER('$country_search')";
//      }
//      $nci++;
//    }
//    $sql_where.= " )";
//    
//    $this->db->where($sql_where);
//    $query = $this->db->get(self::CITY_TABLE);
//    
//    $this->db->cache_off();
//    
//    if ($query->num_rows() == 1) 
//    {
//      $row = $query->row_array();
//      if(!empty($row["city_".$lang]))
//      {
//        return stripslashes($row["city_".$lang]);
//      }
//    }
//    log_message('error', 'City translation error: '.$country_search.', '.$city_search);
//    return stripslashes($city_search);
//  }
//  
//  function get_city_link($country_search,$city_search, $lang = "en")
//  {
//    $this->db->cache_on();
//    
//    $lang = $this->lang_code_convert($lang);
//    
//    $country_search = addslashes($country_search);
//    $city_search    = addslashes($city_search);
//    
//    $sql_where = "( ";
//    $nci = 0;
//    foreach($this->city_fields as $city_field)
//    {
//      if($nci==0)
//      {
//        $sql_where.= "LOWER(`$city_field`) LIKE LOWER('$city_search')";
//      }
//      else
//      {
//        $sql_where.= " OR LOWER(`$city_field`) LIKE LOWER('$city_search')";
//      }
//      $nci++;
//    }
//    
//    $sql_where.= " ) AND ( ";
//    
//    $nci = 0;
//    foreach($this->country_fields as $country_field)
//    {
//      if($nci==0)
//      {
//        $sql_where.= "LOWER(`$country_field`) LIKE LOWER('$country_search')";
//      }
//      else
//      {
//        $sql_where.= " OR LOWER(`$country_field`) LIKE LOWER('$country_search')";
//      }
//      $nci++;
//    }
//    $sql_where.= " )";
//    
//    $this->db->where($sql_where);
//    $query = $this->db->get(self::CITY_TABLE);
//    
//    $this->db->cache_off();
//    
//    if ($query->num_rows() == 1) 
//    {
//      $row = $query->row_array();
//      if((!empty($row["city_".$lang])) &&(!empty($row["country_".$lang])) )
//      {
//        return $row["country_".$lang]."/".$row["city_".$lang];
//      }
//    }
//    log_message('error', 'City translation error: '.$country_search.', '.$city_search);
//    return stripslashes($country_search)."/".stripslashes($city_search);
//  }
    
  /**
   *  langage support
   */
  function lang_code_convert($lang_code)
  {
    //TODO check in array to get langage support list correctly
    switch(strtolower($lang_code))
    {
      case strtolower("fr"):
        return "fr";
      case strtolower("en"):
        return "en";
      case strtolower("es"):
        return "es";
      case strtolower("de"):
        return "de";
      case strtolower("pt"):
        return "pt";
      case strtolower("it"):
        return "it";
      case strtolower("zh-CN"):
        return "zh-CN";
      case strtolower("pl"):
        return "pl";
      case strtolower("ru"):
        return "ru";
      case strtolower("no"):
        return "no";
      case strtolower("fi"):
        return "fi";
      case strtolower("cs"):
        return "cs";
      case strtolower("ko"):
        return "ko";
      case strtolower("ja"):
        return "ja";
      case strtolower("hu"):
        return "hu";
      default:
        return "en";
    }
    
    return "en";
  }
}