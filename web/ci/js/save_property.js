function SavedProperty() {
	this.totalRecords = 0;
	this.results;
	this.results_limit = 10;

        this.jtable;
        this.jtableSorted;

        this.template = document.getElementById('template-saved_property_item').innerHTML;
};

SavedProperty.prototype.setup = function(data) {
	this.totalRecords = data.length;

	this.setData(data);

	this.setClickSort('data_sort_controls', 'sortname-tous', 'name');
        this.setClickSort('data_sort_controls', 'sortdate-tous', 'arrival_date');
        this.setClickSort('data_sort_controls', 'sortcity-tous', 'city');

	this.apply_filters('arrival_date', jOrder.asc);
};

SavedProperty.prototype.setData = function(json_data) {
	this.jtable = jOrder(json_data)
		.index('idIdx', ['id'])
		.index('nameIdx', ['name'], { grouped: true, ordered: true, type: jOrder.string })
		.index('arrival_dateIdx', ['arrival_date'], { grouped: true, ordered: true, type: jOrder.string })
		.index('cityIdx', ['city'], { grouped: true, ordered: true, type: jOrder.string });
};

SavedProperty.prototype.setClickSort = function(divID, DOMNodeID, rowname) {
        var that = this;

        $('#' + DOMNodeID).click(function() {
                $('#' + divID + ' .sorting').removeClass('activesort');
                $(this).addClass('activesort');

                if ($(this).children().hasClass('asc')) {
                        $(this).children().removeClass('asc');
                        $(this).children().addClass('desc');

                        that.apply_filters(rowname, jOrder.desc);
		}
                else {
                        $(this).children().removeClass('desc');
                        $(this).children().addClass('asc');

                        that.apply_filters(rowname, jOrder.asc);
                }

                return false;
        });
};

SavedProperty.prototype.apply_filters = function(field, order) {
	this.jtableSorted = this.jtable.orderby([field], order);

	this.update();

	this.initpaging();
};

SavedProperty.prototype.update = function() {
	var output = '';
	for (var i = 0; i < this.totalRecords; i++) {
		output += Mustache.to_html(this.template, this.jtableSorted[i]);
	}

	$('#favorite_properties').html(output);
};

SavedProperty.prototype.initpaging = function() {
	var show_per_page   = this.results_limit;
	var number_of_items = this.jtableSorted.length;
	var number_of_pages = Math.ceil(number_of_items / show_per_page);

	$('#current_page').val(0);
	$('#show_per_page').val(show_per_page);

	var navigation_html = '<a class="previous_link" href="javascript:SavedProperty.previous();"><</a>';
	var current_link = 0;

	while (number_of_pages > current_link) {
		navigation_html += '<a class="page_link" id="page_link_'+current_link+'" href="javascript:SavedProperty.go_to_page(' + current_link +')" longdesc="' + current_link +'">'+ (current_link + 1) +'</a>';
		current_link++;
	}
	navigation_html += '<a class="next_link" href="javascript:SavedProperty.next();">></a>';

	if (number_of_pages > 1) {
		$('.resultcount').html('1-'+show_per_page);
		$('#resu').css('display', 'block');
		$('#page_navigation').html(navigation_html);
	}
	else {
		$('.resultcount').html(number_of_items);
		$('#page_navigation').html('');
	}

	$('.resulttotal').html(number_of_items);

	if (number_of_pages > 0) {
		$('#navi').css('display', 'inline-block');
		$('#resu').css('display', 'block');
	}
	else {
		$('#navi').css('display', 'none');
		$('#resu').css('display', 'none');
	}

	$('#page_navigation .page_link:first').addClass('active_page');
	$('#favorite_properties').children().css('display', 'none');
	$('#favorite_properties').children().slice(0, show_per_page).css('display', 'block');
	$('.previous_link').css({"pointer-events":"none","color":"#ccc"});
	$('#page_link_0').css({"pointer-events":"none","color":"#ccc"});
};


SavedProperty.previous = function() {
	if ($('.active_page').prev('.page_link').length == true) {
		SavedProperty.go_to_page(parseInt($('#current_page').val()) - 1);
	}
};

