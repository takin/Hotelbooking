/**
 * jQuery JSON plugin 2.4.0
 *
 * @author Brantley Harris, 2009-2011
 * @author Timo Tijhof, 2011-2012
 * @source This plugin is heavily influenced by MochiKit's serializeJSON, which is
 *         copyrighted 2005 by Bob Ippolito.
 * @source Brantley Harris wrote this plugin. It is based somewhat on the JSON.org
 *         website's http://www.json.org/json2.js, which proclaims:
 *         "NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.", a sentiment that
 *         I uphold.
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
(function ($) {
    'use strict';

    var escape = /["\\\x00-\x1f\x7f-\x9f]/g,
    meta = {
        '\b': '\\b',
        '\t': '\\t',
        '\n': '\\n',
        '\f': '\\f',
        '\r': '\\r',
        '"' : '\\"',
        '\\': '\\\\'
    },
    hasOwn = Object.prototype.hasOwnProperty;

    /**
     * jQuery.toJSON
     * Converts the given argument into a JSON representation.
     *
     * @param o {Mixed} The json-serializable *thing* to be converted
     *
     * If an object has a toJSON prototype, that will be used to get the representation.
     * Non-integer/string keys are skipped in the object, as are keys that point to a
     * function.
     *
     */
    $.toJSON = typeof JSON === 'object' && JSON.stringify ? JSON.stringify : function (o) {
        if (o === null) {
            return 'null';
        }

        var pairs, k, name, val,
        type = $.type(o);

        if (type === 'undefined') {
            return undefined;
        }

        // Also covers instantiated Number and Boolean objects,
        // which are typeof 'object' but thanks to $.type, we
        // catch them here. I don't know whether it is right
        // or wrong that instantiated primitives are not
        // exported to JSON as an {"object":..}.
        // We choose this path because that's what the browsers did.
        if (type === 'number' || type === 'boolean') {
            return String(o);
        }
        if (type === 'string') {
            return $.quoteString(o);
        }
        if (typeof o.toJSON === 'function') {
            return $.toJSON(o.toJSON());
        }
        if (type === 'date') {
            var month = o.getUTCMonth() + 1,
            day = o.getUTCDate(),
            year = o.getUTCFullYear(),
            hours = o.getUTCHours(),
            minutes = o.getUTCMinutes(),
            seconds = o.getUTCSeconds(),
            milli = o.getUTCMilliseconds();

            if (month < 10) {
                month = '0' + month;
            }
            if (day < 10) {
                day = '0' + day;
            }
            if (hours < 10) {
                hours = '0' + hours;
            }
            if (minutes < 10) {
                minutes = '0' + minutes;
            }
            if (seconds < 10) {
                seconds = '0' + seconds;
            }
            if (milli < 100) {
                milli = '0' + milli;
            }
            if (milli < 10) {
                milli = '0' + milli;
            }
            return '"' + year + '-' + month + '-' + day + 'T' +
            hours + ':' + minutes + ':' + seconds +
            '.' + milli + 'Z"';
        }

        pairs = [];

        if ($.isArray(o)) {
            for (k = 0; k < o.length; k++) {
                pairs.push($.toJSON(o[k]) || 'null');
            }
            return '[' + pairs.join(',') + ']';
        }

        // Any other object (plain object, RegExp, ..)
        // Need to do typeof instead of $.type, because we also
        // want to catch non-plain objects.
        if (typeof o === 'object') {
            for (k in o) {
                // Only include own properties,
                // Filter out inherited prototypes
                if (hasOwn.call(o, k)) {
                    // Keys must be numerical or string. Skip others
                    type = typeof k;
                    if (type === 'number') {
                        name = '"' + k + '"';
                    } else if (type === 'string') {
                        name = $.quoteString(k);
                    } else {
                        continue;
                    }
                    type = typeof o[k];

                    // Invalid values like these return undefined
                    // from toJSON, however those object members
                    // shouldn't be included in the JSON string at all.
                    if (type !== 'function' && type !== 'undefined') {
                        val = $.toJSON(o[k]);
                        pairs.push(name + ':' + val);
                    }
                }
            }
            return '{' + pairs.join(',') + '}';
        }
    };

    /**
     * jQuery.evalJSON
     * Evaluates a given json string.
     *
     * @param str {String}
     */
    $.evalJSON = typeof JSON === 'object' && JSON.parse ? JSON.parse : function (str) {
        /*jshint evil: true */
        return eval('(' + str + ')');
    };

    /**
     * jQuery.secureEvalJSON
     * Evals JSON in a way that is *more* secure.
     *
     * @param str {String}
     */
    $.secureEvalJSON = typeof JSON === 'object' && JSON.parse ? JSON.parse : function (str) {
        var filtered =
        str
        .replace(/\\["\\\/bfnrtu]/g, '@')
        .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
        .replace(/(?:^|:|,)(?:\s*\[)+/g, '');

        if (/^[\],:{}\s]*$/.test(filtered)) {
            /*jshint evil: true */
            return eval('(' + str + ')');
        }
        throw new SyntaxError('Error parsing JSON, source is not valid.');
    };

    /**
     * jQuery.quoteString
     * Returns a string-repr of a string, escaping quotes intelligently.
     * Mostly a support function for toJSON.
     * Examples:
     * >>> jQuery.quoteString('apple')
     * "apple"
     *
     * >>> jQuery.quoteString('"Where are we going?", she asked.')
     * "\"Where are we going?\", she asked."
     */
    $.quoteString = function (str) {
        if (str.match(escape)) {
            return '"' + str.replace(escape, function (a) {
                var c = meta[a];
                if (typeof c === 'string') {
                    return c;
                }
                c = a.charCodeAt();
                return '\\u00' + Math.floor(c / 16).toString(16) + (c % 16).toString(16);
            }) + '"';
        }
        return '"' + str + '"';
    };

}(jQuery));


