<?php $theme_options_data = get_theme_mods(); ?>
<?php
$class_footer = !empty( $theme_options_data['thim_footer_custom_class'] ) ? $theme_options_data['thim_footer_custom_class'].' site-footer' : 'site-footer';
$class_footer = is_active_sidebar( 'footer_bottom' ) ? $class_footer.' has-footer-bottom' : $class_footer ;

?>
<footer id="colophon" class="<?php echo esc_attr( $class_footer ); ?>">
	<?php if ( is_active_sidebar( 'footer' ) ) : ?>
		<div class="footer">
			<div class="container">
				<div class="row">
					<?php dynamic_sidebar( 'footer' ); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!--==============================powered=====================================-->
	<?php if (  !empty( $theme_options_data['thim_copyright_text'] ) || is_active_sidebar( 'copyright' ) || !isset( $theme_options_data['thim_copyright_text'] ) ) { ?>
		<div class="copyright-area">
			<div class="container">
				<div class="copyright-content">
					<div class="row">
						<?php
						$class_copyright = is_active_sidebar( 'copyright' ) ? 'col-sm-6' : 'col-sm-12';
						if ( isset( $theme_options_data['thim_copyright_text'] ) ) {
							echo '<div class="'.$class_copyright.'"><p class="text-copyright">' . $theme_options_data['thim_copyright_text'] . '</p></div>';
						} else {
							echo '<a href="' . esc_url( 'https://themeforest.net/item/education-wordpress-theme-education-wp/14058034' ) . '" target="_blank">Education WordPress Theme</a> by <a href="' . esc_url( 'http://www.thimpress.com' ) . '" target="_blank">ThimPress.</a> Powered by WordPress.';
						}
						if ( is_active_sidebar( 'copyright' ) ) : ?>
							<div class="col-sm-6 text-right">
								<?php dynamic_sidebar( 'copyright' ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>


</footer><!-- #colophon -->
</div><!--end main-content-->

<!-- Footer Bottom -->
<?php if ( is_active_sidebar( 'footer_bottom' ) ) { ?>
	<?php
	$fb_style = '';
	if ( !empty( $theme_options_data['thim_footer_bottom_bg_img'] ) ) {
		$url_bg   = wp_get_attachment_image_src( $theme_options_data['thim_footer_bottom_bg_img'], 'full' );
		$fb_style = !empty( $url_bg[0] ) ? 'style="background-image: url(' . $url_bg[0] . ');"' : '';
	}
	?>
	<div class="footer-bottom">
		<?php if ( $fb_style !== '' ):
			echo '<div class="thim-bg-overlay-color-half" ' . $fb_style . '>';
		endif;
		?>

		<div class="container">
			<?php dynamic_sidebar( 'footer_bottom' ); ?>
		</div>
		<?php if ( $fb_style !== '' ):
			echo '</div>';
		endif;
		?>
	</div>
<?php } ?>

</div><!-- end content-pusher-->

<?php
if ( isset( $theme_options_data['thim_show_to_top'] ) && $theme_options_data['thim_show_to_top'] == 1 ) { ?>
	<a href="#" id="back-to-top">
		<i class="fa fa-chevron-up"></i>
	</a>
	<?php
}
?>


</div><!-- end wrapper-container -->

<?php wp_footer(); ?>
</body>
</html>