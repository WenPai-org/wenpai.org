/**
 * Branda SUI Tabs
 * http://wpmudev.com/
 *
 * Copyright (c) 2018 Incsub
 * Licensed under the GPLv2+ license.
 *
 */
/* global window, jQuery */
jQuery( window.document ).ready(function($){
    "use strict";
    $('.sui-wrap-branda .sui-sidenav .sui-vertical-tab a' ).on( 'click', function() {
        var container = $(this).closest('.sui-wrap-branda');
        var tabs = $('.sui-sidenav li', container );
        var tab = $(this).data('tab');
        var content, current;
        var url = window.location.href;
        var re = /module=[^&]+/;

        if ( 'undefined' === typeof tab ) {
            return;
        }
        if ( tab === window.branda_current_tab ) {
            return;
        }
        window.branda_current_tab = tab;
        content = $('.sui-box[data-tab]');
        current = $('.sui-box[data-tab="' + tab + '"]');
        if ( url.match( re ) ) {
            url = url.replace( re, 'module=' + tab );
        } else {
            url += '&module=' + tab;
        }
        window.history.pushState( { module: tab }, 'Branda', url );
        tabs.removeClass( 'current' );
        content.hide();
        $(this).parent().addClass( 'current' );
        current.show();
        return false;
    });
});
