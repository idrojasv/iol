<?php
add_action( 'thim_logo', 'thim_logo', 1 );
// logo
if ( !function_exists( 'thim_logo' ) ) :
	function thim_logo() {
		$theme_options_data = get_theme_mods();
		if ( !empty( $theme_options_data['thim_logo'] ) ) {
			$thim_logo       = $theme_options_data['thim_logo'];
			$logo_attachment = wp_get_attachment_image_src( $thim_logo, 'full' );
			if ( $logo_attachment ) {
				$src   = $logo_attachment[0];
				$style = 'width="' . $logo_attachment[1] . '" height="' . $logo_attachment[2] . '"';
			} else {
				// Default image
				// Case: image ID from demo data
				$src   = get_template_directory_uri() . '/images/logo.png';
				$style = 'width="153" height="40"';
			}
		} else {
			// Default image
			// Case: The first install
			$src   = get_template_directory_uri() . '/images/logo-sticky.png';
			$style = 'width="153" height="40"';
		}

		echo '<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . ' - ' . esc_attr( get_bloginfo( 'description' ) ) . '" rel="home" class="no-sticky-logo">';
		echo '<img src="' . $src . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" ' . $style . '>';
		echo '</a>';
	}
endif;
add_action( 'thim_sticky_logo', 'thim_sticky_logo', 1 );

// get sticky logo
if ( !function_exists( 'thim_sticky_logo' ) ) :
	function thim_sticky_logo() {
		$theme_options_data = get_theme_mods();
		if ( !empty( $theme_options_data['thim_sticky_logo'] ) ) {
			$thim_stick_logo = $theme_options_data['thim_sticky_logo'];
			$logo_attachment = wp_get_attachment_image_src( $thim_stick_logo, 'full' );
			if ( $logo_attachment ) {
				$src   = $logo_attachment[0];
				$style = 'width="' . $logo_attachment[1] . '" height="' . $logo_attachment[2] . '"';
			} else {
				// Default image
				// Case: image ID from demo data
				$src   = get_template_directory_uri() . '/images/logo-sticky.png';
				$style = 'width="153" height="40"';
			}
		} else {
			// Default image
			// Case: The first install
			$src   = get_template_directory_uri() . '/images/logo-sticky.png';
			$style = 'width="153" height="40"';
		}
		echo '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home" class="sticky-logo">';
		echo '<img src="' . $src . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" ' . $style . '>';
		echo '</a>';
	}
endif;
