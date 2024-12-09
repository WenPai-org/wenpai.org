/**
 * Branda: Text Replacement
 * http://wpmudev.com/
 *
 * Copyright (c) 2018 Incsub
 * Licensed under the GPLv2 +  license.
 */
/* global window, SUI, ajaxurl */
jQuery( window.document ).ready( function( $ ){
    "use strict";
    /**
     * Open add/edit modal
     */
    $('.branda-text-replacement-new, .branda-text-replacement-edit').on( 'click', function() {
        var parent = $('.sui-box-body', $(this).closest( '.sui-box' ) );
        var id = $(this).data('id');
        var required = false;
        $('[data-required=required]', parent ).each( function() {
            if ( '' === $(this).val() ) {
                var local_parent = $(this).parent();
                local_parent.addClass('sui-form-field-error');
                $('span', local_parent ).addClass( 'sui-error-message' );
                required = true;
            }
        });
        if ( required ) {
            return;
        }
        var data = {
            action: 'branda_text_replacement_save',
            _wpnonce: $(this).data('nonce'),
            id: id,
            find: $('#branda-text-replacement-find-' + id, parent).val(),
            replace: $('#branda-text-replacement-replace-' + id, parent).val(),
            domain: $('#branda-text-replacement-domain-' + id, parent).val(),
            scope: $('.branda-text-replacement-scope input[type=radio]:checked', parent).val(),
            ignorecase: $('.branda-text-replacement-ignorecase input[type=radio]:checked', parent).val(),
            exclude_url: $('.branda-text-replacement-exclude_url input[type=radio]:checked', parent).val(),
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
     * Delete item
     */
    $('.branda-text-replacement-delete').on( 'click', function() {
        if ( 'bulk' === $(this).data('id' ) ) {
            return false;
        }
        var data = {
            action: 'branda_text_replacement_delete',
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
    $( '.branda-text-replacement-delete[data-id=bulk]').on( 'click', function() {
        var data = {
            action: 'branda_text_replacement_delete_bulk',
            _wpnonce: $(this).data('nonce'),
            ids: [],
        }
        $('input[type=checkbox]:checked', $('#branda-text-replacement-items-table' ) ).each( function() {
            data.ids.push( $(this).val() );
        });
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                SUI.openFloatNotice( response.data.message );
            }
        });
        return false;
    });
});
