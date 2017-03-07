<?php
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WC_Product' ) ) {
    return;
}

/**
 * Class WC_Product_LP_Course
 */
class WC_Product_LP_Course extends WC_Product {

    /**
     * Get Price Description
     */
    public function get_price() {
        $course = LP_Course::get_course( $this->post->ID );
        return $course ? $course->get_price() : 0;
    }

    /**
     * Check if a product is purchasable
     */
    public function is_purchasable() {
        return $course = LP_Course::get_course( $this->post->ID );
    }

	/**
	 * 
	 * @return type
	 */
	public function is_virtual() {
        return apply_filters( 'learn_press_wc_product_lp_course_is_virtual', true , $this );
    }

}
