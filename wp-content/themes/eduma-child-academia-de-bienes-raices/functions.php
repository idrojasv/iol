<?php

function thim_child_enqueue_styles() {
	if ( is_multisite() ) {
		wp_enqueue_style( 'thim-child-style', get_stylesheet_uri() );
	} else {
		wp_enqueue_style( 'thim-parent-style', get_template_directory_uri() . '/style.css' );
	}
}

add_action( 'wp_enqueue_scripts', 'thim_child_enqueue_styles', 100 );

/* Asignar Nivel GRATIS ID=1 a todos los nuevos registros en la página web*/

add_action('user_register', 'wpresidence_pmpro_default_level');
function wpresidence_pmpro_default_level($user_id) {
	pmpro_changeMembershipLevel(1,$user_id);
}
