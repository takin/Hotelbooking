<?php
/**
 * @author Louis-Michel
 *
 */
class Db_hb_country extends Model
{
  const CITY_TABLE      = 'hb_city';
  const COUNTRY_TABLE   = 'hb_country';
  const CONTINENT_TABLE = 'continents';

  const CITY_TRANSLATION_TABLE = 'cities2';

  const NATIONALITY_TABLE = 'hb_nationality';


  const JS_CITIES_CACHE_DIR = "cache_queries/jscitydata/hb";

  function Db_hb_country()
  {
      parent::Model();
      $this->load->model('Db_country');
  }

  function get_continent_from_hb_code($continent_hb_code, $lang = "en")
  {
    $lang = $this->Db_country->lang_code_convert($lang);
    $continent_hb_code = $this->validate_hb_continent_code($continent_hb_code);
    $this->db->where("continent_hb_code",$continent_hb_code);
    $query = $this->db->get(self::CONTINENT_TABLE);

    if ($query->num_rows() > 0)
    {
      $row = $query->row();
      $rowfield = "continent_".$lang;
      return $row->$rowfield;
    }
    return NULL;

  }

  function validate_hb_continent_code($continent_hb_code)
  {
    switch(strtolower($continent_hb_code))
    {
      case strtolower("ic"):
        return "as";
      case strtolower("me"):
        return "as";
      default:
        return $continent_hb_code;
    }

  }

  function get_continent($country_search, $lang = "en")
  {
    $lang = $this->Db_country->lang_code_convert($lang);

    $country = $this->get_country($country_search);

    if(!empty($country)) return $this->get_continent_from_hb_code($country->continent_hb_code,$lang);

    return NULL;

  }

  function get_country_sys_name($country_search)
  {

    $country = $this->get_country($country_search);

    if(!empty($country)) return $country->system_name;

    return NULL;
  }

  function get_country($country_search)
  {

    $country_search = $this->db->escape_str($country_search);
    $this->db->select("*");
    $this->db->select(self::COUNTRY_TABLE.".continent_hb_code as continent_hb_code");
    $this->db->select(self::COUNTRY_TABLE.".country_iso_code_2 as country_iso_code_2");

    $this->db->join(self::CITY_TRANSLATION_TABLE,self::COUNTRY_TABLE.".lname_en = ".self::CITY_TRANSLATION_TABLE.".country_en","left");
    $this->db->join(self::CONTINENT_TABLE,self::COUNTRY_TABLE.".continent_hb_code = ".self::CONTINENT_TABLE.".continent_hb_code","left");

    $nbcontinentfield = 0;
    foreach($this->Db_country->get_country_fields() as $country_field)
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
     $this->db->or_where("LOWER(`lname_en`) LIKE LOWER('$country_search')");
    $this->db->group_by("country_en");

//    $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
    $query = $this->db->get(self::COUNTRY_TABLE);

    if ($query->num_rows() == 1)
    {
      return $query->row();
    }
    log_message('info', 'Country translation error: '.$country_search);
    return NULL;
  }

  function get_city($country_search,$city_search, $lang ="en")
  {
    $country_search = $this->db->escape_str($country_search);
    $city_search    = $this->db->escape_str($city_search);
    $base_url = strtolower(str_replace('http://', '', str_replace('www.', '', substr(base_url(), 0, -1))));

    //If city translation is not found in db try to macth english country name to api english country name

    $sql = "SELECT hb_city_data.system_name,
                   hb_city_data.hb_id,
                   hb_city_data.geo_longitude as city_geo_lng,
                   hb_city_data.geo_latitude as city_geo_lat,
                   hb_city_data.modified,
                   hb_city_data.last_search_on,
                   hb_city_data.lname_en as city_lname_en,
                   hb_country.lname_en as country_lname_en,
                   hb_country.system_name as country_system_name,
                   continents.`continent_$lang` as display_continent,
                   IFNULL(`city_$lang`,hb_city_data.city_lname_en_stripped) as display_city,
                   IFNULL(`country_$lang`,(SELECT `country_$lang` FROM cities2 WHERE LOWER(cities2.country_en) LIKE LOWER(country_lname_en) LIMIT 1)) AS display_country,
                   IFNULL(city_by_domain.description,cities2.description) as city_description,
                   IFNULL(city_by_domain.image,cities2.image) as city_image,
                   city_by_domain.city_market_code as city_code
            FROM
            (
            SELECT `hb_city`.`system_name`, `hb_city`.`hb_id`, `hb_city`.`geo_longitude`, `hb_city`.`geo_latitude`, `hb_city`.`modified`, `hb_city`.`last_search_on`,
                   IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en)as city_lname_en_stripped
                   ,hb_country_id, lname_en, hb_city_id
            FROM (`hb_city`)
            ) as hb_city_data
            LEFT JOIN `hb_country` ON `hb_city_data`.`hb_country_id` = `hb_country`.`hb_country_id`
            LEFT JOIN `cities2` ON (hb_country.lname_en = `cities2`.`country_en` AND hb_city_data.city_lname_en_stripped = cities2.city_en)
            LEFT JOIN continents ON hb_country.continent_hb_code = continents.continent_hb_code
            LEFT JOIN city_by_domain ON cities2.city_id = city_by_domain.city_id AND city_by_domain.site_domain_id = ".$this->site_domain_id."
            WHERE ( LOWER(hb_country.`lname_en`) LIKE LOWER(IFNULL((SELECT `country_en` FROM cities2 WHERE LOWER(cities2.`country_$lang`) LIKE LOWER('$country_search') LIMIT 1), '$country_search'))
                    AND LOWER(hb_city_data.`city_lname_en_stripped`) LIKE LOWER('$city_search')) ";

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
//    $this->db->model_cache_single(__CLASS__ , __FUNCTION__);
    $query = $this->db->query($sql);

