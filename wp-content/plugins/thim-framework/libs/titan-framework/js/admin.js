jQuery(document).ready(function ($) {
	"use strict";

	jQuery(window).load(function () {
		// Section Icon
		jQuery('.customizer-icon').each(function () {
			var selector = jQuery(this);
			if (selector.length) {
				var parent = selector.closest('ul');
				var html = jQuery('<div>').append(selector.clone()).remove().html();
				parent.prev("h3").prepend(html);
				selector.remove();
			}
		});
	});

	jQuery(document).on("click", "#import-customize-settings", function (e) {
		e.preventDefault();
		jQuery('#thim-customizer-settings-upload').trigger('click');
	});

	// Add Import Settings form and Upload button
	jQuery('form#customize-controls').after(
		jQuery('<form></form>').attr('id', 'thim-import-form').append(
			jQuery('<input/>')
				.attr('type', 'file')
				.attr('id', 'thim-customizer-settings-upload')
				.attr('name', "thim-customizer-settings-upload")
				.css('position', 'absolute')
				.css('top', '-100px'), // hack sercurity
			jQuery('<input/>').attr('type', 'hidden').attr('name', 'action').val('thim_cusomizer_upload_settings')
		)
	);

	jQuery('#thim-customizer-settings-upload').change(function () {
		var $this = jQuery(this);
		var formData = new FormData(jQuery('#thim-import-form')[0]);
		$('<div />')
			.css({
				position: 'fixed',
				top: 0,
				left: 0,
				right: 0,
				bottom: 0,
				zIndex: 999999,
				opacity: 0.4,
				backgroundColor: '#000'
			})
			.appendTo($(document.body));
		jQuery.ajax({
				url        : ajax_url,
				type       : 'POST',
				//Ajax events
				// Form data
				data       : formData,
				//Options to tell JQuery not to process data or worry about content-type
				cache      : false,
				contentType: false,
				processData: false
			},
			'json'
		).done(function (data) {
			$(window).unbind('beforeunload');
			window.location.href = window.location.href;
			//.alert("Importing is successful\nReload the page to apply import data.");
		});
	});

	//Export settings
	jQuery(document).on("click", "a#thim-customizer-settings-download", function () {
		jQuery.post(
			ajaxurl,
			{
				'action': 'thim_export'
			},
			function (response) {
				jQuery.fileDownload(ajaxurl + '?action=thim_export', {
					failCallback: function () {
						alert('fail');
					}
				});
			}
		);
		return false;
	});
});
