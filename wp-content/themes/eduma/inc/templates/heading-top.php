<?php
global $wp_query, $post;
$GLOBALS['post'] = @$wp_query->post;
$wp_query->setup_postdata( @$wp_query->post );
?>
<?php
$theme_options_data = get_theme_mods();
/***********custom Top Images*************/
$text_color = $custom_title = $subtitle = $bg_color = $bg_header = $class_full = $text_color_header =
$bg_image = $thim_custom_heading = $cate_top_image_src = $front_title = $sub_color = '';

$hide_breadcrumbs = $hide_title = 0;
// color theme options
$cat_obj = $wp_query->get_queried_object();

if ( isset( $cat_obj->term_id ) ) {
	$cat_ID = $cat_obj->term_id;
} else {
	$cat_ID = "";
}

if ( get_post_type() == "product" ) {
	$prefix = 'thim_woo';
} elseif ( get_post_type() == "lpr_course" || get_post_type() == "lpr_quiz" || get_post_type() == "lp_course" || get_post_type() == "lp_quiz" || thim_check_is_course_taxonomy() ) {
	$prefix = 'thim_learnpress';
} elseif ( get_post_type() == "tp_event" ) {
	$prefix = 'thim_event';
} elseif ( get_post_type() == "portfolio" ) {
	$prefix = 'thim_portfolio';
} else {
	$prefix = 'thim_archive';
}

// single and archive
if ( is_page() || is_single() ) {
	$prefix_inner = '_single_';
} else {
	if ( is_front_page() || is_home() ) {
		$prefix       = 'thim';
		$prefix_inner = '_front_page_';
		if ( isset( $theme_options_data[$prefix . $prefix_inner . 'custom_title'] ) && $theme_options_data[$prefix . $prefix_inner . 'custom_title'] <> '' ) {
			$front_title = $theme_options_data[$prefix . $prefix_inner . 'custom_title'];
		}
	} else {
		$prefix_inner = '_cate_';
	}
}
// get data for theme customizer
if ( isset( $theme_options_data[$prefix . $prefix_inner . 'heading_text_color'] ) && $theme_options_data[$prefix . $prefix_inner . 'heading_text_color'] <> '' ) {
	$text_color = $theme_options_data[$prefix . $prefix_inner . 'heading_text_color'];
}
if ( isset( $theme_options_data[$prefix . $prefix_inner . 'sub_heading_text_color'] ) && $theme_options_data[$prefix . $prefix_inner . 'sub_heading_text_color'] <> '' ) {
	$sub_color = $theme_options_data[$prefix . $prefix_inner . 'sub_heading_text_color'];
}

if ( isset( $theme_options_data[$prefix . $prefix_inner . 'heading_bg_color'] ) && $theme_options_data[$prefix . $prefix_inner . 'heading_bg_color'] <> '' ) {
	$bg_color = $theme_options_data[$prefix . $prefix_inner . 'heading_bg_color'];
}

if ( isset( $theme_options_data[$prefix . $prefix_inner . 'top_image'] ) && $theme_options_data[$prefix . $prefix_inner . 'top_image'] <> '' ) {
	$cate_top_image     = $theme_options_data[$prefix . $prefix_inner . 'top_image'];
	$cate_top_image_src = $cate_top_image;

	if ( is_numeric( $cate_top_image ) ) {
		$cate_top_attachment = wp_get_attachment_image_src( $cate_top_image, 'full' );
		$cate_top_image_src  = $cate_top_attachment[0];
	}

}


if ( isset( $theme_options_data[$prefix . $prefix_inner . 'hide_title'] ) ) {
	$hide_title = $theme_options_data[$prefix . $prefix_inner . 'hide_title'];
}

if ( isset( $theme_options_data[$prefix . $prefix_inner . 'hide_breadcrumbs'] ) ) {
	$hide_breadcrumbs = $theme_options_data[$prefix . $prefix_inner . 'hide_breadcrumbs'];
}

