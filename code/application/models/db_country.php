<?php
/**
 * @author Louis-Michel
 *
 */
class Db_country extends Model
{
  const CITY_TABLE      = 'cities2';
  const COUNTRY_TABLE   = 'countries';
  const CONTINENT_TABLE = 'continents';
  const COUNTRY_OF_CONTINENT_TABLE = 'country_continent';
  const HW_CITY_TABLE      = 'hw_city';
  const HW_COUNTRY_TABLE   = 'hw_country';

  const JS_CITIES_CACHE_DIR = "cache_queries/jscitydata";

  var $continent_fields = Array();
  var $country_fields   = Array();
  var $city_fields      = Array();

  function Db_country()
  {
      parent::Model();


      $this->db->simple_query("SET NAMES 'utf8'");

      //Initialize all langages continent fields
      $sql = "SHOW COLUMNS FROM continents WHERE Type LIKE'varchar(255)' AND Field LIKE'continent%'";
      $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
      $query = $this->db->query($sql);

      foreach ($query->result() as $row)
      {
         array_push($this->continent_fields, $row->Field);
      }

      //Initialize all langages country fields
      $sql = "SHOW COLUMNS FROM ".self::CITY_TABLE." WHERE Type LIKE'varchar(255)' AND Field LIKE'country%'";
      $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
      $query = $this->db->query($sql);

      foreach ($query->result() as $row)
      {
         array_push($this->country_fields, $row->Field);
      }

      //Initialize all langages country fields
      $sql = "SHOW COLUMNS FROM ".self::CITY_TABLE." WHERE Type LIKE'varchar(255)' AND Field LIKE'city%'";
      $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
      $query = $this->db->query($sql);

      foreach ($query->result() as $row)
      {
         array_push($this->city_fields, $row->Field);
      }

  }

  function get_continent_fields()
  {
    return $this->continent_fields;
  }

  function get_country_fields()
  {
    return $this->country_fields;
  }

  function get_city_fields()
  {
    return $this->city_fields;
  }

  function get_continent($continent, $lang = 'en')
  {

    $lang = $this->lang_code_convert($lang);

    $continent = $this->db->escape_str($continent);

    $nbcontinentfield = 0;
    foreach($this->continent_fields as $continent_field)
    {
      if($nbcontinentfield==0)
      {
        $this->db->where("LOWER(`$continent_field`) LIKE LOWER('$continent')");
      }
      else
      {
        $this->db->or_where("LOWER(`$continent_field`) LIKE LOWER('$continent')");
      }
      $nbcontinentfield++;
    }

    $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
    $query = $this->db->get(self::CONTINENT_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row_array();

      if(!empty($row["continent_".$lang]))
      {
        return $row["continent_".$lang];
      }
      elseif(!empty($row["continent_en"]))
      {
        return $row["continent_en"];
      }
    }
    log_message('info', 'Continent translation error: '.$continent);

    return stripslashes($continent);
  }

  function get_continent_of_country($country, $lang = 'en')
  {
    $lang = $this->lang_code_convert($lang);
    $country = $this->db->escape_str($country);

    $query = " SELECT * ";
    $query.= " FROM ".self::CITY_TABLE;
    $query.= " JOIN `".self::COUNTRY_OF_CONTINENT_TABLE."` ON `".self::COUNTRY_OF_CONTINENT_TABLE."`.`country_iso_code_2` = ".self::CITY_TABLE.".country_iso_code_2";
    $query.= " JOIN `".self::CONTINENT_TABLE."` ON `".self::COUNTRY_OF_CONTINENT_TABLE."`.`continent_code` = `".self::CONTINENT_TABLE."`.`continent_code`";

    $cfi = 0;
    foreach($this->country_fields as $continent_field)
    {
      if($cfi==0)
      {
        $query.= " WHERE LOWER(".self::CITY_TABLE.".`$continent_field`) LIKE LOWER('$country')";
      }
      else
      {
        $query.= "    OR LOWER(".self::CITY_TABLE.".`$continent_field`) LIKE LOWER('$country')";
      }
      $cfi++;
    }

    $query.= "    GROUP BY ".self::CITY_TABLE.".country_iso_code_2,".self::CITY_TABLE.".country_iso_code_3";
    $query.= " LIMIT 1";

    $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
    $query = $this->db->query($query);

    if ($query->num_rows() == 1)
    {
      $row = $query->row_array();
      if(!empty($row["continent_".$lang]))
      {
        return $row["continent_".$lang];
      }
      elseif(!empty($row["continent_en"]))
      {
        return $row["continent_en"];
      }
    }
    log_message('error', 'get_continent_of_country error: '.$country);

    return NULL;
  }

