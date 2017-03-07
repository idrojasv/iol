<?php
/**
 * Custom functions
 */
defined( 'ABSPATH' ) || exit();
define( 'LP_PMPRO_TEMPLATE', learn_press_template_path() . '/addons/paid-membership-pro/' );

/**
 * Get template file for addon
 *
 * @param      $name
 * @param null $args
 */
function learn_press_pmpro_get_template ( $name, $args = null ) {
	if ( file_exists( learn_press_locate_template( $name, 'learnpress-paid-membership-pro', LP_PMPRO_TEMPLATE ) ) ) {
		learn_press_get_template( $name, $args, 'learnpress-paid-membership-pro/', get_template_directory() . '/' . LP_PMPRO_TEMPLATE );
	} else {
		learn_press_get_template( $name, $args, LP_PMPRO_TEMPLATE, LP_ADDON_PMPRO_PATH . '/templates/' );
	}
}

function learn_press_pmpro_locate_template ( $name ) {
	// Look in folder learnpress-paid-membership-pro in the theme first
	$file = learn_press_locate_template( $name, 'learnpress-paid-membership-pro', LP_PMPRO_TEMPLATE );

	// If template does not exists then look in learnpress/addons/paid-membership-pro in the theme
	if ( ! file_exists( $file ) ) {
		$file = learn_press_locate_template( $name, LP_PMPRO_TEMPLATE, LP_ADDON_PMPRO_PATH . '/templates/' );
	}

	return $file;
}

function lp_pmpro_query_course_by_level ( $level_id ) {
	global $learn_press_pmpro_cache;

	$level_id = intval( $level_id );

	if ( ! empty( $learn_press_pmpro_cache[ 'query_level_' . $level_id ] ) ) {
		return $learn_press_pmpro_cache[ 'query_level_' . $level_id ];
	}
	$post_type                                             = LP_COURSE_CPT;
	$args                                                  = array(
		'post_type'      => array( $post_type ),
		'post_status'    => array( 'publish' ),
		'posts_per_page' => - 1,
		'meta_query'     => array(
			array(
				'key'   => '_lp_pmpro_levels',
				'value' => $level_id,
			),
		),
	);
	$query                                                 = new WP_Query( $args );
	$learn_press_pmpro_cache[ 'query_level_' . $level_id ] = $query;

	return $query;
}

function lp_pmpro_get_all_levels () {
	$pmpro_levels = pmpro_getAllLevels( false, true );
	$pmpro_levels = apply_filters( 'lp_pmpro_levels_array', $pmpro_levels );

	return $pmpro_levels;
}

function lp_pmpro_get_all_levels_id ( $pmpro_levels ) {
	if ( empty( $pmpro_levels ) ) {
		return array();
	}
	$return = array();
	foreach ( $pmpro_levels as $level ) {
		$return[] = $level->id;
	}

	return $return;
}

function lp_pmpro_list_courses ( $levels = null ) {

	global $current_user;
	$list_courses = array();

	if ( ! $levels ) {
		$levels = lp_pmpro_get_all_levels();
	}
	foreach ( $levels as $index => $level ) {
		$the_query = lp_pmpro_query_course_by_level( $level->id );
		if ( ! empty( $the_query->posts ) ) {
			foreach ( $the_query->posts as $key => $course ) {
				$list_courses[ $course->ID ]['id']   = $course->ID;
				$list_courses[ $course->ID ]['link'] = '<a href="' . get_the_permalink( $course->ID ) . '" >' . get_the_title( $course->ID ) . '</a>';
				if ( empty( $list_courses[ $course->ID ]['level'] ) ) {
					$list_courses[ $course->ID ]['level'] = array();
				}
				if ( ! in_array( $level->id, $list_courses[ $course->ID ]['level'] ) ) {
					$list_courses[ $course->ID ]['level'][] = $level->id;
				}
			}
		}

	}
	$list_courses = apply_filters( 'learn_press_pmpro_list_courses', $list_courses, $current_user, $levels );

	return $list_courses;
}

function learn_press_pmpro_check_require_template () {

	global $current_user, $post;
	$user_id        = get_current_user_id();
	$user           = learn_press_get_user( $user_id, true );
	$levels_page_id = pmpro_getOption( "levels_page_id" );
	$all_levels     = lp_pmpro_get_all_levels();
	$all_levels_id  = lp_pmpro_get_all_levels_id( $all_levels );
	$course         = learn_press_get_course( $post->ID );
	$levels         = lp_pmpro_get_all_levels();
	$list_courses   = lp_pmpro_list_courses( $levels );

	/**
	 * Return if user have purchased this course
	 */
	if ( $user->has_purchased_course( $post->ID ) ) {
		return false;
	}

	/**
	 * Return if page is not include any levels membership
	 */
	if ( empty( $levels_page_id ) ) {
		return false;
	}

	/**
	 * Check if this course not assign anyone membership level
	 */
	if ( empty( $list_courses[ $course->ID ] ) ) {
		return false;
	}

	/**
	 * Return if current user is buy this level membership of current page
	 */
	if ( $current_user->membership_levels ) {

		// List memberships level is accessed into this course
		$list_memberships_of_course = lp_pmpro_list_courses( $current_user->membership_levels );

		foreach ( $current_user->membership_levels as $level ) {
			if ( in_array( $level->ID, $list_memberships_of_course ) ) {
				return false;
			}
		}
	}

	/**
	 * Return if not exists level membership
	 */
	if ( empty( $all_levels ) ) {
		return false;
	}

	return array(
		'current_user'   => $current_user,
		'post'           => $post,
		'user_id'        => $user_id,
		'user'           => $user,
		'levels_page_id' => $levels_page_id,
		'all_levels'     => $all_levels,
		'all_levels_id'  => $all_levels_id,
		'course'         => $course,
		'levels'         => $levels,
		'list_courses'   => $list_courses
	);

}