function booking_confirm(base_url, refresh, settleCurrency)
{
	//get form data
	var firstname 	= document.getElementById('firstname').value;
	var lastname 	= document.getElementById('lastname').value;
	var Nationality = document.getElementById('Nationality').value;
	var gender 		= document.getElementById('gender').value;
	var arrival_time = document.getElementById('arrival_time').value;
	var EmailAddress = document.getElementById('EmailAddress').value;
	var EmailAddress2 = document.getElementById('EmailAddress2').value;
	var sms          = document.getElementById('sms').value;
	var phone_number = document.getElementById('phone_number').value;
	var sign_me_up 	= document.getElementById('sign_me_up').value;
	var mail_subscribe 	= document.getElementById('mail_subscribe').checked;
	var conditions 	= document.getElementById('conditions_agree').checked;
	
	var bsid 		= document.getElementById('bsid').value;
	var ccname 		= document.getElementById('ccname').value;
	//var ccnumber 	= document.getElementById('ccnumber').value;
	var ccnumber 	= document.getElementById('ccnumber').value.replace(/ /g,'');

	var cctypeval    = document.getElementById('cctype').value;
	var pos1 = cctypeval.indexOf('-');
	  
	var cctype     = cctypeval.substring(0,pos1);
	var ccexpiry_m 	= document.getElementById('ccexpiry_m').value;
	var ccexpiry_y 	= document.getElementById('ccexpiry_y').value;
	var cvv 		= document.getElementById('cvv').value;
	var settle_bill_total = document.getElementById('settle_bill_total').value;
	
	
	var ccvalidfrom_m = null;
	if (document.getElementById('ccvalidfrom_m') != null)
	{
		ccvalidfrom_m 	= document.getElementById('ccvalidfrom_m').value;
	}
	
	var ccvalidfrom_y = null;
	if (document.getElementById('ccvalidfrom_y') != null)
	{
		ccvalidfrom_y 	= document.getElementById('ccvalidfrom_y').value;
	}
	
	var issueno = null;
	if (document.getElementById('issueno') != null)
	{
		issueno 	= document.getElementById('issueno').value;
	}
	var bookCurrency	= document.getElementById('book-currency').value;

	switch(settleCurrency.toLowerCase())
	{
	case 'eur':
	  settleCurrency = 'EUR';
	  break;
	case 'usd':
	  settleCurrency = 'USD';
	  break;
	case 'gbp':
	  settleCurrency = 'GBP';
	  break;
	default:
	  settleCurrency = 'EUR';
	  break;
	}
	
	//vars for refresh 
//	var propertyName 	= document.getElementById('book-propertyName').value;
	var cardsupported 		= document.getElementsByName('cardsupported[]');
	var propertyNumber 	= document.getElementById('propertyNumber').value;
	var propertyCity 	= document.getElementById('propertyCity').value;
	var propertyCountry = document.getElementById('propertyCountry').value;
//	var dateStart 		= document.getElementById('book-dateStart').value;
//	var numNights 		= document.getElementById('book-numNights').value;

	var cardsArray = new Array();
	for(var i=0;i<cardsupported.length;i++)
	{
		cardsArray.push(cardsupported[i].value);
	}
	if(refresh != true)
	{
	  refresh = false;
	}
	
	$('#submit-payment').hide();
	$('.api_error').hide();
	if(refresh == false)
    {
	  $("#loading_message").show();
    }
	if(refresh == true)
	{
	  $("#loading_message_cur").show();
	}
	
	
	$.ajax({type:"POST",
		url:base_url+"mbooking_try/",
		data: { firstname: firstname,
				lastname: lastname,
				nationality: Nationality,
				gender: gender,
				arrival_time: arrival_time,
				email: EmailAddress,
				email2: EmailAddress2,
				phone_number: phone_number,
				sms: sms,
				sign_me_up: sign_me_up,
				mail_subscribe: mail_subscribe,
				bsid: bsid,
				ccname: ccname,
				ccnumber: ccnumber,
				cctype: cctype,
				ccexpiry_m: ccexpiry_m,
				ccexpiry_y: ccexpiry_y,
				cvv: cvv,
				ccvalidfrom_m: ccvalidfrom_m,
				ccvalidfrom_y: ccvalidfrom_y,
				issueno: issueno,
				cardsupported: cardsArray.toString(),
				propertyNumber: propertyNumber,
				propertyCity: propertyCity,
				propertyCountry: propertyCountry,
				bookCurrency: bookCurrency,
				settleCurrency: settleCurrency,
				settle_bill_total: settle_bill_total,
				conditions_agree: conditions,
				refresh: refresh
				},
//		timeout:2000000,
//		error:function(XMLHttpRequest, textStatus, errorThrown)
//		{
//			$("#loading_message").html("Délai de traitement expiré.");
//			$('#submit-payment').show();
//		},
		success:function(data)
		{
			$("#loading_message").hide();
			$("#loading_message_cur").hide();
			$("#content").html(data);
			
			if(refresh == false)
			{
			  var target = $('#content');
			  var top = target.offset().top;
			  $('html,body').animate({scrollTop: top}, 1000);
			
//			  pageTracker._trackPageview("/click/complete");
			}
      
		}
	});
	
}