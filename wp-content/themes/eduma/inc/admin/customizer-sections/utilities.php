<?php
$support = $titan->createThimCustomizerSection( array(
	'name'     => esc_html__( 'Utilities', 'eduma' ),
	'position' => 99,
) );

$support->createOption( array(
	'name'    => esc_html__( 'Import Demo Data', 'eduma' ),
	'id'      => 'enable_import_demo',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Enable/Disable', 'eduma' ),
	'default' => true,
) );

$support->createOption( array(
	'name'    => esc_html__( 'Register Redirect', 'eduma' ),
	'id'      => 'register_redirect',
	'type'    => 'text',
	'desc'    => esc_html__( 'Enter register redirect url. Blank will redirect to home page.', 'eduma' ),
	'default' => '',
) );

$support->createOption( array(
	'name'    => esc_html__( 'Login Redirect', 'eduma' ),
	'id'      => 'login_redirect',
	'type'    => 'text',
	'desc'    => esc_html__( 'Enter login redirect url. Blank will redirect to home page.', 'eduma' ),
	'default' => '',
) );