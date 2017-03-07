<?php
/**
 * Template for displaying the content of single quiz
 */
global $quiz;
if ( learn_press_user_has_started_quiz() ) {
	remove_action( 'learn_press_single_quiz_summary', 'learn_press_single_quiz_description' );
}
?>
<?php do_action( 'learn_press_before_single_quiz' ); ?>
	<div itemscope id="quiz-<?php the_ID(); ?>" <?php learn_press_quiz_class(); ?>>
		<?php do_action( 'learn_press_before_single_quiz_summary' ); ?>
		<?php do_action( 'learn_press_single_quiz_summary' ); ?>
		<?php learn_press_single_quiz_time_counter(); ?>
		<?php do_action( 'learn_press_after_single_quiz_summary' ); ?>
	</div>
<?php do_action( 'learn_press_after_single_quiz' ); ?>