<?php

// Right Drawer Options
$header->addSubSection( array(
	'name'     => esc_html__( 'Off Canvas Sidebar', 'eduma' ),
	'id'       => 'display_right_drawer',
	'position' => 16,
) );


$header->createOption( array(
	'name'    => esc_html__( 'Show / Hide', 'eduma' ),
	'id'      => 'show_offcanvas_sidebar',
	'type'    => 'checkbox',
	'desc'    => 'show/hide',
	'default' => false,
) );

$header->createOption( array(
	'name'        => esc_html__( 'Background Color', 'eduma' ),
	'id'          => 'bg_offcanvas_sidebar_color',
	'type'        => 'color-opacity',
	'default'     => '#141414',
	'livepreview' => '$(".slider_sidebar").css("background-color", value);'
) );

$header->createOption( array(
	'name'        => esc_html__( 'Text Color', 'eduma' ),
	'id'          => 'offcanvas_sidebar_text_color',
	'type'        => 'color-opacity',
	'default'     => '#a9a9a9',
	'livepreview' => '$(".slider_sidebar,.slider_sidebar .widget-title,caption").css("color", value)'
) );

$header->createOption( array(
	'name'        => esc_html__( 'Link Color', 'eduma' ),
	'id'          => 'offcanvas_sidebar_link_color',
	'type'        => 'color-opacity',
	'default'     => '#ffffff',
	'livepreview' => '$(".slider_sidebar a").css("color", value)'
) );

$header->createOption( array(
	'name'    => esc_html__( 'Icon', 'eduma' ),
	'id'      => 'icon_offcanvas_sidebar',
	'type'    => 'text',
	'default' => 'fa-bars',
	'desc'    => sprintf( wp_kses( __( 'Enter <a href="%s" target="_blank">FontAwesome</a> icon name. For example: fa-bars, fa-user.', 'eduma' ), array(  'a' => array( 'href' => array(), 'target' => arr ) ) ), esc_url( 'http://fontawesome.io/icons/' ) )
) );