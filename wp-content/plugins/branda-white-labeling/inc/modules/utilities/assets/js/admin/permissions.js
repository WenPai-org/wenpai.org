/**
 * Branda: Permissions
 * http://wpmudev.com/
 *
 * Copyright (c) 2019 Incsub
 * Licensed under the GPLv2 +  license.
 */
/* global window, SUI, ajaxurl */
var Branda = Branda || {};
/**
 * Dialogs
 */
Branda.permissions_add_user = 'branda-permissions-add-user';
/**
 * Dashboard Row Buttons Bind
 */
Branda.permissions_add_user_bind = function( container ) {
    /**
     * Delete item
     */
    jQuery('.branda-permissions-delete', container ).on( 'click', function( e ) {
        var $this = jQuery(this),
			$row = $this.closest( '.sui-builder-field' ),
			data = {
            action: 'branda_permissions_delete_user',
            _wpnonce: $row.data('nonce'),
            id: $row.data('id' )
        };

        jQuery('.sui-loading-text', $this ).hide();
        jQuery('.sui-loading', $this ).show();
        e.preventDefault();
        jQuery.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                var $parent = jQuery('.branda-admin-permissions-user-items' );
                jQuery( '[data-id=' + response.data.id + ']', $parent ).detach();
                SUI.openFloatNotice( response.data.message, 'success', true );
            } else {
                SUI.openFloatNotice( response.data.message );
            }
			jQuery('.sui-loading-text', $this ).show();
			jQuery('.sui-loading', $this ).hide();
        });
    });
};


Branda.prepareUserAddSearch = function ( $ ) {
	var searchuser = $('#searchuser');
	var hash       = searchuser.data('hash');

	var languageSearching     = searchuser.data('language-searching');
	var languageNoresults     = searchuser.data('language-noresults');
	var languageErrorLoading  = searchuser.data('language-error-load');
//	var languageInputTooShort = searchuser.data('language-input-tooshort');

	searchuser.SUIselect2({
		allowClear: true,
		dropdownCssClass: 'sui-select-dropdown',
		dropdownParent: $('.sui-modal #branda-permissions-add-user'),
		ajax: {
			url: ajaxurl,
			type: "POST",
			data: function (params) {
				return {
					action: 'branda_usersearch',
					hash: hash,
					q: params.term,
				};
			},
			processResults: function (data) {
				return {
					results: data.data
				};
			},
		},
		templateResult: function (result) {
			if (typeof result.id !== 'undefined' && typeof result.label !== 'undefined') {
				return $(result.label);
			}
			return result.text;
		},
		templateSelection: function (result) {
			$('.branda-permissions-add-user').data({'email':result.email, 'avatar':result.thumb, 'display-name':result.displayName });
			return result.display || result.text;
		},
		language: {
			searching: function () {
				return languageSearching;
			},
			noResults: function () {
				return languageNoresults;
			},
			errorLoading: function () {
				return languageErrorLoading;
			},
//			inputTooShort: function () {
//				return languageInputTooShort;
//			},
		}
	});
};

jQuery( window.document ).ready( function( $ ){
    "use strict";
    /**
     * Bulk select modules
     */
    $( '.branda-permissions-status input[type=checkbox].all' ).on( 'change', function() {
        var $parent = $('.sui-box', $(this).closest( 'div' ) );
        if ( $(this).is(':checked') ) {
            $('input[type=checkbox]', $parent ).prop( 'checked', true );
        } else {
            $('input[type=checkbox]', $parent ).removeProp( 'checked' );
        }
    });

	//searching users
	Branda.prepareUserAddSearch( $ );

	/**
	 * Save New User
	 */
	$('.branda-permissions-add-user').on( 'click', function() {
		var $parent = $(this).closest( '.sui-modal' ),
			id = $( '#searchuser', $parent ).val(),
			login = $( '#user_login', $parent ).val(),
			self = $(this);

		if ( ! id && ! login ) {
			return;
		}

		$('.sui-loading-text', self ).hide();
		$('.sui-loading-text-adding', self ).show();
		$('.sui-button', $parent ).addClass( 'sui-button-onload' );

		var data = {
			action: 'branda_permissions_add_user',
			_wpnonce: $(this).data('nonce'),
			id: id,
			login: login,
		};
		$.post( ajaxurl, data, function( response ) {
			$('.sui-loading-text', self ).show();
			$('.sui-loading-text-adding', self ).hide();
			$('.sui-button', $parent ).removeClass( 'sui-button-onload' );

			if ( response.success ) {
				var $parent = $('.branda-admin-permissions-user-items' ),
					$row,
					template_data;

				template_data = {
					'id' : response.data.id,
					'nonce' : response.data.nonce,
					'email' : "undefined" !== typeof response.data.email ? response.data.email : self.data('email'),
					'avatar' : "undefined" !== typeof response.data.avatar ? response.data.avatar : self.data('avatar'),
					'title' : "undefined" !== typeof response.data.display_name ? response.data.display_name : self.data('display-name'),
				};

				var template = wp.template( Branda.permissions_add_user + '-row' );

				$parent.append( template( template_data ) );
				$row = $('[data-id='+response.data.id+']', $parent );
				Branda.permissions_add_user_bind( $row );

				$('.select2-selection__clear' ).trigger('mousedown');
				SUI.closeModal();
				SUI.openFloatNotice( response.data.message, 'success', true );
			} else {
				SUI.openFloatNotice( response.data.message );
			}

		});
	});
	/**
	 * bind
	 */
	Branda.permissions_add_user_bind( $( '.branda-admin-page' ) );
});
