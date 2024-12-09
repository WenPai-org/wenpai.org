/**
 * Branda admin file for module "Header Content".
 * ￼
 * @since 3.0.0
 */
jQuery(document).ready(function ($) {
	$('.ub-header-subsites-toggle').on('change', function () {
		var subsites_options = $('.ub-header-subsites');
		if ($(this).is(':checked') && 'on' === $(this).val()) {
			subsites_options.show();
		} else {
			subsites_options.hide();
		}
	});
});
