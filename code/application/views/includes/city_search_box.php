<div class="box_content box_round group side_search" id="side_search_box_city" style="display:none;">
    
    

    <div class="content_block" style="margin-bottom:0px;">
        <script>
        $(document).ready(function(){
                
                 loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>","<?php echo _('Chargement...');?>",'cities',cities,'search-country','search-city',"<?php echo $country_selected;?>","<?php echo $city_selected; ?>");
                
        });
        function keyaction(e)
        {
            if (!$('ul#suggestion').is(':visible') && e.keyCode == 13)
            {
                goToSearchPage('<?php echo site_url();?>','<?php echo _('Choisir le pays'); ?>','<?php echo _('Pays introuvable'); ?>','<?php echo _('Choisir la ville'); ?>','<?php echo _('Ville introuvable'); ?>','<?php echo _('Date invalide'); ?>','search-country','search-city','datepick','book-night','book-property-currency','search-custom');
            }
        }
        </script>
        <form class="group side_search" id="side_search" action="" method="post">

            <?php
            if(!empty($date_selected))
            {
                $date_selected = $date_selected;
                setcookie("date_selected", $date_selected);
            }
             else if(!empty($_COOKIE["date_selected"]))
            {
                $date_selected = $_COOKIE["date_selected"];
            }else
            {
                $date_selected = get_date_default();
            }
             
            if(!empty($numnights_selected))
            {
                $numnights_selected = $numnights_selected;
                setcookie("numnights_selected", $numnights_selected);
            }
            elseif(!empty($_COOKIE["numnights_selected"]))
            {
                $numnights_selected = $_COOKIE["numnights_selected"];
            }else{
                $numnights_selected = 2;
            }
            
            ?>
            
            <script>
            $(document).ready(

                function()
                {
                    jQuery("#book-pick").datepicker({ dateFormat: 'd MM, yy', minDate: 0});
                    var date_avail = getCookie('date_selected');
                    if(isValidDate(date_avail))
                    {
                        date_avail_list = date_avail.split('-');
						/* Extract year, month and date separately
						This is done to make date function cross browser compatible by passing all(year,month,day) explicilty
						*/
						var year = date_avail_list[0];
						var month = date_avail_list[1]-1;
						var day = date_avail_list[2];
						date_avail = new Date(year,month,day);
                        $("#book-pick").datepicker( "setDate" , date_avail );
                    }
                    else
                    {
                        date_avail = new Date();
                        date_avail.setDate(date_avail.getDate()+10);
                        $("#book-pick").datepicker( "setDate" , date_avail );
                    }

                    var numnight_avail = getCookie('numnights_selected');
                    if(numnight_avail)
                    {
                        document.getElementById('book-night').value = numnight_avail;
                    }
                    else
                    {
                        numnight_avail = 2;
                        document.getElementById('book-night').value = numnight_avail;
                    }

                    <?php /*?>var search_input_terms = decodeURI(getCookie('search_input_terms'));
                    if(search_input_terms)
                    {
                        document.getElementById('search-custom').value = search_input_terms;
                    }
                    else
                    {
                        document.getElementById('search-custom').value = '<?php echo _('Enter a city name or hostel name');?>';
                    }<?php */?>

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
                        document.getElementById('book-property-currency').value = currency_value;
                    }
                    else
                    {
                        currency_value = '<?php echo $this->config->item('site_currency_default')?>';
                        document.getElementById('book-property-currency').value = currency_value;
                    }
                }
                );
            </script>

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
            <input style="display:none;"  id="type_search_choice" class="type_search" type="radio" name="type_search" value="1" checked="checked">
            <input style="display:none;" id="type_search_keyword" class="type_search" type="radio" name="type_search" value="2">
            <label class="notshow" for="search-custom"><?php echo _('Search by city or hostel name:'); ?></label>
            

            

            

            <div class="group">
                <div class="left">
                <label for="search-date"><?php echo _('Arrivée le:');?></label>
                <input type="text" id="book-pick" name="book-pick" class="search_date" value="<?php echo $date_selected;?>" />
                </div>
                <div class="left">
                <?php
                select_nights(_('Nuits:'),"book-night","book-night",$numnights_selected);
                ?>
                </div>
                
                <?php if($current_view != "auth/reset_password_form"){?>
                <div class="left">
  <label for="book-night"><?php echo _("Devise:");?></label>
<?php $this->Db_currency->select_currency("book-property-currency","book-property-currency",$this->config->item('site_currency_selected'),"",$this->site_lang); ?>
</div>
<?php }?>
            <div class="left">

<label >&nbsp;</label>
            <input onfocus="this.blur()" type="button" name="search-submit" class="box_round button-green side_submit hoverit" id="search-submit" onclick="goToSearchPage('<?php echo site_url();?>','<?php echo _('Choisir le pays'); ?>','<?php echo _('Pays introuvable'); ?>','<?php echo _('Choisir la ville'); ?>','<?php echo _('Ville introuvable'); ?>','<?php echo _('Date invalide'); ?>','search-country','search-city','book-pick','book-night','book-property-currency','search-custom')" value="<?php echo _('Search Now')?>"/>
            
            <input type="hidden" id="custom-type" name="custom-type" value =""/>
            <input type="hidden" id="custom-url"  name="custom-url" value =""/>
            </div></div>
        </form>
    </div>
</div>
