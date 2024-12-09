function branda_images_add_already_used_sites() {
	var sites = [];
	jQuery( '.branda-images-subsites .simple-option-media').each( function() {
		var id = jQuery( this ).data('blog-id');
		if ( id ) {
			sites.push( id );
		}
	});
	return sites;
}

jQuery( document ).ready( function( $ ) {
	/**
	 * init set
	 */
	$('select.branda-images-quantity').each( function() {
		var parent = $(this).closest( '.sui-row');
		var quant = $('option:selected', $('select.branda-images-quantity', parent ) );
		if ( 0 < quant.length ) {
			quant = quant.val();
			parent.data( 'previous', quant );
			$( 'input[type=number]', parent ).prop( 'max', branda_images.quants[ quant ].max );
		}
	});
	/**
	 * handle site add
	 */
	$( '#branda-images-search' ).on( 'select2:select', function (e) {
		var data = e.params.data;
	})
	$( '.branda-images-subsite-add' ).on( 'click', function() {
		var target = $('.branda-images-subsites' );
		var subsite = $( '#branda-images-search' );
		var data = subsite.SUIselect2( 'data' );
		if ( 0 === data.length ) {
			return;
		}
		/**
		 * Add row
		 */
		var template = wp.template( 'branda-images-subsite' );
		data = {
			id: data[0].id,
			subtitle: data[0].subtitle,
			title: data[0].title,
		}
		$('>.sui-notice', target )
			.hide()
			.after( template( data ) )
		;
		/**
		 * Reset SUIselect2
		 */
		subsite.val( null ).trigger( 'change' );
		/**
		 * Handle images
		 */
		var container_id = '#branda-images-subsite-container-' + data.id;
		target = $( container_id + ' .images' );
		template = wp.template( 'simple-options-media' );
		data = {
			id: 'favicon',
			image_id: 'time-'+Math.random().toString(36).substring(7),
			section_key: 'subsites',
			value: '',
			image_src: '',
			file_name: '',
			disabled: '',
			container_class: ''
		};

		if ( !window.ub_bind_reset_image_images ) {
			function ub_bind_reset_image_images( obj ) {
				var container = jQuery(obj).closest('.simple-option');
				if ( container.hasClass( 'simple-option-gallery' ) ) {
					jQuery(obj).closest('.sui-upload').detach();
					return;
				}
				jQuery('.attachment-id', container ).val( null ).trigger('change');
				jQuery('.sui-upload', container).removeClass( 'sui-has_file' );
			}
		}

		if ( !window.ub_media_bind_images ) {
			function ub_media_bind_images( obj, event ) {
				var file_frame;
				var wp_media_post_id;
				var container = jQuery(obj).closest('.sui-upload');
				var set_to_post_id = jQuery('.attachment-id', container ).val();
				SUI.closeNotice('branda-only-images');

				event.preventDefault();
				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					// Set the post ID to what we want
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					// Open frame
					file_frame.open();
					return;
				} else {
					// Set the wp.media post id so the uploader grabs the ID we want when initialised
					wp.media.model.settings.post.id = set_to_post_id;
				}

				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Select a image to upload',
					button: {
						text: 'Use this image',
					},
					library: {type: 'image'},
					multiple: false	// Set to true to allow multiple files to be selected
				});

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					var attachment = file_frame.state().get('selection').first().toJSON();
					if (attachment.hasOwnProperty('mime') && !attachment.mime.includes('image/')) {
						SUI.openInlineNotice( 'branda-only-images', ub_admin.messages.common.only_image );
						return;
					} else {
						SUI.closeNotice('branda-only-images');
					}

					// Do something with attachment.id and/or attachment.url here
					jQuery('.sui-image-preview', container ).prop( 'style', 'background-image: url(' + attachment.url + ')' );

					/**
					 * Fill focal background
					 */
					if ( 'content_background' === container.data( 'id' ) ) {
						jQuery('canvas.branda-focal', container.closest('form') )
							.css( 'backgroundImage', 'url(' + attachment.url + ')' )
						;
					}
					/**
					 * Fill SUI data
					 */
					jQuery('.sui-upload-file span', container ).html( attachment.filename );
					jQuery('.attachment-id', container).val(attachment.id).trigger('change');
					container.addClass( 'sui-has_file' );
					/**
					 * Restore the main post ID
					 */
					wp.media.model.settings.post.id = wp_media_post_id;
					/**
					 * add new
					 */
					var target = container.closest( 'div.simple-option' );
					if ( target.hasClass( 'simple-option-gallery' ) ) {
						if ( '' !== jQuery('.image-wrapper:last-child .attachment-id', target ).val() ) {
							var ub_template = wp.template( 'simple-options-media' );
							jQuery('.images', target).append( ub_template({
								id: container.data('id'),
								section_key: container.data('section_key'),
								disabled: 'disabled',
								container_class: ''
							}));
							jQuery( ".image-wrapper", target ).last().find( ".button-select-image" ).on( 'click', function( event ) {
								ub_media_bind_images( this, event );
							});
							jQuery( ".images .image-reset", target ).on( 'click', function() {
								ub_bind_reset_image_images( this );
								return false;
							});
						}
					}

				});
				// Finally, open the modal
				file_frame.open();
			}
		}

		target.append( template( data ) );
		$( '.button-select-image', target ).on( 'click', function( event ) {
			ub_media_bind_images( this, event );
		});
		$( '.image-reset', target ).on( 'click', function() {
			ub_bind_reset_image_images( this );
			return false;
		});
		$( '.branda-images-delete', container_id ).on( 'click', function() {
			$(container_id).remove();
		});
	});

	/**
	 * Delete
	 */
	$( '.branda-images-delete' ).on( 'click', function () {
		var data = {
			action: 'branda_images_delete_subsite',
			_wpnonce: $(this).data('nonce'),
			id: $(this).data('id')
		};
		$.post( ajaxurl, data, function( response ) {
			if ( response.success ) {
				window.location.reload();
			} else {
				SUI.openFloatNotice( response.data.message );
			}
		});
	});

	$('.branda-images-quantity').on( 'change', function() {
		var parent = $(this).closest( '.sui-row');
		var select = $('select.branda-images-quantity', parent );
		var quant = $('option:selected', select ).val()
		var prev = parent.data( 'previous' );
		var amount = $('.branda-images-amount', parent );
		var value = parseInt( amount.val() );
		value *= branda_images.quants[ prev ].quant;
		value /= branda_images.quants[ quant ].quant;
		amount.val( Math.floor( value ) ).prop( 'max', branda_images.quants[ quant ].max );
		parent.data( 'previous', quant );
	});

});
