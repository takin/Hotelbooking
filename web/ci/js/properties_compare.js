
function display_compare_box(compare_values) {
	var compare_data       = $('#compare_data');
	var total_com_property = $('#total_com_property');
	var property_compare   = $('#property_compare');
	var total_pro          = compare_values.split(",");
	var total_pro1         = total_pro.length;

	if (total_pro1 > 1) {  
		$('#comparelink').css("display", "inline-block");
		$('.head123 p').css("width", "84%");
	}

	$.ajax({
		type:'GET',
		url:'http://'+window.location.host+'/cmain/ajax_property_compare/'+compare_values,
		success:function(data){
			compare_data.html(data);
			total_com_property.val(total_pro1);
			//property_compare.css("display", "block");	
		}
	});
}

function compare_property(value,proname,protype) { 
	this.total_com_property = $('#total_com_property');
	this.property_compare = $('#property_compare');
	this.comparelink = $('#comparelink');
	this.class_head = $('.head123 p');
	this.limit_compare_message = $('#limit_compare_message').val();
	this.compare_count = $('.compare_count');

	if ($('#pro_compare_' + value).is(':checked')) {
		// must be at least 2 hostels to compare
		if (total_com_property.val() >= 0 && total_com_property.val() <= 4) {
			$('input[name="pro_compare"]:checked').parent().find('label').css('color', '#3087C9').css('cursor', 'pointer');
//			$('#pro_compare_' + value).parent().find('label').css('color', '#3087C9');
		}

		if (this.total_com_property.val() == 5) {
			$('#pro_compare_'+value).attr('checked', false);
			alert(this.limit_compare_message);
			return false;
		}
		else {
	//		$('#proselect_'+value).fadeIn();
	//		setTimeout(function(){
	  //      		$('#proselect_'+value).fadeOut();
	   //		}, 2000);
			this.compare_count.html(parseInt(this.total_com_property.val())+1);
			this.total_com_property.val(parseInt(this.total_com_property.val())+1);
			var compare_pro = getCookie('compare');

			if (compare_pro != '') {
				var haschacked= false;
				compare_proSplit = compare_pro.split(",");
				compare_pro1 = new Array();

				if (compare_pro != '') {
					for (i = 0; i < compare_proSplit.length; i++) {
						compare_pro1[i] = compare_proSplit[i];
						if (value == compare_proSplit[i]) {
							haschacked=true;
						}
					}
				}
				
				if (haschacked == false) {
					compare_pro1.push(value)
				}

				pweb_setCookie("compare", compare_pro1, 24);
			}
			else {
			 	var compare_cookie = new Array(value);
				pweb_setCookie("compare",compare_cookie,24);
			}

			if(this.total_com_property.val() == 1) {
				$.ajax({
					type:'GET',
					url:'http://'+window.location.host+'/cmain/ajax_compare_property_data/'+value,
					success:function(propertydata){
						 var prtype = propertydata[1] ;
						 $('#compare_data').html('<div id=property_'+value+' class="show-data"><div class="show-data-first-colum">'+propertydata+'</div><div class="show-data-last-colum"><a href="#" onclick="remove_pro('+value+');">X</a></div><input type="hidden" name="property_id[]" id="property_id_'+value+'" value="'+value+'"/></div>');	
					}
				});
			}
			else {
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
	else {
		$('#pro_compare_' + value).parent().find('label').css('color', '#000').css('cursor', 'default');
		// must be at least 2 hostels to compare
		if (total_com_property.val() == 1) {
			$('input[name="pro_compare"]:checked').parent().find('label').css('color', '#000').css('cursor', 'pointer');
		}

		var compare_pro2 = getCookie('compare');

		if (compare_pro2 != '') {
			compare_proSplit = compare_pro2.split(",");
			compare_pro3 = new Array();

			if (compare_pro2 != '') {
				for (i = 0; i < compare_proSplit.length; i++) {
				        compare_pro3[i] = compare_proSplit[i];
				}
			}
			
			compare_pro3.splice(compare_pro3.indexOf(value),1);
			pweb_setCookie("compare",compare_pro3,24);
		}
	
		this.compare_count.html(parseInt(this.total_com_property.val()) - 1);
		this.total_com_property.val(parseInt(this.total_com_property.val()) - 1);

		$('#property_' + value).remove();
	}

	if(this.total_com_property.val()>0) {
		//this.property_compare.css("display","block");	
	}
	else {
		//this.property_compare.css("display","none");		
	}

	if (this.total_com_property.val() == 1) {
		this.comparelink.css("display","none");
		this.class_head.css("width","91.5%");
	}

	//$(document).scrollTop(10);
}


function remove_pro(value) {
	this.compare_count      = $('.compare_count');
	this.total_com_property = $('#total_com_property');
	this.property_compare   = $('#property_compare');
	this.comparelink        = $('#comparelink');
	this.class_head         = $('.head123 p');
	this.compare_data       = $('#compare_data');

	if (value!='') {
		var compare_pro2 = getCookie('compare');

		if (compare_pro2 != '') {
			compare_proSplit = compare_pro2.split(",");
			compare_pro3 = new Array();

			if (compare_pro2 != '') {
				for (i = 0; i < compare_proSplit.length; i++) {
					compare_pro3[i] = compare_proSplit[i];
				}
			}

			compare_pro3.splice(compare_pro3.indexOf(value), 1);
			pweb_setCookie("compare", compare_pro3, 24);
		}

		$('#property_' + value).remove();	
		$('#pro_compare_' + value).attr('checked', false);

		this.compare_count.html(parseInt(this.total_com_property.val()) - 1);
		this.total_com_property.val(parseInt(this.total_com_property.val()) - 1);

		if (this.total_com_property.val() == 1) {
			this.comparelink.css("display", "none");
			this.class_head.css("width", "91.5%");
		}

		if (this.total_com_property.val() == 0) {
			this.property_compare.css("display", "none");
		}	
	}
	else {
		var compare_pro2 = getCookie('compare');

		if (compare_pro2 != '') {
			compare_proSplit = compare_pro2.split(",");
			compare_pro3 = new Array();

			if (compare_pro2 != '') {
				for (i = 0; i < compare_proSplit.length; i++) {
					compare_pro3.splice(compare_pro3.indexOf(compare_proSplit[i]), 1);
					pweb_setCookie("compare", compare_pro3, 24);
				}
			}
		}

		this.compare_data.html('');
		$('input[name=pro_compare]').attr('checked', false);
		this.compare_count.html(0);
		this.total_com_property.val(0);
		this.property_compare.css("display", "none");
	}
}

function property_compare_popup() {
	var chks = document.getElementsByName('property_id[]');
	var pro_id = '';

	for (var i = 0; i < chks.length; i++) {
		if (i == 0) {
			pro_id = chks[i].value;		
		}
		else {
			pro_id = pro_id + ',' + chks[i].value;		
		}
	}

	// show throbber
	var wait_message = $('#wait_message').val();
	var text = '<div class="loading-dispo-city loading-quick-preview" id="loading-pics"><p>' + wait_message + '</p></div>';
	$('#property_compare_data').empty().append(text);

	var ajaxrequest =
	$.ajax({
		type:'GET',
		dataType: "json",
		url:'http://'+window.location.host+'/cmain/ajax_compare_property/'+pro_id,
		success:function(data) {		
			$('#property_compare_data').empty().html(data['html']);

			pweb_filter.addFilterMap('compare_property', 'map_canvas', 'en', data.map_data[0].Geo.Latitude,data.map_data[0].Geo.Longitude);
			
                        pweb_filter.toggleMap('compare_property');
                        pweb_filter.toggleMap('city'); 
                        
			$('#map_lat').val(JSON.stringify(data.map_data));

                        for (var i = 0; i < chks.length; i++) {
                            // private 
                            if (
                                $('#prop_tab_box_' + chks[i].value + ' .private_currency').length
                                && $('#prop_tab_box_' + chks[i].value + ' .private_price').length
                            ) {
                                $(".private_price_container_" + chks[i].value + " span").html($("#prop_tab_box_" + chks[i].value + " .private_currency").html() + $("#prop_tab_box_" +  chks[i].value + " .private_price").html());
                            }

                            // dorm
                            if (
                                $('#prop_tab_box_' + chks[i].value + ' .dorms_currency').length
                                && $('#prop_tab_box_' + chks[i].value + ' .dorms_price').length
                            ) {
                                $(".dorm_price_container_" + chks[i].value + " span").html($("#prop_tab_box_" + chks[i].value + " .dorms_currency").html() + $("#prop_tab_box_" +  chks[i].value + " .dorms_price").html());
                            }

                        }
		}
	});

	$('.fancybox-close,.fancybox-overlay').live('click', function(){ 
		ajaxrequest.abort();
	});
}

$(document).ready(function() {
	// click on label will trigger compare
	$('.com_div label').live('click', function(event) {
		var obj = $(this);

		var total_com_property = $('#total_com_property');

		if (total_com_property.val() >= 1) {
			if (obj.parent().find('input').is(':checked')) {
				setTimeout(function() {
					if ($('#compare_data .show-data a').length > 1) {
						$('#comparelink a').trigger('click');
					}
				}, 100);
			}
		}
	});

	$(".compare_displaypopup").fancybox({
		'titlePosition'		: 'inside',
		'transitionIn'		: 'none',
		'transitionOut'		: 'none',
             beforeClose: function() {
                    pweb_filter.toggleMap('city');
                    pweb_filter.toggleMap('compare_property');
                }
	});
});
