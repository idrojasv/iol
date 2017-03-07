<?php

$notification = isset( $_REQUEST['notification'] ) ? $_REQUEST['notification'] : '';
$wp_nonce     = isset( $_REQUEST['wp_nonce'] ) ? $_REQUEST['wp_nonce'] : '';

ob_start();
if ( $notification == 'warring_overwritten' ) {
	$count_dismiss = intval( get_option( 'tp_importer_warring_overwritten', 0 ) );
	$count_dismiss ++;
	$updated = update_option( 'tp_importer_warring_overwritten', $count_dismiss );

	if ( $updated ) {
		echo json_encode( array(
			'return' => true,
			'msg'    => '',
			'log'    => ob_get_clean()
		) );
		exit();
	}
} else if ( $notification == 'dismiss_goto_import' ) {
	$verify = wp_verify_nonce( $wp_nonce, 'tp_dismiss_goto_import' );

	if ( $verify === false ) {
		echo json_encode( array(
			'return' => false,
			'msg'    => __( 'WordPress invalid nonce!', 'thim-framework' ),
			'log'    => ob_get_clean()
		) );
	} else {
		set_theme_mod( 'thim_enable_import_demo', false );

		echo json_encode( array(
			'return' => true,
			'msg'    => '',
			'log'    => ob_get_clean()
		) );
		exit();
	}

}