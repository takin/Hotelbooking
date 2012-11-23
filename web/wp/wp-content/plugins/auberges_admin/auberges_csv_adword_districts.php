<?php
/*
New CSV to be create for the campaign based on District with the following field:

Permanent country ID
Country in English
Country in language
Country URL
Permanent city ID
City in English
City in language
City URL
Permanent District ID
District in English
District in language
District URL
Number of hostels in district
Number of hotels in district
Number of apartments in district
Number of guesthouses in district
Number of camping in district
 */

//Put the district in which the hostel is in the CSV of the Hostels, might need to find a way to put more than 1 value in one cell.

if(is_admin())
{
  $today = date("Y-m-d",mktime(0, 0, 0, date("m") , date("d"), date("Y")));

  $domain = $_POST["domain_districts"];

  if(strcasecmp(substr($domain, 0, 4),"www.")==0)
  {
    $domain = substr($domain, 4);
  }
  $lang_code = $_POST["districts_adword_lang"];

  $api_digit = "01";
  $sql_query = "SELECT
                    	hw_country.hw_country_id as permanent_country_id,
                    	hw_country.hw_country as country_en,
                    	cities2.`country_$lang_code` as country_lang,
                    	continents.`continent_$lang_code` as continent_lang,
                    	CONCAT('$domain/',continents.`continent_$lang_code`,'/',cities2.`country_$lang_code`) as country_url,
                    	hw_city.hw_city_id as permanent_city_id,
                    	hw_city.hw_city as city_en,
                    	cities2.`city_$lang_code` as city_lang,
                    	CONCAT('$domain/',cities2.`country_$lang_code`,'/',cities2.`city_$lang_code`) as city_url,
                    	districts.district_id as permanent_district_id,
                    	districts.district_name as district_name_en,
                    	districts.slug as district_slug,
                    	(SELECT `term_$lang_code` FROM translation_links WHERE term = districts.slug) as district_slug_lang,
                    	NULL as district_name_lang,
                    	-- property_type,
                    	count(*) as total_property_count,
                    	(	SELECT count(*) FROM hw_hostel_district
                    		LEFT JOIN hw_hostel ON hw_hostel_district.property_number = hw_hostel.property_number
                    		WHERE hw_hostel_district.district_id = districts.district_id
                    		AND property_type LIKE 'Hostel'
                    	) as hostel_count,
                    	(	SELECT count(*) FROM hw_hostel_district
                    		LEFT JOIN hw_hostel ON hw_hostel_district.property_number = hw_hostel.property_number
                    		WHERE hw_hostel_district.district_id = districts.district_id
                    		AND property_type LIKE 'Hotel'
                    	) as hotel_count,
                    	(	SELECT count(*) FROM hw_hostel_district
                    		LEFT JOIN hw_hostel ON hw_hostel_district.property_number = hw_hostel.property_number
                    		WHERE hw_hostel_district.district_id = districts.district_id
                    		AND property_type LIKE 'Apartment'
                    	) as apart_count,
                    	(	SELECT count(*) FROM hw_hostel_district
                    		LEFT JOIN hw_hostel ON hw_hostel_district.property_number = hw_hostel.property_number
                    		WHERE hw_hostel_district.district_id = districts.district_id
                    		AND property_type LIKE 'Guesthouse'
                    	) as guesthouse_count,
                    	(	SELECT count(*) FROM hw_hostel_district
                    		LEFT JOIN hw_hostel ON hw_hostel_district.property_number = hw_hostel.property_number
                    		WHERE hw_hostel_district.district_id = districts.district_id
                    		AND property_type LIKE 'Camping'
                    	) as camping_count
              FROM hw_hostel_district
              LEFT JOIN districts ON hw_hostel_district.district_id = districts.district_id
              LEFT JOIN hw_hostel ON hw_hostel_district.property_number = hw_hostel.property_number
              LEFT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id
              LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
              LEFT JOIN continents ON continents.continent_id = hw_country.continent_id
              LEFT JOIN cities2 ON (hw_city.hw_city = cities2.city_en AND hw_country.hw_country = cities2.country_en)
              GROUP BY hw_hostel_district.district_id
              ORDER BY country_en ASC, city_en ASC, district_name_en ASC";

//HB query for adwords countries
  if($_POST["districts_api_used"] == "HB")
  {
    $api_digit = "02";
    $sql_query = "SELECT
                    	hb_country.hb_country_id as permanent_country_id,
                    	hb_country.lname_en as country_en,
                    	cities2.`country_$lang_code` as country_lang,
                    	continents.`continent_$lang_code` as continent_lang,
                    	CONCAT('$domain/',continents.`continent_$lang_code`,'/',cities2.`country_$lang_code`) as country_url,
                    	hb_city.hb_city_id as permanent_city_id,
                    	IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) as city_en,
                    	cities2.`city_$lang_code` as city_lang,
                    	CONCAT('$domain/',cities2.`country_$lang_code`,'/',cities2.`city_$lang_code`) as city_url,
                    	districts.district_id as permanent_district_id,
                    	districts.district_name as district_name_en,
                    	districts.slug as district_slug,
                    	(SELECT `term_$lang_code` FROM translation_links WHERE term = districts.slug) as district_slug_lang,
                    	NULL as district_name_lang,
                    	-- property_type,
                    	count(*) as total_property_count,
                    	(	SELECT count(*) FROM hb_hostel_district
                    		LEFT JOIN hb_hostel ON hb_hostel_district.property_number = hb_hostel.property_number
                    		WHERE hb_hostel_district.district_id = districts.district_id
                    		AND property_type LIKE 'Hostel'
                    	) as hostel_count,
                    	(	SELECT count(*) FROM hb_hostel_district
                    		LEFT JOIN hb_hostel ON hb_hostel_district.property_number = hb_hostel.property_number
                    		WHERE hb_hostel_district.district_id = districts.district_id
                    		AND property_type LIKE 'Hotel'
                    	) as hotel_count,
                    	(	SELECT count(*) FROM hb_hostel_district
                    		LEFT JOIN hb_hostel ON hb_hostel_district.property_number = hb_hostel.property_number
                    		WHERE hb_hostel_district.district_id = districts.district_id
                    		AND property_type LIKE 'Apartment'
                    	) as apart_count,
                    	(	SELECT count(*) FROM hb_hostel_district
                    		LEFT JOIN hb_hostel ON hb_hostel_district.property_number = hb_hostel.property_number
                    		WHERE hb_hostel_district.district_id = districts.district_id
                    		AND property_type LIKE 'Guesthouse'
                    	) as guesthouse_count,
                    	(	SELECT count(*) FROM hb_hostel_district
                    		LEFT JOIN hb_hostel ON hb_hostel_district.property_number = hb_hostel.property_number
                    		WHERE hb_hostel_district.district_id = districts.district_id
                    		AND property_type LIKE 'Camping'
                    	) as camping_count
              FROM hb_hostel_district
              LEFT JOIN districts ON hb_hostel_district.district_id = districts.district_id
              LEFT JOIN hb_hostel ON hb_hostel_district.property_number = hb_hostel.property_number
              LEFT JOIN hb_city ON hb_hostel.city_hb_id = hb_city.hb_id
              LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
              LEFT JOIN continents ON continents.continent_hb_code = hb_country.continent_hb_code
              LEFT JOIN cities2 ON (IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) = cities2.city_en AND hb_country.lname_en = cities2.country_en)
              GROUP BY hb_hostel_district.district_id
              ORDER BY country_en ASC, city_en ASC, district_name_en ASC";
  }

  CsvCUSTOM($sql_query,$domain,$header,"adwords_".$_POST["districts_api_used"]."_districts_".$lang_code."_".$today.".csv",$lang_code,$api_digit);
}


