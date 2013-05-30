<?php
class Db_hostels
{
  const HOSTELS_TABLE = 'hw_hostels_old';
  const HW_HOSTEL_TABLE = 'hw_hostel';


  var $db = "";

  function Db_hostels($conn)
  {
    $this->db = $conn;
  }

  function get_all_hostels()
  {
    $query = " SELECT property_number,property_name,property_type";
    $query.= " FROM ".self::HW_HOSTEL_TABLE;
    $query.= " ORDER BY property_name ASC";

    $query = $this->db->get_results($query);

    return $query;
  }

  function get_all_hb_hostels()
  {
    $query = " SELECT property_number,property_name,property_type";
    $query.= " FROM hb_hostel";
    $query.= " ORDER BY property_name ASC";

    $query = $this->db->get_results($query);

    return $query;
  }

  function get_city_hostels_stats($country, $city, $currency)
  {
    $country = $this->db->escape($country);
    $city    = $this->db->escape($city);

    $query = "SELECT hw_country as country_en ,hw_city as city_en,";
    $query.= "       MIN(hw_hostel_price.bed_price) AS city_min_price,";
    $query.= "       hw_hostel_price.currency_price as city_price_currency, count(*) as property_count";
    $query.= " FROM ".self::HW_HOSTEL_TABLE;
    $query.= " LEFT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id";
    $query.= " LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id";
    $query.= " LEFT JOIN hw_hostel_price ON hw_hostel.hw_hostel_id = hw_hostel_price.hw_hostel_id";
    $query.= " WHERE (hw_hostel_price.currency_price LIKE'$currency' OR hw_hostel_price.currency_price IS NULL)";
    $query.= "   AND hw_country LIKE'$country'";
    $query.= "   AND hw_city    LIKE'$city'";
    $query.= " GROUP BY hw_country,hw_city";

    $query = $this->db->get_row($query);

    return $query;
  }

  function get_city_hb_hostels_stats($country, $city, $currency)
  {
    $country = $this->db->escape($country);
    $city    = $this->db->escape($city);

    $query = "SELECT hb_country.lname_en as country_en, hb_city.lname_en as city_en,
                      MIN(hb_hostel_price.bed_price) AS city_min_price,
                     hb_hostel_price.currency_code as city_price_currency, count(*) as property_count

              FROM `hb_hostel`
              LEFT JOIN hb_city ON hb_hostel.city_hb_id = hb_city.hb_id
              LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
              LEFT JOIN hb_hostel_price ON hb_hostel.property_number = hb_hostel_price.hostel_hb_id
              WHERE (hb_hostel_price.currency_code LIKE'$currency' OR hb_hostel_price.currency_code IS NULL)
                AND hb_country.lname_en LIKE'$country'
                AND hb_city.lname_en    LIKE'$city'
              GROUP BY hb_country.lname_en,hb_city.lname_en";

    $query = $this->db->get_row($query);

    return $query;
  }

