$(document).ready(function(){	
													
function assignval(value){
	
	$('select[name=perpage1]').val(value);
	wrap1.trigger('refresh.evtpaginate', { perPage : value } );
	$('select[name=perpage2]').val(value);
	wrap2.trigger('refresh.evtpaginate', { perPage : value } );
	$('select[name=perpage3]').val(value);
	wrap3.trigger('refresh.evtpaginate', { perPage : value } );
	$('select[name=perpage4]').val(value);
	wrap4.trigger('refresh.evtpaginate', { perPage : value } );
	$('select[name=perpage5]').val(value);
	wrap5.trigger('refresh.evtpaginate', { perPage : value } );
	$('select[name=perpage6]').val(value);
	wrap6.trigger('refresh.evtpaginate', { perPage : value } );
	$('select[name=perpage7]').val(value);
	wrap7.trigger('refresh.evtpaginate', { perPage : value } );
	$('select[name=perpage8]').val(value);
	wrap8.trigger('refresh.evtpaginate', { perPage : value } );
}

var wrap1 = $('#tous-page');					

$('.action1').click(function(){
	var action = $(this).attr('rel'); // get the appropriate action from the rel attribute
	wrap1.trigger(action+'.evtpaginate');
	return false;
});
	
$('select[name=perpage1]').change(function(){
	assignval($(this).val());									  
	
	return false;
});

	// listen out for events triggered by the plugin to update the counter

wrap1.bind( 'initialized.evtpaginate', function(e, startnum, totalnum ){
	$('#count1').text(startnum);
	$('#total1').text(totalnum);
	$('#count12').text(startnum);
	$('#total12').text(totalnum);
}); 

wrap1.bind( 'finished.evtpaginate', function(e, num, isFirst, isLast ){ $('#count1').text(num); } ); 	

wrap1.evtpaginate({perPage:$('select[name=perpage1]').val(), atEnd:'loop'}); // call the plugin!

// page hostel

var wrap2 = $('#hostel-page');					

$('.action2').click(function(){
	var action2 = $(this).attr('rel'); // get the appropriate action from the rel attribute
	wrap2.trigger(action2+'.evtpaginate');
	return false;
});
	
$('select[name=perpage2]').change(function(){
	assignval($(this).val());										  
	
	return false;
});

	// listen out for events triggered by the plugin to update the counter

wrap2.bind( 'initialized.evtpaginate', function(e, startnum2, totalnum2 ){
	$('#count2').text(startnum2);
	$('#total2').text(totalnum2);
	$('#count22').text(startnum2);
	$('#total22').text(totalnum2);
}); 

wrap2.bind( 'finished.evtpaginate', function(e, num, isFirst, isLast ){ $('#count2').text(num); } ); 	

wrap2.evtpaginate({perPage:$('select[name=perpage2]').val(), atEnd:'loop'}); // call the plugin!


// page hotel

var wrap3 = $('#hotel-page');					

$('.action3').click(function(){
	var action = $(this).attr('rel'); // get the appropriate action from the rel attribute
	wrap3.trigger(action+'.evtpaginate');
	return false;
});
	
$('select[name=perpage3]').change(function(){
	assignval($(this).val());										  
	
	return false;
});

	// listen out for events triggered by the plugin to update the counter

wrap3.bind( 'initialized.evtpaginate', function(e, startnum, totalnum ){
	$('#count3').text(startnum);
	$('#total3').text(totalnum);
	$('#count32').text(startnum);
	$('#total32').text(totalnum);
}); 

wrap3.bind( 'finished.evtpaginate', function(e, num, isFirst, isLast ){ $('#count3').text(num); } ); 	

wrap3.evtpaginate({perPage:$('select[name=perpage3]').val(), atEnd:'loop'}); // call the plugin!

// page apart

var wrap4 = $('#apart-page');					

$('.action4').click(function(){
	var action = $(this).attr('rel'); // get the appropriate action from the rel attribute
	wrap4.trigger(action+'.evtpaginate');
	return false;
});
	
$('select[name=perpage4]').change(function(){
	assignval($(this).val());	
	
	return false;
});

	// listen out for events triggered by the plugin to update the counter

wrap4.bind( 'initialized.evtpaginate', function(e, startnum, totalnum ){
	$('#count4').text(startnum);
	$('#total4').text(totalnum);
	$('#count42').text(startnum);
	$('#total42').text(totalnum);
}); 

wrap4.bind( 'finished.evtpaginate', function(e, num, isFirst, isLast ){ $('#count4').text(num); } ); 	

wrap4.evtpaginate({perPage:$('select[name=perpage4]').val(), atEnd:'loop'}); // call the plugin!

// page guest

var wrap5 = $('#guest-page');					

$('.action5').click(function(){
	var action = $(this).attr('rel'); // get the appropriate action from the rel attribute
	wrap5.trigger(action+'.evtpaginate');
	return false;
});
	
$('select[name=perpage5]').change(function(){
	assignval($(this).val());										   
	
	return false;
});

	// listen out for events triggered by the plugin to update the counter

wrap5.bind( 'initialized.evtpaginate', function(e, startnum, totalnum ){
	$('#count5').text(startnum);
	$('#total5').text(totalnum);
	$('#count52').text(startnum);
	$('#total52').text(totalnum);
}); 

wrap5.bind( 'finished.evtpaginate', function(e, num, isFirst, isLast ){ $('#count5').text(num); } ); 	

wrap5.evtpaginate({perPage:$('select[name=perpage5]').val(), atEnd:'loop'}); // call the plugin!


// page search city

var wrap6 = $('#city-page');					

$('.action6').click(function(){
	var action = $(this).attr('rel'); // get the appropriate action from the rel attribute
	wrap6.trigger(action+'.evtpaginate');
	return false;
});
	
$('select[name=perpage6]').change(function(){
	assignval($(this).val());										   
	
	return false;
});

	// listen out for events triggered by the plugin to update the counter

wrap6.bind( 'initialized.evtpaginate', function(e, startnum, totalnum ){
	$('#count6').text(startnum);
	$('#total6').text(totalnum);
	$('#count62').text(startnum);
	$('#total62').text(totalnum);
}); 

wrap6.bind( 'finished.evtpaginate', function(e, num, isFirst, isLast ){ $('#count6').text(num); } ); 	

wrap6.evtpaginate({perPage:$('select[name=perpage6]').val(), atEnd:'loop'}); // call the plugin!


// page search country

var wrap7 = $('#country-page');					

$('.action7').click(function(){
	var action = $(this).attr('rel'); // get the appropriate action from the rel attribute
	wrap7.trigger(action+'.evtpaginate');
	return false;
});
	
$('select[name=perpage7]').change(function(){
	assignval($(this).val());										   
	
	return false;
});

	// listen out for events triggered by the plugin to update the counter

wrap7.bind( 'initialized.evtpaginate', function(e, startnum, totalnum ){
	$('#count7').text(startnum);
	$('#total7').text(totalnum);
	$('#count72').text(startnum);
	$('#total72').text(totalnum);
}); 

wrap7.bind( 'finished.evtpaginate', function(e, num, isFirst, isLast ){ $('#count7').text(num); } ); 	

wrap7.evtpaginate({perPage:$('select[name=perpage7]').val(), atEnd:'loop'}); // call the plugin!

// page search property

var wrap8 = $('#property-page');					

$('.action8').click(function(){
	var action = $(this).attr('rel'); // get the appropriate action from the rel attribute
	wrap8.trigger(action+'.evtpaginate');
	return false;
});
	
$('select[name=perpage8]').change(function(){
	assignval($(this).val());										   
	
	return false;
});

	// listen out for events triggered by the plugin to update the counter

wrap8.bind( 'initialized.evtpaginate', function(e, startnum, totalnum ){
	$('#count8').text(startnum);
	$('#total8').text(totalnum);
	$('.count82').text(startnum);
	$('#total82').text(totalnum);
}); 

wrap8.bind( 'finished.evtpaginate', function(e, num, isFirst, isLast ){ $('.count8').text(num); } ); 	

wrap8.evtpaginate({perPage:$('select[name=perpage8]').val(), atEnd:'loop'}); // call the plugin!


					
});