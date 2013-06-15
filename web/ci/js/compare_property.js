$(document).ready(function(){
	$('.hideRowButton').click(function() {
		var rowid = $(this).parent().parent().attr('id');
		$("#" + rowid).fadeOut("slow");    
	});

	$('.closeCompareProperty_btn').live('click', function() {
		var procloseid = parseInt($(this).attr('id')) + 1;

		$("#quick_com_data th:nth-child(" + procloseid + ")").removeClass("control_button").addClass("hiddencolumn");
		$("#quick_com_data td:nth-child(" + procloseid + ")").removeClass("control_button").addClass("hiddencolumn");
		$("#map_td").removeClass("hiddencolumn");
                pweb_filter.updateMarkers("compare_property")
	});

	$('.restore').click(function() { 
		$('.quick_compare_data tr').fadeIn("slow");
		$('.quick_compare_data tr td').addClass("control_button").removeClass("hiddencolumn");
		$('.quick_compare_data tr th').addClass("control_button").removeClass("hiddencolumn");
		$('#showmore').html('Show Less');
	});

	$('#showmore').toggle(
		function() {
			$(".quick_compare_grey1").fadeIn("slow");
			$('#showmore').html("<?php echo _('Show Less')?>");
		},
		function() {
			$(".quick_compare_grey1").fadeOut("slow");
			$('#showmore').html("<?php echo _('Show More')?>");
		}
	);

	$('.link_color a').click(function() {
		var objDiv = document.getElementById("quick_compare_scroll");
		objDiv.scrollTop = objDiv.scrollHeight; 
	});

	$('.head_remove_button a').click(function() {
		$(".quick_compare_head").fadeOut("slow");
		$(".logo_div").fadeOut("slow");    
	});
});

$(function() {
	$('#quick_com_data tr.row_white').mouseover(function() {
		$(this).children("td:eq(0)").css("background-color", "#e5f6fd") ;

	}).mouseout(function() {
		$(this).children("td:eq(0)").css("background-color", "") ;
	})
});

function printdiv(printpageId) {
	var printpage  = $('#printpage');
	var map_canvas_compareProperty = $('#map_canvas_compareProperty');
	var static_map = $('#static_map');

	printpage.css("display","none");

	var img_url = "http://" + window.location.host + "/images/map-marker.png";
	var map_data = $('#map_lat').val();
	var markers_data = JSON.parse(map_data);
	var latlon='';

	for (var i in markers_data) {  
		latlon = 'markers=icon:' + img_url + '|' + markers_data[i].Geo.Latitude + ',' + markers_data[i].Geo.Longitude;   
	}

	$('#map_canvas_compareProperty').css({
		display : 'none',
		width   : '',
		height  : ''
	});

	$('#static_map').css({
		display : 'block',
		width   : '800',
		height  : '350'
	});

	document.getElementById('static_map').innerHTML = '<img width="800" height="350" src="http://maps.googleapis.com/maps/api/staticmap?center=' + markers_data[0].Geo.Latitude + markers_data[0].Geo.Longitude + '&zoom=8&size=900x350&maptype=roadmap&' + latlon + '&sensor=false"/>';

	var html ="<html>" +  document.getElementById(printpageId).innerHTML + "</html>";

	printpage.css("display","block");
	var myWindow=window.open('','','width=200,height=100');
	myWindow.document.write(html);
	myWindow.document.close();
	myWindow.focus();
	myWindow.print();

	map_canvas_compareProperty.css({display:'block',width :'800',height :'350'});
	static_map.css({display:'none',width :'',height :''});
	myWindow.close();
}
