;jQuery( document ).ready( function( $ ) {
    var brandaModalInputData = {},
        modalOpenedByButton = false;

    $( '#branda-tracking-codes-items-table .actions button.sui-button-icon[data-modal-open^="branda-tracking-codes"]:first-child, button[data-modal-open="branda-tracking-codes-new"]' ).on( 'click', function( e ) {
        brandaModalInputData = {};
        modalOpenedByButton = true;

        var modalID = $(this).attr( 'data-modal-open' ),
            modal = $( `#${modalID}` ),
            isNew = 'branda-tracking-codes-new' === modalID;

            bradnaSetModalTCFields( modal, isNew );
    } );

    $( 'body' ).on( 'keyup', '.branda-provider-inputs input', function(){
        brandaModalInputData[ $(this).attr( 'id' ) ] = $(this).val();
    } );

    $('.wp-list-table.tracking-codes span.delete a').on( 'click', function() {
        return window.confirm( ub_tracking_codes.delete );
    });
    $('.tab-tracking-codes .button.action').on( 'click', function() {
        var value = $('select', $(this).parent()).val();
        if ( '-1' === value ) {
            return false;
        }
        if ( 'delete' === value ) {
            return window.confirm( ub_tracking_codes.bulk_delete );
        }
        return true;
    });
    /**
     * save code
     */
    $( 'button.branda-tracking-codes-save' ).on( 'click', function() {
        var dialog = $(this).closest( '.sui-modal' );
        var data = {
            action: 'branda_tracking_codes_save',
            _wpnonce: $(this).data('nonce'),
        };
        $('input, select, textarea', dialog ).each( function() {
            if ( undefined === $(this).prop( 'name' ) ) {
                return;
            }
            if ( 'radio' === $(this).prop( 'type' ) ) {
                if ( $(this).is(':checked' ) ) {
                    data[$(this).data('name')] = $(this).val();
                }
            } else {
                data[$(this).prop('name')] = $(this).val();
            }
        });
        var i= 0;
        var editor = $('.branda-general-code label', dialog ).prop( 'for' );

        if ( editor in SUI.editors ) {
            data['branda[code]'] = SUI.editors[ editor ].getValue();
        }

        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                SUI.openFloatNotice( response.data.message );
            }
        });
    });
    /**
     * reset
     */
    $( '.branda-tracking-codes-reset' ).on( 'click', function() {
        var id = $(this).data( 'id' );
        var dialog = $( '#branda-tracking-codes-' + id );
        var args = {
            action: 'branda_admin_panel_tips_reset',
            id: id,
            _wpnonce: $(this).data( 'nonce' )
        };
        $.post(
            ajaxurl,
            args,
            function ( response ) {
                if (
                    'undefined' !== typeof response.success &&
                    response.success &&
                    'undefined' !== typeof response.data
                ) {
                    var data = response.data;
                    if ( 'undefined' !== typeof data.active ) {
                        $('.branda-general-active input[value='+data.active+']', dialog ).click();
                    }
                    if ( 'undefined' !== typeof data.title ) {
                        $('[name="branda[title]"]', dialog ).val( data.title );
                    }
                    if ( 'undefined' !== typeof data.code ) {
                        var editor_id = 'branda-general-code-' + id;
                        var all = document.querySelectorAll('.ace_editor');
                        for (var i = 0; i < all.length; i++) {
                            if (
                                all[i].env &&
                                all[i].env.editor &&
                                all[i].env.textarea &&
                                all[i].env.textarea.id &&
                                editor_id === all[i].env.textarea.id
                            ) {
                                all[i].env.editor.setValue( data.code );
                            }
                        }
                    }
                    if ( 'undefined' !== typeof data.place ) {
                        $('.branda-location-place input[value='+data.place+']', dialog ).click();
                    }
                    if ( 'undefined' !== typeof data.filter ) {
                        $('.branda-location-filter input[value='+data.filter+']', dialog ).click();
                    }
                    if ( 'undefined' !== typeof data.users ) {
                        $('select[name="branda[users]"]', dialog ).SUIselect2( 'val', [ data.users ] );
                    }
                    if ( 'undefined' !== typeof data.authors ) {
                        $('select[name="branda[authors]"]', dialog ).SUIselect2( 'val', [ data.authors ] );
                    }
                    if ( 'undefined' !== typeof data.archives ) {
                        $('select[name="branda[archives]"]', dialog ).SUIselect2( 'val', [ data.archives ] );
                    }
                }
            }
        );
    });
    /**
     * delete item/bulk
     */
    $( '.branda-tracking-codes-delete' ).on( 'click', function() {
        var id = $(this).data('id');
        var action = 'branda_tracking_codes_delete';
        var ids = [];
        if ( 'bulk' === id ) {
            action = 'branda_tracking_codes_bulk_delete';
            $('tbody .check-column input:checked').each( function() {
                ids.push( $(this).val() );
            });
        }
        var data = {
            action: action,
            id: $(this).data('id' ),
            ids: ids,
            _wpnonce: $(this).data('nonce'),
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
     * User filter exclusions
     */
    function branda_tracking_codes_user_filter_exclusion( e ) {
        var select = e.target;
        var data = e.params.data;
        if ( data.selected ) {
            switch ( data.id ) {
                case 'logged':
                    $('option', select ).each( function() {
                        $(this).prop( 'disabled', false );
                    });
                    $('[value=anonymous]', select ).prop( 'disabled', true );

                    break;
                case 'anonymous':
                    $('option', select ).each( function() {
                        $(this).prop( 'disabled', true );
                    });
                    $('[value=anonymous]', select ).prop( 'disabled', false );
                    break;
            }
        } else {
            switch ( data.id ) {
                case 'logged':
                    $('[value=anonymous]', select ).prop( 'disabled', false );
                    break;
                case 'anonymous':
                    $('option', select ).each( function() {
                        $(this).prop( 'disabled', false );
                    });
                    break;
            }
        }
    }
    $('.branda-tracking-codes-filter-users').on( 'select2:select', function( e ) {
        branda_tracking_codes_user_filter_exclusion( e );
        $(e.target).SUIselect2({
            dropdownCssClass: 'sui-select-dropdown'
        });
    });
    $('.branda-tracking-codes-filter-users').on( 'select2:unselect', function( e ) {
        branda_tracking_codes_user_filter_exclusion( e );
        // Added timeout because select2 doesn't allow enough time for the select element to load causing an error.
        setTimeout(function() {
            $(e.target).SUIselect2({
                dropdownCssClass: 'sui-select-dropdown'
            });
        });
    });

    function bradnaSetModalTCFields( modal, isNew ) {
        if ( isNew ) {
            modal.find( 'select[name="branda[provider]"]' ).val( 'google_tag' ).trigger("change");
        }

        var provider = ! isNew ? modal.find( 'select[name="branda[provider]"]' ).val() : '',
            group_id = modal.find( '.branda-tracking-codes-id' ).val(),
            inputs = bradnaGetModalInputs( provider ),
            existing_inputs = modal.find( '.branda-provider-inputs input' ),
            existing_inputs_data = {},
            i_pairs = {},
            inputCountrer = 1;

        // Try apply stored data when opening a Modal to Edit (only when opening not when changing the Select value).
        if ( typeof group_id !== 'undefined' && 'new' !== group_id && modalOpenedByButton ) {
            var modal_stored_provider_holder = $( `span[data-groupid="${group_id}"]` );

            provider = modal_stored_provider_holder.attr( 'data-ub-provider' );
            modalOpenedByButton = false;
            
            modal.find( 'select[name="branda[provider]"]' ).val( provider ).trigger("change");
            return;
        }

        for( var i = 0; i < existing_inputs.length; i++ ) {
            var i_key = $(existing_inputs[i]).attr( 'name' ).replace(/branda\[|\]/g,''),
                i_value = $(existing_inputs[i]).val(),
                provider_inputs_wrap = $('<div />'),
                input_field_id = `branda-general-${i_key}-${group_id}`;

                if ( typeof brandaModalInputData[input_field_id] !== 'undefined' ) {
                    i_value = brandaModalInputData[i_key];
                }
                
            i_pairs[i_key] = i_value;
        }

        if ( typeof existing_inputs_data[group_id] === 'undefined' ) {
            existing_inputs_data[group_id] = i_pairs;
        }

        Object.keys(inputs).forEach((key, index) => {
            if ( typeof provider_inputs_wrap === 'undefined' ) {
                return;
            }

            var input_id = `branda-general-${key}-${group_id}`;

             // Check if we have this input in the existing inputs of the Modal.
             if ( typeof existing_inputs_data[group_id][key] !== 'undefined' ) {
                inputs[key].value = existing_inputs_data[group_id][key];
             }else if ( typeof brandaModalInputData[input_id] !== 'undefined' ) {
                inputs[key].value = brandaModalInputData[input_id];
            }

            var input_wrap = $('<div />'),
                input_label = $('<label />', {
                    'for': input_id,
                    'class': 'sui-label',
                    'text': inputs[key].label
                }),
                input_element = $('<input />',{
                    type: 'text',
                    id: input_id,
                    name: `branda[${key}]`,
                    'class': 'sui-form-control',
                    'placeholder': inputs[key].placeholder,
                    'value':  'new' !== group_id ? inputs[key].value : '',

                });

            input_wrap.append( input_label );
            input_wrap.append( input_element );
            provider_inputs_wrap.append( input_wrap );

            if ( inputCountrer <  Object.keys(inputs).length  ) {
                provider_inputs_wrap.append( $( '<br />' ) );
            }

            inputCountrer++
        });
        modal.find( '.branda-provider-inputs' ).empty();
        modal.find( '.branda-provider-inputs' ).append( provider_inputs_wrap );
    }

    function bradnaGetModalInputs( provider ) {
        var defaults = window.Branda_Helper.messages.default_fields;
        var inputsData = null;

        if ( '' !== provider && typeof provider !== "undefined" ) {
            if ( typeof window.Branda_Helper.messages.providers[provider].inputs !== "undefined" ) {
                inputsData = window.Branda_Helper.messages.providers[provider].inputs;
            }
        }

        if ( null === inputsData ) {
            inputsData = defaults;
        }

        return inputsData;
    }

    function branda_tracking_codes_display_selected_info( select_element ) {
        var selected_value = select_element.val();

        if ( typeof window.Branda_Helper.messages.providers[selected_value] !== 'undefined' ) {
            var provider = window.Branda_Helper.messages.providers[selected_value],
                parent_wrapper = select_element.closest( '.branda-tracking-provider-details-wrap' );

            if ( typeof parent_wrapper !== 'undefined' ) {
                var description_area = parent_wrapper.find( '.branda-tracking-provider-short-description' ),
                    link_item = parent_wrapper.find( '.branda-tracking-provider-data-link a.data-link' );

                if ( typeof provider['description'] !== 'undefined' ) {
                   description_area.html( provider.description );
                }
    
                if ( typeof provider['info_link'] !== 'undefined' ) {
                    link_item.attr( 'href', provider['info_link'] );
                }
            }
        }

        var modal = select_element.closest( '.sui-modal.sui-active' ),
            groupIDField = modal.find( 'input.branda-tracking-codes-id[name="branda[id]"]' ),
            // When changing selected Provider we can consider that the modal is not New.
            isNew = false;// 'new' === groupIDField.val();

        bradnaSetModalTCFields( modal, isNew );
    }

    $( '.branda-tracking-provider .sui-select' ).on('change', function (e) {
        branda_tracking_codes_display_selected_info( $(this) );
    });

    $( '.branda-tracking-provider .sui-select' ).each(function() {
        branda_tracking_codes_display_selected_info( $(this) );
    });

});
