<?php

/* Import/Export Customizer Setting */
if ( !function_exists( 'thim_cusomizer_upload_settings' ) ) :
	function thim_cusomizer_upload_settings() {
		$file_ext = pathinfo( $_FILES['thim-customizer-settings-upload']['name'], PATHINFO_EXTENSION );
		if ( $file_ext == 'json' ) {
			$setting_data = json_decode( thim_file_get_contents( $_FILES['thim-customizer-settings-upload']['tmp_name'] ), true );
			foreach ( $setting_data as $mod => $value ) {
				if ( $mod ) {
					set_theme_mod( $mod, $value );
				}
			}
		}
		if ( function_exists( 'thim_generate' ) && function_exists( 'thim_options_variation' ) ) {
			$options = get_theme_mods();
			thim_options_variation( $options );
			thim_generate( THIM_DIR . 'style', '.css', $options );
		}

		die();
	}
endif;

add_action( 'wp_ajax_thim_cusomizer_upload_settings', 'thim_cusomizer_upload_settings' );

if ( !function_exists( 'thim_ajax_get_attachment_url' ) ) :
	function thim_ajax_get_attachment_url() {
		check_ajax_referer( 'thim_customize_attachment', 'nonce' );

		if ( !isset( $_POST['attachment_id'] ) ) {
			exit();
		}
		$attachment_id = $_POST['attachment_id'];
		echo wp_get_attachment_url( $attachment_id );
		exit();
	}
endif;
add_action( 'wp_ajax_thim_ajax_get_attachment_url', 'thim_ajax_get_attachment_url' );
add_action( 'wp_ajax_nopriv_thim_ajax_get_attachment_url', 'thim_ajax_get_attachment_url' );

if ( !function_exists( 'thim_ajax_url' ) ) :
	function thim_ajax_url() {
		echo '<script type="text/javascript">
        var ajax_url ="' . get_site_url() . '/wp-admin/admin-ajax.php";
        </script>';
	}
endif;
add_action( 'wp_print_scripts', 'thim_ajax_url' );
/* End Import/Export Customizer Setting */


function thim_export() {

	ob_start();
	$blogname  = strtolower( str_replace( ' ', '-', get_option( 'blogname' ) ) );
	$file_name = $blogname . '-theme-' . date( 'Ydm' ) . '.json';
	$options   = get_theme_mods();

	unset( $options['nav_menu_locations'] );

	foreach ( $options as $key => $value ) {
		$value              = maybe_unserialize( $value );
		$need_options[$key] = $value;
	}

	$json_file = json_encode( $need_options );

	ob_clean();

	header( 'Content-Type: text/json; charset=' . get_option( 'blog_charset' ) );
	header( 'Content-Disposition: attachment; filename="' . $file_name . '"' );

	echo ent2ncr( $json_file );

	die;
}


add_action( 'wp_ajax_thim_export', 'thim_export' );
