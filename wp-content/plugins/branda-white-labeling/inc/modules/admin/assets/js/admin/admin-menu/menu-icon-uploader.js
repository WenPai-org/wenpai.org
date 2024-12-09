(function ($) {
	window.Branda = window.Branda || {};

	Branda.Menu_Icon_Uploader = function ($el, image_id) {
		if ($el.hasClass('.sui-upload')) {
			return;
		}

		/**
		 * Public methods
		 */
		this.is_attachment_loaded = is_attachment_loaded;
		this.get_attachment = get_attachment;

		var self = this,
			attachment = false;

		if (!image_id) {
			show_uploader({
				id: '',
				url: '',
				filename: ''
			});
		} else {
			attachment = wp.media.attachment(image_id);
			if (is_attachment_loaded()) {
				show_uploader(template_args(attachment));
			} else {
				attachment.fetch().then(function () {
					show_uploader(template_args(attachment));
					send_attachment_loaded_event(attachment);
				});
			}
		}

		function is_attachment_loaded() {
			return attachment && !!attachment.get('filename');
		}

		function get_attachment() {
			return attachment;
		}

		function send_attachment_loaded_event(attachment) {
			setTimeout(function () {
				$(self).trigger('icon-attachment-loaded', [attachment]);
			});
		}

		function show_uploader(args) {
			var template = wp.template('admin-menu-media-uploader'),
				markup = template(args);

			$el.append($(markup));
			init_listeners();
		}

		function init_listeners() {
			$(".sui-upload-button", $el).on('click', function (event) {
				ub_media_bind(this, event);
			});
			$(".sui-upload-button--remove", $el).on('click', function () {
				ub_bind_reset_image(this);
			});
			$('.attachment-id', $el).on('change', function () {
				var new_item_id = $(this).val();
				attachment = wp.media.attachment(new_item_id);

				$(self).trigger('icon-image-id-changed', [new_item_id]);
			});
		}

		function template_args(attachment) {
			return {
				id: attachment.get('id'),
				url: attachment.get('url'),
				filename: attachment.get('filename')
			};
		}
	};
})(jQuery);
