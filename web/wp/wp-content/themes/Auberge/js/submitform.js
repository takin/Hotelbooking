$(document).ready(function(){
	  
	$("#sendemail-submit").click(function(){					   				   
		$(".error").hide();
		var hasError = false;
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		
		var nameVal = $("#name").val();
		
		if((nameVal == '')) {
			$("#name1").addClass("form_error");
			$("#name").addClass("form_error");
			hasError = true;
		}else{
			$("#name1").removeClass("form_error");
			$("#name").removeClass("form_error");
		}
		
		var emailFromVal = $("#email").val();
		
		if((emailFromVal == '') || (emailFromVal == 'Email')) {
			$("#email1").addClass("form_error");
			$("#email").addClass("form_error");
			hasError = true;
		} else if(!emailReg.test(emailFromVal)) {	
			$("#email1").addClass("form_error");
			$("#email").addClass("form_error");
			hasError = true;
		} else{
			$("#email1").removeClass("form_error");
			$("#email").removeClass("form_error");
		}
		
		var subjectVal = $("#subject").val();
		if ((subjectVal == '') || (subjectVal == 'Subject')) {
			$("#subject1").addClass("form_error");
			$("#subject").addClass("form_error");
			hasError = true;
		} else{
			$("#subject1").removeClass("form_error");
			$("#subject").removeClass("form_error");
		}
		
		var messageVal = $("#message").val();
		if(messageVal == '') {
			$("#message1").addClass("form_error");
			$("#message").addClass("form_error");
			hasError = true;
		} else{
			$("#message1").removeClass("form_error");
			$("#message").removeClass("form_error");
		}
		
		var verifVal = $("#verif").val();
		if(verifVal == '9') {
			$("#verif1").removeClass("form_error");
			$("#verif").removeClass("form_error");
			
		}else{
			$("#verif1").addClass("form_error");
			$("#verif").addClass("form_error");
			hasError = true;
		}
		
		var numreservVal = $("#numreserv").val();
		
		var validationVal = $("#validation").val();
		
		var errorstringVal = $("#errorstring").val();
		
		var adminemailVal = $("#adminemail").val();
		
			
		var templateurlVal = $("#templateurl").val();
		var permaVal = $("#perma").val();
		var sitenameVal = $("#sitename").val();
		
		var imageVal = '<li class="loading"><img src="'+templateurlVal+'/images/loading-cform.gif" alt="Chargement" id="loading" /></li>';
				
		
		if(hasError == false) {
			$(this).hide();
			$("li.buttons").after(imageVal);
			
			$.post(templateurlVal+"/sendemail.php",
   				{ name: nameVal, emailFrom: emailFromVal, subject: subjectVal, message: messageVal, sitename: sitenameVal, numreserv: numreservVal, adminemail: adminemailVal },
   					function(data){
						$("#sendEmail").before('<p class="success-sent">'+validationVal+'</p>');
						$("#sendEmail").slideUp("normal", function() {
									
							
						});
   					}
				 );
		} else {
			
			$("li.buttons").after('<li class="error">* '+errorstringVal+'</li>');
		}
		
		return false;
	});						   
});