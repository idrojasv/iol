<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $course;
$is_enrolled = learn_press_is_enrolled_course();
$is_required = get_post_meta( get_the_ID(), '_lpr_course_enrolled_require', true );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/CreativeWork">
	<?php do_action( 'learn_press_before_course_header' ); ?>

	<?php
	do_action( 'learn_press_before_the_title' );
	the_title( '<h1 class="entry-title" itemprop="name">', '</h1>' );
	do_action( 'learn_press_after_the_title' );
	?>

	<div class="course-meta">
		<?php learn_press_course_instructor(); ?>
		<?php learn_press_course_categories(); ?>
		<?php if ( $is_enrolled || $is_required == 'no' ) thim_course_forum_link(); ?>
		<?php thim_course_ratings(); ?>
	</div>
	<div class="course-payment">
		<?php
		if ( !$is_enrolled ) {
			learn_press_course_price();
			learn_press_course_payment_form();
			learn_press_course_enroll_button();
		} else {
			echo '<button class="course-enrolled"><i class="fa fa-check"></i>' . esc_html__( 'Enrolled', 'eduma' ) . '</button>';
		}
		?>
	</div>

	<?php learn_press_course_thumbnail(); ?>

	<?php do_action( 'learn_press_before_course_content' ); ?>
	<?php
	do_action( 'learn_press_before_the_content' );
	if ( $is_enrolled || $is_required == 'no' ) {
		learn_press_get_template_part( 'course_content', 'learning_page' );
	} else {
		learn_press_get_template_part( 'course_content', 'landing_page' );
	}
	do_action( 'learn_press_after_the_content' );
	?>
	<?php do_action( 'learn_press_before_course_footer' ); ?>

</article>