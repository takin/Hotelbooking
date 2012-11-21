$(document).ready(function(){
	$('#cur-gbp').click(function(){
		$('a.cur-selected').toggleClass('cur-selected');
		$(this).toggleClass('cur-selected');
		$('span.gbp').show();
		$('span.usd').hide();
		$('span.eur').hide();
		return false;
	});
	$('#cur-eur').click(function(){
		$('a.cur-selected').toggleClass('cur-selected');
		$(this).toggleClass('cur-selected');
		$('span.gbp').hide();
		$('span.usd').hide();
		$('span.eur').show();
		return false;
	});
	$('#cur-usd').click(function(){
		$('a.cur-selected').toggleClass('cur-selected');
		$(this).toggleClass('cur-selected');
		$('span.gbp').hide();
		$('span.usd').show();
		$('span.eur').hide();
		return false;
	});
});