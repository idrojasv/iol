<?php
/**
 * Template for displaying the students of a course
 */
learn_press_prevent_access_directly();
$count = learn_press_count_students_enrolled();
?>
<?php do_action( 'learn_press_before_course_students' ); ?>
<div class="course-students">
	<label><?php esc_html_e( 'Students', 'eduma' ); ?></label>
	<?php do_action( 'learn_press_begin_course_students' ); ?>

	<div class="value"><i class="fa fa-group"></i>
		<?php echo esc_html( $count ); ?>
	</div>
	<?php do_action( 'learn_press_end_course_students' ); ?>

</div>
<?php do_action( 'learn_press_after_course_students' ); ?>