if ( isset( $theme_options_data[$prefix . $prefix_inner . 'sub_title'] ) ) {
	$subtitle = $theme_options_data[$prefix . $prefix_inner . 'sub_title'];
}

if ( is_page() || is_single() ) {
	$postid               = get_the_ID();
	$using_custom_heading = get_post_meta( $postid, 'thim_mtb_using_custom_heading', true );
	if ( $using_custom_heading ) {
		$hide_title       = get_post_meta( $postid, 'thim_mtb_hide_title_and_subtitle', true );
		$hide_breadcrumbs = get_post_meta( $postid, 'thim_mtb_hide_breadcrumbs', true );
		$custom_title     = get_post_meta( $postid, 'thim_mtb_custom_title', true );
		$subtitle         = get_post_meta( $postid, 'thim_subtitle', true );

		$text_color_1 = get_post_meta( $postid, 'thim_mtb_text_color', true );
		if ( $text_color_1 <> '' ) {
			$text_color = $text_color_1;
		}
		$sub_color_1 = get_post_meta( $postid, 'thim_mtb_color_sub_title', true );
		if ( $sub_color_1 <> '' ) {
			$sub_color = $sub_color_1;
		}
		$bg_color_1 = get_post_meta( $postid, 'thim_mtb_bg_color', true );
		if ( $bg_color_1 <> '' ) {
			$bg_color = $bg_color_1;
		}
		$cate_top_image = get_post_meta( $postid, 'thim_mtb_top_image', true );
		if ( $cate_top_image ) {
			$post_page_top_attachment = wp_get_attachment_image_src( $cate_top_image, 'full' );
			$cate_top_image_src       = $post_page_top_attachment[0];
		}
	}
} else {
	$thim_custom_heading = get_term_meta( $cat_ID, 'thim_custom_heading', true );
	if ( $thim_custom_heading == 'custom' ) {
		$text_color_1 = get_term_meta( $cat_ID, $prefix . '_cate_heading_text_color', true );
		$bg_color_1   = get_term_meta( $cat_ID, $prefix . '_cate_heading_bg_color', true );
		$sub_color_1  = get_term_meta( $cat_ID, $prefix . '_cate_sub_heading_bg_color', true );
		// reset default
		if ( $text_color_1 != '#' ) {
			$text_color = $text_color_1;
		}
		if ( $bg_color_1 != '#' ) {
			$bg_color = $bg_color_1;
		}
		if ( $sub_color_1 != '#' ) {
			$sub_color = $sub_color_1;
		}

		$hide_breadcrumbs = get_term_meta( $cat_ID, $prefix . '_cate_hide_breadcrumbs', true );
		$hide_title       = get_term_meta( $cat_ID, $prefix . '_cate_hide_title', true );
		$cate_top_image   = get_term_meta( $cat_ID, $prefix . '_top_image', true );
		if ( $cate_top_image ) {
			$cate_top_image_src = $cate_top_image['src'];
		}
	}
}

$hide_title       = ( $hide_title == 'on' ) ? '1' : $hide_title;
$hide_breadcrumbs = ( $hide_breadcrumbs == 'on' ) ? '1' : $hide_breadcrumbs;

$cate_top_image_src = thim_ssl_secure_url( $cate_top_image_src );

// css
$c_css_style = $css_line = $c_css_overlay = $c_css_1 = '';
$c_css_style .= ( $text_color != '' ) ? 'color: ' . $text_color . ';' : '';
$c_css_style .= ( $bg_color != '' ) ? 'background-color: ' . $bg_color . ';' : '';
$c_css_style .= ( $cate_top_image_src != '' ) ? 'background-image:url(' . $cate_top_image_src . ');' : '';
$c_css           = ( $c_css_style != '' ) ? 'style="' . $c_css_style . '"' : '';
$c_css_sub_color = ( $sub_color != '' ) ? 'style="color:' . $sub_color . '"' : '';
$c_css_overlay   = ( $cate_top_image_src != '' && $bg_color != '' ) ? '<span class="overlay-top-header" style="background-color:' . $bg_color . '"></span>' : '';

