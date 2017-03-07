<?php
/*
 * Creating a logo Options
 */
$logo = $titan->createThemeCustomizerSection( array(
	'name'     => 'title_tagline',
	'position' => 1,
) );

$logo->createOption( array(
	'name'    => esc_html__( 'Logo', 'eduma' ),
	'id'      => 'logo',
	'type'    => 'upload',
	'desc'    => esc_html__( 'Upload your logo.', 'eduma' ),
) );

$logo->createOption( array(
	'name'    => esc_html__( 'Sticky Logo', 'eduma' ),
	'id'      => 'sticky_logo',
	'type'    => 'upload',
	'desc'    => esc_html__( 'Upload your sticky logo.', 'eduma' ),
) );

$logo->createOption( array(
	'name'    => esc_html__( 'Width Logo', 'eduma' ),
	'id'      => 'width_logo',
	'type'    => 'number',
	'default' => '153',
	'max'     => '1024',
	'min'     => '0',
	'step'    => '1',
) );


/**
 * Support favicon for WordPress < 4.3
 */
if ( !function_exists( 'wp_site_icon' ) ) {
	$logo->createOption( array(
		'name'    => esc_html__( 'Favicon', 'eduma' ),
		'id'      => 'favicon',
		'type'    => 'upload',
		'desc'    => esc_html__( 'Upload your favicon.', 'eduma' ),
		'default' => THIM_URI . 'images/favicon.png',
	) );
}