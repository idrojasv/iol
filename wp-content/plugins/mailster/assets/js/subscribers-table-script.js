jQuery(document).ready(function ($) {

	"use strict"

	var bulk_update_info, form_submitted = false,
		count;

	$('#cb-select-all-1, #cb-select-all-2').on('change', function () {

		var $input = $('#all_subscribers'),
			label = $input.data('label');

		count = $input.data('count');

		if ($(this).is(':checked') && count > $('#the-list').find('tr').length && confirm(label)) {
			$input.val(1);
		} else {
			$input.val(0);
		}
	});

	$('#subscribers-overview-form').on('submit', function (event) {
		var $this = $(this),
			$input = $('#all_subscribers');

		if (1 == $input.val()) {
			event.preventDefault();

			if (form_submitted) return;
			form_submitted = true;

			window.onbeforeunload = function () {
				return mailsterL10n.onbeforeunload;
			};

			bulk_update_info = $('<div class="alignright bulk-update-info spinner">' + mailsterL10n.initprogess + '</div>').prependTo('.bulkactions');

			do_batch($this.serialize(), 0, function () {
				bulk_update_info.removeClass('spinner');
				window.onbeforeunload = null;
				setTimeout(function () {
					location.reload();
				}, 1000);
			});


		}

	});

	function do_batch(data, page, cb) {
		if (!page) page = 0;

		$.post(location.href, {
			'post_data': data,
			'page': page,
			'count': count
		}, function (response) {

			bulk_update_info.html(response.message);
			if (response.success_message) console.log(response.success_message);
			if (response.error_message) console.error(response.error_message);

			if (!response.finished) {
				setTimeout(function () {
					do_batch(data, response.page, cb);
				}, 300);
			} else {
				cb && cb();
			}

		});

	}

});