/**
 * Ultimate Branding
 * http://wpmudev.com/
 *
 * Copyright (c) 2017-2018 Incsub
 * Licensed under the GPLv2+ license.
 *
 * Switch button
 */

/* global window, jQuery, switch_button */

jQuery( window.document ).ready(function(){
    "use strict";
    if ( jQuery.fn.switchButton ) {
        var ultimate_branding_admin_check_slaves  = function() {
            jQuery('.simple-option .master-field' ).each( function() {
                var slave = jQuery(this).data('slave');
                if ( slave ) {
                    var slaves = jQuery( '.simple-option.' + slave );
                    var show = jQuery( '.switch-button-background', jQuery(this).closest('td') ).hasClass( 'checked' );
                    /**
                     * exception:
                     * module: Comments Control
                     */
                    if ( show && 'enabled-posts' === slave ) {
                        show = jQuery( '.switch-button-background', jQuery( '.simple-option .master-field[data-slave="enabled"]' ).closest( 'td' ) ).hasClass( 'checked' );
                    }
                    if ( show ) {
                        slaves.show();
                    } else {
                        slaves.hide();
                    }
                }
            });
        };
    }
});

