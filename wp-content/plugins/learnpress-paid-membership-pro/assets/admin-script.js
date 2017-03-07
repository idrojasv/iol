'use strict';

(function ($) {

	$(document).ready(function () {

		// Membership Settings Admin
		var $wrap = $('#learn-press-pmpro-settings-admin'),
			$buyThoughMembership = $('#learn_press_buy_through_membership', $wrap),
			$btnBuyCourse = $('#learn_press_button_buy_course', $wrap);

		$buyThoughMembership.on('change', function () {
			console.log($btnBuyCourse.closest('tr'))

			if ($(this).prop('checked')) {
				$btnBuyCourse.closest('tr').addClass('hide-if-js');
			}
			else {
				$btnBuyCourse.closest('tr').removeClass('hide-if-js');
			}
		}).trigger('change');

	});
})(jQuery)