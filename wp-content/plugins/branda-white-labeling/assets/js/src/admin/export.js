/**
 * Branda
 * http://wpmudev.com/
 *
 * Copyright (c) 2017-2018 Incsub
 * Licensed under the GPLv2+ license.
 *
 * Export & Import javascript
 */

/* global window, jQuery */

jQuery( window.document ).ready(function( $ ){
    /**
     * Open upload input
     */
    $( '.simple-option-file .sui-upload-button' ).on( 'click', function() {
        $('.branda-upload', $(this).closest( '.sui-upload' ) ).click();
    });
    /**
     * reset
     */
    $( '.simple-option-file .sui-upload-file button' ).on( 'click', function() {
        $(this).closest( '.sui-upload' ).removeClass( 'sui-has_file' );
        SUI.closeNotice( 'branda-wrong-filetype' );
    });
    /**
     * bind change
     */
    jQuery( '.module-utilities-import-php .branda-upload').on( 'change', function(e) {
        var parent = $(this).closest( '.sui-box-body' );
        var button = jQuery( 'button[type=submit]', parent );
        var value =  jQuery(this).val();
        if ( '' === value ) {
            button.prop( 'disabled', true );
            $( '.sui-upload', parent ).removeClass( 'sui-has_file' );
            SUI.closeNotice( 'branda-wrong-filetype' );;
        } else {
            $( '.sui-upload', parent ).addClass( 'sui-has_file' );
            var base = value.split( /[\/\\]/ );
            if ( 0 < base.length ) {
                $('.sui-upload-file span', parent ).html( base[ base.length - 1 ] );
            }
            var re = /json$/i;
            if ( re.test( value ) ) {
                button.removeAttr( 'disabled' );
                SUI.closeNotice( 'branda-wrong-filetype' );
            } else {
                SUI.openInlineNotice( 'branda-wrong-filetype', ub_admin.messages.export.not_json );
            }
        }
    });
});