  function get_country_list($lang = "en",$continent_en = NULL)
  {
    $lang = $this->lang_code_convert($lang);

    $this->db->select("hw_country,country_iso_code_2,geo_longitude,geo_latitude,continent_code,continent_en,continent_".$lang." AS translated_continent");
    $this->db->join(self::CONTINENT_TABLE, self::CONTINENT_TABLE.'.continent_id = '.self::HW_COUNTRY_TABLE.'.continent_id', 'left');
    if(!empty($continent_en))
    {
      $continent_en = $this->db->escape_str($continent_en);
      $this->db->where("continent_en LIKE '$continent_en'");
    }
    $query = $this->db->get(self::HW_COUNTRY_TABLE);

    if ($query->num_rows() > 0)
    {
      return $query->result();
    }

    return NULL;
  }

  function get_country($country_search, $lang = "en")
  {

    $lang = $this->lang_code_convert($lang);
    $country_search = $this->db->escape_str($country_search);

    $nbcontinentfield = 0;
    foreach($this->country_fields as $country_field)
    {
      if($nbcontinentfield==0)
      {
        $this->db->where("LOWER(`$country_field`) LIKE LOWER('$country_search')");
      }
      else
      {
        $this->db->or_where("LOWER(`$country_field`) LIKE LOWER('$country_search')");
      }
      $nbcontinentfield++;
    }

    $this->db->group_by("country_en");

    $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
    $query = $this->db->get(self::CITY_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row_array();
      return $row["country_".$lang];
    }
    log_message('info', 'Country translation error: '.$country_search);
    return stripslashes($country_search);
  }

  function get_city_geo($country_search,$city_search)
  {
    $country_search = $this->db->escape_str($country_search);
    $city_search    = $this->db->escape_str($city_search);

    $sql_where = "( ";
    $nci = 0;
    foreach($this->city_fields as $city_field)
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
    foreach($this->country_fields as $country_field)
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

    $this->db->where($sql_where);
    $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
    $query = $this->db->get(self::CITY_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row();
      if((!empty($row->lat))&&(!empty($row->lng)))
      {
        return $row->lat.",".$row->lng;
      }
      log_message('debug', 'City geo unavailable: '.$country_search.', '.$city_search);
      return NULL;
    }
    log_message('info', 'City geo error could not find city in db: '.$country_search.', '.$city_search);
    return NULL;
  }

  function get_city($country_search,$city_search, $lang = "en")
  {

    $lang = $this->lang_code_convert($lang);

    $country_search = $this->db->escape_str($country_search);
    $city_search    = $this->db->escape_str($city_search);

    $sql_where = "( ";
    $nci = 0;
    foreach($this->city_fields as $city_field)
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
    foreach($this->country_fields as $country_field)
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

    $this->db->where($sql_where);

    $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
    $query = $this->db->get(self::CITY_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row_array();
      if(!empty($row["city_".$lang]))
      {
        return stripslashes($row["city_".$lang]);
      }
    }
    log_message('info', 'City translation error: '.$country_search.', '.$city_search);
    return stripslashes($city_search);
  }

  function get_city_link($country_search,$city_search, $lang = "en")
  {
    $lang = $this->lang_code_convert($lang);

    $country_search = $this->db->escape_str($country_search);
    $city_search    = $this->db->escape_str($city_search);

    $sql_where = "( ";
    $nci = 0;
    foreach($this->city_fields as $city_field)
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
    foreach($this->country_fields as $country_field)
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

    $this->db->where($sql_where);

    $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
    $query = $this->db->get(self::CITY_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row_array();
      if((!empty($row["city_".$lang])) &&(!empty($row["country_".$lang])) )
      {
        return $row["country_".$lang]."/".$row["city_".$lang];
      }
    }
    log_message('info', 'City translation error: '.$country_search.', '.$city_search);
    return stripslashes($country_search)."/".stripslashes($city_search);
  }

