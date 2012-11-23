function setCities(city_empty,countryFieldId,cityFieldId)
{
  var cntrySel = document.getElementById(countryFieldId);
  var i = 0;
  var options = Array(city_empty);
  var values = Array(city_empty);
  
  $(xmlData).find('Country').each(function(){
    var countrySelectText = $(this).find('countrySelectText').text();
    var countrySelectVal = $(this).find('countrySelectVal').text();
    if(cntrySel.value == countrySelectVal)
    {
      $(this).find('City').each(function(){
        var citySelectText = $(this).find('cityNameSelectText').text();
        var citySelectVal = $(this).find('cityNameSelectVal').text();
        if(citySelectVal)
        {
          options.push(citySelectText);
          values.push(citySelectVal);
        }
      });
      
      i++;
    }
  });
  changeSelect(cityFieldId, options, values);
  
  citySelectField = document.getElementById(cityFieldId);
  sortSelect(citySelectField,1);
}
function setCountries(cities_array,countryFieldId)
{
	selectField = document.getElementById(countryFieldId);
	selectField.options.length = 0;
	
	var i=0;
	for (country in cities_array)
  {
    selectField.options[i] = new Option(cities_array[country][0], country);
    i++;
  }
	
}

function changeSelect(fieldID, newOptions, newValues)
{
  selectField = document.getElementById(fieldID);
//  selectField.options.length = 0;
  
  //Clear select cross browser compatible
  for (i=selectField.options.length-1; i>=0; i--)
  {
    selectField.removeChild(selectField.options[i]);
  }
  
  for (var i=0; i<newOptions.length; i++)
  {
    selectField.options[i] = new Option(newOptions[i], newValues[i]);
  }
}

function sortCountrySelect(fieldID,startIndex,cities_array)
{
  selectField = document.getElementById(fieldID);
  
  var valSorted = new Array();

  for(i=startIndex; i<selectField.options.length; i++)  {
    valSorted[i] = selectField.options[i].text;
  }
  
  valSorted.sort();

  for(i=0; i<selectField.options.length-startIndex; i++)  {
    
    selectField.options[startIndex+i].text = valSorted[i];
    selectField.options[startIndex+i].value = getCitiesArrayIndex(valSorted[i],cities_array);
    //var test = getCitiesArrayIndex(valSorted[i],cities_array);
    //alert('index: '+ test+" text: "+valSorted[i])
  }
  
}

function removeAccent(s)
{
    var r=s.toLowerCase();
    r = r.replace(new RegExp("\\s", 'g'),"");
    r = r.replace(new RegExp("[àáâãäå]", 'g'),"a");
    r = r.replace(new RegExp("æ", 'g'),"ae");
    r = r.replace(new RegExp("ç", 'g'),"c");
    r = r.replace(new RegExp("[èéêë]", 'g'),"e");
    r = r.replace(new RegExp("[ìíîï]", 'g'),"i");
    r = r.replace(new RegExp("ñ", 'g'),"n");                            
    r = r.replace(new RegExp("[òóôõö]", 'g'),"o");
    r = r.replace(new RegExp("œ", 'g'),"oe");
    r = r.replace(new RegExp("[ùúûü]", 'g'),"u");
    r = r.replace(new RegExp("[ýÿ]", 'g'),"y");
    r = r.replace(new RegExp("\\W", 'g'),"");
    return r;
}

function sortSelect(selElem,startIndex) {
  var tmpAry = new Array();
  for (var i=0;i<selElem.options.length-startIndex;i++) {
          tmpAry[i] = new Array();
          tmpAry[i][0] = removeAccent(selElem.options[i+startIndex].text);
          tmpAry[i][1] = selElem.options[i+startIndex].text;
          tmpAry[i][2] = selElem.options[i+startIndex].value;
  }
  tmpAry.sort();
  while (selElem.options.length > startIndex) {
      selElem.options[startIndex] = null;
  }
  for (var i=0;i<tmpAry.length;i++) {
          var op = new Option(tmpAry[i][1], tmpAry[i][2]);
          selElem.options[i+startIndex] = op;
  }
  return;
}

function getCitiesArrayIndex(search_value,cities_array)
{
  for(var countryIndex in cities_array)
  {
    if(cities_array[countryIndex][0] === search_value) return countryIndex;
  }
  
  return "";
}
var xmlData = null;

