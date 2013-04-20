var SaveProperty = function() {
	var dialogTemplate = null;
	var maxCharacters  = 75;

	function init() {
		dialogTemplate = document.getElementById('template-save-favorite')
			? document.getElementById('template-save-favorite').innerHTML
			: '';

		bindAddToFav();
	}

	function bindAddToFav() {
		$('.save_to_favorites').live('click', function(event) {
			event.preventDefault();

			var obj = $(this);

			var propertyNumber = obj.attr('id').replace('save_to_favorites_', '');

			showSaveDialog({
				favoriteId     : '',
				propertyNumber : propertyNumber,
				imageURL       : $('#info_pic img').attr('src'),
				propertyName   : obj.attr('title'),
				'location'     : obj.attr('rel'),
				date           : $('#city_results_arrive_date').html(),
				nights         : $('#city_results_numnights_selected').html(),
				notes          : '',
				characters     : 0
			});
		});
	}

	function showSaveDialog(data) {
		var output = Mustache.to_html(dialogTemplate, data);
	}

	function handleSaveForm() {
	}

	function countRemainingChars(currentElem, counterContainer) {
		var obj       = $(elem);
		var remaining = maxCharacters - obj.val().length;

		if (remaining < 0) {
			remaining = 0;

			obj.val(obj.val().substr(0, maxCharacters));
		}

		$(counterContainer).html(remaining);

		if (remaining == 0) {
			return false;
		}

		return true;
	}

	return {
		handleSaveForm      : handleSaveForm,
		countRemainingChars : countRemainingChars,
		init                : init,
		bindAddToFav        : bindAddToFav
	}
}();


$(document).ready(function() {
	SaveProperty.init();
	SaveProperty.bindAddToFav();
});
