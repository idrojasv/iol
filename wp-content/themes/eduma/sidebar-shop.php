<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package thim
 */
if ( ! is_active_sidebar( 'sidebar_shop' ) ) {
	return;
}
$theme_options_data = get_theme_mods();
$sticky_sidebar     = ( !isset( $theme_options_data['thim_sticky_sidebar'] ) || $theme_options_data['thim_sticky_sidebar'] === true ) ? ' sticky-sidebar' : '';
?>

<div id="sidebar" class="widget-area col-sm-3<?php echo esc_attr( $sticky_sidebar ); ?>" role="complementary">
	<?php if ( ! dynamic_sidebar( 'sidebar_shop' ) ) :
		dynamic_sidebar( 'sidebar_shop' );
	endif; // end sidebar widget area ?>
</div><!-- #secondary-2 -->