function triggerWarning(warning_message)
{
	document.getElementById("warning").innerHTML = '<p>'+warning_message+'</p>';
	document.getElementById("warning").style.display = "block";
}

function closeWarning()
{
  document.getElementById("warning").style.display = "none";
}

function cardchange(base_url)
{
  var cctypeval    = document.getElementById('cctype').value;
  
  var pos1 = cctypeval.indexOf('-');
  
  var cctype     = cctypeval.substring(0,pos1);
  var cccurrency = cctypeval.substring(pos1+1);
  
  var settleCurrency  = document.getElementById('book-settle-currency');
  
  if((cccurrency.toLowerCase() != "all") && (cccurrency.toLowerCase() != settleCurrency.value.toLowerCase()))
  {
    
    var i=0;
    
    while ((i < settleCurrency.options.length) && (settleCurrency.options[i].value.toLowerCase() != cccurrency.toLowerCase())) {i++;}
       
    if (i < settleCurrency.options.length)
    {
      settleCurrency.selectedIndex = i;
    }
    
    booking_confirm(base_url, true);
  }
}

function booking_confirm(base_url, refresh, settleCurrency)
{
	//get form data
	var firstname 	= document.getElementById('firstname').value;
	var lastname 	= document.getElementById('lastname').value;
	var Nationality = document.getElementById('Nationality').value;
	var gender 		= document.getElementById('gender').value;
	var arrival_time = document.getElementById('arrival_time').value;
	var EmailAddress = document.getElementById('EmailAddress').value;
	var sms          = document.getElementById('sms').value;
	var phone_number = document.getElementById('phone_number').value;
	var sign_me_up 	= document.getElementById('sign_me_up').value;
	var mail_subscribe 	= document.getElementById('mail_subscribe').checked;
	
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
	
	var roomNumbers = document.getElementsByName('book-roomNumber[]');
	var roomDescriptions = document.getElementsByName('book-roomTypeDescription[]');
	var roomDescriptionsTrans = document.getElementsByName('book-roomTypeDescriptionTranslated[]');
	
  var secure_final = document.getElementById('secure-final').value; 
	var secure_cookie 		= "";
	var secure_pares 		= "";
	var secure_transid 		= "";
	var secure_newsession 	= "";
	var secure_ip 			= "";
	var secure_usersession 	= "";
  
  var CADDepositAmount = document.getElementById('analytic-value').value;
	
	if(secure_final==true)
	{
		secure_cookie 		= document.getElementById('secure-cookie').value;
		secure_pares 		= document.getElementById('secure-pares').value;
		secure_transid 		= document.getElementById('secure-transid').value;
		secure_newsession 	= document.getElementById('secure-newsessionid').value;
		secure_ip 			= document.getElementById('secure-ip').value;
		secure_usersession 	= document.getElementById('secure-usersessionid').value;
	}
	
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
	if (typeof settleCurrency == "undefined")
	{
	  
	}
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
	var roomPreferences = document.getElementsByName('book-roomPreferences[]');
	var nbPersons 		= document.getElementsByName('book-nbPersons[]');
	var propertyName 	= document.getElementById('book-propertyName').value;
	var propertyNumber 	= document.getElementById('propertyNumber').value;
	var dateStart 		= document.getElementById('book-dateStart').value;
	var numNights 		= document.getElementById('book-numNights').value;

	if(refresh != true)
	{
	  refresh = false;
	}
	var rpArray = new Array();
	var npArray = new Array();
	for(var i=0;i<roomPreferences.length;i++)
	{
		rpArray.push(roomPreferences[i].value);
		npArray.push(nbPersons[i].value);
	}
	
	var roomNumberArray = new Array();
	var roomDescArray = new Array();
	var roomDescTransArray = new Array();
	for(var i=0;i<roomDescriptions.length;i++)
	{
		roomDescArray.push(roomDescriptions[i].value);
		roomNumberArray.push(roomNumbers[i].value);
		roomDescTransArray.push(roomDescriptionsTrans[i].value);
	}
	
	$('#submit-payment').hide();
	
	$('.api_error').hide();
	
	if(refresh == false)
  {
	  $('#ssl-img').hide();
		$("#loading_message").show();
  }
	if(refresh == true)
	{
	  $("#loading_message_cur").show();
	}
	
	
	$.ajax({type:"POST",
		url:base_url+"chostel/booking_check/",
		data: { firstname: firstname,
				lastname: lastname,
				nationality: Nationality,
				gender: gender,
				arrival_time: arrival_time,
				email_address: EmailAddress,
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
				roomPreferences: rpArray.toString(),
				roomNumber: roomNumberArray.toString(),
				roomTypeDescription: roomDescArray.toString(),
				roomTypeDescriptionTranslated: roomDescTransArray.toString(),
				nbPersons: npArray.toString(),
				propertyName: propertyName,
				propertyNumber: propertyNumber,
				dateStart: dateStart,
				numNights: numNights,
				bookCurrency: bookCurrency,
				settleCurrency: settleCurrency,
				secure_final: secure_final,
				secure_cookie: secure_cookie,
				secure_pares: secure_pares,
				secure_transid: secure_transid,
				secure_newsession: secure_newsession,
				secure_ip: secure_ip,
				secure_usersession: secure_usersession,
				refresh: refresh,
				CADDepositAmount: CADDepositAmount
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
			$("#main").html(data);
			
      if(refresh == false)
      {
        $('.booking_widget').hide();
				$('.booking_end_widget').show();
				var target = $('#wrapper');
        var top = target.offset().top;
        $('html,body').animate({scrollTop: top}, 1000);
       			
        //pageTracker._trackPageview("/click/complete");
      }
      
		}
	});
	
}