  /**
   *
   */
  function select_country($select_id,$select_name,$country_selected = "", $otherAttributes = "",$value_lang = "en", $text_lang = "fr", $no_selection_text = NULL)
  {
    $value_lang = $this->lang_code_convert($value_lang);
    $text_lang  = $this->lang_code_convert($text_lang);

    $selected = "";
    if($country_selected!="")
    {
      $selected = $country_selected;
    }

    $this->db->group_by("country_en");
    $this->db->order_by("country_".$text_lang, "ASC");
    $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
    $query = $this->db->get(self::CITY_TABLE);

    ?>
    <select <?php echo $otherAttributes; ?> name="<?php echo $select_name; ?>" id="<?php echo $select_id; ?>">
    <?php
    if(!empty($no_selection_text))
    {
      ?>
      <option <?php if(empty($selected)) echo "selected=\"selected\" "; ?>value="">----- <?php echo $no_selection_text; ?> -----</option>
      <?php
    }

    foreach ($query->result_array() as $row)
    {
      ?>
      <option <?php if(strcasecmp($selected,$row["country_".$value_lang])==0) echo "selected=\"selected\" "; ?>value="<?php echo $row["country_".$value_lang]; ?>"><?php echo $row["country_".$text_lang];?></option>
      <?php
    }
    ?>
    </select>
    <?php
  }

  function get_all_hw_cities_translated($lang = "en")
  {
    $lang = $this->lang_code_convert($lang);

    $sql = "SELECT hw_city_id,hw_country.hw_country_id,hw_city,hw_country,`city_$lang` AS display_city ,`country_$lang` AS display_country, ";
    $sql.= "       ".self::HW_COUNTRY_TABLE.".country_iso_code_2, ".self::HW_COUNTRY_TABLE.".geo_latitude AS country_geo_lat, ".self::HW_COUNTRY_TABLE.".geo_longitude AS country_geo_lng,";
    $sql.= "       ".self::CITY_TABLE.".lat AS city_geo_lat, ".self::CITY_TABLE.".lng AS city_geo_lng, ";
    $sql.= "       continent_en, `continent_".$lang."` AS translated_continent ";
    $sql.= " FROM ".self::HW_CITY_TABLE;
    $sql.= " LEFT JOIN ".self::HW_COUNTRY_TABLE." ON ".self::HW_CITY_TABLE.".hw_country_id = ".self::HW_COUNTRY_TABLE.".hw_country_id";
    $sql.= " LEFT JOIN ".self::CITY_TABLE." ON (".self::HW_CITY_TABLE.".hw_city = ".self::CITY_TABLE.".city_en AND ".self::HW_COUNTRY_TABLE.".hw_country = ".self::CITY_TABLE.".country_en)";
    $sql.= " LEFT JOIN ".self::CONTINENT_TABLE." ON ".self::CONTINENT_TABLE.".continent_id = ".self::HW_COUNTRY_TABLE.".continent_id ";
    $sql.= " ORDER BY hw_country,hw_city ASC";

    $query = $this->db->query($sql);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }

