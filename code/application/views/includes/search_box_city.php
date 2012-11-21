<div id="mainbar" class="clearfix" >
	<form class="clearfix" id="search-form" action="" method="post">
    <input type="hidden" id="search-country" name="search-country" value="<?php echo $country_selected;?>" />
		<input type="hidden" id="search-city" name="search-city" value="<?php echo $city_selected;?>" />
		<ul class="city-lp">

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
              var date_avail = getCookie('date_selected');
              if(isValidDate(date_avail))
              {
                document.getElementById('datepick').value = date_avail;
              }
        		  else
        		  {
        			  var date_avail = new Date();
        			  date_avail.setDate(date_avail.getDate()+10);
        		    date_avail = siteDateString(date_avail);
        		    document.getElementById('datepick').value = date_avail;
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
            }
          );
        </script>

        <label for="search-date"><?php echo _('Arrivée le:');?></label>
        <input type="text" id="datepick" name="datepick" value="<?php echo $date_selected;?>" />

      </li>
			<li>
        <?php
        select_nights(_('Nuits:'),"search-night","search-night",$numnights_selected);
        ?>
      </li>

      <li>
        <?php if($current_view != "auth/reset_password_form"){?>
        <label for="search-currency"><?php echo _("Devise:");?></label>
        <?php $this->Db_currency->select_currency("search-currency","search-currency",$this->config->item('site_currency_selected'),"",$this->site_lang); ?>
        <?php }?>
      </li>


      <li class="search-submit-city">
        <input onfocus="this.blur()" type="button" name="search-submit" id="search-submit" onclick="goToSearchPage('<?php echo site_url();?>','<?php echo _('Choisir le pays'); ?>','<?php echo _('Pays introuvable'); ?>','<?php echo _('Choisir la ville'); ?>','<?php echo _('Ville introuvable'); ?>','<?php echo _('Date invalide'); ?>','search-country','search-city','datepick','search-night','search-currency','search-custom')"/>
      </li>
			<?php /*?><li class="guarantee-small">
				<a class="openup" href="<?php echo base_url(); ?>guarantee"><?php echo _('Booking Guarantee')?>: <?php echo _('Up to $100')?></a>
			</li><?php */?>
    </ul>
    <input type="hidden" id="custom-type" name="custom-type" value =""/>
    <input type="hidden" id="custom-url"  name="custom-url" value =""/>
  </form>
</div><!-- end mainbar -->