  function get_top_hw_hostels($booker_country_code = "", $currency_code = "EUR", $lang = "en", $include_test_bookings = false, $domain = "", $top_count = 6)
  {

    $since_date = mktime(0, 0, 0, date("m"), date("d")-7, date("Y"));
    if($include_test_bookings == TRUE)
    {
      $since_date = mktime(0, 0, 0, date("m"), date("d"), date("Y")-1);
    }

    $since_date = date("Y-m-d",$since_date);


    $api_lang = $this->db->escape($this->hw_lang_code_convert($lang));

    $currency = $this->db->escape($currency);

    if($include_test_bookings == true)
    {
      $include_test_bookings = "";
    }
    else
    {
      $include_test_bookings = "AND customer_booking_reference NOT LIKE '%TEST%' AND (test_booked != 1 OR (test_booked IS NULL))";
    }

    if(!empty($domain))
    {
      $domain = $this->db->escape($domain);
      $domain = "AND LOWER(site_domain) LIKE LOWER('$domain')";
    }

    $query = "SELECT API_booked,
                         site_domain_id, site_domain,
                         property_city,
                         property_country,
                         translated_city,
                         translated_country,
              --           translated_continent,
              --           continent_en,
                         property_booking_count,
                         top_hostel_of_city,
                         top_hostel_of_city as property_number,
                         hw_hostel.property_type as property_type,
                         hw_hostel.property_name as property_name,
                         hw_hostel.property_number as property_number,
                         hw_hostel.imageURL as image_url,
                         short_description as hostel_desc_en,
                         translated_desc,
                         bed_price as min_price,
                         hw_hostel_price.currency_price as price_currency,
                         (
                            SELECT hw_hostel_id
                            FROM
                                (
                                SELECT count(DISTINCT currency_price) as price_count, hw_hostel_id
                                FROM hw_hostel_price
                                GROUP BY hw_hostel_id
                                ORDER BY price_count DESC
                                LIMIT 1
                                ) as converting_hostel
                        ) as converting_hostel_id,
                        ROUND(bed_price/
                        (
                            SELECT bed_price
                             FROM hw_hostel_price
                             WHERE (hw_hostel_id = converting_hostel_id AND currency_price LIKE(price_currency)) LIMIT 1
                        )
                        *
                        (
                            SELECT bed_price
                             FROM hw_hostel_price
                             WHERE (hw_hostel_id = converting_hostel_id AND currency_price LIKE'$currency_code') LIMIT 1
                        ) , 2 ) as converted_price,
                        '$currency_code' as converted_currency
              FROM
              (
                  SELECT API_booked,
                         site_domain_id, site_domain,
                         property_city,
                         property_country,
                         `city_$lang` as translated_city,
                         `country_$lang` as translated_country,
              --           translated_continent,
               --          continent_en,
                         property_booking_count,
                         (
                             SELECT  transactions_hostelworld.property_number
                              FROM transactions_hostelworld
                              LEFT JOIN hw_hostel ON transactions_hostelworld.property_number = hw_hostel.property_number
                              LEFT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id
                              LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
                              LEFT JOIN continents ON continents.continent_id = hw_country.continent_id
                              WHERE  LOWER(API_booked) LIKE LOWER('hw')
                                 $include_test_bookings
                                 AND LOWER(hw_city.hw_city) LIKE LOWER(property_city)
                                 AND LOWER(hw_country.hw_country) LIKE LOWER(property_country)
                                 AND DATE(booking_time) > '$since_date'
                                GROUP BY transactions_hostelworld.property_number
                               ORDER BY count(*) DESC
                               LIMIT 1
                         ) as top_hostel_of_city


                  FROM
                  (
                  SELECT API_booked, transactions_hostelworld.site_domain_id, site_domain,
                         hw_city.hw_city as property_city,
                         hw_country.hw_country as property_country,
              --           `continent_fr` as translated_continent,
              --           `continent_en`,
                         count(*) as property_booking_count
                  FROM transactions_hostelworld
                  LEFT JOIN site_domains ON transactions_hostelworld.site_domain_id = site_domains.site_domain_id
                  LEFT JOIN hw_hostel ON transactions_hostelworld.property_number = hw_hostel.property_number
                  LEFT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id
                  LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
              --    LEFT JOIN continents ON continents.continent_id = hw_country.continent_id
                  WHERE  LOWER(API_booked) LIKE LOWER('hw')
                     $include_test_bookings
                     $domain
                     AND hw_city.hw_city IS NOT NULL
                     AND DATE(booking_time) > '$since_date'
                    --  AND LOWER(`continent_en`) LIKE LOWER('asia')

                   GROUP BY property_country, property_city
                   ORDER BY property_booking_count DESC
                   LIMIT $top_count
                  ) as top_cities
                  LEFT JOIN cities2 ON (top_cities.property_city = cities2.city_en AND top_cities.property_country = cities2.country_en)
              ) top_cities_with_top_hostel
              LEFT JOIN hw_hostel ON top_cities_with_top_hostel.top_hostel_of_city = hw_hostel.property_number
              LEFT JOIN hw_hostel_price ON hw_hostel.hw_hostel_id = hw_hostel_price.hw_hostel_id
              LEFT JOIN hw_hostel_description as base_desc_table ON hw_hostel.hw_hostel_id = base_desc_table.hw_hostel_id
              LEFT JOIN
                ( SELECT hw_hostel_id,langage as requested_lang,short_description as translated_desc FROM hw_hostel_description
                  WHERE  hw_hostel_description.langage LIKE'$api_lang'
                ) AS hostel_translated
                ON hw_hostel.hw_hostel_id = hostel_translated.hw_hostel_id
              WHERE base_desc_table.langage = 'English'
              AND hw_hostel_price.currency_price = 'EUR'";

    //Booker country assumes to be always the same no longer in key
    //-- is there to use cache created with old key
    $generic_key_var = "$currency_code-$lang-$api_lang-$include_test_bookings-$domain--$top_count";

    $results = $this->get_db_results_with_cached("tophostelhw_", $query, $generic_key_var,$currency_code, TRUE);

    return $results;
  }

