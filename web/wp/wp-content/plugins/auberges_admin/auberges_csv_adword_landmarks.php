<?php
/*
New CSV to be create for the campaign based on Landmarks with the following field:

Permanent country IDCountry in English
Country in language
Country URL
Permanent city number
City in English
City in language
City URL
Permanent landmark ID
landmark in English
landmark in language
landmark URL
Number of hostels by landmark
Number of hotels by Landmark
Number of apartments by landmark
Number of guesthouses by landmark
Number of camping by landmark

Put the landmarks within 2KM of each hostelsin the CSV of the Hostels, might need to find a way to put more than 1 value in one cell.
 */
if(is_admin())
{
  $today = date("Y-m-d",mktime(0, 0, 0, date("m") , date("d"), date("Y")));

  $domain = $_POST["domain_landmarks"];

  if(strcasecmp(substr($domain, 0, 4),"www.")==0)
  {
    $domain = substr($domain, 4);
  }
  $lang_code = $_POST["landmarks_adword_lang"];

  $api_digit = "01";
  //order by distance before grouping to make sure
  //that landmark city and country is the closest to the landmark

  $sql_query = "SELECT
                  hw_country.hw_country_id as permanent_country_id,
                	hw_country.hw_country as country_en,
                	cities2.`country_$lang_code` as country_lang,
                	continents.`continent_$lang_code` as continent_lang,
                	hw_city.hw_city_id as permanent_city_id,
                	hw_city.hw_city as city_en,
                	cities2.`city_$lang_code` as city_lang,
                	permanent_landmark_id,
                	landmark_name_en,
                	landmark_slug,
                	(SELECT `term_$lang_code` FROM translation_links WHERE term = landmark_slug) as landmark_slug_lang,
                	NULL as landmark_name_lang,
                  min_distance,
                  total_property_count,
                  hostel_count,
                  hotel_count,
                	apart_count,
                	guesthouse_count,
                	camping_count,
                   (SELECT group_concat( DISTINCT type ORDER BY type ASC SEPARATOR '|')
                    FROM landmark_of_type
                    LEFT JOIN landmark_type as lt ON landmark_of_type.landmark_type_id = lt.landmark_type_id
                    WHERE landmark_of_type.landmark_id = permanent_landmark_id
                   ) as landmark_types
                FROM
                (
                    SELECT hw_hostel_landmark.landmark_id as permanent_landmark_id,
                        hw_hostel_landmark.property_number,
                        hw_hostel.hw_city_id,
                        landmarks.landmark_name as landmark_name_en,
                        NULL as landmark_name_lang,
                        landmarks.slug as landmark_slug,
                        distance as min_distance,
                        SUM(IF(property_type LIKE'Hostel',1,0 )) as hostel_count,
                        SUM(IF(property_type LIKE'Hotel',1,0 )) as hotel_count,
                        SUM(IF(property_type LIKE'Apartment',1,0 )) as apart_count,
                        SUM(IF(property_type LIKE'Guesthouse',1,0 )) as guesthouse_count,
                        SUM(IF(property_type LIKE'Campsite',1,0 )) as camping_count,
                        count(*) as total_property_count
                    FROM hw_hostel_landmark
                    LEFT JOIN landmarks ON hw_hostel_landmark.landmark_id = landmarks.landmark_id
                    LEFT JOIN hw_hostel ON hw_hostel_landmark.property_number = hw_hostel.property_number
                    WHERE source = 2
                    GROUP BY hw_hostel.hw_city_id,hw_hostel_landmark.landmark_id
                ) as landmarks_grouped
                LEFT JOIN hw_city ON landmarks_grouped.hw_city_id = hw_city.hw_city_id
                LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
                LEFT JOIN continents ON continents.continent_id = hw_country.continent_id
                LEFT JOIN cities2 ON (hw_city.hw_city = cities2.city_en AND hw_country.hw_country = cities2.country_en)
                ORDER BY country_en ASC, city_en ASC, landmark_name_en ASC";

  //HB query for adwords countries
  if($_POST["landmarks_api_used"] == "HB")
  {
    $api_digit = "02";
    $sql_query = "SELECT
                  hb_country.hb_country_id as permanent_country_id,
                	hb_country.lname_en as country_en,
                	cities2.`country_$lang_code` as country_lang,
                	continents.`continent_$lang_code` as continent_lang,
                	hb_city.hb_city_id as permanent_city_id,
                	IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) as city_en,
                	cities2.`city_$lang_code` as city_lang,
                	permanent_landmark_id,
                	landmark_name_en,
                	landmark_slug,
                	(SELECT `term_$lang_code` FROM translation_links WHERE term = landmark_slug) as landmark_slug_lang,
                	NULL as landmark_name_lang,
                  min_distance,
                  total_property_count,
                  hostel_count,
                  hotel_count,
                	apart_count,
                	guesthouse_count,
                	camping_count,
                   (SELECT group_concat( DISTINCT type ORDER BY type ASC SEPARATOR '|')
                    FROM landmark_of_type
                    LEFT JOIN landmark_type as lt ON landmark_of_type.landmark_type_id = lt.landmark_type_id
                    WHERE landmark_of_type.landmark_id = permanent_landmark_id
                   ) as landmark_types
                FROM
                (
                    SELECT hb_hostel_landmark.landmark_id as permanent_landmark_id,
                        hb_hostel_landmark.property_number,
                        hb_hostel.city_hb_id,
                        landmarks.landmark_name as landmark_name_en,
                        NULL as landmark_name_lang,
                        landmarks.slug as landmark_slug,
                        distance as min_distance,
                        SUM(IF(property_type LIKE'Hostel',1,0 )) as hostel_count,
                        SUM(IF(property_type LIKE'Hotel',1,0 )) as hotel_count,
                        SUM(IF(property_type LIKE'Apartment',1,0 )) as apart_count,
                        SUM(IF(property_type LIKE'Guesthouse',1,0 )) as guesthouse_count,
                        SUM(IF(property_type LIKE'Campsite',1,0 )) as camping_count,
                        count(*) as total_property_count
                    FROM hb_hostel_landmark
                    LEFT JOIN landmarks ON hb_hostel_landmark.landmark_id = landmarks.landmark_id
                    LEFT JOIN hb_hostel ON hb_hostel_landmark.property_number = hb_hostel.property_number
                    WHERE source = 2
                    GROUP BY hb_hostel.city_hb_id,hb_hostel_landmark.landmark_id
                ) as landmarks_grouped
                LEFT JOIN hb_city ON landmarks_grouped.city_hb_id = hb_city.hb_id
                LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
                LEFT JOIN continents ON continents.continent_hb_code = hb_country.continent_hb_code
                LEFT JOIN cities2 ON (IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) = cities2.city_en AND hb_country.lname_en = cities2.country_en)
                ORDER BY country_en ASC, city_en ASC, landmark_name_en ASC";
  }
