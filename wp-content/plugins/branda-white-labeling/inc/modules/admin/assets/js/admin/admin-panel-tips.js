jQuery( document ).ready( function () {
	// Dismiss or Hide admin panel tip.
	jQuery( '.admin-panel-tips .apt-action a' ).click( function () {
		var parent1 = jQuery( this ).parent();
		var parent2 = parent1.parent();
		var args = {
			action: 'branda_admin_panel_tips',
			id: parent2.data( 'id' ),
			nonce: parent1.data( 'nonce' ),
			user_id: parent2.data( 'user-id' )
		};
		jQuery( this ).parent().html( branda_admin_panel_tips.saving );
		jQuery.post( ajaxurl, args, function ( response ) {
			parent2.hide();
		} );
		return false;
	} );

	// Date picker for expiry date.
	if ( 'function' === typeof jQuery.fn.datepicker ) {
		jQuery( '#till .datepicker' ).each( function () {
			jQuery( this ).datepicker( {
				altFormat: 'yy-mm-dd',
				altField: '#' + jQuery( this ).data( 'alt' ),
				selectOtherMonths: true,
				showButtonPanel: true,
				minDate: jQuery( this ).data( 'min' )
			} );
		} );
	}
} );
