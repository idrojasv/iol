<?php
/**
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$user        = learn_press_get_current_user();
$course = LP()->global['course'];
$viewable = learn_press_user_can_view_quiz( $item->ID, $course->id );
$tag      = $viewable ? 'a' : 'span';
$target   = apply_filters( 'learn_press_section_item_link_target', '_blank', $item );
?>

<li <?php learn_press_course_item_class( $item->ID ); ?> data-type="<?php echo $item->post_type; ?>">

	<div class="meta-left">
		<?php do_action( 'learn_press_before_section_item_title', $item, $section, $course ); ?>
		<div class="index"><?php echo '<span class="label">'.esc_html__( 'Quiz', 'eduma' ) . '</span>' . $index; ?></div>
	</div>
	<!-- start class meta-center -->
	<div class="meta-center<?php //echo (!$viewable) ? ' has-right' : ''; ?>">
	<<?php echo $tag; ?> class="quiz-title course-item-title button-load-item" target="<?php echo $target; ?>" <?php echo $viewable ? 'href="' . $course->get_item_link( $item->ID ) . '"' : ''; ?> data-id="<?php echo $item->ID; ?>">

		<?php echo apply_filters( 'learn_press_section_item_title', get_the_title( $item->ID ), $item ); ?>

	</<?php echo $tag; ?>>

	<?php if ( $user->can_view_item( $item->ID, $course->id ) !== false ) { ?>
		<div class="completed-button"><i class="fa fa-check"></i></div>
	<?php } ?>

	<?php
	//if ( !$viewable ) {
		//echo '<div class="locked">' . esc_html__( 'Locked', 'eduma' ) . '</div>';
	//}
	?>
	</div>

	<?php do_action( 'learn_press_after_section_item_title', $item, $section, $course ); ?>

</li>