function booking_confirm2(base_url, refresh, settleCurrency)
{
	//get form data
	var firstname 	= document.getElementById('firstname').value;
	var lastname 	= document.getElementById('lastname').value;
	var Nationality = document.getElementById('Nationality').value;
	var arrival_time = document.getElementById('arrival_time').value;
	var EmailAddress = document.getElementById('EmailAddress').value;
	var phone_number = document.getElementById('phone_number').value;
	var sms          = document.getElementById('sms').value;
	var sign_me_up 	= document.getElementById('sign_me_up').value;
	var mail_subscribe 	= document.getElementById('mail_subscribe').checked;

  var CADDepositAmount = document.getElementById('analytic-value').value;
	
	
	var	f_count = document.getElementById('female_count').value;
	var	m_count = document.getElementById('male_count').value;
	
	var card_types  = document.getElementById('propertyCardTypes').value;
	var ccname 		= document.getElementById('ccname').value;
	//var ccnumber 	= document.getElementById('ccnumber').value;
	var ccnumber 	= document.getElementById('ccnumber').value.replace(/ /g,'');

	var cctype     = document.getElementById('cctype').value;
	var ccexpiry_m 	= document.getElementById('ccexpiry_m').value;
	var ccexpiry_y 	= document.getElementById('ccexpiry_y').value;
	var cvv 		= document.getElementById('cvv').value;
	
	
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
	
	//vars for refresh 
	var roomPreferences = document.getElementsByName('book-roomPreferences[]');
	var nbPersons 		= document.getElementsByName('book-nbPersons[]');
	var propertyName 	= document.getElementById('book-propertyName').value;
	var propertyNumber 	= document.getElementById('propertyNumber').value;
	var dateStart 		= document.getElementById('book-dateStart').value;
	var numNights 		= document.getElementById('book-numNights').value;

	if(refresh != true)
	{
	  refresh = false;
	}
	var rpArray = new Array();
	var npArray = new Array();
	for(var i=0;i<roomPreferences.length;i++)
	{
		rpArray.push(roomPreferences[i].value);
		npArray.push(nbPersons[i].value);
	}
	
	$('#submit-payment').hide();
	$('.api_error').hide();
	if(refresh == false)
  {
	  $('#ssl-img').hide();
		$("#loading_message").show();
  }
	if(refresh == true)
	{
	  $("#loading_message_cur").show();
	}
	
	
	$.ajax({type:"POST",
		url:base_url+"chostelbk/booking_check/",
		data: { firstname: firstname,
				lastname: lastname,
				nationality: Nationality,
				female_count: f_count,
				male_count: m_count,
				arrival_time: arrival_time,
				email_address: EmailAddress,
				phone_number: phone_number,
				sms: sms,
				sign_me_up: sign_me_up,
				mail_subscribe: mail_subscribe,
				ccname: ccname,
				ccnumber: ccnumber,
				cctype: cctype,
				ccexpiry_m: ccexpiry_m,
				ccexpiry_y: ccexpiry_y,
				cvv: cvv,
				ccvalidfrom_m: ccvalidfrom_m,
				ccvalidfrom_y: ccvalidfrom_y,
				issueno: issueno,
				roomPreferences: rpArray.toString(),
				nbPersons: npArray.toString(),
				propertyName: propertyName,
				propertyNumber: propertyNumber,
				dateStart: dateStart,
				numNights: numNights,
				bookCurrency: bookCurrency,
				settleCurrency: settleCurrency,
				propertyCardTypes: card_types,
				refresh: refresh,
				CADDepositAmount: CADDepositAmount
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
			$("#main").html(data);
			
      if(refresh == false)
      {
        $('.booking_widget').hide();
				$('.booking_end_widget').show();
				var target = $('#wrapper');
        var top = target.offset().top;
        $('html,body').animate({scrollTop: top}, 1000);
       			
        //pageTracker._trackPageview("/click/complete");
      }
		}
		
	});
	
}

