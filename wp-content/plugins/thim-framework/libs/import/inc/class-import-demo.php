<?php

/**
 * Create admin menu Import Demo
 *
 * Class TP_Import_Menu
 */
class TP_Import_Menu {

	/**
	 * Construct
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
		add_action( 'wp_ajax_tp_make_site', array( $this, 'make_site_callback' ) );
		add_action( 'wp_ajax_tp_dismiss_notification', array( $this, 'dismiss_notification' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Create new option type: import
	 */
	function create_import_option() {
		require_once( TP_FRAMEWORK_LIBS_DIR . 'import/inc/class-option-import.php' );
	}

	/**
	 * Create admin menu
	 */
	function create_admin_menu() {
		add_submenu_page(
			'tools.php',
			__( 'Demo Importer', 'thim-framework' ),
			__( 'Demo Importer', 'thim-framework' ),
			'manage_options',
			'thim-import-demo',
			'tp_page_content'
		);

		$tp_warring_lost_data = get_option( 'tp_importer_warring_overwritten', - 1 );
		if ( $tp_warring_lost_data == - 1 ) {
			update_option( 'tp_importer_warring_overwritten', 0 );
		}
	}

	/**
	 * Ajax process for importing
	 */
	function make_site_callback() {
		if ( current_user_can( 'manage_options' ) ) {
			require_once( TP_FRAMEWORK_LIBS_DIR . 'import/inc/import/tp-import.php' );
			die;
		}
	}

	/**
	 * Ajax dismiss notification
	 */
	function dismiss_notification() {
		if ( current_user_can( 'manage_options' ) ) {
			require_once( TP_FRAMEWORK_LIBS_DIR . 'import/inc/import/dismiss-notification.php' );
			die;
		}
	}

	/**
	 * admin_enqueue_scripts
	 */
	function admin_enqueue_scripts() {
		wp_register_script( 'tp-admin-import', TP_FRAMEWORK_LIBS_URI . 'import/js/admin.js', array( 'jquery' ), TP_FRAMEWORK_VERSION, true );

		wp_enqueue_script( 'tp-admin-import' );

		wp_localize_script( 'tp-admin-import', 'tp_admin_import', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'wp_nonce' => wp_create_nonce( 'tp_dismiss_goto_import' )
		) );
	}

}

new TP_Import_Menu();
