<?php

set_time_limit(1200);

if(is_admin())
{
//  print $_POST['hostels_lang'];
//  print $_POST['domain_hostels'];
//  print $_POST['hostels_currency'];

  $today = date("Y-m-d",mktime(0, 0, 0, date("m") , date("d"), date("Y")));

  $domain = $_POST["domain_hostels"];

  if(strcasecmp(substr($domain, 0, 4),"www.")==0)
  {
    $domain = substr($domain, 4);
  }
  include(get_template_directory()."/ci/db_wp_links.php");
  $aubergedb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);
//  $aubergedb->hide_errors();

  $hostel_link = new Db_links($aubergedb,$domain);
	$domain_lang = $hostel_link->get_lang_from_domain($domain);
  $hostel_link = $hostel_link->get_link("info");

  $api_digit = "01";
  $sql_query = "SELECT hw_country as country_en,hw_city as city_en,
  										 hw_city.hw_city_id as city_id_from_db,
  										 hw_country.hw_country_id as country_id_from_db,
  										 `city_$domain_lang` AS citylang , `country_$domain_lang` AS countrylang,";
  $sql_query.= "       property_name, hw_hostel.property_number, property_type, rating, address1, address2, phone, fax, email, currency_code as hostel_currency,";
  $sql_query.= "       hw_hostel_price.bed_price, hw_hostel_price.currency_price as price_currency,
  										(
  											SELECT group_concat( DISTINCT LPAD(hw_hostel_landmark.landmark_id,6,'0') ORDER BY landmark_name ASC SEPARATOR '|')
                        FROM hw_hostel_landmark
                        LEFT JOIN landmarks as lt ON hw_hostel_landmark.landmark_id = lt.landmark_id
                        WHERE lt.source = 2
                        AND hw_hostel_landmark.distance < 2
                        AND hw_hostel_landmark.property_number = hw_hostel.property_number
                        ORDER BY hw_hostel_landmark.distance ASC
                        LIMIT 10
                      ) as landmarks_of_property,
    									(
    										SELECT group_concat( DISTINCT LPAD(hw_hostel_district.district_id,5,'0') ORDER BY district_name ASC SEPARATOR '|')
                        FROM hw_hostel_district
                        LEFT JOIN districts as dt ON hw_hostel_district.district_id = dt.district_id
                        WHERE hw_hostel_district.property_number = hw_hostel.property_number
                        ORDER BY dt.area ASC
                        LIMIT 10
                       ) as districts_of_property";
  $sql_query.= " FROM `hw_hostel`";
  $sql_query.= " LEFT JOIN hw_rating ON hw_hostel.property_number = hw_rating.property_number";
  $sql_query.= " LEFT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id";
  $sql_query.= " LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id";
  $sql_query.= " LEFT JOIN hw_hostel_price ON hw_hostel.hw_hostel_id = hw_hostel_price.hw_hostel_id";
  $sql_query.= " LEFT JOIN cities2 ON (hw_city.hw_city = cities2.city_en AND hw_country.hw_country = cities2.country_en)";
  $sql_query.= " WHERE hw_hostel_price.currency_price LIKE'".$_POST['hostels_currency']."' OR hw_hostel_price.currency_price IS NULL";
  $sql_query.= " ORDER BY hw_country ASC, hw_city ASC, property_name ASC";
//  $sql_query.= " LIMIT 100";

  if($_POST["hostels_api_used"] == "HB")
  {
    $api_digit = "02";
    $sql_query = "SELECT hb_country.lname_en as country_en,
    										 hb_city.hb_city_id as city_id_from_db,
  										 	 hb_country.hb_country_id as country_id_from_db,
                         IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) as city_en,
                        `city_$domain_lang` AS citylang , `country_$domain_lang` AS countrylang ,
                        property_name, hb_hostel.property_number,
                        property_type, rating_overall as rating,
                        address1, address2, address3
                        phone,email,
                        hb_hostel.currency as hostel_currency,
                        hb_hostel_price.bed_price,
                        hb_hostel_price.currency_code as price_currency,
                      (
  											SELECT group_concat( DISTINCT LPAD(hb_hostel_landmark.landmark_id,6,'0') ORDER BY landmark_name ASC SEPARATOR '|')
                        FROM hb_hostel_landmark
                        LEFT JOIN landmarks as lt ON hb_hostel_landmark.landmark_id = lt.landmark_id
                        WHERE lt.source = 2
                        AND hb_hostel_landmark.distance < 2
                        AND hb_hostel_landmark.property_number = hb_hostel.property_number
                        ORDER BY hb_hostel_landmark.distance ASC
                        LIMIT 10
                      ) as landmarks_of_property,
    									(
    										SELECT group_concat( DISTINCT LPAD(hb_hostel_district.district_id,5,'0') ORDER BY district_name ASC SEPARATOR '|')
                        FROM hb_hostel_district
                        LEFT JOIN districts as dt ON hb_hostel_district.district_id = dt.district_id
                        WHERE hb_hostel_district.property_number = hb_hostel.property_number
                        ORDER BY dt.area ASC
                        LIMIT 10
                       ) as districts_of_property
                  FROM `hb_hostel`
                  LEFT JOIN hb_city ON hb_hostel.city_hb_id = hb_city.hb_id
                  LEFT JOIN hb_country ON hb_city.hb_country_id = hb_country.hb_country_id
                  LEFT JOIN hb_hostel_price ON hb_hostel.property_number = hb_hostel_price.hostel_hb_id
                  LEFT JOIN cities2 ON (IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) = cities2.city_en AND hb_country.lname_en = cities2.country_en)
                  WHERE hb_hostel_price.currency_code LIKE'".$_POST['hostels_currency']."' OR hb_hostel_price.currency_code IS NULL
                  ORDER BY  hb_country.lname_en ASC, hb_city.lname_en ASC, property_number ASC";
  }
  CsvCUSTOM($aubergedb,$hostel_link,$_POST["hostels_api_used"],$sql_query,$domain,$domain_lang,$_POST['hostels_currency'],$_POST['x_value'],"adwords_".$_POST["hostels_api_used"]."_hostels_".$today.".csv", $api_digit);
}


