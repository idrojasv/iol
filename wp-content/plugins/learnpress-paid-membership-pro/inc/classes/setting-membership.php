<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class LP_Settings_PMPro_Membership extends LP_Settings_Base {

	/**
	 * Constructor
	 */
	public function __construct () {
		$this->id   = 'membership';
		$this->text = __( 'Memberships', 'learnpress-paid-membership-pro' );

		parent::__construct();
	}

	/**
	 * Tab's sections
	 *
	 * @return mixed
	 */
	public function get_sections () {
		$sections = array(
			'general' => array(
				'id'    => 'general',
				'title' => __( 'Settings', 'learnpress-paid-membership-pro' )
			)
		);

		return $sections = apply_filters( 'learn_press_settings_sections_' . $this->id, $sections );
	}

	public function output_section_general () {
		include LP_ADDON_PMPRO_PATH . '/inc/views/membership.php';
	}

	public function get_settings () {
		return apply_filters(
			'learn_press_membership_settings',
			array(
				array( 'section' => 'general' ),
				array(
					'title' => __( 'Paid Memberships Pro add-on for LearnPress', 'learnpress-paid-membership-pro' ),
					'type'  => 'title'
				),
				array(
					'title'   => __( 'Always buy the course through membership', 'learnpress' ),
					'id'      => $this->get_field_name( 'buy_through_membership' ),
					'default' => 'no',
					'type'    => 'checkbox'
				),
				array(
					'title'   => __( 'Button Buy Course', 'learnpress' ),
					'id'      => $this->get_field_name( 'button_buy_course' ),
					'default' => 'Buy Now',
					'type'    => 'text'
				),
				array(
					'title'   => __( 'Button Buy Membership', 'learnpress' ),
					'id'      => $this->get_field_name( 'button_buy_membership' ),
					'default' => 'Buy Membership',
					'type'    => 'text'
				)
			)
		);
	}
}

add_filter( 'learn_press_settings_class_membership', 'lp_pmpro_filter_class_setting_membership' );

function lp_pmpro_filter_class_setting_membership () {
	return 'LP_Settings_PMPro_Membership';
}

return new LP_Settings_PMPro_Membership();