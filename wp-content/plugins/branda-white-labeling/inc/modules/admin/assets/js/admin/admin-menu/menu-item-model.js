(function ($) {
	window.Branda = window.Branda || {};

	window.Branda.Menu_Item_Model = function (values) {
		/**
		 * To make sure the original values are not changed, make a copy
		 */
		var model_values = $.extend({}, values),
			/**
			 * @type {Branda.Menu_Item_Model}
			 */
			self = this;

		this.get = function (id) {
			var value = model_values[id] || '',
				default_value = model_values[(id + '_default')] || '';

			return value || default_value;
		};

		this.get_values = function () {
			return model_values;
		};

		this.set = function (item, value) {
			model_values[item] = value;

			$(self).trigger('value-change');
		};

		this.get_title = function () {
			return self.get('title');
		};

		this.get_icon_type = function () {
			return self.get('icon_type');
		};

		this.is_icon_type_svg = function () {
			return self.get_icon_type() === 'svg';
		};

		this.is_icon_type_url = function () {
			return self.get_icon_type() === 'url';
		};

		this.is_icon_type_upload = function () {
			return self.get_icon_type() === 'upload';
		};

		this.is_icon_type_dashicon = function () {
			return self.get_icon_type() === 'dashicon';
		};

		this.get_dashicon = function () {
			return self.get('dashicon');
		};

		this.get_icon_image_id = function () {
			return self.get('icon_image_id');
		};

		this.set_icon_image_id = function (id) {
			self.set('icon_image_id', id);
		};

		this.get_icon_url = function () {
			return self.get('icon_url');
		};

		this.get_icon_svg = function () {
			return self.get('icon_svg');
		};

		this.get_submenu = function () {
			var submenu_data = self.get('submenu');

			// 'submenu' can have an object or true in it
			if (typeof submenu_data !== 'object') {
				submenu_data = {};
			}

			return submenu_data;
		};

		this.set_submenu = function (submenu) {
			self.set('submenu', submenu);
		};

		this.has_submenu = function () {
			return !!self.get('submenu');
		};

		this.is_native = function () {
			return !!self.get('is_native');
		}
	};

	window.Branda.Menu_Item_Model.get_defaults = function (is_main) {
		var values = {
			icon_type: 'none',
			link_type: 'none'
		};

		if (is_main) {
			values.submenu = true;
		}

		return values;
	};
})(jQuery);
