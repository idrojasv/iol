<?php
$styling->addSubSection( array(
	'name'     => esc_html__( 'Layout', 'eduma' ),
	'id'       => 'styling_layout',
	'position' => 10,
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Body Layout', 'eduma' ),
	'id'      => 'box_layout',
	'type'    => 'select',
	'options' => array(
		'boxed' => esc_html__( 'Boxed', 'eduma' ),
		'wide'  => esc_html__( 'Wide', 'eduma' ),
	),
	'default' => 'Wide',
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Pattern Background', 'eduma' ),
	'id'      => 'user_bg_pattern',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Check the box to display a pattern in the background. If checked, select the pattern from below.', 'eduma' ),
	'default' => false,
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Select Pattern Background', 'eduma' ),
	'id'      => 'bg_pattern',
	'type'    => 'radio-image',
	'options' => array(
		THIM_URI . 'images/patterns/pattern1.png'  => THIM_URI . 'images/patterns/pattern1.png',
		THIM_URI . 'images/patterns/pattern2.png'  => THIM_URI . 'images/patterns/pattern2.png',
		THIM_URI . 'images/patterns/pattern3.png'  => THIM_URI . 'images/patterns/pattern3.png',
		THIM_URI . 'images/patterns/pattern4.png'  => THIM_URI . 'images/patterns/pattern4.png',
		THIM_URI . 'images/patterns/pattern5.png'  => THIM_URI . 'images/patterns/pattern5.png',
		THIM_URI . 'images/patterns/pattern6.png'  => THIM_URI . 'images/patterns/pattern6.png',
		THIM_URI . 'images/patterns/pattern7.png'  => THIM_URI . 'images/patterns/pattern7.png',
		THIM_URI . 'images/patterns/pattern8.png'  => THIM_URI . 'images/patterns/pattern8.png',
		THIM_URI . 'images/patterns/pattern9.png'  => THIM_URI . 'images/patterns/pattern9.png',
		THIM_URI . 'images/patterns/pattern10.png' => THIM_URI . 'images/patterns/pattern10.png',
	)
) );

$styling->createOption( array(
	'name'        => esc_html__( 'Upload Background', 'eduma' ),
	'id'          => 'bg_upload',
	'type'        => 'upload',
	'desc'        => esc_html__( 'Upload your background.', 'eduma' ),
	'livepreview' => ''
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Repeat Background', 'eduma' ),
	'id'      => 'bg_repeat',
	'type'    => 'select',
	'options' => array(
		'repeat'    => 'repeat',
		'repeat-x'  => 'repeat-x',
		'repeat-y'  => 'repeat-y',
		'no-repeat' => 'no-repeat'
	),
	'default' => 'no-repeat'
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Background Position', 'eduma' ),
	'id'      => 'bg_position',
	'type'    => 'select',
	'options' => array(
		'left top'      => esc_html__( 'Left Top', 'eduma' ),
		'left center'   => esc_html__( 'Left Center', 'eduma' ),
		'left bottom'   => esc_html__( 'Left Bottom', 'eduma' ),
		'right top'     => esc_html__( 'Right Top', 'eduma' ),
		'right center'  => esc_html__( 'Right Center', 'eduma' ),
		'right bottom'  => esc_html__( 'Right Bottom', 'eduma' ),
		'center top'    => esc_html__( 'Center Top', 'eduma' ),
		'center center' => esc_html__( 'Center Center', 'eduma' ),
		'center bottom' => esc_html__( 'Center Bottom', 'eduma' )
	),
	'default' => 'center center'
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Background Attachment', 'eduma' ),
	'id'      => 'bg_attachment',
	'type'    => 'select',
	'options' => array(
		'scroll'  => 'scroll',
		'fixed'   => 'fixed',
		'local'   => 'local',
		'initial' => 'initial',
		'inherit' => 'inherit'
	),
	'default' => 'inherit'
) );

$styling->createOption( array(
	'name'    => esc_html__( 'Background Size', 'eduma' ),
	'id'      => 'bg_size',
	'type'    => 'select',
	'options' => array(
		'100% 100%' => '100% 100%',
		'contain'   => 'contain',
		'cover'     => 'cover',
		'inherit'   => 'inherit',
		'initial'   => 'initial'
	),
	'default' => 'inherit'
) );