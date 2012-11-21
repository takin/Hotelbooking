<?php

define("MAPS_HOST", "maps.google.com");
define("KEY", "abcdefg");

// Opens a connection to a MySQL server
$connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$connection) {
  die("Not connected : " . mysql_error());
}

// Set the active MySQL database
$db_selected = mysql_select_db(DB_NAME_AUBERGE, $connection);
if (!$db_selected) {
  die("Can\'t use db : " . mysql_error());
}

// Select all the rows in the cities table that have no geocodes
//$query = "SELECT * FROM cities2 WHERE 1";
$query = "SELECT * FROM cities2
					WHERE (lat IS NULL)
						 OR (lng IS NULL)
						 OR (country_iso_code_2 IS NULL)
						 OR (country_iso_code_3 IS NULL)";
//$query = "SELECT * FROM cities2 WHERE country_en LIKE'France'";
$result = mysql_query($query);
if (!$result) {
  die("Invalid query: " . mysql_error());
}

// Initialize delay in geocode speed
$delay = 0;
$base_url = "http://" . MAPS_HOST . "/maps/geo?output=xml";// . "&key=" . KEY;

// Iterate through the rows, geocoding each address
//$row = @mysql_fetch_assoc($result);
while ($row = @mysql_fetch_assoc($result)) {
  $geocode_pending = true;
//   print_r($row);
  while ($geocode_pending) {
    $address = $row["city_en"].", ".$row["country_en"];
    $id = $row["city_id"];
    $request_url = $base_url . "&q=" . urlencode($address);
    $xml = simplexml_load_file($request_url) or die("url not loading");

    $status = $xml->Response->Status->code;
    if (strcmp($status, "200") == 0) {
      // Successful geocode
      $geocode_pending = false;
      $coordinates = $xml->Response->Placemark->Point->coordinates;
      $coordinatesSplit = split(",", $coordinates);
      // Format: Longitude, Latitude, Altitude
      $lat = $coordinatesSplit[1];
      $lng = $coordinatesSplit[0];
//       print "<pre>";
//       print_r($xml);
//       print "</pre>";
//       exit;
//!!!!!!!!!!!!!!!!!!!!!!
//TODO CHECK FOR UTF8 CHARACTER ! I suspect this to break utf8 data already in DB
      $countryFound = $xml->Response->Placemark->AddressDetails->Country->CountryName;
      $countryCode  = $xml->Response->Placemark->AddressDetails->Country->CountryNameCode;
      if((empty($row["country_iso_code_2"])) ||
         (strcasecmp($countryCode,$row["country_iso_code_2"])==0)||
         (strcasecmp($countryFound,$row["country_en"])==0))
      {
        $sql = "UPDATE cities2 " .
               " SET lat = '%s', lng = '%s' ";

        if(empty($row["country_iso_code_2"]))
        {
          $sql.= ", country_iso_code_2 = \"".mysql_real_escape_string($countryCode)."\"";
        }
        $sql.= " WHERE city_id = '%s' LIMIT 1;";
        $query = sprintf($sql,
               mysql_real_escape_string($lat),
               mysql_real_escape_string($lng),
               mysql_real_escape_string($id));
        print $query."<br>";
//         exit;
        $update_result = mysql_query($query);
        if (!$update_result) {
          die("Invalid query: " . mysql_error());
        }
      }
      else
      {
        $query = sprintf("UPDATE cities2 " .
               " SET lat = NULL, lng = NULL " .
               " WHERE city_id = '%s' LIMIT 1;",
               mysql_real_escape_string($id));
        $update_result = mysql_query($query);
//         print $query."<br>";
        if (!$update_result) {
          die("Invalid query: " . mysql_error());
        }
        echo "removed GEO of ".$address." because it found a place in $countryFound,$countryCode<br>";
      }

    } else if (strcmp($status, "620") == 0) {
      // sent geocodes too fast
      $delay += 10000;
    } else {
      // failure to geocode
      $geocode_pending = false;
      echo "Address " . $address . " failed to geocoded. ";
      echo "Received status " . $status . "<br>\n";
    }
    usleep($delay);
  }
}
?>

