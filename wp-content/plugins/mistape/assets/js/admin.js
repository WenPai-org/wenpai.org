(function ($) {

    var Deco_Mistape_Admin = {

        init: function () {

            // remove tab query arg
            $('input[name="_wp_http_referer"]').val(function (i, v) {
                return v.replace(/&tab=.*/i, '');
            });

            // mail recipient switch
            $('input[id^="mistape_email_recipient_type-"]').change(function () {
                var checked = $('input[id^="mistape_email_recipient_type-"]:checked').val();
                $.when($('div[id^="mistape_email_recipient_list-"]:not([id$=checked])').slideUp('fast')).then(function () {
                    $('#mistape_email_recipient_list-' + checked).slideDown('fast');
                });
            });

            // shortcode option
            $('#mistape_shortcode_option').change(function () {
                if ($(this).is(':checked')) {
                    $('#mistape_shortcode_help').slideDown('fast');
                } else {
                    $('#mistape_shortcode_help').slideUp('fast');
                }
            });

            // caption format switch
            $('#mistape_register_shortcode, input[id^="mistape_caption_format-"]').change(function () {
                if ($('#mistape_register_shortcode').is(':checked') || $('input[id^="mistape_caption_format-"]:checked').val() === 'image') {
                    $('#mistape_caption_image').slideDown('fast');
                } else {
                    $('#mistape_caption_image').slideUp('fast');
                }
            });

            // caption text mode switch
            $('input[id^="mistape_caption_text_mode-"]').change(function () {
                var $textarea = $('#mistape_custom_caption_text');
                if ($(this).val() == 'default') {
                    $textarea.data('custom', $textarea.val());
                    $textarea.val($textarea.data('default'));
                    $textarea.attr('disabled', true);
                } else {
                    if ($textarea.data('custom')) {
                        $textarea.val($textarea.data('custom'));
                    }
                    $textarea.attr('disabled', false);
                }
            });

            $('input[id^="mistape_caption_text_mode_for_mobile-"]').change(function () {
                var $textarea = $('#mistape_custom_caption_text_for_mobile');
                if ($(this).val() == 'default') {
                    $textarea.data('custom', $textarea.val());
                    $textarea.val($textarea.data('default'));
                    $textarea.attr('disabled', true);
                } else {
                    if ($textarea.data('custom')) {
                        $textarea.val($textarea.data('custom'));
                    }
                    $textarea.attr('disabled', false);
                }
            });

            // dialog preview

            $('#preview-dialog-btn').on('click', function (e) {
                e.preventDefault(e);
                var mode = $('.dialog_mode_choice:checked').val();
                Deco_Mistape_Admin.previewDialog(mode);
            });

            // Tab switching without reload
            $('.nav-tab').click(function (ev) {
                ev.preventDefault();
                if (!$(this).hasClass('nav-tab-active')) {
                    $(this).siblings().removeClass('nav-tab-active');
                    $(this).addClass('nav-tab-active');
                    $('.mistape-tab-contents').hide();
                    $('#' + $(this).data('bodyid')).show();
                    Deco_Mistape_Admin.ChangeUrl($(this).text(), $(this).attr('href'));
                }
            });

            // Tooltip image
            $('.hover-image').hover(function () {
                if (!$(this).find('.tooltip-img').length) {
                    var imgSrc = $(this).data('img-url');
                    $(this).append('<img class="tooltip-img" src="' + imgSrc + '"/>');
                }
                $(this).addClass("tooltip-show");
            }, function () {
                $(this).removeClass("tooltip-show");
            });

            //Color picker
            var colorPickerInp = $('.mistape_color_picker');
            colorPickerInp.wpColorPicker({
                change: function () {
                    setTimeout(function () {
                        Deco_Mistape_Admin.colorScheme($('.mistape_color_picker').val());
                    }, 10);

                }
            });
            Deco_Mistape_Admin.colorScheme(colorPickerInp.val());
        },

        previewDialog: function (mode) {
            var currentMode = $('#mistape_dialog').data('mode');
            // request updated dialog if mode was changed
            if (mode == currentMode) {
                $('#mistape_dialog').css('display', 'flex');
                decoMistape.dlg.toggle();
            }
            else {

                $('#preview-dialog-spinner').addClass('is-active');
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: decoMistape.ajaxurl,
                    data: {
                        action: 'mistape_preview_dialog',
                        mode: mode
                    },
                    success: function (response) {
                        if (response.success === true) {
                            $('#mistape_dialog').replaceWith(response.data).css('display', 'flex');
                            decoMistape.initDialogFx();
                            decoMistape.dlg.toggle();
                        }
                    },
                    complete: function () {
                        $('#preview-dialog-spinner').removeClass('is-active');
                    }
                })
            }
        },

        ChangeUrl: function (title, url) {
            if (typeof (history.pushState) != "undefined") {
                var obj = {Title: title, Url: url};
                history.pushState(obj, obj.Title, obj.Url);
            }
        },

        colorScheme: function (color) {
            //var styleBox = $('.mistape-styles');
            //var output = '<style type="text/css">' +
            //                '.mistape-test, .mistape_mistake_inner {color: ' + color + ' !important;}' +
            //               '#mistape_dialog h2::before, #mistape_dialog .mistape_action, .mistape-letter-back {background-color: ' + color + ' !important; }' +
            //               '#mistape_reported_text:before, #mistape_reported_text:after {border-color: ' + color + ' !important;}' +
            //                '.mistape-letter-front .front-left {border-left-color: ' + color + ' !important;}' +
            //                '.mistape-letter-front .front-right {border-right-color: ' + color + ' !important;}' +
            //                '.mistape-letter-front .front-bottom, .mistape-letter-back > .mistape-letter-back-top, .mistape-letter-top {border-bottom-color: ' + color + ' !important;}' +
            //                '.mistape-logo svg, .select-logo__img svg {fill: ' + color + ' !important;}' +
            //            '</style>';

            //if (!styleBox.length) {
            //    $('body').prepend('<div class="mistape-styles"></div>');
            //    styleBox = $('.mistape-styles');
            //}

            //styleBox.html(output);

            var css = '.mistape-test, .mistape_mistake_inner {color: ' + color + ' !important;}' +
                '#mistape_dialog h2::before, #mistape_dialog .mistape_action, .mistape-letter-back {background-color: ' + color + ' !important; }' +
                '#mistape_reported_text:before, #mistape_reported_text:after {border-color: ' + color + ' !important;}' +
                '.mistape-letter-front .front-left {border-left-color: ' + color + ' !important;}' +
                '.mistape-letter-front .front-right {border-right-color: ' + color + ' !important;}' +
                '.mistape-letter-front .front-bottom, .mistape-letter-back > .mistape-letter-back-top, .mistape-letter-top {border-bottom-color: ' + color + ' !important;}' +
                '.mistape-logo svg, .select-logo__img svg {fill: ' + color + ' !important;}',
                head = document.head || document.getElementsByTagName('head')[0],
                style = document.createElement('style');

            style.type = 'text/css';
            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }

            head.appendChild(style);

        }

    };

    $(document).ready(Deco_Mistape_Admin.init);

})(jQuery);