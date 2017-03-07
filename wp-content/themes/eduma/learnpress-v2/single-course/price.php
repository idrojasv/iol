<?php
/**
 * Template for displaying the price of a course
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$course = LP()->global['course'];

if ( learn_press_is_enrolled_course() ) {
	return;
}

$is_required = $course->is_required_enroll();

?>
<?php if ( $price = $course->get_price_html() ) {

	$origin_price = $course->get_origin_price_html();

	?>

	<div class="course-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		<?php if ( $course->is_free() || !$is_required ) : ?>
			<div class="value free-course" itemprop="price" content="<?php esc_attr_e( 'Free', 'eduma' ); ?>">
				<?php esc_html_e( 'Free', 'eduma' ); ?>
			</div>
		<?php else: $price = learn_press_format_price( $course->get_price(), true ); ?>
			<div class="value <?php echo $price != $origin_price ? 'has-origin' : ''; ?>" itemprop="price" content="<?php echo esc_attr( $price ); ?>">
				<?php echo esc_html( $price ); ?>
				<?php
				if ( $price != $origin_price ) {
					echo '<span class="course-origin-price">' . $origin_price . '</span>';
				}
				?>
			</div>
		<?php endif; ?>
		<meta itemprop="priceCurrency" content="<?php echo learn_press_get_currency_symbol(); ?>" />

	</div>

	<?php

}
?>
