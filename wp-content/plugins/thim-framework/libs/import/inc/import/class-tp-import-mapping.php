<?php

class TP_Import_Mapping {
	private $terms;
	private $posts;

	/**
	 * TP_Import_Mapping constructor.
	 */
	public function __construct() {
		$this->terms = $this->get_arr_mapping( 'thim_import_mapping_terms' );
		$this->posts = $this->get_arr_mapping( 'thim_import_mapping_posts' );
	}

	/**
	 * Run mapping.
	 */
	public function mapping() {
		$this->remap_nav_menu();
		$this->remap_so_layout_builder();
	}

	/**
	 * @param $key_option
	 *
	 * @return array
	 */
	private function get_arr_mapping( $key_option ) {
		$option = get_option( $key_option );

		if ( ! is_array( $option ) ) {
			return array();
		}

		return $option;
	}

	/**
	 * Remap widget nav menu
	 */
	private function remap_nav_menu() {
		$nav_menus = get_option( 'widget_nav_menu' );

		if ( empty( $nav_menus ) ) {
			return;
		}

		$terms = $this->terms;

		if ( empty( $terms ) ) {
			return;
		}

		foreach ( $nav_menus as $key => $nav_menu ) {
			$nav_menu_id = ! empty( $nav_menu['nav_menu'] ) ? (int) $nav_menu['nav_menu'] : false;

			if ( ! $nav_menu_id ) {
				continue;
			}

			if ( empty( $terms[ $nav_menu_id ] ) ) {
				continue;
			}

			$new_nav_menu_id = (int) $terms[ $nav_menu_id ];
			$nav_menu_obj    = wp_get_nav_menu_object( $new_nav_menu_id );

			if ( ! $nav_menu_obj ) {
				continue;
			}

			$nav_menu['nav_menu'] = $new_nav_menu_id;
			$nav_menus[ $key ]    = $nav_menu;
		}

		update_option( 'widget_nav_menu', $nav_menus );
	}

	/**
	 * Remap nav menu in site origin layout builder.
	 */
	private function remap_so_layout_builder() {
		$widgets = get_option( 'widget_siteorigin-panels-builder' );

		if ( ! is_array( $widgets ) ) {
			return;
		}

		foreach ( $widgets as $key => $widget ) {
			$_panels_data = self::_arr_get_arr( $widget, 'panels_data' );

			if ( ! is_array( $_panels_data ) ) {
				continue;
			}

			$_widgets = self::_arr_get_arr( $_panels_data, 'widgets' );
			if ( ! is_array( $_widgets ) ) {
				continue;
			}

			foreach ( $_widgets as $_key => $_widget ) {
				$nav_menu_id = ! empty( $_widget['nav_menu'] ) ? (int) $_widget['nav_menu'] : false;

				if ( ! $nav_menu_id ) {
					continue;
				}

				$new_nav_menu_id = $this->_get_mapping_id_term( $nav_menu_id );
				$nav_menu_obj    = wp_get_nav_menu_object( $new_nav_menu_id );
				if ( ! $nav_menu_obj ) {
					continue;
				}

				$_widget['nav_menu'] = $new_nav_menu_id;
				$_widgets[ $_key ]   = $_widget;
			}

			$widget['panels_data']['widgets'] = $_widgets;
			$widgets[ $key ]                  = $widget;

			update_option( 'widget_siteorigin-panels-builder', $widgets );
		}




	}

	private static function _arr_get_arr( $arr, $key = array() ) {
		$value = isset( $arr[ $key ] ) ? $arr[ $key ] : false;

		if ( ! is_array( $value ) ) {
			return false;
		}

		return $value;
	}

	private function _get_mapping_id_term( $old_id ) {
		$terms = $this->terms;

		if ( empty( $terms[ $old_id ] ) ) {
			return false;
		}

		return $terms[ $old_id ];
	}
}