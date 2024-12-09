/**
 * Branda
 * http://wpmudev.com/
 *
 * Copyright (c) 2018 Incsub
 * Licensed under the GPLv2+ license.
 */
/* global window, jQuery, SUI, ajaxurl */

jQuery( window.document ).ready(function($){
    /**
     * Show Welcome
     */
    function branda_show_welcome() {
        if (
            'undefined' === typeof SUI ||
            ! $( '#branda-welcome' ).length
        ) {
            window.setTimeout( branda_show_welcome, 100 );
        } else {
            SUI.openModal( 'branda-welcome', this, null, true );
            $('button.branda-welcome-all-modules').on( 'click', function () {
                var dialog = $(this).closest( '.sui-modal');
                var data = {
                    action: 'branda_welcome_get_modules',
                    nonce: $(this).data('nonce')
                };
                jQuery.post(ajaxurl, data, function( response ) {
                    if ( response.success ) {
                        dialog
                            .removeClass( 'sui-modal-sm' ).addClass( 'sui-modal-xl' )
                            .removeClass( 'branda-welcome-step1' ).addClass( 'branda-welcome-step2' )
                        ;
                        $('.sui-box-body', dialog ).removeClass( 'sui-content-center' );
                        $('.sui-box-title', dialog ).html( response.data.title );
                        $('.sui-box-body p', dialog )
                            .html( response.data.description )
                            .after( response.data.content )
                        ;
                        window.branda_modules_mark_all( '.branda-group-checkbox', dialog );
                        window.branda_modules_save_bulk( '.branda-welcome-activate', dialog, true );
                    } else if ( undefined !== typeof response.data.message ) {
                        SUI.openFloatNotice( response.data.message );
                    }
                });
            });
        }
    }
    branda_show_welcome();
    /**
     * Search modules on dashboard
     */
    $('#branda-dashboard-widget-summary-search')
        .on( 'change keydown keyup blur reset copy paste cut input', function() {
            var search = $(this).val();
            var target = $('#branda-dashboard-widget-modules');
            var re;
            if ( '' === search ) {
                $('tr, .sui-box', target ).show();
                $('#branda-dashboard-search-no-results').hide();
                return;
            }
            re = new RegExp( search, 'i' );
            $('td.sui-table--name', target).each( function() {
                var value = $(this).html();
                if ( value.match( re ) ) {
                    $(this).parent().show();
                    $(this).closest('.sui-box').show();
                } else {
                    $(this).parent().hide();
                }
            });
            $('table', target).each( function() {
                if ( 1 > $('tbody tr:visible', $(this)).length ) {
                    $(this).closest('.sui-box').hide();
                } else {
                    $(this).closest('.sui-box').show();
                }
            });
            if ( 1 > $('table:visible', target).length ) {
                $('#branda-dashboard-search-no-results').show();
                $('#branda-dashboard-search-no-results span').html( search );
            } else {
                $('#branda-dashboard-search-no-results').hide();
            }
        });
    if ( $( '#branda-notice-permissions-settings-data' ).length ) {
        var $data = $( '#branda-notice-permissions-settings-data' );
        SUI.openFloatNotice( $data.html(), 'success', true, undefined, 'branda-notice-permissions-settings' );
        $( '#branda-notice-permissions-settings' ).data( 'nonce', $data.data( 'nonce' ) ).data( 'id', $data.data( 'id' ) );
    /**
     * Message
     */
    $( '#branda-notice-permissions-settings .sui-icon-check' ).on( 'click', function() {
        var $notice = $( '#branda-notice-permissions-settings' );
        var data = {
            action: 'branda_notice_permissions_notice_save',
            _wpnonce: $notice.data('nonce'),
            id: $notice.data('id')
        };
        $.post( ajaxurl, data );
    });
    }
});
