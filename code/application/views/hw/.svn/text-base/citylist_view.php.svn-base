<?php
$expires = 86400;
if(!empty($content_type))
{
  header("Content-type: $content_type; charset=utf-8");
}
else
{
  header('Content-type: text/html; charset=utf-8');
}
header("Pragma: public");
header("Cache-Control: max-age=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');

//If data source comes from HW API
if($data_source_api)
{
  //error handling standard
  if($api_error==false)
  {
    //$this->Db_country->get_country($country->countryName,$this->site_lang)
    foreach($cityCountryList->Country as $country)
    {
      echo $javascript_varname."['".$country->countryName."'] = ['".addslashes($this->Db_country->get_country($country->countryName,$this->site_lang))."',new Array(";
      
      $citiespercountry = "'"._('Choisir la ville')."',";
  
      foreach($country->Cities->city as $city)
      {
        $citiespercountry .= "'".addslashes($this->Db_country->get_city($country->countryName,$city,$this->site_lang))."',";
      }
      //Remove last character
      $citiespercountry = substr($citiespercountry,0,(strlen($citiespercountry)-1));
      echo $citiespercountry.")];\n";
    }
  }
  elseif($api_error_msg==false)
  {
    //serveur inaccessible en ce moment
  }
  else
  {
   //Error from api with msg
  }
}
//data source comes from db cache
else
{
  $englishNames = FALSE;
  if(strcasecmp($this->wordpress->get_option("aj_show_encity"),"true")==0)
  {
    $englishNames = TRUE;
  }
//  echo $this->Db_country->get_cached_js_cities_data($javascript_varname, $this->site_lang, $englishNames);
  echo $this->Db_country->get_cached_xml_cities_data($javascript_varname,$this->site_lang,$englishNames);
}
?>