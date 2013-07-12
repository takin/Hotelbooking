function initpaging(show_per_page)
{
	var show_per_page = show_per_page;  
    var number_of_items = $('#property_list').children().size(); 
    var number_of_pages = Math.ceil(number_of_items/show_per_page);  
    $('#current_page').val(0);  
    $('#show_per_page').val(show_per_page);

    var navigation_html = '<a class="previous_link" href="javascript:previous();"><</a>';  
    var current_link = 0;  
    while(number_of_pages > current_link){  
        navigation_html += '<a class="page_link" id="page_link_'+current_link+'" href="javascript:go_to_page(' + current_link +')" longdesc="' + current_link +'">'+ (current_link + 1) +'</a>';  
        current_link++;  
    }  
    navigation_html += '<a class="next_link" href="javascript:next();">></a>';  
    if(number_of_pages>1){
		  $('.resultcount').html('1-'+show_per_page);
		  $('.pagination_result').css('display', 'block'); 
	      $('.page_navigation').html(navigation_html); 
	}else{
		  $('.resultcount').html(number_of_items);
		  $('.page_navigation').html(''); 
		  
	} 
		$('.resulttotal').html(number_of_items);
	if(number_of_pages>0){
		$('.main_pagination_div').css('display', 'inline-block');
		$('.pagination_result').css('display', 'block'); 
	}else{
		$('.main_pagination_div').css('display', 'none');
		$('.pagination_result').css('display', 'none');  
	}
  
    $('.page_navigation .page_link:first').addClass('active_page');  
  
    $('#property_list').children().css('display', 'none');  
  
    $('#property_list').children().slice(0, show_per_page).css('display', 'block');  
	$('.previous_link').css({"pointer-events":"none","color":"#ccc"});
	$('#page_link_0').css({"pointer-events":"none","color":"#ccc"});
}
  
function previous(){  
  
    new_page = parseInt($('#current_page').val()) - 1;  
    if($('.active_page').prev('.page_link').length==true){  
        go_to_page(new_page);  
    }  
  
}  
                                       
function next(){  
    new_page = parseInt($('#current_page').val()) + 1;  
    if($('.active_page').next('.page_link').length==true){  
        go_to_page(new_page);  
    }  
  
}  
function go_to_page(page_num){  
	$("html, body").animate({ scrollTop: 200 }, 400);
    var show_per_page = parseInt($('#show_per_page').val());
	var number_of_items = $('#property_list').children().size(); 
	var number_of_pages = Math.ceil(number_of_items/show_per_page);  
	$('.page_link').css({"pointer-events":"visible ","color":"#227BBD"});
	$('#page_link_'+page_num).css({"pointer-events":"none","color":"#ccc"});
    if(page_num>0){
		$('.previous_link').css({"pointer-events":"visible ","color":"#227BBD"});
	}
	else{
		$('.previous_link').css({"pointer-events":"none","color":"#ccc"});
	}
	if(page_num==number_of_pages-1){
		$('.next_link').css({"pointer-events":"none","color":"#ccc"});
		var startfrom=show_per_page*parseFloat(number_of_pages-1);
		$('.resultcount').html(startfrom+'-'+number_of_items);
	}else{
		 if(page_num==0){
		 	var startfrom=1;
		 }
		 else if(page_num==1){
		 	var startfrom=show_per_page+1;
			}
		 else{
		 	var startfrom=(show_per_page*parseFloat(page_num))+1;	
		 }
		var endto=show_per_page*parseFloat(page_num+1);
		$('.resultcount').html(startfrom+'-'+endto);
		$('.next_link').css({"pointer-events":"visible ","color":"#227BBD"});
	}      
    start_from = page_num * show_per_page;  
  
    end_on = start_from + show_per_page;                          
  
    $('#property_list').children().css('display', 'none').slice(start_from, end_on).css('display', 'block');  
  
       $('.page_link[longdesc=' + page_num +']').addClass('active_page').siblings('.active_page').removeClass('active_page');  

       //added .change() to trigger change event
       $('#current_page').val(page_num).change();  
}