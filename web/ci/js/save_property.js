function SavedProperty() {
	this.request;

        this.jtable;
        this.jtable_hits;
        this.jtable_hits_sorted;
        this.results_limit;
	this.FiltersCounts = {};

        this.template = document.getElementById('template-saved_property_item').innerHTML;
};

SavedProperty.prototype.setup = function(data) {
	//data = jQuery.parseJSON(data);

	var totalRecords = data.length;

	this.setData(data);

	this.setClickSort('data_sort_controls','sortname-tous','propertyName');
        this.setClickSort('data_sort_controls','sortdate-tous','arrivalDate');
        this.setClickSort('data_sort_controls','sortcity-tous','city');

	$('#data_sort_controls').show();

	this.apply_filters();
	this.set_init_filters_value();
};

SavedProperty.prototype.setData = function(json_data) {
        jOrder.logging = null;

	this.jtable = jOrder(json_data);
		//.index('propertyNumber', ['propertyNumber'], { grouped: false, ordered: true, type: jOrder.number });

	this.FiltersCounts['city_results_count_total'] = json_data.length;
};

SavedProperty.prototype.setClickSort = function(divID, DOMNodeID, rowname) {
        var that = this;

        $('#' + DOMNodeID).click(function() {
                $('#' + divID + ' .sorting').removeClass('activesort');
                $(this).addClass('activesort');

                if ($(this).children().hasClass('asc')) {
                        $(this).children().removeClass('asc');
                        $(this).children().addClass('desc');
                        that.sort_hits(rowname,jOrder.desc,true);
		}
                else {
                        $(this).children().removeClass('desc');
                        $(this).children().addClass('asc');

                        that.sort_hits(rowname, jOrder.asc, true);
                }

                return false;
        });
};

SavedProperty.prototype.sort_hits = function(indexname,dir,update) {
//        this.actual_sort_index = this.fetch_index(indexname);
  //      this.actual_sort_order = dir;

   //     if(this.actual_sort_index === false)
     //   {
                //log error in console
         //       return false;
       // }

        this.jtable_hits_sorted = jOrder( this.jtable_hits );

            //.index('propertyNumber', ['propertyNumber'], { grouped: false, ordered: true, type: jOrder.number })
            //.index(this.actual_sort_index.row, [this.actual_sort_index.row], {grouped: true, ordered: true, type: this.actual_sort_index.type})
            //.orderby([this.actual_sort_index.row], this.actual_sort_order,{ indexName: this.actual_sort_index.row});

        if(update !== undefined)
        {
          //      this.update();
        }

};// end sort_hits


SavedProperty.prototype.init_counts = function() {
	this.FiltersCounts['city_results_count_current']    = 0;
	this.FiltersCounts['city_results_count_total_temp'] = 0;
	this.FiltersCounts['city_results_filtered_temp']    = 0;
};

SavedProperty.prototype.apply_filters = function() {
//        this.$data_empty_msg.hide();
 //       this.$sort_controls_div.hide();
   //     this.$data_div.html("");
     //   this.$data_loading_msg.show();

        this.init_counts();

        //this.jtable_hits = this.jtable.filter(this.get_filters());
        this.jtable_hits = this.data; //this.jtable.filter(function() {});

        if(this.count_st==0) {
                this.compute_counts();
//                this.update_counts();
                this.count_st++;
        }

  //      this.update_counts();

  //      this.sort_hits(this.actual_sort_index.row, this.actual_sort_order);
        this.sort_hits();

        this.update();
};

SavedProperty.prototype.set_init_filters_value = function() {
        this.FiltersInitValues[this.TypeFilterCheckBoxes.$checkall_li[0].firstChild.id] = this.TypeFilterCheckBoxes.$checkall_li[0].firstChild.checked;

        for (var i = 0; i < this.TypeFilterCheckBoxes.$checkboxes_li.length; i++)
        {
                this.FiltersInitValues[this.TypeFilterCheckBoxes.$checkboxes_li[i].firstChild.id] = this.TypeFilterCheckBoxes.$checkboxes_li[i].firstChild.checked;
        }
};

SavedProperty.prototype.update = function() {
        var that = this;

        if(0 && this.jtable_hits_sorted.length <= 0)
        {
                this.$data_loading_msg.hide();
                this.$data_empty_msg.show();
                this.$sort_controls_div.hide();
                this.$data_div.html("");
                $('#applied_filter_hosting_property').hide();
                $('#cb_group_type_filter li').find(':input').each(function(){
                                        var type_val = $(this).is(':checked');
                                        var type_input = $(this).attr('id');
                                         if((type_input == 'type_all') && (type_val == true)){
                                                 $('#applied_filter_hosting_property').hide();
                                                 temp =0;
                                                 return false;
                                          }else if(type_val == true){
                                                           $('#applied_filter_hosting_property').show();
                                                  return false;
                                                  }

                                });
        }
        else {
                var output = Mustache.to_html(this.template, this.jtable_hits_sorted);

//                this.$data_loading_msg.hide();
  //              this.$sort_controls_div.show();

                $('#favorite_properties').html(output);
return;
    //            this.$data_div.html(output);

                //Init jquery UI tabs
                $('ul.ui-tabs-nav').tabs();
                $('#cb_group_type_filter li').find(':input').each(function(){
                                        var type_val = $(this).is(':checked');
                                        var type_input = $(this).attr('id');
                                         if((type_input == 'type_all') && (type_val == true)){
                                                 $('#applied_filter_hosting_property').hide();
                                                 return false;
                                          }else if(type_val == true){
                                                   $('#applied_filter_hosting_property').show();
                                                  }
                                });

                //Map tab events
                that.tabs_map_binded = new Array();
                $('a[name=city_map_show_property]').click(function() {
                        prop_number = $(this).attr("rel");
                        that.changeMapProperty('property',prop_number);
                });

                this.display_extra_filters();

                $(".picture_number").each(function(index, value) {
                        index = index +1;
                        $(this).html(index);
                });

        }

        //update count
        this.FiltersCounts['city_results_count_current'] = this.jtable_hits_sorted.length;
        this.FiltersCounts['city_results_count_total']   = this.FiltersCounts['city_results_filtered'];
        this.FiltersCounts['city_results_count_total_temp']   = this.FiltersCounts['city_results_filtered_temp'];
        this.update_counts();


        if(this.FiltersCounts['city_results_count_current'] < this.FiltersCounts['city_results_count_total_temp'])
        {
                $('#show_more_results').show();
        }
        else
        {
                $('#show_more_results').hide();
        }

        this.cleanupDistrcitsAndLandmarks();

        this.initpaging();
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
/*
				$('#search_load').show();
				$('#city_results_count').show();
				$('#city_load').hide();
				$('#wrap').show();
*/
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
