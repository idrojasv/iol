<?php
/*
 * Front page displays settings: Posts page
 */
$display->addSubSection( array(
	'name'     => esc_html__( 'Blog Page', 'eduma' ),
	'id'       => 'display_frontpage',
	'position' => 1,
) );

$display->createOption( array(
	'name'    => esc_html__( 'Blog Page Layout', 'eduma' ),
	'id'      => 'front_page_cate_layout',
	'type'    => 'radio-image',
	'options' => array(
		'full-content'  => THIM_URI . 'images/admin/layout/body-full.png',
		'sidebar-left'  => THIM_URI . 'images/admin/layout/sidebar-left.png',
		'sidebar-right' => THIM_URI . 'images/admin/layout/sidebar-right.png'
	),
	'default' => 'sidebar-right'
) );

$display->createOption( array(
	'name'    => esc_html__( 'Hide Breadcrumbs', 'eduma' ),
	'id'      => 'front_page_hide_breadcrumbs',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Check this box to hide/show breadcrumbs.', 'eduma' ),
	'default' => false,
) );

$display->createOption( array(
	'name'    => esc_html__( 'Hide Title', 'eduma' ),
	'id'      => 'front_page_hide_title',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Check this box to hide/show title.', 'eduma' ),
	'default' => false,
) );

$display->createOption( array(
	'name'        => esc_html__( 'Top Image', 'eduma' ),
	'id'          => 'front_page_top_image',
	'type'        => 'upload',
	'desc'        => esc_html__( 'Enter URL or upload a top image file for header.', 'eduma' ),
	'default'     => THIM_URI . 'images/bg-page.jpg',
	'livepreview' => ''
) );

$display->createOption( array(
	'name'        => esc_html__( 'Heading Background Color', 'eduma' ),
	'id'          => 'front_page_heading_bg_color',
	'type'        => 'color-opacity',
	'livepreview' => ''
) );

$display->createOption( array(
	'name'    => esc_html__( 'Heading Text Color', 'eduma' ),
	'id'      => 'front_page_heading_text_color',
	'type'    => 'color-opacity',
	'default' => '#fff',
) );

$display->createOption( array(
	'name'    => esc_html__( 'Sub Heading Text Color', 'eduma' ),
	'id'      => 'front_page_sub_heading_text_color',
	'type'    => 'color-opacity',
	'default' => '#878787',
) );

$display->createOption( array(
	'name'    => esc_html__( 'Custom Title', 'eduma' ),
	'id'      => 'front_page_custom_title',
	'type'    => 'text',
	'default' => '',
) );

$display->createOption( array(
	'name'    => esc_html__( 'Sub Title', 'eduma' ),
	'id'      => 'front_page_sub_title',
	'type'    => 'text',
	'default' => '',
) );
