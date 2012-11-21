$(document).ready(function()
{
  var test1 = 1;
	$('#sortname-tous').click(function(){
	  
		$('#tous-list .sorting').removeClass('activesort');
		$(this).addClass('activesort');
		var mylist = $('#tous-page');
		var listitems = mylist.children('div.hostel-list').get();
		
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
		
		 $('#tous-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage1]').val()} );
		 return false;
	});
	
	var test2 = 1;
	$('#sortprice-tous').click(function(){
	
		  $('#tous-list .sorting').removeClass('activesort');
			$(this).addClass('activesort');
			var mylist = $('#tous-page');
			var listitems = mylist.children('div.hostel-list').get();
			
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
				
			 $('#tous-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage1]').val()} );
			 return false;
	});
	
	
	var test22 = 1;
	$('#sortcote-tous').click(function(){
	
		  $('#tous-list .sorting').removeClass('activesort');
			$(this).addClass('activesort');
			var mylist = $('#tous-page');
			var listitems = mylist.children('div.hostel-list').get();
			
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
				
			 $('#tous-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage1]').val()} );
			 return false;
	});
	
	
	//hostel
	
	var test3 = 1;
	$('#sortname-hostel').click(function(){
	  
		$('#hostel-list .sorting').removeClass('activesort');
		$(this).addClass('activesort');
		var mylist = $('#hostel-page');
		var listitems = mylist.children('div.hostel-list').get();
		
		if(test3==1)
			{
				test3=2;
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
				test3=1;
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
		
		 $('#hostel-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage2]').val()} );
		 return false;
	});
	
	var test4 = 1;
	$('#sortprice-hostel').click(function(){
	
		  $('#hostel-list .sorting').removeClass('activesort');
			$(this).addClass('activesort');
			var mylist = $('#hostel-page');
			var listitems = mylist.children('div.hostel-list').get();
			
			if(test4==1)
			{
				test4=2;
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
				test4=1;
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
				
			 $('#hostel-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage2]').val()} );
			 return false;
	});
	
	var test42 = 1;
	$('#sortcote-hostel').click(function(){
	
		  $('#hostel-list .sorting').removeClass('activesort');
			$(this).addClass('activesort');
			var mylist = $('#hostel-page');
			var listitems = mylist.children('div.hostel-list').get();
			
			if(test42==1)
			{
				test42=2;
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
				test42=1;
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
				
			 $('#hostel-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage2]').val()} );
			 return false;
	});
	
	
	//hotel
	
	var test5 = 1;
	$('#sortname-hotel').click(function(){
	  
		$('#hotel-list .sorting').removeClass('activesort');
		$(this).addClass('activesort');
		var mylist = $('#hotel-page');
		var listitems = mylist.children('div.hostel-list').get();
		
		if(test5==1)
			{
				test5=2;
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
				test5=1;
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
		
		 $('#hotel-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage3]').val()} );
		 return false;
	});
	
	var test6 = 1;
	$('#sortprice-hotel').click(function(){
	
		  $('#hotel-list .sorting').removeClass('activesort');
			$(this).addClass('activesort');
			var mylist = $('#hotel-page');
			var listitems = mylist.children('div.hostel-list').get();
			
			if(test6==1)
			{
				test6=2;
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
				test6=1;
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
				
			 $('#hotel-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage3]').val()} );
			 return false;
	});
	
	var test62 = 1;
	$('#sortcote-hotel').click(function(){
	
		  $('#hotel-list .sorting').removeClass('activesort');
			$(this).addClass('activesort');
			var mylist = $('#hotel-page');
			var listitems = mylist.children('div.hostel-list').get();
			
			if(test62==1)
			{
				test62=2;
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
				test62=1;
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
				
			 $('#hotel-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage3]').val()} );
			 return false;
	});
	
	
	//apart
	
	var test7 = 1;
	$('#sortname-apart').click(function(){
	  
		$('#apart-list .sorting').removeClass('activesort');
		$(this).addClass('activesort');
		var mylist = $('#apart-page');
		var listitems = mylist.children('div.hostel-list').get();
		
		if(test7==1)
			{
				test7=2;
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
				test7=1;
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
		
		 $('#apart-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage4]').val()} );
		 return false;
	});
	
	var test8 = 1;
	$('#sortprice-apart').click(function(){
	
		  $('#apart-list .sorting').removeClass('activesort');
			$(this).addClass('activesort');
			var mylist = $('#apart-page');
			var listitems = mylist.children('div.hostel-list').get();
			
			if(test8==1)
			{
				test8=2;
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
				test8=1;
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
				
			 $('#apart-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage4]').val()} );
			 return false;
	});
	
	var test82 = 1;
	$('#sortcote-apart').click(function(){
	
		  $('#apart-list .sorting').removeClass('activesort');
			$(this).addClass('activesort');
			var mylist = $('#apart-page');
			var listitems = mylist.children('div.hostel-list').get();
			
			if(test82==1)
			{
				test82=2;
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
				test82=1;
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
				
			 $('#apart-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage3]').val()} );
			 return false;
	});
	
	
	//guest
	
	var test9 = 1;
	$('#sortname-guest').click(function(){
	  
		$('#guest-list .sorting').removeClass('activesort');
		$(this).addClass('activesort');
		var mylist = $('#guest-page');
		var listitems = mylist.children('div.hostel-list').get();
		
		if(test9==1)
			{
				test9=2;
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
				test9=1;
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
		
		 $('#guest-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage5]').val()} );
		 return false;
	});
	
	var test10 = 1;
	$('#sortprice-guest').click(function(){
	
		  $('#guest-list .sorting').removeClass('activesort');
			$(this).addClass('activesort');
			var mylist = $('#guest-page');
			var listitems = mylist.children('div.hostel-list').get();
			
			if(test10==1)
			{
				test10=2;
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
				test10=1;
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
				
			 $('#guest-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage5]').val()} );
			 return false;
	});
	
	var test102 = 1;
	$('#sortcote-guest').click(function(){
	
		  $('#guest-list .sorting').removeClass('activesort');
			$(this).addClass('activesort');
			var mylist = $('#guest-page');
			var listitems = mylist.children('div.hostel-list').get();
			
			if(test102==1)
			{
				test102=2;
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
				test102=1;
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
				
			 $('#guest-page').trigger('refresh.evtpaginate', {perPage:$('select[name=perpage3]').val()} );
			 return false;
	});
	
	 
});