/**
 * Ultimate Branding
 * http://wpmudev.com/
 *
 * Copyright (c) 2017-2019 Incsub
 * Licensed under the GPLv2+ license.
 */
/* global wp, window, wp_media_post_id, jQuery, ajaxurl, SUI, tinyMCE */

jQuery( window.document ).ready( function($) {
	// Show New features modals.
	if ( $( '.branda-new-features' ).length ) {
		var modal_id = $( '.branda-new-features' ).prop( 'id' );
		window.setTimeout( function() {
			SUI.openModal( modal_id, this, null, true );
		}, 100 );
	}

	// Hit on Got it.
	$( '.branda-new-feature-got-it' ).on( 'click', function() {
		var data = {
			action: 'ultimate_branding_new_feature_dismiss',
            _ajax_nonce: $(this).data('nonce'),
			id: $( this ).data( 'dismiss-id' )
		};
        jQuery.post(ajaxurl, data, function( response ) {
			SUI.closeModal();
        });
        return false;
	});

	/**
	 * Handle activate/deactivate module
	 */
    $( '.ub-activate-module, .ub-deactivate-module' ).on( 'click', function() {
        var data = {
            action: 'ultimate_branding_toggle_module',
            module: $(this).data('slug'),
            nonce: $(this).data('nonce'),
            state: $(this).hasClass('ub-activate-module')? 'on':'off'
        };
        jQuery.post(ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else if ( undefined !== typeof response.data.message ) {
                SUI.openFloatNotice( response.data.message );
            }
        });
        return false;
    });

	/**
	 * Manage all modules
	 */
	function branda_modules_mark_all( selector, parent ) {
		jQuery( selector, parent )
			.on( 'click', function() {
				jQuery(this).prop( 'indeterminate', false );
			}).on( 'change', function() {
				if ( jQuery(this).is(':checked' ) ) {
					jQuery('input[type=checkbox]', jQuery(this).closest( '.sui-col' ) ).prop( 'checked', true );
				} else {
					jQuery('input[type=checkbox]', jQuery(this).closest( '.sui-col' ) ).removeAttr( 'checked' );
				}
			});
		jQuery.each( jQuery( selector, parent ), function() {
			if ( ! jQuery( this ).is( ':checked' ) ) {
				jQuery( this ).prop( 'indeterminate', true );
			}
		});
	}
    branda_modules_mark_all( '#branda-manage-all-modules .branda-group-checkbox, .branda-import-import .branda-group-checkbox', 'body' );

	/**
	 * Trigger tab change when mobile dropdown is changed.
	 */
    $( '#branda-mobile-nav' ).change(function () {
        var tab = $(this).val();
        $('a[data-tab="' + tab + '"]').trigger('click');
    });

	/**
	 * save bulk "Manage All Modules".
	 */
	function branda_modules_save_bulk( selector, parent, is_welcome ) {
		jQuery( selector, parent ).on( 'click', function() {
			var branda = [];
			var button = jQuery(this);
			var dialog = button.closest('.sui-modal');
			jQuery('input[type=checkbox]:checked', dialog ).each( function() {
				var value = jQuery(this).prop('name');
				if ( value ) {
					branda.push( value );
				}
			});
			if ( 0 === branda.length ) {
				if ( is_welcome ) {
					SUI.openFloatNotice( window.ub_admin.messages.welcome.empty, 'warning' );
					return false;
				} else {
					branda = 'turn-off-all-modules';
				}
			}
			jQuery('.sui-loading-text', button ).hide();
			jQuery('.sui-loading', button ).show();
			button.prop('disabled', true );
			var data = {
				action: 'branda_manage_all_modules',
				branda: branda,
				nonce: jQuery(this).data('nonce'),
			};
			jQuery.post(ajaxurl, data, function( response ) {
				if ( response.success ) {
					window.location.reload();
				} else if ( undefined !== typeof response.data.message ) {
					var notice_type = response.data.type || 'error';
					SUI.openFloatNotice( response.data.message, notice_type, response.data.can_dismiss, response.data.close_after );
					jQuery('.sui-loading-text', button ).show();
					jQuery('.sui-loading', button ).hide();
					button.removeProp( 'disabled' );
				}
			});
		});
	}
    branda_modules_save_bulk( '#branda-manage-all-modules .sui-box-footer button.branda-save-all', 'body', false );

	/**
	 * Reset dialog content
	 */
    $('.branda-dialog-reset').on( 'click', function() {
        var parent = $('.sui-box-body', $(this).closest( '.sui-box' ) );
        var id = $(this).data('id');
            $.each( $('input', parent ), function() {
                switch( $(this).prop('type' ) ) {
                    case 'radio':
                        if ( $(this).val() === $(this).data('default' ) ) {
                            $(this).prop( 'checked', true );
                            $(this).closest( 'label').trigger( 'click' ).addClass( 'active' );
                        } else {
                            $(this).removeAttr( 'checked' );
                            $(this).closest( 'label').trigger( 'click' ).removeClass( 'active' );
                        }
                        break;
                    default:
                        this.value = this.defaultValue;
                }
            });
            $.each( $('textarea', parent ), function() {
                this.value = this.defaultValue;
                tinyMCE.get( $(this).prop('id') ).setContent( this.defaultValue );
            });
            return false;
    });

	/**
	 * Copy Settings
	 *
	 * @since 3.0.0
	 */
    function branda_copy_settings_check( dialog ) {
        var module_id = $('.branda-copy-settings-select option:selected', dialog ).val();
        var sections = $('.branda-copy-settings-' + module_id + ' .branda-copy-settings-section:checked');
        if ( 1 > sections.length ) {
            $('.branda-copy-settings-copy-button', dialog ).prop( 'disabled', true );
        } else {
            $('.branda-copy-settings-copy-button', dialog ).removeAttr( 'disabled' );
        }
    }
    $('.branda-copy-settings-select').on( 'change', function() {
        var dialog = $(this).closest('.sui-modal');
        var module_id = $(':selected', $(this) ).val();
        $('.branda-copy-settings-options', dialog ).hide();
        if ( '' !== module_id ) {
            $('.branda-copy-settings-copy-button', dialog ).prop( 'disabled', true );
            $('.branda-copy-settings-' + module_id ).show();
        }
        branda_copy_settings_check( dialog );
    });
    $('.branda-copy-settings-section').on( 'change', function() {
        var dialog = $(this).closest('.sui-modal');
        branda_copy_settings_check( dialog );
    });
    $('.branda-copy-settings-copy-button').on( 'click', function() {
        var dialog = $(this).closest('.sui-modal');
        var module_id = $('.branda-copy-settings-select option:selected', dialog ).val();
        var data = {
            action: 'branda_module_copy_settings',
            target_module: $(this).data('module'),
            source_module: module_id,
            nonce: $(this).data('nonce'),
            sections: [],
        };
        var sections = $('.branda-copy-settings-' + module_id + ' .branda-copy-settings-section:checked');
        if ( 0 < sections.length ) {
            $.each( sections, function() {
                data.sections.push( $(this).val() );
            });
            jQuery.post(ajaxurl, data, function( response ) {
                if ( response.success ) {
                    window.location.reload();
                } else if ( undefined !== typeof response.data.message ) {
                    SUI.openFloatNotice( response.data.message );
                }
            });
        }
        return false;

    });

	/**
	 * Bulk delete button
	 *
	 * @since 3.0.0
	 */
    $( 'input[type=checkbox].branda-cb-select-all' ).on( 'change', function() {
        var parent = $(this).closest( '.branda-settings-tab' );
        if ( $(this).is(':checked') ) {
            $('.check-column input[type=checkbox]:not(.branda-cb-select-all)', parent ).prop( 'checked', true ).filter( ':first' ).trigger( 'change' );
        } else {
            $('.check-column input[type=checkbox]:not(.branda-cb-select-all)', parent ).removeProp( 'checked' ).filter( ':first' ).trigger( 'change' );
        }
    });
    $( '.branda-bulk-delete' ).on( 'click', function() {
        var parent = $(this).closest( '.branda-settings-tab' ),
	form = $(this).closest( 'form' );
        var action = $( 'select option:selected', form ).val();
        if ( '-1' === action ) {
            return false;
        }
        if ( 0 === $( '.check-column input:checked', parent ).length ) {
            return false;
        }
        SUI.openModal( $(this).data('dialog'), this, null, true );
    });

	/**
	 * Send test email
	 *
	 * @since 3.0.0
	 */
    $( '.branda-test-email .sui-box-footer button' ).on( 'click', function() {
        var $button = $(this);
        var email = $('input[type=email]', $(this).closest('.sui-box') );
        var data = {
            action: $(this).data('action'),
            _wpnonce: $(this).data('nonce'),
            email: email.val()
        };
        if ( '' === email.val() ) {
            email.parent().addClass( 'sui-form-field-error' );
            $('span', email.parent()).addClass('sui-error-message');
            return;
        }
        $('span', $button ).hide();
        $('i', $button ).show();
        $button.addClass( 'sui-button-ghost' ).prop( 'disabled', true );
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                $('span', $button ).show();
                $('i', $button ).hide();
                $button.removeClass( 'sui-button-ghost' ).removeProp( 'disabled' );
                SUI.openFloatNotice( response.data.message );
            }
        });
    });

	if ( "undefined" !== typeof window.ub_admin.admin_notices ) {
		$.each( window.ub_admin.admin_notices, function( i, val ) {
			var text = val.message,
				type = val.type,
				can_dismiss = val.can_dismiss;
			SUI.openFloatNotice( text, type, can_dismiss );
		});
	}
});

