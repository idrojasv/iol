;(jQuery(function ($) {
	function get_cart_option() {
		return LP_WooCommerce_Payment.woocommerce_cart_option;
	}

	$('form.purchase-course').submit(function () {
		var $form = $(this),
			$button = $('button.purchase-button', this),
			$view_cart = $('.view-cart-button', this),
			$clicked = $form.find('input:focus, button:focus'),
			addToCart = $clicked.hasClass('button-add-to-cart');
		$button.removeClass('added').addClass('loading');
		$form.find('#learn-press-wc-message, input[name="purchase-course"]').remove();

		$.ajax({
			url     : window.location.href.addQueryVar('r', Math.random()),// Do not cache this page
			data    : $(this).serialize(),
			error   : function () {
				$button.removeClass('loading');
			},
			dataType: 'text',
			success : function (response) {
				response = LP.parseJSON(response);
				if (response.message && !response.single_purchase) {
					var $message = $(response.message).addClass('woocommerce-message');
					$form.prepend($('<div id="learn-press-wc-message"></div>').append($message));
					LP.unblockContent();
					$('body, html').css('overflow', 'visible');
				}
				if (response.redirect) {
					LP.reload(response.redirect);
				} else {
					$form.find('.purchase-button, .button-add-to-cart').remove();
				}
			}
		});
		return false;
	});
}));