  function get_top_hb_hostels($booker_country_code = "", $currency_code = "EUR", $lang = "en", $include_test_bookings = false, $domain = "", $top_count = 6)
  {
    $since_date = mktime(0, 0, 0, date("m"), date("d")-7, date("Y"));
    if($include_test_bookings == TRUE)
    {
      $since_date = mktime(0, 0, 0, date("m"), date("d"), date("Y")-1);
    }

    $since_date = date("Y-m-d",$since_date);

    $lang     = $this->db->escape($lang);
    $currency = $this->db->escape($currency);

    $api_lang = $this->db->escape($this->hb_lang_code_convert($lang));

    //force test booking includes for starting the site
    $include_test_bookings = true;
    if($include_test_bookings == true)
    {
      $include_test_bookings = "";
    }
    else
    {
      $include_test_bookings = "AND customer_booking_reference NOT LIKE '%TEST%' AND (test_booked != 1 OR (test_booked IS NULL))";
    }

    if(!empty($domain))
    {
      $domain = $this->db->escape($domain);
      $domain = "AND LOWER(site_domain) LIKE LOWER('$domain')";
    }

    $query = "SELECT API_booked,
                     site_domain_id, site_domain,
                     property_city,
                     property_country,
                     translated_city,
                     translated_country,
               --      translated_continent,
               --      continent_en,
                     property_booking_count,
                     top_hostel_of_city,
                     top_hostel_of_city as property_number,
                     hb_hostel.property_type as property_type,
                     hb_hostel.property_name as property_name,
                     short_description as hostel_desc_en,
                     translated_desc,
                     bed_price as min_price,
                     hb_hostel_price.currency_code as price_currency,
                    ROUND( bed_price*
                           (SELECT hb_equivalent FROM currencies WHERE currency_code = '$currency_code')
                           /
                           (SELECT hb_equivalent FROM currencies WHERE currency_code = price_currency)
                           ,2) as converted_price,
                    '$currency_code' as converted_currency,
                    (IFNULL((SELECT hb_hostel_image.url FROM hb_hostel_image WHERE  hb_hostel_image.hostel_hb_id = hb_hostel.property_number AND hb_hostel_image.url LIKE'%-1.jpg' LIMIT 1),
                            (SELECT hb_hostel_image.url FROM hb_hostel_image WHERE  hb_hostel_image.hostel_hb_id = hb_hostel.property_number LIMIT 1))) as image_url
              FROM
              (
                  SELECT API_booked,
                         site_domain_id, site_domain,
                         property_city_hb_id,
                         property_city,
                         property_country,
                         `city_$lang` as translated_city,
                         `country_$lang` as translated_country,
                  --       translated_continent,
                  --       continent_en,
                         property_booking_count,
                         (
                             SELECT  transactions_hostelworld.property_number
                              FROM transactions_hostelworld
                              LEFT JOIN site_domains ON transactions_hostelworld.site_domain_id = site_domains.site_domain_id
                              LEFT JOIN hb_hostel ON transactions_hostelworld.property_number = hb_hostel.property_number
                              LEFT JOIN hb_city ON hb_hostel.city_hb_id = hb_city.hb_id
                              LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
                 --             LEFT JOIN continents ON continents.continent_hb_code = hb_country.continent_hb_code
                              WHERE  LOWER(API_booked) LIKE LOWER('hb')
                                $include_test_bookings
                                 AND hb_city.hb_id = property_city_hb_id
                                 AND DATE(booking_time) > '$since_date'
                                GROUP BY transactions_hostelworld.property_number
                               ORDER BY count(*) DESC
                               LIMIT 1
                         ) as top_hostel_of_city

                  FROM
                  (
                  SELECT API_booked, transactions_hostelworld.site_domain_id, site_domain,
                         IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en)as property_city,
                         hb_country.lname_en as property_country,
              			hb_city.hb_id as property_city_hb_id,
              --           `continent_fr` as translated_continent,
              --           `continent_en`,
                         count(*) as property_booking_count
                  FROM transactions_hostelworld
                  LEFT JOIN site_domains ON transactions_hostelworld.site_domain_id = site_domains.site_domain_id
                  LEFT JOIN hb_hostel ON transactions_hostelworld.property_number = hb_hostel.property_number
                  LEFT JOIN hb_city ON hb_hostel.city_hb_id = hb_city.hb_id
                  LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
              --    LEFT JOIN continents ON continents.continent_hb_code = hb_country.continent_hb_code
                  WHERE  LOWER(API_booked) LIKE LOWER('hb')
                    $include_test_bookings
                    $domain
                   -- AND hb_city.lname_en IS NOT NULL
                    --  AND LOWER(`continent_en`) LIKE LOWER('asia')
                      AND DATE(booking_time) > '$since_date'
                   GROUP BY property_city_hb_id
                   ORDER BY property_booking_count DESC
                   LIMIT $top_count
                  ) as top_cities
                  LEFT JOIN cities2 ON (top_cities.property_city = cities2.city_en AND top_cities.property_country = cities2.country_en)
              ) AS top_cities_with_top_hostel

