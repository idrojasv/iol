<?php
/**
 * @author        ThimPress
 * @package       LearnPress/Templates
 * @version       2.1.4
 */

defined( 'ABSPATH' ) || exit();

?>

<?php do_action( 'learn_press_before_item_meta', $item ); ?>

<?php if ( $item->post_type == 'lp_quiz' ) : ?>
	<div class="meta"><?php echo thim_quiz_questions( $item->ID ) . ' ' . esc_html__( 'questions', 'eduma' ); ?></div>
<?php else: ?>
	<div class="meta"><?php echo thim_lesson_duration( $item->ID ); ?></div>
<?php endif ?>

<?php do_action( 'learn_press_after_item_meta', $item ); ?>

