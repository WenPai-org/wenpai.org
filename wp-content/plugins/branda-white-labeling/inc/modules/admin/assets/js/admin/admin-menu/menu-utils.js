(function () {
	window.Branda = window.Branda || {};

	window.Branda.Menu_Utils = {
		/**
		 * https://stackoverflow.com/a/27747377/3871020
		 * @returns {string}
		 */
		generate_random_menu_item_id: function () {
			var empty_array = new Uint8Array(6),
				dec_to_hex = function (dec) {
					return ('0' + dec.toString(16)).substr(-2)
				},
				crypto = (window.crypto || window.msCrypto),
				random = Array.from(crypto.getRandomValues(empty_array), dec_to_hex).join('');

			return 'menu_item_' + random;
		}
	};

})(jQuery);
