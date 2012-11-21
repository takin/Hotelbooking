function goToSearchPage(base_url,countryEmptyVal,countryWarning,cityEmptyVal,cityWarning,dateWarning,
						countryId,cityId,dateId,nightsId, currencyId, searchId)
{
	var district_id = 0;
	var landmark_id = 0;
	var type_filter = "";
	
	if(document.getElementById('district_id'))
	{
		district_id = document.getElementById('district_id').value;
	}
	if(document.getElementById('landmark_id'))
	{
		landmark_id = document.getElementById('landmark_id').value;
	}
	if(document.getElementById('type_filter'))
	{
		//removed filter by type as per projects/aj-features/tasks/20
//		type_filter = document.getElementById('type_filter').value;
	}
	if((typeof clear_suggestions == 'function')&&(document.getElementById("search-suggest")))
	{ 
		clear_suggestions(); 
	}
	var countrySelected = countryEmptyVal;
	var citySelected    = cityEmptyVal;
	if(document.getElementById(countryId).type == 'hidden')
	{
		countrySelected = document.getElementById(countryId).value;
	}
	else
	{
		countrySelected = $('#'+countryId+' :selected').val();
	}
	if(document.getElementById(cityId).type == 'hidden')
	{
		citySelected = document.getElementById(cityId).value;
	}
	else
	{
		citySelected = $('#'+cityId+' :selected').val();
	}
//	var searchDate 	=  new Date($('#'+dateId).val().replace('-',','));
	var searchDate 	=  $('#'+dateId).datepicker( "getDate" );
//	var searchDateString = $('#'+dateId).val();
	var searchDateString = siteDateString(searchDate);
	var searchNbNights  = $('#'+nightsId+' :selected').val();
	var searchCurrency  = $('#'+currencyId+' :selected').val();
	
	var searchTerm = $('#'+searchId).val();
	var searchURL = $('#custom-url').val();
	var searchURLtype = $('#custom-type').val();
	
	var querystring = "?";
	if(searchCurrency)
    {
		querystring = querystring+"currency="+searchCurrency+"&";
    }
	if(district_id > 0)
	{
		querystring = querystring+"di="+district_id+"&";
	}
	if(landmark_id > 0)
	{
		querystring = querystring+"la="+landmark_id+"&";
	}
	if(type_filter !== "")
	{
		querystring = querystring+"cat="+type_filter+"&";
	}
	if(querystring === "?")
	{
		querystring = "";
	}
	else
	{
		querystring = querystring.substring(0, querystring.length - 1);
	}
	
	countrySelected = customurlencode(countrySelected);
	citySelected    = customurlencode(citySelected);
	
	var validDate = false;
	validDate = isValidDate(searchDateString);
	
	var todayDate = new Date();
	todayDate.setHours(0);
	todayDate.setMinutes(0);
	todayDate.setSeconds(0);
	
	searchDate.setHours(23);
	searchDate.setMinutes(59);
	searchDate.setSeconds(59);

	if((document.getElementById(searchId)) && !$('#'+searchId).hasClass('disabled'))
	{
		pweb_setCookie('date_selected',searchDateString,2);
		pweb_setCookie('numnights_selected',searchNbNights,2);
		pweb_setCookie('search_input_terms',encodeURIComponent(searchTerm),2);
		
		if(validDate == false)
		{
		  triggerWarning(dateWarning);
		}
		else if(todayDate > searchDate)
		{
			triggerWarning(dateWarning);	
		}
		else if(searchURL)
		{
			
			$("#loading-search").show();
			
			switch(searchURLtype)
			{
				//property link
				case '0':
					window.location = searchURL + querystring;
					break;
				//city link
				case '1':
					window.location = searchURL + "/" + searchDateString + "/"+searchNbNights + querystring;
					break;
				//country link
				case '2':
					window.location = searchURL + querystring;
					break;
				case 'moreresults':
					window.location = searchURL;
					break;
				default:
					window.location = searchURL + querystring;
					break;
			}
			
		}
		else if(typeof searchSuggest == 'function')
		{
			if(suggest_xhr && (searchTerm.length > 2)) 
			{
				create_suggest_box(suggest_xhr.responseText);
			}
//			searchTerm = customurlencode(searchTerm);
//			$("#loading-search").show();
//			window.location = base_url + "s/"+searchTerm;
		}	
	}
	else if($('#'+countryId+' :selected').val() == countryEmptyVal)
	{
		triggerWarning(countryWarning);
	}
	else if($('#'+cityId+' :selected').val() == cityEmptyVal)
	{
		triggerWarning(cityWarning);
	}
	else if(validDate == false)
	{
	  triggerWarning(dateWarning);
	}
	else if(todayDate > searchDate)
	{
		triggerWarning(dateWarning);	
	}
	else
	{
		$("#loading-search").show();
		window.location = base_url + countrySelected+"/"+citySelected+"/"+searchDateString+"/"+searchNbNights + querystring;
	}
}

