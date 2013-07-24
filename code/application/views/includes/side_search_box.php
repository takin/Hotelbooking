<?php
$switch_api = false;
if ($this->session->userdata('switch_api')) {
    $switch_api = true;
}
?>
<div class="box_content box_round group side_search" id="side_search_box">

    <?php if (($current_view == "city_view") || ($current_view == "hostel_view")) {
        ?>
        <span id="modify_search">
            <a class="modify_search collapse" >
                <strong>
                    <?php
                    if ($switch_api) {
                        echo _('Enter your dates');
                    } else {
                        echo _('Modify search');
                    }
                    ?>
                </strong>
            </a>
        </span>
        <span id="search_now" style="display:none;" class="search_title"><?php echo _('Search Now') ?></span>
    <?php } else { ?>
        <span class="search_title"><?php echo _('Search Now') ?></span>
    <?php } ?>
    <?php
    if (($current_view == "city_view") || ($current_view == "hostel_view")) {
        $side_search_wrap = "side_search_wrap_city";
    } else {
        $side_search_wrap = "side_search_wrap";
    }
    ?>

    <div id="<?php echo $side_search_wrap; ?>" >
        <script>
            $(document).ready(function() {
<?php if (!isset($country_selected) || ($country_selected === NULL)): ?>
                    loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>", "<?php echo _('Chargement...'); ?>", 'cities', cities, 'search-country', 'search-city');
<?php elseif (!isset($city_selected) || ($city_selected === NULL)): ?>
                    loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>", "<?php echo _('Chargement...'); ?>", 'cities', cities, 'search-country', 'search-city', "<?php echo $country_selected; ?>");
<?php else: ?>

                    loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>", "<?php echo _('Chargement...'); ?>", 'cities', cities, 'search-country', 'search-city', "<?php echo $bc_country; ?>", "<?php echo $bc_city; ?>");
<?php endif; ?>
            });
            function keyaction(e)
            {
                if (!$('ul#suggestion').is(':visible') && e.keyCode == 13)
                {
                    goToSearchPage('<?php echo site_url(); ?>', '<?php echo _('Choisir le pays'); ?>', '<?php echo _('Pays introuvable'); ?>', '<?php echo _('Choisir la ville'); ?>', '<?php echo _('Ville introuvable'); ?>', '<?php echo _('Date invalide'); ?>', 'search-country', 'search-city', 'datepick', 'search-night', 'search-currency', 'search-custom');
                }
            }
        </script>
        <form class="group side_search" id="side_search" action="" method="post">

<?php
if (!empty($date_selected)) {
    $date_selected = $date_selected;
} else if (!empty($_COOKIE["date_selected"])) {
    $date_selected = $_COOKIE["date_selected"];
} else {
    $date_selected = get_date_default();
}

if (!empty($numnights_selected)) {
    $numnights_selected = $numnights_selected;
} elseif (!empty($_COOKIE["numnights_selected"])) {
    $numnights_selected = $_COOKIE["numnights_selected"];
} else {
    $numnights_selected = 2;
}
?>

            <script>
                $(document).ready(
                        function()
                        {
                            var date_avail = getCookie('date_selected');
                            if (isValidDate(date_avail))
                            {
                                date_avail_list = date_avail.split('-');
                                /* Extract year, month and date seprately
                                 This is done to  make date function cross browser compatible by passing all of them explicilty
                                 */
                                var year = date_avail_list[0];
                                var month = date_avail_list[1] - 1;
                                var day = date_avail_list[2];
                                date_avail = new Date(year, month, day);
                                $("#datepick").datepicker("setDate", date_avail);
                            }
                            else
                            {
                                date_avail = new Date();
                                date_avail.setDate(date_avail.getDate() + 10);
                                $("#datepick").datepicker("setDate", date_avail);
                            }

                            var numnight_avail = getCookie('numnights_selected');
                            if (numnight_avail)
                            {
                                document.getElementById('search-night').value = numnight_avail;
                            }
                            else
                            {
                                numnight_avail = 2;
                                document.getElementById('search-night').value = numnight_avail;
                            }

<?php /* ?>var search_input_terms = decodeURI(getCookie('search_input_terms'));
  if(search_input_terms)
  {
  document.getElementById('search-custom').value = search_input_terms;
  }
  else
  {
  document.getElementById('search-custom').value = '<?php echo _('Enter a city name or hostel name');?>';
  }<?php */ ?>

                            function getURLParameter(name) {
                                return decodeURI(
                                        (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search) || [, null])[1]
                                        );
                            }
                            var currency_value = getURLParameter('currency');
                            if (!currency_value || currency_value == "null")
                            {
                                currency_value = getCookie('currency_selected');
                            }
                            if (currency_value)
                            {
                                //document.getElementById('search-currency').value = currency_value;
                            }
                            else
                            {
                                currency_value = '<?php echo $this->config->item('site_currency_default') ?>';
                                //document.getElementById('search-currency').value = currency_value;
                            }
                        }
                );
            </script>

