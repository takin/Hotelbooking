<?php

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
$query = "SELECT * FROM cities2 WHERE (lat IS NULL) OR (lng IS NULL)";
$result = mysql_query($query);

if (!$result) {
  die("Invalid query: " . mysql_error());
}

// Initialize delay in geocode speed
$delay = 1000;
$base_url = "https://maps.googleapis.com/maps/api/geocode/xml?sensor=false&address=";

libxml_use_internal_errors(true);

// Iterate through the rows, geocoding each address
while ($row = @mysql_fetch_assoc($result)) {

    $id = $row["city_id"];
    $address = $row["city_en"].", ".$row["country_en"];

    $url = $base_url . urlencode($address);

    $xml = simplexml_load_file($url) or die("url not loading");
    if ($xml === false) {

      echo "Failed loading XML for url ".$url."<br>";

      foreach(libxml_get_errors() as $error) {
        echo "\t", $error->message."<br>";
      }
    }

    //echo("xml: ".print_r($xml));

    $status = $xml->status;

    if (strcmp($status, "OK") == 0) {

      // Successful geocode
      $geocode_pending = false;
      $coordinates = $xml->result->geometry->location;
      $lat = $coordinates->lat;
      $lng = $coordinates->lng;

      $sql = "UPDATE cities2 SET lat = '%s', lng = '%s' WHERE city_id = '%s';";

      $query = sprintf($sql,
               mysql_real_escape_string($lat),
               mysql_real_escape_string($lng),
               mysql_real_escape_string($id));

      $update_result = mysql_query($query);

      if (!$update_result) {
          die("Invalid query: " . mysql_error());
      }

      echo "Address " . $address . " encoded (".$lat.",".$lng."). ". "<br>\n";;

    } else {

      // failure to geocode
      echo "Address " . $address . " failed to geocoded (" . $request_url . "). ";
      echo "Received status " . $status . "<br>\n";
    }

    usleep($delay);
}
?>

