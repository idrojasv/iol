<?php
do_action( 'learn_press_before_all_courses' );
// All enrolled courses
echo '<h3 class="box-title">' . esc_html__( 'Learning courses', 'eduma' ) . '</h3>';
do_action( 'learn_press_before_enrolled_course' );
$my_query = learn_press_get_enrolled_courses( $user->ID );
if ( $my_query->have_posts() ) :
	echo '<div class="thim-carousel-wrapper thim-course-carousel thim-course-grid" data-visible="3" data-pagination="0" data-navigation="1">';
	while ( $my_query->have_posts() ) : $my_query->the_post();
		learn_press_get_template( 'profile/user-courses/enrolled-course.php' );
	endwhile;
	echo '</div>';
else :
	do_action( 'learn_press_before_no_enrolled_course' );
	echo '<p>' . esc_html__( 'You have not taken any courses yet!', 'eduma' ) . '</p>';
	do_action( 'learn_press_after_no_enrolled_course' );
endif;
do_action( 'learn_press_after_enrolled_course' );
wp_reset_postdata();

// All passed courses
echo '<h3 class="box-title">' . esc_html__( 'Finished courses', 'eduma' ) . '</h3>';
do_action( 'learn_press_before_passed_course' );
$my_query = learn_press_get_passed_courses( $user->ID );
if ( $my_query->have_posts() ) :
	echo '<div class="thim-carousel-wrapper thim-course-carousel thim-course-grid" data-visible="3" data-pagination="0" data-navigation="1">';
	while ( $my_query->have_posts() ) : $my_query->the_post();
		learn_press_get_template( 'profile/user-courses/passed-course.php' );
	endwhile;
	echo '</div>';

else :
	do_action( 'learn_press_before_no_passed_course' );
	echo '<p class="thim-empty-course">' . esc_html__( 'You have not finished any courses yet!', 'eduma' ) . '</p>';
	do_action( 'learn_press_after_no_passed_course' );
endif;
do_action( 'learn_press_after_passed_course' );
wp_reset_postdata();

// All own courses
if ( user_can( $user->ID, 'edit_lpr_courses' ) ) {

	echo '<h3 class="box-title">' . esc_html__( 'Own courses', 'eduma' ) . '</h3>';
	do_action( 'learn_press_before_own_course' );
	$my_query = learn_press_get_own_courses( $user->ID );
	if ( $my_query->have_posts() ) :
		echo '<div class="thim-carousel-wrapper thim-course-carousel thim-course-grid" data-visible="3" data-pagination="0" data-navigation="1">';
		while ( $my_query->have_posts() ) : $my_query->the_post();
			learn_press_get_template( 'profile/user-courses/own-course.php' );
		endwhile;
		echo '</div>';
	else :
		do_action( 'learn_press_before_no_own_course' );
		echo '<p>' . esc_html__( 'You don\'t have got any published courses yet!', 'eduma' ) . '</p>';
		do_action( 'learn_press_after_no_own_course' );
	endif;
	do_action( 'learn_press_after_own_course' );
	wp_reset_postdata();
};

do_action( 'learn_press_after_all_courses' );
