<?php

$display->addSubSection( array(
	'name'     => esc_html__( 'Sharing', 'eduma' ),
	'id'       => 'display_sharing',
	'desc'     => esc_html__( 'Show social sharing button.', 'eduma' ),
	'position' => 6,
) );

$display->createOption( array(
	'name'    => esc_html__('Facebook', 'eduma'),
	'id'      => 'sharing_facebook',
	'type'    => 'checkbox',
	'default' => true,
) );

$display->createOption( array(
	'name'    => esc_html__('Twitter', 'eduma'),
	'id'      => 'sharing_twitter',
	'type'    => 'checkbox',
	'default' => true,
) );

$display->createOption( array(
	'name'    => esc_html__('Google Plus', 'eduma'),
	'id'      => 'sharing_google',
	'type'    => 'checkbox',
	'default' => true,
) );

$display->createOption( array(
	'name'    => esc_html__('Pinterest', 'eduma'),
	'id'      => 'sharing_pinterest',
	'type'    => 'checkbox',
	'default' => true,
) );

