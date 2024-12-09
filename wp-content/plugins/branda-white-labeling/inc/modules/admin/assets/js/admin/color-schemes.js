jQuery(document).ready(function($){
    $( '.branda-color-schemes-save' ).on( 'click', function() {
        var dialog = $(this).closest( '.sui-modal' );
        var data = {
            action: 'branda_color_schemes_save',
            _wpnonce: $(this).data( 'nonce' )
        };
        $('input[type=text]', dialog ).each( function() {
            data[$(this).prop('name')] = $(this).val();
        });
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                SUI.openFloatNotice( response.data.message );
            }
        });
    });
    /**
     * Reset button
     */
    $( '.branda-color-schemes-reset' ).on( 'click', function() {
        var dialog = $(this).closest( '.sui-modal' );
        var $suiPickerInputs = $( '.sui-colorpicker-input', dialog )
        $suiPickerInputs.each( function( index, element ) {
            var $suiPickerInput = $(element);
            var $suiPicker      = $suiPickerInput.closest( '.sui-colorpicker-wrap' );
            var reset_value     = $suiPickerInput.data('attribute');
            var $suiPickerColor = $suiPicker.find( '.sui-colorpicker-value span[role=button]' );
            $suiPickerInput.val( reset_value ).trigger( 'change' );
            $suiPickerColor.find( 'span' ).css({
                'background-color': reset_value
            });
        });
    });
});
