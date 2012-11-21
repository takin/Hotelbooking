$(document).ready(function(){	
 
  var test2 = 1;	
	
	var mylist = $('#tous-page');
	var listitems = mylist.children('div.hostel-list').get();
	
	if(test2==1)
	{
		test2=2;
		listitems.sort(function(a, b) {				
			var compA = parseFloat(a.getElementsByTagName("strong").item(0).innerHTML);
			var compB = parseFloat(b.getElementsByTagName("strong").item(0).innerHTML);
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		})
		
	}
	else
	{
		test2=1;
		listitems.sort(function(a, b) {					
			var compA = parseFloat(a.getElementsByTagName("strong").item(0).innerHTML);
			var compB = parseFloat(b.getElementsByTagName("strong").item(0).innerHTML);
			return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;					
		})
	}
	
	$.each(listitems, function(idx, itm) {
		mylist.append(itm);
	});
		
	 //$('#tous-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage1]').val()} );
	 //return false;

	
	
	//hostel
	
	var test4 = 1;
	
	var mylist = $('#hostel-page');
	var listitems = mylist.children('div.hostel-list').get();
	
	if(test4==1)
	{
		test4=2;
		test2=1;
		listitems.sort(function(a, b) {				
			var compA = parseFloat(a.getElementsByTagName("strong").item(0).innerHTML);
			var compB = parseFloat(b.getElementsByTagName("strong").item(0).innerHTML);
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		})
	}
	else
	{
		test4=1;
		listitems.sort(function(a, b) {					
			var compA = parseFloat(a.getElementsByTagName("strong").item(0).innerHTML);
			var compB = parseFloat(b.getElementsByTagName("strong").item(0).innerHTML);
			return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;					
		})
	}
	
	$.each(listitems, function(idx, itm) {
		mylist.append(itm);
		});
		
	 //$('#hostel-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage2]').val()} );
	 //return false;
	 
	//hotel
		
	var test6 = 1;
	
	
	var mylist = $('#hotel-page');
	var listitems = mylist.children('div.hostel-list').get();
	
	if(test6==1)
	{
		test6=2;
		test2=1;
		listitems.sort(function(a, b) {				
			var compA = parseFloat(a.getElementsByTagName("strong").item(0).innerHTML);
			var compB = parseFloat(b.getElementsByTagName("strong").item(0).innerHTML);
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		})
	}
	else
	{
		test6=1;
		listitems.sort(function(a, b) {					
			var compA = parseFloat(a.getElementsByTagName("strong").item(0).innerHTML);
			var compB = parseFloat(b.getElementsByTagName("strong").item(0).innerHTML);
			return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;					
		})
	}
	
	$.each(listitems, function(idx, itm) {
		mylist.append(itm);
		});
		
	 //$('#hotel-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage2]').val()} );
	 //return false;
	
	//apart
	
		
	var test8 = 1;
	
	var mylist = $('#apart-page');
	var listitems = mylist.children('div.hostel-list').get();
	
	if(test8==1)
	{
		test8=2;
		test2=1;
		listitems.sort(function(a, b) {				
			var compA = parseFloat(a.getElementsByTagName("strong").item(0).innerHTML);
			var compB = parseFloat(b.getElementsByTagName("strong").item(0).innerHTML);
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		})
	}
	else
	{
		test8=1;
		listitems.sort(function(a, b) {					
			var compA = parseFloat(a.getElementsByTagName("strong").item(0).innerHTML);
			var compB = parseFloat(b.getElementsByTagName("strong").item(0).innerHTML);
			return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;					
		})
	}
	
	$.each(listitems, function(idx, itm) {
		mylist.append(itm);
		});
		
	 //return false;
	
	
	
	//guest
	
	var test10 = 1;
	
		  
			var mylist = $('#guest-page');
			var listitems = mylist.children('div.hostel-list').get();
			
			if(test10==1)
			{
				test10=2;
				test2=1;
				listitems.sort(function(a, b) {				
					var compA = parseFloat(a.getElementsByTagName("strong").item(0).innerHTML);
					var compB = parseFloat(b.getElementsByTagName("strong").item(0).innerHTML);
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
				})
			}
			else
			{
				test10=1;
				listitems.sort(function(a, b) {					
					var compA = parseFloat(a.getElementsByTagName("strong").item(0).innerHTML);
					var compB = parseFloat(b.getElementsByTagName("strong").item(0).innerHTML);
					return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;					
				})
			}
			
			$.each(listitems, function(idx, itm) {
				mylist.append(itm);
				});
				
			 //$('#guest-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage2]').val()} );	
	 
});