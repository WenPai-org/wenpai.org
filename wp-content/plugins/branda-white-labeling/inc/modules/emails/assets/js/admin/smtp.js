jQuery( document ).ready( function( $ ) {
    /**
     * Deactivate Plugin
     */
    $('.branda-section-plugins button.sui-button').on( 'click', function() {
        var data = {
            action: $(this).closest('table').data('action'),
            mode: $(this).data('mode'),
            _wpnonce: $(this).closest('tr').data('nonce'),
            id: $(this).closest('tr').data('plugin' )
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