//   $sql_query.= " LIMIT 20";
// print "<pre>";
// print_r( $sql_query);
// print "</pre>";
  CsvCUSTOM($sql_query,$domain,$header,"adwords_".$_POST["landmarks_api_used"]."_landmarks_".$lang_code."_".$today.".csv",$lang_code,$api_digit);
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

    $header = "\"permanent_country_id\";";
    $header .= "\"country_en\";";
    $header .= "\"country_lang\";";
    $header .= "\"country_url\";";
    $header .= "\"permanent_city_id\";";
    $header .= "\"city_en\";";
    $header .= "\"city_lang\";";
    $header .= "\"city_url\";";
    $header .= "\"permanent_landmark_id\";";
    $header .= "\"landmark_name_en\";";
    $header .= "\"landmark_name_lang\";";
    $header .= "\"landmark_slug_lang\";";
    $header .= "\"total_property_count\";";
    $header .= "\"hostel_count\";";
    $header .= "\"hotel_count\";";
    $header .= "\"apart_count\";";
    $header .= "\"guesthouse_count\";";
    $header .= "\"camping_count\";";
    $header .= "\"landmark_types\";";
    $header .= "\"landmark_url\";";

    $out = $header;
    $out .= $csv_terminated;

    //Send something to browser before computing SQL to prevent timeout
