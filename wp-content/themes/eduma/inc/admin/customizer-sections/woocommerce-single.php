<?php
$woocommerce->addSubSection( array(
	'name'     => esc_html__( 'Product Page', 'eduma' ),
	'id'       => 'woo_single',
	'position' => 2,
) );


$woocommerce->createOption( array(
	'name'    => esc_html__( 'Select Layout Default', 'eduma' ),
	'id'      => 'woo_single_layout',
	'type'    => 'radio-image',
	'options' => array(
		'full-content'  => THIM_URI . 'images/admin/layout/body-full.png',
		'sidebar-left'  => THIM_URI . 'images/admin/layout/sidebar-left.png',
		'sidebar-right' => THIM_URI . 'images/admin/layout/sidebar-right.png'
	),
	'default' => 'full-content'
) );

$woocommerce->createOption( array(
	'name'    => esc_html__( 'Hide Breadcrumbs?', 'eduma' ),
	'id'      => 'woo_single_hide_breadcrumbs',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Check this box to hide/show Breadcrumbs.', 'eduma' ),
	'default' => false,
) );

$woocommerce->createOption( array(
	'name'    => esc_html__( 'Hide Title', 'eduma' ),
	'id'      => 'woo_single_hide_title',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Check this box to hide/show title.', 'eduma' ),
	'default' => false,
) );

$woocommerce->createOption( array(
	'name'        => esc_html__( 'Top Image', 'eduma' ),
	'id'          => 'woo_single_top_image',
	'type'        => 'upload',
	'desc'        => esc_html__( 'Enter URL or upload a top image file for header.', 'eduma' ),
	'default'     => THIM_URI . 'images/bg-page.jpg',
	'livepreview' => ''
) );

$woocommerce->createOption( array(
	'name'        => esc_html__( 'Heading Background Color', 'eduma' ),
	'id'          => 'woo_single_heading_bg_color',
	'type'        => 'color-opacity',
	'livepreview' => ''
) );

$woocommerce->createOption( array(
	'name'    => esc_html__( 'Heading Text Color', 'eduma' ),
	'id'      => 'woo_single_heading_text_color',
	'type'    => 'color-opacity',
	'default' => '#fff',
) );
$woocommerce->createOption( array(
	'name'    => esc_html__( 'Sub Heading Text Color', 'eduma' ),
	'id'      => 'woo_single_sub_heading_text_color',
	'type'    => 'color-opacity',
	'default' => '#878787',
) );

$woocommerce->createOption( array(
	'name'    => esc_html__( 'Sub Title', 'eduma' ),
	'id'      => 'woo_single_sub_title',
	'type'    => 'text',
	'default' => '',
) );
