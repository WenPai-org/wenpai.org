jQuery(document).ready(function ($) {
    'use strict';
    $(document).on('click', '.exmage-migrate-button,.exmage-convert-external-button', function (event) {
        convert_attachment($(this));
    });

    function convert_attachment($button) {
        let attachment_id = $button.data('attachment_id'),
            $container = $button.closest('.exmage-external-url-container'),
            $message = $container.find('.exmage-migrate-message'),
            to_external = $button.is('.exmage-migrate-button') ? 0 : 1;
        if (!$button.hasClass('exmage-button-loading')) {
            $button.addClass('exmage-button-loading');
            $message.html('');
            $.ajax({
                url: exmage_admin_params.ajaxurl,
                type: 'POST',
                data: {
                    action: 'exmage_convert_external_image',
                    attachment_id: attachment_id,
                    to_external: to_external,
                    _exmage_ajax_nonce: exmage_admin_params._exmage_ajax_nonce,
                },
                success(response) {
                    if (response.status === 'success') {
                        // $container.find('.exmage-external-url-content').html(response.message);
                        $container.find('.exmage-external-url-content').html('');
                    } else {
                        $message.html('<span class="exmage-message-error"><span class="exmage-use-url-message-content">' + response.message + '</span></span>');
                    }
                },
                error() {
                    $message.html('<span class="exmage-message-error"><span class="exmage-use-url-message-content">An error occurs</span></span>');
                },
                complete() {
                    $button.removeClass('exmage-button-loading');
                }
            });
        }
    }

    /*Process image url*/
    $(document).on('click', '.exmage-use-url-input-multiple-add', function () {
        exmage_handle_url_input($(this).closest('.exmage-use-url-container').find('.exmage-use-url-input-multiple'));
    });
});

function exmage_handle_url_input($input) {
    let $container = $input.closest('.exmage-use-url-container'),
        $overlay = $container.find('.exmage-use-url-input-overlay'),
        $message = $container.find('.exmage-use-url-message');
    if ($overlay.hasClass('exmage-hidden')) {
        $message.html('');
        setTimeout(function () {
            let urls = $input.val();
            let is_url_valid = false, is_single = $input.is('input');
            try {
                if (is_single) {
                    let url_obj = new URL(urls);
                    if (url_obj.protocol === 'https:' || url_obj.protocol === 'http:') {
                        is_url_valid = true;
                    } else {
                        $message.html('<p class="exmage-message-error"><span class="exmage-use-url-message-content">Please enter a valid image URL</span></p>');
                    }
                } else {
                    if (urls) {
                        is_url_valid = true;
                    } else {
                        $message.html('<p class="exmage-message-error"><span class="exmage-use-url-message-content">Please enter at least a valid image URL to continue</span></p>');
                        return;
                    }
                }
            } catch (e) {
                $overlay.addClass('exmage-hidden');
                $message.html('<p class="exmage-message-error"><span class="exmage-use-url-message-content">Please enter a valid image URL</span></p>');
                return;
            }
            if (is_url_valid) {
                $overlay.removeClass('exmage-hidden');
                jQuery.ajax({
                    url: exmage_admin_params.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'exmage_handle_url',
                        urls: urls,
                        post_id: exmage_admin_params.post_id,
                        _exmage_ajax_nonce: exmage_admin_params._exmage_ajax_nonce,
                        is_single: is_single ? 1 : '',
                    },
                    success(response) {
                        let active_frame = wp.media ? wp.media.frame : '';
                        if (response.status === 'success') {
                            let details = response.details, message = '';
                            for (let i in details) {
                                if (details[i].status === 'success') {
                                    if (is_single) {
                                        message += `<p class="exmage-message-${details[i].status}"><span class="exmage-use-url-message-content">${details[i].message}</span>, ID: <a target="_blank" href="${details[i].edit_link}">${details[i].id}</a></p>`;
                                    } else {
                                        message += `<li class="exmage-message-${details[i].status}"><span class="exmage-result-url">${details[i].url} =><span class="exmage-use-url-message-content">${details[i].message}</span>, ID: <a target="_blank" href="${details[i].edit_link}">${details[i].id}</a></li>`;
                                    }
                                    if (active_frame) {
                                        let _state = active_frame.content.view._state;
                                        let selection = active_frame.state().get('selection');

                                        if ('upload' === active_frame.content.mode()) {
                                            active_frame.content.mode('browse');
                                        }
                                        if (_state === 'library' || _state === 'edit-attachment') {
                                            // let attachments = [];
                                            // attachments.push(details[i].id);
                                            // selection.map(function (attachment) {
                                            //     attachment = attachment.toJSON();
                                            //     if (attachment.id) {
                                            //         attachments.push(attachment.id);
                                            //     }
                                            // });
                                            if (selection) {
                                                selection.reset();
                                                // for (let i in attachments) {
                                                //     selection.add(wp.media.attachment(attachments[i]));
                                                // }
                                                selection.add(wp.media.attachment(details[i].id));
                                            }
                                            if (active_frame.content.get() && active_frame.content.get().collection) {
                                                active_frame.content.get().collection._requery(true);
                                            }
                                            active_frame.trigger('library:selection:add');
                                        } else {
                                            if (selection) {
                                                selection.reset();
                                                selection.add(wp.media.attachment(details[i].id));
                                            }
                                            wp.media.attachment(details[i].id).fetch();
                                        }
                                    }
                                } else {
                                    if (details[i].id) {
                                        let item_message = `<span class="exmage-use-url-message-content">${details[i].message}</span>, ID: <a target="_blank" href="${details[i].edit_link}">${details[i].id}</a>`;
                                        if (active_frame && active_frame.content.get() && active_frame.content.get().collection) {
                                            item_message = `${item_message}. <span class="exmage-select-existing-image" data-attachment_id="${details[i].id}">${exmage_admin_params.i18n_select_existing_image}</span>.`;
                                        }
                                        if (is_single) {
                                            message += `<p class="exmage-message-${details[i].status}">${item_message}</p>`;
                                        } else {
                                            message += `<li class="exmage-message-${details[i].status}"><span class="exmage-result-url">${details[i].url} =>${item_message}</li>`;
                                        }
                                    } else {
                                        if (is_single) {
                                            message += `<p class="exmage-message-${details[i].status}"><span class="exmage-use-url-message-content">${details[i].message}</span></p>`;
                                        } else {
                                            message += `<li class="exmage-message-${details[i].status}"><span class="exmage-result-url">${details[i].url} =><span class="exmage-use-url-message-content">${details[i].message}</span></li>`;
                                        }
                                    }
                                }
                            }
                            if (!is_single) {
                                message = `<ol>${message}</ol>`;
                            }
                            $message.html(message);
                        } else if (response.status === 'queue') {
                            $message.html('<p class="exmage-message-queue"><span class="exmage-use-url-message-content">' + response.message + '</span></p>');
                        } else {
                            $message.html('<p class="exmage-message-error"><span class="exmage-use-url-message-content">' + response.message + '</span></p>');
                        }
                    },
                    error() {
                        $message.html('<p class="exmage-message-error"><span class="exmage-use-url-message-content">An error occurs.</span></p>');
                    },
                    complete() {
                        $overlay.addClass('exmage-hidden');
                    }
                });
            }
        }, 1);
    }
}
