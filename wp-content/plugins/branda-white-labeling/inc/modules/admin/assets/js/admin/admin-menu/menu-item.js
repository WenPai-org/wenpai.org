(function ($) {
	window.Branda = window.Branda || {};

	/**
	 * @param id
	 * @param model {Branda.Menu_Item_Model}
	 * @constructor
	 */
	var Menu_Item_Elements = function (id, model) {
		var template = model.has_submenu()
			? wp.template('menu-builder-field')
			: wp.template('submenu-builder-field'),
			template_args = $.extend({}, {id: id}, model.get_values());

		var markup = template(template_args);

		this.root = $(markup);
		this.submenu_container = $('.branda-submenu-container', this.root);
		this.item_header = $('> .sui-accordion-item-header', this.root);
		this.item_body = $('> .sui-accordion-item-body', this.root);

		this.duplicate_button = $('.branda-custom-admin-menu-duplicate', this.item_header);
		this.is_invisible_checkbox = $('.branda-custom-admin-menu-is-invisible input[type=checkbox]', this.item_header);
		this.is_hidden_checkbox = $('.branda-custom-admin-menu-is-hidden input[type=checkbox]', this.item_header);
		this.is_selected_checkbox = $('.branda-custom-admin-menu-is-selected', this.item_header);
		this.icon_container = $('.branda-menu-item-icon-container', this.item_header);
		this.item_header_title = $('.branda-menu-item-title', this.item_header);
		this.remove_button = $('.branda-custom-admin-menu-remove', this.item_header);
		this.undo_button = $('.branda-custom-admin-menu-undo', this.item_header);
		this.item_controls = $('.branda-custom-admin-menu-controls', this.item_header);
		this.dropdown_anchor = $('.sui-dropdown-anchor', this.item_controls);

		this.settings_container = $('.branda-menu-item-settings-container', this.item_body);
		this.title_form_field = $('[name="title"]', this.item_body).closest('.sui-form-field');
	};

	window.Branda.Menu_Item = function (id, item, container) {
		// Public methods
		this.get_data = get_data;
		this.get_el = get_el;
		this.get_id = get_id;
		this.replicate_data = replicate_data;
		this.duplicate = duplicate;
		this.make_invisible = make_invisible;
		this.make_visible = make_visible;
		this.hide = hide;
		this.unhide = unhide;
		this.is_hidden = is_hidden;
		this.is_invisible = is_invisible;
		this.is_main = is_main;
		this.is_selected = is_selected;
		this.open = open;
		this.validate = validate;

		var menu_item_id = id;
		/**
		 * @type {Branda.Menu_Item_Model}
		 */
		var model = new Branda.Menu_Item_Model(item);
		/**
		 * @type {Branda.Menu_Item}
		 */
		var self = this;
		/**
		 * @type {Branda.Menu_Icon_Uploader}
		 */
		var uploader;

		/**
		 * If this is a main menu item then we need a submenu
		 * @type {Branda.Menu_Item_Container}
		 */
		var submenu;

		var elements = new Menu_Item_Elements(id, model);

		// Set up the current menu item
		init();

		function init() {
			init_submenu();
			init_dashicon_select();
			init_side_tabs();
			init_icon_uploader();
			init_header_icon();
			init_accordion();
			init_model_change_listener();
			init_field_value_listener();
			init_is_invisible_checkbox();
			init_is_hidden_checkbox();
			init_duplicate_button();
			init_tabs();
			init_item_selection_checkbox();
			init_select_all_change_listener();
			init_remove_button();
			init_undo_button();
			init_controls_dropdown();
		}

		function init_dashicon_select() {
			var $input = $('[name="dashicon"]', elements.settings_container),
				selected = model.get_dashicon(),
				value = selected.replace('dashicons-', '');

			new Branda.Dashicon_Picker($input, value);

			$input.off().on('change', handle_dashicon_value_change);
		}

		function handle_dashicon_value_change() {
			var $input = $(this),
				name = $input.prop('name');

			model.set(name, 'dashicons-' + $input.val());
		}

		function init_accordion() {
			elements.item_header.on('click', function () {
				$(this).closest('.sui-accordion-item').toggleClass(accordion_open_class());
			});
		}

		function init_side_tabs() {
			// Look at the model values to add the 'checked' attribute to the correct radio input and switch to the corresponding tab
			$('label.sui-tab-item input[type="radio"]', elements.settings_container).each(function () {
				var $radio = $(this),
					param_value = model.get($radio.prop('name'));

				if (param_value === $radio.val()) {
					$radio.prop('checked', true);

					switch_side_tab.apply(
						$(this).closest('label.sui-tab-item')
					);
				}
			});

			// Listen for further events
			$('.sui-side-tabs label.sui-tab-item', elements.settings_container).on('click', switch_side_tab);
		}

		function init_icon_uploader() {
			uploader = new Branda.Menu_Icon_Uploader(
				$('.branda-admin-menu-icon-uploader', elements.settings_container),
				model.get_icon_image_id()
			);

			$(uploader).on('icon-image-id-changed', function (event, new_image_id) {
				model.set_icon_image_id(new_image_id);
			});
			$(uploader).on('icon-attachment-loaded', function (event, attachment) {
				if (!attachment || !model.is_icon_type_upload()) {
					return;
				}

				set_item_header_icon(
					build_header_icon_from_attachment(attachment)
				);
			});
		}

		function build_header_icon_from_attachment(attachment) {
			var url = attachment.get('url');

			return build_image_icon_el(url);
		}

		function init_header_icon() {
			var $icon = false;

			if (model.is_icon_type_dashicon() && model.get_dashicon()) {
				$icon = build_dashicon_el(model.get_dashicon());
			} else if (model.is_icon_type_upload() && uploader.is_attachment_loaded()) {
				$icon = build_header_icon_from_attachment(uploader.get_attachment());
			} else if (model.is_icon_type_svg() && model.get_icon_svg()) {
				var svg = model.get_icon_svg();
				$icon = build_masked_icon_el(svg.trim());
			} else if (model.is_icon_type_url() && model.get_icon_url()) {
				$icon = build_image_icon_el(model.get_icon_url());
			} else {
				$icon = $('<i></i>');
			}

			set_item_header_icon($icon);
		}

		function set_item_header_icon($icon_el) {
			elements.icon_container.html($icon_el ? $icon_el : '');
		}

		function build_dashicon_el(dashicon) {
			return $('<i></i>')
				.addClass('dashicons-before')
				.addClass(dashicon);
		}

		function build_masked_icon_el(mask_image) {
			var value = 'url("' + mask_image + '")';

			return $('<div></div>')
				.addClass('branda-custom-admin-menu-mask')
				.css('-webkit-mask-image', value)
				.css('mask-image', value);
		}

		function build_image_icon_el(url) {
			var value = 'url("' + url + '")';

			return $('<div></div>')
				.addClass('branda-custom-admin-menu-img')
				.css('background-image', value);
		}

		function init_submenu() {
			if (is_main() && !submenu) {
				submenu = new Branda.Menu_Item_Container();
				var submenu_data = model.get_submenu();

				for (var key in submenu_data) {
					if (!submenu_data.hasOwnProperty(key)) {
						continue;
					}

					var submenu_params = submenu_data[key];
					submenu.add(new Branda.Menu_Item(key, submenu_params, submenu));
				}

				// Add the submenu markup in the submenu tab
				elements.submenu_container.append(submenu.get_el());
			}
		}

		function is_main() {
			return model.has_submenu();
		}

		function get_data() {
			if (is_main()) {
				model.set_submenu(submenu.get_data());
			}

			return model.get_values();
		}

		function get_el() {
			return elements.root;
		}

		function get_id() {
			return menu_item_id;
		}

		function replicate_data() {
			/**
			 * Make exact copy of current state
			 */
			var replicated = $.extend({}, model.get_values(), {
				is_native: false,
				was_native: false,
				title: model.get_title() + ' ' + branda_custom_menu.duplicate_postfix
			});

			/**
			 * Now change values
			 */
			// Copy defaults as actual values
			Object.keys(replicated).map(function (key) {
				if (
					replicated.hasOwnProperty(key)
					&& key.includes('_default')
				) {
					var actual_key = key.replace('_default', '');
					if (
						replicated.hasOwnProperty(actual_key) // A key exists without the '_default' part
						&& !replicated[actual_key] // but the value against it is empty
					) {
						replicated[actual_key] = replicated[key];
					}
					delete replicated[key];
				}
			});
			// Do the same stuff to submenu items
			if (is_main()) {
				replicated.submenu = submenu.replicate_data();
			}

			return replicated;
		}

		function duplicate() {
			elements.duplicate_button.trigger('click');
		}

		function make_invisible() {
			elements.is_invisible_checkbox.each( function() {
				var $parent = $(this).closest('.sui-accordion-item');

				if ( ! $parent.hasClass( 'branda-menu-item-hidden' ) ) {
					$(this).prop('checked', true).trigger('change');
				}
			});
		}

		function make_visible() {
			elements.is_invisible_checkbox.prop('checked', false).trigger('change');
		}

		function hide() {
			elements.is_hidden_checkbox.each( function() {
				var $parent = $(this).closest('.sui-accordion-item');

				if ( ! $parent.hasClass( 'branda-menu-item-invisible' ) ) {
					$(this).prop('checked', true).trigger('change');
				}
			});
		}

		function unhide() {
			elements.is_hidden_checkbox.prop('checked', false).trigger('change');
		}

		/**
		 * @returns {Branda.Menu_Item}
		 */
		function create_duplicate_object() {
			var params = replicate_data(),
				duplicate_id = Branda.Menu_Utils.generate_random_menu_item_id();

			return new Branda.Menu_Item(duplicate_id, params, container);
		}

		function init_duplicate_button() {
			elements.duplicate_button.on('click', function (e) {
				e.preventDefault();
				e.stopPropagation();

				include_duplicate(create_duplicate_object());
			});
		}

		/**
		 * @param duplicate {Branda.Menu_Item}
		 */
		function include_duplicate(duplicate) {
			duplicate.get_el().insertAfter(elements.root);

			$(self).trigger('menu-item-duplicated', [duplicate]);
		}

		function init_is_invisible_checkbox() {
			elements.is_invisible_checkbox.on('change', function () {
				var $accordion_item = $(this).closest('.sui-accordion-item'),
					is_checked = $(this).is(':checked');

				$accordion_item
					.removeClass(accordion_open_class())
					.toggleClass('branda-menu-item-invisible', is_checked);

				$(self).trigger('visibility-change');

				handle_value_change.apply(this);
			});
		}

		function init_is_hidden_checkbox() {
			elements.is_hidden_checkbox.on('change', function () {
				var $accordion_item = $(this).closest('.sui-accordion-item'),
					is_checked = $(this).is(':checked');

				$accordion_item
					.removeClass(accordion_open_class())
					.toggleClass('branda-menu-item-hidden', is_checked);

				$(self).trigger('visibility-change');

				handle_value_change.apply(this);
			});
		}

		function init_field_value_listener() {
			$('input[type="text"],input[type="url"],input[type="radio"],textarea', elements.settings_container)
				.on('input propertychange change', _.debounce(handle_value_change, 500));
		}

		function handle_value_change() {
			var $input = $(this),
				name = $input.prop('name'),
				val = $input.is('[type="checkbox"]')
					? $input.is(':checked') ? 1 : ''
					: $input.val();

			model.set(name, val);
		}

		function init_model_change_listener() {
			$(model).on('value-change', function () {
				init_header_icon();
				update_header_title_element();
			});
		}

		function update_header_title_element() {
			elements.item_header_title.text(model.get_title());
		}

		function init_tabs() {
			$('.sui-tabs [data-tabs] > div', elements.root).on('click', function () {
				var $tab = $(this),
					$container = $tab.closest('.sui-tabs'),
					$tabs = $container.children('[data-tabs]'),
					$panes = $container.children('[data-panes]'),
					index = $tabs.children().index($tab);

				$tabs.children('.active').removeClass('active');
				$panes.children('.active').removeClass('active');

				$tab.addClass('active');
				$panes.children().eq(index).addClass('active');
			});
		}

		function switch_side_tab() {
			var $tab = $(this),
				content = $('input[type="radio"]', $tab).data('tabMenu'),
				$container = $tab.closest('.sui-side-tabs');

			$('> .sui-tabs-menu > .active', $container).removeClass('active');
			$('> .sui-tabs-content > .active', $container).removeClass('active');
			$tab.addClass('active');
			if (content) {
				$container.find('[data-tab-content="' + content + '"]').addClass('active');
			}
		}

		function init_select_all_change_listener() {
			$(container).on('select-all-status-changed', function (event, select_all) {
				elements.is_selected_checkbox.prop('checked', select_all);
			});
		}

		function init_item_selection_checkbox() {
			elements.is_selected_checkbox.on('change', function () {
				$(self).trigger('selection-change');
			});
		}

		function is_selected() {
			return elements.is_selected_checkbox.is(':checked');
		}

		function is_hidden() {
			return elements.is_hidden_checkbox.is(':checked');
		}

		function is_invisible() {
			return elements.is_invisible_checkbox.is(':checked');
		}

		function validate() {
			var title = model.get_title() + '',
				error = '<span class="hidden sui-error-message">' + branda_custom_menu.title_missing_error + '</span>',
				$field_container = elements.title_form_field,
				$field = $field_container.find('.sui-form-control'),
				is_valid = true;

			if ( is_hidden() || is_invisible() ) {
				// Hidden items should always be considered valid
				return true;
			}

			remove_title_error($field_container);
			$field.on('focus', function () {
				remove_title_error($field_container);
			});

			if (title.trim() === '') {
				$field_container.addClass('sui-form-field-error');
				$(error).insertAfter($field);
				is_valid = false;
			}

			return validate_submenu() && is_valid;
		}

		function remove_title_error($field_container) {
			$('.sui-error-message', $field_container).remove();
			$field_container.removeClass('sui-form-field-error');
		}

		function validate_submenu() {
			if (!is_main()) {
				return true;
			}

			return submenu.validate();
		}

		function init_remove_button() {
			elements.remove_button.on('click', function () {
				remove();
			});
		}

		function remove() {
			var position = elements.root.index();
			elements.root.remove();

			$(self).trigger('menu-item-removed', [position]);
		}

		function accordion_open_class() {
			return 'sui-accordion-item--open';
		}

		function is_open() {
			return elements.root.hasClass(accordion_open_class());
		}

		function open() {
			var open_class = accordion_open_class();

			elements.root.removeClass(open_class).addClass(open_class);
		}

		function init_undo_button() {
			var dirty_class = 'branda-menu-item-dirty';

			$(model).on('value-change', function () {
				var is_dirty = !_.isEqual(item, model.get_values());
				elements.root.toggleClass(dirty_class, is_dirty);
			});

			elements.undo_button.on('click', function (e) {
				e.preventDefault();
				e.stopPropagation();

				undo();
				elements.root.removeClass(dirty_class);
			});
		}

		function undo() {
			var fresh = new Branda.Menu_Item(get_id(), item, container);

			// Include a fresh copy
			include_duplicate(fresh);
			// and remove the old one
			remove();

			if (is_open()) {
				fresh.open();
			}
		}

		function init_controls_dropdown() {
			maybe_show_controls_as_dropdown();

			Branda.Window_Resize_Manager.on_resize(maybe_show_controls_as_dropdown);

			elements.dropdown_anchor.off().on('click', function (e) {
				e.preventDefault();
				e.stopPropagation();

				$(this).closest('.sui-dropdown').toggleClass('open');
			});
		}

		function maybe_show_controls_as_dropdown() {
			elements.item_controls.toggleClass('sui-dropdown', is_small_screen());
		}

		function is_small_screen() {
			return $(window).width() < 783;
		}
	};
})(jQuery);
