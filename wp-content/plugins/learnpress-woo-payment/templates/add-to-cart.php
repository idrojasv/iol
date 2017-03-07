<?php
/**
 * Template for displaying add-to-cart button
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 2.2
 */

defined( 'ABSPATH' ) || exit();
$course = LP()->global['course'];
?>
<?php if ( LP()->settings->get( 'woo_purchase_button' ) == 'single' ) { ?>
	<input type="hidden" name="single-purchase" value="yes" />
<?php } else { ?>
	<button class="button button-add-to-cart" data-action="add-to-cart" data-block-content="yes"><?php _e( 'Add to cart', 'learnpress' ); ?></button>
<?php } ?>
<input type="hidden" name="add-to-cart" value="<?php echo $course->id; ?>" />