    if ($query->num_rows() == 1)
    {
      return $query->row();
    }
    log_message('info', 'City translation error: '.$country_search.', '.$city_search);
    return NULL;
  }

  function get_country_list($lang = "en",$continent_en = NULL)
  {
    $lang = $this->Db_country->lang_code_convert($lang);

    $this->db->select("system_name,lname_en,".self::COUNTRY_TABLE.".continent_hb_code,country_iso_code_2,geo_longitude,geo_latitude,continent_code,continent_en,continent_".$lang." AS translated_continent");
    $this->db->join(self::CONTINENT_TABLE, self::CONTINENT_TABLE.'.continent_hb_code = '.self::COUNTRY_TABLE.'.continent_hb_code', 'right');
    if(!empty($continent_en))
    {
      $continent_en = $this->db->escape_str($continent_en);
      $this->db->where("continent_en LIKE '$continent_en'");
    }
    $query = $this->db->get(self::COUNTRY_TABLE);

    if ($query->num_rows() > 0)
    {
      return $query->result();
    }

    return NULL;
  }

  function get_all_hb_cities_translated($lang = "en")
  {
    $lang = $this->Db_country->lang_code_convert($lang);

    $sql = "SELECT hb_city_id, hb_country_id, city_system_name, country_system_name,  stripped_city_en,
                   `city_$lang` AS display_city ,
                   IF(`country_$lang` IS NULL,(SELECT `country_$lang` FROM cities2 WHERE LOWER(cities2.country_en) LIKE LOWER(country_lname_en) LIMIT 1),`country_$lang`) AS display_country,
                   city_lname_en, country_lname_en,
                   hb_city_data.country_iso_code_2, country_geo_lat, country_geo_lng,
                   hb_city_data.continent_hb_code,
                   cities2.lat AS city_geo_lat, cities2.lng AS city_geo_lng,
                   continent_en, `continent_$lang` AS translated_continent
            FROM
            (
                        SELECT hb_city_id, hb_country.hb_country_id, hb_city.system_name AS city_system_name, hb_country.system_name AS country_system_name,
                               IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en)as stripped_city_en,
                               hb_city.lname_en AS city_lname_en ,hb_country.lname_en AS country_lname_en,
                               hb_country.country_iso_code_2, hb_country.geo_latitude AS country_geo_lat, hb_country.geo_longitude AS country_geo_lng,
                               hb_country.continent_hb_code
                        FROM hb_city
                        LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
                        ORDER BY hb_country.system_name, hb_city.system_name ASC
            )
            as hb_city_data
            LEFT JOIN cities2 ON (hb_city_data.stripped_city_en = cities2.city_en AND hb_city_data.country_lname_en = cities2.country_en)
            LEFT JOIN continents ON continents.continent_hb_code = hb_city_data.continent_hb_code ";

    $query = $this->db->query($sql);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }

    return NULL;
  }


  function cityCountryList_DropDown_DB($lang, $addEnglishNames = FALSE,  $country = NULL)
  {
    $this->load->helper('xml');

    $lang = $this->Db_country->lang_code_convert($lang);

    $citylistobject = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><CityCountryList></CityCountryList>");

    $last_country_id = 0;
    $cities = NULL;
    $city = NULL;

    foreach($this->get_all_hb_cities_translated($lang) AS $hb_city)
    {

      $hb_city->country_lname_en = xml_convert($hb_city->country_lname_en);
      $hb_city->city_lname_en    = xml_convert($hb_city->city_lname_en);
      $hb_city->stripped_city_en = xml_convert($hb_city->stripped_city_en);
      $hb_city->display_country  = xml_convert($hb_city->display_country);
      $hb_city->display_city     = xml_convert($hb_city->display_city);

      if(($last_country_id != $hb_city->hb_country_id) ||
          ($last_country_id == 0))
      {

        $country_object = new SimpleXMLElement("<Country></Country>");
        $country_object = $citylistobject->addChild("Country", $country_object);

//        $country_object->addAttribute("size", 4);
        $country_object->addChild("countryContinent", $hb_city->continent_en);
        $country_object->addChild("countryContinentTranslated", $hb_city->translated_continent);
        $country_object->addChild("countryName",      $hb_city->country_lname_en);

        $countryTranslated = $hb_city->display_country;
        if(empty($countryTranslated))
        {
          $countryTranslated = $hb_city->country_lname_en;
        }

        $country_object->addChild("countryNameTranslated",      $countryTranslated);
        $country_object->addChild("countryCode",      $hb_city->country_iso_code_2);

        if($addEnglishNames === TRUE)
        {
          $country_object->addChild("countrySelectText", $countryTranslated." ".$hb_city->country_lname_en);
        }
        else
        {
          $country_object->addChild("countrySelectText", $countryTranslated);
        }

        $country_object->addChild("countrySelectVal",      $countryTranslated);
        $country_object->addChild("countryGeoLat",      $hb_city->country_geo_lat);
        $country_object->addChild("countryGeoLng",      $hb_city->country_geo_lng);

        $cities = new SimpleXMLElement("<Cities></Cities>");
        $cities = $country_object->addChild("Cities", $cities);

        $city = new SimpleXMLElement("<City></City>");
        $city = $cities->addChild("City", $cities);
        $city->addChild("cityName", $hb_city->city_system_name);

        if(!empty($hb_city->display_city))
        {
          $city->addChild("cityNameTranslated", $hb_city->display_city);
          if($addEnglishNames === TRUE)
          {
            $city->addChild("cityNameSelectText", $hb_city->display_city." ".$hb_city->stripped_city_en);
          }
          else
          {
            $city->addChild("cityNameSelectText", $hb_city->display_city);
          }
          $city->addChild("cityNameSelectVal", $hb_city->display_city);
        }
        else
        {
          $city->addChild("cityNameSelectText", $hb_city->stripped_city_en);
          $city->addChild("cityNameSelectVal", $hb_city->stripped_city_en);
        }

      }
      else
      {
        //Add city to actual country
        $city = new SimpleXMLElement("<City></City>");
        $city = $cities->addChild("City", $cities);
        $city->addChild("cityName", $hb_city->city_system_name);

        if(!empty($hb_city->display_city))
        {
          $city->addChild("cityNameTranslated", $hb_city->display_city);
          if($addEnglishNames === TRUE)
          {
            $city->addChild("cityNameSelectText", $hb_city->display_city." ".$hb_city->stripped_city_en);
          }
          else
          {
            $city->addChild("cityNameSelectText", $hb_city->display_city);
          }
          $city->addChild("cityNameSelectVal", $hb_city->display_city);
        }
        else
        {
          $city->addChild("cityNameSelectText", $hb_city->stripped_city_en);
          $city->addChild("cityNameSelectVal", $hb_city->stripped_city_en);
        }
      }

      $last_country_id = $hb_city->hb_country_id;
    }

    return $citylistobject;
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

  function update_nationality_status($sync_status)
  {
    $this->db->set('api_sync_status', $sync_status);
    $this->db->update(self::NATIONALITY_TABLE);
  }

  function delete_nationality_sync_status($sync_status = 0)
  {
    $this->db->where('api_sync_status', $sync_status);
    $this->db->delete(self::NATIONALITY_TABLE);
  }

  /**
   *  nationality langage support
   */
  function nationality_lang_code_convert($lang_code)
  {
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
      case strtolower("pl"):
        return "pl";
      case strtolower("dk"):
        return "dk";
      case strtolower("nl"):
        return "nl";
      case strtolower("nw"):
        return "nw";
      case strtolower("sw"):
        return "sw";
      default:
        return "en";
    }

    return "en";
  }

  function list_nationalities($lang)
  {
    $this->load->model('Db_country');
    $lang = $this->Db_country->lang_code_convert($lang);

    $sql = "SELECT * FROM
    				(
    				SELECT ".self::NATIONALITY_TABLE.".country_en as nationality,
            			IFNULL(".self::CITY_TRANSLATION_TABLE.".`country_$lang`,IFNULL((SELECT ".self::CITY_TRANSLATION_TABLE.".`country_$lang` FROM ".self::CITY_TRANSLATION_TABLE." WHERE LOWER(".self::CITY_TRANSLATION_TABLE.".country_en) LIKE LOWER(".self::NATIONALITY_TABLE.".country_en) LIMIT 1),".self::NATIONALITY_TABLE.".country_en)) AS nationality_translated
            FROM ".self::NATIONALITY_TABLE."
            LEFT JOIN ".self::CITY_TRANSLATION_TABLE." ON ".self::NATIONALITY_TABLE.".country_en = ".self::CITY_TRANSLATION_TABLE.".country_en
            GROUP BY ".self::NATIONALITY_TABLE.".country_en
            )
            AS translated_nationalities
            ORDER BY nationality_translated ASC";

    $query = $this->db->query($sql);

    if ($query->num_rows() > 0)
    {
      $list = $query->result();

      //Sorting of accented character is bad in MySQL so next sorting is needed
      $this->load->helper('text');
      function accent_insensitive_cmp($a, $b) {
        return strcmp(normalize_string($a->nationality_translated), normalize_string($b->nationality_translated));
      }

      usort($list, 'accent_insensitive_cmp');
      return $list;
    }
    return NULL;
  }

  function list_options_nationalities($lang , $nationality_selected = "")
  {
    $this->load->model('Db_country');
    $lang = $this->Db_country->lang_code_convert($lang);

    $nationalities = $this->list_nationalities($lang);

    if(is_null($nationalities))
    {
      $lang = "en";
      $nationalities = $this->list_nationalities($lang);
    }

    if(is_null($nationalities)) return "";

    foreach($nationalities as $nationality)
    {
      $nationality_field = "nationality_translated";
      $country_field     = "nationality";
      ?>
      <option <?php if(strcmp($nationality_selected,$nationality->$country_field)==0) echo "selected=\"selected\""; ?> value="<?php echo $nationality->$country_field;?>"><?php echo $nationality->$nationality_field;?></option>
      <?php
    }
  }

  function parse_nationalities($xml, $lang)
  {
    $xml = simplexml_load_string($xml);

    if($xml->success == false)
    {
      log_message("error", "HB API error parsing nationalities: ".$xml->errors);
      return false;
    }
    $return = true;

    $lang = $this->nationality_lang_code_convert($lang);

//    $this->update_nationality_status(0);

    foreach($xml->location as $location)
    {
      $xml_country     = trim((string)$location->country);
      $xml_nationality = trim((string)$location->nationality);

      $db_country = $this->get_country($xml_country);

      $country_ref = NULL;
      if(!is_null($db_country))
      {
        $country_ref = $db_country->hb_id;
      }

      if(is_null($country_ref))
      {
        $this->db->where("country_$lang", $xml_country);
      }
      else
      {
        $this->db->where("country_hb_id", $country_ref);
      }

      $query = $this->db->get(self::NATIONALITY_TABLE);

      if ($query->num_rows() > 0)
      {
        $this->db->set("nationality_$lang", $xml_nationality);
        $this->db->set('api_sync_status', 1);

        if(is_null($country_ref))
        {
          $this->db->where("country_$lang", $xml_country);
//          $this->db->or_where("country_$lang", (string)$location->country);
        }
        else
        {
          $this->db->set("country_$lang", $xml_country);
          $this->db->where("country_hb_id", $country_ref);
        }

        if($this->db->update(self::NATIONALITY_TABLE) === false)
        {

          $return = false;
        }
      }
      else
      {
        if(!is_null($country_ref))
        {
          $this->db->set("country_hb_id", $country_ref);
        }
        $this->db->set("country_$lang", $xml_country);
        $this->db->set("nationality_$lang", $xml_nationality);
        $this->db->set('api_sync_status', 1);

        if($this->db->insert(self::NATIONALITY_TABLE) === false)
        {
          $return = false;
        }
      }
    }

//    $this->delete_nationality_sync_status();

    return $return;
  }
}
?>