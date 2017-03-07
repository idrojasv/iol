<?php

class ob_wp_import extends WP_Import {

	var $preStringOption;
	var $results;
	var $getOptions;
	var $saveOptions;
	var $termNames;

	function import_woosetting( $file ) {

		if ( is_file( POST_COUNT ) ) {
			unlink( POST_COUNT );
		}
		if ( is_file( MENU_MAPPING ) ) {
			unlink( MENU_MAPPING );
		}
		if ( is_file( PROCESS_TERM ) ) {
			unlink( PROCESS_TERM );
		}
		if ( is_file( PROCESS_POSTS ) ) {
			unlink( PROCESS_POSTS );
		}
		if ( is_file( MENU_ITEM_ORPHANS ) ) {
			unlink( MENU_ITEM_ORPHANS );
		}
		if ( is_file( MENU_MISSING ) ) {
			unlink( MENU_MISSING );
		}
		if ( is_file( URL_REMAP ) ) {
			unlink( URL_REMAP );
		}
		if ( is_file( POST_ORPHANS ) ) {
			unlink( POST_ORPHANS );
		}
		if ( is_file( FEATURE_IMAGES ) ) {
			unlink( FEATURE_IMAGES );
		}

		if ( ! is_file( $file ) ) {
			return false;
		}

		$woo_datas = file_get_contents( $file );
		$woo_datas = json_decode( $woo_datas, true );
		if ( count( array_filter( $woo_datas ) ) < 1 ) {
			return false;
		}
		$keys = array_keys( $woo_datas );
		foreach ( $keys as $key ) {
			if ( $option_data = unserialize( $woo_datas[ $key ] ) ) {
				update_option( $key, unserialize( $woo_datas[ $key ] ) );
			} else {
				$format = __( 'Update Option "%d" error', 'thim-framework' );
				echo sprintf( $format, $key );
			}
		}

		return true;
	}

	/**
	 * @param $option_file File path of restore setting file
	 */
	function set_options( $option_file ) {
		$theme_slug = get_option( 'stylesheet' );

		if ( ! is_file( $option_file ) ) {
			return false;
		}

		$setting_data = json_decode( thim_file_get_contents( $option_file ), true );
		foreach ( $setting_data as $mod => $value ) {
			if ( $mod ) {
				set_theme_mod( $mod, $value );
			}
		}

		if ( function_exists( 'thim_generate' ) && function_exists( 'thim_options_variation' ) ) {
			$options = get_theme_mods();
			thim_options_variation( $options );
			thim_generate( THIM_DIR . 'style', '.css', $options );
		}

		return true;
	}

	/**
	 * @return bool
	 * Function reset menu for theme
	 */
	function set_menus() {
		return true;
	}

	/**
	 *
	 */
	function import_revslider() {

		@$data_rev = file_get_contents( REV_IMPORT );

		if ( $handle = opendir( EXIST_REV_IMPORT/* . "inc/admin/data/revslider"*/ ) ) {
			$check = 0;
			while ( false !== ( $entry = readdir( $handle ) ) ) {
				if ( $entry != "." && $entry != ".." ) {
					$_FILES['import_file']['tmp_name'] = EXIST_REV_IMPORT/*TP_THEME_THIM_DIR . "inc/admin/data/revslider/"*/ . '/' . $entry;
					if ( $data_rev == $entry ) {
						$check = 1;
						continue;
					}
					if ( ! $data_rev ) {
						$check = 1;
					}
					if ( class_exists( 'RevSlider' ) ) {
						if ( $check ) {
							$slider   = new RevSlider();
							$response = $slider->importSliderFromPost( true, true );
							file_put_contents( REV_IMPORT, $entry );

							return 1;
						}
					} else {
						return false;
					}
				}
			}
			closedir( $handle );
		} else {
			return false;
		}
		unlink( REV_IMPORT );

		return 2;
	}

