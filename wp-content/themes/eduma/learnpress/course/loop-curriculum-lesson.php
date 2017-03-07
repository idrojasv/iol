<?php
$viewable = learn_press_user_can_view_lesson( $lesson_quiz );//learn_press_is_enrolled_course();
$class    = $viewable ? 'viewable' : '';
$tag      = $viewable ? 'a' : 'span';

?>
<li <?php learn_press_course_lesson_class( $lesson_quiz, $class ); ?>>


	<?php do_action( 'learn_press_course_lesson_quiz_before_title', $lesson_quiz, $viewable ); ?>
	<span class="index"><?php echo esc_html__( 'Lecture', 'eduma' ) . ' ' . $index; ?></span>

	<<?php echo ent2ncr( $tag ); ?> <?php echo ( $viewable ) ? 'href="' . learn_press_get_course_lesson_permalink( $lesson_quiz ) . '"' : ''; ?> class="lesson-title" data-effect="mfp-zoom-in" lesson-id="<?php echo esc_attr( $lesson_quiz ); ?>" data-id="<?php echo esc_attr( $lesson_quiz ); ?>">
	<?php do_action( 'learn_press_course_lesson_quiz_begin_title', $lesson_quiz, $viewable ); ?>
	<?php echo get_the_title( $lesson_quiz ); ?>
	<?php do_action( 'learn_press_course_lesson_quiz_end_title', $lesson_quiz, $viewable ); ?>
</<?php echo ent2ncr( $tag ); ?>>
<?php do_action( 'learn_press_course_lesson_quiz_after_title', $lesson_quiz, $viewable ); ?>

<?php if ( !learn_press_is_enrolled_course() && $viewable && learn_press_is_lesson_preview( $lesson_quiz ) ) : ?>
	<a class="lesson-preview" href="<?php echo esc_url( learn_press_get_course_lesson_permalink( $lesson_quiz ) ); ?>" lesson-id="<?php echo esc_attr( $lesson_quiz ); ?>" data-id="<?php echo esc_attr( $lesson_quiz ); ?>">
		<?php esc_html_e( 'Preview', 'eduma' ); ?>
	</a>
<?php endif; ?>

<?php if ( learn_press_user_has_completed_lesson( $lesson_quiz ) ) {
	echo '<span class="completed-button">' . esc_html__( 'Completed', 'eduma' ) . '</span>';
}
?>

<?php
if ( !$viewable ) {
	echo '<span class="locked">' . esc_html__( 'Locked', 'eduma' ) . '</span>';
}
?>

<span class="meta"><?php echo thim_lesson_duration( $lesson_quiz ); ?></span>

</li>