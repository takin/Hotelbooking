/* @modified by sasha karpin
 * jQuery dropdown: A simple dropdown plugin
 *
 * Inspired by Bootstrap: http://twitter.github.com/bootstrap/javascript.html#dropdowns
 *
 * Copyright 2013 Cory LaViska for A Beautiful Site, LLC. (http://abeautifulsite.net/)
 *
 * Dual licensed under the MIT / GPL Version 2 licenses
 *
*/
if(jQuery) (function($) {
	
	$.extend($.fn, {
		dropdown: function(options) {
			if ( $(this).data('dropdown-saf') )
				return $(this).data('dropdown-saf') ;

			switch( options.method ) {
				case 'hide':
					hide();
					break;
				case 'attach':
					$(this).attr('data-dropdown', options.dropdown);
					break;
				case 'detach':
					hide();
					$(this).removeAttr('data-dropdown');
					break;
				case 'disable':
					$(this).addClass('dropdown-disabled');
					break;
				case 'enable':
					hide();
					$(this).removeClass('dropdown-disabled');
					break;
			}
			
            var ui = this;
			var dDown = $($(this).attr('data-dropdown'));
			
			var obj = {};
			obj.ui = this;
			obj.getActive = function(){
				activeDom = dDown.find('li[active]');
                if (activeDom.length) 
                    return activeDom;
                else
                    return dDown.find("li:eq(0)");
                
			}
			
			$(this).on('click.dropdown', show);
			
			if ( options.onchange)
				dDown.find('li').on('click.dropdownitem', function(evt){
					hide();
					el = $(this); 
					dDown.find('li').removeAttr('active');
					el.attr('active', 1);
					options.onchange(ui, el);
				});
			
			$(this).data('dropdown-saf', obj);
			
			return obj;			
			
		}
	});
	
	function show(event) {
		
		var trigger = $(this),
			dropdown = $(trigger.attr('data-dropdown')),
			isOpen = trigger.hasClass('dropdown-open');
		
		// In some cases we don't want to show it
		if( $(event.target).hasClass('dropdown-ignore') ) return;
		
		event.preventDefault();
		event.stopPropagation();
		hide();
		
		if( isOpen || trigger.hasClass('dropdown-disabled') ) return;
		
		// Show it
		trigger.addClass('dropdown-open');
		dropdown
			.data('dropdown-trigger', trigger)
			.show();
			
		// Position it
		position();
		
		// Trigger the show callback
		dropdown
			.trigger('show', {
				dropdown: dropdown,
				trigger: trigger
			});
		
	}
	
	function hide(event) {
		
		// In some cases we don't hide them
		var targetGroup = event ? $(event.target).parents().addBack() : null;
		
		// Are we clicking anywhere in a dropdown?
		if( targetGroup && targetGroup.is('.dropdown') ) {
			// Is it a dropdown menu?
			if( targetGroup.is('.dropdown-menu') ) {
				// Did we click on an option? If so close it.
				if( !targetGroup.is('A') ) return;
			} else {
				// Nope, it's a panel. Leave it open.
				return;
			}
		}
		
		// Hide any dropdown that may be showing
		$(document).find('.dropdown:visible').each( function() {
			var dropdown = $(this);
			dropdown
				.hide()
				.removeData('dropdown-trigger')
				.trigger('hide', { dropdown: dropdown });
		});
		
		// Remove all dropdown-open classes
		$(document).find('.dropdown-open').removeClass('dropdown-open');
		
	}
	
	function position() {
		
		var dropdown = $('.dropdown:visible').eq(0),
			trigger = dropdown.data('dropdown-trigger'),
			hOffset = trigger ? parseInt(trigger.attr('data-horizontal-offset') || 0, 10) : null,
			vOffset = trigger ? parseInt(trigger.attr('data-vertical-offset') || 0, 10) : null;

        hOffset = trigger.outerWidth()/2;
		hOffset = hOffset - dropdown.width()/2;
		
		if( dropdown.length === 0 || !trigger ) return;
		
		// Position the dropdown relative-to-parent...
		if( dropdown.hasClass('dropdown-relative') ) {
			dropdown.css({
				left: dropdown.hasClass('dropdown-anchor-right') ?
					trigger.position().left - (dropdown.outerWidth(true) - trigger.outerWidth(true)) - parseInt(trigger.css('margin-right')) + hOffset :
					trigger.position().left + parseInt(trigger.css('margin-left')) + hOffset,
				top: trigger.position().top + trigger.outerHeight(true) - parseInt(trigger.css('margin-top')) + vOffset
			});
		} else {
			// ...or relative to document
			dropdown.css({
				left: dropdown.hasClass('dropdown-anchor-right') ? 
					trigger.offset().left - (dropdown.outerWidth() - trigger.outerWidth()) + hOffset : trigger.offset().left + hOffset,
				top: trigger.offset().top + trigger.outerHeight() + vOffset
			});
		}
	}
	
	$(document).ready(function(){
		//$(document).on('click.dropdown', '[data-dropdown]', show);
		console.log($("#search-currency"));
		$(document).on('click.dropdown', hide);
		$(window).on('resize', position);
	})	
})(jQuery);