	/**
	 * @param $import_array Widget Json
	 *
	 * @return bool
	 */
	function import_widgets( $import_array ) {

		global $wp_registered_sidebars;
		$json_data     = $import_array;
		$json_data     = json_decode( $json_data, true );
		$sidebars_data = $json_data[0];
		$widget_data   = $json_data[1];
		$new_widgets   = array();

		foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

			foreach ( $import_widgets as $import_widget ) :
				//if the sidebar exists
				if ( isset( $wp_registered_sidebars[ $import_sidebar ] ) ) :
					$title               = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
					$index               = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
					$current_widget_data = get_option( 'widget_' . $title );
					$new_widget_name     = $this->new_widget_name( $title, $index );
					$new_index           = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

					if ( ! empty( $new_widgets[ $title ] ) && is_array( $new_widgets[ $title ] ) ) {
						while ( array_key_exists( $new_index, $new_widgets[ $title ] ) ) {
							$new_index ++;
						}
					}
					$current_sidebars[ $import_sidebar ][] = $title . '-' . $new_index;
					if ( array_key_exists( $title, $new_widgets ) ) {
						$new_widgets[ $title ][ $new_index ] = $widget_data[ $title ][ $index ];
						$multiwidget                         = $new_widgets[ $title ]['_multiwidget'];
						unset( $new_widgets[ $title ]['_multiwidget'] );
						$new_widgets[ $title ]['_multiwidget'] = $multiwidget;
					} else {
						$current_widget_data[ $new_index ] = $widget_data[ $title ][ $index ];
						$new_multiwidget                   = isset( $widget_data[ $title ]['_multiwidget'] ) ? $widget_data[ $title ]['_multiwidget'] : false;
						$current_multiwidget               = isset( $current_widget_data['_multiwidget'] ) ? $current_widget_data['_multiwidget'] : false;
						$multiwidget                       = ( $current_multiwidget != $new_multiwidget ) ? $current_multiwidget : 1;
						unset( $current_widget_data['_multiwidget'] );
						$current_widget_data['_multiwidget'] = $multiwidget;
						$new_widgets[ $title ]               = $current_widget_data;
					}

				endif;
			endforeach;
		endforeach;
		if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
			update_option( 'sidebars_widgets', $current_sidebars );

			foreach ( $new_widgets as $title => $content ) {
				update_option( 'widget_' . $title, $content );
			}
		}
		// IMPORT Widget Logic
		$widgets_logic_file = TP_THEME_THIM_DIR . 'inc/admin/data/widget/widget_logic_options.txt'; // widgets data file

		if ( is_file( $widgets_logic_file ) ) {
			$import = explode( "\n", file_get_contents( $widgets_logic_file ) );

			if ( trim( array_shift( $import ) ) == "[START=WIDGET LOGIC OPTIONS]" && trim( array_pop( $import ) ) == "[STOP=WIDGET LOGIC OPTIONS]" ) {

				foreach ( $import as $import_option ) {
					list( $key, $value ) = explode( "\t", $import_option );
					echo "$key, $value <br/>";
					$wl_options[ $key ] = json_decode( $value );
				}
				$wl_options['msg'] = __( 'Success! Options file imported', 'widget-logic' );
			} else {
				$wl_options['msg'] = __( 'Invalid options file', 'widget-logic' );
			}

			update_option( 'widget_logic', $wl_options );
		}

		return true;
	}

	/**
	 * @param $widget_name  Name of widget
	 * @param $widget_index Number order in withget
	 *
	 * @return string
	 */
	function new_widget_name( $widget_name, $widget_index ) {
		$current_sidebars = get_option( 'sidebars_widgets' );
		$all_widget_array = array();
		foreach ( $current_sidebars as $sidebar => $widgets ) {
			if ( ! empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
				foreach ( $widgets as $widget ) {
					$all_widget_array[] = $widget;
				}
			}
		}
		while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
			$widget_index ++;
		}
		$new_widget_name = $widget_name . '-' . $widget_index;

		return $new_widget_name;
	}

}
