<?php
/**
 * @author Louis-Michel
 *
 */
class Db_hw_search extends Model
{
  const HW_HOSTEL_TABLE    = 'hw_hostel';
  const HW_CITY_TABLE      = 'hw_city';
  const HW_COUNTRY_TABLE   = 'hw_country';
  const CITY_TRANSLATION_TABLE = 'cities2';

  var $CI;

  function Db_hw_search()
  {
      parent::Model();
  }

  function prepare_search_terms($terms)
  {
    //Disregards some terms
    $replacements = array("hotels" => "",
    											"hotel" => "",
    											"hostels" => "",
    											"hostel" => "",
    											"youth" => "",
    											"auberges" => "",
    											"auberge" => "",
    											"auberge de jeunesse" => "",
    											"albergues" => "",
    											"albergue" => "",
    											"albergue juvenil" => "",
    											"hostal" => "",
    											"jugendherberge" => "",
    											"international" => "",
                          "ostello" => "",
                          "albergo" => "",
                          "guesthouse" => "",
                          "guest house" => "",
                          "lodge" => "",
                          "residential" => "",
                          "residence" => "",
                          "pousada" => "",
                          "pension" => "",
                          "motel" => "",
                          "motels" => "",
                          "camping" => "",
                          "backpackers" => "",
                          "backpacker" => "",
                          "bed and breakfast" => "",
                          "B&B" => "",
                          "bed & breakfast" => "",
                          "apartments" => "",
                          "appartements" => "",
                          "appartment" => "",
                          "apartment" => "",
                          "apartamentos" => "");

    foreach($replacements as $i => $r)
    {
      $terms = mb_eregi_replace ($i, $r, trim($terms));
    }

    return trim($terms);
  }