function CsvCUSTOM($sql_query,$domain,$header="",$filename = 'export.csv',$lang_code_2 = "en",$api_digit = "01")
{
    include(get_template_directory()."/ci/db_wp_country.php");
    require_once(get_template_directory()."/translator_tools/translator_tools.php");
    require_once(get_template_directory()."/ci/wp_url.php");

    $aubergedb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);
    $aubergedb->hide_errors();

    $aj_country = new Db_country($aubergedb);

    $translation_system = new TranslatorTool();

    $csv_terminated = "\n";
    $csv_separator = ";";
    $csv_enclosed = '"';
    $csv_escaped = "\\";

    // Gets the data from the database
    $results = $aubergedb->get_results($sql_query);

    $header = "\"permanent_country_id\";";
    $header .= "\"country_en\";";
    $header .= "\"country_lang\";";
    $header .= "\"country_url\";";
    $header .= "\"permanent_city_id\";";
    $header .= "\"city_en\";";
    $header .= "\"city_lang\";";
    $header .= "\"city_url\";";
    $header .= "\"permanent_district_id\";";
    $header .= "\"district_name_en\";";
    $header .= "\"district_name_lang\";";
    $header .= "\"district_slug_lang\";";
    $header .= "\"total_property_count\";";
    $header .= "\"hostel_count\";";
    $header .= "\"hotel_count\";";
    $header .= "\"apart_count\";";
    $header .= "\"guesthouse_count\";";
    $header .= "\"camping_count\";";
    $header .= "\"district_orig_slug\";";
    $header .= "\"district_url\";";

    $out = $header;
    $out .= $csv_terminated;

    // Format the data
    foreach($results as $row)
    {
        if(($row->hostel_count == 0) &&
        ($row->hotel_count == 0) &&
        ($row->apart_count == 0) &&
        ($row->guesthouse_count == 0) &&
        ($row->camping_count == 0))
        {
          continue;
        }
        $country_lang = $row->country_en;
        $city_lang   = $row->city_en;

        if(!empty($row->country_lang))
        {
          $country_lang = $row->country_lang;
        }
        else
        {
          $country_lang = $aj_country->get_country($row->country_en,$lang_code_2);
        }

        if(!empty($row->city_lang))
        {
          $city_lang    = $row->city_lang;
        }

        $district_lang = $translation_system->single_translate($row->district_name_en, $lang_code_2);
        if(empty($district_lang))
        {
          $district_lang  = $row->district_name_en;
        }
        $district_lang = str_replace('"','\\"',$district_lang);

        $schema_insert = '';
        $schema_insert.= "\"".str_pad($row->permanent_country_id,3,"0",STR_PAD_LEFT)."-$api_digit\";";
        $schema_insert.= "\"".$row->country_en."\";";
        $schema_insert.= "\"".$country_lang."\";";
        $schema_insert.= "\"".$domain."/".url_title($row->continent_lang)."/".urlencode($country_lang)."\";";
        $schema_insert.= "\"".str_pad($row->permanent_city_id,5,"0",STR_PAD_LEFT)."-$api_digit\";";
        $schema_insert.= "\"".$row->city_en."\";";
        $schema_insert.= "\"".$city_lang."\";";
        $schema_insert.= "\"".$domain."/".urlencode($country_lang)."/".urlencode($city_lang)."\";";
        $schema_insert.= "\"".str_pad($row->permanent_district_id,5,"0",STR_PAD_LEFT)."\";";
        $schema_insert.= "\"".$row->district_name_en."\";";
        $schema_insert.= "\"".$district_lang."\";";
        $schema_insert.= "\"".$row->district_slug_lang."\";";
        $schema_insert.= "\"".$row->total_property_count."\";";
        $schema_insert.= "\"".$row->hostel_count."\";";
        $schema_insert.= "\"".$row->hotel_count."\";";
        $schema_insert.= "\"".$row->apart_count."\";";
        $schema_insert.= "\"".$row->guesthouse_count."\";";
        $schema_insert.= "\"".$row->camping_count."\";";
        $schema_insert.= "\"".$row->district_slug."\";";

        $slugurl = $row->district_slug;
        if(!empty($row->district_slug_lang))
        {
          $slugurl = $row->district_slug_lang;
        }

        $schema_insert.= "\"".$domain."/".urlencode($country_lang)."/".urlencode($city_lang)."/district/".$slugurl."\";";

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