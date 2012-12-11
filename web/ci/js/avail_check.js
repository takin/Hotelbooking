function checkAvailability(base_url, country, city, propertyNumber, dateId, n, propertyName, currency, dateWarning, avail_table_id)
{
	$("#loading_dispo").show();
	
	var cards = "";
	if(document.getElementById('book-property-cards') != null)
	{
		cards = document.getElementById('book-property-cards').value;
	}

//	var c=obj.currency.options[obj.currency.selectedIndex].value;
//	var l=obj.language.value;
	var todayDate = new Date();
  todayDate.setHours(0);
  todayDate.setMinutes(0);
  todayDate.setSeconds(0);
  
  var searchDate 	=  $("#"+dateId).datepicker( "getDate" );
  var d = siteDateString(searchDate);
  
  searchDate.setHours(23);
  searchDate.setMinutes(59);
  searchDate.setSeconds(59);
  
	if(isValidDate(d) == false)
	{
	  triggerWarning(dateWarning);
    $("#loading_dispo").hide();
	}
	else if(todayDate > searchDate)
  {
	  triggerWarning(dateWarning);
    $("#loading_dispo").hide();
  }
	else
	{
	  closeWarning();
	$.ajax({type:"post",
			url:base_url+"/booking_avail/",
			data: { country_selected: country, city_selected: city, propertyNumber: propertyNumber, dateStart: d, numNights: n, propertyName: propertyName, currency:currency, propertyCards: cards },
			timeout:60000,
			error:function(XMLHttpRequest, textStatus, errorThrown)
			{
				$("#"+avail_table_id).html("<ul class=\"error\"><li>Erreur.<li>"+textStatus+"</li><li>"+errorThrown+"</li></li></ul>");
				$("#loading_dispo").hide();
			},
			success:function(data)
			{
				$("#"+avail_table_id).html(data);
				$("#loading_dispo").hide();
			}
		});
	}
	
}