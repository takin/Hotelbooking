$(document).ready(function()
{
  var test1 = 1;
	$('#sortname-tous').click(function(){
	  
		$('#tous-list .sorting').removeClass('activesort');
		$(this).addClass('activesort');
		var mylist = $('#tous-page');
		var listitems = mylist.children('div').get();
		
		if(test1==1)
			{
				test1=2;
				$(this).children().removeClass('asc');
				$(this).children().addClass('desc');
				listitems.sort(function(a, b) {					
					var compA = a.getElementsByTagName("a").item(0).innerHTML.toUpperCase();
					var compB = b.getElementsByTagName("a").item(0).innerHTML.toUpperCase();				  
				  return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;				 			
				})
			}
			else
			{
				test1=1;
				$(this).children().removeClass('desc');
				$(this).children().addClass('asc');				
				listitems.sort(function(a, b) {								
					var compA = a.getElementsByTagName("a").item(0).innerHTML.toUpperCase();
					var compB = b.getElementsByTagName("a").item(0).innerHTML.toUpperCase();
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;						 
				})
			}			
		
		$.each(listitems, function(idx, itm) {
			mylist.append(itm);
			});
		
		 $('#tous-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage1]').val()} );
		 return false;
	});
	
	var test2 = 1;
	$('#sortname-city').click(function(){
	  
		$('#city-list .sorting').removeClass('activesort');
		$(this).addClass('activesort');
		var mylist = $('#city-page');
		var listitems = mylist.children('div').get();
		
		if(test2==1)
			{
				test2=2;
				$(this).children().removeClass('asc');
				$(this).children().addClass('desc');
				listitems.sort(function(a, b) {					
					var compA = a.getElementsByTagName("a").item(0).innerHTML.toUpperCase();
					var compB = b.getElementsByTagName("a").item(0).innerHTML.toUpperCase();				  
				  return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;				 			
				})
			}
			else
			{
				test2=1;
				$(this).children().removeClass('desc');
				$(this).children().addClass('asc');				
				listitems.sort(function(a, b) {								
					var compA = a.getElementsByTagName("a").item(0).innerHTML.toUpperCase();
					var compB = b.getElementsByTagName("a").item(0).innerHTML.toUpperCase();
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;						 
				})
			}			
		
		$.each(listitems, function(idx, itm) {
			mylist.append(itm);
			});
		
		 $('#city-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage6]').val()} );
		 return false;
	});
	
	var test3 = 1;
	$('#sortname-country').click(function(){
	  
		$('#country-list .sorting').removeClass('activesort');
		$(this).addClass('activesort');
		var mylist = $('#country-page');
		var listitems = mylist.children('div').get();
		
		if(test3==1)
			{
				test3=2;
				$(this).children().removeClass('asc');
				$(this).children().addClass('desc');
				listitems.sort(function(a, b) {					
					var compA = a.getElementsByTagName("a").item(0).innerHTML.toUpperCase();
					var compB = b.getElementsByTagName("a").item(0).innerHTML.toUpperCase();				  
				  return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;				 			
				})
			}
			else
			{
				test3=1;
				$(this).children().removeClass('desc');
				$(this).children().addClass('asc');				
				listitems.sort(function(a, b) {								
					var compA = a.getElementsByTagName("a").item(0).innerHTML.toUpperCase();
					var compB = b.getElementsByTagName("a").item(0).innerHTML.toUpperCase();
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;						 
				})
			}			
		
		$.each(listitems, function(idx, itm) {
			mylist.append(itm);
			});
		
		 $('#country-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage3]').val()} );
		 return false;
	});
	
	var test4 = 1;
	$('#sortname-property').click(function(){
	  
		$('#property-list .sorting').removeClass('activesort');
		$(this).addClass('activesort');
		var mylist = $('#property-page');
		var listitems = mylist.children('div').get();
		
		if(test4==1)
			{
				test4=2;
				$(this).children().removeClass('asc');
				$(this).children().addClass('desc');
				listitems.sort(function(a, b) {					
					var compA = a.getElementsByTagName("a").item(0).innerHTML.toUpperCase();
					var compB = b.getElementsByTagName("a").item(0).innerHTML.toUpperCase();				  
				  return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;				 			
				})
			}
			else
			{
				test4=1;
				$(this).children().removeClass('desc');
				$(this).children().addClass('asc');				
				listitems.sort(function(a, b) {								
					var compA = a.getElementsByTagName("a").item(0).innerHTML.toUpperCase();
					var compB = b.getElementsByTagName("a").item(0).innerHTML.toUpperCase();
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;						 
				})
			}			
		
		$.each(listitems, function(idx, itm) {
			mylist.append(itm);
			});
		
		 $('#property-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage4]').val()} );
		 return false;
	});
	
	
});