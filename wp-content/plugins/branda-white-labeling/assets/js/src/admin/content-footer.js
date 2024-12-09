/**
 * Ultimate Branding
 * http://wpmudev.com/
 *
 * Copyright (c) 2017-2018 Incsub
 * Licensed under the GPLv2+ license.
 *
 * Footer content js.
 */

/* global window, jQuery, switch_button */
jQuery( window.document ).ready( function ( $ ) {
	"use strict";
	// Show/hide on button toggle.
	$( '#subsites_option .ub-footer-subsites-toggle' ).on( 'change', function () {
		var subsites_options = $( '#subsites_option .ub-footer-subsites' );
		if ( $( this ).is( ':checked' ) && 'on' === $( this ).val() ) {
			subsites_options.show();
		} else {
			subsites_options.hide();
		}
	} );
} );