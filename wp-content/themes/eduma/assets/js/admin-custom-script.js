jQuery(function ($) {
	$(document).ready(function () {
		thim_custom_admin_select();

		thim_eduma_install_demo();
	});


	function thim_custom_admin_select() {
		$('#customize-control-thim_config_logo_mobile select').on('change', function () {
			if ($(this).val() == "custom_logo") {
				$('#customize-control-thim_logo_mobile').show();
				$('#customize-control-thim_sticky_logo_mobile').show();
			} else {
				$('#customize-control-thim_logo_mobile').hide();
				$('#customize-control-thim_sticky_logo_mobile').hide();
			}
		}).trigger('change');

		$('#customize-control-thim_page_builder_chosen select').on('change', function () {
			if ($(this).val() == "visual_composer") {
				$('#customize-control-thim_footer_bottom_bg_img').show();
			} else {
				$('#customize-control-thim_footer_bottom_bg_img').hide();
			}
		}).trigger('change');


		$('#customize-control-thim_box_layout select').on('change', function () {
			if ($(this).val() == "boxed") {
				$('#customize-control-thim_user_bg_pattern').show();
				$('#customize-control-thim_bg_pattern').show();
				$('#customize-control-thim_bg_upload').show();
				$('#customize-control-thim_bg_repeat').show();
				$('#customize-control-thim_bg_position').show();
				$('#customize-control-thim_bg_attachment').show();
				$('#customize-control-thim_bg_size').show();
			} else {
				$('#customize-control-thim_user_bg_pattern').hide();
				$('#customize-control-thim_bg_pattern').hide();
				$('#customize-control-thim_bg_upload').hide();
				$('#customize-control-thim_bg_repeat').hide();
				$('#customize-control-thim_bg_position').hide();
				$('#customize-control-thim_bg_attachment').hide();
				$('#customize-control-thim_bg_size').hide();
			}
		}).trigger('change');


		$('#customize-control-thim_preload select').on('change', function () {
			if ( $(this).val() == 'image' ) {
				$('#customize-control-thim_preload_image').show();
			} else {
				$('#customize-control-thim_preload_image').hide();
			}
		}).trigger('change');
	}


	function thim_eduma_install_demo() {
		if ($('.thim-demo-browser.theme-browser').length == 0) {
			return;
		}

		var $html = '<div class="thim-choose-page-builder"><h3 class="title">Please select page builder before Import Demo.</h3>';
		$html += '<select id="thim-select-page-builder">';
		$html += '<option value="">Select</option>';
		$html += '<option value="site_origin">Site Origin</option>';
		$html += '<option value="visual_composer">Visual Composer</option>';
		$html += '</select></div>';

		$('.thim-demo-browser.theme-browser').prepend($html);

		if( $('#thim-select-page-builder').val() === '' ) {
			$('.thim-demo-browser').addClass('overlay');
		}

		//$('.theme[aria-describedby*=demo-university-4]').hide();
		$(document).on('change', '#thim-select-page-builder', function () {

			var elem = $(this),
				elem_val = elem.val(),
				elem_parent = elem.parents('.thim-demo-browser'),
				data = {
					action    : 'thim_update_theme_mods',
					thim_key  : 'thim_page_builder_chosen',
					thim_value: elem_val
				};
			// if( elem_val == 'visual_composer') {
			// 	$('.theme[aria-describedby*=demo-university-4]').hide();
			// }else{
			// 	$('.theme[aria-describedby*=demo-university-4]').show();
			// }

			if (elem_val !== '') {
				elem_parent.removeClass('overlay').addClass('loading');
				$.post(ajaxurl, data, function (response) {
					console.log(response);
					elem_parent.removeClass('loading');
				});
			}else{
				elem_parent.addClass('overlay');
			}

		});
	}
});