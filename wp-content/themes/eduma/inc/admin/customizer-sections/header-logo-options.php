<?php
// main menu
$header->addSubSection( array(
	'name'     => esc_html__( 'Header Options', 'eduma' ),
	'id'       => 'display_logo_options',
	'position' => 5,
) );

$header->createOption( array(
	'name'    => esc_html__( 'Margin Top Header', 'eduma' ),
	'id'      => 'margin_top_header',
	'type'    => 'number',
	'default' => '0',
	'max'     => '100',
	'min'     => '0',
	'step'    => '1',
	'desc'    => esc_html__( 'Unit px.', 'eduma' )
) );

$header->createOption( array(
	'name'    => esc_html__( 'Margin Top Logo', 'eduma' ),
	'id'      => 'margin_top_logo',
	'type'    => 'number',
	'default' => '0',
	'max'     => '50',
	'min'     => '-50',
	'step'    => '1',
	'desc'    => esc_html__( 'Unit px.', 'eduma' )
) );

$header->createOption( array(
	'name'    => esc_html__( 'Margin Bottom Logo', 'eduma' ),
	'id'      => 'margin_bottom_logo',
	'type'    => 'number',
	'default' => '0',
	'max'     => '50',
	'min'     => '-50',
	'step'    => '1',
	'desc'    => esc_html__( 'Unit px.', 'eduma' )
) );