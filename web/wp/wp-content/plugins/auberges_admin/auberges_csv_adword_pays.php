<?php

set_time_limit(1200);

if(is_admin())
{
  $today = date("Y-m-d",mktime(0, 0, 0, date("m") , date("d"), date("Y")));

  $domain = $_POST["domain_pays"];

  if(strcasecmp(substr($domain, 0, 4),"www.")==0)
  {
    $domain = substr($domain, 4);
  }
  $api_digit = "01";
  $sql_query = "SELECT hw_country.hw_country_id as country_id_from_db,
  								continent_en,`continent_".$_POST["adword_pays_lang"]."` AS continentlang, hw_country as country_en
                  , IF(`country_".$_POST["adword_pays_lang"]."` IS NULL,(SELECT `country_".$_POST["adword_pays_lang"]."` FROM cities2 WHERE LOWER(cities2.country_en) LIKE LOWER(hw_country) LIMIT 1),`country_".$_POST["adword_pays_lang"]."`) AS countrylang
                  , count(DISTINCT hw_city) AS cities_count
                  , count(DISTINCT hw_hostel.property_number) AS total_property_count
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'EUR',hw_hostel_price.bed_price,NULL)) AS best_eur_price
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'GBP',hw_hostel_price.bed_price,NULL)) AS best_gbp_price
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'USD',hw_hostel_price.bed_price,NULL)) AS best_usd_price
                  ,SUM(IF(LOWER(property_type) LIKE'hostel'    ,IF(hw_hostel_price.currency_price LIKE'EUR',1,IF(hw_hostel_price.currency_price IS NULL,1,0)),0)) AS hostels_count
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'EUR',IF(LOWER(property_type) LIKE'hostel',hw_hostel_price.bed_price,NULL),NULL)) AS best_hostel_eur_price
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'GBP',IF(LOWER(property_type) LIKE'hostel',hw_hostel_price.bed_price,NULL),NULL)) AS best_hostel_gbp_price
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'USD',IF(LOWER(property_type) LIKE'hostel',hw_hostel_price.bed_price,NULL),NULL)) AS best_hostel_usd_price
                  ,SUM(IF(LOWER(property_type) LIKE'hotel'     ,IF(hw_hostel_price.currency_price LIKE'EUR',1,IF(hw_hostel_price.currency_price IS NULL,1,0)),0)) AS hotels_count
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'EUR',IF(LOWER(property_type) LIKE'hotel',hw_hostel_price.bed_price,NULL),NULL)) AS best_hotel_eur_price
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'GBP',IF(LOWER(property_type) LIKE'hotel',hw_hostel_price.bed_price,NULL),NULL)) AS best_hotel_gbp_price
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'USD',IF(LOWER(property_type) LIKE'hotel',hw_hostel_price.bed_price,NULL),NULL)) AS best_hotel_usd_price
                  ,SUM(IF(LOWER(property_type) LIKE'apartment' ,IF(hw_hostel_price.currency_price LIKE'EUR',1,IF(hw_hostel_price.currency_price IS NULL,1,0)),0)) AS apartments_count
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'EUR',IF(LOWER(property_type) LIKE'apartment',hw_hostel_price.bed_price,NULL),NULL)) AS best_apartment_eur_price
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'GBP',IF(LOWER(property_type) LIKE'apartment',hw_hostel_price.bed_price,NULL),NULL)) AS best_apartment_gbp_price
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'USD',IF(LOWER(property_type) LIKE'apartment',hw_hostel_price.bed_price,NULL),NULL)) AS best_apartment_usd_price
                  ,SUM(IF(LOWER(property_type) LIKE'guesthouse',IF(hw_hostel_price.currency_price LIKE'EUR',1,IF(hw_hostel_price.currency_price IS NULL,1,0)),0)) AS guesthouses_count
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'EUR',IF(LOWER(property_type) LIKE'guesthouse',hw_hostel_price.bed_price,NULL),NULL)) AS best_guesthouse_eur_price
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'GBP',IF(LOWER(property_type) LIKE'guesthouse',hw_hostel_price.bed_price,NULL),NULL)) AS best_guesthouse_gbp_price
                  ,MIN(IF(hw_hostel_price.currency_price LIKE'USD',IF(LOWER(property_type) LIKE'guesthouse',hw_hostel_price.bed_price,NULL),NULL)) AS best_guesthouse_usd_price
                FROM `hw_hostel`
                LEFT JOIN hw_rating ON hw_hostel.property_number = hw_rating.property_number
                RIGHT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id
                LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
                LEFT JOIN continents ON continents.continent_id = hw_country.continent_id
                LEFT JOIN hw_hostel_price ON hw_hostel.hw_hostel_id = hw_hostel_price.hw_hostel_id
                LEFT JOIN cities2 ON (hw_city.hw_city = cities2.city_en AND hw_country.hw_country = cities2.country_en)
                 GROUP BY hw_country
                ORDER BY hw_country ASC";

  //HB query for adwords countries
  if($_POST["country_api_used"] == "HB")
  {
    $api_digit = "02";

    $sql_query = "SELECT t.hb_country_id AS country_id_from_db,
	       continent_en,
	       continent_".$_POST["adword_pays_lang"]." AS continentlang,
	       t.lname_en AS country_en,
	       ci2.country_".$_POST["adword_pays_lang"]." AS countrylang,

	       MIN(t.cities_count) AS cities_count,
	       SUM(t.h_cnt) AS total_property_count,
	       MIN(t.eur) AS best_eur_price,
	       MIN(t.gbp) AS best_gbp_price,
	       MIN(t.usd) AS best_usd_price,

	       SUM(t.hs_cnt) AS hostels_count,
	       MIN(t.hs_eur) AS best_hostel_eur_price,
	       MIN(t.hs_gbp) AS best_hostel_gbp_price,
	       MIN(t.hs_usd) AS best_hostel_usd_price,

		   SUM(t.ht_cnt) AS hotels_count,
	       MIN(t.ht_eur) AS best_hotel_eur_price,
	       MIN(t.ht_gbp) AS best_hotel_gbp_price,
	       MIN(t.ht_usd) AS best_hotel_usd_price,

	       SUM(t.a_cnt) AS apartments_count,
	       MIN(t.a_eur) AS best_apartment_eur_price,
	       MIN(t.a_gbp) AS best_apartment_gbp_price,
	       MIN(t.a_usd) AS best_apartment_usd_price,

	       SUM(t.gh_cnt) AS guesthouses_count,
	       MIN(t.gh_eur) AS best_guesthouse_eur_price,
	       MIN(t.gh_gbp) AS best_guesthouse_gbp_price,
	       MIN(t.gh_usd) AS best_guesthouse_usd_price

	FROM (

	SELECT co.continent_hb_code, co.hb_country_id, co.lname_en,
	       COUNT(DISTINCT ci.hb_id) AS cities_count,
	       COUNT(DISTINCT h.property_number) AS h_cnt,
	       MIN(hp_eur.bed_price) AS eur,
	       MIN(hp_gbp.bed_price) AS gbp,
	       MIN(hp_usd.bed_price) AS usd,
	       COUNT(DISTINCT h.property_number) AS hs_cnt,
	       MIN(hp_eur.bed_price) AS hs_eur,
	       MIN(hp_gbp.bed_price) AS hs_gbp,
	       MIN(hp_usd.bed_price) AS hs_usd,
	       NULL AS ht_cnt, NULL AS ht_eur, NULL AS ht_gbp, NULL AS ht_usd,
	       NULL AS a_cnt, NULL AS a_eur, NULL AS a_gbp, NULL AS a_usd,
		   NULL AS gh_cnt, NULL AS gh_eur, NULL AS gh_gbp, NULL AS gh_usd
	  FROM hb_country co
	    JOIN hb_city ci ON co.hb_country_id = ci.hb_country_id
	    LEFT JOIN hb_hostel h ON ci.hb_id = h.city_hb_id AND h.property_type = 'Hostel'
	    LEFT JOIN hb_hostel_price hp_eur ON h.property_number = hp_eur.hostel_hb_id AND hp_eur.currency_code = 'EUR'
	    LEFT JOIN hb_hostel_price hp_gbp ON h.property_number = hp_gbp.hostel_hb_id AND hp_gbp.currency_code = 'GBP'
	    LEFT JOIN hb_hostel_price hp_usd ON h.property_number = hp_usd.hostel_hb_id AND hp_usd.currency_code = 'USD'
	  GROUP BY co.hb_country_id

	UNION ALL

	SELECT co.continent_hb_code, co.hb_country_id, co.lname_en,
	       NULL AS cities_count,
	       COUNT(DISTINCT h.property_number) AS hcnt,
	       MIN(hp_eur.bed_price) AS best_eur,
	       MIN(hp_gbp.bed_price) AS best_gbp,
	       MIN(hp_usd.bed_price) AS best_usd,
	       NULL AS hb_cnt, NULL AS hs_eur,NULL AS hs_gbp, NULL AS hs_usd,
	       COUNT(DISTINCT h.property_number) AS ht_cnt,
	       MIN(hp_eur.bed_price) AS ht_eur,
	       MIN(hp_gbp.bed_price) AS ht_gbp,
	       MIN(hp_usd.bed_price) AS ht_usd,
	       NULL AS a_cnt, NULL AS a_eur, NULL AS a_gbp, NULL AS a_usd,
	       NULL AS gh_cnt, NULL AS gh_eur, NULL AS gh_gbp, NULL AS gh_usd
	  FROM hb_country co
	    JOIN hb_city ci ON co.hb_country_id = ci.hb_country_id
	    JOIN hb_hostel h ON ci.hb_id = h.city_hb_id AND h.property_type = 'Hotel'
	    LEFT JOIN hb_hostel_price hp_eur ON h.property_number = hp_eur.hostel_hb_id AND hp_eur.currency_code = 'EUR'
	    LEFT JOIN hb_hostel_price hp_gbp ON h.property_number = hp_gbp.hostel_hb_id AND hp_gbp.currency_code = 'GBP'
	    LEFT JOIN hb_hostel_price hp_usd ON h.property_number = hp_usd.hostel_hb_id AND hp_usd.currency_code = 'USD'
	  GROUP BY co.hb_country_id

	UNION ALL

	SELECT co.continent_hb_code, co.hb_country_id, co.lname_en,
	       NULL AS cities_count,
	       COUNT(DISTINCT h.property_number) AS hcnt,
	       MIN(hp_eur.bed_price) AS best_eur,
	       MIN(hp_gbp.bed_price) AS best_gbp,
	       MIN(hp_usd.bed_price) AS best_usd,
	       NULL AS hs_cnt, NULL AS hs_eur, NULL AS hs_gbp, NULL AS hs_usd,
	       NULL AS ht_cnt, NULL AS ht_eur, NULL AS ht_gbp, NULL AS ht_usd,
	       COUNT(DISTINCT h.property_number) AS a_cnt,
	       MIN(hp_eur.bed_price) AS a_eur,
	       MIN(hp_gbp.bed_price) AS a_gbp,
	       MIN(hp_usd.bed_price) AS a_usd,
	       NULL AS gh_cnt, NULL AS gh_eur, NULL AS gh_gbp, NULL AS gh_usd
	  FROM hb_country co
	    JOIN hb_city ci ON co.hb_country_id = ci.hb_country_id
	    JOIN hb_hostel h ON ci.hb_id = h.city_hb_id AND h.property_type = 'Apartment'
	    LEFT JOIN hb_hostel_price hp_eur ON h.property_number = hp_eur.hostel_hb_id AND hp_eur.currency_code = 'EUR'
	    LEFT JOIN hb_hostel_price hp_gbp ON h.property_number = hp_gbp.hostel_hb_id AND hp_gbp.currency_code = 'GBP'
	    LEFT JOIN hb_hostel_price hp_usd ON h.property_number = hp_usd.hostel_hb_id AND hp_usd.currency_code = 'USD'
	  GROUP BY co.hb_country_id

	UNION ALL

	SELECT co.continent_hb_code, co.hb_country_id, co.lname_en,
	       NULL AS cities_count,
	       COUNT(DISTINCT h.property_number) AS hcnt,
	       MIN(hp_eur.bed_price) AS best_eur,
	       MIN(hp_gbp.bed_price) AS best_gbp,
	       MIN(hp_usd.bed_price) AS best_usd,
	       NULL AS hs_cnt, NULL AS hs_eur, NULL AS hs_gbp, NULL AS hs_usd,
	       NULL AS ht_cnt, NULL AS ht_eur, NULL AS ht_gbp, NULL AS ht_usd,
	       NULL AS a_cnt, NULL AS a_eur, NULL AS a_gbp, NULL AS a_usd,
	       COUNT(DISTINCT h.property_number) AS gh_cnt,
	       MIN(hp_eur.bed_price) AS gh_eur,
	       MIN(hp_gbp.bed_price) AS gh_gbp,
	       MIN(hp_usd.bed_price) AS gh_usd
	  FROM hb_country co
	    JOIN hb_city ci ON co.hb_country_id = ci.hb_country_id
	    JOIN hb_hostel h ON ci.hb_id = h.city_hb_id AND h.property_type = 'Guesthouse'
	    LEFT JOIN hb_hostel_price hp_eur ON h.property_number = hp_eur.hostel_hb_id AND hp_eur.currency_code = 'EUR'
	    LEFT JOIN hb_hostel_price hp_gbp ON h.property_number = hp_gbp.hostel_hb_id AND hp_gbp.currency_code = 'GBP'
	    LEFT JOIN hb_hostel_price hp_usd ON h.property_number = hp_usd.hostel_hb_id AND hp_usd.currency_code = 'USD'
	  GROUP BY co.hb_country_id) t

	    JOIN continents cn ON t.continent_hb_code = cn.continent_hb_code
	    LEFT JOIN (SELECT DISTINCT country_en, country_".$_POST["adword_pays_lang"]." FROM cities2) ci2 ON t.lname_en = ci2.country_en

   GROUP BY t.hb_country_id;";

  }
  CsvCUSTOM($sql_query,$domain,$header,"adwords_".$_POST["country_api_used"]."_countries_".$_POST["adword_pays_lang"]."_".$today.".csv",$_POST["adword_pays_lang"],$api_digit);
}


