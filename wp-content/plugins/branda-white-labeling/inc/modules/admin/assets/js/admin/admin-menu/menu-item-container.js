(function ($) {
	window.Branda = window.Branda || {};

	var Menu_Item_Container_Elements = function () {
		var template = wp.template('menu-item-container'),
			markup = template({});

		this.root = $(markup);
		this.builder_header = $('> .sui-box-builder-header', this.root);
		this.builder_body = $('> .sui-box-builder-body', this.root);
		this.builder_fields = $('> .sui-builder-fields', this.builder_body);
		this.add_button = $('> .sui-button-dashed', this.builder_body);

		this.select_all_checkbox = $('.branda-menu-select-all', this.builder_header);
		this.bulk_controls = $('.branda-custom-menu-bulk-controls', this.builder_header);
		this.duplicate_button = $('.branda-custom-admin-menu-duplicate', this.bulk_controls);
		this.make_invisible_button = $('.branda-custom-admin-menu-make-invisible', this.bulk_controls);
		this.make_visible_button = $('.branda-custom-admin-menu-make-visible', this.bulk_controls);
		this.hide_button = $('.branda-custom-admin-menu-hide', this.bulk_controls);
		this.unhide_button = $('.branda-custom-admin-menu-unhide', this.bulk_controls);
	};

	window.Branda.Menu_Item_Container = function () {
		// Public methods
		this.get_el = get_el;
		this.get_data = get_data;
		this.add = add;
		this.replicate_data = replicate_data;
		this.validate = validate;

		/**
		 * @type {Branda.Menu_Item[]}
		 */
		var items = [];

		/**
		 * @type {Menu_Item_Container_Elements}
		 */
		var elements;

		/**
		 * @type {Branda.Menu_Item_Container}
		 */
		var self = this;

		function get_data() {
			var data = {};

			items.forEach(function (menu_item) {
				data[menu_item.get_id()] = menu_item.get_data();
			});

			return data;
		}

		/**
		 * @param event
		 * @param duplicate {Branda.Menu_Item}
		 */
		function include_duplicate(event, duplicate) {
			// Listen for events
			hook_listeners(duplicate);
			// Insert the duplicate at the desired position
			items.splice(duplicate.get_el().index(), 0, duplicate);
			// New item added so select all should be unchecked
			set_select_all_checkbox_state(false);
		}

		function remove_item(event, position) {
			items.splice(position, 1);
		}

		function replicate_data() {
			var replicated = {};

			items.forEach(function (menu_item) {
				var new_id = Branda.Menu_Utils.generate_random_menu_item_id();

				replicated[new_id] = menu_item.replicate_data();
			});

			return replicated;
		}

		function add(item) {
			items.push(item);
			hook_listeners(item);
		}

		function hook_listeners(item) {
			var $item = $(item);

			$item.on('menu-item-duplicated', include_duplicate);
			$item.on('menu-item-removed', remove_item);
			$item.on('selection-change', handle_item_selection_status_changed);
			$item.on('visibility-change', handle_item_visibility_changed);
		}

		/**
		 * @type {Branda.Menu_Item[]}
		 */
		function filter_out_invisible_items(filterable) {
			return _.filter(filterable, function (menu_item) {
				return !menu_item.is_invisible();
			});
		}

		function filter_out_hidden_items(filterable) {
			return _.filter(filterable, function (menu_item) {
				return !menu_item.is_hidden();
			});
		}

		function validate() {
			var is_valid = true;
			items.forEach(function (menu_item) {
				is_valid = menu_item.validate() && is_valid;
			});

			return is_valid;
		}

		/**
		 * @type {Branda.Menu_Item[]}
		 */
		function get_selected_items() {
			return _.filter(items, function (menu_item) {
				return menu_item.is_selected();
			});
		}

		function adjust_hide_buttons(is_not_hidden) {
			if (is_not_hidden) {
				elements.hide_button.show();
				elements.unhide_button.hide();
			} else {
				elements.hide_button.hide();
				elements.unhide_button.show();
			}
		}

		function adjust_visibility_buttons(is_visible) {
			if (is_visible) {
				elements.make_invisible_button.show();
				elements.make_visible_button.hide();
			} else {
				elements.make_invisible_button.hide();
				elements.make_visible_button.show();
			}
		}

		function set_bulk_controls_visibility(show_controls) {
			if (show_controls) {
				elements.bulk_controls.show();
			} else {
				elements.bulk_controls.hide();
			}
		}

		function handle_item_selection_status_changed() {
			// Update visibility of bulk controls
			var selected_items = get_selected_items();
			set_bulk_controls_visibility(!!selected_items.length);

			var visible_items = filter_out_invisible_items(selected_items);
			var non_hidden_items = filter_out_hidden_items(selected_items);
			adjust_visibility_buttons(!!visible_items.length);
			adjust_hide_buttons(!!non_hidden_items.length);

			// Check or uncheck the select all checkbox
			set_select_all_checkbox_state(selected_items.length === items.length);
		}

		function set_select_all_checkbox_state(checked) {
			elements.select_all_checkbox.prop('checked', checked);
		}

		function handle_item_visibility_changed() {
			var selected_items = get_selected_items();
			var visible_items = filter_out_invisible_items(selected_items);
			var non_hidden_items = filter_out_hidden_items(selected_items);

			adjust_visibility_buttons(!!visible_items.length);
			adjust_hide_buttons(!!non_hidden_items.length);
		}

		function include_menu_items() {
			items.forEach(function (menu_item) {
				elements.builder_fields.append(menu_item.get_el());
			});
		}

		function init_sortable() {
			var old_position;

			elements.root.sortable({
				items: '.sui-accordion-item',
				start: function (event, ui) {
					// jQuery elements for menu items and containers are created independently
					// and then inserted into a container so we need to refresh positions
					elements.root.sortable("refreshPositions");
					// Save the old position of the item being dragged
					old_position = ui.item.index();
				},
				stop: function (event, ui) {
					var new_position = ui.item.index();

					if (
						is_valid_item_position(old_position)
						&& is_valid_item_position(new_position)
					) {
						// Update our internal array so array positions match markup positions
						move_item(old_position, new_position);
					}
				}
			});
		}

		function is_valid_item_position(position) {
			return position >= 0 && position < items.length;
		}

		function move_item(from, to) {
			var element = items[from];
			items.splice(from, 1);
			items.splice(to, 0, element);
		}

		function get_el() {
			// Construct the element as late as possible so that all the menu items are included
			if (!elements) {
				elements = new Menu_Item_Container_Elements();
				include_menu_items();

				init_sortable();
				init_add_button_listener();
				init_select_all_listener();
				init_duplicate_button_listener();
				init_make_invisible_button();
				init_make_visible_button_listener();
				init_hide_button_listener();
				init_unhide_button_listener();
			}

			return elements.root;
		}

		function init_select_all_listener() {
			elements.select_all_checkbox.on('change', function () {
				var is_checked = $(this).is(':checked');

				$(self).trigger('select-all-status-changed', [is_checked]);
				set_bulk_controls_visibility(is_checked);
				if (is_checked) {
					var visible_items = filter_out_invisible_items(items).length;
					var non_hidden_items = filter_out_hidden_items(items).length;
					adjust_visibility_buttons(!!visible_items);
					adjust_hide_buttons(!!non_hidden_items);
				}
			});
		}

		function init_duplicate_button_listener() {
			elements.duplicate_button.on('click', function () {
				get_selected_items().forEach(function (menu_item) {
					menu_item.duplicate();
				});
			});
		}

		function init_hide_button_listener() {
			elements.hide_button.on('click', function () {
				get_selected_items().forEach(function (menu_item) {
					menu_item.hide();
				});

				adjust_hide_buttons(false);
			});
		}

		function init_make_invisible_button() {
			elements.make_invisible_button.on('click', function () {
				get_selected_items().forEach(function (menu_item) {
					menu_item.make_invisible();
				});

				adjust_visibility_buttons(false);
			});
		}

		function init_unhide_button_listener() {
			elements.unhide_button.on('click', function () {
				get_selected_items().forEach(function (menu_item) {
					menu_item.unhide();
				});

				adjust_hide_buttons(true);
			});
		}

		function init_make_visible_button_listener() {
			elements.make_visible_button.on('click', function () {
				get_selected_items().forEach(function (menu_item) {
					menu_item.make_visible();
				});

				adjust_visibility_buttons(true);
			});
		}

		function init_add_button_listener() {
			elements.add_button.on('click', add_new_item);
		}

		/**
		 * TODO: not the ideal way to check whether the current container contains main menu items
		 * @returns {boolean}
		 */
		function contains_main_items() {
			return items.length
				? items[0].is_main()
				: false; // Main menu should never be empty
		}

		function add_new_item() {
			var is_main = contains_main_items(),
				item_args = Branda.Menu_Item_Model.get_defaults(is_main),
				new_id = Branda.Menu_Utils.generate_random_menu_item_id();

			var new_item = new Branda.Menu_Item(new_id, item_args, self);
			add(new_item);
			elements.builder_fields.append(new_item.get_el());
		}
	};
})(jQuery);
