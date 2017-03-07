<?php
$styling->addSubSection( array(
	'name'     => esc_html__( 'Support', 'eduma' ),
	'id'       => 'styling_rtl',
	'position' => 15,
) );

$styling->createOption( array(
	'name'    => esc_html__( 'RTL Support', 'eduma' ),
	'id'      => 'rtl_support',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Enable/Disable', 'eduma' ),
	'default' => false,
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Smooth Scroll', 'eduma' ),
	'id'      => 'smooth_scroll',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Enable/Disable', 'eduma' ),
	'default' => true,
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Remove Query String', 'eduma' ),
	'id'      => 'remove_query_string',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Enable/Disable', 'eduma' ),
	'default' => false,
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Pre-loader', 'eduma' ),
	'id'      => 'preload',
	'desc'    => esc_html__( 'Enable/Disable', 'eduma' ),
	'type'    => 'select',
	'options' => array(
		'' => esc_html__( 'No Pre-loader', 'eduma' ),
		'style_1' => esc_html__( 'Style 1', 'eduma' ),
		'style_2' => esc_html__( 'Style 2', 'eduma' ),
		'style_3' => esc_html__( 'Style 3', 'eduma' ),
		'image'   => esc_html__( 'Image', 'eduma' ),
	),
	'default' => 'style_3'
) );

$styling->createOption( array(
	'name' => esc_html__( 'Pre-loader Image', 'eduma' ),
	'id'   => 'preload_image',
	'type' => 'upload',
	'desc' => esc_html__( 'Choose your image pre-loader. Recommend to use .gif format. Blank to display pre-loader default.', 'eduma' ),
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Google Analytics', 'eduma' ),
	'desc' => esc_html__( 'Enter your ID Google Analytics.', 'eduma' ),
	'id'      => 'google_analytics',
	'type'    => 'text',
	'default' => '',
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Facebook Pixel', 'eduma' ),
	'desc' => esc_html__( 'Enter your ID Facebook Pixel.', 'eduma' ),
	'id'      => 'facebook_pixel',
	'type'    => 'text',
	'default' => '',
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Custom Body Class', 'eduma' ),
	'id'      => 'body_custom_class',
	'type'    => 'text',
	'default' => '',
) );