function SavedProperty() {
	this.request;

        this.jtable;
        this.jtable_hits;
        this.jtable_hits_sorted;
        this.results_limit;

        this.template;
};

SavedProperty.property.setup = function(data) {
	data = jQuery.parseJSON(data);

	var totalRecords = data.property_list.length;

	this.setRequestData(data.request);
	this.setData(data.property_list);

	this.setClickSort('data_sort_controls','sortname-tous','propertyName');
        this.setClickSort('data_sort_controls','sortprice-tous','display_price');
        this.setClickSort('data_sort_controls','sortcote-tous','overall_rating');

	$('#data_sort_controls').show();
	this.apply_filters();
	this.set_init_filters_value();
};

SavedProperty.prototype.setRequestData = function(json_request_data) {
        this.request = json_request_data;
};

SavedProperty.prototype.setData = function(json_data) {
        jOrder.logging = null;

	this.jtable = jOrder(json_data)
		.index('propertyNumber', ['propertyNumber'], { grouped: false, ordered: true, type: jOrder.number })
		.index('propertyType', ['propertyType'], { grouped: true , ordered: true, type: jOrder.string });

	this.FiltersCounts['city_results_count_total'] = json_data.length;
};

SavedProperty.prototype.setClickSort = function(divID, DOMNodeID, rowname) {
        var that = this;

        $('#'+DOMNodeID).click(function(){

                $('#'+divID+' .sorting').removeClass('activesort');
                $(this).addClass('activesort');

                if($(this).children().hasClass('asc'))
        {
                        $(this).children().removeClass('asc');
                        $(this).children().addClass('desc');
                        that.sort_hits(rowname,jOrder.desc,true);
        }
                else
                {
                        $(this).children().removeClass('desc');
                        $(this).children().addClass('asc');

                        that.sort_hits(rowname,jOrder.asc,true);
                }
                return false;
        });
};

SavedProperty.prototype.apply_filters = function() {
        this.$data_empty_msg.hide();
        this.$sort_controls_div.hide();
        this.$data_div.html("");
        this.$data_loading_msg.show();

        this.init_counts();

        this.jtable_hits = this.jtable.filter(this.get_filters());

        if(this.count_st==0) {
                this.compute_counts();
                this.update_counts();
                this.count_st++;
        }

        this.update_counts();

        this.sort_hits(this.actual_sort_index.row, this.actual_sort_order);

        this.update();
};

SavedProperty.prototype.set_init_filters_value = function() {
        this.FiltersInitValues[this.TypeFilterCheckBoxes.$checkall_li[0].firstChild.id] = this.TypeFilterCheckBoxes.$checkall_li[0].firstChild.checked;

        for (var i = 0; i < this.TypeFilterCheckBoxes.$checkboxes_li.length; i++)
        {
                this.FiltersInitValues[this.TypeFilterCheckBoxes.$checkboxes_li[i].firstChild.id] = this.TypeFilterCheckBoxes.$checkboxes_li[i].firstChild.checked;
        }

        for (var i = 0; i < this.FacilitiesFilterCheckBoxes.$checkboxes_li.length; i++)
        {
                this.FiltersInitValues[this.FacilitiesFilterCheckBoxes.$checkboxes_li[i].firstChild.id] = this.FacilitiesFilterCheckBoxes.$checkboxes_li[i].firstChild.checked;
        }
        for (var i = 0; i < this.DistrictsCheckBoxes.$checkboxes_li.length; i++)
        {
                this.FiltersInitValues[this.DistrictsCheckBoxes.$checkboxes_li[i].firstChild.id] = this.DistrictsCheckBoxes.$checkboxes_li[i].firstChild.checked;
        }
        for (var i = 0; i < this.LandmarksCheckBoxes.$checkboxes_li.length; i++)
        {
                this.FiltersInitValues[this.LandmarksCheckBoxes.$checkboxes_li[i].firstChild.id] = this.LandmarksCheckBoxes.$checkboxes_li[i].firstChild.checked;
        }

        this.FiltersInitValues['breakfast_2nd_filter'] = false;
        this.FiltersInitValues['downtown_2nd_filter'] = false;
};

SavedProperty.prototype.update = function() {
        var that = this;

        //Re initiatilize prop_number_to_focus of property map
        this.pweb_maps['property'].prop_number_to_focus = -1;

        if(this.jtable_hits_sorted.length <= 0)
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
        else
        {
                var output = Mustache.to_html(this.template, { "properties": this.jtable_hits_sorted});

                this.$data_loading_msg.hide();
                this.$sort_controls_div.show();

                this.$data_div.html(output);

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
       //Review tab events
        $('a[name=review_show_property]').click(function()
        {
                var prop_number = this.rel;

                $("#city_comments_"+prop_number).html('<p><img src="http://'+window.location.host+'/images/V2/loading-squares.gif" alt="" /></p>');
                $.ajax(
                        {
                                type:"POST",
                                url:"http://"+window.location.host+"/reviews_map/"+prop_number+"/2",
                                success:function(data)
                                                {
                                                        $("#city_comments_"+prop_number).html(data);
                                                }
                        });
        });

        $("a.prop_more_info").click(function (){
                var ID = $(this).attr('rel');
                $("#prop_more_info_wrap_"+ID).toggle();
                return false;
        });

        $("a.prop_more_info_close").click(function (){
                var ID = $(this).attr('rel');
                $("#prop_more_info_wrap_"+ID).toggle();
                return false;
        });

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
	}

	function loadSavedPropertiesPage() {
		$.ajax({
			type:"GET",
			url:availibility_url,
			success:function(data) {
				pweb_filter.setup(data);

				$('#search_load').show();
				$('#city_results_count').show();
				$('#city_load').hide();
				$('#wrap').show();

				$(".display_preview").fancybox({
					'titlePosition' : 'inside',
					'transitionIn'  : 'none',
					'transitionOut' : 'none'
				});

				$(".box_content").hover(
					function(){
						$(this).find('.quick_view_bg').slideDown(500);
					},function(){
						$(this).find('.quick_view_bg').slideUp(300);
					}
				);

				var cookie_value = getCookie('compare');
				var total_property =    cookie_value.split(",");
				var property_selected = total_property.length;

				if(property_selected != ''){
					for(i=0;i<property_selected;i++){
						$("#pro_compare_"+total_property[i]).attr('checked',true);
						$('#compare_count_'+total_property[i]).html(property_selected);
					}
				}
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
