
function display_compare_box(compare_values){
var compare_data = $('#compare_data');
var total_com_property = $('#total_com_property');
var property_compare  = $('#property_compare');
var total_pro = compare_values.split(",");
var total_pro1= total_pro.length;
	if(total_pro1>1)
		{  
			$('#comparelink').css("display","inline-block");
			$('.head123 p').css("width","84%");
		}
	$.ajax({
		type:'GET',
			url:'http://'+window.location.host+'/cmain/ajax_property_compare/'+compare_values,
		success:function(data){
			compare_data.html(data);
			total_com_property.val(total_pro1);
			property_compare.css("display","block");	
		}
	});

}

function compare_property(value,proname,protype)
{  
   this.total_com_property = $('#total_com_property');
   this.property_compare = $('#property_compare');
   this.comparelink = $('#comparelink');
   this.class_head = $('.head123 p');
   this.limit_compare_message = $('#limit_compare_message').val();
   this.compare_count = $('.compare_count');
   
	if($('#pro_compare_'+value).is(':checked'))
	{		
		if(this.total_com_property.val()==5)
		{   
			$('#pro_compare_'+value).attr('checked', false);
			alert(this.limit_compare_message);
			return false;
		}
		else
		{
			$('#proselect_'+value).fadeIn();
				setTimeout(function(){
	        	$('#proselect_'+value).fadeOut();
	   		 }, 5000);
			this.compare_count.html(parseInt(this.total_com_property.val())+1);
			this.total_com_property.val(parseInt(this.total_com_property.val())+1);
			var compare_pro = getCookie('compare');
			if(compare_pro!='')
			{
			    var haschacked= false;
				compare_proSplit = compare_pro.split(",");
				compare_pro1 = new Array();
				if(compare_pro!= ''){
				    for(i=0;i<compare_proSplit.length;i++){
				        compare_pro1[i] = compare_proSplit[i];
							if(value==compare_proSplit[i]){
								haschacked=true;
							}
				    }
				}
				
				if(haschacked== false)
				{
					compare_pro1.push(value)
				}
				pweb_setCookie("compare",compare_pro1,24);
			}
			else
			{	
			 	var compare_cookie = new Array(value);
				pweb_setCookie("compare",compare_cookie,24);
			}
			if(this.total_com_property.val()==1)
			{
				$.ajax({
					type:'GET',
					url:'http://'+window.location.host+'/cmain/ajax_compare_property_data/'+value,
					success:function(propertydata){
					 var prtype = propertydata[1] ;
					 $('#compare_data').html('<div id=property_'+value+' class="show-data"><div class="show-data-first-colum">'+propertydata+'</div><div class="show-data-last-colum"><a href="#" onclick="remove_pro('+value+');">X</a></div><input type="hidden" name="property_id[]" id="property_id_'+value+'" value="'+value+'"/></div>');	
					}
				});
			}
			else
			{
				$.ajax({
					type:'GET',
					url:'http://'+window.location.host+'/cmain/ajax_compare_property_data/'+value,
					success:function(propertydata){
					$('#compare_data').append('<div id=property_'+value+' class="show-data"><div class="show-data-first-colum">'+propertydata+'</div><div class="show-data-last-colum"><a href="#" onclick="remove_pro('+value+');">X</a></div><input type="hidden" name="property_id[]" id="property_id_'+value+'" value="'+value+'"/></div>');
				   }
				});
				this.comparelink.css("display","inline-block");
				this.class_head.css("width","84%");
			}
		}	
	}
	else
	{
			var compare_pro2 = getCookie('compare');
			if(compare_pro2!='')
			{
				compare_proSplit = compare_pro2.split(",");
				compare_pro3 = new Array();
				if(compare_pro2!= ''){
				    for(i=0;i<compare_proSplit.length;i++){
				        compare_pro3[i] = compare_proSplit[i];
				    }
				}
				compare_pro3.splice(compare_pro3.indexOf(value),1);
				pweb_setCookie("compare",compare_pro3,24);
			}	
		this.compare_count.html(parseInt(this.total_com_property.val())-1);
		this.total_com_property.val(parseInt(this.total_com_property.val())-1);
		$('#property_'+value).remove();
	}
	if(this.total_com_property.val()>0)
	{
		this.property_compare.css("display","block");	
	}
	else
	{
		this.property_compare.css("display","none");		
	}
	if(this.total_com_property.val()==1)
	{
		this.comparelink.css("display","none");
		this.class_head.css("width","91.5%");
	}
}


