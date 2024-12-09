(function($){
	// Save settings.
	$( '.branda-module-save-email-logs-settings' ).on( 'click', function () {
		var form = $( '.module-branda-form.module-emails-email-logs-php' );
		SUI.brandaSaveSettings.call( this, $, form )
	});

	// Open Filters.
	$( '.sui-pagination-wrap .branda-open-inline-filter' ).on( 'click', function(e) {
		var $this    = $( this ),
			$wrapper = $this.closest( '.sui-pagination-wrap' ),
			$button  = $wrapper.find( '.sui-button-icon' ),
			$filters = $this.closest( '.branda-actions-bar' ).next( '.sui-pagination-filter' );

		$button.toggleClass( 'sui-active' );
		$filters.toggleClass( 'sui-open' );

		e.preventDefault();
		e.stopPropagation();
	});

	// Clear filters.
	$( '.branda-entries-clear-filter' ).on( 'click', function(e) {
		var $form = $( this ).closest('form');

		e.preventDefault();
		$form.find('.branda-filter-field').val( '' ).trigger('change');
	});

	// Toggle Clear Button.
	$( '.sui-pagination-filter .branda-filter-field' ).on( 'change apply.daterangepicker', toggleClearButton ).trigger('change');
	function toggleClearButton( e ) {
		let $form = $( this ).closest( 'form' );
		let $clearFilter = $form.find( '.branda-entries-clear-filter' );
		let is_one_not_empty = $form.find( '.branda-filter-field' ).map(function(){return $(this).val();}).get().some((element) => element !== '');

		if ( is_one_not_empty ) {
			$clearFilter.removeAttr( 'disabled' );
		} else {
			$clearFilter.prop( 'disabled', true );
		}
	}

	// Remove filter.
	$( '.sui-active-filter-remove' ).on( 'click', function(e) {
		let $this    = $( this ),
			possibleFilters = [ 'order_by', 'keyword', 'recipient', 'from_email', 'date_range' ],
			currentFilter = $this.data( 'filter' ),
			re = new RegExp( '&' + currentFilter + '=[^&]*', 'i' );

		if ( -1 !== possibleFilters.indexOf( currentFilter ) ) {
			location.href = location.href.replace( re, '' );
		}
	});

	setTimeout(
		function() {
			// Datepicker.
			$( 'input.branda-filter-date' ).daterangepicker({
				autoUpdateInput: false,
				autoApply: true,
				alwaysShowCalendars: true,
				ranges: window.branda_datepicker_ranges,
				locale: window.branda_datepicker_locale
		}) }, 3000 );

	$( 'input.branda-filter-date' ).on( 'apply.daterangepicker', function( ev, picker ) {
		$( this ).val( picker.startDate.format( 'MM/DD/YYYY' ) + ' - ' + picker.endDate.format( 'MM/DD/YYYY' ) );
	});

    /**
     * Delete item
     */
    $('.branda-email-logs-delete').on( 'click', function() {
        if ( 'bulk' === $(this).data('id' ) ) {
            return false;
        }
        var data = {
            action: 'branda_email_logs_delete',
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

    /**
     * Bulk: confirm
     */
    $( '.branda-email-logs-delete[data-id=bulk]').on( 'click', function() {
        var data = {
            action: 'branda_email_logs_delete_bulk',
            _wpnonce: $(this).data('nonce'),
            ids: [],
        }
        $('input[type=checkbox]:checked', $('#branda-email-logs-table' ) ).each( function() {
            data.ids.push( $(this).val() );
        });
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                SUI.openFloatNotice( response.data.message );
            }
        });
        return false;
    });

	// Disable/Enable Apply button
	$( '#branda-email-logs-table .check-column input, .branda-box-actions select[name="branda_action"]' ).on( 'change', function() {
		var checkboxCount = $( '#branda-email-logs-table .check-column input:checked' ).length,
			applyButtons = $(  'button.branda-bulk-delete' );

		applyButtons.each( function() {
			var action = $( this ).closest( 'form' ).find( 'select[name="branda_action"] option:selected' ).val();

			if ( '-1' !== action && checkboxCount ) {
				$( this ).removeAttr( 'disabled' );
			} else {
				$( this ).prop( 'disabled', true );
			}
		});
	});

	// Change chevron down(up)
	$( '#branda-email-logs-table tr.sui-accordion-item' ).on( 'click', function() {
		var chevron = $(this).find( '.sui-accordion-open-indicator > span' );
		if (chevron.hasClass('sui-icon-chevron-down')) {
			chevron.removeClass('sui-icon-chevron-down').addClass('sui-icon-chevron-up');
		} else {
			chevron.addClass('sui-icon-chevron-down').removeClass('sui-icon-chevron-up');
		}
	});

})( jQuery );