function CsvCUSTOM($aubergedb,$hostel_link,$api_used,$sql_query,$domain,$domain_lang,$post_currency,$limit_var, $filename = 'export.csv', $api_digit)
{


    include(get_template_directory()."/ci/db_wp_hostels.php");
    require_once(get_template_directory()."/ci/wp_url.php");
    require_once(get_template_directory()."/translator_tools/translator_tools.php");
    $hostels = new Db_hostels($aubergedb);
		$hostel_urlcat = new Db_links($aubergedb,$domain);

		$translation_system = new TranslatorTool();

    $csv_terminated = "\n";
    $csv_separator = ";";
    $csv_enclosed = '"';
    $csv_escaped = "\\";
    // Gets the data from the database
    //On dev with big results it can cause memory explosion
    $results = $aubergedb->get_results($sql_query);
    $header = "\"Country en\";\"permanent country ID\";\"City en\";\"permanent city ID\";\"Country $domain_lang\";\"City $domain_lang\";\"City property count \";\"City minimum price\";\"City price currency\";\"Property name\";\"Property Name modified full\";\"Property Name modified\";\"Property Number\";\"Property Type\";\"URL\";\"Real URL\";\"Best price\";\"Best price currency\";\"Rating\";\"Address1\";\"Address2\";\"phone\";\"Fax\";\"Email\";\"Hostel currency\";";
    $header.= "\"Property districts\";;;;;;;;;;";
    $header.= "\"Property landmarks\";;;;;;;;;";
    $out = $header;
    $out .= $csv_terminated;

    $citydata = NULL;

    $lastcity = "";

    // Format the data
    foreach($results as $row)
    {


        $country = $row->country_en;
        $city    = $row->city_en;

        if(!empty($row->countrylang)) $country = $row->countrylang;
        if(!empty($row->citylang))    $city    = $row->citylang;

        $modified_prop_name = clean_hostel_name($row->property_name);

//        $displayurl = $domain."/".url_title($row->property_name);
//        $displayurl = $domain."/".url_title($city);
        $displayurl = $domain."/".url_title($modified_prop_name);
				$urlcat = $hostel_urlcat->get_property_type_link($row->property_type,$domain_lang);

        if(mb_strlen($displayurl,"UTF-8")>35)
        {
          $displayurl = mb_substr ($displayurl,0,35,"UTF-8");
        }

        if(strcmp($lastcity,$country.$city)!=0)
        {
          if($api_used == "HB")
          {
            $citydata = $hostels->get_city_hb_hostels_stats($row->country_en, $row->city_en,$post_currency);
          }
          else
          {
            $citydata = $hostels->get_city_hostels_stats($row->country_en, $row->city_en,$post_currency);
          }

        }
        $lastcity = $country.$city;

        $schema_insert = '';
        $schema_insert.= $csv_enclosed.$row->country_en.$csv_enclosed.";";
        $schema_insert.= $csv_enclosed.str_pad($row->country_id_from_db,3,"0",STR_PAD_LEFT)."-$api_digit".$csv_enclosed.";";
        $schema_insert.= $csv_enclosed.$row->city_en.$csv_enclosed.";";
        $schema_insert.= $csv_enclosed.str_pad($row->city_id_from_db,5,"0",STR_PAD_LEFT)."-$api_digit".$csv_enclosed.";";
        $schema_insert.= $country.";".$csv_enclosed.$city.$csv_enclosed.";";

        $schema_insert.= $citydata->property_count.";";
//        $schema_insert.= round($citydata->city_min_price,0,PHP_ROUND_HALF_UP).";";
        $schema_insert.= floor($citydata->city_min_price).";";
        $schema_insert.= $citydata->city_price_currency.";";

        $schema_insert.= $csv_enclosed.$row->property_name.$csv_enclosed.";";


        //Full modified prop name
        $schema_insert.= $csv_enclosed.$modified_prop_name.$csv_enclosed.";";

        if(mb_strlen($modified_prop_name,"UTF-8")>$limit_var)
        {
          $modified_prop_name = mb_substr ($modified_prop_name,0,$limit_var,"UTF-8");
        }

        //Limited modified prop name
        $schema_insert.= $csv_enclosed.$modified_prop_name.$csv_enclosed.";";
        $schema_insert.= $row->property_number.";";
        $schema_insert.= $row->property_type.";";
        $schema_insert.= $displayurl.";";
        //$schema_insert.= "http://www.".$domain."/".urlencode($hostel_link)."/".url_title($row->property_name)."/".$row->property_number.";";
				$schema_insert.= "http://www.".$domain."/".urlencode($urlcat)."/".url_title($row->property_name)."/".$row->property_number.";";
//        $schema_insert.= round($row->bed_price,0,PHP_ROUND_HALF_UP).";";
        $schema_insert.= floor($row->bed_price).";";
        $schema_insert.= $row->price_currency.";";
        $schema_insert.= $row->rating.";";
        $schema_insert.= $csv_enclosed.addslashes($row->address1).$csv_enclosed.";";
        $schema_insert.= $csv_enclosed.addslashes($row->address2).$csv_enclosed.";";
        $schema_insert.= $csv_enclosed.str_replace('"'," ",$row->phone).$csv_enclosed.";";
        $schema_insert.= $csv_enclosed.addslashes($row->fax).$csv_enclosed.";";
        $schema_insert.= $csv_enclosed.addslashes($row->email).$csv_enclosed.";";
        $schema_insert.= $csv_enclosed.$row->hostel_currency.$csv_enclosed.";";
//         $schema_insert.= $csv_enclosed.$row->districts_of_property.$csv_enclosed.";";

        $districts = explode('|',$row->districts_of_property);
        for($c=0;$c<10;$c++)
        {
          if(!empty($districts[$c]))
          {
            $schema_insert.= $csv_enclosed.$districts[$c].$csv_enclosed.";";
          }
          else
          {
            $schema_insert.= ";";
          }
        }

//         $schema_insert.= $csv_enclosed.$row->landmarks_of_property.$csv_enclosed.";";

        $landmarks = explode('|',$row->landmarks_of_property);
        for($c=0;$c<10;$c++)
        {
          if(!empty($landmarks[$c]))
          {
            $schema_insert.= $csv_enclosed.$landmarks[$c].$csv_enclosed.";";
          }
          else
          {
            $schema_insert.= ";";
          }
        }

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

function clean_hostel_name($name)
{
  mb_internal_encoding("UTF-8");
  mb_regex_encoding("UTF-8");

//  $name = mb_strtolower  ($name,"UTF-8");
  $first2letters = mb_substr ($name,0,2,"UTF-8");
  $first3letters = mb_substr ($name,0,3,"UTF-8");

  if((mb_strtolower($first2letters, "UTF-8") == "la" ) ||
     (mb_strtolower($first2letters, "UTF-8") == "il" ) )
  {
    $name = mb_substr ($name,2,mb_strlen($name,"UTF-8"),"UTF-8");
  }
  if(mb_strtolower($first3letters, "UTF-8") == "the")
  {
    $name = mb_substr ($name,3,mb_strlen($name,"UTF-8"),"UTF-8");
  }

//  $find      = array("hotel", "hostel", "youth", "auberge", "auberge de jeunesse", "albergue", "albergue juvenil", "hostal", "jugendherberge", "international",
//                     "ostello", "albergo", "guesthouse", "guest house", "lodge", "residential", "residence", "pousada", "pension",
//                     "motel", "camping", "backpacker", "bed and breakfast", "B&B", "bed & breakfast", "apartment", "apartamentos");
//  $replace   = array("", "", "", "", "", "", "", "", "", "",
//                     "", "", "", "", "", "", "", "", "",
//                     "", "", "", "", "", "", "", "");
//  $name = mb_eregi_replace ($find, $replace, $name);

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
                        "camping" => "",
                        "backpackers" => "",
                        "backpacker" => "",
                        "bed and breakfast" => "",
                        "B&B" => "",
                        "bed & breakfast" => "",
                        "apartments" => "",
                        "apartment" => "",
                        "apartamentos" => "");

  foreach($replacements as $i => $r)
  {
    $name = mb_eregi_replace ($i, $r, $name);
  }


  $find      = array("  ");
  $replace   = array(" ");

  $name = str_replace($find, $replace, $name);

  return trim($name);
}
?>