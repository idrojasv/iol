jQuery(document).ready(function ($) {
	var tp_ajax_url = tp_admin_import.ajax_url;
	var tp_wp_nonce = tp_admin_import.wp_nonce;
	$('#tp-goto-import-page .notice-dismiss, #tp-goto-import-page .tp_import_ignore').click(function (event) {
		$('#tp-goto-import-page').fadeOut();

		$.ajax({
			url     : tp_ajax_url,
			method  : 'POST',
			data    : 'action=tp_dismiss_notification&notification=dismiss_goto_import&wp_nonce=' + tp_wp_nonce,
			dataType: 'json',
			success : function (response) {
				console.log(response);
			}
		});
	});
});