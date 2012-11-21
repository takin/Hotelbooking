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
$query = "SELECT * FROM hw_country 
          WHERE (geo_latitude IS NULL) OR (geo_longitude IS NULL)";
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

  while ($geocode_pending) {
    
    $db_iso_code_2 = $row["country_iso_code_2"];
      //Handling HW API bad iso codes
      switch ($db_iso_code_2)
      {
        case "WAL":
          $db_iso_code_2 = "GB";
          break;
        case "SCO":
          $db_iso_code_2 = "GB";
          break;
        case "NIR":
          $db_iso_code_2 = "GB";
          break;
        case "ENG":
          $db_iso_code_2 = "GB";
          break;
          //AS per http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2 TP changed to TL
        case "TP":
          $db_iso_code_2 = "TL";
          break;
      }
      
    $address = $row["hw_country"]. ", ". $db_iso_code_2;
    $id = $row["hw_country_id"];
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
//      print "<textarea>";
//      print_r($xml);
//      print "</textarea>";
      $countryFound = $xml->Response->Placemark->AddressDetails->Country->CountryName;
      $countryCode  = $xml->Response->Placemark->AddressDetails->Country->CountryNameCode;
      
           
      if((strcasecmp($countryCode,$db_iso_code_2)==0)||
         (strcasecmp($countryFound,$row["hw_country"])==0))
      {
        $query = sprintf("UPDATE hw_country " .
               " SET geo_latitude = '%s', geo_longitude = '%s' " .
               " WHERE hw_country_id = '%s' LIMIT 1;",
               mysql_real_escape_string($lat),
               mysql_real_escape_string($lng),
               mysql_real_escape_string($id));
        $update_result = mysql_query($query);
        if (!$update_result) {
          die("Invalid query: " . mysql_error());
        }
      }
      else
      {
        $query = sprintf("UPDATE hw_country " .
               " SET geo_latitude = NULL, geo_longitude = NULL " .
               " WHERE hw_country_id = '%s' LIMIT 1;",
               mysql_real_escape_string($id));
        $update_result = mysql_query($query);
        if (!$update_result) {
          die("Invalid query: " . mysql_error());
        }
        echo "removed GEO of ".$address." (".$row["country_iso_code_2"].") because it found a place in $countryFound,$countryCode<br>";
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