              LEFT JOIN hb_hostel ON top_cities_with_top_hostel.top_hostel_of_city = hb_hostel.property_number
              LEFT JOIN hb_hostel_price ON hb_hostel.property_number = hb_hostel_price.hostel_hb_id
              LEFT JOIN hb_hostel_description as base_desc_table ON hb_hostel.property_number = base_desc_table.hostel_hb_id
              LEFT JOIN
                ( SELECT hostel_hb_id,language as requested_lang,short_description as translated_desc FROM hb_hostel_description
                  WHERE  hb_hostel_description.language LIKE'$api_lang'
                ) AS hostel_translated
                ON hb_hostel.property_number = hostel_translated.hostel_hb_id
              WHERE base_desc_table.language = 'en'
              AND hb_hostel_price.currency_code = 'EUR'
    					GROUP BY top_hostel_of_city
              ORDER BY property_booking_count DESC, min_price ASC";

    //Booker country assumes to be always the same no longer in key
    //-- is there to use cache created with old key
    $generic_key_var = "$currency_code-$lang-$api_lang-$include_test_bookings-$domain--$top_count";

    $results = $this->get_db_results_with_cached("tophostelhb_", $query, $generic_key_var, $currency_code, TRUE);

    return $results;
  }

  function get_db_results_with_cached($key_prefix, $query, $generic_key_var, $currency_code, $startProcess)
  {
    $results = array();
    $needReload = FALSE;

    $last_week_date = mktime(0, 0, 0, date("m"), date("d")-7, date("Y"));
    $old_key_var = "$generic_key_var-".date("Y-W",$last_week_date);
    $key_var     = "$generic_key_var-".date("Y-W");

    $generic_cache_key = $key_prefix.md5($generic_key_var);
    $old_cache_key = $key_prefix.md5($old_key_var);
    $cache_key     = $key_prefix.md5($key_var);

   //If forcing refresh of caching
    if (!empty($_GET['cacherun']) && ($_GET['cacherun'] == 'run') )
    {
      $results = $this->db->get_results($query);
      set_transient( $cache_key, $results, 0);
      set_transient( $generic_cache_key, $results, 0);

      //delete old cache key no more useful
      if(get_transient( $old_cache_key ) !== false)
      {
        delete_transient($old_cache_key);
      }
    }
    //no cache for the current key
    elseif ( false === ( $results = get_transient( $cache_key ) ) )
    {
      //no cache for the previous key
      if( false === ( $results = get_transient( $old_cache_key ) ) )
      {
        //no cache for the generic key
        if( false === ( $results = get_transient( $generic_cache_key ) ) )
        {
          //for now store the previous week result while the cache is updated
          set_transient( $cache_key, $results, 0);

          $needReload = TRUE;
        }
      }
      //create cache for this week and set for now last week cache
      else
      {
        //for now store the previous week result while the cache is updated
        set_transient( $cache_key, $results, 0);

        $needReload = TRUE;
      }
    }

    if (($startProcess) && ($needReload))
    {
      $wgetOptions = "";

      if ((isset($_SERVER['PHP_AUTH_USER'])) && (isset($_SERVER['PHP_AUTH_PW'])))
      {
        $wgetOptions = "--http-user=user=".$_SERVER['PHP_AUTH_USER']." --http-password=password=".$_SERVER['PHP_AUTH_PW'];
      }

      //start parallel process to load new key in DB
      //TONOTICE prevent more than one process like this to run?????? check to see if already running before?
      //TONOTICE function should be independant from URL now only on homepage
      // LIKE adding $_SERVER["REQUEST_URI"] at the end and then append to query string if any
      $cmd = "/usr/bin/wget ".$wgetOptions." \"http://".$_SERVER["HTTP_HOST"] ."/?currency=".$currency_code."&cacherun=run\" -O ".CI_ABSPATH."cache_queries/cache_processes/last_index_cached.html";
      $outputfile = CI_ABSPATH."cache_queries/cache_processes/cachinghomepage.html";
      $pidfile    = CI_ABSPATH."cache_queries/cache_processes/cachinghomepagePID.txt";

      $cmd = sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile);
      exec($cmd);
    }

    return $results;
  }

  function get_top_hostels($API = "hw", $user_country_code = "", $currency_code = "EUR", $lang = "en", $include_test_bookings = false, $domain = "", $top_count = 6)
  {
    $tophostels = array();
    if(strcasecmp($API,"hb") == 0)
    {
      $tophostels = $this->get_top_hb_hostels($user_country_code, $currency_code, $lang, $include_test_bookings, $domain, $top_count);
      if(empty($tophostels))
      {
        $tophostels = $this->get_top_hb_hostels("", $currency_code, $lang, $include_test_bookings, $domain, $top_count);
      }
      return $tophostels;
    }
    else
    {
      $tophostels = $this->get_top_hw_hostels($user_country_code, $currency_code, $lang, $include_test_bookings, $domain, $top_count);
      if(empty($tophostels)  || count($tophostels) < 4)
      {
        $tophostels = $this->get_top_hw_hostels("", $currency_code, $lang, $include_test_bookings, $domain, $top_count);
      }
      return $tophostels;
    }

    return $this->get_top_hw_hostels($user_country_code, $currency_code, $lang, $include_test_bookings, $domain, $top_count);
  }

  function get_hw_top_cities_of_continent($booker_country_code = "", $currency_code = "EUR", $lang = "en", $continent_en = "", $include_test_bookings = false,$domain = "", $top_count = 4)
  {
    $since_date = mktime(0, 0, 0, date("m")-6, date("d"),   date("Y"));
    $since_date = date("Y-m-d",$since_date);

    $lang         = $this->db->escape($lang);
    $currency_code     = $this->db->escape($currency_code);

    $booker_country = "";
    if(!empty($booker_country_code))
    {
      $booker_country_code = $this->db->escape($booker_country_code);

      $query = "SELECT country_en FROM cities2 WHERE LOWER(country_iso_code_2) LIKE LOWER('$booker_country_code') GROUP BY country_iso_code_2 LIMIT 1";
      $booker_country_code = $this->db->get_var($query);

      if(!empty($booker_country_code))
      {
        $booker_country = "AND LOWER(home_country) LIKE LOWER('$booker_country_code') ";
      }
    }

    if(!empty($domain))
    {
      $domain = $this->db->escape($domain);
      $domain = "AND LOWER(site_domain) LIKE LOWER('$domain')";
    }

    if(!empty($continent_en))
    {
      $continent_en = $this->db->escape($continent_en);
      $continent_en = "AND LOWER(`continent_en`) LIKE LOWER('$continent_en')";
    }


    if($include_test_bookings == true)
    {
      $include_test_bookings = "";
    }
    else
    {
      $include_test_bookings = "AND customer_booking_reference NOT LIKE '%TEST%' AND (test_booked != 1 OR (test_booked IS NULL))";
    }

    $query = "SELECT API_booked,
                 site_domain_id, site_domain,
                 property_city,
                 property_country,
                 `city_$lang` as translated_city,
                 `country_$lang` as translated_country,
                 translated_continent,
                 continent_en,
                 property_booking_count,
                 (
                     SELECT  MIN(hw_hostel_price.bed_price) AS city_min_price
                         FROM `hw_hostel`
                      LEFT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id
                      LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
                      LEFT JOIN hw_hostel_price ON hw_hostel.hw_hostel_id = hw_hostel_price.hw_hostel_id
                      WHERE ((hw_hostel_price.currency_price LIKE'EUR')
                        AND hw_country LIKE(property_country)
                        AND hw_city    LIKE(property_city))
                        AND (hw_hostel_price.bed_price > 0)
                      GROUP BY hw_country,hw_city
                 ) as city_min_price,
                  (
                      SELECT hw_hostel_id
                      FROM
                          (
                          SELECT count(DISTINCT currency_price) as price_count, hw_hostel_id
                          FROM hw_hostel_price
                          GROUP BY hw_hostel_id
                          ORDER BY price_count DESC
                          LIMIT 1
                          ) as converting_hostel
                  ) as converting_hostel_id,
                  ROUND(
                  (
                     SELECT  MIN(hw_hostel_price.bed_price) AS city_min_price
                      FROM `hw_hostel`
                      LEFT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id
                      LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
                      LEFT JOIN hw_hostel_price ON hw_hostel.hw_hostel_id = hw_hostel_price.hw_hostel_id
                      WHERE ((hw_hostel_price.currency_price LIKE'EUR')
                        AND hw_country LIKE(property_country)
                        AND hw_city    LIKE(property_city))
                        AND (hw_hostel_price.bed_price > 0)
                      GROUP BY hw_country,hw_city
                 )
                 /
                  (
                      SELECT bed_price
                       FROM hw_hostel_price
                       WHERE (hw_hostel_id = converting_hostel_id AND currency_price LIKE('EUR')) LIMIT 1
                  )
                  *
                  (
                      SELECT bed_price
                       FROM hw_hostel_price
                       WHERE (hw_hostel_id = converting_hostel_id AND currency_price LIKE'$currency_code') LIMIT 1
                  ) , 2 ) as converted_city_min_price,
                  '$currency_code' as converted_currency
              FROM
              (
              SELECT API_booked, transactions_hostelworld.site_domain_id, site_domain,
                     hw_city.hw_city as property_city,
                     hw_country.hw_country as property_country,
                     `continent_$lang` as translated_continent,
                     `continent_en`,
                     count(*) as property_booking_count
              FROM transactions_hostelworld
              LEFT JOIN site_domains ON transactions_hostelworld.site_domain_id = site_domains.site_domain_id
              LEFT JOIN hw_hostel ON transactions_hostelworld.property_number = hw_hostel.property_number
              LEFT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id
              LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
              LEFT JOIN continents ON continents.continent_id = hw_country.continent_id
              WHERE  LOWER(API_booked) LIKE LOWER('hw')
                $include_test_bookings
                $domain
                AND hw_city.hw_city IS NOT NULL
                $continent_en
                $booker_country
                AND DATE(booking_time) > '$since_date'
              GROUP BY property_country, property_city
              ORDER BY property_booking_count DESC
              LIMIT $top_count
              ) as top_cities
              LEFT JOIN cities2 ON (top_cities.property_city = cities2.city_en AND top_cities.property_country = cities2.country_en)";

    $generic_key_var = "$currency_code-$lang-$include_test_bookings-$domain-$booker_country-$continent_en-$top_count";

    $results = $this->get_db_results_with_cached("topcitieshw_", $query, $generic_key_var, "", FALSE);

    return $results;
  }


  function get_hb_top_cities_of_continent($booker_country_code = "", $currency_code = "EUR", $lang = "en", $continent_en = "", $include_test_bookings = false,$domain = "", $top_count = 4)
  {
    $lang          = $this->db->escape($lang);
    $currency_code = $this->db->escape($currency_code);

    $booker_country = "";
    if(!empty($booker_country_code))
    {
      $booker_country_code = $this->db->escape($booker_country_code);

      $query = "SELECT country_en FROM cities2 WHERE LOWER(country_iso_code_2) LIKE LOWER('$booker_country_code') GROUP BY country_iso_code_2 LIMIT 1";
      $booker_country_code = $this->db->get_var($query);

      if(!empty($booker_country_code))
      {
        $booker_country = "AND LOWER(home_country) LIKE LOWER('$booker_country_code') ";
      }
    }

    if(!empty($domain))
    {
      $domain = $this->db->escape($domain);
      $domain = "AND LOWER(site_domain) LIKE LOWER('$domain')";
    }

    if(!empty($continent_en))
    {
      $continent_en = $this->db->escape($continent_en);
      $continent_en = "AND LOWER(`continent_en`) LIKE LOWER('$continent_en')";
    }

    //force test booking includes for starting the site
    $include_test_bookings = true;
    if($include_test_bookings == true)
    {
      $include_test_bookings = "";
    }
    else
    {
      $include_test_bookings = "AND customer_booking_reference NOT LIKE '%TEST%' AND (test_booked != 1 OR (test_booked IS NULL))";
    }

    $query = "SELECT API_booked,
                     site_domain_id, site_domain,
                     property_city,
                     property_country,
                     translated_city,
                     translated_country,
                		 top_cities_translated.city_hb_id,
                   	 translated_continent,
                     continent_en,
                     city_booking_count,
        							ROUND(
                         MIN(bed_price)
                         * (SELECT hb_equivalent FROM currencies WHERE currency_code = '$currency_code' LIMIT 1)
                         / (SELECT hb_equivalent FROM currencies WHERE currency_code = 'EUR' LIMIT 1)
                         ,2
                       ) as converted_city_min_price,
                       '$currency_code' as converted_currency
    					FROM
    					(
               SELECT API_booked,
                       site_domain_id, site_domain,
                       city_en_name as property_city,
                       country_en_name as property_country,
                       IFNULL(`city_$lang`,city_en_name) as translated_city,
                       IFNULL(`country_$lang`,(SELECT `country_$lang` FROM cities2 WHERE LOWER(cities2.country_en) LIKE LOWER(country_en_name) LIMIT 1)) AS translated_country,
                       city_hb_id,
                       translated_continent,
                       continent_en,
                       city_booking_count,
                       ROUND(
                         (
                          SELECT  MIN(hb_hostel_price.bed_price)
                          FROM `hb_hostel`
                          LEFT JOIN hb_city ON hb_hostel.city_hb_id = hb_city.hb_id
                          LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
                          LEFT JOIN hb_hostel_price ON hb_hostel_price.hostel_hb_id = hb_hostel.property_number
                          WHERE ((hb_hostel_price.currency_code LIKE'EUR')
                            AND hb_country.lname_en LIKE(property_country)
                            AND hb_city.lname_en    LIKE(property_city))
                           GROUP BY hb_city.hb_id
                         )
                         * (SELECT hb_equivalent FROM currencies WHERE currency_code = '$currency_code' LIMIT 1)
                         / (SELECT hb_equivalent FROM currencies WHERE currency_code = 'EUR' LIMIT 1)
                         ,2
                       ) as converted_city_min_price,
                       '$currency_code' as converted_currency
                FROM
                (
                SELECT  API_booked, transactions_hostelworld.site_domain_id, site_domain,
                        IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en)as city_en_name,
                        hb_country.lname_en as country_en_name,
                        hb_city.hb_id as city_hb_id,
                        continent_en,
                        `continent_$lang` as translated_continent,
                         count(*) as city_booking_count
                FROM transactions_hostelworld
                LEFT JOIN site_domains ON transactions_hostelworld.site_domain_id = site_domains.site_domain_id
                LEFT JOIN hb_hostel ON transactions_hostelworld.property_number = hb_hostel.property_number
                LEFT JOIN hb_city ON hb_hostel.city_hb_id = hb_city.hb_id
                LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
                LEFT JOIN continents ON hb_country.continent_hb_code = continents.continent_hb_code
                WHERE LOWER(API_booked) LIKE LOWER('HB')
                  $include_test_bookings
                  $domain
                  $continent_en
                  $booker_country
                GROUP BY hb_hostel.city_hb_id
                ORDER BY city_booking_count DESC
                LIMIT $top_count
                ) as top_cities
                LEFT JOIN cities2 ON (top_cities.city_en_name = cities2.city_en AND top_cities.country_en_name = cities2.country_en)
              ) top_cities_translated
              LEFT JOIN hb_hostel ON top_cities_translated.city_hb_id = hb_hostel.city_hb_id
              LEFT JOIN hb_hostel_price ON hb_hostel_price.hostel_hb_id = hb_hostel.property_number
              WHERE  hb_hostel_price.currency_code LIKE'EUR'
              GROUP BY top_cities_translated.city_hb_id";

    $generic_key_var = "$currency_code-$lang-$include_test_bookings-$domain-$booker_country-$continent_en-$top_count";

    $results = $this->get_db_results_with_cached("topcitieshb_", $query, $generic_key_var, "", FALSE);

    return $results;
  }


  function get_top_cities($API = "hw", $user_country_code = "", $currency_code = "EUR", $lang = "en", $continent_en = "", $include_test_bookings = false, $domain = "", $top_count = 4)
  {
    $topcities = array();
    if(strcasecmp($API,"hb") == 0)
    {
      $topcities = $this->get_hb_top_cities_of_continent($user_country_code, $currency_code, $lang, $continent_en, $include_test_bookings,$domain, $top_count);
      if(empty($topcities))
      {
        $topcities = $this->get_hb_top_cities_of_continent("", $currency_code, $lang, $continent_en, $include_test_bookings,$domain, $top_count);
      }
      return $topcities;
    }
    else
    {
      $topcities = $this->get_hw_top_cities_of_continent($user_country_code, $currency_code, $lang, $continent_en, $include_test_bookings, $domain, $top_count);
      if(empty($topcities))
      {
        $topcities = $this->get_hw_top_cities_of_continent("", $currency_code, $lang, $continent_en, $include_test_bookings, $domain, $top_count);
      }
      return $topcities;
    }

    return $this->get_hw_top_cities_of_continent("", $currency_code, $lang, $continent_en, $include_test_bookings, $domain, $top_count);
  }
  /**
   * HW Api handle the following langages:
   * French, Spanish, German and Italian. The default language is English.
   *
   * If not one of these, it return other so we get an empty translated description field to translate
   */
  function hw_lang_code_convert($lang_code)
  {
    switch(strtolower($lang_code))
    {
      case strtolower("fr"):
        return "French";
      case strtolower("es"):
        return "Spanish";
      case strtolower("de"):
        return "German";
      case strtolower("it"):
        return "Italian";
      case strtolower("en"):
        return "English";
      default:
        return "Other";
    }

    return "English";
  }
  /**
   * HB Api handle the following langages:
   *
   * If not one of these, it return xxxx so we get an empty translated description field to translate
   */
  function hb_lang_code_convert($lang_code)
  {
    switch(strtolower($lang_code))
    {
      case strtolower("en"):
        return "en";
      case strtolower("es"):
        return "es";
      case strtolower("de"):
        return "de";
      case strtolower("it"):
        return "it";
      case strtolower("fr"):
        return "fr";
      case strtolower("pl"):
        return "pl";
      case strtolower("pt"):
        return "pt";
      case strtolower("dk"):
        return "dk";
      case strtolower("nl"):
        return "nl";
      case strtolower("nw"):
        return "nw";
      case strtolower("sw"):
        return "sw";
      default:
        return "xxxx";
    }

    return "en";
  }
}
?>