SavedProperty.next = function() {
	if ($('.active_page').next('.page_link').length == true) {
		SavedProperty.go_to_page(parseInt($('#current_page').val()) + 1);
	}
};

SavedProperty.go_to_page = function(page_num) {
	$("html, body").animate({ scrollTop: 200 }, 400);

	var show_per_page = parseInt($('#show_per_page').val(), 10);
	var number_of_items = $('#favorite_properties').children().size();
	var number_of_pages = Math.ceil(number_of_items / show_per_page);

	$('.page_link').css({"pointer-events":"visible ","color":"#227BBD"});
	$('#page_link_'+page_num).css({"pointer-events":"none","color":"#ccc"});

	if (page_num > 0) {
		$('.previous_link').css({"pointer-events":"visible ","color":"#227BBD"});
	}
	else {
		$('.previous_link').css({"pointer-events":"none","color":"#ccc"});
	}

	if (page_num == number_of_pages - 1) {
		$('.next_link').css({"pointer-events":"none","color":"#ccc"});
		var startfrom = show_per_page*parseFloat(number_of_pages-1);
		$('.resultcount').html(startfrom+'-'+number_of_items);
	}
	else {
		if (page_num == 0) {
			var startfrom=1;
		}
		else if (page_num == 1) {
			var startfrom = show_per_page+1;
		}
		else {
			var startfrom=(show_per_page*parseFloat(page_num))+1;
		}

		var endto=show_per_page*parseFloat(page_num+1);
		$('.resultcount').html(startfrom+'-'+endto);
		$('.next_link').css({"pointer-events":"visible ","color":"#227BBD"});
	}

	start_from = page_num * show_per_page;
	end_on = start_from + show_per_page;

	$('#favorite_properties').children().css('display', 'none').slice(start_from, end_on).css('display', 'block');
	$('.page_link[longdesc=' + page_num +']').addClass('active_page').siblings('.active_page').removeClass('active_page');
	$('#current_page').val(page_num);
};











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

		loadSavedPropertyList();
	}

	function loadSavedPropertyList() {
		if (typeof(favorite_properties_url) == 'undefined') {
			return;
		}

		var savedProperty = new SavedProperty();

		$.ajax({
			type    : "GET",
			url     : favorite_properties_url,
			success : function(data) {
				savedProperty.setup(data);
			}
		});

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
				dateVal        : $.datepicker.formatDate('yy-mm-dd', $('#book-pick').datepicker('getDate')),
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
		dialog.find('.date_show').datepicker({
			dateFormat: 'd MM yy',
			showOn: 'button',
			altField: "#date",
			altFormat:'yy-mm-dd',
			onSelect: function(newVal) {
				$('#date_show, #save_fav .ui-datepicker-trigger').hide();
				$('#save_fav .schedule_details .date .num').show();

				$('#save_fav .schedule_details .date .num').text(newVal);
			}
		});
		dialog.find('.ui-datepicker').hide();
		$('#date_show, #save_fav .ui-datepicker-trigger').hide();
		dialog.show();
	}

	function changeDate(label, input) {
		$(label).hide();
		$('#save_fav .ui-datepicker-trigger').show();
		$(input).show();
	}

	function handleSaveForm(formElem) {
		var form = $(formElem);

		var propertyNumber = form.find('input[name="propertyNumber"]').val();

		form.find('.actions').hide();

		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			data: {
				id             : form.find('input[name="id"]').val(),
				propertyNumber : propertyNumber,
				nights         : form.find('input[name="nights"]').val(),
				date           : form.find('input[name="date"]').val(),
				notes          : form.find('textarea[name="notes"]').val()
			},
			dataType: 'json',
			success: function() {
				form.find('.actions').show();

				$('#prop_tab_box_' + propertyNumber).find('.save_to_favorites').hide();
				$('#prop_tab_box_' + propertyNumber).find('.saved_to_favorites').show();

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
		bindAddToFav        : bindAddToFav,
		changeDate          : changeDate
	}
}();


$(document).ready(function() {
	SaveProperty.init();
	SaveProperty.bindAddToFav();
});
