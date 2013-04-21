var SaveProperty = function() {
	var dialogTemplate = null;
	var maxCharacters  = 75;
	var dialog         = null;

	function init() {
		dialog = $('#save_property_dialog');

		dialogTemplate = document.getElementById('template-save-favorite')
			? document.getElementById('template-save-favorite').innerHTML
			: '';

		bindAddToFav();
		bindCloseDialog();
	}

	function bindCloseDialog() {
		dialog.find('.close a').live('click', function(event) {
			event.preventDefault();

			dialog.hide();
		});
	}

	function bindAddToFav() {
		$('.save_to_favorites').live('click', function(event) {
			event.preventDefault();

			var obj = $(this);

			var propertyNumber = obj.attr('id').replace('save_to_favorites_', '');

			showSaveDialog({
				favoriteId     : '',
				propertyNumber : propertyNumber,
				imageURL       : $('#prop_tab_box_' + propertyNumber + ' .info_pic img').attr('src'),
				propertyName   : obj.attr('title'),
				city           : $('.city_selected').html(),
				country        : $('.country_selected').html(),
				date           : $('#city_results_arrive_date').html(),
				nights         : $('#city_results_numnights_selected').html(),
				notes          : '',
				characters     : 0,
				isUpdate       : false,
				isNew          : true
			});
		});
	}

	function showSaveDialog(data) {
		var output = Mustache.to_html(dialogTemplate, data);

		dialog.find('.content').html(output);
		countRemainingChars(dialog.find('.notes'), dialog.find('.characters .num'));
		dialog.find('.date').datepicker({
			dateFormat: 'd MM yy',
			showOn: 'button',
			altField: "#date",
			altFormat:'yy-mm-d',
			onSelect: function() {
			}
		});
		dialog.find('.ui-datepicker').hide();
		dialog.show();
	}

	function handleSaveForm(formElem) {
		var form = $(formElem);

		form.find('.actions').hide();

		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			data: {
				id             : form.find('input[name="id"]').val(),
				propertyNumber : form.find('input[name="propertyNumber"]').val(),
				nights         : form.find('input[name="nights"]').val(),
				date           : form.find('input[name="date"]').val(),
				notes          : form.find('textarea[name="notes"]').val()
			},
			dataType: 'json',
			success: function() {
				form.find('.actions').show();
				dialog.hide();
			}
		}).fail(function() {
			form.find('.actions').show();
		});

		return false;
	}

	function countRemainingChars(currentElem, counterContainer) {
		var obj       = $(currentElem);
		var remaining = maxCharacters - obj.val().length;

		if (remaining < 0) {
			remaining = 0;

			obj.val(obj.val().substr(0, maxCharacters));
		}

		$(counterContainer).html(remaining);

		// plural/singular
		if (remaining == 1) {
			$(counterContainer).parent().find('.plural').hide();
			$(counterContainer).parent().find('.singular').show();
		}
		else {
			$(counterContainer).parent().find('.plural').show();
			$(counterContainer).parent().find('.singular').hide();
		}

		// no new chars
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
