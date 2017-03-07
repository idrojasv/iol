<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $course;
?>

<?php do_action( 'learn_press_before_main_content' ); ?>

<?php
while ( have_posts() ) : the_post();

	learn_press_get_template( 'course_content.php' );
	thim_related_courses();

endwhile;
?>
<?php do_action( 'learn_press_after_main_content' ); ?>