function loadCitiesMenu(base_url,loading_message,citiesVarName,citiesVar,countryFieldId,cityFieldId,selectedcountry,selectedcity)
{
	setCountries(citiesVar,countryFieldId);
	selectCountryField = document.getElementById(countryFieldId);
	selectCountryField.options[selectCountryField.length] = new Option(loading_message, '');	
	
	$.ajax({
	    type: "GET",
	    url: base_url+"/citylistdb/",
	    dataType: 'xml',
	    data: { citiesVarName: citiesVarName },
	    success: 
	    function(xml){
	      xmlData = xml;
	      var selectCountryField = document.getElementById(countryFieldId);
	      
	      var i=0;

//	      for(i=1;i<7;i++)
//	      {
//	        var topCountry = selectCountryField.options[i].value;
//
//	        $(xml).find('Country').each(function(){
//	          var countrySelectText = $(this).find('countrySelectText').text();
//	          var countrySelectVal = $(this).find('countrySelectVal').text();
//	          var countryName = $(this).find('countryName').text();
//	          if(topCountry == countryName)
//	          {
//	            selectCountryField.options[i] = new Option(countrySelectText, countrySelectVal);
//	          }
//	        });
//	      }
	      i++;
	      var cookie_country_val = getCookie('country_selected');
	      var cookie_city_val    = getCookie('city_selected');
	      
	      cookie_country_val = decodeURIComponent(cookie_country_val.replace(/[+]/g,' '));
	      cookie_city_val    = decodeURIComponent(cookie_city_val.replace(/[+]/g,' '));
	      
	      $(xml).find('Country').each(function(){
	        var countrySelectText = $(this).find('countrySelectText').text();
	        var countrySelectVal  = $(this).find('countrySelectVal').text();
	        var countryName       = $(this).find('countryName').text();
	        selectCountryField.options[i] = new Option(countrySelectText, countrySelectVal);

	        if(countryName.toLowerCase() == cookie_country_val.toLowerCase())
	        {
	          cookie_country_val = countrySelectVal;
	          $(this).find('City').each(function(){
	            var cityName       = $(this).find('cityName').text();
	            var citySelectVal  = $(this).find('cityNameSelectVal').text();
	            if(cityName.toLowerCase() == cookie_city_val.toLowerCase())
	            {
	              cookie_city_val = citySelectVal;
	            }
	          });
	        }
	        
	        i++;
	      });
	      
//	      selectField.options.sort();
	      sortSelect(selectField,1);
//		   sortCountrySelect(countryFieldId,8,citiesVar);
//		   
		   if (typeof selectedcountry != "undefined")
			 {
			   var i=0;
			   while ((i < selectCountryField.options.length) && (selectCountryField.options[i].value.toLowerCase() != selectedcountry.toLowerCase()) ) {i++;}
			   
			   if (i < selectCountryField.options.length)
			   {
				   selectCountryField.selectedIndex = i;
			   }
			   setCities(nocityval,countryFieldId,cityFieldId);
			 }
		   else
		   {
			   var selected_country = cookie_country_val;
			   
			   if(cookie_country_val != "")
			   {
				   var i=0;
				   while ((i < selectCountryField.options.length) && (selectCountryField.options[i].value.toLowerCase() != cookie_country_val.toLowerCase())) {i++;}
				   
				   if (i < selectCountryField.options.length)
				   {
					   selectCountryField.selectedIndex = i;
				   }
				   setCities(nocityval,countryFieldId,cityFieldId);
			   }
		   }
		   
		   selectCityField = document.getElementById(cityFieldId);
		   if (typeof selectedcity != "undefined")
			 {
				var i=0;
			
				while ((i < selectCityField.options.length) && (selectCityField.options[i].value.toLowerCase() != selectedcity.toLowerCase())) {i++;}
				   
				if (i < selectCityField.options.length)
				{
					selectCityField.selectedIndex = i;
				}
			 }
		   else
		   {
			   if(cookie_city_val != "")
			   {
					var i=0;
						while ( (i < selectCityField.options.length) && (selectCityField.options[i].value.toLowerCase() != cookie_city_val.toLowerCase())) {i++;}
						   
						if (i < selectCityField.options.length)
						{
							selectCityField.selectedIndex = i;
						}
			   }
			 }
			
		 }});
	
}