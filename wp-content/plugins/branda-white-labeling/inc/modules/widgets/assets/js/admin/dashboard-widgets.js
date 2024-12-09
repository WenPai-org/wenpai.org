/**
 * Branda: Dashboard Widgets
 * http://wpmudev.com/
 *
 * Copyright (c) 2018-2019 Incsub
 * Licensed under the GPLv2 +  license.
 */
/* global window, SUI, ajaxurl */
var Branda = Branda || {};
/**
 * Dialogs
 */
Branda.dashboard_widgets_dialog_edit = 'branda-dashboard-widgets-edit';
Branda.dashboard_widgets_dialog_delete = 'branda-dashboard-widgets-delete';
    /**
     * Delete item
     */
    jQuery('.branda-dashboard-widgets-delete').on( 'click', function( e ) {
        var data = {
            action: 'branda_dashboard_widget_delete',
            _wpnonce: jQuery(this).data('nonce'),
            id: jQuery(this).data('id' )
        };
        e.preventDefault();
        jQuery.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                var jQueryparent = jQuery('.branda-dashboard-widgets-items' );
                jQuery( '[data-id=' + response.data.id + ']', jQueryparent ).detach();
                SUI.closeModal();
                SUI.openFloatNotice( response.data.message, 'success' );
                if ( 1 > jQuery( '[data-id]', jQueryparent ).length ) {
                    jQuery( '.sui-box-builder-message', jQueryparent.parent() ).show();
                }
            } else {
                SUI.openFloatNotice( response.data.message );
            }
        });
    });
    /**
     * Dialog edit
     */
    jQuery('.branda-settings-tab-content-dashboard-widgets .branda-section-text').on( 'click', '.branda-dashboard-widgets-item-edit', function(e) {
        var jQuerybutton = jQuery(this);
        var template;
        var jQuerydialog = jQuery( '#' + Branda.dashboard_widgets_dialog_edit );
        var jQueryparent = jQuerybutton.closest( '.sui-builder-field' );
        var data = {
            id: 'undefined' !== typeof jQueryparent.data( 'id' )? jQueryparent.data( 'id' ):'new',
            title: '',
            content: '',
            content_meta: '',
            nonce: jQuerybutton.data( 'nonce' ),
            site: 'on',
            network: 'on'
        };
        var editor;
        e.preventDefault();
        /**
         * Dialog title
         */
        if ( 'new' === data.id ) {
            jQuerydialog.addClass( 'branda-dialog-new' );
        } else {
            var args = {
                action: 'branda_dashboard_widgets_get',
                _wpnonce: jQueryparent.data('nonce'),
                id: data.id
            };
            jQuerydialog.removeClass( 'branda-dialog-new' );
            jQuery.ajax({
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
            data.nonce =  jQueryparent.data( 'nonce' );
        }
        /**
         * set
         */
        jQuery( 'input[name="branda[title]"]', jQuerydialog ).val( data.title );
        jQuery( 'input[name="branda[content]"]', jQuerydialog ).val( data.content );
        jQuery( 'input[name="branda[id]"]', jQuerydialog ).val( data.id );
        jQuery( 'input[name="branda[nonce]"]', jQuerydialog ).val( data.nonce );
        editor = jQuery( 'textarea', jQuerydialog );
        editor.val( data.content );
        editor = editor.prop( 'id' );
        editor = tinymce.get( editor );
        if ( null !== editor ) {
            editor.setContent( data.content );
        }
        /**
         * visibility
         */
        template = wp.template( Branda.dashboard_widgets_dialog_edit + '-pane-visibility' );
        jQuery( '.' + Branda.dashboard_widgets_dialog_edit + '-pane-visibility', jQuerydialog ).html( template( data ) );
        /**
         * Re-init elements
         */
        SUI.brandaSideTabs();
        jQuery( '.sui-tabs-flushed .branda-first-tab', jQuerydialog ).trigger( 'click' );
	// Open edit dialog
	SUI.openModal(
		Branda.dashboard_widgets_dialog_edit,
		this,
		undefined,
		true
	);
    });
    /**
     * Dialog delete
     */
    jQuery('.branda-settings-tab-content-dashboard-widgets .branda-section-text').on( 'click', '.branda-dashboard-widgets-item-delete', function(e) {
        var jQuerydialog = jQuery( '#' + Branda.dashboard_widgets_dialog_delete );
        var jQueryparent = jQuery(this).closest( '.sui-builder-field' );
        jQuery( '.branda-dashboard-widgets-delete', jQuerydialog )
            .data( 'id', jQueryparent.data('id') )
            .data( 'nonce', jQueryparent.data('nonce') )
        ;
	SUI.openModal(
		Branda.dashboard_widgets_dialog_delete,
		this,
		undefined,
		true
	);
    });

/**
 * Add feed
 */
jQuery( window.document ).ready( function( $ ){
    "use strict";
    /**
     * Sortable
     */
    $.fn.branda_dashboard_widgets_sortable_init = function() {
        $('.branda-dashboard-widgets-items').sortable({
            items: '.sui-builder-field'
        });
    }
    $.fn.branda_dashboard_widgets_sortable_init();
    /**
     * Save Dashboard Widget
     */
    $('.branda-dashboard-widgets-save').on( 'click', function() {
        var $parent = $(this).closest( '.sui-modal' );
        var editor_id = Branda.dashboard_widgets_dialog_edit + '-content';
        var data = {
            action: 'branda_dashboard_widget_save',
            _wpnonce: $( 'input[name="branda[nonce]"]', $parent ).val(),
            id: $( 'input[name="branda[id]"]', $parent ).val(),
            content: $.fn.branda_editor( editor_id ),
            title: $( 'input[name="branda[title]"]', $parent ).val(),
            site: $( '[name="branda[site]"]:checked', $parent ).val(),
            network: $( '[name="branda[network]"]:checked', $parent).val(),
        };
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                var $parent = $('.branda-dashboard-widgets-items' );
                var $row = $('[data-id='+response.data.id+']', $parent );
                if ( 0 < $row.length ) {
                    $( '.sui-builder-field-label', $row ).html( response.data.title );
                    $( '.sui-builder-field', $row )
                        .data( 'id', response.data.id )
                        .data( 'nonce', response.data.nonce )
                    ;
                } else {
                    var template = wp.template( Branda.dashboard_widgets_dialog_edit + '-row' );
                    $parent.append( template( response.data ) );
                }
                SUI.closeModal();
                SUI.openFloatNotice( response.data.message, 'success' );
                $.fn.branda_dashboard_widgets_sortable_init;
            } else {
                SUI.openFloatNotice( response.data.message );
            }
        });
    });
    /**
     * Dialog "Reset Widget Visibility List"
     */
    $( '#branda-dashboard-widgets-visibility-reset .branda-data-reset-confirm' ).on( 'click', function() {
        var data = {
            action: 'branda_dashboard_widget_visibility_reset',
            _wpnonce: $( this ).data( 'nonce' )
        };
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                SUI.openFloatNotice( response.data.message );
            }
        });
    });
});