function showissueno()
{
	$('.issue_no').show();
}
function hideissueno()
{
	$('.issue_no').hide();
}

function showvalidfrom()
{
	$('.valid_from').show();
}
function hidevalidfrom()
{
	$('.valid_from').hide();
}

function pweb_setCookie(c_name,value,exhours)
{
	
	var exdate=new Date();
	exdate.setHours(exdate.getHours() + exhours);
	var c_value=escape(value) + ((exhours==null) ? "" : '; expires='+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value + '; path=/';
}

function getCookie(c_name)
{
if (document.cookie.length>0)
  {
  c_start=document.cookie.indexOf(c_name + "=");
  if (c_start!=-1)
    {
    c_start=c_start + c_name.length+1;
    c_end=document.cookie.indexOf(";",c_start);
    if (c_end==-1) c_end=document.cookie.length;
    return unescape(document.cookie.substring(c_start,c_end));
    }
  }
return "";
}

function customurlencode(url)
{
	url = url.replace("/", "-2F-");
	url = url.replace(/'/g, "-27-");
	url = url.replace(/ /g, "+");
	
	return url;
}

var dtCh= "-";
var minYear=1900;

function isInteger(s){
  var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
  var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
  // February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
  var dayarray = new Array();
  for (var i = 1; i <= n; i++) {
    this[i] = 31;
    if (i==4 || i==6 || i==9 || i==11) {dayarray[i] = 30;}
    if (i==2) {dayarray[i] = 29;}
   } 
   return dayarray;
}

function isValidDate(dtStr){
  var daysInMonth = DaysArray(12);
  var pos1=dtStr.indexOf(dtCh);
  var pos2=dtStr.indexOf(dtCh,pos1+1);
  var strYear=dtStr.substring(0,pos1);
  var strMonth=dtStr.substring(pos1+1,pos2);
  var strDay=dtStr.substring(pos2+1);
  var strYr = strYear;
  if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1);
  if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1);
  for (var i = 1; i <= 3; i++) {
    if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1);
  }
  var month = parseInt(strMonth);
  var day   = parseInt(strDay);
  var year  = parseInt(strYr);
  
  if (pos1==-1 || pos2==-1){
//    alert("The date format should be : mm/dd/yyyy")
    return false;
  }
  if (strMonth.length<1 || month<1 || month>12){
//    alert("Please enter a valid month")
    return false;
  }
  
  if (strDay.length < 1 || day < 1 || day > 31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
//    alert("Please enter a valid day")
    return false;
  }
  if (strYear.length != 4 || year==0 || year<minYear){
//    alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
    return false;
  }
  if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
//    alert("Please enter a valid date")
    return false;
  }
return true;
}

function auto_persons_count(source_field_id, target_field_id, max_persons)
{
	var targetValue = max_persons - document.getElementById(source_field_id).value;
	var selectBox = document.getElementById(target_field_id);
	var i=0;
	while ((i < selectBox.options.length) && (selectBox.options[i].value != targetValue) ) {i++;}
	if (i < selectBox.options.length)
    {
		selectBox.selectedIndex = i;
    }
}

function checkForMaxGuests(select_array,maxPax)
{
	
}