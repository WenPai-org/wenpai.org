/**
 * Branda: Signup Code
 * http://wpmudev.com/
 *
 * Copyright (c) 2019 Incsub
 * Licensed under the GPLv2 +  license.
 */
/* global window, SUI, ajaxurl */
/**
 * Globals
 */
var Branda = Branda || {};
/**
 * Bind row buttons
 */
Branda.signup_code_row_buttons_bind = function( container ) {
    var $ = jQuery;
    $('.branda-signup-code-user, .branda-signup-code-blog', container ).each( function() {
        $('.sui-button-icon', $(this) ).has( '.sui-icon-trash' ).on( 'click', function() {
            $.fn.branda_flag_status( this );
            $(this).closest( '.sui-box' ).detach();
        });
    });
};
jQuery( window.document ).ready( function( $ ) {
    "use strict";
    /**
     * Bind
     */
    Branda.signup_code_row_buttons_bind( $( '.branda-admin-page' ) );
    /**
     * Add
     */
    $('.branda-add', $('.branda-signup-code-user, .branda-signup-code-blog' ) ).on( 'click', function() {
        var template = wp.template( $(this).data('template') );
        var $target = $('.sui-box-builder-body .sui-box-builder-fields', $(this).closest( '.sui-box-builder' ) );
		var new_row_id = 'new-' + $.fn.branda_generate_id();
		var args = {
            id: new_row_id
        };
        $target.append( template( args ) );
        $.fn.branda_flag_status( this );
        Branda.signup_code_row_buttons_bind( $(this).closest( '.sui-tab-boxed' ) );

		// Initialize the new select field
		var $select = jQuery('[data-id="' + new_row_id + '"] select');
		SUI.suiSelect($select);
    });
});
