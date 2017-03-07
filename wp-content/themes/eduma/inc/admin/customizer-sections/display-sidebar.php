<?php

$display->addSubSection( array(
	'name'     => esc_html__( 'Sidebar', 'eduma' ),
	'id'       => 'display_sidebar',
	'desc'     => esc_html__( 'Options for sidebar.', 'eduma' ),
	'position' => 7,
) );

$display->createOption( array(
	'name'    => esc_html__('Sticky Sidebar', 'eduma'),
	'id'      => 'sticky_sidebar',
	'type'    => 'checkbox',
	'default' => true,
) );

