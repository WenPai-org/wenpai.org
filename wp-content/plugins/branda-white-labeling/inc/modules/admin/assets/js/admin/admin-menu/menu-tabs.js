(function ($) {
	window.Branda = window.Branda || {};

	var Menu_Tabs_Elements = function () {
		var tabs_template = wp.template('menu-tabs');

		var markup = tabs_template({});

		this.root = $(markup);
		this.tabs_navigation = $('.sui-tabs-navigation', this.root);
		this.tabs_menu = $('.sui-tabs-menu', this.root);
		this.tabs_content = $('.sui-tabs-content', this.root);

		SUI.tabsOverflow(this.tabs_navigation);
	};

	window.Branda.Menu_Tabs = function (is_deletion_allowed) {
		this.get_el = get_el;
		this.add_tab = add_tab;
		this.destroy_active_tab = destroy_active_tab;
		this.activate_first_tab = activate_first_tab;

		var tab_template = wp.template('menu-tab'),
			tab_content_template = wp.template('menu-tab-content');

		var elements = new Menu_Tabs_Elements();

		/**
		 * Holds the key of the currently active menu
		 */
		var active_menu = '';

		/**
		 * @type {Object.<string, Branda.Custom_Admin_Menu>}
		 */
		var menus = {};

		function get_el() {
			return elements.root;
		}

		function add_tab(key, label, active) {
			var $tab = create_tab_element(key, label, active),
				$content = create_tab_content_element(key, active);

			if (menus.hasOwnProperty(key)) {
				return;
			}

			// Store menu instance for later
			menus[key] = new Branda.Custom_Admin_Menu(key);
			// Add the menu element to tab content
			$content.html(menus[key].get_el());

			elements.tabs_menu.append($tab);
			elements.tabs_content.append($content);

			refresh_sui();

			$tab.on('click', handle_tab_click);
			$('span', $tab).on('click', stop);
			$('i.sui-icon-close', $tab).on('click', handle_tab_removal);

			if (active) {
				$tab.trigger('click');
			}

			change_visibility();
		}

		function tabs_exist() {
			return $('.sui-tab-item', elements.tabs_menu).length;
		}

		function change_visibility() {
			if (tabs_exist()) {
				elements.root.show();
			} else {
				elements.root.hide();
			}
		}

		function handle_tab_click() {
			var $tab = $(this),
				key = $tab.data('menuKey');

			destroy_active_tab();

			if (menus.hasOwnProperty(key)) {
				menus[key].init();
				active_menu = key;
			}
		}

		function destroy_active_tab() {
			if (active_menu && menus.hasOwnProperty(active_menu)) {
				menus[active_menu].destroy();
			}
		}

		function create_tab_element(key, label, active) {
			var markup = tab_template({
				key: key,
				label: label,
				is_active: active,
				is_deletion_allowed: is_deletion_allowed
			});

			return $(markup);
		}

		function create_tab_content_element(key, active) {
			var markup = tab_content_template({
				key: key,
				is_active: active
			});

			return $(markup);
		}

		function stop(e) {
			e.stopPropagation();
			e.preventDefault();
		}

		function show_loader($icon) {
			$icon
				.removeClass('sui-icon-close')
				.addClass('sui-icon-loader sui-loading');
			$icon.closest('span').addClass('disabled');
		}

		function handle_tab_removal(e) {
			stop(e);

			var $close_icon = $(this),
				$tab = $close_icon.closest('.sui-tab-item'),
				key = $tab.data('menuKey'),
				$content = $('#' + $tab.data('content')),
				menu = menus[key];

			show_loader($close_icon);

			menu.remove().done(function () {
				var removed_index = $tab.index();

				$tab.remove();
				$content.remove();

				delete menus[key];

				refresh_sui();
				change_visibility();
				if (tabs_exist() && !a_tab_is_active()) {
					activate_tab_by_index(next_tab_to_activate(removed_index));
				}
			});
		}

		function next_tab_to_activate(removed_index) {
			// If possible, choose the previous tab
			if (removed_index - 1 >= 0) {
				return removed_index - 1;
			}

			// Othewise choose the next tab
			return removed_index;
		}

		function activate_first_tab() {
			activate_tab_by_index(0);
		}

		function activate_tab_by_index(index) {
			if (index < 0) {
				return;
			}
			$('.sui-tab-item', elements.tabs_menu).eq(index).trigger('click');
		}

		function a_tab_is_active() {
			return !!$('.sui-tab-item.active', elements.tabs_menu).length;
		}

		function refresh_sui() {
			// Force SUI to refresh
			SUI.tabs();
		}
	};
})(jQuery);
