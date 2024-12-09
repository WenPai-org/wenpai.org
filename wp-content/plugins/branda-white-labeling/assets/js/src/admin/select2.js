/**
 * Ultimate Branding
 * http://wpmudev.com/
 *
 * Copyright (c) 2017-2018 Incsub
 * Licensed under the GPLv2+ license.
 */
/* global wp, window, wp_media_post_id, jQuery, ajaxurl, ace, switch_button */

/**
 * close block
 */
/**
 * Simple Options: select2
 */
jQuery( window.document ).ready(function($){
    function brandaFormat(site) {
        if (site.loading) {
            return site.text;
        }
        var markup = "<div class='select2-result-site clearfix'>";
        markup += "<div class='select2-result-title'>" + site.title + "</div>";
        if ( 'undefined' !== typeof site.subtitle ) {
            markup += "<div class='select2-result-subtitle'>" + site.subtitle + "</div>";
        }
        markup += "</div>";
        return markup;
    }
    function brandaFormatSelection( site ) {
        return site.title || site.text;
    }
    if (jQuery.fn.SUIselect2) {
	var args = {
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function( params ) {
                    var query = {
                        user_id: $(this).data('user-id') || $('select', $(this)).data('user-id'),
                        _wpnonce: $(this).data('nonce') || $('select', $(this)).data('nonce'),
                        action: $(this).data('action') || $('select', $(this)).data('action'),
                        page: params.page,
                        q: params.term,
                        extra: ( 'function' === typeof window[$(this).data('extra')] )? window[$(this).data('extra')]() : ''
                    };
                    return query;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            escapeMarkup: function (markup) { return markup; },
            templateResult: brandaFormat,
            templateSelection: brandaFormatSelection,
            dropdownCssClass: 'sui-select-dropdown'
        };

	$('.sui-select-ajax').each( function() {
		// Change inputTooShort message.
		if ( "undefined" !== typeof $(this).data('input-too-short') ) {
			var inputTooShortFunction =  function ( args ) {
				return $(this).data('input-too-short');
			};
			args.language = { inputTooShort: inputTooShortFunction.bind(this) };
		}

		// Change dropdownParent.
		if ( "undefined" !== typeof $(this).data('dropdown-parent-class') ) {
			args.dropdownParent = $( '.' + $(this).data('dropdown-parent-class') );
		}

		$(this).SUIselect2(args);
	});
    }
});
