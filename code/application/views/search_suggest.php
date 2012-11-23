<ul id="suggestion">
<?php
if(!empty($suggestions))
{
  $last_link_type = -1;
  foreach($suggestions as $key => $suggestion)
  {
    //If link type has changed display new title
    if($last_link_type != $suggestion->link_type)
    {
      switch($suggestion->link_type)
			{
				case LINK_PROPERTY:
				  echo "<li class=\"title\">"._("Property")."</li>";
					break;
				case LINK_CITY:
				  echo "<li class=\"title\">"._("City")."</li>";
					break;
				case LINK_COUNTRY:
				  echo "<li class=\"title\">"._("Country")."</li>";
					break;
			}
    }
    $last_link_type = $suggestion->link_type;

    $keys = implode('|', explode(' ', $search_term));
    echo "<li id='sug".$key."'>";

    switch($suggestion->link_type)
		{
			case LINK_PROPERTY:
				$title = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $suggestion->property_name);
				echo "<a rel='".LINK_PROPERTY."' href='".$this->Db_links->build_property_page_link($suggestion->property_type, $suggestion->property_name, $suggestion->property_number, $this->site_lang)."'>".$title.", ".$suggestion->city_lang.", ". $suggestion->country_lang."</a>";
				break;
			case LINK_CITY:
				$cityname = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $suggestion->city_lang);
				$countryname = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $suggestion->country_lang);
				echo  "<a rel='".LINK_CITY."' href='".site_url(customurlencode($suggestion->country_lang)."/".customurlencode($suggestion->city_lang))."'>".$cityname.", ". $countryname."</a>";
				break;
			case LINK_COUNTRY:
				$continentname = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $suggestion->continent_lang);
				$countryname = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $suggestion->country_lang);
				echo "<a rel='".LINK_COUNTRY."' href='".site_url(customurlencode($suggestion->continent_lang)."/".customurlencode($suggestion->country_lang))."'>".$countryname.", ". $continentname."</a>";
				break;
		}
    echo "</li>";

  }
  /*if($show_more_results_link)
  {
    ?>
    <li><a class="more-results" rel='moreresults' href="<?php echo site_url("s/".$search_term);?>"><?php echo _("See more results"); ?> &raquo;</a></li>
    <?php
  }*/
}
else
{
  echo '<li class="no-results">'._("Sorry but we have not found any results. Please try again.").'</li>';
}
?>
</ul>