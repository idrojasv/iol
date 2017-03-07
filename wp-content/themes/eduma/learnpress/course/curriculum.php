<?php
/**
 * Template for displaying the curriculum of a course
 */
// template variable $curriculum
learn_press_prevent_access_directly();
global $course;
$section = 1;
?>
<?php do_action( 'learn_press_before_course_curriculum' ); ?>
	<div class="course-curriculum">
		<?php if ( $curriculum = $course->get_curriculum() ): ?>
			<ul class="curriculum-sections">
				<?php foreach ( $curriculum as $course_part ) : ?>
					<?php
					learn_press_get_template(
						'course/loop-curriculum.php',
						array(
							'curriculum_course' => $course_part,
							'section_index'           => $section
						)
					); ?>
					<?php $section ++; ?>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<p><?php esc_html_e( 'Curriculum is empty', 'eduma' ); ?></p>
		<?php endif; ?>
	</div>
<?php
do_action( 'learn_press_after_course_curriculum' );
