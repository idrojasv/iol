<?php
$events->addSubSection( array(
	'name'     => esc_html__( 'Archive', 'eduma' ),
	'id'       => 'event_archive',
	'position' => 2,
) );

$events->createOption( array(
	'name'    => esc_html__( 'Layout', 'eduma' ),
	'id'      => 'event_cate_layout',
	'type'    => 'radio-image',
	'options' => array(
		'full-content'  => THIM_URI . 'images/admin/layout/body-full.png',
		'sidebar-left'  => THIM_URI . 'images/admin/layout/sidebar-left.png',
		'sidebar-right' => THIM_URI . 'images/admin/layout/sidebar-right.png'
	),
	'default' => 'sidebar-right'
) );

$events->createOption( array(
	'name'    => esc_html__( 'Hide Breadcrumbs', 'eduma' ),
	'id'      => 'event_cate_hide_breadcrumbs',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Check this box to hide/show breadcrumbs.', 'eduma' ),
	'default' => false,
) );

$events->createOption( array(
	'name'    => esc_html__( 'Hide Title', 'eduma' ),
	'id'      => 'event_cate_hide_title',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Check this box to hide/show title.', 'eduma' ),
	'default' => false,
) );

$events->createOption( array(
	'name'        => esc_html__( 'Top Image', 'eduma' ),
	'id'          => 'event_cate_top_image',
	'type'        => 'upload',
	'desc'        => esc_html__( 'Enter URL or upload a top image file for header.', 'eduma' ),
	'default'     => THIM_URI . 'images/bg-page.jpg',
	'livepreview' => ''
) );

$events->createOption( array(
	'name'        => esc_html__( 'Heading Background Color', 'eduma' ),
	'id'          => 'event_cate_heading_bg_color',
	'type'        => 'color-opacity',
	'livepreview' => ''
) );

$events->createOption( array(
	'name'    => esc_html__( 'Heading Text Color', 'eduma' ),
	'id'      => 'event_cate_heading_text_color',
	'type'    => 'color-opacity',
	'default' => '#fff',
) );

$events->createOption( array(
	'name'    => esc_html__( 'Sub Heading Text Color', 'eduma' ),
	'id'      => 'event_cate_sub_heading_text_color',
	'type'    => 'color-opacity',
	'default' => '#878787',
) );

$events->createOption( array(
	'name'    => esc_html__( 'Sub Title', 'eduma' ),
	'id'      => 'event_cate_sub_title',
	'type'    => 'text',
	'default' => '',
) );

