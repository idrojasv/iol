<?php
$styling->addSubSection( array(
	'name'     => esc_html__( 'Color', 'eduma' ),
	'id'       => 'styling_color',
	'position' => 13,
	'livepreview' => '$("body").css("color", value);'
) );


$styling->createOption( array(
	'name'        => esc_html__( 'Body Background Color', 'eduma' ),
	'id'          => 'body_bg_color',
	'type'        => 'color-opacity',
	'default'     => '#fff',
	'livepreview' => '$("body #main-content").css("background-color", value);'
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Primary Color', 'eduma' ),
	'id'      => 'body_primary_color',
	'type'    => 'color-opacity',
	'default' => '#ffb606',
	'livepreview' => '$(".thim-list-event .item-event .time-from, footer#colophon .copyright-area .text-copyright a, .thim-color, .counter-box .display-percentage, .thim-course-grid .course-item .thim-course-content .course-title a:hover, .thim-widget-courses .view-all-courses:hover, .course-item .course-thumbnail .course-wishlist-box .course-wishlist:hover, article .entry-header .date-meta, .thim-experience .title, .thim-footer-info-3 .heading").css("color", value);
					$(".thim-course-megamenu .course-readmore, .thim-course-grid .course-item .thim-course-content .course-meta:before, .thim-course-grid .course-item .course-thumbnail > a.course-readmore, .navigation .navbar-nav #magic-line, .thim-owl-carousel-post .info, .thim-register-now-form .title:before, article .readmore a, .widget-area aside:before, .wpcf7-form .wpcf7-submit, .mc4wp-form .mc4wp-form-fields button[type=submit], .mc4wp-form .mc4wp-form-fields input[type=submit], .grid-horizontal .item-post:nth-child(2n) .article-wrapper, .thim-widget-carousel-categories .content-wrapper").css("backgroundColor", value);	
					'
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Secondary Color', 'eduma' ),
	'id'      => 'body_secondary_color',
	'type'    => 'color-opacity',
	'default' => '#4caf50',
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Button Hover Background Color', 'eduma' ),
	'id'      => 'button_hover_color',
	'type'    => 'color-opacity',
	'default' => '#e6a303',
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Button Text Color', 'eduma' ),
	'id'      => 'button_text_color',
	'type'    => 'color-opacity',
	'default' => '#333',
) );
