<?php
/**
 * Template for displaying collection content within the loop
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//learn_press_get_template_part( 'content', 'course' );

?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'learn_press_collections_before_loop_item' ); ?>

	<a href="<?php the_permalink(); ?>" class="collection-title">

		<?php do_action( 'learn_press_collections_loop_item_title' ); ?>

	</a>

	<?php do_action( 'learn_press_collections_after_loop_item' ); ?>

</div>