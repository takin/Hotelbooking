var suggest_xhr = null;
var last_suggest_value = "";
var suggest_select_id = 0;
var clickoutfn = function(){};
//On click out remove suggestions
$(document).click(function(event)
{
	if(event.target.id !== 'search-submit')
	{
		event.stopPropagation();
		clickoutfn();
	}
});

function searchSuggest(e, base_url, suggest_url_type, show_more_results_link, term_from_start)
{
	var suggest_term = document.getElementById("search-custom").value;
	
	suggest_term = suggest_term.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g," ");
	
	$('#custom-url').val("");
	$('#custom-type').val("");

	//on down arrow
	if($('ul#suggestion').is(':visible') && e.keyCode == 40)
	{
		if($("#sug"+(suggest_select_id))[0]) suggest_select_id = suggest_select_id + 1;
		
		if(suggest_select_id > 0) $("#sug"+(suggest_select_id-1)).addClass('suggest-selection');
		if(suggest_select_id > 1) $("#sug"+(suggest_select_id-2)).removeClass('suggest-selection');
		
	}
	//on up arrow
	else if($('ul#suggestion').is(':visible') && e.keyCode == 38)
	{
		if(suggest_select_id > 0) suggest_select_id = suggest_select_id -1;
		
		if(suggest_select_id > 0) $("#sug"+(suggest_select_id-1)).addClass('suggest-selection');
		if($("#sug"+(suggest_select_id))[0]) $("#sug"+suggest_select_id).removeClass('suggest-selection');
		
	}
	//on enter
	else if($('ul#suggestion').is(':visible') && e.keyCode == 13)
	{
		$('#sug'+(suggest_select_id-1)+' a').click();
	}
	else if((suggest_term.length > 2) && ((last_suggest_value != suggest_term) || (e.keyCode == 40) ))
	{
		last_suggest_value = suggest_term;
		if(suggest_xhr)
		{
			suggest_xhr.abort();
		}
		$('img#input-loading').show();
		suggest_xhr = $.ajax(
				{
						type:"GET",
						url:base_url+"suggest/"+suggest_term+"/"+suggest_url_type+"/"+show_more_results_link+"/"+term_from_start,
						success:function(data)
						{
							create_suggest_box(data);
						}
				});
	}
	else if(suggest_term.length < 3)
	{
		clear_suggestions();
	}
}
function create_suggest_box(data)
{
	$('img#input-loading').hide();
	
	document.getElementById("search-suggest").innerHTML = data;
	$('#search-suggest li a').click(function() {
		
		if(this.rel =='moreresults')
		{
		 return true;
		}
		
		var  search_text_input = $(this).text();
		//if property just copy the property name to input text box
		if(this.rel == 0)
		{
			search_text_input = search_text_input.split(',');
			search_text_input = search_text_input[0];
		}
		 
		$('#search-custom').val(search_text_input);
		$('#custom-url').val(this.href);
		$('#custom-type').val(this.rel);

		clear_suggestions();
		return false;
	});
	//On hover reset arrow selections
	$('#search-suggest li').hover(function() {
		$("#sug"+(suggest_select_id-1)).removeClass('suggest-selection');
		suggest_select_id = 0;
	});
	
	clickoutfn = function(){clear_suggestions();};
}
function clear_suggestions()
{
	suggest_select_id = 0;
	last_suggest_value = "";
	if(suggest_xhr) suggest_xhr.abort();
	$('ul#suggestion').hide();
	$('img#input-loading').hide();
	document.getElementById("search-suggest").innerHTML = "";
	clickoutfn = function(){};
}
