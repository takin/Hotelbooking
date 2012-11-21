<?php
/**
 * @author Louis-Michel
 *
 */
class Db_hb_search extends Model
{
  const HW_HOSTEL_TABLE    = 'hw_hostel';
  const HW_CITY_TABLE      = 'hw_city';
  const HW_COUNTRY_TABLE   = 'hw_country';
  const CITY_TRANSLATION_TABLE = 'cities2';

  var $CI;

  function Db_hb_search()
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

  function suggest_hb_data($terms, $lang = 'en', $limit = 5, $filter = 'all', $from_start = FALSE, $include_eng = TRUE)
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

        $city_where.= " AND ((CONCAT(IFNULL(`city_$lang`,hb_city.lname_en),', ',IFNULL(`country_$lang`,hb_country.lname_en)) LIKE CONVERT(_utf8 '$added_term' USING utf8) COLLATE utf8_general_ci)";
        $country_where.= " AND ((CONCAT(IFNULL(`country_$lang`,hb_country.lname_en),', ',`continent_$lang`) LIKE CONVERT(_utf8 '$added_term' USING utf8) COLLATE utf8_general_ci)";
        $property_where.= " AND `property_name` LIKE CONVERT(_utf8 '$added_term' USING utf8) COLLATE utf8_general_ci";
        if($include_eng === TRUE)
        {
          $city_where   .= "OR (CONCAT(IFNULL(`city_en`,hb_city.lname_en),', ',IFNULL(`country_en`,hb_country.lname_en)) LIKE CONVERT(_utf8 '$added_term' USING utf8) COLLATE utf8_general_ci)";
          $country_where.= "OR (CONCAT(IFNULL(`country_en`,hb_country.lname_en),', ',`continent_en`) LIKE CONVERT(_utf8 '$added_term' USING utf8) COLLATE utf8_general_ci)";
        }
        $city_where   .= ")";
        $country_where.= ")";

      }
      else
      {
        $city_where.= " WHERE ((CONCAT(IFNULL(`city_$lang`,hb_city.lname_en),', ',IFNULL(`country_$lang`,hb_country.lname_en)) LIKE CONVERT(_utf8 '$term' USING utf8) COLLATE utf8_general_ci)";
        $country_where.= " WHERE ((CONCAT(IFNULL(`country_$lang`,hb_country.lname_en),', ',`continent_$lang`) LIKE CONVERT(_utf8 '$term' USING utf8) COLLATE utf8_general_ci)";
        $property_where.= " WHERE ( `property_name` LIKE CONVERT(_utf8 '$term' USING utf8) COLLATE utf8_general_ci";
        if($include_eng === TRUE)
        {
          $city_where   .= "OR (CONCAT(IFNULL(`city_en`,hb_city.lname_en),', ',IFNULL(`country_en`,hb_country.lname_en)) LIKE CONVERT(_utf8 '$term' USING utf8) COLLATE utf8_general_ci)";
          $country_where.= "OR (CONCAT(IFNULL(`country_en`,hb_country.lname_en),', ',`continent_en`) LIKE CONVERT(_utf8 '$term' USING utf8) COLLATE utf8_general_ci)";
        }
        $city_where   .= ")";
        $country_where.= ")";
      }
    }
    $property_where.= " )";

    $sql_cities     = " SELECT ".LINK_CITY." as link_type, NULL as property_type,NULL as property_name, NULL as property_number, NULL as imageURL, NULL as prop_lng, NULL as prop_lat,
                                hb_country.lname_en as hb_country, IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) as hb_city,
                                 IFNULL(`city_$lang`,IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en)) as city_lang,
                                IFNULL(`country_$lang`,hb_country.lname_en) as country_lang,
                                NULL as continent_lang
                        FROM hb_city
                        LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
                        LEFT JOIN cities2 ON (IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) = cities2.city_en AND hb_country.lname_en = cities2.country_en)
                        $city_where
                        ORDER BY city_lang ASC
                        $limit";
    $sql_countries  = " SELECT ".LINK_COUNTRY." as link_type, NULL as property_type,NULL as property_name, NULL as property_number, NULL as imageURL, NULL as prop_lng, NULL as prop_lat,
                                hb_country.lname_en as hb_country, NULL as hb_city,
                                NULL as city_lang,
                                IFNULL(`country_$lang`,hb_country.lname_en) as country_lang,
                                `continent_$lang` as continent_lang
                        FROM hb_country
                        LEFT JOIN cities2 ON (hb_country.lname_en = cities2.country_en)
                        LEFT JOIN continents ON continents.continent_hb_code = hb_country.continent_hb_code
                        $country_where
                        GROUP BY hb_country.lname_en
                        ORDER BY country_lang ASC
                        $limit";
   $sql_properties = " SELECT ".LINK_PROPERTY." as link_type, property_type, property_name, property_number, NULL as imageURL, hb_hostel.geo_longitude as prop_lng, hb_hostel.geo_latitude as prop_lat,
                            hb_country.lname_en as hb_country, IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) as hb_city,
                            IFNULL(`city_$lang`,IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en)) as city_lang,
                            IFNULL(`country_$lang`,hb_country.lname_en) as country_lang,
                            NULL as continent_lang
                        FROM hb_hostel
                        LEFT JOIN hb_city ON hb_hostel.city_hb_id = hb_city.hb_id
                        LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
                        LEFT JOIN cities2 ON (IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) = cities2.city_en AND hb_country.lname_en = cities2.country_en)
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

  function search_hb_data($terms, $lang = 'en', $limit = NULL)
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
        $city_where.= " AND CONCAT(IFNULL(`city_$lang`,city_stripped),', ',IFNULL(`country_$lang`,hb_country.lname_en)) LIKE CONVERT(_utf8 '%$term%' USING utf8) COLLATE utf8_general_ci";
        $country_where.= " AND CONCAT(IFNULL(`country_$lang`,hb_country.lname_en),', ',`continent_$lang`) LIKE CONVERT(_utf8 '%$term%' USING utf8) COLLATE utf8_general_ci";
        $property_where.= " AND CONCAT(property_name,', ',IFNULL(`city_$lang`,city_stripped),', ',IFNULL(`country_$lang`,hb_country.lname_en)) LIKE CONVERT(_utf8 '%$term%' USING utf8) COLLATE utf8_general_ci";
      }
      else
      {
        $city_where.= " WHERE CONCAT(IFNULL(`city_$lang`,city_stripped),', ',IFNULL(`country_$lang`,hb_country.lname_en)) LIKE CONVERT(_utf8 '%$term%' USING utf8) COLLATE utf8_general_ci";
        $country_where.= " WHERE CONCAT(IFNULL(`country_$lang`,hb_country.lname_en),', ',`continent_$lang`) LIKE CONVERT(_utf8 '%$term%' USING utf8) COLLATE utf8_general_ci";
        $property_where.= " AND ( CONCAT(property_name,', ',IFNULL(`city_$lang`,IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en)),', ',IFNULL(`country_$lang`,hb_country.lname_en)) LIKE CONVERT(_utf8 '%$term%' USING utf8) COLLATE utf8_general_ci";
      }
    }
    $property_where.= " )";

    $sql = "(
    						SELECT link_type, property_type, property_name, property_number, imageURL, prop_lng, prop_lat,
                       short_description as short_desc, NULL as translated_desc,
                       country, city, city_lang, country_lang,
                       continent_lang
                FROM
                (
                  SELECT ".LINK_PROPERTY." as link_type, property_type, property_name, property_number, NULL as imageURL, hb_hostel.geo_longitude as prop_lng, hb_hostel.geo_latitude as prop_lat,
                          hb_country.lname_en as country, IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) as city, IFNULL(`city_$lang`,IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en)) as city_lang, IFNULL(`country_$lang`,hb_country.lname_en) as country_lang,
              NULL as continent_lang,
                          hb_hostel.hb_hostel_id
                  FROM hb_hostel
                  LEFT JOIN hb_city ON hb_hostel.city_hb_id = hb_city.hb_id
                  LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
                  LEFT JOIN cities2 ON (IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) = cities2.city_en AND hb_country.lname_en = cities2.country_en)

                  WHERE TRUE
                  $property_where
                  $limit
                ) AS hb_hostel_select
                LEFT JOIN
                (
                    SELECT hb_hostel_description.short_description,hb_hostel_description.hostel_hb_id
                    FROM hb_hostel_description
                    WHERE hb_hostel_description.language LIKE'en'
                ) AS hostel_desc_eng
                ON hb_hostel_select.property_number = hostel_desc_eng.hostel_hb_id
            )
            UNION
            (
                SELECT ".LINK_CITY." as link_type, NULL as property_type,NULL as property_name, NULL as property_number, NULL as imageURL, NULL as prop_lng, NULL as prop_lat, NULL as short_desc,NULL as translated_desc,
                        hb_country.lname_en as country,city_stripped as city, IFNULL(`city_de`,city_stripped) as city_lang, IFNULL(`country_de`,hb_country.lname_en) as country_lang,
                        NULL as continent_lang
                FROM
                	(
                      SELECT *,IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) as city_stripped
                      FROM hb_city
                  ) as hb_city_stripped
                LEFT JOIN hb_country ON hb_city_stripped.hb_country_id = hb_country.hb_country_id
                LEFT JOIN cities2 ON (hb_city_stripped.city_stripped = cities2.city_en AND hb_country.lname_en = cities2.country_en)
                $city_where
                $limit
            )
            UNION
            (
                SELECT ".LINK_COUNTRY." as link_type, NULL as property_type,NULL as property_name, NULL as property_number, NULL as imageURL, NULL as prop_lng, NULL as prop_lat, NULL as short_desc,NULL as translated_desc,
                        hb_country.lname_en as country, NULL as hw_city, NULL as city_lang, IFNULL(`country_$lang`,hb_country.lname_en) as country_lang,
                        `continent_$lang` as continent_lang
                FROM hb_country
                LEFT JOIN cities2 ON (hb_country.lname_en = cities2.country_en)
                LEFT JOIN continents ON continents.continent_hb_code = hb_country.continent_hb_code
                $country_where
                GROUP BY hb_country.lname_en
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