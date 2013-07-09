jQuery(document).ready(function() {
    handleShareEmailPDF();

    handleTooltip();

    jQuery("img").error(function() {
        jQuery(this).hide();
    });
});
jQuery(function()
{

    jQuery('.select-translate').change(function() {

        var api = jQuery(this).data('jsp');

        var version = jQuery(this).val();
        if (version == 'translate') {
            jQuery(this).parent().find(".original").fadeOut(500, function() {
                jQuery(this).parent().find(".translated").fadeIn(500);
            });
        }
        if (version == 'original') {
            jQuery(this).parent().find(".translated").fadeOut(500, function() {
                $(this).parent().find(".original").fadeIn(500);
            });
        }
    });
    //To show full search if user has no search cookies
    var city_selected = getCookie('city_selected');

    // Don't show back to result in all cases
    $('#back_to_results').hide();

    if (city_selected == '')
    {
        $('a.modify_search').removeClass('expand collapse').addClass('expand');
        // show everything in side search box 
        // except modify search and back to result
        // because this user land page
        $('#modify_search').show();

        $('#search_now').hide();
        $('#side_search_wrap_city').show();
        $('#side_search_wrap').show();
    }
    else
    {
        $('a.modify_search').removeClass('expand collapse').addClass('collapse');
        // hide everything in side search box 
        // except header
        $('#modify_search').show();

        $('#search_now').hide();
        $('#side_search_wrap_city').hide();
        $('#side_search_wrap').hide();
    }

    if ($("input[type='hidden'][name='switch_api']").val() == true) {

        $('a.modify_search').removeClass('expand collapse').addClass('expand');
        // show everything in side search box 
        // except modify search and back to result
        // because this user land page
        $('#modify_search').show();

        $('#search_now').hide();
        $('#side_search_wrap_city').show();
        $('#side_search_wrap').show();

    }

});

