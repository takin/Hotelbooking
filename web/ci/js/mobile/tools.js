 function scrollGo() {
   var x = $(this).offset().top;
   $('html,body').animate({scrollTop: x}, 0);
	 this.value = '';
  }
 $(document).ready(function() {
  	$('#show-menu').click(function() {
  		$(this).toggleClass('minus');
			$('.main-menu').slideToggle('fast');
			return false;
 		});
		
		$('.trigger').click(function() {
  		if (!$(this).hasClass('minus')){
				$('#content h2.minus').each(function (){
					$(this).toggleClass('minus');
					$(this).next('.trigger-content').slideToggle('fast');
				});
			}
			$(this).toggleClass('minus');
			$(this).next('.trigger-content').slideToggle('fast');							
			return false;
 		});
		
		$('a.change-dates').click(function() {
  		$(this).toggleClass('minus');
			$(this).next('.change-dates-select').slideToggle('fast');
			return false;
 		});
		//$("a.openup").fancybox();
		
		/*$('.checkbox').iphoneStyle({
			checkedLabel: 'YES',
			uncheckedLabel: 'NO'
		});*/
		
		/*$('input.autovalue[type="text"]').focus(function() {	
			this.value = '';
			scrollGo();
		});*/
		$('#search-custom').focus(scrollGo); 
		
 });
 
 
