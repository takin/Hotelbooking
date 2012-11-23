<script type="text/javascript">

window.onload = function()
{
	var height = $(window).height();
	$('#map_canvas').height(height);	
	single_map_init('map_canvas','<?php echo $title; ?>',<?php echo $latlng; ?>,'<?php echo $codeAddress;?>');
}

window.onresize = function() {
  var height = $(window).height();
	$('#map_canvas').height(height);
	
}


</script>

<div style="position:absolute;top:0px; left:0px; width:100%;"id="map_canvas"></div>