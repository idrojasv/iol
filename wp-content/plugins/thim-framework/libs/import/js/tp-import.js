/**
 * File: tp import
 * Description: Action import data demo, demo files to make site as demo site
 * Author: Andy Ha (tu@wpbriz.com)
 * Copyright 2007-2014 wpbriz.com. All rights reserved.
 */

/**
 * Function import
 * Call ajax to process
 * @constructor
 */
jQuery(document).ready(function ($) {
	$('.tp_process_messase').css('font-size', 'smaller');

	var tp_pointers_str = tp_pointer;
	var $pointers = $('.tp-btn-import').pointer({
		content: '<h3>' + tp_pointers_str.welcome + '</h3><p>' + tp_pointers_str.content + '</p>',
		close: function () {
			// Once the close button is hit
		}
	}).pointer('open').pointer('close');

	$(".tp-btn-import[data-disabled=true]").hover(function () {
		var wp_pointer = $(this).attr('data-pointer');
		$('#' + wp_pointer).slideDown(500);
	}, function () {
		var wp_pointer = $(this).attr('data-pointer');
		$('#' + wp_pointer).delay(500).slideUp();
	});


	$('.tp-btn-import').on('click', function (event) {
		var disabled = $(this).attr('data-disabled');
		if (disabled !== undefined && disabled == 'true') {
			return;
		}

		var demo_data = $(this).attr('data-site');

		var demo_name = $('#' + demo_data).text();
		var r = confirm('You\'re choosing ' + demo_name + ' to import. Click yes to continue or cancel to exit.');
		if (!r) {
			return;
		}

		window.onbeforeunload = function () {
			return 'The import process will cause errors if you leave this page!';
		};

		tp_open_popup();
		setValProgress('.tp_progress_import .meter > span', '1px');
		import_type('download&demodata=' + demo_data, 0);
	});

	function setValProgress(selector, val, speed) {
		$(selector).animate({width: val}, speed);
	}

	$('.tp_notification .notice-dismiss').on('click', function (event) {
		var $parent = $(this).parent();
		var notification = $(this).attr('data-tp-dismiss');

		$.ajax({
			type: 'POST',
			data: 'action=tp_dismiss_notification&notification=' + notification,
			url: ajaxurl,
			dataType: 'json',
			success: function (response) {
				$parent.fadeOut();
				console.log(response);
			}
		});
	});

	$('.tp-close-import-popup').on('click', function (event) {
		location.reload();
	});

	function tp_open_popup() {
		var $body = $('body');
		$body.addClass('open-popup');
		$body.append('<div id="TB_overlay" class="TB_overlayBG"></div>');

		$('.tp-popup').animate({
			'top': 0
		}, 500);
	}

	function tp_import_complete() {
		unlock_progress_import();

		$('.tp_progress_import .meter > span').css('width', '100%');
		$('.tp_progress_import .meter > span').addClass('stop success');
		$('.tp-close-import-popup').show();
		$('.tp-complete').show();
	}

	function tp_import_error() {
		unlock_progress_import();

		$('.tp_progress_import .meter > span').addClass('stop');
		$('.tp-close-import-popup').show();
		$('.tp_progress_error_message').show();
	}

	function import_type(type, method) {
		jQuery.ajax({
			type: 'POST',
			data: 'action=tp_make_site&method=' + method + '&type=' + type,
			url: ajaxurl,
			dataType: 'json',
			success: function (response) {
				var next_step = response.next;
				if (response.log !== undefined && response.log != '') {
					$('.tp_progress_error_message .log .content').append('<p>' + response.log + '</p>');
				}

				/**
				 * Done
				 */
				if (next_step == 'done') {
					tp_import_complete();

					return;
				}

				/**
				 * Revolution Error
				 */
				if (next_step == 'revolution_error') {
					window.onbeforeunload = null;
					$('.tp-complete .content-message').append(response.message);
					tp_import_complete();
					return;
				}

				/**
				 * Error
				 */
				if (next_step == 'error') {
					if (response.message !== undefined && response.message != '') {
						$('.tp_progress_error_message .tp-error .content').append('<p>' + response.message + '</p>');
					}
					tp_import_error();
					return;
				}

				var current_val_progress = parseInt($('.tp_progress_import .meter > span').css('width'));
				var max_width_progres = parseInt($('.tp_progress_import .meter').css('width'));
				var new_val_progress = current_val_progress + parseInt((max_width_progres - current_val_progress) * 10 / 100);
				setValProgress('.tp_progress_import .meter > span', new_val_progress + 'px', 5000);
				import_type(next_step, method);
			},
			error: function (err) {
				if (err.status == 500) {
					$('.tp_progress_error_message .tp-error .content').append('<p>' + tp_notifications.err + '</p>');
				} else {
					$('.tp_progress_error_message .tp-error .content').append('<p>' + err.responseText + '</p>');
				}
				tp_import_error();
			}
		});
	}

	function unlock_progress_import() {
		window.onbeforeunload = null;
	}
});