?>
<?php if ( $hide_title != '1' ) { ?>

	<div class="top_site_main" <?php echo ent2ncr( $c_css ); ?>>
		<?php echo ent2ncr( $c_css_overlay ) ?>
		<div class="page-title-wrapper">
			<div class="banner-wrapper container">
				<?php
				if ( is_single() ) {
					$typography = 'h2';
				} else {
					$typography = 'h1';
				}
				if ( ( get_post_type() == "product" ) ) {
					echo '<' . $typography . '>';
					woocommerce_page_title();
					echo '</' . $typography . '>';
					if ( $subtitle != '' ) {
						echo '<div class="banner-description" ' . $c_css_sub_color . '><p>' . $subtitle . '</p></div>';
					} else {
						echo '<div class="banner-description" ' . $c_css_sub_color . '>';
						do_action( 'woocommerce_archive_description' );
						echo '</div>';
					}
				} elseif ( get_post_type() == "lpr_course" || get_post_type() == "lpr_quiz" || get_post_type() == "lp_course" || get_post_type() == "lp_quiz" || thim_check_is_course() || thim_check_is_course_taxonomy() ) {
					if ( is_single() ) {
						if ( $custom_title ) {
							echo '<' . $typography . '>' . $custom_title . '</' . $typography . '>';
						} else {
							$course_cat = get_the_terms( $postid, 'course_category' );
							if ( !empty( $course_cat ) ) {
								echo '<' . $typography . '>';
								echo esc_html( $course_cat[0]->name );
								echo '</' . $typography . '>';
							}
						}
					} else {
						echo '<' . $typography . '>';
						thim_learnpress_page_title();
						echo '</' . $typography . '>';
					}

					if ( ( get_post_type() == "lpr_course" || get_post_type() == "lp_course" ) && is_tax() ) {
						if ( get_queried_object()->description != '' ) {
							echo '<div class="banner-description" ' . $c_css_sub_color . '><p>' . get_queried_object()->description . '</p></div>';
						} else {
							echo ( $subtitle != '' ) ? '<div class="banner-description" ' . $c_css_sub_color . '><p>' . $subtitle . '</p></div>' : '';
						}
					} else {
						echo ( $subtitle != '' ) ? '<div class="banner-description" ' . $c_css_sub_color . '><p>' . $subtitle . '</p></div>' : '';
					}
				} elseif ( get_post_type() == "lp_collection" ) {

					echo '<' . $typography . '>';
					learn_press_collections_page_title();
					echo '</' . $typography . '>';

				} elseif ( ( is_category() || is_archive() || is_search() || is_404() ) && !thim_use_bbpress() ) {
					if ( get_post_type() == "tp_event" ) {
						echo '<' . $typography . '>';
						echo esc_html__( 'Events', 'eduma' );
						echo '</' . $typography . '>';
					} else {
						echo '<' . $typography . '>';
						echo thim_archive_title();
						echo '</' . $typography . '>';
						if ( category_description( $cat_ID ) != '' ) {
							//thim_archive_description( '<div class="banner-description" ' . $c_css_sub_color . '>', '</div>' );
						} else {
							echo ( $subtitle != '' ) ? '<div class="banner-description" ' . $c_css_sub_color . '><p>' . $subtitle . '</p></div>' : '';
						}
					}
				} elseif ( thim_use_bbpress() ) {
					echo '<' . $typography . '>';
					echo thim_forum_title();
					echo '</' . $typography . '>';
				} elseif ( is_page() || is_single() ) {
					if ( is_single() ) {
						if ( get_post_type() == "post" ) {
							if ( $custom_title ) {
								$single_title = $custom_title;
							} else {
								$category     = get_the_category();
								$category_id  = get_cat_ID( $category[0]->cat_name );
								$single_title = get_category_parents( $category_id, false, " " );
							}
							echo '<' . $typography . '>' . $single_title;
							echo '</' . $typography . '>';
						}
						if ( get_post_type() == "tp_event" ) {
							if ( ! $custom_title ) {
								$custom_title = esc_html__( 'Events', 'eduma' );
							}
							echo '<' . $typography . '>' . $custom_title . '</' . $typography . '>';
						}
						if ( get_post_type() == "portfolio" ) {
							if ( ! $custom_title ) {
								$custom_title = esc_html__( 'Portfolio', 'eduma' );
							}
							echo '<' . $typography . '>' . $custom_title . '</' . $typography . '>';
						}
						if ( get_post_type() == "our_team" ) {
							echo '<' . $typography . '>' . esc_html__( 'Our Team', 'eduma' );
							echo '</' . $typography . '>';
						}
						if ( get_post_type() == "testimonials" ) {
							echo '<' . $typography . '>' . esc_html__( 'Testimonials', 'eduma' );
							echo '</' . $typography . '>';
						}
					} else {
						echo '<' . $typography . '>';
						echo ( $custom_title != '' ) ? $custom_title : get_the_title( get_the_ID() );
						echo '</' . $typography . '>';
					}
					echo ( $subtitle != '' ) ? '<div class="banner-description" ' . $c_css_sub_color . '><p>' . $subtitle . '</p></div>' : '';
				} elseif ( !is_front_page() && is_home() ) {
					echo '<h1>';
					echo ( $front_title != '' ) ? $front_title : esc_html__( 'Blog', 'eduma' );
					echo '</h1>';
					echo ( $subtitle != '' ) ? '<div class="banner-description" ' . $c_css_sub_color . '><p>' . $subtitle . '</p></div>' : '';
				}
				?>
			</div>
		</div>
	</div>
<?php } ?>

