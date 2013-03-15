function display_property_pics(propertyNumber,propertyName,property_pics_div_id)
{
//	$("#avail-overview-"+property_pics_div_id).hide();
	$("#loading-pics-"+propertyNumber).show();

	$("#"+property_pics_div_id).html('');
	$.ajax({type:"post",
			url:"http://"+window.location.host+"/prop_pics/",
			data: { propertyNumber: propertyNumber,propertyName: propertyName },
			timeout:10000,
			error:function(XMLHttpRequest, textStatus, errorThrown)
			{
				$("#"+property_pics_div_id).html("<ul class=\"error\"><li>Erreur.<li>"+textStatus+"</li><li>"+errorThrown+"</li></li></ul>");
				$("#loading-pics-"+propertyNumber).hide();
			},
			success:function(data)
			{
				$("#"+property_pics_div_id).html(data);
				$("#loading-pics-"+propertyNumber).hide();
				
				$('#thumbnail_list_'+propertyNumber+' img').jail({effect:"fadeIn"});
				$('#slideshow_'+propertyNumber+' img').jail({effect:"fadeIn", callback : startslideshowlist(propertyNumber)});
				
				$('#thumbnail_list_'+propertyNumber+' a.openup').bind('mouseover',function(){
					$('#thumbnail_list_'+propertyNumber+' a.openup').fancybox();
					return false;
				});
			}
		});
}
function startslideshowlist(propertyNumber)
{
	var main_pic = $('#main-pic-'+propertyNumber);
	if (main_pic.length){
		main_pic.cycle({
			fx:      'fade',
			timeout:  7000
		});
	}
}