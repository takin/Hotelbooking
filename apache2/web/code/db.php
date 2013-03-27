<?php

$mysqli = new mysqli('92.243.25.30','aj_site','2bVHhwjCGQrRnGW2');
$mysqli->select_db('aj_ci');

$result = $mysqli->query('SELECT * FROM continents2');

while($row = $result->fetch_assoc()) {
print $row['continent_id'].' | '.$row['continent_code'] . '<br/>';

}

$result->close();

?>
