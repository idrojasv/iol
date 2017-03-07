<?php
/**
 * WooCommerce Payments Addon functions
 *
 * @author  ThimPress
 * @version 2.2
 * @package LearnPress/Functions
 */

defined( 'ABSPATH' ) || exit();
define( 'LP_WC_TEMPLATE', learn_press_template_path() . '/addons/woo-payment/' );
/**
 * Get template file for addon
 *
 * @param      $name
 * @param null $args
 */
function learn_press_wc_get_template( $name, $args = null ) {
	if ( file_exists( learn_press_locate_template( $name, 'learnpress', LP_WC_TEMPLATE ) ) ) {
		learn_press_get_template( $name, $args, 'learnpress-woo-payment/', get_template_directory() . '/' . LP_WC_TEMPLATE );
	} else {
		learn_press_get_template( $name, $args, LP_WC_TEMPLATE, LP_ADDON_WOOCOMMERCE_PAYMENT_PATH . '/templates/' );
	}
}

function learn_press_wc_locate_template( $name ) {
	// Look in folder learnpress-woo-payment in the theme first
	$file = learn_press_locate_template( $name, 'learnpress', LP_WC_TEMPLATE );

	// If template does not exists then look in learnpress/addons/woo-payment in the theme
	if ( !file_exists( $file ) ) {
		$file = learn_press_locate_template( $name, LP_WC_TEMPLATE, LP_ADDON_WOOCOMMERCE_PAYMENT_PATH . '/templates/' );
	}
	return $file;
}