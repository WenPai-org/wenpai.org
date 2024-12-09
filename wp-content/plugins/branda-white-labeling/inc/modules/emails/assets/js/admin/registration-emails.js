jQuery( document ).ready( function( $ ) {
    /**
     * reset section
     */
    $('.branda-registration-emails-reset').on( 'click', function() {
        var data = {
            action: 'branda_registration_emails_reset',
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
});
