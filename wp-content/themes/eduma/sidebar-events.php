<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package thim
 */
if ( !is_active_sidebar( 'sidebar_events' ) ) {
	return;
}
$theme_options_data = get_theme_mods();
$sticky_sidebar     = ( !isset( $theme_options_data['thim_sticky_sidebar'] ) || $theme_options_data['thim_sticky_sidebar'] === true ) ? ' sticky-sidebar' : '';
?>

<div id="sidebar" class="widget-area col-sm-3 sidebar-events<?php echo esc_attr( $sticky_sidebar ); ?>" role="complementary">
	<?php if ( !dynamic_sidebar( 'sidebar_events' ) ) :
		dynamic_sidebar( 'sidebar_events' );
	endif; // end sidebar widget area ?>
</div><!-- #secondary -->