function CsvCUSTOM($sql_query,$domain,$header="",$filename = 'export.csv',$lang_code_2 = "en",$api_digit)
{
    require_once(get_template_directory()."/ci/wp_url.php");

    include(get_template_directory()."/ci/db_wp_country.php");
    $aubergedb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);
    $aubergedb->hide_errors();

    $aj_country = new Db_country($aubergedb);

    $csv_terminated = "\n";
    $csv_separator = ";";
    $csv_enclosed = '"';
    $csv_escaped = "\\";

    // Gets the data from the database
    $results = $aubergedb->get_results($sql_query);

    $header = "\"Country_db_id\";\"Continent_en\";\"Country_en\";\"Continent\";\"Country\";\"Continent_real_url\";\"Country_real_url\";\"Continent_display_url\";\"Country_display_url\";\"Display URL\";\"Translated\";";
    $header.= "\"Cities Count\";\"Total property count\";\"Best property price EUR\";\"Best property price GBP\";\"Best property price USD\";";
    $out = $header;
    $out .= $csv_terminated;

    // Format the data
    foreach($results as $row)
    {
        $continent_lang = $row->continent_en;
        $country_lang   = $row->country_en;

        if(!empty($row->countrylang))
        {
          $country_lang = $row->countrylang;
        }

        if(!empty($row->continentlang))    $continent_lang    = $row->continentlang;

        $schema_insert = '';
        $schema_insert.= "\"".str_pad($row->country_id_from_db,3,"0",STR_PAD_LEFT)."-$api_digit\";";
        $schema_insert.= $row->continent_en.";".$row->country_en.";";
        $schema_insert.= $continent_lang.";".$country_lang.";";
        $schema_insert.= urlencode($continent_lang).";".urlencode($country_lang).";";
        $schema_insert.= url_title($continent_lang).";".url_title($country_lang).";";

        $url = $domain."/".url_title($country_lang);
        if(mb_strlen($url,"UTF-8")>35)
        {
          $url = mb_substr ($url,0,35,"UTF-8");
        }
        $schema_insert.= $url.";";

        if(empty($row->countrylang))
        {
          $schema_insert.= "NO;";
        }
        else
        {
          $schema_insert.= "YES;";
        }

        $schema_insert.= $row->cities_count.";";
        $schema_insert.= $row->total_property_count.";";
        $schema_insert.= floor($row->best_eur_price).";";
        $schema_insert.= floor($row->best_gbp_price).";";
        $schema_insert.= floor($row->best_usd_price).";";

        $out .= $schema_insert;
        $out .= $csv_terminated;
    } // end while

    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Length: " . strlen($out));
    header("Content-type: application/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=$filename");
    echo $out;
    exit;

}
?>