function handleShareEmailPDF() {
	var blinkTimeout = null

	var send = false;

	var validFields = {
		to_email   : false,
		subject    : false,
		message    : false,
		from_name  : false,
		from_email : false
	};

	var share_overlay = $('#share-overlay');

	addValidation();

	jQuery('.share-pdf, #share-email, #show-share-overlay').click(function(event) {
		event.preventDefault();

		var id = $(this).attr('id');

		// show or not the pdf pic
		if (id == 'share-pdf' || $(this).hasClass('prevPDF') || $(this).hasClass('share-pdf')) {
			$('#email_show_pdf').show();
			$('#email_send_pdf').val('1');

			$('#show-share-overlay').addClass('prevPDF');

			$('#share_email_form .title, #submit_email').hide();
			$('#share_email_form .title_pdf, #submit_pdf').show();
		}
		else {
			$('#email_show_pdf').hide();
			$('#email_send_pdf').val('0');
			$('#show-share-overlay').removeClass('prevPDF');

			$('#share_email_form .title, #submit_email').show();
			$('#share_email_form .title_pdf, #submit_pdf').hide();
		}


		if (id == 'show-share-overlay') {
			clearValues(true);
		}
		else {
			clearValues();
		}

		$('#share_email_form').show();
		$('#share-overlay .confirmation').hide();

		share_overlay.css('display', 'block');

		validFields = {
			to_email   : false,
			subject    : false,
			message    : false,
			from_name  : false,
			from_email : false
		};
	});

	jQuery('#close-share-overlay, #close_x a').click(function(event) {
		event.preventDefault();

		removeValidation();

		clearValues();
		validFields = {
			to_email   : false,
			subject    : false,
			message    : false,
			from_name  : false,
			from_email : false
		};

		share_overlay.css('display', 'none');
	});

	jQuery('#close-share-conformation_overlay').click(function(event) {
		event.preventDefault();

		clearValues();
		validFields = {
			to_email   : false,
			subject    : false,
			message    : false,
			from_name  : false,
			from_email : false
		};

		share_overlay.css('display', 'none');
	});

	jQuery('#share_email_form').submit(function(event) {
		event.preventDefault();
	});

	function trySubmit(field) {
		if (send) {
			return;
		}

		validFields[field] = true;

		var ok = true;
		for (var field_name in validFields) {
			if (validFields[field_name] == false) {
				ok = false;
			}
		}

		if (ok) {
			$('#submit_email, #submit_pdf, #close-share-overlay').hide();
			$('#share_please_wait').show();

			blinkTimeout = setInterval(function() {
				if ($('#share_please_wait').css('visibility') == 'hidden') {
					$('#share_please_wait').css('visibility', 'visible');
				}
				else {
					$('#share_please_wait').css('visibility', 'hidden');
				}
			}, 500);

			send = true;

			var to_email   = $('#to_email').val();
			var subject    = $('#subject').val();
			var message    = $('#message').val();
			var from_name  = $('#from_name').val();
			var from_email = $('#from_email').val();
			var subscribe  = $('#subscribe').val();
			var with_pdf   = $('#email_send_pdf').val();

			var date = $("#book-pick").datepicker( "getDate" );

			$.ajax({
				type     : "POST",
				url      : $('#share_email_form').attr('action'),
				data     : {
					to_email   : to_email,
					subject    : subject,
					message    : message,
					from_name  : from_name,
					from_email : from_email,
					subscribe  : subscribe,
					with_pdf   : with_pdf,
					property_type   : $('#property_type').val(),
					property_name   : $('#property_name').val(),
					property_number : $('#property_number').val(),
					date            : siteDateString(date) || '',
					nights          : parseInt($('#book-night').val(), 10) || ''
				},
				success  : function(response) {
					if (with_pdf) {
						$('#submit_pdf, #close-share-overlay').show();
					}
					else {		
						$('#submit_email, #close-share-overlay').show();
					}

					clearTimeout(blinkTimeout);
					$('#share_please_wait').hide();

					send = false;

					var go_next = false;

					if (typeof(response) != 'undefined') {
						if (response.ok) {
							go_next = true;
						}
					}
					else {
						go_next = false;
					}

					if (go_next) {
						$('#share_email_form, #email_show_pdf').hide();
						$('#share-overlay .confirmation').show();

						$('#email_recipient').text(to_email || '');
						$('#from_feedback').text(from_email || '');
						$('#to_feedback').text(to_email || '');
						$('#subject_feedback').text(subject || '');
						$('#message_feedback').text(message || '');
					}
					else {
						$('#share_email_form, #email_show_pdf').show();
						$('#share-overlay .confirmation').hide();
					}
				},
				dataType : 'json'
			});
		}
	}

	function addValidation() {
		var to_email = new LiveValidation('to_email', {
			onlyOnSubmit : true,
			validMessage : " ",
			failureMessage: '  ',
			onValid      : function() {
				trySubmit('to_email');
			}
		});
		to_email.add(Validate.Email, {failureMessage: $('#invalidEmailError').html()});
		to_email.add(Validate.Presence, {failureMessage: $('#missingEmailError').html()});

		var subject = new LiveValidation('subject', {
			onlyOnSubmit : true,
			validMessage : " ",
			failureMessage: '  ',
			onValid      : function() {
				trySubmit('subject');
			}
		});
		subject.add(Validate.Presence, {failureMessage: $('#missingSubjectError').html()});

		var message = new LiveValidation('message', {
			onlyOnSubmit : true,
			validMessage : " ",
			failureMessage: '  ',
			onValid      : function() {
				trySubmit('message');
			}
		});
		message.add(Validate.Presence, {failureMessage: $('#missingMessageError').html()});

		var from_name = new LiveValidation('from_name', {
			onlyOnSubmit : true,
			validMessage : " ",
			failureMessage: '  ',
			onValid      : function() {
				trySubmit('from_name');
			}
		});
		from_name.add(Validate.Presence, {failureMessage: $('#missingNameError').html()});

		var from_email = new LiveValidation('from_email', {
			onlyOnSubmit : true,
			validMessage : " ",
			failureMessage: '  ',
			onValid      : function() {
				trySubmit('from_email');
			}
		});
		from_email.add(Validate.Email, {failureMessage: $('#invalidEmailError').html()});
		from_email.add(Validate.Presence, {failureMessage: $('#missingEmailError').html()});
	}

	function clearValues(to_fields) {
		if (typeof(to_fields) == 'undefined' || !to_fields) {
			$('#subject').val('');
			$('#message').val('');
			$('#from_name').val('');
			$('#from_email').val('');
		}

		$('#to_email').val('');
	}

	function removeValidation() {
		var to_email = new LiveValidation('to_email', { validMessage: " " });
		to_email.destroy();

		var subject = new LiveValidation('subject', { validMessage: " " });
		subject.destroy();

		var message = new LiveValidation('message', { validMessage: " " });
		message.destroy();

		var from_name = new LiveValidation('from_name', { validMessage: " " });
		from_name.destroy();

		var from_email = new LiveValidation('from_email', { validMessage: " " });
		from_email.destroy();
	}

    var blinkTimeout = null

    var send = false;

    var validFields = {
        to_email: false,
        subject: false,
        message: false,
        from_name: false,
        from_email: false
    };

    var share_overlay = $('#share-overlay');

    addValidation();

    jQuery('.share-pdf, #share-email, #show-share-overlay').click(function(event) {
        event.preventDefault();

        var id = $(this).attr('id');

        // show or not the pdf pic
        if (id == 'share-pdf' || $(this).hasClass('prevPDF') || $(this).hasClass('share-pdf')) {
            $('#email_show_pdf').show();
            $('#email_send_pdf').val('1');

            $('#show-share-overlay').addClass('prevPDF');

            $('#share_email_form .title, #submit_email').hide();
            $('#share_email_form .title_pdf, #submit_pdf').show();
        }
        else {
            $('#email_show_pdf').hide();
            $('#email_send_pdf').val('0');
            $('#show-share-overlay').removeClass('prevPDF');

            $('#share_email_form .title, #submit_email').show();
            $('#share_email_form .title_pdf, #submit_pdf').hide();
        }


        if (id == 'show-share-overlay') {
            clearValues(true);
        }
        else {
            clearValues();
        }

        $('#share_email_form').show();
        $('#share-overlay .confirmation').hide();

        share_overlay.css('display', 'block');

        validFields = {
            to_email: false,
            subject: false,
            message: false,
            from_name: false,
            from_email: false
        };
    });

    jQuery('#close-share-overlay, #close_x a').click(function(event) {
        event.preventDefault();

        removeValidation();

        clearValues();
        validFields = {
            to_email: false,
            subject: false,
            message: false,
            from_name: false,
            from_email: false
        };

        share_overlay.css('display', 'none');
    });

    jQuery('#close-share-conformation_overlay').click(function(event) {
        event.preventDefault();

        clearValues();
        validFields = {
            to_email: false,
            subject: false,
            message: false,
            from_name: false,
            from_email: false
        };

        share_overlay.css('display', 'none');
    });

    jQuery('#share_email_form').submit(function(event) {
        event.preventDefault();
    });

    function trySubmit(field) {
        if (send) {
            return;
        }

        validFields[field] = true;

        var ok = true;
        for (var field_name in validFields) {
            if (validFields[field_name] == false) {
                ok = false;
            }
        }

        if (ok) {
            $('#submit_email, #submit_pdf, #close-share-overlay').hide();
            $('#share_please_wait').show();

            blinkTimeout = setInterval(function() {
                if ($('#share_please_wait').css('visibility') == 'hidden') {
                    $('#share_please_wait').css('visibility', 'visible');
                }
                else {
                    $('#share_please_wait').css('visibility', 'hidden');
                }
            }, 500);

            send = true;

            var to_email = $('#to_email').val();
            var subject = $('#subject').val();
            var message = $('#message').val();
            var from_name = $('#from_name').val();
            var from_email = $('#from_email').val();
            var subscribe = $('#subscribe').val();
            var with_pdf = $('#email_send_pdf').val();

            var date = $("#book-pick").length ? $("#book-pick").datepicker("getDate") : null;

            $.ajax({
                type: "POST",
                url: $('#share_email_form').attr('action'),
                data: {
                    to_email: to_email,
                    subject: subject,
                    message: message,
                    from_name: from_name,
                    from_email: from_email,
                    subscribe: subscribe,
                    with_pdf: with_pdf,
                    property_type: $('#property_type').val(),
                    property_name: $('#property_name').val(),
                    property_number: $('#property_number').val(),
                    date: date ? siteDateString(date) || '' : '',
                    nights: parseInt($('#book-night').val(), 10) || ''
                },
                success: function(response) {
                    if (with_pdf) {
                        $('#submit_pdf, #close-share-overlay').show();
                    }
                    else {
                        $('#submit_email, #close-share-overlay').show();
                    }

                    clearTimeout(blinkTimeout);
                    $('#share_please_wait').hide();

                    send = false;

                    var go_next = false;

                    if (typeof(response) != 'undefined') {
                        if (response.ok) {
                            go_next = true;
                        }
                    }
                    else {
                        go_next = false;
                    }

                    if (go_next) {
                        $('#share_email_form, #email_show_pdf').hide();
                        $('#share-overlay .confirmation').show();

                        $('#email_recipient').text(to_email || '');
                        $('#from_feedback').text(from_email || '');
                        $('#to_feedback').text(to_email || '');
                        $('#subject_feedback').text(subject || '');
                        $('#message_feedback').text(message || '');
                    }
                    else {
                        $('#share_email_form, #email_show_pdf').show();
                        $('#share-overlay .confirmation').hide();
                    }
                },
                dataType: 'json'
            });
        }
    }

    function addValidation() {
        var to_email = new LiveValidation('to_email', {
            onlyOnSubmit: true,
            validMessage: " ",
            failureMessage: '  ',
            onValid: function() {
                trySubmit('to_email');
            }
        });
        to_email.add(Validate.Email, {failureMessage: '  '});
        to_email.add(Validate.Presence, {failureMessage: '  '});

        var subject = new LiveValidation('subject', {
            onlyOnSubmit: true,
            validMessage: " ",
            failureMessage: '  ',
            onValid: function() {
                trySubmit('subject');
            }
        });
        subject.add(Validate.Presence, {failureMessage: '  '});

        var message = new LiveValidation('message', {
            onlyOnSubmit: true,
            validMessage: " ",
            failureMessage: '  ',
            onValid: function() {
                trySubmit('message');
            }
        });
        message.add(Validate.Presence, {failureMessage: '  '});

        var from_name = new LiveValidation('from_name', {
            onlyOnSubmit: true,
            validMessage: " ",
            failureMessage: '  ',
            onValid: function() {
                trySubmit('from_name');
            }
        });
        from_name.add(Validate.Presence, {failureMessage: '  '});

        var from_email = new LiveValidation('from_email', {
            onlyOnSubmit: true,
            validMessage: " ",
            failureMessage: '  ',
            onValid: function() {
                trySubmit('from_email');
            }
        });
        from_email.add(Validate.Email, {failureMessage: '  '});
        from_email.add(Validate.Presence, {failureMessage: '  '});
    }

    function clearValues(to_fields) {
        if (typeof(to_fields) == 'undefined' || !to_fields) {
            $('#subject').val('');
            $('#message').val('');
            $('#from_name').val('');
            $('#from_email').val('');
        }

        $('#to_email').val('');
    }

    function removeValidation() {
        var to_email = new LiveValidation('to_email', {validMessage: " "});
        to_email.destroy();

        var subject = new LiveValidation('subject', {validMessage: " "});
        subject.destroy();

        var message = new LiveValidation('message', {validMessage: " "});
        message.destroy();

        var from_name = new LiveValidation('from_name', {validMessage: " "});
        from_name.destroy();

        var from_email = new LiveValidation('from_email', {validMessage: " "});
        from_email.destroy();
    }
}

function handleTooltip() {
   $('#check_in_out').cluetip({
        width: '600px', 
        local:true, 
        cursor: 'pointer',
        arrows: false,
        dropShadow: false,
        sticky: false,
        positionBy: 'auto',
        cluetipClass: 'mcweb',
        tracking: true,
	splitTitle: '|',
	onShow: function(ct, ci) {
		ci.css('color', '#3087C9');
	}
    });
}
