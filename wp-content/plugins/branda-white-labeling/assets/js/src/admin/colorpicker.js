/**
 * Ultimate Branding
 * http://wpmudev.com/
 *
 * Copyright (c) 2017 Incsub
 * Licensed under the GPLv2+ license.
 *
 * Color picker
 */

/* global window, jQuery  */

jQuery( window.document ).ready(function($){
    "use strict";
    var self = this,
        $suiPickerInputs = $( '.sui-colorpicker-input' );

    $suiPickerInputs.wpColorPicker( {

        change: function( event, ui ) {
            var $this = $( this );
            $this.val( ui.color.toCSS() ).trigger( 'change' );
        }
    });

    if ( $suiPickerInputs.hasClass( 'wp-color-picker' ) ) {

        $suiPickerInputs.each( function() {

            var $suiPickerInput = $(this),
                $suiPicker      = $suiPickerInput.closest( '.sui-colorpicker-wrap' ),
                $suiPickerColor = $suiPicker.find( '.sui-colorpicker-value span[role=button]' ),
                $suiPickerValue = $suiPicker.find( '.sui-colorpicker-value' ),
                $suiPickerClear = $suiPickerValue.find( 'button' ),
                $suiPickerType  = 'hex'
            ;

            var $wpPicker       = $suiPickerInput.closest( '.wp-picker-container' ),
                $wpPickerButton = $wpPicker.find( '.wp-color-result' ),
                $wpPickerAlpha  = $wpPickerButton.find( '.color-alpha' ),
                $wpPickerClear  = $wpPicker.find( '.wp-picker-clear' )
            ;

            // Check if alpha exists
            if ( $suiPickerInput.data( 'alpha' ) === true ) {

                $suiPickerType = 'rgba';

                // Listen to color change
                $suiPickerInput.bind( 'change', function() {

                    // Change color preview
                    $suiPickerColor.find( 'span' ).css({
                        'background-color': $wpPickerAlpha.css( 'background' )
                    });

                    // Change color value
                    $suiPickerValue.find( 'input' ).val( $suiPickerInput.val() );

                } );

            } else {

                // Listen to color change
                $suiPickerInput.bind( 'change', function() {

                    // Change color preview
                    $suiPickerColor.find( 'span' ).css({
                        'background-color': $wpPickerButton.css( 'background-color' )
                    });

                    // Change color value
                    $suiPickerValue.find( 'input' ).val( $suiPickerInput.val() );

                } );
            }

            // Add picker type class
            $suiPicker.find( '.sui-colorpicker' ).addClass( 'sui-colorpicker-' + $suiPickerType );

            // Open iris picker
            $suiPicker.find( '.sui-button, span[role=button]' ).on( 'click', function( e ) {

                $wpPickerButton.click();

                e.preventDefault();
                e.stopPropagation();

            } );

            // Clear color value
            $suiPickerClear.on( 'click', function( e ) {
                e.preventDefault();

                var reset_value = '';

                $wpPickerClear.click();
                $suiPickerValue.find( 'input' ).val( reset_value );
                $suiPickerInput.val( reset_value ).trigger( 'change' );
                $suiPickerColor.find( 'span' ).css({
                    'background-color': reset_value
                });

                e.preventDefault();
                e.stopPropagation();

            } );

        } );
    }

});
