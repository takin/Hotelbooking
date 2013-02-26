<div id="content" class="city-view">

	<div class="page-meta group">
		<h1 class="text-shadow-wrapper padit dot-icon"><?php echo _("Trouver une auberge");?></h1>
	</div>

	<div id="search-hostel" class="white-back round-corner5 border-around search-hostel basic">
			<form action="" method="" autocomplete="off">
			<ul class="group">
  			<li id="keyword-input">
    			<label for="search-custom"><?php echo _('Search by city name:'); ?></label>
          <input type="text" value="<?php echo $city_country?>" onkeyup="searchSuggest(event,'<?php echo site_url();?>','cities',0,1);" name="search-custom" id="search-custom" class="text autovalue" autocomplete="off">
    			<span id="search-suggest"></span>
        </li>
  			<li>
    			<label for="search-date"><?php echo _('Arrivée le:');?></label>
          <?php
          select_day("search-day","search-day",$day_selected);
          select_month_year("search-year-month","search-year-month","",0,12,$month_year_selected);
          ?>
  			</li>
  			<li class="quarter">
          <?php
		  $hb_api_used = ($this->api_used == HB_API) ? TRUE : FALSE;
          select_nights(_('Nuits:'),"search-night","search-night",$numnights_selected, $hb_api_used);
          ?>
  			</li>
			</ul>
      <input type="hidden" id="custom-type" name="custom-type" value =""/>
      <input type="hidden" id="custom-url"  name="custom-url" value ="<?php echo $custom_url?>"/>
			</form>
	</div>

  <script>
  function build_search_target()
  {
    if(document.getElementById('custom-url').value)
    {
	    window.location = document.getElementById('custom-url').value + "/" + document.getElementById('search-year-month').value+"-"+document.getElementById('search-day').value+"/"+document.getElementById('search-night').value;
    }
    else
    {
      alert('<?php echo _('Choisir la ville');?>');
    }
  }
  </script>
	<div class="book-now">
		<a class="white green-button"  href="#" onClick="build_search_target(); return false;"><span class="link"><?php echo _('Search Now');?></span></a>
	</div>

</div>