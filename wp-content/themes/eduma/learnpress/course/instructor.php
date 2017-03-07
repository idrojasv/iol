<?php
/**
 * Template for displaying the instructor of a course
 */

learn_press_prevent_access_directly();
do_action( 'learn_press_before_course_instructor' );

?>
	<div class="course-author" itemscope itemtype="http://schema.org/Person">
		<?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
		<div class="author-contain">
			<label itemprop="jobTitle"><?php esc_html_e( 'Teacher', 'eduma' ); ?></label>
			<div class="value" itemprop="name">
				<a itemprop="url" href="<?php echo esc_url( apply_filters( 'learn_press_instructor_profile_link', '#', $user_id = null, get_the_ID() ) ); ?>">
					<?php echo get_the_author(); ?>
				</a>
			</div>
		</div>
	</div>
<?php

do_action( 'learn_press_after_course_instructor' );

