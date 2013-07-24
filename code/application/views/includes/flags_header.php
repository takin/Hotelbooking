<?php if ($this->wordpress->get_option('aj_type_site') == "Youth Hostels"){?>
<select id="language-dropdown">
    <option data-imagecss="flag-en" value="http://www.youth-hostels.co.uk"></option>
    <option data-imagecss="flag-ie" value="http://www.youth-hostels.ie"></option>
    <option data-imagecss="flag-fr" value="http://www.aubergesdejeunesse.com"></option>
    <option data-imagecss="flag-es" value="http://www.alberguesjuveniles.es"></option>
    <option data-imagecss="flag-de" value="http://www.jugendherbergen.eu"></option>
    <option data-imagecss="flag-it" value="http://www.ostellidellagioventu.com"></option>
</select>
<!--<ul class="group flag-header">
	
	<li><a title="Youth-Hostels.co.uk" class="flag-en" href="">Youth-Hostels.co.uk</a></li>
	<li><a title="Youth-Hostels.ie" class="flag-ie" href="http://www.youth-hostels.ie">Youth-Hostels.ie</a></li>			
	<li><a title="AubergesDeJeunesse.com" class="flag-fr" href="">AubergesDeJeunesse.com</a></li>	
	<li><a title="AlberguesJuveniles.es" class="flag-es" href="">AlberguesJuveniles.es</a></li>
	<li><a title="Jugendherbergen.eu" class="flag-de" href="">Jugendherbergen.eu</a></li>
	
	<li><a title="OstelliDellaGioventu.com" class="flag-it" href="">OstelliDellaGioventu.com</a></li>	
</ul> 
-->
<?php }else{ ?>
<select id="language-dropdown" style="width:60px">
    <option data-image="/images/blank.gif" data-imagecss="flag-en-us" data-href="http://www.nofeeshostels.com?currency=USD">&nbsp;</option>
    <option data-image="/images/blank.gif" data-imagecss="flag-en" value="http://www.nofeeshostels.com?currency=GBP">&nbsp;</option>
    <option data-image="/images/blank.gif" data-imagecss="flag-eu" value="http://www.nofeeshostels.com?currency=EUR">&nbsp;</option>
    <option data-image="/images/blank.gif" data-imagecss="flag-fr" value="http://www.auberges.com">&nbsp;</option>
    <option data-image="/images/blank.gif" data-imagecss="flag-es" value="http://www.hostales.com">&nbsp;</option>
    
    <option data-image="/images/blank.gif" data-imagecss="flag-de" value="http://www.herbergen.com">&nbsp;</option>
    <option data-image="/images/blank.gif" data-imagecss="flag-it" value="http://www.hostelli.com">&nbsp;</option>
</select>
<!--
<ul class="group flag-header">
	<li><a title="NoFeesHostels.com" class="flag-en-us" href="http://www.nofeeshostels.com?currency=USD">NoFeesHostels.com</a></li>	
	<li><a title="NoFeesHostels.com" class="flag-en" href="http://www.nofeeshostels.com?currency=GBP">NoFeesHostels.com</a></li>
	<li><a title="NoFeesHostels.com" class="flag-eu" href="http://www.nofeeshostels.com?currency=EUR">NoFeesHostels.com</a></li>
	<li><a title="Auberges.com" class="flag-fr" href="http://www.auberges.com">Auberges.com</a></li>
	<li><a title="Hostales.com" class="flag-es" href="http://www.hostales.com">Hostales.com</a></li>

	<li><a title="Herbergen.com" class="flag-de" href="http://www.herbergen.com">Herbergen.com</a></li>
    <li><a title="Hostelli.com" class="flag-it" href="http://www.hostelli.com">Hostelli.com</a></li>
</ul>
-->
<script>

try {
        var pages = $("#language-dropdown").msDropdown({on:{change:function(data, ui) {
                                                var val = data.value;
                                                if(val!="")
                                                    window.location = val;
                                            }}}).data("dd");

    } catch(e) {
        //console.log(e);    
    }
</script>
<?php } ?>
