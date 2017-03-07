<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css' );
    }
endif;
load_child_theme_textdomain('wpestate', get_stylesheet_directory().'/languages');
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css' );

// END ENQUEUE PARENT ACTION

/* Asignar Nivel GRATIS ID=1 a todos los nuevos registros en la página web*/

add_action('user_register', 'wpresidence_pmpro_default_level');
function wpresidence_pmpro_default_level($user_id) {
	pmpro_changeMembershipLevel(1,$user_id);
}
