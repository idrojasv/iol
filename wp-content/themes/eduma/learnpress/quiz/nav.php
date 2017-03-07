<?php
learn_press_prevent_access_directly();
if ( learn_press_user_has_completed_quiz( learn_press_get_current_user_id() ) ) {
	return;
}
$prev = null;
$next = null;
?>
<?php do_action( 'learn_press_before_quiz_question_nav_buttons' ); ?>
<div class="quiz-question-nav-buttons">
	<?php $question_id = !empty( $_REQUEST['question'] ) ? $_REQUEST['question'] : 0; ?>
	<button type="button" data-nav="prev" class="prev-question btn" data-url="<?php echo esc_attr($prev); ?>"><?php esc_html_e( 'Back', 'eduma' ); ?></button>
	<button type="button" data-nav="next" class="next-question btn" data-url="<?php echo esc_attr($next); ?>"><?php esc_html_e( 'Next', 'eduma' ); ?></button>
</div>
<?php do_action( 'learn_press_after_quiz_question_nav_buttons' ); ?>