<?php
if (!empty($search_term)) {
    ;
}
//        elseif(!empty($_COOKIE["search_input_terms"]))
//        {
//          $search_term = urldecode($_COOKIE["search_input_terms"]);
//        }
?>
            <input style="display:none;"  id="type_search_choice" class="type_search" type="radio" name="type_search" value="1" checked="checked">
            <input style="display:none;" id="type_search_keyword" class="type_search" type="radio" name="type_search" value="2">
            <label class="notshow" for="search-custom"><?php echo _('Search by city or hostel name:'); ?></label>
            <?php
            /* if (isset($search_term))
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
              } */
            ?>
            <label class="notshow" for="search-country"><?php echo _('Spécifier le pays'); ?></label>
            <select id="search-country" name="search-country" class="search_country" onchange="setCities('<?php echo _('Choisir la ville'); ?>', 'search-country', 'search-city');">
                <option value="no_country_selected"><?php echo _('Choisir le pays'); ?></option>
            </select>

            <label class="notshow" for="search-city"><?php echo _('Spécifier la ville'); ?></label>
            <select id="search-city" name="search-city" class="search_city">
                <option value="no_city_selected"><?php echo _('Choisir la ville'); ?></option>
            </select>

            <div class="search_suggest_block">
                <input class="textinput text_suggest disabled" type="text" id="search-custom" name="search-custom" onkeypress="keyaction(event)" onkeyup="searchSuggest(event, '<?php echo site_url(); ?>', 'all', 1, 0);" autocomplete="off" value="<?php echo _('Enter a city name or hostel name'); ?>" />
                <img style="display:none;" id="input-loading" src="<?php echo base_url(); ?>images/input-loading.gif" alt="" />
                <span id="search-suggest"></span>
            </div>

            <div class="group">
                <div class="left">
                    <label for="search-date"><?php echo _('Arrivée le:'); ?></label>
                    <input type="text" id="datepick" name="datepick" class="search_date" value="<?php echo $date_selected; ?>" />
                </div>
                <div class="left">
<?php
$hb_api_used = ($this->api_used == HB_API) ? TRUE : FALSE;

select_nights(_('Nuits:'), "search-night", "search-night", $numnights_selected, $hb_api_used);
?>
                </div>
            </div>



<?php if ($current_view != "auth/reset_password_form") { ?>
                <!-- // this goes to header (@sasya karpin) 
                    <label for="search-currency"><?php echo _("Devise:"); ?></label>
                        <?php $this->Db_currency->select_currency("search-currency", "search-currency", $this->config->item('site_currency_selected'), "", $this->site_lang); ?>
                    <?php } ?>
                    -->

            <div class="searchcenter">
                <input onfocus="this.blur()" type="button" name="search-submit" class="box_round button-blue side_submit hoverit" id="search-submit" onclick="goToSearchPage('<?php echo site_url(); ?>', '<?php echo _('Choisir le pays'); ?>', '<?php echo _('Pays introuvable'); ?>', '<?php echo _('Choisir la ville'); ?>', '<?php echo _('Ville introuvable'); ?>', '<?php echo _('Date invalide'); ?>', 'search-country', 'search-city', 'datepick', 'search-night', 'search-currency', 'search-custom')" value="<?php echo _('Search Now') ?>"/>
            </div>
<?php /* ?>
  <?php //removed for now so it is always showing as expanded ?>
  <a class="more_options" id="more_options_side" href="#">+ <?php echo _("More Options");?></a>
  <a class="more_options" style="display:none;" id="less_options_side" href="#">+ <?php echo _("Less Options");?></a><?php */ ?>

            <input type="hidden" id="custom-type" name="custom-type" value =""/>
            <input type="hidden" id="custom-url"  name="custom-url" value =""/>
        </form>
    </div>
</div>