//     header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

    header("Content-type: application/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=$filename");

//     ob_start();


//     $uploads = wp_upload_dir();
//     $dir = $uploads['basedir'];
//     $handle = false;
//     $filename = $dir."/".$filename.".gz";

//     if (!$handle = gzopen($filename, "wb9"))
//     {
//       echo "Cannot open file ($filename)";
//       exit;
//     }

//     $msg = "Download should be available in less than 5 min";
//     ignore_user_abort(true);
//     ob_end_clean();

//     header('HTTP/1.0 204 No Content', true);
//     header('Content-Length: 0',true);
//     header("Connection: close", true);
//     echo $msg;
//     flush();

    // Gets the data from the database

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    set_time_limit(500);
    ini_set('memory_limit', '1280M');
//     $aubergedb->show_errors();
    $results = $aubergedb->get_results($sql_query);

    if(!$results)
    {
      ob_start();
      $aubergedb->print_error();
      $dberror = ob_get_contents();
      ob_end_clean();
      error_log("Adword landmarks CSV $filename error with SQL: $dberror", 0);
    }
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

        $landmark_lang = $translation_system->single_translate($row->landmark_name_en, $lang_code_2);
        if(empty($landmark_lang))
        {
          $landmark_lang  = $row->landmark_name_en;
        }
        $landmark_lang = str_replace('"','\\"',$landmark_lang);

        $schema_insert = '';
//         $schema_insert.= "\"".str_pad($row->city_id_from_db,5,"0",STR_PAD_LEFT)."-$api_digit\";";
         $schema_insert.= "\"".str_pad($row->permanent_country_id,3,"0",STR_PAD_LEFT)."-$api_digit\";";
        $schema_insert.= "\"".$row->country_en."\";";
        $schema_insert.= "\"".$country_lang."\";";
        $schema_insert.= "\"".$domain."/".url_title($row->continent_lang)."/".urlencode($country_lang)."\";";
        $schema_insert.= "\"".str_pad($row->permanent_city_id,5,"0",STR_PAD_LEFT)."-$api_digit\";";
        $schema_insert.= "\"".$row->city_en."\";";
        $schema_insert.= "\"".$city_lang."\";";
        $schema_insert.= "\"".$domain."/".urlencode($country_lang)."/".urlencode($city_lang)."\";";
        $schema_insert.= "\"".str_pad($row->permanent_landmark_id,6,"0",STR_PAD_LEFT)."\";";
        $schema_insert.= "\"".$row->landmark_name_en."\";";
        $schema_insert.= "\"".$landmark_lang."\";";
        $schema_insert.= "\"".$row->landmark_slug_lang."\";";
        $schema_insert.= "\"".$row->total_property_count."\";";
        $schema_insert.= "\"".$row->hostel_count."\";";
        $schema_insert.= "\"".$row->hotel_count."\";";
        $schema_insert.= "\"".$row->apart_count."\";";
        $schema_insert.= "\"".$row->guesthouse_count."\";";
        $schema_insert.= "\"".$row->camping_count."\";";
        $schema_insert.= "\"".$row->landmark_types."\";";

        $schema_insert.= "\"".$domain."/".urlencode($country_lang)."/".urlencode($city_lang)."/landmark/".$row->landmark_slug_lang."\";";

        $out .= $schema_insert;
        $out .= $csv_terminated;
        echo $out;

//         if (gzwrite($handle, $out) === FALSE)
//         {
//           error_log("Adword landmarks CSV: Cannot write content to file ($filename)", 0);
//                 exit;
//         }
        $out = "";
//         ob_flush();
//         flush();
    } // end while

//     error_log("Adword after output SQL", 0);
//     ob_end_flush();
//     flush();

//     gzclose($handle);

}
?>