<?php
if(is_admin())
{
  $today = date("Y-m-d",mktime(0, 0, 0, date("m") , date("d"), date("Y")));

  $domain = $_POST["domain"];

  if(strcasecmp(substr($domain, 0, 4),"www.")==0)
  {
    $domain = substr($domain, 4);
  }

//  $sql_query = "SELECT `country_en`,
//                        `city_en`,
//                        `country_".$_POST["adword_lang"]."` AS countrylang,
//                        `city_".$_POST["adword_lang"]."` AS citylang,
//                        CONCAT('$domain','/',`country_".$_POST["adword_lang"]."`,'/',`city_".$_POST["adword_lang"]."`) AS display_url
//                FROM `cities2`";

//  $sql_query = "SELECT hw_city as city_en,
//                       hw_country as country_en,
//                       `city_".$_POST["adword_lang"]."` AS citylang ,
//                       `country_".$_POST["adword_lang"]."` AS countrylang
//                       FROM hw_city ";
//  $sql_query.= "LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id ";
//  $sql_query.= "LEFT JOIN cities2 ON (hw_city.hw_city = cities2.city_en AND hw_country.hw_country = cities2.country_en) ";
//  $sql_query.= "ORDER BY hw_country,hw_city ASC";

 $api_digit = "01";
 $sql_query = "  SELECT hw_city.hw_city_id as city_id_from_db,
                  continent_en,`continent_".$_POST["adword_lang"]."` AS continentlang, hw_country as country_en, hw_city as city_en, `city_".$_POST["adword_lang"]."` AS citylang , `country_".$_POST["adword_lang"]."` AS countrylang
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
                 GROUP BY hw_country, hw_city
                ORDER BY hw_country ASC, hw_city ASC, property_name ASC";
//  print $sql_query;
//HB query for adwords countries
  if($_POST["city_api_used"] == "HB")
  {
    $api_digit = "02";
    $sql_query = "SELECT hb_city.hb_city_id as city_id_from_db,
    									continent_en,`continent_".$_POST["adword_lang"]."` AS continentlang, hb_country.lname_en as country_en,
                        IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) as city_en,
                        `city_".$_POST["adword_lang"]."` AS citylang , `country_".$_POST["adword_lang"]."` AS countrylang
                      , count(DISTINCT hb_hostel.property_number) AS total_property_count
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'EUR',hb_hostel_price.bed_price,NULL)) AS best_eur_price
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'GBP',hb_hostel_price.bed_price,NULL)) AS best_gbp_price
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'USD',hb_hostel_price.bed_price,NULL)) AS best_usd_price
                      ,SUM(IF(LOWER(property_type) LIKE'hostel'    ,IF(hb_hostel_price.currency_code LIKE'EUR',1,IF(hb_hostel_price.currency_code IS NULL,1,0)),0)) AS hostels_count
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'EUR',IF(LOWER(property_type) LIKE'hostel',hb_hostel_price.bed_price,NULL),NULL)) AS best_hostel_eur_price
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'GBP',IF(LOWER(property_type) LIKE'hostel',hb_hostel_price.bed_price,NULL),NULL)) AS best_hostel_gbp_price
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'USD',IF(LOWER(property_type) LIKE'hostel',hb_hostel_price.bed_price,NULL),NULL)) AS best_hostel_usd_price
                      ,SUM(IF(LOWER(property_type) LIKE'hotel'     ,IF(hb_hostel_price.currency_code LIKE'EUR',1,IF(hb_hostel_price.currency_code IS NULL,1,0)),0)) AS hotels_count
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'EUR',IF(LOWER(property_type) LIKE'hotel',hb_hostel_price.bed_price,NULL),NULL)) AS best_hotel_eur_price
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'GBP',IF(LOWER(property_type) LIKE'hotel',hb_hostel_price.bed_price,NULL),NULL)) AS best_hotel_gbp_price
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'USD',IF(LOWER(property_type) LIKE'hotel',hb_hostel_price.bed_price,NULL),NULL)) AS best_hotel_usd_price
                      ,SUM(IF(LOWER(property_type) LIKE'apartment' ,IF(hb_hostel_price.currency_code LIKE'EUR',1,IF(hb_hostel_price.currency_code IS NULL,1,0)),0)) AS apartments_count
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'EUR',IF(LOWER(property_type) LIKE'apartment',hb_hostel_price.bed_price,NULL),NULL)) AS best_apartment_eur_price
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'GBP',IF(LOWER(property_type) LIKE'apartment',hb_hostel_price.bed_price,NULL),NULL)) AS best_apartment_gbp_price
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'USD',IF(LOWER(property_type) LIKE'apartment',hb_hostel_price.bed_price,NULL),NULL)) AS best_apartment_usd_price
                      ,SUM(IF(LOWER(property_type) LIKE'guesthouse',IF(hb_hostel_price.currency_code LIKE'EUR',1,IF(hb_hostel_price.currency_code IS NULL,1,0)),0)) AS guesthouses_count
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'EUR',IF(LOWER(property_type) LIKE'guesthouse',hb_hostel_price.bed_price,NULL),NULL)) AS best_guesthouse_eur_price
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'GBP',IF(LOWER(property_type) LIKE'guesthouse',hb_hostel_price.bed_price,NULL),NULL)) AS best_guesthouse_gbp_price
                      ,MIN(IF(hb_hostel_price.currency_code LIKE'USD',IF(LOWER(property_type) LIKE'guesthouse',hb_hostel_price.bed_price,NULL),NULL)) AS best_guesthouse_usd_price
                    FROM `hb_hostel`
                    RIGHT JOIN hb_city ON hb_hostel.city_hb_id = hb_city.hb_id
                    LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
                    LEFT JOIN continents ON continents.continent_hb_code = hb_country.continent_hb_code
                    LEFT JOIN hb_hostel_price ON hb_hostel.property_number = hb_hostel_price.hostel_hb_id
                    LEFT JOIN cities2 ON (IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) = cities2.city_en AND hb_country.lname_en = cities2.country_en)
                    GROUP BY hb_country.lname_en, hb_city.lname_en
                    ORDER BY hb_country.lname_en ASC, hb_city.lname_en ASC, property_name ASC";
  }
  CsvCUSTOM($sql_query,$domain,$header,"adwords_".$_POST["city_api_used"]."_cities_".$_POST["adword_lang"]."_".$today.".csv",$_POST["adword_lang"],$_POST['city_x_value'],$api_digit);
}