    return NULL;
  }

  function cityCountryList_DropDown_DB($lang, $addEnglishNames = FALSE,  $country = NULL)
  {
    $lang = $this->lang_code_convert($lang);

    $citylistobject = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><CityCountryList></CityCountryList>");

    $last_country_id = 0;
    $cities = NULL;
    $city = NULL;

    foreach($this->get_all_hw_cities_translated($lang) AS $hw_city)
    {

      if(($last_country_id != $hw_city->hw_country_id) ||
          ($last_country_id == 0))
      {

        $country_object = new SimpleXMLElement("<Country></Country>");
        $country_object = $citylistobject->addChild("Country", $country_object);

        $country_object->addAttribute("size", 4);
        $country_object->addChild("countryContinent", $hw_city->continent_en);
        $country_object->addChild("countryContinentTranslated", $hw_city->translated_continent);
        $country_object->addChild("countryName",      $hw_city->hw_country);

        $countryTranslated = $hw_city->display_country;
        if(empty($countryTranslated))
        {
          $countryTranslated = $this->get_country($hw_city->hw_country,$lang);
        }

        $country_object->addChild("countryNameTranslated",      $countryTranslated);
        $country_object->addChild("countryCode",      $hw_city->country_iso_code_2);

        if($addEnglishNames === TRUE)
        {
          $country_object->addChild("countrySelectText", $countryTranslated." ".$hw_city->hw_country);
        }
        else
        {
          $country_object->addChild("countrySelectText", $countryTranslated);
        }

        $country_object->addChild("countrySelectVal",      $countryTranslated);
        $country_object->addChild("countryGeoLat",      $hw_city->country_geo_lat);
        $country_object->addChild("countryGeoLng",      $hw_city->country_geo_lng);

        $cities = new SimpleXMLElement("<Cities></Cities>");
        $cities = $country_object->addChild("Cities", $cities);

        $city = new SimpleXMLElement("<City></City>");
        $city = $cities->addChild("City", $cities);
        $city->addChild("cityName", $hw_city->hw_city);

        if(!empty($hw_city->display_city))
        {
          $city->addChild("cityNameTranslated", $hw_city->display_city);
          if($addEnglishNames === TRUE)
          {
            $city->addChild("cityNameSelectText", $hw_city->display_city." ".$hw_city->hw_city);
          }
          else
          {
            $city->addChild("cityNameSelectText", $hw_city->display_city);
          }
          $city->addChild("cityNameSelectVal", $hw_city->display_city);
        }
        else
        {
          $city->addChild("cityNameSelectText", $hw_city->hw_city);
          $city->addChild("cityNameSelectVal", $hw_city->hw_city);
        }

      }
      else
      {
        //Add city to actual country
        $city = new SimpleXMLElement("<City></City>");
        $city = $cities->addChild("City", $cities);
        $city->addChild("cityName", $hw_city->hw_city);

        if(!empty($hw_city->display_city))
        {
          $city->addChild("cityNameTranslated", $hw_city->display_city);
          if($addEnglishNames === TRUE)
          {
            $city->addChild("cityNameSelectText", $hw_city->display_city." ".$hw_city->hw_city);
          }
          else
          {
            $city->addChild("cityNameSelectText", $hw_city->display_city);
          }
          $city->addChild("cityNameSelectVal", $hw_city->display_city);
        }
        else
        {
          $city->addChild("cityNameSelectText", $hw_city->hw_city);
          $city->addChild("cityNameSelectVal", $hw_city->hw_city);
        }
      }

      $last_country_id = $hw_city->hw_country_id;
    }

    return $citylistobject;
  }

  function get_js_cities_data($javascript_varname, $lang = "en", $dashEnglishNames = FALSE)
  {
    $cities_data = "";

    $previous_country = "";
    $country_line = "";

    foreach($this->get_all_hw_cities_translated($lang) as $hw_city)
    {
      $display_country = $hw_city->hw_country;
      $display_city    = $hw_city->hw_city;

      if(!empty($hw_city->display_city))    $display_city    = $hw_city->display_city;

      if(empty($hw_city->display_country))
      {
        $display_country = $this->get_country($hw_city->hw_country, $lang);
      }
      else
      {
        $display_country = $hw_city->display_country;
      }

      if($dashEnglishNames === TRUE)
      {
        if(strcasecmp($display_country, $hw_city->hw_country) != 0)
        {
          $display_country .= " ".$hw_city->hw_country;
        }

        if(strcasecmp($display_city, $hw_city->hw_city) != 0)
        {
          $display_city    .= " ".$hw_city->hw_city;
        }
      }

      if(strcmp($previous_country,$hw_city->hw_country) != 0)
      {
        if(!empty($previous_country))
        {
          //Remove last character
          $country_line = substr($country_line,0,(strlen($country_line)-1));
//          echo $country_line.")];\n";
          $cities_data.= $country_line.")];\n";
          $country_line = "";
        }

        $country_line.= $javascript_varname."['".$hw_city->hw_country."'] = ['".addslashes($display_country)."',new Array(";

        $country_line.= "'"._('Choisir la ville')."',";
        $country_line.= "'".addslashes($display_city)."',";

        $previous_country = $hw_city->hw_country;
      }
      else
      {
        $country_line.= "'".addslashes($display_city)."',";
      }
    }

    $country_line = substr($country_line,0,(strlen($country_line)-1));
//    echo $country_line.")];\n";
    $cities_data.= $country_line.")];\n";
    return $cities_data;
  }

  function fwrite_js_cities_data($javascript_varname, $lang = "en", $dashEnglishNames = FALSE)
  {
    $jsCacheFile = FCPATH.self::JS_CITIES_CACHE_DIR."/".$javascript_varname."_".$lang.".js";

    if($dashEnglishNames === TRUE)
    {
      $jsCacheFile = FCPATH.self::JS_CITIES_CACHE_DIR."/".$javascript_varname."_".$lang."-EN.js";
    }

    $filedata = $this->get_js_cities_data($javascript_varname, $lang, $dashEnglishNames);

    $this->load->helper('file');
    if ( ! write_file($jsCacheFile, $filedata ,"w+"))
    {
         log_message("error", "Unable to write the file: $jsCacheFile");
         return FALSE;
    }
    return $filedata;
  }

  function get_cached_js_cities_data($javascript_varname, $lang = "en", $dashEnglishNames = FALSE)
  {
    $jsCacheFile = FCPATH.self::JS_CITIES_CACHE_DIR."/".$javascript_varname."_".$lang.".js";
    if($dashEnglishNames === TRUE)
    {
      $jsCacheFile = FCPATH.self::JS_CITIES_CACHE_DIR."/".$javascript_varname."_".$lang."-EN.js";
    }

    if(file_exists($jsCacheFile))
    {
      return file_get_contents($jsCacheFile);
    }
    else
    {
      return $this->fwrite_js_cities_data($javascript_varname, $lang, $dashEnglishNames);
    }
  }

  function clear_js_cities_data()
  {
    $this->load->helper('file');
//    echo FCPATH.self::JS_CITIES_CACHE_DIR."/";
    delete_files(FCPATH.self::JS_CITIES_CACHE_DIR."/");
  }

  function fwrite_xml_cities_data($javascript_varname, $lang = "en", $addEnglishNames = FALSE)
  {
    $jsCacheFile = FCPATH.self::JS_CITIES_CACHE_DIR."/".$javascript_varname."_".$lang.".xml";

    if($addEnglishNames === TRUE)
    {
      $jsCacheFile = FCPATH.self::JS_CITIES_CACHE_DIR."/".$javascript_varname."_".$lang."-EN.xml";
    }

    $xmlObj = $this->cityCountryList_DropDown_DB($lang, $addEnglishNames);

    $xmlObj = preg_replace("/>\s*</",">\n<",$xmlObj->asXML());

    $this->load->helper('file');
    if ( ! write_file($jsCacheFile, $xmlObj ,"w+"))
//    if ( ! $xmlObj->asXML($jsCacheFile))
    {
         log_message("error", "Unable to write the file: $jsCacheFile");
         return FALSE;
    }
    return $xmlObj;
  }

  function get_cached_xml_cities_data($javascript_varname, $lang = "en", $addEnglishNames = FALSE)
  {
    $jsCacheFile = FCPATH.self::JS_CITIES_CACHE_DIR."/".$javascript_varname."_".$lang.".xml";
    if($addEnglishNames === TRUE)
    {
      $jsCacheFile = FCPATH.self::JS_CITIES_CACHE_DIR."/".$javascript_varname."_".$lang."-EN.xml";
    }
    if(file_exists($jsCacheFile))
    {
      return file_get_contents($jsCacheFile);
    }
    else
    {
      return $this->fwrite_xml_cities_data($javascript_varname, $lang, $addEnglishNames);
    }
  }

  function clear_xml_cities_data()
  {
    $this->load->helper('file');
//    echo FCPATH.self::JS_CITIES_CACHE_DIR."/";
    delete_files(FCPATH.self::JS_CITIES_CACHE_DIR."/");
  }

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