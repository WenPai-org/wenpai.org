/**
 * Ultimate Branding
 * http://wpmudev.com/
 *
 * Copyright (c) 2017 Incsub
 * Licensed under the GPLv2+ license.
 *
 * Date picker
 */

/* global window, jQuery, ace */

jQuery( window.document ).ready(function($){
    "use strict";
    if ( $.fn.datepicker ) {
        $('.simple-option input.datepicker').each( function() {
            $(this).datepicker({
                altFormat: 'yy-mm-dd',
                altField: '#'+$(this).data('alt')
            });
        });
    }
});
