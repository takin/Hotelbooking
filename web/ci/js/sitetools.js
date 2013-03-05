$(document).ready(function(){
	/*$("img").error(function(){
		$(this).hide();
	});*/
	$('#more_options_side').click(function(){
		$('#more_choices_side').toggle();
		$('#less_options_side').toggle();
		$(this).toggle();
	});
	$('#less_options_side').click(function(){
		$('#more_choices_side').toggle();
		$('#more_options_side').toggle();
		$(this).toggle();
	});
	$('#read_more_hostel').click(function(){
		$('#top_info_short').toggle();
		$('#top_info_long').toggle();
		$(this).toggle();
		$('#read_less_hostel').toggle();
	});
	$('#read_less_hostel').click(function(){
		$('#top_info_short').toggle();
		$('#top_info_long').toggle();
		$(this).toggle();
		$('#read_more_hostel').toggle();
	});
	$('#thumbnail_list img').jail({effect:"fadeIn"});
	$('#slideshow img').jail({effect:"fadeIn", callback : startslideshow()});	
	$('.hb_frame .city_lp .info_pic img').jail({effect:"fadeIn"});
	$('.hb_frame .city_lp .info_pic img').error(function(){
			$(this).closest('.hostel_list').hide();
	});
	//$('.city_lp .info_pic img').jail({effect:"fadeIn"});
	
	$("a.modify_search").click(function(){
                $('#side_search_wrap').toggle();
		 $(this).toggleClass('expand');
                  $(this).toggleClass('collapse');

                 if ($(this).hasClass('expand'))
                    {
                        // Show everything in side search box 
                        $('#side_search_wrap_city').show();
                        $('#side_search_wrap').show();		
                
                    }
                    else
                    {
                        // hide everything in side search box 
                        // except header
                         $('#side_search_wrap_city').hide();
                         $('#side_search_wrap').hide();		
                    }
                    
		 return false;
	});
	
	$("span.filter_title").click(function(){
		 $(this).next('.filter_content').toggle();
		 $(this).toggleClass('expand');
		 return false;
	});
	
	$("a#show_more_district").click(function(){
		 $('#more_district').toggle();
		 $(this).toggle();
		 return false;
	});
	
	$("a#show_less_district").click(function(){
		 $('#more_district').toggle();
		 $("a#show_more_district").toggle();
		 return false;
	});
	
	$("a#show_more_land").click(function(){
		 $('#more_land').toggle();
		 $(this).toggle();
		 return false;
	});
	
	$("a#show_less_land").click(function(){
		 $('#more_land').toggle();
		 $("a#show_more_land").toggle();
		 return false;
	});
	
	$("a.review_static").click(function (){
		var reviewID = $(this).attr('rel');
		$("#review_wrap_"+reviewID).toggle();
		return false;
	});
	$("a.review_wrap_close").click(function (){
		var reviewID = $(this).attr('rel');
		$("#review_wrap_"+reviewID).toggle();
		return false;
	});
	
	var search_custom_default = $('#search-custom').val();
	
	$('#search-custom').focus(function() {	
		var search_custom = $('#search-custom');
		search_custom.removeClass('disabled');
		search_custom.val('');
		search_custom.select();
		if (search_custom.value == search_custom.defaultValue){
			
		}
		if(search_custom.value != search_custom.defaultValue){
			
		}
		$("#search-city").addClass('disabled');
		$("#search-country").addClass('disabled');	
		$('input:radio[name=type_search]')[1].checked = true;		
	});
	
	$('#search-country').click(function() {
		$('#search-custom').addClass('disabled');
		$("#search-city").removeClass('disabled');
		$("#search-country").removeClass('disabled');
		$('input:radio[name=type_search]')[0].checked = true;
	});
	
	$('#search-city').click(function() {		
		$('#search-custom').addClass('disabled');
		$("#search-city").removeClass('disabled');
		$("#search-country").removeClass('disabled');
		$('input:radio[name=type_search]')[0].checked = true;
	});
	
	$('input:radio[name=type_search]').change(function() {
		if ($('input[name=type_search]:checked').val() == '1'){
			$('#search-custom').addClass('disabled');
			$("#search-city").removeClass('disabled');
			$("#search-country").removeClass('disabled');
		}else {
			var search_custom = $('#search-custom');
			search_custom.removeClass('disabled');
			search_custom.val('');
			search_custom.select();
			if (search_custom.value == search_custom.defaultValue){
				
			}
			if(search_custom.value != search_custom.defaultValue){
				
			}
			$("#search-city").addClass('disabled');
			$("#search-country").addClass('disabled');	
		}				

	});
		
	$(".iframe").fancybox();
	
});
function startslideshow(){
	var main_pic = $('.main-pic');
	if (main_pic.length){
		main_pic.cycle({
			fx:      'fade',
			timeout:  7000
		});
	}
	
	/*$('a.control-left').click(function() {
	$('.main-pic').cycle('pause');
	//return false;
	});
	
	$('a.control-right').click(function() {
	$('.main-pic').cycle('pause');
	//return false;
	});*/
	
	/*$('ul.control a').hover(function() {
				$(this).stop().animate({
										opacity: 1
								}, 350);
		}, function() {
	if ($(this).hasClass ("activeSlide")) {
	
		$(this).stop().animate({
										opacity: 1
								}, 350);
	
	}else{
	
					$(this).stop().animate({
										opacity: 0.5
								}, 350);
	}
	
	});*/
}