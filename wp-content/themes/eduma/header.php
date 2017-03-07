<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package thim
 */
?><!DOCTYPE html>
<?php
$theme_options_data = get_theme_mods();
$item_only          = !empty( $_REQUEST['content-item-only'] ) ? $_REQUEST['content-item-only'] : false;
?>
<html itemscope itemtype="http://schema.org/WebPage" <?php language_attributes(); ?><?php if ( isset( $theme_options_data['thim_rtl_support'] ) && $theme_options_data['thim_rtl_support'] == '1' ) {
	echo " dir=\"rtl\"";
} ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php esc_url( bloginfo( 'pingback_url' ) ); ?>">
	<?php
	$custom_sticky = $class_header = '';
	if ( isset( $theme_options_data['thim_rtl_support'] ) && $theme_options_data['thim_rtl_support'] == '1' ) {
		$class_header .= 'rtl';
	}
	if ( isset( $theme_options_data['thim_config_att_sticky'] ) && $theme_options_data['thim_config_att_sticky'] == 'sticky_custom' ) {
		$custom_sticky .= ' bg-custom-sticky';
	}
	if ( isset( $theme_options_data['thim_header_sticky'] ) && $theme_options_data['thim_header_sticky'] == 1 && !( is_singular( 'lpr_course' ) || is_singular( 'lp_course' ) ) ) {
		$custom_sticky .= ' sticky-header';
	}
	if ( isset( $theme_options_data['thim_header_position'] ) ) {
		$custom_sticky .= ' ' . $theme_options_data['thim_header_position'];
	}

	if ( isset( $theme_options_data['thim_header_style'] ) ) {
		$custom_sticky .= ' ' . $theme_options_data['thim_header_style'];
	} else {
		$custom_sticky .= ' header_v1';
	}

	// mobile menu custom class
	if ( isset( $theme_options_data['thim_config_logo_mobile'] ) && $theme_options_data['thim_config_logo_mobile'] == 'custom_logo' ) {
		if ( wp_is_mobile() && ( ( isset( $theme_options_data['thim_logo_mobile'] ) && $theme_options_data['thim_logo_mobile'] <> '' ) ||
				( isset( $theme_options_data['thim_sticky_logo_mobile'] ) && $theme_options_data['thim_sticky_logo_mobile'] <> '' )
			)
		) {
			$custom_sticky .= ' mobile-logo-custom';
		}
	}


	if ( isset( $theme_options_data['thim_box_layout'] ) && $theme_options_data['thim_box_layout'] == 'boxed' ) {
		$class_boxed = 'boxed-area';
	} else {
		$class_boxed = '';
	}

	if ( isset( $theme_options_data['thim_preload'] ) && !empty( $theme_options_data['thim_preload'] ) && empty( $item_only ) ) {
		$class_header .= ' thim-body-preload';
	} else {
		$class_header .= ' thim-body-load-overlay';
	}

	wp_head();

	?>
</head>

<body <?php body_class( $class_header ); ?> id="thim-body">
<?php if ( isset( $theme_options_data['thim_preload'] ) && !empty( $theme_options_data['thim_preload'] ) && empty( $item_only ) ) { ?>
	<div id="preload">
		<?php
		if ( !empty( $theme_options_data['thim_preload_image'] ) && $theme_options_data['thim_preload'] == 'image' ) {
			echo wp_get_attachment_image( $theme_options_data['thim_preload_image'], 'full' );
		} else {
			if ( $theme_options_data['thim_preload'] == 'style_1' ) {
				?>
				<div class="cssload-loader">
					<div class="cssload-inner cssload-one"></div>
					<div class="cssload-inner cssload-two"></div>
					<div class="cssload-inner cssload-three"></div>
				</div>
				<?php
			} else if ( $theme_options_data['thim_preload'] == 'style_2' ) {
				?>
				<div class="cssload-loader-style-2">
					<div class="cssload-loader-inner-style-2"></div>
				</div>
				<?php
			} else {
				?>
				<div class="cssload-loader-style-3">
					<div class="sk-cube1 sk-cube"></div>
					<div class="sk-cube2 sk-cube"></div>
					<div class="sk-cube4 sk-cube"></div>
					<div class="sk-cube3 sk-cube"></div>
				</div>
				<?php
			}
		}
		?>

	</div>
<?php } ?>

<!-- menu for mobile-->
<div id="wrapper-container" class="wrapper-container">
	<div class="content-pusher <?php echo esc_attr( $class_boxed ); ?>">


		<?php
		// stick header
		$data_height = '';
		if ( isset( $theme_options_data['thim_margin_top_header'] ) && $theme_options_data['thim_margin_top_header'] != '0' ) {
			$data_height = ' data-height-sticky="' . $theme_options_data['thim_margin_top_header'] . '"';
		}

		?>

		<header id="masthead" class="site-header affix-top<?php echo esc_attr( $custom_sticky ); ?>" <?php echo esc_attr( $data_height ); ?>>
			<?php
			//Toolbar
			if ( isset( $theme_options_data['thim_toolbar_show'] ) && $theme_options_data['thim_toolbar_show'] == '1' ) {
				get_template_part( 'inc/header/toolbar' );
			}

			if ( isset( $theme_options_data['thim_header_style'] ) ) {
				get_template_part( 'inc/header/' . $theme_options_data['thim_header_style'] );
			} else {
				get_template_part( 'inc/header/header_v1' );
			}

			?>
		</header>
		<!-- Mobile Menu-->
		<nav class="mobile-menu-container mobile-effect">
			<?php get_template_part( 'inc/header/menu-mobile' ); ?>
		</nav>
		<div id="main-content">