jQuery(document).ready(function ($) {
    'use strict';
    if (typeof wp === 'undefined') {
        return;
    }
    let active_media_frame;
    if (wp.media) {
        wp.media.view.Modal.prototype.on('open', function () {
            if (wp.media.frame.content.get() !== null) {
                active_media_frame = wp.media.frame;
            }
        });
        wp.media.view.Modal.prototype.on('close', function () {
            active_media_frame = undefined;
        });
    }
    if (typeof wp.media.view.Attachment != 'undefined') {
        wp.media.view.Attachment.prototype.template = wp.media.template('exmage-attachment');
    }
    // $.extend(wp.Uploader.prototype, {
    //     success: function (file_attachment) {
    //         console.log(file_attachment);
    //     }
    // });
    $(document).on('search', '.exmage-use-url-input', function () {
        let $input = $(this);
        if (!$input.val()) {
            $input.closest('.exmage-use-url-container').find('.exmage-use-url-message').html('');
        }
    });
    $(document).on('paste', '.exmage-use-url-input', function () {
        exmage_handle_url_input($(this));
    });
    $(document).on('keydown', '.exmage-use-url-input', function (event) {
        if (event.keyCode === 13) {
            exmage_handle_url_input($(this));
            return false;
        }
    });

    $(document).on('click', '.exmage-select-existing-image', function () {
        let active_frame = wp.media.frame;
        if (active_frame) {
            if ('upload' === active_frame.content.mode()) {
                active_frame.content.mode('browse');
            }
            let $select = $(this), attachment_id = $select.data('attachment_id'),
                _state = active_frame.content.view._state;
            let selection = active_frame.state().get('selection');
            if (selection) {
                selection.reset();
                selection.add(wp.media.attachment(attachment_id));
            }
            if (_state === 'library' || _state === 'edit-attachment') {
                if (!wp.media.attachment(attachment_id).attributes.url) {
                    if (active_frame.content.get() && active_frame.content.get().collection) {
                        active_frame.content.get().collection._requery(true);
                    }
                    active_frame.trigger('library:selection:add');
                } else {
                    if (!wp.media.attachment(attachment_id).attributes.url) {
                        wp.media.attachment(attachment_id).fetch();
                    }
                }
            }
        }
    });
});