<?php
// fix header_overlay
$c_css_1 .= ( $cate_top_image_src != '' ) ? 'background-image:url(' . $cate_top_image_src . ');' : '';
$c_css_1 .= ( $bg_color != '' ) ? 'background-color:' . $bg_color . '' : '';
$c_css_1       = ( $c_css_1 != '' ) ? 'style="' . $c_css_1 . '"' : '';
$c_css_overlay = ( $cate_top_image_src != '' && $bg_color != '' ) ? '<span class="overlay-top-header" style="background-color:' . $bg_color . '"></span>' : '';

$site_main_class = ( isset( $theme_options_data['thim_header_position'] ) && $theme_options_data['thim_header_position'] == 'header_overlay' ) ? 'top_site_main top_site_overlay' : 'top_site_main';

if ( $hide_title == '1' ) { ?>
	<div
		class="<?php echo ent2ncr( $site_main_class ); ?>" <?php echo ent2ncr( $c_css_1 ); ?>><?php echo ent2ncr( $c_css_overlay ) ?></div>
<?php } ?>


<?php
if ( $hide_breadcrumbs != '1' ) {
	if ( !is_front_page() || !is_home() ) { ?>
		<div class="breadcrumbs-wrapper">
			<div class="container">
				<?php
				//Check seo by yoast breadcrumbs
				$wpseo = get_option( 'wpseo_internallinks' );
				if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) && $wpseo['breadcrumbs-enable'] ) {
					if ( function_exists( 'yoast_breadcrumb' ) ) {
						yoast_breadcrumb( '<ul id="breadcrumbs">', '</ul>' );
					}
				} else {

					if ( get_post_type() == 'product' ) {
						woocommerce_breadcrumb();
					} elseif ( get_post_type() == 'lpr_course' || get_post_type() == 'lpr_quiz' || get_post_type() == 'lp_course' || get_post_type() == 'lp_quiz' || thim_check_is_course() || thim_check_is_course_taxonomy() ) {
						thim_learnpress_breadcrumb();
					} elseif ( get_post_type() == 'lp_collection' ) {
						thim_courses_collection_breadcrumb();
					} elseif ( thim_use_bbpress() ) {
						bbp_breadcrumb();
					} else {
						thim_breadcrumbs();
					}
				}

				?>
			</div>
		</div>
	<?php }
}