function CsvCUSTOM($sql_query,$domain,$header="",$filename = 'export.csv',$lang_code_2 = "en",$limit_var,$api_digit)
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

    $header = "\"City_db_id\";\"Country_en\";\"City_en\";\"Country\";\"City\";\"Country_real_url\";\"City_real_url\";\"Country_display_url\";\"City_display_url\";\"Display URL\";\"Translated\";\"Custom city name\";";
    $header.= "\"Total property count\";\"Best property price EUR\";\"Best property price GBP\";\"Best property price USD\";";
    $header.= "\"Total hostels count\";\"Best hostel price EUR\";\"Best hostel price GBP\";\"Best hostel price USD\";";
    $header.= "\"Total hotels count\";\"Best hotel price EUR\";\"Best hotel price GBP\";\"Best hotel price USD\";";
    $header.= "\"Total guesthouses count\";\"Best guesthouse price EUR\";\"Best guesthouse price GBP\";\"Best guesthouse price USD\";";
    $header.= "\"Total apartments count\";\"Best apartment price EUR\";\"Best apartment price GBP\";\"Best apartment price USD\";";
		$header.= "\"Continent Lang\";\"Continent URL\";";

    $out = $header;
    $out .= $csv_terminated;

    // Format the data
    foreach($results as $row)
    {
        $country_lang = $row->country_en;
        $city_lang   = $row->city_en;

        if(!empty($row->countrylang))
        {
          $country_lang = $row->countrylang;
        }
        else
        {
          $country_lang = $aj_country->get_country($row->country_en,$lang_code_2);
        }
        if(!empty($row->citylang))    $city_lang    = $row->citylang;

        $schema_insert = '';
        $schema_insert.= "\"".str_pad($row->city_id_from_db,5,"0",STR_PAD_LEFT)."-$api_digit\";";
        $schema_insert.= $row->country_en.";"."\"".$row->city_en."\"".";";
        $schema_insert.= $country_lang.";"."\"".$city_lang."\"".";";
        $schema_insert.= urlencode($country_lang).";".urlencode($city_lang).";";
        $schema_insert.= url_title($country_lang).";".url_title($city_lang).";";

        $url = $domain."/".url_title($city_lang);
        if(mb_strlen($url,"UTF-8")>35)
        {
          $url = mb_substr ($url,0,35,"UTF-8");
        }
        $schema_insert.= $url.";";

        if(empty($row->citylang))
        {
          $schema_insert.= "NO;";
        }
        else
        {
          $schema_insert.= "YES;";
        }

        $custom_city_name = $city_lang;

        if(mb_strlen($custom_city_name,"UTF-8") > $limit_var)
        {
          $custom_city_name = mb_substr ($custom_city_name,0,$limit_var,"UTF-8");
        }

        $schema_insert.= "\"".$custom_city_name."\"".";";

        $schema_insert.= $row->total_property_count.";";
        $schema_insert.= floor($row->best_eur_price).";";
        $schema_insert.= floor($row->best_gbp_price).";";
        $schema_insert.= floor($row->best_usd_price).";";

        $schema_insert.= $row->hostels_count.";";
        $schema_insert.= floor($row->best_hostel_eur_price).";";
        $schema_insert.= floor($row->best_hostel_gbp_price).";";
        $schema_insert.= floor($row->best_hostel_usd_price).";";

        $schema_insert.= $row->hotels_count.";";
        $schema_insert.= floor($row->best_hotel_eur_price).";";
        $schema_insert.= floor($row->best_hotel_gbp_price).";";
        $schema_insert.= floor($row->best_hotel_usd_price).";";

        $schema_insert.= $row->guesthouses_count.";";
        $schema_insert.= floor($row->best_guesthouse_eur_price).";";
        $schema_insert.= floor($row->best_guesthouse_gbp_price).";";
        $schema_insert.= floor($row->best_guesthouse_usd_price).";";

        $schema_insert.= $row->apartments_count.";";
        $schema_insert.= floor($row->best_apartment_eur_price).";";
        $schema_insert.= floor($row->best_apartment_gbp_price).";";
        $schema_insert.= floor($row->best_apartment_usd_price).";";

				$schema_insert.= $row->continentlang.";";
				$schema_insert.= urlencode($row->continentlang).";";

        $out .= $schema_insert;
        $out .= $csv_terminated;
    } // end while

    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Length: " . strlen($out));
    // Output to browser with appropriate mime type, you choose ;)
//    header("Content-type: text/x-csv");
    //header("Content-type: text/csv");
    header("Content-type: application/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=$filename");
    echo $out;
    exit;

}
?>