<?php
/**
 * Template for displaying single collection content
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php do_action( 'learn_press_collections_before_single_collection' ); ?>

	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/CreativeWork">

		<?php do_action( 'learn_press_collections_before_single_summary' ); ?>

		<div class="collection-summary">

			<?php the_content(); ?>

		</div>

		<?php do_action( 'learn_press_after_single_collection_summary' ); ?>

	</div><!-- #post-## -->

<?php do_action( 'learn_press_collections_after_single_collection' ); ?>