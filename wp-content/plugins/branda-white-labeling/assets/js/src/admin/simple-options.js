/**
 * Branda
 * http://wpmudev.com/
 *
 * Copyright (c) 2017-2019 Incsub
 * Licensed under the GPLv2+ license.
 *
 * Simple Options
 */

/* global window, jQuery, ajaxurl, ub_admin, wp, tinyMCE, SUI, ace */


SUI.brandaSaveSettings = function($, form) {
        if ( "undefined" === typeof form ) {
            form = $('[data-tab=' + $(this).closest('.sui-box').data('tab') + '] form');
        }
        if ( 'undefined' !== typeof form ) {
            var error = false;
            var errorParents = [];
            $( 'input[type=number]', form ).each( function() {
                var val = $(this).val();
                if ( 'undefined' !== typeof val ) {
                    var min = $(this).prop( 'min' );
                    var max = $(this).prop( 'max' );
                    var $parent = $(this).closest( '.sui-form-field' );
                    $('.sui-error-message', $parent ).remove();
                    $parent.removeClass( 'sui-form-field-error' );
                    val = parseInt( val );
                    if ( 'undefined' !== typeof min ) {
                        min = parseInt( min );
                        if ( val < min ) {
                            $parent.addClass( 'sui-form-field-error' );
                            $parent.append( '<span class="sui-error-message">'+ub_admin.messages.form.number.min+'</span>' );
                            error = true;
							errorParents.push( $parent );
                        }
                    }
                    if ( 'undefined' !== typeof max ) {
                        max = parseInt( max );
                        if ( val > max ) {
                            $parent.addClass( 'sui-form-field-error' );
                            $parent.append( '<span class="sui-error-message">' + ub_admin.messages.form.number.max + '</span>' );
                            error = true;
							errorParents.push( $parent );
                        }
                    }
                }
            });
            if ( error ) {
				$.each( errorParents, function( index, value ) {
					value.on('change', function( e ) {
						var $this = $( e.currentTarget );
						$('.sui-error-message', $this ).remove();
						$this.removeClass( 'sui-form-field-error' );
					});
				});
                return;
            }
            form.submit();
        }
};

