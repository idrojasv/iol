<?php
$courses->addSubSection( array(
	'name'     => esc_html__( 'Single', 'eduma' ),
	'id'       => 'display_learnpress_single',
	'position' => 2,
) );

$courses->createOption( array(
	'name'    => esc_html__( 'Layout', 'eduma' ),
	'id'      => 'learnpress_single_layout',
	'type'    => 'radio-image',
	'options' => array(
		'full-content'  => THIM_URI . 'images/admin/layout/body-full.png',
		'sidebar-left'  => THIM_URI . 'images/admin/layout/sidebar-left.png',
		'sidebar-right' => THIM_URI . 'images/admin/layout/sidebar-right.png'
	),
	'default' => 'sidebar-right'
) );

$courses->createOption( array(
	'name'    => esc_html__( 'Hide Breadcrumbs', 'eduma' ),
	'id'      => 'learnpress_single_hide_breadcrumbs',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Check this box to hide/show breadcrumbs.', 'eduma' ),
	'default' => false,
) );

$courses->createOption( array(
	'name'    => esc_html__( 'Hide Title', 'eduma' ),
	'id'      => 'learnpress_single_hide_title',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Check this box to hide/show title.', 'eduma' ),
	'default' => false,
) );

$courses->createOption( array(
	'name'        => esc_html__( 'Top Image', 'eduma' ),
	'id'          => 'learnpress_single_top_image',
	'type'        => 'upload',
	'desc'        => esc_html__( 'Enter URL or upload a top image file for header.', 'eduma' ),
	'default'     => THIM_URI . 'images/bg-page.jpg',
	'livepreview' => ''
) );

$courses->createOption( array(
	'name'        => esc_html__( 'Heading Background Color', 'eduma' ),
	'id'          => 'learnpress_single_heading_bg_color',
	'type'        => 'color-opacity',
	'livepreview' => ''
) );

$courses->createOption( array(
	'name'    => esc_html__( 'Heading Text Color', 'eduma' ),
	'id'      => 'learnpress_single_heading_text_color',
	'type'    => 'color-opacity',
	'default' => '#fff',
) );

$courses->createOption( array(
	'name'    => esc_html__( 'Sub Heading Text Color', 'eduma' ),
	'id'      => 'learnpress_single_sub_heading_text_color',
	'type'    => 'color-opacity',
	'default' => '#878787',
) );

$courses->createOption( array(
	'name'    => esc_html__( 'Sub Title', 'eduma' ),
	'id'      => 'learnpress_single_sub_title',
	'type'    => 'text',
	'default' => '',
) );