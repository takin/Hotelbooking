<?php

//$aubergedb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);
//$aubergedb->hide_errors();

function prep_url($str = '')
{
  if ($str == 'http://' OR $str == '')
  {
    return '';
  }

  if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://')
  {
    $str = 'http://'.$str;
  }

  return $str;
}
function generate_sitemap($site_domain,$api_used,$db)
{
  require_once('sitemap-writer.php');
  require_once(get_template_directory()."/ci/ci_wp_helper.php");
  require_once(get_template_directory()."/ci/db_wp_links.php");
  require_once(get_template_directory()."/ci/db_wp_country.php");
  require_once(get_template_directory()."/ci/db_wp_hostels.php");
  
  $DomainName = str_replace(array("www.","."), array("","-"), $site_domain);
  $fileName   = "sitemap-".$DomainName.".xml";
  $stylesheet = "http://".$site_domain.parse_url(get_bloginfo("wpurl"), PHP_URL_PATH)."/wp-content/plugins/auberges_admin/sitemap.xsl";
	$sitemappath = $_SERVER["DOCUMENT_ROOT"].'/ci/sitemaps/';
	
  $sitemapGen = new SitemapWriter($sitemappath,$fileName,$stylesheet);
  
  if($sitemapGen->getSitemapError()==FALSE)
  {
    $aj_links   = new Db_links($db,$site_domain);
    $aj_country = new Db_country($db);
    $aj_hostels  = new Db_hostels($db);
    
    $lang       = $aj_links->get_lang_from_domain($site_domain);
    $domain_url = prep_url($site_domain);
    
    $sitemapGen->initSitemap();
    
    $homeURLcount = 0;
    //Add root site URL
    $sitemapGen->AddUrl($domain_url);
    $homeURLcount++;
    
    //Add hostel homepage URL
    $sitemapGen->AddUrl($domain_url."/".$aj_links->get_link("homepage"));
    $homeURLcount++;
    
    //Add continents URLs
    $continentsURLcount = 0;
    foreach($aj_country->get_all_continents($lang) as $continent)
    {
      $sitemapGen->AddUrl($domain_url."/".$continent->continent);
      $continentsURLcount++;
    }
    
    $countriesURLcount = 0;
    $citiesURLcount    = 0;
    $hostelsURLcount   = 0;
    
    if($api_used == "HB")
    {
      //Add HB countries URLs
      foreach($aj_country->get_all_hb_countries($lang) as $country)
      {
        
        $continent_url = $country->continent_lang;
        if(empty($continent_url))
        {
          //Case continent hb code is -> ic, me
          switch($country->continent_hb_code)
          {
             case "me":
              $continent_url = $aj_country->get_continent("asia",$lang);;
              break;
             case "ic":
              $continent_url = $aj_country->get_continent("asia",$lang);;
              break;
          }
        }
        
        $country_url = $country->country;
        if(empty($country_url))
        {
          $country_url = $country->country_en;
        }
        
        $sitemapGen->AddUrl($domain_url."/".$continent_url."/".customurlencode($country_url));
        $countriesURLcount++;
      }
      
      //Add HB cities URLs
      foreach($aj_country->get_all_hb_cities($lang) as $city)
      {
        $display_city = $city->display_city;
        if(empty($display_city))
        {
          $display_city = $city->city_lname_en;
        }
        
        $display_country = $city->display_ountry;
        
        if(empty($display_country))
        {
          $display_country = $city->country_lname_en;
        }
        
        $sitemapGen->AddUrl($domain_url."/".customurlencode($display_country)."/".customurlencode($display_city));
        $citiesURLcount++;
      }
      
      //Add HB hostels URLs
      foreach($aj_hostels->get_all_hb_hostels() as $hostel)
      {
        $sitemapGen->AddUrl(build_property_page_link($hostel->property_type,$hostel->property_name,$hostel->property_number,$site_domain));
        $hostelsURLcount++;
      }
    }
    else 
    {
      
      //Add HW countries URLs
      foreach($aj_country->get_all_countries($lang) as $country)
      {
        $sitemapGen->AddUrl($domain_url."/".$country->continent."/".customurlencode($country->country));
        $countriesURLcount++;
      }
      
      //Add HW cities URLs
      foreach($aj_country->get_all_cities($lang) as $city)
      {
        $sitemapGen->AddUrl($domain_url."/".customurlencode($city->country)."/".customurlencode($city->city));
        $citiesURLcount++;
      }
      
      //Add HW hostels URLs
      foreach($aj_hostels->get_all_hostels() as $hostel)
      {
        $sitemapGen->AddUrl(build_property_page_link($hostel->property_type,$hostel->property_name,$hostel->property_number,$site_domain));
        $hostelsURLcount++;
      }
    }
    
    
    $sitemapGen->closeSitemap();
    
  }
  
  if($sitemapGen->getSitemapError()==FALSE)
  {
    echo "<a href=\"$domain_url/$fileName\" target=\"_blank\">$fileName</a>";
    
    echo "<tr><td><table>";
    echo "<tr><td width=\"400\">URL Homes<td><td width=\"200\">$homeURLcount</td></tr>";
    echo "<tr><td width=\"400\">URL Continents<td><td width=\"200\">$continentsURLcount</td></tr>";
    echo "<tr><td width=\"400\">URL Countries<td><td width=\"200\">$countriesURLcount</td></tr>";
    echo "<tr><td width=\"400\">URL Cities<td><td width=\"200\">$citiesURLcount</td></tr>";
    echo "<tr><td width=\"400\">URL hostels<td><td width=\"200\">$hostelsURLcount</td></tr>";
    echo "<tr><td width=\"400\">URL Totals<td><td width=\"200\">".$sitemapGen->get_url_count()."</td></tr>";
    echo "</table></td></tr>";
  }
  else
  {
    echo $sitemapGen->getSitemapStatus();
  }
}

?>
<div class="wrap">
<h2>Administration</h2>

<form method="post" action="">
    <?php //settings_fields( 'baw-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Build sitemap</th>
        <td>
          <select name="domain">
            <?php 
            global $aubergedb;
            $sql_query = "SELECT * FROM `site_domains`";
            $results = $aubergedb->get_results($sql_query);
            foreach($results as $row)
            {
              ?>
              <option value="<?php echo $row->site_domain; ?>" <?php if($_POST["domain"]==$row->site_domain) echo "selected";?>><?php echo $row->site_domain; ?></option>
              <?php
            }
            ?>
          </select>
          <select name="hostels_api_used">
            <option value="HW" <?php if($_POST["hostels_api_used"] =="HW") echo "selected";?>>HostelWorld</option>
            <option value="HB" <?php if($_POST["hostels_api_used"] =="HB") echo "selected";?>>HostelBookers</option>
          </select>
          <input class="button" type="submit" name="sitemap_gen" value="Générer" />
        
          <?php 
            if(!empty($_POST["sitemap_gen"])&&(!empty($_POST["domain"]))&&(!empty($_POST["hostels_api_used"])))
            {
              generate_sitemap($_POST["domain"],$_POST["hostels_api_used"],$aubergedb);
            }
          ?>
        </td>
        </tr>
        
    </table>
</form>

</div>