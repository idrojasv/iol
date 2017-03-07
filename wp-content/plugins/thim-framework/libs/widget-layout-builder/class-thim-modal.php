<?php

/**
 * Class Thim_Modal.
 *
 * @since 0.9.0
 */
class Thim_Modal extends Thim_Singleton {
	/**
	 * Thim_Modal constructor.
	 *
	 * @since 0.9.0
	 */
	protected function __construct() {
		$this->init_hooks();
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.9.0
	 */
	private function init_hooks() {
		add_action( 'admin_footer', array( $this, 'add_iframe_template' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 0.9.1
	 */
	public function enqueue_scripts() {
		wp_register_script( 'thim-modal', TP_THEME_FRAMEWORK_URI . 'libs/widget-layout-builder/js/modal.js', array( 'jquery', 'backbone' ), TP_FRAMEWORK_VERSION );
		wp_register_style( 'thim-modal', TP_THEME_FRAMEWORK_URI . 'libs/widget-layout-builder/css/modal.css', array(  ), TP_FRAMEWORK_VERSION );
	}

	/**
	 * Add iframe template.
	 *
	 * @since 0.9.0
	 */
	public function add_iframe_template() {
		$file = TP_FRAMEWORK_LIBS_DIR . '//widget-layout-builder/templates/iframe.php';
		Thim_Template_Helper::render_template( $file );
	}

	/**
	 * Enqueue script thim modal. You need call this function if you want to add modal.
	 *
	 * @since 0.9.0
	 */
	public static function enqueue_modal() {
		wp_enqueue_script( 'thim-modal' );
		wp_enqueue_style( 'thim-modal' );
	}
}