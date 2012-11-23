<?php
  if(!empty($_POST["geocode_cities"]))
  {
    include("phpsqlgeocode_xml.php");
  }
  
  if(!empty($_POST["geocode_countries"]))
  {
    include("geocode_countries.php");
  }
?>
<div class="wrap">
<h2>Administration - Auberges - Database</h2>

<form method="post" action="">
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Geodcode all cities in DB</th>
        <td><input class="button" type="submit" name="geocode_cities" value="Geocode" /></td>
        </tr>
        <tr>
        <th scope="row">Geodcode all countries in DB</th>
          <td>
          <input class="button" type="submit" name="geocode_countries" value="Geocode" />
          </td>
        </tr>
    </table>
</form>
</div>