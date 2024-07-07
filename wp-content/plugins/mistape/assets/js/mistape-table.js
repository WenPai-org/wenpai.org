(function ($) {
	"use strict";

	// return if no args passed from backend
	if (!window.decoMistape) {
		return;
	}

	window.decoMistape = $.extend(window.decoMistape, {

		onReady: function () {
			$('.row-actions a[data-thickbox-content]').click(function (e) {
				$('#mistape-thickbox > p').text($(e.currentTarget).data('thickbox-content'));
			});

			$('.add-to-ban-list').click(function(e){
				var text = decoMistape.strings.add_to_ban_list.replace('%s', $(e.currentTarget).closest('.row-actions').siblings('.ip').text());
				if (!confirm(text)) {
					e.preventDefault();
				}
			});

			$('.remove-from-ban-list').click(function(e){
				var text = decoMistape.strings.remove_from_ban_list.replace('%s', $(e.currentTarget).closest('.row-actions').siblings('.ip').text());
				if (!confirm(text)) {
					e.preventDefault();
				}
			});
		}
		
	});

	$(document).ready(decoMistape.onReady);

})(jQuery);