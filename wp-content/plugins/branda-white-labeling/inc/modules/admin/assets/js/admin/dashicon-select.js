jQuery(document).ready(function ($) {
	window.Branda = window.Branda || {};

	window.Branda.Dashicon_Select = function ($el, icon) {
		// Already initialized?
		if ($el.hasClass('branda-dashicon-selector')) {
			return;
		}

		// Initialize
		var self = this;
		if (icon) {
			set_icon(icon);
		}
		$el.addClass('branda-dashicon-selector');
		$el
			.on('click', '.branda-dashicons span.dashicons', handle_dashicon_click)
			.on('input propertychange', '.branda-general-icon-search', handle_dashicon_search)
			.on('click', '.branda-general-icon-clear', handle_dashicon_clear)
		;

		$('.sui-accordion-item-header', $el).off().on('click', function () {
			$(this).closest('.sui-accordion-item').toggleClass('sui-accordion-item--open');

			return false;
		});

		// Functions
		function handle_dashicon_click() {
			set_icon($(this).data('code'));

			$(self).trigger('dashicon-selected');
		}

		function set_icon(code) {
			var html = '<span class="dashicons dashicons-' + code + '"></span>';

			$('input[name][type="hidden"]', $el).val(code).trigger('change');
			$('.sui-accordion-col span', $el).html(html);
			$('.branda-dashicon-selection .branda-dashicon-preview', $el).html(html);
			$('.branda-dashicon-selection', $el).show();
		}

		function handle_dashicon_search() {
			var $input_field = $(this),
				keyword = $input_field.val(),
				$matches = $('[data-code*="' + keyword + '"]', $el);

			if ('' === keyword) {
				$('.branda-dashicons, .branda-dashicons span', $el).show();
				return;
			}

			$('.branda-dashicons, .branda-dashicons span', $el).hide();
			$matches.each(function () {
				$(this).show();
				$(this).closest('.branda-dashicons').show();
			});
		}

		function handle_dashicon_clear() {
			$('input[name][type="hidden"]', $el).val('').trigger('change');
			$('.sui-accordion-col span', $el).html('');
			$('.branda-dashicon-selection .branda-dashicon-preview', $el).html('');
			$('.branda-dashicon-selection', $el).hide();

			$(self).trigger('dashicon-cleared');
		}
	};
});
