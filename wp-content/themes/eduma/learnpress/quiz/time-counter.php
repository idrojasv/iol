<?php
/**
 * Template for displaying the remaining time of a quiz
 *
 */
learn_press_prevent_access_directly();

if ( learn_press_user_has_completed_quiz() ) {
	return;
}
if ( learn_press_get_quiz_duration() ):
	?>
	<?php do_action( 'learn_press_before_quiz_clock' ); ?>
	<div class="quiz-clock">
		<div class="quiz-total">
			<i class="fa fa-bullhorn"></i>
			<div class="quiz-text"><?php echo '<span class="number">'.thim_quiz_questions( get_the_ID() ) . '</span> '; ?><?php echo thim_quiz_questions( get_the_ID() ) > 1 ? esc_html__( 'Questions', 'eduma' ) : esc_html__( 'Question', 'eduma' ); ?></div>
		</div>

		<div class="quiz-timer">
			<i class="fa fa-clock-o"></i>
			<span class="quiz-text"><?php echo apply_filters( 'learn_press_quiz_time_remaining_text', esc_html__( 'Time', 'eduma' ) ); ?></span>
			<span id="quiz-countdown"></span>
			<span class="quiz-text"><?php echo apply_filters( 'learn_press_quiz_time_label', esc_html__( '(h:m:s)', 'eduma' ) ); ?></span>
		</div>
	</div>
	<?php do_action( 'learn_press_after_quiz_clock' ); ?>
<?php endif; ?>