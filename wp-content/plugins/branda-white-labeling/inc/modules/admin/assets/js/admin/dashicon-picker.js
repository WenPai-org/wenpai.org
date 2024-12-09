jQuery(document).ready(function ($) {
	window.Branda = window.Branda || {};

	window.Branda.Dashicon_Picker = function ($input, icon) {
		var $el;

		init();

		function init() {
			init_el();
			init_dashicon_click_listener();
			init_search();

			set_icon(icon);
			insert_el();
		}

		function init_el() {
			var template = wp.template('dashicon-picker'),
				markup = template({});

			$el = $(markup);
		}

		function insert_el() {
			$el.insertAfter($input);
		}

		function init_dashicon_click_listener() {
			get_all_icons().on('click', function () {
				var icon_code = $(this).data('code');

				set_icon(icon_code);
			});
		}

		function init_search() {
			$('.sui-form-control', $el).on('input propertychange', _.debounce(function () {
				var $input_field = $(this),
					keyword = $input_field.val(),
					$matches = $('[data-code*="' + keyword + '"]', $el),
					$groups_and_icons = $('.branda-dashicon-picker-group, .dashicons', $el);

				if ('' === keyword) {
					$groups_and_icons.show();
					return;
				}

				$groups_and_icons.hide();
				$matches.each(function () {
					$(this).show();
					$(this).closest('.branda-dashicon-picker-group').show();
				});
			}, 100));
		}

		function set_icon(code) {
			// Set input val
			$input.val(code).trigger('change');

			// Set icon as active
			get_all_icons().removeClass('active');
			get_icon(code).addClass('active');
		}

		function get_icon(code) {
			return $('[data-code="' + code + '"]', $el);
		}

		function get_all_icons() {
			return $('.dashicons', $el);
		}
	}
});
