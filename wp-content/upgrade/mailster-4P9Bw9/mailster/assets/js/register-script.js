jQuery(document).ready(function ($) {

	"use strict"

	var wpnonce = mailsterregisterL10n.wpnonce;


	$('.register_form_wrap')
		.on('focus', 'input', function () {
			$(this).attr('data-placeholder', $(this).attr('placeholder'));
			$(this).attr('placeholder', '');
		})
		.on('blur', 'input', function () {
			$(this).attr('placeholder', $(this).attr('data-placeholder'));
		})
		.on('click', '.envato-signup', function () {

			popup(this.href, 600, 900, 'mailster_envato_signup');
			return false;
		})
		.on('click', '.howto-purchasecode', function () {

			popup(this.href, 600, 350, 'mailster_how_to_puchasecode');
			return false;
		})
		.on('submit', '.register_form', function () {

			var form = $(this),
				wrap = form.parent(),
				purchasecode = wrap.find('input.register-form-purchasecode').val(),
				slug = wrap.find('input.register-form-slug').val();

			form.removeClass('has-error').prop('disabled', true);
			wrap.addClass('loading');

			_ajax('register', {
				purchasecode: purchasecode,
				slug: slug
			}, function (response) {

				form.prop('disabled', false);
				wrap.removeClass('loading');
				if (response.success) {
					wrap.addClass('step-2').removeClass('step-1')
					$('.register_form_2').find('input').eq(0).focus();
				} else {
					form.addClass('has-error').find('.error-msg').html(response.error);
				}

			})

			return false;

		})
		.on('submit', '.register_form_2', function () {

			var form = $(this),
				wrap = form.parent(),
				purchasecode = wrap.find('input.register-form-purchasecode').val(),
				slug = wrap.find('input.register-form-slug').val();

			form.removeClass('has-error').prop('disabled', true);
			wrap.addClass('loading');

			_ajax('register', {
				purchasecode: purchasecode,
				slug: slug,
				data: form.serialize()
			}, function (response) {

				form.prop('disabled', false);
				wrap.removeClass('loading');
				if (response.success) {
					wrap.addClass('step-3').removeClass('step-2')
					$(document).trigger('verified.' + slug);
				} else {
					if (response.code == 406) {
						form = wrap.find('.register_form');
						form.parent().removeClass('step-2').addClass('step-1');
					}
					form.addClass('has-error').find('.error-msg').html(response.error);
				}
			})

			return false;

		})
		.removeClass('loading');


	function popup(url, width, height, windowname) {

		var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left,
			dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top,
			windowWidth = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width,
			windowHeight = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height,
			left = ((windowWidth / 2) - (width / 2)) + dualScreenLeft,
			top = ((windowHeight / 2) - (height / 2)) + dualScreenTop,
			newWindow = window.open(url, windowname, 'scrollbars=auto,resizable=1,menubar=0,toolbar=0,location=0,directories=0,status=0, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);

		if (window.focus)
			newWindow.focus();

	}

	function _ajax(action, data, callback, errorCallback, dataType) {

		if ($.isFunction(data)) {
			if ($.isFunction(callback)) {
				errorCallback = callback;
			}
			callback = data;
			data = {};
		}
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: $.extend({
				action: 'mailster_' + action,
				_wpnonce: wpnonce
			}, data),
			success: function (data, textStatus, jqXHR) {
				callback && callback.call(this, data, textStatus, jqXHR);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				if (textStatus == 'error' && !errorThrown) return;
				if (console) console.error($.trim(jqXHR.responseText));
				errorCallback && errorCallback.call(this, jqXHR, textStatus, errorThrown);

			},
			dataType: dataType ? dataType : "JSON"
		});
	}

	window.verifyMailster = function (slug, purchasecode, username, email) {

		var wrap = $('.register_form_wrap-' + slug);
		wrap.find('input.register-form-purchasecode').val(purchasecode);
		wrap.find('input.username').val(username);
		wrap.find('input.email').val(email);

		if (purchasecode) {
			wrap.find('.register_form').parent().removeClass('step-1').addClass('step-2');
			//wrap.find('.register_form_2').submit();
		} else {
			wrap.find('.register_form').submit();
		}

	}


});