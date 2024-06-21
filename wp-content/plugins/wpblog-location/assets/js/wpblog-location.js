jQuery( function($) {
    $( 'input[name="ip_address_format"]' ).on( 'click', function() {
        if ( 'ip_address_format_custom_radio' !== $(this).attr( 'id' ) )
            $( 'input[name="ip_address_format_custom"]' ).val( $( this ).val() );
    });

    $( 'input[name="ip_address_format_custom"]' ).on( 'input', function() {
        $( '#ip_address_format_custom_radio' ).prop( 'checked', true );
    } );

    $( 'input[name="ip_address_custom_for_admin"]' ).on( 'click', function() {
        if ( 'ip_address_custom_for_admin_radio' !== $(this).attr( 'id' ) )
            $( 'ip_address_custom_for_admin_custom"]' ).val( $( this ).val() );
    });

    $( 'input[name="ip_address_custom_for_admin_cu"]' ).on( 'input', function() {
        $( '#ip_address_custom_for_admin_radio' ).prop( 'checked', true );
    } );
} );