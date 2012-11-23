function submit_group_request()
{
	$('#group_request_success').hide();
	$('#submit-button').hide();
	$('#loading_message').show();
	
	var formdata = jQuery("#group_request").serialize()+'&datecal='+siteDateString($('#datepick').datepicker( "getDate" ));
	jQuery.ajax({
	    type: "POST",
	    url:"http://"+window.location.host+"/ax/group_request",
//	    dataType: 'xml',
	    data: formdata,
	    success: function(data){
	    	$('#loading_message').hide();
	    	$('#group_request_success').show();
	    	}
	    });
}
function check_error(){
	var demand, nstaff,rm_type;
	$('#check_error').hide();
	
	//validate staff nb because livevalidation fails to do it
	nstaff = $("#nb_person_staff option:selected").val();
	rm_type = $("#rm-type option:selected").val();
	
	if ((nstaff !== "0")&&(rm_type===""))
	{
		$("#rm-type").addClass('LV_invalid_field');
		$("#rm_type_error").show();
		$('#check_error').show();
	}
	else
	{
		$("#rm-type").removeClass('LV_invalid_field');
		$("#rm_type_error").hide();
		$("#group_request").submit();
		return false;
	}
	
	if($('select').hasClass('LV_invalid_field') || $('input').hasClass('LV_invalid_field')){
		$('#check_error').show();
		return false;
	}else{
		return true;
	}
}

function updatetotal()
{
	var nbmale, nbfemale, nbstaff,total;
	
	nbmale   = $("#nb-male-gp").val();
	nbfemale = $("#nb-female-gp").val();
	nbstaff  = $("#nb_person_staff").val();
	
	if(nbmale === "") nbmale = 0;
	if(nbfemale === "") nbfemale = 0;
	if(nbstaff === "") nbstaff = 0;
	
	total = parseInt(nbmale)+parseInt(nbfemale)+parseInt(nbstaff);
	$(".total-nb-people").text(total);
}
$(document).ready(function(){
	$("#nb-male-gp").change(function() {updatetotal()});
	$("#nb-female-gp").change(function() {updatetotal()});
	$("#nb_person_staff").change(function() {updatetotal()});
});
