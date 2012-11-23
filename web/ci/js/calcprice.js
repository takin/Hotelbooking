$(document).ready(function(){		
	
	$("#booking-table select").change(function () {
		var selectbox = $(this);
		var theid = selectbox.attr('id');
		var nbsel = selectbox.val();
		var nbtxt = selectbox.find("option:selected").text();

		//var nbtxt = $(theid + " option[value='"+nbsel+"']").text();
		var theclass = selectbox.attr('class')+"_";
		var nbroom = selectbox.attr('id').replace(theclass, '');
		
		if (theclass == 'privatesel_'){
			$("#pnbguest_" + nbroom).children().text(nbtxt);
			if (nbsel != 0){
			
				$("#psubtotal_calc_" + nbroom).calc(
					"qty * price",
					
					{
						qty: nbtxt,
						price:$("#psubtotal_init_" + nbroom)
					},
					
					function (s){
					
						return s.toFixed(2);
					},
					
					function ($this){							
						var sum = $this.sum();
						$("#bigTotal").text($(".calc_sum").sum().toFixed(2));			
						$("#depositTotal").text((parseFloat($("#bigTotal").text()) / 10).toFixed(2));
					}
				);
				
				
				$("#proomnb_" + nbroom).show();		
			}else{
				$("#proomnb_" + nbroom).hide();
				$("#psubtotal_calc_" + nbroom).text(0.00);
				$("#bigTotal").text($(".calc_sum").sum().toFixed(2));		
				$("#depositTotal").text((parseFloat($("#bigTotal").text()) / 10).toFixed(2));
			}
		}
		
		if (theclass == 'sharedsel_'){
			$("#snbguest_" + nbroom).children().text(nbsel);
			if (nbsel != 0){
				
				$("#ssubtotal_calc_" + nbroom).calc(
					"qty * price",
					
					{
						qty: nbsel,
						price: $("#ssubtotal_init_" + nbroom)
					},
					
					function (s){
					
						return s.toFixed(2);
					},
					
					function ($this){
						
						var sum = $this.sum();
						
						$("#bigTotal").text($(".calc_sum").sum().toFixed(2));	
						$("#depositTotal").text((parseFloat($("#bigTotal").text()) / 10).toFixed(2));
					}
				);
				
				$("#sroomnb_" + nbroom).show();		
			}else{
				$("#sroomnb_" + nbroom).hide();
				$("#ssubtotal_calc_" + nbroom).text(0.00);
				$("#bigTotal").text($(".calc_sum").sum().toFixed(2));	
				$("#depositTotal").text((parseFloat($("#bigTotal").text()) / 10).toFixed(2));
			}
		}
						 
		var showtable = false;
		$("#booking-table select").each(function () {
			if ($(this).val() != 0){
				showtable = true;
			}
		});
		
		if (showtable == true){
			$("#selection").show();
			$("#formerror").hide();
			
		}else{
			$("#selection").hide();			
		}
	
	})

});