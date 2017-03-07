<?php
/**
 * Template for displaying the price of a course
 */
learn_press_prevent_access_directly();

do_action( 'learn_press_before_course_price' );

?>
	<div class="course-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		<?php do_action( 'learn_press_begin_course_price' ); ?>
		<div class="value <?php echo learn_press_is_free_course( get_the_ID() ) ? 'free-course' : ''; ?>" itemprop="price" content="<?php echo learn_press_get_course_price( null, false ); ?>">
			<?php echo learn_press_get_course_price( null, true ); ?>
		</div>
		<meta itemprop="priceCurrency" content="<?php echo learn_press_get_currency_symbol(); ?>" />
		<?php do_action( 'learn_press_end_course_price' ); ?>
	</div>
<?php do_action( 'learn_press_after_course_price' ); ?>