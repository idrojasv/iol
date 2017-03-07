<?php
/**
 * Template for displaying the enroll button of a course
 */
learn_press_prevent_access_directly();

global $course;
$course_status = learn_press_get_user_course_status();
// only show enroll button if user had not enrolled

if ( ( '' == $course_status && $course->is_require_enrollment() ) ) {

	do_action( 'learn_press_before_course_enroll_button' );
	?>
	<button class="btn take-course thim-enroll-course-button" data-course-id="<?php the_ID(); ?>" data-text="<?php esc_attr_e( 'Take this course', 'eduma' ); ?>" data-loading-text="<?php esc_attr_e('Processing', 'eduma'); ?>"><?php esc_html_e( 'Take this course', 'eduma' ); ?></button>
	<?php do_action( 'learn_press_after_course_enroll_button' ); ?>

<?php } ?>