<?php

class LP_Certificates_Admin_Settings extends LP_Settings_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id   = 'certificates';
		$this->text = __( 'Certificates', 'learnpress-paid-membership-pro' );
		//add_action( 'learn_press_settings_save_' . $this->id, array( $this, 'update_settings' ) );
		add_filter( 'learn_press_settings_class_certificates', array( $this, 'settings_class' ) );

		parent::__construct();
	}

	public function settings_class() {
		return 'LP_Certificates_Admin_Settings';
	}

	public function save() {
		parent::save();
	}

	public function output_section_general() {
		include LP_ADDON_CERTIFICATES_PATH . '/incs/html/admin-settings.php';
	}

	/**
	 * Tab's sections
	 *
	 * @return mixed
	 */
	public function get_sections() {
		$sections = array(
			'general' => array(
				'id'    => 'general',
				'title' => __( 'Settings', 'learnpress-paid-membership-pro' )
			)
		);

		return $sections = apply_filters( 'learn_press_settings_sections_' . $this->id, $sections );
	}

	public function get_settings() {
		return apply_filters(
			'learn_press_certificates_settings', array(
				array(
					'title' => __( 'Share on social networks', 'learnpress-paid-membership-pro' ),
					'type'  => 'title'
				),
				array(
					'title'   => __( 'Facebook', 'learnpress-paid-membership-pro' ),
					'id'      => $this->get_field_name( 'cert_share[facebook]' ),
					'default' => 'yes',
					'type'    => 'checkbox'
				),
				array(
					'title'   => __( 'Twitter', 'learnpress-paid-membership-pro' ),
					'id'      => $this->get_field_name( 'cert_share[twitter]' ),
					'default' => 'yes',
					'type'    => 'checkbox'
				),
				array(
					'title'   => __( 'Plusonce', 'learnpress-paid-membership-pro' ),
					'id'      => $this->get_field_name( 'cert_share[plusone]' ),
					'default' => 'yes',
					'type'    => 'checkbox'
				)
			)
		);
	}
}


return new LP_Certificates_Admin_Settings();