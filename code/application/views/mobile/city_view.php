<div id="content" class="city-view">

	<div class="page-meta group">
		<a class="edit-search" href="<?php echo site_url('m');?>"><?php echo _("Modify search");?></a>
		<h1 class="text-shadow-wrapper marg10bot"><a href="<?php echo site_url('m');?>"><?php echo $city_selected;?>, <?php echo $country_selected;?></a></h1>
	</div>
	  	
	<div class="search-city-cache">
		<h2 class="trigger box-shadow-wrapper round-corner5"><span class="text-shadow-white"><?php echo _("Enter Dates");?></span></h2>
		<div class="trigger-content white-back"<?php if($searchmode < 1){echo ' style="display:block"';};?>>
			<div class="group">
				<div class="group input-block">
				<label for="search-date"><?php echo _('Arrivée le:');?></label>
				<?php
				select_day("search-day","search-day",$day_selected);
				select_month_year("search-year-month","search-year-month","",0,12,$month_year_selected);
				?>
				</div>
			 
				<div class="group">
				<?php
				select_nights(_('Nuits:'),"search-night","search-night",2, $numnights_selected);
				?>
				</div>
				<script>
				function build_search_target()
				{
					window.location = '<?php echo site_url($country_selected."/".$city_selected);?>' + "/" + document.getElementById('search-year-month').value+"-"+document.getElementById('search-day').value+"/"+document.getElementById('search-night').value;
				}
				</script>
				<div class="book-now">
				<a class="white green-button"  href="#" onClick="build_search_target(); return false;"><span class="link"><?php echo _('Search Now');?></span></a>
				</div>
			</div>
		</div>
		<h3><?php echo _("Auberges de jeunesse, Hôtels, Appartements, Chambres d'hôtes, Bed and Breakfast, Pensions - Plus de 30000!!");?></h3>
	</div>
		
  <?php if($property_list['property_count'] != 0){?>

	<div class="city-sort green-button group">
		<a id="sortname-all" class="sorting" href="#"><span><?php echo _("Hostel Name");?></span></a>
		<a id="sortprice-all" class="sorting activesort" href="#"><span class="asc"><?php echo _("Prix");?></span></a>
		<a id="sortcote-all" class="sorting" href="#"><span><?php echo _("Cote");?></span></a>
		<?php /*?><a class="plus" href="#">+</a><?php */?>
	</div>
	<?php }?>
	<div id="all-list">
	<?php
    $count = 0;
    if(!empty($property_list))
    {
      foreach ($property_list as $property_type_list)
      {
        if(is_array($property_type_list))
        {
          foreach ($property_type_list as $property)
          {
            if($this->api_used == HB_API)
            {
              ;
            }
            else
            {
              //if number of night selected equal or higher than minnights, equal or lower than maxnights and availble nights
              if((($numnights_selected >= (int)$property->minNights) &&
                 (($numnights_selected <= (int)$property->maxNights) &&
                 ($numnights_selected <= count($property->AvailableDates->availableDate))))
                 || ($searchmode < 1))
              {
                $count++;
                $this->load->view("mobile/property_list",array("property" => $property,"date_selected" => $date_selected,"numnights_selected" => $numnights_selected));
              }
            }
          }
        }
      }
    }
    if ($count == 0)
    {
      ?>
      <div class="white-back round-corner5 border-around basic content-block">
				<p style="margin-bottom:0px;"><?php echo _('Désolé aucun résultat pour ce critère');?></p>
			</div>
      <?php
    }
    ?>

	</div>
	<div class="bottom-nav">
	<a href="<?php echo site_url('m');?>" class="change-search text-shadow-wrapper">&laquo; <?php echo _("Modify search");?></a>
	<?php if($property_list['property_count'] >= 4){?>
	<a class="black-button round-corner5 small-button totop" href="#"><span class="asc"><?php echo _("Back to top");?></span></a>
	<?php }?>
	</div>

</div>