jQuery( window.document ).ready(function($){
    "use strict";

    /**
     * add editor content
     */
    $.fn.extend( {
        branda_editor: function( editor_id ) {
            var content = $( '#' + editor_id ).val();
            var editor = tinyMCE.get( editor_id );
            if ( editor ) {
                content = editor.getContent();
            }
            return content;
        },
        branda_flag_status: function( element ) {
            var $header = $('[data-tab=' + $( element ).closest('.sui-box.branda-settings-tab').data( 'tab' ) + '] .sui-box-status');
            /**
             * Avoid to flag dialog changes!
             */
            if ( 0 < $( element ).closest( '.sui-modal' ).length ) {
                return;
            }
            /**
             * Avoid to flag unnecessary!
             */
            if ( 0 < $( element ).closest( '.branda-avoid-flag' ).length ) {
                return;
            }
            window.branda_has_changed = 'unsaved';
            $( '.branda-status-changes-saved', $header ).hide();
            $( '.branda-status-changes-unsaved', $header ).show();
        },
        branda_generate_id: function() {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
            for (var i = 0; i < 10; i++) {
                text += possible.charAt(Math.floor(Math.random() * possible.length));
            }
            return text;
        }
    });
    /**
     * Slider widget
     */
    if ( $.fn.slider ) {
        $('.simple-option div.ui-slider').each( function() {
            var id = $(this).data('target-id');
            if ( id ) {
                var target = $('#'+id);
                var value = target.val();
                var max = $(this).data('max') || 100;
                var min = $(this).data('min') || 0;
                $(this).slider({
                    value: value,
                    min: min,
                    max: max,
                    slide: function( event, ui ) {
                        target.val( ui.value );
                    }
                });
            }
        });
    }
    /**
     * sortable
     */
    $('table.sortable').sortable({
        items: 'tbody tr'
    });
    $('.branda-social-logos-main-container').sortable({
        items: '.simple-option'
    });
    $('.sui-box-builder-fields').sortable({
        items: '.sui-can-move'
    });
    /**
     * reset section
     */
    $('.branda-reset-section').on('click', function() {
        var dialog_id = 'branda-dialog-reset-section';
        var $dialog = $('#'+dialog_id);
        var $button = $('.branda-data-reset-confirm', $dialog );
        $button.data( 'module', $(this).data('module') );
        $button.data( 'network', $(this).data('network') );
        $button.data( 'nonce', $(this).data('nonce') );
        $button.data( 'section', $(this).data('section') );
        $('.sui-box-body b', $dialog ).html( $(this).data('title') );
        SUI.openModal( dialog_id, this, null, true );
    });
    $('#branda-dialog-reset-section .branda-data-reset-confirm').on( 'click', function() {
        var data = {
            action: 'simple_option_reset_section',
            module: $(this).data('module'),
            nonce: $(this).data('nonce'),
            network: $(this).data('network'),
            section: $(this).data('id') || $(this).data('section')
        };
        jQuery.post(ajaxurl, data, function(response) {
            if ( response.success ) {
                window.location.reload();
            } else if ( 'undefined' !== response.data.message ) {
                SUI.openFloatNotice( response.data.message );
                SUI.closeModal();
            }
        });
    });
    /**
     * slave sections
     */
    $('.simple-options .postbox.section-is-slave').each( function() {
        var $this = $(this);
        var section = $this.data('master-section');
        var field = $this.data('master-field');
        var value = $this.data('master-value');
        $('[name="simple_options['+section+']['+field+']"]').on( 'change', function() {
            if ( 'checkbox' === $(this).prop('type') ) {
                if ( 'on' === value && $(this).is(":checked") ) {
                    $this.show();
                } else if ( 'off' === value && !$(this).is(":checked") ) {
                    $this.show();
                } else {
                    $this.hide();
                }
            } else {
                if ( $(this).val() === value ) {
                    $this.show();
                } else {
                    $this.hide();
                }
            }
        });
    });
    /**
     * Field complex master
     */
    $( '.simple-option.simple-option-complex-master' ).each( function() {
        var $this = $(this);
        var section = $this.data('master-section');
        var field = $this.data('master-field');
        var value = $this.data('master-value');
        if ( "undefined" === typeof section || "undefined" === typeof field || "undefined" === typeof value) {
            return;
        }
        $( '[name="simple_options[' + section + '][' + field + ']' )
            .on( 'change', function() {
                if ( $(this).val() === value ) {
                    $this.show();
                } else {
                    $this.hide();
                }
            });
    });
    /**
     * Copy section data
     */
    $( '.simple-options .ub-copy-section-settings a' ).on( 'click', function(e) {
        e.preventDefault();
        var parent = $(this).closest('div');
        var data = {
            'action': 'simple_option',
            'do': 'copy',
            'nonce': parent.data('nonce'),
            'module': parent.data('module'),
            'section': parent.data('section'),
            'from': $('select', parent).val()
        };
        if ( '-1' === data.from ) {
            window.alert( window.ub_admin.messages.copy.select_first );
            return false;
        }
        if ( window.confirm( window.ub_admin.messages.copy.confirm ) ) {
            jQuery.post(ajaxurl, data, function(response) {
                if ( response.success ) {
                    window.location.reload();
                } else {
                    if ( "undefined" === typeof response.data.message ) {
                        window.alert( window.ub_admin.messages.wrong );
                    } else {
                        window.alert( response.data.message );
                    }
                }
            });
        }
        return false;
    });
    /**
     * CSS editor
     * Using the Shared UI ACE editor.
     */
    function branda_ace_editor_placeholder( id, placeholder ) {
        var shouldShow = !SUI.editors[id].session.getValue().length;
        var node = SUI.editors[id].renderer.emptyMessageNode;
        if (!shouldShow && node) {
            SUI.editors[id].renderer.scroller.removeChild(SUI.editors[id].renderer.emptyMessageNode);
            SUI.editors[id].renderer.emptyMessageNode = null;
        } else if (shouldShow && !node) {
            node = SUI.editors[id].renderer.emptyMessageNode = window.document.createElement('div');
            node.textContent = placeholder;
            node.className = 'ace_emptyMessage';
            node.style.padding = '0 9px';
            SUI.editors[id].renderer.scroller.appendChild(node);
        }
    }
    SUI.editors = {};
    SUI.editors_value = {};
    jQuery( '.sui-ace-editor' ).each( function() {
        var id = this.id;
        var editor_id = $(this).data( 'id' );
        SUI.editors[id] = ace.edit( id );
        SUI.editors[id].getSession().setUseWorker( false );
        SUI.editors[id].setShowPrintMargin( false );
        SUI.editors[id].setTheme( 'ace/theme/sui' );
        SUI.editors[id].getSession().setMode( 'ace/mode/css' );
        if ( $(this).hasClass('ub_html_editor') ) {
            SUI.editors[id].getSession().setMode( 'ace/mode/html' );
        }
        SUI.editors[id].session.setTabSize(4);
        SUI.editors_value[id] = $( '#' + editor_id );
        SUI.editors[id].getSession().on('change', function () {
            SUI.editors_value[id].val(SUI.editors[id].getSession().getValue());
        });
        if ( '' !== $(this).prop( 'placeholder' ) ) {
            SUI.editors[id].on( 'input', function() {
                branda_ace_editor_placeholder( id, $(this).prop( 'placeholder' ) );
            });
            branda_ace_editor_placeholder( id, $(this).prop( 'placeholder' ) );
        }
        $(this).on( 'change', function() {
            $.fn.branda_flag_status( this );
        });
    });

    /**
     * ACE Editor selectors.
     *
     * Add selectors content to the editor.
     */
    jQuery( '.sui-ace-selectors .sui-selector' ).on( 'click', function(e) {
        e.preventDefault();
        var $this = $(this);
        // Editor id.
        var $editor_id = $( 'div.sui-ace-editor', $(this).closest( '.simple-option' ) ).prop( 'id' );
        // Editor.
        var $editor = SUI.editors[$editor_id];
        // Editor mode.
        var $mode = $editor.session.$modeId;
        // Get the selector data based on mode.
        var $selector = $mode === 'ace/mode/css' ? $this.data( "selector" ) + " {}" : $this.data( "selector" );
        // Add the selector data to the editor.
        $editor.insert( $selector );
        if ( $('#' + $editor_id).hasClass( 'ub_css_editor' ) ) {
            $editor.navigateLeft( 1 );
            $editor.insert( "\n" );
            $editor.navigateRight( 2 );
            $editor.insert( "\n" );
            $editor.navigateLeft( 3 );
        }
        $editor.focus();
    });
    /**
     * Handle SUI-tab
     */
    // Define global SUI object if it doesn't exist.
    if ( 'object' !== typeof window.SUI ) {
        window.SUI = {};
    }
    SUI.brandaSideTabs = function()  {
        $('.sui-side-tabs label.sui-tab-item input').on( 'click', function() {
            var $this      = $(this),
                $label     = $this.parent( 'label' ),
                $data      = $this.data( 'tab-menu' ),
                $wrapper   = $this.closest( '.sui-side-tabs' ),
                $alllabels = $( '.sui-tab-item', $(this).closest( '.sui-tabs-menu' ) ),
                $allinputs = $alllabels.find( 'input' ),
                $container = $wrapper.find('.sui-tabs-content').first()
            ;
            $alllabels.removeClass( 'active' );
            $allinputs.removeAttr( 'checked' );
            $container.find( '> div' ).removeClass( 'active' );
            $label.addClass( 'active' );
            $this.prop( 'checked', true );
            if ( $wrapper.find( '.sui-tabs-content div[data-tab-content="' + $data + '"]' ).length ) {
                $wrapper.find( '.sui-tabs-content div[data-tab-content="' + $data + '"]' ).addClass( 'active' );
            }
        });
    };
    SUI.brandaSideTabs();
	/**
     * SUI: save
     */
	var $module_save_button = $('.branda-module-save');
	$module_save_button.on('click', function () {
		window.branda_has_changed = 'saving';
	});

$module_save_button.on( 'click', function () { SUI.brandaSaveSettings.call(this, jQuery) });
    /**
     * reset whole module
     */
    $(' .branda-reset-module' ).on( 'click', function() {
        var dialog_id = 'branda-dialog-reset-module-' + $(this).data('module');
        if ( 'undefined' === $( '#' + dialog_id ) ) {
            if ( window.confirm( ub_admin.messages.reset.module ) ) {
                var args = {
                    action: 'branda_reset_module',
                    module: $(this).data('module'),
                    _wpnonce: $(this).data('nonce')
                };
                $.post( ajaxurl, args, function( response ) {
                    if ( response.success ) {
                        window.location.reload();
                    } else {
                        SUI.openFloatNotice( response.data.message );
                    }
                });
            }
        } else {
            SUI.openModal( dialog_id, this, null, true );
        }
        return false;
    });
    $( '.branda-dialog-reset-module button.branda-reset' ).on( 'click', function() {
        var args = {
            action: 'branda_reset_module',
            module: $(this).data('module'),
            _wpnonce: $(this).data('nonce')
        };
        $.post( ajaxurl, args, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                SUI.openFloatNotice( response.data.message );
            }
        });
    });
    /**
     * show hide slaves
     */
    $( 'input[type=checkbox].master-field' ).on( 'change', function() {
        var slaves;
        var slave = $(this).data('slave');
        if ( slave ) {
            slaves = $( '.' + slave );
            if ( $(this).is(':checked') ) {
                slaves.show();
                return;
            }
            slaves.hide();
        }
    });
    /**
     * required fields
     */
    $('.sui-form-field [data-required=required]').on( 'change, paste, keyup', function() {
        if ( '' !== $(this).val() ) {
            var local_parent = $(this).parent();
            local_parent.removeClass('sui-form-field-error');
            $('span', local_parent ).removeClass( 'sui-error-message' );
        }
    });
    $('#simple_options_reply-to_email').on( 'change', function() {
        var $replyName = $( '#simple_options_reply-to_name' );
        if ( '' !== $(this).val() ) {
            $replyName.removeAttr('disabled');
        } else {
            $replyName.prop( 'disabled', true );
        }
    }).trigger( 'change' );

    /**
     * Admin social media icons
     */
    $.each( $('.branda-social-logo-preview'), function() {
        var target = $(this);
        var source = $('.sui-tabs-menu input[type=radio]', target.parent() );
        var mode = false;
        source.each( function() {
            if ( $(this).is(':checked') ) {
                mode = $(this).val();
            }
            $(this).on( 'change', function() {
                if ( $(this).is(':checked' ) ) {
                    if ('color' === $(this).val() ) {
                        target.addClass( 'social-logo-color' );
                    } else {
                        target.removeClass( 'social-logo-color' );
                    }
                }
            });
        });
        if ( 'color' === mode ) {
            target.addClass( 'social-logo-color' );
        } else {
            target.removeClass( 'social-logo-color' );
        }
    });

    /**
     * social media
     */
    function branda_social_media_bind_delete() {
        $('button.branda-social-logo-remove' ).on( 'click', function() {
            var parent = $(this).closest('.sui-accordion-item-body');
            $( '.branda-social-logo-add-dialog-button', parent ).removeAttr( 'disabled' );
            var target = $('.branda-social-logo-add-dialog .branda-social-logo-li-'+$(this).data('id'), parent );
            target.removeClass( 'hidden' );
            $( 'input', target ).removeAttr( 'checked' );
            $(this).closest('.sui-form-field').detach();
        });
    }
    branda_social_media_bind_delete();

    $('.branda-social-logo-add-accounts' ).on( 'click', function() {
        var parent = $(this).closest('.sui-box');
        var items = $('li:not(.hidden) :checked', parent );
        var branda_template = wp.template( $(this).data( 'template' ) );
        items.each( function() {
            $('.branda-social-logos-main-container', $(this).closest( 'form' ) ).append(
                branda_template({
                    id: $(this).val(),
                    label: $(this).data('label')
                })
            );
            $(this).closest( 'li' ).addClass( 'hidden' );
        });
        branda_social_media_bind_delete();
        /**
         * check to disable button
         */
        if ( 0 === $( 'li', parent ).not('.hidden').length ) {
            $('.branda-social-logo-add-dialog-button', $(this).closest('.sui-accordion-item-body') ).prop( 'disabled', true );
        }
        SUI.closeModal();
        /**
         * re-inicialize sortable
         */
        $('.branda-social-logos-main-container').sortable({
            items: '.simple-option'
        });
    });

    /**
     * add circle to canvas
     */
    function branda_move_focal( context, el, x, y ) {
        var canvas = el.get(0);
        var parent = el.closest( '.sui-form-field' );
        var nx = Math.round( 100 * x / canvas.width );
        var ny = Math.round( 100 * y / canvas.height );
        context.clearRect( 0, 0, canvas.width, canvas.height );
        context.beginPath();
        context.arc( x, y, 7, 0, 2 * Math.PI, false);
        context.fillStyle = $('.sui-wrap-branda').hasClass( 'sui-color-accessible' )? '#000':'#17A8E3';
        context.fill();
        context.lineWidth = 3;
        context.strokeStyle = '#fff';
        context.stroke();
        $( 'input.branda-focal-x', parent ).val( nx );
        $( 'input.branda-focal-y', parent ).val( ny );
        $( 'span.branda-focal-x', parent ).html( nx );
        $( 'span.branda-focal-y', parent ).html( ny );
        el.css( 'backgroundPosition', nx + '% ' + ny + '%' );
        return context;
    }
    $('canvas.branda-focal').each( function() {
        var el = $(this);
        var parent = el.closest( '.sui-form-field' );
        var canvas = el.get(0);
        var context = canvas.getContext('2d');
        var x = Math.round( canvas.width * $( 'input.branda-focal-x', parent ).val() / 100 );
        var y = Math.round( canvas.height * $( 'input.branda-focal-y', parent ).val() / 100 );
        context = branda_move_focal( context, el, x, y );
        canvas.addEventListener('mousedown', function(e) {
            context = branda_move_focal( context, el, e.offsetX, e.offsetY );
        }, true);
        $(this).css( 'backgroundImage', 'url('+$(this).data( 'background-image' )+')' );
    });

    /**
     * Social media: show inactive options
     */
    $( '.ub-radio.branda-social-media-show' ).on( 'click', function() {
        var elements = $('.simple-option-sui-tab', $(this).closest( '.sui-box-body' ) );
        if ( $(this).is(':checked' ) && 'on' === $(this).val() ) {
            elements.removeClass( 'branda-not-affected' );
            $('.sui-notice', $(this).closest( '.simple-option-sui-tab' ) ).hide();
        } else {
            var first = true;
            $('.sui-notice', $(this).closest( '.simple-option-sui-tab' ) ).show();
            $.each( elements, function() {
                if ( ! first ) {
                    $(this).addClass( 'branda-not-affected' );
                }
                first = false;
            });
        }
    });

    /**
     * Indicator
     */
    window.branda_has_changed = 'saved';
    window.onbeforeunload = function() {
        if ( 'unsaved' === window.branda_has_changed ) {
            return ub_admin.messages.unsaved;
        }
    };
    $( '.branda-settings-tab-content input, .branda-settings-tab-content textarea, .branda-settings-tab-content select' ).on( 'change', function() {
	var not_settings = $( this ).closest( '.branda-box-actions' ).length || $( this ).closest( '.sui-table' ).length;
	if ( ! not_settings ) {
		$.fn.branda_flag_status( this );
	}
    });

});