/**
* Improve SUI notifications
*/
(function($) {
	/**
	 * Open float SUI notice
	 *
	 * @param {string} text
	 * @param {string} type
	 * @param {bool} can_dismiss
	 * @param {int} close_after
	 * @param {string} id
	 */
	SUI.openFloatNotice = function ( text, type, can_dismiss, close_after, id ) {
		SUI.openNoticeBranda( true, id, text, type, can_dismiss, close_after );
	}

	/**
	 * Open inline SUI notice
	 *
	 * @param {string} notice_id
	 * @param {string} text
	 * @param {string} type
	 * @param {bool} can_dismiss
	 * @param {int} close_after
	 */
	SUI.openInlineNotice = function ( notice_id, text, type, can_dismiss, close_after ) {
		SUI.openNoticeBranda( false, notice_id, text, type, can_dismiss, close_after );
	}

	/**
	 * Private SUI notice function
	 *
	 * @param {bool} float
	 * @param {string} notice_id
	 * @param {string} text
	 * @param {string} type
	 * @param {bool} can_dismiss
	 * @param {int} close_after
	 */
	SUI.openNoticeBranda = function ( float, notice_id, text, type, can_dismiss, close_after ) {
		if ( type === undefined ) {
			type = 'error';
		}
		var icon = 'info';
		switch (type) {
			case 'info':
				if ( can_dismiss === undefined ) {
					can_dismiss = true;
				}
				break;
			case 'success':
				icon = 'check-tick';
				if ( close_after === undefined ) {
					close_after = 3000;
				}
				break;
			case 'warning':
			case 'error':
				icon = 'warning-alert';
				break;
			case 'loading':
				icon = 'loader';
				break;
		}

		// set default values.
		if ( can_dismiss === undefined ) {
			can_dismiss = false;
		}
		if ( close_after === undefined ) {
			close_after = false;
		}
		if ( notice_id === undefined ) {
			// Generate random id.
			notice_id = 'sui-notice-' + Math.random().toString( 36 ).substr( 2, 9 );
		}

		if ( float ) {
			// get notice container or create it.
			var notice_container = $( '.sui-floating-notices' ).eq( 0 );
			if ( ! notice_container.length ) {
				notice_container = $( '<div class="sui-floating-notices"></div>' );
				notice_container.insertAfter( $( '.sui-header' ).eq( 0 ) );
			}

			var notice = $( '<div role="alert" id="' + notice_id + '" class="sui-notice" aria-live="assertive"></div>' );
			notice.appendTo( notice_container );
		} else {
			$( '#' + notice_id ).removeClass (function (index, className) {
				return (className.match (/(^|\s)sui-notice-\S+/g) || []).join(' ');
			});
		}

		var notice_options = {type: type, icon: icon};
		if (close_after) {
			notice_options.autoclose = {timeout: close_after};
		} else {
			notice_options.autoclose = {show: false};
		}
		if (can_dismiss) {
			notice_options.dismiss = {show: true};
		}
		if (text instanceof jQuery) {
			SUI.openNotice( notice_id, '<p></p>', notice_options );
			$( '#' + notice_id ).find( '.sui-notice-message>p' ).append( text );
		} else {
			SUI.openNotice( notice_id, '<p>' + text + '</p>', notice_options );
		}
	};
	SUI.closeNoticeBranda = function () {
		try {
			SUI.closeModal();
		} catch (e) {
		}
	};
})(jQuery);