$(document).ready(function(){
    
    var selectableItems = {};
    
    $("#booking-table select").change(function () {
        
        var selectbox = $(this);
        var theid = selectbox.attr('id');
        var nbsel = selectbox.val();
        var nbtxt = selectbox.find("option:selected").text();
        
        // remember the value
        selectableItems[ theid ] = nbsel;
        
        var theclass = selectbox.attr('class')+"_";
        var nbroom = selectbox.attr('id').replace(theclass, '');
        
        var current_totals = 0;
                
        $( "td.pnbguest strong" ).each(function() {
            if($(this).is(':visible')) {
                current_totals += parseInt($(this).html());
            }
        });
        
        $( "td.snbguest strong" ).each(function() {
            if($(this).is(':visible')) {
                current_totals += parseInt($(this).html());
            }
        });
        
        if((current_totals+ parseInt(nbsel)) <= parseInt($("input[type='hidden'][name='maximum_guests']").val())) {
                
            if (theclass == 'privatesel_'){
            
                $("#pnbguest_" + nbroom).children().text(nbsel);
            
                if (nbsel != 0){
			
                    $("#psubtotal_calc_" + nbroom).calc(
                        "qty * price",
					
                        {
                            qty: nbsel,
                            price:$("#psubtotal_init_" + nbroom)
                        },
					
                        function (s){					
                            return s.toFixed(2);
                        },
					
                        function ($this){
                            var sum = $this.sum();
                            $("#bigTotal").text($(".calc_sum").sum().toFixed(2));			
                            $("#depositTotal").text((parseFloat($("#bigTotal").text()) * parseFloat($("input[type='hidden'][name='deposit_percent']").val())).toFixed(2));
                            $("span#totaldeposit_email").text((parseFloat($("#bigTotal").text()) * parseFloat($("input[type='hidden'][name='deposit_percent']").val())).toFixed(2));
                            $("span#totalarrival_email").text(parseFloat($(".calc_sum").sum()).toFixed(2));
                        }
                        );
				
				
                    $("#proomnb_" + nbroom).show();	
                
                }else{
                    $("#proomnb_" + nbroom).hide();
                    $("#psubtotal_calc_" + nbroom).text(0.00);
                    $("#bigTotal").text($(".calc_sum").sum().toFixed(2));		
                    $("#depositTotal").text((parseFloat($("#bigTotal").text()) / 10).toFixed(2));
                }
            
                $( "td#pnbrooms_"+nbroom+" strong" ).html( Math.ceil( nbsel / parseInt($( "td#pnbroom_"+nbroom+"" ).attr('roombeds')) ) );
            
                $('table tr[class="preservation preservation_'+nbroom+'"] td:nth-child(4) span').each(function () {
                    $(this).html(nbsel);
                });
            
                $('table tr[class="preservation preservation_'+nbroom+'"] td:nth-child(5) span').each(function () {
                    $(this).html( (parseFloat($(this).parent().prev().prev().find("span").html()) * nbsel).toFixed(2) );
                });
            
                $('table tr[class="preservation preservation_'+nbroom+'"]').each(function () {
                    $(this).show();
                });
                
                if (selectbox.find("option:selected").attr('selection_title') !== undefined) {
            
                    $( "td#pnbrooms_"+nbroom+" a" ).attr('title', selectbox.find("option:selected").attr('selection_title'));
            
                    $("td#pnbrooms_"+nbroom+" a").cluetip({
                        splitTitle: '|', 
                        local:true, 
                        cursor: 'pointer',
                        arrows: false,
                        dropShadow: false,
                        sticky: false,
                        positionBy: 'bottomTop',
                        cluetipClass: 'mcweb',
                        topOffset: 10
                    });
    
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
                            $("#depositTotal").text((parseFloat($("#bigTotal").text()) * parseFloat($("input[type='hidden'][name='deposit_percent']").val())).toFixed(2));
                            $("span#totaldeposit_email").text((parseFloat($("#bigTotal").text()) * parseFloat($("input[type='hidden'][name='deposit_percent']").val())).toFixed(2));
                            $("span#totalarrival_email").text(parseFloat($(".calc_sum").sum()).toFixed(2));
                        }
                        );
				
                    $("#sroomnb_" + nbroom).show();	
                
                }else{
                    $("#sroomnb_" + nbroom).hide();
                    $("#ssubtotal_calc_" + nbroom).text(0.00);
                    $("#bigTotal").text($(".calc_sum").sum().toFixed(2));	
                    $("#depositTotal").text((parseFloat($("#bigTotal").text()) / 10).toFixed(2));
                }
            
                $( "td#snbrooms_"+nbroom+" strong" ).html( Math.ceil( nbsel / parseInt($( "td#snbroom_"+nbroom+"" ).attr('roombeds')) ) );
            
                $('table tr[class="sreservation sreservation_'+nbroom+'"] td:nth-child(4) span').each(function () {
                    $(this).html(nbsel);
                });
            
                $('table tr[class="sreservation sreservation_'+nbroom+'"] td:nth-child(5) span').each(function () {
                    $(this).html( (parseFloat($(this).parent().prev().prev().find("span").html()) * nbsel).toFixed(2) );
                });
            
                $('table tr[class="sreservation sreservation_'+nbroom+'"]').each(function () {
                    $(this).show();
                });
                
                        
                if (selectbox.find("option:selected").attr('selection_title') !== undefined) {
                        
                    $( "td#snbrooms_"+nbroom+" a" ).attr('title', selectbox.find("option:selected").attr('selection_title'));
            
                    $("td#snbrooms_"+nbroom+" a").cluetip({
                        splitTitle: '|', 
                        local:true, 
                        cursor: 'pointer',
                        arrows: false,
                        dropShadow: false,
                        sticky: false,
                        positionBy: 'bottomTop',
                        cluetipClass: 'mcweb',
                        topOffset: 10
                    });
    
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
                $('div.confirmationEmail').show();
                        
            }else{
                $("#selection").hide();
                $('table#sharedemailreservationView').hide();
                $('table#privateemailreservationView').hide();
                $('div.confirmationEmail').hide();
            }
        
            var total_guests = 0;
                
            $( "td.pnbguest strong" ).each(function() {
                if($(this).is(':visible')) {
                    total_guests += parseInt($(this).html());
                }
            });
        
            $( "td.snbguest strong" ).each(function() {
                if($(this).is(':visible')) {
                    total_guests += parseInt($(this).html());
                }
            });
        
            $( "td.total_people strong" ).html(total_guests);
        
            $().toastmessage({
                text     : '',
                sticky   : false,
                position : 'middle-center',
                type     : 'showNoticeToast',
                close    : function () {
                    console.log("toast is closed ...");
                }
            });
        
                
            $().toastmessage('showToast', {
                text     : $("input[type='hidden'][name='total_guests']").val()+': <strong>'+total_guests+'</strong>',
                sticky   : false,
                type     : 'notice'
            });
        
            if( total_guests > 0 ) {
                $('table#reservationView').show();
            } else {
                $('table#reservationView').hide();
            }
        
        } else {
            
            $(this).val($(this).find('option:first').val());
            
            $().toastmessage('showToast', {
                text     : $("input[type='hidden'][name='maximum_guests_message']").val(),
                position : 'middle-center',
                stayTime:10000,
                sticky   : false,
                type     : 'notice'
            });
            
            if (theclass == 'privatesel_'){
                $("#proomnb_" + nbroom).hide();
                $("#psubtotal_calc_" + nbroom).text(0.00);
            }
            
            if (theclass == 'sharedsel_'){
                $("#sroomnb_" + nbroom).hide();
                $("#ssubtotal_calc_" + nbroom).text(0.00);
            }
            
            
        }
        
        // save the cookie
        mySetCookie('bookingTableSelect', $.toJSON(selectableItems), 1);
        
    })

    // load the cookie for PDF
    var queryString = myGetUrlVars();

    if (typeof(queryString['print']) != 'undefined') {
        var bookingTableSelectStr = myGetCookie('bookingTableSelect');
        if (bookingTableSelectStr) {
            // parse the cookie
            var bookingTableSelect = jQuery.parseJSON(bookingTableSelectStr);

            if (typeof(bookingTableSelect) == 'object' && bookingTableSelect != undefined) {
                // identify each field and preload the value
                for (var fieldId in bookingTableSelect) {
                    var obj = $('#' + fieldId);

                    if (obj.length) {
                        obj.val(bookingTableSelect[ fieldId ]);
                        obj.trigger('change');
                    }
                    else {
                    }
                }
            }
            else {
                if (typeof(bookingTableSelect) == 'string') {
                    bookingTableSelect = bookingTableSelect.replace(/^{/, '').replace(/}$/, '');

                    var itemList = bookingTableSelect.split(',');
                    for (var i = 0; i < itemList.length; i++) {
                        var keyVal = itemList[i].split(':');

                        var obj = $('#' + keyVal[0].replace(/"/g, ''));

                        if (obj.length) {
                            obj.val(keyVal[1].replace(/"/g, ''));
                            obj.trigger('change');
                        }
                    }
                }
            }
        }
    }
});




// set and get cookies
function mySetCookie(c_name, value, exdays) {
    var exdate = new Date();

    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());

    document.cookie = c_name + "=" + c_value + ';path=/';
}

function myGetCookie(c_name) {
    var i, x, y, ARRcookies = document.cookie.split(";");

    for (i = 0; i < ARRcookies.length; i++) {
        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
        x = x.replace(/^\s+|\s+$/g,"");

        if (x == c_name) {
            return unescape(y);
        }
    }
}

function myGetUrlVars() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }

    return vars;
}
