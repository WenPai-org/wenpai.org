/**
 * Branda: Admin Help Content
 * http://wpmudev.com/
 *
 * Copyright (c) 2018-2019 Incsub
 * Licensed under the GPLv2 +  license.
 */
/* global window, SUI, ajaxurl */
var Branda = Branda || {};
Branda.admin_help_content_dialog_edit = 'branda-admin-help-content-edit';
Branda.admin_help_content_dialog_delete = 'branda-admin-help-content-delete';
jQuery( document ).ready( function ( $ ) {
    "use strict";
    /**
     * Sortable
     */
    $.fn.branda_admin_help_content_sortable_init = function() {
        $('.branda-admin-help-content-items').sortable({
            items: '.sui-builder-field'
        });
    }
    $.fn.branda_admin_help_content_sortable_init();
    /**
     * Scroll window to top when a help menu opens
     */
    $( document ).on( 'screen:options:open', function () {
        $( 'html, body' ).animate( {scrollTop: 0}, 'fast' );
    } );
    /**
     * SUI: add item
     */
    $( '.branda-admin-help-content-save' ).on( 'click', function () {
        var button = $( this );
        var $dialog = button.closest( '.sui-modal' );
        var id = $('[name="branda[id]"]', $dialog ).val();
        var editor_id = $( 'textarea.wp-editor-area', $dialog ).prop( 'id' );
        var content = $.fn.branda_editor( editor_id );
        var data = {
            action: 'branda_admin_help_save',
            _wpnonce: button.data( 'nonce' ),
            id: id,
            title: $( 'input[type=text]', $dialog ).val(),
            content: content,
        };
        $.post( ajaxurl, data, function ( response ) {
            if ( response.success ) {
                var $parent = $('.module-admin-help-content-php .branda-admin-help-content-items' );
                var $row = $('[data-id='+response.data.id+']', $parent );
                if ( 0 < $row.length ) {
                    $( '.sui-builder-field-label', $row ).html( response.data.title );
                    $( '.sui-builder-field', $row )
                        .data( 'id', response.data.id )
                        .data( 'nonce', response.data.nonce )
                    ;
                } else {
                    var template = wp.template( Branda.admin_help_content_dialog_edit + '-row' );
                    $parent.append( template( response.data ) );
                    $row = $('[data-id='+response.data.id+']', $parent );
                }
                SUI.closeModal();
                SUI.openFloatNotice( response.data.message, 'success' );
                $.fn.branda_admin_help_content_sortable_init;
            } else {
                SUI.openFloatNotice( response.data.message );
            }
        } );
    } );
    /**
     * Dialog delete
     */
    $('.branda-settings-tab-content-admin-help-content .branda-section-items').on( 'click', '.branda-admin-help-content-item-delete', function() {
        var $dialog = $( '#' + Branda.admin_help_content_dialog_delete );
        var $parent = $(this).closest( '.sui-builder-field' );
        $( '.branda-admin-help-content-delete', $dialog )
            .data( 'id', $parent.data('id') )
            .data( 'nonce', $parent.data('nonce') )
        ;
	SUI.openModal(
		Branda.admin_help_content_dialog_delete,
		this,
		undefined,
		true
	);
    });
    /**
     * SUI: delete item
     */
    $( '.branda-admin-help-content-delete' ).on( 'click', function () {
        var button = $( this );
        var data = {
            action: 'branda_admin_help_delete',
            _wpnonce: button.data( 'nonce' ),
            id: button.data( 'id' ),
        };
        $.post( ajaxurl, data, function ( response ) {
            if ( response.success ) {
                var $parent = $('.module-admin-help-content-php .branda-admin-help-content-items' );
                $( '[data-id=' + data.id + ']', $parent ).detach();
                SUI.closeModal();
                SUI.openFloatNotice( response.data.message, 'success' );
            } else {
                SUI.openFloatNotice( response.data.message );
            }
        } );
    } );
    /**
     * Dialog edit
     */
    jQuery('.branda-settings-tab-content-admin-help-content .branda-section-items').on( 'click', '.branda-admin-help-content-item-edit', function(e) {
            var $button = $(this);
            var template;
            var $dialog = $( '#' + Branda.admin_help_content_dialog_edit );
            var $parent = $button.closest( '.sui-builder-field' );
            var data = {
                id: 'undefined' !== typeof $parent.data( 'id' )? $parent.data( 'id' ):'new',
                title: '',
                content: '',
                nonce: $button.data( 'nonce' )
            };
            var editor = $( 'textarea[name="branda[content]"]', $dialog ).prop( 'id' );
            editor = tinymce.get( editor );
            e.preventDefault();
            /**
             * Dialog title
             */
            if ( 'new' === data.id ) {
                $dialog.addClass( 'branda-dialog-new' );
            } else {
                var args = {
                    action: 'branda_admin_help_content_get',
                    _wpnonce: $parent.data('nonce'),
                    id: data.id
                };
                $dialog.removeClass( 'branda-dialog-new' );
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: args,
                    async: false
                }).success( function( response ) {
                    if ( ! response.success ) {
                        SUI.openFloatNotice( response.data.message );
                    }
                    data = response.data;
                });
                if ( 'undefined' === typeof data.title ) {
                    return false;
                }
                data.nonce =  $parent.data( 'nonce' );
            }
            /**
             * set
             */
            $('input[name="branda[title]"]', $dialog ).val( data.title );
            $('textarea[name="branda[content]"]', $dialog ).val( data.content );
	if ( null !== editor ) {
            editor.setContent( data.content );
	}
            $('.branda-admin-help-content-save', $dialog ).data( 'nonce', data.nonce );
            $( 'input[name="branda[id]"]', $dialog ).val( data.id );
	// Open edit dialog
	SUI.openModal(
		Branda.admin_help_content_dialog_edit,
		this,
		undefined,
		true
	);
        });
});
