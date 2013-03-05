jQuery(document).ready(function(){
		jQuery("img").error(function(){
			jQuery(this).hide();
		});
});
jQuery(function()
{

	jQuery('.select-translate').change(function() {

			var api = jQuery(this).data('jsp');

			var version = jQuery(this).val();
			if (version =='translate'){
				jQuery(this).parent().find(".original").fadeOut(500, function () { jQuery(this).parent().find(".translated").fadeIn(500); });
			}
			if (version =='original'){
				jQuery(this).parent().find(".translated").fadeOut(500, function () { $(this).parent().find(".original").fadeIn(500); });
			}
		});
	//To show full search if user has no search cookies
	var city_selected = getCookie('city_selected');	

                $('a.modify_search').toggleClass('expand');
                $('a.modify_search').toggleClass('collapse');
          if(city_selected == '' )
          {
               // show everything in side search box 
               // except modify search and back to result
               // because this user land page
                $('#modify_search').hide();
                $('#back_to_results').hide();
		
                $('#search_now').show();
                $('#side_search_wrap_city').show();
		$('#side_search_wrap').show();
                $('#back_to_results').hide();		
	  }
          else
          {
               // hide everything in side search box 
               // except header
                $('#modify_search').show();
                $('#back_to_results').show();
		
                $('#search_now').hide();
                $('#side_search_wrap_city').hide();
		$('#side_search_wrap').show();		
          }
      
});