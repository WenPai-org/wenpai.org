/*global jQuery, document, window, wp, simple_settings_vars*/
jQuery(document).ready(function ($) {
    'use strict';

    // Setup color picker
    if ($('.simple-settings-color-picker').length) {
        $('.simple-settings-color-picker').wpColorPicker();
    }

    // Setup select2
    if ($('.simple-settings-select2').length) {
        $('.simple-settings-select2').select2();
    }

    // Setup CodeMirror
    if ($('.simple-settings-html').length) {
        $('.simple-settings-html').each(function (index, elem) {
            wp.CodeMirror.fromTextArea(elem, {
                lineNumbers: true,
                mode: 'text/html',
                showCursorWhenSelecting: true
            });
        });
    }

    // Setup tooltips
    $('.simple-settings-help-tip').tooltip({
        content: function () {
            return $(this).prop('title');
        },
        tooltipClass: 'simple-settings-ui-tooltip',
        position: {
            my: 'center top',
            at: 'center bottom+10',
            collision: 'flipfit'
        },
        hide: {
            duration: 200
        },
        show: {
            duration: 200
        }
    });

    // Setup uploaders
    if ($('.' + simple_settings_vars.func + '_settings_upload_button').length) {
        var file_frame;

        $('body').on('click', '.' + simple_settings_vars.func + '_settings_upload_button', function (e) {
            e.preventDefault();

            var button = $(this);

            window.formfield = $(this).parent().prev();

            // If the media frame already exists, reopen it
            if (file_frame) {
                file_frame.open();
                return;
            }

            // Create the media frame
            wp.media.frames.file_frame = wp.media({
                frame: 'post',
                state: 'insert',
                title: button.data('uploader_title'),
                button: {
                    text: button.data('uploader_button_text')
                },
                multiple: false
            });

            file_frame = wp.media.frames.file_frame;

            file_frame.on('menu:render:default', function (view) {
                // Store our views in an object
                var views = {};

                // Unset default menu items
                view.unset('library-separator');
                view.unset('gallery');
                view.unset('featured-image');
                view.unset('embed');

                // Initialize the views in our object
                view.set(views);
            });

            // Run a callback on select
            file_frame.on('insert', function () {
                var selection = file_frame.state().get('selection');

                selection.each(function (attachment) {
                    attachment = attachment.toJSON();
                    window.formfield.val(attachment.url);
                });
            });

            // Open the modal
            file_frame.open();
        });

        window.formfield = '';
    }
});
