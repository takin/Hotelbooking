<div id="mainbar" class="clearfix" >
  <script>
  $(document).ready(function(){
	    <?php if(!isset($country_selected)||($country_selected===NULL)):?>
	     loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>","<?php echo _('Chargement...');?>",'cities',cities,'search-country','search-city');
	    <?php elseif(!isset($city_selected)||($city_selected===NULL)):?>
	     loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>","<?php echo _('Chargement...');?>",'cities',cities,'search-country','search-city',"<?php echo $country_selected;?>");
	    <?php else:?>
	     loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>","<?php echo _('Chargement...');?>",'cities',cities,'search-country','search-city',"<?php echo $country_selected;?>","<?php echo $city_selected; ?>");
	    <?php endif;?>
  });
  function keyaction(e)
  {
	  if (!$('ul#suggestion').is(':visible') && e.keyCode == 13)
    {
      goToSearchPage('<?php echo site_url();?>','<?php echo _('Choisir le pays'); ?>','<?php echo _('Pays introuvable'); ?>','<?php echo _('Choisir la ville'); ?>','<?php echo _('Ville introuvable'); ?>','<?php echo _('Date invalide'); ?>','search-country','search-city','datepick','search-night','search-currency','search-custom');
	  }
  }
  </script>
  <form class="clearfix" id="search-form" action="" method="post">
    <ul>

			<li id="city-input">
  			<label class="notshow" for="search-country"><?php echo _('Spécifier le pays');?></label>
  			<select id="search-country" name="search-country" onchange="setCities('<?php echo _('Choisir la ville'); ?>','search-country','search-city');">
  			<option value="no_country_selected"><?php echo _('Choisir le pays'); ?></option>
  			</select>
			</li>
			<li>
  			<label class="notshow" for="search-city"><?php echo _('Spécifier la ville');?></label>
  			<select id="search-city" name="search-city">
  			<option value="no_city_selected"><?php echo _('Choisir la ville'); ?></option>
  			</select>
			</li>

      <li>
        <?php
        if(!empty($date_selected))
        {
          ;
        }
        elseif(!empty($_COOKIE["date_selected"]))
        {
          $date_selected = $_COOKIE["date_selected"];
        }
        else
        {
          $date_selected = get_date_default();
        }
        if(!empty($numnights_selected))
        {
          ;
        }
        elseif(!empty($_COOKIE["numnights_selected"]))
        {
          $numnights_selected = $_COOKIE["numnights_selected"];
        }
        else
        {
          $numnights_selected = 2;
        }
        ?>
        <script>
        $(document).ready(

            function()
            {
              var date_cookie = getCookie('date_selected');
              if(isValidDate(date_cookie))
              {
					var date_array = date_cookie.split('-');
					var date_avail 	= new Date(date_array[0],date_array[1]-1,date_array[2]);
					$("#datepick").datepicker( "setDate" , date_avail );
              }
        		  else
        		  {
        			  var date_avail = new Date();
    						date_avail.setDate(date_avail.getDate()+10);
    						$("#datepick").datepicker( "setDate" , date_avail );
        		  }

              var numnight_avail = getCookie('numnights_selected');
              if(numnight_avail)
              {
                document.getElementById('search-night').value = numnight_avail;
              }
        		  else
        		  {
        		    numnight_avail = 2;
        		    document.getElementById('search-night').value = numnight_avail;
        		  }

              function getURLParameter(name) {
        					return decodeURI(
        							(RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
        					);
        			}
        			var currency_value = getURLParameter('currency');
        			if(!currency_value || currency_value == "null")
        			{
        				currency_value = getCookie('currency_selected');
        			}
        			if(currency_value)
        			{
        				//document.getElementById('search-currency').value = currency_value;
        			}
        			else
        			{
        				currency_value = '<?php echo $this->config->item('site_currency_default')?>';
        				//document.getElementById('search-currency').value = currency_value;
        			}
            }
          );
        </script>

        <label for="search-date"><?php echo _('Arrivée le:');?></label>
        <input type="text" id="datepick" name="datepick" value="<?php echo $date_selected;?>" />

      </li>

      <li>
        <?php
		$hb_api_used = ($this->api_used == HB_API) ? TRUE : FALSE;
        select_nights(_('Nuits:'),"search-night","search-night",$numnights_selected, $hb_api_used );
        ?>
      </li>
      <li style="clear:both;"></li>
			<li id="keyword-input">
        <?php
        if(!empty($search_term))
        {
          ;
        }
//        elseif(!empty($_COOKIE["search_input_terms"]))
//        {
//          $search_term = urldecode($_COOKIE["search_input_terms"]);
//        }
        ?>

  			<label class="notshow" for="search-custom"><?php echo _('Search by city or hostel name:'); ?></label>
  			<?php
  			if (isset($search_term))
  			{
  			  $input_value = $search_term;
  			  $class = ''
  			  ?>
				  <script type="text/javascript">
					 $(function() {$("#search-city").addClass('disabled');$("#search-country").addClass('disabled');});
				  </script>
				  <?php
  			}
  			else
  			{
  			  $input_value = _('Enter a city name or hostel name');
  			  $class = ' disabled';
				}
				?>
				<input class="textinput<?php echo $class;?>" type="text" id="search-custom" name="search-custom" onkeypress="keyaction(event)" onkeyup="searchSuggest(event,'<?php echo site_url();?>','all',1,0);" autocomplete="off" value="<?php echo _('Enter a city name or hostel name');?>" />
				<img style="display:none;" id="input-loading" src="<?php echo base_url();?>images/input-loading.gif" alt="" />
				<span id="search-suggest"></span>
			</li>
			<li style="margin-top:15px;">
        <?php if($current_view != "auth/reset_password_form"){?>
        <label for="search-currency"><?php echo _("Devise:");?></label>
        <?php $this->Db_currency->select_currency("search-currency","search-currency",$this->config->item('site_currency_selected'),"",$this->site_lang); ?>
        <?php }?>
      </li>
			<li class="search-submit">
        <input onfocus="this.blur()" type="button" name="search-submit" id="search-submit" onclick="goToSearchPage('<?php echo site_url();?>','<?php echo _('Choisir le pays'); ?>','<?php echo _('Pays introuvable'); ?>','<?php echo _('Choisir la ville'); ?>','<?php echo _('Ville introuvable'); ?>','<?php echo _('Date invalide'); ?>','search-country','search-city','datepick','search-night','search-currency','search-custom')" value="<?php echo _('Search Now')?>"/>
      </li>
    </ul>
    <input type="hidden" id="custom-type" name="custom-type" value =""/>
    <input type="hidden" id="custom-url"  name="custom-url" value =""/>



    <h1 style="display:none;"><?php echo _("Auberges de jeunesse, Hôtels, Appartements, Chambres d'hôtes, Bed and Breakfast, Pensions - Plus de 30000!!");?></h1>

    <?php /*
    $last_booking = $this->Db_model->get_last_booking_info($this->api_used);

    if(!is_null($last_booking))
    {
      ?>
      <div class="last-booking">
      <p><strong><?php echo _("Dernière Réservation:");?></strong></p>
      <p><?php printf(ngettext("%d nuit", "%d nuits", $last_booking->num_nights), $last_booking->num_nights); ?> -
      <a href="<?php echo $this->Db_links->build_property_page_link($last_booking->property_type,$last_booking->property_name,$last_booking->property_number,$this->site_lang); ?>"><?php echo $last_booking->property_name;?></a></p>
      </div>
      <?php
    }
    */?>
  </form>
	<?php /*?><div id="loading-search" style="position: absolute; top: 115px; left: 205px; display:none;">
    <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="70" height="20">
      <param name="allowFullScreen" value="false" />
      <param name="movie" value="<?php echo base_url(); ?>images/loading-search.swf" />
      <param name="quality" value="high" />
      <param name="wmode" value="transparent" />
      <embed src="<?php echo base_url(); ?>images/loading-search.swf" quality="high" wmode="transparent" width="70" height="20" name="loading-search" align="middle" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" />
    </object>
  </div><?php */?>


	<?php
    /*if ($this->wordpress->get_option('aj_show_stamp'))
    {

      $csspath = $this->wordpress->get_option('aj_api_ascii');

      if(empty($csspath))
      {
        $csspath = $this->wordpress->get_option('aj_api_name');
      }
      ?>
      <a class="openup" href="<?php echo base_url(); ?>guarantee"><img class="guarantee" src="<?php echo base_url();?>images/<?php echo $csspath; ?>/guarantee.png" alt="" /></a>
      <?php
    }*/
  ?>
	<?php if ($current_view == 'city_view' && $searchmode == 0){?>
	<div id="notification" class="notification-search">
		<div class="notification-inside">
			<p><?php echo _("To view prices and availability, please enter your dates.");?></p>
		</div>
		<span class="notification-arrow"></span>
	</div>
	<script>
		$(document).ready(function(){
			$(function(){
				var notifheight = $("#notification").height();
				var notifmargin = notifheight/2;
				$("#notification").css("margin-top","-"+notifmargin+"px");
				$("#notification").delay(3500).fadeIn(400).animate({"left": "-=10px"}, "slow");
			});
		});
	</script>
	<?php }?>
</div><!-- end mainbar -->
