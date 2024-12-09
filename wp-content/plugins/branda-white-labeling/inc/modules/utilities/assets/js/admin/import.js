jQuery( document ).ready( function ( $ ) {
	// On file select.
	$( '.module-utilities-import-php #simple_options_import_file' ).on( 'change', function () {
		var filename = $( this ).val();
		var target = $( this ).closest( 'form' );
		if ( filename.match( /.json$/ ) ) {
			// Enable button only if selected file is json.
			$( '.branda-import-import', target ).removeAttr( 'disabled' );
		} else {
			// Disable button if not json.
			$( '.branda-import-import', target ).prop( 'disabled', true );
		}
	} );
} );