  function suggest_hw_data($terms, $lang = 'en', $limit = 5, $filter = 'all', $from_start = FALSE, $include_eng = TRUE)
  {
   if(empty($terms)) return array();

    $this->CI =& get_instance();
    $this->CI->load->model('Db_country');
    $lang = $this->CI->Db_country->lang_code_convert($lang);

    if(!empty($limit))
    {
      $limit = " LIMIT ".$limit;
    }
    else
    {
      $limit = "";
    }
    $terms = $this->prepare_search_terms($terms);

    if(empty($terms)) return array();
    $terms = explode (" ", $terms);

    $property_where = "";
    $city_where = "";
    $country_where = "";

    foreach($terms as $key => $term)
    {
      $term = $this->db->escape_like_str($term);

      if($from_start == TRUE)
      {
        $term = "$term%";
      }
      else
      {
        $term = "%$term%";
      }

      if($key > 0)
      {
        $added_term = $term;
        if($from_start == TRUE)
        {
          $added_term = "%$term";
        }
        $city_where.= " AND ((CONCAT(IFNULL(`city_$lang`,hw_city),', ',IFNULL(`country_$lang`,hw_country)) LIKE CONVERT(_utf8 '$added_term' USING utf8) COLLATE utf8_general_ci)";
        $country_where.= " AND ((CONCAT(IFNULL(`country_$lang`,hw_country),', ',`continent_$lang`) LIKE CONVERT(_utf8 '$added_term' USING utf8) COLLATE utf8_general_ci)";
        $property_where.= " AND `property_name` LIKE CONVERT(_utf8 '$added_term' USING utf8) COLLATE utf8_general_ci";
        if($include_eng === TRUE)
        {
          $city_where   .= "OR (CONCAT(IFNULL(`city_en`,hw_city),', ',IFNULL(`country_en`,hw_country)) LIKE CONVERT(_utf8 '$added_term' USING utf8) COLLATE utf8_general_ci)";
          $country_where.= "OR (CONCAT(IFNULL(`country_en`,hw_country),', ',`continent_en`) LIKE CONVERT(_utf8 '$added_term' USING utf8) COLLATE utf8_general_ci)";
        }
        $city_where   .= ")";
        $country_where.= ")";
      }
      else
      {
        $city_where.= " WHERE ((CONCAT(IFNULL(`city_$lang`,hw_city),', ',IFNULL(`country_$lang`,hw_country)) LIKE CONVERT(_utf8 '$term' USING utf8) COLLATE utf8_general_ci)";
        $country_where.= " WHERE ((CONCAT(IFNULL(`country_$lang`,hw_country),', ',`continent_$lang`) LIKE CONVERT(_utf8 '$term' USING utf8) COLLATE utf8_general_ci)";
        $property_where.= " WHERE ( `property_name` LIKE CONVERT(_utf8 '$term' USING utf8) COLLATE utf8_general_ci";

        if($include_eng === TRUE)
        {
          $city_where   .= "OR (CONCAT(IFNULL(`city_en`,hw_city),', ',IFNULL(`country_en`,hw_country)) LIKE CONVERT(_utf8 '$term' USING utf8) COLLATE utf8_general_ci)";
          $country_where.= "OR (CONCAT(IFNULL(`country_en`,hw_country),', ',`continent_en`) LIKE CONVERT(_utf8 '$term' USING utf8) COLLATE utf8_general_ci)";
        }
        $city_where   .= ")";
        $country_where.= ")";
      }
    }
    $property_where.= " )";

    $sql_cities     = " SELECT ".LINK_CITY." as link_type, NULL as property_type,NULL as property_name, NULL as property_number, NULL as imageURL, NULL as prop_lng, NULL as prop_lat,
                                hw_country,hw_city, IFNULL(`city_$lang`,hw_city) as city_lang, IFNULL(`country_$lang`,hw_country) as country_lang,
                                NULL as continent_lang
                        FROM hw_city
                        LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
                        LEFT JOIN cities2 ON (hw_city = cities2.city_en AND hw_country = cities2.country_en)
                        $city_where
                        ORDER BY city_lang ASC
                        $limit";
    $sql_countries  = " SELECT ".LINK_COUNTRY." as link_type, NULL as property_type,NULL as property_name, NULL as property_number, NULL as imageURL, NULL as prop_lng, NULL as prop_lat,
                                hw_country, NULL as hw_city, NULL as city_lang, IFNULL(`country_$lang`,hw_country) as country_lang,
                                `continent_$lang` as continent_lang
                        FROM hw_country
                        LEFT JOIN cities2 ON (hw_country = cities2.country_en)
                        LEFT JOIN continents ON continents.continent_id = hw_country.continent_id
                        $country_where
                        GROUP BY hw_country
                        ORDER BY country_lang ASC
                        $limit";
    $sql_properties = " SELECT ".LINK_PROPERTY." as link_type, property_type, property_name, property_number, imageURL, hw_hostel.geo_longitude as prop_lng, hw_hostel.geo_latitude as prop_lat,
                                hw_country, hw_city, IFNULL(`city_$lang`,hw_city) as city_lang, IFNULL(`country_$lang`,hw_country) as country_lang,
                                NULL as continent_lang
                        FROM hw_hostel
                        LEFT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id
                        LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
                        LEFT JOIN cities2 ON (hw_city = cities2.city_en AND hw_country = cities2.country_en)
                        $property_where
                        ORDER BY property_name ASC
                        $limit";

    $sql = "";
    switch($filter)
    {
      case 'cities':
        $sql = $sql_cities;
        break;
      case 'countries':
        $sql = $sql_countries;
        break;
      case 'properties':
        $sql = $sql_properties;
        break;
      case 'all':
      case 'default':
        $sql = "(
                  $sql_cities
                )
                UNION
                (
                  $sql_countries
                )
                UNION
                (
                  $sql_properties
                )";
        break;

    }
    $query = $this->db->query($sql);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return NULL;

  }

  function search_hw_data($terms, $lang = 'en', $limit = NULL)
  {
    if(empty($terms)) return array();

    $terms = $this->prepare_search_terms($terms);

    if(!empty($limit))
    {
      $limit = " LIMIT ".$limit;
    }
    else
    {
      $limit = "";
    }

    $this->CI =& get_instance();
    $this->CI->load->model('Db_country');
    $lang = $this->CI->Db_country->lang_code_convert($lang);

    $terms = explode (" ", $terms);

    if(empty($terms)) return array();

    $property_where = "";
    $city_where = "";
    $country_where = "";

    foreach($terms as $key => $term)
    {
      $term = $this->db->escape_like_str($term);


      if($key > 0)
      {
        $city_where.= " AND CONCAT(IFNULL(`city_$lang`,hw_city),', ',IFNULL(`country_$lang`,hw_country)) LIKE CONVERT(_utf8 '%$term%' USING utf8) COLLATE utf8_general_ci";
        $country_where.= " AND CONCAT(IFNULL(`country_$lang`,hw_country),', ',`continent_$lang`) LIKE CONVERT(_utf8 '%$term%' USING utf8) COLLATE utf8_general_ci";
        $property_where.= " AND CONCAT(property_name,', ',IFNULL(`city_$lang`,hw_city),', ',IFNULL(`country_$lang`,hw_country)) LIKE CONVERT(_utf8 '%$term%' USING utf8) COLLATE utf8_general_ci";
      }
      else
      {
        $city_where.= " WHERE CONCAT(IFNULL(`city_$lang`,hw_city),', ',IFNULL(`country_$lang`,hw_country)) LIKE CONVERT(_utf8 '%$term%' USING utf8) COLLATE utf8_general_ci";
        $country_where.= " WHERE CONCAT(IFNULL(`country_$lang`,hw_country),', ',`continent_$lang`) LIKE CONVERT(_utf8 '%$term%' USING utf8) COLLATE utf8_general_ci";
        $property_where.= " AND ( CONCAT(property_name,', ',IFNULL(`city_$lang`,hw_city),', ',IFNULL(`country_$lang`,hw_country)) LIKE CONVERT(_utf8 '%$term%' USING utf8) COLLATE utf8_general_ci";
      }
    }
    $property_where.= " )";

    $sql = "(
    						SELECT link_type, property_type, property_name, property_number, imageURL, prop_lng, prop_lat,
                       short_description as short_desc, NULL as translated_desc,
                       hw_country, hw_city, city_lang, country_lang,
                       continent_lang
                FROM
    						(
                  SELECT ".LINK_PROPERTY." as link_type, property_type, property_name, property_number, imageURL, hw_hostel.geo_longitude as prop_lng, hw_hostel.geo_latitude as prop_lat,
                          hw_country, hw_city, IFNULL(`city_$lang`,hw_city) as city_lang, IFNULL(`country_$lang`,hw_country) as country_lang,
                          NULL as continent_lang,
                          hw_hostel.hw_hostel_id
                  FROM hw_hostel
                  LEFT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id
                  LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
                  LEFT JOIN cities2 ON (hw_city = cities2.city_en AND hw_country = cities2.country_en)

                  WHERE TRUE
                  $property_where
                  $limit
                ) AS hw_hostel_select
                  LEFT JOIN
                  (
                      SELECT hw_hostel_description.hw_hostel_id, hw_hostel_description.langage as requested_lang, hw_hostel_description.short_description
                      FROM hw_hostel_description
                      WHERE hw_hostel_description.langage LIKE'English'
                  ) AS hostel_desc_eng
                  ON hw_hostel_select.hw_hostel_id = hostel_desc_eng.hw_hostel_id

                 -- LEFT JOIN
                 --  ( SELECT hw_hostel_description_translated.hw_hostel_id, hw_hostel_description_translated.langage as requested_lang, hw_hostel_description_translated.short_description as translated_desc
                 --     FROM hw_hostel_description as hw_hostel_description_translated
                 --     WHERE hw_hostel_description_translated.langage LIKE'German'
                 --   ) AS hostel_translated
                 --   ON hw_hostel_select.hw_hostel_id = hostel_translated.hw_hostel_id
            )
            UNION
            (
                SELECT ".LINK_CITY." as link_type, NULL as property_type,NULL as property_name, NULL as property_number, NULL as imageURL, NULL as prop_lng, NULL as prop_lat, NULL as short_desc,NULL as translated_desc,
                        hw_country,hw_city, IFNULL(`city_$lang`,hw_city) as city_lang, IFNULL(`country_$lang`,hw_country) as country_lang,
                        NULL as continent_lang
                FROM hw_city
                LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
                LEFT JOIN cities2 ON (hw_city = cities2.city_en AND hw_country = cities2.country_en)
                $city_where
                $limit
            )
            UNION
            (
                SELECT ".LINK_COUNTRY." as link_type, NULL as property_type,NULL as property_name, NULL as property_number, NULL as imageURL, NULL as prop_lng, NULL as prop_lat, NULL as short_desc,NULL as translated_desc,
                        hw_country, NULL as hw_city, NULL as city_lang, IFNULL(`country_$lang`,hw_country) as country_lang,
                        `continent_$lang` as continent_lang
                FROM hw_country
                LEFT JOIN cities2 ON (hw_country = cities2.country_en)
                LEFT JOIN continents ON continents.continent_id = hw_country.continent_id
                $country_where
                GROUP BY hw_country
                $limit
            )";

    $query = $this->db->query($sql);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return NULL;
  }
}
?>