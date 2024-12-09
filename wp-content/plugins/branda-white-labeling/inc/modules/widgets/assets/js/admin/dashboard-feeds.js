/**
 * Branda: Dashboard Feeds
 * http://wpmudev.com/
 *
 * Copyright (c) 2018-2019 Incsub
 * Licensed under the GPLv2 +  license.
 */
/* global window, SUI, ajaxurl */

/**
 * Add feed
 */
jQuery( window.document ).ready( function( $ ){
    "use strict";

    /**
     * Open add/edit modal
     */
    $('.branda-dashboard-feeds-add, .branda-dashboard-feeds-save').on( 'click', function() {
        var parent = $('.sui-box-body', $(this).closest( '.sui-box' ) );
        var error = false;
        var id = $(this).data('id');
        $( '[data-required=required]', parent ).each( function() {
			var local_parent = $(this).parent();
			local_parent.removeClass('sui-form-field-error');
			$('span', local_parent ).removeClass( 'sui-error-message' );
            if ( '' === $(this).val() ) {
                local_parent.addClass('sui-form-field-error');
                $('span', local_parent ).addClass( 'sui-error-message' );
                error = true;
            }
        });
        $( 'input[type=number]', parent ).each( function() {
			var min = $(this).prop( 'min' );
			var local_parent = $(this).parent();
			if ( 'undefined' !== typeof min ) {
				var val = parseInt( $(this).val() );
				$('.sui-error-message', local_parent ).remove();
				local_parent.removeClass( 'sui-form-field-error' );
				min = parseInt( min );
				if ( val < min ) {
					local_parent.addClass( 'sui-form-field-error' );
					local_parent.append( '<span class="sui-error-message">'+ub_admin.messages.form.number.min+'</span>' );
					error = true;
				}
			}
        });

        if ( error ) {
            return;
        }
        var data = {
            action: 'branda_dashboard_feed_save',
            _wpnonce: $(this).data('nonce'),
            id: id,
            link: $('#branda-general-link-' + id, parent).val(),
            url: $('#branda-general-url-' + id, parent).val(),
            title: $('#branda-general-title-' + id, parent).val(),
            items: $('#branda-display-items-' + id, parent).val(),
            show_summary: $('.branda-display-show_summary input[type=radio]:checked', parent).val(),
            show_date: $('.branda-display-show_date input[type=radio]:checked', parent).val(),
            show_author: $('.branda-display-show_author input[type=radio]:checked', parent).val(),
            site: $('.branda-visibility-site input[type=radio]:checked', parent).val(),
            network: $('.branda-visibility-network input[type=radio]:checked', parent).val(),
        };
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                $.each( response.data.fields, function( name, message ) {
                    var field = $( name, parent ).closest( '.sui-form-field' );
                    field.addClass( 'sui-form-field-error' );
                    $( 'span.hidden', field ).addClass( 'sui-error-message' ).html( message );
                });
            }
        });
    });

    /**
     * Delete feed
     */
    $('.branda-dashboard-feeds-delete').on( 'click', function() {
        if ( 'bulk' === $(this).data('id') ) {
            return;
        }
        var data = {
            action: 'branda_dashboard_feed_delete',
            _wpnonce: $(this).data('nonce'),
            id: $(this).data('id' )
        };
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                SUI.openFloatNotice( response.data.message );
            }
        });
    });
    /**
     * Bulk: confirm
     */
    $( '.branda-dashboard-feeds-delete[data-id=bulk]').on( 'click', function() {
        var data = {
            action: 'branda_dashboard_feed_delete_bulk',
            _wpnonce: $(this).data('nonce'),
            ids: [],
        }
        $('#branda-dashboard-feeds-panel .check-column :checked').each( function() {
            data.ids.push( $(this).val() );
        });
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                SUI.openFloatNotice( response.data.message );
            }
        });
    });
    /**
     * Try to fetch site name and feed
     */
    $( '.branda-dashboard-feeds-url button' ).on( 'click', function() {
        var $parent = $(this).closest( '.sui-tabs' );
        var $input = $('input', $parent );
        var $target = $( '.'+$input.data('target'), $parent );
        var field;
        var data = {
            action: 'branda_get_site_data',
            _wpnonce: $input.data('nonce'),
            id: $input.data('id'),
            url: $input.val(),
        }
        SUI.openInlineNotice( 'branda-feeds-info', ub_admin.messages.feeds.fetch, 'loading' );
        $( '.branda-list', $target ).html('').hide();
        $.post( ajaxurl, data, function( response ) {
            if (
                response.success &&
                'undefined' !== response.data
            ) {
                if ( 0 === response.data.length ) {
                    SUI.openInlineNotice( 'branda-feeds-info', ub_admin.messages.feeds.no, 'warning' );
                    return;
                }
                if ( 1 === response.data.length ) {
                    /**
                     * Title
                     */
                    field = $('.branda-general-title input', $parent );
                    if (
                        '' === field.val() &&
                        'undefined' !== response.data[0].title
                    ) {
                        field.val( response.data[0].title );
                    }
                    /**
                     * href
                     */
                    field = $('.branda-general-url input', $parent );
                    if (
                        '' === field.val() &&
                        'undefined' !== response.data[0].href
                    ) {
                        field.val( response.data[0].href );
                    }
                } else {
                    var row = wp.template( $input.data('tmpl') + '-row' );
                    var list = '';
                    $.each( response.data, function( index, value ) {
                        list += row( value );
                    });
                    $('.branda-list', $target ).html( list ).show();
                    $( 'label', $target ).on( 'click', function() {
                        /**
                         * Title
                         */
                        field = $('.branda-general-title input', $parent );
                        field.val( $('.branda-title', $(this) ).html() );
                        /**
                         * href
                         */
                        field = $('.branda-general-url input', $parent );
                        field.val( $('.branda-href', $(this) ).html() );
                    });
                }
                SUI.closeNotice( 'branda-feeds-info' );
            } else {
                SUI.openInlineNotice( 'branda-feeds-info', ub_admin.messages.feeds.no, 'warning' );
            }
        });
    });
});