function remove_pro(value)
{	this.compare_count = $('.compare_count');
	this.total_com_property = $('#total_com_property');
    this.property_compare = $('#property_compare');
	this.comparelink = $('#comparelink');
    this.class_head = $('.head123 p');
	this.compare_data = $('#compare_data');
	
	if(value!='')
	{
		var compare_pro2 = getCookie('compare');
			if(compare_pro2!='')
			{
				compare_proSplit = compare_pro2.split(",");
				compare_pro3 = new Array();
				if(compare_pro2!= ''){
				    for(i=0;i<compare_proSplit.length;i++){
				        compare_pro3[i] = compare_proSplit[i];
				    }
				}
				compare_pro3.splice(compare_pro3.indexOf(value),1);
				pweb_setCookie("compare",compare_pro3,24);
			}
		$('#property_'+value).remove();	
		$('#pro_compare_'+value).attr('checked', false);
		this.compare_count.html(parseInt(this.total_com_property.val())-1);
		this.total_com_property.val(parseInt(this.total_com_property.val())-1);
		if(this.total_com_property.val()==1)
		{
			this.comparelink.css("display","none");
			this.class_head.css("width","91.5%");
		}
		if(this.total_com_property.val()==0)
		{
			this.property_compare.css("display","none");	
		}	
	}
	else
	{
		var compare_pro2 = getCookie('compare');
			if(compare_pro2!='')
			{
				compare_proSplit = compare_pro2.split(",");
				compare_pro3 = new Array();
				if(compare_pro2!= ''){
				    for(i=0;i<compare_proSplit.length;i++){
						compare_pro3.splice(compare_pro3.indexOf(compare_proSplit[i]),1);
						pweb_setCookie("compare",compare_pro3,24);
				    }
				}
			}
		this.compare_data.html('');
		$('input[name=pro_compare]').attr('checked', false);
		this.compare_count.html(0);
		this.total_com_property.val(0);
		this.property_compare.css("display","none");
	}
}
function property_compare_popup()
{
	var chks = document.getElementsByName('property_id[]');
	var pro_id='';
	for (var i = 0; i < chks.length; i++)
	{
		if(i==0){
				pro_id=chks[i].value;		
			}
			else{
				pro_id=pro_id+','+chks[i].value;		
			}
	}
	var ajaxrequest =
	$.ajax({
		type:'GET',
		dataType: "json",
		url:'http://'+window.location.host+'/cmain/ajax_compare_property/'+pro_id,
		success:function(data){
		
			$('#property_compare_data').empty().html(data['html']);
			pweb_filter.addFilterMap('city', 'map_canvas', 'en', data.map_data[0].Geo.Latitude,data.map_data[0].Geo.Longitude);
			pweb_filter.addFilterMap('property', 'map_canvas', 'en', data.map_data[0].Geo.Latitude,data.map_data[0].Geo.Longitude);
			pweb_filter.pweb_maps['city'].prop_number_to_focus = pro_id;
			pweb_filter.pweb_maps['property'].prop_number_to_focus = pro_id;
			pweb_filter.pweb_maps['city'].updateMarkers(data.map_data);
			pweb_filter.pweb_maps['city'].enableMap();
			$('#map_lat').val(JSON.stringify(data.map_data));			
		}
	});
	
	    $('.fancybox-close,.fancybox-overlay').live('click', function(){ 
			ajaxrequest.abort();
		});
}

$(document).ready(function() {
	$(".compare_displaypopup").fancybox({
		'titlePosition'		: 'inside',
		'transitionIn'		: 'none',
		'transitionOut'		: 'none'
	});
});