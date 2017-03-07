<?php
$viewable = learn_press_user_can_view_quiz( $lesson_quiz );//learn_press_is_enrolled_course();
$tag      = $viewable ? 'a' : 'span';
$target   = apply_filters( 'learn_press_quiz_link_target', '_blank', $lesson_quiz );
?>
<li <?php learn_press_course_quiz_class( $lesson_quiz ); ?>>


	<?php do_action( 'learn_press_course_lesson_quiz_before_title', $lesson_quiz, $viewable ); ?>

	<span class="index"><?php echo esc_html__( 'Quiz', 'eduma' ) . ' ' . $index; ?></span>

	<<?php echo ent2ncr( $tag ); ?> class="quiz-title" target="<?php echo esc_attr( $target ); ?>" <?php echo ( $viewable ) ? 'href="' . get_the_permalink( $lesson_quiz ) . '"' : ''; ?> data-id="<?php echo esc_attr( $lesson_quiz ); ?>">
	<?php do_action( 'learn_press_course_lesson_quiz_begin_title', $lesson_quiz, $viewable ); ?>
	<?php echo get_the_title( $lesson_quiz ); ?>
	<?php do_action( 'learn_press_course_lesson_quiz_end_title', $lesson_quiz, $viewable ); ?>
</<?php echo ent2ncr($tag); ?>>
<?php do_action( 'learn_press_course_lesson_quiz_after_title', $lesson_quiz, $viewable ); ?>
<?php if ( learn_press_user_has_completed_quiz( null, $lesson_quiz ) ) {
	echo '<span class="completed-button">' . esc_html__( 'Completed', 'eduma' ) . '</span>';
}
?>
<?php
if ( !$viewable ) {
	echo '<span class="locked">' . esc_html__( 'Locked', 'eduma' ) . '</span>';
}
?>
<span class="meta"><?php echo thim_quiz_questions( $lesson_quiz ) . ' ' . esc_html__( 'questions', 'eduma' ); ?></span>

</li>