<?php

// main menu

$header->addSubSection( array(
	'name'     => esc_html__( 'Mobile Menu', 'eduma' ),
	'id'       => 'display_mobile_menu',
	'position' => 15,
) );


$header->createOption( array(
	'name'    => esc_html__( 'Background Color', 'eduma' ),
	'id'      => 'bg_mobile_menu_color',
	'default' => '#232323',
	'type'    => 'color-opacity'
) );


$header->createOption( array(
	'name'    => esc_html__( 'Text Color', 'eduma' ),
	'id'      => 'mobile_menu_text_color',
	'default' => '#777',
	'type'    => 'color-opacity'
) );
$header->createOption( array(
	'name'    => esc_html__( 'Text Hover Color', 'eduma' ),
	'id'      => 'mobile_menu_text_hover_color',
	'default' => '#fff',
	'type'    => 'color-opacity'
) );

$header->createOption( array(
	'name'    => esc_html__( 'Config Logo', 'eduma' ),
	'desc'    => '',
	'id'      => 'config_logo_mobile',
	'options' => array(
		'default_logo' => esc_html__( 'Default', 'eduma' ),
		'custom_logo'  => esc_html__( 'Custom', 'eduma' ),
	),
	'type'    => 'select',
	'default' => 'default_logo'
) );


$header->createOption( array(
	'name'    => esc_html__( 'Logo', 'eduma' ),
	'id'      => 'logo_mobile',
	'type'    => 'upload',
	'desc'    => esc_html__( 'Upload your logo.', 'eduma' ),
) );

$header->createOption( array(
	'name'    => esc_html__( 'Sticky Logo', 'eduma' ),
	'id'      => 'sticky_logo_mobile',
	'type'    => 'upload',
	'desc'    => esc_html__( 'Upload your sticky logo.', 'eduma' ),
) );

$header->createOption( array(
	'name'    => esc_html__( 'Show Login/Logout', 'eduma' ),
	'id'      => 'display_login_menu_mobile',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Display Login/Logout on mobile menu.', 'eduma' ),
	'default' => false,
) );