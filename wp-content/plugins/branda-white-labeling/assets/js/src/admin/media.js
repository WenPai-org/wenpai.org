/**
 * Ultimate Branding
 * http://wpmudev.com/
 *
 * Copyright (c) 2017-2018 Incsub
 * Licensed under the GPLv2+ license.
 *
 * Native WP media for custom login image module
 */

/* global window, jQuery, wp, ajaxurl, wp_media_post_id */

function ub_bind_reset_image( obj ) {
    var container = jQuery(obj).closest('.simple-option');
    if ( container.hasClass( 'simple-option-gallery' ) ) {
        jQuery(obj).closest('.sui-upload').detach();
        return;
    }
    jQuery('.attachment-id', container ).val( null ).trigger('change');
    jQuery('.sui-upload', container).removeClass( 'sui-has_file' );
}

function ub_media_bind( obj, event ) {
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
                    ub_media_bind( this, event );
                });
                jQuery( ".images .image-reset", target ).on( 'click', function() {
                    ub_bind_reset_image( this );
                    return false;
                });
            }
        }

    });
    // Finally, open the modal
    file_frame.open();
}

jQuery( window.document ).ready( function( $ ) {
    $(".simple-option-media .images, .simple-option-gallery .images").each( function() {
        var ub_template = wp.template( 'simple-options-media' );
        var target = $( this );
        var data = window['_'+target.prop('id')];
        $.each( data.images, function(){
            target.append( ub_template( this ) );
            $( ".image-wrapper .button-select-image", target ).on( 'click', function( event ) {
                ub_media_bind( this, event );
            });
            $( ".images .image-reset", target ).on( 'click', function() {
                ub_bind_reset_image( this );
                return false;
            });
        });
    });
    $(".simple-option-media .image-reset, .simple-option-gallery .image-reset").on("click", function(){
        ub_bind_reset_image( this );
        return false;
    });
    // Restore the main ID when the add media button is pressed
    $( 'a.add_media' ).on( 'click', function() {
        wp.media.model.settings.post.id = wp_media_post_id;
    });
    $('.branda-add-image').on('click', function() {
        var ub_template = wp.template( 'simple-options-media' );
        var taget = $( '.images', $(this).parent());
    });
});

