(function ($) {
	window.Branda = window.Branda || {};

	window.Branda.Window_Resize_Manager = {
		on_resize: function (callback) {
			$(window).on('resize.branda-custom-admin-menu', _.debounce(function () {
				callback();
			}, 200));
		},
		reset: function () {
			$(window).off('resize.branda-custom-admin-menu');
		}
	};

	var Custom_Admin_Menu_Elements = function (menu_key) {
		var template = wp.template('custom-admin-menu');

		var markup = template({menu_key: menu_key});

		this.root = $(markup);
		this.builder = $('> .branda-custom-admin-menu-builder-fields', this.root);

		this.footer = $('> .sui-box-footer', this.root);
		this.discard_button = $('.branda-discard-admin-menu-changes', this.footer);
		this.save_button = $('.branda-apply-admin-menu-changes', this.footer);
	};

	window.Branda.Custom_Admin_Menu = function (menu_key) {
		this.init = init;
		this.destroy = destroy;
		this.get_el = get_el;
		this.remove = remove;

		/**
		 * @var {Branda.Menu_Item_Container}
		 */
		var menu_items;

		var elements = new Custom_Admin_Menu_Elements(menu_key);

		var destroyed = false;

		var deferred_ajax = [];

		function init() {
			set_destroyed(false);

			disable_interaction();
			init_menu_items()
				.fail(show_error)
				.always(enable_interaction);

			init_discard_button();
			init_save_button();
		}

		/**
		 * Returns the menu to uninitialized state
		 */
		function destroy() {
			set_destroyed(true);

			reject_deferred_ajax();
			enable_interaction();
			destroy_items();
			remove_builder_fields();
			remove_listeners();
		}

		function init_menu_items() {
			clear_window_resize_listeners();

			return fetch_menu_data()
				.done(initialize_items)
				.done(add_builder_fields);
		}

		/**
		 * This is crucial because if we don't delete the window resize listeners, old menu items will stay in memory forever
		 */
		function clear_window_resize_listeners() {
			Branda.Window_Resize_Manager.reset();
		}

		function init_discard_button() {
			elements.discard_button.on('click', handle_discard_button_click);
		}

		function handle_discard_button_click() {
			var sure = confirm(branda_custom_menu.discard_confirm);
			if (!sure) {
				return;
			}

			disable_interaction();

			delete_menu_and_reload()
				.fail(show_error)
				.always(enable_interaction);
		}

		function init_save_button() {
			elements.save_button.on('click', handle_save_button_click);
		}

		function handle_save_button_click() {
			disable_interaction();

			save_menu_and_reload()
				.fail(show_error)
				.always(enable_interaction)
				.always(function () {
					display_success_notice();
					setTimeout(function () {
						window.location.reload();
					}, 500);
				});
		}

		function scroll_to_first_error() {
			var $dialog = $('#branda-admin_menu-custom-admin-menu'),
				open_class = 'sui-accordion-item--open',
				$first_error = $('.sui-form-field-error', $dialog).first(),
				$panes_container = $first_error.closest('[data-panes]'),
				$tabs_container = $first_error.closest('.sui-tabs').find('[data-tabs]');

			$('.' + open_class, $dialog).removeClass(open_class);

			$panes_container.children('.active').removeClass('active');
			$tabs_container.children('.active').removeClass('active');

			var $selected_pane = $first_error.closest('[data-panes] > div'),
				$selected_tab = $tabs_container.children('div').eq($selected_pane.index());

			$selected_pane.addClass('active');
			$selected_tab.addClass('active');
			$first_error.parents('.sui-builder-field').addClass(open_class);

			scroll_to_element($dialog, $first_error);
		}

		function scroll_to_element($container, $el) {
			var position = $container.offset().top + $el.position().top;

			setTimeout(function () {
				$container.animate({scrollTop: position}, 1000);
			}, 50);
		}

		function display_success_notice() {
			SUI.openFloatNotice( branda_custom_menu.setting_updated, 'success' );
		}

		function remove_listeners() {
			elements.save_button.off();
			elements.discard_button.off();
		}

		function get_el() {
			return elements.root;
		}

		function set_buttons_state(enabled) {
			elements.save_button.prop('disabled', !enabled);
			elements.discard_button.prop('disabled', !enabled);
		}

		function set_loading_icon_visibility(visible) {
			var loading_class = 'branda-admin-menu-loading';
			elements.root.removeClass(loading_class);
			if (visible) {
				elements.root.addClass(loading_class);
			}
		}

		function disable_interaction() {
			set_buttons_state(false);
			set_loading_icon_visibility(true);
		}

		function enable_interaction() {
			set_buttons_state(true);
			set_loading_icon_visibility(false);
		}

		function fetch_menu_data() {
			return post('branda_admin_bar_load_menu', {});
		}

		function post(action, extra_data) {
			var deferred = new $.Deferred(),
				data = $.extend({
					action: action,
					key: menu_key,
					_wpnonce: branda_custom_menu.nonce
				}, extra_data);

			$.post(ajaxurl, data, function (response) {
				response = (response || {});
				var data = response.data || {};

				if (response.success) {
					deferred.resolve(data);
				} else {
					deferred.rejectWith({}, [data.message || '']);
				}
			}).fail(function (jqXHR, textStatus, error) {
				deferred.rejectWith({}, [error || jqXHR.responseText || textStatus]);
			});

			deferred_ajax.push(deferred);

			return deferred;
		}

		function reject_deferred_ajax() {
			deferred_ajax.forEach(function (deferred) {
				if ( $.inArray( deferred.state(), [ 'rejected', 'resolved' ] ) ) {
					// Too late
					return;
				}

				deferred.reject(['']);
			});
		}

		function initialize_items(data) {
			var menu_data = data.menu || {};
			menu_items = new Branda.Menu_Item_Container();

			for (var key in menu_data) {
				if (!menu_data.hasOwnProperty(key)) {
					continue;
				}

				menu_items.add(
					new Branda.Menu_Item(key, menu_data[key], menu_items)
				);
			}
		}

		function destroy_items() {
			menu_items = null;
		}

		function add_builder_fields() {
			elements.builder.html(menu_items.get_el());
		}

		function remove_builder_fields() {
			elements.builder.html('');
		}

		function show_error(message) {
			if (!is_destroyed()) {
				alert(message);
			}
		}

		function save_menu_and_reload() {
			var menu_data = JSON.stringify(menu_items.get_data());

			return do_menu_action_and_reload('branda_admin_bar_save_menu', {
				menu: menu_data
			});
		}

		function delete_menu_and_reload() {
			return do_menu_action_and_reload('branda_admin_bar_remove_menu');
		}

		function do_menu_action_and_reload(ajax_action, data) {
			var deferred = new $.Deferred();

			post(ajax_action, data).done(function () {
				init_menu_items().done(function (data) {
					deferred.resolve(data);
				}).fail(function (message) {
					deferred.rejectWith({}, [message]);
				});
			}).fail(function (message) {
				deferred.rejectWith({}, [message]);
			});

			return deferred;
		}

		/**
		 * Makes an ajax call to remove the menu permanently
		 */
		function remove() {
			return post('branda_admin_bar_remove_menu', {});
		}

		function set_destroyed(is_destroyed) {
			destroyed = is_destroyed;
		}

		function is_destroyed() {
			return destroyed;
		}
	};
})(jQuery);
