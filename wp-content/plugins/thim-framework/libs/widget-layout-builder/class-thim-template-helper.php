<?php

/**
 * Class Thim_Template_Helper.
 *
 * @since 0.9.0
 *
 */
class Thim_Template_Helper {
	/**
	 * Render template.
	 *
	 * @since 0.9.0
	 *
	 * @param $file
	 * @param $args
	 *
	 * @return bool
	 */
	public static function render_template( $file, $args = array() ) {
		if ( ! is_file( $file ) || ! is_readable( $file ) ) {
			return false;
		}

		include $file;

		return true;
	}

	/**
	 * Get template.
	 *
	 * @since 0.9.0
	 *
	 * @param $file
	 * @param $args
	 *
	 * @return bool|string
	 */
	public static function get_template( $file, $args = array() ) {
		if ( ! is_file( $file ) || ! is_readable( $file ) ) {
			return false;
		}

		ob_start();
		include $file;

		return ob_get_clean();
	}
}