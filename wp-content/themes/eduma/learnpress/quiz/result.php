<?php
global $learn_press;
if ( ! learn_press_user_has_completed_quiz( learn_press_get_current_user_id() ) ) {
	return;
}
$result = learn_press_get_quiz_result();
?>
<div class="quiz-results">
	<h3 class="result-title"><?php esc_html_e( 'Your result', 'eduma' ); ?></h3>

	<div class="result-summary">
		<div class="result-field correct">
			<span><?php esc_html_e( 'Correct', 'eduma' ); ?></span>
			<span class="value"><?php printf( '%d', $result['correct'] ); ?></span>
		</div>
		<div class="result-field wrong">
			<span><?php esc_html_e( 'Incorrect', 'eduma' ); ?></span>
			<span class="value"><?php printf( '%d', $result['wrong'] ); ?></span>
		</div>
		<div class="result-field empty">
			<span><?php esc_html_e( 'Skipped', 'eduma' ); ?></span>
			<span class="value"><?php printf( '%d', $result['empty'] ); ?></span>
		</div>
		<div class="result-field points">
			<span><?php esc_html_e( 'Points', 'eduma' ); ?></span>
			<span class="value"><?php printf( '%d/%d', $result['mark'], $result['mark_total'] ); ?></span>
		</div>
	</div>
</div>

<?php

$user_id = get_current_user_id();
if ( ! $user_id ) {
	$user_id = learn_press_get_current_user_id();
}
$quiz_id = get_the_ID();
if ( ! learn_press_user_has_completed_quiz( $user_id, $quiz_id ) || ! get_post_meta( $quiz_id, '_lpr_show_quiz_result', true ) ) {
	return;
}
$question_list = learn_press_get_quiz_questions( $quiz_id );
$answers       = learn_press_get_question_answers( $user_id, $quiz_id );
?>

<?php do_action( 'learn_press_quiz_questions_before_questions' ); ?>
<?php if ( $question_list ) { ?>
	<div class="quiz-questions">
		<h3 class="answers-title"><?php esc_html_e( 'Review your answers', 'eduma' ); ?></h3>
		<?php do_action( 'learn_press_quiz_questions_before_questions_loop' ); ?>
		<ul>
			<?php foreach ( $question_list as $question_id => $question_options ) : ?>
				<li class="<?php echo thim_check_answer( $question_id, $answers ); ?>">
					<h4><?php echo get_the_title( $question_id ); ?></h4>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php do_action( 'learn_press_quiz_questions_after_questions_loop' ); ?>
	</div>
<?php } ?>
<?php do_action( 'learn_press_quiz_questions_after_questions' ); ?>

