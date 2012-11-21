$(document).ready(function()
{
  var test1 = 1;
	$('#sortname-all').click(function(){
	  
		$('.city-sort .sorting').removeClass('activesort');
		$(this).addClass('activesort');
		var mylist = $('#all-list');
		var listitems = mylist.children('div.hostel-list-item').get();
		
		if(test1==1)
			{
				test1=2;
				$(this).children().removeClass('asc');
				$(this).children().addClass('desc');
				listitems.sort(function(a, b) {					
					var compA = a.getElementsByTagName("h2").item(0).innerHTML.toUpperCase();
					var compB = b.getElementsByTagName("h2").item(0).innerHTML.toUpperCase();				  
				  return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;				 			
				})
			}
			else
			{
				test1=1;
				$(this).children().removeClass('desc');
				$(this).children().addClass('asc');				
				listitems.sort(function(a, b) {								
					var compA = a.getElementsByTagName("h2").item(0).innerHTML.toUpperCase();
					var compB = b.getElementsByTagName("h2").item(0).innerHTML.toUpperCase();
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;						 
				})
			}			
		
		$.each(listitems, function(idx, itm) {
			mylist.append(itm);
			});
				
		 return false;
	});
	
	var test2 = 1;
	$('#sortprice-all').click(function(){
	
		  $('.city-sort .sorting').removeClass('activesort');
			$(this).addClass('activesort');
			var mylist = $('#all-list');
			var listitems = mylist.children('div.hostel-list-item').get();
			
			if(test2==1)
			{
				test2=2;
				$(this).children().removeClass('asc');
				$(this).children().addClass('desc');
				listitems.sort(function(a, b) {					
					var compA = parseFloat(a.getElementsByTagName("strong").item(0).innerHTML);
					var compB = parseFloat(b.getElementsByTagName("strong").item(0).innerHTML);
					return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;					
				})
			}
			else
			{
				test2=1;
				$(this).children().removeClass('desc');
				$(this).children().addClass('asc');	
				listitems.sort(function(a, b) {				
					var compA = parseFloat(a.getElementsByTagName("strong").item(0).innerHTML);
					var compB = parseFloat(b.getElementsByTagName("strong").item(0).innerHTML);
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
				})
			}
			
			$.each(listitems, function(idx, itm) {
				mylist.append(itm);
				});				
			 
			 return false;
	});
	
	
	var test22 = 1;
	$('#sortcote-all').click(function(){
	
		  $('.city-sort .sorting').removeClass('activesort');
			$(this).addClass('activesort');
			var mylist = $('#all-list');
			var listitems = mylist.children('div.hostel-list-item').get();
			
			if(test22==1)
			{
				test22=2;
				$(this).children().removeClass('asc');
				$(this).children().addClass('desc');
				listitems.sort(function(a, b) {					
					var compA = parseFloat(a.getElementsByTagName("strong").item(1).innerHTML);
					var compB = parseFloat(b.getElementsByTagName("strong").item(1).innerHTML);
					return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;					
				})
			}
			else
			{
				test22=1;
				$(this).children().removeClass('desc');
				$(this).children().addClass('asc');	
				listitems.sort(function(a, b) {				
					var compA = parseFloat(a.getElementsByTagName("strong").item(1).innerHTML);
					var compB = parseFloat(b.getElementsByTagName("strong").item(1).innerHTML);
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
				})
			}
			
			$.each(listitems, function(idx, itm) {
				mylist.append(itm);
				});				
			
			 return false;
	});
});