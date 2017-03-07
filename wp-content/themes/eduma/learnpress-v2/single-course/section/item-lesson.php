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
$course      = LP()->global['course'];
$viewable    = learn_press_user_can_view_lesson( $item->ID, $course->id );//learn_press_is_enrolled_course();
$tag         = $viewable ? 'a' : 'span';
$target      = apply_filters( 'learn_press_section_item_link_target', '_blank', $item );
//$item_status = $user->get_item_status( $item->ID );
$item_status = $user->get_item_status( $item->ID );
$security    = wp_create_nonce( sprintf( 'complete-item-%d-%d-%d', $user->id, $course->id, $item->ID ) );
//xxx($item_status);
$course_item = $course->get_item( $item->ID );

$is_enrolled      = $user->has_enrolled_course( $course->id );
$require_enrolled = $course->is_require_enrollment();

$item_preview = $item_status !== 'completed' && !$is_enrolled && $require_enrolled && $course_item->is_preview();

//$meta_center_class = ( !$viewable || $item_preview || $item_status === 'completed' ) ? 'meta-center has-right' : 'meta-center';
$meta_center_class = ( $item_preview ) ? 'meta-center has-right' : 'meta-center';
$meta_center_class = ( $item_status === 'completed' ) ? $meta_center_class.' has-completed' : $meta_center_class;

?>

<li <?php learn_press_course_item_class( $item->ID ); ?> data-type="<?php echo $item->post_type; ?>">

	<div class="meta-left">
		<?php do_action( 'learn_press_before_section_item_title', $item, $section, $course ); ?>
		<div class="index"><?php echo '<span class="label">' . esc_html__( 'Lecture', 'eduma' ) . '</span>' . $index; ?></div>
	</div>
	<!-- start class meta-center -->
	<div class="<?php echo esc_attr( $meta_center_class ); ?>">
	<<?php echo $tag; ?> class="lesson-title course-item-title button-load-item" target="<?php echo $target; ?>" <?php echo $viewable ? 'href="' . $course->get_item_link( $item->ID ) . '"' : ''; ?> data-id="<?php echo $item->ID; ?>" data-complete-nonce="<?php echo wp_create_nonce( 'learn-press-complete-' . $item->post_type . '-' . $item->ID ); ?>">

		<?php echo apply_filters( 'learn_press_section_item_title', get_the_title( $item->ID ), $item ); ?>

	</<?php echo $tag; ?>>



	<?php if ( $user->can_view_item( $item->ID, $course->id ) !== false ) { ?>
		<div class="completed-button"><i class="fa fa-check"></i></div>
	<?php } ?>

	<?php if ( $item_preview ): ?>
		<a class="lesson-preview button-load-item" href="<?php echo $course->get_item_link( $item->ID ); ?>" data-id="<?php echo $item->ID; ?>" data-complete-nonce="<?php echo wp_create_nonce( 'learn-press-complete-' . $item->post_type . '-' . $item->ID ); ?>"><?php esc_html_e( 'Preview', 'eduma' ); ?></a>
	<?php endif; ?>

	<?php
	//if ( !$viewable ) {
		//echo '<div class="locked"><i class="fa fa-fa-lock"></i>' . esc_html__( 'Locked', 'eduma' ) . '</div>';
		//echo '<div class="locked"><i class="fa fa-lock"></i></div>';
	//}
	?>
	</div><!-- End class meta-center -->

	<?php do_action( 'learn_press_after_section_item_title', $item, $section, $course ); ?>

</li>
